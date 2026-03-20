<?php
namespace App\Repositories\DK\DK_Client;

use App\Models\DK\DK_Client\DK_Client__Staff;

use App\Models\DK_Client\DK_Client_Department;
use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Contact;

use App\Models\DK_Client\DK_Client_Follow_Record;
use App\Models\DK_Client\DK_Client_Trade_Record;


use App\Models\DK_Client\DK_Client_Project;
use App\Models\DK_Client\DK_Client_Record;
use App\Models\DK_Client\DK_Client_Finance_Daily;

use App\Models\DK\DK_Pivot_User_Project;

use App\Models\DK\DK_Order;
use App\Models\DK\DK_Client;
use App\Models\DK\DK_District;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Record_Current;


use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Delivery;


use App\Jobs\DK_Client\AutomaticDispatchingJob;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DK_Client__ExportRepository {

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


    // 【数据导出】工单
    public function o1__export__delivery_dental__export__by_ids($post_data)
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
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','name']); },
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

//            $cellData[$k]['creator_name'] = $v['creator']['name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            $cellData[$k]['order_quality'] = $v['order_quality'];

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['name'];
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

            $cellData[$k]['teeth_count'] = config('dk.common-config.teeth_count.'.$v['order_er']['field_1']);

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
//            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
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
    public function o1__export__delivery_aesthetic__export__by_ids($post_data)
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
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','name']); },
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

//            $cellData[$k]['creator_name'] = $v['creator']['name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['name'];
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
//            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
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
    public function o1__export__delivery_luxury__export__by_ids($post_data)
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
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','name']); },
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

//            $cellData[$k]['creator_name'] = $v['creator']['name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['name'];
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
//            $cellData[$k]['inspector_name'] = $v['inspector']['name'];
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


}