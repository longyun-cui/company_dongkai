<?php
namespace App\Repositories\YH;

use App\Models\YH\YH_User;
use App\Models\YH\YH_UserExt;
use App\Models\YH\YH_Client;
use App\Models\YH\YH_Car;
use App\Models\YH\YH_Route;
use App\Models\YH\YH_Pricing;
use App\Models\YH\YH_Order;
use App\Models\YH\YH_Circle;
use App\Models\YH\YH_Attachment;
use App\Models\YH\YH_Finance;
use App\Models\YH\YH_Record;
use App\Models\YH\YH_Item;
use App\Models\YH\YH_Task;
use App\Models\YH\YH_Pivot_Circle_Order;
use App\Models\YH\YH_Pivot_Item_Relation;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class YHAdminRepository {

    private $evn;
    private $auth_check;
    private $me;
    private $me_admin;
    private $model;
    private $modelUser;
    private $modelItem;
    private $repo;
    private $service;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new YH_User;
        $this->modelItem = new YH_Item;
        $this->modelClinet = new YH_Client;
        $this->modelCar = new YH_Car;

        $this->view_blade_404 = env('TEMPLATE_YH_ADMIN').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("yh_admin")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("yh_admin")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);
    }




    // 返回（后台）主页视图
    public function view_admin_index()
    {
        $this->get_me();

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);


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


        $query = YH_Car::select('id');


        // 车辆统计
        $car_count_for_all = YH_Car::count("*");
        $car_count_for_car = YH_Car::where('item_type',1)->count("*");
        $car_count_for_trailer = YH_Car::where('item_type',21)->count("*");
        $return['car_count_for_all'] = $car_count_for_all;
        $return['car_count_for_car'] = $car_count_for_car;
        $return['car_count_for_trailer'] = $car_count_for_trailer;

        // 空闲车辆
        $car_idle_query = YH_Car::select('id')
            ->whereDoesntHave('car_order_list_for_current', function ($query) {
                $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
            })
            ->whereDoesntHave('trailer_order_list_for_current', function ($query) {
                $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
            })
            ->whereDoesntHave('car_order_list_for_future', function ($query) {
                $query->whereNull('actual_departure_time');
            })
            ->whereDoesntHave('trailer_order_list_for_future', function ($query) {
                $query->whereNull('actual_departure_time');
            });
        $car_idle_query_for_all = clone $car_idle_query;
        $car_idle_query_for_car = clone $car_idle_query;
        $car_idle_query_for_trailer = clone $car_idle_query;
        $idle_count_for_all = (clone $car_idle_query)->count("*");
        $idle_count_for_car = (clone $car_idle_query)->where('item_type',1)->count("*");
        $idle_count_for_trailer = (clone $car_idle_query)->where('item_type',21)->count("*");
        $return['idle_count_for_all'] = $idle_count_for_all;
        $return['idle_count_for_car'] = $idle_count_for_car;
        $return['idle_count_for_trailer'] = $idle_count_for_trailer;

        // 工作中
        $car_working_query = YH_Car::select('id')
            ->where(function ($query){
                $query->whereHas('car_order_list_for_current',function($query) {
                        $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                    })
                    ->orWhereHas('trailer_order_list_for_current',function($query) {
                        $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                    });
            });
        $car_working_query_for_all = clone $car_working_query;
        $car_working_query_for_car = clone $car_working_query;
        $car_working_query_for_trailer = clone $car_working_query;
        $working_count_for_all = (clone $car_working_query)->count("*");
        $working_count_for_car = (clone $car_working_query)->where('item_type',1)->count("*");
        $working_count_for_trailer = (clone $car_working_query)->where('item_type',21)->count("*");
        $return['working_count_for_all'] = $working_count_for_all;
        $return['working_count_for_car'] = $working_count_for_car;
        $return['working_count_for_trailer'] = $working_count_for_trailer;

        // 待发车
        $car_waiting_for_departure_query = YH_Car::select('id')
            ->where(function ($query) {
                $query->whereDoesntHave('car_order_list_for_current', function ($query) {
                        $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                    })
                    ->whereDoesntHave('trailer_order_list_for_current', function ($query) {
                        $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                    });
            })
            ->where(function ($query) {
                $query->whereHas('car_order_list_for_future', function ($query) {
                    $query->whereNull('actual_departure_time');
                })
                    ->orWhereHas('trailer_order_list_for_future', function ($query) {
                        $query->whereNull('actual_departure_time');
                    });
            });
        $car_waiting_query_for_all = clone $car_waiting_for_departure_query;
        $car_waiting_query_for_car = clone $car_waiting_for_departure_query;
        $car_waiting_query_for_trailer = clone $car_waiting_for_departure_query;
        $waiting_count_for_all = (clone $car_waiting_for_departure_query)->count("*");
        $waiting_count_for_car = (clone $car_waiting_for_departure_query)->where('item_type',1)->count("*");
        $waiting_count_for_trailer = (clone $car_waiting_for_departure_query)->where('item_type',21)->count("*");
        $return['waiting_count_for_all'] = $waiting_count_for_all;
        $return['waiting_count_for_car'] = $waiting_count_for_car;
        $return['waiting_count_for_trailer'] = $waiting_count_for_trailer;








        // 订单统计
        $order_count_for_all = YH_Order::count("*");
        $order_count_for_unpublished = YH_Order::where('is_published', 0)->count("*");
        $order_count_for_waiting_for_departure = YH_Order::where('is_published', 1)->whereNotNull('actual_departure_time')->count("*");
        $order_count_for_working = YH_Order::where('is_published', 1)->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time')->count("*");
        $order_count_for_waiting_for_receipt = YH_Order::where('is_published', 1)->whereNotNull('actual_arrival_time')->whereColumn(DB::raw('amount + oil_card_amount - time_limitation_deduction'),'>','income_total')->count("*");
        $order_count_for_received = YH_Order::where('is_published', 1)->whereNotNull('actual_arrival_time')->whereColumn(DB::raw('amount + oil_card_amount - time_limitation_deduction'), '<=', 'income_total')->count("*");


        $return['order_count_for_all'] = $order_count_for_all;
        $return['order_count_for_unpublished'] = $order_count_for_unpublished;
        $return['order_count_for_waiting_for_departure'] = $order_count_for_waiting_for_departure;
        $return['order_count_for_working'] = $order_count_for_working;
        $return['order_count_for_waiting_for_receipt'] = $order_count_for_waiting_for_receipt;
        $return['order_count_for_received'] = $order_count_for_received;




        // 本月每日订单量
        $statistics_order_this_month_data = YH_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "))
            ->get()->keyBy('day');
        $return['statistics_order_this_month_data'] = $statistics_order_this_month_data;

        // 上月每日订单量
        $statistics_order_last_month_data = YH_Order::select('id','assign_time')
