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
//                'department_district_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
//                'department_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
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
    public function o1__get_statistic_data_of_production_caller_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 员工统计
        $query_order = DK_Common__Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->groupBy('creator_id');


        // 员工（经理）统计
        $query_order_for_manager = DK_Common__Order::select('department_manager_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->groupBy('department_manager_id');


        // 员工（组长）统计
        $query_order_for_supervisor = DK_Common__Order::select('department_supervisor_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->groupBy('department_supervisor_id');


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $project_id = $post_data['project'];

                $query_order->where('project_id', $project_id);
                $query_order_for_manager->where('project_id', $project_id);
                $query_order_for_supervisor->where('project_id', $project_id);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->where('published_date',$the_date);
            $query_order_for_manager->where('published_date',$the_date);
            $query_order_for_supervisor->where('published_date',$the_date);

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

            $query_order->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_for_manager->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
            $query_order_for_supervisor->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start']))
            {
                $query_order->where('published_date', '>=', $post_data['date_start']);
                $query_order_for_manager->where('published_date', '>=', $post_data['date_start']);
                $query_order_for_supervisor->where('published_date', '>=', $post_data['date_start']);
            }
            if(!empty($post_data['date_ended']))
            {
                $query_order->where('published_date', '<=', $post_data['date_ended']);
                $query_order_for_manager->where('published_date', '<=', $post_data['date_ended']);
                $query_order_for_supervisor->where('published_date', '<=', $post_data['date_ended']);
            }
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('creator_id')->toArray();
        $query_order_for_manager = $query_order_for_manager->get()->keyBy('department_manager_id')->toArray();
        $query_order_for_supervisor = $query_order_for_supervisor->get()->keyBy('department_supervisor_id')->toArray();
//        dd($query_order);



        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
//                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
                'department_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
            ])
            ->where('user_status',1)
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");



        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        // 客服经理
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
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

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;

            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;






            // 组长
            if($v->department_group_er) $supervisor_id = $v->department_group_er->leader_id;
            else $supervisor_id = -1;
            if($v->department_group_er && isset($query_order_for_supervisor[$supervisor_id]))
            {
                $list[$k]->group_count_for_all = $query_order_for_supervisor[$supervisor_id]['order_count_for_all'];
                $list[$k]->group_count_for_inspected = $query_order_for_supervisor[$supervisor_id]['order_count_for_inspected'];
                $list[$k]->group_count_for_accepted = $query_order_for_supervisor[$supervisor_id]['order_count_for_accepted'];
                $list[$k]->group_count_for_refused = $query_order_for_supervisor[$supervisor_id]['order_count_for_refused'];
                $list[$k]->group_count_for_repeated = $query_order_for_supervisor[$supervisor_id]['order_count_for_repeated'];
            }
            else
            {
                $list[$k]->group_count_for_all = 0;
                $list[$k]->group_count_for_inspected = 0;
                $list[$k]->group_count_for_accepted = 0;
                $list[$k]->group_count_for_refused = 0;
                $list[$k]->group_count_for_repeated = 0;
            }

            // 审核
            $v->group_count_for_accepted_inside = 0;
            // 有效单量
            $v->group_count_for_effective = $v->group_count_for_inspected - $v->group_count_for_refused - $v->group_count_for_repeated;
            // 有效率
            if($v->group_count_for_all > 0)
            {
                $v->group_rate_for_accepted = round(($v->group_count_for_accepted * 100 / $v->group_count_for_all),2);
            }
            else $v->group_rate_for_accepted = 0;






            // 经理
            if($v->department_district_er) $manager_id = $v->department_district_er->leader_id;
            else $manager_id = -1;
            if($v->department_district_er && isset($query_order_for_manager[$manager_id]))
            {
                $list[$k]->district_count_for_all = $query_order_for_manager[$manager_id]['order_count_for_all'];
                $list[$k]->district_count_for_inspected = $query_order_for_manager[$manager_id]['order_count_for_inspected'];
                $list[$k]->district_count_for_accepted = $query_order_for_manager[$manager_id]['order_count_for_accepted'];
                $list[$k]->district_count_for_refused = $query_order_for_manager[$manager_id]['order_count_for_refused'];
                $list[$k]->district_count_for_repeated = $query_order_for_manager[$manager_id]['order_count_for_repeated'];
            }
            else
            {
                $list[$k]->district_count_for_all = 0;
                $list[$k]->district_count_for_inspected = 0;
                $list[$k]->district_count_for_accepted = 0;
                $list[$k]->district_count_for_refused = 0;
                $list[$k]->district_count_for_repeated = 0;
            }

            // 审核
            $v->district_count_for_accepted_inside = 0;
            // 有效单量
            $v->district_count_for_effective = $v->district_count_for_inspected - $v->district_count_for_refused - $v->district_count_for_repeated;
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

        $grouped_by_district = $list->groupBy('team_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

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
    public function o1__get_statistic_data_of_production_caller_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Common__Order::select('creator_id','published_date')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
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

        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0,'-1','0']))
            {
                $query_order->where('team_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0,'-1','0']))
            {
                $query_order->where('team_group_id', $post_data['department_group']);
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


        $query = DK_User::select(['id','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");


        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0,'-1','0']))
            {
                $query->where('team_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0,'-1','0']))
            {
                $query->where('team_group_id', $post_data['department_group']);
            }
        }

        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
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
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
            }

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
    // 【生产-统计】坐席近期
    public function o1__get_statistic_data_of_production_caller_recent($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_supervisor_id','published_at')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 工单统计
            $query_order = DK_Common__Order::select('creator_id','published_at')
                ->groupBy('creator_id');
        }


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
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "));


//        count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
//                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
//                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
//                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
//                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
//                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside

        if($rank_staff_type == 41)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 工单统计
            $order_list = $query_order->get();
        }

        foreach($order_list as $k => $v)
        {
            $v->order_count_for_effective = $v->order_count_for_accepted + $v->order_count_for_repeated + $v->order_count_for_accepted_inside;

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





        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 部门
        if($me->user_type == 41)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('team_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('team_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);
        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);
        }
        else
        {
            $query->where('team_id','>',0)
//                ->where('team_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);
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
                if(isset($order_list[$v->id][6])) $list[$k]->order_6 = $order_list[$v->id][6]['order_count_for_effective'];
                else $list[$k]->order_6 = 0;
                if(isset($order_list[$v->id][5])) $list[$k]->order_5 = $order_list[$v->id][5]['order_count_for_effective'];
                else $list[$k]->order_5 = 0;
                if(isset($order_list[$v->id][4])) $list[$k]->order_4 = $order_list[$v->id][4]['order_count_for_effective'];
                else $list[$k]->order_4 = 0;
                if(isset($order_list[$v->id][3])) $list[$k]->order_3 = $order_list[$v->id][3]['order_count_for_effective'];
                else $list[$k]->order_3 = 0;
                if(isset($order_list[$v->id][2])) $list[$k]->order_2 = $order_list[$v->id][2]['order_count_for_effective'];
                else $list[$k]->order_2 = 0;
                if(isset($order_list[$v->id][1])) $list[$k]->order_1 = $order_list[$v->id][1]['order_count_for_effective'];
                else $list[$k]->order_1 = 0;
                if(isset($order_list[$v->id][0])) $list[$k]->order_0 = $order_list[$v->id][0]['order_count_for_effective'];
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
    public function o1__get_statistic_data_of_production_caller_daily($post_data)
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




        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
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


        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
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
            if($me->staff_category == 31)
            {
                $department_id = $me->department_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('department_id',$department_id)->get();
                $query->whereIn('id',$project_list);
            }
            else if($me->staff_category == 41)
            {
                $team_id = $me->team_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }
        }

        // 质检部
        if($me->staff_category == 51)
        {
            $team_id = $me->team_id;
            if($me->team_id > 0)
            {
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);
            }

            if($me->staff_position == 99)
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
     * 数据-导出
     */
    // 【数据-导出】工单
    public function o1__statistic_order_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "period")
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start']  : date('Y-m-d');
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended']  : date('Y-m-d');

            $record_operate_type = 21;
            $record_column_type = 'period';
            $record_before = $the_start;
            $record_after = $the_ended;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].'00:00:00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].'23:59:50'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0',-1,0]))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
