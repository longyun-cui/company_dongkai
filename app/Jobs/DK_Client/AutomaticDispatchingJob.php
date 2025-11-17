<?php

namespace App\Jobs\DK_Client;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Client_Delivery;

use App\Models\DK_Client\DK_Client_Department;
use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Contact;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class AutomaticDispatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $client_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id)
    {
        //
        $this->client_id = $client_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client_id = $this->client_id;
        $client = DK_Client::find($client_id);
        if($client)
        {
            $staff_list = DK_Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
                ->where('client_id',$client_id)
                ->where('is_take_order',1)
                ->where('is_take_order_date',date('Y-m-d'))
                ->orderBy('is_take_order_datetime','asc')
                ->get();

            $delivery_list = DK_Pivot_Client_Delivery::select('*')
                ->where('client_id',$client_id)
                ->where('client_staff_id',0)
                ->get();


            $staff_list = $staff_list->values(); // 重置索引确保从0开始
            $staffCount = $staff_list->count();
            if($staffCount == 0) return;
            $deliveryCount = $delivery_list->count();
            if($deliveryCount == 0) return;



            // 使用原子锁避免并发冲突
            // 创建原子锁（设置最大等待时间和自动释放时间）
            $lock = Cache::lock("client:{$client_id}:assign_lock", 10); // 锁最多持有10秒
//        if (!$lock->get())
//        {
//            abort(423, '系统正在分配任务中，请稍后重试');
//        }

            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                // 尝试获取锁，最多等待5秒
                $lock->block(5); // 这里会阻塞直到获取锁或超时

//            $staffIndex = 0;
//            foreach ($delivery_list as $delivery)
//            {
//                $staff = $staff_list[$staffIndex % $staffCount];
//                $delivery->client_staff_id = $staff->id;
//                $delivery->save(); // 触发模型事件（如有）
//                $staffIndex++;
//            }


                // 从缓存获取上次位置（不存在则初始化为0）
                $lastIndex = Cache::get("client:{$client_id}:last_staff_index", 0);
                $currentIndex = $lastIndex % $staffCount;
                $newIndex = $currentIndex;

                foreach ($delivery_list as $delivery) {
                    $staff = $staff_list[$currentIndex];
                    $delivery->client_staff_id = $staff->id;
                    $delivery->save();

                    // 计算下一个索引
                    $currentIndex = ($currentIndex + 1) % $staffCount;
                    $newIndex = $currentIndex; // 记录最后的下一个位置
                }

                // 将新位置写入缓存（有效期10小时）
                Cache::put(
                    "client:{$client_id}:last_staff_index",
                    $newIndex,
                    now()->addHours(10)
                );



                DB::commit();
//            $lock->release();
                optional($lock)->release(); // 确保无论如何都释放锁
            }
            catch (Exception $e)
            {
                DB::rollback();
//            $lock->release();
                optional($lock)->release(); // 确保无论如何都释放锁
                $msg = '操作失败，请重试！';
                $msg = $e->getMessage();
//            exit($e->getMessage());
                return ;
            }
            finally
            {
//            $lock->release();
                optional($lock)->release(); // 确保无论如何都释放锁
            }

        }
        else return;
    }
}
