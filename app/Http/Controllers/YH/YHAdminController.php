<?php
namespace App\Http\Controllers\YH;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\YH\YH_User;
use App\Models\YH\YH_Item;

use App\Repositories\YH\YHAdminRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class YHAdminController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new YHAdminRepository;
    }




    // 登陆
    public function login()
    {
        if(request()->isMethod('get'))
        {
            $view_blade = env('TEMPLATE_YH_ADMIN').'entrance.login';
            return view($view_blade);
        }
        else if(request()->isMethod('post'))
        {
            $where['email'] = request()->get('email');
            $where['mobile'] = request()->get('mobile');
            $where['password'] = request()->get('password');

//            $email = request()->get('email');
//            $admin = SuperAdministrator::whereEmail($email)->first();

            $mobile = request()->get('mobile');
            $admin = YH_User::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('yh_admin')->login($admin,true);
                        else Auth::guard('yh_admin')->login($admin,true);
                        return response_success();
                    }
                    else return response_error([],'账户or密码不正确！');
                }
                else return response_error([],'账户已禁用！');
            }
            else return response_error([],'账户不存在！');
        }
    }

    // 退出
    public function logout()
    {
        Auth::guard('yh_admin')->logout();
        return redirect('/login');
    }




    // 返回主页视图
    public function view_admin_index()
    {
        return $this->repo->view_admin_index();
    }


    /*
     * 用户基本信息
     */
    // 【基本信息】返回-视图
    public function view_my_profile_info_index()
    {
        return $this->repo->view_my_profile_info_index();
    }
    // 【基本信息】编辑
    public function operate_my_profile_info_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_my_profile_info_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_my_profile_info_save(request()->all());
    }
    // 【基本信息】修改-密码
    public function operate_my_account_password_change()
    {
        if(request()->isMethod('get')) return $this->repo->view_my_account_password_change();
        else if (request()->isMethod('post')) return $this->repo->operate_my_account_password_change_save(request()->all());
    }




    /*
     * USER-STAFF 用户-员工管理
     *
     */
    // 【用户】SELECT2 District
    public function operate_user_select2_sales()
    {
        return $this->repo->operate_user_select2_sales(request()->all());
    }

    // 【用户-员工管理】添加
    public function operate_user_staff_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }
    // 【用户-员工管理】编辑
    public function operate_user_staff_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }


    // 【用户】登录
    public function operate_user_user_login()
    {
        $user_id = request()->get('user_id');
        $user = User::where('id',$user_id)->first();
        if($user)
        {
//            $type = request()->get('type','');
//            if($type == "admin")
//            {
//                $admin_id = request()->get('admin_id');
//                $admin = User::where('id',$admin_id)->first();
//
//                Auth::guard('gps')->login($user,true);
//
//                if(request()->isMethod('get')) return redirect(env('DOMAIN_GPS').'/admin');
//                else if(request()->isMethod('post')) return response_success();
//
//            }

            Auth::guard('staff')->login($user,true);

            $return['user'] = $user;

            if(request()->isMethod('get')) return redirect(env('DOMAIN_STAFF'));
            else if(request()->isMethod('post')) return response_success($return);
        }
        else return response_error([]);

    }
    // 【用户】修改-密码
    public function operate_user_staff_password_admin_change()
    {
        return $this->repo->operate_user_staff_password_admin_change(request()->all());
    }
    // 【用户】修改-密码
    public function operate_user_staff_password_admin_reset()
    {
        return $this->repo->operate_user_staff_password_admin_reset(request()->all());
    }


    // 【用户-员工管理】管理员-删除（）
    public function operate_user_staff_admin_delete()
    {
        return $this->repo->operate_user_staff_admin_delete(request()->all());
    }
    // 【用户-员工管理】管理员-恢复
    public function operate_user_staff_admin_restore()
    {
        return $this->repo->operate_user_staff_admin_restore(request()->all());
    }
    // 【用户-员工管理】管理员-永久删除
    public function operate_user_staff_admin_delete_permanently()
    {
        return $this->repo->operate_user_staff_admin_delete_permanently(request()->all());
    }


    // 【用户-员工管理】启用
    public function operate_user_staff_admin_enable()
    {
        return $this->repo->operate_user_staff_admin_enable(request()->all());
    }
    // 【用户-员工管理】禁用
    public function operate_user_staff_admin_disable()
    {
        return $this->repo->operate_user_staff_admin_disable(request()->all());
    }




    // 【员工管理】【全部用户】返回-列表-视图
    public function view_staff_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_staff_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_staff_list_for_all_datatable(request()->all());
    }
    // 【用户】【个人用户】返回-列表-视图
    public function view_user_list_for_individual()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_list_for_individual(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_list_for_individual_datatable(request()->all());
    }
    // 【用户】【组织】返回-列表-视图
    public function view_user_list_for_org()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_list_for_org(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_list_for_org_datatable(request()->all());
    }







    /*
     * 客户管理
     */
    // 【客户管理】添加
    public function operate_user_client_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_client_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_client_save(request()->all());
    }
    // 【客户管理】编辑
    public function operate_user_client_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_client_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_client_save(request()->all());
    }


    // 【客户管理】启用
    public function operate_user_client_admin_enable()
    {
        return $this->repo->operate_user_client_admin_enable(request()->all());
    }
    // 【客户管理】禁用
    public function operate_user_client_admin_disable()
    {
        return $this->repo->operate_user_client_admin_disable(request()->all());
    }


    // 【客户管理】返回-列表-视图
    public function view_user_client_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_client_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_client_list_for_all_datatable(request()->all());
    }







    /*
     * ITEM 内容管理
     */
    // 【内容】返回-列表-视图（全部内容）
    public function view_item_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_list_for_all_datatable(request()->all());
    }


    // 【内容】添加
    public function operate_item_item_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_item_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_item_save(request()->all());
    }
    // 【内容】编辑
    public function operate_item_item_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_item_edit(request()->all());
        else if (request()->isMethod('post')) return $this->repo->operate_item_item_save(request()->all());
    }

    // 【内容】获取-详情
    public function operate_item_item_get()
    {
        return $this->repo->operate_item_item_get(request()->all());
    }


    // 【内容】删除
    public function operate_item_item_delete()
    {
        return $this->repo->operate_item_item_delete(request()->all());
    }
    // 【内容】恢复
    public function operate_item_item_restore()
    {
        return $this->repo->operate_item_item_restore(request()->all());
    }
    // 【内容】永久删除
    public function operate_item_item_delete_permanently()
    {
        return $this->repo->operate_item_item_delete_permanently(request()->all());
    }


    // 【内容】批量-删除
    public function operate_item_item_delete_bulk()
    {
        return $this->repo->operate_item_item_delete_bulk(request()->all());
    }
    // 【内容】批量-恢复
    public function operate_item_item_restore_bulk()
    {
        return $this->repo->operate_item_item_restore_bulk(request()->all());
    }
    // 【内容】批量-彻底删除
    public function operate_item_item_delete_permanently_bulk()
    {
        return $this->repo->operate_item_item_delete_permanently_bulk(request()->all());
    }
    // 【内容】批量-操作
    public function operate_item_item_operate_bulk()
    {
        return $this->repo->operate_item_item_operate_bulk(request()->all());
    }


    // 【内容】发布
    public function operate_item_item_publish()
    {
        return $this->repo->operate_item_item_publish(request()->all());
    }
    // 【内容】完成
    public function operate_item_item_complete()
    {
        return $this->repo->operate_item_item_complete(request()->all());
    }
    // 【内容】启用
    public function operate_item_item_enable()
    {
        return $this->repo->operate_item_item_enable(request()->all());
    }
    // 【内容】禁用
    public function operate_item_item_disable()
    {
        return $this->repo->operate_item_item_disable(request()->all());
    }








    /*
     * 任务管理
     */
    // 【任务】导入
    public function operate_item_task_list_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_task_list_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_task_list_import_save(request()->all());
    }

    // 【任务】返回-列表-视图（全部任务）
    public function view_task_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_task_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_task_list_for_all_datatable(request()->all());
    }


    // 【任务】添加
    public function operate_item_task_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_task_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_task_save(request()->all());
    }
    // 【任务】编辑
    public function operate_item_task_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_task_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_task_save(request()->all());
    }

    // 【任务】获取-详情
    public function operate_item_task_get()
    {
        return $this->repo->operate_item_task_get(request()->all());
    }
    // 【任务】删除
    public function operate_item_task_delete()
    {
        return $this->repo->operate_item_task_delete(request()->all());
    }
    // 【任务】恢复
    public function operate_item_task_restore()
    {
        return $this->repo->operate_item_task_restore(request()->all());
    }
    // 【任务】永久删除
    public function operate_item_task_delete_permanently()
    {
        return $this->repo->operate_item_task_delete_permanently(request()->all());
    }
    // 【任务】发布
    public function operate_item_task_publish()
    {
        return $this->repo->operate_item_task_publish(request()->all());
    }
    // 【任务】完成
    public function operate_item_task_complete()
    {
        return $this->repo->operate_item_task_complete(request()->all());
    }
    // 【任务】启用
    public function operate_item_task_enable()
    {
        return $this->repo->operate_item_task_enable(request()->all());
    }
    // 【任务】禁用
    public function operate_item_task_disable()
    {
        return $this->repo->operate_item_task_disable(request()->all());
    }

    // 【任务】备注编辑
    public function operate_item_task_remark_edit()
    {
        return $this->repo->operate_item_task_remark_save(request()->all());
    }



    /*
     * Task 任务管理
     */
    // 【任务管理】管理员-删除
    public function operate_item_task_admin_delete()
    {
        return $this->repo->operate_item_task_admin_delete(request()->all());
    }
    // 【任务管理】管理员-恢复
    public function operate_item_task_admin_restore()
    {
        return $this->repo->operate_item_task_admin_restore(request()->all());
    }
    // 【任务管理】管理员-永久删除
    public function operate_item_task_admin_delete_permanently()
    {
        return $this->repo->operate_item_task_admin_delete_permanently(request()->all());
    }
    // 【任务管理】管理员-启用
    public function operate_item_task_admin_enable()
    {
        return $this->repo->operate_item_task_admin_enable(request()->all());
    }
    // 【任务管理】管理员-禁用
    public function operate_item_task_admin_disable()
    {
        return $this->repo->operate_item_task_admin_disable(request()->all());
    }


    // 【任务管理】管理员-批量-操作
    public function operate_item_task_admin_operate_bulk()
    {
        return $this->repo->operate_item_task_admin_operate_bulk(request()->all());
    }
    // 【任务管理】管理员-批量-删除
    public function operate_item_task_admin_delete_bulk()
    {
        return $this->repo->operate_item_task_admin_delete_bulk(request()->all());
    }
    // 【任务管理】管理员-批量-恢复
    public function operate_item_task_admin_restore_bulk()
    {
        return $this->repo->operate_item_task_admin_restore_bulk(request()->all());
    }
    // 【任务管理】管理员-批量-彻底删除
    public function operate_item_task_admin_delete_permanently_bulk()
    {
        return $this->repo->operate_item_task_admin_delete_permanently_bulk(request()->all());
    }







    /*
     * 车辆管理
     */
    // 【车辆管理】返回-列表-视图（全部任务）
    public function view_item_car_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_car_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_car_list_for_all_datatable(request()->all());
    }


    // 【车辆管理】添加
    public function operate_item_car_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_car_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_car_save(request()->all());
    }
    // 【车辆管理】编辑
    public function operate_item_car_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_car_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_car_save(request()->all());
    }


    // 【车辆管理】启用
    public function operate_item_car_admin_enable()
    {
        return $this->repo->operate_item_car_admin_enable(request()->all());
    }
    // 【车辆管理】禁用
    public function operate_item_car_admin_disable()
    {
        return $this->repo->operate_item_car_admin_disable(request()->all());
    }








    /*
     * 订单管理
     */
    // 【订单管理】返回-列表-视图（全部任务）
    public function view_item_order_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_list_for_all_datatable(request()->all());
    }


    // 【订单管理】SELECT2 Client 客户
    public function operate_order_select2_client()
    {
        return $this->repo->operate_order_select2_client(request()->all());
    }
    // 【订单管理】SELECT2 Car 车辆
    public function operate_order_select2_car()
    {
        return $this->repo->operate_order_select2_car(request()->all());
    }
    // 【订单管理】SELECT2 Trailer 车挂
    public function operate_order_select2_trailer()
    {
        return $this->repo->operate_order_select2_trailer(request()->all());
    }

    // 【订单管理】SELECT2 Car 车辆
    public function operate_order_list_select2_car()
    {
        return $this->repo->operate_order_list_select2_car(request()->all());
    }


    // 【订单管理】添加
    public function operate_item_order_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_order_save(request()->all());
    }
    // 【订单管理】编辑
    public function operate_item_order_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_order_save(request()->all());
    }


    // 【订单管理】获取-详情
    public function operate_item_order_get()
    {
        return $this->repo->operate_item_order_get(request()->all());
    }
    // 【订单管理】获取-详情
    public function operate_item_order_get_html()
    {
        return $this->repo->operate_item_order_get_html(request()->all());
    }


    // 【订单管理】获取-详情
    public function operate_item_order_delete()
    {
        return $this->repo->operate_item_order_delete(request()->all());
    }


    // 【订单管理】发布
    public function operate_item_order_publish()
    {
        return $this->repo->operate_item_order_publish(request()->all());
    }


    // 【订单管理】修改-文本信息
    public function operate_item_order_info_text_set()
    {
        return $this->repo->operate_item_order_info_text_set(request()->all());
    }
    // 【订单管理】修改-时间信息
    public function operate_item_order_info_time_set()
    {
        return $this->repo->operate_item_order_info_time_set(request()->all());
    }
    // 【订单管理】修改-SELECT2信息
    public function operate_item_order_info_select_set()
    {
        return $this->repo->operate_item_order_info_select_set(request()->all());
    }
    // 【订单管理】修改-客户信息
    public function operate_item_order_info_client_set()
    {
        return $this->repo->operate_item_order_info_client_set(request()->all());
    }
    // 【订单管理】修改-车辆信息
    public function operate_item_order_info_car_set()
    {
        return $this->repo->operate_item_order_info_car_set(request()->all());
    }

    // 【订单管理】添加-行程记录
    public function operate_item_order_travel_set()
    {
        return $this->repo->operate_item_order_travel_set(request()->all());
    }




    // 【订单管理-财务往来记录】返回-列表-视图（全部任务）
    public function view_item_order_finance_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_finance_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_finance_record_datatable(request()->all());
    }

    // 【订单管理】添加-财务记录
    public function operate_item_order_finance_record_create()
    {
        return $this->repo->operate_item_order_finance_record_create(request()->all());
    }
    // 【订单管理】修改-财务记录
    public function operate_item_order_finance_record_edit()
    {
        return $this->repo->operate_item_order_finance_record_edit(request()->all());
    }




    // 【订单管理-修改记录】返回-列表-视图（全部任务）
    public function view_item_order_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_modify_record_datatable(request()->all());
    }








    /*
     * Finance 财务
     */
    // 【财务】返回-全部内容-列表-视图
    public function view_finance_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_record_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_record_list_for_all_datatable(request()->all());
    }











    /*
     * Statistic 统计
     */
    // 【统计】概览
    public function view_statistic_index()
    {
        return $this->repo->view_statistic_index();
    }
    // 【统计】用户
    public function view_statistic_user()
    {
        return $this->repo->view_statistic_user(request()->all());
    }
    // 【统计】内容
    public function view_statistic_item()
    {
        return $this->repo->view_statistic_item(request()->all());
    }


    // 【统计】返回-全部内容-列表-视图
    public function view_statistic_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_list_for_all_datatable(request()->all());
    }







}
