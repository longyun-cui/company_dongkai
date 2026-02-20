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


use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Statistic;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__OrderRepository {

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




    // 【工单】返回-列表-数据
    public function o1__order__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Order::select('dk_common__order.*');


        // 客服部
        if($me->staff_category == 41)
        {
            if($me->staff_position == 31)
            {
                // 部门总监
                $query->where('creator_department_id',$me->department_id);
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
                $query->where('creator_team_id',$me->team_id);
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
                $query->where('creator_team_id',$me->team_id);
                $query->where('creator_team_group_id',$me->team_group_id);
                $query->where('creator_team_group_id','>',0);

//                $query->where(function ($query) use($me) {
//                    $query->where('creator_id', $me->id)
//                        ->orWhere(function ($query) use($me) {
//                            $query->where('creator_team_id',$me->team_id)
//                                ->where('creator_team_group_id',$me->team_group_id)
//                                ->where('creator_team_group_id','>',0);
//                        });
//                });
            }
            else if($me->staff_position == 99)
            {
                // 职员
                $query->where('creator_id', $me->id);
            }
        }

        // 质检部
        if($me->staff_category == 51)
        {
            $query->where('dk_common__order.is_published','<>',0);

            if($me->staff_position == 31)
            {
                // 部门总监
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
                // 一对一
//                $subordinates = DK_Common__Staff::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
//                $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($subordinates) {
//                    $query->whereIn('user_id', $subordinates);
//                });
                // 多对对
                $subordinates = DK_Common__Staff::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();
                $project_list = DK_Pivot__Staff_Project::select('project_id')->whereIn('user_id',$subordinates)->get()->pluck('project_id')->toArray();
                $query->where('is_published','<>',0)->whereIn('dk_common__order.project_id', $project_list);
                if($me->team_id != 0)
                {
                    $query->where('dk_common__order.team_id',$me->team_id);
                }
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
            }
            else if($me->staff_position == 99)
            {
                // 职员
                // 一对一
//                $query->where('is_published','<>',0)->whereHas('project_er', function ($query) use ($me) {
//                    $query->where('user_id', $me->id);
//                });
                // 多对多
                $project_list = DK_Pivot__Staff_Project::select('project_id')->where('user_id',$me->id)->get()->pluck('project_id')->toArray();
                $query->where('dk_common__order.is_published','<>',0)->whereIn('dk_common__order.project_id', $project_list);
                if($me->team_id != 0)
                {
                    $query->where('dk_common__order.team_id',$me->team_id);
                }
            }
        }

        // 复核部
        if($me->staff_category == 61)
        {
            $query->where('dk_common__order.is_published','<>',0);
            $query->where('dk_common__order.appealed_status','>',0);

            if($me->staff_position == 31)
            {
                // 部门总监
            }
            else if($me->staff_position == 41)
            {
                // 团队经理
                $query->where('dk_common__order.is_published','<>',0);
            }
            else if($me->staff_position == 61)
            {
                // 小组主管
            }
            else if($me->staff_position == 99)
            {
                // 职员
                $query->where('dk_common__order.is_published','<>',0);
            }
        }

        // 运营部
        if($me->staff_category == 71)
        {
            $query->where('dk_common__order.is_published','<>',0);

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



        if(!empty($post_data['id'])) $query->where('dk_common__order.id', $post_data['id']);
        if(!empty($post_data['remark'])) $query->where('dk_common__order.remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('dk_common__order.description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('dk_common__order.content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['name'])) $query->where('dk_common__order.name', 'like', "%{$post_data['name']}%");

        if(!empty($post_data['client_name'])) $query->where('dk_common__order.client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('dk_common__order.client_phone', $post_data['client_phone']);
//        if(!empty($post_data['client_phone'])) $query->where('client_phone', 'like', "%{$post_data['client_phone']}");

        // 发布日期
        if(!empty($post_data['assign'])) $query->where('dk_common__order.published_date', $post_data['assign']);
//        if(!empty($post_data['assign_start'])) $query->where('published_date', '>=', $post_data['assign_start']);
//        if(!empty($post_data['assign_ended'])) $query->where('published_date', '<=', $post_data['assign_ended']);
        if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
        {
            $query->whereDate('dk_common__order.published_date', '>=', $post_data['assign_start']);
            $query->whereDate('dk_common__order.published_date', '<=', $post_data['assign_ended']);
        }
        else if(!empty($post_data['assign_start']))
        {
            $query->where('dk_common__order.published_date', $post_data['assign_start']);
        }
        else if(!empty($post_data['assign_ended']))
        {
            $query->where('dk_common__order.published_date', $post_data['assign_ended']);
        }


        // 交付日期
        if(!empty($post_data['delivered_date'])) $query->where('dk_common__order.delivered_date', $post_data['delivered_date']);



        // 工单种类 []
        if(isset($post_data['order_category']))
        {
            $order_category = intval($post_data['order_category']);
            if(!in_array($order_category,[-1]))
            {
                $query->where('dk_common__order.order_category', $order_category);
            }
        }

        // 工单类型 []
        if(isset($post_data['item_type']))
        {
            $order_type = intval($post_data['order_type']);
            if(!in_array($order_type,[-1]))
            {
                $query->where('dk_common__order.item_type', $order_type);
            }
        }


        // 创建方式 [人工|导入|api]
        if(isset($post_data['created_type']))
        {
            $created_type = intval($post_data['created_type']);
            if(!in_array($created_type,[-1]))
            {
                $query->where('dk_common__order.created_type', $created_type);
            }
        }

        // 地区
//        if(!empty($post_data['district_city'])) $query->where('location_city', $post_data['district_city']);
//        if(!empty($post_data['district_district'])) $query->where('location_district', $post_data['district_district']);
        if(!empty($post_data['district_city']))
        {
            if(!in_array($post_data['location_city'],[-1]))
            {
                $query->where('dk_common__order.location_city', $post_data['location_city']);
            }
        }
        if(!empty($post_data['district_district']))
        {
            if(!in_array($post_data['location_district'],[-1]))
            {
//                $query->where('dk_common__order.location_district', $post_data['location_district']);
                $query->whereIn('dk_common__order.location_district', $post_data['location_district']);
            }
        }


        // 团队-单选
//        if(!empty($post_data['team']))
//        {
//            $team = intval($post_data['team']);
//            if(!in_array($team,[-1,0]))
//            {
//                $query->where('dk_common__order.creator_team_id', $team);
//            }
//        }
        // 团队-多选
        if(!empty($post_data['team']) && count($post_data['team']) > 0)
        {
            $query->whereIn('dk_common__order.creator_team_id', $post_data['team']);
        }


        // 员工
        if(!empty($post_data['staff']))
        {
            $staff = intval($post_data['staff']);
            if(!in_array($staff,[-1,0]))
            {
                $query->where('dk_common__order.creator_id', $staff);
            }
        }


        // 客户
        if(isset($post_data['client']))
        {
            $client = intval($post_data['client']);
            if(!in_array($client,[-1,0]))
            {
                $query->where('dk_common__order.client_id', $client);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            $projectId = intval($post_data['project']);
            if(!in_array($projectId,[-1,0]))
            {
                if(isset($post_data['distribute_type']) && $post_data['distribute_type'] == 1)
                {
                    $project = DK_Common__Project::find($post_data['project']);
                    $project_ids = DK_Common__Project::select('id')
                        ->where('item_status',1)
                        ->where('location_city',$project->location_city)
                        ->pluck('id');

                    $query
                        ->leftJoin('dk_pivot_client_delivery as d', function($join) use ($post_data,$project_ids) {
                            $join->on('d.client_phone', '=', 'dk_common__order.client_phone')
                                ->where('d.project_id', '=', $post_data['project']);
                        })
                        ->leftJoin('dk_common__order as o2', function($join) use ($projectId) {
                            $join->on('o2.client_phone', '=', 'dk_common__order.client_phone')
                                ->where('o2.project_id', '=', $projectId);
                        })
                        ->whereIn('dk_common__order.project_id', $project_ids)
                        ->where('dk_common__order.project_id', '!=', $post_data['project'])
                        ->whereNull('d.client_phone')
                        ->whereNull('o2.id')
                        ->where('dk_common__order.inspected_result','通过');
                }
                else
                {
                    $query->where('dk_common__order.project_id', $projectId);
                }
            }
        }

        // 客户类型
        if(isset($post_data['client_type']))
        {
            $client_type = intval($post_data['client_type']);
            if(!in_array($post_data['client_type'],[-1,0]))
            {
                $query->where('dk_common__order.client_type', $client_type);
            }
        }


        // 是否+V
        if(!empty($post_data['is_wx']))
        {
            $is_wx = intval($post_data['is_wx']);
            if(!in_array($is_wx,[-1]))
            {
                $query->where('dk_common__order.is_wx', $is_wx);
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
                    $query->where('dk_common__order.is_published', 0);
                }
                else if($inspected_status == '待审核')
                {
                    $query->where('dk_common__order.is_published', 1)->whereIn('inspected_status', [0,9]);
                }
                else if($inspected_status == '已审核')
                {
                    $query->where('dk_common__order.inspected_status', 1);
                }
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
            if($me->creator_team_id > 0)
            {
                if(in_array('拒绝',$post_data['inspected_result']))
                {
                    $post_data['inspected_result'][] = '不合格';
                }
            }
            if(count($post_data['inspected_result']))
            {
                $query->whereIn('dk_common__order.inspected_result', $post_data['inspected_result']);
            }
        }


        // 申诉状态
        if(!empty($post_data['appealed_status']))
        {
            $appealed_status = $post_data['appealed_status'];
            if(in_array($appealed_status,config('info.appealed_status')))
            {
                if($appealed_status == '已申诉')
                {
                    $query->whereIn('dk_common__order.appealed_status', [1,9]);
                }
                else if($appealed_status == '申诉中')
                {
                    $query->where('dk_common__order.appealed_status', 1);
                }
                else if($appealed_status == '申诉结束')
                {
                    $query->where('dk_common__order.appealed_status', 9);
                }
            }
        }


        // 交付状态
        if(!empty($post_data['delivered_status']))
        {
            $delivered_status = $post_data['delivered_status'];
            if(in_array($delivered_status,['待交付','正常交付','已操作','已处理']))
            {
                if($delivered_status == '待交付')
                {
                    $query->where('dk_common__order.delivered_status', 0);
                }
                else if($delivered_status == '正常交付')
                {
                    $query->where('dk_common__order.delivered_status', 1);
                }
                else if($delivered_status == '已操作')
                {
                    $query->where('dk_common__order.delivered_status', 1);
                }
                else if($delivered_status == '已处理')
                {
                    $query->where('dk_common__order.delivered_status', 1);
                }
            }
        }
        // 交付结果
        if(!empty($post_data['delivered_result']))
        {
            if(count($post_data['delivered_result']))
            {
                $query->whereIn('dk_common__order.delivered_result', $post_data['delivered_result']);
            }
        }


        // 录音质量 []
        if(isset($post_data['recording_quality']))
        {
            $recording_quality = intval($post_data['recording_quality']);
            if(!in_array($recording_quality,[-1]))
            {
                $query->where('dk_common__order.recording_quality', $recording_quality);
            }
        }


        $total = $query->count();
//        dd($total);

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;
        if($limit > 200) $limit = 200;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy('dk_common__order.id', "desc");

        if($limit == -1) $list = $query->skip($skip)->take(200)->get();
        else $list = $query->skip($skip)->take($limit)->get();

        $list->load([
            'creator'=>function($query) { $query->select('id','name'); },
//            'owner'=>function($query) { $query->select('id','name'); },
            'inspector'=>function($query) { $query->select('id','name'); },
            'deliverer'=>function($query) { $query->select('id','name'); },
            'client_er'=>function($query) { $query->select('id','name'); },
            'project_er'=>function($query) { $query->select('id','name','alias_name','is_distributive'); },
            'delivered_project_er'=>function($query) { $query->select('id','name','alias_name'); },
            'delivered_client_er'=>function($query) { $query->select('id','name'); },
            'creator_team_er'=>function($query) { $query->select('id','name'); },
            'creator_team_group_er'=>function($query) { $query->select('id','name'); },
        ]);

//        $list = DK_Common__Order::whereIn('id', $query)
//            ->with([
//                'creator'=>function($query) { $query->select('id','name'); },
//                'owner'=>function($query) { $query->select('id','name'); },
//                'client_er'=>function($query) { $query->select('id','name'); },
//                'inspector'=>function($query) { $query->select('id','name'); },
//                'deliverer'=>function($query) { $query->select('id','name'); },
//                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
//                'team_er',
//                'team_group_er',
//                'department_manager_er',
//                'department_supervisor_er'
//            ])
//            ->orderBy("id", "desc")
//            ->skip($skip)
//            ->take($limit)
//            ->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

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

            if(in_array($me->user_type,[0,1,9,11]))
            {
            }
            else if(in_array($me->user_type,[71,77]))
            {
                if($v->item_category == 1)
                {
                    $time = time();
                    if(($v->published_at > 0) && (($time - $v->published_at) > 86400))
                    {
                        $client_phone = $v->client_phone;
                        $v->client_phone = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                    }
                }
            }
            else if(in_array($me->user_type,[41,81,84,88]))
            {
                $time = time();
                if(!$v->is_me || (($v->published_at > 0) && (($time - $v->published_at) > 86400)))
                {
//                    $len = strlen($client_phone);  // 字符串长度
                    $client_phone = $v->client_phone;
                    if(is_numeric($client_phone))
                    {
                        $v->client_phone = substr($client_phone, 0, 3).'****'.substr($client_phone, -4);
                    }
                }
            }

        }
//        dd($list->toArray());


//        if($me->id > 10000)
//        {
//            $record["creator_id"] = $me->id;
//            $record["record_category"] = 1; // record_category=1 browse/share
//            $record["record_type"] = 1; // record_type=1 browse
//            $record["page_type"] = 1; // page_type=1 default platform
//            $record["page_module"] = 2; // page_module=2 other
//            $record["page_num"] = ($skip / $limit) + 1;
//            $record["open"] = "order-list";
//            $record["from"] = request('from',NULL);
//            $this->record_for_user_visit($record);
//        }


        return datatable_response($list, $draw, $total);
    }
    // 【工单】获取 GET
    public function o1__order__item_get($post_data)
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

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()
            ->with([
                'owner'=>function($query) { $query->select('id','name'); },
                'client_er'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
            ])
            ->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【工单】保存 SAVE
    public function o1__order__item_save($post_data)
    {

        $fields = [
            'operate' => 'required',
            'work_shift' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required|numeric',
            'client_intention' => 'required',
//            'field_1' => 'required',
//            'location_city' => 'required',
//            'location_district' => 'required',
            'description' => 'required',
        ];
        $messages = [
            'operate.required' => 'operate.required.',
            'work_shift.required' => '请选择班次！',
            'project_id.required' => '请选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户电话！',
            'client_phone.numeric' => '客户电话格式有误！',
//            'client_type.required' => '请选择患者类型！',
            'client_intention.required' => '请选择客户意向！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
//        dd($post_data);
        $orderCategory = $post_data['order_category'];

        if ($orderCategory == 1)
        {
            $fields['client_type'] = 'required';
            $fields['field_1'] = 'required';
            $messages['client_type.required'] = '请选择患者类型！';
            $messages['field_1.required'] = '请选择牙齿数量！';
        }
        else if ($orderCategory == 11)
        {
            $fields['field_1'] = 'required';
            $messages['field_1.required'] = '请选择类型！';
        }
        else if ($orderCategory == 31)
        {
            $fields['field_1'] = 'required';
            $messages['field_1.required'] = '请选择品类！';
        }

        $v = Validator::make($post_data, $fields, $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];

        if(!empty($location_city) && !empty($location_district))
        {
        }
        else
        {
            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
            }
            else return response_error([],"请选择城市和区域！");
        }
//        dd($custom_location_city.$custom_location_district);


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
//        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
//
//        $me->load(['team_er','team_group_er']);


        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Common__Order;
//            $post_data["order_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["creator_company_id"] = $me->company_id;
            $post_data["creator_department_id"] = $me->department_id;
            $post_data["creator_team_id"] = $me->team_id;
            $post_data["creator_team_sub_id"] = $me->team_sub_id;
            $post_data["creator_team_group_id"] = $me->team_group_id;
            $post_data["creator_team_unit_id"] = $me->team_unit_id;

        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Common__Order::find($operate_id);
            if(!$mine) return response_error([],"该【工单】不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Common__Project::find($post_data['project_id']);
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
            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

//            $mine_data['team_id'] = $me->team_id;
//            $mine_data['team_group_id'] = $me->team_group_id;
//            if($me->team_er) $mine_data['department_manager_id'] = $me->team_er->leader_id;
//            if($me->team_group_er) $mine_data['department_supervisor_id'] = $me->team_group_er->leader_id;
//
//            if(!empty($custom_location_city) && !empty($custom_location_district))
//            {
//                $mine_data['location_city'] = $custom_location_city;
//                $mine_data['location_district'] = $custom_location_district;
//            }


            $mine_data['client_phone'] = ltrim($mine_data['client_phone'], '0');
            $mine_data['description'] = trim($mine_data['description']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
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
    // 【工单】【口腔】保存 SAVE
    public function o1__order_dental__item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户电话！',
            'client_phone.numeric' => '客户电话格式有误！',
            'client_type.required' => '请患者类型！',
            'client_intention.required' => '请选择客户意向！',
            'field_1.required' => '请选择牙齿数量！',
            'field_2.required' => '请选择班次！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required|numeric',
            'client_type' => 'required',
            'client_intention' => 'required',
            'teeth_count' => 'required',
            'field_2' => 'required',
//            'location_city' => 'required',
//            'location_district' => 'required',
            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

//        $me->load(['team_er','team_group_er']);


        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Common__Order;
            $post_data["order_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

            $post_data["creator_company_id"] = $me->company_id;
            $post_data["creator_department_id"] = $me->department_id;
            $post_data["creator_team_id"] = $me->team_id;
//            $post_data["creator_sub_team_id"] = $me->sub_team_id;
            $post_data["creator_team_group_id"] = $me->team_group_id;

//            $mine_data['team_id'] = $me->team_id;
//            $mine_data['creator_team_group_id'] = $me->team_group_id;
//            if($me->team_er) $mine_data['department_manager_id'] = $me->team_er->leader_id;
//            if($me->team_group_er) $mine_data['department_supervisor_id'] = $me->team_group_er->leader_id;

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Common__Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Common__Project::find($post_data['project_id']);
            if(!$project) return response_error([],"选择【项目】不存在，刷新页面重试！");
        }



        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $mine_data['client_phone'] = ltrim($mine_data['client_phone'], '0');

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
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
    // 【工单】【医美】保存 SAVE
    public function o1__order_aesthetic__item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户电话！',
            'client_phone.numeric' => '客户电话格式有误！',
//            'client_type.required' => '请患者类型！',
//            'client_intention.required' => '请选择客户意向！',
            'field_1.required' => '请选择品类！',
            'field_2.required' => '请选择班次！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required|numeric',
//            'client_type' => 'required',
//            'client_intention' => 'required',
            'field_1' => 'required',
            'field_2' => 'required',
//            'location_city' => 'required',
//            'location_district' => 'required',
            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];

        if(!empty($location_city) && !empty($location_district))
        {
        }
        else
        {
            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
            }
            else return response_error([],"请选择城市和区域！");
        }
//        dd($custom_location_city.$custom_location_district);


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

//        $me->load(['team_er','team_group_er']);


        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Common__Order;
            $post_data["order_category"] = 11;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

            $post_data["creator_company_id"] = $me->company_id;
            $post_data["creator_department_id"] = $me->department_id;
            $post_data["creator_team_id"] = $me->team_id;
//            $post_data["creator_sub_team_id"] = $me->sub_team_id;
            $post_data["creator_team_group_id"] = $me->team_group_id;

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Common__Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Common__Project::find($post_data['project_id']);
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
            $mine_data['creator_team_id'] = $me->team_id;
            $mine_data['creator_team_group_id'] = $me->team_group_id;
            if($me->team_er) $mine_data['department_manager_id'] = $me->team_er->leader_id;
            if($me->team_group_er) $mine_data['department_supervisor_id'] = $me->team_group_er->leader_id;

            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
                $mine_data['location_city'] = $custom_location_city;
                $mine_data['location_district'] = $custom_location_district;
            }

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $mine_data['client_phone'] = ltrim($mine_data['client_phone'], '0');

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
    // 【工单】【二奢】保存 SAVE
    public function o1__order_luxury__item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'project_id.required' => '请选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请选择项目！',
            'client_name.required' => '请填写客户信息！',
            'client_phone.required' => '请填写客户电话！',
            'client_phone.numeric' => '客户电话格式有误！',
//            'client_type.required' => '请患者类型！',
//            'client_intention.required' => '请选择客户意向！',
            'field_1.required' => '请选择品类！',
            'field_2.required' => '请选择班次！',
//            'location_city.required' => '请选择城市！',
//            'location_district.required' => '请选择行政区！',
            'description.required' => '请输入通话小结！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:1',
            'client_name' => 'required',
            'client_phone' => 'required|numeric',
//            'client_type' => 'required',
//            'client_intention' => 'required',
            'field_1' => 'required',
            'field_2' => 'required',
//            'location_city' => 'required',
//            'location_district' => 'required',
            'description' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];

        if(!empty($location_city) && !empty($location_district))
        {
        }
        else
        {
            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
            }
            else return response_error([],"请选择城市和区域！");
        }
//        dd($custom_location_city.$custom_location_district);


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        $me->load(['team_er','team_group_er']);


        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new DK_Common__Order;
            $post_data["order_category"] = 31;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;

            $post_data["creator_company_id"] = $me->company_id;
            $post_data["creator_department_id"] = $me->department_id;
            $post_data["creator_team_id"] = $me->team_id;
//            $post_data["creator_sub_team_id"] = $me->sub_team_id;
            $post_data["creator_team_group_id"] = $me->team_group_id;

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->count("*");
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Common__Order::find($operate_id);
            if(!$mine) return response_error([],"该工单不存在，刷新页面重试！");

            if(in_array($me->user_type,[84,88]) && $mine->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");

//            $is_repeat = DK_Common__Order::where('client_phone',$post_data['client_phone'])->where('project_id',$post_data['project_id'])->where('id','<>',$operate_id)->count("*");
        }
        else return response_error([],"参数有误！");

//        $post_data['is_repeat'] = $is_repeat;

        if(!empty($post_data['project_id']))
        {
            $project = DK_Common__Project::find($post_data['project_id']);
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
            $mine_data['creator_team_id'] = $me->team_id;
            $mine_data['creator_team_group_id'] = $me->team_group_id;
            if($me->team_er) $mine_data['department_manager_id'] = $me->team_er->leader_id;
            if($me->team_group_er) $mine_data['department_supervisor_id'] = $me->team_group_er->leader_id;

            if(!empty($custom_location_city) && !empty($custom_location_district))
            {
                $mine_data['location_city'] = $custom_location_city;
                $mine_data['location_district'] = $custom_location_district;
            }

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $mine_data['client_phone'] = ltrim($mine_data['client_phone'], '0');

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


    public function o1__order__import__by_txt($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'project_id.required' => '请选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请选择项目！',
            'client_id.required' => '请选择客户！',
            'client_id.numeric' => '选择客户参数有误！',
            'client_id.min' => '请选择客户！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_id' => 'required|numeric|min:0',
            'client_id' => 'required|numeric|min:0',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $time = time();
        $date = date('Y-m-d');

        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9])) return response_error([],"你没有操作权限！");

        $project_id = $post_data['project_id'];
        $client_id = $post_data['client_id'];
        if($project_id > 0 || $client_id > 0)
        {
        }
        else return response_error([],"项目和客户必须选择一个！");

        $order_category = 0;

        if($project_id > 0)
        {
            $project = DK_Common__Project::find($project_id);
            if($project)
            {
                $order_category = $project->project_category;
            }
            else response_error([],"该【项目】不存在！");
        }
        else
        {
            if($client_id > 0)
            {
                $client = DK_Common__Client::find($client_id);
                if($client)
                {
                    $order_category = $client->client_category;
                }
                else response_error([],"该【项目】不存在！");
            }
        }

        // 单文件
        if(!empty($post_data["txt-file"]))
        {

//            $result = upload_storage($post_data["attachment"]);
//            $result = upload_storage($post_data["attachment"], null, null, 'assign');
            $result = upload_file_storage($post_data["txt-file"],null,'dk/unique/attachment','');
            if($result["result"])
            {
//                $mine->attachment_name = $result["name"];
//                $mine->attachment_src = $result["local"];
//                $mine->save();
                $attachment_file = storage_resource_path($result["local"]);

                $file_data = file($attachment_file);

                $collection = collect($file_data)->map(function ($line) {
                    return trim($line);
                });
                $chunks = $collection->chunk(500);
                $chunks = $chunks->toArray();
//                dd($chunks);

                $insert_data = [];
                foreach($chunks as $key => $value)
                {
                    $data = [];
                    foreach($value as $v)
                    {
                        if(is_numeric(trim($v)))
                        {
                            $data[] = [
                                'creator_id'=>$me->id,
                                'created_type'=>9,
                                'client_phone'=>trim($v),
                                'order_category'=>$order_category,
                                'delivered_client_id'=>$client_id,
                                'delivered_project_id'=>$project_id,
                                'is_published'=>1,
//                                'published_date'=>$date,
                                'inspected_status'=>1,
                                'inspected_result'=>'通过',
                                'created_at'=>$time
                            ];
                        }
                    }
                    $insert_data[] = $data;
                };



                // 启动数据库事务
                DB::beginTransaction();
                try
                {
                    foreach($insert_data as $insert_value)
                    {
                        $modal_order = new DK_Common__Order;
                        $bool = $modal_order->insert($insert_value);

                        if(!$bool) throw new Exception("DK_Common__Order--insert--fail");
                    }

                    DB::commit();
                    return response_success(['count'=>$collection->count()]);
                }
                catch (Exception $e)
                {
                    DB::rollback();
                    $msg = '操作失败，请重试！';
                    $msg = $e->getMessage();
//                    exit($e->getMessage());
                    return response_fail([],$msg);
                }
            }
            else return response_error([],"attachment-file--upload--fail");
        }
        else return response_error([],"清选择txt文件！");

    }




    // 【工单】【操作记录】返回-列表-数据
    public function o1__order__item_operation_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Common__Order__Operation_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['order_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


        if(in_array($me->staff_category,[41,51,61]))
        {
            $query->whereIn('operate_category',[1,41]);
            $query->whereIn('operate_type',[1,9,11,12,51,61,69]);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
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
    // 【工单】【交付记录】返回-列表-数据
    public function o1__order__item_delivery_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $order = DK_Common__Order::select('*')->find($id);
//        dd($order->client_phone);

        $order_list = DK_Common__Order::select('*')
            ->with([
                'project_er'=>function($query) { $query->select(['id','name','alias_name']); },
                'delivered_project_er'=>function($query) { $query->select(['id','name','alias_name']); },
                'delivered_client_er'=>function($query) { $query->select(['id','name']); },
            ])
            ->where('client_phone',$order->client_phone)
            ->where('id','<>',$id)
            ->where('is_published','>',0)
            ->where('order_category',$order->order_category)
            ->get();

        $delivery_list = DK_Common__Delivery::select('*')
            ->with([
                'original_project_er'=>function($query) { $query->select(['id','name','alias_name']); },
                'project_er'=>function($query) { $query->select(['id','name','alias_name']); },
                'client_er'=>function($query) { $query->select(['id','name']); },
            ])
            ->where('client_phone',$order->client_phone)
            ->get();


        $draw = isset($post_data['draw']) ? $post_data['draw'] : 0;
        $skip = isset($post_data['start']) ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = [];
        foreach ($order_list as $k => $v)
        {
            $v->item_type = "order";
            $list[] = $v;
        }
        foreach ($delivery_list as $k => $v)
        {
            $v->item_type = "delivery";
            $list[] = $v;
        }
        $total = count($list);
//        dd($delivery_list->toArray());
        return datatable_response($list, $draw, $total);
    }




    // 【工单】发布
    public function o1__order__item_publish($post_data)
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

        $time = time();
        $date = date("Y-m-d");

        $operate = $post_data["operate"];
        if($operate != 'order--item-publish') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        if($item->is_published != 0)
        {
            return response_error([],"该【工单】已经发布过了，不要重复发布，刷新页面看下！");
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $project_id = $item->project_id;
        $client_phone = $item->client_phone;

        $is_today_repeat = DK_Common__Order::where(['client_phone'=>(int)$client_phone])
            ->where('id','<>',$id)
            ->where('is_published','>',0)
            ->where('published_date',$date)
            ->where('order_category',$item->order_category)
            ->count("*");
        if($is_today_repeat > 0)
        {
            return response_error([],"该号码今日已经提交过，不能重复提交！");
        }

        $is_repeat = DK_Common__Order::where(['project_id'=>$project_id,'client_phone'=>(int)$client_phone])
            ->where('id','<>',$id)
            ->where('is_published','>',0)
            ->where('order_category',$item->order_category)
            ->whereIn('inspected_result',['通过','内部通过','郊区通过','折扣通过'])
            ->count("*");
        if($is_repeat == 0)
        {
            $is_repeat = DK_Common__Delivery::where(['project_id'=>$project_id,'client_phone'=>(int)$client_phone])->count("*");
        }
        if($is_repeat > 0) $is_repeat += 1;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->inspected_status == 1)
            {
                $item->inspected_status = 9;
            }


            // 二奢直接交付
            if($item->order_category == 99 && false)
            {
                $inspected_result = '通过';
                $delivered_result = '正常交付';

                // 审核
                $item->inspector_id = 0;
                $item->inspected_status = 1;
                $item->inspected_result = $inspected_result;
                $item->inspected_date = $date;
                $item->inspected_at = $time;


                $project = DK_Common__Project::find($item->project_id);
                if($project->client_id != 0)
                {
                    $delivered_client_id = $project->client_id;
                    $client = DK_Common__Client::find($delivered_client_id);
                    if(!$client) return response_error([],"客户不存在！");
                }
                else $delivered_client_id = 0;

                $delivered_project_id = $item->project_id;


                // 交付
                $item->is_distributive_condition = 0;
                $item->client_id = $delivered_client_id;
                $item->deliverer_id = 0;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_at = $time;
                $item->delivered_date = $date;


                $pivot_delivery = new DK_Common__Delivery;
                if($client)
                {
                    $pivot_delivery_data["company_id"] = $client->company_id;
                    $pivot_delivery_data["channel_id"] = $client->channel_id;
                    $pivot_delivery_data["business_id"] = $client->business_id;
                }
                $pivot_delivery_data["order_category"] = $item->order_category;
                $pivot_delivery_data["delivery_type"] = 1;
                $pivot_delivery_data["project_id"] = $delivered_project_id;
                $pivot_delivery_data["client_id"] = $delivered_client_id;
                $pivot_delivery_data["original_project_id"] = $item->project_id;
                $pivot_delivery_data["order_id"] = $item->id;
                $pivot_delivery_data["client_type"] = $item->client_type;
                $pivot_delivery_data["client_phone"] = $item->client_phone;
                $pivot_delivery_data["delivered_result"] = '正常交付';
                $pivot_delivery_data["delivered_date"] = $date;
                $pivot_delivery_data["creator_id"] = $me->id;

                $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                if(!$bool_0) throw new Exception("DK_Common__Delivery--insert--fail");

            }


            $item->is_repeat = $is_repeat;
            $item->is_published = 1;
            $item->published_at = $time;
            $item->published_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 11;
                $record_data["operate_type"] = 1;
                $record_data["process_category"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }

            DB::commit();



            if(env('APP_ENV') == "production" && $item->order_category == 1)
            {
                if($item->api_is_pushed == 0)
                {
                    $push_data["item"] = "补牙";
                    $push_data["name"] = $item->client_name;
                    $push_data["phone"] = $item->client_phone;
                    $push_data["intention"] = $item->client_intention;
                    $push_data["city"] = $item->location_city;
                    $push_data["area"] = $item->location_district;
                    $push_data["toothcount"] = $item->teeth_count;
                    $push_data["addvx"] = (($item->is_wx) ? '是' : '否');
                    $push_data["vxaccount"] = (($item->wx_id) ? $item->wx_id : '');
                    $push_data["source"] = $item->channel_source;
                    $push_data["description"] = $item->description;

                    $request_result = $this->operate_api_push_order($push_data);

                    if($request_result['success'])
                    {
                        $result = json_decode($request_result['result']);
                        if($result->code == 0)
                        {
                            $item->api_is_pushed = 1;
                            $item->save();
                            return response_success([],"发布成功，推送成功!");
                        }
                        else
                        {
                            return response_error([],"发布成功，推送返回失败!");
                        }
                    }
                    else
                    {
                        return response_error([],"发布成功，接口推送失败!");
                    }
                }
            }


            return response_success([],"发布成功!");
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

    // 【工单】审核
    public function o1__order__item_inspecting_save($post_data)
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
        if($operate != 'order--item-inspecting') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,51,61,71]))
        {
            return response_error([],"你没有操作权限！");
        }

        $inspected_result = $post_data["order-item-inspecting--inspected-result"];
        if(!in_array($inspected_result,config('dk.common-config.inspected_result')))
        {
            return response_error([],"审核结果非法！");
        }
        $inspected_description = $post_data["order-item-inspecting--description"];
        $recording_quality = $post_data["order-item-inspecting--recording-quality"];

        $project_id = $post_data["project_id"];
        $client_type = $post_data["client_type"];
        $client_name = $post_data["client_name"];
        $client_intention = $post_data["client_intention"];
        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];
        $field_1 = $post_data["field_1"];
        $description = trim($post_data["description"]);



        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');



        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["order_id"] = $id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 41;
        $record_data["operate_type"] = 51;
        $record_data["description"] = $inspected_description;


        $record_content = [];


        if(true)
        {
            $record_row = [];
            $record_row['title'] = '员工操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '质检审核';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '审核时间';
            $record_row['field'] = 'inspected_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '审核结果';
            $record_row['field'] = 'inspected_result';
            $record_row['code'] = $inspected_result;

            $before__inspected_result = !empty($item->inspected_result) ? $item->inspected_result : '';
            if($before__inspected_result == '不合格') $record_row['before'] = '拒绝.';
            else $record_row['before'] = $before__inspected_result;

            if($inspected_result == '不合格') $record_row['after'] = '拒绝.';
            else $record_row['after'] = $inspected_result;

            $record_content[] = $record_row;
        }
        if($inspected_description)
        {
            $record_row = [];
            $record_row['title'] = '审核说明';
            $record_row['field'] = 'inspected_description';
            $record_row['before'] = '';
            $record_row['after'] = $inspected_description;
            $record_content[] = $record_row;
        }

        // 项目
        if($item->project_id != $project_id)
        {
            $item->load([
                'project_er'=>function($query) { $query->select('id','name'); }
            ]);

            $project = DK_Common__Project::find($project_id);
            if($project)
            {
                $record_row = [];
                $record_row['title'] = '项目修改';
                $record_row['field'] = 'project_id';
                $record_row['before'] = $item->project_er->name.'('.$item->project_id.')';
                $record_row['after'] = $project->name.'('.$project_id.')';
                $record_content[] = $record_row;
            }
            else return response_error([],"选择的【项目】不存在，刷新页面重试！");
        }
        // 客户姓名
        if($item->client_name != $client_name)
        {
            $record_row = [];
            $record_row['title'] = '客户姓名';
            $record_row['field'] = 'client_name';
            $record_row['before'] = $item->client_name;
            $record_row['after'] = $client_name;
            $record_content[] = $record_row;
        }
        // 患者类型
        if($item->client_type != $client_type)
        {
            $record_row = [];
            $record_row['title'] = '患者类型';
            $record_row['field'] = 'client_type';
            $record_row['before'] = config('dk.common-config.dental_type.'.$item->client_type);
            $record_row['after'] = config('dk.common-config.dental_type.'.$client_type);
            $record_content[] = $record_row;
        }
        // 客户意愿
        if($item->client_intention != $client_intention)
        {
            $record_row = [];
            $record_row['title'] = '客户意愿';
            $record_row['field'] = 'client_intention';
            $record_row['before'] = $item->client_intention;
            $record_row['after'] = $client_intention;
            $record_content[] = $record_row;
        }
        // 城市区域
        if($item->location_city != $location_city || $item->location_district != $location_district)
        {
            $record_row = [];
            $record_row['title'] = '城市区域';
            $record_row['field'] = 'location_city';
            $record_row['before'] = $item->location_city.'-'.$item->location_district;
            $record_row['after'] = $location_city.'-'.$location_district;
            $record_content[] = $record_row;
        }
