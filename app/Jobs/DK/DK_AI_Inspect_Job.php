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
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Team;

use App\Repositories\DK\DK_Staff\DK_Staff__CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class DK_AI_Inspect_Job implements ShouldQueue
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
    public function __construct($ai_inspected_record_id)
    {
        //
        $this->id = $ai_inspected_record_id;
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

        $ai_platform = config('dk.common-config.ai_platform_text');
        $ai_model = config('dk.common-config.ai_model_text');
        $ai_prompt = config('dk.common-config.ai_prompt_text');


        $id = $this->id;
        $item = DK_Common__Order__AI_Inspected__Record::find($id);
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
                $ai_platform = !empty($project->ai_platform) ? $project->ai_platform : $ai_platform;
                $ai_model = !empty($project->ai_model) ? $project->ai_model : $ai_model;
                $ai_prompt = !empty($project->ai_prompt) ? ($project->ai_prompt) : $ai_prompt;
//                $ai_prompt = !empty($project->ai_prompt) ? ($project->ai_prompt.$ai_prompt) : $ai_prompt;
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
                    else
                    {
                        $url = $protocol . '//' . $hostname . $port . '/recordFile/listen?file=' . $path;
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
            $ai_data['ai_prompt'] = $ai_prompt;
            $ai_data['order_id'] = $order_id;

            $item->ai_platform = $ai_platform;
            $item->ai_model = $ai_model;
            $item->ai_prompt = $ai_prompt;

            $bool_ai = $item->save();
            if(!$bool_ai)
            {
                throw new Exception("DK_Common_DK_Common__Order__AI_Inspected__Record_Order--update--fail");
            }
            else
            {
                $ai_inspecting_post_date['platform'] = $ai_data['ai_platform'];
                $ai_inspecting_post_date['model'] = $ai_data['ai_model'];
                $ai_inspecting_post_date['prompt'] = $ai_data['ai_prompt'];
                $ai_inspecting_post_date['voice_record'] = $voice_record_url;
                $ai_inspecting_post_date['voice_record_list'] = $recording_address_list;

                $microtime_ai = microtime(true);
                $ai_inspecting_response = $this->commonRepository->o1__api__ai_inspecting__from__ali($ai_inspecting_post_date);
                $microtime_ended = microtime(true);

                $item->item_status = 9;
                $item->ai_used_time = $microtime_ended - $microtime_ai;
                $item->program_used_time = $microtime_ended - $microtime_start;
                $item->result = $ai_inspecting_response;
                $bool_ai_2 = $item->save();
                if(!$bool_ai_2)
                {
                    throw new Exception("DK_Common__Order__AI_Inspected__Record--update--fail");
                }


                $result = json_decode($ai_inspecting_response);
                if(isset($result->choices[0]->message->content))
                {
                    $content = $result->choices[0]->message->content;
                    $content_decode = json_decode($content);
                    if(!$content_decode)
                    {
                        $content_fix = robustJsonFix($content);
                        $content_decode = json_decode($content_fix);
                        if(!$content_decode)
                        {
                            $content_fix_2 = robustJsonFixer($content_fix);
                            $content_decode = json_decode($content_fix_2);
                        }
                        else $content_decode = null;
                    }


                    if($content_decode)
                    {
                        if(array_key_exists($content_decode->审核结果,config('dk.common-config.ai_inspected_result_to_order_inspected_result')))
                        {
                            $order->inspected_status = 1;
                            $order->inspected_result = $content_decode->审核结果;
//                        $order->inspected_result = config('dk.common-config.ai_inspected_result_to_order_inspected_result.'.$content_decode->审核结果);
                            $order->inspected_at = $time;
                            $order->inspected_date = $date;
                            $bool_order = $order->save();
                            if(!$bool_order) throw new Exception("DK_Common__Order--update--fail");


                            $record_data["ip"] = Get_IP();
                            $record_data["record_object"] = 1;
                            $record_data["record_category"] = 1;
                            $record_data["record_type"] = 1;
                            $record_data["creator_id"] = 101;
                            $record_data["creator_company_id"] = 0;
                            $record_data["creator_department_id"] = 0;
                            $record_data["creator_team_id"] = 0;
                            $record_data["order_id"] = $order->id;
                            $record_data["operate_object"] = 1;
                            $record_data["operate_category"] = 41;
                            $record_data["operate_type"] = 51;
                            $record_data["description"] = $content_decode->判定依据及理由;


                            $record_content = [];


                            if(true)
                            {
                                $record_row = [];
                                $record_row['title'] = '员工操作';
                                $record_row['field'] = 'item_operation';
                                $record_row['before'] = '';
                                $record_row['after'] = '质检审核';
                                $record_content[] = $record_row;
                            }
                            if(true)
                            {
                                $record_row = [];
                                $record_row['title'] = '审核时间';
                                $record_row['field'] = 'inspected_time';
                                $record_row['before'] = '';
                                $record_row['after'] = $datetime;
                                $record_content[] = $record_row;
                            }
                            if(true)
                            {
                                $record_row = [];
                                $record_row['title'] = '审核结果';
                                $record_row['field'] = 'inspected_result';
                                $record_row['before'] = '';
                                $record_row['after'] = config('dk.common-config.ai_inspected_result_to_order_inspected_result.'.$content_decode->审核结果);
                                $record_content[] = $record_row;
                            }
                            if(true)
                            {
                                $record_row = [];
                                $record_row['title'] = '判定理由';
                                $record_row['field'] = 'inspected_description';
                                $record_row['before'] = '';
                                $record_row['after'] = $content_decode->判定理由;
                                $record_content[] = $record_row;
                            }

                            $record_data["content"] = json_encode($record_content);
                            $record = new DK_Common__Order__Operation_Record;

                            $bool_1 = $record->fill($record_data)->save();
                            if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");

                        }
                    }

                }


            }

            $order->ai_inspected_status = 9;
            $order->save();

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
