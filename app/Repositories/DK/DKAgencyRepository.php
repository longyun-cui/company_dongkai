<?php
namespace App\Repositories\DK;

use App\Models\DK_Choice\DK_Choice_Call_Record;

use App\Models\DK_Client\DK_Client_Department;
use App\Models\DK_Client\DK_Client_User;
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

class DKAgencyRepository {

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
        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);

        if(Auth::guard("dk_agency")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_agency")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

    }




    // 返回（后台）主页视图
    public function view_admin_index()
    {
        $this->get_me();
        if(!($this->auth_check))
        {
            $view_blade = env('TEMPLATE_DK_AGENCY').'entrance.login';
            return view($view_blade);
        }
        $me = $this->me;

        if($me->company_category == 1)
        {
            $client_list = DK_Client::where('company_id',$me->id)->get();
        }
        else if($me->company_category == 11)
        {
            $client_list = DK_Client::where('channel_id',$me->id)->get();
        }
        else if($me->company_category == 21)
        {
            $client_list = DK_Client::where('business_id',$me->id)->get();
        }
        else
        {
            $client_list = [];
        }

//        dd($client_list->toArray());
        $view_data['client_list'] = $client_list;
        $view_blade = env('TEMPLATE_DK_AGENCY').'entrance.index';
        return view($view_blade)->with($view_data);

        dd('已经登陆了');

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

        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.errors.404';
        return view($view_blade);
    }






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




    // 【交付管理】返回-列表-数据
    public function get_datatable_delivery_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Pivot_Client_Delivery::select('*')
//            ->where('client_id',$me->client_id)
            ->with([
                'client_er',
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



//        if(!empty($post_data['time_type']))
//        {
//            if($post_data['time_type'] == "month")
//            {
//                // 指定月份
//                if(!empty($post_data['month']))
//                {
//                    $month_arr = explode('-', $post_data['month']);
//                    $month_year = $month_arr[0];
//                    $month_month = $month_arr[1];
//
//                    $query->whereYear(DB::Raw("from_unixtime(created_at)"), $month_year)->whereMonth(DB::Raw("from_unixtime(created_at)"), $month_month);
//
////                    $month_start = $post_data['month'].'-01';
////                    if(in_array($month_month,['01','03','05','07','08','10','12'])) $month_ended = $post_data['month'].'-31';
////                    else if($month_month == "02") $month_ended = $post_data['month'].'-28';
////                    else $month_ended = $post_data['month'].'-30';
////
////                    $query->whereDate(DB::Raw("from_unixtime(created_at)"), '>=', $month_start)
////                        ->whereDate(DB::Raw("from_unixtime(created_at)"), '<=', $month_ended);
//                }
//            }
//            else if($post_data['time_type'] == "date")
//            {
//                // 指定日期
//                if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), $post_data['assign']);
//            }
//            else if($post_data['time_type'] == "period")
//            {
//
//                if(!empty($post_data['assign_start'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), '>=', $post_data['assign_start']);
//                if(!empty($post_data['assign_ended'])) $query->whereDate(DB::Raw("from_unixtime(created_at)"), '<=', $post_data['assign_ended']);
//            }
//            else
//            {}
//        }



        // 审核状态
        // 项目
//        if(!empty($post_data['exported_status']))
//        {
//            if(is_numeric($post_data['exported_status']) && $post_data['exported_status'] > 0)
//            {
//                $query->where('exported_status', $post_data['exported_status']);
//            }
//        }


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
//        if(isset($post_data['district']))
//        {
//            if(count($post_data['district']) > 0)
//            {
//                $query->whereHas('order_er', function($query) use($post_data) {
//                    $query->whereIn('location_district',$post_data['district']);
//                }
//                );
//            }
//        }




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

    public function get_datatable_delivery_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Pivot_Client_Delivery::select('company_id','channel_id','business_id','delivered_date')
            ->addSelect(DB::raw("
                    delivered_date as date_day,
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

    public function get_datatable_delivery_project($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Pivot_Client_Delivery::select('client_id','delivered_date')
            ->with(['client_er'])
            ->addSelect(DB::raw("
                    delivered_date as date_day,
                    count(*) as delivery_count
                "))
            ->groupBy('client_id')
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            });



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


    // 【数据导出】工单
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
            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];

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



}