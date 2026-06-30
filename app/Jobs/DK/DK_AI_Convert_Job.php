<?php
namespace App\Jobs\DK;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Order__Operation_Record;
use App\Models\DK\DK_Common\DK_Common__Order__AI_Inspected__Record;
use App\Models\DK\DK_Common\DK_Common__Order__AI_Converted__Record;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Team;

use App\Repositories\DK\DK_Staff\DK_Staff__CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class DK_AI_Convert_Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $id;
    protected $commonRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ai_converted_record_id)
    {
        //
        $this->id = $ai_converted_record_id;
        $this->commonRepository = new DK_Staff__CommonRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $microtime_start = microtime(true);

        $ai_platform = config('dk.common-config.ai_platform__for___converting');
        $ai_model = config('dk.common-config.ai_model__for___converting');


        $id = $this->id;
        $item = DK_Common__Order__AI_Converted__Record::find($id);
        if(!$item)
        {
            return;
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $order_id = $item->order_id;

            $order = DK_Common__Order::withTrashed()->find($order_id);
            if(!$order)
            {
                throw new Exception("该【工单】不存在！");
            }


            $project = DK_Common__Project::find($order->project_id);
            if($project)
            {
//                $ai_platform = !empty($project->ai_platform) ? $project->ai_platform : $ai_platform;
//                $ai_model = !empty($project->ai_model) ? $project->ai_model : $ai_model;
            }
            else
            {
                throw new Exception("该工单的【项目】不存在！");
            }

            $voice_record_url = '';
            $recording_address_list = $order->recording_address_list;
            if(!empty($recording_address_list))
            {
                $recording_address_list = json_decode($recording_address_list);
                if(count($recording_address_list) > 0) $voice_record_url = $recording_address_list[0];
                if(!empty($voice_record_url))
                {
                }
                else
                {
                    throw new Exception("该工单的【录音】不存在！");
                }
            }
            else
            {
                $staff_id = $order->creator_id;

                $staff = DK_Common__Staff::withTrashed()->find($staff_id);
                if(!$staff)
                {
                    throw new Exception("该【员工】不存在！");
                }
                $team_id = $staff->team_id;
                $agent[] = $staff->api_staffNo;

                $team = DK_Common__Team::withTrashed()->find($team_id);
                if(!$team)
                {
                    throw new Exception("所属【团队】不存在！");
                }


                if($order->order_category == 1)
                {
                    $serverFrom_name = $team->serverFrom_name;
                    $API_Customer_Password = $team->api_customer_password;
                    $API_Customer_Account = $team->api_customer_account;
                    $API_customerUserName = $team->api_customer_name;
                }
                else if($order->order_category == 11)
                {
                    $serverFrom_name = $team->serverFrom_name_2;
                    $API_Customer_Password = $team->api_customer_password_2;
                    $API_Customer_Account = $team->api_customer_account_2;
                    $API_customerUserName = $team->api_customer_name_2;
                }
                else
                {
                    $serverFrom_name = $team->serverFrom_name;
                    $API_Customer_Password = $team->api_customer_password;
                    $API_Customer_Account = $team->api_customer_account;
                    $API_customerUserName = $team->api_customer_name;
                }

                $get_recording_data = [];
                $get_recording_data['serverFrom_name'] = $serverFrom_name;
                $get_recording_data['api_customer_password'] = $API_Customer_Password;
                $get_recording_data['api_customer_account'] = $API_Customer_Account;
                $get_recording_data['api_customer_name'] = $API_customerUserName;
                $get_recording_data['client_phone'] = $order->client_phone;
                $get_recording_data['published_date'] = $order->published_date;

                $response = $this->commonRepository->o1__api__get_call_recording__from__by($get_recording_data);
                if($response['error'] == 0)
                {
                    $order->recording_address_list = $response['recording_address_list'];
                    $bool = $order->save();
                    $recording_address_list = json_decode($response['recording_address_list']);
                    if(count($recording_address_list) > 0) $voice_record_url = $recording_address_list[0];

                }
                else if($response['error'] == 1)
                {
                    throw new Exception($response['result']);

                }
                else
                {
//                    throw new Exception("录音文件地址获取失败！");
                    throw new Exception(json_encode($response));
                }

            }

            if(!empty($voice_record_url))
            {
                $ch = curl_init($voice_record_url);
                curl_setopt($ch, CURLOPT_NOBODY, true); // 只获取头部信息，不下载body内容
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($responseCode == 200)
                {
                }
                else
                {
                    $myUrl = parse_url($voice_record_url);
                    $protocol = isset($myUrl['scheme']) ? $myUrl['scheme'] . ':' : '';
                    $hostname = isset($myUrl['host']) ? $myUrl['host'] : '';
                    $port = isset($myUrl['port']) ? ':' . $myUrl['port'] : '';
                    $path = isset($myUrl['path']) ? $myUrl['path'] : '';

                    $url = '';
                    if($hostname == 'call01.zlyx.jjccyun.cn')
                    {
                        $url = 'http://8.142.7.121:9091/res/rs1/recordFile/listen?file=' . $path;
                    }
                    else if($hostname == 'call02.zlyx.jjccyun.cn')
                    {
                        $url = $protocol . '//' . $hostname . $port . '/recordFile/listen?file=' . $path;
                    }
                    else if($hostname == 'fnjvce02.zlexin.cn')
                    {
                        $url = $protocol . '//' . $hostname . $port . '/recordFile/listen?file=' . $path;
                    }
                    else
                    {
//                        $url = $protocol . '//' . $hostname . $port . '/recordFile/listen?file=' . $path;
                        $url = $voice_record_url;
                    }

                    $ch = curl_init($url); // 初始化cURL会话
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 设置选项，返回响应内容而不是输出到页面上
                    curl_setopt($ch, CURLOPT_HEADER, 0); // 不需要头部信息，设置为0
                    $response = curl_exec($ch); // 执行cURL请求并获取响应结果
                    if(curl_errno($ch))
                    {
                        // 检查是否有错误发生
    //                    echo 'Error:' . curl_error($ch);
                    }
                    else
                    {
    //                    echo $response; // 输出响应内容
                    }
                    curl_close($ch); // 关闭cURL会话


                    sleep(1); // 暂停1秒


                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_NOBODY, true); // 只获取头部信息，不下载body内容
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($responseCode == 200)
                    {
                    }
                    else
                    {
                        throw new Exception("录音文件不存在！");
                    }
                }
            }
            else
            {
                throw new Exception("录音文件地址不存在！");
            }




            $time = time();
            $date = date("Y-m-d");
            $datetime = date('Y-m-d H:i:s');


            $ai_data['ai_platform'] = $ai_platform;
            $ai_data['ai_model'] = $ai_model;
            $ai_data['order_id'] = $order_id;

            $item->ai_platform = $ai_platform;
            $item->ai_model = $ai_model;

            $bool_ai = $item->save();
            if(!$bool_ai)
            {
                throw new Exception("DK_Common_DK_Common__Order__AI_Converted__Record_Order--update--fail");
            }
            else
            {
                $ai_inspecting_post_date['platform'] = $ai_data['ai_platform'];
                $ai_inspecting_post_date['model'] = $ai_data['ai_model'];
                $ai_inspecting_post_date['voice_record'] = $voice_record_url;
                $ai_inspecting_post_date['voice_record_list'] = $recording_address_list;

                $microtime_ai = microtime(true);
                $ai_converting_response = $this->commonRepository->o1__api__ai_converting__from__ali($ai_inspecting_post_date);
                $microtime_ended = microtime(true);

                $item->item_status = 9;
                $item->ai_used_time = $microtime_ended - $microtime_ai;
                $item->program_used_time = $microtime_ended - $microtime_start;
                $item->result = $ai_converting_response;
                $bool_ai_2 = $item->save();
                if(!$bool_ai_2)
                {
                    throw new Exception("DK_Common__Order__AI_Converted__Record--update--fail");
                }

                $response_decode = json_decode($ai_converting_response,true);
                if(!empty($response_decode['output']['task_id']))
                {
                    sleep(3); // 暂停1秒
                    // 设置请求头
                    $apiKey = env('DASHSCOPE_API_KEY');
                    $headers = [
                        'Authorization: Bearer ' . $apiKey
                    ];
                    $url = 'https://dashscope.aliyuncs.com/api/v1/tasks/'.$response_decode['output']['task_id'];

                    // 初始化cURL会话
                    $ch = curl_init();
                    // 设置cURL选项
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    // 执行cURL会话
                    $response_json = curl_exec($ch);
                    $response = json_decode($response_json,true);

                    $SUCCEEDED = false;
                    if($response['output']['task_status'] == "SUCCEEDED")
                    {
                        $SUCCEEDED = true;
                    }
                    else
                    {
                        sleep(2); // 暂停1秒
                        $response_json = curl_exec($ch);
                        $response = json_decode($response_json,true);
                        if($response['output']['task_status'] == "SUCCEEDED")
                        {
                            $SUCCEEDED = true;
                        }
                    }

                    if($SUCCEEDED)
                    {
                        $item->result = $response_json;
                        $bool_ai_3 = $item->save();

                        $task_results = $response['output']['results'];
                        $transcription_text = '';
                        $transcription_text_array = [];
                        $transcription_content = [];

                        foreach($task_results as $v)
                        {
                            $url = $v['transcription_url'];
                            $transcript_json = file_get_contents($url);
                            $transcription_content[] = $transcript_json;
                            $transcript_json_decode = json_decode($transcript_json,true);
                            $transcript_array = $transcript_json_decode['transcripts'];
                            foreach($transcript_array as $k => $val)
                            {
                                $sentences_list = $val['sentences'];
                                foreach($sentences_list as $key => $value)
                                {
                                    if($k == 1)
                                    {
                                        $transcription_text_array[(int)$value['sentence_id']] = '[客服] '.$value['text'];
                                    }
                                    else
                                    {
                                        $transcription_text_array[(int)$value['sentence_id']] = '【潜在客户】'.$value['text'];
                                    }
                                }
                            }
                        }

                        if(count($transcription_text_array) > 0)
                        {
                            ksort($transcription_text_array);
                            foreach($transcription_text_array as $k => $v)
                            {
                                $transcription_text .= $v. "\n";
                            }
                        }

                        $order->ai_converted_status = 9;
                        $order->content = $transcription_text;
                        $order->save();


                        $item->item_status = 9;
                        $item->content = $transcription_text;
                        $item->result2 = json_encode($transcription_content);
                        $item->save();

                    }
                    else
                    {
                        $order->ai_converted_status = 19;
                        $order->save();

                        $item->item_status = 19;
                        $item->description = '任务未完成，稍后获取结果！';
                        $item->save();
                    }
                    // 检查是否有错误发生
                    if (curl_errno($ch))
                    {
                        throw new Exception('Curl error: ' . curl_error($ch));
                    }
                    // 关闭cURL资源
                    curl_close($ch);
                }
                else
                {
                    throw new Exception('task_id不存在！');
                }


            }

//            $order->ai_inspected_status = 9;
//            $order->save();

            DB::commit();
            return;
        }
        catch (Exception $e)
        {
//            DB::rollback();
            DB::commit();
            $msg = $e->getMessage();
//            exit($e->getMessage());
//            return response_fail([],$msg);
            $item->item_status = 99;
            $item->description = $msg;
            $item->save();

            $order->ai_inspected_status = 99;
            $order->save();
            return;
        }
    }
}
