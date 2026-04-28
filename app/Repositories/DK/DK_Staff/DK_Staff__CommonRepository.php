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

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__CommonRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelOrder;
    private $view_blade_403;
    private $view_blade_404;


    public function __construct()
    {
        $this->modelUser = new DK_Common__Staff;
        $this->modelOrder = new DK_Common__Order;

        $this->view_blade_403 = env('DK_STAFF__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_STAFF__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
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
     * select2
     */
    // 公司
    public function o1__select2__company($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Company::select(['id','name as text'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'all')
            {
            }
            else if($type == 'company')
            {
                $query->where(['company_category'=>1]);
            }
            else if($type == 'channel')
            {
                $query->where(['company_category'=>11]);
                if(!empty($post_data['company_id']))
                {
                    $query->where('superior_company_id',$post_data['company_id']);
                }
            }
            else if($type == 'business')
            {
                $query->where(['company_category'=>21]);
                if(!empty($post_data['channel_id']))
                {
                    $query->where('superior_company_id',$post_data['channel_id']);
                }
            }
            else
            {
//                $query->where(['department_type'=>11]);
            }
        }
        else
        {
//            $query->where(['department_type'=>11]);
        }

//        if($me->staff_type == 81)
//        {
//            $query->where('id',$me->department_district_id);
//        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'[选择公司]'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 部门
    public function o1__select2__department($post_data)
    {
        $query = DK_Common__Department::select(['id','name as text'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['department_category']))
        {
            $query->where('department_category',$post_data['department_category']);
        }
        if(!empty($post_data['department_type']))
        {
            $query->where('department_type',$post_data['department_type']);
        }
        if(!empty($post_data['company_id']))
        {
            $query->where('company_id',$post_data['company_id']);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'选择部门'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 团队
    public function o1__select2__team($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Team::select(['id','name as text'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }


        if(in_array($me->staff_position,[31,41,51,61,71]))
        {
            $query->where('department_id',$me->department_id);
        }

        if(in_array($me->staff_position,[41,51,61,71]))
        {
            $query->where('superior_team_id',$me->team_id);
        }

        if(in_array($me->staff_position,[61]))
        {
            $query->where('superior_team_group_id',$me->team_group_id);
        }


        // 部门类型
        if(!empty($post_data['department_type']))
        {
            $query->where('department_type',$post_data['department_type']);
        }
        // 部门id
        if(!empty($post_data['department_id']))
        {
            $query->where('department_id',$post_data['department_id']);
        }
        // 团队种类
        if(!empty($post_data['item_category']))
        {
            $query->where('team_category',$post_data['item_category']);
        }
        // 团队类型
        if(!empty($post_data['item_type']))
        {
            $query->where('team_type',$post_data['item_type']);
        }
        // 团队种类
        if(!empty($post_data['team_category']))
        {
            $query->where('team_category',$post_data['team_category']);
        }
        // 团队类型
        if(!empty($post_data['team_type']))
        {
            $query->where('team_type',$post_data['team_type']);
        }
        // 上级团队
        if(!empty($post_data['superior_team_id']))
        {
            $query->where('superior_team_id',$post_data['superior_team_id']);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'选择团队'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 员工
    public function o1__select2__staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Staff::select(['id','name as text'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }


        if($me->department_id > 0)
        {
            $query->where('department_id',$me->department_id);
        }
        if($me->team_id > 0)
        {
            $query->where('team_id',$me->team_id);
        }


        if(!empty($post_data['staff_category']))
        {
            $staff_category_int = intval($post_data['staff_category']);
            if(!in_array($staff_category_int,[-1,0]))
            {
                $query->where('staff_category',$staff_category_int);
            }
        }
        if(!empty($post_data['staff_type']))
        {
            $staff_type_int = intval($post_data['staff_type']);
            if(!in_array($staff_type_int,[-1,0]))
            {
                $query->where('staff_type',$staff_type_int);
            }
        }


        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'inspector') $query->where(['user_type'=>77]);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'选择员工'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 地区
    public function o1__select2__location($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $item_category = $post_data['item_category'];

        if($item_category == 1)
        {
            $query = DK_Common__Location::select(['id','location_city as text'])
                ->where('active',1)
                ->where('item_status',1);

            if(!empty($post_data['keyword']))
            {
                $keyword = "%{$post_data['keyword']}%";
                $query->where('location_city','like',"%$keyword%");
            }

            $list = $query->orderBy('id','asc')->get()->toArray();
        }
        else if($item_category == 11)
        {
            $location_city = !empty($post_data['location_city']) ? $post_data['location_city'] : '';
            $query = DK_Common__Location::select(['id','location_district as text'])
                ->where('active',1)
                ->where('item_status',1)
                ->where('location_city',$location_city);

            if(!empty($post_data['keyword']))
            {
                $keyword = "%{$post_data['keyword']}%";
                $query->where('location_district','like',"%$keyword%");
            }

            $query_list = $query->orderBy('id','asc')->get()->toArray();

            if(count($query_list) > 0)
            {
                $list = explode("-",$query_list[0]['text']);
                foreach($list as $key => $value)
                {
                    $list[$key] = ['id'=>$value,'text'=>$value];
                }
            }
            else
            {
                $list = [];
            }
        }







//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'[选择地区]'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 客户
    public function o1__select2__client($post_data)
    {
        $query = DK_Common__Client::select(['id','name as text'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['client_category']))
        {
            $client_category_int = intval($post_data['client_category']);
            if(!in_array($client_category_int,[-1,0]))
            {
                $query->where('client_category',$client_category_int);
            }
        }
        if(!empty($post_data['client_type']))
        {
            $client_type_int = intval($post_data['client_type']);
            if(!in_array($client_type_int,[-1,0]))
            {
                $query->where('client_type',$client_type_int);
            }
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'选择客户'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 项目
    public function o1__select2__project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Project::select(['id','name as text','alias_name'])
            ->where('active',1)
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['project_category']))
        {
            $project_category_int = intval($post_data['project_category']);
            if(!in_array($project_category_int,[-1,0]))
            {
                $query->where('project_category',$project_category_int);
            }
        }
        if(!empty($post_data['project_type']))
        {
            $project_type_int = intval($post_data['project_type']);
            if(!in_array($project_type_int,[-1,0,]))
            {
                $query->where('project_type',$project_type_int);
            }
        }


        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'选择项目'];
//        array_unshift($list,$unSpecified);

        return $list;
    }



    // 【密码】返回修改视图
    public function o1__my_account__password_change__view()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.my-account.my-account-password-change';
        return view($view_blade)->with($return);
    }
    // 【密码】保存数据
    public function o1__my_account__password_change__save($post_data)
    {
        $messages = [
            'password_pre.required' => '请输入旧密码',
            'password_new.required' => '请输入新密码',
            'password_confirm.required' => '请输入确认密码',
        ];
        $v = Validator::make($post_data, [
            'password_pre' => 'required',
            'password_new' => 'required',
            'password_confirm' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $password_pre = request()->get('password_pre');
        $password_new = request()->get('password_new');
        $password_confirm = request()->get('password_confirm');

        if($password_new == $password_confirm)
        {
            $this->get_me();
            $me = $this->me;
            if(password_check($password_pre,$me->password))
            {
                $me->password = password_encode($password_new);
                $bool = $me->save();
                if($bool) return response_success([], '密码修改成功！');
                else return response_fail([], '密码修改失败！');
            }
            else
            {
                return response_fail([], '原密码有误！');
            }
        }
        else return response_error([],'两次密码输入不一致！');
    }



    //
    public function o1__api__ai_inspecting__from__ali($post_data)
    {
        $platform = $post_data['platform'];
        $model = $post_data['model'];
        $prompt = $post_data['prompt'];
        $audio = $post_data['voice_record'];
        $audio_list = $post_data['voice_record_list'];

        $content_list = [];
        foreach($audio_list as $k => $v)
        {
            $audio = [];
            $audio['type'] = "input_audio";
            $audio['input_audio']['data'] = $v;
            $audio['input_audio']['format'] = "mp3";
            $content_list[] = $audio;
        }
        {
            $text = [];
            $text['type'] = "text";
            $text['text'] = $prompt;
            $content_list[] = $text;
        }

        // 设置请求的URL
        $url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        // 若没有配置环境变量，请用阿里云百炼API Key将下行替换为：$apiKey = "sk-xxx";
        $apiKey = env('DASHSCOPE_API_KEY');
        // 设置请求头
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];
        // 设置请求体
        $data = [
            // 模型列表：https://help.aliyun.com/model-studio/getting-started/models
            "model" => $model,
            "messages" => [
                [
                    "role" => "system",
                    "content" => "你是一个严格的质检员，请分析录音内容，请严格遵循JSON Schema输出。禁止使用Markdown格式，禁止包含json代码块标记，禁止换行，输出内容必须是单行的紧凑JSON字符串，每一个返回字段均为关联数组类型， :之后不要再有{，也不要包含英文字符的单引号与双引号，如需引用标注，请使用中文的引号，避免数据格式混乱。不要包含任何其他解释或文字。如果信息在录音中不存在，请对应字段填null！"
                ],
                [
                    "role" => "user",
                    "content" => $content_list
                ]
            ],
//            "parameters" => [
//                "response_format" => [
//                    "type" => "json_object",
//                    "schema" => [
//                        "type" => "object",
//                        "properties" => [
//                        ],
//                        "required" => []
//                    ]
//                ]
//            ],
//            "stream" => true,
//            "stream_options" => [
//                "include_usage" => true
//            ],
            "modalities" => ["text"],
            "audio" => [
                "format" => "mp3"
            ]
        ];
//        dd($data);


        // 初始化cURL会话
        $ch = curl_init();
        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // 执行cURL会话
        $response = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        // 关闭cURL资源
        curl_close($ch);
        // 输出响应结果
        return $response;


    }


    // 【工单】外呼系统呼叫记录
    public function o1__api__get_call_recording__from__by($post_data)
    {

//        dd($post_data);
        $serverFrom_name = $post_data['serverFrom_name'];
        $API_Customer_Password = $post_data['api_customer_password'];
        $API_Customer_Account = $post_data['api_customer_account'];
        $client_phone = $post_data['client_phone'];
        $published_date = $post_data['published_date'];


        $timestamp = time();
        $seq = $timestamp;
        $digest = md5($API_Customer_Account.'@'.$timestamp.'@'.$seq.'@'.$API_Customer_Password);

        $request_data['authentication']['customer'] = $API_Customer_Account;
        $request_data['authentication']['timestamp'] = strval($timestamp);
        $request_data['authentication']['seq'] = strval($seq);
        $request_data['authentication']['digest'] = $digest;

        $request_data['request']['seq'] = '';
        $request_data['request']['userData'] = '';
//        $request_data['request']['agent'] = $agent;
        $request_data['request']['callee'] = $client_phone;
        $request_data['request']['startTime'] = $published_date.' 00:00:00';
        $request_data['request']['endTime'] = $published_date.' 23:59:59';


        if($serverFrom_name == "FNJ")
        {
            $server = "http://feiniji.cn";
            $url = "http://feiniji.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "call-01")
        {
            $server = "http://call01.zlyx.jjccyun.cn";
            $url = "http://call01.zlyx.jjccyun.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "call-02")
        {
            $server = "http://call02.zlyx.jjccyun.cn";
            $url = "http://call02.zlyx.jjccyun.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "call-03")
        {
            $server = "http://call03.zlyx.jjccyun.cn";
            $url = "http://call03.zlyx.jjccyun.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "call-04")
        {
            $server = "http://call04.zlyx.jjccyun.cn";
            $url = "http://call04.zlyx.jjccyun.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "call-04")
        {
            $server = "http://call04.zlyx.jjccyun.cn";
            $url = "http://call04.zlyx.jjccyun.cn/openapi/V2.0.6/getCdrList";
        }
        else if($serverFrom_name == "sys-21")
        {
            $server = "http://okcc8.zytchina.net";
            $url = "http://okcc8.zytchina.net/openapi/V2.0.6/getCdrList";
        }
        else
        {
            return response_error([],"请先配置API！");
        }


        $request_data = json_encode($request_data);
//        dd($request_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_data); // post的变量
        $request_result = curl_exec($ch);


        $return = [];
        $return['error'] = 0;
        $return['status'] = 1;
        $return['result'] = '';
        $return['recording_address_list'] = '';

        if(curl_errno($ch))
        {
            curl_close($ch);

            $return['error'] = 1;
            $return['status'] = 9;
            $return['result'] = '请求失败！';
        }
        else
        {
            curl_close($ch);

            $result = json_decode($request_result);
            if($result->result->error == "0")
            {
                if($result->data)
                {
                    $file = [];
                    $response = $result->data->response;
                    if($response->total > 0)
                    {
                        $success_count = 0;
                        foreach ($response->cdr as $k => $v)
                        {
                            if($v->serviceType == 4) $success_count += 1;
                        }

                        if($success_count > 0)
                        {
                            foreach ($response->cdr as $k => $v)
                            {
                                if(!empty($v->filename)) $file[] = $server.$v->filename;
                            }
                        }
                        else return response_error([],'没有有效通话记录，非自动点拨通话！');

                        if(count($file) > 0)
                        {

                            $recording_address_list = json_encode($file);
                            $return['recording_address_list'] = $recording_address_list;
                        }
                        else
                        {
                            $return['error'] = 1;
                            $return['result'] = '没有有效通话记录c！';
                        }
                    }
                    else
                    {
                        $return['error'] = 1;
                        $return['result'] = '没有有效通话记录b！';
                    }
                }
                else
                {
                    $return['error'] = 1;
                    $return['result'] = '没有有效通话记录a！';
                }
            }
            else
            {
                $return['error'] = 1;
                $return['result'] = $result->result->msg;
            }
        }

        return $return;


    }





}