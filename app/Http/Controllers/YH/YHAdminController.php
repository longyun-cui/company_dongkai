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


    // 返回主页视图
    public function view_admin_404()
    {
        return $this->repo->view_admin_404();
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
     * 驾驶员管理
     */
    // 【驾驶员管理】返回-列表-视图（全部任务）
    public function view_user_driver_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_driver_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_driver_list_for_all_datatable(request()->all());
    }
    // 【驾驶员管理】【修改记录】返回-列表-视图（全部任务）
    public function view_user_driver_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_driver_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_driver_modify_record_datatable(request()->all());
    }


    // 【驾驶员管理】添加
    public function operate_user_driver_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_driver_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_driver_save(request()->all());
    }
    // 【驾驶员管理】编辑
    public function operate_user_driver_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_driver_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_driver_save(request()->all());
    }


    // 【驾驶员管理】修改-文本-text-信息
    public function operate_user_driver_info_text_set()
    {
        return $this->repo->operate_user_driver_info_text_set(request()->all());
    }
    // 【驾驶员管理】修改-时间-time-信息
    public function operate_user_driver_info_time_set()
    {
        return $this->repo->operate_user_driver_info_time_set(request()->all());
    }
    // 【驾驶员管理】修改-选项-option-信息
    public function operate_user_driver_info_option_set()
    {
        return $this->repo->operate_user_driver_info_option_set(request()->all());
    }
    // 【驾驶员管理】修改-图片-image-信息
    public function operate_user_driver_info_image_set()
    {
        return $this->repo->operate_user_driver_info_image_set(request()->all());
    }

    // 【驾驶员管理】获取-附件-attachment-HTML-页面
    public function operate_user_driver_info_attachment_get_html()
    {
        return $this->repo->operate_user_driver_info_attachment_get_html(request()->all());
    }
    // 【驾驶员管理】添加-附件-attachment-信息
    public function operate_user_driver_info_attachment_set()
    {
        return $this->repo->operate_user_driver_info_attachment_set(request()->all());
    }
    // 【驾驶员管理】删除-附件-attachment-信息
    public function operate_user_driver_info_attachment_delete()
    {
        return $this->repo->operate_user_driver_info_attachment_delete(request()->all());
    }
    // 【驾驶员管理】获取-附件-attachment-信息
    public function operate_user_driver_get_attachment_html()
    {
        return $this->repo->operate_user_driver_get_attachment_html(request()->all());
    }


    // 【驾驶员管理】删除
    public function operate_user_driver_admin_delete()
    {
        return $this->repo->operate_user_driver_admin_delete(request()->all());
    }
    // 【驾驶员管理】恢复
    public function operate_user_driver_admin_restore()
    {
        return $this->repo->operate_user_driver_admin_restore(request()->all());
    }
    // 【驾驶员管理】永久删除
    public function operate_user_driver_admin_delete_permanently()
    {
        return $this->repo->operate_user_driver_admin_delete_permanently(request()->all());
    }

    // 【驾驶员管理】启用
    public function operate_user_driver_admin_enable()
    {
        return $this->repo->operate_user_driver_admin_enable(request()->all());
    }
    // 【驾驶员管理】禁用
    public function operate_user_driver_admin_disable()
    {
        return $this->repo->operate_user_driver_admin_disable(request()->all());
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



    /*
     * Task 任务管理
     */


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
    // 【车辆管理】【修改记录】返回-列表-视图（全部任务）
    public function view_item_car_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_car_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_car_modify_record_datatable(request()->all());
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


    // 【车辆管理】修改-文本-text-信息
    public function operate_item_car_info_text_set()
    {
        return $this->repo->operate_item_car_info_text_set(request()->all());
    }
    // 【车辆管理】修改-时间-time-信息
    public function operate_item_car_info_time_set()
    {
        return $this->repo->operate_item_car_info_time_set(request()->all());
    }
    // 【车辆管理】修改-选项-option-信息
    public function operate_item_car_info_option_set()
    {
        return $this->repo->operate_item_car_info_option_set(request()->all());
    }
    // 【车辆管理】添加-附件-attachment-信息
    public function operate_item_car_info_attachment_set()
    {
        return $this->repo->operate_item_car_info_attachment_set(request()->all());
    }
    // 【车辆管理】删除-附件-attachment-信息
    public function operate_item_car_info_attachment_delete()
    {
        return $this->repo->operate_item_car_info_attachment_delete(request()->all());
    }
    // 【车辆管理】获取-附件-attachment-信息
    public function operate_item_car_get_attachment_html()
    {
        return $this->repo->operate_item_car_get_attachment_html(request()->all());
    }


    // 【车辆管理】删除
    public function operate_item_car_admin_delete()
    {
        return $this->repo->operate_item_car_admin_delete(request()->all());
    }
    // 【车辆管理】恢复
    public function operate_item_car_admin_restore()
    {
        return $this->repo->operate_item_car_admin_restore(request()->all());
    }
    // 【车辆管理】永久删除
    public function operate_item_car_admin_delete_permanently()
    {
        return $this->repo->operate_item_car_admin_delete_permanently(request()->all());
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
     * 固定线路
     */
    // 【固定线路】返回-列表-视图（全部任务）
    public function view_item_route_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_route_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_route_list_for_all_datatable(request()->all());
    }
    // 【固定线路】【修改记录】返回-列表-视图（全部任务）
    public function view_item_route_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_route_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_route_modify_record_datatable(request()->all());
    }


    // 【固定线路】添加
    public function operate_item_route_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_route_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_route_save(request()->all());
    }
    // 【固定线路】编辑
    public function operate_item_route_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_route_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_route_save(request()->all());
    }


    // 【固定线路】修改-文本-text-信息
    public function operate_item_route_info_text_set()
    {
        return $this->repo->operate_item_route_info_text_set(request()->all());
    }
    // 【固定线路】修改-时间-time-信息
    public function operate_item_route_info_time_set()
    {
        return $this->repo->operate_item_route_info_time_set(request()->all());
    }
    // 【固定线路】修改-选项-option-信息
    public function operate_item_route_info_option_set()
    {
        return $this->repo->operate_item_route_info_option_set(request()->all());
    }
    // 【定价管理】添加-附件-attachment-信息
    public function operate_item_route_info_attachment_set()
    {
        return $this->repo->operate_item_route_info_attachment_set(request()->all());
    }
    // 【定价管理】删除-附件-attachment-信息
    public function operate_item_route_info_attachment_delete()
    {
        return $this->repo->operate_item_route_info_attachment_delete(request()->all());
    }
    // 【定价管理】获取-附件-attachment-信息
    public function operate_item_route_get_attachment_html()
    {
        return $this->repo->operate_item_route_get_attachment_html(request()->all());
    }


    // 【固定线路】删除
    public function operate_item_route_admin_delete()
    {
        return $this->repo->operate_item_route_admin_delete(request()->all());
    }
    // 【固定线路】恢复
    public function operate_item_route_admin_restore()
    {
        return $this->repo->operate_item_route_admin_restore(request()->all());
    }
    // 【固定线路】永久删除
    public function operate_item_route_admin_delete_permanently()
    {
        return $this->repo->operate_item_route_admin_delete_permanently(request()->all());
    }

    // 【固定线路】启用
    public function operate_item_route_admin_enable()
    {
        return $this->repo->operate_item_route_admin_enable(request()->all());
    }
    // 【固定线路】禁用
    public function operate_item_route_admin_disable()
    {
        return $this->repo->operate_item_route_admin_disable(request()->all());
    }








    /*
     * 定价管理
     */
    // 【定价管理】返回-列表-视图
    public function view_item_pricing_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_pricing_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_pricing_list_for_all_datatable(request()->all());
    }
    // 【定价管理】【修改记录】返回-列表-视图（全部任务）
    public function view_item_pricing_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_pricing_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_pricing_modify_record_datatable(request()->all());
    }


    // 【定价管理】添加
    public function operate_item_pricing_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_pricing_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_pricing_save(request()->all());
    }
    // 【定价管理】编辑
    public function operate_item_pricing_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_pricing_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_pricing_save(request()->all());
    }


    // 【定价管理】修改-文本-text-信息
    public function operate_item_pricing_info_text_set()
    {
        return $this->repo->operate_item_pricing_info_text_set(request()->all());
    }
    // 【定价管理】修改-时间-time-信息
    public function operate_item_pricing_info_time_set()
    {
        return $this->repo->operate_item_pricing_info_time_set(request()->all());
    }
    // 【定价管理】修改-选项-option-信息
    public function operate_item_pricing_info_option_set()
    {
        return $this->repo->operate_item_pricing_info_option_set(request()->all());
    }
    // 【定价管理】添加-附件-attachment-信息
    public function operate_item_pricing_info_attachment_set()
    {
        return $this->repo->operate_item_pricing_info_attachment_set(request()->all());
    }
    // 【定价管理】删除-附件-attachment-信息
    public function operate_item_pricing_info_attachment_delete()
    {
        return $this->repo->operate_item_pricing_info_attachment_delete(request()->all());
    }
    // 【定价管理】获取-附件-attachment-信息
    public function operate_item_pricing_get_attachment_html()
    {
        return $this->repo->operate_item_pricing_get_attachment_html(request()->all());
    }


    // 【定价管理】删除
    public function operate_item_pricing_admin_delete()
    {
        return $this->repo->operate_item_pricing_admin_delete(request()->all());
    }
    // 【定价管理】恢复
    public function operate_item_pricing_admin_restore()
    {
        return $this->repo->operate_item_pricing_admin_restore(request()->all());
    }
    // 【定价管理】永久删除
    public function operate_item_pricing_admin_delete_permanently()
    {
        return $this->repo->operate_item_pricing_admin_delete_permanently(request()->all());
    }

    // 【定价管理】启用
    public function operate_item_pricing_admin_enable()
    {
        return $this->repo->operate_item_pricing_admin_enable(request()->all());
    }
    // 【定价管理】禁用
    public function operate_item_pricing_admin_disable()
    {
        return $this->repo->operate_item_pricing_admin_disable(request()->all());
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
    // 【订单管理】SELECT2 Circle 环线
    public function operate_order_select2_circle()
    {
        return $this->repo->operate_order_select2_circle(request()->all());
    }
    // 【订单管理】SELECT2 Route 线路
    public function operate_order_select2_route()
    {
        return $this->repo->operate_order_select2_route(request()->all());
    }
    // 【订单管理】SELECT2 Pricing 定价
    public function operate_order_select2_pricing()
    {
        return $this->repo->operate_order_select2_pricing(request()->all());
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
    // 【订单管理】SELECT2 Trailer 车挂
    public function operate_order_select2_driver()
    {
        return $this->repo->operate_order_select2_driver(request()->all());
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

    // 【订单管理】导入
    public function operate_item_order_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_order_import_save(request()->all());
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
    // 【订单管理】获取-附件
    public function operate_item_order_get_attachment_html()
    {
        return $this->repo->operate_item_order_get_attachment_html(request()->all());
    }


    // 【订单管理】删除
    public function operate_item_order_delete()
    {
        return $this->repo->operate_item_order_delete(request()->all());
    }
    // 【订单管理】发布
    public function operate_item_order_publish()
    {
        return $this->repo->operate_item_order_publish(request()->all());
    }
    // 【订单管理】完成
    public function operate_item_order_complete()
    {
        return $this->repo->operate_item_order_complete(request()->all());
    }
    // 【订单管理】弃用
    public function operate_item_order_abandon()
    {
        return $this->repo->operate_item_order_abandon(request()->all());
    }
    // 【订单管理】复用
    public function operate_item_order_reuse()
    {
        return $this->repo->operate_item_order_reuse(request()->all());
    }
    // 【内容】审核
    public function operate_item_order_verify()
    {
        return $this->repo->operate_item_order_verify(request()->all());
    }



    // 【订单管理】修改-文本-信息
    public function operate_item_order_info_text_set()
    {
        return $this->repo->operate_item_order_info_text_set(request()->all());
    }
    // 【订单管理】修改-时间-信息
    public function operate_item_order_info_time_set()
    {
        return $this->repo->operate_item_order_info_time_set(request()->all());
    }
    // 【订单管理】修改-option-信息
    public function operate_item_order_info_option_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【订单管理】修改-radio-信息
    public function operate_item_order_info_radio_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【订单管理】修改-select-信息
    public function operate_item_order_info_select_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【订单管理】添加-attachment-信息
    public function operate_item_order_info_attachment_set()
    {
        return $this->repo->operate_item_order_info_attachment_set(request()->all());
    }
    // 【订单管理】删除-attachment-信息
    public function operate_item_order_info_attachment_delete()
    {
        return $this->repo->operate_item_order_info_attachment_delete(request()->all());
    }
    // 【订单管理】修改-客户信息
    public function operate_item_order_info_client_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【订单管理】修改-车辆信息
    public function operate_item_order_info_car_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
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
     * 环线管理
     */
    // 【环线管理】返回-列表-视图
    public function view_item_circle_detail()
    {
        return $this->repo->view_item_circle_detail(request()->all());
    }

    // 【环线管理】返回-列表-视图
    public function view_item_circle_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_circle_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_circle_list_for_all_datatable(request()->all());
    }
    // 【环线管理】【修改记录】返回-列表-视图
    public function view_item_circle_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_circle_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_circle_modify_record_datatable(request()->all());
    }


    // 【环线管理】返回-财务-数据
    public function get_item_circle_analysis()
    {
        return $this->repo->get_item_circle_analysis(request()->all());
    }
    // 【环线管理】返回-财务-数据
    public function get_item_circle_finance_record()
    {
        return $this->repo->get_item_circle_finance_record_datatable(request()->all());
    }


    // 【环线管理】SELECT2 Order 订单
    public function operate_circle_select2_order_list()
    {
        return $this->repo->operate_circle_select2_order_list(request()->all());
    }
    // 【环线管理】SELECT2 Car 车辆
    public function operate_circle_select2_car()
    {
        return $this->repo->operate_circle_select2_car(request()->all());
    }


    // 【环线管理】添加
    public function operate_item_circle_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_circle_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_circle_save(request()->all());
    }
    // 【环线管理】编辑
    public function operate_item_circle_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_circle_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_circle_save(request()->all());
    }


    // 【环线管理】修改-文本-text-信息
    public function operate_item_circle_info_text_set()
    {
        return $this->repo->operate_item_circle_info_text_set(request()->all());
    }
    // 【环线管理】修改-时间-time-信息
    public function operate_item_circle_info_time_set()
    {
        return $this->repo->operate_item_circle_info_time_set(request()->all());
    }
    // 【环线管理】修改-选项-option-信息
    public function operate_item_circle_info_option_set()
    {
        return $this->repo->operate_item_circle_info_option_set(request()->all());
    }


    // 【环线管理】删除
    public function operate_item_circle_admin_delete()
    {
        return $this->repo->operate_item_circle_admin_delete(request()->all());
    }
    // 【环线管理】恢复
    public function operate_item_circle_admin_restore()
    {
        return $this->repo->operate_item_circle_admin_restore(request()->all());
    }
    // 【环线管理】永久删除
    public function operate_item_circle_admin_delete_permanently()
    {
        return $this->repo->operate_item_circle_admin_delete_permanently(request()->all());
    }

    // 【环线管理】启用
    public function operate_item_circle_admin_enable()
    {
        return $this->repo->operate_item_circle_admin_enable(request()->all());
    }
    // 【环线管理】禁用
    public function operate_item_circle_admin_disable()
    {
        return $this->repo->operate_item_circle_admin_disable(request()->all());
    }








    /*
     * Finance 财务
     */
    // 【财务管理】返回-全部内容-列表-视图
    public function view_finance_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_record_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_record_list_for_all_datatable(request()->all());
    }
    // 【财务管理】【修改记录】返回-列表-视图
    public function view_finance_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_modify_record_datatable(request()->all());
    }

    // 【财务管理】导入
    public function operate_finance_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_import();
        else if (request()->isMethod('post')) return $this->repo->operate_finance_import_save(request()->all());
    }


    // 【财务管理】删除
    public function operate_finance_delete()
    {
        return $this->repo->operate_finance_delete(request()->all());
    }
    // 【财务管理】完成
    public function operate_finance_confirm()
    {
        return $this->repo->operate_finance_confirm(request()->all());
    }


    // 【环线管理】修改-文本-text-信息
    public function operate_finance_info_text_set()
    {
        return $this->repo->operate_finance_info_text_set(request()->all());
    }
    // 【环线管理】修改-时间-time-信息
    public function operate_finance_info_time_set()
    {
        return $this->repo->operate_finance_info_time_set(request()->all());
    }
    // 【环线管理】修改-选项-option-信息
    public function operate_finance_info_option_set()
    {
        return $this->repo->operate_finance_info_option_set(request()->all());
    }











    /*
     * Statistic 统计
     */
    // 【统计】概览
    public function view_statistic_index()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_index(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data(request()->all());
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


    // 【统计】返回-概览-数据
    public function get_statistic_data_for_comprehensive()
    {
        return $this->repo->get_statistic_data_for_comprehensive(request()->all());
    }
    // 【统计】返回-订单-数据
    public function get_statistic_data_for_order()
    {
        return $this->repo->get_statistic_data_for_order(request()->all());
    }
    // 【统计】返回-财务-数据
    public function get_statistic_data_for_finance()
    {
        return $this->repo->get_statistic_data_for_finance(request()->all());
    }






    /*
     * Export 导出
     */
    // 【统计】导出
    public function operate_statistic_export()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_export(request()->all());
        else if(request()->isMethod('post')) return $this->repo->operate_statistic_export(request()->all());
    }


    // 【统计】订单-导出
    public function operate_statistic_export_for_order()
    {
        $this->repo->operate_statistic_export_for_order(request()->all());
    }
    // 【统计】环线-导出
    public function operate_statistic_export_for_circle()
    {
        $this->repo->operate_statistic_export_for_circle(request()->all());
    }
    // 【统计】财务-导出
    public function operate_statistic_export_for_finance()
    {
        $this->repo->operate_statistic_export_for_finance(request()->all());
    }







}
