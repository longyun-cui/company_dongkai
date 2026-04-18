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

use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__IndexRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
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
            $admin = Auth::guard("dk_staff_user")->user();

//            $department_district_id = $admin->department_district_id;
//            $department_group_id = $admin->department_group_id;
//
//            if($department_district_id > 0)
//            {
//                $department_district = DK_Department::find($department_district_id);
//                if($department_district)
//                {
//                    if($department_district->item_status != 1)
//                    {
//                        return response_error([],'员工所属团队已禁用！');
//                    }
//                }
//                else return response_error([],'员工所属团队不存在！');
//            }
//
//            if($department_group_id > 0)
//            {
//                $department_group = DK_Department::find($department_group_id);
//                if($department_group)
//                {
//                    if($department_group->item_status != 1)
//                    {
//                        return response_error([],'员工所属小组已禁用！');
//                    }
//                }
//                else return response_error([],'员工所属小组不存在！');
//            }

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




    // 返回【主页】视图
    public function view__staff__index()
    {
        $this->get_me();
        $me = $this->me;

//        if($me->id > 10000)
//        {
//            $record["creator_id"] = $me->id;
//            $record["record_category"] = 1; // record_category=1 browse/share
//            $record["record_type"] = 1; // record_type=1 browse
//            $record["page_type"] = 1; // page_type=1 default platform
//            $record["page_module"] = 1; // page_module=1 index
//            $record["page_num"] = 0;
//            $record["open"] = "root";
//            $record["from"] = request('from',NULL);
//            $this->record_for_user_visit($record);
//        }

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        $this_month = date('Y-m');
        $this_month_start_date = date('Y-m-01'); // 本月开始日期
        $this_month_ended_date = date('Y-m-t'); // 本月结束日期
        $this_month_start_datetime = date('Y-m-01 00:00:00'); // 本月开始时间
        $this_month_ended_datetime = date('Y-m-t 23:59:59'); // 本月结束时间
        $this_month_start_timestamp = strtotime($this_month_start_date); // 本月开始时间戳
        $this_month_ended_timestamp = strtotime($this_month_ended_datetime); // 本月结束时间戳

        $last_month_start_date = date('Y-m-01',strtotime('last month')); // 上月开始时间
        $last_month_ended_date = date('Y-m-t',strtotime('last month')); // 上月开始时间
        $last_month_start_datetime = date('Y-m-01 00:00:00',strtotime('last month')); // 上月开始时间
        $last_month_ended_datetime = date('Y-m-t 23:59:59',strtotime('last month')); // 上月结束时间
        $last_month_start_timestamp = strtotime($last_month_start_date); // 上月开始时间戳
        $last_month_ended_timestamp = strtotime($last_month_ended_datetime); // 上月月结束时间戳



        // 【客服】部门
        $department_list = DK_Common__Department::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('department_category',41)
//            ->where('team_type',11)
            ->get();
        $view_data['department_list'] = $department_list;


        // 【客服】团队
        $team_list = DK_Common__Team::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('team_category',41)
            ->where('team_type',11)
            ->get();
        $view_data['team_list'] = $team_list;


        // 【客服】人员
        $staff_query = DK_Common__Staff::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('staff_category',41)
            ->whereIn('staff_position',[61,99]);

        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                // 部门总监
                $staff_query->where('department_id',$me->department_id);
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
                $staff_query->where('team_id',$me->team_id);
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
                $staff_query->where('team_id',$me->team_id);
                $staff_query->where('team_group_id',$me->team_group_id);
            }
            else
            {
                $staff_query->where('team_id',-1);
            }
        }
        $staff_list = $staff_query->get();
        $view_data['staff_list'] = $staff_list;


        // 【质检】人员
        $inspector_query = DK_Common__Staff::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('staff_category',51)
            ->whereIn('staff_position',[41,61,99]);
        // 质检部
        if($me->staff_category == 51)
        {
            if($me->staff_position == 31)
            {
                // 部门总监
                $inspector_query->where('department_id',$me->department_id);
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
                $inspector_query->where('team_id',$me->team_id);
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
                $inspector_query->where('team_id',$me->team_id);
                $inspector_query->where('team_group_id',$me->team_group_id);
            }
            else
            {
                $inspector_query->where('team_id',-1);
            }
        }
        $inspector_list = $inspector_query->get();
        $view_data['inspector_list'] = $inspector_list;




        // 项目
        $project_query = DK_Common__Project::select('id','name')
            ->where('active',1)
            ->where('item_status',1);
        // 客服部
        if($me->staff_category == 41)
        {
            $project_ids = DK_Pivot__Department_Project::select('project_id')
                ->where('department_id',$me->department_id)
                ->get()
                ->pluck('project_id')
                ->toArray();
            $project_query->whereIn('id',$project_ids)->get();

            if($me->staff_position == 31)
            {
                // 部门总监
//                $project_ids = DK_Pivot__Department_Project::select('project_id')->where('department_id',$me->department_id)->get()->pluck('project_id')->toArray();
//                $project_query->whereIn('id',$project_ids)->get();
            }
            if(in_array($me->staff_position,[41,51,61,71,99]))
            {
                // 团队成员
                $project_ids = DK_Pivot__Team_Project::select('project_id')->where('team_id',$me->team_id)->get()->pluck('project_id')->toArray();
                $project_query->whereIn('id',$project_ids)->get();
            }
        }

        // 质检部 & 复核部
        if(in_array($me->staff_category,[51,61]))
        {
//            $project_ids = DK_Pivot__Department_Project::select('project_id')->where('department_id',$me->department_id)->get()->pluck('project_id')->toArray();
//            $project_query->whereIn('id',$project_ids)->get();
//
//            if($me->staff_position == 31)
//            {
//                // 部门总监
////                $project_ids = DK_Pivot__Department_Project::select('project_id')->where('department_id',$me->department_id)->get()->pluck('project_id')->toArray();
////                $project_query->whereIn('id',$project_ids)->get();
//            }
//            else if($me->staff_position == 41)
//            {
//                // 团队经理（多对对）
//                $project_ids = DK_Pivot__Team_Project::select('project_id')->where('team_id',$me->team_id)->get()->pluck('project_id')->toArray();
//                $project_query->whereIn('id',$project_ids)->get();
//            }
//            else if($me->staff_position == 61)
//            {
//                // 小组主管（多对对）
//                $staff_ids = DK_Common__Staff::select('id')->where('team_group_id',$me->id)->get()->pluck('id')->toArray();
//                $project_ids = DK_Pivot__Staff_Project::select('project_id')->whereIn('staff_id',$staff_ids)->get()->pluck('project_id')->toArray();
//                $project_query->whereIn('id',$project_ids)->get();
//            }
//            else if($me->staff_position == 99)
//            {
//                // 职员（多对多）
//                $project_ids = DK_Pivot__Staff_Project::select('project_id')->where('staff_id',$me->id)->get()->pluck('project_id')->toArray();
//                $project_query->whereIn('id',$project_ids)->get();
//            }
        }

        // 运营部
        if($me->staff_category == 71)
        {

            if($me->staff_position == 31)
            {
                // 部门总监
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
            }
            else if($me->staff_position == 99)
            {
                // 职员
            }
        }

        $project_list = (clone $project_query)->get();
        $project_list__for__dental = (clone $project_query)->where('project_category',1)->get();