//            $query->whereBetween('inspected_at',[$start_timestamp,$ended_timestamp]);
            $query->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->where('published_date',$the_date);
        }
        else if($export_type == "period")
        {
            $query->whereBetween('published_date',[$the_start,$the_ended]);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('published_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('published_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('published_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

//        $data = $query->orderBy('inspected_at','desc')->orderBy('id','desc')->get();
//        $data = $query->orderBy('published_at','desc')->orderBy('id','desc')->get();
//        dd($the_month_start_date);
        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';


            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';


            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'发布时间',
                'creator_name'=>'创建人',
                'work_shift'=>'班次',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'发布时间',
                'creator_name'=>'创建人',
                'work_shift'=>'班次',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【工单】'.date('Ymd.His').'【口腔】'.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单
    public function o1__statistic_order_export_for_dental($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "period")
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start']  : date('Y-m-d');
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended']  : date('Y-m-d');

            $record_operate_type = 21;
            $record_column_type = 'period';
            $record_before = $the_start;
            $record_after = $the_ended;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].'00:00:00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].'23:59:50'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 1;

        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0',-1,0]))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
//            $query->whereBetween('inspected_at',[$start_timestamp,$ended_timestamp]);
            $query->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->where('published_date',$the_date);
        }
        else if($export_type == "period")
        {
            $query->whereBetween('published_date',[$the_start,$the_ended]);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('published_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('published_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('published_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

//        $data = $query->orderBy('inspected_at','desc')->orderBy('id','desc')->get();
//        $data = $query->orderBy('published_at','desc')->orderBy('id','desc')->get();
//        dd($the_month_start_date);
        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];

            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';


            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);


            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';


            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【工单】'.date('Ymd.His').'【口腔】'.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单
    public function o1__statistic_order_export_for_aesthetic($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "period")
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start']  : date('Y-m-d');
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended']  : date('Y-m-d');

            $record_operate_type = 21;
            $record_column_type = 'period';
            $record_before = $the_start;
            $record_after = $the_ended;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].'00:00:00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].'23:59:50'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 11;

        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
            }
        }

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
            $query->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->where('published_date',$the_date);
        }
        else if($export_type == "period")
        {
            $query->whereBetween('published_date',[$the_start,$the_ended]);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('published_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('published_date', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('published_date', '<=', $the_ended_timestamp);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';


            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);


            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'work_shift'=>'班次',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【工单】'.date('Ymd.His').'【医美】'.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>60, 'Q'=>60, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单
    public function o1__statistic_order_export_for_luxury($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';

        $export_type = isset($post_data['export_type']) ? $post_data['export_type']  : '';
        if($export_type == "all")
        {
            $record_operate_type = 100;
            $record_column_type = 'all';
            $record_before = '全部';
            $record_after = '全部';
        }
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "month")
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
        else if($export_type == "period")
        {
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start']  : date('Y-m-d');
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended']  : date('Y-m-d');

            $record_operate_type = 21;
            $record_column_type = 'period';
            $record_before = $the_start;
            $record_after = $the_ended;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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
            $the_start  = isset($post_data['order_start']) ? $post_data['order_start'].'00:00:00'  : '';
            $the_ended  = isset($post_data['order_ended']) ? $post_data['order_ended'].'23:59:50'  : '';

            $the_start_timestamp  = strtotime($the_start);
            $the_ended_timestamp  = strtotime($the_ended);

            $record_operate_type = 1;
            $record_before = $the_start;
            $record_after = $the_ended;
        }


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 31;

        $staff_id = 0;
        $client_id = 0;
        $project_id = 0;

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
            $query->whereBetween('published_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->where('published_date',$the_date);
        }
        else if($export_type == "period")
        {
            $query->whereBetween('published_date',[$the_start,$the_ended]);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('published_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('published_date', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('published_date', '<=', $the_ended_timestamp);
            }
        }


        if($staff_id) $query->where('creator_id',$staff_id);
        if($client_id) $query->where('client_id',$client_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';


            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);


            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'work_shift'=>'班次',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【工单】'.date('Ymd.His').'【二奢】'.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>60, 'Q'=>60, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }




    // 【数据-导出】工单-下载-IDs
    public function o1__statistic_order_export_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;


//        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 1;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
//            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单-下载-IDs
    public function o1__statistic_order_export_by_ids_for_dental($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 1;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'work_shift'=>'班次',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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




        $title = '【工单】'.date('Ymd.His').'【口腔】'.'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单-下载-IDs
    public function o1__statistic_order_export_by_ids_for_aesthetic($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 11;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';


            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';


            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';


            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "脸部";
            else if($v['field_1'] == 21) $cellData[$k]['field_1'] = "植发";
            else if($v['field_1'] == 31) $cellData[$k]['field_1'] = "身体";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'work_shift'=>'班次',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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




        $title = '【工单】'.date('Ymd.His').'【医美】'.'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>60, 'Q'=>60, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】工单-下载-IDs
    public function o1__statistic_order_export_by_ids_for_luxury($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 31;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            // ID
            $cellData[$k]['id'] = $v['id'];

            // ID
            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            // ID
            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            // ID
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            // ID
            if($v['field_2'] == 1) $cellData[$k]['work_shift'] = '白班';
            else if($v['field_2'] == 9) $cellData[$k]['work_shift'] = '夜班';
            else $cellData[$k]['work_shift'] = '--';

            // ID
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            // ID
            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];

            // ID
            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            // ID
            $cellData[$k]['client_name'] = $v['client_name'];
            // ID
            $cellData[$k]['client_phone'] = $v['client_phone'];
            // ID
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            // ID
            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            // ID
            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'work_shift'=>'班次',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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




        $title = '【工单】'.date('Ymd.His').'【二奢】'.'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>60, 'Q'=>60, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }




    // 【数据-导出】交付
    public function o1__statistic_delivery_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';
        $record_data_title = '';

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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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


        $client_id = 0;
        $project_id = 0;

        // 客户
        $client_title = '';
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
                $client_er = DK_Common__Client::find($client_id);
                if($client_er)
                {
                    $client_title = '【'.$client_er->username.'】';
                    $record_data_title = $client_er->username;
                }
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->join('dk_pivot_client_delivery', 'dk_admin_order.id', '=', 'dk_pivot_client_delivery.order_id')
            ->where('dk_admin_order.item_category',1)
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ]);



        if($export_type == "month")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_pivot_client_delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_pivot_client_delivery.client_id',$client_id);
        if($project_id) $query->where('dk_pivot_client_delivery.project_id',$project_id);


        $data = $query->orderBy('dk_pivot_client_delivery.id','desc')->get();
        $data = $data->toArray();

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 110;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【交付】'.date('Ymd.His').$project_title.$client_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('交付工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】交付
    public function o1__statistic_delivery_export_for_dental($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';
        $record_data_title = '';

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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 11;

        $client_id = 0;
        $project_id = 0;

        // 客户
        $client_title = '';
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
                $client_er = DK_Common__Client::find($client_id);
                if($client_er)
                {
                    $client_title = '【'.$client_er->username.'】';
                    $record_data_title = $client_er->username;
                }
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }



        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->join('dk_pivot_client_delivery', 'dk_admin_order.id', '=', 'dk_pivot_client_delivery.order_id')
            ->where('dk_admin_order.item_category',1)
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ]);



        if($export_type == "month")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_pivot_client_delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_pivot_client_delivery.client_id',$client_id);
        if($project_id) $query->where('dk_pivot_client_delivery.project_id',$project_id);


        $data = $query->orderBy('dk_pivot_client_delivery.id','desc')->get();
        $data = $data->toArray();

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 110;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【交付】'.date('Ymd.His').'【口腔】'.$project_title.$client_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('交付工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】交付
    public function o1__statistic_delivery_export_for_aesthetic($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 11;


        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
            }
        }

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
            $query->whereBetween('published_date',[$start_timestamp,$ended_timestamp]);
        }
        else if($export_type == "date")
        {
            $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('inspected_at',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '>=', $post_data['order_start']);
                $query->where('inspected_at', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '<=', $post_data['order_ended']);
                $query->where('inspected_at', '<=', $the_ended_timestamp);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

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

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【交付】'.date('Ymd.His').'【医美】'.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>60, 'P'=>60, 'Q'=>20, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据-导出】交付
    public function o1__statistic_delivery_export_for_luxury($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $time = time();

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';
        $record_data_title = '';

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
        else if($export_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;
        }
        else if($export_type == "latest")
        {
            $record_last = DK_Record::select('*')
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


        $item_category = isset($post_data['item_category']) ? $post_data['item_category'] : 31;


        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;


        // 客户
        $client_title = '';
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
                $client_er = DK_Common__Client::find($client_id);
                if($client_er)
                {
                    $client_title = '【'.$client_er->username.'】';
                    $record_data_title = $client_er->username;
                }
            }
        }

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $staff_id = $post_data['staff'];
            }
        }

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_date  = isset($post_data['date'])  ? $post_data['date']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->join('dk_pivot_client_delivery', 'dk_admin_order.id', '=', 'dk_pivot_client_delivery.order_id')
            ->where('dk_admin_order.item_category',31)
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('item_category',$item_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });



        if($export_type == "month")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_pivot_client_delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_pivot_client_delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_pivot_client_delivery.client_id',$client_id);
        if($project_id) $query->where('dk_pivot_client_delivery.project_id',$project_id);


        $data = $query->orderBy('dk_pivot_client_delivery.id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];

            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['team'] = !empty($cellData[$k]['team']) ? $cellData[$k]['team'] : '--';

            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

//            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
        else if($export_type == "date")
        {
            $month_title = '【'.$the_date.'】';
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


        $title = '【交付】'.date('Ymd.His').'【二奢】'.$client_title.$project_title.$month_title.$time_title;

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>60, 'P'=>60, 'Q'=>20, 'R'=>20, 'S'=>20, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }

    


    // 【数据-导出】去重数据-下载
    public function o1__statistic_duplicate_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return view($this->view_blade_403);



        $time = time();
        $date = date('Y-m-d');

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';

        $when_data = [];
        $time_type = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == "all")
        {
            $record_operate_type = 101;
            $record_column_type = 'all';
            $record_before = '全部';
            $record_after = '全部';

            $time_title = '【全部】';
        }
        else if($time_type == "date")
        {
            $the_date  = isset($post_data['date']) ? $post_data['date']  : date('Y-m-d');

            $when_data['the_date'] = $the_date;

            $record_operate_type = 31;
            $record_column_type = 'date';
            $record_before = $the_date;
            $record_after = $the_date;

            $time_title = '【'.$the_date.'】';
        }
        else if($time_type == "month")
        {
            $the_month  = isset($post_data['month']) ? $post_data['month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $when_data['the_month'] = $the_month;
            $when_data['the_month_start_date'] = $the_month_start_date;
            $when_data['the_month_ended_date'] = $the_month_ended_date;

            $record_operate_type = 11;
            $record_column_type = 'month';
            $record_before = $the_month;
            $record_after = $the_month;

            $time_title = '【'.$the_month.'月】';
        }
        else if($time_type == 'period')
        {

            $the_start  = isset($post_data['start']) ? $post_data['start']  : date('Y-m-d');
            $the_ended  = isset($post_data['ended']) ? $post_data['ended']  : date('Y-m-d');

            $when_data['the_start'] = $the_start;
            $when_data['the_ended'] = $the_ended;

            $record_operate_type = 21;
            $record_column_type = 'period';
            $record_before = $the_start;
            $record_after = $the_ended;

            $time_title = '【按时间段】【'.$the_start.'-'.$the_ended.'】';

        }
        else if($time_type == "latest")
        {
            $record_last = DK_Record::select('*')
                ->where(['creator_id'=>$me->id,'operate_category'=>[109,110],'operate_type'=>99])
                ->orderBy('id','desc')->first();

            if($record_last) $start_timestamp = $record_last->after;
            else $start_timestamp = 0;

            $ended_timestamp = $time;

            $record_operate_type = 99;
            $record_column_type = 'datetime';
            $record_before = '';
            $record_after = $time;

            $time_title = '【最新】';
        }
        else
        {
            $record_operate_type = 101;
            $record_column_type = 'all';
            $record_before = '全部';
            $record_after = '全部';

            $time_title = '【全部】';
        }


        $client_id = 0;
        $project_id = 0;

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $project_id = $post_data['project'];
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title .= $project_er->name.' ';
                }

            }
        }

        // 客户
        $client_title = '';
        $record_data_title = '';
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0,'-1','0']))
            {
                $client_id = $post_data['client'];
                $client_er = DK_Common__Project::find($client_id);
                if($client_er)
                {
                    $client_title = '【'.$client_er->username.'】';
                    $record_data_title = $client_er->username.' ';
                }
            }
        }


