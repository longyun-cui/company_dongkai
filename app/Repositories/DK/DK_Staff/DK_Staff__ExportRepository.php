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

use App\Models\DK\DK_Common\DK_Common__Record__by_Operation;

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



    // 【导出】工单-下载-IDs
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单-下载-IDs
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单-下载-IDs
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
    // 【导出】工单-下载-IDs
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


    // 【数据导出】工单
    public function operate_statistic_export_for_order_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_cagetory,[0,1,9,71])) return view($this->view_blade_403);


        if(in_array($me->staff_cagetory,[0,1,9,71]))
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
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'client_er'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
//            ->when($team_id, function ($query) use ($team_id) {
//                return $query->where('team_id', $team_id);
//            })
            ->whereIn('id',$ids_array);


        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];
            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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




    /*
     * 导出
     */
    // 【导出】工单
    public function o1__order_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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
            $project = (int)$post_data['project'];
            if(!in_array($project,[-1,0]))
            {
                $project_id = $project;
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


        $the_month = isset($post_data['month']) ? $post_data['month'] : date('Y-m');
        $the_date = isset($post_data['date']) ? $post_data['date'] : date('Y-m-d');


        // 工单
        $query = DK_Common__Order::select('*')
            ->with([
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });


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

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单
    public function o1__order_export__for__dental($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 1;

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
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });


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

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];

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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单
    public function o1__order_export__for__aesthetic($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 11;

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
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });


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

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->staff_category,[51]))
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
    // 【导出】工单
    public function o1__order_export__for__luxury($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 31;

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
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });


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

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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


            if($v['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";


            $cellData[$k]['client_name'] = $v['client_name'];
            $cellData[$k]['client_phone'] = $v['client_phone'];
            if(in_array($me->staff_category,[51]))
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




    // 【导出】工单-下载-IDs
    public function o1__order_export__by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
                'client_er'=>function($query) { $query->select('id','name'); },
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


        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单-下载-IDs
    public function o1__order_export__by_ids__for__dental($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
                'client_er'=>function($query) { $query->select('id','name'); },
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


        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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
    // 【导出】工单-下载-IDs
    public function o1__order_export__by_ids__for__aesthetic($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
                'client_er'=>function($query) { $query->select('id','name'); },
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


        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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
    // 【导出】工单-下载-IDs
    public function o1__order_export__by_ids__for__luxury($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
                'client_er'=>function($query) { $query->select('id','name'); },
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
            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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




    // 【导出】交付
    public function o1__delivery_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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
                    $client_title = '【'.$client_er->name.'】';
                    $record_data_title = $client_er->name;
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
            ->join('dk_common__delivery', 'dk_common__order.id', '=', 'dk_common__delivery.order_id')
            ->where('dk_common__order.order_category',1)
            ->with([
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ]);



        if($export_type == "month")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_common__delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_common__delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_common__delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_common__delivery.client_id',$client_id);
        if($project_id) $query->where('dk_common__delivery.project_id',$project_id);


        $data = $query->orderBy('dk_common__delivery.id','desc')->get();
        $data = $data->toArray();

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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


        $record = new DK_Common__Record__by_Operation;

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
    // 【导出】交付
    public function o1__delivery_export__for__dental($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 11;

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
                    $client_title = '【'.$client_er->name.'】';
                    $record_data_title = $client_er->name;
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
            ->join('dk_common__delivery', 'dk_common__order.id', '=', 'dk_common__delivery.order_id')
            ->where('dk_common__order.order_category',1)
            ->with([
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ]);



        if($export_type == "month")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_common__delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_common__delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_common__delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_common__delivery.client_id',$client_id);
        if($project_id) $query->where('dk_common__delivery.project_id',$project_id);


        $data = $query->orderBy('dk_common__delivery.id','desc')->get();
        $data = $data->toArray();

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];

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
            if(in_array($me->staff_category,[61]))
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['field_1']);

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


        $record = new DK_Common__Record__by_Operation;

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
    // 【导出】交付
    public function o1__delivery_export__for__aesthetic($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 11;


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
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });


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

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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
    // 【导出】交付
    public function o1__delivery_export__for__luxury($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        if(in_array($me->staff_category,[41,51]))
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
            $record_last = DK_Common__Record__by_Operation::select('*')
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


        $order_category = isset($post_data['order_category']) ? $post_data['order_category'] : 31;


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
                    $client_title = '【'.$client_er->name.'】';
                    $record_data_title = $client_er->name;
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
            ->join('dk_common__delivery', 'dk_common__order.id', '=', 'dk_common__delivery.order_id')
            ->where('dk_common__order.order_category',31)
            ->with([
                'client_er'=>function($query) { $query->select('id','name'); },
                'creator'=>function($query) { $query->select('id','name'); },
                'inspector'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'creator_team_er'=>function($query) { $query->select('id','name'); },
                'creator_team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->where('order_category',$order_category)
            ->when($team_id, function ($query) use ($team_id) {
                return $query->where('team_id', $team_id);
            });



        if($export_type == "month")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($export_type == "date")
        {
            $query->whereDate('dk_common__delivery.delivered_date',$the_date);
        }
        else if($export_type == "latest")
        {
            $query->whereBetween('dk_common__delivery.delivered_date',[$start_timestamp,$time]);
        }
        else
        {
            if(!empty($post_data['order_start']))
            {
                $query->where('dk_common__delivery.delivered_date', '>=', $the_start);
            }
            if(!empty($post_data['order_ended']))
            {
                $query->where('dk_common__delivery.delivered_date', '<=', $the_ended);
            }
        }


        if($client_id) $query->where('dk_common__delivery.client_id',$client_id);
        if($project_id) $query->where('dk_common__delivery.project_id',$project_id);


        $data = $query->orderBy('dk_common__delivery.id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

            $cellData[$k]['client_er_name'] = $v['client_er']['name'];
            if($v['delivered_at']) $cellData[$k]['delivered_at'] = date('Y-m-d H:i:s', $v['delivered_at']);
            else $cellData[$k]['delivered_at'] = '';

            $cellData[$k]['creator_name'] = $v['creator']['name'];

            $cellData[$k]['team'] = $v['creator_team_er']['name'].' - '.$v['creator_team_group_er']['name'];
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
            if(in_array($me->staff_category,[51]))
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




    // 【导出】去重数据-下载
    public function o1__duplicate_export($post_data)
    {
//        dd($post_data);
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,9,71])) return view($this->view_blade_403);

        $time = time();
        $date = date('Y-m-d');

        $record_operate_type = 1;
        $record_column_type = null;
        $record_before = '';
        $record_after = '';

        $when_data = [];
        $time_type = isset($post_data['time_type']) ? $post_data['time_type']  : '';


        $record_operate_type = 101;
        $record_column_type = 'all';
        $record_before = '全部';
        $record_after = '全部';

        $time_title = '【全部】';


        $client_id = 0;
        $project_id = 0;

        // 项目
        $project_title = '';
        $record_data_title = '';
        if(!empty($post_data['project']))
        {
            $project_id_int = (int)$post_data['project'];
            if(!in_array($project_id_int,[-1,0]))
            {
                $project_er = DK_Common__Project::find($project_id_int);
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
            $client_id_int = (int)$post_data['client'];
            if(!in_array($client_id_int,[-1,0]))
            {
                $client_er = DK_Common__Project::find($client_id_int);
                if($client_er)
                {
                    $client_title = '【'.$client_er->name.'】';
                    $record_data_title = $client_er->name.' ';
                }
            }
        }


        $record = new DK_Common__Record__by_Operation;

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
        if(!empty($project_id_int))
        {
            $record_data["item_id"] = $project_id_int;
            $record_data["title"] = $record_data_title;
        }
        if(!empty($client_id_int))
        {
            $record_data["item_id"] = $client_id_int;
            $record_data["title"] = $record_data_title;
        }

        $record->fill($record_data)->save();

        $title = '【去重】'.date('Ymd.His').$time_title.$project_title.$client_title;


        // 工单
        $query_order = DK_Common__Order::select('client_phone')->where('delivered_status',1);
        $query_delivery = DK_Common__Delivery::select('client_phone');

        if(!empty($project_id_int))
        {
            $query_order->where('delivered_project_id',$project_id_int);
            $query_delivery->where('project_id',$project_id_int);
        }
        if(!empty($client_id_int))
        {
            $query_order->where('delivered_client_id',$client_id_int);
            $query_delivery->where('client_id',$client_id_int);
        }

        $data_order = $query_order->orderBy('id','desc')->get();
        $data_delivery = $query_delivery->orderBy('id','desc')->get();

        $data = $data_order->concat($data_delivery)->unique('client_phone');

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


}