//        // 城市
//        if($item->location_city != $location_city)
//        {
//            $record_row = [];
//            $record_row['title'] = '城市';
//            $record_row['field'] = 'location_city';
//            $record_row['before'] = $item->location_city;
//            $record_row['after'] = $location_city;
//            $record_content[] = $record_row;
//        }
//        // 区域
//        if($item->location_district != $location_district)
//        {
//            $record_row = [];
//            $record_row['title'] = '区域';
//            $record_row['field'] = 'location_district';
//            $record_row['before'] = $item->location_district;
//            $record_row['after'] = $location_district;
//            $record_content[] = $record_row;
//        }
        // 自定义1
        if($item->field_1 != $field_1)
        {
            $record_row = [];
            if($item->order_category == 1)
            {
                $record_row['title'] = '牙齿数量';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.teeth_count.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.teeth_count.'.$field_1);
            }
            else if($item->order_category == 11)
            {
                $record_row['title'] = '品类';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.aesthetic_type.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.aesthetic_type.'.$field_1);
            }
            else if($item->order_category == 31)
            {
                $record_row['title'] = '品类';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.luxury_type.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.luxury_type.'.$field_1);
            }
            $record_content[] = $record_row;
        }
        // 通话小结
        if($item->description != $description)
        {
            $record_row = [];
            $record_row['title'] = '通话小结';
            $record_row['field'] = 'project_id';
            $record_row['before'] = $item->description;
            $record_row['after'] = $description;
            $record_content[] = $record_row;
        }


        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->project_id != $project_id) $item->project_id = $project_id;
            if($item->client_name != $client_name) $item->client_name = $client_name;
            if($item->client_type != $client_type) $item->client_type = $client_type;
            if($item->client_intention != $client_intention) $item->client_intention = $client_intention;
            if($item->location_city != $location_city) $item->location_city = $location_city;
            if($item->location_district != $location_district) $item->location_district = $location_district;
            if($item->field_1 != $field_1) $item->field_1 = $field_1;
            if($item->description != $description) $item->description = $description;

            $item->inspector_id = $me->id;
            $item->inspected_status = 1;
            $item->inspected_result = $inspected_result;
