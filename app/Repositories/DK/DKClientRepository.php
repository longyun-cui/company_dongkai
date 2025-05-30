<?php
namespace App\Repositories\DK;

use App\Models\DK\DK_Department;
use App\Models\DK\DK_User;
use App\Models\DK_Choice\DK_Choice_Call_Record;

use App\Models\DK_Client\DK_Client_Department;
use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Contact;

use App\Models\DK_Client\DK_Client_Follow_Record;
use App\Models\DK_Client\DK_Client_Trade_Record;


use App\Models\DK_Client\DK_Client_Project;
use App\Models\DK_Client\DK_Client_Record;
use App\Models\DK_Client\DK_Client_Finance_Daily;

use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Client_Delivery;

use App\Models\DK\DK_Order;
use App\Models\DK\DK_Client;
use App\Models\DK\DK_District;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Record_Current;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DKClientRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_Client_User;

        $this->view_blade_403 = env('TEMPLATE_DK_CLIENT').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_CLIENT').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_client_staff")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_client_staff")->user();
            $this->me->load('client_er');
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 返回（后台）主页视图
    public function view_admin_index()
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        $the_date  = date('Y-m-d');
        $the_date_timestamp = strtotime($the_date);

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


        $order_count_for_all = DK_Pivot_Client_Delivery::where('client_id',$me->client_id)
            ->count("*");
        $order_count_for_month = DK_Pivot_Client_Delivery::where('client_id',$me->client_id)
            ->whereBetween('created_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->count("*");
        $order_count_for_today = DK_Pivot_Client_Delivery::where('client_id',$me->client_id)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$the_date)
            ->count("*");
        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_month'] = $order_count_for_month;
        $return['order_count_for_today'] = $order_count_for_today;


        // 工单统计
        $query = DK_Order::select('id');

        // 本月每日工单量
        $query_this_month = DK_Pivot_Client_Delivery::select('id','created_at')
            ->where('client_id',$me->client_id)
            ->whereBetween('created_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日工单量
        $query_last_month = DK_Pivot_Client_Delivery::select('id','created_at')
            ->where('client_id',$me->client_id)
            ->whereBetween('created_at',[$last_month_start_timestamp,$last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as sum
                "));

        $statistics_order_this_month_data = $query_this_month->get()->keyBy('day');
        $return['statistics_order_this_month_data'] = $statistics_order_this_month_data;

        $statistics_order_last_month_data = $query_last_month->get()->keyBy('day');
        $return['statistics_order_last_month_data'] = $statistics_order_last_month_data;


        ;

        // 统计
        $daily_total = DK_Client_Finance_Daily::select('*')
            ->select(DB::raw("
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_of_total_daily_cost
                "))
            ->where('client_id',$me->client_id)
            ->first();
        $funds_recharge_total = floatval($me->client_er->funds_recharge_total);
        $funds_consumption_total = floatval($daily_total->total_of_total_daily_cost);
        $funds_balance = floatval($funds_recharge_total - $funds_consumption_total);

        $return['funds_recharge_total'] = format_number($funds_recharge_total);
        $return['funds_consumption_total'] = format_number($funds_consumption_total);
        $return['funds_balance'] = format_number($funds_balance);

        $department_list = DK_Client_Department::select('id','name')
            ->where('client_id',$me->client_id)
            ->get();
        $return['department_list'] = $department_list;

        $staff_list = DK_Client_User::select('id','username')
            ->where('client_id',$me->client_id)
            ->get();
        $return['staff_list'] = $staff_list;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.index';
        return view($view_blade)->with($return);
    }
    public function view_admin_index1()
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);

        $department_list = DK_Client_Department::select('id','name')
            ->where('client_id',$me->client_id)
            ->get();
        $return['department_list'] = $department_list;

        $staff_list = DK_Client_User::select('id','username')
            ->where('client_id',$me->client_id)
            ->whereNotIn('user_type',[0,1,9,11])
            ->get();
        $return['staff_list'] = $staff_list;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.index1';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.errors.404';
        return view($view_blade);
    }



    public function view_data_voice_record($post_data)
    {
        $record_id = $post_data['record_id'];

        $order = DK_Order::where('call_record_id',$record_id)->orderBy("id", "desc")->first();
        if($order)
        {
            $call_record = DK_CC_Call_Record::find($record_id);
            if($call_record)
            {
                $serverFrom = $call_record['serverFrom_name'];
                if($serverFrom == 'FNJ')
                {
                    $server_http = 'http://feiniji.cn';
                }
                else if($serverFrom == 'call-01')
                {
                    $server_http = 'http://call01.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-02')
                {
                    $server_http = 'http://call02.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-03')
                {
                    $server_http = 'http://call03.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-04')
                {
                    $server_http = 'http://call04.zlyx.jjccyun.cn';
                }
                else
                {
                    $server_http = 'http://feiniji.cn';
                }

                $record_file_address = $server_http . $call_record->recordFile;


                $ch = curl_init($record_file_address);
                curl_setopt_array($ch, [
                    CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                    CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                    CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                    CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($statusCode === 200)
                {
                    $view_data['record_file_address'] = $record_file_address;
                    $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.data.voice-record';
                    return view($view_blade)->with($view_data);
                }
            }
            else
            {
                $call_record_current = DK_CC_Call_Record_Current::find($record_id);

                $serverFrom = $call_record_current['serverFrom_name'];
                if($serverFrom == 'FNJ')
                {
                    $server_http = 'http://feiniji.cn';
                }
                else if($serverFrom == 'call-01')
                {
                    $server_http = 'http://call01.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-02')
                {
                    $server_http = 'http://call02.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-03')
                {
                    $server_http = 'http://call03.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-04')
                {
                    $server_http = 'http://call04.zlyx.jjccyun.cn';
                }
                else
                {
                    $server_http = 'http://feiniji.cn';
                }

                $record_file_address = $server_http . $call_record_current->recordFile;
                $ch = curl_init($record_file_address);
                curl_setopt_array($ch, [
                    CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                    CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                    CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                    CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($statusCode === 200)
                {
                    $view_data['record_file_address'] = $record_file_address;
                }
                $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.data.voice-record';
                return view($view_blade)->with($view_data);

            }
        }
        else
        {
            $call_record_current = DK_CC_Call_Record_Current::find($record_id);

            $serverFrom = $call_record_current['serverFrom_name'];
            if($serverFrom == 'FNJ')
            {
                $server_http = 'http://feiniji.cn';
            }
            else if($serverFrom == 'call-01')
            {
                $server_http = 'http://call01.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-02')
            {
                $server_http = 'http://call02.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-03')
            {
                $server_http = 'http://call03.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-04')
            {
                $server_http = 'http://call04.zlyx.jjccyun.cn';
            }
            else
            {
                $server_http = 'http://feiniji.cn';
            }

            $record_file_address = $server_http . $call_record_current->recordFile;
            $ch = curl_init($record_file_address);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($statusCode === 200)
            {
                $view_data['record_file_address'] = $record_file_address;
            }
            $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.data.voice-record';
            return view($view_blade)->with($view_data);

        }


    }


    public function view_data_of_order_detail($post_data)
    {
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.data.delivery-detail';

        $order_id  = isset($post_data['order_id']) ? medsci_decode($post_data['order_id'],'2024') : 0;
        if(!$order_id)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数1有误！';
            return view($view_blade)->with($view_data);
        }

        $phone  = isset($post_data['phone']) ? $post_data['phone']  : 0;
        if(!$phone)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数2有误！';
            return view($view_blade)->with($view_data);
        }

        $order = DK_Order::select(['id','client_name','client_phone','wx_id','location_city','location_district','description','recording_address_list'])->find($order_id);
        if($order)
        {
            if($order->client_phone == $phone)
            {
                if($order->recording_address_list)
                {
                    $recording_list = json_decode($order->recording_address_list);
                    $order->recording_list = $recording_list;
                    $view_data['recording_list'] = $recording_list;
                }
                $view_data['data'] = $order;
                return view($view_blade)->with($view_data);
            }
            else
            {
                $view_data['data'] = null;
                $view_data['error'] = '电话有误！';
                return view($view_blade)->with($view_data);
            }
        }
        else
        {
            $view_data['data'] = null;
            $view_data['error'] = '交付有误！';
            return view($view_blade)->with($view_data);
        }
    }


    public function view_data_of_delivery_detail($post_data)
    {
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.data.delivery-detail';

        $delivery_id  = isset($post_data['delivery_id']) ? $post_data['delivery_id']  : 0;
        if(!$delivery_id)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数1有误！';
            return view($view_blade)->with($view_data);
        }

        $phone  = isset($post_data['phone']) ? $post_data['phone']  : 0;
        if(!$phone)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数2有误！';
            return view($view_blade)->with($view_data);
        }

        $delivery = DK_Pivot_Client_Delivery::with([
            'order_er'=>function($query) {
                $query->select(['id','client_name','client_phone','wx_id','location_city','location_district','description','recording_address_list']);
        }
        ])->find($delivery_id);
        if($delivery)
        {
            if($delivery->client_phone == $phone)
            {
                if($delivery->order_er)
                {
                    $order = $delivery->order_er;
                    if($order->recording_address_list)
                    {
                        $recording_list = json_decode($order->recording_address_list);
                        $order->recording_list = $recording_list;
                        $view_data['recording_list'] = $recording_list;
                    }
                    $view_data['data'] = $order;
                    return view($view_blade)->with($view_data);
                }
            }
            else
            {
                $view_data['data'] = null;
                $view_data['error'] = '电话有误！';
                return view($view_blade)->with($view_data);
            }
        }
        else
        {
            $view_data['data'] = null;
            $view_data['error'] = '交付有误！';
            return view($view_blade)->with($view_data);
        }
    }



    // 【交付管理】返回-列表-数据
    public function query_last_delivery()
    {
        $this->get_me();
        $me = $this->me;

        $last_delivery = DK_Pivot_Client_Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                return $query->where('department_id', $me->department_id);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            })
            ->orderBy('id','desc')
            ->first();
        if($last_delivery) return response_success(['last_delivery'=>$last_delivery]);
        else return response_success([]);
    }






    /*
     * select2
     */
    // 【部门】
    public function v1_operate_for_select2_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Client_Department::select(['id','name as text'])
            ->where('client_id',$me->client_id)
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

//        if(!empty($post_data['type']))
//        {
//            $type = $post_data['type'];
//            if($type == 'manager') $query->where(['user_type'=>81]);
//            else if($type == 'supervisor') $query->where(['user_type'=>84]);
//            else $query->where(['user_type'=>81]);
//        }
//        else $query->where(['user_type'=>81]);

//        if($me->user_type == 81)
//        {
//            $query->where('department_district_id',$me->department_district_id);
//        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>'-1','text'=>'选择部门'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    // 【员工】
    public function v1_operate_for_select2_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Client_User::select(['id','username as text'])
            ->where('client_id',$me->client_id)
            ->where('user_type','!=',11)
            ->where(['user_status'=>1])
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                return $query->where('department_id', $me->department_id);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('username','like',"%$keyword%");
        }

//        if(in_array($me->user_type,[41,71,77,81,84,88]))
//        {
//            $department_district_id = $me->department_district_id;
//            $query->where('department_district_id',$department_district_id);
//        }
//
//        if(!empty($post_data['type']))
//        {
//            $type = $post_data['type'];
//            if($type == 'inspector') $query->where(['user_type'=>77]);
//        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>'-1','text'=>'选择员工'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    // 【联系渠道】
    public function v1_operate_for_select2_contact($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Client_Contact::select(['id','name as text'])
            ->where('client_id',$me->client_id)
            ->where(['item_status'=>1])
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('username','like',"%$keyword%");
        }

