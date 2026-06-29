<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;

use App\Models\DK\DK_Common\DK_Common__Location;

use App\Models\DK\DK_Common\DK_Common__Client;
use App\Models\DK\DK_Common\DK_Common__Project;

use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Order__Operation_Record;
use App\Models\DK\DK_Common\DK_Common__Delivery;

use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

use App\Models\DK\DK_Common\DK_Common__Order__AI_Converted__Record;
use App\Models\DK\DK_Common\DK_Common__Order__AI_Inspected__Record;

use App\Repositories\DK\DK_Staff\DK_Staff__CommonRepository;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__AIRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelOrder;
    private $view_blade_403;
    private $view_blade_404;
    protected $commonRepository;


    public function __construct()
    {
        $this->view_blade_403 = env('DK_STAFF__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_STAFF__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');

        $this->commonRepository = new DK_Staff__CommonRepository;
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_staff_user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_staff_user")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    /*
     * AI
     */
    // 【AI】返回-列表-数据
    public function o1__ai__converted_record__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Order__AI_Converted__Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'order_er'=>function($query) { $query->select(['*']); },
            ])
            ->where('active',1);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);


        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            $item_status_int = intval($post_data['item_status']);
            if(!in_array($item_status_int,[-1,0]))
            {
                $query->where('item_status', $item_status_int);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(!empty($v->result))
            {
            }
            else
            {
            }
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }
    // 【AI】返回-列表-数据
    public function o1__ai__record__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Order__AI_Inspected__Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'order_er'=>function($query) { $query->select(['*']); },
            ])
            ->where('active',1);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);


        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            $item_status_int = intval($post_data['item_status']);
            if(!in_array($item_status_int,[-1,0]))
            {
                $query->where('item_status', $item_status_int);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(!empty($v->result))
            {
                $result = json_decode($v->result);
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
                    }
                    $list[$k]->content = $content_decode;
                }
                else
                {
                    if(isset($result->error))
                    {
                        $list[$k]->content = $result->error;
                    }
                    else $list[$k]->content = null;
                }

                if(isset($result->usage))
                {
                    $list[$k]->usage = $result->usage;
                }
                else
                {
                    $list[$k]->usage = null;
                }

                if(isset($result->id))
                {
                    $list[$k]->chatcmpl = $result->id;
                }
                else
                {
                    $list[$k]->chatcmpl = null;
                }
            }
            else
            {
                $list[$k]->content = null;
                $list[$k]->usage = null;
            }
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    public function o1__ai__item__inspecting($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

//        $operate = $post_data["operate"];
//        if($operate != 'order--item-inspecting--by-ai') return response_error([],"参数[operate]有误！");
//        $item_id = $post_data["item_id"];
//        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");


        $prompt_text = !empty($post_data['prompt_text']) ? $post_data['prompt_text'] : config('dk.common-config.ai_prompt_text');
        $recording_address = $post_data['recording_address'];
        $recording_address_list = [];
        $recording_address_list[] = $recording_address;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,71]))
        {
            return response_error([],"你没有操作权限！");
        }



        $ai_inspecting_post_date['platform'] = 'ali';
        $ai_inspecting_post_date['model'] = 'qwen3.5-omni-plus';
        $ai_inspecting_post_date['prompt'] = $prompt_text;
        $ai_inspecting_post_date['voice_record'] = $recording_address;
        $ai_inspecting_post_date['voice_record_list'] = $recording_address_list;

        $microtime_ai = microtime(true);
        $ai_inspecting_response = $this->commonRepository->o1__api__ai_inspecting__from__ali($ai_inspecting_post_date);
        if(!empty($ai_inspecting_response))
        {
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
                }
                return response_success(['result'=>$content_decode],"审核完成!");
            }
            else
            {
                return response_fail([],'返回结果为空！');
            }
        }
        else return response_fail([],"返回结果有误！");


    }


    public function o1__ai__converted__item__get_result($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $microtime_start = microtime(true);

        $operate = $post_data["operate"];
        if($operate != 'ai--converted--get-result') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,51,61,71]))
        {
            return response_error([],"你没有操作权限！");
        }


        $item = DK_Common__Order__AI_Converted__Record::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【记录】不存在，刷新页面重试！");


        $order_id = $item->order_id;
        $order = DK_Common__Order::withTrashed()->find($order_id);
        if(!$order) return response_error([],"该【工单】不存在！");


        $response_decode = json_decode($item->result,true);
        if(!empty($response_decode['output']['task_id']))
        {
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
                                $transcription_text_array[(int)$value['sentence_id']] = '【客服】'.$value['text'];
                            }
                            else
                            {
                                $transcription_text_array[(int)$value['sentence_id']] = '【潜在客户】'.$value['text'];
                            }
                        }
                    }
                }
//                    dd($transcription_text_array);

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
//                echo 'Curl error: ' . curl_error($ch);
                $item->description = 'Curl error: ' . curl_error($ch);
                $item->save();
            }
            // 关闭cURL资源
            curl_close($ch);
        }
        else
        {
            $item->item_status = 99;
            $item->description = 'task_id不存在！';
            $item->save();
        }


        return response_success([]);

    }


}