//        dd($project_list__for__dental);
        $project_list__for__aesthetic = (clone $project_query)->where('project_category',11)->get();
        $project_list__for__luxury = (clone $project_query)->where('project_category',31)->get();
        $view_data['project_list'] = $project_list;
        $view_data['project_list__for__dental'] = $project_list__for__dental;
        $view_data['project_list__for__aesthetic'] = $project_list__for__aesthetic;
        $view_data['project_list__for__luxury'] = $project_list__for__luxury;


        // 客户
        $client_query = DK_Common__Client::select('id','name')
            ->where('active',1)
            ->where('item_status',1);
        $client_list = (clone $client_query)->get();
        $client_list__for__dental = (clone $client_query)->where('client_category',1)->get();
        $client_list__for__aesthetic = (clone $client_query)->where('client_category',11)->get();
        $client_list__for__luxury = (clone $client_query)->where('client_category',31)->get();
        $view_data['client_list'] = $client_list;
        $view_data['client_list__for__dental'] = $client_list__for__dental;
        $view_data['client_list__for__aesthetic'] = $client_list__for__aesthetic;
        $view_data['client_list__for__luxury'] = $client_list__for__luxury;


        // 地区-城市
        $location_city_list = DK_Common__Location::select('id','location_city')
            ->whereIn('item_status',[1])
            ->get();
        $view_data['location_city_list'] = $location_city_list;

        $view_blade = env('DK_STAFF__TEMPLATE').'index';
        return view($view_blade)->with($view_data);
    }



    // 返回【403】页面
    public function view__staff__403()
    {
        $this->get_me();
        return view($this->view_blade_403);
    }

    // 返回【404】页面
    public function view__staff__404()
    {
        $this->get_me();
        return view($this->view_blade_404);
    }


    //
    public function view__staff__test()
    {
//        $this->get_me();

        // 设置请求的URL
        // 以下是北京地域url，如果使用新加坡地域的模型，需要将url替换为：https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions
        $url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        // 若没有配置环境变量，请用阿里云百炼API Key将下行替换为：$apiKey = "sk-xxx";
        // 新加坡和北京地域的API Key不同。获取API Key：https://help.aliyun.com/model-studio/get-api-key
        $apiKey = env('DASHSCOPE_API_KEY');
        // 设置请求头
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];
        // 设置请求体
        $data = [
            // 模型列表：https://help.aliyun.com/model-studio/getting-started/models
            "model" => "qwen3.5-omni-plus",
            "messages" => [
                [
                    "role" => "system",
                    "content" => "你是一个严格的质检员，请分析录音内容，请严格遵循JSON Schema输出。禁止使用Markdown格式，禁止包含json代码块标记，禁止换行，输出内容必须是单行的紧凑JSON字符串。不要包含任何其他解释或文字。如果信息在录音中不存在，请对应字段填null！"
                ],
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "input_audio",
                            "input_audio" => [
                                "data" => "http://call02.zlyx.jjccyun.cn/data/voicerecord/11/20260417/none-20260417-173852-13362365888-fnj13yldx.mp3",
                                "format" => "mp3"
                            ]
                        ],
                        [
                            "type" => "text",
                            "text" => "请帮我分析录音，提取以下信息：1.客户所在的城市与行政区；2.需要医疗的牙齿数量（数字）；3.客户上门的意愿（布尔值：true/false 或 枚举值：有意愿/无意愿）。"
                        ]
                    ]
                ]
            ],
            "parameters" => [
                "response_format" => [
                    "type" => "json_object",
                    "schema" => [
                        "type" => "object",
                        "properties" => [
                            "location" => [
                                "type" => "object",
                                "properties" => [
                                    "city" => [
                                        "type" => "string",
                                        "description" => "客户所在城市，例如：嘉兴市"
                                    ],
                                    "district" => [
                                        "type" => "string",
                                        "description" => "客户所在行政区，例如：南湖区"
                                    ]
                                ],
                                "required" => ["city"]
                            ],
                            "dental_need" => [
                                "type" => "object",
                                "properties" => [
                                    "tooth_count" => [
                                        "type" => "integer",
                                        "description" => "需要医疗的牙齿数量"
                                    ],
                                    "visit_willingness" => [
                                        "type" => "boolean",
                                        "description" => "客户是否有上门意愿"
                                    ]
                                ],
                                "required" => ["visit_willingness"]
                            ]
                        ],
                        "required" => ["location", "dental_need"]
                    ]
                ]
            ],
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
//        echo $response;
        echo '<br>';
//        dd($response);
        dd(json_decode($response,true));


    }




}