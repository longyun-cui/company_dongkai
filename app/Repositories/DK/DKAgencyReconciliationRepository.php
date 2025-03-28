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


use App\Models\DK_Reconciliation\DK_Reconciliation_Operation_Record;
use App\Models\DK_Reconciliation\DK_Reconciliation_Project;
use App\Models\DK_Reconciliation\DK_Reconciliation_Daily;
use App\Models\DK_Reconciliation\DK_Reconciliation_Trade_Record;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DKAgencyReconciliationRepository {

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

        $this->view_blade_403 = env('TEMPLATE_DK_AGENCY').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_AGENCY').'entrance.errors.404';

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
    public function view_reconciliation_index()
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


        // 项目统计
        $query_project = DK_Reconciliation_Project::select(DB::raw("
                    count(*) as project_count_for_all,
                    count(IF(item_status = 1, TRUE, NULL)) as project_count_for_enable,
                    sum(funds_recharge_total) as project_sum_for_recharge,
                    sum(funds_revenue_total) as project_sum_for_revenue,
                    sum(funds_bad_debt_total) as project_sum_for_bad_debt,
                    sum(funds_consumption_total) as project_sum_for_consumption,
                    sum(channel_commission_total) as project_sum_for_channel_commission,
                    sum(daily_cost_total) as project_sum_for_daily_cost_total
                "))
            ->where('company_id',$me->id)
            ->first();
        $query_project->profit = $query_project->funds_consumption_total - $query_project->channel_commission_total - $query_project->daily_cost_total;
        $view_data['project'] = $query_project;

        // 交付统计
        $query_daily = DK_Reconciliation_Daily::select(DB::raw("
                    SUM(CASE WHEN assign_date = CURDATE() THEN delivery_quantity ELSE 0 END) AS today_quantity,
                    SUM(CASE  WHEN assign_date BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())  THEN delivery_quantity  ELSE 0  END) AS month_quantity,
                    SUM(delivery_quantity) AS total_quantity
                "))
            ->where('company_id',$me->id)
            ->first();
        $view_data['daily'] = $query_daily;


        $view_blade = env('TEMPLATE_DK_AGENCY').'reconciliation.reconciliation';
        return view($view_blade)->with($view_data);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_DK_AGENCY').'entrance.errors.404';
        return view($view_blade);
    }



    // 【select2】项目
    public function reconciliation_v1_operate_select2_project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query =DK_Reconciliation_Project::select(['id','name as text'])
            ->where('company_id',$me->id)
//            ->where('item_type','!=',11)
            ->where(['item_status'=>1]);

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
        $unSpecified = ['id'=>'-1','text'=>'选择项目'];
        array_unshift($list,$unSpecified);
        return $list;
    }




    /*
     * 项目-管理 Project
     */
    // 【项目-管理】返回-列表-数据
    public function reconciliation_v1_operate_for_project_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Reconciliation_Project::select('*')
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('company_id',$me->id);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['mobile'])) $query->where('mobile', $post_data['mobile']);

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

        if(in_array($me->user_type, [41,71,81]))
        {
            $department_district_id = $me->department_district_id;
            $project_list = DK_Pivot_Team_Project::select('project_id')->where('team_id',$department_district_id)->get();
            $query->whereIn('id',$project_list);

            $department_district_id = $me->department_district_id;
            $inspector_list = DK_User::select('id')->whereIn('user_type',[71,77])->where('department_district_id',$department_district_id)->get();
            $query->with(['pivot_project_user'=>function($query) use($inspector_list) { $query->whereIn('user_id',$inspector_list); }]);
        }
        else
        {
            $query->with(['pivot_project_user','pivot_project_team']);
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
//        else $query->orderBy("name", "asc");
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【项目-管理】获取 GET
    public function reconciliation_v1_operate_for_project_item_get($post_data)
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
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Reconciliation_Project::withTrashed()
            ->with([
            ])
            ->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【项目-管理】保存 SAVE
    public function reconciliation_v1_operate_for_project_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'item_category.required' => '请选择项目种类！',
            'project_id.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'item_category' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:DK_Reconciliation_Project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");

        if($operate_type == 'create')
        {
            // 添加 ( $id==0，添加一个项目 )
            $is_exist = DK_Reconciliation_Project::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【项目】已存在，请勿重复添加！");

            $mine = new DK_Reconciliation_Project;
            $post_data["active"] = 1;
            $post_data["company_id"] = $me->id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit')
        {
            // 编辑
            $mine = DK_Reconciliation_Project::find($operate_id);
            if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");
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
            else throw new Exception("insert--project--fail");

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



    // 【项目-管理】项目日报
    public function reconciliation_v1_operate_for_project_statistic_daily($post_data)
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


        $project_id = $post_data['project_id'];


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $the_date  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');


        $query_this_month = DK_Reconciliation_Daily::select('project_id','assign_date','delivery_quantity','cooperative_unit_price','funds_bad_debt_total','funds_should_settled_total','funds_already_settled_total','channel_commission','daily_cost')
            ->where('project_id',$project_id)
            ->whereBetween('assign_date',[$the_month_start_date,$the_month_ended_date])
//            ->groupBy('assign_date')
            ->addSelect(DB::raw("
                    DATE_FORMAT(assign_date,'%Y-%m-%d') as date_day,
                    DATE_FORMAT(assign_date,'%e') as day
                "))
//            ->addSelect(DB::raw("
//                    count(*) as delivery_count_for_all,
//                    sum(funds_bad_debt_total) as daily_count_for_bad_debt_num,
//                    sum(funds_already_settled_total) as daily_count_for_already_settled_count
//
//                "))
            ->orderBy("assign_date", "desc");

        $total = $query_this_month->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        $list = $query_this_month->get();


        $total_data = [];
        $total_data['project_id'] = $project_id;
        $total_data['date_day'] = '统计';
        $total_data['delivery_quantity'] = 0;
        $total_data['cooperative_unit_price'] = '--';
        $total_data['revenue'] = 0;
        $total_data['profit'] = 0;
        $total_data['funds_bad_debt_total'] = 0;
        $total_data['funds_should_settled_total'] = 0;
        $total_data['funds_already_settled_total'] = 0;
        $total_data['channel_commission'] = 0;
        $total_data['daily_cost'] = 0;


        foreach ($list as $k => $v)
        {
            $revenue = ($v->delivery_quantity * $v->cooperative_unit_price);
            $profit = $revenue - $v->channel_commission - $v->daily_cost;
            $list[$k]['revenue'] = $revenue;
            $list[$k]['profit'] = $profit;
            $funds_should_settled_total = $revenue - $v->funds_bad_debt_total;
            $list[$k]['funds_should_settled_total'] = $funds_should_settled_total;

            $total_data['revenue'] += $revenue;
            $total_data['profit'] += $profit;
            $total_data['delivery_quantity'] += $v->delivery_quantity;
            $total_data['funds_bad_debt_total'] += $v->funds_bad_debt_total;
            $total_data['funds_should_settled_total'] += $funds_should_settled_total;
            $total_data['funds_already_settled_total'] += $v->funds_already_settled_total;
            $total_data['channel_commission'] += $v->channel_commission;
            $total_data['daily_cost'] += $v->daily_cost;
        }
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }







    // 【项目-管理】管理员-删除
    public function reconciliation_v1_operate_for_project_item_delete_by_admin($post_data)
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
        $mine = DK_District::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【地区】不存在，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Department--delete--fail");

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
    // 【项目-管理】管理员-恢复
    public function reconciliation_v1_operate_for_project_item_restore_by_admin($post_data)
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
        $mine = DK_District::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【部门】不存在，刷新页面重试！");
        if($mine->company_id != $me->id) return response_error([],"归属错误，刷新页面重试！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_District--restore--fail");

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
    // 【项目-管理】管理员-彻底删除
    public function reconciliation_v1_operate_for_project_item_delete_permanently_by_admin($post_data)
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
        $mine = DK_District::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【地区】不存在，刷新页面重试！");
        if($mine->company_id != $me->id) return response_error([],"归属错误，刷新页面重试！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_District--delete--fail");

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

    // 【项目-管理】管理员-启用
    public function reconciliation_v1_operate_for_project_item_enable_by_admin($post_data)
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
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Reconciliation_Project::find($id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 1;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Reconciliation_Project--update--fail");

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
    // 【项目-管理】管理员-禁用
    public function reconciliation_v1_operate_for_project_item_disable_by_admin($post_data)
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
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Reconciliation_Project::find($id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_District--update--fail");

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




    // 【项目-管理】充值
    public function reconciliation_v1_operate_for_project_item_recharge_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'transaction-datetime.required' => '请输入成交时间！',
//            'transaction-count.required' => '请输入成交数量！',
            'transaction-amount.required' => '请输入成交金额！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'transaction-datetime' => 'required',
//            'transaction-count' => 'required',
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
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $mine = DK_Reconciliation_Project::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');

        $follow_update = [];


        $trade = new DK_Reconciliation_Trade_Record;

        $trade_data["item_category"] = 1;
        $trade_data["item_id"] = $operate_id;
        $trade_data["project_id"] = $operate_id;

        $trade_data["trade_category"] = 1;
        $trade_data["trade_type"] = 1;
        $trade_data["company_id"] = $me->id;
        $trade_data["creator_id"] = $me->id;

//        $trade_data["title"] = $post_data['transaction-title'];

        $trade_data["transaction_datetime"] = $post_data['transaction-datetime'];
        if(!empty($trade_data["transaction_datetime"]))
        {
            $update['field'] = 'transaction_datetime';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_datetime"];
            $operation_update[] = $update;
        }
        $trade_data["transaction_date"] = $post_data['transaction-datetime'];

//        $trade_data["transaction_count"] = $post_data['transaction-count'];
//        if(!empty($trade_data["transaction_count"]))
//        {
//            $update['field'] = 'transaction_count';
//            $update['before'] = '';
//            $update['after'] = $trade_data["transaction_count"];
//            $follow_update[] = $update;
//        }
        $trade_data["transaction_amount"] = $post_data['transaction-amount'];
        if(!empty($trade_data["transaction_amount"]))
        {
            $update['field'] = 'transaction_amount';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_amount"];
            $operation_update[] = $update;
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
            $operation_update[] = $update;
        }

        $operation_data["custom_text_1"] = json_encode($operation_update);


        $operation_data["item_category"] = 1;
        $operation_data["item_id"] = $operate_id;
        $operation_data["project_id"] = $operate_id;
        $operation_data["operation_category"] = 88;
        $operation_data["operation_type"] = 1;
        $operation_data["company_id"] = $me->id;
        $operation_data["creator_id"] = $me->id;
        $operation_data["operation_datetime"] = $datetime;
        $operation_data["operation_date"] = $datetime;

        $operation = new DK_Reconciliation_Operation_Record();

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool_t = $trade->fill($trade_data)->save();
            if($bool_t)
            {
                $follow_data['custom_id'] = $trade->id;
                $bool_o = $operation->fill($operation_data)->save();
                if($bool_o)
                {
//                    $trade->follow_id = $follow->id;
//                    $bool_t_2 = $trade->save();
//                    if(!$bool_t_2) throw new Exception("DK_Client_Trade_Record--update--fail");


                    $project = DK_Reconciliation_Project::withTrashed()->lockForUpdate()->find($operate_id);
                    $project->timestamps = false;
                    $project->funds_recharge_total += $post_data['transaction-amount'];
                    $project->save();
                    $bool_p = $mine->save();
                    if(!$bool_p) throw new Exception("DK_Reconciliation_Project--update--fail");
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

    // 【每日结算-管理】结算
    public function reconciliation_v1_operate_for_daily_item_settle_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'transaction-datetime.required' => '请输入结算时间！',
//            'transaction-count.required' => '请输入成交数量！',
            'transaction-amount.required' => '请输入结算金额！',
            'transaction-pay-type.required' => '请输入结算方式！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'transaction-datetime' => 'required',
//            'transaction-count' => 'required',
            'transaction-amount' => 'required',
            'transaction-pay-type' => 'required',
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

        $mine = DK_Reconciliation_Daily::withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");
        $project_id = $mine->project_id;


        $datetime = date('Y-m-d H:i:s');

        $operation_update = [];


        $trade = new DK_Reconciliation_Trade_Record;

        $trade_data["item_category"] = 11;
        $trade_data["item_id"] = $operate_id;

        $trade_data["trade_category"] = 21;
        $trade_data["trade_type"] = 21;
        $trade_data["company_id"] = $me->id;
        $trade_data["creator_id"] = $me->id;

//        $trade_data["title"] = $post_data['transaction-title'];

        $trade_data["transaction_pay_type"] = $post_data['transaction-pay-type'];

        $trade_data["transaction_datetime"] = $post_data['transaction-datetime'];
        if(!empty($trade_data["transaction_datetime"]))
        {
            $update['field'] = 'transaction_datetime';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_datetime"];
            $operation_update[] = $update;
        }
        $trade_data["transaction_date"] = $post_data['transaction-datetime'];

//        $trade_data["transaction_count"] = $post_data['transaction-count'];
//        if(!empty($trade_data["transaction_count"]))
//        {
//            $update['field'] = 'transaction_count';
//            $update['before'] = '';
//            $update['after'] = $trade_data["transaction_count"];
//            $follow_update[] = $update;
//        }
        $trade_data["transaction_amount"] = $post_data['transaction-amount'];
        if(!empty($trade_data["transaction_amount"]))
        {
            $update['field'] = 'transaction_amount';
            $update['before'] = '';
            $update['after'] = $trade_data["transaction_amount"];
            $operation_update[] = $update;
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
            $operation_update[] = $update;
        }

        $operation_data["custom_text_1"] = json_encode($operation_update);

        $operation_data["operation_category"] = 88;
        $operation_data["operation_type"] = 21;

        $operation_data["company_id"] = $me->id;

        $operation_data["item_category"] = 11;
        $operation_data["item_id"] = $operate_id;
        $operation_data["project_id"] = $mine->project_id;

        $operation_data["creator_id"] = $me->id;
        $operation_data["operation_datetime"] = $datetime;
        $operation_data["operation_date"] = $datetime;

        $operation = new DK_Reconciliation_Operation_Record;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool_t = $trade->fill($trade_data)->save();
            if($bool_t)
            {
                $operation_data['trade_id'] = $trade->id;
                $bool_o = $operation->fill($operation_data)->save();
                if($bool_o)
                {
                    $trade->operation_id = $operation->id;
                    $bool_t_2 = $trade->save();
                    if(!$bool_t_2) throw new Exception("DK_Client_Trade_Record--update--fail");

                    $mine = DK_Reconciliation_Daily::lockForUpdate()->withTrashed()->find($operate_id);
                    $mine->timestamps = false;
                    $mine->funds_already_settled_total += $post_data['transaction-amount'];
                    $bool_m = $mine->save();
                    if($bool_m)
                    {
                        $project = DK_Reconciliation_Project::withTrashed()->lockForUpdate()->find($project_id);
                        $project->timestamps = false;
                        $project->funds_consumption_total += $post_data['transaction-amount'];
                        $bool_p = $project->save();
                        if(!$bool_p) throw new Exception("DK_Reconciliation_Project--update--fail");
                    }
                    else throw new Exception("DK_Reconciliation_Daily--update--fail");
                }
                else throw new Exception("DK_Reconciliation_Operation_Record--insert--fail");
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





    // 【项目-管理】字段修改
    public function reconciliation_v1_operate_for_project_field_set($post_data)
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


        $item = DK_Project::withTrashed()->find($id);
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
                $is_repeat = DK_Project::where(['name'=>$column_value])->where('id','<>',$id)->count("*");
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
                        $inspector_list_insert[$i] = ['creator_id'=>$me->id,'department_id'=>$me->department_district_id,'relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $item->pivot_project_user()->wherePivot('department_id',$me->department_district_id)->sync($inspector_list_insert);
//                    $mine->pivot_project_user()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $item->pivot_project_user()->wherePivot('department_id',$me->department_district_id)->detach();
                }
            }
            else if($column_key == "client_id")
            {
                if(in_array($column_value,[-1,0,'-1','0']))
                {
                }
                else
                {
                    $client = DK_Client::withTrashed()->find($column_value);
                    if(!$client) throw new Exception("该【客户】不存在，刷新页面重试！");

                    $return['text'] = $client->username;
                }
            }
            else
            {
//            $item->timestamps = false;
                $item->$column_key = $column_value;
                $bool = $item->save();
                if(!$bool) throw new Exception("DK_Project--update--fail");
            }

            if(false) throw new Exception("DK_Project--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
//                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                if(true)
                {
                }
                else
                {
                    $record = new DK_Record;

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
                    else throw new Exception("DK_Record--insert--fail");
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

    // 【每日结算-管理】字段修改
    public function reconciliation_v1_operate_for_daily_field_set($post_data)
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


        $item = DK_Reconciliation_Daily::withTrashed()->find($id);
        if(!$item) return response_error([],"该【结算】不存在，刷新页面重试！");

        $project_id = $item->project_id;

        // 判断对象是否合法
        if(in_array($me->user_type,[84,88]) && $item->creator_id != $me->id) return response_error([],"该【结算】不是你的，你不能操作！");


        $datetime = date('Y-m-d H:i:s');

        $operation_update = [];


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
                return response_error([],"电话没有修改！");
            }
            else if($column_key == "location_city")
            {
                if($item->$column_key2 == $column_select_value2) return response_error([],"地域没有修改！");
            }
            else
            {
                return response_error([],"没有修改！");
            }
        }

        $return['value'] = $column_value;
        $return['text'] = $column_value;


//        if(in_array($column_key,["delivery_quantity","cooperative_unit_price","funds_bad_debt_total"]))
//        {
//            $consumption_before = (($item->delivery_quantity * $item->cooperative_unit_price) - $item->funds_bad_debt_total);
//        }

        if(in_array($column_key,["delivery_quantity","cooperative_unit_price","funds_bad_debt_total","channel_commission","daily_cost"]))
        {
            $revenue_before = ($item->delivery_quantity * $item->cooperative_unit_price);
            $consumption_before = (($item->delivery_quantity * $item->cooperative_unit_price) - $item->funds_bad_debt_total);
            $bad_debt_before = $item->funds_bad_debt_total;
            $channel_commission_before = $item->channel_commission;
            $daily_cost_before = $item->daily_cost;
        }


        $update['field'] = $column_key;
        $update['before'] = $before;
        $update['after'] = $after;
        $operation_update[] = $update;



        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            $item->$column_key = $column_value;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Reconciliation_Daily--update--fail");
            else
            {

//                if(in_array($column_key,["delivery_quantity","cooperative_unit_price","funds_bad_debt_total"]))
//                {
//                    $consumption_after = (($item->delivery_quantity * $item->cooperative_unit_price) - $item->funds_bad_debt_total);
//
//                    $project = DK_Reconciliation_Project::withTrashed()->lockForUpdate()->find($project_id);
//                    $project->timestamps = false;
//                    $project->funds_consumption_total = ($project->funds_consumption_total - $consumption_before + $consumption_after);
//                    $bool_p = $project->save();
//                    if(!$bool_p) throw new Exception("DK_Reconciliation_Project--update--fail");
//                }


                if(in_array($column_key,["delivery_quantity","cooperative_unit_price","funds_bad_debt_total","channel_commission","daily_cost"]))
                {
                    $revenue_after = ($item->delivery_quantity * $item->cooperative_unit_price);
                    $consumption_after = (($item->delivery_quantity * $item->cooperative_unit_price) - $item->funds_bad_debt_total);
                    $bad_debt_after = $item->funds_bad_debt_total;
                    $channel_commission_after = $item->channel_commission;
                    $daily_cost_after = $item->daily_cost;

                    $project = DK_Reconciliation_Project::withTrashed()->lockForUpdate()->find($project_id);
                    $project->timestamps = false;
//                    $project->funds_revenue_total = ($project->funds_revenue_total - $revenue_before + $revenue_after);
//                    $project->funds_consumption_total = ($project->funds_consumption_total - $consumption_before + $consumption_after);
//                    $project->funds_bad_debt_total = ($project->funds_bad_debt_total - $bad_debt_before + $bad_debt_after);
//                    $project->channel_commission_total = ($project->channel_commission_total - $channel_commission_before + $channel_commission_after);
//                    $project->daily_cost_total = ($project->daily_cost_total - $daily_cost_before + $daily_cost_after);
                    if($column_key == "delivery_quantity")
                    {
                        $project->funds_revenue_total = ($project->funds_revenue_total - $revenue_before + $revenue_after);
                        $project->funds_consumption_total = ($project->funds_consumption_total - $consumption_before + $consumption_after);
                    }
                    else if($column_key == "cooperative_unit_price")
                    {
                        $project->funds_revenue_total = ($project->funds_revenue_total - $revenue_before + $revenue_after);
                        $project->funds_consumption_total = ($project->funds_consumption_total - $consumption_before + $consumption_after);
                    }
                    else if($column_key == "funds_bad_debt_total")
                    {
                        $project->funds_consumption_total = ($project->funds_consumption_total - $consumption_before + $consumption_after);
                        $project->funds_bad_debt_total = ($project->funds_bad_debt_total - $bad_debt_before + $bad_debt_after);
                    }
                    else if($column_key == "channel_commission")
                    {
                        $project->channel_commission_total = ($project->channel_commission_total - $channel_commission_before + $channel_commission_after);
                    }
                    else if($column_key == "daily_cost")
                    {
                        $project->daily_cost_total = ($project->daily_cost_total - $daily_cost_before + $daily_cost_after);
                    }
                    $bool_p = $project->save();
                    if(!$bool_p) throw new Exception("DK_Reconciliation_Project--update--fail");
                }




                $return['item'] = $item;

                // 需要记录(已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $operation_data["custom_text_1"] = json_encode($operation_update);

                    $operation_data["operation_category"] = 1;
                    $operation_data["operation_type"] = 1;

                    $operation_data["company_id"] = $me->id;

                    $operation_data["item_category"] = 11;
                    $operation_data["item_id"] = $id;
                    $operation_data["project_id"] = $item->project_id;

                    $operation_data["creator_id"] = $me->id;
                    $operation_data["operation_datetime"] = $datetime;
                    $operation_data["operation_date"] = $datetime;

                    $operation = new DK_Reconciliation_Operation_Record;


                    if(in_array($column_key,['client_id','project_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }



                    $bool_o = $operation->fill($operation_data)->save();
                    if($bool_o)
                    {
                    }
                    else throw new Exception("DK_Reconciliation_Operation_Record--insert--fail");
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





    /*
     * 每日结算-管理 Daily
     */
    // 【每日结算-管理】返回-列表-数据
    public function reconciliation_v1_operate_for_daily_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Reconciliation_Daily::select('*')
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'project_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('company_id',$me->id);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

//        if(!empty($post_data['assign_date'])) $query->where('assign_date', $post_data['assign_date']);

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


        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('assign_date',$the_day);
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

            $query->whereBetween('assign_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('assign_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('assign_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }

        // 项目
        if(isset($post_data['project']))
        {
            if(!in_array($post_data['project'],[-1,0,'-1','0']))
            {
                $query->whereIn('project_id', $post_data['project']);
            }
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
//        else $query->orderBy("name", "asc");
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        $total_data = [];
        $total_data['id'] = '统计';
        $total_data['project_id'] = '--';
        $total_data['assign_date'] = '--';
        $total_data['date_day'] = '统计';
        $total_data['delivery_quantity'] = 0;
        $total_data['cooperative_unit_price'] = '--';
        $total_data['revenue'] = 0;
        $total_data['profit'] = 0;
        $total_data['funds_bad_debt_total'] = 0;
        $total_data['funds_should_settled_total'] = 0;
        $total_data['funds_already_settled_total'] = 0;
        $total_data['to_be_settled'] = 0;
        $total_data['channel_commission'] = 0;
        $total_data['daily_cost'] = 0;
        $total_data['remark'] = '';
        $total_data['creator_id'] = '';
        $total_data['updated_at'] = '';


        foreach ($list as $k => $v)
        {
            $revenue = ($v->delivery_quantity * $v->cooperative_unit_price);
            $bad_debt = $v->funds_bad_debt_total;
            $profit = $revenue - $bad_debt - $v->channel_commission - $v->daily_cost;
            $list[$k]['revenue'] = $revenue;
            $list[$k]['profit'] = $profit;
            $funds_should_settled_total = $revenue - $v->funds_bad_debt_total;
            $list[$k]['funds_should_settled_total'] = $funds_should_settled_total;


            $total_data['delivery_quantity'] += $v->delivery_quantity;
            $total_data['funds_bad_debt_total'] += $v->funds_bad_debt_total;
            $total_data['funds_should_settled_total'] += $funds_should_settled_total;
            $total_data['funds_already_settled_total'] += $v->funds_already_settled_total;
            $total_data['channel_commission'] += $v->channel_commission;
            $total_data['daily_cost'] += $v->daily_cost;

            $total_data['revenue'] += $revenue;
            $total_data['profit'] += $profit;
            $to_be_settled = $funds_should_settled_total - $v->funds_already_settled_total;
            $total_data['to_be_settled'] += $to_be_settled;
        }
        $list[] = $total_data;

        return datatable_response($list, $draw, $total);
    }
    // 【每日结算-管理】获取 GET
    public function reconciliation_v1_operate_for_daily_item_get($post_data)
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
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Reconciliation_Daily::withTrashed()
            ->with([
                'project_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【每日结算-管理】保存 SAVE
    public function reconciliation_v1_operate_for_daily_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'item_category.required' => '请选择项目种类！',
            'project_id.required' => '请填选择项目！',
            'project_id.numeric' => '选择项目参数有误！',
            'project_id.min' => '请填选择项目！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'item_category' => 'required',
            'project_id' => 'required|numeric|min:1',
//            'name' => 'required|unique:DK_Reconciliation_Project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");

        if($operate_type == 'create')
        {
            // 添加 ( $id==0，添加一个项目 )
            $is_exist = DK_Reconciliation_Daily::select('id')
                ->where('project_id',$post_data["project_id"])
                ->where('assign_date',$post_data["assign_date"])
                ->count();
            if($is_exist) return response_error([],"该【项目】此日的结算已存在，请勿重复添加！");

            $mine = new DK_Reconciliation_Daily;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["company_id"] = $me->id;
        }
        else if($operate_type == 'edit')
        {
            // 编辑
            $mine = DK_Reconciliation_Daily::find($operate_id);
            if(!$mine) return response_error([],"该【日报】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");

        $project_id = $post_data["project_id"];
        $project = DK_Reconciliation_Project::find($project_id);
        if($project)
        {
            $post_data['cooperative_unit_price'] = $project->cooperative_unit_price;
        }
        else return response_error([],"选择的【项目】不存在，刷新页面重试！");


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
                $project = DK_Reconciliation_Project::withTrashed()->lockForUpdate()->find($project_id);
                $project->timestamps = false;
                $project->funds_revenue_total += ($post_data['delivery_quantity'] * $project->cooperative_unit_price);
                $project->funds_consumption_total += ($post_data['delivery_quantity'] * $project->cooperative_unit_price);
                $project->channel_commission_total += ($post_data['channel_commission']);
                $project->daily_cost_total += ($post_data['daily_cost']);
                $bool_p = $project->save();
                if(!$bool_p) throw new Exception("DK_Reconciliation_Project--update--fail");
            }
            else throw new Exception("DK_Reconciliation_Daily--insert--fail");

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

















    /*
     * 联系渠道管理
     */
    // 【交易-员工管理】返回-列表-数据
    public function reconciliation_v1_operate_for_trade_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Reconciliation_Trade_Record::select('*')
            ->withTrashed()
            ->with([
                'delivery_er',
                'creator'=>function($query) { $query->select(['id','name']); },
                'deleter_er'=>function($query) { $query->select(['id','name']); },
                'authenticator_er'=>function($query) { $query->select(['id','name','true_name']); }
            ])
            ->where('company_id',$me->id)
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
    public function reconciliation_v1_operate_for_trade_item_save($post_data)
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
            $is_exist = DK_Client_Contact::select('id')->where('name',$post_data["name"])->where('company_id',$me->id)->count();
            if($is_exist) return response_error([],"该【名称】已存在，请勿重复添加！");

            $mine = new DK_Client_Contact;
            $post_data["active"] = 1;
            $post_data["company_id"] = $me->id;
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
    public function reconciliation_v1_operate_for_trade_item_get($post_data)
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
    public function reconciliation_v1_operate_for_trade_item_delete($post_data)
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
    public function reconciliation_v1_operate_for_trade_item_confirm($post_data)
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









    // 【工单-管理】【操作记录】返回-列表-数据
    public function reconciliation_v1_operate_for_item_operation_record_datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Reconciliation_Operation_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['item_id'=>$id])
            ->when(($post_data['item_category'] == 'reconciliation-project'), function ($query) use ($id) {
//                return $query->where('item_id', $id);
                return $query->where('item_category', 1);
            })
            ->when(($post_data['item_category'] == 'reconciliation-daily'), function ($query) use ($id) {
//                return $query->where('daily_id', $id);
                return $query->where('item_category', 11);
            });
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



}