//        if(in_array($me->user_type,[41,71,77,81,84,88]))
//        {
//            $department_district_id = $me->department_district_id;
//            $query->where('department_district_id',$department_district_id);
//        }
//
//        if(!empty($post_data['type']))
//        {
//            $type = $post_data['type'];
//            if($type == 'inspector') $query->where(['user_type'=>77]);
//        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>'-1','text'=>'选择联系渠道'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    



    // 【交付-管理】交付列表
    public function get_datatable_delivery_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->with([
                'client_staff_er',
                'order_er'
            ])
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            })
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->where('delivered_date', $post_data['assign']);

        if(!empty($post_data['quality'])) $query->where('order_quality', $post_data['quality']);



        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,'-1']))
            {
                $query->where('client_id', $post_data['client']);
            }
        }


        //  员工
        if(isset($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $query->where('client_staff_id', $post_data['staff']);
            }
        }



        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('delivered_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        // 患者类型
        if(isset($post_data['client_type']))
        {
            if(!in_array($post_data['client_type'],[-1,'-1']))
            {
                $query->where('client_type', $post_data['client_type']);
            }
        }

        // 导出状态
        if(isset($post_data['exported_status']))
        {
            if(!in_array($post_data['exported_status'],[-1,'-1']))
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }

        // 分配状态
        if(isset($post_data['assign_status']))
        {
//            if(!in_array($post_data['assign_status'],[-1,'-1']))
//            {
//                $query->where('assign_status', $post_data['assign_status']);
//            }
            if(!in_array($post_data['assign_status'],[-1,'-1']))
            {
//                $query->where('assign_status', $post_data['assign_status']);
                if($post_data['assign_status'] == 0)
                {
                    $query->where('assign_status', 0);
                    $query->where('client_staff_id', 0);
                }
                else if($post_data['assign_status'] == 1)
                {
                    $query->where(function ($query) {
                        $query->where('assign_status', 1)->orWhere('client_staff_id', '>', 0);
                    });
                }
            }
        }

//        dd($post_data['is_api_pushed']);
        // 是否api推送
        if(isset($post_data['is_api_pushed']))
        {
            if(!in_array($post_data['is_api_pushed'],[-1,'-1']))
            {
                $query->where('is_api_pushed', $post_data['is_api_pushed']);
            }
        }


        // 区域
        if(isset($post_data['city']))
        {
            if(count($post_data['city']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_city',$post_data['city']);
                });
            }
        }
        // 区域
        if(isset($post_data['district']))
        {
            if(count($post_data['district']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_district',$post_data['district']);
                });
            }
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }

    // 【交付-管理】交付日报
    public function get_datatable_delivery_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Pivot_Client_Delivery::select('company_id','channel_id','business_id','delivered_date')
            ->where('client_id',$me->client_id)
            ->addSelect(DB::raw("
                    delivered_date as date_day,
                    DAY(delivered_date) as day,
                    count(*) as delivery_count
                "))
            ->groupBy('delivered_date')
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            });


        // 客户
        if(!empty($post_data['client']) && !in_array($post_data['client'],[-1,0]))
        {
            $query->where('client_id', $post_data['client']);
        }


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);


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
        else $query->orderBy("delivered_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }


    // 【交付-管理】导出
    public function operate_delivery_export_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'client_er'=>function($query) { $query->select('id','username'); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

//            $cellData[$k]['client_er_name'] = $v['client_er']['username'];


            if($v['order_er']['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['order_er']['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['order_er']['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['description'] = $v['order_er']['description'];

//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
//            'client_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }







    // 【工单-管理】【操作记录】返回-列表-数据
    public function v1_operate_for_delivery_item_follow_record_datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client_Follow_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->where(['delivery_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


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
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    // 【财务-管理】返回-列表-数据
    public function get_datatable_finance_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with(['creator'])
            ->where('client_id',$me->client_id);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


//        if(!empty($post_data['assign'])) $query->whereDate("assign_date", $post_data['assign']);
//        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
//        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['date']))
                {
                    $query->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
                if(!empty($post_data['assign_start']))
                {
                    $query->whereDate("assign_date", ">=", $post_data['assign_start']);
                }
                if(!empty($post_data['assign_ended']))
                {
                    $query->whereDate("assign_date", "<=", $post_data['assign_ended']);
                }
            }
            else
            {}
        }


        // 统计
        $daily_total = (clone $query)->select(DB::raw("
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_of_total_daily_cost
                "))
            ->first();
//        dd($daily_total->toArray());
//        $daily_total = $daily_total[0];


        $total_data = [];
        $total_data['id'] = '合计';
        $total_data['name'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['creator_id'] = 0;
        $total_data['channel_id'] = 0;

        $total_data['delivery_quantity'] = $daily_total->total_of_delivery_quantity;
        $total_data['delivery_quantity_of_invalid'] = $daily_total->total_of_delivery_quantity_of_invalid;
        $total_data['cooperative_unit_price'] = '--';

        $total_data['total_daily_cost'] = $daily_total->total_of_total_daily_cost;


        $total_data['created_at'] = "--";
        $total_data['description'] = "--";


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if($v->creator_id == $me->id)
            {
                $list[$k]->is_me = 1;
                $v->is_me = 1;
            }
            else
            {
                $list[$k]->is_me = 0;
                $v->is_me = 0;
            }
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }




    /*
     * 部门管理
     */
    // 【部门-管理】返回-列表-数据
    public function v1_operate_for_department_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Client_Department::select(['id','item_status','name','department_type','leader_id','superior_department_id','remark','creator_id','created_at','updated_at','deleted_at'])
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'leader'=>function($query) { $query->select(['id','username','true_name']); },
                'superior_department_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('client_id',$me->client_id);

        if(in_array($me->user_type,[41,81]))
        {
            $query->where('superior_department_id',$me->department_district_id);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 部门类型 [大区|组]
        if(!empty($post_data['department_type']))
        {
            if(!in_array($post_data['department_type'],[-1,0]))
            {
                $query->where('item_type', $post_data['department_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach($list as $k => $v)
        {
            if($v->department_type == 11)
            {
                $v->district_id = $v->id;
            }
            else if($v->department_type == 21)
            {
                $v->district_id = $v->superior_department_id;
            }

            $v->district_group_id = $v->district_id.'.'.$v->id;
        }

        return datatable_response($list, $draw, $total);
    }
    // 【部门-管理】保存数据
    public function v1_operate_for_department_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入部门名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Client_Department::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【部门】已存在，请勿重复添加！");

            $mine = new DK_Client_Department;
            $post_data["active"] = 1;
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client_Department::find($operate_id);
            if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['superior_department_id'] = $me->department_district_id;
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--department--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门-管理】获取数据
    public function v1_operate_for_department_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::with([])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【部门-管理】管理员-删除
    public function operate_department_delete_by_admin($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'item-delete-by-admin') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Department::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client_Department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门-管理】管理员-恢复
    public function operate_department_restore_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
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

        $operate = $post_data["operate"];
        if($operate != 'item-restore-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Department::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_Client_Department--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门-管理】管理员-彻底删除
    public function operate_department_delete_permanently_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-delete-permanently-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Department::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_Client_Department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【部门-管理】管理员-启用
    public function operate_department_enable_by_admin($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'item-enable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Department::find($id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 1;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门-管理】管理员-禁用
    public function operate_department_disable_by_admin($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'item-disable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Department::find($id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * 员工管理
     */
    // 【员工-员工管理】返回-列表-数据
    public function v1_operate_for_staff_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::withTrashed()->select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'department_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->whereIn('user_category',[11])
            ->where('client_id',$me->client_id)
            ->where('id','!=',$me->id);

        if($me->user_type == 11)
        {
            $query->whereIn('user_type',[41,61,66,71,77,81,84,88]);
        }
        else if($me->user_type == 61)
        {
            $query->whereIn('user_type',[66]);
        }
        else if($me->user_type == 41)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[71,77,81,84,88]);
        }
        else if($me->user_type == 71)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[77]);
        }
        else if($me->user_type == 81)
        {
            $query->where('department_id',$me->department_id)
                ->whereIn('user_type',[84,88]);
        }
        else if($me->user_type == 84)
        {
            $query->where('department_id',$me->department_id)
                ->whereIn('user_type',[88]);
        }
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['mobile'])) $query->where('mobile', $post_data['mobile']);


        // 部门-大区
        if(!empty($post_data['department']))
        {
            if(!in_array($post_data['department'],[-1,0,'-1','0']))
            {
                $query->where('department_id', $post_data['department']);
            }
        }

        // 员工类型
        if(!empty($post_data['user_type']))
        {
            if(!in_array($post_data['user_type'],[-1,0]))
            {
                $query->where('user_type', $post_data['user_type']);
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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }
    // 【员工-管理】保存数据
    public function v1_operate_for_staff_item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'username.required' => '请输入用户名！',
            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
            'mobile' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,31,41,61,71,81])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_name_exist = DK_Client_User::where('username',$post_data['username'])->first();
            if($is_name_exist) return response_error([],"用户名已存在！");

            $is_mobile_exist = DK_Client_User::where('mobile',$post_data['mobile'])->first();
            if($is_mobile_exist) return response_error([],"手机号（工号）已存在！");

            $mine = new DK_Client_User;
            $post_data["user_status"] = 0;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_mobile_exist = DK_Client_User::where('mobile',$post_data['mobile'])->first();
                if($is_mobile_exist) return response_error([],"手机号（工号）重复，请更换工号再试一次！");
            }
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['department_district_id'] = $me->department_district_id;
            }
//            if($me->user_type == 81)
//            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//            }

            if($post_data["user_type"] == 71 || $post_data["user_type"] == 77)
            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//                unset($mine_data['department_district_id']);
                unset($mine_data['department_group_id']);
            }
            else if($post_data["user_type"] == 81)
            {
                unset($mine_data['department_group_id']);
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
//                    $user_ext = new DK_Client_UserExt;
//                    $user_ext_create['user_id'] = $mine->id;
//                    $bool_2 = $user_ext->fill($user_ext_create)->save();
//                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
                }

                // 头像
                if(!empty($post_data["portrait"]))
                {
                    // 删除原图片
                    $mine_portrait_img = $mine->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_resource_path($mine_portrait_img)))
                    {
                        unlink(storage_resource_path($mine_portrait_img));
                    }

//                    $result = upload_storage($post_data["portrait"]);
//                    $result = upload_storage($post_data["portrait"], null, null, 'assign');
                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$mine->id,'dk/unique/portrait_for_user','');
                    if($result["result"])
                    {
                        $mine->portrait_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--portrait_img--file--fail");
                }
                else
                {
                    if($operate == 'create')
                    {
                        $portrait_path = "dk/unique/portrait_for_user/".date('Y-m-d');
                        if (!is_dir(storage_resource_path($portrait_path)))
                        {
                            mkdir(storage_resource_path($portrait_path), 0777, true);
                        }
                        copy(storage_resource_path("materials/portrait/user0.jpeg"), storage_resource_path($portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg"));
                        $mine->portrait_img = $portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg";
                        $mine->save();
                    }
                }

            }
            else throw new Exception("insert--user--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】获取数据
    public function v1_operate_for_staff_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_User::with([
                'department_er'=>function($query) { $query->select(['id','name']); }
            ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【员工-管理】管理员-删除
    public function operate_staff_delete_by_admin($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'item-delete-by-admin') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client_User--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】管理员-恢复
    public function operate_staff_restore_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
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

        $operate = $post_data["operate"];
        if($operate != 'item-restore-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能恢复你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_Client_User--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】管理员-彻底删除
    public function operate_staff_delete_permanently_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-delete-permanently-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_Client_User--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【员工-管理】管理员-重置密码
    public function operate_staff_password_reset_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-password-reset-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode('12345678');
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】管理员-修改密码
    public function operate_staff_password_change_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'password.required' => '请输入密码！',
            'password_confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'password' => 'required',
            'password_confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        if($operate != 'item-password-change-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["password"];
        $confirm = $post_data["password_confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode($password);
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【员工-管理】管理员-启用
    public function operate_staff_enable_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-enable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->user_status = 1;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】管理员-禁用
    public function operate_staff_disable_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'user_id.required.',
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


        $operate = $post_data["operate"];
        if($operate != 'item-disable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->user_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【员工-管理】管理员-晋升
    public function operate_staff_promote_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-promote-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->user_type = 84;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【员工-管理】管理员-降职
    public function operate_staff_demote_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'user_id.required.',
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


        $operate = $post_data["operate"];
        if($operate != 'item-demote-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,41])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_User::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->user_type = 88;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }






    // 【交付管理】批量-分配状态
    public function operate_bulk_assign_status($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'assign_status.required' => 'assign_status.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'assign_status' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'bulk-assign-status') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
        $assign_status = $post_data["assign_status"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['assign_status'] = $assign_status;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $mine = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");


                $before = $mine->$assign_status;

                $mine->assign_status = $assign_status;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "assign_status";

                    $record_data["before"] = $before;
                    $record_data["after"] = $assign_status;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("DK_Client_Record--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-分配
    public function operate_bulk_assign_staff($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'staff_id.required' => 'staff_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'staff_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'bulk-assign-staff') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,84])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
        $client_staff_id = $post_data["staff_id"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['client_staff_id'] = $client_staff_id;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $mine = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");

                $before = $mine->client_staff_id;

                $mine->client_staff_id = $client_staff_id;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "client_staff_id";

                    $record_data["before"] = $before;
                    $record_data["after"] = $client_staff_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-API-推送
    public function operate_bulk_api_push($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'bulk-api-push') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);


        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $url = "https://qw-openapi-tx.dustess.com/auth/v1/access_token/token";

        $curl_data['ClientID'] = env('API_SCRM_ClientID');
        $curl_data['ClientSecret'] = env('API_SCRM_ClientSecret');
        $curl_data = json_encode($curl_data);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data); // post的变量
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            return response_fail([],'token请求失败');
        }
        else
        {
            $result = json_decode($result);
            if($result->success)
            {
                $token = $result->data->accessToken;
            }
        }
        curl_close($ch);


        if(!empty($token))
        {
            $delivery_list = DK_Pivot_Client_Delivery::withTrashed()
                ->with('order_er')
                ->whereIn('id',$ids_array)->get();
//        dd($delivery_list->toArray());

            $customer_list = [];
            foreach($delivery_list as $key => $item)
            {
                if($item->is_api_pushed == 0)
                {
                    $customer = [];

                    $customer['source'] = "2r4";

                    $customer['pool'] = env('API_SCRM_Pool');
                    $customer['remark'] = $item->order_er->client_name;
                    $customer['prov_city'] = $item->order_er->location_city.'-'.$item->order_er->location_district;


                    $mobile['type'] = "mobile";
                    $mobile['display'] = "手机号";
                    $mobile['tel'] = $item->order_er->client_phone;
                    $customer['mobiles'][] = $mobile;

                    if(!empty($item->order_er->wx_id))
                    {
                        $wx['type'] = "wx_id";
                        $wx['display'] = "微信号";
                        $wx['tel'] = $item->order_er->wx_id;
                        $customer['mobiles'][] = $wx;
                    }

                    $customer['description'] = $item->order_er->description;

                    // 自定义字段
                    $custom_fields = [];

                    $delivery_time['id'] = 'delivery_time';
                    $delivery_time['type'] = 'text';
                    $delivery_time['string_value'] = $item->created_at->format('Y-m-d');
                    $custom_fields[] = $delivery_time;

                    $teeth_count['id'] = 'teeth_count';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $teeth_count['id'] = 'field1';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $customer['custom_fields'] = $custom_fields;

                    $customer['description'] = $item->order_er->description;

                    $customer_list[] = $customer;
                }
            }


            if(count($customer_list) > 0)
            {
                $api_push_data['customer_list'] = $customer_list;
                $api_push_data_json = json_encode($api_push_data);

                $push_url = "https://qw-openapi-tx.dustess.com/customer/v1/batchAddCustomer?accessToken=".$token;

                $push_ch = curl_init();
                curl_setopt($push_ch, CURLOPT_URL, $push_url);
                curl_setopt($push_ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
                curl_setopt($push_ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($push_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($push_ch, CURLOPT_POST, true); // post数据
                curl_setopt($push_ch, CURLOPT_POSTFIELDS, $api_push_data_json); // post的变量
                $push_result = curl_exec($push_ch);
                if(curl_errno($push_ch))
                {
                    return response_fail([],'api推送请求失败！');
                }
                else
                {
                    $push_result_decode = json_decode($push_result);
                    if($push_result_decode->success)
                    {
                    }
                    else
                    {
                        return response_fail(['data'=>$push_result],'推送数据失败！');
                    }
                }
                curl_close($push_ch);
            }
            else return response_fail(['count'=>count($customer_list)],'工单已推送过，本次未推送数据！');

        }
        else return response_fail([],'token不存在！');


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_update['is_api_pushed'] = 1;
            $delivered_update['is_api_pusher_id'] = $me->id;
            $delivered_update['is_api_pushed_at'] = time();
            $bool = DK_Pivot_Client_Delivery::withTrashed()->whereIn('id',$ids_array)
                ->update($delivered_update);
            if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["operate_object"] = 91;
                $record_data["operate_category"] = 111;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "ids";

                $record_data["title"] = $ids;
                $record_data["content"] = $push_result;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

//            foreach($ids_array as $key => $id)
//            {
//                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
//                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");
//
//
////                $before = $item->client_staff_id;
//
//                $item->is_api_pushed = 1;
//                $bool = $item->save();
//                if(!$bool) throw new Exception("item--update--fail");
//                else
//                {
////                    $record = new DK_Client_Record;
////
////                    $record_data["ip"] = Get_IP();
////                    $record_data["record_object"] = 21;
////                    $record_data["record_category"] = 11;
////                    $record_data["record_type"] = 1;
////                    $record_data["creator_id"] = $me->id;
////                    $record_data["order_id"] = $id;
////                    $record_data["operate_object"] = 91;
////                    $record_data["operate_category"] = 99;
////                    $record_data["operate_type"] = 1;
////                    $record_data["column_name"] = "client_staff_id";
////
////                    $record_data["before"] = $before;
////                    $record_data["after"] = $client_staff_id;
////
////                    $bool_1 = $record->fill($record_data)->save();
////                    if(!$bool_1) throw new Exception("insert--record--fail");
//                }
//
//            }


            DB::commit();
            return response_success(['count'=>count($customer_list)]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }







    // 【工单-管理】字段修改
    public function v1_operate_for_user_field_set($post_data)
    {
        $messages = [
            'operate_category.required' => 'operate_category.required.',
            'column_key.required' => 'column_key.required.',
            'column_value.required' => 'column_value.required.',
        ];
        $v = Validator::make($post_data, [
            'operate_category' => 'required',
            'column_key' => 'required',
            'column_value' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate_category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
//        $id = $post_data["item-id"];
//        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

//        $operate_type = $post_data["operate-type"];

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");



        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == 'is_take_order')
            {
                if(($column_value == 1) && ($me->is_take_order_date != date('Y-m-d')))
                {
                    $me->is_take_order_date = date('Y-m-d');
                    $me->is_take_order_today = 0;
                }
                $me->is_take_order_datetime = date('Y-m-d H:i:s');
            }
            $me->$column_key = $column_value;
            $bool = $me->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");
            else
            {
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【工单-管理】字段修改
    public function v1_operate_for_parent_client_field_set($post_data)
    {
        $messages = [
            'operate_category.required' => 'operate_category.required.',
            'column_key.required' => 'column_key.required.',
            'column_value.required' => 'column_value.required.',
        ];
        $v = Validator::make($post_data, [
            'operate_category' => 'required',
            'column_key' => 'required',
            'column_value' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate_category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
//        $id = $post_data["item-id"];
//        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

//        $operate_type = $post_data["operate-type"];

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");


        $parent_client = DK_Client::find($me->client_id);
        if(!$parent_client) return response_error([],"所属客户不存在！");

        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $parent_client->$column_key = $column_value;
            $bool = $parent_client->save();
            if(!$bool) throw new Exception("DK_Client--update--fail");
            else
            {
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【交付管理】自动-分配
    public function v1_operate_for_delivery_automatic_dispatching_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'ids.required' => 'ids.required.',
//            'staff_id.required' => 'staff_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'ids' => 'required',
//            'staff_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'automatic-dispatching-by-admin') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
//        $client_staff_id = $post_data["staff_id"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        $staff_list = DK_Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
            ->where('client_id',$me->client_id)
            ->where('is_take_order',1)
            ->where('is_take_order_date',date('Y-m-d'))
            ->orderBy('is_take_order_datetime','asc')
            ->get();

        $delivery_list = DK_Pivot_Client_Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->where('client_staff_id',0)
            ->get();

        $staff_list = $staff_list->values(); // 重置索引确保从0开始
        $staffCount = $staff_list->count();
        if($staffCount == 0) return response_error([],"暂时没有接单员工！");
        $deliveryCount = $delivery_list->count();
        if($deliveryCount == 0) return response_error([],"暂时没有未分配工单！");

        $clientId = $me->client_id;

        // 使用原子锁避免并发冲突
        // 创建原子锁（设置最大等待时间和自动释放时间）
        $lock = Cache::lock("client:{$clientId}:assign_lock", 10); // 锁最多持有10秒
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
            $lastIndex = Cache::get("client:{$clientId}:last_staff_index", 0);
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
                "client:{$clientId}:last_staff_index",
                $newIndex,
                now()->addHours(10)
            );


//            $num = 0;
//            foreach($delivery_list as $key => $delivery)
//            {
//                $bool = $mine->save();
//                if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
//                else
//                {
//                    $record = new DK_Client_Record;
//
//                    $record_data["ip"] = Get_IP();
//                    $record_data["record_object"] = 21;
//                    $record_data["record_category"] = 11;
//                    $record_data["record_type"] = 1;
//                    $record_data["creator_id"] = $me->id;
//                    $record_data["order_id"] = $id;
//                    $record_data["operate_object"] = 91;
//                    $record_data["operate_category"] = 99;
//                    $record_data["operate_type"] = 1;
//                    $record_data["column_name"] = "client_staff_id";
//
//                    $record_data["before"] = $before;
//                    $record_data["after"] = $client_staff_id;
//
//                    $bool_1 = $record->fill($record_data)->save();
//                    if(!$bool_1) throw new Exception("DK_Client_Record--insert--fail");
//                }
//            }


            DB::commit();
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
        finally
        {
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
        }

    }




    /*
     * 联系渠道管理
     */
    // 【联系渠道-员工管理】返回-列表-数据
    public function v1_operate_for_contact_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Client_Contact::select(['id','item_status','name','contact_type','client_staff_id','remark','creator_id','created_at','updated_at','deleted_at'])
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 部门类型 [大区|组]
        if(!empty($post_data['contact_type']))
        {
            if(!in_array($post_data['contact_type'],[-1,0,'-1','0']))
            {
                $query->where('contact_type', $post_data['contact_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }
    // 【联系渠道-管理】保存数据
    public function v1_operate_for_contact_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入联系渠道名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Client_Contact::select('id')->where('name',$post_data["name"])->where('client_id',$me->client_id)->count();
            if($is_exist) return response_error([],"该【名称】已存在，请勿重复添加！");

            $mine = new DK_Client_Contact;
            $post_data["active"] = 1;
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client_Contact::find($operate_id);
            if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;
            unset($mine_data['operate']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("DK_Client_Contact--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【联系渠道-管理】获取数据
    public function v1_operate_for_contact_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Contact::with([
            'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【联系渠道-管理】管理员-删除
    public function operate_contact_delete_by_admin($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'item-delete-by-admin') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Contact::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client_Contact--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【联系渠道-管理】管理员-恢复
    public function operate_contact_restore_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
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

        $operate = $post_data["operate"];
        if($operate != 'item-restore-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Contact::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能恢复你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_Client_Contact--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【联系渠道-管理】管理员-彻底删除
    public function operate_contact_delete_permanently_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-delete-permanently-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Contact::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_Client_Contact--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【联系渠道-管理】管理员-启用
    public function operate_contact_enable_by_admin($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-enable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Contact::find($id);
        if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->user_type < $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 1;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_Contact--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【联系渠道-管理】管理员-禁用
    public function operate_contact_disable_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'user_id.required.',
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


        $operate = $post_data["operate"];
        if($operate != 'item-disable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Contact::find($id);
        if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client_Contact--update--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    // 【交付-管理】返回-列表-数据
    public function v1_operate_for_delivery_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->with([
                'order_er'=>function($query) {
                    $query->with(['creator'=>function($query) { $query->select('id','username'); }]);
                },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
                'client_contact_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            })
            ->when((in_array($me->user_type,[81,84]) && $me->client_er->user_category != 31), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->where('delivered_date', $post_data['assign']);



        // 交付客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,'-1']))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 上门状态
        if(isset($post_data['is_wx']))
        {
            if(in_array($post_data['is_wx'],[0,1]))
            {
//                if($post_data['is_wx'] == 0) $query->where('client_contact_id', 0);
//                else if($post_data['is_wx'] == 1) $query->where('client_contact_id', '>', 0);
                if($post_data['is_wx'] == 0) $query->where('is_wx', 0);
                else if($post_data['is_wx'] == 1) $query->where('is_wx', 1);
            }
        }

        // 联系渠道
        if(isset($post_data['contact']))
        {
            if(count($post_data['contact']) > 0)
            {
                $query->whereIn('client_contact_id',$post_data['contact']);
            }
        }


        // 回访状态
        if(isset($post_data['is_callback']))
        {
            if(!in_array($post_data['is_callback'],[-1,'-1']))
            {
                $query->where('is_callback', $post_data['is_callback']);
            }
        }
        // 回访时间
        if(!empty($post_data['callback_date'])) $query->where('callback_date', $post_data['callback_date']);


        // 上门状态
        if(isset($post_data['is_come']))
        {
            if(!in_array($post_data['is_come'],[-1,'-1']))
            {
                $query->where('is_come', $post_data['is_come']);
            }
        }
        // 上门时间
        if(!empty($post_data['come_date'])) $query->where('come_date', $post_data['come_date']);



        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('delivered_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        // 患者类型
        if(isset($post_data['client_type']))
        {
            if(!in_array($post_data['client_type'],[-1,'-1']))
            {
                $query->where('client_type', $post_data['client_type']);
            }
        }

        // 导出状态
        if(isset($post_data['exported_status']))
        {
            if(!in_array($post_data['exported_status'],[-1,'-1']))
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }

        // 分配状态
        if(isset($post_data['assign_status']))
        {
            if(!in_array($post_data['assign_status'],[-1,'-1']))
            {
//                $query->where('assign_status', $post_data['assign_status']);
                if($post_data['assign_status'] == 0)
                {
                    $query->where('client_staff_id', 0);
                }
                else if($post_data['assign_status'] == 1)
                {
                    $query->where('client_staff_id', '>', 0);
                }
            }
        }

//        dd($post_data['is_api_pushed']);
        // 是否api推送
        if(isset($post_data['is_api_pushed']))
        {
            if(!in_array($post_data['is_api_pushed'],[-1,'-1']))
            {
                $query->where('is_api_pushed', $post_data['is_api_pushed']);
            }
        }


        // 区域
        if(isset($post_data['city']))
        {
            if(count($post_data['city']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_city',$post_data['city']);
                });
            }
        }
        // 区域
        if(isset($post_data['district']))
        {
            if(count($post_data['district']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_district',$post_data['district']);
                });
            }
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }
    // 【交付-管理】获取数据
    public function v1_operate_for_delivery_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Pivot_Client_Delivery::with([
            'client_contact_er'=>function($query) { $query->select(['id','name']); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }

    // 【交付-管理】保存数据
    public function v1_operate_for_delivery_item_customer_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'is_wx.required' => '请选择是否加微信！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'is_wx' => 'required',
            'is_wx' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Pivot_Client_Delivery::with([
            'client_contact_er'=>function($query) { $query->select(['id','name']); }
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');


        $follow_update = [];

        $is_wx = $post_data["is_wx"];
        if($is_wx != $mine->is_wx)
        {
            $update['field'] = 'is_wx';
            $update['before'] = $mine->is_wx;
            $update['after'] = $is_wx;
            $follow_update[] = $update;

            $mine->is_wx = $is_wx;
        }

        $customer_remark = $post_data["customer_remark"];
        if($customer_remark != $mine->customer_remark)
        {
            $update['field'] = 'customer_remark';
            $update['before'] = $mine->customer_remark;
            $update['after'] = $customer_remark;
            $follow_update[] = $update;

            $mine->customer_remark = $customer_remark;
        }

        $client_contact_id = $post_data["client_contact_id"];
        $contact = DK_Client_Contact::select('id','name')->find($client_contact_id);
        if(!$contact) return response_error([],"【联系渠道】不存在，请刷新页面重试！");
        if($client_contact_id != $mine->client_contact_id)
        {
            $update['field'] = 'client_contact_id';
            if($mine->client_contact_er)
            {
                $update['before'] = $mine->client_contact_er->name;
            }
            else
            {
                $update['before'] = '';
            }
            $update['before_id'] = $mine->client_contact_id;
            $update['after'] = $contact->name;
            $update['after_id'] = $client_contact_id;
            $follow_update[] = $update;

            $mine->client_contact_id = $client_contact_id;
        }


        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 11;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = json_encode($follow_update);
        $follow_data["follow_datetime"] = $datetime;
        $follow_data["follow_date"] = $datetime;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $follow->fill($follow_data)->save();
            if($bool)
            {
//                $mine->timestamps = false;
                $mine->last_operation_datetime = $datetime;
                $mine->last_operation_date = $datetime;
                $bool_d = $mine->save();
            }
            else throw new Exception("DK_Client_Follow_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付-管理】保存数据
    public function v1_operate_for_delivery_item_callback_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'follow-datetime.required' => '请输入跟进时间！',
//            'is_come.required' => '请选择上门状态！',
            'callback-datetime.required' => '请选择回访时间！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'callback-datetime' => 'required',
//            'is_come' => 'required',
            'callback-datetime' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Pivot_Client_Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');


        $follow_update = [];

        // 回访状态
//        $is_callback = $post_data["is_callback"];
//        if($is_callback != $mine->is_callback)
//        {
//            $update['field'] = 'is_callback';
//            $update['before'] = $mine->is_callback;
//            $update['after'] = $is_callback;
//            $follow_update[] = $update;
//
//            $mine->is_callback = $is_callback;
//        }
        // 回访时间
        $callback_datetime = $post_data['callback-datetime'];
        if(!empty($callback_datetime))
        {
            $update['field'] = 'callback_datetime';
            $update['before'] = '';
            $update['after'] = $callback_datetime;
            $follow_update[] = $update;

            $mine->callback_datetime = $callback_datetime;
            $mine->callback_date = $callback_datetime;
        }
        // 回访备注
        $trade_data["description"] = $post_data['callback-description'];
        if(!empty($trade_data["description"]))
        {
            $update['field'] = 'callback_description';
            $update['before'] = '';
            $update['after'] = $trade_data["description"];
            $follow_update[] = $update;
        }



        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 21;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = json_encode($follow_update);
//        $follow_data["follow_datetime"] = $post_data['follow-datetime'];
//        $follow_data["follow_date"] = $post_data['follow-datetime'];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $follow->fill($follow_data)->save();
            if($bool)
            {
//                $mine->timestamps = false;
                $mine->last_operation_datetime = $datetime;
                $mine->last_operation_date = $datetime;
                $bool_d = $mine->save();
            }
            else throw new Exception("DK_Client_Follow_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付-管理】保存数据
    public function v1_operate_for_delivery_item_come_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'follow-datetime.required' => '请输入跟进时间！',
            'is_come.required' => '请选择上门状态！',
//            'come-datetime.required' => '请选择上门时间！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'follow-datetime' => 'required',
            'is_come' => 'required',
//            'come-datetime' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Pivot_Client_Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');


        $follow_update = [];

        // 上门状态
        $is_come = $post_data["is_come"];
        if($is_come != $mine->is_come)
        {
            $update['field'] = 'is_come';
            $update['before'] = $mine->is_come;
            $update['after'] = $is_come;
            $follow_update[] = $update;

            $mine->is_come = $is_come;
        }
        // 上门时间
        $come_datetime = $post_data['come-datetime'];
        if(!empty($come_datetime))
        {
            $update['field'] = 'come_datetime';
            $update['before'] = '';
            $update['after'] = $come_datetime;
            $follow_update[] = $update;

            $mine->come_datetime = $come_datetime;
            $mine->come_date = $come_datetime;
        }
        // 上门备注
        $trade_data["description"] = $post_data['come-description'];
        if(!empty($trade_data["description"]))
        {
            $update['field'] = 'come_description';
            $update['before'] = '';
            $update['after'] = $trade_data["description"];
            $follow_update[] = $update;
        }



        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 31;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = json_encode($follow_update);
        $follow_data["follow_datetime"] = $post_data['follow-datetime'];
        $follow_data["follow_date"] = $post_data['follow-datetime'];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $follow->fill($follow_data)->save();
            if($bool)
            {
//                $mine->timestamps = false;
                $mine->last_operation_datetime = $datetime;
                $mine->last_operation_date = $datetime;
                $bool_d = $mine->save();
            }
            else throw new Exception("DK_Client_Follow_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付-管理】保存数据
    public function v1_operate_for_delivery_item_trade_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'transaction-datetime.required' => '请输入成交时间！',
            'transaction-count.required' => '请输入成交数量！',
            'transaction-amount.required' => '请输入成交金额！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'transaction-datetime' => 'required',
            'transaction-count' => 'required',
            'transaction-amount' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $mine = DK_Pivot_Client_Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');

        $follow_update = [];


        $trade = new DK_Client_Trade_Record;

        $trade_data["trade_category"] = 1;
        $trade_data["trade_type"] = 1;
        $trade_data["client_id"] = $me->client_id;
        $trade_data["delivery_id"] = $operate_id;
        $trade_data["creator_id"] = $me->id;

        $trade_data["title"] = $post_data['transaction-title'];

        $trade_data["transaction_datetime"] = $post_data['transaction-datetime'];
        if(!empty($trade_data["transaction_datetime"]))
        {
            $update['field'] = 'transaction_datetime';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_datetime"];
            $follow_update[] = $update;
        }
        $trade_data["transaction_date"] = $post_data['transaction-datetime'];

        $trade_data["transaction_count"] = $post_data['transaction-count'];
        if(!empty($trade_data["transaction_count"]))
        {
            $update['field'] = 'transaction_count';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_count"];
            $follow_update[] = $update;
        }
        $trade_data["transaction_amount"] = $post_data['transaction-amount'];
        if(!empty($trade_data["transaction_amount"]))
        {
            $update['field'] = 'transaction_amount';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_amount"];
            $follow_update[] = $update;
        }

        $trade_data["transaction_pay_account"] = $post_data['transaction-pay-account'];
        $trade_data["transaction_receipt_account"] = $post_data['transaction-receipt-account'];
        $trade_data["transaction_order_number"] = $post_data['transaction-order-number'];

        $trade_data["description"] = $post_data['transaction-description'];
        if(!empty($trade_data["description"]))
        {
            $update['field'] = 'transaction_description';
            $update['before'] = '';
            $update['after'] = $trade_data["description"];
            $follow_update[] = $update;
        }


        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 88;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = json_encode($follow_update);
//        $follow_data["follow_datetime"] = $datetime;
//        $follow_data["follow_date"] = $datetime;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool_t = $trade->fill($trade_data)->save();
            if($bool_t)
            {
                $follow_data['custom_id'] = $trade->id;
                $bool_f = $follow->fill($follow_data)->save();
                if($bool_f)
                {
                    $trade->follow_id = $follow->id;
                    $bool_t_2 = $trade->save();
                    if(!$bool_t_2) throw new Exception("DK_Client_Trade_Record--update--fail");

//                    $mine = DK_Pivot_Client_Delivery::lockForUpdate()->withTrashed()->find($operate_id);
//
////                $mine->timestamps = false;
//                    $mine->transaction_num += 1;
//                    $mine->transaction_count += $post_data['transaction-count'];
//                    $mine->transaction_amount += $post_data['transaction-amount'];
//                    $mine->transaction_datetime = $post_data['transaction-datetime'];
//                    $mine->transaction_date = $post_data['transaction-datetime'];
                    $mine->last_operation_datetime = $datetime;
                    $mine->last_operation_date = $datetime;
                    $bool_d = $mine->save();
                    if(!$bool_d) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
                }
                else throw new Exception("DK_Client_Follow_Record--insert--fail");
            }
            else throw new Exception("DK_Client_Trade_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付-管理】保存数据
    public function v1_operate_for_delivery_item_follow_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'follow-datetime.required' => '请输入跟进时间！',
//            'name.required' => '请输入联系渠道名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'follow-datetime' => 'required',
//            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $mine = DK_Pivot_Client_Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');


        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 1;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = $post_data['follow-description'];
        $follow_data["follow_datetime"] = $post_data['follow-datetime'];
        $follow_data["follow_date"] = $post_data['follow-datetime'];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $follow->fill($follow_data)->save();
            if($bool)
            {
//                $mine->timestamps = false;
                $mine->follow_latest_description = $post_data['follow-description'];
                $mine->follow_datetime = $post_data['follow-datetime'];
                $mine->follow_date = $post_data['follow-datetime'];
                $mine->last_operation_datetime = $datetime;
                $mine->last_operation_date = $datetime;
                $bool_d = $mine->save();
                if(!$bool_d) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
            }
            else throw new Exception("DK_Client_Follow_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【交付-管理】质量评价
    public function v1_operate_for_delivery_item_quality_evaluate($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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






        // 判断参数是否合法
        $operate = $post_data["operate"];
        if($operate != 'delivery-quality-evaluate') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $order_quality = $post_data["order_quality"];
        if(!in_array($order_quality,config('info.order_quality'))) return response_error([],"质量结果非法！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84,88])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        if($item->client_id != $me->client_id) return response_error([],"该【工单】不是你的，你不能操作！");
        if(in_array($me->user_type,[88]) && $item->client_staff_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");


        $before = $item->order_quality;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->order_quality = $order_quality;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 93;
                $record_data["operate_type"] = 1;

                $record_data["before"] = $before;
                $record_data["after"] = $order_quality;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Client_Record--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * 联系渠道管理
     */
    // 【交易-员工管理】返回-列表-数据
    public function v1_operate_for_trade_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Client_Trade_Record::select('*')
            ->withTrashed()
            ->with([
                'delivery_er',
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'deleter_er'=>function($query) { $query->select(['id','username','true_name']); },
                'authenticator_er'=>function($query) { $query->select(['id','username','true_name']); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('creator_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('creator_id', $me->id);
            });


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        // 类型 [|]
        if(!empty($post_data['trade_type']))
        {
            if(!in_array($post_data['trade_type'],[-1,0,'-1','0']))
            {
                $query->where('trade_type', $post_data['trade_type']);
            }
        }
        // 是否确认 [|]
        if(!empty($post_data['is_confirmed']))
        {
            if(!in_array($post_data['is_confirmed'],[-1,'-1']))
            {
                $query->where('is_confirmed', $post_data['is_confirmed']);
            }
        }




        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('transaction_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('transaction_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('transaction_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('transaction_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }
    // 【交易-管理】保存数据
    public function v1_operate_for_trade_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入联系渠道名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Client_Contact::select('id')->where('name',$post_data["name"])->where('client_id',$me->client_id)->count();
            if($is_exist) return response_error([],"该【名称】已存在，请勿重复添加！");

            $mine = new DK_Client_Contact;
            $post_data["active"] = 1;
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client_Trade_Record::find($operate_id);
            if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;
            unset($mine_data['operate']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("DK_Client_Contact--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交易-管理】获取数据
    public function v1_operate_for_trade_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Trade_Record::with([
            'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【交易-管理】管理员-删除
    public function v1_operate_for_trade_item_delete($post_data)
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


        $operate = $post_data["operate"];
        if($operate != 'trade-item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Trade_Record::find($item_id);
        if(!$mine) return response_error([],"该【交易】不存在，刷新页面重试！");

        if($mine->is_confirmed == 1) return response_error([],"该【交易】已确认，不能删除！");

        $delivery = DK_Pivot_Client_Delivery::find($mine->delivery_id);
        if(!$delivery) return response_error([],"该【工单】不存在，刷新页面重试！");

//        if($mine->creator_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
//        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");
        if($me->user_type == 88 && $mine->creator_id != $me->id) return response_error([],"你没有权限删除其他人的交易！");
        if(in_array($me->user_type,[81,84]))
        {
            $staff = DK_Client_User::find($mine->creator_id);
            if($staff->department_id != $me->department_id) return response_error([],"你没有权限删除其他团队的交易！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->deleter_id = $me->id;
            $bool = $mine->save();  // 先更新
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client_Trade_Record--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【交易-管理】管理员-删除
    public function v1_operate_for_trade_item_confirm($post_data)
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



        $datetime = date('Y-m-d H:i:s');
        $time = time();

        $operate = $post_data["operate"];
        if($operate != 'trade-item-confirm') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Trade_Record::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【交易】不存在，刷新页面重试！");

        if($mine->is_confirmed == 1) return response_error([],"该【交易】已确认，不能重复确认！");

        $delivery = DK_Pivot_Client_Delivery::find($mine->delivery_id);
        if(!$delivery) return response_error([],"该【工单】不存在，刷新页面重试！");

//        if($mine->creator_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
//        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");
//        if($me->user_type == 88 && $mine->creator_id != $me->id) return response_error([],"你没有权限删除其他人的交易！");
        if(in_array($me->user_type,[81,84]))
        {
            $staff = DK_Client_User::find($mine->creator_id);
            if($staff->department_id != $me->department_id) return response_error([],"你没有权限确认其他团队的交易！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $mine->timestamps = false;
            $mine->is_confirmed = 1;
            $mine->authenticator_id = $me->id;
            $mine->confirmed_at = $time;
            $bool = $mine->save();
            if($bool)
            {

                $the_delivery = DK_Pivot_Client_Delivery::lockForUpdate()->withTrashed()->find($mine->delivery_id);

//                $mine->timestamps = false;
                $the_delivery->transaction_num += 1;
                $the_delivery->transaction_count += $mine->transaction_count;
                $the_delivery->transaction_amount += $mine->transaction_amount;
                $the_delivery->last_operation_datetime = $mine->transaction_datetime;
                $the_delivery->transaction_date = $mine->transaction_date;
                $bool_d = $the_delivery->save();
//                $mine->last_operation_datetime = $datetime;
//                $mine->last_operation_date = $datetime;
                if(!$bool_d) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
            }
            else
            {
                throw new Exception("DK_Client_Trade_Record--update--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }









    // 【生产-统计】坐席排名
    public function v1_operate_for_get_statistic_data_of_production_staff_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Pivot_Client_Delivery::select('client_staff_id','delivered_date')
            ->addSelect(DB::raw("
                    count(*) as delivery_count_for_all,
                    count(IF(is_wx = 1, TRUE, NULL)) as delivery_count_for_wx,
                    count(IF(is_come = 9, TRUE, NULL)) as delivery_count_for_come_9,
                    count(IF(is_come = 11, TRUE, NULL)) as delivery_count_for_come_11,
                    sum(transaction_num) as delivery_count_for_transaction_num,
                    sum(transaction_count) as delivery_count_for_transaction_count,
                    sum(transaction_amount) as delivery_count_for_transaction_amount
                "))
            ->where('client_id', $me->client_id)
            ->groupBy('client_staff_id');

        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $query_order->where('project_id', $project_id);
            }
        }

        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0,'-1','0']))
            {
                $query_order->where('department_district_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0,'-1','0']))
            {
                $query_order->where('department_group_id', $post_data['department_group']);
            }
        }


        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('delivered_date',$the_date);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

//            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_order->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order->where('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order->where('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }

        $query_order = $query_order->get()->keyBy('client_staff_id')->toArray();
//        dd($query_order);


        $query = DK_Client_User::select(['id','user_type','username','true_name','client_id','department_id'])
            ->with([
                'department_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('client_id', $me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('id', $me->id);
            })
            ->whereIn('user_type',[81,84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0,'-1','0']))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0,'-1','0']))
            {
                $query->where('department_group_id', $post_data['department_group']);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->delivery_count_for_all = $query_order[$v->id]['delivery_count_for_all'];
                $list[$k]->delivery_count_for_wx = $query_order[$v->id]['delivery_count_for_wx'];
                $list[$k]->delivery_count_for_come_9 = $query_order[$v->id]['delivery_count_for_come_9'];
                $list[$k]->delivery_count_for_come_11 = $query_order[$v->id]['delivery_count_for_come_11'];
                $list[$k]->delivery_count_for_transaction_num = $query_order[$v->id]['delivery_count_for_transaction_num'];
                $list[$k]->delivery_count_for_transaction_count = $query_order[$v->id]['delivery_count_for_transaction_count'];
                $list[$k]->delivery_count_for_transaction_amount = $query_order[$v->id]['delivery_count_for_transaction_amount'];
            }
            else
            {
                $list[$k]->delivery_count_for_all = 0;
                $list[$k]->delivery_count_for_wx = 0;
                $list[$k]->delivery_count_for_come_9 = 0;
                $list[$k]->delivery_count_for_come_11 = 0;
                $list[$k]->delivery_count_for_transaction_num = 0;
                $list[$k]->delivery_count_for_transaction_count = 0;
                $list[$k]->delivery_count_for_transaction_amount = 0;
            }

        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }

    // 【生产-统计】坐席日报
    public function v1_operate_for_get_statistic_data_of_production_staff_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


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


        $staff_id = $post_data['staff_id'];


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        $query_this_month = DK_Pivot_Client_Delivery::select('client_staff_id','delivered_date')
            ->where('client_staff_id',$staff_id)
            ->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('delivered_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(delivered_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(delivered_date,'%e') as day,
                    count(*) as sum
                "))
            ->addSelect(DB::raw("
                    count(*) as delivery_count_for_all,
                    count(IF(is_wx = 1, TRUE, NULL)) as delivery_count_for_wx,
                    count(IF(is_come = 9, TRUE, NULL)) as delivery_count_for_come_9,
                    count(IF(is_come = 11, TRUE, NULL)) as delivery_count_for_come_11,
                    sum(transaction_num) as delivery_count_for_transaction_num,
                    sum(transaction_count) as delivery_count_for_transaction_count,
                    sum(transaction_amount) as delivery_count_for_transaction_amount
                    
                "))
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            })
            ->orderBy("delivered_date", "desc");

        $total = $query_this_month->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_this_month->get();


        $total_data = [];
        $total_data['client_staff_id'] = $staff_id;
        $total_data['date_day'] = '统计';
        $total_data['staff_count'] = 0;
        $total_data['delivery_count_for_all'] = 0;
        $total_data['delivery_count_for_wx'] = 0;
        $total_data['delivery_count_for_come_9'] = 0;
        $total_data['delivery_count_for_come_11'] = 0;
        $total_data['delivery_count_for_transaction_num'] = 0;
        $total_data['delivery_count_for_transaction_count'] = 0;
        $total_data['delivery_count_for_transaction_amount'] = 0;


        foreach ($list as $k => $v)
        {
            $total_data['delivery_count_for_all'] += $v->delivery_count_for_all;
            $total_data['delivery_count_for_wx'] += $v->delivery_count_for_wx;
            $total_data['delivery_count_for_come_9'] += $v->delivery_count_for_come_9;
            $total_data['delivery_count_for_come_11'] += $v->delivery_count_for_come_11;
            $total_data['delivery_count_for_transaction_num'] += $v->delivery_count_for_transaction_num;
            $total_data['delivery_count_for_transaction_count'] += $v->delivery_count_for_transaction_count;
            $total_data['delivery_count_for_transaction_amount'] += $list[$k]->delivery_count_for_transaction_amount;
        }
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }












    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_dental_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            $cellData[$k]['order_quality'] = $v['order_quality'];

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }


//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];

            if($v['order_er']['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['order_er']['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['order_er']['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'order_quality'=>'工单质量',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>16,
                    'L'=>16,
                    'M'=>40,
                    'N'=>40,
                    'O'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_aesthetic_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }


//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['field_1'] == 1) $cellData[$k]['field_1'] = "脸部";
            else if($v['order_er']['field_1'] == 21) $cellData[$k]['field_1'] = "植发";
            else if($v['order_er']['field_1'] == 31) $cellData[$k]['field_1'] = "身体";
            else if($v['order_er']['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

//            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【医美】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30,
                    'N'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_luxury_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }

//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['order_er']['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['order_er']['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['order_er']['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['order_er']['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

//            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【二奢】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30,
                    'N'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }















    /*
     * 用户基本信息
     */
    // 【基本信息】返回视图
    public function view_my_profile_info_index()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }
    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.my-account.my-profile-info-edit';
        return view($view_blade)->with($return);
    }
    // 【基本信息】保存数据
    public function operate_my_profile_info_save($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;
            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            $bool = $me->fill($mine_data)->save();
            if($bool)
            {
                // 头像
                if(!empty($post_data["portrait_img"]))
                {
                    // 删除原文件
                    $mine_original_file = $me->portrait_img;
                    if(!empty($mine_original_file) && file_exists(storage_path("resource/" . $mine_original_file)))
                    {
                        unlink(storage_path("resource/" . $mine_original_file));
                    }

                    $result = upload_file_storage($post_data["attachment"]);
                    if($result["result"])
                    {
                        $me->portrait_img = $result["local"];
                        $me->save();
                    }
                    else throw new Exception("upload--portrait-img--file--fail");
                }

            }
            else throw new Exception("insert--item--fail");

            DB::commit();
            return response_success(['id'=>$me->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }

    // 【密码】返回修改视图
    public function view_my_account_password_change()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.my-account.my-account-password-change';
        return view($view_blade)->with($return);
    }
    // 【密码】保存数据
    public function operate_my_account_password_change_save($post_data)
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









    /*
     * select2
     */
    //
    public function operate_select2_city($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query = DK_District::select('id','district_city as text')
                ->where(['district_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query = DK_District::select('id','district_city as text')
                ->where('district_city','like',"%$keyword%")
                ->where(['district_status'=>1]);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        foreach ($list as $k => $v)
        {
            $list[$k]['id'] = $v['text'];
        }

        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择城市 '];
        array_unshift($list,$unSpecified);
//        dd($list);

        return $list;
    }
    //
    public function operate_select2_district($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_District::select(['id','district_district as text'])
                ->where(['district_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_District::select(['id','district_district as text'])->where('district_district','like',"%$keyword%")
                ->where(['district_status'=>1]);
        }

        if(!empty($post_data['district_city']))
        {
            $city = $post_data['district_city'];
            $query->where(['district_city'=>$city]);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);


        $district_array = [];
        if(count($list) > 0)
        {
            foreach ($list as $k => $v)
            {
                $district_explode_array = explode("-",$v['text']);
                foreach($district_explode_array as $key => $value)
                {
                    $district_array[] = ['id'=>$value,'text'=>$value];
                }
            }
        }
//        dd($district_array);

        if(!empty($post_data['keyword']))
        {
            $keyword = $post_data['keyword'];
            $district_filter = array_filter($district_array, function ($item) use ($keyword) {
                // 检查 id 和 text 是否包含查询关键词
                return stripos($item['id'], $keyword) !== false ||
                    stripos($item['text'], $keyword) !== false;
            });
            return array_values($district_filter);
        }
        else return $district_array;

    }








    /*
     * 部门管理
     */
    //
    public function operate_department_select2_leader($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Client_User::select(['id','username as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Client_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'manager') $query->where(['user_type'=>81]);
            else if($type == 'supervisor') $query->where(['user_type'=>84]);
            else $query->where(['user_type'=>81]);
        }
        else $query->where(['user_type'=>81]);

        if($me->user_type == 81)
        {
            $query->where('department_district_id',$me->department_district_id);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_department_select2_superior_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(empty($post_data['keyword']))
        {
            $query =DK_Client_Department::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Client_Department::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group') $query->where(['department_type'=>21]);
            else $query->where(['department_type'=>11]);
        }
        else $query->where(['department_type'=>11]);

        if($me->user_type == 81)
        {
            $query->where('id',$me->department_district_id);
        }

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }


    // 【部门管理】返回-列表-视图
    public function view_department_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $return['menu_active_of_department_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.department.department-list';
        return view($view_blade)->with($return);
    }
    // 【部门管理】返回-列表-数据
    public function get_department_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Client_Department::select(['id','item_status','name','department_type','leader_id','superior_department_id','remark','creator_id','created_at','updated_at','deleted_at'])
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'leader'=>function($query) { $query->select(['id','username','true_name']); },
                'superior_department_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('client_id',$me->client_id);

        if(in_array($me->user_type,[41,81]))
        {
            $query->where('superior_department_id',$me->department_district_id);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        // 部门类型 [大区|组]
        if(!empty($post_data['department_type']))
        {
            if(!in_array($post_data['department_type'],[-1,0]))
            {
                $query->where('item_type', $post_data['department_type']);
            }
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach($list as $k => $v)
        {
            if($v->department_type == 11)
            {
                $v->district_id = $v->id;
            }
            else if($v->department_type == 21)
            {
                $v->district_id = $v->superior_department_id;
            }

            $v->district_group_id = $v->district_id.'.'.$v->id;
        }

        return datatable_response($list, $draw, $total);
    }


    // 【部门管理】【修改记录】返回-列表-视图
    public function view_department_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Client_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.department-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【部门管理】【修改记录】返回-列表-数据
    public function get_department_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client_Record::select('*')
            ->with(['creator'])
            ->where(['record_object'=>21, 'operate_object'=>41,'item_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【部门管理】返回-添加-视图
    public function view_department_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '部门';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/department/department-list-for-all';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.department.department-edit';
        return view($view_blade)->with([
            'operate'=>'create',
            'operate_id'=>0,
            'category'=>'item',
            'type'=>$item_type,
            'item_type_text'=>$item_type_text,
            'title_text'=>$title_text,
            'list_text'=>$list_text,
            'list_link'=>$list_link,
        ]);
    }
    // 【部门管理】返回-编辑-视图
    public function view_department_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.department.department-edit';

        $item_type = 'item';
        $item_type_text = '部门';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/department/department-list-for-all';

        if($id == 0)
        {
            return view($view_blade)->with([
                'operate'=>'create',
                'operate_id'=>0,
                'category'=>'item',
                'type'=>$item_type,
                'item_type_text'=>$item_type_text,
                'title_text'=>$title_text,
                'list_text'=>$list_text,
                'list_link'=>$list_link,
            ]);
        }
        else
        {
            $mine = DK_Client_Department::with('leader')->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_DK_CLIENT').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                return view($view_blade)->with([
                    'operate'=>'edit',
                    'operate_id'=>$id,
                    'data'=>$mine,
                    'category'=>'item',
                    'type'=>$item_type,
                    'item_type_text'=>$item_type_text,
                    'title_text'=>$title_text,
                    'list_text'=>$list_text,
                    'list_link'=>$list_link,
                ]);
            }
            else return view(env('TEMPLATE_DK_CLIENT').'errors.404');
        }
    }
    // 【部门管理】保存数据
    public function operate_department_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入部门名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Client_Department::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【部门】已存在，请勿重复添加！");

            $mine = new DK_Client_Department;
            $post_data["active"] = 1;
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Client_Department::find($operate_id);
            if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['superior_department_id'] = $me->department_district_id;
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--department--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【部门管理】【文本-信息】设置-文本-类型
    public function operate_department_info_text_set($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'v-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】【时间-信息】修改-时间-类型
    public function operate_department_info_time_set($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】【选项-信息】修改-radio-select-[option]-类型
    public function operate_department_info_option_set($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->timestamps = false;
            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 41;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    if(in_array($column_key,['leader_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }


                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】【附件】添加
    public function operate_department_info_attachment_set($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-attachment-set') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 41;
                            $attachment_data['item_id'] = $item_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Client_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 21;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["item_id"] = $item_id;
                                $record_data["operate_object"] = 41;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 41;
                    $attachment_data['item_id'] = $item_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Client_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 21;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["item_id"] = $item_id;
                        $record_data["operate_object"] = 41;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】【附件】删除
    public function operate_department_info_attachment_delete($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item->item_id;
                $record_data["operate_object"] = 41;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】【附件】获取
    public function operate_department_get_attachment_html($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Department::with([
            'attachment_list' => function($query) { $query->where(['record_object'=>21, 'operate_object'=>41]); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.item-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【部门管理】管理员-删除
    public function operate_department_admin_delete($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = DK_Client_Department::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】管理员-恢复
    public function operate_department_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
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

        $operate = $post_data["operate"];
        if($operate != 'department-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Client_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("department--restore--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】管理员-彻底删除
    public function operate_department_admin_delete_permanently($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'department-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Client_Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("department--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】管理员-启用
    public function operate_department_admin_enable($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Client_Department::find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【部门管理】管理员-禁用
    public function operate_department_admin_disable($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = DK_Client_Department::find($id);
        if(!$item) return response_error([],"该【部门】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--department--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }












    /*
     * 用户系统
     */


    // 【select2】
    public function operate_business_select2_user($post_data)
    {
        $me = Auth::guard('admin')->user();
        if(empty($post_data['keyword']))
        {
            $list =User::select(['id','username as text'])
                ->where(['userstatus'=>'正常','status'=>1])
                ->whereIn('usergroup',['Agent','Agent2'])
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =User::select(['id','username as text'])
                ->where(['userstatus'=>'正常','status'=>1])
                ->whereIn('usergroup',['Agent','Agent2'])
                ->where('sitename','like',"%$keyword%")
                ->orderBy('id','desc')
                ->get()
                ->toArray();
        }
        array_unshift($list, ['id'=>0,'text'=>'【全部代理】']);
        return $list;
    }
    //
    public function operate_user_select2_sales($post_data)
    {
//        $type = $post_data['type'];
//        if($type == 0) $district_type = 0;
//        else if($type == 1) $district_type = 0;
//        else if($type == 2) $district_type = 1;
//        else if($type == 3) $district_type = 2;
//        else if($type == 4) $district_type = 3;
//        else if($type == 21) $district_type = 4;
//        else if($type == 31) $district_type = 21;
//        else if($type == 41) $district_type = 31;
//        else $district_type = 0;
//        if(!is_numeric($type)) return view(env('TEMPLATE_DK_CLIENT').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_DK_CLIENT').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =DK_Client_User::select(['id','username as text'])
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =DK_Client_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }


    //
    public function operate_user_select2_superior($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_Client_User::select(['id','true_name as text'])
                ->where(['user_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Client_User::select(['id','true_name as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'inspector') $query->where(['user_type'=>71]);
            else if($type == 'customer_service_supervisor') $query->where(['user_type'=>81]);
            else if($type == 'customer_service') $query->where(['user_type'=>84]);
        }
        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_user_select2_department($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $query =DK_Client_Department::select(['id','name as text'])
                ->where(['item_status'=>1]);
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $query =DK_Client_Department::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1]);
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'district') $query->where(['department_type'=>11]);
            else if($type == 'group')
            {
                $id = $post_data['superior_id'];
                $query->where(['department_type'=>21,'superior_department_id'=>$id]);
            }
        }
        else $query->where(['department_type'=>11]);

        $list = $query->orderBy('id','desc')->get()->toArray();
        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
        array_unshift($list,$unSpecified);
        return $list;
    }








    /*
     * USER 用户管理
     */
    // 【用户-员工管理】返回-列表-视图
    public function view_user_staff_list($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        if(in_array($me->user_type,[0,1,9,11]))
        {
            $department_district_list = DK_Client_Department::select('id','name')->where('department_type',11)->get();
            $return['department_district_list'] = $department_district_list;
        }

        $return['menu_active_of_staff_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.user.staff-list';
        return view($view_blade)->with($return);
    }
    // 【用户-员工管理】返回-列表-数据
    public function get_user_staff_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::withTrashed()->select('*')
            ->with(['creator','department_er'])
            ->whereIn('user_category',[11])
            ->where('client_id',$me->client_id)
            ->where('id','!=',$me->id);

        if($me->user_type == 11)
        {
            $query->whereIn('user_type',[41,61,66,71,77,81,84,88]);
        }
        else if($me->user_type == 61)
        {
            $query->whereIn('user_type',[66]);
        }
        else if($me->user_type == 41)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[71,77,81,84,88]);
        }
        else if($me->user_type == 71)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[77]);
        }
        else if($me->user_type == 81)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[84,88]);
        }
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['mobile'])) $query->where('mobile', $post_data['mobile']);


        // 部门-大区
        if(!empty($post_data['department']))
        {
            if(!in_array($post_data['department'],[-1,0,'-1','0']))
            {
                $query->where('department_id', $post_data['department']);
            }
        }

        // 员工类型
        if(!empty($post_data['user_type']))
        {
            if(!in_array($post_data['user_type'],[-1,0]))
            {
                $query->where('user_type', $post_data['user_type']);
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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【用户-员工管理】返回-添加-视图
    public function view_user_staff_create()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list-for-all';

        $view_data['operate'] = 'create';
        $view_data['operate_id'] = 0;
        $view_data['category'] = 'user';
        $view_data['type'] = $item_type;
        $view_data['item_type_text'] = $item_type_text;
        $view_data['title_text'] = $title_text;
        $view_data['list_text'] = $list_text;
        $view_data['list_link'] = $list_link;


        $department_list = DK_Client_Department::select('id','name')->where('client_id',$me->client_id)->get();
        $view_data['department_list'] = $department_list;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.user.staff-edit';
        return view($view_blade)->with($view_data);
    }
    // 【用户-员工管理】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,31,41,61,71,81])) return view($this->view_blade_403);

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.user.staff-edit';

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list';

        $view_data['operate'] = 'create';
        $view_data['operate_id'] = 0;
        $view_data['category'] = 'user';
        $view_data['type'] = $item_type;
        $view_data['item_type_text'] = $item_type_text;
        $view_data['title_text'] = $title_text;
        $view_data['list_text'] = $list_text;
        $view_data['list_link'] = $list_link;

        $department_list = DK_Client_Department::select('id','name')->where('client_id',$me->client_id)->get();
        $view_data['department_list'] = $department_list;

        if($id == 0)
        {
            return view($view_blade)->with($view_data);
        }
        else
        {
            $mine = DK_Client_User::with(['parent','superior'])->find($id);
            if($mine)
            {
//                if($me->user_type == 81)
//                {
//                    if($mine->department_district_id != $me->department_district_id)
//                    {
//                        return view($this->view_blade_403);
//                    }
//                }

//                $mine->custom = json_decode($mine->custom);

                $view_data['operate'] = 'edit';
                $view_data['operate_id'] = $id;
                $view_data['data'] = $mine;

                return view($view_blade)->with($view_data);
            }
            else return view(env('TEMPLATE_DK_CLIENT').'entrance.errors.404');
        }
    }
    // 【用户-员工管理】保存数据
    public function operate_user_staff_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'username.required' => '请输入用户名！',
            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
            'mobile' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,31,41,61,71,81])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_name_exist = DK_Client_User::where('username',$post_data['username'])->first();
            if($is_name_exist) return response_error([],"用户名已存在！");

            $is_mobile_exist = DK_Client_User::where('mobile',$post_data['mobile'])->first();
            if($is_mobile_exist) return response_error([],"手机号（工号）已存在！");

            $mine = new DK_Client_User;
            $post_data["user_status"] = 0;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Client_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_mobile_exist = DK_Client_User::where('mobile',$post_data['mobile'])->first();
                if($is_mobile_exist) return response_error([],"手机号（工号）重复，请更换工号再试一次！");
            }
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['department_district_id'] = $me->department_district_id;
            }
//            if($me->user_type == 81)
//            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//            }

            if($post_data["user_type"] == 71 || $post_data["user_type"] == 77)
            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//                unset($mine_data['department_district_id']);
                unset($mine_data['department_group_id']);
            }
            else if($post_data["user_type"] == 81)
            {
                unset($mine_data['department_group_id']);
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
//                    $user_ext = new DK_Client_UserExt;
//                    $user_ext_create['user_id'] = $mine->id;
//                    $bool_2 = $user_ext->fill($user_ext_create)->save();
//                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
                }

                // 头像
                if(!empty($post_data["portrait"]))
                {
                    // 删除原图片
                    $mine_portrait_img = $mine->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_resource_path($mine_portrait_img)))
                    {
                        unlink(storage_resource_path($mine_portrait_img));
                    }

//                    $result = upload_storage($post_data["portrait"]);
//                    $result = upload_storage($post_data["portrait"], null, null, 'assign');
                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$mine->id,'dk/unique/portrait_for_user','');
                    if($result["result"])
                    {
                        $mine->portrait_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--portrait_img--file--fail");
                }
                else
                {
                    if($operate == 'create')
                    {
                        $portrait_path = "dk/unique/portrait_for_user/".date('Y-m-d');
                        if (!is_dir(storage_resource_path($portrait_path)))
                        {
                            mkdir(storage_resource_path($portrait_path), 0777, true);
                        }
                        copy(storage_resource_path("materials/portrait/user0.jpeg"), storage_resource_path($portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg"));
                        $mine->portrait_img = $portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg";
                        $mine->save();
                    }
                }

            }
            else throw new Exception("insert--user--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【用户-员工管理】管理员-修改密码
    public function operate_user_staff_password_admin_change($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
            'user-password.required' => '请输入密码！',
            'user-password-confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
            'user-password' => 'required',
            'user-password-confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-password-admin-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["user-password"];
        $confirm = $post_data["user-password-confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode($password);
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【用户-员工管理】管理员-重置密码
    public function operate_user_staff_password_admin_reset($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-password-admin-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode('12345678');
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }




    // 【用户-员工管理】管理员-删除
    public function operate_user_staff_admin_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-delete') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_Client_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
            $bool = $user->delete();  // 普通删除
//            $bool = $user->forceDelete();  // 永久删除
            if(!$bool) throw new Exception("user--delete--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【用户-员工管理】管理员-恢复
    public function operate_user_staff_admin_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_Client_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能恢复你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更好的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
            $bool = $user->restore();  // 恢复
            if(!$bool) throw new Exception("user--restore--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【用户-员工管理】管理员-永久删除
    public function operate_user_staff_admin_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有权限！");

        $user = DK_Client_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
        if($user->id == $me->id) return response_error([],"你不能删除你自己！");
        if($user->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更好的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->timestamps = false;
//            $bool = $user->delete();  // 普通删除
            $bool = $user->forceDelete();  // 永久删除
            if(!$bool) throw new Exception("user--delete--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【用户-员工管理】管理员-启用
    public function operate_user_staff_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【用户-员工管理】管理员-禁用
    public function operate_user_staff_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 9;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }

    // 【用户-员工管理】管理员-晋升
    public function operate_user_staff_admin_promote($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-promote') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 88) return response_error([],"只有客服才可以晋升！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_type = 84;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【用户-员工管理】管理员-降职
    public function operate_user_staff_admin_demote($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'user_id.required' => 'user_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'staff-admin-demote') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = DK_Client_User::find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");
        if($user->user_type != 84) return response_error([],"只有客服主管才可能降职！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,41])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_type = 88;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    // 【交付管理】返回-列表-视图
    public function view_item_delivery_list($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 10;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;




        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Client_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 员工
        if(!empty($post_data['staff_id']))
        {
            if(is_numeric($post_data['staff_id']) && $post_data['staff_id'] > 0)
            {
                $staff = DK_Client_User::select(['id','name'])->find($post_data['staff_id']);
                if($staff)
                {
                    $view_data['staff_id'] = $post_data['staff_id'];
                    $view_data['staff_name'] = $staff->name;
                }
                else $view_data['staff_id'] = -1;
            }
            else $view_data['staff_id'] = -1;
        }
        else $view_data['staff_id'] = -1;


        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_name'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';


        // 分配状态
        if(!empty($post_data['assign_status']))
        {
            $view_data['assign_status'] = $post_data['assign_status'];
        }
        else $view_data['assign_status'] = -1;


        // 导出状态
        if(!empty($post_data['exported_status']))
        {
            $view_data['exported_status'] = $post_data['exported_status'];
        }
        else $view_data['exported_status'] = -1;

        // 是否api推送
        if(!empty($post_data['is_api_pushed']))
        {
            $view_data['is_api_pushed'] = $post_data['is_api_pushed'];
        }
        else $view_data['is_api_pushed'] = -1;



        $staff_list = DK_Client_User::select('id','username')->where('client_id',$me->client_id)->whereIn('user_type',[81,84,88])->get();
        $view_data['staff_list'] = $staff_list;

        $view_data['menu_active_of_delivery_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.delivery-list';
        return view($view_blade)->with($view_data);
    }
    // 【交付管理】返回-列表-数据
    public function get_item_delivery_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->client_id)
            ->with([
                'inspector_er',
                'project_er',
                'client_staff_er',
                'order_er'
            ]);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        // 客服经理
        if($me->user_type == 81)
        {
            $district_staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服主管
        if($me->user_type == 84)
        {
            $group_staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$group_staff_list);
        }
        // 客服
        if($me->user_type == 88)
        {
            $query->where('client_staff_id', $me->id);
        }



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);


        // 患者类型
        if(isset($post_data['client_type']))
        {
            if(!in_array($post_data['client_type'],[-1,'-1']))
            {
                $query->where('client_type', $post_data['client_type']);
            }
        }



        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];

                    $query->whereYear(DB::Raw("from_unixtime(created_at)"), $month_year)->whereMonth(DB::Raw("from_unixtime(created_at)"), $month_month);

//                    $month_start = $post_data['month'].'-01';
//                    if(in_array($month_month,['01','03','05','07','08','10','12'])) $month_ended = $post_data['month'].'-31';
//                    else if($month_month == "02") $month_ended = $post_data['month'].'-28';
//                    else $month_ended = $post_data['month'].'-30';
//
//                    $query->whereDate(DB::Raw("from_unixtime(created_at)"), '>=', $month_start)
//                        ->whereDate(DB::Raw("from_unixtime(created_at)"), '<=', $month_ended);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
            }
            else if($post_data['time_type'] == "period")
            {

                if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), '>=', $post_data['assign_start']);
                if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), '<=', $post_data['assign_ended']);
            }
            else
            {}
        }



        // 审核状态
        // 项目
        if(!empty($post_data['exported_status']))
        {
            if(is_numeric($post_data['exported_status']) && $post_data['exported_status'] > 0)
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }


//        if(!empty($post_data['exported_status']))
//        {
//            $exported_status = $post_data['exported_status'];
//            if(in_array($exported_status,['待导出','已导出']))
//            {
//                if($exported_status == '待发布')
//                {
//                    $query->where('is_published', 0);
//                }
//                else if($exported_status == '待导出')
//                {
//                    $query->where('is_published', 1)->whereIn('exported_status', [0,9]);
//                }
//                else if($exported_status == '已导出')
//                {
//                    $query->where('exported_status', 1);
//                }
//            }
//        }

        // 区域
        if(isset($post_data['district']))
        {
            if(count($post_data['district']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                        $query->whereIn('location_district',$post_data['district']);
                    }
                );
            }
        }




        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【交付管理】质量评价
    public function operate_item_delivery_quality_evaluate($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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

        $operate = $post_data["operate"];
        if($operate != 'delivery-quality-evaluate') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

//        if(!in_array($me->user_type,[0,1,9,11,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if($item->client_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $order_quality = $post_data["order_quality"];
        if(!in_array($order_quality,config('info.order_quality'))) return response_error([],"质量结果非法！");

        $before = $item->inspected_result;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->order_quality = $order_quality;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
//                $record = new DK_Client_Record;
//
//                $record_data["ip"] = Get_IP();
//                $record_data["record_object"] = 31;
//                $record_data["record_category"] = 11;
//                $record_data["record_type"] = 1;
//                $record_data["creator_id"] = $me->id;
//                $record_data["order_id"] = $id;
//                $record_data["operate_object"] = 71;
//                $record_data["operate_category"] = 93;
//                $record_data["operate_type"] = 1;
//
//                $record_data["before"] = $before;
//                $record_data["after"] = $order_quality;
//
//                $bool_1 = $record->fill($record_data)->save();
//                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-导出状态
    public function operate_item_delivery_bulk_exported_status($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'operate_exported_status.required' => 'operate_exported_status.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'operate_exported_status' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-exported-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_exported_status = $post_data["operate_exported_status"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['exported_status'] = $operate_exported_status;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");


                $before = $item->$operate_exported_status;

                $item->exported_status = $operate_exported_status;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "exported_status";

                    $record_data["before"] = $before;
                    $record_data["after"] = $operate_exported_status;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-分配状态
    public function operate_item_delivery_bulk_assign_status($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'operate_assign_status.required' => 'operate_assign_status.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'operate_assign_status' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-assign-status-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_assign_status = $post_data["operate_assign_status"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['assign_status'] = $operate_assign_status;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");


                $before = $item->$operate_assign_status;

                $item->assign_status = $operate_assign_status;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "assign_status";

                    $record_data["before"] = $before;
                    $record_data["after"] = $operate_assign_status;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-分配
    public function operate_item_delivery_bulk_assign_staff($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'operate_staff_id.required' => 'operate_staff_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'operate_staff_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-assign-staff-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $client_staff_id = $post_data["operate_staff_id"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['client_staff_id'] = $client_staff_id;

//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");


                $before = $item->client_staff_id;

                $item->client_staff_id = $client_staff_id;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "client_staff_id";

                    $record_data["before"] = $before;
                    $record_data["after"] = $client_staff_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付管理】批量-API-推送
    public function operate_item_delivery_bulk_api_push($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-api-push-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $url = "https://qw-openapi-tx.dustess.com/auth/v1/access_token/token";

        $curl_data['ClientID'] = env('API_SCRM_ClientID');
        $curl_data['ClientSecret'] = env('API_SCRM_ClientSecret');
        $curl_data = json_encode($curl_data);
//        dd($curl_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data); // post的变量
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            return response_fail([],'token请求失败');
        }
        else
        {
            $result = json_decode($result);
            if($result->success)
            {
                $token = $result->data->accessToken;
            }
        }
        curl_close($ch);





        if(!empty($token))
        {
            $delivery_list = DK_Pivot_Client_Delivery::withTrashed()
                ->with('order_er')
                ->whereIn('id',$ids_array)->get();
//        dd($delivery_list->toArray());

            $customer_list = [];
            foreach($delivery_list as $key => $item)
            {
                if($item->is_api_pushed == 0)
                {
                    $customer = [];

                    $customer['source'] = "2r4";

                    $customer['pool'] = env('API_SCRM_Pool');
                    $customer['remark'] = $item->order_er->client_name;
                    $customer['prov_city'] = $item->order_er->location_city.'-'.$item->order_er->location_district;


                    $mobile['type'] = "mobile";
                    $mobile['display'] = "手机号";
                    $mobile['tel'] = $item->order_er->client_phone;
                    $customer['mobiles'][] = $mobile;

                    if(!empty($item->order_er->wx_id))
                    {
                        $wx['type'] = "wx_id";
                        $wx['display'] = "微信号";
                        $wx['tel'] = $item->order_er->wx_id;
                        $customer['mobiles'][] = $wx;
                    }

                    $customer['description'] = $item->order_er->description;

                    // 自定义字段
                    $custom_fields = [];

                    $delivery_time['id'] = 'delivery_time';
                    $delivery_time['type'] = 'text';
                    $delivery_time['string_value'] = $item->created_at->format('Y-m-d');
                    $custom_fields[] = $delivery_time;

                    $teeth_count['id'] = 'teeth_count';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $teeth_count['id'] = 'field1';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $customer['custom_fields'] = $custom_fields;

                    $customer['description'] = $item->order_er->description;

                    $customer_list[] = $customer;
                }
            }


            if(count($customer_list) > 0)
            {
                $api_push_data['customer_list'] = $customer_list;
                $api_push_data_json = json_encode($api_push_data);

                $push_url = "https://qw-openapi-tx.dustess.com/customer/v1/batchAddCustomer?accessToken=".$token;

                $push_ch = curl_init();
                curl_setopt($push_ch, CURLOPT_URL, $push_url);
                curl_setopt($push_ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
                curl_setopt($push_ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($push_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($push_ch, CURLOPT_POST, true); // post数据
                curl_setopt($push_ch, CURLOPT_POSTFIELDS, $api_push_data_json); // post的变量
                $push_result = curl_exec($push_ch);
                if(curl_errno($push_ch))
                {
                    return response_fail([],'api推送请求失败！');
                }
                else
                {
                    $push_result_decode = json_decode($push_result);
                    if($push_result_decode->success)
                    {
                    }
                    else
                    {
                        return response_fail([],'推送数据失败！');
                    }
                }
                curl_close($push_ch);
            }
            else return response_fail(['count'=>count($customer_list)],'工单已推送过，本次未推送数据！');

        }
        else return response_fail([],'token不存在！');


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $delivered_update['is_api_pushed'] = 1;
            $delivered_update['is_api_pusher_id'] = $me->id;
            $delivered_update['is_api_pushed_at'] = time();
            $bool = DK_Pivot_Client_Delivery::withTrashed()->whereIn('id',$ids_array)
                ->update($delivered_update);
            if(!$bool) throw new Exception("DK_Pivot_Client_Delivery--update--fail");
            else
            {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 111;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "ids";

                    $record_data["title"] = $ids;
                    $record_data["content"] = $push_result;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
            }

//            foreach($ids_array as $key => $id)
//            {
//                $item = DK_Pivot_Client_Delivery::withTrashed()->find($id);
//                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");
//
//
////                $before = $item->client_staff_id;
//
//                $item->is_api_pushed = 1;
//                $bool = $item->save();
//                if(!$bool) throw new Exception("item--update--fail");
//                else
//                {
////                    $record = new DK_Client_Record;
////
////                    $record_data["ip"] = Get_IP();
////                    $record_data["record_object"] = 21;
////                    $record_data["record_category"] = 11;
////                    $record_data["record_type"] = 1;
////                    $record_data["creator_id"] = $me->id;
////                    $record_data["order_id"] = $id;
////                    $record_data["operate_object"] = 91;
////                    $record_data["operate_category"] = 99;
////                    $record_data["operate_type"] = 1;
////                    $record_data["column_name"] = "client_staff_id";
////
////                    $record_data["before"] = $before;
////                    $record_data["after"] = $client_staff_id;
////
////                    $bool_1 = $record->fill($record_data)->save();
////                    if(!$bool_1) throw new Exception("insert--record--fail");
//                }
//
//            }


            DB::commit();
            return response_success(['count'=>count($customer_list)]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * ORDER 工单管理
     */
    // 【工单管理】返回-列表-视图
    public function view_item_order_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 20;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;




        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';

        // 员工
        if(!empty($post_data['staff_id']))
        {
            if(is_numeric($post_data['staff_id']) && $post_data['staff_id'] > 0) $view_data['staff_id'] = $post_data['staff_id'];
            else $view_data['staff_id'] = -1;
        }
        else $view_data['staff_id'] = -1;

        // 部门-大区
        if(!empty($post_data['department_district_id']))
        {
            if(is_numeric($post_data['department_district_id']) && $post_data['department_district_id'] > 0) $view_data['department_district_id'] = $post_data['department_district_id'];
            else $view_data['department_district_id'] = -1;
        }
        else $view_data['department_district_id'] = -1;



        // 客户
        if(!empty($post_data['client_id']))
        {
            if(is_numeric($post_data['client_id']) && $post_data['client_id'] > 0) $view_data['client_id'] = $post_data['client_id'];
            else $view_data['client_id'] = -1;
        }
        else $view_data['client_id'] = -1;




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Client_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(is_numeric($post_data['is_wx']) && $post_data['is_wx'] > 0) $view_data['is_wx'] = $post_data['is_wx'];
            else $view_data['is_wx'] = -1;
        }
        else $view_data['is_wx'] = -1;

        // 是否重复
        if(!empty($post_data['is_repeat']))
        {
            if(is_numeric($post_data['is_repeat']) && $post_data['is_repeat'] > 0) $view_data['is_repeat'] = $post_data['is_repeat'];
            else $view_data['is_repeat'] = -1;
        }
        else $view_data['is_repeat'] = -1;

        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;

//        dd($view_data);


        $department_district_list = DK_Client_Department::select('id','name')->where('department_type',11)->get();
        if($me->user_type == 81)
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else if($me->user_type == 84)
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->where('department_group_id',$me->department_group_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        $client_list = DK_Client::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Client_Project::select('id','name')->whereIn('item_type',[1,21])->get();

        $view_data['department_district_list'] = $department_district_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;
        $view_data['project_list'] = $project_list;
        $view_data['menu_active_of_order_list_for_all'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【工单管理】返回-列表-数据
    public function get_item_order_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Order::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->id)
            ->with(['creator','owner','inspector',
                'project_er',
                'department_district_er','department_group_er',
                'department_manager_er','department_supervisor_er']);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        // 客服经理
        if($me->user_type == 81)
        {
//            $subordinates = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates_subordinates = DK_Client_User::select('id')->whereIn('superior_id',$subordinates)->get()->pluck('id')->toArray();
//            $subordinates_list = array_merge($subordinates_subordinates,$subordinates);
//            $subordinates_list[] = $me->id;
//            $query->whereIn('creator_id',$subordinates_list);
            $district_staff_list = DK_Client_User::select('id')->where('department_district_id',$me->department_district_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$district_staff_list);
        }
        // 客服主管
        if($me->user_type == 84)
        {
//            $subordinates = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $subordinates[] = $me->id;
//            $query->whereIn('creator_id',$subordinates);
            $group_staff_list = DK_Client_User::select('id')->where('department_group_id',$me->department_group_id)->get()->pluck('id')->toArray();
            $query->whereIn('creator_id',$group_staff_list);
        }
        // 客服
        if($me->user_type == 88)
        {
            $query->where('creator_id', $me->id);
        }
        // 质检经理
        if($me->user_type == 71)
        {
            // 一对一
//            $subordinates = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                $query->whereIn('user_id', $subordinates);
//            });
            // 多对对
            $subordinates = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
            $project_list = DK_Pivot_User_Project::select('project_id')->whereIn('user_id',$subordinates)->get()->pluck('project_id')->toArray();
            $query->where('is_published','<>',0)->whereIn('project_id', $project_list);
        }
        // 质检员
        if($me->user_type == 77)
        {
            // 一对一
//            $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($me) {
//                $query->where('user_id', $me->id);
//            });
            // 多对多
            $project_list = DK_Pivot_User_Project::select('project_id')->where('user_id',$me->id)->get()->pluck('project_id')->toArray();
            $query->where('is_published','<>',0)->whereIn('project_id', $project_list);
        }

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(published_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $query->where('creator_id', $post_data['staff']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('department_district_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('department_district_id', $post_data['department_district']);
            }
        }


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }


        // 工单类型 [自有|空单|配货|调车]
        if(isset($post_data['order_type']))
        {
            if(!in_array($post_data['order_type'],[-1]))
            {
                $query->where('car_owner_type', $post_data['order_type']);
            }
        }

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(!in_array($post_data['is_wx'],[-1]))
            {
                $query->where('is_wx', $post_data['is_wx']);
            }
        }

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $inspected_status = $post_data['inspected_status'];
            if(in_array($inspected_status,['待发布','待审核','已审核']))
            {
                if($inspected_status == '待发布')
                {
                    $query->where('is_published', 0);
                }
                else if($inspected_status == '待审核')
                {
                    $query->where('is_published', 1)->whereIn('inspected_status', [0,9]);
                }
                else if($inspected_status == '已审核') $query->where('inspected_status', 1);
            }
        }
        // 审核结果
        if(!empty($post_data['inspected_result']))
        {
//            $inspected_result = $post_data['inspected_result'];
//            if(in_array($inspected_result,config('info.inspected_result')))
//            {
//                $query->where('inspected_result', $inspected_result);
//            }
            if(count($post_data['inspected_result']))
            {
                $query->whereIn('inspected_result', $post_data['inspected_result']);
            }
        }





        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【工单管理】【修改记录】返回-列表-视图
    public function view_item_order_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Client_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【工单管理】【修改记录】返回-列表-数据
    public function get_item_order_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client_Record::select('*')
            ->with([
                'creator',
                'before_project_er'=>function($query) { $query->select('id','name'); },
                'after_project_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where(['order_id'=>$id]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

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
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【工单管理】返回-添加-视图
    public function view_item_order_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '工单';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/car-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-edit';
        return view($view_blade)->with($return);
    }
    // 【工单管理】返回-编辑-视图
    public function view_item_order_edit()
    {
        $this->get_me();
        $me = $this->me;

        $id = request("id",0);
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-edit';

        $item_type = 'item';
        $item_type_text = '工单';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/car-list-for-all';

        $return['operate'] = 'edit';
        $return['operate_id'] = $id;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        if($id == 0)
        {
            $return['operate'] = 'create';
            $return['operate_id'] = 0;

            return view($view_blade)->with($return);
        }
        else
        {
            $mine = DK_Order::find($id);
            if($mine)
            {
//                if($mine->deleted_at) return view(env('TEMPLATE_DK_CLIENT').'entrance.errors.404');
//                else
                {
                    $mine->custom = json_decode($mine->custom);
                    $mine->custom2 = json_decode($mine->custom2);
                    $mine->custom3 = json_decode($mine->custom3);

                    $return['data'] = $mine;

                    return view($view_blade)->with($return);
                }
            }
            else return view(env('TEMPLATE_DK_CLIENT').'entrance.errors.404');
        }
    }
    // 【工单管理】保存数据
    public function operate_item_order_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户信息！',
            'location_city.required' => '请选择城市！',
            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required',
            'location_city' => 'required',
            'location_district' => 'required',
            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        $me->load(['department_district_er','department_group_er']);

        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Order;
            $post_data["item_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

//            $is_repeat = DK_Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = DK_Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Client_Project::find($post_data['project_id']);
            if(!$project) return response_error([],"选择【项目】不存在，刷新页面重试！");
        }



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            // 指派日期
            if(!empty($post_data['assign_date']))
            {
                $post_data['assign_time'] = strtotime($post_data['assign_date']);
            }
//            else $post_data['assign_time'] = 0;



            $mine_data = $post_data;
            $mine_data['department_district_id'] = $me->department_district_id;
            $mine_data['department_group_id'] = $me->department_group_id;
            if($me->department_district_er) $mine_data['department_manager_id'] = $me->department_district_er->leader_id;
            if($me->department_group_er) $mine_data['department_supervisor_id'] = $me->department_group_er->leader_id;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
//                if(!empty($post_data['circle_id']))
//                {
//                    $circle_data['order_id'] = $mine->id;
//                    $circle_data['creator_id'] = $me->id;
//                    $circle->pivot_order_list()->attach($circle_data);  //
////                    $circle->pivot_order_list()->syncWithoutDetaching($circle_data);  //
//                }
            }
            else throw new Exception("insert--order--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【工单管理】获取-详情-数据
    public function operate_item_order_get($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $item->should_departure_time_html = date("Y-m-d H:i", $item->should_departure_time);
        $item->should_arrival_time_html = date("Y-m-d H:i", $item->should_arrival_time);

        $item->is_actual_departure = $item->actual_departure_time ? 1 : 0;
        if($item->is_actual_departure) $item->actual_departure_time_html = date("Y-m-d H:i", $item->actual_departure_time);

        $item->is_actual_arrival = $item->actual_arrival_time ? 1 : 0;
        if($item->is_actual_arrival) $item->actual_arrival_time_html = date("Y-m-d H:i", $item->actual_arrival_time);

        $item->is_stopover = $item->stopover_place ? 1 : 0;
        if($item->is_stopover)
        {
            $item->is_stopover_arrival = $item->stopover_arrival_time ? 1 : 0;
            if($item->is_stopover_arrival) $item->stopover_arrival_time_html = date("Y-m-d H:i", $item->stopover_arrival_time);

            $item->is_stopover_departure = $item->stopover_departure_time ? 1 : 0;
            if($item->is_stopover_departure) $item->stopover_departure_time_html = date("Y-m-d H:i", $item->stopover_departure_time);
        }

        return response_success($item,"");

    }
    // 【工单管理】获取-详情-视图
    public function operate_item_order_get_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        if($item->car_owner_type == 1)
        {
            $item->car_owner_type_name = "自有";
            $item->car = $item->car_er ? $item->car_er->name : '';
            $item->trailer = $item->trailer_er ? $item->trailer_er->name : '';
        }
        else if($item->car_owner_type == 21)
        {
            $item->car_owner_type_name = "外请·调车";
            $item->car = $item->outside_car;
            $item->trailer = $item->outside_trailer;
        }
        else if($item->car_owner_type == 41)
        {
            $item->car_owner_type_name = "外配·配货";
            $item->car = $item->car_er ? $item->car_er->name : '';
            $item->trailer = $item->trailer_er ? $item->trailer_er->name : '';
//            $item->car = $item->outside_car;
//            $item->trailer = $item->outside_trailer;
        }

        $item->should_departure_time_html = date("Y-m-d H:i", $item->should_departure_time);
        $item->should_arrival_time_html = date("Y-m-d H:i", $item->should_arrival_time);

        $item->is_actual_departure = $item->actual_departure_time ? 1 : 0;
        if($item->is_actual_departure) $item->actual_departure_time_html = date("Y-m-d H:i", $item->actual_departure_time);

        $item->is_actual_arrival = $item->actual_arrival_time ? 1 : 0;
        if($item->is_actual_arrival) $item->actual_arrival_time_html = date("Y-m-d H:i", $item->actual_arrival_time);

        $item->is_stopover = $item->stopover_place ? 1 : 0;
        if($item->is_stopover)
        {
            $item->is_stopover_arrival = $item->stopover_arrival_time ? 1 : 0;
            if($item->is_stopover_arrival) $item->stopover_arrival_time_html = date("Y-m-d H:i", $item->stopover_arrival_time);

            $item->is_stopover_departure = $item->stopover_departure_time ? 1 : 0;
            if($item->is_stopover_departure) $item->stopover_departure_time_html = date("Y-m-d H:i", $item->stopover_departure_time);
        }


        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-info-html';
        $html = view($view_blade)->with(['data'=>$item])->__toString();

        return response_success(['html'=>$html],"");

    }
    // 【工单管理】获取-附件-视图
    public function operate_item_order_get_attachment_html($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::with(['attachment_list'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");
    }


    // 【工单管理】删除
    public function operate_item_order_delete($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-delete') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[81,82,88]))
        {
            if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($item->is_published == 0 && $item->creator_id == $me->id)
            {
                $item_copy = $item;

                $item->timestamps = false;
//                $bool = $item->forceDelete();  // 永久删除
                $bool = $item->delete();  // 普通删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 31;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $item->timestamps = false;
                $bool = $item->delete();  // 普通删除
//                $bool = $item->forceDelete();  // 永久删除
                if(!$bool) throw new Exception("item--delete--fail");
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 31;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 101;
                    $record_data["operate_type"] = 1;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                DB::commit();
            }

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】发布
    public function operate_item_order_publish($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-publish') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $project_id = $item->project_id;
        $client_phone = $item->client_phone;

        $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$client_phone])
            ->where('id','<>',$id)->where('is_published','>',0)->count("*");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->inspected_status == 1)
            {
                $item->inspected_status = 9;
            }

            $item->is_repeat = $is_repeat;
            $item->is_published = 1;
            $item->published_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 11;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】完成
    public function operate_item_order_complete($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-complete') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_completed = 1;
            $item->completer_id = $me->id;
            $item->completed_at = time();
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 100;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】弃用
    public function operate_item_order_abandon($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-abandon') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 97;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 97;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】复用
    public function operate_item_order_reuse($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-reuse') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 98;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】验证
    public function operate_item_order_verify($post_data)
    {
//        dd($post_data);
        return response_success([]);
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

        $operate = $post_data["operate"];
        if($operate != 'order-verify') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,71,87])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->verifier_id = $me->id;
            $item->verified_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 91;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】审核
    public function operate_item_order_inspect($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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

        $operate = $post_data["operate"];
        if($operate != 'order-inspect') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $inspected_result = $post_data["inspected_result"];
        if(!in_array($inspected_result,config('info.inspected_result'))) return response_error([],"审核结果非法！");
        $inspected_description = $post_data["inspected_description"];

        $before = $item->inspected_result;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->inspector_id = $me->id;
            $item->inspected_status = 1;
            $item->inspected_result = $inspected_result;
            if($inspected_description) $item->inspected_description = $inspected_description;
            $item->inspected_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 92;
                $record_data["operate_type"] = 1;
                $record_data["description"] = $inspected_description;

                $record_data["before"] = $before;
                $record_data["after"] = $inspected_result;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】审核
    public function operate_item_order_follow($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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

        $operate = $post_data["operate"];
        if($operate != 'order-follow') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if($item->client_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $follow_description = trim($post_data["follow_description"]);
        if(!$follow_description)  return response_error([],"输入不能为空！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $follow['time'] = time();
            $follow['description'] = $follow_description;

            if($item->content)
            {
                $follow_content = json_decode($item->content,true);
                $follow_content[] = $follow;

            }
            else
            {
                $follow_content[0] = $follow;
            }

            $follow_encode = json_encode($follow_content);

            $item->content = $follow_encode;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
//                $record = new DK_Client_Record;
//
//                $record_data["ip"] = Get_IP();
//                $record_data["record_object"] = 31;
//                $record_data["record_category"] = 11;
//                $record_data["record_type"] = 1;
//                $record_data["creator_id"] = $me->id;
//                $record_data["order_id"] = $id;
//                $record_data["operate_object"] = 71;
//                $record_data["operate_category"] = 92;
//                $record_data["operate_type"] = 1;
//                $record_data["description"] = $inspected_description;
//
//                $record_data["before"] = $before;
//                $record_data["after"] = $inspected_result;
//
//                $bool_1 = $record->fill($record_data)->save();
//                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】质量评价
    public function operate_item_order_quality_evaluate($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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

        $operate = $post_data["operate"];
        if($operate != 'order-quality-evaluate') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

//        if(!in_array($me->user_type,[0,1,9,11,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if($item->client_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $order_quality = $post_data["order_quality"];
        if(!in_array($order_quality,config('info.order_quality'))) return response_error([],"质量结果非法！");

        $before = $item->inspected_result;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->order_quality = $order_quality;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 93;
                $record_data["operate_type"] = 1;

                $record_data["before"] = $before;
                $record_data["after"] = $order_quality;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【工单管理】【文本】修改-文本-类型
    public function operate_item_order_info_text_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;

        if($column_key == "client_phone")
        {
            if(!in_array($me->user_type,[0,1,11,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "inspected_description")
        {
            if(!in_array($me->user_type,[0,1,11,71,77])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "description")
        {
            if(!in_array($me->user_type,[0,1,11,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,81,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "client_phone")
            {
                $project_id = $item->project_id;
                $client_phone = $item->client_phone;

                $is_repeat = DK_Order::where(['project_id'=>$project_id,'client_phone'=>$client_phone])
                    ->where('id','<>',$id)->where('is_published','>',0)->count("*");
                $item->is_repeat = $is_repeat;
//                dd($item->is_repeat);
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 31;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $column_value;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】【时间】修改-时间-类型
    public function operate_item_order_info_time_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];
        $time_type = $post_data["time_type"];

        $before = $item->$column_key;


        // 应出发时间
        if($column_key == "should_departure_time" && $item->should_arrival_time)
        {
            if(strtotime($column_value) >= $item->should_arrival_time)
            {
                return response_error([],"应出发时间不能超过应到达时间！");
            }
        }
        // 应到达时间
        if($column_key == "should_arrival_time" && $item->should_departure_time)
        {
            if(strtotime($column_value) <= $item->should_departure_time)
            {
                return response_error([],"应到达时间不能在应出发时间之前！");
            }
        }

        // 实际出发时间
        if($column_key == "actual_departure_time")
        {
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_arrival_time)
            {
                if(strtotime($column_value) >= $item->actual_arrival_time)
                {
                    return response_error([],"出发时间不能超过到达时间！");
                }
            }
        }
        // 实际到达时间
        if($column_key == "actual_arrival_time")
        {
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_departure_time)
            {
                if(strtotime($column_value) <= $item->actual_departure_time)
                {
                    return response_error([],"到达时间不能在出发时间之前！");
                }
            }
            else return response_error([],"请先填写出发时间！");
        }

        // 经停到达时间
        if($column_key == "stopover_arrival_time")
        {
            if(!$item->stopover_place) return response_error([],"没有经停点！");
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->actual_departure_time)
            {
                if(strtotime($column_value) <= $item->actual_departure_time)
                {
                    return response_error([],"经停点-到达时间不能在（实际）出发时间之前！");
                }
            }
            else return response_error([],"请先填写（实际）出发时间！");
        }
        // 经停出发时间
        if($column_key == "stopover_departure_time")
        {
            if(!$item->stopover_place) return response_error([],"没有经停点！");
            if(strtotime($column_value) > time()) return response_error([],"时间不能超过当前！");
            if($item->stopover_arrival_time)
            {
                if(strtotime($column_value) <= $item->stopover_arrival_time)
                {
                    return response_error([],"（经停点）出发时间不能在（经停点）到达时间之前！");
                }
            }
            else return response_error([],"请先填写（经停点）到达时间！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 31;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $time_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = strtotime($column_value);

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】【选项】修改-radio-select-[option]-类型
    public function operate_item_order_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;
        $after = $column_value;

        if($column_key == "location_city")
        {
            if(!in_array($me->user_type,[0,1,11,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "car_id")
            {
                if($column_value == 0)
                {
                }
                else
                {
                    $car = DK_Client_Project::withTrashed()->find($column_value);
                    if(!$car) throw new Exception("该【车辆】不存在，刷新页面重试！");

//                $item->driver_name = null;
//                $item->driver_phone = null;
                    $item->driver_name = $car->linkman_name;
                    $item->driver_phone = $car->linkman_phone;
                }
            }
            else if($column_key == "location_city")
            {
                $column_key2 = $post_data["column_key2"];
                $column_value2 = $post_data["column_value2"];

                $before = $item->location_city.' - '.$item->location_district;
                $after = $column_value.' - '.$column_value2;

                $item->$column_key2 = $column_value2;
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("order--update--fail");
            else
            {


                    // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Client_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 31;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    if(in_array($column_key,['client_id','project_id','car_id','driver_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }



                    if($column_key == 'client_id')
                    {
                        $record_data["before_client_id"] = $before;
                        $record_data["after_client_id"] = $column_value;
                    }
                    else if($column_key == 'project_id')
                    {
                        $record_data["before_project_id"] = $before;
                        $record_data["after_project_id"] = $column_value;
                    }

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("insert--record--fail");
                }
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】【附件】添加
    public function operate_item_order_info_attachment_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-attachment-set') return response_error([],"参数[operate]有误！");
        $order_id = $post_data["order_id"];
        if(intval($order_id) !== 0 && !$order_id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($order_id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
        if(!in_array($me->user_type,[0,1,11,81,82,88])) return response_error([],"你没有操作权限！");

//        $operate_type = $post_data["operate_type"];
//        $column_key = $post_data["column_key"];
//        $column_value = $post_data["column_value"];


//        dd($post_data);
        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            // 多图
            $multiple_images = [];
            if(!empty($post_data["multiple_images"][0]))
            {
                // 添加图片
                foreach ($post_data["multiple_images"] as $n => $f)
                {
                    if(!empty($f))
                    {
                        $result = upload_img_storage($f,'','dk/attachment','');
                        if($result["result"])
                        {
                            $attachment = new YH_Attachment;

                            $attachment_data["operate_object"] = 71;
                            $attachment_data['order_id'] = $order_id;
                            $attachment_data['item_id'] = $order_id;
                            $attachment_data['attachment_name'] = $post_data["attachment_name"];
                            $attachment_data['attachment_src'] = $result["local"];
                            $bool = $attachment->fill($attachment_data)->save();
                            if($bool)
                            {
                                $record = new DK_Client_Record;

                                $record_data["ip"] = Get_IP();
                                $record_data["record_object"] = 31;
                                $record_data["record_category"] = 11;
                                $record_data["record_type"] = 1;
                                $record_data["creator_id"] = $me->id;
                                $record_data["order_id"] = $order_id;
                                $record_data["operate_object"] = 71;
                                $record_data["operate_category"] = 71;
                                $record_data["operate_type"] = 1;

                                $record_data["column_name"] = 'attachment';
                                $record_data["after"] = $attachment_data['attachment_src'];

                                $bool_1 = $record->fill($record_data)->save();
                                if($bool_1)
                                {
                                }
                                else throw new Exception("insert--record--fail");
                            }
                            else throw new Exception("insert--attachment--fail");
                        }
                        else throw new Exception("upload--attachment--file--fail");
                    }
                }
            }


            // 单图
            if(!empty($post_data["attachment_file"]))
            {
                $attachment = new YH_Attachment;

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','dk/attachment','');
                if($result["result"])
                {
                    $attachment_data["operate_object"] = 71;
                    $attachment_data['order_id'] = $order_id;
                    $attachment_data['item_id'] = $order_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new DK_Client_Record;

                        $record_data["ip"] = Get_IP();
                        $record_data["record_object"] = 31;
                        $record_data["record_category"] = 11;
                        $record_data["record_type"] = 1;
                        $record_data["creator_id"] = $me->id;
                        $record_data["order_id"] = $order_id;
                        $record_data["operate_object"] = 71;
                        $record_data["operate_category"] = 71;
                        $record_data["operate_type"] = 1;

                        $record_data["column_name"] = 'attachment';
                        $record_data["after"] = $attachment_data['attachment_src'];

                        $bool_1 = $record->fill($record_data)->save();
                        if($bool_1)
                        {
                        }
                        else throw new Exception("insert--record--fail");
                    }
                    else throw new Exception("insert--attachment--fail");
                }
                else throw new Exception("upload--attachment--file--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】【附件】删除
    public function operate_item_order_info_attachment_delete($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'order-attachment-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Attachment::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【附件】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"你没有操作权限！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->creator_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if($bool)
            {
                $record = new DK_Client_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 31;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $item->order_id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 91;

                $record_data["column_name"] = 'attachment';
                $record_data["before"] = $item->attachment_src;

                $bool_1 = $record->fill($record_data)->save();
                if($bool_1)
                {
                }
                else throw new Exception("insert--record--fail");
            }
            else throw new Exception("attachment--delete--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【工单管理】【修改信息】设置-行程时间
    public function operate_item_order_travel_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-order-travel-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["order_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $travel_type = $post_data["travel_type"];
        $travel_time = strtotime($post_data['travel_time']);

        if($travel_time > time('Y-m-d')) return response_error([],"设定时间不能大于当前！");

        if($travel_type == "actual_departure")
        {
        }
        else if($travel_type == "stopover_arrival")
        {
            if(!$item->actual_departure_time) return response_error([],"请按顺序添加时间！");
            if($travel_time < $item->actual_departure_time) return response_error([],"经停到达时间需要在实际出发时间之后！");
        }
        else if($travel_type == "stopover_departure")
        {
            if(!$item->stopover_arrival_time) return response_error([],"请按顺序添加时间！");
            if($travel_time < $item->stopover_arrival_time) return response_error([],"经停出发时间需要在经停到达时间之后！");
        }
        else if($travel_type == "actual_arrival")
        {
            if($item->stopover_place)
            {
                if(!$item->stopover_arrival_time) return response_error([],"请按顺序添加时间！");
                if($travel_time < $item->stopover_arrival_time) return response_error([],"实际到达时间需要在经停出发时间之后！");
            }
            else
            {
                if(!$item->actual_departure_time) return response_error([],"请按顺序添加时间！");
                if($travel_time < $item->actual_departure_time) return response_error([],"实际到达时间需要在实际出发时间之后！");
            }
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($travel_type == "actual_departure")
            {
                $item->actual_departure_time = $travel_time;
            }
            else if($travel_type == "stopover_arrival")
            {
                $item->stopover_arrival_time = $travel_time;
            }
            else if($travel_type == "stopover_departure")
            {
                $item->stopover_departure_time = $travel_time;
            }
            else if($travel_type == "actual_arrival")
            {
                $item->actual_arrival_time = $travel_time;
            }

            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }








    /*
     * Delivery 交付管理
     */
    // 【交付】返回-列表-视图
    public function view_item_delivery_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = 20;
        }
        else $view_data['length'] = 20;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;




        // 工单ID
        if(!empty($post_data['order_id']))
        {
            if(is_numeric($post_data['order_id']) && $post_data['order_id'] > 0) $view_data['order_id'] = $post_data['order_id'];
            else $view_data['order_id'] = '';
        }
        else $view_data['order_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';

        // 员工
        if(!empty($post_data['staff_id']))
        {
            if(is_numeric($post_data['staff_id']) && $post_data['staff_id'] > 0) $view_data['staff_id'] = $post_data['staff_id'];
            else $view_data['staff_id'] = -1;
        }
        else $view_data['staff_id'] = -1;

        // 部门-大区
        if(!empty($post_data['department_district_id']))
        {
            if(is_numeric($post_data['department_district_id']) && $post_data['department_district_id'] > 0) $view_data['department_district_id'] = $post_data['department_district_id'];
            else $view_data['department_district_id'] = -1;
        }
        else $view_data['department_district_id'] = -1;



        // 客户
        if(!empty($post_data['client_id']))
        {
            if(is_numeric($post_data['client_id']) && $post_data['client_id'] > 0) $view_data['client_id'] = $post_data['client_id'];
            else $view_data['client_id'] = -1;
        }
        else $view_data['client_id'] = -1;




        // 项目
        if(!empty($post_data['project_id']))
        {
            if(is_numeric($post_data['project_id']) && $post_data['project_id'] > 0)
            {
                $project = DK_Client_Project::select(['id','name'])->find($post_data['project_id']);
                if($project)
                {
                    $view_data['project_id'] = $post_data['project_id'];
                    $view_data['project_name'] = $project->name;
                }
                else $view_data['project_id'] = -1;
            }
            else $view_data['project_id'] = -1;
        }
        else $view_data['project_id'] = -1;

        // 客户姓名
        if(!empty($post_data['client_name']))
        {
            if($post_data['client_name']) $view_data['client_name'] = $post_data['client_name'];
            else $view_data['client_name'] = '';
        }
        else $view_data['client_'] = '';
        // 客户电话
        if(!empty($post_data['client_phone']))
        {
            if($post_data['client_phone']) $view_data['client_phone'] = $post_data['client_phone'];
            else $view_data['client_phone'] = '';
        }
        else $view_data['client_phone'] = '';

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(is_numeric($post_data['is_wx']) && $post_data['is_wx'] > 0) $view_data['is_wx'] = $post_data['is_wx'];
            else $view_data['is_wx'] = -1;
        }
        else $view_data['is_wx'] = -1;

        // 是否重复
        if(!empty($post_data['is_repeat']))
        {
            if(is_numeric($post_data['is_repeat']) && $post_data['is_repeat'] > 0) $view_data['is_repeat'] = $post_data['is_repeat'];
            else $view_data['is_repeat'] = -1;
        }
        else $view_data['is_repeat'] = -1;

        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $view_data['inspected_status'] = $post_data['inspected_status'];
        }
        else $view_data['inspected_status'] = -1;

//        dd($view_data);


        $department_district_list = DK_Client_Department::select('id','name')->where('department_type',11)->get();
        if($me->user_type == 81)
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else if($me->user_type == 84)
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->where('department_group_id',$me->department_group_id)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        else
        {
            $staff_list = DK_Client_User::select('id','username')
                ->where('user_category',11)
                ->whereIn('user_type',[81,84,88])
                ->get();
        }
        $client_list = DK_Client::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Client_Project::select('id','name')->whereIn('item_type',[1,21])->get();

        $view_data['department_district_list'] = $department_district_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;
        $view_data['project_list'] = $project_list;
        $view_data['menu_active_of_delivery_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.item.order-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【交付】返回-列表-数据
    public function get_item_delivery_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Order::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->client_id)
            ->with(['creator','owner','inspector',
                'project_er',
                'department_district_er','department_group_er',
                'department_manager_er','department_supervisor_er']);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $query->where('creator_id', $post_data['staff']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('department_district_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('department_district_id', $post_data['department_district']);
            }
        }


        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }


        // 工单类型 [自有|空单|配货|调车]
        if(isset($post_data['order_type']))
        {
            if(!in_array($post_data['order_type'],[-1]))
            {
                $query->where('car_owner_type', $post_data['order_type']);
            }
        }

        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            if(!in_array($post_data['is_wx'],[-1]))
            {
                $query->where('is_wx', $post_data['is_wx']);
            }
        }

        // 审核状态
        if(!empty($post_data['inspected_status']))
        {
            $inspected_status = $post_data['inspected_status'];
            if(in_array($inspected_status,['待发布','待审核','已审核']))
            {
                if($inspected_status == '待发布')
                {
                    $query->where('is_published', 0);
                }
                else if($inspected_status == '待审核')
                {
                    $query->where('is_published', 1)->whereIn('inspected_status', [0,9]);
                }
                else if($inspected_status == '已审核') $query->where('inspected_status', 1);
            }
        }
        // 审核结果
        if(!empty($post_data['inspected_result']))
        {
//            $inspected_result = $post_data['inspected_result'];
//            if(in_array($inspected_result,config('info.inspected_result')))
//            {
//                $query->where('inspected_result', $inspected_result);
//            }
            if(count($post_data['inspected_result']))
            {
                $query->whereIn('inspected_result', $post_data['inspected_result']);
            }
        }





        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * Finance 财务
     */
    // 【财务】返回-列表-视图
    public function view_finance_daily_list($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 显示数量
        if(!empty($post_data['record']))
        {
            if($post_data['record'] == 'record')
            {
                $this->record_for_user_operate(21,11,1,$me->id,0,71,0);
            }
        }

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = -1;
        }
        else $view_data['length'] = -1;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;


        // 日报ID
        if(!empty($post_data['daily_id']))
        {
            if(is_numeric($post_data['daily_id']) && $post_data['daily_id'] > 0) $view_data['daily_id'] = $post_data['daily_id'];
            else $view_data['daily_id'] = '';
        }
        else $view_data['daily_id'] = '';

        // 提交日期
        if(!empty($post_data['assign']))
        {
            if($post_data['assign']) $view_data['assign'] = $post_data['assign'];
            else $view_data['assign'] = '';
        }
        else $view_data['assign'] = '';

        // 起始时间
        if(!empty($post_data['assign_start']))
        {
            if($post_data['assign_start']) $view_data['start'] = $post_data['assign_start'];
            else $view_data['start'] = '';
        }
        else $view_data['start'] = '';

        // 截止时间
        if(!empty($post_data['assign_ended']))
        {
            if($post_data['assign_ended']) $view_data['ended'] = $post_data['assign_ended'];
            else $view_data['ended'] = '';
        }
        else $view_data['ended'] = '';


        // 类型
        if(!empty($post_data['order_type']))
        {
            if(is_numeric($post_data['order_type']) && $post_data['order_type'] > 0) $view_data['order_type'] = $post_data['order_type'];
            else $view_data['order_type'] = -1;
        }
        else $view_data['order_type'] = -1;


//        dd($view_data);


        $view_data['menu_active_of_finance_daily_list'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.finance.daily-list';
        return view($view_blade)->with($view_data);
    }
    // 【财务】返回-列表-数据
    public function get_finance_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_Finance_Daily::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with(['creator'])
            ->where('client_id',$me->client_id);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        $me->load(['subordinate_er' => function ($query) {
//            $query->select('id');
//        }]);


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


//        if(!empty($post_data['assign'])) $query->whereDate("assign_date", $post_data['assign']);
//        if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '>=', $post_data['assign_start']);
//        if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), '<=', $post_data['assign_ended']);


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['date']))
                {
                    $query->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
                if(!empty($post_data['assign_start']))
                {
                    $query->whereDate("assign_date", ">=", $post_data['assign_start']);
                }
                if(!empty($post_data['assign_ended']))
                {
                    $query->whereDate("assign_date", "<=", $post_data['assign_ended']);
                }
            }
            else
            {}
        }


        // 统计
        $daily_total = (clone $query)->select(DB::raw("
                    sum(delivery_quantity) as total_of_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_of_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_of_total_daily_cost
                "))
            ->first();
//        dd($daily_total->toArray());
//        $daily_total = $daily_total[0];


        $total_data = [];
        $total_data['id'] = '合计';
        $total_data['name'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['creator_id'] = 0;
        $total_data['channel_id'] = 0;

        $total_data['delivery_quantity'] = $daily_total->total_of_delivery_quantity;
        $total_data['delivery_quantity_of_invalid'] = $daily_total->total_of_delivery_quantity_of_invalid;
        $total_data['cooperative_unit_price'] = '--';

        $total_data['total_daily_cost'] = $daily_total->total_of_total_daily_cost;


        $total_data['created_at'] = "--";
        $total_data['description'] = "--";


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if($v->creator_id == $me->id)
            {
                $list[$k]->is_me = 1;
                $v->is_me = 1;
            }
            else
            {
                $list[$k]->is_me = 0;
                $v->is_me = 0;
            }
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }








    /*
     * Statistic 流量统计
     */
    // 【统计】
    public function view_statistic_index()
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Client_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $client_list = DK_Client::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Client_Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $department_district_list = DK_Client_Department::select('id','name')->where('department_type',11)->get();

        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;
        $view_data['project_list'] = $project_list;
        $view_data['department_district_list'] = $department_district_list;


        $view_data['menu_active_of_statistic_index'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-index';
        return view($view_blade)->with($view_data);
    }
    // 【统计】
    public function view_statistic_user($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $messages = [
            'user-id.required' => 'user-id is required.',
        ];
        $v = Validator::make($post_data, [
            'user-id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $user_id = $post_data["user-id"];
        $user = DK_Client_User::find($user_id);

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 电话量
        $query = YH_TASK::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(completed_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_month)
            ->where(['is_completed'=>1,'owner_id'=>$user_id]);

        $all = $query->get()->keyBy('day');
        $dialog = $query->whereIn('item_result',[1,19])->get()->keyBy('day');
        $plus_wx = $query->where('item_result',19)->get()->keyBy('day');


        // 总转化率【占比】
        $all_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$user_id])
            ->get();
        foreach($all_rate as $k => $v)
        {
            if($v->item_result == 0) $all_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $all_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $all_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $all_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $all_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $all_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $all_rate[$k]->name = "空号";
            else $all_rate[$k]->name = "其他";
        }


        // 今日转化率【占比】
        $today_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$user_id])
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),date('Y-m-d'))
            ->get();
        foreach($today_rate as $k => $v)
        {
            if($v->item_result == 0) $today_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $today_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $today_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $today_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $today_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $today_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $today_rate[$k]->name = "空号";
            else $today_rate[$k]->name = "其他";
        }


        $view_data["head_title"] = '【'.$user->true_name.'】的工作统计';
        $view_data["all"] = $all;
        $view_data["dialog"] = $dialog;
        $view_data["plus_wx"] = $plus_wx;
        $view_data["all_rate"] = $all_rate;
        $view_data["today_rate"] = $today_rate;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-user';
        return view($view_blade)->with($view_data);
    }
    // 【统计】
    public function view_statistic_item($post_data)
    {
        $messages = [
            'id.required' => 'id required',
        ];
        $v = Validator::make($post_data, [
            'id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $item_id = $post_data["id"];
        $item = Def_Item::find($item_id);

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 访问量【统计】
        $data = Def_Record::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_month)
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();
        $data = $data->keyBy('day');




        // 打开设备类型【占比】
        $open_device_type = Def_Record::select('open_device_type',DB::raw('count(*) as count'))
            ->groupBy('open_device_type')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();
        foreach($open_device_type as $k => $v)
        {
            if($v->open_device_type == 0) $open_device_type[$k]->name = "默认";
            else if($v->open_device_type == 1) $open_device_type[$k]->name = "移动端";
            else if($v->open_device_type == 2)  $open_device_type[$k]->name = "PC端";
        }

        // 打开系统类型【占比】
        $open_system = Def_Record::select('open_system',DB::raw('count(*) as count'))
            ->groupBy('open_system')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();

        // 打开APP类型【占比】
        $open_app = Def_Record::select('open_app',DB::raw('count(*) as count'))
            ->groupBy('open_app')
            ->where(['record_category'=>1,'record_type'=>1])
            ->where('item_id',$item_id)
            ->get();




        // 分享【统计】
        $shared_data = Def_Record::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(created_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$this_month_month)
            ->where(['record_category'=>1,'record_type'=>2])
            ->where('item_id',$item_id)
            ->get();
        $shared_data = $shared_data->keyBy('day');


        // 分享【占比】
        $shared_data_scale = Def_Record::select('record_module',DB::raw('count(*) as count'))
            ->groupBy('record_module')
            ->where(['record_category'=>1,'record_type'=>2])
            ->where('item_id',$item_id)
            ->get();
        foreach($shared_data_scale as $k => $v)
        {
            if($v->record_module == 1) $shared_data_scale[$k]->name = "微信好友|QQ好友";
            else if($v->record_module == 2) $shared_data_scale[$k]->name = "朋友圈|QQ空间";
            else $shared_data_scale[$k]->name = "其他";
        }


        $view_data["item"] = $item;
        $view_data["data"] = $data;
        $view_data["open_device_type"] = $open_device_type;
        $view_data["open_app"] = $open_app;
        $view_data["open_system"] = $open_system;
        $view_data["shared_data"] = $shared_data;
        $view_data["shared_data_scale"] = $shared_data_scale;

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-item';
        return view($view_blade)->with($view_data);
    }
    // 【统计】返回（后台）主页视图
    public function get_statistic_data($post_data)
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        $this_month = date('Y-m');
        $this_month_start_date = date('Y-m-1'); // 本月开始日期
        $this_month_ended_date = date('Y-m-t'); // 本月结束日期
        $this_month_start_datetime = date('Y-m-1 00:00:00'); // 本月开始时间
        $this_month_ended_datetime = date('Y-m-t 23:59:59'); // 本月结束时间
        $this_month_start_timestamp = strtotime($this_month_start_datetime); // 本月开始时间戳
        $this_month_ended_timestamp = strtotime($this_month_ended_datetime); // 本月结束时间戳

        $last_month_start_date = date('Y-m-1',strtotime('last month')); // 上月开始时间
        $last_month_ended_date = date('Y-m-t',strtotime('last month')); // 上月开始时间
        $last_month_start_datetime = date('Y-m-1 00:00:00',strtotime('last month')); // 上月开始时间
        $last_month_ended_datetime = date('Y-m-t 23:59:59',strtotime('last month')); // 上月结束时间
        $last_month_start_timestamp = strtotime($last_month_start_datetime); // 上月开始时间戳
        $last_month_ended_timestamp = strtotime($last_month_ended_datetime); // 上月结束时间戳



        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-1',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';

        $staff_id = 0;
        $client_id = 0;
        $car_id = 0;
        $route_id = 0;
        $pricing_id = 0;

        if($type == 'component')
        {
            // 员工
            if(!empty($post_data['staff']))
            {
                if(!in_array($post_data['staff'],[-1,0]))
                {
                    $staff_id = $post_data['staff'];
                }
            }
            // 客户
            if(!empty($post_data['client']))
            {
                if(!in_array($post_data['client'],[-1,0]))
                {
                    $client_id = $post_data['client'];
                }
            }
            // 车辆
            if(!empty($post_data['car']))
            {
                if(!in_array($post_data['car'],[-1,0]))
                {
                    $car_id = $post_data['car'];
                }
            }
            // 线路
            if(!empty($post_data['route']))
            {
                if(!in_array($post_data['route'],[-1,0]))
                {
                    $route_id = $post_data['route'];
                }
            }
            // 定价
            if(!empty($post_data['pricing']))
            {
                if(!in_array($post_data['pricing'],[-1,0]))
                {
                    $pricing_id = $post_data['pricing'];
                }
            }
        }

        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');




        // 工单统计
        $order_count_for_all = DK_Order::select('*')->count("*");
        $order_count_for_unpublished = DK_Order::where('is_published', 0)->count("*");
        $order_count_for_published = DK_Order::where('is_published', 1)->count("*");
        $order_count_for_waiting_for_inspect = DK_Order::where('is_published', 1)->where('inspected_status', 0)->count("*");
        $order_count_for_inspected = DK_Order::where('is_published', 1)->where('inspected_status', '<>', 0);
        $order_count_for_accepted = DK_Order::where('is_published', 1)->where('inspected_result','通过');
        $order_count_for_refused = DK_Order::where('is_published', 1)->where('inspected_result','拒绝');
        $order_count_for_accepted_inside = DK_Order::where('is_published', 1)->where('inspected_result','内部通过');
        $order_count_for_repeat = DK_Order::where('is_published', 1)->where('is_repeat','>',0);



        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_inspected'] = $order_count_for_inspected;
        $return['order_count_for_accepted'] = $order_count_for_accepted;
        $return['order_count_for_refused'] = $order_count_for_refused;
        $return['order_count_for_repeat'] = $order_count_for_repeat;
        $return['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_all),2);




        // 工单统计

        // 本月每日工单量
        $query_for_order_this_month = DK_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_this_month->where('creator_id',$staff_id);
        if($client_id) $query_for_order_this_month->where('client_id',$client_id);
        if($car_id) $query_for_order_this_month->where('car_id',$car_id);
        if($route_id) $query_for_order_this_month->where('route_id',$route_id);
        if($pricing_id) $query_for_order_this_month->where('pricing_id',$pricing_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日工单量
        $query_for_order_last_month = DK_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "));

        if($staff_id) $query_for_order_last_month->where('creator_id',$staff_id);
        if($client_id) $query_for_order_last_month->where('client_id',$client_id);
        if($car_id) $query_for_order_last_month->where('car_id',$car_id);
        if($route_id) $query_for_order_last_month->where('route_id',$route_id);
        if($pricing_id) $query_for_order_last_month->where('pricing_id',$pricing_id);

        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;




        // 财务统计

//        $finance_this_month_income = YH_Finance::select('id')
//            ->where('finance_type',1)
//            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
//            ->sum("transaction_amount");
//
//        $finance_this_month_payout = YH_Finance::select('id')
//            ->where('finance_type',21)
//            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
//            ->sum("transaction_amount");
//
//
//        $finance_last_month_income = YH_Finance::select('id')
//            ->where('finance_type',1)
//            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$the_last_month_start_date,$the_last_month_ended_date])
//            ->sum("transaction_amount");
//
//        $finance_last_month_payout = YH_Finance::select('id')
//            ->where('finance_type',21)
//            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$the_last_month_start_date,$the_last_month_ended_date])
//            ->sum("transaction_amount");
//
//
//        $return_data['finance_this_month_income'] = $finance_this_month_income;
//        $return_data['finance_this_month_payout'] = $finance_this_month_payout;
//        $return_data['finance_last_month_income'] = $finance_last_month_income;
//        $return_data['finance_last_month_payout'] = $finance_last_month_payout;


        $query_for_finance = YH_Finance::select('id','transaction_amount','transaction_time','created_at')
            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(transaction_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(transaction_time,'%e') as day,
                    sum(transaction_amount) as sum,
                    count(*) as count
                "));

        if($staff_id)
        {
            $query_for_finance->whereHas('order_er', function ($query) use ($staff_id) {
                $query->where('creator_id', $staff_id);
            });
        }
        if($client_id)
        {
            $query_for_finance->whereHas('order_er', function ($query) use ($client_id) {
                $query->where('client_id', $client_id);
            });
        }
        if($car_id)
        {
            $query_for_finance->whereHas('order_er', function ($query) use ($car_id) {
                $query->where('car_id', $car_id);
            });
        }
        if($route_id)
        {
            $query_for_finance->whereHas('order_er', function ($query) use ($route_id) {
                $query->where('route_id', $route_id);
            });
        }
        if($pricing_id)
        {
            $query_for_finance->whereHas('order_er', function ($query) use ($pricing_id) {
                $query->where('pricing_id', $pricing_id);
            });
        }

        $query_for_income = clone $query_for_finance;
        $statistics_data_for_income = $query_for_income->where('finance_type',1)->get()->keyBy('day');
        $return_data['statistics_data_for_income'] = $statistics_data_for_income;


        $query_for_payout = clone $query_for_finance;
        $statistics_data_for_payout = $query_for_payout->where('finance_type',21)->get()->keyBy('day');
        $return_data['statistics_data_for_payout'] = $statistics_data_for_payout;


        return response_success($return_data,"");
    }
    // 【统计】返回-综合-数据
    public function get_statistic_data_for_comprehensive($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');
        $the_date_timestamp = strtotime($the_date);

        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-1',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

//        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
//        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始日期
//        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-结束日期
//        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
//        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
//        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
//        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';


        $query = DK_Order::select('*');

        if($me->user_type == 81)
        {
            $query->where('department_manager_id',$me->id);
        }
        else if($me->user_type == 84)
        {
            $query->where('department_supervisor_id',$me->id);
        }
        else if($me->user_type == 88)
        {
            $query->where('creator_id',$me->id);
        }
        else if($me->user_type == 71)
        {
            $query->where('inspector_id',$me->id);
        }
        else if($me->user_type == 77)
        {
            $query->where('inspector_id',$me->id);
        }


        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0]))
            {
                $query->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('department_district_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('department_district_id', $post_data['department_district']);
            }
        }




        // 工单统计
        // 总量
        $order_count_for_all = (clone $query)->count("*");
        $order_count_for_unpublished = (clone $query)->where('is_published', 0)->count("*");
        $order_count_for_published = (clone $query)->where('is_published', 1)->count("*");
        $order_count_for_waiting_for_inspect = (clone $query)->where('is_published', 1)->where('inspected_status', 0)->count("*");
        $order_count_for_inspected = (clone $query)->where('is_published', 1)->where('inspected_status', '<>', 0)->count("*");
        $order_count_for_accepted = (clone $query)->where('is_published', 1)->where('inspected_result','通过')->count("*");
        $order_count_for_accepted_inside = (clone $query)->where('is_published', 1)->where('inspected_result','内部通过')->count("*");
        $order_count_for_refused = (clone $query)->where('is_published', 1)->where('inspected_result','拒绝')->count("*");
        $order_count_for_repeated = (clone $query)->where('is_published', 1)->where('inspected_result','重复')->count("*");
        $order_count_for_repeat = (clone $query)->where('is_published', 1)->where('is_repeat','>',0)->count("*");

        $return_data['order_count_for_all'] = $order_count_for_all;
        $return_data['order_count_for_inspected'] = $order_count_for_inspected;
        $return_data['order_count_for_accepted'] = $order_count_for_accepted;
        $return_data['order_count_for_accepted_inside'] = $order_count_for_accepted_inside;
        $return_data['order_count_for_refused'] = $order_count_for_refused;
        $return_data['order_count_for_repeated'] = $order_count_for_repeated;
        if($order_count_for_inspected)
        {
            $return_data['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_inspected),2);
        }
        else $return_data['order_count_for_rate'] = 0;


        // 当天
        $order_count_of_today_for_all = (clone $query)->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_unpublished = (clone $query)->where('is_published', 0)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_published = (clone $query)->where('is_published', 1)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_waiting_for_inspect = (clone $query)->where('is_published', 1)->where('inspected_status', 0)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_inspected = (clone $query)->where('is_published', 1)->where('inspected_status', '<>', 0)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_accepted = (clone $query)->where('is_published', 1)->where('inspected_result','通过')
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_accepted_inside = (clone $query)->where('is_published', 1)->where('inspected_result','内部通过')
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_refused = (clone $query)->where('is_published', 1)->where('inspected_result','拒绝')
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_repeated = (clone $query)->where('is_published', 1)->where('inspected_result','重复')
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_date)->count("*");
        $order_count_of_today_for_repeat = (clone $query)->where('is_published', 1)->where('is_repeat','>',0)->count("*");


        $return_data['order_count_of_today_for_all'] = $order_count_of_today_for_all;
        $return_data['order_count_of_today_for_inspected'] = $order_count_of_today_for_inspected;
        $return_data['order_count_of_today_for_accepted'] = $order_count_of_today_for_accepted;
        $return_data['order_count_of_today_for_accepted_inside'] = $order_count_of_today_for_accepted_inside;
        $return_data['order_count_of_today_for_refused'] = $order_count_of_today_for_refused;
        $return_data['order_count_of_today_for_repeated'] = $order_count_of_today_for_repeated;
        if($order_count_of_today_for_inspected)
        {
            $return_data['order_count_of_today_for_rate'] = round(($order_count_of_today_for_accepted * 100 / $order_count_of_today_for_inspected),2);
        }
        else $return_data['order_count_of_today_for_rate'] = 0;




        // 当月
        $order_count_of_month_for_all = (clone $query)->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_unpublished = (clone $query)->where('is_published', 0)
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_published = (clone $query)->where('is_published', 1)
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_waiting_for_inspect = (clone $query)->where('is_published', 1)->where('inspected_status', 0)
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_inspected = (clone $query)->where('is_published', 1)->where('inspected_status', '<>', 0)
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_accepted = (clone $query)->where('is_published', 1)->where('inspected_result','通过')
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_accepted_inside = (clone $query)->where('is_published', 1)->where('inspected_result','内部通过')
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_refused = (clone $query)->where('is_published', 1)->where('inspected_result','拒绝')
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_repeated = (clone $query)->where('is_published', 1)->where('inspected_result','重复')
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");
        $order_count_of_month_for_repeat = (clone $query)->where('is_published', 1)->where('is_repeat','>',0)
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])->count("*");

        $return_data['order_count_of_month_for_all'] = $order_count_of_month_for_all;
        $return_data['order_count_of_month_for_inspected'] = $order_count_of_month_for_inspected;
        $return_data['order_count_of_month_for_accepted'] = $order_count_of_month_for_accepted;
        $return_data['order_count_of_month_for_accepted_inside'] = $order_count_of_month_for_accepted_inside;
        $return_data['order_count_of_month_for_refused'] = $order_count_of_month_for_refused;
        $return_data['order_count_of_month_for_repeated'] = $order_count_of_month_for_repeated;
        if($order_count_of_month_for_inspected)
        {
            $return_data['order_count_of_month_for_rate'] = round(($order_count_of_month_for_accepted * 100 / $order_count_of_month_for_inspected),2);
        }
        else $return_data['order_count_of_month_for_rate'] = 0;



        return response_success($return_data,"");
    }
    // 【统计】返回-财务-数据
    public function get_statistic_data_for_order($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';

        $staff_isset = 0;
        $client_isset = 0;
        $route_isset = 0;
        $pricing_isset = 0;
        $car_isset = 0;
        $trailer_isset = 0;
        $driver_isset = 0;


        // 员工
        if(isset($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1]))
            {
                $staff_isset = 1;
                $staff_id = $post_data['staff'];
            }
        }
        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1]))
            {
                $client_isset = 1;
                $client_id = $post_data['client'];
            }
        }
        // 线路
        if(isset($post_data['route']))
        {
            if(!in_array($post_data['route'],[-1]))
            {
                $route_isset = 1;
                $route_id = $post_data['route'];
            }
        }
        // 定价
        if(isset($post_data['pricing']))
        {
            if(!in_array($post_data['pricing'],[-1]))
            {
                $pricing_isset = 1;
                $pricing_id = $post_data['pricing'];
            }
        }
        // 车辆
        if(isset($post_data['car']))
        {
            if(!in_array($post_data['car'],[-1]))
            {
                $car_isset = 1;
                $car_id = $post_data['car'];
            }
        }
        // 车挂
        if(isset($post_data['trailer']))
        {
            if(!in_array($post_data['trailer'],[-1]))
            {
                $trailer_isset = 1;
                $trailer_id = $post_data['trailer'];
            }
        }
        // 驾驶员
        if(isset($post_data['driver']))
        {
            if(!in_array($post_data['driver'],[-1]))
            {
                $driver_isset = 1;
                $driver_id = $post_data['driver'];
            }
        }



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');


        // 工单统计


        // 本月每日工单量
        $query_for_order_this_month = DK_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_this_month->where('creator_id', $staff_id);
        if($client_isset) $query_for_order_this_month->where('client_id', $client_id);
        if($route_isset) $query_for_order_this_month->where('route_id', $route_id);
        if($pricing_isset) $query_for_order_this_month->where('pricing_id', $pricing_id);
        if($car_isset) $query_for_order_this_month->where('car_id', $car_id);
        if($trailer_isset) $query_for_order_this_month->where('trailer_id', $trailer_id);
        if($driver_isset) $query_for_order_this_month->where('driver_id', $driver_id);


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日工单量
        $query_for_order_last_month = DK_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$the_last_month_start_timestamp,$the_last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as quantity,
                    sum(amount + oil_card_amount) as income_sum
                "));

        if($staff_isset) $query_for_order_last_month->where('creator_id', $staff_id);
        if($client_isset) $query_for_order_last_month->where('client_id', $client_id);
        if($route_isset) $query_for_order_last_month->where('route_id', $route_id);
        if($pricing_isset) $query_for_order_last_month->where('pricing_id', $pricing_id);
        if($car_isset) $query_for_order_last_month->where('car_id', $car_id);
        if($trailer_isset) $query_for_order_last_month->where('trailer_id', $trailer_id);
        if($driver_isset) $query_for_order_last_month->where('driver_id', $driver_id);



        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;


        return response_success($return_data,"");
    }
    // 【统计】返回-财务-数据
    public function get_statistic_data_for_finance($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-1',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-1 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_last_month_timestamp = strtotime('last month', $the_month_timestamp);
        $the_last_month_start_date = date('Y-m-1',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_date = date('Y-m-t',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_start_datetime = date('Y-m-1 00:00:00',$the_last_month_timestamp); // 指定月份-上月-开始时间
        $the_last_month_ended_datetime = date('Y-m-t 23:59:59',$the_last_month_timestamp); // 指定月份-上月-结束时间
        $the_last_month_start_timestamp = strtotime($the_last_month_start_datetime); // 指定月份-上月-开始时间戳
        $the_last_month_ended_timestamp = strtotime($the_last_month_ended_datetime); // 指定月份-上月-结束时间戳



        $type = isset($post_data['type']) ? $post_data['type']  : '';


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');



        // 财务统计

//        $finance_this_month_income = YH_Finance::select('id')
//            ->where('finance_type',1)
//            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
//            ->sum("transaction_amount");
//
//        $finance_this_month_payout = YH_Finance::select('id')
//            ->where('finance_type',21)
//            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
//            ->sum("transaction_amount");
//
//
//        $finance_last_month_income = YH_Finance::select('id')
//            ->where('finance_type',1)
//            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$the_last_month_start_date,$the_last_month_ended_date])
//            ->sum("transaction_amount");
//
//        $finance_last_month_payout = YH_Finance::select('id')
//            ->where('finance_type',21)
//            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$the_last_month_start_date,$the_last_month_ended_date])
//            ->sum("transaction_amount");
//
//
//        $return_data['finance_this_month_income'] = $finance_this_month_income;
//        $return_data['finance_this_month_payout'] = $finance_this_month_payout;
//        $return_data['finance_last_month_income'] = $finance_last_month_income;
//        $return_data['finance_last_month_payout'] = $finance_last_month_payout;


        $query_for_finance = YH_Finance::select('id','transaction_amount','transaction_time','created_at')
            ->whereBetween('transaction_time',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(transaction_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(transaction_time,'%e') as day,
                    sum(transaction_amount) as sum,
                    count(*) as count
                "));


        $query_for_income = clone $query_for_finance;
        $statistics_data_for_income = $query_for_income->where('finance_type',1)->get()->keyBy('day');
        $return_data['statistics_data_for_income'] = $statistics_data_for_income;


        $query_for_payout = clone $query_for_finance;
        $statistics_data_for_payout = $query_for_payout->where('finance_type',21)->get()->keyBy('day');
        $return_data['statistics_data_for_payout'] = $statistics_data_for_payout;


        return response_success($return_data,"");
    }


    // 【统计】排名
    public function view_statistic_rank()
    {
        $this->get_me();
        $me = $this->me;

        $department_district_list = DK_Client_Department::select('id','name')->where('department_type',11)->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['department_district_id'] = $me->department_district_id;
            $department_group_list = DK_Client_Department::select('id','name')->where('superior_department_id',$me->department_district_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_rank'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-rank';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        $use = [];
        $use['is_manager'] = 0;
        $use['is_supervisor'] = 0;
        $use['is_customer_service'] = 0;
        $use['is_day'] = 0;
        $use['is_month'] = 0;
        $use['project_id'] = 0;
        $use['the_day'] = 0;
        $use['the_month_start_timestamp'] = 0;
        $use['the_month_ended_timestamp'] = 0;

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $use['project_id'] = $post_data['project'];
            }
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';

        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $use['is_day'] = 1;
            $use['the_day'] = $the_day;
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $use['is_month'] = 1;
            $use['the_month_start_timestamp'] = $the_month_start_timestamp;
            $use['the_month_ended_timestamp'] = $the_month_ended_timestamp;
        }




        $query = DK_Client_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 客服经理
        if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('department_group_id', $me->department_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('department_district_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('department_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);

            $query->withCount([
                'order_list_for_manager as order_count_for_all'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_manager as order_count_for_inspected'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->where('inspected_status', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_manager as order_count_for_accepted'=>function($query) use($use) {
                    $query->where('inspected_result', '通过')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_manager as order_count_for_refused'=>function($query) use($use) {
                    $query->where('inspected_result', '拒绝')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_manager as order_count_for_repeated'=>function($query) use($use) {
                    $query->where('inspected_result', '重复')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
//                'order_list_for_manager as order_count_for_accepted_inside'=>function($query) use($use) {
//                    $query->where('inspected_result', '内部通过')
//                        ->when($use['project_id'], function ($query) use ($use) {
//                            return $query->where('project_id', $use['project_id']);
//                        })
//                        ->when($use['is_month'], function ($query) use ($use) {
//                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
//                        })
//                        ->when($use['is_day'], function ($query) use ($use) {
//                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
//                        });
//                }
            ]);

        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);

            $query->withCount([
                'order_list_for_supervisor as order_count_for_all'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_supervisor as order_count_for_inspected'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->where('inspected_status', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_supervisor as order_count_for_accepted'=>function($query) use($use) {
                    $query->where('inspected_result', '通过')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_supervisor as order_count_for_refused'=>function($query) use($use) {
                    $query->where('inspected_result', '拒绝')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list_for_supervisor as order_count_for_repeated'=>function($query) use($use) {
                    $query->where('inspected_result', '重复')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
//                'order_list_for_supervisor as order_count_for_accepted_inside'=>function($query) use($use) {
//                    $query->where('inspected_result', '内部通过')
//                        ->when($use['project_id'], function ($query) use ($use) {
//                            return $query->where('project_id', $use['project_id']);
//                        })
//                        ->when($use['is_month'], function ($query) use ($use) {
//                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
//                        })
//                        ->when($use['is_day'], function ($query) use ($use) {
//                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
//                        });
//                }
            ]);

        }
        else
        {
            $query->where('department_district_id','>',0)
                ->where('department_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($use) {
                    $query->where('is_published', 1)
                        ->where('inspected_status', 1)
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($use) {
                    $query->where('inspected_result', '通过')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($use) {
                    $query->where('inspected_result', '拒绝')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($use) {
                    $query->where('inspected_result', '重复')
                        ->when($use['project_id'], function ($query) use ($use) {
                            return $query->where('project_id', $use['project_id']);
                        })
                        ->when($use['is_month'], function ($query) use ($use) {
                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
                        })
                        ->when($use['is_day'], function ($query) use ($use) {
                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
                        });
                },
//                'order_list as order_count_for_accepted_inside'=>function($query) use($use) {
//                    $query->where('inspected_result', '内部通过')
//                        ->when($use['project_id'], function ($query) use ($use) {
//                            return $query->where('project_id', $use['project_id']);
//                        })
//                        ->when($use['is_month'], function ($query) use ($use) {
//                            return $query->whereBetween('published_at',[$use['the_month_start_timestamp'],$use['the_month_ended_timestamp']]);
//                        })
//                        ->when($use['is_day'], function ($query) use ($use) {
//                            return $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$use['the_day']);
//                        });
//                }
            ]);

        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】员工排名
    public function view_statistic_rank_by_staff()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_staff'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-rank-by-staff';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // 客服经理
        if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Client_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳


            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】部门排名
    public function view_statistic_rank_by_department()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_rank_by_department'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-rank-by-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::select(['id','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // 客服经理
        if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Client_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳


            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【统计】客服看板
    public function view_statistic_customer_service()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_customer_service'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->where('department_district_id','>',0)
            ->where('department_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
//                $query->where('project_id', $post_data['project']);
                $project_id = $post_data['project'];
            }
        }

        // 客服经理
        if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_Client_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('department_district_id', $me->department_district_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('department_group_id', $me->department_group_id);
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->with([
                'department_district_er' => function($query) use($the_day,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_day,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($the_day,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_day,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '内部通过');
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->with([
                'department_district_er' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp,$project_id) {
                    $query->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
                        ->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);

        }
        else
        {
            $query->with([
                'department_district_er' => function($query) use($project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_manager as district_count_for_all' => function($query) use($project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    }
                                ]);
                        }
                    ]);
                },
                'department_group_er' => function($query) use($project_id) {
                    $query->select(['id','name','leader_id'])->with([
                        'leader' => function($query) use($project_id) {
                            $query->select(['id','username'])
                                ->withCount([
                                    'order_list_for_supervisor as group_count_for_all' => function($query) use($project_id) {
                                        $query->where('is_published', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            });
                                    }
                                ]);
                        }
                    ]);
                },
            ]);

            $query->withCount([
                'order_list as order_count_for_all'=>function($query) use($project_id) {
                    $query->where('is_published', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_inspected'=>function($query) use($project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted'=>function($query) use($project_id) {
                    $query->where('inspected_result', '通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_refused'=>function($query) use($project_id) {
                    $query->where('inspected_result', '拒绝')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_repeated'=>function($query) use($project_id) {
                    $query->where('inspected_result', '重复')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($project_id) {
                    $query->where('inspected_result', '内部通过')
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        });
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("department_district_id", "asc")->orderBy("department_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;




            // 小组数据
            // 总单数
            if(isset($v->department_group_er->leader->group_count_for_all))
            {
                $v->group_count_for_all = $v->department_group_er->leader->group_count_for_all;
            }
            else $v->group_count_for_all = 0;
            // 已审核单数
            if(isset($v->department_group_er->leader->group_count_for_inspected))
            {
                $v->group_count_for_inspected = $v->department_group_er->leader->group_count_for_inspected;
            }
            else $v->group_count_for_inspected = 0;
            // 通过单数
            if(isset($v->department_group_er->leader->group_count_for_accepted))
            {
                $v->group_count_for_accepted = $v->department_group_er->leader->group_count_for_accepted;
            }
            // 拒绝单数
            if(isset($v->department_group_er->leader->group_count_for_refused))
            {
                $v->group_count_for_refused = $v->department_group_er->leader->group_count_for_refused;
            }
            // 重复单数
            if(isset($v->department_group_er->leader->group_count_for_repeated))
            {
                $v->group_count_for_repeated = $v->department_group_er->leader->group_count_for_repeated;
            }
            $v->group_count_for_repeated = 0;

            // 有效单量
            $v->group_count_for_effective = $v->group_count_for_inspected - $v->group_count_for_refused - $v->group_count_for_repeated;

            $v->group_count_for_accepted_inside = 0;

            // 有效率
            if($v->group_count_for_all > 0)
            {
                $v->group_rate_for_accepted = round(($v->group_count_for_accepted * 100 / $v->group_count_for_all),2);
            }
            else $v->group_rate_for_accepted = 0;




            // 大区数据
            // 总单数
            if(isset($v->department_district_er->leader->district_count_for_all))
            {
                $v->district_count_for_all = $v->department_district_er->leader->district_count_for_all;
            }
            else $v->district_count_for_all = 0;
            // 已审核单数
            if(isset($v->department_district_er->leader->district_count_for_inspected))
            {
                $v->district_count_for_inspected = $v->department_district_er->leader->district_count_for_inspected;
            }
            else $v->district_count_for_inspected = 0;
            // 通过单数
            if(isset($v->department_district_er->leader->district_count_for_accepted))
            {
                $v->district_count_for_accepted = $v->department_district_er->leader->district_count_for_accepted;
            }
            else $v->district_count_for_accepted = 0;
            // 拒绝单数
            if(isset($v->department_district_er->leader->district_count_for_refused))
            {
                $v->district_count_for_refused = $v->department_district_er->leader->district_count_for_refused;
            }
            else $v->district_count_for_refused = 0;
            // 重复单数
            if(isset($v->department_district_er->leader->district_count_for_repeated))
            {
                $v->district_count_for_repeated = $v->department_district_er->leader->district_count_for_repeated;
            }
            else $v->district_count_for_repeated = 0;

            // 有效单量
            $v->district_count_for_effective = $v->district_count_for_inspected - $v->district_count_for_refused - $v->district_count_for_repeated;

            $v->district_count_for_accepted_inside = 0;

            // 有效率
            if($v->district_count_for_all > 0)
            {
                $v->district_rate_for_accepted = round(($v->district_count_for_accepted * 100 / $v->district_count_for_all),2);
            }
            else $v->district_rate_for_accepted = 0;

            $v->district_merge = 0;
            $v->group_merge = 0;
        }
//        dd($list->toArray());

        $grouped_by_district = $list->groupBy('department_district_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

            $grouped_by_group = $list->groupBy('department_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】客服看板
    public function view_statistic_inspector()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_inspector'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-inspector';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_inspector($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_User::select(['id','user_status','user_type','username','true_name','department_district_id','department_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
        if($me->user_type == 71)
        {
            $query->where(function ($query) use($me) {
                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
            });
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) use($the_day) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);
                }
            ]);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) use($the_month_start_timestamp,$the_month_ended_timestamp) {
                    $query->where('inspected_status', '<>', 0)
                        ->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
                }
            ]);
        }
        else
        {
            $query->withCount([
                'order_list_for_inspector as order_count_for_inspected'=>function($query) {
                    $query->where('inspected_status', '<>', 0);
                }
            ]);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 40;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("superior_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if($v->user_type == 71)
            {
                $v->superior_id = $v->id;
                $v->superior = $v->id;
                $superior = ['id'=>$v->id,'username'=>$v->username,'true_name'=>$v->true_name];
                $v->superior = json_decode(json_encode($superior));
                $list[$k]->superior =  json_decode(json_encode($superior));
            }
        }
//        dd($list->toArray());

        $a = [];
        $list = $list->sortBy('superior_id');
        $grouped = $list->sortBy('superior_id')->groupBy('superior_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_inspected = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_inspected += $val->order_count_for_inspected;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_inspected = $order_sum_for_inspected;

//                if($order_sum_for_all > 0)
//                {
//                    $v[$key]->order_average_rate_for_inspected = round(($order_sum_for_inspected * 100 / $order_sum_for_all),2);
//                }
//                else $v[$key]->order_average_rate_for_inspected = 0;
            }

            $v[0]->merge = count($v);
        }
        $collapsed = $grouped->collapse();

        return datatable_response($collapsed, $draw, $total);
    }




    // 【流量统计】返回-列表-视图
    public function view_statistic_list_for_all($post_data)
    {
        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【流量统计】返回-列表-数据
    public function get_statistic_list_for_all_datatable($post_data)
    {
        $me = Auth::guard("staff_admin")->user();
        $query = Def_Record::select('*')
            ->with(['creator','object','item']);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");

        if(!empty($post_data['open_device_type']))
        {
            if($post_data['open_device_type'] == "0")
            {
            }
            else if(in_array($post_data['open_system'],[1,2]))
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
            else if($post_data['open_device_type'] == "Unknown")
            {
                $query->where('open_device_type',"Unknown");
            }
            else if($post_data['open_device_type'] == "Others")
            {
                $query->whereNotIn('open_device_type',[1,2]);
            }
            else
            {
                $query->where('open_device_type',$post_data['open_device_type']);
            }
        }
        else
        {
//            $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
        }

        if(!empty($post_data['open_system']))
        {
            if($post_data['open_system'] == "0")
            {
            }
            else if($post_data['open_system'] == "1")
            {
                $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else if(in_array($post_data['open_system'],['Android','iPhone','iPad','Mac','Windows']))
            {
                $query->where('open_system',$post_data['open_system']);
            }
            else if($post_data['open_system'] == "Unknown")
            {
                $query->where('open_system',"Unknown");
            }
            else if($post_data['open_system'] == "Others")
            {
                $query->whereNotIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
            }
            else
            {
                $query->where('open_system',$post_data['open_system']);
            }
        }
        else
        {
//            $query->whereIn('open_system',['Android','iPhone','iPad','Mac','Windows']);
        }

        if(!empty($post_data['open_browser']))
        {
            if($post_data['open_browser'] == "0")
            {
            }
            else if($post_data['open_browser'] == "1")
            {
                $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else if(in_array($post_data['open_browser'],['Chrome','Firefox','Safari']))
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
            else if($post_data['open_browser'] == "Unknown")
            {
                $query->where('open_browser',"Unknown");
            }
            else if($post_data['open_browser'] == "Others")
            {
                $query->whereNotIn('open_browser',['Chrome','Firefox','Safari']);
            }
            else
            {
                $query->where('open_browser',$post_data['open_browser']);
            }
        }
        else
        {
//            $query->whereIn('open_browser',['Chrome','Firefox','Safari']);
        }

        if(!empty($post_data['open_app']))
        {
            if($post_data['open_app'] == "0")
            {
            }
            else if($post_data['open_app'] == "1")
            {
                $query->whereIn('open_app',['WeChat','QQ','Alipay']);
            }
            else if(in_array($post_data['open_app'],['WeChat','QQ','Alipay']))
            {
                $query->where('open_app',$post_data['open_app']);
            }
            else if($post_data['open_app'] == "Unknown")
            {
                $query->where('open_app',"Unknown");
            }
            else if($post_data['open_app'] == "Others")
            {
                $query->whereNotIn('open_app',['WeChat','QQ','Alipay']);
            }
            else
            {
                $query->where('open_app',$post_data['open_app']);
            }
        }
        else
        {
//            $query->whereIn('open_app',['WeChat','QQ']);
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
            $list[$k]->description = replace_blank($v->description);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    // 【统计】【业务报表】显示-视图
    public function view_statistic_delivery_by_daily()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,31,41])) return view($this->view_blade_403);

        // 显示数量
        if(!empty($post_data['length']))
        {
            if(is_numeric($post_data['length']) && $post_data['length'] > 0) $view_data['length'] = $post_data['length'];
            else $view_data['length'] = -1;
        }
        else $view_data['length'] = -1;
        // 第几页
        if(!empty($post_data['page']))
        {
            if(is_numeric($post_data['page']) && $post_data['page'] > 0) $view_data['page'] = $post_data['page'];
            else $view_data['page'] = 1;
        }
        else $view_data['page'] = 1;

        $view_data['menu_active_of_statistic_delivery_by_daily'] = 'active menu-open';
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-delivery-by-daily';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41]))
        {
            $department_district_id = $me->department_district_id;
        }
        else $department_district_id = 0;


        // 团队统计
        $query_order = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '已交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day)
            ->when($department_district_id, function ($query) use ($department_district_id) {
                return $query->where('department_district_id', $department_district_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Finance_Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot_project_user','pivot_project_team']);

        if(in_array($me->user_type,[41]))
        {
            $channel_id = $me->channel_id;
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$channel_id)->get();
            $query->whereIn('id',$project_list);
        }


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司或渠道-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot_project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['department_district']);
                });
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("name", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot_project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;
        $total_data['order_count_for_delivered_today'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }



            // 交付
            // 今日交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_today = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside + $v->order_count_for_delivered_repeated + $v->order_count_for_delivered_rejected;
            // 有效交付 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_inside;
            // 实际产出 = 已交付 + 内部交付
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed;


            // 有效交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 实际交付率
            if($v->order_count_for_delivered_today > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered_today),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;


            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_today'] += $v->order_count_for_delivered_today;
            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }


        // 交付
        // 有效交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;
        // 实际交付率
        if($total_data['order_count_for_delivered_today'] > 0)
        {
            $total_data['order_rate_for_delivered_actual'] = round(($total_data['order_count_for_delivered_actual'] * 100 / $total_data['order_count_for_delivered_today']),2);
        }
        else $total_data['order_rate_for_delivered_actual'] = 0;


        return datatable_response($list, $draw, $total);

    }
    // 【统计】【业务报表】返回-项目列表-数据
    public function get_statistic_data_for_delivery_of_project_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 日报统计
        $query_daily = DK_Finance_Daily::select('project_id')
            ->addSelect(DB::raw("
                    sum(delivery_quantity) as total_delivery_quantity,
                    sum(delivery_quantity_of_invalid) as total_delivery_quantity_of_invalid,
                    sum(total_daily_cost) as total_cost
                "))
//            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(published_at))"),$the_day)
            ->groupBy('project_id');


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                if(!empty($post_data['date']))
                {
                    $query_daily->whereDate("assign_date", $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
                if(!empty($post_data['assign_start']))
                {
                    $query_daily->whereDate("assign_date", ">=", $post_data['assign_start']);
                }
                if(!empty($post_data['assign_ended']))
                {
                    $query_daily->whereDate("assign_date", "<=", $post_data['assign_ended']);
                }
            }
            else
            {}
        }


        $query_daily = $query_daily->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Finance_Project::select('*')
            ->withTrashed()
            ->with(['creator','company_er','channel_er','business_or']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $query->whereIn('channel_id', $channel_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $query->where('channel_id', $post_data['channel']);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query->where('id', $post_data['project']);
            }
        }


        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            if(!in_array($post_data['item_status'],[-1,0]))
            {
                $query->where('item_status', $post_data['item_status']);
            }
        }
        else
        {
            $query->where('item_status', 1);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 100;

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
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '总计';
        $total_data['name'] = '总计';
        $total_data['date_day'] = '总计';
        $total_data['channel_id'] = 0;
        $total_data['business_id'] = 0;

        $total_data['total_delivery_quantity'] = 0;
        $total_data['total_delivery_quantity_of_invalid'] = 0;
        $total_data['delivery_effective_quantity'] = 0;
        $total_data['total_cost'] = 0;
        $total_data['channel_unit_price'] = 0;
        $total_data['channel_cost'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $total_data['cooperative_cost'] = 0;
        $total_data['funds_already_settled_total'] = 0;
        $total_data['funds_bad_debt_total'] = 0;
        $total_data['balance'] = 0;

        foreach($list as $k => $v)
        {

            if(isset($query_daily[$v->id]))
            {
                $list[$k]->total_delivery_quantity = $query_daily[$v->id]['total_delivery_quantity'];
                $list[$k]->total_delivery_quantity_of_invalid = $query_daily[$v->id]['total_delivery_quantity_of_invalid'];
                $list[$k]->total_cost = $query_daily[$v->id]['total_cost'];
            }
            else
            {
                $list[$k]->total_delivery_quantity = 0;
                $list[$k]->total_delivery_quantity_of_invalid = 0;
                $list[$k]->total_cost = 0;
            }
            $list[$k]->channel_cost = 0;
            $list[$k]->cooperative_cost = 0;
            $list[$k]->balance = 0;
            $list[$k]->cooperative_cost = ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));


            $total_data['total_delivery_quantity'] += $v->total_delivery_quantity;
            $total_data['total_delivery_quantity_of_invalid'] += $v->total_delivery_quantity_of_invalid;
            $total_data['total_cost'] += $v->total_cost;

            $total_data['channel_cost'] += ($v->channel_unit_price * $v->total_delivery_quantity);

            $total_data['cooperative_cost'] += ($v->cooperative_unit_price * ($v->total_delivery_quantity - $v->total_delivery_quantity_of_invalid));

            $total_data['funds_already_settled_total'] += $v->funds_already_settled_total;
            $total_data['funds_bad_debt_total'] += $v->funds_bad_debt_total;
        }

        $total_data['channel_unit_price'] = 0;
        $total_data['cooperative_unit_price'] = 0;
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计】【业务报表】返回-日报列表-数据
    public function get_statistic_data_for_delivery_of_daily_list_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Pivot_Client_Delivery::select('id','created_at')
            ->where('client_id',$me->client_id)
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(created_at))"))
            ->addSelect(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as formatted_date,
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as total_of_count,
                    count(IF(assign_status = 1, TRUE, NULL)) as total_of_assign
                "));



        if($me->user_type == 88)
        {
            $query->where('client_staff_id',$me->id);
        }


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                if(!empty($post_data['month']))
                {
                    $month_arr = explode('-', $post_data['month']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query->whereYear(DB::Raw("from_unixtime(created_at)"), $month_year)
                        ->whereMonth(DB::Raw("from_unixtime(created_at)"), $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                if(!empty($post_data['date']))
                {
                    $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['date']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }




        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

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
        else $list = $query->skip($skip)->take($limit)->get();


        $total_data = [];
        $total_data['id'] = '总计';
        $total_data['name'] = '--';
        $total_data['project_id'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['outbound_background'] = '--';
        $total_data['date_day'] = '统计';

        $total_data['attendance_manpower'] = 0;
        $total_data['delivery_quantity'] = 0;
        $total_data['delivery_quantity_of_invalid'] = 0;

        $total_data['manpower_daily_cost'] = 0;
        $total_data['call_charge_daily_cost'] = 0;
        $total_data['material_daily_quantity'] = 0;
        $total_data['material_daily_cost'] = 0;
        $total_data['taxes_daily_cost'] = 0;
        $total_data['total_daily_cost'] = 0;


//        foreach ($list as $k => $v)
//        {
//            if($v->creator_id == $me->id)
//            {
//                $list[$k]->is_me = 1;
//                $v->is_me = 1;
//            }
//            else
//            {
//                $list[$k]->is_me = 0;
//                $v->is_me = 0;
//            }
//
//            $total_data['attendance_manpower'] += $v->attendance_manpower;
//            $total_data['delivery_quantity'] += $v->delivery_quantity;
//            $total_data['delivery_quantity_of_invalid'] += $v->delivery_quantity_of_invalid;
//
//            $total_data['manpower_daily_cost'] += $v->manpower_daily_cost;
//            $total_data['call_charge_daily_cost'] += $v->call_charge_daily_cost;
//            $total_data['material_daily_cost'] += $v->material_daily_cost;
//            $total_data['taxes_daily_cost'] += $v->taxes_daily_cost;
//            $total_data['total_daily_cost'] += $v->total_daily_cost;
//        }
////        dd($list->toArray());
//
//        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计】【业务报表】返回-图标-数据
    public function get_statistic_data_for_delivery_of_daily_chart($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query_for_daily = DK_Finance_Daily::select('*')
//            ->where('finance_type',1)
            ->groupBy("assign_date")
            ->select(DB::raw("
                    assign_date as date,
                    DAY(assign_date) as day,
                    count(*) as quantity,
                    sum(attendance_manpower) as attendance_manpower_total,
                    sum(delivery_quantity) as delivery_quantity_total,
                    sum(delivery_quantity_of_invalid) as delivery_quantity_of_invalid_total,
                    sum(total_daily_cost) as total_daily_cost_total
                "));

        if($me->user_type == 41)
        {
            $project_list = DK_Finance_Project::select('id')->where('channel_id',$me->channel_id)->get();
            $query_for_daily->whereIn('project_id',$project_list);
        }


        // 公司
        if(isset($post_data['company']))
        {
            if(!in_array($post_data['company'],[-1]))
            {
                $channel_list = DK_Finance_Company::select('id')->where('superior_company_id',$post_data['company'])->get()->toArray();
                $project_list = DK_Finance_Project::select('id')->whereIn('channel_id',$channel_list)->get()->toArray();
                $query_for_daily->whereIn('project_id', $project_list);
            }
        }
        // 渠道
        if(isset($post_data['channel']))
        {
            if(!in_array($post_data['channel'],[-1]))
            {
                $project_list = DK_Finance_Project::select('id')->where('channel_id',$post_data['channel'])->get()->toArray();
                $query_for_daily->whereIn('project_id', $project_list);
            }
        }
        // 商务
        if(isset($post_data['business']))
        {
            if(!in_array($post_data['business'],[-1]))
            {
                $query_for_daily->where('business_id', $post_data['business']);
            }
        }
        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1]))
            {
                $query_for_daily->where('project_id', $post_data['project']);
            }
        }


        if(!empty($post_data['time_type']))
        {
            if($post_data['time_type'] == "month")
            {
                // 指定月份
                if(!empty($post_data['time']))
                {
                    $month_arr = explode('-', $post_data['time']);
                    $month_year = $month_arr[0];
                    $month_month = $month_arr[1];
                    $query_for_daily->whereYear("assign_date", $month_year)->whereMonth("assign_date", $month_month);
                }
            }
            else if($post_data['time_type'] == "date")
            {
                // 指定日期
                if(!empty($post_data['time']))
                {
                    $query_for_daily->whereDate("assign_date", $post_data['time']);
                }
            }
            else if($post_data['time_type'] == "period")
            {
            }
            else
            {}
        }




        $statistics_data_for_daily = $query_for_daily->get()->keyBy('day');

        foreach($statistics_data_for_daily as $k => $v)
        {
            // 人均
            if($v->attendance_manpower_total == 0)
            {
                $statistics_data_for_daily[$k]->per_capita = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->per_capita = $v->total_daily_cost_total / $v->attendance_manpower_total;
            }

            // 单均
            if($v->delivery_quantity_total == 0)
            {
                $statistics_data_for_daily[$k]->unit_average = 0;
            }
            else
            {
                $statistics_data_for_daily[$k]->unit_average = $v->total_daily_cost_total / $v->delivery_quantity_total;
            }
        }
//        dd($statistics_data_for_daily->toArray());
        $return_data['statistics_data'] = $statistics_data_for_daily;

        return response_success($return_data,"");
    }




    /*
     * Export 数据导出
     */
    // 【数据导出】
    public function view_statistic_export()
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = DK_Client_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $project_list = DK_Client_Project::select('id','name')->whereIn('item_type',[1,21])->get();

        $view_data['staff_list'] = $staff_list;


        $view_data['menu_active_of_statistic_export'] = 'active menu-open';

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.statistic.statistic-export';
        return view($view_blade)->with($view_data);
    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';

        $export_type = isset($post_data['export_type']) ? $post_data['export_type']  : '';
        if($export_type == "month")
        {
            $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $start_timestamp = $the_month_start_timestamp;
            $ended_timestamp = $the_month_ended_timestamp;

            $record_operate_type = 11;
            $record_column_type = 'month';
            $record_before = $the_month;
            $record_after = $the_month;
        }
        else if($export_type == "day")
        {
            $the_day  = isset($post_data['day']) ? $post_data['day']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'day';
            $record_before = $the_day;
            $record_after = $the_day;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Client_Record::select('*')
                ->where(['creator_id'=>$me->id,'operate_category'=>109,'operate_type'=>99])
                ->orderBy('id','desc')->first();

            if($record_last) $start_timestamp = $record_last->after;
            else $start_timestamp = 0;

            $ended_timestamp = $time;

            $record_operate_type = 99;
            $record_column_type = 'datetime';
            $record_before = '';
            $record_after = $time;
        }
        else
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].':00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].':59'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $staff_id = 0;
        $project_id = 0;

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0]))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Client_Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $order_quality = '';
        if(!empty($post_data['order_quality']))
        {
            if(in_array($post_data['order_quality'],config('info.order_quality')))
            {
                $order_quality = $post_data['order_quality'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_day  = isset($post_data['day'])  ? $post_data['day']  : date('Y-m-d');


        // 工单
        $query = DK_Order::select('*')
            ->where('client_id',$me->id)
            ->with([
                'creator'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','username','true_name'); },
                'project_er'=>function($query) { $query->select('id','name'); },
            ]);

        if($export_type == "month")
        {
            $query->whereBetween('delivered_at',[$start_timestamp,$ended_timestamp]);
        }
        else if($export_type == "day")
        {
            $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(delivered_at))"),$the_day);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('delivered_at',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '>=', $post_data['order_start']);
                $query->where('delivered_at', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '<=', $post_data['order_ended']);
                $query->where('delivered_at', '<=', $the_ended_timestamp);
            }
        }


        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($order_quality) $query->where('order_quality',$order_quality);


//        $data = $query->orderBy('inspected_at','desc')->orderBy('id','desc')->get();
//        $data = $query->orderBy('published_at','desc')->orderBy('id','desc')->get();
        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['delivered_time'] = date('Y-m-d H:i:s', $v['delivered_at']);
            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];
//            $cellData[$k]['recording_address'] = $v['recording_address'];
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'delivered_time'=>'交付时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        if($project_id)
        {
            $record_data["item_id"] = $project_id;
            $record_data["title"] = $record_data_title;
        }

        $record->fill($record_data)->save();


        $month_title = '';
        $time_title = '';
        if($export_type == "month")
        {
            $month_title = '【'.$the_month.'月】';
        }
        else if($export_type == "day")
        {
            $month_title = '【'.$the_day.'】';
        }
        else if($export_type == "latest")
        {
            $month_title = '【最新】';
        }
        else
        {
            if($the_start && $the_ended)
            {
                $time_title = '【'.$the_start.' - '.$the_ended.'】';
            }
            else if($the_start)
            {
                $time_title = '【'.$the_start.'】';
            }
            else if($the_ended)
            {
                $time_title = '【'.$the_ended.'】';
            }
        }


        $title = '【工单】'.date('Ymd.His').$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['order_er']['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['order_er']['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order_luxury_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Pivot_Client_Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['order_er']['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['order_er']['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['order_er']['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['order_er']['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

//            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order_by_order_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name'); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
//            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['delivered_at']);
            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            $cellData[$k]['channel_source'] = $v['channel_source'];
            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];



            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_phone'] = "未选择";


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];
//            $cellData[$k]['recording_address'] = $v['recording_address'];
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
//            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }






    // 【内容】返回-内容-HTML
    public function get_the_user_html($item)
    {
        $item->custom = json_decode($item->custom);
        $user_array[0] = $item;
        $return['user_list'] = $user_array;

        // method A
        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.user-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】返回-内容-HTML
    public function get_the_item_html($item)
    {
        $item->custom = json_decode($item->custom);
        $item_array[0] = $item;
        $return['item_list'] = $item_array;

        // method A
        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】删除-内容-附属文件
    public function delete_the_item_files($item)
    {
        $mine_id = $item->id;
        $mine_cover_pic = $item->cover_pic;
        $mine_attachment_src = $item->attachment_src;
        $mine_content = $item->content;

        // 删除二维码
        if(file_exists(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png')))
        {
            unlink(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png'));
        }

        // 删除原封面图片
        if(!empty($mine_cover_pic) && file_exists(storage_path("resource/" . $mine_cover_pic)))
        {
            unlink(storage_path("resource/" . $mine_cover_pic));
        }

        // 删除原附件
        if(!empty($mine_attachment_src) && file_exists(storage_path("resource/" . $mine_attachment_src)))
        {
            unlink(storage_path("resource/" . $mine_attachment_src));
        }

        // 删除UEditor图片
        $img_tags = get_html_img($mine_content);
        foreach ($img_tags[2] as $img)
        {
            if (!empty($img) && file_exists(public_path($img)))
            {
                unlink(public_path($img));
            }
        }
    }





    // 【内容】【全部】返回-列表-数据
    public function get_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client_Record::select('*')->withTrashed()
            ->with('client_creator')
            ->where('creator_id', $me->id)
//            ->where(['owner_id'=>100,'item_category'=>100])
//            ->where('item_type', '!=',0);
            ->where(['record_object'=>31,'operate_object'=>71]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");


        if($me->user_type == 11)
        {
            // 总经理
        }
        // 质检经理
        else if($me->user_type == 71)
        {

            $subordinates_array = DK_Client_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();

            $query->where(function($query) use($me,$subordinates_array) {
                $query->where('creator_id',$me->id)->orWhereIn('creator_id',$subordinates_array);
            });

        }
        else if($me->user_type == 77)
        {
            // 质检员
            $query->where('creator_id',$me->id);

        }
        else
        {

        }

        $item_type = isset($post_data['item_type']) ? $post_data['item_type'] : '';
        if($item_type == "record") $query->where('operate_category', 109);
//        else if($item_type == "object") $query->where('item_type', 1);
//        else if($item_type == "people") $query->where('item_type', 11);
//        else if($item_type == "product") $query->where('item_type', 22);
//        else if($item_type == "event") $query->where('item_type', 33);
//        else if($item_type == "conception") $query->where('item_type', 91);


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->description = replace_blank($v->description);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }



    /*
     * 说明
     *
     */


    // 【访问记录】
    public function record($post_data)
    {
        $record = new K_Record();

        $browseInfo = getBrowserInfo();
        $type = $browseInfo['type'];
        if($type == "Mobile") $post_data["open_device_type"] = 1;
        else if($type == "PC") $post_data["open_device_type"] = 2;

        $post_data["referer"] = $browseInfo['referer'];
        $post_data["open_system"] = $browseInfo['system'];
        $post_data["open_browser"] = $browseInfo['browser'];
        $post_data["open_app"] = $browseInfo['app'];

        $post_data["ip"] = Get_IP();
        $bool = $record->fill($post_data)->save();
        if($bool) return true;
        else return false;
    }


    // 【访问记录】
    public function record_for_user_operate($record_object,$record_category,$record_type,$creator_id,$item_id,$operate_object,$operate_category,$operate_type = 0,$column_key = '',$before = '',$after = '')
    {
        $record = new DK_Client_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = $record_object;
        $record_data["record_category"] = $record_category;
        $record_data["record_type"] = $record_type;
        $record_data["creator_id"] = $creator_id;
        $record_data["item_id"] = $item_id;
        $record_data["operate_object"] = $operate_object;
        $record_data["operate_category"] = $operate_category;
        $record_data["operate_type"] = $operate_type;
        $record_data["column_name"] = $column_key;
        $record_data["before"] = $before;
        $record_data["after"] = $after;

        $bool = $record->fill($record_data)->save();
        if($bool) return true;
        else return false;
    }




}