//        if($client_id > 0 || $project_id > 0)
//        {
//        }
//        else dd('');


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 111;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        if($project_id)
        {
            $record_data["item_id"] = $project_id;
            $record_data["title"] = $record_data_title;
        }
        if($client_id)
        {
            $record_data["item_id"] = $client_id;
            $record_data["title"] = $record_data_title;
        }

        $record->fill($record_data)->save();

        $title = '【去重】'.date('Ymd.His').$time_title.$project_title.$client_title;


        // 工单
        $query = DK_Common__Order::select('dk_pivot_client_delivery.client_phone')
            ->join('dk_pivot_client_delivery', 'dk_admin_order.id', '=', 'dk_pivot_client_delivery.order_id')
//            ->where(function($q) {
//                $q->where('dk_admin_order.inspected_result', '不合格')
//                    ->orWhereNotNull('dk_pivot_client_delivery.order_id'); // 保留原有连接条件
//            })
//            ->where('dk_admin_order.item_category',1)
            ->when(($time_type == "date"), function ($query) use ($when_data) {
                return $query->where('dk_pivot_client_delivery.delivered_date',$when_data['date']);
            })
            ->when(($time_type == "month"), function ($query) use ($when_data) {
                return $query->whereBetween('dk_pivot_client_delivery.delivered_date',[$when_data['the_month_start_date'],$when_data['the_month_ended_date']]);
            })
            ->when(($time_type == "period"), function ($query) use ($when_data) {
                return $query->where('dk_pivot_client_delivery.delivered_date', '>=',$when_data['the_start'])
                    ->where('dk_pivot_client_delivery.delivered_date', '<=',$when_data['the_ended']);
            });


        $query2 = DK_Common__Order::select('client_phone')->where('inspected_result', '不合格');

        if($project_id)
        {
            $query->where('dk_pivot_client_delivery.project_id',$project_id);
//            $query->where(function ($query) use($project_id) {
//                $query->where('dk_admin_order.project_id',$project_id)
//                    ->orWhere('dk_pivot_client_delivery.project_id',$project_id);
//            });
            $query2->where('project_id',$project_id);
        }
        if($client_id)
        {
            $query->where('dk_pivot_client_delivery.client_id',$client_id);
            $query2->where('client_id',$client_id);
        }

        $data = $query->orderBy('dk_pivot_client_delivery.id','desc')->get();
        $data2 = $query2->orderBy('id','desc')->get();

        $data = $data->merge($data2)->unique('client_phone');

        $upload_path = <<<EOF
