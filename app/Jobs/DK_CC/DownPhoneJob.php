<?php

namespace App\Jobs\DK_CC;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK_A\DK_Pool_Task;
use App\Models\DK_A\DK_Pool;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class DownPhoneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $task_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($task_id)
    {
        //
        $this->task_id = $task_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $task = DK_Pool_Task::find($this->task_id);
        if($task)
        {

            $date = date('Y-m-d');
            $datetime = date('Y-m-d H:i:s');
            $date_Ymd = date('Ymd');

            $task_id = $task->id;
            $pool_id = $task->pool_id;
            $name = $task->name;


            $pool = DK_Pool::find($pool_id);
            if($pool)
            {
                $table = $pool->data_table;
                $modal = $pool->data_modal;
                $region_name = $pool->region_name;
                $pool_name = $pool->region_name;
            }
            else return;


            $telephone_count = $task->extraction_telephone_count;
            $file_num = $task->extraction_file_num;
            $file_size = $task->extraction_file_size;

            $extraction_name = $task->extraction_name;
            if($extraction_name)
            {
                $name = $extraction_name;
            }
            else
            {
                $name = $pool_name.'-'.$date_Ymd;
            }


            $telephone_count_1 = ceil($telephone_count * 0.6);
            $telephone_count_2 = ceil($telephone_count * 0.3);
            $telephone_count_3 = ceil($telephone_count * 0.1);

            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                $telephone_update['task_id'] = $task->id;
                $telephone_update['last_extraction_date'] = $date;

//            $telephone = DB::table($table)->select('phone');
                $telephone = ($modal ?? false)::select('phone')
                    ->where(function ($query) {
                        $query->whereNull('last_call_date')
                            ->orWhereDate('last_call_date', '<', now()->subDays(1)->format('Y-m-d'));
                    }) ?: collect();


                $telephone_1 = (clone $telephone);
                $telephone_2 = (clone $telephone);
                $telephone_3 = (clone $telephone);


                $telephone_1->where('quality','>=',90)
                    ->orderby('task_id','asc')
                    ->limit($telephone_count_1);

                $telephone_2->where('quality','>=',80)->where('quality','<',90)
                    ->orderby('task_id','asc')
                    ->limit($telephone_count_2);

                $telephone_3->where('quality',0)
                    ->orderby('task_id','asc')
                    ->limit($telephone_count_3);


                $telephone_1->update($telephone_update);
                $telephone_2->update($telephone_update);
                $telephone_3->update($telephone_update);


                $telephone_list = ($modal ?? false)::select('task_id','phone','quality')->where('task_id',$task_id)->get() ?: collect();


                $upload_path = <<<EOF
resource/dk/cc/telephone/$date/
EOF;
                $url_path = env('DOMAIN_CDN').'/dk/cc/telephone/'.$date.'/';

                $storage_path = storage_path($upload_path);
                if (!is_dir($storage_path))
                {
                    mkdir($storage_path, 0755, true);
                }
//            $filename = $name.'-'.$task_id;
                $filename = $task_id.'-'.$name;
                $extension = '.txt';


                if($file_num > 1)
                {
                    if($file_size == 0) $file_size = ceil($telephone_count / $file_num);
                    $chunks = $telephone_list->chunk($file_size);

                    $count = 1;
                    $file_list = [];
                    foreach($chunks as $chunk)
                    {
                        $file_name = $filename.'-第'.$count.'批'.$extension;
                        $file_url = $url_path.$file_name;
                        $file_path = $storage_path.$file_name;

                        // 打开文件准备写入
                        $file = fopen($file_path, 'w');
                        $count++;

                        $i['name'] = $file_name;
                        $i['path'] = $file_path;
                        $i['url'] = $file_url;
                        $file_list[] = $i;

                        // 遍历电话号码数组，逐行写入文件
                        foreach ($chunk as $phoneNumber)
                        {
                            fwrite($file, $phoneNumber->phone . PHP_EOL);
                        }

                        // 关闭文件
                        fclose($file);
                    }

                }
                else
                {
                    $file_list = [];

                    $file_name = $filename.$extension;
                    $file_url = $url_path.$filename.$extension;
                    $file_path = $storage_path.$filename.$extension;

                    // 打开文件准备写入
                    $file = fopen($file_path, 'w');

                    // 遍历电话号码数组，逐行写入文件
                    foreach ($telephone_list as $phoneNumber)
                    {
                        fwrite($file, $phoneNumber->phone . PHP_EOL);
                    }

                    // 关闭文件
                    fclose($file);

                    $i['name'] = $file_name;
                    $i['path'] = $file_path;
                    $i['url'] = $file_url;
                    $file_list[] = $i;

                }

                $task->is_completed = 1;
                $task->content = json_encode($file_list);
                $task->save();

                DB::commit();

            }
            catch (Exception $e)
            {
                DB::rollback();

                $msg = $e->getMessage();

                $task->is_completed = 9;
                $task->content = $msg;
                $task->save();

                return;
            }





        }
        else return;

    }
}