//            if($inspected_description)
//            {
//                $item->inspected_description = $inspected_description;
//            }
            $item->recording_quality = $recording_quality;
            $item->inspected_at = $time;
            $item->inspected_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }


            DB::commit();


            if(env('APP_ENV') == "production" && $item->order_category == 1)
            {
                if(in_array($inspected_result,['通过','重复','内部通过','折扣通过','郊区通过']))
                {
                    if($item->api_is_pushed == 0)
                    {
                        $push_data["item"] = "补牙";
                        $push_data["name"] = $item->client_name;
                        $push_data["phone"] = $item->client_phone;
                        $push_data["intention"] = $item->client_intention;
                        $push_data["city"] = $item->location_city;
                        $push_data["area"] = $item->location_district;
                        $push_data["toothcount"] = $item->teeth_count;
                        $push_data["addvx"] = (($item->is_wx) ? '是' : '否');
                        $push_data["vxaccount"] = (($item->wx_id) ? $item->wx_id : '');
                        $push_data["source"] = $item->channel_source;
                        $push_data["description"] = $item->description;

                        $request_result = $this->operate_api_push_order($push_data);

                        if($request_result['success'])
                        {
                            $result = json_decode($request_result['result']);
                            if($result->code == 0)
                            {
                                $item->api_is_pushed = 1;
                                $item->save();
                                return response_success([],"审核成功，推送成功!");
                            }
                            else
                            {
                                return response_error([],"审核成功，推送返回失败!");
                            }
                        }
                        else
                        {
                            return response_error([],"审核成功，接口推送失败!");
                        }
                    }
                }
            }


            return response_success([],"审核完成!");
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

    // 【工单】申诉
    public function o1__order__item_appealing_save($post_data)
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
        if($operate != 'order--item-appealing') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,41]))
        {
            return response_error([],"你没有操作权限！");
        }

        $appealed_url = $post_data["order--item-appealing--url"];
        $appealed_description = $post_data["order--item-appealing--description"];



        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');



        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["order_id"] = $id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 41;
        $record_data["operate_type"] = 61;
        $record_data["description"] = $appealed_description;


        $record_content = [];


        if(true)
        {
            $record_row = [];
            $record_row['title'] = '员工操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '工单申诉';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '申诉时间';
            $record_row['field'] = 'handled_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }
        if($appealed_url)
        {
            $record_row = [];
            $record_row['title'] = '录音url';
            $record_row['field'] = 'handled_url';
            $record_row['code'] = $appealed_url;

            $record_row['before'] = '';
            $record_row['after'] = $appealed_url;

            $record_content[] = $record_row;
        }
        if($appealed_description)
        {
            $record_row = [];
            $record_row['title'] = '申诉说明';
            $record_row['field'] = 'handled_description';
            $record_row['before'] = '';
            $record_row['after'] = $appealed_description;
            $record_content[] = $record_row;
        }
        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->appellant_id = $me->id;
            $item->appealed_status = 1;
            if($appealed_url) $item->appealed_url = $appealed_url;
//            if($appealed_description) $item->appealed_description = $appealed_description;
//            $item->appealed_at = time();
            $item->appealed_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }

            DB::commit();

            return response_success([],"申诉完成!");
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

    // 【工单】申诉·处理
    public function o1__order__item_appealed_handling_save($post_data)
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
        if($operate != 'order--item-appealed-handling') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,51,61,71]))
        {
            return response_error([],"你没有操作权限！");
        }

        $appealed_handled_result = $post_data["order--item-appealed-handling--result"];
        if(!array_key_exists($appealed_handled_result,config('dk.common-config.appealed_handled_result')))
        {
            return response_error([],"处理结果非法！");
        }
        $appealed_handled_description = $post_data["order--item-appealed-handling--description"];

        $project_id = $post_data["project_id"];
        $client_type = $post_data["client_type"];
        $client_name = $post_data["client_name"];
        $client_intention = $post_data["client_intention"];
        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];
        $field_1 = $post_data["field_1"];
        $description = trim($post_data["description"]);


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["order_id"] = $id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 41;
        $record_data["operate_type"] = 69;
        $record_data["description"] = $appealed_handled_description;


        $record_content = [];


        if(true)
        {
            $record_row = [];
            $record_row['title'] = '员工操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '复核处理';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '审核时间';
            $record_row['field'] = 'handled_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '复核结果';
            $record_row['field'] = 'handled_result';
            $record_row['code'] = $appealed_handled_result;

            $record_row['before'] = '';
            $record_row['after'] = $appealed_handled_result;

            $record_content[] = $record_row;
        }
        if($appealed_handled_description)
        {
            $record_row = [];
            $record_row['title'] = '复核说明';
            $record_row['field'] = 'handled_description';
            $record_row['before'] = '';
            $record_row['after'] = $appealed_handled_description;
            $record_content[] = $record_row;
        }

        // 项目
        if($item->project_id != $project_id)
        {
            $item->load([
                'project_er'=>function($query) { $query->select('id','name'); }
            ]);

            $project = DK_Common__Project::find($project_id);
            if($project)
            {
                $record_row = [];
                $record_row['title'] = '项目修改';
                $record_row['field'] = 'project_id';
                $record_row['before'] = $item->project_er->name.'('.$item->project_id.')';
                $record_row['after'] = $project->name.'('.$project_id.')';
                $record_content[] = $record_row;
            }
            else return response_error([],"选择的【项目】不存在，刷新页面重试！");
        }
        // 客户姓名
        if($item->client_name != $client_name)
        {
            $record_row = [];
            $record_row['title'] = '客户姓名';
            $record_row['field'] = 'client_name';
            $record_row['before'] = $item->client_name;
            $record_row['after'] = $client_name;
            $record_content[] = $record_row;
        }
        // 患者类型
        if($item->client_type != $client_type)
        {
            $record_row = [];
            $record_row['title'] = '患者类型';
            $record_row['field'] = 'client_type';
            $record_row['before'] = config('dk.common-config.dental_type.'.$item->client_type);
            $record_row['after'] = config('dk.common-config.dental_type.'.$client_type);
            $record_content[] = $record_row;
        }
        // 客户意愿
        if($item->client_intention != $client_intention)
        {
            $record_row = [];
            $record_row['title'] = '客户意愿';
            $record_row['field'] = 'client_intention';
            $record_row['before'] = $item->client_intention;
            $record_row['after'] = $client_intention;
            $record_content[] = $record_row;
        }
        // 城市区域
        if($item->location_city != $location_city || $item->location_district != $location_district)
        {
            $record_row = [];
            $record_row['title'] = '城市区域';
            $record_row['field'] = 'location_city';
            $record_row['before'] = $item->location_city.'-'.$item->location_district;
            $record_row['after'] = $location_city.'-'.$location_district;
            $record_content[] = $record_row;
        }
//        // 城市
//        if($item->location_city != $location_city)
//        {
//            $record_row = [];
//            $record_row['title'] = '城市';
//            $record_row['field'] = 'location_city';
//            $record_row['before'] = $item->location_city;
//            $record_row['after'] = $location_city;
//            $record_content[] = $record_row;
//        }
//        // 区域
//        if($item->location_district != $location_district)
//        {
//            $record_row = [];
//            $record_row['title'] = '区域';
//            $record_row['field'] = 'location_district';
//            $record_row['before'] = $item->location_district;
//            $record_row['after'] = $location_district;
//            $record_content[] = $record_row;
//        }
        // 自定义1
        if($item->field_1 != $field_1)
        {
            $record_row = [];
            if($item->order_category == 1)
            {
                $record_row['title'] = '牙齿数量';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.teeth_count.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.teeth_count.'.$field_1);
            }
            else if($item->order_category == 11)
            {
                $record_row['title'] = '品类';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.aesthetic_type.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.aesthetic_type.'.$field_1);
            }
            else if($item->order_category == 31)
            {
                $record_row['title'] = '品类';
                $record_row['field'] = 'field_1';
                $record_row['before'] = config('dk.common-config.luxury_type.'.$item->field_1);
                $record_row['after'] = config('dk.common-config.luxury_type.'.$field_1);
            }
            $record_content[] = $record_row;
        }
        // 通话小结
        if($item->description != $description)
        {
            $record_row = [];
            $record_row['title'] = '通话小结';
            $record_row['field'] = 'project_id';
            $record_row['before'] = $item->description;
            $record_row['after'] = $description;
            $record_content[] = $record_row;
        }


        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->project_id != $project_id) $item->project_id = $project_id;
            if($item->client_name != $client_name) $item->client_name = $client_name;
            if($item->client_type != $client_type) $item->client_type = $client_type;
            if($item->client_intention != $client_intention) $item->client_intention = $client_intention;
            if($item->location_city != $location_city) $item->location_city = $location_city;
            if($item->location_district != $location_district) $item->location_district = $location_district;
            if($item->field_1 != $field_1) $item->field_1 = $field_1;
            if($item->description != $description) $item->description = $description;

            if($appealed_handled_result == 1)
            {
                $item->inspected_result = '通过';
            }
            $item->appealed_handler_id = $me->id;
            $item->appealed_status = 9;
            $item->appealed_result = $appealed_handled_result;
//            if($appealed_handled_description) $item->appealed_handled_description = $appealed_handled_description;
//            $item->appealed_at = time();
            $item->appealed_handled_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }


            DB::commit();

            return response_success([],"申诉·处理完成!");
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

    // 【工单】申诉
    public function v1_operate_for_order_item_appeal($post_data)
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
        if($operate != 'order-appeal') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

//        $inspected_result = $post_data["inspected_result"];
//        if(!in_array($inspected_result,config('info.inspected_result'))) return response_error([],"审核结果非法！");
        $appealed_url = $post_data["appealed_url"];
        $appealed_description = $post_data["appealed_description"];


        $before = $item->inspected_result;

        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->appellant_id = $me->id;
            $item->appealed_status = 1;
            if($appealed_url) $item->appealed_url = $appealed_url;
            if($appealed_description) $item->appealed_description = $appealed_description;
