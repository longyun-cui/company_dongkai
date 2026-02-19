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

use App\Models\DK\DK_Common\DK_Common__Record__by_Operation;


use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Statistic;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__ExportRepository {

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



    // 【数据-导出】工单-下载-IDs
    public function o1__export__order__export__by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);


        if(in_array($me->staff_category,[41]))
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


//        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 1;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'delivered_client_er'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
//            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->staff_category,[77]))
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

            $cellData[$k]['delivered_client_er_name'] = $v['delivered_client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[71,77]))
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

            $cellData[$k]['field_1'] = config('dk.common-config.teeth_count.'.$v['field_1']);;

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
            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'delivered_client_er_name'=>'客户',
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
                'field_1'=>'牙齿数量',
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
                'delivered_client_er_name'=>'客户',
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
                'field_1'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Common__Record__by_Operation;

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
    public function o1__export__order_dental__export__by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);


        if(in_array($me->staff_category,[41]))
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 1;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'delivered_client_er'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->staff_category,[77]))
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

            $cellData[$k]['delivered_client_er_name'] = $v['delivered_client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[71,77]))
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

            $cellData[$k]['field_1'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        if($me->team_id <= 0)
        {
            $title_row = [
                'id'=>'ID',
                'delivered_client_er_name'=>'客户',
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
                'field_1'=>'牙齿数量',
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
                'delivered_client_er_name'=>'客户',
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
                'field_1'=>'牙齿数量',
                'description'=>'通话小结',
                'recording_address'=>'录音地址',
                'is_repeat'=>'是否重复',
                'inspector_name'=>'审核人',
                'inspected_time'=>'审核时间',
                'inspected_result'=>'审核结果',
            ];
        }
        array_unshift($cellData, $title_row);


        $record = new DK_Common__Record__by_Operation;

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
    public function o1__export__order_aesthetic__export__by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);


        if(in_array($me->staff_category,[41]))
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 11;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'delivered_client_er'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->staff_category,[77]))
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

            $cellData[$k]['delivered_client_er_name'] = $v['delivered_client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';


            $cellData[$k]['creator_name'] = $v['creator']['name'];
            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[71,77]))
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
            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'delivered_client_er_name'=>'客户',
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
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Common__Record__by_Operation;

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
    public function o1__export__order_luxury__export__by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);


        if(in_array($me->staff_category,[41]))
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 31;

        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'delivered_client_er'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            })
            ->whereIn('id',$ids_array);

//        if(in_array($me->staff_category,[77]))
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
            $cellData[$k]['delivered_client_er_name'] = $v['delivered_client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            // ID
            $cellData[$k]['creator_name'] = $v['creator']['name'];

            // ID
            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[71,77]))
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
            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
            'delivered_client_er_name'=>'客户',
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
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
            'is_repeat'=>'是否重复',
            'inspector_name'=>'审核人',
            'inspected_time'=>'审核时间',
            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Common__Record__by_Operation;

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



}