//            ->where('finance_type',1)
            ->whereBetween('assign_time',[$last_month_start_timestamp,$last_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(assign_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(assign_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(assign_time,'%e') as day,
                    count(*) as sum
                "))
            ->get()->keyBy('day');
        $return['statistics_order_last_month_data'] = $statistics_order_last_month_data;




        // 财务统计

        $finance_this_month_income = YH_Finance::select('id')
            ->where('finance_type',1)
            ->whereBetween('transaction_time',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->sum("transaction_amount");

        $finance_this_month_payout = YH_Finance::select('id')
            ->where('finance_type',21)
            ->whereBetween('transaction_time',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->sum("transaction_amount");


        $finance_last_month_income = YH_Finance::select('id')
            ->where('finance_type',1)
            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$last_month_start_date,$last_month_ended_date])
            ->sum("transaction_amount");

        $finance_last_month_payout = YH_Finance::select('id')
            ->where('finance_type',21)
            ->whereBetween(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"),[$last_month_start_date,$last_month_ended_date])
            ->sum("transaction_amount");


        $return['finance_this_month_income'] = $finance_this_month_income;
        $return['finance_this_month_payout'] = $finance_this_month_payout;
        $return['finance_last_month_income'] = $finance_last_month_income;
        $return['finance_last_month_payout'] = $finance_last_month_payout;


        $statistics_income_data = YH_Finance::select('id','transaction_amount','transaction_time','created_at')
            ->where('finance_type',1)
            ->whereBetween('transaction_time',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(transaction_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(transaction_time,'%e') as day,
                    sum(transaction_amount) as sum,
                    count(*) as count
                "))
            ->get()->keyBy('day');
        $return['statistics_income_data'] = $statistics_income_data;

        $statistics_payout_data = YH_Finance::select('id','transaction_amount','transaction_time','created_at')
            ->where('finance_type',21)
            ->whereBetween('transaction_time',[$this_month_start_timestamp,$this_month_ended_timestamp])
            ->groupBy(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"))
            ->select(DB::raw("
                    FROM_UNIXTIME(transaction_time,'%Y-%m-%d') as date,
                    FROM_UNIXTIME(transaction_time,'%e') as day,
                    sum(transaction_amount) as sum,
                    count(*) as count
                "))
            ->get()->keyBy('day');
        $return['statistics_payout_data'] = $statistics_payout_data;




        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.errors.404';
        return view($view_blade);
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

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }
    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.my-account.my-profile-info-edit';
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

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.my-account.my-account-password-change';
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








    /*
     * USER 用户管理
     */
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
//        if(!is_numeric($type)) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =YH_User::select(['id','username as text'])
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_User::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_category'=>11])->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }

    // 【用户-员工管理】返回-添加-视图
    public function view_user_staff_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list-for-all';

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.staff-edit';
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
    // 【用户-员工管理】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.staff-edit';

        $item_type = 'item';
        $item_type_text = '用户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list-for-all';

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
            $mine = YH_User::with(['parent'])->find($id);
            if($mine)
            {
                if(!in_array($mine->user_category,[0,1,9,11,21,22])) return view(env('TEMPLATE_YH_ADMIN').'entrance.errors.404');
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
            else return view(env('TEMPLATE_YH_ADMIN').'entrance.errors.404');
        }
    }
    // 【用户-员工管理】保存数据
    public function operate_user_staff_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误',
            'true_name.required' => '请输入用户名',
            'mobile.required' => '请输入电话',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'true_name' => 'required',
            'mobile' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new YH_User;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["creator_id"] = $me->id;
            $post_data['username'] = $post_data['true_name'];
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
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

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
                    $user_ext = new YH_UserExt;
                    $user_ext_create['user_id'] = $mine->id;
                    $bool_2 = $user_ext->fill($user_ext_create)->save();
                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
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
                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$mine->id,'yh/unique/portrait_for_user','');
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
                        $portrait_path = "yh/unique/portrait_for_user/".date('Y-m-d');
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

        $user = YH_User::withTrashed()->find($id);
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

        $user = YH_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");
//        if(in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该员工的操作权限！");
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

        $user = YH_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

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

        $user = YH_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

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

        $user = YH_User::withTrashed()->find($id);
        if(!$user) return response_error([],"该员工不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

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

        $user = YH_User::find($id);
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

        $user = YH_User::find($id);
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




    // 【用户-员工管理】返回-列表-视图
    public function view_staff_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['sidebar_staff_list_for_all_active'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.staff-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【用户-员工管理】返回-列表-数据
    public function get_staff_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_User::select('*')
            ->with(['creator'])
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[0,1,9,11,19,21,22,41,42,61,81,82,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

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
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【用户】【个人用户】返回-列表-视图
    public function view_user_list_for_individual($post_data)
    {
        return view(env('TEMPLATE_YH_ADMIN').'entrance.user.user-list-for-individual')
            ->with(['sidebar_user_list_for_individual_active'=>'active menu-open']);
    }
    // 【用户】【个人用户】返回-列表-数据
    public function get_user_list_for_individual_datatable($post_data)
    {
        $me = Auth::guard("staff_admin")->user();
        $query = User::select('*')
            ->with(['district'])
            ->where(['active'=>1,'user_category'=>1,'user_type'=>1]);

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【用户】【组织】返回-列表-视图
    public function view_user_list_for_org($post_data)
    {
        return view(env('TEMPLATE_YH_ADMIN').'entrance.user.user-list-for-org')
            ->with(['sidebar_user_list_for_org_active'=>'active menu-open']);
    }
    // 【用户】【组织】返回-列表-数据
    public function get_user_list_for_org_datatable($post_data)
    {
        $me = Auth::guard("staff_admin")->user();
        $query = User::select('*')
            ->with(['district'])
            ->where(['active'=>1,'user_category'=>11]);

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
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }









    /*
     * 客户管理
     */
    // 【客户管理】返回-添加-视图
    public function view_user_client_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/client-list-for-all';

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.client-edit';
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
    // 【客户管理】返回-编辑-视图
    public function view_user_client_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.client-edit';

        $item_type = 'item';
        $item_type_text = '客户';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/client-list-for-all';

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
            $mine = YH_Client::with(['parent'])->find($id);
            if($mine)
            {
                if(!in_array($mine->user_category,[0,1,9,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
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
            else return view(env('TEMPLATE_YH_ADMIN').'errors.404');
        }
    }
    // 【客户管理】保存数据
    public function operate_user_client_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'username.required' => '请输入客户名称！',
//            'username.unique' => '该客户已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
//            'username' => 'required|unique:yh_client,username',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = YH_Client::select('id')->where('username',$post_data["username"])->count();
            if($is_exist) return response_error([],"该客户名已存在，请勿重复添加！");

            $mine = new YH_Client;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Client::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
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

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
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


    // 【客户管理】管理员-启用
    public function operate_user_client_admin_enable($post_data)
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
        if($operate != 'client-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = YH_Client::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 1;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--client--fail");

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
    // 【客户管理】管理员-禁用
    public function operate_user_client_admin_disable($post_data)
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
        if($operate != 'client-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $user = YH_Client::find($id);
        if(!$user) return response_error([],"该【客户】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->user_status = 9;
            $user->timestamps = false;
            $bool = $user->save();
            if(!$bool) throw new Exception("update--client--fail");

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


    // 【客户管理管理】返回-列表-视图
    public function view_user_client_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_client_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.user.client-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【用户-员工管理】返回-列表-数据
    public function get_user_client_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Client::select('*')
            ->with(['creator'])
            ->whereIn('user_category',[11])
            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

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
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * ITEM 内容管理
     */
    // 【内容】【全部】返回-列表-视图
    public function view_item_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_item_list'] = 'active menu-open';
        $return['menu_active_of_item_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.item-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【内容】【全部】返回-列表-数据
    public function get_item_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;
        $query = YH_Item::select('*')
//            ->withTrashed()
            ->with('owner','creator')
            ->where(['item_category'=>11])
            ->where('item_type', '!=',0);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");

        $item_type = isset($post_data['item_type']) ? $post_data['item_type'] : '-1';
        if(in_array($item_type,[1,11,41,42,99,101])) $query->where('item_type', $item_type);

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
            $list[$k]->custom = json_decode($v->custom,true);
//            $list[$k]->description = replace_blank($v->description);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【内容】【全部】返回-列表-视图
    public function view_task_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $sales = YH_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[41,61,88])->get();

        $return['sales'] = $sales;
        $return['menu_active_of_task_list'] = 'active';
        $return['menu_active_of_task_list_for_all'] = 'active';

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.task-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【内容】【全部】返回-列表-数据
    public function get_task_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Task::select('*')
//            ->withTrashed()
            ->with('owner','creator');
//            ->where('item_category',11)
//            ->where('item_type', '!=',0)
//            ->withCount([
//                'order_list as order_'=>function($query) {
//                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time')->orderby('id','desc');
//                }
//            ]);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->with('ep','parent','fund')
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->where('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");

        $item_type = isset($post_data['item_type']) ? $post_data['item_type'] : '';
        if($item_type == "article") $query->where('item_type', 1);
        else if($item_type == "menu_type") $query->where('item_type', 11);
        else if($item_type == "time_line") $query->where('item_type', 18);
        else if($item_type == "debase") $query->where('item_type', 22);
        else if($item_type == "vote") $query->where('item_type', 29);
        else if($item_type == "ask") $query->where('item_type', 31);



        $owner_id = isset($post_data['finished']) ? $post_data['owner'] : '';
        if(!in_array($owner_id,[-1,0])) $query->where('owner_id', $owner_id);

        $is_completed = isset($post_data['finished']) ? $post_data['finished'] : '';
        if($is_completed == 0) $query->where('is_completed', 0);
        else if($is_completed == 1) $query->where('is_completed', 1);

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
            $list[$k]->custom = json_decode($v->custom,true);
//            $list[$k]->description = replace_blank($v->description);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * ITEM 内容管理
     */
    // 【内容】返回-添加-视图
    public function view_item_item_create()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '内容';
        $title_text = '添加'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/item-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.item-edit';
        return view($view_blade)->with($return);
    }
    // 【内容】返回-编辑-视图
    public function view_item_item_edit($post_data)
    {
        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,21,22])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');

        $id = $post_data["item-id"];
        $mine = $this->modelItem->with(['owner'])->find($id);
        if(!$mine) return view(env('TEMPLATE_YH_ADMIN').'errors.404');


        $operate_category = 'item';

        if($mine->item_type == 0)
        {
            $operate_type = 'item';
            $operate_type_text = '内容';
            $list_link = '/home/item/item-list-for-all';
        }
        else if($mine->item_type == 1)
        {
            $operate_type = 'article';
            $operate_type_text = '文章';
            $list_link = '/home/item/item-article-list';
        }
        else if($mine->item_type == 9)
        {
            $operate_type = 'activity';
            $operate_type_text = '活动';
            $list_link = '/home/item/item-list-for-activity';
        }
        else if($mine->item_type == 11)
        {
            $operate_type = 'menu_type';
            $operate_type_text = '书目';
            $list_link = '/home/item/item-list-for-menu_type';
        }
        else if($mine->item_type == 18)
        {
            $operate_type = 'time_line';
            $operate_type_text = '时间线';
            $list_link = '/home/item/item-list-for-time_line';
        }
        else if($mine->item_type == 88)
        {
            $operate_type = 'advertising';
            $operate_type_text = '广告';
            $list_link = '/home/item/item-list-for-advertising';
        }
        else
        {
            $operate_type = 'item';
            $operate_type_text = '内容';
            $list_link = '/admin/item/item-list';
        }

        $title_text = '编辑'.$operate_type_text;
        $list_text = $operate_type_text.'列表';


        $return['operate_id'] = $id;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.item-edit';
        if($id == 0)
        {
            $return['operate'] = 'create';
            return view($view_blade)->with($return);
        }
        else
        {
            $mine = $this->modelItem->with(['owner'])->find($id);
            if($mine)
            {
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['operate'] = 'edit';
                $return['data'] = $mine;
                return view($view_blade)->with($return);
            }
            else return response("该内容不存在！", 404);
        }
    }
    // 【内容】保存-数据
    public function operate_item_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required',
            'title.required' => '请输入标题！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'title' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return response_error([],"用户类型错误！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];
        $operate_category = $post_data["operate_category"];
        $operate_type = $post_data["operate_type"];

        if($operate == 'create') // 添加 ( $id==0，添加一个内容 )
        {
            $mine = new YH_Item;
            $post_data["item_category"] = 1;
            $post_data["owner_id"] = $me->id;
            $post_data["creator_id"] = $me->id;
            $post_data["item_category"] = 11;

//            if($type == 'item') $post_data["item_type"] = 0;
//            else if($type == 'article') $post_data["item_type"] = 1;
//            else if($type == 'activity') $post_data["item_type"] = 9;
//            else if($type == 'menu_type') $post_data["item_type"] = 11;
//            else if($type == 'time_line') $post_data["item_type"] = 18
//            else if($type == 'advertising') $post_data["item_type"] = 88;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = $this->modelItem->find($operate_id);
            if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");
//            if($me->id != $me_admin->id)
//            {
//                if($mine->creator_id != $me_admin->id) return response_error([],"不是你创建的，你没有操作权限！");
//            }
//            $post_data["updater_id"] = $me_admin->id;
        }
        else return response_error([],"参数【operate】有误！");

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
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            if(!empty($post_data['start'])) {
                $mine_data['time_type'] = 1;
                $mine_data['start_time'] = strtotime($post_data['start']);
            }

            if(!empty($post_data['end'])) {
                $mine_data['time_type'] = 1;
                $mine_data['end_time'] = strtotime($post_data['end']);
            }

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {

                // 封面图片
                if(!empty($post_data["cover"]))
                {
                    // 删除原封面图片
                    $mine_cover_pic = $mine->cover_pic;
                    if(!empty($mine_cover_pic) && file_exists(storage_resource_path($mine_cover_pic)))
                    {
                        unlink(storage_resource_path($mine_cover_pic));
                    }

                    $result = upload_img_storage($post_data["cover"],'','yh/common');
                    if($result["result"])
                    {
                        $mine->cover_pic = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--cover_pic--fail");
                }

                // 附件
                if(!empty($post_data["attachment"]))
                {
                    // 删除原附件
                    $mine_cover_pic = $mine->attachment;
                    if(!empty($mine_cover_pic) && file_exists(storage_resource_path($mine_cover_pic)))
                    {
                        unlink(storage_resource_path($mine_cover_pic));
                    }

                    $result = upload_file_storage($post_data["attachment"],'','attachment');
                    if($result["result"])
                    {
                        $mine->attachment_name = $result["name"];
                        $mine->attachment_src = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--attachment_file--fail");
                }

            }
            else throw new Exception("insert--item--fail");

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


    // 【内容】获取详情
    public function operate_item_item_get($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => '请输入ID！',
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

        $item = Def_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        return response_success($item,"");

    }


    // 【内容】删除
    public function operate_item_item_delete($post_data)
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
        if($operate != 'item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Item::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->owner_id != $me->id) return response_error([],"你没有该内容的操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($item->item_active == 0 && $item->owner_id != $me->id)
            {
                $item_copy = $item;

                $item->timestamps = false;
                $bool = $item->forceDelete();
                $bool = $item->delete();  // 普通删除
                if(!$bool) throw new Exception("item--delete--fail");
                DB::commit();

                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $item->timestamps = false;
//                $bool = $item->delete();  // 普通删除
                $bool = $item->forceDelete();  // 永久删除
                if(!$bool) throw new Exception("item--delete--fail");
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
    // 【内容】恢复
    public function operate_item_item_restore($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-restore') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("item--restore--fail");
            DB::commit();

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
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
    // 【内容】彻底删除
    public function operate_item_item_delete_permanently($post_data)
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
        if($operate != 'item-delete-permanently') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($id == $me->advertising_id)
            {
                $me->timestamps = false;
                $me->advertising_id = 0;
                $bool_0 = $me->save();
                if(!$bool_0) throw new Exception("update--user--fail");
            }

            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("item--delete--fail");
            DB::commit();

            $this->delete_the_item_files($item_copy);

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


    // 【内容】批量-删除
    public function operate_item_item_delete_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->delete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-删除
    public function operate_item_item_restore_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->restore();
                    if(!$bool) throw new Exception("item--restore--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-彻底删除
    public function operate_item_item_delete_permanently_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->forceDelete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }


    // 【内容】发布
    public function operate_item_item_publish($post_data)
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
        if($operate != 'item-publish') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = Def_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_published = 1;
            $item->published_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            DB::commit();

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
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
    // 【内容】完成
    public function operate_item_item_complete($post_data)
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
        if($operate != 'item-complete') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DEF_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19,41])) return response_error([],"用户类型错误！");
        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

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
            DB::commit();

            $item->custom = json_decode($item->custom);
            $item_array[0] = $item;
            $return['item_list'] = $item_array;
            $item_html = view(env('TEMPLATE_STAFF_FRONT').'component.item-list')->with($return)->__toString();
            return response_success(['item_html'=>$item_html]);
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


    // 【内容】启用
    public function operate_item_item_enable($post_data)
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
        if($operate != 'item-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Item::find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--item--fail");

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
    // 【内容】禁用
    public function operate_item_item_disable($post_data)
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
        if($operate != 'item-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Item::find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--item--fail");

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

    // 【ITEM】批量-操作（全部操作）
    public function operate_item_item_operate_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
            'bulk_item_operate.required' => 'bulk_item_operate.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
            'bulk_item_operate' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);

        $this->get_me();
        $me = $this->me;

        $item_operate = $post_data["bulk_item_operate"];
        if(!in_array($item_operate,['启用','禁用','删除','永久删除'])) return response_error([],"参数【operate】有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $current_time = date('Y-m-d H:i:s');
//            $keyword_owner = User::where("id",$item->creator_id)->lockForUpdate()->first();

            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"id有误，刷新页面试试！");

                if($item_operate == "启用") $item_data["item_status"] = 1;
                elseif($item_operate == "禁用") $item_data["item_status"] = 9;
                else $item_data = [];
//                dd($item_data);

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->fill($item_data)->save();
                    if(!$bool) throw new Exception("update--item--fail");
                }
                else throw new Exception('关键词不存在，刷新页面试试！');
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-启用
    public function operate_item_item_enable_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => '请选择关键词！',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $update["item_status"] = 1;
                    $bool = $item->fill($update)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("update--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【内容】批量-禁用
    public function operate_item_item_disable_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => '请选择关键词！',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $update["item_status"] = 9;
                    $bool = $item->fill($update)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("update--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }








    /*
     * 任务管理
     */
    // 【任务管理】返回-导入任务-视图
    public function view_item_task_list_import()
    {
        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9])) return view(env('TEMPLATE_ROOT_FRONT').'errors.404');

        $operate_category = 'item';
        $operate_type = 'item';
        $operate_type_text = '内容';
        $title_text = '添加'.$operate_type_text;
        $list_text = $operate_type_text.'列表';
        $list_link = '/item/item-list';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['operate_category'] = $operate_category;
        $return['operate_type'] = $operate_type;
        $return['operate_type_text'] = $operate_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.task-list-import';
        return view($view_blade)->with($return);
    }
    // 【任务管理】保存-导入任务-数据
    public function operate_item_task_list_import_save($post_data)
    {
//        $messages = [
//            'operate.required' => 'operate.required',
//            'title.required' => '请输入标题！',
//        ];
//        $v = Validator::make($post_data, [
//            'operate' => 'required',
//            'title' => 'required',
//        ], $messages);
//        if ($v->fails())
//        {
//            $messages = $v->errors();
//            return response_error([],$messages->first());
//        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,22])) return response_error([],"用户类型错误！");

        $salesman_id = $post_data["salesman_id"];
        $salesman = YH_User::find($salesman_id);
        if($salesman)
        {
            if(!in_array($salesman->user_type,[0,1,9,11,19,41,61,88])) return response_error([],"该人员不是销售人员！");
        }
        else return response_error([],"该人员不存在！");

        // 附件
        if(!empty($post_data["attachment"]))
        {

//            $result = upload_storage($post_data["attachment"]);
//            $result = upload_storage($post_data["attachment"], null, null, 'assign');
            $result = upload_file_storage($post_data["attachment"],null,'yh/unique/attachment','');
            if($result["result"])
            {
//                $mine->attachment_name = $result["name"];
//                $mine->attachment_src = $result["local"];
//                $mine->save();
            }
            else throw new Exception("upload--attachment--fail");
        }

        $attachment_file = storage_resource_path($result["local"]);

        $data = Excel::load($attachment_file, function($reader) {

//            $data = $reader->all();
//            $data = $data->toArray();

        })->all();
        $data = $data->toArray();


        // 启动数据库事务
        DB::beginTransaction();
        try
        {

            foreach($data as $key => $value)
            {
                $task = new YH_Task;

                $task->item_active = 1;
                $task->creator_id = $me->id;
                $task->owner_id = $salesman_id;
                $task->company = $value['company'];
                $task->fund = $value['fund'];
                $task->name = $value['name'];
                $task->mobile = $value['mobile'];
                $task->address = $value['address'];
                $task->city = $value['city'];
                $task->description = $value['description'];

                $bool = $task->save();
                if(!$bool) throw new Exception("insert--item--fail");
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




    // 【任务管理】批量-操作（全部操作）
    public function operate_item_task_admin_operate_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
            'bulk_item_operate.required' => 'bulk_item_operate.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
            'bulk_item_operate' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }
//        dd($post_data);

        $this->get_me();
        $me = $this->me;

        $item_operate = $post_data["bulk_item_operate"];
        if(!in_array($item_operate,['启用','禁用','删除','永久删除'])) return response_error([],"参数【operate】有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $current_time = date('Y-m-d H:i:s');
//            $keyword_owner = User::where("id",$item->creator_id)->lockForUpdate()->first();

            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"id有误，刷新页面试试！");

                if($item_operate == "启用") $item_data["item_status"] = 1;
                elseif($item_operate == "禁用") $item_data["item_status"] = 9;
                else $item_data = [];

                $item = YH_Task::find($item_id);
                if($item)
                {
                    $bool = $item->fill($item_data)->save();
                    if(!$bool) throw new Exception("update--item--fail");
                }
                else throw new Exception('关键词不存在，刷新页面试试！');
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-删除
    public function operate_item_task_admin_delete_bulk($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'task-admin-delete-bulk') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Task::withTrashed()->find($item_id);
                if($item)
                {
                    $item->timestamps = false;
//                    $bool = $item->delete();  // 普通删除
                    $bool = $item->forceDelete();  // 永久删除
                    if(!$bool) throw new Exception("delete--task--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-删除
    public function operate_item_task_admin_restore_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $item->timestamps = false;
                    $bool = $item->restore();
                    if(!$bool) throw new Exception("item--restore--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }
    // 【任务管理】管理员-批量-彻底删除
    public function operate_item_task_admin_delete_permanently_bulk($post_data)
    {
        $messages = [
            'bulk_item_id.required' => 'bulk_item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'bulk_item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
//        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_ids = $post_data["bulk_item_id"];
            foreach($item_ids as $key => $item_id)
            {
                if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

                $item = YH_Item::find($item_id);
                if($item)
                {
                    $bool = $item->forceDelete();
                    if(!$bool) throw new Exception("delete--item--fail");
                }
                else throw new Exception("内容不存在，刷新页面试试！");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
    }








    /*
     * 车辆管理
     */
    // 【车辆管理】返回-添加-视图
    public function view_item_car_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '车辆';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/car-list-for-all';

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.car-edit';
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
    // 【车辆管理】返回-编辑-视图
    public function view_item_car_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.car-edit';

        $item_type = 'item';
        $item_type_text = '车辆';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/car-list-for-all';

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
            $mine = YH_Car::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
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
            else return view(env('TEMPLATE_YH_ADMIN').'errors.404');
        }
    }
    // 【车辆管理】保存数据
    public function operate_item_car_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入车牌号！',
//            'name.unique' => '该车牌号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:yh_car,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = YH_Car::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【车牌号】已存在，请勿重复添加！");

            $mine = new YH_Car;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Car::find($operate_id);
            if(!$mine) return response_error([],"该【车辆】不存在，刷新页面重试！");
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

            if(in_array($mine_data["trailer_type"],["0","-1"])) unset($mine_data['trailer_type']);
            if(in_array($mine_data["trailer_length"],["0","-1"])) unset($mine_data['trailer_length']);
            if(in_array($mine_data["trailer_volume"],["0","-1"])) unset($mine_data['trailer_volume']);
            if(in_array($mine_data["trailer_weight"],["0","-1"])) unset($mine_data['trailer_weight']);
            if(in_array($mine_data["trailer_axis_count"],["0","-1"])) unset($mine_data['trailer_axis_count']);


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--car--fail");

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


    // 【车辆管理】【文本-信息】设置-文本-类型
    public function operate_item_car_info_text_set($post_data)
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
        if($operate != 'item-car-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Car::withTrashed()->find($id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
                if($me->id == $item->creator_id && $item->is_published == 0)
                {
                }
                else
                {
                    $record = new YH_Record;

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
    // 【车辆管理】【时间-信息】修改-时间-类型
    public function operate_item_car_info_time_set($post_data)
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
        if($operate != 'item-car-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Car::withTrashed()->find($id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

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
    // 【车辆管理】【选项-信息】修改-radio-select-[option]-类型
    public function operate_item_car_info_option_set($post_data)
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
        if($operate != 'item-car-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Car::withTrashed()->find($id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
                if($me->id == $item->creator_id && $item->is_published == 0)
                {
                }
                else
                {
                    $record = new YH_Record;

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


    // 【任务管理】管理员-删除
    public function operate_item_car_admin_delete($post_data)
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
        if($operate != 'car-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Car::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("car--delete--fail");

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
    // 【任务管理】管理员-恢复
    public function operate_item_car_admin_restore($post_data)
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
        if($operate != 'car-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Car::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("car--restore--fail");

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
    // 【任务管理】管理员-彻底删除
    public function operate_item_car_admin_delete_permanently($post_data)
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
        if($operate != 'car-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Car::withTrashed()->find($id);
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
            if(!$bool) throw new Exception("car--delete--fail");

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
    // 【车辆管理】管理员-启用
    public function operate_item_car_admin_enable($post_data)
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
        if($operate != 'car-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Car::find($id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--car--fail");

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
    // 【车辆管理】管理员-禁用
    public function operate_item_car_admin_disable($post_data)
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
        if($operate != 'car-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Car::find($id);
        if(!$item) return response_error([],"该【车辆】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--car--fail");

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


    // 【车辆管理】返回-列表-视图
    public function view_item_car_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $car_list = YH_Car::select(DB::raw("
                id,name,item_type,inspection_validity,transportation_license_validity,transportation_license_change_time
            "))
            ->whereDate('inspection_validity', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
            ->orWhereDate('transportation_license_validity', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
            ->orWhereDate('transportation_license_change_time', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
//            ->where(function ($query) {
//                $query
////                    ->whereNotNull('inspection_validity')
////                    ->where('inspection_validity', '!=', '')
////                    ->where(DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"), '>=', DB::raw("DATE(inspection_validity)"))
//                    ->whereDate('inspection_validity', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
//                    ->get();
//            })
//            ->orWhere(function ($query) {
//                $query
////                    ->whereNotNull('transportation_license_validity')
////                    ->where('transportation_license_validity', '!=', '')
//                    ->whereDate('transportation_license_validity', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
//                    ->get();
//            })
//            ->orWhere(function ($query) {
//                $query
////                    ->whereNotNull('transportation_license_change_time')
////                    ->where('transportation_license_change_time', '!=', '')
//                    ->whereDate('transportation_license_change_time', '<=', DB::raw("DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))
//                    ->get();
//            })
            ->get();


        $last_month_start_date = date('Y-m-01',strtotime('last month')); // 上月开始时间
        $last_month_ended_date = date('Y-m-t',strtotime('last month')); // 上月开始时间
        $last_month_start_datetime = date('Y-m-01 00:00:00',strtotime('last month')); // 上月开始时间
        $last_month_ended_datetime = date('Y-m-t 23:59:59',strtotime('last month')); // 上月结束时间
        $last_month_start_timestamp = strtotime($last_month_start_date); // 上月开始时间戳
        $last_month_ended_timestamp = strtotime($last_month_ended_datetime); // 上月月结束时间戳

        $after_30_day = strtotime(date("Y-m-d 23:59:59")) + 60*60*24*30;

        foreach ($car_list as $k => $v)
        {
            $v->inspection_validity_is = 0;
            if($v->inspection_validity && strtotime($v->inspection_validity) <= $after_30_day)
            {
                $v->inspection_validity_is = 1;
            }

            $v->transportation_license_validity_is = 0;
            if($v->transportation_license_validity && strtotime($v->transportation_license_validity) <= $after_30_day)
            {
                $v->transportation_license_validity_is = 1;
            }

            $v->transportation_license_change_time_is = 0;
            if($v->transportation_license_change_time && strtotime($v->transportation_license_change_time) <= $after_30_day)
            {
                $v->transportation_license_change_time_is = 1;
            }
        }


        $return['car_list'] = $car_list;
        $return['menu_active_of_car_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.car-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【车辆管理】返回-列表-数据
    public function get_item_car_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Car::select('*')
            ->withTrashed()
            ->withCount([
                'car_order_list as car_on_the_road'=>function($query) {
                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                },
                'trailer_order_list as trailer_on_the_road'=>function($query) {
                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                },
                'car_order_list as car_future'=>function($query) {
                    $query->whereNull('actual_departure_time');
                },
                'trailer_order_list as trailer_future'=>function($query) {
                    $query->whereNull('actual_departure_time');
                }
            ])
            ->with(['creator','owner',
                'car_order_list_for_current'=>function($query) {
                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time')->orderby('id','asc')->limit(1);
                },
                'trailer_order_list_for_current'=>function($query) {
                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time')->orderby('id','asc')->limit(1);
                },
                'car_order_list_for_completed'=>function($query) {
                    $query->whereNotNull('actual_arrival_time')->orderby('actual_arrival_time','desc')->limit(1);
                },
                'trailer_order_list_for_completed'=>function($query) {
                    $query->whereNotNull('actual_arrival_time')->orderby('actual_arrival_time','desc')->limit(1);
                },
                'car_order_list_for_future'=>function($query) {
                    $query->whereNull('actual_departure_time')->orderby('actual_departure_time','asc');
                },
                'trailer_order_list_for_future'=>function($query) {
                    $query->whereNull('actual_departure_time')->orderby('actual_departure_time','asc');
                }
            ]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['work_status']) || $post_data['work_status'] == 0)
        {
            $work_status = $post_data['work_status'];
            if(in_array($work_status,[-1,0,1,9,19]))
            {
                if($work_status == 0)
                {
                    $query->whereDoesntHave('car_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        })
                        ->whereDoesntHave('trailer_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        })
                        ->whereDoesntHave('car_order_list_for_future', function ($query) {
                            $query->whereNull('actual_departure_time');
                        })
                        ->whereDoesntHave('trailer_order_list_for_future', function ($query) {
                            $query->whereNull('actual_departure_time');
                        });
                }
                else if($work_status == 1)
                {
                    $query->whereHas('car_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        })
                        ->orWhereHas('trailer_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        });
                }
                else if($work_status == 9)
                {
                    $query->where(function ($query) {
                            $query->whereDoesntHave('car_order_list_for_current', function ($query) {
                                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                                })
                                ->whereDoesntHave('trailer_order_list_for_current', function ($query) {
                                    $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                                });
                        })
                        ->where(function ($query) {
                            $query->whereHas('car_order_list_for_future', function ($query) {
                                    $query->whereNull('actual_departure_time');
                                })
                                ->orWhereHas('trailer_order_list_for_future', function ($query) {
                                    $query->whereNull('actual_departure_time');
                                });
                        });
                }
                else if($work_status == 19)
                {
                    $query->whereDoesntHave('car_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        })
                        ->whereDoesntHave('trailer_order_list_for_current', function ($query) {
                            $query->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                        });
                }
            }
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
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->work_status = 0;
            $list[$k]->current_place = '--';
            $list[$k]->future_place = '--';
            $list[$k]->future_time = 0;

            // 车辆类型 牵引车 || 车挂
            if($v->item_type == 1)
            {
                $mine_on_the_road = $v->car_on_the_road;
                $mine_future = $v->car_future;
                $mine_order_list_for_current = $v->car_order_list_for_current;
                $mine_order_list_for_completed = $v->car_order_list_for_completed;
                $mine_order_list_for_future = $v->car_order_list_for_future;
            }
            else if($v->item_type == 21)
            {
                $mine_on_the_road = $v->trailer_on_the_road;
                $mine_future = $v->trailer_future;
                $mine_order_list_for_current = $v->trailer_order_list_for_current;
                $mine_order_list_for_completed = $v->trailer_order_list_for_completed;
                $mine_order_list_for_future = $v->trailer_order_list_for_future;
            }

//            if(count($v->car_order_list) || count($v->trailer_order_list)) $list[$k]->work_status = 1;
            if($mine_on_the_road)
            {
                $list[$k]->work_status = 1;  // 工作中
                // 未来位置
                if(count($mine_order_list_for_current) > 0)
                {
                    $list[$k]->future_place = $mine_order_list_for_current[0]->destination_place;
                    $list[$k]->future_time = $mine_order_list_for_current[0]->should_arrival_time;
                }
            }
            else
            {
                if($mine_future)
                {
                    $list[$k]->work_status = 9;  // 待发车
                    // 当前位置
                    if(count($mine_order_list_for_completed) > 0)
                    {
                        $list[$k]->current_place = $mine_order_list_for_completed[0]->destination_place;

                        if(count($mine_order_list_for_future) > 0)
                        {
                            $list[$k]->future_place = $mine_order_list_for_future[0]->destination_place;
                            $list[$k]->future_time = $mine_order_list_for_future[0]->should_arrival_time;
                        }
                    }
                    else
                    {
                        if(count($mine_order_list_for_future) > 0)
                        {
                            $list[$k]->current_place = $mine_order_list_for_future[0]->departure_place;
                            $list[$k]->future_place = $mine_order_list_for_future[0]->destination_place;
                            $list[$k]->future_time = $mine_order_list_for_future[0]->should_arrival_time;
                        }
                    }
                }
                else
                {
                    $list[$k]->work_status = 0;  // 空闲中
                    // 当前位置
                    if(count($v->car_order_completed_list) > 0)
                    {
                        $list[$k]->current_place = $v->car_order_completed_list[0]->destination_place;
                    }
                }

            }

        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * 线路管理
     */
    // 【线路管理】返回-添加-视图
    public function view_item_route_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '线路';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/route-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.route-edit';
        return view($view_blade)->with($return);
    }
    // 【线路管理】返回-编辑-视图
    public function view_item_route_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.route-edit';

        $item_type = 'item';
        $item_type_text = '线路';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/route-list-for-all';

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
            $mine = YH_Route::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['data'] = $mine;

                return view($view_blade)->with($return);
            }
            else return view(env('TEMPLATE_YH_ADMIN').'errors.404');
        }
    }
    // 【线路管理】保存数据
    public function operate_item_route_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'title.required' => '请输入线路标题！',
            'title.unique' => '该线路已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'title' => 'required',
//            'title' => 'required|unique:yh_route,title',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = YH_Route::select('id')->where('title',$post_data["title"])->count();
            if($is_exist) return response_error([],"该【线路名】已存在，请勿重复添加！");

            $mine = new YH_Route;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["item_category"] = 1;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Route::find($operate_id);
            if(!$mine) return response_error([],"该【线路】不存在，刷新页面重试！");
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

            if(in_array($mine_data["trailer_length"],["0","-1"])) unset($mine_data['trailer_length']);


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--route--fail");

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


    // 【线路管理】【文本-类型】修改-文本-类型
    public function operate_item_route_info_text_set($post_data)
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
        if($operate != 'item-route-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Route::withTrashed()->find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("route--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 51;
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
    // 【线路管理】【时间-类型】修改-时间-类型
    public function operate_item_route_info_time_set($post_data)
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
        if($operate != 'item-route-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Route::withTrashed()->find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
//            $item->timestamps = false;
            $item->$column_key = strtotime($column_value);
            $bool = $item->save();
            if(!$bool) throw new Exception("route--update--fail");
            else
            {
                // 需要记录(本人修改已发布 || 他人修改)
                if($me->id == $item->creator_id && $item->is_published == 0 && false)
                {
                }
                else
                {
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 51;
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
    // 【线路管理】【选项-类型】修改-radio-select-[option]-类型
    public function operate_item_route_info_option_set($post_data)
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
        if($operate != 'item-route-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Route::withTrashed()->find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 51;
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


    // 【线路管理】管理员-删除
    public function operate_item_route_admin_delete($post_data)
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
        if($operate != 'route-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Route::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("route--delete--fail");

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
    // 【线路管理】管理员-恢复
    public function operate_item_route_admin_restore($post_data)
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
        if($operate != 'route-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Route::withTrashed()->find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("route--restore--fail");

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
    // 【线路管理】管理员-彻底删除
    public function operate_item_route_admin_delete_permanently($post_data)
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
        if($operate != 'route-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Route::withTrashed()->find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("route--delete--fail");

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
    // 【线路管理】管理员-启用
    public function operate_item_route_admin_enable($post_data)
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
        if($operate != 'route-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Route::find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--route--fail");

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
    // 【线路管理】管理员-禁用
    public function operate_item_route_admin_disable($post_data)
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
        if($operate != 'route-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Route::find($id);
        if(!$item) return response_error([],"该【线路】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--route--fail");

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


    // 【线路管理】返回-列表-视图
    public function view_item_route_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_route_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.route-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【线路管理】返回-列表-数据
    public function get_item_route_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Route::select('*')
            ->withTrashed()
//            ->withCount([''])
            ->with(['creator']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


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
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * 定价管理
     */
    // 【定价管理】返回-添加-视图
    public function view_item_pricing_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '定价';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/pricing-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.pricing-edit';
        return view($view_blade)->with($return);
    }
    // 【定价管理】返回-编辑-视图
    public function view_item_pricing_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.pricing-edit';

        $item_type = 'item';
        $item_type_text = '定价';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/pricing-list-for-all';

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
            $mine = YH_Pricing::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['data'] = $mine;

                return view($view_blade)->with($return);
            }
            else return view(env('TEMPLATE_YH_ADMIN').'errors.404');
        }
    }
    // 【定价管理】保存数据
    public function operate_item_pricing_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'title.required' => '请输入线路标题！',
            'title.unique' => '该线路已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'title' => 'required',
//            'title' => 'required|unique:yh_route,title',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = YH_Pricing::select('id')->where('title',$post_data["title"])->count();
            if($is_exist) return response_error([],"该【定价】已存在，请勿重复添加！");

            $mine = new YH_Pricing;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["item_category"] = 1;
            $post_data["item_type"] = 1;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Pricing::find($operate_id);
            if(!$mine) return response_error([],"该【定价】不存在，刷新页面重试！");
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

//            if(in_array($mine_data["trailer_length"],["0","-1"])) unset($mine_data['trailer_length']);


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("insert--pricing--fail");

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


    // 【定价管理】【文本】修改-文本-类型
    public function operate_item_pricing_info_text_set($post_data)
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
        if($operate != 'item-pricing-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Pricing::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
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
    // 【定价管理】【时间】修改-时间-类型
    public function operate_item_pricing_info_time_set($post_data)
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
        if($operate != 'item-pricing-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Pricing::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
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
    // 【定价管理】【选项】修改-radio-select-[option]-类型
    public function operate_item_pricing_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'car_id.required.',
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
        if($operate != 'item-pricing-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Car::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 61;
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


    // 【定价管理】管理员-删除
    public function operate_item_pricing_admin_delete($post_data)
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
        if($operate != 'pricing-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Pricing::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("pricing--delete--fail");

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
    // 【定价管理】管理员-恢复
    public function operate_item_pricing_admin_restore($post_data)
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
        if($operate != 'pricing-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Pricing::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("pricing--restore--fail");

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
    // 【定价管理】管理员-彻底删除
    public function operate_item_pricing_admin_delete_permanently($post_data)
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
        if($operate != 'pricing-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Pricing::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("pricing--delete--fail");

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
    // 【定价管理】管理员-启用
    public function operate_item_pricing_admin_enable($post_data)
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
        if($operate != 'pricing-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Pricing::find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--pricing--fail");

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
    // 【定价管理】管理员-禁用
    public function operate_item_pricing_admin_disable($post_data)
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
        if($operate != 'pricing-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Pricing::find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--pricing--fail");

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


    // 【定价管理】返回-列表-视图
    public function view_item_pricing_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_pricing_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.pricing-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【定价管理】返回-列表-数据
    public function get_item_pricing_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Pricing::select('*')
            ->withTrashed()
//            ->withCount([''])
            ->with(['creator']);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


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
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }








    /*
     * 订单管理
     */
    //
    public function operate_order_select2_client($post_data)
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
//        if(!is_numeric($type)) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
//        if(!in_array($type,[1,2,3,10,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');

        if(empty($post_data['keyword']))
        {
            $list =YH_Client::select(['id','username as text'])
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Client::select(['id','username as text'])->where('username','like',"%$keyword%")
                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }
    //
    public function operate_order_select2_route($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Route::select('*','title as text')
//                ->select(['id','title as text','amount_with_cash','amount_with_invoice'])
                ->where(['item_status'=>1,'item_category'=>1])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Route::select('*','title as text')
//                ->select(['id','title as text','amount_with_cash','amount_with_invoice'])
                ->where('title','like',"%$keyword%")
                ->where(['item_status'=>1,'item_category'=>1])
                ->get()->toArray();
        }
        $unSpecified = ['id'=>0,'text'=>'(清空)','amount_with_cash'=>0,'amount_with_invoice'=>0];
        array_unshift($list,$unSpecified);
        return $list;
    }
    //
    public function operate_order_select2_pricing($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Pricing::select(['id','title as text'])
                ->where(['item_status'=>1,'item_category'=>1])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Pricing::select(['id','title as text'])->where('title','like',"%$keyword%")
                ->where(['item_status'=>1,'item_category'=>1])
                ->get()->toArray();
        }
        return $list;
    }
    //
    public function operate_order_select2_car($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Car::select(['id','name as text'])
                ->where(['item_status'=>1, 'item_type'=>1])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Car::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1, 'item_type'=>1])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }
    //
    public function operate_order_select2_trailer($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Car::select(['id','name as text'])
                ->where(['item_status'=>1, 'item_type'=>21])
//                ->whereIn('item_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Car::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1, 'item_type'=>21])
//                ->whereIn('item_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }
    //
    public function operate_order_select2_container($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Car::select(['id','name as text'])
                ->where(['item_status'=>1, 'item_type'=>31])
//                ->whereIn('item_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Car::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1, 'item_type'=>31])
//                ->whereIn('item_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }
    //
    public function operate_order_list_select2_car($post_data)
    {
        $query = YH_Car::select(['id','name as text']);

        if(!empty($post_data['car_type']))
        {
            if($post_data['car_type'] == 'all')
            {
            }
            else if($post_data['car_type'] == 'car') $query->where('item_type',1);
            else if($post_data['car_type'] == 'trailer') $query->where('item_type',21);
            else if($post_data['car_type'] == 'container') $query->where('item_type',31);
        }

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        $list = $query->get()->toArray();
        return $list;
    }


    // 【订单管理】返回-添加-视图
    public function view_item_order_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '订单';
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

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-edit';
        return view($view_blade)->with($return);
    }
    // 【订单管理】返回-编辑-视图
    public function view_item_order_edit()
    {
        $this->get_me();
        $me = $this->me;

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-edit';

        $item_type = 'item';
        $item_type_text = '订单';
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
            $mine = YH_Order::find($id);
            if($mine)
            {
//                if($mine->deleted_at) return view(env('TEMPLATE_YH_ADMIN').'entrance.errors.404');
//                else
                {
                    $mine->load(['client_er','car_er','trailer_er']);
                    $mine->custom = json_decode($mine->custom);
                    $mine->custom2 = json_decode($mine->custom2);
                    $mine->custom3 = json_decode($mine->custom3);

                    $return['data'] = $mine;

                    return view($view_blade)->with($return);
                }
            }
            else return view(env('TEMPLATE_YH_ADMIN').'entrance.errors.404');
        }
    }
    // 【订单管理】保存数据
    public function operate_item_order_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'amount.required' => 'amount.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'amount' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,82,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new YH_Order;
            $post_data["item_category"] = 1;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Order::find($operate_id);
            if(!$mine) return response_error([],"该订单不存在，刷新页面重试！");

            if(in_array($me->user_type,[82,88]) && $mine->creater_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");
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

            // 指派日期
            if(!empty($post_data['assign_date']))
            {
                $post_data['assign_time'] = strtotime($post_data['assign_date']);
            }
//            else $post_data['assign_time'] = 0;
            // 应出发时间
            if(!empty($post_data['should_departure']))
            {
                $post_data['should_departure_time'] = strtotime($post_data['should_departure']);
            }
//            else $post_data['should_departure_time'] = 0;
            // 应到达时间
            if(!empty($post_data['should_arrival']))
            {
                $post_data['should_arrival_time'] = strtotime($post_data['should_arrival']);
            }
//            else $post_data['should_arrival_time'] = 0;



            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);


            if($mine_data["route_type"] == 11) $mine_data["route_id"] = 0;

            if(!empty($mine_data["trailer_type"]) && in_array($mine_data["trailer_type"],["0","-1"])) unset($mine_data['trailer_type']);
            if(!empty($mine_data["trailer_length"]) && in_array($mine_data["trailer_length"],["0","-1"])) unset($mine_data['trailer_length']);
            if(!empty($mine_data["trailer_volume"]) && in_array($mine_data["trailer_volume"],["0","-1"])) unset($mine_data['trailer_volume']);
            if(!empty($mine_data["trailer_weight"]) && in_array($mine_data["trailer_weight"],["0","-1"])) unset($mine_data['trailer_weight']);
            if(!empty($mine_data["trailer_axis_count"]) && in_array($mine_data["trailer_axis_count"],["0","-1"])) unset($mine_data['trailer_axis_count']);

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

    // 【订单管理】获取详情
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

        $item = YH_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该订单不存在，刷新页面重试！");

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

        $item = YH_Order::with(['client_er','car_er','trailer_er'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该订单不存在，刷新页面重试！");

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


        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-info-html';
        $html = view($view_blade)->with(['data'=>$item])->__toString();

        return response_success(['html'=>$html],"");

    }

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

        $item = YH_Order::with(['attachment_list'])->withTrashed()->find($id);
        if(!$item) return response_error([],"该订单不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-assign-html-for-attachment';
        $html = view($view_blade)->with(['item_list'=>$item->attachment_list])->__toString();

        return response_success(['html'=>$html],"");

    }


    // 【订单管理】删除
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

        $item = YH_Order::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,82,88])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 61;
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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $item_id;
                    $record_data["operate_object"] = 61;
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
    // 【订单管理】发布
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

        $item = YH_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,81,82,88])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[81,82,88]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_published = 1;
            $item->published_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new YH_Record;

                $record_data["record_object"] = 21;
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
    // 【订单管理】完成
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

        $item = YH_Order::withTrashed()->find($id);
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
                $record = new YH_Record;

                $record_data["record_object"] = 21;
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
    // 【订单管理】完成
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

        $item = YH_Order::withTrashed()->find($id);
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
                $record = new YH_Record;

                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 91;
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


    // 【订单管理】返回-列表-视图
    public function view_item_order_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();
        $client_list = YH_Client::select('id','username')->where('user_category',11)->get();
        $car_list = YH_Car::select('id','name')->whereIn('item_type',[1,21])->get();
        $route_list = YH_Route::select('id','title')->get();
        $pricing_list = YH_Pricing::select('id','title')->get();

        $return['staff_list'] = $staff_list;
        $return['client_list'] = $client_list;
        $return['car_list'] = $car_list;
        $return['route_list'] = $route_list;
        $return['pricing_list'] = $pricing_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【订单管理】返回-列表-数据
    public function get_item_order_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Order::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->with(['creator','owner','client_er','route_er','pricing_er','car_er','trailer_er','attachment_list']);
//            ->whereIn('user_category',[11])
//            ->whereIn('user_type',[0,1,9,11,19,21,22,41,61,88]);
//            ->whereHas('fund', function ($query1) { $query1->where('totalfunds', '>=', 1000); } )
//            ->withCount([
//                'members'=>function ($query) { $query->where('usergroup','Agent2'); },
//                'fans'=>function ($query) { $query->rderwhere('usergroup','Service'); }
//            ]);
//            ->where(['userstatus'=>'正常','status'=>1])
//            ->whereIn('usergroup',['Agent','Agent2']);

//        if($me->user_type == 88) $query->where('creator_id', $me->id);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['assign'])) $query->whereDate(DB::Raw("from_unixtime(assign_time)"), $post_data['assign']);
//        if(!empty($post_data['assign'])) $query->where(DB::Raw("from_unixtime(assign_time, '%Y-%m-%d') = {$post_data['assign']}"));
//        if(!empty($post_data['assign'])) $query->whereRaw("from_unixtime(assign_time, '%Y-%m-%d') = {$post_data['assign']}");


        if(!empty($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0]))
            {
                $query->where('creator_id', $post_data['staff']);
            }
        }

        if(!empty($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,0]))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        if(!empty($post_data['car']))
        {
            if(!in_array($post_data['car'],[-1,0]))
            {

                $query->where(function($query1) use($post_data) { $query1->where('car_id', $post_data['car'])->orWhere('trailer_id', $post_data['car']); } );
            }
        }

        if(!empty($post_data['route']))
        {
            if(!in_array($post_data['route'],[-1,0]))
            {
                $query->where('route_id', $post_data['route']);
            }
        }

        if(!empty($post_data['pricing']))
        {
            if(!in_array($post_data['pricing'],[-1,0]))
            {
                $query->where('pricing_id', $post_data['pricing']);
            }
        }


        if(!empty($post_data['status']))
        {
            $order_status = $post_data['status'];
            if(in_array($order_status,["未发布","待发车","进行中","已到达"]))
            {
                if($order_status == "未发布") $query->where('is_published', 0);
                else if($order_status == "待发车") $query->where('is_published', 1)->whereNull('actual_departure_time');
                else if($order_status == "进行中") $query->where('is_published', 1)->whereNotNull('actual_departure_time')->whereNull('actual_arrival_time');
                else if($order_status == "已到达") $query->where('is_published', 1)->whereNotNull('actual_arrival_time');
            }
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
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;

            if($v->is_published != 0)
            {
                $list[$k]->travel_status = "--";
                $list[$k]->travel_result = "--";

                if(!$v->actual_departure_time)
                {
                    $list[$k]->travel_status = "待发车";

                    if($v->should_departure_time)
                    {
                        if(time() <= $v->should_departure_time) $list[$k]->travel_result = "等待出发";
                        else $list[$k]->travel_result = "发车超时";
                    }
                    else $list[$k]->travel_result = "等待出发";
                }
                else
                {
                    if(!$v->actual_arrival_time)
                    {
                        $list[$k]->travel_status = "进行中";

                        if(time() < $v->should_arrival_time) $list[$k]->travel_result = "正常";
                        else $list[$k]->travel_result = "已超时";
                    }
                    else
                    {
                        if($v->is_completed == 1)
                        {
                            $list[$k]->travel_status = "已完成";
                            $list[$k]->travel_result = "已结束";
                        }
                        else
                        {
                            $list[$k]->travel_status = "已到达";
                            if(($v->amount + $v->oil_card_amount - $v->time_limitation_deduction) <= $v->income_total) $list[$k]->travel_result = "已收款";
                            else $list[$k]->travel_result = "待收款";
                        }


                        // 行程记录
                        $journey_time = $v->actual_arrival_time - $v->actual_departure_time;
                        $journey_day=floor($journey_time/86400);
                        $journey_hour=floor($journey_time%86400/3600);
                        $journey_minute=ceil($journey_time%86400%3600/60);
                        $journey_second=floor($journey_time%86400%3600%60/60);
                        if($journey_day == 0)
                        {
                            if($journey_hour == 0) $journey_result = $journey_minute."分钟";
                            else $journey_result = $journey_hour."小时".$journey_minute."分钟";
                        }
                        else
                        {
                            $journey_result = $journey_day."天".$journey_hour."小时".$journey_minute."分钟";
                        }
                        $list[$k]->travel_journey_time = $journey_result;

                        // 发车超时
                        if($v->should_departure_time)
                        {
                            if($v->actual_departure_time <= $v->should_departure_time)
                            {
                                $list[$k]->travel_result = "正常";
                            }
                            else
                            {
                                $departure_subtract = $v->actual_departure_time - $v->should_departure_time;

                                $departure_subtract_day=floor($departure_subtract/86400);
                                $departure_subtract_hour=floor($departure_subtract%86400/3600);
                                $departure_subtract_minute=ceil($departure_subtract%86400%3600/60);
                                $departure_subtract_second=floor($departure_subtract%86400%3600%60/60);
                                if($departure_subtract_day == 0)
                                {
                                    if($departure_subtract_hour == 0) $departure_subtract_result = $departure_subtract_minute."分钟";
                                    else $departure_subtract_result = $departure_subtract_hour."小时".$departure_subtract_minute."分钟";
                                }
                                else
                                {
                                    $departure_subtract_result = $departure_subtract_day."天".$departure_subtract_hour."小时".$departure_subtract_minute."分钟";
                                }
                                $list[$k]->travel_departure_overtime_time = $departure_subtract_result;
                            }
                        }

                        // 到达超时
                        if($v->should_arrival_time)
                        {
                            if($v->actual_arrival_time <= $v->should_arrival_time)
                            {
                                $list[$k]->travel_result = "正常";
                            }
                            else
                            {
                                $arrival_subtract = $v->actual_arrival_time - $v->should_arrival_time;

                                $arrival_subtract_day=floor($arrival_subtract/86400);
                                $arrival_subtract_hour=floor($arrival_subtract%86400/3600);
                                $arrival_subtract_minute=ceil($arrival_subtract%86400%3600/60);
                                $arrival_subtract_second=floor($arrival_subtract%86400%3600%60/60);
                                if($arrival_subtract_day == 0)
                                {
                                    if($arrival_subtract_hour == 0) $arrival_subtract_result = $arrival_subtract_minute."分钟";
                                    else $arrival_subtract_result = $arrival_subtract_hour."小时".$arrival_subtract_minute."分钟";
                                }
                                else
                                {
                                    $arrival_subtract_result = $arrival_subtract_day."天".$arrival_subtract_hour."小时".$arrival_subtract_minute."分钟";
                                }
                                $list[$k]->travel_arrival_overtime_time = $arrival_subtract_result;
                            }
                        }

                    }

                }
            }

        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }




    // 【订单管理】【文本】修改-文本-类型
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

        $item = YH_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【订单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;


        if($column_key == "amount")
        {
            if(!in_array($me->user_type,[0,1,11,81,82,88])) return response_error([],"你没有操作权限！");
        }
        else
        {
            if(!in_array($me->user_type,[0,1,11,81,82,88])) return response_error([],"你没有操作权限！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
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
                    $record = new YH_Record;

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
    // 【订单管理】【时间】修改-时间-类型
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

        $item = YH_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该订单不存在，刷新页面重试！");

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
                    $record = new YH_Record;

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
    // 【订单管理】【选项】修改-radio-select-[option]-类型
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

        $item = YH_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该【订单】不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_type = $post_data["operate_type"];
        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];

        $before = $item->$column_key;

//        if($column_key == "client")
//        {
//            if(!in_array($me->user_type,[0,1,11,41,42])) return response_error([],"你没有操作权限！");
//        }
//        else
//        {
//            if(!in_array($me->user_type,[0,1,11,81,82,88])) return response_error([],"你没有操作权限！");
//        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
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
                    $record = new YH_Record;

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

                    if(in_array($column_key,['client_id','route_id','car_id','trailer_id']))
                    {
                        $record_data["before_id"] = $before;
                        $record_data["after_id"] = $column_value;
                    }

                    if($column_key == 'client_id')
                    {
                        $record_data["before_client_id"] = $before;
                        $record_data["after_client_id"] = $column_value;
                    }
                    else if($column_key == 'route_id')
                    {
                        $record_data["before_route_id"] = $before;
                        $record_data["after_route_id"] = $column_value;
                    }
                    else if($column_key == 'pricing_id')
                    {
                        $record_data["before_pricing_id"] = $before;
                        $record_data["after_pricing_id"] = $column_value;
                    }
                    else if($column_key == 'car_id' || $column_key == 'trailer_id')
                    {
                        $record_data["before_car_id"] = $before;
                        $record_data["after_car_id"] = $column_value;
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
    // 【订单管理】【附件】添加
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

        $item = YH_Order::withTrashed()->find($order_id);
        if(!$item) return response_error([],"该【订单】不存在，刷新页面重试！");

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
            $attachment = new YH_Attachment;
            // 头像
            if(!empty($post_data["attachment_file"]))
            {

//                $result = upload_storage($post_data["portrait"]);
//                $result = upload_storage($post_data["portrait"], null, null, 'assign');
                $result = upload_img_storage($post_data["attachment_file"],'','yh/attachment','');
                if($result["result"])
                {
                    $attachment_data['order_id'] = $order_id;
                    $attachment_data['attachment_name'] = $post_data["attachment_name"];
                    $attachment_data['attachment_src'] = $result["local"];
                    $bool = $attachment->fill($attachment_data)->save();
                    if($bool)
                    {
                        $record = new YH_Record;

                        $record_data["record_object"] = 21;
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
                else throw new Exception("upload--portrait_img--file--fail");
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
    // 【订单管理】【附件】删除
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
                $record = new YH_Record;

                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["item_id"] = $item_id;
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
    // 【订单管理】【修改信息】设置-行程时间
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

        $item = YH_Order::withTrashed()->find($id);
        if(!$item) return response_error([],"该订单不存在，刷新页面重试！");

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




    // 【订单管理】【财务往来记录】返回-列表-视图
    public function view_item_order_finance_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【订单管理】【财务往来记录】返回-列表-数据
    public function get_item_order_finance_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = YH_Finance::select('*')
            ->with(['creator','confirmer','owner'])
            ->where(['order_id'=>$id]);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['type']))
        {
            if($post_data['type'] == "income")
            {
                $query->where('finance_type', 1);
            }
            else if($post_data['type'] == "expenditure")
            {
                $query->where('finance_type', 21);
            }
        }

        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,21]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
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

    // 【订单管理】添加-财务数据-保存数据
    public function operate_item_order_finance_record_create($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'order_id.required' => 'order_id.required.',
            'transaction_date.required' => '请选择交易日期！',
            'transaction_title.required' => '请填写费用类型！',
            'transaction_type.required' => '请填写支付方式！',
            'transaction_amount.required' => '请填写金额！',
//            'transaction_account.required' => '请填写交易账号！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'order_id' => 'required',
            'transaction_date' => 'required',
            'transaction_title' => 'required',
            'transaction_type' => 'required',
            'transaction_amount' => 'required',
//            'transaction_account' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[0,1,11,41,42,81,82,88])) return response_error([],"你没有操作权限！");


//        $operate = $post_data["operate"];
//        $operate_id = $post_data["operate_id"];

        $transaction_date_timestamp = strtotime($post_data['transaction_date']);
        if($transaction_date_timestamp > time('Y-m-d')) return response_error([],"指定日期不能大于今天！");

        $order_id = $post_data["order_id"];
        $order = YH_Order::where('id',$order_id)->lockForUpdate()->first();
        if(!$order) return response_error([],"该订单不存在，刷新页面重试！");

        // 交易类型 收入 || 支出
        $finance_type = $post_data["finance_type"];
        if(!in_array($finance_type,[1,21])) return response_error([],"交易类型错误！");

        $transaction_amount = $post_data["transaction_amount"];
        if(!is_numeric($transaction_amount)) return response_error([],"交易金额必须为数字！");
        if($transaction_amount <= 0) return response_error([],"交易金额必须大于零！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $FinanceRecord = new YH_Finance;

            if(in_array($me->user_type,[41,42]))
            {
                $FinanceRecord_data['is_confirmed'] = 1;
            }

            $FinanceRecord_data['creator_id'] = $me->id;
            $FinanceRecord_data['finance_category'] = 11;
            $FinanceRecord_data['finance_type'] = $finance_type;
            $FinanceRecord_data['order_id'] = $post_data["order_id"];
            $FinanceRecord_data['transaction_time'] = $transaction_date_timestamp;
            $FinanceRecord_data['transaction_type'] = $post_data["transaction_type"];
            $FinanceRecord_data['transaction_amount'] = $post_data["transaction_amount"];
//            $FinanceRecord_data['transaction_account'] = $post_data["transaction_account"];
            $FinanceRecord_data['transaction_receipt_account'] = $post_data["transaction_receipt_account"];
            $FinanceRecord_data['transaction_payment_account'] = $post_data["transaction_payment_account"];
            $FinanceRecord_data['transaction_order'] = $post_data["transaction_order"];
            $FinanceRecord_data['title'] = $post_data["transaction_title"];
            $FinanceRecord_data['description'] = $post_data["transaction_description"];

            $mine_data = $post_data;

            unset($mine_data['operate']);
            unset($mine_data['operate_id']);
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

            $bool = $FinanceRecord->fill($FinanceRecord_data)->save();
            if($bool)
            {
                if(in_array($me->user_type,[41,42]))
                {
                    if($finance_type == 1)
                    {
                        $order->income_total = $order->income_total + $transaction_amount;
                    }
                    else if($finance_type == 21)
                    {
                        $order->expenditure_total = $order->expenditure_total + $transaction_amount;
                    }
                }
                else
                {
                    if($finance_type == 1)
                    {
                        $order->income_to_be_confirm = $order->income_to_be_confirm + $transaction_amount;
                    }
                    else if($finance_type == 21)
                    {
                        $order->expenditure_to_be_confirm = $order->expenditure_to_be_confirm + $transaction_amount;
                    }
                }

                $bool_1 = $order->save();
                if($bool_1)
                {
                }
                else throw new Exception("update--order--fail");
            }
            else throw new Exception("insert--finance--fail");

            DB::commit();
            return response_success(['id'=>$FinanceRecord->id]);
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




    // 【订单管理】【修改记录】返回-列表-视图
    public function view_item_order_modify_record($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $staff_list = YH_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[11,81,82,88])->get();

        $return['staff_list'] = $staff_list;
        $return['menu_active_of_order_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.order-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【订单管理】【修改记录】返回-列表-数据
    public function get_item_order_modify_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = YH_Record::select('*')
            ->with(['creator','before_client_er','after_client_er','before_route_er','after_route_er','before_pricing_er','after_pricing_er','before_car_er','after_car_er'])
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








    /*
     * 环线管理
     */
    //
    public function operate_circle_select2_order_list($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Order::select(['id','title','departure_place','destination_place','assign_time'])
//                ->selectRaw('CONCAT(id, " ", IfNull(title,""), " (", IfNull(departure_place,""), "-", IfNull(destination_place,""), ") (", FROM_UNIXTIME(IfNull(assign_time,"--"),"%Y-%m-%d"), ")") as text')
//                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->orderby('id','desc')->get();
//                ->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Order::select(['id','title','departure_place','destination_place','assign_time'])
//                ->selectRaw('CONCAT(id, " ", IfNull(title,""), " (", IfNull(departure_place,""), "-", IfNull(destination_place,""), ") (", FROM_UNIXTIME(IfNull(assign_time,"--"),"%Y-%m-%d"), ")") as text')
                ->where('id', 'like', $keyword)
                ->orWhere('title', 'like', $keyword)
                ->orWhere('departure_place', 'like', $keyword)
                ->orWhere('destination_place', 'like', $keyword)
//                ->where(['user_status'=>1,'user_category'=>11])
//                ->whereIn('user_type',[41,61,88])
                ->orderby('id','desc')->get();
//                ->toArray();
        }
        foreach($list as $v)
        {
            $v->assign_time = $v->assign_time ? " (".date('Y-m-d',$v->assign_time).")" : "";
            $v->text = $v->id." ".$v->title." (".$v->departure_place."-".$v->destination_place.")".$v->assign_time;
//            unset($v->title);
//            unset($v->departure_place);
//            unset($v->destination_place);
//            unset($v->assign_time);
        }
//        dd($list->toArray());
        return $list->toArray();
    }
    //
    public function operate_circle_select2_car($post_data)
    {
        if(empty($post_data['keyword']))
        {
            $list =YH_Car::select(['id','name as text'])
                ->where(['item_status'=>1, 'item_type'=>1])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        else
        {
            $keyword = "%{$post_data['keyword']}%";
            $list =YH_Car::select(['id','name as text'])->where('name','like',"%$keyword%")
                ->where(['item_status'=>1, 'item_type'=>1])
//                ->whereIn('user_type',[41,61,88])
                ->get()->toArray();
        }
        return $list;
    }


    // 【环线管理】返回-添加-视图
    public function view_item_circle_create()
    {
        $this->get_me();

        $item_type = 'item';
        $item_type_text = '环线';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/circle-list-for-all';

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.circle-edit';
        return view($view_blade)->with($return);
    }
    // 【环线管理】返回-编辑-视图
    public function view_item_circle_edit()
    {
        $this->get_me();

        $id = request("id",0);
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.circle-edit';

        $item_type = 'item';
        $item_type_text = '换线';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/pricing-list-for-all';

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
            $mine = YH_Circle::find($id);
            if($mine)
            {
//                if(!in_array($mine->user_category,[1,9,11,88])) return view(env('TEMPLATE_YH_ADMIN').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['data'] = $mine;

                return view($view_blade)->with($return);
            }
            else return view(env('TEMPLATE_YH_ADMIN').'errors.404');
        }
    }
    // 【环线管理】保存数据
    public function operate_item_circle_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => 'operate.required.',
            'title.required' => '请输入环线标题！',
            'title.unique' => '该环线已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'title' => 'required',
//            'title' => 'required|unique:yh_route,title',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = YH_Circle::select('id')->where('title',$post_data["title"])->count();
            if($is_exist) return response_error([],"该【环线】已存在，请勿重复添加！");

            $mine = new YH_Circle;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
            $post_data["item_category"] = 1;
            $post_data["item_type"] = 1;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Circle::find($operate_id);
            if(!$mine) return response_error([],"该【环线】不存在，刷新页面重试！");
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
            unset($mine_data['operate_category']);
            unset($mine_data['operate_type']);

//            if(in_array($mine_data["trailer_length"],["0","-1"])) unset($mine_data['trailer_length']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if(!empty($post_data["order_list"]))
                {
                    foreach($post_data["order_list"] as $key => $val)
                    {
                        $order_list[$key]['order_id'] = $val;
                        $order_list[$key]['creator_id'] = $me->id;
                    }
//                    $mine->pivot_order_list()->attach($order_list);  //
                    $mine->pivot_order_list()->syncWithoutDetaching($order_list);  //
                }

            }
            else throw new Exception("insert--circle--fail");

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


    // 【环线管理】【文本】修改-文本-类型
    public function operate_item_circle_info_text_set($post_data)
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
        if($operate != 'item-circle-info-text-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Circle::withTrashed()->find($id);
        if(!$item) return response_error([],"该【换线】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 77;
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
    // 【环线管理】【时间】修改-时间-类型
    public function operate_item_circle_info_time_set($post_data)
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
        if($operate != 'item-circle-info-time-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Circle::withTrashed()->find($id);
        if(!$item) return response_error([],"该【环线】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 77;
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
    // 【环线管理】【选项】修改-radio-select-[option]-类型
    public function operate_item_circle_info_option_set($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'car_id.required.',
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
        if($operate != 'item-circle-info-option-set') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Circle::withTrashed()->find($id);
        if(!$item) return response_error([],"该【环线】不存在，刷新页面重试！");

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
                    $record = new YH_Record;

                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["item_id"] = $id;
                    $record_data["operate_object"] = 77;
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


    // 【环线管理】管理员-删除
    public function operate_item_circle_admin_delete($post_data)
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
        if($operate != 'circle-admin-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = YH_Circle::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("circle--delete--fail");

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
    // 【环线管理】管理员-恢复
    public function operate_item_circle_admin_restore($post_data)
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
        if($operate != 'circle-admin-restore') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Circle::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("circle--restore--fail");

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
    // 【环线管理】管理员-彻底删除
    public function operate_item_circle_admin_delete_permanently($post_data)
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
        if($operate != 'circle-admin-delete-permanently') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Circle::withTrashed()->find($id);
        if(!$item) return response_error([],"该【定价】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("circle--delete--fail");

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
    // 【环线管理】管理员-启用
    public function operate_item_circle_admin_enable($post_data)
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
        if($operate != 'circle-admin-enable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Circle::find($id);
        if(!$item) return response_error([],"该【环线】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--circle--fail");

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
    // 【环线管理】管理员-禁用
    public function operate_item_circle_admin_disable($post_data)
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
        if($operate != 'circle-admin-disable') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $item = YH_Circle::find($id);
        if(!$item) return response_error([],"该【环线】不存在，刷新页面重试！");

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
            if(!$bool) throw new Exception("update--circle--fail");

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


    // 【环线管理】返回-列表-视图
    public function view_item_circle_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $return['menu_active_of_circle_list_for_all'] = 'active menu-open';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.item.circle-list-for-all';
        return view($view_blade)->with($return);
    }
    // 【环线管理】返回-列表-数据
    public function get_item_circle_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = YH_Circle::select('*')
            ->withTrashed()
//            ->withCount([''])
            ->with(['creator','car_er',
                'pivot_order_list',
//                'pivot_order_list'=>function($query) {
//                    $query->with('finance_list');
//                },
            ]);

        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【环线管理】返回-分析-数据
    public function get_item_circle_analysis($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $circle_id  = $post_data["circle_id"];
        $pivot_order_list = YH_Pivot_Circle_Order::select('order_id')
            ->withTrashed()
            ->where('circle_id',$circle_id)->get();
        $order_id_list = $pivot_order_list->pluck('order_id')->toArray();


        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 总览
        $order_list = YH_Order::select('*')
            ->whereIn('id',$order_id_list)
            ->get();
        $overview = [];
        foreach($order_list as $k => $v)
        {
            $overview['title'][$k] = $v->id;
            $overview['income'][$k] = intval($v->amount + $v->oil_card_amount - $v->time_limitation_deduction);
            $overview['expenses'][$k] = intval(0 - $v->expenditure_total);
            $overview['profit'][$k] = intval($overview['income'][$k] + $overview['expenses'][$k]);
        }
        $return_data["overview"] = $overview;
//        dd($order_list->toArray());


        // 支出【占比】
        $expenditure_rate = YH_Finance::groupBy('title')
            ->select('title',DB::raw("
                    sum(transaction_amount) as value,
                    count(*) as count
                "))
            ->whereIn('order_id',$order_id_list)
            ->get();
        foreach($expenditure_rate as $k => $v)
        {
            $expenditure_rate[$k]->name = $v->title;
        }
        $return_data["expenditure_rate"] = $expenditure_rate;

        return response_success($return_data,"");
    }
    // 【环线管理】【财务往来记录】返回-列表-数据
    public function get_item_circle_finance_record_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $order_list = YH_Pivot_Circle_Order::select('order_id')
            ->withTrashed()
            ->where('circle_id',$id)->get();
        $order_list_array = $order_list->pluck('order_id')->toArray();


        $query = YH_Finance::select('*')
            ->with(['creator','confirmer','owner','order_er'])
            ->whereIn('order_id',$order_list_array);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        if(!empty($post_data['type']))
        {
            if($post_data['type'] == "income")
            {
                $query->where('finance_type', 1);
            }
            else if($post_data['type'] == "expenditure")
            {
                $query->where('finance_type', 21);
            }
        }

        if(!empty($post_data['finance_type']))
        {
            if(in_array($post_data['finance_type'],[1,21]))
            {
                $query->where('finance_type', $post_data['finance_type']);
            }
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











    /*
     * Finance 财务
     */
    // 【财务管理】返回-列表-视图
    public function view_finance_record_list_for_all($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.finance.finance-list-for-all';
        return view($view_blade)->with($view_data);
    }
    // 【财务管理】返回-列表-数据
    public function get_finance_record_list_for_all_datatable($post_data)
    {
        $this->get_me();
        $me = $this->me;

//        $id  = $post_data["id"];
        $query = YH_Finance::select('*')
            ->with(['creator','confirmer','order_er']);

        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);

        if(!empty($post_data['transaction_time'])) $query->whereDate(DB::raw("FROM_UNIXTIME(transaction_time,'%Y-%m-%d')"), $post_data['transaction_time']);


        if(!empty($post_data['item_type']))
        {
            if(in_array($post_data['item_type'],[1,21]))
            {
                $query->where('item_type', $post_data['item_type']);
            }
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
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);

//            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
//            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【财务管理】确认
    public function operate_finance_confirm($post_data)
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
        if($operate != 'finance-confirm') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Finance::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 权限
        if(!in_array($me->user_type,[41,42])) return response_error([],"用户类型错误，只有财务人员有权限确认！");
//        if(me->user_type ==88 && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_confirmed = 1;
            $item->confirmer_id = $me->id;
            $item->confirmed_at = time();
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $order = YH_Order::find($item->order_id);
                if(!$order) return response_error([],"该订单不存在，刷新页面重试！");

                if($item->finance_type == 1)
                {
                    $order->income_to_be_confirm = $order->income_to_be_confirm - $item->transaction_amount;
                    $order->income_total = $order->income_total + $item->transaction_amount;
                }
                else if($item->finance_type == 21)
                {
                    $order->expenditure_to_be_confirm = $order->expenditure_to_be_confirm - $item->transaction_amount;
                    $order->expenditure_total = $order->expenditure_total + $item->transaction_amount;
                }

                $bool_1 = $order->save();
                if(!$bool_1) throw new Exception("update--order--fail");


//                $record = new YH_Record;
//
//                $record_data["record_category"] = 41;
//                $record_data["record_type"] = 100;
//                $record_data["creator_id"] = $me->id;
//                $record_data["order_id"] = $id;
//                $record_data["operate_category"] = 1;
//                $record_data["operate_type"] = 1;
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
    // 【订单管理】删除
    public function operate_finance_delete($post_data)
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
        if($operate != 'finance-delete') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = YH_Finance::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,41,42,81,82,88])) return response_error([],"用户类型错误！");
//        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");
        if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");
        if($item->is_confirmed == 1 && !in_array($me->user_type,[41,42])) return response_error([],"已确认不能删除！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("finance--delete--fail");
            else
            {
                $order = YH_Order::find($item->order_id);
                if(!$order) return response_error([],"该订单不存在，刷新页面重试！");

                if($item->is_confirmed == 0)
                {
                    if($item->finance_type == 1)
                    {
                        $order->income_to_be_confirm = $order->income_to_be_confirm - $item->transaction_amount;
                    }
                    else if($item->finance_type == 21)
                    {
                        $order->expenditure_to_be_confirm = $order->expenditure_to_be_confirm - $item->transaction_amount;
                    }
                }
                else if($item->is_confirmed == 1)
                {
                    if($item->finance_type == 1)
                    {
                        $order->income_total = $order->income_total - $item->transaction_amount;
                    }
                    else if($item->finance_type == 21)
                    {
                        $order->expenditure_total = $order->expenditure_total - $item->transaction_amount;
                    }
                }

                $bool_1 = $order->save();
                if(!$bool_1) throw new Exception("update--order--fail");

//                $record = new YH_Record;
//
//                $record_data["record_category"] = 1;
//                $record_data["record_type"] = 99;
//                $record_data["creator_id"] = $me->id;
//                $record_data["order_id"] = $item_id;
//                $record_data["operate_category"] = 1;
//                $record_data["operate_type"] = 99;
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









    /*
     * Statistic 流量统计
     */
    // 【流量统计】
    public function view_statistic_index()
    {
        $this->get_me();
        $me = $this->me;

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));

        $staff = [];
        $sales = YH_User::select('id','true_name')->where('user_category',11)->whereIn('user_type',[88])->get();
        foreach($sales as $key => $val)
        {
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
                ->where(['is_completed'=>1,'owner_id'=>$val->id]);

            $staff[$val->true_name]['all'] = $query->get()->keyBy('day');
            $staff[$val->true_name]['dialog'] = $query->whereIn('item_result',[1,19])->get()->keyBy('day');
            $staff[$val->true_name]['wx'] = $query->where('item_result',19)->get()->keyBy('day');
        }


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
            ->where(['is_completed'=>1]);

        $all = $query->get()->keyBy('day');
        $dialog = $query->whereIn('item_result',[1,19,51])->get()->keyBy('day');
        $plus_wx = $query->where('item_result',19)->get()->keyBy('day');




        // 总转化率【占比】
        $all_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1])
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
            ->where(['is_completed'=>1])
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


        $view_data["staff"] = $staff;
        $view_data["all"] = $all;
        $view_data["dialog"] = $dialog;
        $view_data["plus_wx"] = $plus_wx;
        $view_data["all_rate"] = $all_rate;
        $view_data["today_rate"] = $today_rate;

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-index';
        return view($view_blade)->with($view_data);
    }
    // 【流量统计】
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
        $user = YH_User::find($user_id);

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

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-user';
        return view($view_blade)->with($view_data);
    }
    // 【流量统计】
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

        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-item';
        return view($view_blade)->with($view_data);
    }


    // 【流量统计】返回-列表-视图
    public function view_statistic_list_for_all($post_data)
    {
        $view_data["menu_active_statistic_list_for_all"] = 'active';
        $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-list-for-all';
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








    /*
     * 说明
     *
     */




}