resource/dk/admin/telephone/$date/
EOF;
        $url_path = env('DOMAIN_CDN').'/dk/admin/telephone/'.$date.'/';

        $storage_path = storage_path($upload_path);
        if (!is_dir($storage_path))
        {
            mkdir($storage_path, 0766, true);
        }
        $filename = $title;
        $extension = '.txt';

        $file_name = $filename.$extension;
        $file_url = $url_path.$file_name;
        $file_path = $storage_path.$file_name;

        // 打开文件准备写入
        $file = fopen($file_path, 'w');

        // 遍历电话号码数组，逐行写入文件
        foreach ($data as $phoneNumber)
        {
            fwrite($file, $phoneNumber->client_phone . PHP_EOL);
        }

        // 关闭文件
        fclose($file);


        return response()->download($file_path);

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


        $staff_list = DK_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
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
        $user = DK_User::find($user_id);

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


    // 【统计】排名
    public function view_statistic_rank()
    {
        $this->get_me();
        $me = $this->me;

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['team_id'] = $me->team_id;
            $department_group_list = DK_Common__Team::select('id','name')->where('superior_department_id',$me->team_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_rank'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-rank';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_supervisor_id')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 工单统计
            $query_order = DK_Common__Order::select('creator_id')
                ->groupBy('creator_id');
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('published_date',$the_day);
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

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }

        $query_order->addSelect(DB::raw("
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
                "));



        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 工单统计
            $query_order = $query_order->get()->keyBy('creator_id')->toArray();
        }




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





        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 部门
        if($me->user_type == 41)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('team_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('team_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);
        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);
        }
        else
        {
            $query->where('team_id','>',0)
                ->where('team_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);
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
            if(isset($query_order[$v->id]))
            {
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

            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;
            // 有效率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_effective = round(($v->order_count_for_effective * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_effective = 0;


            // 交付
            // 有效交付量
            $list[$k]->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;

            // 有效交付量
            $list[$k]->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;

            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_actual = 0;

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
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-rank-by-staff';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
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

        // bumen经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
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
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
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
    public function get_statistic_data_for_rank_by_staff_of_group($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Common__Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
            ->groupBy('creator_id');

        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $project_id = $post_data['project'];
                $query_order->where('project_id', $project_id);
            }
        }

        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('published_date',$the_day);
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

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }

        $query_order = $query_order->get()->keyBy('creator_id')->toArray();


        $query = DK_User::select(['id','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        dd($me->team_id);


        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
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
            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;
            }

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
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-rank-by-department';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_rank_by_department($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->where('team_id','>',0)
//            ->where('team_group_id','>',0)
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
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
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
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
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


    // 【统计】近期表现
    public function view_statistic_recent()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,81,84])) return view($this->view_blade_403);

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        if($me->user_type == 81)
        {
            $view_data['team_id'] = $me->team_id;
            $department_group_list = DK_Common__Team::select('id','name')->where('superior_department_id',$me->team_id)->get();
            $view_data['department_group_list'] = $department_group_list;
        }

        $view_data['menu_active_of_statistic_recent'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-recent';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_recent($post_data)
    {
        $this->get_me();
        $me = $this->me;



        $rank_object_type  = isset($post_data['rank_object_type'])  ? $post_data['rank_object_type']  : 'staff';
        $rank_staff_type  = isset($post_data['rank_staff_type'])  ? $post_data['rank_staff_type']  : 88;
//        dd($rank_staff_type);


        if($rank_staff_type == 41)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_manager_id','published_at')
                ->groupBy('department_manager_id');
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $query_order = DK_Common__Order::select('department_supervisor_id','published_at')
                ->groupBy('department_supervisor_id');
        }
        else
        {
            // 工单统计
            $query_order = DK_Common__Order::select('creator_id','published_at')
                ->groupBy('creator_id');
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');
            $query_order->where('published_date',$the_day);
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

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
            $query_order->where('published_date','>',date("Y-m-d",strtotime("-7 day")))
                ->addSelect(DB::raw("
                    DATE_FORMAT(published_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(published_date,'%e') as day,
                    count(*) as sum
                "))
                ->groupBy('published_date');
        }

        $query_order->addSelect(DB::raw("
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside
                "));



        if($rank_staff_type == 41)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 81)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_manager_id')->toArray();
        }
        else if($rank_staff_type == 84)
        {
            // 工单统计
            $order_list = $query_order->get()->groupBy('department_supervisor_id')->toArray();
        }
        else
        {
            // 工单统计
            $order_list = $query_order->get();
        }

        foreach($order_list as $k => $v)
        {
            $v->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;

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
        $use['the_day'] = 0;
        $use['the_month_start_timestamp'] = 0;
        $use['the_month_ended_timestamp'] = 0;





        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id'])
            ->where('user_status',1)
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ]);


        // 部门
        if($me->user_type == 41)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 81)
        {
            // 根据部门（大区）查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据部门（小组）查看
            $query->where('team_id', $me->team_id);
            $query->where('team_group_id', $me->team_group_id);
        }


        // 部门-大区
        if(!empty($post_data['department_district']))
        {
            if(!in_array($post_data['department_district'],[-1,0]))
            {
                $query->where('team_id', $post_data['department_district']);
            }
        }
        // 部门-小组
        if(!empty($post_data['department_group']))
        {
            if(!in_array($post_data['department_group'],[-1,0]))
            {
                $query->where('team_group_id', $post_data['department_group']);
            }
        }


        if($rank_staff_type == 81)
        {
            $query->where('user_type', 81);
        }
        else if($rank_staff_type == 84)
        {
            $query->where('user_type', 84);
        }
        else
        {
            $query->where('team_id','>',0)
//                ->where('team_group_id','>',0)
                ->whereIn('user_type',[81,84,88]);
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
//                if(isset($order_list[$v->id][7])) $list[$k]->order_7 = $order_list[$v->id][7]['order_count_for_delivered_effective'];
//                else $list[$k]->order_7 = 0;
                if(isset($order_list[$v->id][6])) $list[$k]->order_6 = $order_list[$v->id][6]['order_count_for_delivered_effective'];
                else $list[$k]->order_6 = 0;
                if(isset($order_list[$v->id][5])) $list[$k]->order_5 = $order_list[$v->id][5]['order_count_for_delivered_effective'];
                else $list[$k]->order_5 = 0;
                if(isset($order_list[$v->id][4])) $list[$k]->order_4 = $order_list[$v->id][4]['order_count_for_delivered_effective'];
                else $list[$k]->order_4 = 0;
                if(isset($order_list[$v->id][3])) $list[$k]->order_3 = $order_list[$v->id][3]['order_count_for_delivered_effective'];
                else $list[$k]->order_3 = 0;
                if(isset($order_list[$v->id][2])) $list[$k]->order_2 = $order_list[$v->id][2]['order_count_for_delivered_effective'];
                else $list[$k]->order_2 = 0;
                if(isset($order_list[$v->id][1])) $list[$k]->order_1 = $order_list[$v->id][1]['order_count_for_delivered_effective'];
                else $list[$k]->order_1 = 0;
                if(isset($order_list[$v->id][0])) $list[$k]->order_0 = $order_list[$v->id][0]['order_count_for_delivered_effective'];
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


    // 【统计】客服看板
    public function view_statistic_customer_service()
    {
        $this->get_me();
        $me = $this->me;

        $view_data['menu_active_of_statistic_customer_service'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->where('team_id','>',0)
            ->where('team_group_id','>',0)
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
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_group_id', $me->team_group_id);
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
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_manager as district_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
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
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_inspected' => function($query) use($the_day,$project_id) {
                                        $query->where('is_published', 1)->where('inspected_status', 1)
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_accepted' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '通过')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_refused' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '拒绝')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
                                    },
                                    'order_list_for_supervisor as group_count_for_repeated' => function($query) use($the_day,$project_id) {
                                        $query->where('inspected_result', '重复')
                                            ->when($project_id, function ($query) use ($project_id) {
                                                return $query->where('project_id', $project_id);
                                            })
                                            ->where('published_date',$the_day);
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
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_inspected'=>function($query) use($the_day,$project_id) {
                    $query->where('is_published', 1)->where('inspected_status', 1)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('published_date',$the_day);
                },
                'order_list as order_count_for_accepted'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '通过');
                },
                'order_list as order_count_for_refused'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '拒绝');
                },
                'order_list as order_count_for_repeated'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
                        ->when($project_id, function ($query) use ($project_id) {
                            return $query->where('project_id', $project_id);
                        })
                        ->where('inspected_result', '重复');
                },
                'order_list as order_count_for_accepted_inside'=>function($query) use($the_day,$project_id) {
                    $query->where('published_date',$the_day)
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

        $grouped_by_district = $list->groupBy('team_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

            $grouped_by_group = $list->groupBy('team_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    public function get_statistic_data_for_customer_service_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 员工统计
        $query_order = DK_Common__Order::select('creator_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
//            ->addSelect(DB::raw("
//                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
//                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
//                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
//                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
//                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
//                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
//                "))
            ->groupBy('creator_id');


        // 员工（经理）统计
        $query_order_for_manager = DK_Common__Order::select('department_manager_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
//            ->addSelect(DB::raw("
//                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
//                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
//                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
//                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
//                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
//                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
//                "))
            ->groupBy('department_manager_id');


        // 员工（组长）统计
        $query_order_for_supervisor = DK_Common__Order::select('department_supervisor_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count_for_all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count_for_repeated,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count_for_accepted_inside
                "))
//            ->addSelect(DB::raw("
//                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
//                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
//                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
//                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
//                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
//                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
//                "))
            ->groupBy('department_supervisor_id');


        // 项目
        $project_id = 0;
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[0,-1]))
            {
                $project_id = $post_data['project'];

                $query_order->where('project_id', $project_id);
                $query_order_for_manager->where('project_id', $project_id);
                $query_order_for_supervisor->where('project_id', $project_id);
            }
        }


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->where('published_date',$the_day);
            $query_order_for_manager->where('published_date',$the_day);
            $query_order_for_supervisor->where('published_date',$the_day);

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

            $query_order->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_order_for_manager->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
            $query_order_for_supervisor->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('creator_id')->toArray();
        $query_order_for_manager = $query_order_for_manager->get()->keyBy('department_manager_id')->toArray();
        $query_order_for_supervisor = $query_order_for_supervisor->get()->keyBy('department_supervisor_id')->toArray();
//        dd($query_order);



        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
//                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
                'department_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
            ])
            ->where('user_status',1)
            ->where('team_id','>',0)
            ->where('team_group_id','>',0)
            ->whereIn('user_type',[84,88]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");



        // 部门经理
        if($me->user_type == 41)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        // 客服经理
        else if($me->user_type == 81)
        {
            // 根据属下查看
//            $subordinates_array = DK_User::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//            $sub_subordinates_array = DK_User::select('id')->whereIn('superior_id',$subordinates_array)->get()->pluck('id')->toArray();
//            $query->whereHas('superior', function($query) use($subordinates_array) { $query->whereIn('id',$subordinates_array); } );

            // 根据部门查看
            $query->where('team_id', $me->team_id);
        }
        else if($me->user_type == 84)
        {
            // 根据属下查看
//            $query->whereHas('superior', function($query) use($me) { $query->where('id',$me->id); } );

            // 根据部门查看
            $query->where('team_group_id', $me->team_group_id);
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

            if(isset($query_order[$v->id]))
            {
                $list[$k]->order_count_for_all = $query_order[$v->id]['order_count_for_all'];
                $list[$k]->order_count_for_inspected = $query_order[$v->id]['order_count_for_inspected'];
                $list[$k]->order_count_for_accepted = $query_order[$v->id]['order_count_for_accepted'];
                $list[$k]->order_count_for_refused = $query_order[$v->id]['order_count_for_refused'];
                $list[$k]->order_count_for_repeated = $query_order[$v->id]['order_count_for_repeated'];
                $list[$k]->order_count_for_accepted_inside = $query_order[$v->id]['order_count_for_accepted_inside'];

//                $list[$k]->order_count_for_delivered = $query_order[$v->id]['order_count_for_delivered'];
//                $list[$k]->order_count_for_delivered_completed = $query_order[$v->id]['order_count_for_delivered_completed'];
//                $list[$k]->order_count_for_delivered_inside = $query_order[$v->id]['order_count_for_delivered_inside'];
//                $list[$k]->order_count_for_delivered_tomorrow = $query_order[$v->id]['order_count_for_delivered_tomorrow'];
//                $list[$k]->order_count_for_delivered_repeated = $query_order[$v->id]['order_count_for_delivered_repeated'];
//                $list[$k]->order_count_for_delivered_rejected = $query_order[$v->id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->order_count_for_all = 0;
                $list[$k]->order_count_for_inspected = 0;
                $list[$k]->order_count_for_accepted = 0;
                $list[$k]->order_count_for_refused = 0;
                $list[$k]->order_count_for_repeated = 0;
                $list[$k]->order_count_for_accepted_inside = 0;

//                $list[$k]->order_count_for_delivered = 0;
//                $list[$k]->order_count_for_delivered_completed = 0;
//                $list[$k]->order_count_for_delivered_inside = 0;
//                $list[$k]->order_count_for_delivered_tomorrow = 0;
//                $list[$k]->order_count_for_delivered_repeated = 0;
//                $list[$k]->order_count_for_delivered_rejected = 0;
            }

            // 审核
            // 有效单量
            $v->order_count_for_effective = $v->order_count_for_inspected - $v->order_count_for_refused - $v->order_count_for_repeated;

            // 通过率
            if($v->order_count_for_all > 0)
            {
                $list[$k]->order_rate_for_accepted = round(($v->order_count_for_accepted * 100 / $v->order_count_for_all),2);
            }
            else $list[$k]->order_rate_for_accepted = 0;

//            // 交付
//            // 有效交付量
//            $v->order_count_for_delivered_effective = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow + $v->order_count_for_delivered_inside;
//
//            // 有效交付率
//            if($v->order_count_for_delivered > 0)
//            {
//                $v->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
//            }
//            else $v->order_rate_for_delivered_effective = 0;
//
//            // 实际产量
//            $v->order_count_for_delivered_actual = $v->order_count_for_delivered_completed + $v->order_count_for_delivered_tomorrow;
//            // 实际产率
//            if($v->order_count_for_delivered > 0)
//            {
//                $v->order_rate_for_delivered_actual = round(($v->order_count_for_delivered_actual * 100 / $v->order_count_for_delivered),2);
//            }
//            else $v->order_rate_for_delivered_actual = 0;




            // 组长
            if($v->department_group_er) $supervisor_id = $v->department_group_er->leader_id;
            else $supervisor_id = -1;
            if($v->department_group_er && isset($query_order_for_supervisor[$supervisor_id]))
            {
                $list[$k]->group_count_for_all = $query_order_for_supervisor[$supervisor_id]['order_count_for_all'];
                $list[$k]->group_count_for_inspected = $query_order_for_supervisor[$supervisor_id]['order_count_for_inspected'];
                $list[$k]->group_count_for_accepted = $query_order_for_supervisor[$supervisor_id]['order_count_for_accepted'];
                $list[$k]->group_count_for_refused = $query_order_for_supervisor[$supervisor_id]['order_count_for_refused'];
                $list[$k]->group_count_for_repeated = $query_order_for_supervisor[$supervisor_id]['order_count_for_repeated'];

//                $list[$k]->group_count_for_delivered = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered'];
//                $list[$k]->group_count_for_delivered_completed = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_completed'];
//                $list[$k]->group_count_for_delivered_inside = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_inside'];
//                $list[$k]->group_count_for_delivered_tomorrow = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_tomorrow'];
//                $list[$k]->group_count_for_delivered_repeated = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_repeated'];
//                $list[$k]->group_count_for_delivered_rejected = $query_order_for_supervisor[$supervisor_id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->group_count_for_all = 0;
                $list[$k]->group_count_for_inspected = 0;
                $list[$k]->group_count_for_accepted = 0;
                $list[$k]->group_count_for_refused = 0;
                $list[$k]->group_count_for_repeated = 0;

//                $list[$k]->group_count_for_delivered = 0;
//                $list[$k]->group_count_for_delivered_completed = 0;
//                $list[$k]->group_count_for_delivered_inside = 0;
//                $list[$k]->group_count_for_delivered_tomorrow = 0;
//                $list[$k]->group_count_for_delivered_repeated = 0;
//                $list[$k]->group_count_for_delivered_rejected = 0;
            }

            // 审核
            $v->group_count_for_accepted_inside = 0;
            // 有效单量
            $v->group_count_for_effective = $v->group_count_for_inspected - $v->group_count_for_refused - $v->group_count_for_repeated;
            // 有效率
            if($v->group_count_for_all > 0)
            {
                $v->group_rate_for_accepted = round(($v->group_count_for_accepted * 100 / $v->group_count_for_all),2);
            }
            else $v->group_rate_for_accepted = 0;


//            // 交付
//            // 有效交付量
//            $v->group_count_for_delivered_effective = $v->group_count_for_delivered_completed + $v->group_count_for_delivered_tomorrow + $v->group_count_for_delivered_inside;
//            // 有效交付率
//            if($v->group_count_for_delivered > 0)
//            {
//                $v->group_rate_for_delivered_effective = round(($v->group_count_for_delivered_effective * 100 / $v->group_count_for_delivered),2);
//            }
//            else $v->group_rate_for_delivered_effective = 0;
//
//            // 实际产量
//            $v->group_count_for_delivered_actual = $v->group_count_for_delivered_completed + $v->group_count_for_delivered_tomorrow;
//            // 实际产率
//            if($v->group_count_for_delivered > 0)
//            {
//                $v->group_rate_for_delivered_actual = round(($v->group_count_for_delivered_actual * 100 / $v->group_count_for_delivered),2);
//            }
//            else $v->group_rate_for_delivered_actual = 0;




            // 经理
            if($v->department_district_er) $manager_id = $v->department_district_er->leader_id;
            else $manager_id = -1;
            if($v->department_district_er && isset($query_order_for_manager[$manager_id]))
            {
                $list[$k]->district_count_for_all = $query_order_for_manager[$manager_id]['order_count_for_all'];
                $list[$k]->district_count_for_inspected = $query_order_for_manager[$manager_id]['order_count_for_inspected'];
                $list[$k]->district_count_for_accepted = $query_order_for_manager[$manager_id]['order_count_for_accepted'];
                $list[$k]->district_count_for_refused = $query_order_for_manager[$manager_id]['order_count_for_refused'];
                $list[$k]->district_count_for_repeated = $query_order_for_manager[$manager_id]['order_count_for_repeated'];

//                $list[$k]->district_count_for_delivered = $query_order_for_manager[$manager_id]['order_count_for_delivered'];
//                $list[$k]->district_count_for_delivered_completed = $query_order_for_manager[$manager_id]['order_count_for_delivered_completed'];
//                $list[$k]->district_count_for_delivered_inside = $query_order_for_manager[$manager_id]['order_count_for_delivered_inside'];
//                $list[$k]->district_count_for_delivered_tomorrow = $query_order_for_manager[$manager_id]['order_count_for_delivered_tomorrow'];
//                $list[$k]->district_count_for_delivered_repeated = $query_order_for_manager[$manager_id]['order_count_for_delivered_repeated'];
//                $list[$k]->district_count_for_delivered_rejected = $query_order_for_manager[$manager_id]['order_count_for_delivered_rejected'];
            }
            else
            {
                $list[$k]->district_count_for_all = 0;
                $list[$k]->district_count_for_inspected = 0;
                $list[$k]->district_count_for_accepted = 0;
                $list[$k]->district_count_for_refused = 0;
                $list[$k]->district_count_for_repeated = 0;

//                $list[$k]->district_count_for_delivered = 0;
//                $list[$k]->district_count_for_delivered_completed = 0;
//                $list[$k]->district_count_for_delivered_inside = 0;
//                $list[$k]->district_count_for_delivered_tomorrow = 0;
//                $list[$k]->district_count_for_delivered_repeated = 0;
//                $list[$k]->district_count_for_delivered_rejected = 0;
            }

            // 审核
            $v->district_count_for_accepted_inside = 0;
            // 有效单量
            $v->district_count_for_effective = $v->district_count_for_inspected - $v->district_count_for_refused - $v->district_count_for_repeated;
            // 有效率
            if($v->district_count_for_all > 0)
            {
                $v->district_rate_for_accepted = round(($v->district_count_for_accepted * 100 / $v->district_count_for_all),2);
            }
            else $v->district_rate_for_accepted = 0;


//            // 交付
//            // 有效交付量
//            $v->district_count_for_delivered_effective = $v->district_count_for_delivered_completed + $v->district_count_for_delivered_tomorrow + $v->district_count_for_delivered_inside;
//            // 有效交付率
//            if($v->district_count_for_delivered > 0)
//            {
//                $v->district_rate_for_delivered_effective = round(($v->district_count_for_delivered_effective * 100 / $v->district_count_for_delivered),2);
//            }
//            else $v->district_rate_for_delivered_effective = 0;
//
//            // 实际产量
//            $v->district_count_for_delivered_actual = $v->district_count_for_delivered_completed + $v->district_count_for_delivered_tomorrow;
//            // 实际产率
//            if($v->district_count_for_delivered > 0)
//            {
//                $v->district_rate_for_delivered_actual = round(($v->district_count_for_delivered_actual * 100 / $v->district_count_for_delivered),2);
//            }
//            else $v->district_rate_for_delivere_actual = 0;


            $v->district_merge = 0;
            $v->group_merge = 0;
        }
//        dd($list->toArray());

        $grouped_by_district = $list->groupBy('team_id');
        foreach ($grouped_by_district as $k => $v)
        {
            $v[0]->district_merge = count($v);

            $grouped_by_group = $list->groupBy('team_group_id');
            foreach ($grouped_by_group as $key => $val)
            {
                $val[0]->group_merge = count($val);
            }
        }
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【统计】质检看板
    public function view_statistic_inspector()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,71,61])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_inspector'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-inspector';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_inspector($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
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
    public function get_statistic_data_for_inspector_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;

        // 员工统计
        $query_order = DK_Common__Order::select('inspector_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count_for_inspected,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count_for_refused
                "))
            ->groupBy('inspector_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);

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

            $query_order->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('inspector_id')->toArray();




        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

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
    // 【统计】运营看板
    public function view_statistic_deliverer()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,61])) return view($this->view_blade_403);

        $view_data['menu_active_of_statistic_operation'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-deliverer';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_deliverer($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[61,66]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        // 运营经理
        if($me->user_type == 61)
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
                'order_list_for_deliverer as order_count_for_delivered'=>function($query) use($the_day) {
                    $query->where('inspected_status', '<>', 0)
                        ->where('delivered_date',$the_day);
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
    public function get_statistic_data_for_deliverer_by_group($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,41,61])) return view($this->view_blade_403);

        // 员工统计
        $query_order = DK_Common__Order::select('inspector_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '通过', TRUE, NULL)) as order_count_for_accepted,
                    count(IF(delivered_result = '拒绝', TRUE, NULL)) as order_count_for_refused
                "))
            ->groupBy('deliverer_id');


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'day')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_order->where('delivered_date',$the_day);

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

            $query_order->whereBetween('inspected_at',[$the_month_start_timestamp,$the_month_ended_timestamp]);
        }
        else
        {
        }


        $query_order = $query_order->get()->keyBy('inspector_id')->toArray();




        $query = DK_User::select(['id','mobile','user_status','user_type','username','true_name','team_id','team_group_id','superior_id'])
            ->with([
                'superior' => function($query) { $query->select(['id','username','true_name']); },
                'department_district_er' => function($query) { $query->select(['id','name','leader_id']); },
            ])
            ->where('user_status',1)
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[71,77]);

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




    // 【统计】员工-客服
    public function view_staff_statistic_customer_service($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id'])
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->find($post_data['staff_id']);
        $view_data['staff'] = $staff;

        $view_data['title_text'] = $staff->username;
        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-staff-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_staff_customer_service($post_data)
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

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');



        $query_this_month = DK_Common__Order::select('creator_id','published_at')
            ->where('creator_id',$staff_id)
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
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
                    
                "));
//            ->addSelect(DB::raw("
//                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
//                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
//                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
//                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
//                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
//                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
//
//                "));

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



            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



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



        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }









    // 【流量统计】返回-列表-视图
    public function view_statistic_list_for_all($post_data)
    {
        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-list-for-all';
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








    // 【统计】员工-客服
    public function view_staff_statistic_company($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff = DK_User::select(['id','user_status','user_type','username','true_name','team_id','team_group_id'])
            ->with([
                'department_district_er' => function($query) { $query->select(['id','name']); },
                'department_group_er' => function($query) { $query->select(['id','name']); }
            ])
            ->find($post_data['staff_id']);
        $view_data['staff'] = $staff;

        $view_data['title_text'] = $staff->username;
        $view_data['menu_active_of_statistic_department'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.statistic-staff-customer-service';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_staff_company($post_data)
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

        $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');



        $query_this_month = DK_Common__Order::select('creator_id','published_at')
            ->where('creator_id',$staff_id)
//            ->whereBetween('published_at',[$this_month_start_timestamp,$this_month_ended_timestamp])  // 当月
            ->whereBetween('published_at',[$the_month_start_timestamp,$the_month_ended_timestamp])
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
                    
                    count(IF(is_published = 1 AND delivered_status = 1, TRUE, NULL)) as order_count_for_delivered,
                    count(IF(delivered_result = '正常交付', TRUE, NULL)) as order_count_for_delivered_completed,
                    count(IF(delivered_result = '内部交付', TRUE, NULL)) as order_count_for_delivered_inside,
                    count(IF(delivered_result = '隔日交付', TRUE, NULL)) as order_count_for_delivered_tomorrow,
                    count(IF(delivered_result = '重复', TRUE, NULL)) as order_count_for_delivered_repeated,
                    count(IF(delivered_result = '驳回', TRUE, NULL)) as order_count_for_delivered_rejected
                    
                "));

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



            // 有效交付率
            if($v->order_count_for_delivered > 0)
            {
                $list[$k]->order_rate_for_delivered_effective = round(($v->order_count_for_delivered_effective * 100 / $v->order_count_for_delivered),2);
            }
            else $list[$k]->order_rate_for_delivered_effective = 0;



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



        // 有效交付率
        if($total_data['order_count_for_delivered'] > 0)
        {
            $total_data['order_rate_for_delivered_effective'] = round(($total_data['order_count_for_delivered_effective'] * 100 / $total_data['order_count_for_delivered']),2);
        }
        else $total_data['order_rate_for_delivered_effective'] = 0;

        $list[] = $total_data;

//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }



    // 【统计】交付看板
    public function view_statistic_company_overview($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return view($this->view_blade_403);

        $company_list = DK_Company::select('id','name')->get();
        $view_data['company_list'] = $company_list;

        $department_district_list = DK_Common__Team::select('id','name')->where('department_type',11)->orderby('rank','asc')->get();
        $view_data['department_district_list'] = $department_district_list;

        $view_data['menu_active_of_statistic_company_overview'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.marketing.company.statistic-company-overview';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_company_overview($post_data)
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
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query_delivery->whereDate('delivered_date',$the_day);

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
//                'department_district_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); },
//                'department_group_er' => function($query) { $query->select(['id','name','leader_id'])->with(['leader']); }
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

    // 【统计】交付看板
    public function view_statistic_company_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return view($this->view_blade_403);

        $company_list = DK_Company::select('id','name')->where('company_category',1)->get();
        $view_data['company_list'] = $company_list;

        $channel_list = DK_Company::select('id','name')->where('company_category',11)->get();
        $view_data['channel_list'] = $channel_list;

        $business_list = DK_Company::select('id','name')->where('company_category',21)->get();
        $view_data['business_list'] = $business_list;

        $view_data['menu_active_of_statistic_company_daily'] = 'active menu-open';
        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.statistic.marketing.company.statistic-company-daily';
        return view($view_blade)->with($view_data);
    }
    public function get_statistic_data_for_company_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Common__Delivery::select('company_id','channel_id','business_id','delivered_date')
            ->addSelect(DB::raw("
                    delivered_date as date_day,
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







    /*
     * Export 数据导出
     */
    // 【数据导出】
    public function view_statistic_export()
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);



        if($me->id > 10000)
        {
            $record["creator_id"] = $me->id;
            $record["record_category"] = 1; // record_category=1 browse/share
            $record["record_type"] = 1; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 default platform
            $record["page_module"] = 3; // page_module=2 other
            $record["page_num"] = 0;
            $record["open"] = "export";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);
        }


        $project_list = DK_Common__Project::select('id','name')->whereIn('item_type',[1,21])->get();
        $staff_list = DK_User::select('id','username')->where('user_category',11)->whereIn('user_type',[11,41,61,66,71,77,81,84,88])->get();
        $client_list = DK_Common__Client::select('id','username','true_name')->where('user_category',11)->get();

        $view_data['project_list'] = $project_list;
        $view_data['staff_list'] = $staff_list;
        $view_data['client_list'] = $client_list;


        $view_data['menu_active_of_statistic_export'] = 'active menu-open';

        $view_blade = env('DK_STAFF__TEMPLATE').'entrance.export.statistic-export';
        return view($view_blade)->with($view_data);
    }
    // 【数据导出】工单
    public function operate_statistic_export_for_order($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


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
            $record_last = DK_Record::select('*')
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


        $client_id = 0;
        $staff_id = 0;
        $project_id = 0;

        // 客户
        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0]))
            {
                $client_id = $post_data['client'];
            }
        }

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
                $project_er = DK_Common__Project::find($project_id);
                if($project_er)
                {
                    $project_title = '【'.$project_er->name.'】';
                    $record_data_title = $project_er->name;
                }
            }
        }

        // 审核结果
        $inspected_result = 0;
        if(!empty($post_data['inspected_result']))
        {
            if(!in_array($post_data['inspected_result'],['-1','0']))
            {
                $inspected_result = $post_data['inspected_result'];
            }
        }


        $the_month  = isset($post_data['month'])  ? $post_data['month']  : date('Y-m');
        $the_day  = isset($post_data['day'])  ? $post_data['day']  : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }


        if($export_type == "month")
        {
            $query->whereBetween('inspected_at',[$start_timestamp,$ended_timestamp]);
        }
        else if($export_type == "day")
        {
            $query->whereDate(DB::raw("DATE(FROM_UNIXTIME(inspected_at))"),$the_day);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('inspected_at',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '>=', $post_data['order_start']);
                $query->where('inspected_at', '>=', $the_start_timestamp);
            }
            if(!empty($post_data['order_ended']))
            {
//                $query->whereDate(DB::raw("FROM_UNIXTIME(inspected_at,'%Y-%m-%d')"), '<=', $post_data['order_ended']);
                $query->where('inspected_at', '<=', $the_ended_timestamp);
            }
        }


        if($client_id) $query->where('client_id',$client_id);
        if($staff_id) $query->where('creator_id',$staff_id);
        if($project_id) $query->where('project_id',$project_id);
        if($inspected_result) $query->where('inspected_result',$inspected_result);

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

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'client_er_name'=>'客户',
            'delivered_at'=>'交付时间',
            'creator_name'=>'创建人',
            'team'=>'团队',
            'published_time'=>'提交时间',
            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
                    'B'=>10,
                    'C'=>20,
                    'D'=>10,
                    'E'=>20,
                    'F'=>20,
                    'G'=>20,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>16,
                    'L'=>10,
                    'M'=>10,
                    'N'=>10,
                    'O'=>60,
                    'P'=>60,
                    'Q'=>10,
                    'R'=>10,
                    'S'=>10,
                    'T'=>20,
                    'U'=>20,
                    'V'=>20
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
        if(!in_array($me->user_type,[0,1,11,19,61,66,71,77])) return view($this->view_blade_403);


        if(in_array($me->user_type,[41,71,77,81,84,88]))
        {
            $team_id = $me->team_id;
        }
        else $team_id = 0;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name','true_name'); },
                'client_er'=>function($query) { $query->select('id','username','true_name'); },
                'inspector'=>function($query) { $query->select('id','name','true_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'department_district_er'=>function($query) { $query->select('id','name'); },
                'department_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->user_type,[77]))
//        {
//            $query->where('inspector_id',$me->id);
//        }



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['username'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['team'] = $v['department_district_er']['name'].' - '.$v['department_group_er']['name'];
            $cellData[$k]['published_time'] = date('Y-m-d H:i:s', $v['published_at']);

            $cellData[$k]['project_er_name'] = $v['project_er']['name'];
            if($me->team_id <= 0)
            {
                $cellData[$k]['project_er_alias_name'] = $v['project_er']['alias_name'];
            }
//            $cellData[$k]['channel_source'] = $v['channel_source'];


            if($v['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->user_type,[71,77]))
            {
                $time = time();
                // if(($v['inspected_at'] > 0) && (($time - $v['inspected_at']) > 86400))
                if(($v['inspected_at'] > 0) && (!isToday($v['inspected_at'])))
                {
                    $client_phone = $v['client_phone'];
                    $cellData[$k]['client_phone'] = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                }
            }


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['wx_id'];
            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['location_city'];
            $cellData[$k]['location_district'] = $v['location_district'];

            $cellData[$k]['teeth_count'] = $v['teeth_count'];

            $cellData[$k]['description'] = $v['description'];

            // 录音
//            if($v['recording_address_list'])
//            {
//                $recording_address_list_text = "";
//                $recording_address_list = json_decode($v['recording_address_list']);
//                if(count($recording_address_list) > 0)
//                {
//                    foreach($recording_address_list as $key => $recording)
//                    {
////                        $recording_address_list_text .= $recording."\r\n";
//                        $recording_address_list_text .= env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $key."\r\n";
//                    }
//                }
//                else
//                {
//                    if($v['call_record_id'] > 0)
//                    {
//                        $recording_address_list_text = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                    }
//                    else $recording_address_list_text = $v['recording_address'];
//                }
//                $cellData[$k]['recording_address'] = rtrim($recording_address_list_text);
//
//            }
//            else
//            {
//                if($v['call_record_id'] > 0)
//                {
//                    $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/voice_record?record_id=' . $v['call_record_id'];
//                }
//                else $cellData[$k]['recording_address'] = $v['recording_address'];
//            }
            if(!empty($v['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }


            // 是否重复
            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
            else $cellData[$k]['is_repeat'] = '--';

            // 审核
            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
                'project_er_alias_name'=>'医院真实名称',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        else
        {
            $title_row = [
                'id'=>'ID',
                'client_er_name'=>'客户',
                'delivered_at'=>'交付时间',
                'creator_name'=>'创建人',
                'team'=>'团队',
                'published_time'=>'提交时间',
                'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
                'client_type'=>'患者类型',
                'client_name'=>'客户姓名',
                'client_phone'=>'客户电话',
                'wx_id'=>'微信号',
                'is_wx'=>'是否+V',
                'location_city'=>'所在城市',
                'location_district'=>'行政区',
                'teeth_count'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 21;
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
                    'A'=>10, 'B'=>20, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20,
                    'H'=>20, 'I'=>20, 'J'=>20, 'K'=>20, 'L'=>20, 'M'=>20, 'N'=>20,
                    'O'=>20, 'P'=>20, 'Q'=>60, 'R'=>60, 'S'=>60, 'T'=>20,
                    'U'=>20, 'V'=>20, 'W'=>20, 'X'=>60, 'Y'=>60, 'Z'=>20
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }






}