//            $item->appealed_at = time();
            $item->appealed_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 93;
                $record_data["operate_type"] = 1;
                $record_data["process_category"] = 1;
                $record_data["description"] = $appealed_description;

                $record_content = [];

                $record_row['field'] = 'appeal_description';
                $record_row['title'] = '说明';
                $record_row['before'] = '';
                $record_row['after'] = $appealed_description;
                $record_content[] = $record_row;

                if($appealed_url)
                {
                    $record_row['field'] = 'appealed_url';
                    $record_row['title'] = '录音';
                    $record_row['before'] = '';
                    $record_row['after'] = $appealed_url;
                    $record_content[] = $record_row;
                }

                $record_row['field'] = 'appeal_time';
                $record_row['title'] = '时间';
                $record_row['before'] = '';
                $record_row['after'] = $datetime;
                $record_content[] = $record_row;

                $record_data["content"] = json_encode($record_content);

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }


            DB::commit();

            return response_success([],"申诉提交完成!");
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

    // 【工单】申诉-处理
    public function v1_operate_for_order_item_appeal_handle($post_data)
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
        if($operate != 'order-appeal-handle') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,91])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $appealed_handled_result = $post_data["appealed_handled_result"];
        if(!array_key_exists($appealed_handled_result,config('info.appealed_handled_result'))) return response_error([],"申诉结果非法！");
        $appealed_handled_description = $post_data["appealed_handled_description"];


        $before = $item->appealed_handled_result;

        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($appealed_handled_result == 1)
            {
                $item->inspected_result = '通过';
            }
            $item->appealed_handler_id = $me->id;
            $item->appealed_status = 9;
            $item->appealed_result = $appealed_handled_result;
            if($appealed_handled_description) $item->appealed_handled_description = $appealed_handled_description;
//            $item->appealed_at = time();
            $item->appealed_handled_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 94;
                $record_data["operate_type"] = 1;
                $record_data["process_category"] = 1;
                $record_data["description"] = $appealed_handled_description;

                $record_content = [];

                if($appealed_handled_result == 1) $result_txt = config('info.appealed_handled_result')[$appealed_handled_result];
                else if($appealed_handled_result == 9) $result_txt = config('info.appealed_handled_result')[$appealed_handled_result];
                else $result_txt = '结果有误';
                $record_row['title'] = '结果';
                $record_row['field'] = 'appealed_handled_result';
                $record_row['code'] = $appealed_handled_result;
                $record_row['before'] = '';
                $record_row['after'] = $result_txt;
                $record_content[] = $record_row;

                $record_row['field'] = 'appealed_handled_description';
                $record_row['title'] = '说明';
                $record_row['before'] = '';
                $record_row['after'] = $appealed_handled_description;
                $record_content[] = $record_row;

                $record_row['field'] = 'appeal_handle_time';
                $record_row['title'] = '时间';
                $record_row['before'] = '';
                $record_row['after'] = $datetime;
                $record_content[] = $record_row;

                $record_data["content"] = json_encode($record_content);

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }


            DB::commit();

            return response_success([],"申诉处理完成!");
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

    // 【工单】交付
    public function o1__order__item_delivering_save($post_data)
    {
//        dd($post_data);
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
        if($operate != 'order--item-delivering') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;
        $order_category = $item->order_category;

        $this->get_me();
        $me = $this->me;

        // [判断]操作权限
        if(!in_array($me->staff_category,[0,1,9,71])) return response_error([],"你没有操作权限！");
//        if(in_array($me->staff_position,[99]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');

        $is_next = 0;
        $is_delivery = 0;
        $delivered_result = '';
        $non_delivery_reason = '';


        $delivered_result = $post_data["order-item-delivering--delivered-result"];
        if(!in_array($delivered_result,config('dk.common-config.delivered_result'))) return response_error([],"交付结果参数有误！");


        // [判断]交付结果
        if(in_array($delivered_result,['正常交付','折扣交付','郊区交付','内部交付']))
        {
            // 立即交付
            $is_next = 1;
            $is_delivery = 1;
        }
        else if(in_array($delivered_result,['隔日交付']))
        {
            // 隔日交付
            $is_next = 0;
            $is_delivery = 9;
        }
        else
        {
            // 不交付
            $is_next = 0;
            $is_delivery = 91;
        }


        // [获取]参数
        $project_id = !empty($post_data["project_id"]) ? (int)$post_data["project_id"] : 0;
        $client_id = !empty($post_data["client_id"]) ? (int)$post_data["client_id"] : 0;
        $delivered_description = $post_data["order-item-delivering--description"];
        $recording_address = $post_data["order-item-delivering--recording-address"];
//        $is_distributive_condition = $post_data["is_distributive_condition"];


        // [设置]交付项目&客户id
        $delivered_project_id = 0;
        $delivered_client_id = 0;


        if($project_id > 0)
        {
            $delivered_project_id = $project_id;
        }
        else
        {
            $delivered_project_id = $item->project_id;
        }


        // [判断]【项目】是否存在
        if($is_next == 1)
        {
            if($delivered_project_id > 0)
            {
                $project = DK_Common__Project::find($delivered_project_id);
                if(!$project)
                {
//            return response_error([],"交付【项目】不存在！");
                    $is_next = 0;
                    $is_delivery = 99;
                    $delivered_result = '交付失败';
                    $non_delivery_reason = '项目不存在！';
                }

                if($client_id > 0)
                {
                    $delivered_client_id = $client_id;
                }
                else
                {
                    $delivered_client_id == $project->client_id;
                }
            }
            else
            {
                $is_next = 0;
                $is_delivery = 99;
                $delivered_result = '交付失败';
                $non_delivery_reason = '交付【项目】不存在！';
            }
        }


        // [判断]【客户】是否存在
        if($is_next == 1)
        {
            if($delivered_client_id > 0)
            {
                $client = DK_Common__Client::find($delivered_client_id);
                if(!$client)
                {
//                return response_error([],"交付【客户】不存在！");
                    $is_next = 0;
                    $is_delivery = 99;
                    $delivered_result = '交付失败';
                    $non_delivery_reason = '客户不存在！';
                }
            }
            else
            {
                $is_next = 0;
                $is_delivery = 99;
                $delivered_result = '交付失败';
                $non_delivery_reason = '交付【客户】不存在！';
            }
        }


        // [判断]【工单】是否重复
        if($is_next == 1)
        {
            $is_order_list = DK_Common__Order::select('id','order_category','client_phone','delivered_project_id','delivered_client_id')
                ->where('order_category',$order_category)
                ->where('client_phone',$client_phone)
                ->where(function ($query) use($delivered_project_id,$delivered_client_id) {
                    $query->where('delivered_project_id',$delivered_project_id)->orWhere('delivered_client_id',$delivered_client_id);
                })
                ->get();
            if(count($is_order_list) > 0)
            {
                $is_delivery = 99;
                $delivered_result = '交付失败';
                foreach($is_order_list as $o)
                {
                    // 判断项目
                    if($o->delivered_project_id == $delivered_project_id)
                    {
                        $delivered_result = '项目·重复';
                        $non_delivery_reason = '【项目】重复';
                        break; // 跳出循环
                    }

                    // 判断客户
                    if($o->delivered_client_id == $delivered_client_id)
                    {
                        $delivered_result = '客户·重复';
                        $non_delivery_reason = '【客户】重复';
                        break; // 跳出循环
                    }
                }
            }
        }


        // [判断]【交付】是否重复
        if($is_next == 1)
        {
            $is_delivered_list = DK_Common__Delivery::select('id','order_category','client_phone','project_id','client_id')
                ->where(['order_category'=>$order_category,'client_phone'=>$client_phone])
                ->where(function ($query) use($delivered_project_id,$delivered_client_id) {
                    $query->where('project_id',$delivered_project_id)->orWhere('client_id',$delivered_client_id);
                })
                ->get();
            if(count($is_delivered_list) > 0)
            {
                $is_delivery = 99;
                $delivered_result = '交付失败';
                foreach($is_delivered_list as $d)
                {
                    // 判断项目
                    if($d->project_id == $delivered_project_id)
                    {
                        $delivered_result = '项目·重复';
                        $non_delivery_reason = '【交付项目】重复';
                        break; // 跳出循环
                    }

                    // 判断客户
                    if($d->client_id == $delivered_client_id)
                    {
                        $delivered_result = '客户·重复';
                        $non_delivery_reason = '【交付客户】重复';
                        break; // 跳出循环
                    }
                }
            }
        }




        $before = $item->delivered_result;
        $before = !empty($before) ? $before : '';


        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["order_id"] = $id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 71;
        $record_data["column_name"] = "delivered_result";

        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_delivering';
            $record_row['before'] = '';
            $record_row['after'] = '正常交付';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '时间';
            $record_row['field'] = 'delivered_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }

        if($is_next == 1)
        {
            $record_row = [];
            $record_row['title'] = '项目';
            $record_row['field'] = 'project_id';
            $record_row['before'] = '';
            if($project)
            {
                $record_row['value'] = $project_id;
                $record_row['after'] = $project->name.'('.$project_id.')';
            }
            else
            {
                $record_row['after'] = $item->project_id;
            }
            $record_content[] = $record_row;
        }

        if($is_next == 1)
        {
            $record_row = [];
            $record_row['title'] = '客户';
            $record_row['field'] = 'client_id';
            $record_row['before'] = '';
            if($client)
            {
                $record_row['value'] = $client_id;
                $record_row['after'] = $client->name.'('.$client_id.')';
            }
            else
            {
                $record_row['after'] = $item->client_id;
            }
            $record_content[] = $record_row;
        }


        if(true)
        {
            $record_row = [];
            $record_row['title'] = '结果';
            $record_row['field'] = 'delivered_result';
            $record_row['code'] = '';
            $record_row['before'] = $before;
            $record_row['after'] = $delivered_result;
            $record_content[] = $record_row;
        }

        if($non_delivery_reason)
        {
            $record_row = [];
            $record_row['title'] = '说明';
            $record_row['field'] = 'delivered_description';
            $record_row['before'] = '';
            $record_row['after'] = $non_delivery_reason;
            $record_content[] = $record_row;
        }

        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($is_delivery > 0)
            {
                if($is_delivery == 1)
                {
                    $delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$item->id])->first();
                    if($delivery)
                    {
                        if($client)
                        {
                            $delivery->company_id = $client->company_id;
                            $delivery->channel_id = $client->channel_id;
                            $delivery->business_id = $client->business_id;
                        }
                        $delivery->project_id = $project_id;
                        $delivery->client_id = $client_id;
                        $delivery->delivered_result = $delivered_result;
                        $delivery->delivered_date = $date;
                        $bool_delivery = $delivery->save();
                        if(!$bool_delivery) throw new Exception("DK_Common__Delivery--update--fail");
                    }
                    else
                    {
                        $delivery = new DK_Common__Delivery;
                        if($client)
                        {
                            $delivery_data["company_id"] = $client->company_id;
                            $delivery_data["channel_id"] = $client->channel_id;
                            $delivery_data["business_id"] = $client->business_id;
                        }
                        $delivery_data["order_category"] = $item->order_category;
                        $delivery_data["delivery_type"] = 1;
                        $delivery_data["project_id"] = $project_id;
                        $delivery_data["client_id"] = $client_id;
                        $delivery_data["original_project_id"] = $item->project_id;
                        $delivery_data["order_id"] = $item->id;
                        $delivery_data["client_type"] = $item->client_type;
                        $delivery_data["client_phone"] = $item->client_phone;
                        $delivery_data["delivered_result"] = $delivered_result;
                        $delivery_data["delivered_date"] = $date;
                        $delivery_data["creator_id"] = $me->id;

                        $bool_delivery = $delivery->fill($delivery_data)->save();
                        if(!$bool_delivery) throw new Exception("DK_Common__Delivery--insert--fail");
                    }

                    $item->delivered_project_id = $project_id;
                    $item->delivered_client_id = $client_id;
                    $item->deliverer_id = $me->id;
                    $item->delivered_status = 1;
                    $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
                    $item->delivered_date = $date;
                    $item->delivered_at = $time;
                    $bool = $item->save();
                    if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                }
                else
                {

                    $item->deliverer_id = $me->id;
                    $item->delivered_status = $is_delivery;
                    $item->delivered_result = $delivered_result;
//                $item->delivered_description = $non_delivery_reason;
                    $item->delivered_date = $date;
                    $item->delivered_at = $time;
                    $bool = $item->save();
                    if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                }
            }

            $record = new DK_Common__Order__Operation_Record;
            $bool_record = $record->fill($record_data)->save();
            if(!$bool_record) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");

            DB::commit();


            // 自动分发
            if($is_delivery == 1)
            {
                $client = DK_Common__Client::find($client_id);
                if($client->is_automatic_dispatching == 1)
                {
//                dd($client_id);
                    AutomaticDispatchingJob::dispatch($client->id);
                }
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
    // 【工单】批量-交付
    public function o1__order__bulk_delivering_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'project_id.required' => 'project_id.required.',
            'client_id.required' => 'client_id.required.',
            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
            'delivered_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-delivered-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $project_id = $post_data["project_id"];
        $client_id = $post_data["client_id"];
        if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
        {
//            $project = DK_Common__Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
//
//            $delivered_project_id = $item->project_id;
        }
        else if(!in_array($project_id,['-1','0',-1,0]) && !in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Common__Project::find($project_id);
            if(!$project) return response_error([],"项目不存在！");

            $client = DK_Common__Client::find($client_id);
            if(!$client) return response_error([],"客户不存在！");

            $delivered_project_id = $project_id;
        }
        else
        {
            return response_error([],"项目和客户必须同时选择或同时不选！");
        }


        $delivered_description = $post_data["delivered_description"];

        $date = date("Y-m-d");


        $sorted = collect($ids_array)->sort();

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['project_id'] = $project_id;
//            $delivered_para['client_id'] = $client_id;
//            $delivered_para['deliverer_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
//            $delivered_para['delivered_result'] = $delivered_result;
//            $delivered_para['delivered_at'] = time();

//            $bool = DK_Common__Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            $count = 0;
            $msg = '';
            $ids = [];
            foreach($sorted as $key => $id)
            {

                $item = DK_Common__Order::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");


                if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
                {
                    $project = DK_Common__Project::find($item->project_id);
                    if($project->client_id != 0)
                    {
                        $delivered_client_id = $project->client_id;
                        $client = DK_Common__Client::find($delivered_client_id);
                        if(!$client) return response_error([],"客户不存在！");
                    }
                    else $delivered_client_id = 0;

                    $delivered_project_id = $item->project_id;
                }
                else
                {
                    $delivered_project_id = $project_id;
                    $delivered_client_id = $client_id;
                }


                // 订单重复
                $order_repeated = DK_Common__Order::withTrashed()->where('id','!=',$id)
                    ->where('client_phone',$item->client_phone)
                    ->where('client_id',$delivered_client_id)
                    ->whereNotIn('delivered_result',['拒绝','驳回'])
                    ->get();
                if(count($order_repeated) > 0)
                {
                    $msg = '有部分重复交付';
                    continue;
                }

                // 交付重复
                $delivery_repeated = DK_Common__Delivery::where(['client_id'=>$delivered_client_id,'client_phone'=>$item->client_phone])
                    ->get();
                if(count($delivery_repeated) > 0)
                {
                    $msg = '有部分重复交付';
                    continue;
                }


//                if(!in_array($delivered_client_id,['-1','0',-1,0]) && $delivered_result == "正常交付")
                if(!in_array($delivered_client_id,['-1','0',-1,0]) && in_array($delivered_result,["正常交付","折扣交付","郊区交付","内部交付"]))
                {
                    $pivot_delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$id])->first();
                    if($pivot_delivery)
                    {
                        if($client)
                        {
                            $pivot_delivery->company_id = $client->company_id;
                            $pivot_delivery->channel_id = $client->channel_id;
                            $pivot_delivery->business_id = $client->business_id;
                        }
                        $pivot_delivery->project_id = $delivered_project_id;
                        $pivot_delivery->client_id = $delivered_client_id;
                        $pivot_delivery->delivered_result = $delivered_result;
                        $pivot_delivery->delivered_date = $date;
                        $bool_0 = $pivot_delivery->save();
                        if(!$bool_0) throw new Exception("pivot_client_delivery--update--fail");
                    }
                    else
                    {
                        if($client)
                        {
                            $pivot_delivery_data["company_id"] = $client->company_id;
                            $pivot_delivery_data["channel_id"] = $client->channel_id;
                            $pivot_delivery_data["business_id"] = $client->business_id;
                        }
                        $pivot_delivery = new DK_Common__Delivery;
                        $pivot_delivery_data["order_category"] = $item->order_category;
                        $pivot_delivery_data["pivot_type"] = 95;
                        $pivot_delivery_data["project_id"] = $delivered_project_id;
                        $pivot_delivery_data["client_id"] = $delivered_client_id;
                        $pivot_delivery_data["original_project_id"] = $item->project_id;
                        $pivot_delivery_data["order_id"] = $item->id;
                        $pivot_delivery_data["client_type"] = $item->client_type;
                        $pivot_delivery_data["client_phone"] = $item->client_phone;
                        $pivot_delivery_data["delivered_result"] = $delivered_result;
                        $pivot_delivery_data["delivered_date"] = $date;
                        $pivot_delivery_data["creator_id"] = $me->id;

                        $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                        if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
                    }
                }


                $before = $item->delivered_result;

                $item->client_id = $delivered_client_id;
                $item->deliverer_id = $me->id;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $item->delivered_date = $date;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 95;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "delivered_result";

                    $record_data["before"] = $before;
                    $record_data["after"] = $delivered_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                $count += 1;
                $ids[] = $id;

            }


            DB::commit();


            $client = DK_Common__Client::find($delivered_client_id);
            if($client->is_automatic_dispatching == 1)
            {
                AutomaticDispatchingJob::dispatch($client->id);
            }

            return response_success(['ids'=>$ids],$msg);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



