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

use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

use App\Models\DK\DK_Common\DK_Statistic__Project_Daily;
use App\Models\DK\DK_Common\DK_Statistic__Client_Daily;

use App\Models\DK_CC\DK_CC_Call_Record_Current;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__StatisticRepository {

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







    // 【统计】返回-综合-数据
    public function o1__get_statistic_data_of_comprehensive_overview($post_data)
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


        $query = DK_Common__Order::select('id');
        $query_distributed = DK_Common__Delivery::select('id')->where('delivery_type',11);

        if($me->user_type == 41)
        {
            $query->where('team_id',$me->team_id);
        }
        else if($me->user_type == 81)
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
                $query_distributed->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('team_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('team_id', $post_data['department_district']);
            }
        }








        // 分发当天数据
        $query_distributed_of_all = (clone $query_distributed)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_all_for_all'] = $query_distributed_of_all[0]->distributed_count_for_all;




        // 工单统计
        // 总量统计
        $query_order_of_all = (clone $query)
            ->whereIn('created_type',[1,91,99])
            ->where('item_category',1)
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count_for_inspected_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count_for_inspected_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_count_for_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_count_for_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_all_for_all = $query_order_of_all[0]->order_count_for_all;
        $order_of_all_for_unpublished = $query_order_of_all[0]->order_count_for_unpublished;
        $order_of_all_for_published = $query_order_of_all[0]->order_count_for_published;

        $return_data['order_of_all_for_all'] = $order_of_all_for_all;
        $return_data['order_of_all_for_unpublished'] = $order_of_all_for_unpublished;
        $return_data['order_of_all_for_published'] = $order_of_all_for_published;


        $order_of_all_for_inspected_all = $query_order_of_all[0]->order_count_for_inspected_all;
        $order_of_all_for_inspected_accepted = $query_order_of_all[0]->order_count_for_inspected_accepted;
        $order_of_all_for_inspected_accepted_discount = $query_order_of_all[0]->order_count_for_inspected_accepted_discount;
        $order_of_all_for_inspected_accepted_suburb = $query_order_of_all[0]->order_count_for_inspected_accepted_suburb;
        $order_of_all_for_inspected_accepted_inside = $query_order_of_all[0]->order_count_for_inspected_accepted_inside;
        $order_of_all_for_inspected_refused = $query_order_of_all[0]->order_count_for_inspected_refused;
        $order_of_all_for_inspected_repeated = $query_order_of_all[0]->order_count_for_inspected_repeated;

        $return_data['order_of_all_for_inspected_all'] = $order_of_all_for_inspected_all;
        $return_data['order_of_all_for_inspected_accepted'] = $order_of_all_for_inspected_accepted;
        $return_data['order_of_all_for_inspected_accepted_discount'] = $order_of_all_for_inspected_accepted_discount;
        $return_data['order_of_all_for_inspected_accepted_suburb'] = $order_of_all_for_inspected_accepted_suburb;
        $return_data['order_of_all_for_inspected_accepted_inside'] = $order_of_all_for_inspected_accepted_inside;
        $return_data['order_of_all_for_inspected_refused'] = $order_of_all_for_inspected_refused;
        $return_data['order_of_all_for_inspected_repeated'] = $order_of_all_for_inspected_repeated;


        $order_of_all_for_delivered_all = $query_order_of_all[0]->order_count_for_delivered_all;
        $order_of_all_for_delivered_completed = $query_order_of_all[0]->order_count_for_delivered_completed;
        $order_of_all_for_delivered_discount = $query_order_of_all[0]->order_count_for_delivered_discount;
        $order_of_all_for_delivered_suburb = $query_order_of_all[0]->order_count_for_delivered_suburb;
        $order_of_all_for_delivered_inside = $query_order_of_all[0]->order_count_for_delivered_inside;
        $order_of_all_for_delivered_tomorrow = $query_order_of_all[0]->order_count_for_delivered_tomorrow;
        $order_of_all_for_delivered_repeated = $query_order_of_all[0]->order_count_for_delivered_repeated;
        $order_of_all_for_delivered_rejected = $query_order_of_all[0]->order_count_for_delivered_rejected;
        $order_of_all_for_delivered_effective = $order_of_all_for_delivered_completed + $order_of_all_for_delivered_tomorrow + $order_of_all_for_delivered_inside;
        if($order_of_all_for_all)
        {
            $order_of_all_for_delivered_effective_rate = round(($order_of_all_for_delivered_effective * 100 / $order_of_all_for_all),2);
        }
        else $order_of_all_for_delivered_effective_rate = 0;

        $return_data['order_of_all_for_delivered_all'] = $order_of_all_for_delivered_all;
        $return_data['order_of_all_for_delivered_completed'] = $order_of_all_for_delivered_completed;
        $return_data['order_of_all_for_delivered_discount'] = $order_of_all_for_delivered_discount;
        $return_data['order_of_all_for_delivered_suburb'] = $order_of_all_for_delivered_suburb;
        $return_data['order_of_all_for_delivered_inside'] = $order_of_all_for_delivered_inside;
        $return_data['order_of_all_for_delivered_tomorrow'] = $order_of_all_for_delivered_tomorrow;
        $return_data['order_of_all_for_delivered_repeated'] = $order_of_all_for_delivered_repeated;
        $return_data['order_of_all_for_delivered_rejected'] = $order_of_all_for_delivered_rejected;
        $return_data['order_of_all_for_delivered_effective'] = $order_of_all_for_delivered_effective;
        $return_data['order_of_all_for_delivered_effective_rate'] = $order_of_all_for_delivered_effective_rate;




        $query_delivered_of_all = (clone $query)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as delivered_count_for_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as delivered_count_for_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected
                "))
            ->get();

        $deliverer_of_all_for_all = $query_delivered_of_all[0]->delivered_count_for_all;
        $deliverer_of_all_for_completed = $query_delivered_of_all[0]->delivered_count_for_completed;
        $deliverer_of_all_for_discount = $query_delivered_of_all[0]->delivered_count_for_discount;
        $deliverer_of_all_for_suburb = $query_delivered_of_all[0]->delivered_count_for_suburb;
        $deliverer_of_all_for_inside = $query_delivered_of_all[0]->delivered_count_for_inside;
        $deliverer_of_all_for_tomorrow = $query_delivered_of_all[0]->delivered_count_for_tomorrow;
        $deliverer_of_all_for_repeated = $query_delivered_of_all[0]->delivered_count_for_repeated;
        $deliverer_of_all_for_rejected = $query_delivered_of_all[0]->delivered_count_for_rejected;


        $return_data['deliverer_of_all_for_all'] = $deliverer_of_all_for_all;
        $return_data['deliverer_of_all_for_completed'] = $deliverer_of_all_for_completed;
        $return_data['deliverer_of_all_for_discount'] = $deliverer_of_all_for_discount;
        $return_data['deliverer_of_all_for_suburb'] = $deliverer_of_all_for_suburb;
        $return_data['deliverer_of_all_for_inside'] = $deliverer_of_all_for_inside;
        $return_data['deliverer_of_all_for_tomorrow'] = $deliverer_of_all_for_tomorrow;
        $return_data['deliverer_of_all_for_repeated'] = $deliverer_of_all_for_repeated;
        $return_data['deliverer_of_all_for_rejected'] = $deliverer_of_all_for_rejected;








        // 分发当天数据
        $query_distributed_of_today = (clone $query_distributed)
            ->where('delivered_date',$the_date)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_today_for_all'] = $query_distributed_of_today[0]->distributed_count_for_all;




        // 客服报单-当天统计
        $query_order_of_today = (clone $query)->where('published_date',$the_date)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count_for_inspected_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count_for_inspected_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_count_for_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_count_for_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_today_for_all = $query_order_of_today[0]->order_count_for_all;
        $order_of_today_for_unpublished = $query_order_of_today[0]->order_count_for_unpublished;
        $order_of_today_for_published = $query_order_of_today[0]->order_count_for_published;

        $return_data['order_of_today_for_all'] = $order_of_today_for_all;
        $return_data['order_of_today_for_unpublished'] = $order_of_today_for_unpublished;
        $return_data['order_of_today_for_published'] = $order_of_today_for_published;


        $order_of_today_for_inspected_all = $query_order_of_today[0]->order_count_for_inspected_all;
        $order_of_today_for_inspected_accepted = $query_order_of_today[0]->order_count_for_inspected_accepted;
        $order_of_today_for_inspected_accepted_discount = $query_order_of_today[0]->order_count_for_inspected_accepted_discount;
        $order_of_today_for_inspected_accepted_suburb = $query_order_of_today[0]->order_count_for_inspected_accepted_suburb;
        $order_of_today_for_inspected_accepted_inside = $query_order_of_today[0]->order_count_for_inspected_accepted_inside;
        $order_of_today_for_inspected_refused = $query_order_of_today[0]->order_count_for_inspected_refused;
        $order_of_today_for_inspected_repeated = $query_order_of_today[0]->order_count_for_inspected_repeated;

        $return_data['order_of_today_for_inspected_all'] = $order_of_today_for_inspected_all;
        $return_data['order_of_today_for_inspected_accepted'] = $order_of_today_for_inspected_accepted;
        $return_data['order_of_today_for_inspected_accepted_discount'] = $order_of_today_for_inspected_accepted_discount;
        $return_data['order_of_today_for_inspected_accepted_suburb'] = $order_of_today_for_inspected_accepted_suburb;
        $return_data['order_of_today_for_inspected_accepted_inside'] = $order_of_today_for_inspected_accepted_inside;
        $return_data['order_of_today_for_inspected_refused'] = $order_of_today_for_inspected_refused;
        $return_data['order_of_today_for_inspected_repeated'] = $order_of_today_for_inspected_repeated;


        $order_of_today_for_delivered_all = $query_order_of_today[0]->order_count_for_delivered_all;
        $order_of_today_for_delivered_completed = $query_order_of_today[0]->order_count_for_delivered_completed;
        $order_of_today_for_delivered_discount = $query_order_of_today[0]->order_count_for_delivered_discount;
        $order_of_today_for_delivered_suburb = $query_order_of_today[0]->order_count_for_delivered_suburb;
        $order_of_today_for_delivered_inside = $query_order_of_today[0]->order_count_for_delivered_inside;
        $order_of_today_for_delivered_tomorrow = $query_order_of_today[0]->order_count_for_delivered_tomorrow;
        $order_of_today_for_delivered_repeated = $query_order_of_today[0]->order_count_for_delivered_repeated;
        $order_of_today_for_delivered_rejected = $query_order_of_today[0]->order_count_for_delivered_rejected;
        $order_of_today_for_delivered_effective = $order_of_today_for_delivered_completed + $order_of_today_for_delivered_tomorrow + $order_of_today_for_delivered_inside;
        if($order_of_today_for_all)
        {
            $order_of_today_for_delivered_effective_rate = round(($order_of_today_for_delivered_effective * 100 / $order_of_today_for_all),2);
        }
        else $order_of_today_for_delivered_effective_rate = 0;

        $return_data['order_of_today_for_delivered_all'] = $order_of_today_for_delivered_all;
        $return_data['order_of_today_for_delivered_completed'] = $order_of_today_for_delivered_completed;
        $return_data['order_of_today_for_delivered_discount'] = $order_of_today_for_delivered_discount;
        $return_data['order_of_today_for_delivered_suburb'] = $order_of_today_for_delivered_suburb;
        $return_data['order_of_today_for_delivered_inside'] = $order_of_today_for_delivered_inside;
        $return_data['order_of_today_for_delivered_tomorrow'] = $order_of_today_for_delivered_tomorrow;
        $return_data['order_of_today_for_delivered_repeated'] = $order_of_today_for_delivered_repeated;
        $return_data['order_of_today_for_delivered_rejected'] = $order_of_today_for_delivered_rejected;
        $return_data['order_of_today_for_delivered_effective'] = $order_of_today_for_delivered_effective;
        $return_data['order_of_today_for_delivered_effective_rate'] = $order_of_today_for_delivered_effective_rate;


        // 交付人员-工作统计
        $query_delivered_of_today = (clone $query)->where('delivered_date',$the_date)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_status = 1 AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_same_day,
                    count(IF(delivered_status = 1 AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_other_day,
                    
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '正常交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_same_day,
                    count(IF(delivered_result = '正常交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_other_day,
                    
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as delivered_count_for_discount,
                    count(IF(delivered_result = '折扣交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_discount_by_same_day,
                    count(IF(delivered_result = '折扣交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_discount_by_other_day,
                    
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as delivered_count_for_suburb,
                    count(IF(delivered_result = '郊区交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_suburb_by_same_day,
                    count(IF(delivered_result = '郊区交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_suburb_by_other_day,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '内部交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_same_day,
                    count(IF(delivered_result = '内部交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_other_day,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected
                "))
            ->get();

        $deliverer_of_today_for_all = $query_delivered_of_today[0]->delivered_count_for_all;
        $deliverer_of_today_for_all_by_same_day = $query_delivered_of_today[0]->delivered_count_for_all_by_same_day;
        $deliverer_of_today_for_all_by_other_day = $query_delivered_of_today[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_today_for_completed = $query_delivered_of_today[0]->delivered_count_for_completed;
        $deliverer_of_today_for_completed_by_same_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_same_day;
        $deliverer_of_today_for_completed_by_other_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_today_for_discount = $query_delivered_of_today[0]->delivered_count_for_discount;
        $deliverer_of_today_for_discount_by_same_day = $query_delivered_of_today[0]->delivered_count_for_discount_by_same_day;
        $deliverer_of_today_for_discount_by_other_day = $query_delivered_of_today[0]->delivered_count_for_discount_by_other_day;

        $deliverer_of_today_for_suburb = $query_delivered_of_today[0]->delivered_count_for_suburb;
        $deliverer_of_today_for_suburb_by_same_day = $query_delivered_of_today[0]->delivered_count_for_suburb_by_same_day;
        $deliverer_of_today_for_suburb_by_other_day = $query_delivered_of_today[0]->delivered_count_for_suburb_by_other_day;

        $deliverer_of_today_for_inside = $query_delivered_of_today[0]->delivered_count_for_inside;
        $deliverer_of_today_for_inside_by_same_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_same_day;
        $deliverer_of_today_for_inside_by_other_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_today_for_tomorrow = $query_delivered_of_today[0]->delivered_count_for_tomorrow;
        $deliverer_of_today_for_tomorrow_by_same_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_same_day;
        $deliverer_of_today_for_tomorrow_by_other_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_today_for_repeated = $query_delivered_of_today[0]->delivered_count_for_repeated;
        $deliverer_of_today_for_repeated_by_same_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_same_day;
        $deliverer_of_today_for_repeated_by_other_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_today_for_rejected = $query_delivered_of_today[0]->delivered_count_for_rejected;
        $deliverer_of_today_for_rejected_by_same_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_same_day;
        $deliverer_of_today_for_rejected_by_other_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_today_for_all'] = $deliverer_of_today_for_all;
        $return_data['deliverer_of_today_for_all_by_same_day'] = $deliverer_of_today_for_all_by_same_day;
        $return_data['deliverer_of_today_for_all_by_other_day'] = $deliverer_of_today_for_all_by_other_day;

        $return_data['deliverer_of_today_for_completed'] = $deliverer_of_today_for_completed;
        $return_data['deliverer_of_today_for_completed_by_same_day'] = $deliverer_of_today_for_completed_by_same_day;
        $return_data['deliverer_of_today_for_completed_by_other_day'] = $deliverer_of_today_for_completed_by_other_day;

        $return_data['deliverer_of_today_for_discount'] = $deliverer_of_today_for_discount;
        $return_data['deliverer_of_today_for_discount_by_same_day'] = $deliverer_of_today_for_discount_by_same_day;
        $return_data['deliverer_of_today_for_discount_by_other_day'] = $deliverer_of_today_for_discount_by_other_day;

        $return_data['deliverer_of_today_for_suburb'] = $deliverer_of_today_for_suburb;
        $return_data['deliverer_of_today_for_suburb_by_same_day'] = $deliverer_of_today_for_suburb_by_same_day;
        $return_data['deliverer_of_today_for_suburb_by_other_day'] = $deliverer_of_today_for_suburb_by_other_day;

        $return_data['deliverer_of_today_for_inside'] = $deliverer_of_today_for_inside;
        $return_data['deliverer_of_today_for_inside_by_same_day'] = $deliverer_of_today_for_inside_by_same_day;
        $return_data['deliverer_of_today_for_inside_by_other_day'] = $deliverer_of_today_for_inside_by_other_day;

        $return_data['deliverer_of_today_for_tomorrow'] = $deliverer_of_today_for_tomorrow;
        $return_data['deliverer_of_today_for_tomorrow_by_same_day'] = $deliverer_of_today_for_tomorrow_by_same_day;
        $return_data['deliverer_of_today_for_tomorrow_by_other_day'] = $deliverer_of_today_for_tomorrow_by_other_day;

        $return_data['deliverer_of_today_for_repeated'] = $deliverer_of_today_for_repeated;
        $return_data['deliverer_of_today_for_repeated_by_same_day'] = $deliverer_of_today_for_repeated_by_same_day;
        $return_data['deliverer_of_today_for_repeated_by_other_day'] = $deliverer_of_today_for_repeated_by_other_day;

        $return_data['deliverer_of_today_for_rejected'] = $deliverer_of_today_for_rejected;
        $return_data['deliverer_of_today_for_rejected_by_same_day'] = $deliverer_of_today_for_rejected_by_same_day;
        $return_data['deliverer_of_today_for_rejected_by_other_day'] = $deliverer_of_today_for_rejected_by_other_day;








        // 分发当月数据
        $query_distributed_of_month = (clone $query_distributed)
            ->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date])
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_month_for_all'] = $query_distributed_of_month[0]->distributed_count_for_all;




        // 当月统计
        $query_order_of_month = (clone $query)->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date])
            ->whereIn('created_type',[1,91,99])
            ->where('item_category',1)
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count_for_inspected_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count_for_inspected_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_count_for_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_count_for_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();


        $order_of_month_for_all = $query_order_of_month[0]->order_count_for_all;
        $order_of_month_for_unpublished = $query_order_of_month[0]->order_count_for_unpublished;
        $order_of_month_for_published = $query_order_of_month[0]->order_count_for_published;

        $return_data['order_of_month_for_all'] = $order_of_month_for_all;
        $return_data['order_of_month_for_unpublished'] = $order_of_month_for_unpublished;
        $return_data['order_of_month_for_published'] = $order_of_month_for_published;


        $order_of_month_for_inspected_all = $query_order_of_month[0]->order_count_for_inspected_all;
        $order_of_month_for_inspected_accepted = $query_order_of_month[0]->order_count_for_inspected_accepted;
        $order_of_month_for_inspected_accepted_discount = $query_order_of_month[0]->order_count_for_inspected_accepted_discount;
        $order_of_month_for_inspected_accepted_suburb = $query_order_of_month[0]->order_count_for_inspected_accepted_suburb;
        $order_of_month_for_inspected_accepted_inside = $query_order_of_month[0]->order_count_for_inspected_accepted_inside;
        $order_of_month_for_inspected_refused = $query_order_of_month[0]->order_count_for_inspected_refused;
        $order_of_month_for_inspected_repeated = $query_order_of_month[0]->order_count_for_inspected_repeated;

        $return_data['order_of_month_for_inspected_all'] = $order_of_month_for_inspected_all;
        $return_data['order_of_month_for_inspected_accepted'] = $order_of_month_for_inspected_accepted;
        $return_data['order_of_month_for_inspected_accepted_discount'] = $order_of_month_for_inspected_accepted_discount;
        $return_data['order_of_month_for_inspected_accepted_suburb'] = $order_of_month_for_inspected_accepted_suburb;
        $return_data['order_of_month_for_inspected_accepted_inside'] = $order_of_month_for_inspected_accepted_inside;
        $return_data['order_of_month_for_inspected_refused'] = $order_of_month_for_inspected_refused;
        $return_data['order_of_month_for_inspected_repeated'] = $order_of_month_for_inspected_repeated;


        $order_of_month_for_delivered_all = $query_order_of_month[0]->order_count_for_delivered_all;
        $order_of_month_for_delivered_completed = $query_order_of_month[0]->order_count_for_delivered_completed;
        $order_of_month_for_delivered_discount = $query_order_of_month[0]->order_count_for_delivered_discount;
        $order_of_month_for_delivered_suburb = $query_order_of_month[0]->order_count_for_delivered_suburb;
        $order_of_month_for_delivered_inside = $query_order_of_month[0]->order_count_for_delivered_inside;
        $order_of_month_for_delivered_tomorrow = $query_order_of_month[0]->order_count_for_delivered_tomorrow;
        $order_of_month_for_delivered_repeated = $query_order_of_month[0]->order_count_for_delivered_repeated;
        $order_of_month_for_delivered_rejected = $query_order_of_month[0]->order_count_for_delivered_rejected;
        $order_of_month_for_delivered_effective = $order_of_month_for_delivered_completed + $order_of_month_for_delivered_tomorrow + $order_of_month_for_delivered_inside;
        if($order_of_month_for_all)
        {
            $order_of_month_for_delivered_effective_rate = round(($order_of_month_for_delivered_effective * 100 / $order_of_month_for_all),2);
        }
        else $order_of_month_for_delivered_effective_rate = 0;

        $return_data['order_of_month_for_delivered_all'] = $order_of_month_for_delivered_all;
        $return_data['order_of_month_for_delivered_completed'] = $order_of_month_for_delivered_completed;
        $return_data['order_of_month_for_delivered_discount'] = $order_of_month_for_delivered_discount;
        $return_data['order_of_month_for_delivered_suburb'] = $order_of_month_for_delivered_suburb;
        $return_data['order_of_month_for_delivered_inside'] = $order_of_month_for_delivered_inside;
        $return_data['order_of_month_for_delivered_tomorrow'] = $order_of_month_for_delivered_tomorrow;
        $return_data['order_of_month_for_delivered_repeated'] = $order_of_month_for_delivered_repeated;
        $return_data['order_of_month_for_delivered_rejected'] = $order_of_month_for_delivered_rejected;
        $return_data['order_of_month_for_delivered_effective'] = $order_of_month_for_delivered_effective;
        $return_data['order_of_month_for_delivered_effective_rate'] = $order_of_month_for_delivered_effective_rate;




        $query_delivered_of_month = (clone $query)
            ->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date])
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as delivered_count_for_discount,
                    
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as delivered_count_for_suburb,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected
                "))
            ->get();

        $deliverer_of_month_for_all = $query_delivered_of_month[0]->delivered_count_for_all;
//        $deliverer_of_month_for_all_by_same_day = $query_delivered_of_month[0]->delivered_count_for_all_by_same_day;
//        $deliverer_of_month_for_all_by_other_day = $query_delivered_of_month[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_month_for_completed = $query_delivered_of_month[0]->delivered_count_for_completed;
//        $deliverer_of_month_for_completed_by_same_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_same_day;
//        $deliverer_of_month_for_completed_by_other_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_month_for_discount = $query_delivered_of_month[0]->delivered_count_for_discount;
//        $deliverer_of_month_for_discount_by_same_day = $query_delivered_of_month[0]->delivered_count_for_discount_by_same_day;
//        $deliverer_of_month_for_discount_by_other_day = $query_delivered_of_month[0]->delivered_count_for_discount_by_other_day;

        $deliverer_of_month_for_inside = $query_delivered_of_month[0]->delivered_count_for_inside;
//        $deliverer_of_month_for_inside_by_same_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_same_day;
//        $deliverer_of_month_for_inside_by_other_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_month_for_suburb = $query_delivered_of_month[0]->delivered_count_for_suburb;
//        $deliverer_of_month_for_suburb_by_same_day = $query_delivered_of_month[0]->delivered_count_for_suburb_by_same_day;
//        $deliverer_of_month_for_suburb_by_other_day = $query_delivered_of_month[0]->delivered_count_for_suburb_by_other_day;

        $deliverer_of_month_for_tomorrow = $query_delivered_of_month[0]->delivered_count_for_tomorrow;
//        $deliverer_of_month_for_tomorrow_by_same_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_same_day;
//        $deliverer_of_month_for_tomorrow_by_other_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_month_for_repeated = $query_delivered_of_month[0]->delivered_count_for_repeated;
//        $deliverer_of_month_for_repeated_by_same_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_same_day;
//        $deliverer_of_month_for_repeated_by_other_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_month_for_rejected = $query_delivered_of_month[0]->delivered_count_for_rejected;
//        $deliverer_of_month_for_rejected_by_same_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_same_day;
//        $deliverer_of_month_for_rejected_by_other_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_month_for_all'] = $deliverer_of_month_for_all;
//        $return_data['deliverer_of_month_for_all_by_same_day'] = $deliverer_of_month_for_all_by_same_day;
//        $return_data['deliverer_of_month_for_all_by_other_day'] = $deliverer_of_month_for_all_by_other_day;

        $return_data['deliverer_of_month_for_completed'] = $deliverer_of_month_for_completed;
//        $return_data['deliverer_of_month_for_completed_by_same_day'] = $deliverer_of_month_for_completed_by_same_day;
//        $return_data['deliverer_of_month_for_completed_by_other_day'] = $deliverer_of_month_for_completed_by_other_day;

        $return_data['deliverer_of_month_for_discount'] = $deliverer_of_month_for_discount;
//        $return_data['deliverer_of_month_for_discount_by_same_day'] = $deliverer_of_month_for_discount_by_same_day;
//        $return_data['deliverer_of_month_for_discount_by_other_day'] = $deliverer_of_month_for_discount_by_other_day;

        $return_data['deliverer_of_month_for_suburb'] = $deliverer_of_month_for_suburb;
//        $return_data['deliverer_of_month_for_suburb_by_same_day'] = $deliverer_of_month_for_suburb_by_same_day;
//        $return_data['deliverer_of_month_for_suburb_by_other_day'] = $deliverer_of_month_for_suburb_by_other_day;

        $return_data['deliverer_of_month_for_inside'] = $deliverer_of_month_for_inside;
//        $return_data['deliverer_of_month_for_inside_by_same_day'] = $deliverer_of_month_for_inside_by_same_day;
//        $return_data['deliverer_of_month_for_inside_by_other_day'] = $deliverer_of_month_for_inside_by_other_day;

        $return_data['deliverer_of_month_for_tomorrow'] = $deliverer_of_month_for_tomorrow;
//        $return_data['deliverer_of_month_for_tomorrow_by_same_day'] = $deliverer_of_month_for_tomorrow_by_same_day;
//        $return_data['deliverer_of_month_for_tomorrow_by_other_day'] = $deliverer_of_month_for_tomorrow_by_other_day;

        $return_data['deliverer_of_month_for_repeated'] = $deliverer_of_month_for_repeated;
//        $return_data['deliverer_of_month_for_repeated_by_same_day'] = $deliverer_of_month_for_repeated_by_same_day;
//        $return_data['deliverer_of_month_for_repeated_by_other_day'] = $deliverer_of_month_for_repeated_by_other_day;

        $return_data['deliverer_of_month_for_rejected'] = $deliverer_of_month_for_rejected;
//        $return_data['deliverer_of_month_for_rejected_by_same_day'] = $deliverer_of_month_for_rejected_by_same_day;
//        $return_data['deliverer_of_month_for_rejected_by_other_day'] = $deliverer_of_month_for_rejected_by_other_day;




        return response_success($return_data,"");
    }

    // 【统计】返回-综合-数据
    public function o1__get_statistic_data_of_statistic_comprehensive($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query_order = DK_Common__Order::where('is_published',1);
        $query_order_inspected = DK_Common__Order::where('is_published',1);
        $query_order_delivered = DK_Common__Order::where('is_published',1);
        $query_delivery = DK_Common__Delivery::select('order_category');


        if($me->user_type == 41)
        {
            $query_order->where('team_id',$me->team_id);
        }
        else if($me->user_type == 81)
        {
            $query_order->where('department_manager_id',$me->id);
        }
        else if($me->user_type == 84)
        {
            $query_order->where('department_supervisor_id',$me->id);
        }
        else if($me->user_type == 88)
        {
            $query_order->where('creator_id',$me->id);
        }
        else if($me->user_type == 71)
        {
            $query_order->where('inspector_id',$me->id);
        }
        else if($me->user_type == 77)
        {
            $query_order->where('inspector_id',$me->id);
        }


        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $query_order->where('project_id', $post_data['project']);
                $query_delivery->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('team_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            $department_district_array = array_diff($post_data['department_district'], [-1,0,'-1','0']);
            if(count($department_district_array))
            {
                $query_order->whereIn('team_id', $department_district_array);
            }
        }


        $query_order_published = (clone $query_order);
        $query_order_inspected = (clone $query_order);
        $query_order_delivered = (clone $query_order);


        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order_published->where('published_date',$the_date);
            $query_order_inspected->where('inspected_date',$the_date);
            $query_order_delivered->where('delivered_date',$the_date);

            $query_delivery->where('delivered_date',$the_date);
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

            $query_order_published->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_inspected->whereBetween('inspected_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_delivered->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);

            $query_delivery->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order_published->where('published_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_published->where('published_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_order_inspected->where('inspected_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_inspected->where('inspected_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_order_delivered->where('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_delivered->where('delivered_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_delivery->where('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_delivery->where('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
//            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
//            $query_order->where('published_date',$the_date);
//            $query_delivery->where('delivered_date',$the_date);
        }




        // 坐席发布-数据统计
        $query_order_published_data = $query_order_published
            ->whereIn('created_type',[1,91,99])
//            ->select('item_category')
            ->addSelect(DB::raw("
                    count(*) as order_count_for_published,
                    
                    count(IF(item_category = 1, TRUE, NULL)) as order_dental_for_published,
                    
                    count(IF(item_category = 1 and inspected_status <> 0, TRUE, NULL)) as order_dental_for_inspected_all,
                    count(IF(item_category = 1 and inspected_result = '通过', TRUE, NULL)) as order_dental_for_inspected_accepted,
                    count(IF(item_category = 1 and inspected_result = '郊区通过', TRUE, NULL)) as order_dental_for_inspected_accepted_suburb,
                    count(IF(item_category = 1 and inspected_result = '内部通过', TRUE, NULL)) as order_dental_for_inspected_accepted_inside,
                    count(IF(item_category = 1 and inspected_result = '重复', TRUE, NULL)) as order_dental_for_inspected_repeated,
                    count(IF(item_category = 1 and inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_dental_for_inspected_refused,
                    
                    count(IF(item_category = 1 and appealed_status > 0, TRUE, NULL)) as order_dental_for_appealed,
                    count(IF(item_category = 1 and appealed_result = 1, TRUE, NULL)) as order_dental_for_appealed_success,
                    count(IF(item_category = 1 and appealed_result = 9, TRUE, NULL)) as order_dental_for_appealed_fail,
                    
                    count(IF(item_category = 1 and delivered_status = 1, TRUE, NULL)) as order_dental_for_delivered_all,
                    count(IF(item_category = 1 and delivered_result = '正常交付', TRUE, NULL)) as order_dental_for_delivered_completed,
                    count(IF(item_category = 1 and delivered_result = '内部交付', TRUE, NULL)) as order_dental_for_delivered_inside,
                    count(IF(item_category = 1 and delivered_result = '隔日交付', TRUE, NULL)) as order_dental_for_delivered_tomorrow,
                    count(IF(item_category = 1 and delivered_result = '重复', TRUE, NULL)) as order_dental_for_delivered_repeated,
                    count(IF(item_category = 1 and delivered_result = '驳回', TRUE, NULL)) as order_dental_for_delivered_rejected,
                    
                    
                    count(IF(item_category = 31, TRUE, NULL)) as order_luxury_for_published,
                    
                    count(IF(item_category = 31 and inspected_status <> 0, TRUE, NULL)) as order_luxury_for_inspected_all,
                    count(IF(item_category = 31 and inspected_result = '通过', TRUE, NULL)) as order_luxury_for_inspected_accepted,
                    count(IF(item_category = 31 and inspected_result = '内部通过', TRUE, NULL)) as order_luxury_for_inspected_accepted_inside,
                    count(IF(item_category = 31 and inspected_result = '重复', TRUE, NULL)) as order_luxury_for_inspected_repeated,
                    count(IF(item_category = 31 and inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_luxury_for_inspected_refused,
                    
                    count(IF(item_category = 31 and delivered_status = 1, TRUE, NULL)) as order_luxury_for_delivered_all,
                    count(IF(item_category = 31 and delivered_result = '正常交付', TRUE, NULL)) as order_luxury_for_delivered_completed,
                    count(IF(item_category = 31 and delivered_result = '内部交付', TRUE, NULL)) as order_luxury_for_delivered_inside,
                    count(IF(item_category = 31 and delivered_result = '隔日交付', TRUE, NULL)) as order_luxury_for_delivered_tomorrow,
                    count(IF(item_category = 31 and delivered_result = '重复', TRUE, NULL)) as order_luxury_for_delivered_repeated,
                    count(IF(item_category = 31 and delivered_result = '驳回', TRUE, NULL)) as order_luxury_for_delivered_rejected
                "))
//            ->groupBy('item_category')
            ->get();
        if(count($query_order_published_data) > 0)
        {
            $order_published_data = $query_order_published_data[0];

            $order_published_data->order_dental_for_inspected_effective = $order_published_data->order_dental_for_inspected_accepted + $order_published_data->order_dental_for_inspected_accepted_inside;
            if($order_published_data->order_dental_for_published > 0)
            {
                $order_published_data->order_dental_for_inspected_effective_rate = round(($order_published_data->order_dental_for_inspected_effective / $order_published_data->order_dental_for_published * 100),2);
            }
            else $order_published_data->order_dental_for_inspected_effective_rate = 0;


            $return_data['order_published_data'] = $order_published_data;
        }
        else $return_data['order_published_data'] = [];


        // 质检审核-数据统计
        $query_order_inspected_data = $query_order_inspected
            ->whereIn('created_type',[1,91,99])
//            ->select('item_category')
            ->addSelect(DB::raw("
                    count(*) as order_count_for_published,
                    
                    count(IF(item_category = 1, TRUE, NULL)) as order_dental_for_inspected,
                    
                    count(IF(item_category = 1 and inspected_status <> 0, TRUE, NULL)) as order_dental_for_inspected_all,
                    count(IF(item_category = 1 and inspected_result = '通过', TRUE, NULL)) as order_dental_for_inspected_accepted,
                    count(IF(item_category = 1 and inspected_result = '郊区通过', TRUE, NULL)) as order_dental_for_inspected_accepted_suburb,
                    count(IF(item_category = 1 and inspected_result = '内部通过', TRUE, NULL)) as order_dental_for_inspected_accepted_inside,
                    count(IF(item_category = 1 and inspected_result = '重复', TRUE, NULL)) as order_dental_for_inspected_repeated,
                    count(IF(item_category = 1 and inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_dental_for_inspected_refused,
                    
                    
                    count(IF(item_category = 31, TRUE, NULL)) as order_luxury_for_inspected,
                    
                    count(IF(item_category = 31 and inspected_status <> 0, TRUE, NULL)) as order_luxury_for_inspected_all,
                    count(IF(item_category = 31 and inspected_result = '通过', TRUE, NULL)) as order_luxury_for_inspected_accepted,
                    count(IF(item_category = 31 and inspected_result = '内部通过', TRUE, NULL)) as order_luxury_for_inspected_accepted_inside,
                    count(IF(item_category = 31 and inspected_result = '重复', TRUE, NULL)) as order_luxury_for_inspected_repeated,
                    count(IF(item_category = 31 and inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_luxury_for_inspected_refused
                "))
//            ->groupBy('item_category')
            ->get();
        $return_data['order_inspected_data'] = $query_order_inspected_data[0];



        // 运营交付-数据统计
        $query_order_delivered_data = $query_order_delivered
            ->whereIn('created_type',[1,91,99])
//            ->select('item_category')
            ->addSelect(DB::raw("
                    
                    
                    count(IF(item_category = 1 and delivered_status = 1, TRUE, NULL)) as order_dental_for_delivered_all,
                    count(IF(item_category = 1 and delivered_status = 1 AND published_date = delivered_date, TRUE, NULL)) as order_dental_for_delivered_all_by_same_day,
                    count(IF(item_category = 1 and delivered_status = 1 AND published_date <> delivered_date, TRUE, NULL)) as order_dental_for_delivered_all_by_other_day,
                    
                    
                    count(IF(item_category = 1 and delivered_result = '正常交付', TRUE, NULL)) as order_dental_for_delivered_completed,
                    count(IF(item_category = 1 and delivered_result = '正常交付' AND published_date = delivered_date, TRUE, NULL)) as order_dental_for_delivered_completed_by_same_day,
                    count(IF(item_category = 1 and delivered_result = '正常交付' AND published_date <> delivered_date, TRUE, NULL)) as order_dental_for_delivered_completed_by_other_day,
                    
                    
                    count(IF(item_category = 1 and delivered_result = '内部交付', TRUE, NULL)) as order_dental_for_delivered_inside,
                    count(IF(item_category = 1 and delivered_result = '内部交付' AND published_date = delivered_date, TRUE, NULL)) as order_dental_for_delivered_inside_by_same_day,
                    count(IF(item_category = 1 and delivered_result = '内部交付' AND published_date <> delivered_date, TRUE, NULL)) as order_dental_for_delivered_inside_by_other_day,
                  
                    
                    count(IF(item_category = 1 and delivered_result = '隔日交付', TRUE, NULL)) as order_dental_for_delivered_tomorrow,
                    count(IF(item_category = 1 and delivered_result = '重复', TRUE, NULL)) as order_dental_for_delivered_repeated,
                    count(IF(item_category = 1 and delivered_result = '驳回', TRUE, NULL)) as order_dental_for_delivered_rejected,
                    
                   
                    count(IF(item_category = 31 and delivered_status = 1, TRUE, NULL)) as order_luxury_for_delivered_all,
                    count(IF(item_category = 31 and delivered_status = 1 AND published_date = delivered_date, TRUE, NULL)) as order_luxury_for_delivered_all_by_same_day,
                    count(IF(item_category = 31 and delivered_status = 1 AND published_date <> delivered_date, TRUE, NULL)) as order_luxury_for_delivered_all_by_other_day,
                    
                    
                    count(IF(item_category = 31 and delivered_result = '正常交付', TRUE, NULL)) as order_luxury_for_delivered_completed,
                    count(IF(item_category = 31 and delivered_result = '正常交付' AND published_date = delivered_date, TRUE, NULL)) as order_luxury_for_delivered_completed_by_same_day,
                    count(IF(item_category = 31 and delivered_result = '正常交付' AND published_date <> delivered_date, TRUE, NULL)) as order_luxury_for_delivered_completed_by_other_day,
                    
                    
                    count(IF(item_category = 31 and delivered_result = '内部交付', TRUE, NULL)) as order_luxury_for_delivered_inside,
                    count(IF(item_category = 31 and delivered_result = '内部交付' AND published_date = delivered_date, TRUE, NULL)) as order_luxury_for_delivered_inside_by_same_day,
                    count(IF(item_category = 31 and delivered_result = '内部交付' AND published_date <> delivered_date, TRUE, NULL)) as order_luxury_for_delivered_inside_by_other_day,
                    
                    
                    count(IF(item_category = 31 and delivered_result = '隔日交付', TRUE, NULL)) as order_luxury_for_delivered_tomorrow,
                    count(IF(item_category = 31 and delivered_result = '重复', TRUE, NULL)) as order_luxury_for_delivered_repeated,
                    count(IF(item_category = 31 and delivered_result = '驳回', TRUE, NULL)) as order_luxury_for_delivered_rejected
                "))
//            ->groupBy('item_category')
            ->get();
        $return_data['order_delivered_data'] = $query_order_delivered_data[0];


        // 交付数据
        $delivery_data = (clone $query_delivery)
            ->select(DB::raw("
                    count(IF(order_category = 1, TRUE, NULL)) as delivery_dental_for_all,
                    count(IF(delivery_type = 11, TRUE, NULL)) as delivery_dental_for_distributed
                "))
            ->get();
        $return_data['delivery_data'] = $delivery_data[0];


        return response_success($return_data,"");
    }

    // 【统计】返回-综合-日报
    public function o1__get_statistic_data_of_statistic_comprehensive_daily($post_data)
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




        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        $query_order = DK_Common__Order::select('creator_id','published_at','published_date')
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,91,99])
            ->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('published_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(published_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(published_date,'%e') as day,
                    count(*) as sum
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    COUNT(DISTINCT creator_id) AS attendance_manpower
                    
                "))
            ->orderBy("published_date", "desc");

        $total = $query_order->count();


        $query_delivery = DK_Common__Delivery::select('creator_id','delivered_date')
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('delivered_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(delivered_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(delivered_date,'%e') as day,
                    count(*) as sum
                "))
            ->addSelect(DB::raw("
                    count(*) as delivery_count_for_all,
                    
                    count(IF(delivery_type = 11, TRUE, NULL)) as delivery_count_for_distributed
                    
                "))
            ->orderBy("delivered_date", "desc");


        $query_cdr = DK_VOS_CDR::select('holdtime','call_date')
            ->whereBetween('call_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('call_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(call_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(call_date,'%e') as day,
                    count(*) as cnt,
                    sum(CEIL(holdtime / 60)) as minutes
                "))
            ->orderBy("call_date", "desc");

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $order_list = $query_order->get();
        $delivery_list = $query_delivery->get();
        $cdr_list = $query_cdr->get();

        // 转换为键值对的集合
        $keyed1 = $order_list->keyBy('date_day');
        $keyed2 = $delivery_list->keyBy('date_day');
        $keyed3 = $cdr_list->keyBy('date_day');
//        dd($keyed2->keys());

        // 获取所有唯一键
        $allIds = $keyed1->keys()->merge($keyed2->keys())->merge($keyed3->keys())->unique();
//        dd($allIds);

        // 合并对应元素
        $merged = $allIds->map(function ($id) use ($keyed1, $keyed2, $keyed3) {
            if($keyed3->get($id))
            {
                if($keyed1->get($id) && $keyed2->get($id))
                {
//                return $keyed1->get($id)->merge($keyed2->get($id));
                    return collect(array_merge(
                        $keyed1->get($id)->toArray(),
                        $keyed2->get($id)->toArray(),
                        $keyed3->get($id)->toArray()
                    ));
                }
                else if($keyed1->get($id) && !$keyed2->get($id))
                {
//                    return $keyed1->get($id);
                    return collect(array_merge(
                        $keyed1->get($id)->toArray(),
                        $keyed3->get($id)->toArray()
                    ));
                }
                else if(!$keyed1->get($id) && $keyed2->get($id))
                {
//                    return $keyed2->get($id);
                    return collect(array_merge(
                        $keyed2->get($id)->toArray(),
                        $keyed3->get($id)->toArray()
                    ));
                }
                else
                {
                    return $keyed3->get($id);
                }
            }
            else
            {
                if($keyed1->get($id) && $keyed2->get($id))
                {
//                return $keyed1->get($id)->merge($keyed2->get($id));
                    return collect(array_merge(
                        $keyed1->get($id)->toArray(),
                        $keyed2->get($id)->toArray()
                    ));
                }
                else if($keyed1->get($id) && !$keyed2->get($id))
                {
                    return $keyed1->get($id);
                }
                else if(!$keyed1->get($id) && $keyed2->get($id))
                {
                    return $keyed2->get($id);
                }
//            return array_merge(
//                $keyed1->get($id, [])->toArray(),
//                $keyed2->get($id, [])->toArray()
//            );
            }
        })->sortByDesc('date_day')->values(); // 重新索引为数字键

//        dd($merged->toArray());


        $total = $merged->count();



        $total_data = [];
        $total_data['published_at'] = 0;
        $total_data['date_day'] = '统计';
        $total_data['attendance_manpower'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_all_per'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_accepted_per'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;
        $total_data['order_count_for_effective'] = 0;
        $total_data['delivery_count_for_all'] = 0;
        $total_data['delivery_count_for_distributed'] = 0;

        $total_data['cnt'] = 0;
        $total_data['minutes'] = 0;



        foreach ($merged as $k => $v)
        {
            $total_data['attendance_manpower'] += $v['attendance_manpower'];

            // 审核
            $v['order_count_for_effective'] = $v['order_count_for_accepted'] + $v['order_count_for_repeated'] + $v['order_count_for_accepted_inside'];
            $merged[$k]['order_count_for_effective'] = $v['order_count_for_effective'];

            // 通过率
            if($v['order_count_for_all'] > 0)
            {
                $merged[$k]['order_rate_for_accepted'] = round(($v['order_count_for_accepted'] * 100 / $v['order_count_for_all']),2);
            }
            else $merged[$k]['order_rate_for_accepted'] = 0;

            // 有效率
            if($v['order_count_for_all'] > 0)
            {
                $merged[$k]['order_rate_for_effective'] = round(($v['order_count_for_effective'] * 100 / $v['order_count_for_all']),2);
            }
            else $merged[$k]['order_rate_for_effective'] = 0;


            // 人均提交量 && 人均通过量
            if($v['attendance_manpower'] > 0)
            {
                $merged[$k]['order_count_for_all_per'] = round(($v['order_count_for_all'] / $v['attendance_manpower']),2);
                $merged[$k]['order_count_for_accepted_per'] = round(($v['order_count_for_accepted'] / $v['attendance_manpower']),2);
            }
            else
            {
                $merged[$k]['order_count_for_all_per'] = 0;
                $merged[$k]['order_count_for_accepted_per'] = 0;
            }


            // 单均通话 & 单均分钟
            if($v['order_count_for_all'] > 0)
            {
                if(!empty($v['cnt']))
                {
                    $merged[$k]['cnt_per'] = round(($v['cnt'] / $v['order_count_for_all']),2);
                }
                else
                {
                    $merged[$k]['cnt'] = 0;
                    $merged[$k]['cnt_per'] = 0;
}
                if(!empty($v['minutes']))
                {
                    $merged[$k]['minutes_per'] = round(($v['minutes'] / $v['order_count_for_all']),2);
                }
                else
                {
                    $merged[$k]['minutes'] = 0;
                    $merged[$k]['minutes_per'] = 0;
                }
            }
            else
            {
                $merged[$k]['cnt_per'] = 0;
                $merged[$k]['minutes_per'] = 0;
            }


            $total_data['order_count_for_all'] += $v['order_count_for_all'];
            $total_data['order_count_for_inspected'] += $v['order_count_for_inspected'];
            $total_data['order_count_for_accepted'] += $v['order_count_for_accepted'];
            $total_data['order_count_for_refused'] += $v['order_count_for_refused'];
            $total_data['order_count_for_repeated'] += $v['order_count_for_repeated'];
            $total_data['order_count_for_accepted_inside'] += $v['order_count_for_accepted_inside'];
            $total_data['order_count_for_effective'] += $merged[$k]['order_count_for_effective'];

            $total_data['delivery_count_for_all'] += $merged[$k]['delivery_count_for_all'];
            $total_data['delivery_count_for_distributed'] += $merged[$k]['delivery_count_for_distributed'];

            $total_data['cnt'] += $merged[$k]['cnt'];
            $total_data['minutes'] += $merged[$k]['minutes'];

        }

        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;

        // 有效率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_effective'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_effective'] = 0;


        // 人均提交量 && 人均通过量
        if($total_data['attendance_manpower'] > 0)
        {
            $total_data['order_count_for_all_per'] = round(($total_data['order_count_for_all'] / $total_data['attendance_manpower']),2);
            $total_data['order_count_for_accepted_per'] = round(($total_data['order_count_for_accepted'] / $total_data['attendance_manpower']),2);
        }
        else
        {
            $total_data['order_count_for_all_per'] = 0;
            $total_data['order_count_for_accepted_per'] = 0;
        }


        // 单均通话 & 单均分钟
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['cnt_per'] = round(($total_data['cnt'] / $total_data['order_count_for_all']),2);
            $total_data['minutes_per'] = round(($total_data['minutes'] / $total_data['order_count_for_all']),2);
        }
        else
        {
            $total_data['cnt_per'] = 0;
            $total_data['minutes_per'] = 0;
        }

        $merged[] = $total_data;

//        dd($list->toArray());

        return datatable_response($merged, $draw, $total);
    }





    // 【统计】返回-通话-日报-月览
    public function o1__get_statistic_data_of_statistic_call_list($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_VOS_CDR::select('*');

//        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['phone'])) $query->where('phone', $post_data['phone']);


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(!empty($post_data['length']) && $post_data['length'] = -1) $limit = 50;


        if(!empty($post_data['phone']))
        {
            $query->orderBy("call_date", "desc");
        }
        else $query->orderBy("id", "desc");

//        if(isset($post_data['order']))
//        {
//            $columns = $post_data['columns'];
//            $order = $post_data['order'][0];
//            $order_column = $order['column'];
//            $order_dir = $order['dir'];
//
//            $field = $columns[$order_column]["data"];
//            $query->orderBy($field, $order_dir);
//        }
//        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】返回-综合-日报-概览
    public function o1__get_statistic_data_of_statistic_call_daily_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query_order = DK_Common__Order::where('is_published',1);
        $query_call = DK_VOS_CDR::select('phone');


        if($me->user_type == 41)
        {
            $query_order->where('team_id',$me->team_id);
        }
        else if($me->user_type == 81)
        {
            $query_order->where('department_manager_id',$me->id);
        }
        else if($me->user_type == 84)
        {
            $query_order->where('department_supervisor_id',$me->id);
        }
        else if($me->user_type == 88)
        {
            $query_order->where('creator_id',$me->id);
        }
        else if($me->user_type == 71)
        {
            $query_order->where('inspector_id',$me->id);
        }
        else if($me->user_type == 77)
        {
            $query_order->where('inspector_id',$me->id);
        }


        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $query_order->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('team_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            $department_district_array = array_diff($post_data['department_district'], [-1,0,'-1','0']);
            if(count($department_district_array))
            {
                $query_order->whereIn('team_id', $department_district_array);
            }
        }


        $query_order_published = (clone $query_order);
        $query_order_inspected = (clone $query_order);
        $query_order_delivered = (clone $query_order);


        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order_published->where('published_date',$the_date);
            $query_order_inspected->where('inspected_date',$the_date);
            $query_order_delivered->where('delivered_date',$the_date);

            $query_call->where('call_date',$the_date);
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

            $query_order_published->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_inspected->whereBetween('inspected_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_delivered->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);

            $query_call->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order_published->where('published_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_published->where('published_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_order_inspected->where('inspected_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_inspected->where('inspected_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_order_delivered->where('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order_delivered->where('delivered_date', '<=', $post_data['date_ended']);

            if(!empty($post_data['date_start'])) $query_call->where('call_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_call->where('call_date', '<=', $post_data['date_ended']);
        }
        else
        {
//            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
//            $query_order->where('published_date',$the_date);
//            $query_delivery->where('delivered_date',$the_date);
        }




        // 坐席发布-数据统计
        $query_order_published_data = (clone $query_order_published)
            ->whereIn('created_type',[1,91,99])
//            ->select('item_category')
            ->addSelect(DB::raw("
                    count(*) as order_for_all,
                    
                    count(IF(item_category = 1, TRUE, NULL)) as dental_for_all,
                    count(IF(item_category = 1 and inspected_status <> 0, TRUE, NULL)) as dental_for_inspected_all,
                    count(IF(item_category = 1 and inspected_result = '通过', TRUE, NULL)) as dental_for_inspected_accepted
                "))
//            ->groupBy('item_category')
            ->get();
        if(count($query_order_published_data) > 0)
        {
            $order_published_data = $query_order_published_data[0];

            $return_data['order_data'] = $order_published_data;
        }
        else $return_data['order_data'] = [];


        $query_order_s_data = (clone $query_order_published)
            ->addSelect(DB::raw("
                    client_phone,
                    COUNT(a_vos_e_cdr.phone) AS call_count
                "))
            ->whereIn('dk_admin_order.created_type',[1,99])
            ->join('a_vos_e_cdr', 'a_vos_e_cdr.phone', '=', 'dk_admin_order.client_phone')
            ->where('a_vos_e_cdr.call_date', '<', $the_date)
            ->groupBy('dk_admin_order.client_phone')
        ->get();
//        dd($query_order_s_data->groupBy('call_count')->toArray());
        $order_s_data = $query_order_s_data->groupBy('call_count');
        $return_data['order_s_data'] = $order_s_data;

//        SELECT
//    do.client_phone,
//    COUNT(vc.phone) AS call_count
//FROM (SELECT DISTINCT client_phone FROM dk_admin_order) do
//        LEFT JOIN a_vos_e_cdr vc ON do.client_phone = vc.phone
//GROUP BY do.client_phone;



        // 通话数据
        $call_total = (clone $query_call)
            ->select(DB::raw("
                    count(*) as call_for_all
                "))
            ->first();
        $call_data['call_for_all'] = $call_total->call_for_all;

        $call_dealt = (clone $query_call)
            ->join('dk_admin_order', 'a_vos_e_cdr.phone', '=', 'dk_admin_order.client_phone')
            ->where('dk_admin_order.published_date', '<', $the_date)
            ->select(DB::raw("
                    count(*) as call_for_dealt
                "))
            ->first();
        $call_data['call_for_dealt'] = $call_dealt->call_for_dealt;

        $return_data['call_data'] = $call_data;


        return response_success($return_data,"");
    }
    // 【统计】返回-通话-日报-月览
    public function o1__get_statistic_data_of_statistic_call_daily_month($post_data)
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




        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        $query_order = DK_Common__Order::select('creator_id','published_at','published_date')
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,91,99])
            ->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('published_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(published_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(published_date,'%e') as day,
                    count(*) as sum,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    COUNT(DISTINCT creator_id) AS attendance_manpower
                "))
            ->orderBy("published_date", "desc");

        $total = $query_order->count();



        $query_cdr = DK_VOS_CDR::select('holdtime','call_date')
            ->whereBetween('call_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('call_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(call_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(call_date,'%e') as day,
                    count(*) as cnt,
                    sum(CEIL(holdtime / 60)) as minutes,
                    count(IF(holdtime <= 8, TRUE, NULL)) as cnt_8
                "))
            ->orderBy("call_date", "desc");

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $order_list = $query_order->get();
        $cdr_list = $query_cdr->get();

        // 转换为键值对的集合
        $keyed2 = $order_list->keyBy('date_day');
        $keyed1 = $cdr_list->keyBy('date_day');
//        dd($keyed2->keys());

        // 获取所有唯一键
        $allIds = $keyed1->keys()->merge($keyed2->keys())->unique();
//        dd($allIds);

        // 合并对应元素
        $merged = $allIds->map(function ($id) use ($keyed1, $keyed2) {
            if($keyed1->get($id) && $keyed2->get($id))
            {
//                return $keyed1->get($id)->merge($keyed2->get($id));
                return collect(array_merge(
                    $keyed1->get($id)->toArray(),
                    $keyed2->get($id)->toArray()
                ));
            }
            else if($keyed1->get($id) && !$keyed2->get($id))
            {
                return $keyed1->get($id);
            }
            else if(!$keyed1->get($id) && $keyed2->get($id))
            {
                return $keyed2->get($id);
            }
        })->sortByDesc('date_day')->values(); // 重新索引为数字键

//        dd($merged->toArray());


        $total = $merged->count();



        $total_data = [];
        $total_data['published_at'] = 0;
        $total_data['date_day'] = '统计';
        $total_data['attendance_manpower'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_all_per'] = 0;

        $total_data['cnt'] = 0;
        $total_data['cnt_8'] = 0;
        $total_data['minutes'] = 0;



        foreach ($merged as $k => $v)
        {
            $total_data['attendance_manpower'] += $v['attendance_manpower'];


            // 人均提交量 && 人均通过量
            if($v['attendance_manpower'] > 0)
            {
                $merged[$k]['cnt_per_for_manpower'] = round((($v['cnt'] - $v['cnt_8']) / $v['attendance_manpower']),2);
                $merged[$k]['minutes_per_for_manpower'] = round((($v['minutes'] - $v['cnt_8']) / $v['attendance_manpower']),2);
            }
            else
            {
                $merged[$k]['cnt_per_for_manpower'] = 0;
                $merged[$k]['minutes_per_for_manpower'] = 0;
            }


            // 单均通话 & 单均分钟
            if($v['order_count_for_all'] > 0)
            {
                if(!empty($v['cnt']))
                {
                    $merged[$k]['cnt_per'] = round(($v['cnt'] / $v['order_count_for_all']),2);
                }
                else
                {
                    $merged[$k]['cnt'] = 0;
                    $merged[$k]['cnt_per'] = 0;
                }
                if(!empty($v['minutes']))
                {
                    $merged[$k]['minutes_per'] = round(($v['minutes'] / $v['order_count_for_all']),2);
                }
                else
                {
                    $merged[$k]['minutes'] = 0;
                    $merged[$k]['minutes_per'] = 0;
                }
            }
            else
            {
                $merged[$k]['cnt_per'] = 0;
                $merged[$k]['minutes_per'] = 0;
            }


            $total_data['order_count_for_all'] += $v['order_count_for_all'];

            $total_data['cnt'] += $merged[$k]['cnt'];
            $total_data['cnt_8'] += $merged[$k]['cnt_8'];
            $total_data['minutes'] += $merged[$k]['minutes'];

        }


        // 人均通话量 && 人均通话分钟
        if($total_data['attendance_manpower'] > 0)
        {
            $total_data['cnt_per_for_manpower'] = round((($total_data['cnt'] - $total_data['cnt_8']) / $total_data['attendance_manpower']),2);
            $total_data['minutes_per_for_manpower'] = round((($total_data['minutes'] - $total_data['cnt_8']) / $total_data['attendance_manpower']),2);
        }
        else
        {
            $total_data['cnt_per_for_manpower'] = 0;
            $total_data['minutes_per_for_manpower'] = 0;
        }


        // 单均通话 & 单均分钟
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['cnt_per'] = round(($total_data['cnt'] / $total_data['order_count_for_all']),2);
            $total_data['minutes_per'] = round(($total_data['minutes'] / $total_data['order_count_for_all']),2);
        }
        else
        {
            $total_data['cnt_per'] = 0;
            $total_data['minutes_per'] = 0;
        }

        $merged[] = $total_data;

//        dd($list->toArray());

        return datatable_response($merged, $draw, $total);
    }


    // 【统计】返回-通话-日报-月览
    public function o1__get_statistic_data_of_statistic_call_order_daily_month($post_data)
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




        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 城市
        $city = 0;
        if(isset($post_data['city']))
        {
            if(!in_array($post_data['city'],['-1','0']))
            {
                $city = $post_data['city'];
            }
        }

        $query_order = DK_A_Order::select('order_date')
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereBetween('order_date',[$the_month_start_date,$the_month_ended_date])
            ->when($city, function ($query) use ($city) {
                return $query->where('region_name', $city);
            })
            ->groupBy('order_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(order_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(order_date,'%e') as day,
                    count(*) as count,
                    sum(call_cnt_1_8) as sum_call_cnt_1_8,
                    sum(call_cnt_9_15) as sum_call_cnt_9_15,
                    sum(call_cnt_16_25) as sum_call_cnt_16_25,
                    sum(call_cnt_26_45) as sum_call_cnt_26_45,
                    sum(call_cnt_46_90) as sum_call_cnt_46_90,
                    sum(call_cnt_91_above) as sum_call_cnt_91_above
                "))
            ->orderBy("order_date", "desc");



        $total = $query_order->count();



        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_order->get();



        $total_data = [];
        $total_data['order_date'] = 0;
        $total_data['date_day'] = '统计';
        $total_data['count'] = 0;
        $total_data['sum_all'] = 0;
        $total_data['sum_call_cnt_1_8'] = 0;
        $total_data['sum_call_cnt_9_15'] = 0;
        $total_data['sum_call_cnt_16_25'] = 0;
        $total_data['sum_call_cnt_26_45'] = 0;
        $total_data['sum_call_cnt_46_90'] = 0;
        $total_data['sum_call_cnt_91_above'] = 0;

        $total_data['cnt'] = 0;
        $total_data['cnt_8'] = 0;
        $total_data['minutes'] = 0;



        foreach ($list as $k => $v)
        {

            $v->sum_all = $v->sum_call_cnt_1_8
                        + $v->sum_call_cnt_9_15
                        + $v->sum_call_cnt_16_25
                        + $v->sum_call_cnt_26_45
                        + $v->sum_call_cnt_46_90
                        + $v->sum_call_cnt_91_above;
//            $list[$v]->sum_all = $v->sum_all;

            // 单均通话次数
            if($v->count > 0)
            {
                $v->per_call = round(($v->sum_all / $v->count),2);
                $v->per_call_cnt_1_8 = round(($v->sum_call_cnt_1_8 / $v->count),2);
                $v->per_call_cnt_9_15 = round(($v->sum_call_cnt_9_15 / $v->count),2);
                $v->per_call_cnt_16_25 = round(($v->sum_call_cnt_16_25 / $v->count),2);
                $v->per_call_cnt_26_45 = round(($v->sum_call_cnt_26_45 / $v->count),2);
                $v->per_call_cnt_46_90 = round(($v->sum_call_cnt_46_90 / $v->count),2);
                $v->per_call_cnt_91_above = round(($v->sum_call_cnt_91_above / $v->count),2);
            }
            else
            {
                $v->per_call = 0;
                $v->per_call_cnt_1_8 = 0;
                $v->per_call_cnt_9_15 = 0;
                $v->per_call_cnt_16_25 = 0;
                $v->per_call_cnt_26_45 = 0;
                $v->per_call_cnt_46_90 = 0;
                $v->per_call_cnt_91_above = 0;
            }

            $total_data['count'] += $v->count;
            $total_data['sum_all'] += $v->sum_all;
            $total_data['sum_call_cnt_1_8'] += $v->sum_call_cnt_1_8;
            $total_data['sum_call_cnt_9_15'] += $v->sum_call_cnt_9_15;
            $total_data['sum_call_cnt_16_25'] += $v->sum_call_cnt_16_25;
            $total_data['sum_call_cnt_26_45'] += $v->sum_call_cnt_26_45;
            $total_data['sum_call_cnt_46_90'] += $v->sum_call_cnt_46_90;
            $total_data['sum_call_cnt_91_above'] += $v->sum_call_cnt_91_above;

            if($total_data['count'] > 0)
            {
                $total_data['per_call'] = round(($total_data['sum_all'] / $total_data['count']),2);
                $total_data['per_call_cnt_1_8'] = round(($total_data['sum_call_cnt_1_8'] / $total_data['count']),2);
                $total_data['per_call_cnt_9_15'] = round(($total_data['sum_call_cnt_9_15'] / $total_data['count']),2);
                $total_data['per_call_cnt_16_25'] = round(($total_data['sum_call_cnt_16_25'] / $total_data['count']),2);
                $total_data['per_call_cnt_26_45'] = round(($total_data['sum_call_cnt_26_45'] / $total_data['count']),2);
                $total_data['per_call_cnt_46_90'] = round(($total_data['sum_call_cnt_46_90'] / $total_data['count']),2);
                $total_data['per_call_cnt_91_above'] = round(($total_data['sum_call_cnt_91_above'] / $total_data['count']),2);
            }
            else
            {
                $total_data['per_call'] = 0;
                $total_data['per_call_cnt_1_8'] = 0;
                $total_data['per_call_cnt_9_15'] = 0;
                $total_data['per_call_cnt_16_25'] = 0;
                $total_data['per_call_cnt_26_45'] = 0;
                $total_data['per_call_cnt_46_90'] = 0;
                $total_data['per_call_cnt_91_above'] = 0;
            }

        }

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【统计】返回-通话-日报-月览
    public function o1__get_statistic_data_of_statistic_call_order_city($post_data)
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




        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 城市
        $city = 0;
        if(isset($post_data['city']))
        {
            if(!in_array($post_data['city'],['-1','0']))
            {
                $city = $post_data['city'];
            }
        }

        $query_order = DK_A_Order::select('region_name')
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
//            ->whereBetween('order_date',[$the_month_start_date,$the_month_ended_date])
//            ->when($city, function ($query) use ($city) {
//                return $query->where('region_name', $city);
//            })
            ->groupBy('region_name')
            ->addSelect(DB::raw("
                    count(*) as count,
                    sum(call_cnt_1_8) as sum_call_cnt_1_8,
                    sum(call_cnt_9_15) as sum_call_cnt_9_15,
                    sum(call_cnt_16_25) as sum_call_cnt_16_25,
                    sum(call_cnt_26_45) as sum_call_cnt_26_45,
                    sum(call_cnt_46_90) as sum_call_cnt_46_90,
                    sum(call_cnt_91_above) as sum_call_cnt_91_above
                "))
            ->orderBy("count", "desc");


        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_order->get();
        $total = $list->count();



        $total_data = [];
        $total_data['order_date'] = 0;
        $total_data['region_name'] = '统计';
        $total_data['count'] = 0;
        $total_data['sum_all'] = 0;
        $total_data['sum_call_cnt_1_8'] = 0;
        $total_data['sum_call_cnt_9_15'] = 0;
        $total_data['sum_call_cnt_16_25'] = 0;
        $total_data['sum_call_cnt_26_45'] = 0;
        $total_data['sum_call_cnt_46_90'] = 0;
        $total_data['sum_call_cnt_91_above'] = 0;

        $total_data['cnt'] = 0;
        $total_data['cnt_8'] = 0;
        $total_data['minutes'] = 0;



        foreach ($list as $k => $v)
        {

            $v->sum_all = $v->sum_call_cnt_1_8
                + $v->sum_call_cnt_9_15
                + $v->sum_call_cnt_16_25
                + $v->sum_call_cnt_26_45
                + $v->sum_call_cnt_46_90
                + $v->sum_call_cnt_91_above;
//            $list[$v]->sum_all = $v->sum_all;

            // 单均通话次数
            if($v->count > 0)
            {
                $v->per_call = round(($v->sum_all / $v->count),2);
                $v->per_call_cnt_1_8 = round(($v->sum_call_cnt_1_8 / $v->count),2);
                $v->per_call_cnt_9_15 = round(($v->sum_call_cnt_9_15 / $v->count),2);
                $v->per_call_cnt_16_25 = round(($v->sum_call_cnt_16_25 / $v->count),2);
                $v->per_call_cnt_26_45 = round(($v->sum_call_cnt_26_45 / $v->count),2);
                $v->per_call_cnt_46_90 = round(($v->sum_call_cnt_46_90 / $v->count),2);
                $v->per_call_cnt_91_above = round(($v->sum_call_cnt_91_above / $v->count),2);
            }
            else
            {
                $v->per_call = 0;
                $v->per_call_cnt_1_8 = 0;
                $v->per_call_cnt_9_15 = 0;
                $v->per_call_cnt_16_25 = 0;
                $v->per_call_cnt_26_45 = 0;
                $v->per_call_cnt_46_90 = 0;
                $v->per_call_cnt_91_above = 0;
            }

            $total_data['count'] += $v->count;
            $total_data['sum_all'] += $v->sum_all;
            $total_data['sum_call_cnt_1_8'] += $v->sum_call_cnt_1_8;
            $total_data['sum_call_cnt_9_15'] += $v->sum_call_cnt_9_15;
            $total_data['sum_call_cnt_16_25'] += $v->sum_call_cnt_16_25;
            $total_data['sum_call_cnt_26_45'] += $v->sum_call_cnt_26_45;
            $total_data['sum_call_cnt_46_90'] += $v->sum_call_cnt_46_90;
            $total_data['sum_call_cnt_91_above'] += $v->sum_call_cnt_91_above;

            if($total_data['count'] > 0)
            {
                $total_data['per_call'] = round(($total_data['sum_all'] / $total_data['count']),2);
                $total_data['per_call_cnt_1_8'] = round(($total_data['sum_call_cnt_1_8'] / $total_data['count']),2);
                $total_data['per_call_cnt_9_15'] = round(($total_data['sum_call_cnt_9_15'] / $total_data['count']),2);
                $total_data['per_call_cnt_16_25'] = round(($total_data['sum_call_cnt_16_25'] / $total_data['count']),2);
                $total_data['per_call_cnt_26_45'] = round(($total_data['sum_call_cnt_26_45'] / $total_data['count']),2);
                $total_data['per_call_cnt_46_90'] = round(($total_data['sum_call_cnt_46_90'] / $total_data['count']),2);
                $total_data['per_call_cnt_91_above'] = round(($total_data['sum_call_cnt_91_above'] / $total_data['count']),2);
            }
            else
            {
                $total_data['per_call'] = 0;
                $total_data['per_call_cnt_1_8'] = 0;
                $total_data['per_call_cnt_9_15'] = 0;
                $total_data['per_call_cnt_16_25'] = 0;
                $total_data['per_call_cnt_26_45'] = 0;
                $total_data['per_call_cnt_46_90'] = 0;
                $total_data['per_call_cnt_91_above'] = 0;
            }

        }

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }






    public function o1__get_statistic_data_of_company_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query_delivery = DK_Common__Delivery::select('company_id','channel_id','business_id')
            ->addSelect(DB::raw("
                    count(*) as delivery_count_for_all
                "))
            ->groupBy('company_id')
            ->groupBy('channel_id')
            ->groupBy('business_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_delivery->whereDate('delivered_date',$the_date);

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

            $query_delivery->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_delivery->whereDate('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_delivery->whereDate('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        $delivery_list = $query_delivery->get();




        $query = DK_Company::select('id','name','user_status','company_category','company_type','superior_company_id')
            ->with([
//                'superior' => function($query) { $query->select(['id','username','true_name']); },
//                'team_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
//                'team_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
            ])
            ->where('user_status',1)
            ->whereIn('company_category',[1,11,21]);



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
        else $query->orderBy("superior_company_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();
//        dd($list->toArray());

        $list_all = [];
        $grouped_by_category = $list->groupBy('company_category');
//        dd($list->toArray());
//        dd($grouped_by_category[11]->toArray());
        $grouped_by_superior = $list->groupBy('superior_company_id');
//        dd($grouped_by_superior->toArray());


        // 公司
        foreach($grouped_by_category[1] as $k => $v)
        {
            $collect_company = collect([]);
            $list_company = [];
            $index = 0;

            // 渠道是否存在
            if(isset($grouped_by_superior[$v->id]))
            {
                // 渠道
                foreach($grouped_by_superior[$v->id] as $ke => $val)
                {
                    $index++;

                    $un['index'] = $index;
                    $un['id'] = 0;
                    $un['name'] = '未分配商务';
                    $un['company_category'] = 11;
                    $un['company_id'] = $v->id;
                    $un['company_name'] = $v->name;
                    $un['channel_id'] = $val->id;
                    $un['channel_name'] = $val->name;
                    $un['business_id'] = 0;
                    $un['business_name'] = '--';
                    $un['company_merge'] = 0;
                    if(isset($grouped_by_superior[$val->id])) $un['channel_merge'] = count($grouped_by_superior[$val->id]) + 1;
                    else $un['channel_merge'] = 1;
                    $list_company[] = $un;

                    // 商务是否存在
                    if(isset($grouped_by_superior[$val->id]))
                    {
                        $un['channel_merge'] = count($grouped_by_superior[$val->id]);
                        // 商务
                        foreach($grouped_by_superior[$val->id] as $key => $value)
                        {
                            $index++;

                            $value->index = $index;
                            $value->company_id = $v->id;
                            $value->company_name = $v->name;
                            $value->channel_id = $val->id;
                            $value->channel_name = $val->name;
                            $value->business_id = $value->id;
                            $value->business_name = $value->name;
                            $value->company_merge = 0;
                            $value->channel_merge = 0;
                            $list_company[] = $value->toArray();
                        }
                    }
                }

                $list_company[0]['company_merge'] = $index;
            }
            else
            {
                $un['id'] = 0;
                $un['name'] = '';
                $un['company_category'] = 1;
                $un['company_id'] = $v->id;
                $un['company_name'] = $v->name;
                $un['channel_id'] = 0;
                $un['channel_name'] = '--';
                $un['business_id'] = 0;
                $un['business_name'] = '--';
                $un['company_merge'] = 1;
                $un['channel_merge'] = 1;
                $list_company[] = $un;
            }


            $list_all = array_merge($list_all,$list_company);

        }
//        dd($delivery_list->toArray());


        $delivery_list = collect($delivery_list->toArray());
        foreach($list_all as $k => $v)
        {
            $list_all[$k]['delivery_count'] = 0;
            $list_all[$k]['delivery_count_for_channel'] = 0;
            $list_all[$k]['delivery_count_for_company'] = 0;

            // 统计【商务】交付量
            $delivery = $delivery_list->where('company_id',$v['company_id'])
                ->where('channel_id',$v['channel_id'])
                ->where('business_id',$v['business_id']);
            if($delivery->isNotEmpty() && $delivery->count() > 0)
            {
                $list_all[$k]['delivery_count'] = $delivery->first()['delivery_count_for_all'];
            }

            $delivery = $delivery_list->where('company_id',$v['company_id']);
//            dd($delivery);

            // 统计【公司】交付量
            if($v['company_category'] == 11 && $v['company_merge'] > 0)
            {
                $delivery = $delivery_list->where('company_id',$v['company_id']);
//                dd($delivery);
                if($delivery->isNotEmpty() && $delivery->count() > 0)
                {
                    foreach($delivery as $delivery_ed)
                    {
                        $list_all[$k]['delivery_count_for_company'] += $delivery_ed['delivery_count_for_all'];
                    }
                }
            }


            // 统计【渠道】交付量
            if($v['company_category'] == 11 && $v['channel_merge'] > 0)
            {
                $delivery = $delivery_list->where('channel_id',$v['channel_id']);
                if($delivery->isNotEmpty() && $delivery->count() > 0)
                {
                    foreach($delivery as $delivery_ed)
                    {
                        $list_all[$k]['delivery_count_for_channel'] += $delivery_ed['delivery_count_for_all'];
                    }
                }
            }

        }
//        dd($list_all);


        return datatable_response(collect($list_all), $draw, $total);
    }

    public function o1__get_statistic_data_of_company_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Common__Delivery::select('company_id','channel_id','business_id','delivered_date')
            ->addSelect(DB::raw("
                    delivered_date as date_day,
                    DAY(delivered_date) as day,
                    count(*) as delivery_count
                "))
            ->groupBy('delivered_date');


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);


        // 商务
        if(isset($post_data['business']) && !in_array($post_data['business'],[-1,'-1']))
        {
            $query->where('business_id', $post_data['business']);
        }
        else
        {
            // 渠道
            if(isset($post_data['channel']) && !in_array($post_data['channel'],[-1,'-1']))
            {
                $query->where('channel_id', $post_data['channel']);
            }
            else
            {
                // 公司
                if(isset($post_data['company']) && !in_array($post_data['company'],[-1,'-1']))
                {
                    $query->where('company_id', $post_data['company']);
                }
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
        else $query->orderBy("delivered_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }




    // 【统计】【交付统计】项目
    public function v1_11($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');

        // 工单统计（当日）
        $query_order_production = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as production_published_num,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as production_inspected_num,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as production_accepted_num,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as production_repeated_num,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as production_refused_num,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as production_accepted_suburb_num,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as production_accepted_inside_num
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_today_num,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as marketing_tomorrow_num,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_delivered_inside_num,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_delivered_repeated_num,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_delivered_rejected_num
                "))
            ->where('published_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        // 工单统计（隔日）
        $query_order_other_day = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as other_day_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_yesterday_num,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as other_day_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as other_day_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as other_day_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as other_day_delivered_rejected
                "))
            ->where('published_date','<>',$assign_date)
            ->where('delivered_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query_delivery = DK_Common__Delivery::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(order_category = 1, TRUE, NULL)) as marketing_delivered_num,
                    count(IF(order_category = 1 AND delivery_type = 1, TRUE, NULL)) as marketing_normal_num,
                    count(IF(order_category = 1 AND delivery_type = 11, TRUE, NULL)) as marketing_distribute_num
                "))
            ->where('delivered_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $project_list = DK_Common__Project::select('id','name','alias_name')
//            ->where('item_status', 1)
            ->withTrashed()
            ->get();

        foreach ($project_list as $k => $v)
        {
            $project_list[$k]->production_published_num = 0;
            $project_list[$k]->production_inspected_num = 0;
            $project_list[$k]->production_accepted_num = 0;
            $project_list[$k]->production_repeated_num = 0;
            $project_list[$k]->production_refused_num = 0;
            $project_list[$k]->production_accepted_suburb_num = 0;
            $project_list[$k]->production_accepted_inside_num = 0;

            $project_list[$k]->marketing_delivered_num = 0;
            $project_list[$k]->marketing_today_num = 0;
            $project_list[$k]->marketing_tomorrow_num = 0;
            $project_list[$k]->marketing_yesterday_num = 0;
            $project_list[$k]->marketing_distribute_num = 0;

            // 当日生产
            if(isset($query_order_production[$v->id]))
            {
                $project_list[$k]->production_published_num = $query_order_production[$v->id]['production_published_num'];
                $project_list[$k]->production_inspected_num = $query_order_production[$v->id]['production_inspected_num'];
                $project_list[$k]->production_accepted_num = $query_order_production[$v->id]['production_accepted_num'];
                $project_list[$k]->production_repeated_num = $query_order_production[$v->id]['production_repeated_num'];
                $project_list[$k]->production_refused_num = $query_order_production[$v->id]['production_refused_num'];
                $project_list[$k]->production_accepted_suburb_num = $query_order_production[$v->id]['production_accepted_suburb_num'];
                $project_list[$k]->production_accepted_inside_num = $query_order_production[$v->id]['production_accepted_inside_num'];

                $project_list[$k]->marketing_today_num = $query_order_production[$v->id]['marketing_today_num'];
                $project_list[$k]->marketing_tomorrow_num = $query_order_production[$v->id]['marketing_tomorrow_num'];
            }

            // 隔日交付
            if(isset($query_order_other_day[$v->id]))
            {
                $project_list[$k]->marketing_yesterday_num = $query_order_other_day[$v->id]['marketing_yesterday_num'];
            }

            // 交付统计
            if(isset($query_delivery[$v->id]))
            {
                $project_list[$k]->marketing_delivered_num = $query_delivery[$v->id]['marketing_delivered_num'];
                $project_list[$k]->marketing_distribute_num = $query_delivery[$v->id]['marketing_distribute_num'];
            }
        }

        $project_list_filtered = $project_list->filter(function ($item) {
            return ($item->production_published_num > 0 || $item->marketing_yesterday_num > 0 || $item->marketing_delivered_num > 0);
        });
//        dd($list_filtered);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            foreach ($project_list_filtered as $k => $v)
            {

                $daily = DK_Statistic__Project_Daily::select('*')
                    ->where('project_id',$v->id)
                    ->where('statistic_date',$assign_date)
                    ->first();

                if($daily)
                {
                    if($daily->is_confirmed = 1)
                    {
                        continue;
                    }
                }
                else
                {
                    $daily = new DK_Statistic__Project_Daily;
                    $daily->creator_id = $me->id;
                }

                $daily->statistic_date = $assign_date;
                $daily->project_id = $v->id;

                $daily->production_published_num = $v->production_published_num;
                $daily->production_inspected_num = $v->production_inspected_num;
                $daily->production_accepted_num = $v->production_accepted_num;
                $daily->production_accepted_suburb_num = $v->production_accepted_suburb_num;
                $daily->production_accepted_inside_num = $v->production_accepted_inside_num;
                $daily->production_repeated_num = $v->production_repeated_num;
                $daily->production_refused_num = $v->production_refused_num;

                $daily->marketing_delivered_num = $v->marketing_delivered_num;
                $daily->marketing_today_num = $v->marketing_today_num;
                $daily->marketing_yesterday_num = $v->marketing_yesterday_num;
                $daily->marketing_tomorrow_num = $v->marketing_tomorrow_num;
                $daily->marketing_distribute_num = $v->marketing_distribute_num;

                $bool = $daily->save();
                if(!$bool) throw new Exception("DK_Statistic__Project_Daily--save--fail");

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
    public function o1__statistic__marketing__project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $assign_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        // 客服部
        if($me->staff_category == 41)
        {
            $team_id = $me->team_id;
            if($me->staff_position == 61)
            {
                $team_group_id = $me->team_group_id;
            }
        }
        // 工单统计（当日）
        $query_order_today = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(delivered_status = 1, TRUE, NULL)) as count__for__order_today_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as count__for__order_today_normal,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as count__for__order_today_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as count__for__order_today_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as count__for__order_today_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as count__for__order_today_tomorrow,
                    count(IF(inspected_result = '不合格' AND delivered_status = 1, TRUE, NULL)) as count__for__order_today_refused
                "))
            ->where('order_category',1)
            ->where('published_date',$assign_date)
            ->groupBy('delivered_project_id')
            ->get()
            ->keyBy('delivered_project_id')
            ->toArray();


        // 工单统计（前日）
        $query_order_yesterday = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(delivered_result in ('正常交付','折扣交付','郊区交付','内部交付'), TRUE, NULL)) as count__for__order_yesterday_all
                "))
            ->where('order_category',1)
            ->where('published_date','<>',$assign_date)
            ->where('delivered_date',$assign_date)
            ->groupBy('delivered_project_id')
            ->get()
            ->keyBy('delivered_project_id')
            ->toArray();


        // 交付统计
        $query_delivery = DK_Common__Delivery::select('project_id')
            ->addSelect(DB::raw("
                    count(*) as count__for__delivered_all,
                    count(IF(delivery_type = 1, TRUE, NULL)) as count__for__delivered_normal,
                    count(IF(delivery_type = 11, TRUE, NULL)) as count__for__delivered_distribute
                "))
            ->where('order_category',1)
            ->where('delivered_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('id','name','alias_name','daily_goal');
//            ->where('item_status', 1)

        // 客服部
        if($me->staff_category == 41)
        {
            $team_id = $me->team_id;
            $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
            $query->whereIn('id',$project_list);
        }

        // 团队
        if(!empty($post_data['team']))
        {
            $team = (int)$post_data['team'];
            if(!in_array($team,[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($team) {
                    $query->where('team_id', $team);
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

        if($limit == -1) $project_list = $query->get();
        else $project_list = $query->skip($skip)->take($limit)->get();
//        $project_list = $query_project->withTrashed()->get();


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有项目';
        $total_data['pivot__project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['count__for__order_today_all'] = 0;
        $total_data['count__for__order_today_normal'] = 0;
        $total_data['count__for__order_today_discount'] = 0;
        $total_data['count__for__order_today_suburb'] = 0;
        $total_data['count__for__order_today_inside'] = 0;
        $total_data['count__for__order_today_tomorrow'] = 0;
        $total_data['count__for__order_today_refused'] = 0;

        $total_data['count__for__order_yesterday_all'] = 0;

        $total_data['count__for__delivered_normal'] = 0;
        $total_data['count__for__delivered_distribute'] = 0;

        $total_data['remark'] = '';


        foreach ($project_list as $k => $v)
        {
            $project_list[$k]->count__for__order_today_all = 0;
            $project_list[$k]->count__for__order_today_normal = 0;
            $project_list[$k]->count__for__order_today_discount = 0;
            $project_list[$k]->count__for__order_today_suburb = 0;
            $project_list[$k]->count__for__order_today_inside = 0;
            $project_list[$k]->count__for__order_today_refused = 0;
            $project_list[$k]->count__for__order_today_tomorrow = 0;

            $project_list[$k]->count__for__order_yesterday_all = 0;

            $project_list[$k]->count__for__delivered_all = 0;
            $project_list[$k]->count__for__delivered_normal = 0;
            $project_list[$k]->count__for__delivered_distribute = 0;

            // 当日生产
            if(isset($query_order_today[$v->id]))
            {
                $project_list[$k]->count__for__order_today_all = $query_order_today[$v->id]['count__for__order_today_all'];
                $project_list[$k]->count__for__order_today_normal = $query_order_today[$v->id]['count__for__order_today_normal'];
                $project_list[$k]->count__for__order_today_discount = $query_order_today[$v->id]['count__for__order_today_discount'];
                $project_list[$k]->count__for__order_today_suburb = $query_order_today[$v->id]['count__for__order_today_suburb'];
                $project_list[$k]->count__for__order_today_inside = $query_order_today[$v->id]['count__for__order_today_inside'];
                $project_list[$k]->count__for__order_today_refused = $query_order_today[$v->id]['count__for__order_today_refused'];
                $project_list[$k]->count__for__order_today_tomorrow = $query_order_today[$v->id]['count__for__order_today_tomorrow'];

                $project_list[$k]->marketing_today_num = $query_order_today[$v->id]['marketing_today_num'];
                $project_list[$k]->marketing_tomorrow_num = $query_order_today[$v->id]['marketing_tomorrow_num'];
            }

            // 隔日交付
            if(isset($query_order_yesterday[$v->id]))
            {
                $project_list[$k]->count__for__order_yesterday_all = $query_order_yesterday[$v->id]['count__for__order_yesterday_all'];
            }

            // 交付统计
            if(isset($query_delivery[$v->id]))
            {
                $project_list[$k]->count__for__delivered_normal = $query_delivery[$v->id]['count__for__delivered_normal'];
                $project_list[$k]->count__for__delivered_distribute = $query_delivery[$v->id]['count__for__delivered_distribute'];
            }

            // 当日完成率
            if($v->daily_goal > 0)
            {
                $project_list[$k]->rate__for__completed = round(($v->count__for__delivered_normal * 100 / $v->daily_goal),2);
            }
            else $project_list[$k]->rate__for__completed = 0;


        }

//        $project_list_filtered = $project_list->filter(function ($item) {
//            return ($item->production_published_num > 0 || $item->marketing_yesterday_num > 0 || $item->marketing_delivered_num > 0);
//        });

        foreach ($project_list as $k => $v)
        {

            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['count__for__order_today_all'] += $v->count__for__order_today_all;
            $total_data['count__for__order_today_normal'] += $v->count__for__order_today_normal;
            $total_data['count__for__order_today_discount'] += $v->count__for__order_today_discount;
            $total_data['count__for__order_today_suburb'] += $v->count__for__order_today_suburb;
            $total_data['count__for__order_today_inside'] += $v->count__for__order_today_inside;
            $total_data['count__for__order_today_refused'] += $v->count__for__order_today_refused;
            $total_data['count__for__order_today_tomorrow'] += $v->count__for__order_today_tomorrow;

            $total_data['count__for__order_yesterday_all'] += $v->count__for__order_yesterday_all;

            $total_data['count__for__delivered_normal'] += $v->count__for__delivered_normal;
            $total_data['count__for__delivered_distribute'] += $v->count__for__delivered_distribute;


            // 当日完成率
            if($v->daily_goal > 0)
            {
                $project_list[$k]->rate__for__completed = round(($v->count__for__delivered_normal * 100 / $v->daily_goal),2);
            }
            else $project_list[$k]->rate__for__completed = 0;
        }

        return datatable_response($project_list, $draw, $total);

    }
    // 【统计】【交付统计】客户
    public function o1__get_statistic_data_of_marketing_client1($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        // 团队统计
        $query_order = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('delivered_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot__project_staff','pivot__project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
            $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $team_id = $me->team_id;
            if($me->team_id > 0)
            {
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot__Staff_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
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
    public function o1__get_statistic_data_of_marketing_client($post_data)
    {

        $this->get_me();
        $me = $this->me;

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        // 工单统计
        $query_order = DK_Common__Order::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->whereDate('delivered_date',$the_date)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();


        $query = DK_Common__Client::select('*')
//            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator']);


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['team']))
        {
            if(!in_array($post_data['team'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
                    $query->where('team_id', $post_data['team']);
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
        $total_data['pivot__project_team'] = [];
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
    public function v2_operate_for_get_statistic_data_of_marketing_client($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $team_id = 0;
        $team_group_id = 0;
        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
            if($me->user_type == 84)
            {
                $team_group_id = $me->team_group_id;
            }
        }


        // 工单统计
        $query_order = DK_Common__Order::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
//            ->whereDate('delivered_date',$the_date)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->when($team_group_id, function ($query) use ($team_group_id) {
                return $query->where('team_group_id', $team_group_id);
            });

//        $query_order = DK_Common__Order::where('is_published',1);
        $query_order_delivered = (clone $query_order);

//        $query_delivery = DK_Common__Delivery::select('order_category');
        // 交付统计
        $query_delivery = DK_Common__Delivery::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
//            ->whereDate('delivered_date',$the_date)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });



        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type'] : 'date';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');

            $query_order->where('published_date',$the_date);
            $query_order_delivered->where('delivered_date',$the_date);

            $query_delivery->where('delivered_date',$the_date);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query_order->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_delivered->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);

            $query_delivery->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {

            if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
            {
                $query_order->whereDate("delivered_date", '>=', $post_data['assign_start']);
                $query_order->whereDate("delivered_date", '<=', $post_data['assign_ended']);

                $query_order_delivered->whereDate("delivered_date", '>=', $post_data['assign_start']);
                $query_order_delivered->whereDate("delivered_date", '<=', $post_data['assign_ended']);

                $query_delivery->whereDate("delivered_date", '>=', $post_data['assign_start']);
                $query_delivery->whereDate("delivered_date", '<=', $post_data['assign_ended']);
            }
            else if(!empty($post_data['assign_start']))
            {
                $query_order->where("delivered_date", $post_data['assign_start']);
                $query_order_delivered->where("delivered_date", $post_data['assign_start']);
                $query_delivery->where("delivered_date", $post_data['assign_start']);
            }
            else if(!empty($post_data['assign_ended']))
            {
                $query_order->where("delivered_date", $post_data['assign_ended']);
                $query_order_delivered->where("delivered_date", $post_data['assign_ended']);
                $query_delivery->where("delivered_date", $post_data['assign_ended']);
            }

//            if(!empty($post_data['date_start'])) $query_order->where('published_date', '>=', $post_data['date_start']);
//            if(!empty($post_data['date_ended'])) $query_order->where('published_date', '<=', $post_data['date_ended']);
//
//            if(!empty($post_data['date_start'])) $query_order_delivered->where('delivered_date', '>=', $post_data['date_start']);
//            if(!empty($post_data['date_ended'])) $query_order_delivered->where('delivered_date', '<=', $post_data['date_ended']);
//
//            if(!empty($post_data['date_start'])) $query_delivery->where('delivered_date', '>=', $post_data['date_start']);
//            if(!empty($post_data['date_ended'])) $query_delivery->where('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
            $the_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');
            $query_order->where('published_date',$the_date);

            $query_delivery->where('delivered_date',$the_date);
        }






        $order_list = $query_order->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();




        if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
        {
            $query_order->whereDate("delivered_date", '>=', $post_data['assign_start']);
            $query_order->whereDate("delivered_date", '<=', $post_data['assign_ended']);
        }
        else if(!empty($post_data['assign_start']))
        {
            $query_order->where("delivered_date", $post_data['assign_start']);
        }
        else if(!empty($post_data['assign_ended']))
        {
            $query_order->where("delivered_date", $post_data['assign_ended']);
        }

        $order_list = $query_order->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();


        $query = DK_Common__Client::select('*')
            ->where('user_status', 1)
            ->withTrashed()
            ->with(['creator']);


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0,'-1','0']))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
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

            if(isset($order_list[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $order_list[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $order_list[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $order_list[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_inside = $order_list[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $order_list[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $order_list[$v->id]['order_count_for_delivered_rejected'];
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








    // 【生产-统计】坐席看板
    public function o1__statistic__production__caller_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 工单统计（员工）统计
        $query_order__for__staff = DK_Common__Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count__for__all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count__for__inspected,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count__for__accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count__for__accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count__for__repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count__for__refused
                "))
            ->groupBy('creator_id');


        // 工单统计（团队）统计
        $query_order__for__team = DK_Common__Order::select('creator_team_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count__for__all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count__for__inspected,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count__for__accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count__for__accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count__for__repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count__for__refused
                "))
            ->groupBy('creator_team_id');


        // 工单统计（小组）统计
        $query_order__for__group = DK_Common__Order::select('creator_team_group_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count__for__all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count__for__inspected,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count__for__accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count__for__accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count__for__repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count__for__refused
                "))
            ->groupBy('creator_team_group_id');


        if($me->staff_category == 41)
        {
            // 部门总监
            if($me->staff_position == 31)
            {
                // 根据部门查看
                $query_order__for__staff->where('creator_department_id', $me->department_id);
                $query_order__for__team->where('creator_department_id', $me->department_id);
                $query_order__for__group->where('creator_department_id', $me->department_id);
            }
            // 团队经理
            else if($me->staff_position == 41)
            {
                // 根据团队查看
                $query_order__for__staff->where('creator_team_id', $me->team_id);
                $query_order__for__team->where('creator_team_id', $me->team_id);
                $query_order__for__group->where('creator_team_id', $me->team_id);
            }
            // 小组主管
            else if($me->staff_position == 61)
            {
                // 根据小组查看
                $query_order__for__staff->where('creator_team_id', $me->team_id)->where('creator_team_group_id', $me->team_group_id);
                $query_order__for__team->where('creator_team_id', $me->team_id)->where('creator_team_group_id', $me->team_group_id);
                $query_order__for__group->where('creator_team_id', $me->team_id)->where('creator_team_group_id', $me->team_group_id);
            }
        }


        // 团队
        $project_id = 0;
        if(isset($post_data['team']))
        {
            $team_id_int = (int)$post_data['team'];
            if(!in_array($team_id_int,[0,-1]))
            {
                $query_order__for__staff->where('creator_team_id', $team_id_int);
                $query_order__for__team->where('creator_team_id', $team_id_int);
                $query_order__for__group->where('creator_team_id', $team_id_int);
            }
        }

        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            $project_id_int = (int)$post_data['project'];
            if(!in_array($project_id_int,[0,-1]))
            {
                $query_order__for__staff->where('project_id', $project_id_int);
                $query_order__for__team->where('project_id', $project_id_int);
                $query_order__for__group->where('project_id', $project_id_int);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order__for__staff->where('published_date',$the_date);
            $query_order__for__team->where('published_date',$the_date);
            $query_order__for__group->where('published_date',$the_date);

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

            $query_order__for__staff->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order__for__team->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order__for__group->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start']))
            {
                $query_order__for__staff->where('published_date', '>=', $post_data['date_start']);
                $query_order__for__team->where('published_date', '>=', $post_data['date_start']);
                $query_order__for__group->where('published_date', '>=', $post_data['date_start']);
            }
            if(!empty($post_data['date_ended']))
            {
                $query_order__for__staff->where('published_date', '<=', $post_data['date_ended']);
                $query_order__for__team->where('published_date', '<=', $post_data['date_ended']);
                $query_order__for__group->where('published_date', '<=', $post_data['date_ended']);
            }
        }
        else
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order__for__staff->where('published_date',$the_date);
            $query_order__for__team->where('published_date',$the_date);
            $query_order__for__group->where('published_date',$the_date);
        }


        $staff_count = $query_order__for__staff->get()->keyBy('creator_id')->toArray();
        $team_count = $query_order__for__team->get()->keyBy('creator_team_id')->toArray();
        $group_count = $query_order__for__group->get()->keyBy('creator_team_group_id')->toArray();
//        dd($query_order);



        $query = DK_Common__Staff::select(['id','staff_category','staff_position','name','department_id','team_id','team_group_id'])
            ->with([
//                'superior' => function($query) { $query->select(['id','name']); },
                'team_er' => function($query) { $query->select(['id','name']); },
                'team_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('active',1)
            ->where('item_status',1)
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
            ->whereIn('staff_category',[41])
            ->whereIn('staff_position',[41,61,99]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                // 根据部门查看
                $query->where('department_id', $me->department_id);
            }
            // 团队经理
            else if($me->staff_position == 41)
            {
                // 根据部门查看
                $query->where('team_id', $me->team_id);
            }
            // 小组主管
            else if($me->staff_position == 61)
            {
                // 根据部门查看
                $query->where('team_id', $me->team_id);
                $query->where('team_group_id', $me->team_group_id);
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
        else $query->orderBy("team_id", "asc")->orderBy("team_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $staff_id = $v->id;
            if($staff_id && !empty($group_count) && count($group_count) && !empty($staff_count[$v->id]))
            {
                $list[$k]->staff_count__for__all = $staff_count[$staff_id]['order_count__for__all'];
                $list[$k]->staff_count__for__inspected = $staff_count[$staff_id]['order_count__for__inspected'];
                $list[$k]->staff_count__for__effective = $staff_count[$staff_id]['order_count__for__effective'];
                $list[$k]->staff_count__for__accepted = $staff_count[$staff_id]['order_count__for__accepted'];
                $list[$k]->staff_count__for__accepted_normal = $staff_count[$staff_id]['order_count__for__accepted_normal'];
                $list[$k]->staff_count__for__accepted_discount = $staff_count[$staff_id]['order_count__for__accepted_discount'];
                $list[$k]->staff_count__for__accepted_suburb = $staff_count[$staff_id]['order_count__for__accepted_suburb'];
                $list[$k]->staff_count__for__accepted_inside = $staff_count[$staff_id]['order_count__for__accepted_inside'];
                $list[$k]->staff_count__for__repeated = $staff_count[$staff_id]['order_count__for__repeated'];
                $list[$k]->staff_count__for__refused = $staff_count[$staff_id]['order_count__for__refused'];
            }
            else
            {
                $list[$k]->staff_count__for__all = 0;
                $list[$k]->staff_count__for__inspected = 0;
                $list[$k]->staff_count__for__effective = 0;
                $list[$k]->staff_count__for__accepted = 0;
                $list[$k]->staff_count__for__accepted_normal = 0;
                $list[$k]->staff_count__for__accepted_discount = 0;
                $list[$k]->staff_count__for__accepted_suburb = 0;
                $list[$k]->staff_count__for__accepted_inside = 0;
                $list[$k]->staff_count__for__repeated = 0;
                $list[$k]->staff_count__for__refused = 0;
            }

            // 有效率
            if($list[$k]->staff_count__for__all)
            {
                $list[$k]->staff_rate__for__effective = round((
                    $list[$k]->staff_count__for__effective * 100 / $list[$k]->staff_count__for__all
                ),2);
            }
            else $list[$k]->staff_rate__for__effective = 0;




            // 小组
            $team_group_id = $v->team_group_id;
            if($team_group_id && !empty($group_count) && count($group_count) && isset($group_count[$team_group_id]))
            {
                $list[$k]->group_count__for__all = $group_count[$team_group_id]['order_count__for__all'];
                $list[$k]->group_count__for__inspected = $group_count[$team_group_id]['order_count__for__inspected'];
                $list[$k]->group_count__for__effective = $group_count[$team_group_id]['order_count__for__effective'];
                $list[$k]->group_count__for__accepted = $group_count[$team_group_id]['order_count__for__accepted'];
                $list[$k]->group_count__for__accepted_normal = $group_count[$team_group_id]['order_count__for__accepted_normal'];
                $list[$k]->group_count__for__accepted_discount = $group_count[$team_group_id]['order_count__for__accepted_discount'];
                $list[$k]->group_count__for__accepted_suburb = $group_count[$team_group_id]['order_count__for__accepted_suburb'];
                $list[$k]->group_count__for__accepted_inside = $group_count[$team_group_id]['order_count__for__accepted_inside'];
                $list[$k]->group_count__for__repeated = $group_count[$team_group_id]['order_count__for__repeated'];
                $list[$k]->group_count__for__refused = $group_count[$team_group_id]['order_count__for__refused'];
            }
            else
            {
                $list[$k]->group_count__for__all = 0;
                $list[$k]->group_count__for__inspected = 0;
                $list[$k]->group_count__for__effective = 0;
                $list[$k]->group_count__for__accepted = 0;
                $list[$k]->group_count__for__accepted_normal = 0;
                $list[$k]->group_count__for__accepted_discount = 0;
                $list[$k]->group_count__for__accepted_suburb = 0;
                $list[$k]->group_count__for__accepted_inside = 0;
                $list[$k]->group_count__for__repeated = 0;
                $list[$k]->group_count__for__refused = 0;
            }

            // 有效率
            if($list[$k]->group_count__for__all)
            {
                $list[$k]->group_rate__for__effective = round((
                    $list[$k]->group_count__for__effective * 100 / $list[$k]->group_count__for__all
                ),2);
            }
            else $list[$k]->group_rate__for__effective = 0;




            // 团队
            $team_id = $v->team_id;
            if($team_id && !empty($team_count) && count($team_count) && isset($team_count[$team_id]))
            {
                $list[$k]->team_count__for__all = $team_count[$team_id]['order_count__for__all'];
                $list[$k]->team_count__for__inspected = $team_count[$team_id]['order_count__for__inspected'];
                $list[$k]->team_count__for__effective = $team_count[$team_id]['order_count__for__effective'];
                $list[$k]->team_count__for__accepted = $team_count[$team_id]['order_count__for__accepted'];
                $list[$k]->team_count__for__accepted_normal = $team_count[$team_id]['order_count__for__accepted_normal'];
                $list[$k]->team_count__for__accepted_discount = $team_count[$team_id]['order_count__for__accepted_discount'];
                $list[$k]->team_count__for__accepted_suburb = $team_count[$team_id]['order_count__for__accepted_suburb'];
                $list[$k]->team_count__for__accepted_inside = $team_count[$team_id]['order_count__for__accepted_inside'];
                $list[$k]->team_count__for__repeated = $team_count[$team_id]['order_count__for__repeated'];
                $list[$k]->team_count__for__refused = $team_count[$team_id]['order_count__for__refused'];
            }
            else
            {
                $list[$k]->team_count__for__all = 0;
                $list[$k]->team_count__for__inspected = 0;
                $list[$k]->team_count__for__effective = 0;
                $list[$k]->team_count__for__accepted = 0;
                $list[$k]->team_count__for__accepted_normal = 0;
                $list[$k]->team_count__for__accepted_discount = 0;
                $list[$k]->team_count__for__accepted_suburb = 0;
                $list[$k]->team_count__for__accepted_inside = 0;
                $list[$k]->team_count__for__repeated = 0;
                $list[$k]->team_count__for__refused = 0;
            }

            // 有效率
            if($list[$k]->team_count__for__all)
            {
                $list[$k]->team_rate__for__effective = round((
                    $list[$k]->team_count__for__effective * 100 / $list[$k]->team_count__for__all
                ),2);
            }
            else $list[$k]->team_rate__for__effective = 0;


            $v->team_merge = 0;
            $v->group_merge = 0;
        }
//        dd($list->toArray());

        $grouped_by_team = $list->groupBy('team_id');
        foreach ($grouped_by_team as $k => $v)
        {
            $v[0]->team_merge = count($v);

            $grouped_by_group = $list->groupBy('team_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【生产-统计】坐席排名
    public function o1__statistic__production__caller_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 工单统计
        $query_order = DK_Common__Order::select('creator_id','published_date')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count__for__all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count__for__inspected,
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count__for__accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count__for__accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count__for__repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count__for__refused
                "))
            ->groupBy('creator_id');

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

        // 团队
        if(!empty($post_data['team']))
        {
            $team_id_int = (int)$post_data['team'];
            if(!in_array($team_id_int,[-1,0]))
            {
                $query_order->where('team_id', $team_id_int);
            }
        }
        // 团队-小组
        if(!empty($post_data['group']))
        {
            $group_id_int = (int)$post_data['group'];
            if(!in_array($group_id_int,[-1,0]))
            {
                $query_order->where('team_group_id', $group_id_int);
            }
        }


        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('published_date',$the_date);
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
            $query_order->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order->where('published_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order->where('published_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }

        $query_order = $query_order->get()->keyBy('creator_id')->toArray();


        // 员工查询
        $query = DK_Common__Staff::select(['id','name','team_id','team_group_id'])
            ->with([
                'team_er' => function($query) { $query->select(['id','name']); },
                'team_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('active',1)
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
            ->where('staff_category',41)
            ->whereIn('staff_position',[41,61,99]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


        // 团队
        if(!empty($post_data['team']))
        {
            $team_id_int = (int)$post_data['team'];
            if(!in_array($team_id_int,[-1,0]))
            {
                $query->where('team_id', $team_id_int);
            }
        }
        // 团队-小组
        if(!empty($post_data['group']))
        {
            $group_id_int = (int)$post_data['group'];
            if(!in_array($group_id_int,[-1,0]))
            {
                $query->where('team_group_id', $group_id_int);
            }
        }


        // 客服部
        if($me->staff_category == 41)
        {
            // 部门总监
            if($me->staff_position == 31)
            {
                // 根据部门查看
                $query->where('department_id', $me->department_id);
            }
            // 团队经理
            else if($me->staff_position == 41)
            {
                // 根据团队查看
                $query->where('team_id', $me->team_id);
            }
            // 小组主管
            else if($me->staff_position == 61)
            {
                // 根据小区查看
                $query->where('team_id', $me->team_id);
                $query->where('team_group_id', $me->team_group_id);
            }
        }
        else
        {
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
        else $query->orderBy("team_id", "asc")->orderBy("team_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count__for__all = $query_order[$v->id]['order_count__for__all'];
                $list[$k]->order_count__for__inspected = $query_order[$v->id]['order_count__for__inspected'];
                $list[$k]->order_count__for__effective = $query_order[$v->id]['order_count__for__effective'];
                $list[$k]->order_count__for__accepted = $query_order[$v->id]['order_count__for__accepted'];
                $list[$k]->order_count__for__accepted_normal = $query_order[$v->id]['order_count__for__accepted_normal'];
                $list[$k]->order_count__for__accepted_discount = $query_order[$v->id]['order_count__for__accepted_discount'];
                $list[$k]->order_count__for__accepted_suburb = $query_order[$v->id]['order_count__for__accepted_suburb'];
                $list[$k]->order_count__for__accepted_inside = $query_order[$v->id]['order_count__for__accepted_inside'];
                $list[$k]->order_count__for__repeated = $query_order[$v->id]['order_count__for__repeated'];
                $list[$k]->order_count__for__refused = $query_order[$v->id]['order_count__for__refused'];
            }
            else
            {
                $list[$k]->order_count__for__all = 0;
                $list[$k]->order_count__for__inspected = 0;
                $list[$k]->order_count__for__effective = 0;
                $list[$k]->order_count__for__accepted = 0;
                $list[$k]->order_count__for__accepted_normal = 0;
                $list[$k]->order_count__for__accepted_discount = 0;
                $list[$k]->order_count__for__accepted_suburb = 0;
                $list[$k]->order_count__for__accepted_inside = 0;
                $list[$k]->order_count__for__repeated = 0;
                $list[$k]->order_count__for__refused = 0;
            }

            // 有效率
            if($v->order_count__for__all > 0)
            {
                $list[$k]->order_rate__for__effective = round(($v->order_count__for__effective * 100 / $v->order_count__for__all),2);
            }
            else $list[$k]->order_rate__for__effective = 0;
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【生产-统计】坐席近期
    public function o1__statistic__production__caller_recent($post_data)
    {
        $this->get_me();
        $me = $this->me;



        // 工单统计
        $query_order = DK_Common__Order::select('creator_id','published_at')
            ->groupBy('creator_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where("published_date",$the_date);
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
            $query_order->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order->where('published_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order->where('published_date', '<=', $post_data['date_ended']);
        }
        else
        {
            $query_order->where('published_date','>',date("Y-m-d",strtotime("-7 day")))
                ->addSelect(DB::raw("
                    published_date as date_day,
                    DATE_FORMAT(published_date,'%e') as day,
                    count(*) as sum
                "))
                ->groupBy('published_date');
        }

        $query_order->addSelect(DB::raw("
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount
                "));

        // 工单统计
        $order_list = $query_order->get();

        foreach($order_list as $k => $v)
        {
            $date_day = date_create($v->date_day);
            $today = date_create(date('Y-m-d'));

            $diff = $today->diff($date_day)->days;
            $v->diff = $diff;
        }
        $order_list = $order_list->groupBy('creator_id');
        foreach($order_list as $k => $v)
        {
            $order_list[$k] = $v->keyBy('diff');
        }
        $order_list = $order_list->toArray();
//        dd($order_list);




        $use = [];
        $use['is_manager'] = 0;
        $use['is_supervisor'] = 0;
        $use['is_customer_service'] = 0;
        $use['is_day'] = 0;
        $use['is_month'] = 0;
        $use['project_id'] = 0;
        $use['the_date'] = 0;
        $use['the_month_start_timestamp'] = 0;
        $use['the_month_ended_timestamp'] = 0;





        $query = DK_Common__Staff::select(['id','item_status','name','team_id','team_group_id'])
            ->with([
                'team_er' => function($query) { $query->select(['id','name']); },
                'team_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('active',1)
            ->where('item_status',1)
            ->where('staff_category',41)
            ->whereIn('staff_position',[41,61,99]);


        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                // 根据部门查看
                $query->where('department_id', $me->department_id);
            }
            // 团队经理
            else if($me->staff_position == 41)
            {
                // 根据部门查看
                $query->where('team_id', $me->team_id);
            }
            // 小组主管
            else if($me->user_type == 61)
            {
                // 根据部门查看
                $query->where('team_id', $me->team_id);
                $query->where('team_group_id', $me->team_group_id);
            }
        }


        // 部门-大区
        if(!empty($post_data['team']))
        {
            $team_id_int = (int)$post_data['team'];
            if(!in_array($team_id_int,[-1,0]))
            {
                $query->where('team_id', $team_id_int);
            }
        }
        // 部门-小组
        if(!empty($post_data['group']))
        {
            $group_id_int = (int)$post_data['group'];
            if(!in_array($group_id_int,[-1,0]))
            {
                $query->where('team_group_id', $group_id_int);
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
        else $query->orderBy("team_id", "asc")->orderBy("team_group_id", "asc")->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            if(isset($order_list[$v->id]))
            {
//                if(isset($order_list[$v->id][7])) $list[$k]->order_7 = $order_list[$v->id][7]['order_count_for_effective'];
//                else $list[$k]->order_7 = 0;
                if(isset($order_list[$v->id][6])) $list[$k]->order_6 = $order_list[$v->id][6]['order_count__for__effective'];
                else $list[$k]->order_6 = 0;
                if(isset($order_list[$v->id][5])) $list[$k]->order_5 = $order_list[$v->id][5]['order_count__for__effective'];
                else $list[$k]->order_5 = 0;
                if(isset($order_list[$v->id][4])) $list[$k]->order_4 = $order_list[$v->id][4]['order_count__for__effective'];
                else $list[$k]->order_4 = 0;
                if(isset($order_list[$v->id][3])) $list[$k]->order_3 = $order_list[$v->id][3]['order_count__for__effective'];
                else $list[$k]->order_3 = 0;
                if(isset($order_list[$v->id][2])) $list[$k]->order_2 = $order_list[$v->id][2]['order_count__for__effective'];
                else $list[$k]->order_2 = 0;
                if(isset($order_list[$v->id][1])) $list[$k]->order_1 = $order_list[$v->id][1]['order_count__for__effective'];
                else $list[$k]->order_1 = 0;
                if(isset($order_list[$v->id][0])) $list[$k]->order_0 = $order_list[$v->id][0]['order_count__for__effective'];
                else $list[$k]->order_0 = 0;
            }
            else
            {
//                $list[$k]->order_7 = 0;
                $list[$k]->order_6 = 0;
                $list[$k]->order_5 = 0;
                $list[$k]->order_4 = 0;
                $list[$k]->order_3 = 0;
                $list[$k]->order_2 = 0;
                $list[$k]->order_1 = 0;
                $list[$k]->order_0 = 0;
            }


        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【生产-统计】坐席日报
    public function o1__get_statistic_data_of_caller_daily($post_data)
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


        $query_this_month = DK_Common__Order::select('creator_id','published_at','published_date')
            ->where('creator_id',$staff_id)
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
//            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date])
            ->groupBy('published_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(published_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(published_date,'%e') as day,
                    count(*) as sum
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                    
                "))
            ->orderBy("published_date", "desc");

        $total = $query_this_month->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_this_month->get();
//        dd($statistics_order_this_month_data);




        $total_data = [];
        $total_data['creator_id'] = $staff_id;
        $total_data['published_at'] = 0;
        $total_data['date_day'] = '统计';
        $total_data['staff_count'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;
        $total_data['order_count_for_effective'] = 0;



        foreach ($list as $k => $v)
        {

            // 审核
            $v->order_count_for_effective = $v->order_count_for_accepted + $v->order_count_for_repeated + $v->order_count_for_accepted_inside;
            $list[$k]->order_count_for_effective = $v->order_count_for_effective;

            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;


            $total_data['order_count_for_all'] += $v->order_count_for_all;
            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;
            $total_data['order_count_for_effective'] += $list[$k]->order_count_for_effective;

        }

        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;

        // 有效率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_effective'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_effective'] = 0;

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }


    // 【生产-统计】质检看板
    public function o1__get_statistic_data_of_production_inspector_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Common__Order::select('inspector_id','inspected_date')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused
                "))
            ->groupBy('inspector_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('inspected_date',$the_date);
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

            $query_order->whereBetween('inspected_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order->where('inspected_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order->where('inspected_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('inspector_id')->toArray();




        $query = DK_Common__Staff::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'team_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[61,66,71,77]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
//        if($me->user_type == 71)
//        {
//            $query->where(function ($query) use($me) {
//                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
//            });
//        }

        // 根据部门查看
        if($me->team_id > 0)
        {
            // 根据部门查看
            $query->where('team_id', $me->team_id);
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
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();


        // 数据拼接
        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
            }
            else
            {
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
            }
        }



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
//        $list = $list->sortBy('team_id');
//        $grouped = $list->sortBy('team_id')->groupBy('team_id');
        $grouped = $list->groupBy('team_id');
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
    // 【生产-统计】运营看板
    public function o1__get_statistic_data_of_production_deliverer_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,61])) return view($this->view_blade_403);

        // 员工统计
        $query_delivery = DK_Common__Delivery::select('creator_id')
            ->addSelect(DB::raw("
                    count(*) as order_count_for_delivered
                "))
            ->groupBy('creator_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_delivery->where("delivered_date",$the_date);

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

//            $query_delivery->whereBetween('delivered_date',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_delivery->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_delivery->where('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_delivery->where('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        $query_order = $query_delivery->get()->keyBy('creator_id')->toArray();


        $query = DK_Common__Staff::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'team_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[61,66]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 审核经理
//        if($me->user_type == 71)
//        {
//            $query->where(function ($query) use($me) {
//                $query->where('id',$me->id)->orWhereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );
//            });
//        }

        // 根据部门查看
        if($me->team_id > 0)
        {
            // 根据部门查看
            $query->where('team_id', $me->team_id);
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
        else $query->orderBy("id", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();


        // 数据拼接
        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
            }
            else
            {
                $list[$k]->order_count_for_delivered = 0;
            }
        }



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
//        $list = $list->sortBy('team_id');
//        $grouped = $list->sortBy('team_id')->groupBy('team_id');
        $grouped = $list->groupBy('team_id');
        foreach ($grouped as $k => $v)
        {
            $order_sum_for_all = 0;
            $order_sum_for_delivered = 0;

            foreach ($v as $key => $val)
            {
//                $order_sum_for_all += $val->order_count_for_all;
                $order_sum_for_delivered += $val->order_count_for_delivered;
            }


            foreach ($v as $key => $val)
            {
                $v[$key]->merge = 0;
//                $v[$key]->order_sum_for_all = $order_sum_for_all;
                $v[$key]->order_sum_for_delivered = $order_sum_for_delivered;

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




    public function o1__statistic__production__project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date'] : date('Y-m-d');

        $team_id = 0;
        $team_group_id = 0;

        // 客服部
        if($me->staff_category == 41)
        {
            $team_id = $me->team_id;
            if($me->staff_position == 61)
            {
                $team_group_id = $me->team_group_id;
            }
        }

        // 工单统计
        $query_order = DK_Common__Order::select('project_id','creator_team_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count_for_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count_for_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '不合格', TRUE, NULL)) as order_count_for_accepted_non,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_count_for_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_count_for_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('published_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('creator_team_id', $team_id);
            })
            ->when($team_group_id, function ($query) use ($team_group_id) {
                return $query->where('creator_team_group_id', $team_group_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with([
                'creator',
                'inspector_er',
                'pivot__project_staff',
                'pivot__project_team'
            ]);


        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                $department_id = $me->department_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('department_id',$department_id)->get();
                $query->whereIn('id',$project_list);
            }
            else if($me->staff_position == 41)
            {
                $team_id = $me->team_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
            else if($me->staff_position == 99)
            {
                $project_list = DK_Pivot__Staff_Project::select('project_id')->where('staff_id',$me->id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        // 质检部
        if($me->staff_category == 51)
        {
            if($me->staff_position == 31)
            {
                $department_id = $me->department_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('department_id',$department_id)->get();
                $query->whereIn('id',$project_list);
            }
            else if($me->staff_position == 41)
            {
                $team_id = $me->team_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
            else if($me->staff_position == 99)
            {
                $project_list = DK_Pivot__Staff_Project::select('project_id')->where('staff_id',$me->id)->get();
                $query->whereIn('id',$project_list);
            }
        }




        // 团队
        if(!empty($post_data['team']))
        {
            $team = (int)$post_data['team'];
            if(!in_array($team,[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($team) {
                    $query->where('team_id', $team);
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
        $total_data['pivot__project_team'] = [];
        $total_data['daily_goal'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_discount'] = 0;
        $total_data['order_count_for_accepted_suburb'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;
        $total_data['order_count_for_accepted_non'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_discount'] = 0;
        $total_data['order_count_for_delivered_suburb'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(in_array($me->user_type,[0,1,11,61,66,71,77]) && $me->team_id <= 0)
            {
                if($v['alias_name']) $list[$k]['name'] .= ' ('.$v['alias_name'].')';
            }


            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_discount = $query_order[$v->id]['order_count_for_accepted_discount'];
                $list[$k]->order_count_for_accepted_suburb = $query_order[$v->id]['order_count_for_accepted_suburb'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
                $list[$k]->order_count_for_accepted_non = $query_order[$v->id]['order_count_for_accepted_non'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_discount = $query_order[$v->id]['order_count_for_delivered_discount'];
                $list[$k]->order_count_for_delivered_suburb = $query_order[$v->id]['order_count_for_delivered_suburb'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_discount = 0;
                $list[$k]->order_count_for_accepted_suburb = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
                $list[$k]->order_count_for_accepted_non = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_discount = 0;
                $list[$k]->order_count_for_delivered_suburb = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_accepted + $v->order_count_for_accepted_discount;
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;
            // 完成率
            if($v->daily_goal > 0)
            {
                $list[$k]->order_rate_for_achieved = round(($v->order_count_for_effective * 100 / $v->daily_goal),2);
            }
            else
            {
                if($v->order_count_for_effective > 0) $list[$k]->order_rate_for_achieved = 100;
                else $list[$k]->order_rate_for_achieved = 0;
            }


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_discount + $v->order_count_for_delivered_tomorrow;
            // 实际交付量
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_discount + $v->order_count_for_delivered_tomorrow;


            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_all'] += $v->order_count_for_all;

            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_discount'] += $v->order_count_for_accepted_discount;
            $total_data['order_count_for_accepted_suburb'] += $v->order_count_for_accepted_suburb;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;
            $total_data['order_count_for_accepted_non'] += $v->order_count_for_accepted_non;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_discount'] += $v->order_count_for_delivered_discount;
            $total_data['order_count_for_delivered_suburb'] += $v->order_count_for_delivered_suburb;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;


        }


        // 审核
        // 有效单量
        $total_data['order_count_for_effective'] = $total_data['order_count_for_accepted'] + $total_data['order_count_for_accepted_discount'];
        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;
        // 完成率
        if($total_data['daily_goal'] > 0)
        {
            $total_data['order_rate_for_achieved'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['daily_goal']),2);
        }
        else
        {
            if($total_data['order_count_for_accepted'] > 0) $total_data['order_rate_for_achieved'] = 100;
            else $total_data['order_rate_for_achieved'] = 0;
        }

        // 审核

        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;


        $total_data = $total_data;



        return datatable_response($list, $draw, $total);

    }

    public function o1__statistic__production__team($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 工单统计
        $query_order = DK_Common__Order::select('creator_team_id','published_date')
            ->addSelect(DB::raw("
                    count(DISTINCT creator_id) as staff_count,
                    count(IF(is_published = 1, TRUE, NULL)) as count__for__order_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as count__for__order_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as count__for__order_accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as count__for__order_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as count__for__order_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as count__for__order_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as count__for__order_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as count__for__order_refused
                "))
            ->whereDate("published_date",$the_date);
//            ->groupBy('team_id')


        // 时间
        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('published_date',$the_date);
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
            $query_order->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query_order->where('published_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query_order->where('published_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }

        $query_order = $query_order->groupBy('creator_team_id')->get()->keyBy('creator_team_id')->toArray();


        $query = DK_Common__Team::select('id','name')
            ->where('team_category',41)
            ->where('team_type',11)
            ->where(['item_status'=>1]);


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
        else $list = $query->skip($skip)->take($limit)->get();

        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '--';
        $total_data['staff_count'] = 0;
        $total_data['count__for__order_all'] = 0;
        $total_data['count__for__order_inspected'] = 0;
        $total_data['count__for__order_accepted'] = 0;
        $total_data['count__for__order_accepted_normal'] = 0;
        $total_data['count__for__order_accepted_discount'] = 0;
        $total_data['count__for__order_accepted_suburb'] = 0;
        $total_data['count__for__order_accepted_inside'] = 0;
        $total_data['count__for__order_accepted_all'] = 0;
        $total_data['count__for__order_accepted_effective'] = 0;
        $total_data['count__for__order_repeated'] = 0;
        $total_data['count__for__order_refused'] = 0;



        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->staff_count = $query_order[$v->id]['staff_count'];
                $list[$k]->count__for__order_all = $query_order[$v->id]['count__for__order_all'];
                $list[$k]->count__for__order_inspected = $query_order[$v->id]['count__for__order_inspected'];
                $list[$k]->count__for__order_accepted_normal = $query_order[$v->id]['count__for__order_accepted_normal'];
                $list[$k]->count__for__order_accepted_discount = $query_order[$v->id]['count__for__order_accepted_discount'];
                $list[$k]->count__for__order_accepted_suburb = $query_order[$v->id]['count__for__order_accepted_suburb'];
                $list[$k]->count__for__order_accepted_inside = $query_order[$v->id]['count__for__order_accepted_inside'];
                $list[$k]->count__for__order_accepted_all = (
                    $query_order[$v->id]['count__for__order_accepted_normal'] +
                    $query_order[$v->id]['count__for__order_accepted_discount'] +
                    $query_order[$v->id]['count__for__order_accepted_suburb'] +
                    $query_order[$v->id]['count__for__order_accepted_inside']
                );
                $list[$k]->count__for__order_accepted_effective = (
                    $query_order[$v->id]['count__for__order_accepted_normal'] +
                    $query_order[$v->id]['count__for__order_accepted_discount']
                );
                $list[$k]->count__for__order_repeated = $query_order[$v->id]['count__for__order_repeated'];
                $list[$k]->count__for__order_refused = $query_order[$v->id]['count__for__order_refused'];
            }
            else
            {
                $list[$k]->staff_count = 0;
                $list[$k]->count__for__order_all = 0;
                $list[$k]->count__for__order_inspected = 0;
                $list[$k]->count__for__order_accepted = 0;
                $list[$k]->count__for__order_accepted_normal = 0;
                $list[$k]->count__for__order_accepted_discount = 0;
                $list[$k]->count__for__order_accepted_suburb = 0;
                $list[$k]->count__for__order_accepted_inside = 0;
                $list[$k]->count__for__order_accepted_all = 0;
                $list[$k]->count__for__order_accepted_effective = 0;
                $list[$k]->count__for__order_repeated = 0;
                $list[$k]->count__for__order_refused = 0;
            }

            // 人均 提交量 & 通过量 & 有效量
            if($v->staff_count > 0)
            {
                $list[$k]->per__for__order_all = round(($v->count__for__order_all / $v->staff_count),2);
                $list[$k]->per__for__order_accepted_all = round(($v->count__for__order_accepted_all / $v->staff_count),2);
                $list[$k]->per__for__order_accepted_effective = round(($v->count__for__order_accepted_effective / $v->staff_count),2);
            }
            else
            {
                $list[$k]->per__for__order_all = 0;
                $list[$k]->per__for__order_accepted_all = 0;
                $list[$k]->per__for__order_accepted_effective = 0;
            }

            // 通过率 & 有效率
            if($v->count__for__order_all > 0)
            {
                $list[$k]->rate__for__order_accepted_all = round(($v->count__for__order_accepted_all * 100 / $v->count__for__order_all),2);
                $list[$k]->rate__for__order_accepted_effective = round(($v->count__for__order_accepted_effective * 100 / $v->count__for__order_all),2);
            }
            else
            {
                $list[$k]->rate__for__order_accepted_all = 0;
                $list[$k]->rate__for__order_accepted_effective = 0;
            }



            $total_data['staff_count'] += $v->staff_count;
            $total_data['count__for__order_all'] += $v->count__for__order_all;
            $total_data['count__for__order_inspected'] += $v->count__for__order_inspected;
            $total_data['count__for__order_accepted_normal'] += $v->count__for__order_accepted_normal;
            $total_data['count__for__order_accepted_discount'] += $v->count__for__order_accepted_discount;
            $total_data['count__for__order_accepted_suburb'] += $v->count__for__order_accepted_suburb;
            $total_data['count__for__order_accepted_inside'] += $v->count__for__order_accepted_inside;
            $total_data['count__for__order_accepted_all'] += (
                $v->count__for__order_accepted_normal +
                $v->count__for__order_accepted_discount +
                $v->count__for__order_accepted_suburb +
                $v->count__for__order_accepted_inside
            );
            $total_data['count__for__order_accepted_effective'] += (
                $v->count__for__order_accepted_normal +
                $v->count__for__order_accepted_discount
            );
            $total_data['count__for__order_repeated'] += $v->count__for__order_repeated;
            $total_data['count__for__order_refused'] += $v->count__for__order_refused;

        }

        // 人均提交量
        if($total_data['staff_count'] > 0)
        {
            $total_data['per__for__order_all'] = round(($total_data['count__for__order_all'] / $total_data['staff_count']),2);
            $total_data['per__for__order_accepted_all'] = round(($total_data['count__for__order_accepted_all'] / $total_data['staff_count']),2);
            $total_data['per__for__order_accepted_effective'] = round(($total_data['count__for__order_accepted_effective'] / $total_data['staff_count']),2);
        }
        else
        {
            $total_data['per__for__order_all'] = 0;
            $total_data['per__for__order_accepted_all'] = 0;
            $total_data['per__for__order_accepted_effective'] = 0;
        }


        // 通过率 & 有效率
        if($total_data['count__for__order_all'] > 0)
        {
            $total_data['rate__for__order_accepted_all'] = round(($total_data['count__for__order_accepted_all'] * 100 / $total_data['count__for__order_all']),2);
            $total_data['rate__for__order_accepted_effective'] = round(($total_data['count__for__order_accepted_effective'] * 100 / $total_data['count__for__order_all']),2);
        }
        else
        {
            $total_data['rate__for__order_accepted_all'] = 0;
            $total_data['rate__for__order_accepted_effective'] = 0;
        }


        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }









    // 【统计列表】【项目】返回-列表-数据
    public function o1__statistic__project_daily__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $query = DK_Statistic__Project_Daily::select('*')
            ->with([
                'project_er'=>function ($query) { $query->select('id','name','alias_name'); },
                'creator'=>function ($query) { $query->select('id','username','true_name'); },
                'completer'=>function ($query) { $query->select('id','username','true_name'); }
            ]);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        if(!empty($post_data['assign_date'])) $query->where("statistic_date", $post_data['assign_date']);


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计列表】【项目】生成-日报
    public function o1__statistic__project_daily__create($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');

        // 工单统计（当日）
        $query_order_production = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as production_published_num,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as production_inspected_num,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as production_accepted_num,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as production_accepted_suburb_num,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as production_accepted_inside_num,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as production_repeated_num,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as production_refused_num
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_today_num,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_delivered_inside_num,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as marketing_tomorrow_num,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_delivered_repeated_num,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_delivered_rejected_num
                "))
            ->where('published_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        // 工单统计（隔日）
        $query_order_other_day = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as other_day_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_yesterday_num,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as other_day_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as other_day_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as other_day_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as other_day_delivered_rejected
                "))
            ->where('published_date','<>',$assign_date)
            ->where('delivered_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query_delivery = DK_Common__Delivery::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(order_category = 1, TRUE, NULL)) as marketing_delivered_num,
                    count(IF(order_category = 1 AND delivery_type = 1, TRUE, NULL)) as marketing_normal_num,
                    count(IF(order_category = 1 AND delivery_type = 11, TRUE, NULL)) as marketing_distribute_num
                "))
            ->where('delivered_date',$assign_date)
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $project_list = DK_Common__Project::select('id','name','alias_name')
//            ->where('item_status', 1)
            ->withTrashed()
            ->get();

        foreach ($project_list as $k => $v)
        {
            $project_list[$k]->production_published_num = 0;
            $project_list[$k]->production_inspected_num = 0;
            $project_list[$k]->production_accepted_num = 0;
            $project_list[$k]->production_repeated_num = 0;
            $project_list[$k]->production_refused_num = 0;
            $project_list[$k]->production_accepted_suburb_num = 0;
            $project_list[$k]->production_accepted_inside_num = 0;

            $project_list[$k]->marketing_delivered_num = 0;
            $project_list[$k]->marketing_today_num = 0;
            $project_list[$k]->marketing_tomorrow_num = 0;
            $project_list[$k]->marketing_yesterday_num = 0;
            $project_list[$k]->marketing_distribute_num = 0;

            // 当日生产
            if(isset($query_order_production[$v->id]))
            {
                $project_list[$k]->production_published_num = $query_order_production[$v->id]['production_published_num'];
                $project_list[$k]->production_inspected_num = $query_order_production[$v->id]['production_inspected_num'];
                $project_list[$k]->production_accepted_num = $query_order_production[$v->id]['production_accepted_num'];
                $project_list[$k]->production_repeated_num = $query_order_production[$v->id]['production_repeated_num'];
                $project_list[$k]->production_refused_num = $query_order_production[$v->id]['production_refused_num'];
                $project_list[$k]->production_accepted_suburb_num = $query_order_production[$v->id]['production_accepted_suburb_num'];
                $project_list[$k]->production_accepted_inside_num = $query_order_production[$v->id]['production_accepted_inside_num'];

                $project_list[$k]->marketing_today_num = $query_order_production[$v->id]['marketing_today_num'];
                $project_list[$k]->marketing_tomorrow_num = $query_order_production[$v->id]['marketing_tomorrow_num'];
            }

            // 隔日交付
            if(isset($query_order_other_day[$v->id]))
            {
                $project_list[$k]->marketing_yesterday_num = $query_order_other_day[$v->id]['marketing_yesterday_num'];
            }

            // 交付统计
            if(isset($query_delivery[$v->id]))
            {
                $project_list[$k]->marketing_delivered_num = $query_delivery[$v->id]['marketing_delivered_num'];
                $project_list[$k]->marketing_distribute_num = $query_delivery[$v->id]['marketing_distribute_num'];
            }
        }

        $project_list_filtered = $project_list->filter(function ($item) {
            return ($item->production_published_num > 0 || $item->marketing_yesterday_num > 0 || $item->marketing_delivered_num > 0);
        });
//        dd($list_filtered);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            foreach ($project_list_filtered as $k => $v)
            {

                $daily = DK_Statistic__Project_Daily::select('*')
                    ->where('project_id',$v->id)
                    ->where('statistic_date',$assign_date)
                    ->first();

                if($daily)
                {
                    if($daily->is_confirmed = 1)
                    {
                        continue;
                    }
                }
                else
                {
                    $daily = new DK_Statistic__Project_Daily;
                    $daily->creator_id = $me->id;
                }

                $daily->statistic_date = $assign_date;
                $daily->project_id = $v->id;

                $daily->production_published_num = $v->production_published_num;
                $daily->production_inspected_num = $v->production_inspected_num;
                $daily->production_accepted_num = $v->production_accepted_num;
                $daily->production_accepted_suburb_num = $v->production_accepted_suburb_num;
                $daily->production_accepted_inside_num = $v->production_accepted_inside_num;
                $daily->production_repeated_num = $v->production_repeated_num;
                $daily->production_refused_num = $v->production_refused_num;

                $daily->marketing_delivered_num = $v->marketing_delivered_num;
                $daily->marketing_today_num = $v->marketing_today_num;
                $daily->marketing_yesterday_num = $v->marketing_yesterday_num;
                $daily->marketing_tomorrow_num = $v->marketing_tomorrow_num;
                $daily->marketing_distribute_num = $v->marketing_distribute_num;

                $bool = $daily->save();
                if(!$bool) throw new Exception("DK_Statistic__Project_Daily--save--fail");

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

    // 【统计列表】【项目】返回-列表-数据
    public function o1__statistic__project_daily__item_field_set($post_data)
    {
        $messages = [
            'operate-category.required' => 'operate-category.required.',
            'operate-type.required' => 'operate-type.required.',
            'item-id.required' => 'item-id.required.',
            'column-type.required' => 'column-type.required.',
            'column-key.required' => 'column-key.required.',
        ];
        $v = Validator::make($post_data, [
            'operate-category' => 'required',
            'operate-type' => 'required',
            'item-id' => 'required',
            'column-type' => 'required',
            'column-key' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate-category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item-id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");


        $mine = DK_Statistic__Project_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $operate_type = $post_data["operate-type"];

        $column_type = $post_data["column-type"];

        $column_key = $post_data["column-key"];

        $column_text_value = $post_data["item-field-set-text-value"];
        $column_textarea_value = $post_data["item-field-set-textarea-value"];
        $column_datetime_value = $post_data["item-field-set-datetime-value"];
        $column_date_value = $post_data["item-field-set-date-value"];
        $column_select_value = isset($post_data['item-field-set-select-value']) ? $post_data['item-field-set-select-value'] : '';
        $column_radio_value  = isset($post_data['item-field-set-radio-value']) ? $post_data['item-field-set-radio-value'] : '';

        if($column_type == 'text') $column_value = $column_text_value;
        else if($column_type == 'textarea') $column_value = $column_textarea_value;
        else if($column_type == 'radio') $column_value = $column_radio_value;
        else if($column_type == 'select') $column_value = $column_select_value;
        else if($column_type == 'select2') $column_value = $column_select_value;
        else if($column_type == 'datetime') $column_value = $column_datetime_value;
        else if($column_type == 'datetime_timestamp') $column_value = strtotime($column_datetime_value);
        else if($column_type == 'date') $column_value = $column_date_value;
        else if($column_type == 'date_timestamp') $column_value = strtotime($column_date_value);
        else $column_value = '';

        $before = $mine->$column_key;
        $after = $column_value;


        $return['value'] = $column_value;
        $return['text'] = $column_value;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $mine->$column_key = $column_value;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Statistic__Project_Daily--update--fail");

            if(false) throw new Exception("DK_Statistic__Project_Daily--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(true)
                {
                }
                else
                {
                    $record = new DK_Statistic_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["project_id"] = $id;
                    $record_data["operate_object"] = 61;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $column_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("DK_Statistic_Record--insert--fail");
                }
            }

            DB::commit();
            return response_success(['data'=>$return]);
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
    // 【统计列表】【项目】确认
    public function o1__statistic__project_daily__item_confirm($post_data)
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
        if($operate != 'daily-item-confirm') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $mine = DK_Statistic__Project_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $time = time();
        $date = date('Y-m-d');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->is_confirmed = 1;
            $mine->confirmer_id = $me->id;
            $mine->confirmed_at = $time;
            $mine->confirmed_date = $date;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Statistic__Project_Daily--update--fail");
            else
            {
                $record = new DK_Statistic_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["project_id"] = $id;
                $record_data["operate_object"] = 61;
                $record_data["operate_category"] = 100;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Statistic_Record--update--fail");
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
    // 【统计列表】【项目】删除
    public function o1__statistic__project_daily__item_delete($post_data)
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
        if($operate != 'daily-item-delete') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $mine = DK_Statistic__Project_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $time = time();
        $date = date('Y-m-d');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Statistic__Project_Daily--delete--fail");
            else
            {
                $record = new DK_Statistic_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["project_id"] = $id;
                $record_data["operate_object"] = 61;
                $record_data["operate_category"] = 101;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Statistic_Record--update--fail");
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

    // 【统计列表】【项目】返回-列表-数据
    public function o1__statistic__project__show($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $query = DK_Statistic__Project_Daily::select('project_id')
            ->addSelect(DB::raw("
                    count(*) as statistic_day_num,
                    sum(production_accepted_num) as production_accepted_total,
                    sum(production_accepted_suburb_num) as production_accepted_suburb_total,
                    sum(production_accepted_inside_num) as production_accepted_inside_total,
                    sum(marketing_delivered_num) as marketing_delivered_total,
                    sum(marketing_today_num) as marketing_today_total,
                    sum(marketing_yesterday_num) as marketing_yesterday_total,
                    sum(marketing_tomorrow_num) as marketing_tomorrow_total,
                    sum(marketing_distribute_num) as marketing_distribute_total,
                    sum(marketing_special_num) as marketing_special_total
                "))
            ->with([
                'project_er'=>function ($query) { $query->select('id','name','alias_name'); }
            ])
            ->groupBy('project_id');

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        // 项目
        if(!empty($post_data['assign_project']))
        {
            if(count($post_data['assign_project']))
            {
                $query->whereIn('project_id', $post_data['assign_project']);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'all')
        {
            $title = '全部统计';
        }
        if($time_type == 'date')
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }
        else if($time_type == 'month')
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
            {
                $query->whereDate("statistic_date", '>=', $post_data['assign_start']);
                $query->whereDate("statistic_date", '<=', $post_data['assign_ended']);
                $title  = $post_data['assign_start'].'<br> - <br>'.$post_data['assign_ended'];
            }
            else if(!empty($post_data['assign_start']))
            {
                $query->where("statistic_date", $post_data['assign_start']);
                $title  = $post_data['assign_start'];
            }
            else if(!empty($post_data['assign_ended']))
            {
                $query->where("statistic_date", $post_data['assign_ended']);
                $title  = $post_data['assign_ended'];
            }
        }
        else
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

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
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['project_id'] = '统计';
        $total_data['statistic_date'] = '统计';
        $total_data['statistic_day_num'] = '统计';
        $total_data['production_accepted_total'] = 0;
        $total_data['production_accepted_suburb_total'] = 0;
        $total_data['production_accepted_inside_total'] = 0;
        $total_data['marketing_delivered_total'] = 0;
        $total_data['marketing_today_total'] = 0;
        $total_data['marketing_yesterday_total'] = 0;
        $total_data['marketing_tomorrow_total'] = 0;
        $total_data['marketing_distribute_total'] = 0;
        $total_data['marketing_special_total'] = 0;

        foreach ($list as $k => $v)
        {
            $list[$k]->statistic_date = $title;
//            $list[$k]->content_decode = json_decode($v->content);

            $total_data['production_accepted_total'] += $v->production_accepted_total;
            $total_data['production_accepted_suburb_total'] += $v->production_accepted_suburb_total;
            $total_data['production_accepted_inside_total'] += $v->production_accepted_inside_total;
            $total_data['marketing_delivered_total'] += $v->marketing_delivered_total;
            $total_data['marketing_today_total'] += $v->marketing_today_total;
            $total_data['marketing_yesterday_total'] += $v->marketing_yesterday_total;
            $total_data['marketing_tomorrow_total'] += $v->marketing_tomorrow_total;
            $total_data['marketing_distribute_total'] += $v->marketing_distribute_total;
            $total_data['marketing_special_total'] += $v->marketing_special_total;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计列表】【客户】返回-列表-数据
    public function o1__statistic__project__detail($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $project_id = $post_data['project_id'];

        $query = DK_Statistic__Project_Daily::select('*')
            ->with([
                'project_er'=>function ($query) { $query->select('id','name','alias_name'); }
            ])
            ->where('project_id',$project_id);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'all')
        {
            $title = '全部统计';
        }
        if($time_type == 'date')
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }
        else if($time_type == 'month')
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
            {
                $query->whereDate("statistic_date", '>=', $post_data['assign_start']);
                $query->whereDate("statistic_date", '<=', $post_data['assign_ended']);
                $title  = $post_data['assign_start'].'<br> - <br>'.$post_data['assign_ended'];
            }
            else if(!empty($post_data['assign_start']))
            {
                $query->where("statistic_date", $post_data['assign_start']);
                $title  = $post_data['assign_start'];
            }
            else if(!empty($post_data['assign_ended']))
            {
                $query->where("statistic_date", $post_data['assign_ended']);
                $title  = $post_data['assign_ended'];
            }
        }
        else
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("statistic_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['project_id'] = '统计';
        $total_data['statistic_date'] = '统计';
        $total_data['description'] = '--';
        $total_data['production_published_num'] = 0;
        $total_data['production_accepted_num'] = 0;
        $total_data['production_accepted_suburb_num'] = 0;
        $total_data['production_accepted_inside_num'] = 0;
        $total_data['marketing_delivered_num'] = 0;
        $total_data['marketing_today_num'] = 0;
        $total_data['marketing_yesterday_num'] = 0;
        $total_data['marketing_tomorrow_num'] = 0;
        $total_data['marketing_distribute_num'] = 0;
        $total_data['marketing_special_num'] = 0;

        foreach ($list as $k => $v)
        {
//            $list[$k]->statistic_date = $title;
//            $list[$k]->content_decode = json_decode($v->content);

            $total_data['production_published_num'] += $v->production_published_num;
            $total_data['production_accepted_num'] += $v->production_accepted_num;
            $total_data['production_accepted_suburb_num'] += $v->production_accepted_suburb_num;
            $total_data['production_accepted_inside_num'] += $v->production_accepted_inside_num;
            $total_data['marketing_delivered_num'] += $v->marketing_delivered_num;
            $total_data['marketing_today_num'] += $v->marketing_today_num;
            $total_data['marketing_yesterday_num'] += $v->marketing_yesterday_num;
            $total_data['marketing_tomorrow_num'] += $v->marketing_tomorrow_num;
            $total_data['marketing_distribute_num'] += $v->marketing_distribute_num;
            $total_data['marketing_special_num'] += $v->marketing_special_num;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }


    // 【统计列表】【客户】返回-列表-数据
    public function o1__statistic__client_daily__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $query = DK_Statistic__Client_Daily::select('*')
            ->with([
                'client_er'=>function ($query) { $query->select('id','name'); },
                'creator'=>function ($query) { $query->select('id','name'); },
                'completer'=>function ($query) { $query->select('id','name'); }
            ]);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        if(!empty($post_data['assign_date'])) $query->where("statistic_date", $post_data['assign_date']);


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

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
        else $list = $query->skip($skip)->take($limit)->get();


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['client_id'] = '统计';
        $total_data['statistic_date'] = '统计';
        $total_data['statistic_day_num'] = '统计';
        $total_data['production_published_num'] = 0;
        $total_data['production_inspected_num'] = 0;
        $total_data['production_accepted_num'] = 0;
        $total_data['production_accepted_discount_num'] = 0;
        $total_data['production_accepted_suburb_num'] = 0;
        $total_data['production_accepted_inside_num'] = 0;
        $total_data['production_repeated_num'] = 0;
        $total_data['production_refused_num'] = 0;
        $total_data['marketing_delivered_num'] = 0;
        $total_data['marketing_delivered_discount_num'] = 0;
        $total_data['marketing_delivered_suburb_num'] = 0;
        $total_data['marketing_delivered_inside_num'] = 0;
        $total_data['marketing_today_num'] = 0;
        $total_data['marketing_yesterday_num'] = 0;
        $total_data['marketing_tomorrow_num'] = 0;
        $total_data['marketing_distribute_num'] = 0;
        $total_data['marketing_special_num'] = 0;
        $total_data['description'] = '--';
        $total_data['is_confirmed'] = '--';

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
            $total_data['production_published_num'] += $v->production_published_num;
            $total_data['production_inspected_num'] += $v->production_inspected_num;
            $total_data['production_accepted_num'] += $v->production_accepted_num;
            $total_data['production_accepted_discount_num'] += $v->production_accepted_discount_num;
            $total_data['production_accepted_suburb_num'] += $v->production_accepted_suburb_num;
            $total_data['production_accepted_inside_num'] += $v->production_accepted_inside_num;
            $total_data['production_repeated_num'] += $v->production_repeated_num;
            $total_data['production_refused_num'] += $v->production_refused_num;
            $total_data['marketing_delivered_num'] += $v->marketing_delivered_num;
            $total_data['marketing_delivered_discount_num'] += $v->marketing_delivered_discount_num;
            $total_data['marketing_delivered_suburb_num'] += $v->marketing_delivered_suburb_num;
            $total_data['marketing_delivered_inside_num'] += $v->marketing_delivered_inside_num;
            $total_data['marketing_today_num'] += $v->marketing_today_num;
            $total_data['marketing_yesterday_num'] += $v->marketing_yesterday_num;
            $total_data['marketing_tomorrow_num'] += $v->marketing_tomorrow_num;
            $total_data['marketing_distribute_num'] += $v->marketing_distribute_num;
            $total_data['marketing_special_num'] += $v->marketing_special_num;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计列表】【客户】生成-日报
    public function o1__statistic__client_daily__create($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');

        // 工单统计（当日）
        $query_order_production = DK_Common__Order::select('delivered_client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as production_published_num,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as production_inspected_num,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as production_accepted_num,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as production_repeated_num,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as production_refused_num,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as production_accepted_discount_num,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as production_accepted_suburb_num,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as production_accepted_inside_num
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_today_num,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as marketing_tomorrow_num,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_delivered_discount_num,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_delivered_suburb_num,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_delivered_inside_num,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_delivered_repeated_num,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_delivered_rejected_num
                "))
            ->where('published_date',$assign_date)
            ->groupBy('delivered_client_id')
            ->get()
            ->keyBy('delivered_client_id')
            ->toArray();


        // 工单统计（隔日）
        $query_order_other_day = DK_Common__Order::select('delivered_client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as other_day_delivered_num,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as marketing_yesterday_num,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as other_day_delivered_tomorrow,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as other_day_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as other_day_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as other_day_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as other_day_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as other_day_delivered_rejected
                "))
            ->where('published_date','<>',$assign_date)
            ->where('delivered_date',$assign_date)
            ->groupBy('delivered_client_id')
            ->get()
            ->keyBy('delivered_client_id')
            ->toArray();


        $query_delivery = DK_Common__Delivery::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(order_category = 1, TRUE, NULL)) as marketing_delivered_num,
                    count(IF(order_category = 1 AND delivered_result = '折扣交付', TRUE, NULL)) as marketing_delivered_discount_num,
                    count(IF(order_category = 1 AND delivered_result = '郊区交付', TRUE, NULL)) as marketing_delivered_suburb_num,
                    count(IF(order_category = 1 AND delivered_result = '内部交付', TRUE, NULL)) as marketing_delivered_inside_num,
                    count(IF(order_category = 1 AND delivery_type = 1, TRUE, NULL)) as marketing_normal_num,
                    count(IF(order_category = 1 AND delivery_type = 11, TRUE, NULL)) as marketing_distribute_num
                "))
            ->where('delivered_date',$assign_date)
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();


        $client_list = DK_Common__Client::select('id','name')
//            ->where('item_status', 1)
            ->withTrashed()
            ->get();

        foreach ($client_list as $k => $v)
        {
            $client_list[$k]->production_published_num = 0;
            $client_list[$k]->production_inspected_num = 0;
            $client_list[$k]->production_accepted_num = 0;
            $client_list[$k]->production_repeated_num = 0;
            $client_list[$k]->production_refused_num = 0;
            $client_list[$k]->production_accepted_discount_num = 0;
            $client_list[$k]->production_accepted_suburb_num = 0;
            $client_list[$k]->production_accepted_inside_num = 0;

            $client_list[$k]->marketing_delivered_num = 0;
            $client_list[$k]->marketing_delivered_discount_num = 0;
            $client_list[$k]->marketing_delivered_suburb_num = 0;
            $client_list[$k]->marketing_delivered_inside_num = 0;
            $client_list[$k]->marketing_today_num = 0;
            $client_list[$k]->marketing_tomorrow_num = 0;
            $client_list[$k]->marketing_yesterday_num = 0;
            $client_list[$k]->marketing_distribute_num = 0;

            // 当日生产
            if(isset($query_order_production[$v->id]))
            {
                $client_list[$k]->production_published_num = $query_order_production[$v->id]['production_published_num'];
                $client_list[$k]->production_inspected_num = $query_order_production[$v->id]['production_inspected_num'];
                $client_list[$k]->production_accepted_num = $query_order_production[$v->id]['production_accepted_num'];
                $client_list[$k]->production_repeated_num = $query_order_production[$v->id]['production_repeated_num'];
                $client_list[$k]->production_refused_num = $query_order_production[$v->id]['production_refused_num'];
                $client_list[$k]->production_accepted_discount_num = $query_order_production[$v->id]['production_accepted_discount_num'];
                $client_list[$k]->production_accepted_suburb_num = $query_order_production[$v->id]['production_accepted_suburb_num'];
                $client_list[$k]->production_accepted_inside_num = $query_order_production[$v->id]['production_accepted_inside_num'];

                $client_list[$k]->marketing_today_num = $query_order_production[$v->id]['marketing_today_num'];
                $client_list[$k]->marketing_tomorrow_num = $query_order_production[$v->id]['marketing_tomorrow_num'];
            }

            // 隔日交付
            if(isset($query_order_other_day[$v->id]))
            {
                $client_list[$k]->marketing_yesterday_num = $query_order_other_day[$v->id]['marketing_yesterday_num'];
            }

            // 交付统计
            if(isset($query_delivery[$v->id]))
            {
                $client_list[$k]->marketing_delivered_num = $query_delivery[$v->id]['marketing_delivered_num'];
                $client_list[$k]->marketing_delivered_discount_num = $query_delivery[$v->id]['marketing_delivered_discount_num'];
                $client_list[$k]->marketing_delivered_suburb_num = $query_delivery[$v->id]['marketing_delivered_suburb_num'];
                $client_list[$k]->marketing_delivered_inside_num = $query_delivery[$v->id]['marketing_delivered_inside_num'];
                $client_list[$k]->marketing_distribute_num = $query_delivery[$v->id]['marketing_distribute_num'];
            }
        }

        $client_list_filtered = $client_list->filter(function ($item) {
            return ($item->production_published_num > 0 || $item->marketing_yesterday_num > 0 || $item->marketing_delivered_num > 0);
        });
//        dd($list_filtered);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            foreach ($client_list_filtered as $k => $v)
            {

                $daily = DK_Statistic__Client_Daily::select('*')
                    ->where('client_id',$v->id)
                    ->where('statistic_date',$assign_date)
                    ->first();

                if($daily)
                {
                    if($daily->is_confirmed == 1)
                    {
                        continue;
                    }
                }
                else
                {
                    $daily = new DK_Statistic__Client_Daily;
                    $daily->creator_id = $me->id;
                }

                $daily->statistic_date = $assign_date;
                $daily->client_id = $v->id;

                $daily->production_published_num = $v->production_published_num;
                $daily->production_inspected_num = $v->production_inspected_num;
                $daily->production_accepted_num = $v->production_accepted_num;
                $daily->production_accepted_discount_num = $v->production_accepted_discount_num;
                $daily->production_accepted_suburb_num = $v->production_accepted_suburb_num;
                $daily->production_accepted_inside_num = $v->production_accepted_inside_num;
                $daily->production_repeated_num = $v->production_repeated_num;
                $daily->production_refused_num = $v->production_refused_num;

                $daily->marketing_delivered_num = $v->marketing_delivered_num;
                $daily->marketing_delivered_discount_num = $v->marketing_delivered_discount_num;
                $daily->marketing_delivered_suburb_num = $v->marketing_delivered_suburb_num;
                $daily->marketing_delivered_inside_num = $v->marketing_delivered_inside_num;
                $daily->marketing_today_num = $v->marketing_today_num;
                $daily->marketing_yesterday_num = $v->marketing_yesterday_num;
                $daily->marketing_tomorrow_num = $v->marketing_tomorrow_num;
                $daily->marketing_distribute_num = $v->marketing_distribute_num;

                $bool = $daily->save();
                if(!$bool) throw new Exception("DK_Statistic__Client_Daily--save--fail");

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

    // 【统计列表】【项目】返回-列表-数据
    public function o1__statistic__client_daily__item_field_set($post_data)
    {
        $messages = [
            'operate-category.required' => 'operate-category.required.',
            'operate-type.required' => 'operate-type.required.',
            'item-id.required' => 'item-id.required.',
            'column-type.required' => 'column-type.required.',
            'column-key.required' => 'column-key.required.',
        ];
        $v = Validator::make($post_data, [
            'operate-category' => 'required',
            'operate-type' => 'required',
            'item-id' => 'required',
            'column-type' => 'required',
            'column-key' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate-category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item-id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");


        $mine = DK_Statistic__Client_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");

        $operate_type = $post_data["operate-type"];

        $column_type = $post_data["column-type"];

        $column_key = $post_data["column-key"];

        $column_text_value = $post_data["item-field-set-text-value"];
        $column_textarea_value = $post_data["item-field-set-textarea-value"];
        $column_datetime_value = $post_data["item-field-set-datetime-value"];
        $column_date_value = $post_data["item-field-set-date-value"];
        $column_select_value = isset($post_data['item-field-set-select-value']) ? $post_data['item-field-set-select-value'] : '';
        $column_radio_value  = isset($post_data['item-field-set-radio-value']) ? $post_data['item-field-set-radio-value'] : '';

        if($column_type == 'text') $column_value = $column_text_value;
        else if($column_type == 'textarea') $column_value = $column_textarea_value;
        else if($column_type == 'radio') $column_value = $column_radio_value;
        else if($column_type == 'select') $column_value = $column_select_value;
        else if($column_type == 'select2') $column_value = $column_select_value;
        else if($column_type == 'datetime') $column_value = $column_datetime_value;
        else if($column_type == 'datetime_timestamp') $column_value = strtotime($column_datetime_value);
        else if($column_type == 'date') $column_value = $column_date_value;
        else if($column_type == 'date_timestamp') $column_value = strtotime($column_date_value);
        else $column_value = '';

        $before = $mine->$column_key;
        $after = $column_value;


        $return['value'] = $column_value;
        $return['text'] = $column_value;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $mine->$column_key = $column_value;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Statistic__Client_Daily--update--fail");

            if(false) throw new Exception("DK_Statistic__Client_Daily--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(true)
                {
                }
                else
                {
                    $record = new DK_Statistic_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["project_id"] = $id;
                    $record_data["operate_object"] = 21;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $column_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("DK_Statistic_Record--insert--fail");
                }
            }

            DB::commit();
            return response_success(['data'=>$return]);
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
    // 【统计列表】【项目】确认
    public function o1__statistic__client_daily__item_confirm($post_data)
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
        if($operate != 'daily-item-confirm') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $mine = DK_Statistic__Client_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $time = time();
        $date = date('Y-m-d');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->is_confirmed = 1;
            $mine->confirmer_id = $me->id;
            $mine->confirmed_at = $time;
            $mine->confirmed_date = $date;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Statistic__Client_Daily--update--fail");
            else
            {
                $record = new DK_Statistic_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["project_id"] = $id;
                $record_data["operate_object"] = 21;
                $record_data["operate_category"] = 100;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Statistic_Record--update--fail");
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
    // 【统计列表】【项目】删除
    public function o1__statistic__client_daily__item_delete($post_data)
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
        if($operate != 'daily-item-delete') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $mine = DK_Statistic__Client_Daily::withTrashed()->find($id);
        if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $time = time();
        $date = date('Y-m-d');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Statistic__Client_Daily--delete--fail");
            else
            {
                $record = new DK_Statistic_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["project_id"] = $id;
                $record_data["operate_object"] = 21;
                $record_data["operate_category"] = 101;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Statistic_Record--update--fail");
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

    // 【统计列表】【客户】返回-列表-数据
    public function o1__statistic__client__show($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $query = DK_Statistic__Client_Daily::select('client_id')
            ->addSelect(DB::raw("
                    count(*) as statistic_day_num,
                    sum(production_accepted_num) as production_accepted_total,
                    sum(production_accepted_discount_num) as production_accepted_discount_total,
                    sum(production_accepted_suburb_num) as production_accepted_suburb_total,
                    sum(production_accepted_inside_num) as production_accepted_inside_total,
                    sum(marketing_delivered_num) as marketing_delivered_total,
                    sum(marketing_delivered_discount_num) as marketing_delivered_discount_total,
                    sum(marketing_delivered_suburb_num) as marketing_delivered_suburb_total,
                    sum(marketing_delivered_inside_num) as marketing_delivered_inside_total,
                    sum(marketing_today_num) as marketing_today_total,
                    sum(marketing_yesterday_num) as marketing_yesterday_total,
                    sum(marketing_tomorrow_num) as marketing_tomorrow_total,
                    sum(marketing_distribute_num) as marketing_distribute_total,
                    sum(marketing_special_num) as marketing_special_total
                "))
            ->with([
                'client_er'=>function ($query) { $query->select('id','username'); }
            ])
            ->groupBy('client_id');

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        // 客户
        if(!empty($post_data['assign_client']))
        {
            if(count($post_data['assign_client']))
            {
                $query->whereIn('client_id', $post_data['assign_client']);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'all')
        {
            $title = '全部统计';
        }
        if($time_type == 'date')
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }
        else if($time_type == 'month')
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
            {
                $query->whereDate("statistic_date", '>=', $post_data['assign_start']);
                $query->whereDate("statistic_date", '<=', $post_data['assign_ended']);
                $title  = $post_data['assign_start'].'<br> - <br>'.$post_data['assign_ended'];
            }
            else if(!empty($post_data['assign_start']))
            {
                $query->where("statistic_date", $post_data['assign_start']);
                $title  = $post_data['assign_start'];
            }
            else if(!empty($post_data['assign_ended']))
            {
                $query->where("statistic_date", $post_data['assign_ended']);
                $title  = $post_data['assign_ended'];
            }
        }
        else
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

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
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['client_id'] = '统计';
        $total_data['statistic_date'] = '统计';
        $total_data['statistic_day_num'] = '统计';
        $total_data['production_accepted_total'] = 0;
        $total_data['production_accepted_discount_total'] = 0;
        $total_data['production_accepted_suburb_total'] = 0;
        $total_data['production_accepted_inside_total'] = 0;
        $total_data['marketing_delivered_total'] = 0;
        $total_data['marketing_delivered_discount_total'] = 0;
        $total_data['marketing_delivered_suburb_total'] = 0;
        $total_data['marketing_delivered_inside_total'] = 0;
        $total_data['marketing_today_total'] = 0;
        $total_data['marketing_yesterday_total'] = 0;
        $total_data['marketing_tomorrow_total'] = 0;
        $total_data['marketing_distribute_total'] = 0;
        $total_data['marketing_special_total'] = 0;

        foreach ($list as $k => $v)
        {
            $list[$k]->statistic_date = $title;
//            $list[$k]->content_decode = json_decode($v->content);

            $total_data['production_accepted_total'] += $v->production_accepted_total;
            $total_data['production_accepted_discount_total'] += $v->production_accepted_discount_total;
            $total_data['production_accepted_suburb_total'] += $v->production_accepted_suburb_total;
            $total_data['production_accepted_inside_total'] += $v->production_accepted_inside_total;
            $total_data['marketing_delivered_total'] += $v->marketing_delivered_total;
            $total_data['marketing_delivered_discount_total'] += $v->marketing_delivered_discount_total;
            $total_data['marketing_delivered_suburb_total'] += $v->marketing_delivered_suburb_total;
            $total_data['marketing_delivered_inside_total'] += $v->marketing_delivered_inside_total;
            $total_data['marketing_today_total'] += $v->marketing_today_total;
            $total_data['marketing_yesterday_total'] += $v->marketing_yesterday_total;
            $total_data['marketing_tomorrow_total'] += $v->marketing_tomorrow_total;
            $total_data['marketing_distribute_total'] += $v->marketing_distribute_total;
            $total_data['marketing_special_total'] += $v->marketing_special_total;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【统计列表】【客户】返回-列表-数据
    public function o1__statistic__client__detail($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $client_id = $post_data['client_id'];

        $query = DK_Statistic__Client_Daily::select('*')
            ->with([
                'client_er'=>function ($query) { $query->select('id','username'); }
            ])
            ->where('client_id',$client_id);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'all')
        {
            $title = '全部统计';
        }
        if($time_type == 'date')
        {
            $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date']  : date('Y-m-d');
            $query->where("statistic_date",$assign_date);
            $title  = $assign_date;
        }
        else if($time_type == 'month')
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
            {
                $query->whereDate("statistic_date", '>=', $post_data['assign_start']);
                $query->whereDate("statistic_date", '<=', $post_data['assign_ended']);
                $title  = $post_data['assign_start'].'<br> - <br>'.$post_data['assign_ended'];
            }
            else if(!empty($post_data['assign_start']))
            {
                $query->where("statistic_date", $post_data['assign_start']);
                $title  = $post_data['assign_start'];
            }
            else if(!empty($post_data['assign_ended']))
            {
                $query->where("statistic_date", $post_data['assign_ended']);
                $title  = $post_data['assign_ended'];
            }
        }
        else
        {
            $assign_month  = isset($post_data['assign_month']) ? $post_data['assign_month']  : date('Y-m');
            $assign_month_timestamp = strtotime($assign_month);

            $assign_month_start_date = date('Y-m-01',$assign_month_timestamp); // 指定月份-开始日期
            $assign_month_ended_date = date('Y-m-t',$assign_month_timestamp); // 指定月份-结束日期

            $query->whereBetween('statistic_date',[$assign_month_start_date,$assign_month_ended_date]);
            $title  = $assign_month.'月';
        }

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("statistic_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['client_id'] = '统计';
        $total_data['statistic_date'] = '统计';
        $total_data['description'] = '--';
        $total_data['production_accepted_num'] = 0;
        $total_data['production_accepted_discount_num'] = 0;
        $total_data['production_accepted_suburb_num'] = 0;
        $total_data['production_accepted_inside_num'] = 0;
        $total_data['marketing_delivered_num'] = 0;
        $total_data['marketing_delivered_discount_num'] = 0;
        $total_data['marketing_delivered_suburb_num'] = 0;
        $total_data['marketing_delivered_inside_num'] = 0;
        $total_data['marketing_today_num'] = 0;
        $total_data['marketing_yesterday_num'] = 0;
        $total_data['marketing_tomorrow_num'] = 0;
        $total_data['marketing_distribute_num'] = 0;
        $total_data['marketing_special_num'] = 0;

        foreach ($list as $k => $v)
        {
//            $list[$k]->statistic_date = $title;
//            $list[$k]->content_decode = json_decode($v->content);

            $total_data['production_accepted_num'] += $v->production_accepted_num;
            $total_data['production_accepted_discount_num'] += $v->production_accepted_discount_num;
            $total_data['production_accepted_suburb_num'] += $v->production_accepted_suburb_num;
            $total_data['production_accepted_inside_num'] += $v->production_accepted_inside_num;
            $total_data['marketing_delivered_num'] += $v->marketing_delivered_num;
            $total_data['marketing_delivered_discount_num'] += $v->marketing_delivered_discount_num;
            $total_data['marketing_delivered_suburb_num'] += $v->marketing_delivered_suburb_num;
            $total_data['marketing_delivered_inside_num'] += $v->marketing_delivered_inside_num;
            $total_data['marketing_today_num'] += $v->marketing_today_num;
            $total_data['marketing_yesterday_num'] += $v->marketing_yesterday_num;
            $total_data['marketing_tomorrow_num'] += $v->marketing_tomorrow_num;
            $total_data['marketing_distribute_num'] += $v->marketing_distribute_num;
            $total_data['marketing_special_num'] += $v->marketing_special_num;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }




    // 【统计列表】【客户】返回-列表-数据
    public function o1_statistic__call_task_analysis__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");


        $assign_date  = isset($post_data['assign_date']) ? $post_data['assign_date'] : date('Y-m-d');

        $query = DK_CC_Call_Record_Current::select('taskId','taskName','call_date')
//            ->addSelectRaw("
//                count(*) as call_count,
//                sum(ceil(timeLength / 60)) as call_time_sum,
//                COUNT(DISTINCT orders.id) as order_count
//            ")
            ->addSelect(DB::raw("
                count(*) as call_count,
                sum(ceil(timeLength / 60)) as call_time_sum,
                COUNT(DISTINCT o.id) as order_count
            "))
            ->leftJoin('dk_admin_order as o', function($join) use ($assign_date) {
                $join->on('dk_cc_call_record_of_current.callee', '=', 'o.client_phone')
                    ->whereRaw('DATE(o.published_date) = DATE(dk_cc_call_record_of_current.call_date)')
//                    ->whereDate('o.published_date', '=', DB::raw('DATE(dk_cc_call_record_of_current.call_date)'))
                    ->whereIn('o.inspected_result', ['通过', '折扣通过', '内部通过', '不合格']);
            })
            ->where('call_date', $assign_date)
            ->groupBy('taskID');

        if(!empty($post_data['keyword'])) $query->where('taskName', 'like', "%{$post_data['keyword']}%");
//        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
//        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
//        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
//        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : -1;
//        if($limit > 100) $limit = 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("taskName", "asc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();


        $total_data = [];
        $total_data['taskId'] = '统计';
        $total_data['taskName'] = '统计';
        $total_data['call_date'] = $assign_date;
        $total_data['call_count'] = 0;
        $total_data['call_time_sum'] = 0;
        $total_data['order_count'] = 0;

        foreach ($list as $k => $v)
        {
            $total_data['call_count'] += $v->call_count;
            $total_data['call_time_sum'] += $v->call_time_sum;
            $total_data['order_count'] += $v->order_count;
        }
//        dd($list->toArray());

        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }










    /*
     * Statistic 流量统计
     */
    // 【统计】
    public function view_statistic_index($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 日期
        if(!empty($post_data['date']))
        {
            if($post_data['date']) $view_data['date'] = $post_data['date'];
            else $view_data['date'] = '';
        }
        else $view_data['date'] = '';

        // 月份
        if(!empty($post_data['month']))
        {
            if($post_data['month']) $view_data['month'] = $post_data['month'];
            else $view_data['month'] = '';
        }
        else $view_data['month'] = '';


        $staff_list = DK_Common__Staff::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $client_list = DK_Common__Client::select('id','username')->where('user_category',11)->get();
        $project_list = DK_Common__Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();

        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;
        $view_data['project_list'] = $project_list;
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_index'] = 'active menu-open';

        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-index';
        return view($view_blade)->with($view_data);
    }
    // 【统计】
    public function view_statistic_user($post_data)
    {
        $this->get_me();
        $me = $this->me;
        dd($me);

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
        $user = DK_Common__Staff::find($user_id);

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

        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-user';
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

        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-item';
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
            // 项目
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
        $order_count_for_all = DK_Common__Order::select('*')->count("*");
        $order_count_for_unpublished = DK_Common__Order::where('is_published', 0)->count("*");
        $order_count_for_published = DK_Common__Order::where('is_published', 1)->count("*");
        $order_count_for_waiting_for_inspect = DK_Common__Order::where('is_published', 1)->where('inspected_status', 0)->count("*");
        $order_count_for_inspected = DK_Common__Order::where('is_published', 1)->where('inspected_status', '<>', 0);
        $order_count_for_accepted = DK_Common__Order::where('is_published', 1)->where('inspected_result','通过');
        $order_count_for_refused = DK_Common__Order::where('is_published', 1)->where('inspected_result','拒绝');
        $order_count_for_accepted_inside = DK_Common__Order::where('is_published', 1)->where('inspected_result','内部通过');
        $order_count_for_repeat = DK_Common__Order::where('is_published', 1)->where('is_repeat','>',0);



        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_inspected'] = $order_count_for_inspected;
        $return['order_count_for_accepted'] = $order_count_for_accepted;
        $return['order_count_for_refused'] = $order_count_for_refused;
        $return['order_count_for_repeat'] = $order_count_for_repeat;
        $return['order_count_for_rate'] = round(($order_count_for_accepted * 100 / $order_count_for_all),2);




        // 工单统计

        // 本月每日工单量
        $query_for_order_this_month = DK_Common__Order::select('id','assign_time')
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
        $query_for_order_last_month = DK_Common__Order::select('id','assign_time')
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


        $query = DK_Common__Order::select('id');
        $query_distributed = DK_Common__Delivery::select('id')->where('delivery_type',11);

        if($me->user_type == 41)
        {
            $query->where('team_id',$me->team_id);
        }
        else if($me->user_type == 81)
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
                $query_distributed->where('project_id', $post_data['project']);
            }
        }


        // 部门-大区
//        if(!empty($post_data['department_district']))
//        {
//            if(!in_array($post_data['department_district'],[-1,0]))
//            {
//                $query->where('team_id', $post_data['department_district']);
//            }
//        }
        if(!empty($post_data['department_district']))
        {
            if(count($post_data['department_district']))
            {
                $query->whereIn('team_id', $post_data['department_district']);
            }
        }








        // 分发当天数据
        $query_distributed_of_all = (clone $query_distributed)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_all_for_all'] = $query_distributed_of_all[0]->distributed_count_for_all;




        // 工单统计
        // 总量统计
        $query_order_of_all = (clone $query)->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_all_for_all = $query_order_of_all[0]->order_count_for_all;
        $order_of_all_for_unpublished = $query_order_of_all[0]->order_count_for_unpublished;
        $order_of_all_for_published = $query_order_of_all[0]->order_count_for_published;

        $return_data['order_of_all_for_all'] = $order_of_all_for_all;
        $return_data['order_of_all_for_unpublished'] = $order_of_all_for_unpublished;
        $return_data['order_of_all_for_published'] = $order_of_all_for_published;


        $order_of_all_for_inspected_all = $query_order_of_all[0]->order_count_for_inspected_all;
        $order_of_all_for_inspected_accepted = $query_order_of_all[0]->order_count_for_inspected_accepted;
        $order_of_all_for_inspected_accepted_inside = $query_order_of_all[0]->order_count_for_inspected_accepted_inside;
        $order_of_all_for_inspected_refused = $query_order_of_all[0]->order_count_for_inspected_refused;
        $order_of_all_for_inspected_repeated = $query_order_of_all[0]->order_count_for_inspected_repeated;

        $return_data['order_of_all_for_inspected_all'] = $order_of_all_for_inspected_all;
        $return_data['order_of_all_for_inspected_accepted'] = $order_of_all_for_inspected_accepted;
        $return_data['order_of_all_for_inspected_accepted_inside'] = $order_of_all_for_inspected_accepted_inside;
        $return_data['order_of_all_for_inspected_refused'] = $order_of_all_for_inspected_refused;
        $return_data['order_of_all_for_inspected_repeated'] = $order_of_all_for_inspected_repeated;


        $order_of_all_for_delivered_all = $query_order_of_all[0]->order_count_for_delivered_all;
        $order_of_all_for_delivered_completed = $query_order_of_all[0]->order_count_for_delivered_completed;
        $order_of_all_for_delivered_inside = $query_order_of_all[0]->order_count_for_delivered_inside;
        $order_of_all_for_delivered_tomorrow = $query_order_of_all[0]->order_count_for_delivered_tomorrow;
        $order_of_all_for_delivered_repeated = $query_order_of_all[0]->order_count_for_delivered_repeated;
        $order_of_all_for_delivered_rejected = $query_order_of_all[0]->order_count_for_delivered_rejected;
        $order_of_all_for_delivered_effective = $order_of_all_for_delivered_completed + $order_of_all_for_delivered_tomorrow + $order_of_all_for_delivered_inside;
        if($order_of_all_for_all)
        {
            $order_of_all_for_delivered_effective_rate = round(($order_of_all_for_delivered_effective * 100 / $order_of_all_for_all),2);
        }
        else $order_of_all_for_delivered_effective_rate = 0;

        $return_data['order_of_all_for_delivered_all'] = $order_of_all_for_delivered_all;
        $return_data['order_of_all_for_delivered_completed'] = $order_of_all_for_delivered_completed;
        $return_data['order_of_all_for_delivered_inside'] = $order_of_all_for_delivered_inside;
        $return_data['order_of_all_for_delivered_tomorrow'] = $order_of_all_for_delivered_tomorrow;
        $return_data['order_of_all_for_delivered_repeated'] = $order_of_all_for_delivered_repeated;
        $return_data['order_of_all_for_delivered_rejected'] = $order_of_all_for_delivered_rejected;
        $return_data['order_of_all_for_delivered_effective'] = $order_of_all_for_delivered_effective;
        $return_data['order_of_all_for_delivered_effective_rate'] = $order_of_all_for_delivered_effective_rate;




        $query_delivered_of_all = (clone $query)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected
                "))
            ->get();

        $deliverer_of_all_for_all = $query_delivered_of_all[0]->delivered_count_for_all;
        $deliverer_of_all_for_completed = $query_delivered_of_all[0]->delivered_count_for_completed;
        $deliverer_of_all_for_inside = $query_delivered_of_all[0]->delivered_count_for_inside;
        $deliverer_of_all_for_tomorrow = $query_delivered_of_all[0]->delivered_count_for_tomorrow;
        $deliverer_of_all_for_repeated = $query_delivered_of_all[0]->delivered_count_for_repeated;
        $deliverer_of_all_for_rejected = $query_delivered_of_all[0]->delivered_count_for_rejected;


        $return_data['deliverer_of_all_for_all'] = $deliverer_of_all_for_all;
        $return_data['deliverer_of_all_for_completed'] = $deliverer_of_all_for_completed;
        $return_data['deliverer_of_all_for_inside'] = $deliverer_of_all_for_inside;
        $return_data['deliverer_of_all_for_tomorrow'] = $deliverer_of_all_for_tomorrow;
        $return_data['deliverer_of_all_for_repeated'] = $deliverer_of_all_for_repeated;
        $return_data['deliverer_of_all_for_rejected'] = $deliverer_of_all_for_rejected;








        // 分发当天数据
        $query_distributed_of_today = (clone $query_distributed)->whereDate(DB::raw("DATE(FROM_UNIXTIME(updated_at))"),$the_date)
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_today_for_all'] = $query_distributed_of_today[0]->distributed_count_for_all;




        // 客服报单-当天统计
        $query_order_of_today = (clone $query)->where('published_date',$the_date)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();

        $order_of_today_for_all = $query_order_of_today[0]->order_count_for_all;
        $order_of_today_for_unpublished = $query_order_of_today[0]->order_count_for_unpublished;
        $order_of_today_for_published = $query_order_of_today[0]->order_count_for_published;

        $return_data['order_of_today_for_all'] = $order_of_today_for_all;
        $return_data['order_of_today_for_unpublished'] = $order_of_today_for_unpublished;
        $return_data['order_of_today_for_published'] = $order_of_today_for_published;


        $order_of_today_for_inspected_all = $query_order_of_today[0]->order_count_for_inspected_all;
        $order_of_today_for_inspected_accepted = $query_order_of_today[0]->order_count_for_inspected_accepted;
        $order_of_today_for_inspected_accepted_inside = $query_order_of_today[0]->order_count_for_inspected_accepted_inside;
        $order_of_today_for_inspected_refused = $query_order_of_today[0]->order_count_for_inspected_refused;
        $order_of_today_for_inspected_repeated = $query_order_of_today[0]->order_count_for_inspected_repeated;

        $return_data['order_of_today_for_inspected_all'] = $order_of_today_for_inspected_all;
        $return_data['order_of_today_for_inspected_accepted'] = $order_of_today_for_inspected_accepted;
        $return_data['order_of_today_for_inspected_accepted_inside'] = $order_of_today_for_inspected_accepted_inside;
        $return_data['order_of_today_for_inspected_refused'] = $order_of_today_for_inspected_refused;
        $return_data['order_of_today_for_inspected_repeated'] = $order_of_today_for_inspected_repeated;


        $order_of_today_for_delivered_all = $query_order_of_today[0]->order_count_for_delivered_all;
        $order_of_today_for_delivered_completed = $query_order_of_today[0]->order_count_for_delivered_completed;
        $order_of_today_for_delivered_inside = $query_order_of_today[0]->order_count_for_delivered_inside;
        $order_of_today_for_delivered_tomorrow = $query_order_of_today[0]->order_count_for_delivered_tomorrow;
        $order_of_today_for_delivered_repeated = $query_order_of_today[0]->order_count_for_delivered_repeated;
        $order_of_today_for_delivered_rejected = $query_order_of_today[0]->order_count_for_delivered_rejected;
        $order_of_today_for_delivered_effective = $order_of_today_for_delivered_completed + $order_of_today_for_delivered_tomorrow + $order_of_today_for_delivered_inside;
        if($order_of_today_for_all)
        {
            $order_of_today_for_delivered_effective_rate = round(($order_of_today_for_delivered_effective * 100 / $order_of_today_for_all),2);
        }
        else $order_of_today_for_delivered_effective_rate = 0;

        $return_data['order_of_today_for_delivered_all'] = $order_of_today_for_delivered_all;
        $return_data['order_of_today_for_delivered_completed'] = $order_of_today_for_delivered_completed;
        $return_data['order_of_today_for_delivered_inside'] = $order_of_today_for_delivered_inside;
        $return_data['order_of_today_for_delivered_tomorrow'] = $order_of_today_for_delivered_tomorrow;
        $return_data['order_of_today_for_delivered_repeated'] = $order_of_today_for_delivered_repeated;
        $return_data['order_of_today_for_delivered_rejected'] = $order_of_today_for_delivered_rejected;
        $return_data['order_of_today_for_delivered_effective'] = $order_of_today_for_delivered_effective;
        $return_data['order_of_today_for_delivered_effective_rate'] = $order_of_today_for_delivered_effective_rate;


        // 交付人员-工作统计
        $query_delivered_of_today = (clone $query)->where('delivered_date',$the_date)
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_status = 1 AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_same_day,
                    count(IF(delivered_status = 1 AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_all_by_other_day,
                    
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '正常交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_same_day,
                    count(IF(delivered_result = '正常交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_completed_by_other_day,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '内部交付' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_same_day,
                    count(IF(delivered_result = '内部交付' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_inside_by_other_day,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '重复' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_repeated_by_same_day,
                    count(IF(delivered_result = '重复' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_repeated_by_other_day,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected,
                    count(IF(delivered_result = '驳回' AND published_date = '{$the_date}', TRUE, NULL)) as delivered_count_for_rejected_by_same_day,
                    count(IF(delivered_result = '驳回' AND published_date <> '{$the_date}', TRUE, NULL)) as delivered_count_for_rejected_by_other_day
                "))
            ->get();

        $deliverer_of_today_for_all = $query_delivered_of_today[0]->delivered_count_for_all;
        $deliverer_of_today_for_all_by_same_day = $query_delivered_of_today[0]->delivered_count_for_all_by_same_day;
        $deliverer_of_today_for_all_by_other_day = $query_delivered_of_today[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_today_for_completed = $query_delivered_of_today[0]->delivered_count_for_completed;
        $deliverer_of_today_for_completed_by_same_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_same_day;
        $deliverer_of_today_for_completed_by_other_day = $query_delivered_of_today[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_today_for_inside = $query_delivered_of_today[0]->delivered_count_for_inside;
        $deliverer_of_today_for_inside_by_same_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_same_day;
        $deliverer_of_today_for_inside_by_other_day = $query_delivered_of_today[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_today_for_tomorrow = $query_delivered_of_today[0]->delivered_count_for_tomorrow;
        $deliverer_of_today_for_tomorrow_by_same_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_same_day;
        $deliverer_of_today_for_tomorrow_by_other_day = $query_delivered_of_today[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_today_for_repeated = $query_delivered_of_today[0]->delivered_count_for_repeated;
        $deliverer_of_today_for_repeated_by_same_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_same_day;
        $deliverer_of_today_for_repeated_by_other_day = $query_delivered_of_today[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_today_for_rejected = $query_delivered_of_today[0]->delivered_count_for_rejected;
        $deliverer_of_today_for_rejected_by_same_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_same_day;
        $deliverer_of_today_for_rejected_by_other_day = $query_delivered_of_today[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_today_for_all'] = $deliverer_of_today_for_all;
        $return_data['deliverer_of_today_for_all_by_same_day'] = $deliverer_of_today_for_all_by_same_day;
        $return_data['deliverer_of_today_for_all_by_other_day'] = $deliverer_of_today_for_all_by_other_day;

        $return_data['deliverer_of_today_for_completed'] = $deliverer_of_today_for_completed;
        $return_data['deliverer_of_today_for_completed_by_same_day'] = $deliverer_of_today_for_completed_by_same_day;
        $return_data['deliverer_of_today_for_completed_by_other_day'] = $deliverer_of_today_for_completed_by_other_day;

        $return_data['deliverer_of_today_for_inside'] = $deliverer_of_today_for_inside;
        $return_data['deliverer_of_today_for_inside_by_same_day'] = $deliverer_of_today_for_inside_by_same_day;
        $return_data['deliverer_of_today_for_inside_by_other_day'] = $deliverer_of_today_for_inside_by_other_day;

        $return_data['deliverer_of_today_for_tomorrow'] = $deliverer_of_today_for_tomorrow;
        $return_data['deliverer_of_today_for_tomorrow_by_same_day'] = $deliverer_of_today_for_tomorrow_by_same_day;
        $return_data['deliverer_of_today_for_tomorrow_by_other_day'] = $deliverer_of_today_for_tomorrow_by_other_day;

        $return_data['deliverer_of_today_for_repeated'] = $deliverer_of_today_for_repeated;
        $return_data['deliverer_of_today_for_repeated_by_same_day'] = $deliverer_of_today_for_repeated_by_same_day;
        $return_data['deliverer_of_today_for_repeated_by_other_day'] = $deliverer_of_today_for_repeated_by_other_day;

        $return_data['deliverer_of_today_for_rejected'] = $deliverer_of_today_for_rejected;
        $return_data['deliverer_of_today_for_rejected_by_same_day'] = $deliverer_of_today_for_rejected_by_same_day;
        $return_data['deliverer_of_today_for_rejected_by_other_day'] = $deliverer_of_today_for_rejected_by_other_day;








        // 分发当月数据
        $query_distributed_of_month = (clone $query_distributed)->whereBetween('updated_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->select(DB::raw("
                    count(*) as distributed_count_for_all
                "))
            ->get();
        $return_data['distributed_of_month_for_all'] = $query_distributed_of_month[0]->distributed_count_for_all;




        // 当月统计
        $query_order_of_month = (clone $query)->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(*) as order_count_for_all,
                    count(IF(is_published = 0, TRUE, NULL)) as order_count_for_unpublished,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_published,
                    
                    count(IF(is_published = 1 AND inspected_status <> 0, TRUE, NULL)) as order_count_for_inspected_all,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_inspected_accepted,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_inspected_accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_inspected_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_inspected_refused,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered_all,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->get();


        $order_of_month_for_all = $query_order_of_month[0]->order_count_for_all;
        $order_of_month_for_unpublished = $query_order_of_month[0]->order_count_for_unpublished;
        $order_of_month_for_published = $query_order_of_month[0]->order_count_for_published;

        $return_data['order_of_month_for_all'] = $order_of_month_for_all;
        $return_data['order_of_month_for_unpublished'] = $order_of_month_for_unpublished;
        $return_data['order_of_month_for_published'] = $order_of_month_for_published;


        $order_of_month_for_inspected_all = $query_order_of_month[0]->order_count_for_inspected_all;
        $order_of_month_for_inspected_accepted = $query_order_of_month[0]->order_count_for_inspected_accepted;
        $order_of_month_for_inspected_accepted_inside = $query_order_of_month[0]->order_count_for_inspected_accepted_inside;
        $order_of_month_for_inspected_refused = $query_order_of_month[0]->order_count_for_inspected_refused;
        $order_of_month_for_inspected_repeated = $query_order_of_month[0]->order_count_for_inspected_repeated;

        $return_data['order_of_month_for_inspected_all'] = $order_of_month_for_inspected_all;
        $return_data['order_of_month_for_inspected_accepted'] = $order_of_month_for_inspected_accepted;
        $return_data['order_of_month_for_inspected_accepted_inside'] = $order_of_month_for_inspected_accepted_inside;
        $return_data['order_of_month_for_inspected_refused'] = $order_of_month_for_inspected_refused;
        $return_data['order_of_month_for_inspected_repeated'] = $order_of_month_for_inspected_repeated;


        $order_of_month_for_delivered_all = $query_order_of_month[0]->order_count_for_delivered_all;
        $order_of_month_for_delivered_completed = $query_order_of_month[0]->order_count_for_delivered_completed;
        $order_of_month_for_delivered_inside = $query_order_of_month[0]->order_count_for_delivered_inside;
        $order_of_month_for_delivered_tomorrow = $query_order_of_month[0]->order_count_for_delivered_tomorrow;
        $order_of_month_for_delivered_repeated = $query_order_of_month[0]->order_count_for_delivered_repeated;
        $order_of_month_for_delivered_rejected = $query_order_of_month[0]->order_count_for_delivered_rejected;
        $order_of_month_for_delivered_effective = $order_of_month_for_delivered_completed + $order_of_month_for_delivered_tomorrow + $order_of_month_for_delivered_inside;
        if($order_of_month_for_all)
        {
            $order_of_month_for_delivered_effective_rate = round(($order_of_month_for_delivered_effective * 100 / $order_of_month_for_all),2);
        }
        else $order_of_month_for_delivered_effective_rate = 0;

        $return_data['order_of_month_for_delivered_all'] = $order_of_month_for_delivered_all;
        $return_data['order_of_month_for_delivered_completed'] = $order_of_month_for_delivered_completed;
        $return_data['order_of_month_for_delivered_inside'] = $order_of_month_for_delivered_inside;
        $return_data['order_of_month_for_delivered_tomorrow'] = $order_of_month_for_delivered_tomorrow;
        $return_data['order_of_month_for_delivered_repeated'] = $order_of_month_for_delivered_repeated;
        $return_data['order_of_month_for_delivered_rejected'] = $order_of_month_for_delivered_rejected;
        $return_data['order_of_month_for_delivered_effective'] = $order_of_month_for_delivered_effective;
        $return_data['order_of_month_for_delivered_effective_rate'] = $order_of_month_for_delivered_effective_rate;




        $query_delivered_of_month = (clone $query)->whereBetween('delivered_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
            ->whereIn('created_type',[1,91,99])
            ->select(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as delivered_count_for_all,
                    count(IF(delivered_status = 1 AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_all_by_same_day,
                    count(IF(delivered_status = 1 AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_all_by_other_day,
                    
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as delivered_count_for_completed,
                    count(IF(delivered_result = '正常交付' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_completed_by_same_day,
                    count(IF(delivered_result = '正常交付' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_completed_by_other_day,
                    
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as delivered_count_for_inside,
                    count(IF(delivered_result = '内部交付' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_inside_by_same_day,
                    count(IF(delivered_result = '内部交付' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_inside_by_other_day,
                    
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as delivered_count_for_tomorrow,
                    
                    count(IF(delivered_result = '重复', TRUE, NULL)) as delivered_count_for_repeated,
                    count(IF(delivered_result = '重复' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_repeated_by_same_day,
                    count(IF(delivered_result = '重复' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_repeated_by_other_day,
                    
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as delivered_count_for_rejected,
                    count(IF(delivered_result = '驳回' AND published_at > '{$the_month_start_timestamp}' AND published_at < '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_rejected_by_same_day,
                    count(IF(delivered_result = '驳回' AND published_at < '{$the_month_start_timestamp}' AND published_at > '{$the_month_ended_timestamp}', TRUE, NULL)) as delivered_count_for_rejected_by_other_day
                "))
            ->get();

        $deliverer_of_month_for_all = $query_delivered_of_month[0]->delivered_count_for_all;
        $deliverer_of_month_for_all_by_same_day = $query_delivered_of_month[0]->delivered_count_for_all_by_same_day;
        $deliverer_of_month_for_all_by_other_day = $query_delivered_of_month[0]->delivered_count_for_all_by_other_day;

        $deliverer_of_month_for_completed = $query_delivered_of_month[0]->delivered_count_for_completed;
        $deliverer_of_month_for_completed_by_same_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_same_day;
        $deliverer_of_month_for_completed_by_other_day = $query_delivered_of_month[0]->delivered_count_for_completed_by_other_day;

        $deliverer_of_month_for_inside = $query_delivered_of_month[0]->delivered_count_for_inside;
        $deliverer_of_month_for_inside_by_same_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_same_day;
        $deliverer_of_month_for_inside_by_other_day = $query_delivered_of_month[0]->delivered_count_for_inside_by_other_day;

        $deliverer_of_month_for_tomorrow = $query_delivered_of_month[0]->delivered_count_for_tomorrow;
        $deliverer_of_month_for_tomorrow_by_same_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_same_day;
        $deliverer_of_month_for_tomorrow_by_other_day = $query_delivered_of_month[0]->delivered_count_for_tomorrow_by_other_day;

        $deliverer_of_month_for_repeated = $query_delivered_of_month[0]->delivered_count_for_repeated;
        $deliverer_of_month_for_repeated_by_same_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_same_day;
        $deliverer_of_month_for_repeated_by_other_day = $query_delivered_of_month[0]->delivered_count_for_repeated_by_other_day;

        $deliverer_of_month_for_rejected = $query_delivered_of_month[0]->delivered_count_for_rejected;
        $deliverer_of_month_for_rejected_by_same_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_same_day;
        $deliverer_of_month_for_rejected_by_other_day = $query_delivered_of_month[0]->delivered_count_for_rejected_by_other_day;


        $return_data['deliverer_of_month_for_all'] = $deliverer_of_month_for_all;
        $return_data['deliverer_of_month_for_all_by_same_day'] = $deliverer_of_month_for_all_by_same_day;
        $return_data['deliverer_of_month_for_all_by_other_day'] = $deliverer_of_month_for_all_by_other_day;

        $return_data['deliverer_of_month_for_completed'] = $deliverer_of_month_for_completed;
        $return_data['deliverer_of_month_for_completed_by_same_day'] = $deliverer_of_month_for_completed_by_same_day;
        $return_data['deliverer_of_month_for_completed_by_other_day'] = $deliverer_of_month_for_completed_by_other_day;

        $return_data['deliverer_of_month_for_inside'] = $deliverer_of_month_for_inside;
        $return_data['deliverer_of_month_for_inside_by_same_day'] = $deliverer_of_month_for_inside_by_same_day;
        $return_data['deliverer_of_month_for_inside_by_other_day'] = $deliverer_of_month_for_inside_by_other_day;

        $return_data['deliverer_of_month_for_tomorrow'] = $deliverer_of_month_for_tomorrow;
        $return_data['deliverer_of_month_for_tomorrow_by_same_day'] = $deliverer_of_month_for_tomorrow_by_same_day;
        $return_data['deliverer_of_month_for_tomorrow_by_other_day'] = $deliverer_of_month_for_tomorrow_by_other_day;

        $return_data['deliverer_of_month_for_repeated'] = $deliverer_of_month_for_repeated;
        $return_data['deliverer_of_month_for_repeated_by_same_day'] = $deliverer_of_month_for_repeated_by_same_day;
        $return_data['deliverer_of_month_for_repeated_by_other_day'] = $deliverer_of_month_for_repeated_by_other_day;

        $return_data['deliverer_of_month_for_rejected'] = $deliverer_of_month_for_rejected;
        $return_data['deliverer_of_month_for_rejected_by_same_day'] = $deliverer_of_month_for_rejected_by_same_day;
        $return_data['deliverer_of_month_for_rejected_by_other_day'] = $deliverer_of_month_for_rejected_by_other_day;




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



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');


        // 工单统计


        // 本月每日工单量
        $query_for_order_this_month = DK_Common__Order::select('id','assign_time')
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


        $statistics_data_for_order_this_month = $query_for_order_this_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_this_month'] = $statistics_data_for_order_this_month;

        // 上月每日工单量
        $query_for_order_last_month = DK_Common__Order::select('id','assign_time')
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



        $statistics_data_for_order_last_month = $query_for_order_last_month->get()->keyBy('day');
        $return_data['statistics_data_for_order_last_month'] = $statistics_data_for_order_last_month;


        return response_success($return_data,"");
    }




    // 【统计】交付看板
    public function view_statistic_delivery()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-delivery';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        // 工单统计
        $query_order = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('delivered_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot__project_staff','pivot__project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
            $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $team_id = $me->team_id;
            if($me->team_id > 0)
            {
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot__Staff_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
        $total_data['daily_goal'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_suburb'] = 0;
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
    // 【统计】交付看板
    public function view_statistic_delivery_by_client()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery_by_client'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-delivery-by-client';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_client($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        // 工单统计
        $query_order = DK_Common__Order::select('client_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('delivered_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->groupBy('client_id')
            ->get()
            ->keyBy('client_id')
            ->toArray();


        $query = DK_Common__Client::select('*')
//            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator']);


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
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
    // 【统计】交付看板
    public function view_statistic_delivery_by_project()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_delivery'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-delivery';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_delivery_by_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        // 团队统计
        $query_order = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('delivered_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot__project_staff','pivot__project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
            $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $team_id = $me->team_id;
            if($me->team_id > 0)
            {
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot__Staff_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
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


    // 【统计】项目看板
    public function view_statistic_project()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_project'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-project';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date'] : date('Y-m-d');


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;

        if(in_array($me->user_type,[84]))
        {
            $team_group_id = $me->team_group_id;
        }
        else $team_group_id = 0;


        // 工单统计
        $query_order = DK_Common__Order::select('project_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count_for_accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count_for_accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    count(IF(inspected_result = '不合格', TRUE, NULL)) as order_count_for_accepted_non,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused
                "))
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '折扣交付', TRUE, NULL)) as order_count_for_delivered_discount,
                    count(IF(delivered_result = '郊区交付', TRUE, NULL)) as order_count_for_delivered_suburb,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                "))
            ->where('published_date',$the_day)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->when($team_group_id, function ($query) use ($team_group_id) {
                return $query->where('team_group_id', $team_group_id);
            })
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id')
            ->toArray();


        $query = DK_Common__Project::select('*')
            ->where('item_status', 1)
            ->withTrashed()
            ->with(['creator','inspector_er','pivot__project_staff','pivot__project_team']);

        if(in_array($me->user_type,[41,81,84]))
        {
            $team_id = $me->team_id;
            $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
            $query->whereIn('id',$project_list);
        }

        if(in_array($me->user_type,[71,77]))
        {
            $team_id = $me->team_id;
            if($me->team_id > 0)
            {
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        if($me->user_type == 77)
        {
            $project_list = DK_Pivot__Staff_Project::select('project_id')->where('user_id',$me->id)->get();
            $query->whereIn('id',$project_list);
        }

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");



        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->whereHas('pivot__project_team',  function ($query) use($post_data) {
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
        $total_data['pivot__project_team'] = [];
        $total_data['daily_goal'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_discount'] = 0;
        $total_data['order_count_for_accepted_suburb'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;
        $total_data['order_count_for_accepted_non'] = 0;

        $total_data['order_count_for_delivered'] = 0;
        $total_data['order_count_for_delivered_completed'] = 0;
        $total_data['order_count_for_delivered_discount'] = 0;
        $total_data['order_count_for_delivered_suburb'] = 0;
        $total_data['order_count_for_delivered_inside'] = 0;
        $total_data['order_count_for_delivered_tomorrow'] = 0;
        $total_data['order_count_for_delivered_repeated'] = 0;
        $total_data['order_count_for_delivered_rejected'] = 0;

        $total_data['order_count_for_delivered_per'] = 0;
        $total_data['order_count_for_delivered_effective'] = 0;
        $total_data['order_count_for_delivered_effective_per'] = 0;
        $total_data['order_count_for_delivered_actual'] = 0;

        $total_data['remark'] = '';

        foreach ($list as $k => $v)
        {

            if(in_array($me->user_type,[0,1,11,61,66,71,77]) && $me->team_id <= 0)
            {
                if($v['alias_name']) $list[$k]['name'] .= ' ('.$v['alias_name'].')';
            }


            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_discount = $query_order[$v->id]['order_count_for_accepted_discount'];
                $list[$k]->order_count_for_accepted_suburb = $query_order[$v->id]['order_count_for_accepted_suburb'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
                $list[$k]->order_count_for_accepted_non = $query_order[$v->id]['order_count_for_accepted_non'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_discount = $query_order[$v->id]['order_count_for_delivered_discount'];
                $list[$k]->order_count_for_delivered_suburb = $query_order[$v->id]['order_count_for_delivered_suburb'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_discount = 0;
                $list[$k]->order_count_for_accepted_suburb = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
                $list[$k]->order_count_for_accepted_non = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_discount = 0;
                $list[$k]->order_count_for_delivered_suburb = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_accepted + $v->order_count_for_accepted_discount;
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;
            // 完成率
            if($v->daily_goal > 0)
            {
                $list[$k]->order_rate_for_achieved = round(($v->order_count_for_effective * 100 / $v->daily_goal),2);
            }
            else
            {
                if($v->order_count_for_effective > 0) $list[$k]->order_rate_for_achieved = 100;
                else $list[$k]->order_rate_for_achieved = 0;
            }


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_discount + $v->order_count_for_delivered_tomorrow;
            // 实际交付量
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_discount + $v->order_count_for_delivered_tomorrow;


            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['daily_goal'] += $v->daily_goal;

            $total_data['order_count_for_all'] += $v->order_count_for_all;

            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_discount'] += $v->order_count_for_accepted_discount;
            $total_data['order_count_for_accepted_suburb'] += $v->order_count_for_accepted_suburb;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;
            $total_data['order_count_for_accepted_non'] += $v->order_count_for_accepted_non;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_discount'] += $v->order_count_for_delivered_discount;
            $total_data['order_count_for_delivered_suburb'] += $v->order_count_for_delivered_suburb;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;


        }


        // 审核
        // 有效单量
        $total_data['order_count_for_effective'] = $total_data['order_count_for_accepted'] + $total_data['order_count_for_accepted_discount'];
        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;
        // 完成率
        if($total_data['daily_goal'] > 0)
        {
            $total_data['order_rate_for_achieved'] = round(($total_data['order_count_for_effective'] * 100 / $total_data['daily_goal']),2);
        }
        else
        {
            if($total_data['order_count_for_accepted'] > 0) $total_data['order_rate_for_achieved'] = 100;
            else $total_data['order_rate_for_achieved'] = 0;
        }

        // 审核

        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;


        $total_data = $total_data;



        return datatable_response($list, $draw, $total);

    }


    // 【统计】部门看板
    public function view_statistic_department()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

        // 部门统计
        $query_order = DK_Common__Order::select('team_id')
            ->addSelect(DB::raw("
                    count(DISTINCT creator_id) as staff_count,
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside,
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                    
                "))
            ->where('published_date',$the_day)
            ->groupBy('team_id')
            ->get()
            ->keyBy('team_id')
            ->toArray();
//        dd($query_order);

        $query = DK_Common__Team::select('id','name')
//            ->withCount([
//                'department_district_staff_list as staff_count' => function($query) use($the_day) {
//                    $query->whereHas('order_list', function($query) use($the_day) {
//                        $query->where('published_date',$the_day);
//                    });
//                },
//                'order_list_for_district as order_count_for_all'=>function($query) use($the_day) {
//                    $query->where('is_published', 1)->where('published_date',$the_day);
//                },
//                'order_list_for_district as order_count_for_accepted'=>function($query) use($the_day) {
//                    $query->where('inspected_result', '通过')->where('published_date',$the_day);
//                }
//            ])
            ->where('department_type',11)
            ->where(['item_status'=>1]);

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
        else $list = $query->skip($skip)->take($limit)->get();

        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['name'] = '所有区';
        $total_data['staff_count'] = 0;
        $total_data['order_count_for_all'] = 0;
        $total_data['order_count_for_inspected'] = 0;
        $total_data['order_count_for_accepted'] = 0;
        $total_data['order_count_for_refused'] = 0;
        $total_data['order_count_for_repeated'] = 0;
        $total_data['order_count_for_accepted_inside'] = 0;

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
        $total_data['order_count_for_delivered_actual_per'] = 0;



        foreach ($list as $k => $v)
        {
            if(isset($query_order[$v->id]))
            {
                $list[$k]->staff_count = $query_order[$v->id]['staff_count'];
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->staff_count = 0;
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

                $list[$k]->order_count_for_delivered = 0;
                $list[$k]->order_count_for_delivered_completed = 0;
                $list[$k]->order_count_for_delivered_inside = 0;
                $list[$k]->order_count_for_delivered_tomorrow = 0;
                $list[$k]->order_count_for_delivered_repeated = 0;
                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

            // 人均提交量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_all_per = round(($v->order_count_for_all / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_all_per = 0;

            // 人均通过量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_accepted_per = round(($v->order_count_for_accepted / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_accepted_per = 0;


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;
            // 实际产出
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;


            // 人均交付量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_per = round(($v->order_count_for_delivered / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_per = 0;

            // 人均交付有效量
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_effective_per = round(($v->order_count_for_delivered_effective / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_effective_per = 0;

            // 人均实际产出
            if($v->staff_count > 0)
            {
                $list[$k]->order_count_for_delivered_actual_per = round(($v->order_count_for_delivered_actual / $v->staff_count),2);
            }
            else $list[$k]->order_count_for_delivered_actual_per = 0;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



            $total_data['staff_count'] += $v->staff_count;
            $total_data['order_count_for_all'] += $v->order_count_for_all;
            $total_data['order_count_for_inspected'] += $v->order_count_for_inspected;
            $total_data['order_count_for_accepted'] += $v->order_count_for_accepted;
            $total_data['order_count_for_refused'] += $v->order_count_for_refused;
            $total_data['order_count_for_repeated'] += $v->order_count_for_repeated;
            $total_data['order_count_for_accepted_inside'] += $v->order_count_for_accepted_inside;

            $total_data['order_count_for_delivered'] += $v->order_count_for_delivered;
            $total_data['order_count_for_delivered_completed'] += $v->order_count_for_delivered_completed;
            $total_data['order_count_for_delivered_inside'] += $v->order_count_for_delivered_inside;
            $total_data['order_count_for_delivered_tomorrow'] += $v->order_count_for_delivered_tomorrow;
            $total_data['order_count_for_delivered_repeated'] += $v->order_count_for_delivered_repeated;
            $total_data['order_count_for_delivered_rejected'] += $v->order_count_for_delivered_rejected;

            $total_data['order_count_for_delivered_effective'] += $v->order_count_for_delivered_effective;
            $total_data['order_count_for_delivered_actual'] += $v->order_count_for_delivered_actual;

        }

        // 通过率
        if($total_data['order_count_for_all'] > 0)
        {
            $total_data['order_rate_for_accepted'] = round(($total_data['order_count_for_accepted'] * 100 / $total_data['order_count_for_all']),2);
        }
        else $total_data['order_rate_for_accepted'] = 0;

        // 人均提交量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_all_per'] = round(($total_data['order_count_for_all'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_all_per'] = 0;

        // 人均通过量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_accepted_per'] = round(($total_data['order_count_for_accepted'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_accepted_per'] = 0;



        // 人均交付量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_per'] = round(($total_data['order_count_for_delivered'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_all_per'] = 0;

        // 人均有效交付量
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_effective_per'] = round(($total_data['order_count_for_delivered_effective'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_delivered_effective_per'] = 0;

        // 人均实际产出
        if($total_data['staff_count'] > 0)
        {
            $total_data['order_count_for_delivered_actual_per'] = round(($total_data['order_count_for_delivered_actual'] / $total_data['staff_count']),2);
        }
        else $total_data['order_count_for_delivered_actual_per'] = 0;

        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;

        $total_data = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }




}