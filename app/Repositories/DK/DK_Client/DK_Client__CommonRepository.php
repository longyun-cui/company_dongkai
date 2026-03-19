<?php
namespace App\Repositories\DK\DK_Client;

use App\Models\DK\DK_Client\DK_Client__Team;
use App\Models\DK\DK_Client\DK_Client__Staff;


use App\Models\DK_Client\DK_Client_Contact;

use App\Models\DK_Client\DK_Client_Record;
use App\Models\DK_Client\DK_Client_Finance_Daily;

use App\Models\DK\DK_Order;
use App\Models\DK\DK_Client;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Record_Current;


use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Delivery;



use App\Jobs\DK_Client\AutomaticDispatchingJob;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DK_Client__CommonRepository {

    private $env;
    private $auth_check;
    private $me;
    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;


    public function __construct()
    {
        $this->modelUser = new DK_Client__Staff;

        $this->view_blade_403 = env('DK_CLIENT__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_CLIENT__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_client__user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_client__user")->user();
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


        $order_count_for_all = DK_Common__Delivery::where('client_id',$me->client_id)
            ->count("*");
        $order_count_for_month = DK_Common__Delivery::where('client_id',$me->client_id)
            ->whereBetween('created_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->count("*");
        $order_count_for_today = DK_Common__Delivery::where('client_id',$me->client_id)
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(created_at))"),$the_date)
            ->count("*");
        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_month'] = $order_count_for_month;
        $return['order_count_for_today'] = $order_count_for_today;


        // 工单统计
        $query = DK_Order::select('id');

        // 本月每日工单量
        $query_this_month = DK_Common__Delivery::select('id','created_at')
            ->where('client_id',$me->client_id)
            ->whereBetween('created_at',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(created_at,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(created_at,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(created_at,'%e') as day,
                    count(*) as sum
                "));

        // 上月每日工单量
        $query_last_month = DK_Common__Delivery::select('id','created_at')
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

        $staff_list = DK_Client__Staff::select('id','username')
            ->where('client_id',$me->client_id)
            ->get();
        $return['staff_list'] = $staff_list;

        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.index';
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

        $staff_list = DK_Client__Staff::select('id','username')
            ->where('client_id',$me->client_id)
            ->whereNotIn('user_type',[0,1,9,11])
            ->get();
        $return['staff_list'] = $staff_list;

        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.index1';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.errors.404';
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
                    $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
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
                $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
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
            $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
            return view($view_blade)->with($view_data);

        }


    }


    public function view_data_of_order_detail($post_data)
    {
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.delivery-detail';

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

        $order = DK_Common__Order::select(['id','client_name','client_phone','wx_id','location_city','location_district','description','recording_address_list'])->find($order_id);
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
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.delivery-detail';

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

        $delivery = DK_Common__Delivery::with([
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

        $last_delivery = DK_Common__Delivery::select('*')
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
    public function o1__select2__team($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Client__Team::select(['id','name as text'])
            ->where('client_id',$me->client_id)
            ->where(['active'=>1,'item_status'=>1]);

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
//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>'-1','text'=>'选择部门'];
//        array_unshift($list,$unSpecified);
        return $list;
    }
    // 【员工】
    public function o1__select2__staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Client__Staff::select(['id','name as text'])
            ->where(['active'=>1,'item_status'=>1])
            ->where('client_id',$me->client_id)
            ->whereNotIn('staff_position',[0,1,9])
            ->when(in_array($me->staff_position,[41]), function ($query) use ($me) {
                return $query->where('team_id', $me->team_id);
            })
            ->when(in_array($me->staff_position,[61]), function ($query) use ($me) {
                return $query->where('team_group_id', $me->team_group_id);
            });

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
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
//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>'-1','text'=>'选择员工'];
//        array_unshift($list,$unSpecified);
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
                $staff_list = DK_Client__Staff::where('department_id',$me->department_id)->get()->pluck('id')->toArray();
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

        $query = DK_Common__Delivery::select('*')
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
                $staff_list = DK_Client__Staff::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
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
        $query = DK_Common__Delivery::select('company_id','channel_id','business_id','delivered_date')
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
        $query = DK_Common__Delivery::select('*')
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
                $mine = DK_Common__Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");


                $before = $mine->$assign_status;

                $mine->assign_status = $assign_status;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
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
                $mine = DK_Common__Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");

                $before = $mine->client_staff_id;

                $mine->client_staff_id = $client_staff_id;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
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
            $delivery_list = DK_Common__Delivery::withTrashed()
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
            $bool = DK_Common__Delivery::withTrashed()->whereIn('id',$ids_array)
                ->update($delivered_update);
            if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
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
//                $item = DK_Common__Delivery::withTrashed()->find($id);
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
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");
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

            if($column_key == 'is_automatic_dispatching' && $column_value == 1)
            {
                AutomaticDispatchingJob::dispatch($me->client_id);
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

        $staff_list = DK_Client__Staff::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
            ->where('client_id',$me->client_id)
            ->where('is_take_order',1)
            ->where('is_take_order_date',date('Y-m-d'))
            ->orderBy('is_take_order_datetime','asc')
            ->get();

        $delivery_list = DK_Common__Delivery::select('*')
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
//                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
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







}