//        // 启动数据库事务
//        DB::beginTransaction();
//        try
//        {
//            foreach($ids_array as $key => $id)
//            {
//
//                $item = DK_Common__Order::withTrashed()->find($id);
//                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
//
//                if(in_array($client_id,['-1','0',-1,0]))
//                {
//                    $project = DK_Common__Project::find($item->project_id);
//                    if($project->client_id != 0)
//                    {
//                        $delivered_client_id = $project->client_id;
//                    }
//                    else $delivered_client_id = 0;
//                }
//                else
//                {
//                    $delivered_client_id = $client_id;
//                }
//
//                $is_new = 1;
//                if($is_new == 1)
//                {
//                    $client = DK_Common__Client::find($delivered_client_id);
//                    $is_automatic_dispatching = $client->is_automatic_dispatching;
//                    if($is_automatic_dispatching == 1)
//                    {
//
//                        $staff_list = DK_Common__Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
//                            ->where('client_id',$delivered_client_id)
//                            ->where('is_take_order',1)
//                            ->where('is_take_order_date',date('Y-m-d'))
//                            ->orderBy('is_take_order_datetime','asc')
//                            ->get();
//                        $staff_list = $staff_list->values(); // 重置索引确保从0开始
//                        $staffCount = $staff_list->count();
//                        if($staffCount > 0)
//                        {
//                            // 使用原子锁避免并发冲突
//                            $lock = Cache::lock("client:{$delivered_client_id}:assign_lock", 10);
//                            if (!$lock->get())
//                            {
//                                dd(1);
//                            }
//                            else
//                                {
//                                    // 尝试获取锁，最多等待5秒
//                                    $lock->block(5); // 这里会阻塞直到获取锁或超时
//
//                                    // 从缓存获取上次位置（不存在则初始化为0）
//                                    $lastIndex = Cache::get("client:{$delivered_client_id}:last_staff_index", 0);
//                                    $currentIndex = $lastIndex % $staffCount;
//                                    $newIndex = $currentIndex;
//
//                                    $staff = $staff_list[$currentIndex];
//                                    $pivot_delivery->client_staff_id = $staff->id;
//                                    $pivot_delivery->save();
//
//                                    // 计算下一个索引
//                                    $currentIndex = ($currentIndex + 1) % $staffCount;
//                                    $newIndex = $currentIndex; // 记录最后的下一个位置
//
//                                    // 将新位置写入缓存（有效期10小时）
//                                    Cache::put(
//                                        "client:{$delivered_client_id}:last_staff_index",
//                                        $newIndex,
//                                        now()->addHours(10)
//                                    );
//
//                                    optional($lock)->release(); // 确保无论如何都释放锁
//
//                            }
//
//                        }
//                    }
//                }
//            }
//
//            DB::commit();
//            return response_success([]);
//        }
//        catch (Exception $e)
//        {
//            DB::rollback();
////            dd(2);
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//            $msg = '操作失败，请重试！';
//            $msg = $e->getMessage();
////                        exit($e->getMessage());
//            return response_fail([],$msg);
//        }
//        finally
//        {
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//        }


    }
    // 【工单】交付（一键交付-傻瓜式交付）
    public function o1__order__item_delivering_save__by_fool($post_data)
    {
//        dd($post_data);
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
        if($operate != 'order--item-delivering-save--by-fool') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;
        $order_category = $item->order_category;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,9,71])) return response_error([],"你没有操作权限！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $is_next = 0;
        $is_delivery = 0;
        $delivered_result = '';
        $non_delivery_reason = '';


        // [判断]【工单】是否审核
        if($item->inspected_status != 1)
        {
            // 【工单】未审核
//            return response_error([],"请先审核工单！");
            $is_next = 0;
            $is_delivery = 0;
            $delivered_result = '交付失败';
            $non_delivery_reason = '工单未审核';
        }
        else
        {
            // 【工单】已审核
            $is_next = 1;
            if($item->inspected_result == "通过")
            {
                $is_delivery = 1;
                $delivered_result = '正常交付';
            }
            else if($item->inspected_result == "折扣通过")
            {
                $is_delivery = 1;
                $delivered_result = '折扣交付';
            }
            else if($item->inspected_result == "郊区通过")
            {
                $is_delivery = 1;
                $delivered_result = '郊区交付';
            }
            else if($item->inspected_result == "内部通过")
            {
                $is_delivery = 1;
                $delivered_result = '内部交付';
            }
            else if($item->inspected_result == "不合格")
            {
                $is_delivery = 1;
                $delivered_result = '正常交付';
            }
            else
            {
                $is_next = 1;
                $is_delivery = 91;
                $delivered_result = '不交付';
                $non_delivery_reason = '非有效单';
            }
        }


        // [判断]【项目】是否存在
        if($is_next == 1)
        {
            $project_id = $item->project_id;
            $project = DK_Common__Project::find($project_id);
            if(!$project)
            {
//            return response_error([],"项目不存在！");
                $is_next = 0;
                $is_delivery = 99;
                $delivered_result = '交付失败';
                $non_delivery_reason = '项目不存在！';
            }
        }

        // [判断]【客户】是否存在
        if($is_next == 1)
        {
            $client_id = $project->client_id;
            $client = DK_Common__Client::find($client_id);
            if(!$client)
            {
//            return response_error([],"客户不存在！");
                $is_next = 0;
                $is_delivery = 99;
                $delivered_result = '交付失败';
                $non_delivery_reason = '客户不存在！';
            }
        }


        // [判断]【工单】是否重复
        if($is_next == 1)
        {
            $is_order_list = DK_Common__Order::select('id','order_category','client_phone','delivered_project_id','delivered_client_id')
                ->where('order_category',$order_category)
                ->where('client_phone',$client_phone)
                ->where(function ($query) use($project_id,$client_id) {
                    $query->where('delivered_project_id',$project_id)->orWhere('delivered_client_id',$client_id);
                })
                ->get();
            if(count($is_order_list) > 0)
            {
                $is_delivery = 99;
                $delivered_result = '交付失败';
                foreach($is_order_list as $o)
                {
                    // 判断项目
                    if($o->delivered_project_id == $project_id)
                    {
                        $delivered_result = '重复';
//                        $delivered_result = '项目·重复';
                        $non_delivery_reason = '【项目】重复';
                        break; // 跳出循环
                    }

                    // 判断客户
                    if($o->delivered_client_id == $client_id)
                    {
                        $delivered_result = '重复';
//                        $delivered_result = '客户·重复';
                        $non_delivery_reason = '【客户】重复';
                        break; // 跳出循环
                    }
                }
            }
        }


        // [判断]【交付】是否重复
        if($is_next == 1)
        {
            $is_delivered_list = DK_Common__Delivery::select('id','order_category','client_phone','project_id','client_id')
                ->where(['order_category'=>$order_category,'client_phone'=>$client_phone])
                ->where(function ($query) use($project_id,$client_id) {
                    $query->where('project_id',$project_id)->orWhere('client_id',$client_id);
                })
                ->get();
            if(count($is_delivered_list) > 0)
            {
                $is_delivery = 99;
                $delivered_result = '交付失败';
                foreach($is_delivered_list as $d)
                {
                    // 判断项目
                    if($d->project_id == $project_id)
                    {
                        $delivered_result = '重复';
//                        $delivered_result = '项目·重复';
                        $non_delivery_reason = '【交付项目】重复';
                        break; // 跳出循环
                    }

                    // 判断客户
                    if($d->client_id == $client_id)
                    {
                        $delivered_result = '重复';
//                        $delivered_result = '客户·重复';
                        $non_delivery_reason = '【交付客户】重复';
                        break; // 跳出循环
                    }
                }
            }
        }


        $before = $item->delivered_result;
        $before = !empty($before) ? $before : '';


        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["order_id"] = $id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 71;
        $record_data["operate_type"] = 9;
        $record_data["column_name"] = "delivered_result";

        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '员工操作';
            $record_row['field'] = 'item_delivering';
            $record_row['before'] = '';
            $record_row['after'] = '一键交付';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作时间';
            $record_row['field'] = 'delivered_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }

        if($is_next == 1)
        {
            $record_row = [];
            $record_row['title'] = '交付项目';
            $record_row['field'] = 'project_id';
            $record_row['before'] = '';
            if($project)
            {
                $record_row['value'] = $project_id;
                $record_row['after'] = $project->name.'('.$project_id.')';
            }
            else
            {
                $record_row['after'] = $item->project_id;
            }
            $record_content[] = $record_row;
        }

        if($is_next == 1)
        {
            $record_row = [];
            $record_row['title'] = '交付客户';
            $record_row['field'] = 'client_id';
            $record_row['before'] = '';
            if($client)
            {
                $record_row['value'] = $client_id;
                $record_row['after'] = $client->name.'('.$client_id.')';
            }
            else
            {
                $record_row['after'] = $item->client_id;
            }
            $record_content[] = $record_row;
        }


        if(true)
        {
            $record_row = [];
            $record_row['title'] = '交付结果';
            $record_row['field'] = 'delivered_result';
            $record_row['code'] = '';
            $record_row['before'] = $before;
            $record_row['after'] = $delivered_result;
            $record_content[] = $record_row;
        }

        if($non_delivery_reason)
        {
            $record_row = [];
            $record_row['title'] = '结果说明';
            $record_row['field'] = 'delivered_description';
            $record_row['before'] = '';
            $record_row['after'] = $non_delivery_reason;
            $record_content[] = $record_row;
        }

        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($is_delivery > 0)
            {
                if($is_delivery == 1)
                {
                    $delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$item->id])->first();
                    if($delivery)
                    {
                        if($client)
                        {
                            $delivery->company_id = $client->company_id;
                            $delivery->channel_id = $client->channel_id;
                            $delivery->business_id = $client->business_id;
                        }
                        $delivery->project_id = $project_id;
                        $delivery->client_id = $client_id;
                        $delivery->delivered_result = $delivered_result;
                        $delivery->delivered_date = $date;
                        $bool_delivery = $delivery->save();
                        if(!$bool_delivery) throw new Exception("DK_Common__Delivery--update--fail");
                    }
                    else
                    {
                        $delivery = new DK_Common__Delivery;
                        if($client)
                        {
                            $delivery_data["company_id"] = $client->company_id;
                            $delivery_data["channel_id"] = $client->channel_id;
                            $delivery_data["business_id"] = $client->business_id;
                        }
                        $delivery_data["order_category"] = $item->order_category;
                        $delivery_data["delivery_type"] = 1;
                        $delivery_data["project_id"] = $project_id;
                        $delivery_data["client_id"] = $client_id;
                        $delivery_data["original_project_id"] = $item->project_id;
                        $delivery_data["order_id"] = $item->id;
                        $delivery_data["client_type"] = $item->client_type;
                        $delivery_data["client_phone"] = $item->client_phone;
                        $delivery_data["delivered_result"] = $delivered_result;
                        $delivery_data["delivered_date"] = $date;
                        $delivery_data["creator_id"] = $me->id;

                        $bool_delivery = $delivery->fill($delivery_data)->save();
                        if(!$bool_delivery) throw new Exception("DK_Common__Delivery--insert--fail");
                    }

                    $item->delivered_project_id = $project_id;
                    $item->delivered_client_id = $client_id;
                    $item->deliverer_id = $me->id;
                    $item->delivered_status = 1;
                    $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
                    $item->delivered_date = $date;
                    $item->delivered_at = $time;
                    $bool = $item->save();
                    if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                }
                else
                {

                    $item->deliverer_id = $me->id;
                    $item->delivered_status = $is_delivery;
                    $item->delivered_result = $delivered_result;
//                $item->delivered_description = $non_delivery_reason;
                    $item->delivered_date = $date;
                    $item->delivered_at = $time;
                    $bool = $item->save();
                    if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                }
            }

            $record = new DK_Common__Order__Operation_Record;
            $bool_record = $record->fill($record_data)->save();
            if(!$bool_record) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");

            DB::commit();


            // 自动分发
            if($is_delivery == 1)
            {
                $client = DK_Common__Client::find($client_id);
                if($client->is_automatic_dispatching == 1)
                {
//                dd($client_id);
                    AutomaticDispatchingJob::dispatch($client->id);
                }
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
    // 【工单】批量-交付（一键交付-傻瓜式交付）
    public function o1__order__bulk_delivering_save__by_fool($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
//            'project_id.required' => 'project_id.required.',
//            'client_id.required' => 'client_id.required.',
//            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
//            'project_id' => 'required',
//            'client_id' => 'required',
//            'delivered_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order--bulk-delivering-save--by-fool') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_category,[0,1,9,71])) return response_error([],"你没有操作权限！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $sorted = collect($ids_array)->sort();

        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $count = 0;
            $msg = '';
            $ids = [];

            foreach($sorted as $key => $id)
            {
                $is_next = 0;
                $is_delivery = 0;
                $delivered_result = '';
                $non_delivery_reason = '';

                $item = DK_Common__Order::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
                $client_phone = $item->client_phone;
                $order_category = $item->order_category;


                // [判断]【工单】是否审核
                if($item->inspected_status != 1)
                {
                    // 【工单】未审核
//            return response_error([],"请先审核工单！");
                    $is_next = 0;
                    $is_delivery = 0;
                    $delivered_result = '交付失败';
                    $non_delivery_reason = '工单未审核';
                }
                else
                {
                    // 【工单】已审核
                    $is_next = 1;
                    if($item->inspected_result == "通过")
                    {
                        $is_delivery = 1;
                        $delivered_result = '正常交付';
                    }
                    else if($item->inspected_result == "折扣通过")
                    {
                        $is_delivery = 1;
                        $delivered_result = '折扣交付';
                    }
                    else if($item->inspected_result == "郊区通过")
                    {
                        $is_delivery = 1;
                        $delivered_result = '郊区交付';
                    }
                    else if($item->inspected_result == "内部通过")
                    {
                        $is_delivery = 1;
                        $delivered_result = '内部交付';
                    }
                    else if($item->inspected_result == "不合格")
                    {
                        $is_delivery = 1;
                        $delivered_result = '正常交付';
                    }
                    else
                    {
                        $is_next = 1;
                        $is_delivery = 91;
                        $delivered_result = '不交付';
                        $non_delivery_reason = '非有效单';
                    }
                }


                // [判断]【项目】是否存在
                if($is_next == 1)
                {
                    $project_id = $item->project_id;
                    $project = DK_Common__Project::find($project_id);
                    if(!$project)
                    {
//            return response_error([],"项目不存在！");
                        $is_next = 0;
                        $is_delivery = 99;
                        $delivered_result = '交付失败';
                        $non_delivery_reason = '项目不存在！';
                    }
                }

                // [判断]【客户】是否存在
                if($is_next == 1)
                {
                    $client_id = $project->client_id;
                    $client = DK_Common__Client::find($client_id);
                    if(!$client)
                    {
//            return response_error([],"客户不存在！");
                        $is_next = 0;
                        $is_delivery = 99;
                        $delivered_result = '交付失败';
                        $non_delivery_reason = '客户不存在！';
                    }
                }


                // [判断]【工单】是否重复
                if($is_next == 1)
                {
                    $is_order_list = DK_Common__Order::select('id','order_category','client_phone','delivered_project_id','delivered_client_id')
                        ->where('order_category',$order_category)
                        ->where('client_phone',$client_phone)
                        ->where(function ($query) use($project_id,$client_id) {
                            $query->where('delivered_project_id',$project_id)->orWhere('delivered_client_id',$client_id);
                        })
                        ->get();
                    if(count($is_order_list) > 0)
                    {
                        $is_delivery = 99;
                        $delivered_result = '交付失败';
                        foreach($is_order_list as $o)
                        {
                            // 判断项目
                            if($o->delivered_project_id == $project_id)
                            {
                                $delivered_result = '重复';
//                        $delivered_result = '项目·重复';
                                $non_delivery_reason = '【项目】重复';
                                break; // 跳出循环
                            }

                            // 判断客户
                            if($o->delivered_client_id == $client_id)
                            {
                                $delivered_result = '重复';
//                        $delivered_result = '客户·重复';
                                $non_delivery_reason = '【客户】重复';
                                break; // 跳出循环
                            }
                        }
                    }
                }


                // [判断]【交付】是否重复
                if($is_next == 1)
                {
                    $is_delivered_list = DK_Common__Delivery::select('id','order_category','client_phone','project_id','client_id')
                        ->where(['order_category'=>$order_category,'client_phone'=>$client_phone])
                        ->where(function ($query) use($project_id,$client_id) {
                            $query->where('project_id',$project_id)->orWhere('client_id',$client_id);
                        })
                        ->get();
                    if(count($is_delivered_list) > 0)
                    {
                        $is_delivery = 99;
                        $delivered_result = '交付失败';
                        foreach($is_delivered_list as $d)
                        {
                            // 判断项目
                            if($d->project_id == $project_id)
                            {
                                $delivered_result = '重复';
//                        $delivered_result = '项目·重复';
                                $non_delivery_reason = '【交付项目】重复';
                                break; // 跳出循环
                            }

                            // 判断客户
                            if($d->client_id == $client_id)
                            {
                                $delivered_result = '重复';
//                        $delivered_result = '客户·重复';
                                $non_delivery_reason = '【交付客户】重复';
                                break; // 跳出循环
                            }
                        }
                    }
                }


                $before = $item->delivered_result;
                $before = !empty($before) ? $before : '';


                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 1;
                $record_data["record_category"] = 1;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 1;
                $record_data["operate_category"] = 71;
                $record_data["operate_type"] = 19;
                $record_data["column_name"] = "delivered_result";

                $record_content = [];

                if(true)
                {
                    $record_row = [];
                    $record_row['title'] = '员工操作';
                    $record_row['field'] = 'item_delivering';
                    $record_row['before'] = '';
                    $record_row['after'] = '一键交付';
                    $record_content[] = $record_row;
                }
                if(true)
                {
                    $record_row = [];
                    $record_row['title'] = '操作时间';
                    $record_row['field'] = 'delivered_time';
                    $record_row['before'] = '';
                    $record_row['after'] = $datetime;
                    $record_content[] = $record_row;
                }

                if($is_next == 1)
                {
                    $record_row = [];
                    $record_row['title'] = '交付项目';
                    $record_row['field'] = 'project_id';
                    $record_row['before'] = '';
                    if($project)
                    {
                        $record_row['value'] = $project_id;
                        $record_row['after'] = $project->name.'('.$project_id.')';
                    }
                    else
                    {
                        $record_row['after'] = $item->project_id;
                    }
                    $record_content[] = $record_row;
                }

                if($is_next == 1)
                {
                    $record_row = [];
                    $record_row['title'] = '交付客户';
                    $record_row['field'] = 'client_id';
                    $record_row['before'] = '';
                    if($client)
                    {
                        $record_row['value'] = $client_id;
                        $record_row['after'] = $client->name.'('.$client_id.')';
                    }
                    else
                    {
                        $record_row['after'] = $item->client_id;
                    }
                    $record_content[] = $record_row;
                }


                if(true)
                {
                    $record_row = [];
                    $record_row['title'] = '交付结果';
                    $record_row['field'] = 'delivered_result';
                    $record_row['code'] = '';
                    $record_row['before'] = $before;
                    $record_row['after'] = $delivered_result;
                    $record_content[] = $record_row;
                }

                if($non_delivery_reason)
                {
                    $record_row = [];
                    $record_row['title'] = '结果说明';
                    $record_row['field'] = 'delivered_description';
                    $record_row['before'] = '';
                    $record_row['after'] = $non_delivery_reason;
                    $record_content[] = $record_row;
                }

                $record_data["content"] = json_encode($record_content);



                if($is_delivery > 0)
                {
                    if($is_delivery == 1)
                    {
                        $delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$item->id])->first();
                        if($delivery)
                        {
                            if($client)
                            {
                                $delivery->company_id = $client->company_id;
                                $delivery->channel_id = $client->channel_id;
                                $delivery->business_id = $client->business_id;
                            }
                            $delivery->project_id = $project_id;
                            $delivery->client_id = $client_id;
                            $delivery->delivered_result = $delivered_result;
                            $delivery->delivered_date = $date;
                            $bool_delivery = $delivery->save();
                            if(!$bool_delivery) throw new Exception("DK_Common__Delivery--update--fail");
                        }
                        else
                        {
                            $delivery = new DK_Common__Delivery;
                            if($client)
                            {
                                $delivery_data["company_id"] = $client->company_id;
                                $delivery_data["channel_id"] = $client->channel_id;
                                $delivery_data["business_id"] = $client->business_id;
                            }
                            $delivery_data["order_category"] = $item->order_category;
                            $delivery_data["delivery_type"] = 1;
                            $delivery_data["project_id"] = $project_id;
                            $delivery_data["client_id"] = $client_id;
                            $delivery_data["original_project_id"] = $item->project_id;
                            $delivery_data["order_id"] = $item->id;
                            $delivery_data["client_type"] = $item->client_type;
                            $delivery_data["client_phone"] = $item->client_phone;
                            $delivery_data["delivered_result"] = $delivered_result;
                            $delivery_data["delivered_date"] = $date;
                            $delivery_data["creator_id"] = $me->id;

                            $bool_delivery = $delivery->fill($delivery_data)->save();
                            if(!$bool_delivery) throw new Exception("DK_Common__Delivery--insert--fail");
                        }

                        $item->delivered_project_id = $project_id;
                        $item->delivered_client_id = $client_id;
                        $item->deliverer_id = $me->id;
                        $item->delivered_status = 1;
                        $item->delivered_result = $delivered_result;
//                $item->delivered_description = $delivered_description;
                        $item->delivered_date = $date;
                        $item->delivered_at = $time;
                        $bool = $item->save();
                        if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                    }
                    else
                    {

                        $item->deliverer_id = $me->id;
                        $item->delivered_status = $is_delivery;
                        $item->delivered_result = $delivered_result;
//                $item->delivered_description = $non_delivery_reason;
                        $item->delivered_date = $date;
                        $item->delivered_at = $time;
                        $bool = $item->save();
                        if(!$bool) throw new Exception("DK_Common__Order--update--fail");
                    }
                }

                $record = new DK_Common__Order__Operation_Record;
                $bool_record = $record->fill($record_data)->save();
                if(!$bool_record) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");



                $count += 1;
                $ids[] = $id;

            }


            DB::commit();


//            $client = DK_Common__Client::find($delivered_client_id);
//            if($client->is_automatic_dispatching == 1)
//            {
//                AutomaticDispatchingJob::dispatch($client->id);
//            }

            return response_success(['ids'=>$ids],$msg);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



//        // 启动数据库事务
//        DB::beginTransaction();
//        try
//        {
//            foreach($ids_array as $key => $id)
//            {
//
//                $item = DK_Common__Order::withTrashed()->find($id);
//                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
//
//                if(in_array($client_id,['-1','0',-1,0]))
//                {
//                    $project = DK_Common__Project::find($item->project_id);
//                    if($project->client_id != 0)
//                    {
//                        $delivered_client_id = $project->client_id;
//                    }
//                    else $delivered_client_id = 0;
//                }
//                else
//                {
//                    $delivered_client_id = $client_id;
//                }
//
//                $is_new = 1;
//                if($is_new == 1)
//                {
//                    $client = DK_Common__Client::find($delivered_client_id);
//                    $is_automatic_dispatching = $client->is_automatic_dispatching;
//                    if($is_automatic_dispatching == 1)
//                    {
//
//                        $staff_list = DK_Common__Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
//                            ->where('client_id',$delivered_client_id)
//                            ->where('is_take_order',1)
//                            ->where('is_take_order_date',date('Y-m-d'))
//                            ->orderBy('is_take_order_datetime','asc')
//                            ->get();
//                        $staff_list = $staff_list->values(); // 重置索引确保从0开始
//                        $staffCount = $staff_list->count();
//                        if($staffCount > 0)
//                        {
//                            // 使用原子锁避免并发冲突
//                            $lock = Cache::lock("client:{$delivered_client_id}:assign_lock", 10);
//                            if (!$lock->get())
//                            {
//                                dd(1);
//                            }
//                            else
//                                {
//                                    // 尝试获取锁，最多等待5秒
//                                    $lock->block(5); // 这里会阻塞直到获取锁或超时
//
//                                    // 从缓存获取上次位置（不存在则初始化为0）
//                                    $lastIndex = Cache::get("client:{$delivered_client_id}:last_staff_index", 0);
//                                    $currentIndex = $lastIndex % $staffCount;
//                                    $newIndex = $currentIndex;
//
//                                    $staff = $staff_list[$currentIndex];
//                                    $pivot_delivery->client_staff_id = $staff->id;
//                                    $pivot_delivery->save();
//
//                                    // 计算下一个索引
//                                    $currentIndex = ($currentIndex + 1) % $staffCount;
//                                    $newIndex = $currentIndex; // 记录最后的下一个位置
//
//                                    // 将新位置写入缓存（有效期10小时）
//                                    Cache::put(
//                                        "client:{$delivered_client_id}:last_staff_index",
//                                        $newIndex,
//                                        now()->addHours(10)
//                                    );
//
//                                    optional($lock)->release(); // 确保无论如何都释放锁
//
//                            }
//
//                        }
//                    }
//                }
//            }
//
//            DB::commit();
//            return response_success([]);
//        }
//        catch (Exception $e)
//        {
//            DB::rollback();
////            dd(2);
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//            $msg = '操作失败，请重试！';
//            $msg = $e->getMessage();
////                        exit($e->getMessage());
//            return response_fail([],$msg);
//        }
//        finally
//        {
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//        }


    }

    // 【工单】分发
    public function o1__order__item_distributing_save($post_data)
    {
//        dd($post_data);
//        return response_success([]);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'project_id.required' => '请选择项目',
            'client_id.required' => '请选择客户',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-distribute') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $client_id = $post_data["client_id"];
        if($client_id == "-1")
        {
            return response_error([],"请选择客户！");
//            $project = DK_Common__Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
        }
        $client = DK_Common__Client::find($client_id);
        if(!$client) return response_error([],"客户不存在！");

        $project_id = $post_data["project_id"];
        if($project_id == "-1")
        {
            return response_error([],"请选择项目！");
//            $project = DK_Common__Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
        }
        $project = DK_Common__Project::find($project_id);
        if(!$project) return response_error([],"项目不存在！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 是否已经分发
        $is_distributed_list = DK_Common__Delivery::where(['client_id'=>$client_id,'client_phone'=>$client_phone])->get();
        if(count($is_distributed_list) > 0)
        {
            return response_error([],"该客户已经交付过该号码，不可以重复分发！");
        }

        // 是否已经交付
        $is_order_list = DK_Common__Order::with('project_er')->where(['client_phone'=>$client_phone,'delivered_result'=>'正常交付'])->get();
        if(count($is_order_list) > 0)
        {
            foreach($is_order_list as $o)
            {
                if($o->client_id == $client_id)
                {
                    return response_error([],"该号码已在其他工单交付过该客户，不可以重复分发！");
                }

                if($o->project_er->client_id == $client_id)
                {
                    return response_error([],"该号码已在其他工单交付过默认客户，不可以重复分发！");
                }
            }
        }


        $before = $item->delivered_result;

//        $delivered_description = $post_data["delivered_description"];
//        $recording_address = $post_data["recording_address"];

        $date = date("Y-m-d");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            if($client_id != "-1")
//            {
            $pivot_delivery = new DK_Common__Delivery;
            $pivot_delivery_data["order_category"] = $item->order_category;
            $pivot_delivery_data["pivot_type"] = 96;
            $pivot_delivery_data["project_id"] = $project_id;
            $pivot_delivery_data["client_id"] = $client_id;
            $pivot_delivery_data["company_id"] = $client->company_id;
            $pivot_delivery_data["channel_id"] = $client->channel_id;
            $pivot_delivery_data["business_id"] = $client->business_id;
            $pivot_delivery_data["original_project_id"] = $item->project_id;
            $pivot_delivery_data["order_id"] = $item->id;
            $pivot_delivery_data["client_type"] = $item->client_type;
            $pivot_delivery_data["client_phone"] = $item->client_phone;
            $pivot_delivery_data["delivered_result"] = $delivered_result;
            $pivot_delivery_data["creator_id"] = $me->id;
            $pivot_delivery_data["delivered_date"] = $date;

            $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
            if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
//            }

//            $item->client_id = $client_id;
//            $item->deliverer_id = $me->id;
//            $item->delivered_status = 1;
//            $item->delivered_result = $delivered_result;
//            $item->delivered_description = $delivered_description;
//            $item->recording_address = $recording_address;
//            $item->delivered_at = time();
//            $bool = $item->save();
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
            $record = new DK_Common__Order__Operation_Record;

            $record_data["ip"] = Get_IP();
            $record_data["record_object"] = 21;
            $record_data["record_category"] = 11;
            $record_data["record_type"] = 1;
            $record_data["creator_id"] = $me->id;
            $record_data["order_id"] = $id;
            $record_data["operate_object"] = 71;
            $record_data["operate_category"] = 96;
            $record_data["operate_type"] = 1;
            $record_data["column_name"] = "client_id";

            $record_data["before"] = $before;
            $record_data["after"] = $client_id;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

            $bool_1 = $record->fill($record_data)->save();
            if(!$bool_1) throw new Exception("insert--record--fail");
//            }

            DB::commit();
//            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



        $is_new = 0;
        if($is_new == 1)
        {
            if($client)
            {
            }
            else
            {
//                $client = DK_Common__Client::find($client_id);

                if(in_array($client_id,['-1','0',-1,0]))
                {
                    $client = DK_Common__Client::find($client_id);
                }

            }
            $is_automatic_dispatching = $client->is_automatic_dispatching;
            if($is_automatic_dispatching == 1)
            {

                $staff_list = DK_Common__Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
                    ->where('client_id',$client_id)
                    ->where('is_take_order',1)
                    ->where('is_take_order_date',date('Y-m-d'))
                    ->orderBy('is_take_order_datetime','asc')
                    ->get();
                $staff_list = $staff_list->values(); // 重置索引确保从0开始
                $staffCount = $staff_list->count();
                if($staffCount > 0)
                {
                    // 使用原子锁避免并发冲突
                    $lock = Cache::lock("client:{$client_id}:assign_lock", 10);
//                    if (!$lock->get())
//                    {
//                        abort(423, '系统正在分配任务中，请稍后重试');
//                    }

                    // 启动数据库事务
                    DB::beginTransaction();
                    try
                    {
                        // 尝试获取锁，最多等待5秒
                        $lock->block(5); // 这里会阻塞直到获取锁或超时

                        // 从缓存获取上次位置（不存在则初始化为0）
                        $lastIndex = Cache::get("client:{$client_id}:last_staff_index", 0);
                        $currentIndex = $lastIndex % $staffCount;
                        $newIndex = $currentIndex;

                        $staff = $staff_list[$currentIndex];
                        $pivot_delivery->client_staff_id = $staff->id;
                        $pivot_delivery->save();

                        // 计算下一个索引
                        $currentIndex = ($currentIndex + 1) % $staffCount;
                        $newIndex = $currentIndex; // 记录最后的下一个位置

                        // 将新位置写入缓存（有效期10小时）
                        Cache::put(
                            "client:{$client_id}:last_staff_index",
                            $newIndex,
                            now()->addHours(10)
                        );

                        DB::commit();
                        optional($lock)->release(); // 确保无论如何都释放锁
                        return response_success([]);
                    }
                    catch (Exception $e)
                    {
                        DB::rollback();
//                        $lock->release();
                        optional($lock)->release(); // 确保无论如何都释放锁
                        $msg = '操作失败，请重试！';
                        $msg = $e->getMessage();
//                        exit($e->getMessage());
                        return response_fail([],$msg);
                    }
                    finally
                    {
//                        $lock->release();
                        optional($lock)->release(); // 确保无论如何都释放锁
                    }
                }
                else
                {
                    return response_success([]);
                }
            }
            else
            {
                return response_success([]);
            }
        }
        else
        {
            return response_success([]);
        }

    }


    // 【工单】外呼系统呼叫记录
    public function o1__order__item_get_call_record__by_api($post_data)
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
        if($operate != 'item-get-api-call-record') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【内容】不存在，刷新页面重试！");
        $staff_id = $item->creator_id;

        $staff = DK_Common__Staff::withTrashed()->find($staff_id);
        if(!$staff) return response_error([],"该【员工】不存在，刷新页面重试！");
        $team_id = $staff->team_id;
        $agent[] = $staff->api_staffNo;

        $team = DK_Common__Team::withTrashed()->find($team_id);
        if(!$team) return response_error([],"该【所属部门】不存在，刷新页面重试！");


        $serverFrom_name = $team->serverFrom_name;
        $API_Customer_Password = $team->api_customer_password;
        $API_Customer_Account = $team->api_customer_account;
        $API_customerUserName = $team->api_customer_name;


        $timestamp = time();
        $seq = $timestamp;
        $digest = md5($API_Customer_Account.'@'.$timestamp.'@'.$seq.'@'.$API_Customer_Password);
//        dd($API_Customer_Account.'--'.$seq.'--'.$digest);

        $request_data['authentication']['customer'] = $API_Customer_Account;
        $request_data['authentication']['timestamp'] = strval($timestamp);
        $request_data['authentication']['seq'] = strval($seq);
        $request_data['authentication']['digest'] = $digest;

        $request_data['request']['seq'] = '';
        $request_data['request']['userData'] = '';
//        $request_data['request']['agent'] = $agent;
        $request_data['request']['callee'] = $item->client_phone;
        $request_data['request']['startTime'] = $item->published_date.' 00:00:00';
        $request_data['request']['endTime'] = $item->published_date.' 23:59:59';
//dd($request_data);

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
            $server = "http://feiniji.cn";
            $url = "http://feiniji.cn/openapi/V2.0.6/getCdrList";
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


        if(curl_errno($ch))
        {
            curl_close($ch);
            return response_error([],"请求失败！");
        }
        else
        {
            curl_close($ch);

            $result = json_decode($request_result);
//            dd($result);
            if($result->result->error == "0")
            {
                if($result->data)
                {
                    $file = [];
                    $response = $result->data->response;
                    if($response->total > 0)
                    {
                        foreach ($response->cdr as $k => $v)
                        {
                            if(!empty($v->filename)) $file[] = $server.$v->filename;
                        }

                        if(count($file) > 0)
                        {

                            if(count($file) == 1)
                            {
                                $item->recording_address = $file[0];
                                $item->recording_address_list = json_encode($file);
                            }
                            else
                            {
                                $item->recording_address_list = json_encode($file);
                            }
                            // 启动数据库事务
                            DB::beginTransaction();
                            try
                            {
                                $bool_1 = $item->save();
                                if(!$bool_1) throw new Exception("DK_Common__Order--update--fail");

                                DB::commit();
                                return response_success(['data'=>$item]);
                            }
                            catch (Exception $e)
                            {
                                DB::rollback();
                                $msg = '操作失败，请重试！';
                                $msg = $e->getMessage();
//                                exit($e->getMessage());
                                return response_fail([],$msg);
                            }
                        }
                        return response_error([],'没有有效通话记录b！');
                    }
                    return response_error([],'没有有效通话记录a！');
                }
                else
                {
                    return response_error([],'未找到通话记录！');
                }
            }
            else
            {
                return response_error([],$result->result->msg);
            }
        }


    }


    // 【工单】获取 GET
    public function v1_operate_for_order_item_get_phone_pool_info($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'phone.required' => 'phone.required.',
            'city.required' => 'city.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'phone' => 'required',
            'city' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");

        $city = $post_data["city"];
        if(!in_array($city,config('sys.data.city_pool'))) return response_error([],"城市不存在！");
        $city_pool_table = config('sys.data.city_pool_table_kv.'.$city);

        $phone = $post_data["phone"];

        $item = DB::table($city_pool_table)->where('phone',$phone)->first();
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }




    // 【项目】【选项-信息】修改-radio-select-[option]-类型
    public function o1__project__item_field_set($post_data)
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


        $item = DK_Common__Project::withTrashed()->find($id);
        if(!$item) return response_error([],"该【项目】不存在，刷新页面重试！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate-type"];

        $column_type = $post_data["column-type"];

        $column_key = $post_data["column-key"];
        $column_key2 = $post_data["column-key2"];

        $column_text_value = $post_data["field-set-text-value"];
        $column_textarea_value = $post_data["field-set-textarea-value"];
        $column_datetime_value = $post_data["field-set-datetime-value"];
        $column_date_value = $post_data["field-set-date-value"];
        $column_select_value = isset($post_data['field-set-select-value']) ? $post_data['field-set-select-value'] : '';
        $column_select_value2 = isset($post_data['field-set-select-value2']) ? $post_data['field-set-select-value2'] : '';
        $column_radio_value  = isset($post_data['field-set-radio-value']) ? $post_data['field-set-radio-value'] : '';

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

        $before = $item->$column_key;
        $after = $column_value;


        $return['value'] = $column_value;
        $return['text'] = $column_value;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "name")
            {
                $is_repeat = DK_Common__Project::where(['name'=>$column_value])->where('id','<>',$id)->count("*");
                if($is_repeat) throw new Exception("名称重复！");
            }
            else if($column_key == "inspector_list")
            {
                if(!empty($post_data["column_value"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $inspector_list = $post_data["column_value"];
                    foreach($inspector_list as $i)
                    {
                        $inspector_list_insert[$i] = ['creator_id'=>$me->id,'department_id'=>$me->team_id,'relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $item->pivot_project_user()->wherePivot('department_id',$me->team_id)->sync($inspector_list_insert);
//                    $mine->pivot_project_user()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $item->pivot_project_user()->wherePivot('department_id',$me->team_id)->detach();
                }
            }
            else if($column_key == "client_id")
            {
                if(in_array($column_value,[-1,0,'-1','0']))
                {
                }
                else
                {
                    $client = DK_Common__Client::withTrashed()->find($column_value);
                    if(!$client) throw new Exception("该【客户】不存在，刷新页面重试！");

                    $return['text'] = $client->name;
                }
            }
            else
            {
//            $item->timestamps = false;
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Project--update--fail");

            if(false) throw new Exception("DK_Common__Project--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(true)
                {
                }
                else
                {
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
                    $record_data["operate_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $column_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    if(in_array($column_key,['client_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }

                    if($column_key == 'client_id')
                    {
                        $record_data["before_client_id"] = $before;
                        $record_data["after_client_id"] = $column_value;
                    }

                    $bool_1 = $record->fill($record_data)->save();
                    if($bool_1)
                    {
                    }
                    else throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
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
    // 【工单】字段修改
    public function o1__order__item_field_set($post_data)
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

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【工单】不存在，刷新页面重试！");

        // 判断对象是否合法
        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");


        $operate_type = $post_data["operate-type"];

        $column_type = $post_data["column-type"];

        $column_key = $post_data["column-key"];
        $column_key2 = $post_data["column-key2"];

        $column_text_value = $post_data["field-set-text-value"];
        $column_textarea_value = $post_data["field-set-textarea-value"];
        $column_datetime_value = $post_data["field-set-datetime-value"];
        $column_date_value = $post_data["field-set-date-value"];
        $column_select_value = isset($post_data['field-set-select-value']) ? $post_data['field-set-select-value'] : '';
        $column_select_value2 = isset($post_data['field-set-select-value2']) ? $post_data['field-set-select-value2'] : '';
        $column_radio_value  = isset($post_data['field-set-radio-value']) ? $post_data['field-set-radio-value'] : '';

        if($column_type == 'text') $column_value = $column_text_value;
        else if($column_type == 'textarea') $column_value = $column_textarea_value;
        else if($column_type == 'radio')
        {
            $column_value = $column_radio_value;
        }
        else if($column_type == 'select') $column_value = $column_select_value;
        else if($column_type == 'select2') $column_value = $column_select_value;
        else if($column_type == 'datetime') $column_value = $column_datetime_value;
        else if($column_type == 'datetime_timestamp') $column_value = strtotime($column_datetime_value);
        else if($column_type == 'date') $column_value = $column_date_value;
        else if($column_type == 'date_timestamp') $column_value = strtotime($column_date_value);
        else $column_value = '';

        $before = $item->$column_key;
        $after = $column_value;
//        dd((string)$before.'-'.(string)$after.'-'.strlen($before));

        if($column_type == "radio")
        {
            $after = $column_value;
        }

        if($before == $after)
        {
            if($column_key == "client_phone")
            {
                return response_error([],"没有修改1！");
            }
            else if($column_key == "location_city")
            {
                if($item->$column_key2 == $column_select_value2) return response_error([],"没有修改2！");
            }
            else
            {
                return response_error([],"没有修改3！");
            }
        }

        $return['value'] = $column_value;
        $return['text'] = $column_value;


        if($column_key == "client_phone")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,84,88])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "inspected_description")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($column_key,['client_id','project_id']))
        {
            if(in_array($column_value,[-1,0,'-1','0'])) return response_error([],"选择有误！");
        }



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $is_repeat = 0;
            if($column_key == "client_phone")
            {
                $project_id = $item->project_id;
                $client_phone = $item->client_phone;
                $column_value = (int)$column_value;

                $is_repeat = DK_Common__Order::where(['project_id'=>$project_id,'client_phone'=>(int)$column_value])
                    ->where('id','<>',$id)->where('is_published','>',0)->count("*");
                if($is_repeat == 0)
                {
                    $is_repeat = DK_Common__Delivery::where(['project_id'=>$project_id,'client_phone'=>(int)$column_value])->count("*");
                }
                if($is_repeat > 0) $is_repeat += 1;
                $item->is_repeat = $is_repeat;
            }
            else if($column_key == "project_id")
            {
                if(in_array($column_value,[-1,0,'-1','0']))
                {
                }
                else
                {
                    $project = DK_Common__Project::withTrashed()->find($column_value);
                    if(!$project) throw new Exception("该【项目】不存在，刷新页面重试！");

                    $project_id = $item->project_id;
                    $client_phone = $item->client_phone;

                    $is_repeat = DK_Common__Order::where(['project_id'=>$column_value,'client_phone'=>(int)$client_phone])
                        ->where('id','<>',$id)->where('is_published','>',0)->count("*");
                    if($is_repeat == 0)
                    {
                        $is_repeat = DK_Common__Delivery::where(['project_id'=>$column_value,'client_phone'=>(int)$client_phone])->count("*");
                    }
                    if($is_repeat > 0) $is_repeat += 1;
                    $item->is_repeat = $is_repeat;

                    $return['text'] = $project->name;
                }
            }
            else if($column_key == "location_city")
            {
                $before = $item->location_city.' - '.$item->location_district;

                $column_value2 = $column_select_value2;
                $item->$column_key2 = $column_value2;

                $after = $column_value.' - '.$column_value2;
                $return['value2'] = $column_value2;
                $return['text'] = $after;
            }

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Order--update--fail");
            else
            {

                $return['item'] = $item;

                // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 1;
                    $record_data["process_category"] = 1;

                    if($operate_type == "add") $record_data["operate_type"] = 1;
                    else if($operate_type == "edit") $record_data["operate_type"] = 11;

                    $record_data["column_type"] = $column_type;
                    $record_data["column_name"] = $column_key;
                    $record_data["before"] = $before;
                    $record_data["after"] = $after;

                    if(in_array($column_key,['client_id','project_id']))
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
                    else throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
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







    // 【工单】【文本】修改-文本-类型
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

        $item = DK_Common__Order::withTrashed()->find($id);
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
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "inspected_description")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77])) return response_error([],"你没有操作权限！");
        }
        else if($column_key == "description")
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,61,66,81,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【工单】不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $is_repeat = 0;
            if($column_key == "client_phone")
            {
                $project_id = $item->project_id;
                $client_phone = $item->client_phone;

                $is_repeat = DK_Common__Order::where(['project_id'=>$project_id,'client_phone'=>$column_value])
                    ->where('id','<>',$id)->where('is_published','>',0)->count("*");
//

                $item->is_repeat = $is_repeat;
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
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
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
            return response_success(['is_repeat'=>$is_repeat]);
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
    // 【工单】【时间】修改-时间-类型
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

        $item = DK_Common__Order::withTrashed()->find($id);
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
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
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
    // 【工单】【选项】修改-radio-select-[option]-类型
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

        $item = DK_Common__Order::withTrashed()->find($id);
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
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,61,66,71,77,81,84,88])) return response_error([],"你没有操作权限！");
        }

        if(in_array($me->user_type,['client_id','project_id']))
        {
            if(in_array($column_value,["-1",-1])) return response_error([],"选择有误！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == "project_id")
            {
                if($column_value == 0)
                {
                }
                else
                {
                    $project = DK_Common__Project::withTrashed()->find($column_value);
                    if(!$project) throw new Exception("该【项目】不存在，刷新页面重试！");

                    $project_id = $item->project_id;
                    $client_phone = $item->client_phone;

                    $is_repeat = DK_Common__Order::where(['project_id'=>$column_value,'client_phone'=>$client_phone])
                        ->where('id','<>',$id)->where('is_published','>',0)->count("*");
                    $item->is_repeat = $is_repeat;
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
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
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

                    if(in_array($column_key,['client_id','project_id']))
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




    // 【工单】交付
    public function operate_item_order_deliver($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'project_id.required' => '请选择项目',
            'client_id.required' => '请选择客户',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-deliver') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $project_id = $post_data["project_id"];
        $client_id = $post_data["client_id"];
        if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Common__Project::find($item->project_id);
            if($project->client_id != 0)
            {
                $delivered_client_id = $project->client_id;
                $client = DK_Common__Client::find($delivered_client_id);
                if(!$client) return response_error([],"客户不存在！");
            }
            else $delivered_client_id = 0;

            $delivered_project_id = $item->project_id;
        }
        else if(!in_array($project_id,['-1','0',-1,0]) && !in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Common__Project::find($project_id);
            if(!$project) return response_error([],"项目不存在！");

            $client = DK_Common__Client::find($client_id);
            if(!$client) return response_error([],"客户不存在！");

            $delivered_project_id = $project_id;
            $delivered_client_id = $client_id;
        }
        else
        {
            return response_error([],"项目和客户必须同时选择或同时不选！");
        }



        // 是否已经分发
        $is_distributed_list = DK_Common__Delivery::where(['client_id'=>$client_id,'client_phone'=>$client_phone])
            ->whereNotIn('delivered_result',['拒绝','驳回'])
            ->get();
        if(count($is_distributed_list) > 0)
        {
            return response_error([],"该客户已经交付过该号码，不可以重复分发！");
        }

        // 是否已经交付
        $is_order_list = DK_Common__Order::with('project_er')
            ->where(['client_phone'=>$client_phone])
            ->whereIn('delivered_result',["正常交付","折扣交付","郊区交付","内部交付"])
            ->get();
//        dd($is_order_list->toArray());

        if(count($is_order_list) > 0)
        {
            foreach($is_order_list as $o)
            {
                if($o->client_id == $delivered_client_id)
                {
                    return response_error([],"该号码正常交付过该客户，不要重复交付！");
                }

//                if($o->project_er->client_id == $delivered_client_id)
//                {
//                    return dd($o->project_er->client_id.'-'.$delivered_client_id);
//                    return response_error([],"该号码正常交付过【默认】客户，不要重复交付！");
//                }
            }
        }

        $before = $item->delivered_result;

        $delivered_description = $post_data["delivered_description"];
        $recording_address = $post_data["recording_address"];

        $is_distributive_condition = $post_data["is_distributive_condition"];

        $date = date("Y-m-d");
        $is_new = 0;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            if($delivered_client_id != "-1" && $delivered_result == "正常交付")
            if($delivered_client_id != "-1" && in_array($delivered_result,["正常交付","折扣交付","郊区交付","内部交付"]))
            {
                $pivot_delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$item->id])->first();
                if($pivot_delivery)
                {
                    if($client)
                    {
                        $pivot_delivery->company_id = $client->company_id;
                        $pivot_delivery->channel_id = $client->channel_id;
                        $pivot_delivery->business_id = $client->business_id;
                    }
                    $pivot_delivery->project_id = $delivered_project_id;
                    $pivot_delivery->client_id = $delivered_client_id;
                    $pivot_delivery->delivered_result = $delivered_result;
                    $pivot_delivery->delivered_date = $date;
                    $bool_0 = $pivot_delivery->save();
                    if(!$bool_0) throw new Exception("pivot_client_delivery--update--fail");
                }
                else
                {
                    $is_new = 1;
                    $pivot_delivery = new DK_Common__Delivery;
                    if($client)
                    {
                        $pivot_delivery_data["company_id"] = $client->company_id;
                        $pivot_delivery_data["channel_id"] = $client->channel_id;
                        $pivot_delivery_data["business_id"] = $client->business_id;
                    }
                    $pivot_delivery_data["order_category"] = $item->order_category;
                    $pivot_delivery_data["pivot_type"] = 95;
                    $pivot_delivery_data["project_id"] = $delivered_project_id;
                    $pivot_delivery_data["client_id"] = $delivered_client_id;
                    $pivot_delivery_data["original_project_id"] = $item->project_id;
                    $pivot_delivery_data["order_id"] = $item->id;
                    $pivot_delivery_data["client_type"] = $item->client_type;
                    $pivot_delivery_data["client_phone"] = $item->client_phone;
                    $pivot_delivery_data["delivered_result"] = $delivered_result;
                    $pivot_delivery_data["delivered_date"] = $date;
                    $pivot_delivery_data["creator_id"] = $me->id;

                    $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                    if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
                }
            }



            $item->is_distributive_condition = $is_distributive_condition;
            $item->client_id = $delivered_client_id;
            $item->deliverer_id = $me->id;
            $item->delivered_status = 1;
            $item->delivered_result = $delivered_result;
            $item->delivered_description = $delivered_description;
            $item->recording_address = $recording_address;
            $item->delivered_at = time();
            $item->delivered_date = $date;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 71;
                $record_data["operate_category"] = 95;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "delivered_result";

                $record_data["before"] = $before;
                $record_data["after"] = $delivered_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();


            $client = DK_Common__Client::find($delivered_client_id);
            if($client->is_automatic_dispatching == 1)
            {
                AutomaticDispatchingJob::dispatch($client->id);
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


        $is_new = 0;
        if($is_new == 1)
        {
            if($client)
            {
            }
            else
            {
                $client = DK_Common__Client::find($client_id);

                if(in_array($client_id,['-1','0',-1,0]))
                {
                    $client = DK_Common__Client::find($delivered_client_id);
                }

            }
            $is_automatic_dispatching = $client->is_automatic_dispatching;
            if($is_automatic_dispatching == 1)
            {

                $staff_list = DK_Common__Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
                    ->where('client_id',$delivered_client_id)
                    ->where('is_take_order',1)
                    ->where('is_take_order_date',date('Y-m-d'))
                    ->orderBy('is_take_order_datetime','asc')
                    ->get();
                $staff_list = $staff_list->values(); // 重置索引确保从0开始
                $staffCount = $staff_list->count();
                if($staffCount > 0)
                {
                    // 使用原子锁避免并发冲突
                    $lock = Cache::lock("client:{$delivered_client_id}:assign_lock", 10);
//                    if (!$lock->get())
//                    {
//                        abort(423, '系统正在分配任务中，请稍后重试');
//                    }

                    // 启动数据库事务
                    DB::beginTransaction();
                    try
                    {
                        // 尝试获取锁，最多等待5秒
                        $lock->block(5); // 这里会阻塞直到获取锁或超时

                        // 从缓存获取上次位置（不存在则初始化为0）
                        $lastIndex = Cache::get("client:{$delivered_client_id}:last_staff_index", 0);
                        $currentIndex = $lastIndex % $staffCount;
                        $newIndex = $currentIndex;

                        $staff = $staff_list[$currentIndex];
                        $pivot_delivery->client_staff_id = $staff->id;
                        $pivot_delivery->save();

                        // 计算下一个索引
                        $currentIndex = ($currentIndex + 1) % $staffCount;
                        $newIndex = $currentIndex; // 记录最后的下一个位置

                        // 将新位置写入缓存（有效期10小时）
                        Cache::put(
                            "client:{$delivered_client_id}:last_staff_index",
                            $newIndex,
                            now()->addHours(10)
                        );

                        DB::commit();
                        optional($lock)->release(); // 确保无论如何都释放锁
                        return response_success([]);
                    }
                    catch (Exception $e)
                    {
                        DB::rollback();
//                        $lock->release();
                        optional($lock)->release(); // 确保无论如何都释放锁
                        $msg = '操作失败，请重试！';
                        $msg = $e->getMessage();
//                        exit($e->getMessage());
                        return response_fail([],$msg);
                    }
                    finally
                    {
//                        $lock->release();
                        optional($lock)->release(); // 确保无论如何都释放锁
                    }
                }
                else
                {
                    return response_success([]);
                }
            }
            else
            {
                return response_success([]);
            }
        }
        else
        {
            return response_success([]);
        }

    }
    // 【工单】批量-交付
    public function operate_item_order_bulk_deliver($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'project_id.required' => 'project_id.required.',
            'client_id.required' => 'client_id.required.',
            'delivered_result.required' => 'delivered_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
            'delivered_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'order-delivered-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $delivered_result = $post_data["delivered_result"];
        if(!in_array($delivered_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $project_id = $post_data["project_id"];
        $client_id = $post_data["client_id"];
        if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
        {
//            $project = DK_Common__Project::find($item->project_id);
//            if($project->client_id != 0) $client_id = $project->client_id;
//
//            $delivered_project_id = $item->project_id;
        }
        else if(!in_array($project_id,['-1','0',-1,0]) && !in_array($client_id,['-1','0',-1,0]))
        {
            $project = DK_Common__Project::find($project_id);
            if(!$project) return response_error([],"项目不存在！");

            $client = DK_Common__Client::find($client_id);
            if(!$client) return response_error([],"客户不存在！");

            $delivered_project_id = $project_id;
        }
        else
        {
            return response_error([],"项目和客户必须同时选择或同时不选！");
        }


        $delivered_description = $post_data["delivered_description"];

        $date = date("Y-m-d");


        $sorted = collect($ids_array)->sort();

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['project_id'] = $project_id;
//            $delivered_para['client_id'] = $client_id;
//            $delivered_para['deliverer_id'] = $me->id;
//            $delivered_para['delivered_status'] = 1;
//            $delivered_para['delivered_result'] = $delivered_result;
//            $delivered_para['delivered_at'] = time();

//            $bool = DK_Common__Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            $count = 0;
            $msg = '';
            $ids = [];
            foreach($sorted as $key => $id)
            {

                $item = DK_Common__Order::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");


                if(in_array($project_id,['-1','0',-1,0]) && in_array($client_id,['-1','0',-1,0]))
                {
                    $project = DK_Common__Project::find($item->project_id);
                    if($project->client_id != 0)
                    {
                        $delivered_client_id = $project->client_id;
                        $client = DK_Common__Client::find($delivered_client_id);
                        if(!$client) return response_error([],"客户不存在！");
                    }
                    else $delivered_client_id = 0;

                    $delivered_project_id = $item->project_id;
                }
                else
                {
                    $delivered_project_id = $project_id;
                    $delivered_client_id = $client_id;
                }


                // 订单重复
                $order_repeated = DK_Common__Order::withTrashed()->where('id','!=',$id)
                    ->where('client_phone',$item->client_phone)
                    ->where('client_id',$delivered_client_id)
                    ->whereNotIn('delivered_result',['拒绝','驳回'])
                    ->get();
                if(count($order_repeated) > 0)
                {
                    $msg = '有部分重复交付';
                    continue;
                }

                // 交付重复
                $delivery_repeated = DK_Common__Delivery::where(['client_id'=>$delivered_client_id,'client_phone'=>$item->client_phone])
                    ->get();
                if(count($delivery_repeated) > 0)
                {
                    $msg = '有部分重复交付';
                    continue;
                }


//                if(!in_array($delivered_client_id,['-1','0',-1,0]) && $delivered_result == "正常交付")
                if(!in_array($delivered_client_id,['-1','0',-1,0]) && in_array($delivered_result,["正常交付","折扣交付","郊区交付","内部交付"]))
                {
                    $pivot_delivery = DK_Common__Delivery::where(['delivery_type'=>1,'order_id'=>$id])->first();
                    if($pivot_delivery)
                    {
                        if($client)
                        {
                            $pivot_delivery->company_id = $client->company_id;
                            $pivot_delivery->channel_id = $client->channel_id;
                            $pivot_delivery->business_id = $client->business_id;
                        }
                        $pivot_delivery->project_id = $delivered_project_id;
                        $pivot_delivery->client_id = $delivered_client_id;
                        $pivot_delivery->delivered_result = $delivered_result;
                        $pivot_delivery->delivered_date = $date;
                        $bool_0 = $pivot_delivery->save();
                        if(!$bool_0) throw new Exception("pivot_client_delivery--update--fail");
                    }
                    else
                    {
                        if($client)
                        {
                            $pivot_delivery_data["company_id"] = $client->company_id;
                            $pivot_delivery_data["channel_id"] = $client->channel_id;
                            $pivot_delivery_data["business_id"] = $client->business_id;
                        }
                        $pivot_delivery = new DK_Common__Delivery;
                        $pivot_delivery_data["order_category"] = $item->order_category;
                        $pivot_delivery_data["pivot_type"] = 95;
                        $pivot_delivery_data["project_id"] = $delivered_project_id;
                        $pivot_delivery_data["client_id"] = $delivered_client_id;
                        $pivot_delivery_data["original_project_id"] = $item->project_id;
                        $pivot_delivery_data["order_id"] = $item->id;
                        $pivot_delivery_data["client_type"] = $item->client_type;
                        $pivot_delivery_data["client_phone"] = $item->client_phone;
                        $pivot_delivery_data["delivered_result"] = $delivered_result;
                        $pivot_delivery_data["delivered_date"] = $date;
                        $pivot_delivery_data["creator_id"] = $me->id;

                        $bool_0 = $pivot_delivery->fill($pivot_delivery_data)->save();
                        if(!$bool_0) throw new Exception("insert--pivot_client_delivery--fail");
                    }
                }


                $before = $item->delivered_result;

                $item->client_id = $delivered_client_id;
                $item->deliverer_id = $me->id;
                $item->delivered_status = 1;
                $item->delivered_result = $delivered_result;
                $item->delivered_description = $delivered_description;
                $item->delivered_at = time();
                $item->delivered_date = $date;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Common__Order__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 71;
                    $record_data["operate_category"] = 95;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "delivered_result";

                    $record_data["before"] = $before;
                    $record_data["after"] = $delivered_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

                $count += 1;
                $ids[] = $id;

            }


            DB::commit();


            $client = DK_Common__Client::find($delivered_client_id);
            if($client->is_automatic_dispatching == 1)
            {
                AutomaticDispatchingJob::dispatch($client->id);
            }

            return response_success(['ids'=>$ids],$msg);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



//        // 启动数据库事务
//        DB::beginTransaction();
//        try
//        {
//            foreach($ids_array as $key => $id)
//            {
//
//                $item = DK_Common__Order::withTrashed()->find($id);
//                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
//
//                if(in_array($client_id,['-1','0',-1,0]))
//                {
//                    $project = DK_Common__Project::find($item->project_id);
//                    if($project->client_id != 0)
//                    {
//                        $delivered_client_id = $project->client_id;
//                    }
//                    else $delivered_client_id = 0;
//                }
//                else
//                {
//                    $delivered_client_id = $client_id;
//                }
//
//                $is_new = 1;
//                if($is_new == 1)
//                {
//                    $client = DK_Common__Client::find($delivered_client_id);
//                    $is_automatic_dispatching = $client->is_automatic_dispatching;
//                    if($is_automatic_dispatching == 1)
//                    {
//
//                        $staff_list = DK_Common__Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
//                            ->where('client_id',$delivered_client_id)
//                            ->where('is_take_order',1)
//                            ->where('is_take_order_date',date('Y-m-d'))
//                            ->orderBy('is_take_order_datetime','asc')
//                            ->get();
//                        $staff_list = $staff_list->values(); // 重置索引确保从0开始
//                        $staffCount = $staff_list->count();
//                        if($staffCount > 0)
//                        {
//                            // 使用原子锁避免并发冲突
//                            $lock = Cache::lock("client:{$delivered_client_id}:assign_lock", 10);
//                            if (!$lock->get())
//                            {
//                                dd(1);
//                            }
//                            else
//                                {
//                                    // 尝试获取锁，最多等待5秒
//                                    $lock->block(5); // 这里会阻塞直到获取锁或超时
//
//                                    // 从缓存获取上次位置（不存在则初始化为0）
//                                    $lastIndex = Cache::get("client:{$delivered_client_id}:last_staff_index", 0);
//                                    $currentIndex = $lastIndex % $staffCount;
//                                    $newIndex = $currentIndex;
//
//                                    $staff = $staff_list[$currentIndex];
//                                    $pivot_delivery->client_staff_id = $staff->id;
//                                    $pivot_delivery->save();
//
//                                    // 计算下一个索引
//                                    $currentIndex = ($currentIndex + 1) % $staffCount;
//                                    $newIndex = $currentIndex; // 记录最后的下一个位置
//
//                                    // 将新位置写入缓存（有效期10小时）
//                                    Cache::put(
//                                        "client:{$delivered_client_id}:last_staff_index",
//                                        $newIndex,
//                                        now()->addHours(10)
//                                    );
//
//                                    optional($lock)->release(); // 确保无论如何都释放锁
//
//                            }
//
//                        }
//                    }
//                }
//            }
//
//            DB::commit();
//            return response_success([]);
//        }
//        catch (Exception $e)
//        {
//            DB::rollback();
////            dd(2);
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//            $msg = '操作失败，请重试！';
//            $msg = $e->getMessage();
////                        exit($e->getMessage());
//            return response_fail([],$msg);
//        }
//        finally
//        {
////                        $lock->release();
////            optional($lock)->release(); // 确保无论如何都释放锁
//        }


    }
    // 【工单】【获取】正常交付记录
    public function operate_item_order_deliver_get_delivered($post_data)
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
        if($operate != 'order-deliver-get-delivered') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该工单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $client_phone = $item->client_phone;


        $order_repeat = DK_Common__Order::select('id','client_id','project_id','client_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'client_er'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); }
            ])->where(['client_phone'=>$client_phone])
//            ->where('id','<>',$id)
//            ->where('delivered_status',1)
            ->where(function ($query) use($id) {
                $query->where('id','<>',$id)
                    ->orWhere(function($query) use($id) { $query->where('id',$id)->where('delivered_status',1); } );
            })
            ->where('is_published','>',0)
            ->get();
        $return['order_repeat'] = $order_repeat;

        $deliver_repeat = DK_Common__Delivery::select('id','client_id','order_id','project_id','client_phone','creator_id')
            ->with([
                'creator'=>function($query) { $query->select('id','name'); },
                'client_er'=>function($query) { $query->select('id','name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); }
            ])->where(['client_phone'=>$client_phone])->get();
        $return['deliver_repeat'] = $deliver_repeat;


        return response_success($return,"");

    }
    // 【工单】下载录音
    public function operate_item_order_download_recording($post_data)
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
        if($operate != 'order-download-recording') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
        $client_phone = $item->client_phone;

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,71,77,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

//        $call_list = DK_CC_Call_Record::select('id','recordFile')->where('staffNo',$item->api_staffNo)->where('callee',$item->client_phone)->get();

        $file_list = [];
        $i['name'] = $item->client_phone.'-'.$item->id.'.mp3';
        $i['path'] = $item->recording_address;
        $i['url'] = $item->recording_address;
        $i['call_record_id'] = $item->call_record_id;
        $file_list[] = $i;

        return response_success(json_encode($file_list));

    }




    // 【api】推送订单
    public function operate_api_push_order($post_data)
    {
        $this->get_me();
        $me = $this->me;
        $customer = $me->customer_er;


        $url = "http://8.142.7.121:9091/api/zl/order?token=zd1a1e02dbe547dt";

//        $API_ID = $customer->api_id;
//        $API_Password = $customer->api_password;
//        $timestamp = time();
//        $seq = $id;
//        $digest = md5($API_ID.'@'.$timestamp.'@'.$seq.'@'.$API_Password);


        $post_data = json_encode($post_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); // post的变量
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            $return['success'] = false;
            $return['msg'] =  "cURL Error: " . curl_error($ch);
        }
        else
        {
            $return['success'] = true;
            $return['result'] =  $result;
        }
        curl_close($ch);
        return $return;
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





    // 【修改记录】返回-列表-数据
    public function get_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Order__Operation_Record::select('*')->withTrashed()
            ->with('creator')
//            ->where(['owner_id'=>100,'item_category'=>100])
//            ->where('item_type', '!=',0);
            ->where(['record_object'=>21,'operate_object'=>71]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");


        // 创建方式 [人工|导入|api]
        if(isset($post_data['operate_type']))
        {
            if(!in_array($post_data['operate_type'],[-1,'-1']))
            {
                $query->where('operate_type', $post_data['operate_type']);
            }
        }

        // 员工
        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $query->where('creator_id', $post_data['staff']);
            }
        }


        if($me->user_type == 11)
        {
            // 总经理
        }
        // 质检经理
        else if($me->user_type == 71)
        {

            $subordinates_array = DK_Common__Staff::select('id')->where('superior_id',$me->id)->get()->pluck('id')->toArray();

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

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
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





}