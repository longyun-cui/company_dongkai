<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK_Choice\DK_Choice_User;
use App\Models\DK_Choice\DK_Customer;
use App\Models\DK_Choice\DK_Choice_Record_Visit;

use App\Repositories\DK\DKAdmin2_Repository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKAdmin2_Controller extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKAdmin2_Repository;
    }





    // 账号唯一登录
    public function check_is_only_me()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_admin_2')->check())
        {
            $token = request('_token');
            if(Auth::guard('dk_admin_2')->user()->admin_token == $token)
            {
                $result['message'] = 'success';
                $result['result'] = 'access';
            }
        }

        return Response::json($result);
    }

    // 登陆
    public function login()
    {
        if(request()->isMethod('get'))
        {
            $record["record_category"] = 99; // record_category=1 browse/search/share/login
            $record["record_type"] = 0; // record_type=1 browse
            $record["page_type"] = 1; // page_type=1 login
            $record["page_module"] = 1; // page_module=1 login page
            $record["page_num"] = 0;
            $record["open"] = "login";
            $record["from"] = request('from',NULL);
            $this->record_for_user_visit($record);

            $view_blade = env('TEMPLATE_DK_ADMIN_2').'entrance.login';
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
            $admin = DK_Choice_User::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {

                    if($admin->login_error_num >= 3)
                    {
                        return response_error([],'账户or密码不正确啊！');
                    }

                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_admin_2')->login($admin,true);
                        else Auth::guard('dk_admin_2')->login($admin);
                        Auth::guard('dk_admin_2')->user()->login_error_num = 0;
                        Auth::guard('dk_admin_2')->user()->admin_token = $token;
                        Auth::guard('dk_admin_2')->user()->save();

                        if(Auth::guard('dk_admin_2')->user()->id > 10000)
                        {
                            $record["creator_id"] = Auth::guard('dk_admin_2')->user()->id;
                            $record["record_category"] = 99; // record_category=99 browse/search/share/login
                            $record["record_type"] = 1; // record_type=1 browse
                            $record["page_type"] = 1; // page_type=9 login
                            $record["page_module"] = 1; // page_module=9 login success
                            $record["page_num"] = 0;
                            $record["open"] = "login";
                            $record["from"] = request('from',NULL);
                            $this->record_for_user_visit($record);
                        }


                        return response_success();
                    }
                    else
                    {
                        $record["user_id"] = $admin->id;
                        $record["record_category"] = 99; // record_category=1 browse/search/share/login
                        $record["record_type"] = 99; // record_type=1 browse
                        $record["page_type"] = 1; // page_type=9 login
                        $record["page_module"] = 1; // page_module=1 login page
                        $record["page_num"] = 0;
                        $record["open"] = "login";
                        $record["from"] = request('from',NULL);
                        $this->record_for_user_visit($record);

                        $admin->increment('login_error_num');
                        if($admin->login_error_num >= 3)
                        {
                            $admin->user_status = 99;
                            $admin->admin_token = '';
                            $admin->save();
                        }
                        return response_error([],'账户or密码不正确！');
                    }
                }
                else if($admin->user_status == 99)
                {
                    $record["user_id"] = $admin->id;
                    $record["record_category"] = 99; // record_category=1 browse/search/share/login
                    $record["record_type"] = 99; // record_type=1 browse
                    $record["page_type"] = 1; // page_type=9 login
                    $record["page_module"] = 1; // page_module=1 login page
                    $record["page_num"] = 0;
                    $record["open"] = "login";
                    $record["from"] = request('from',NULL);
                    $this->record_for_user_visit($record);

                    $admin->increment('login_error_num');
                    return response_error([],'账户or密码不正确啊！');
                }
                else return response_error([],'账户已禁用！');
            }
            else return response_error([],'账户or密码不正确.');
        }
    }

    // 退出
    public function logout()
    {
        Auth::guard('dk_admin_2')->user()->admin_token = '';
        Auth::guard('dk_admin_2')->user()->save();
        Auth::guard('dk_admin_2')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_admin_2')->logout();
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




    // 【用户】登录
    public function operate_user_user_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_User_2::select('*')->find($user_id);
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

            Auth::guard('user')->login($user,true);

            $return['user'] = $user;

            if(request()->isMethod('get')) return redirect(env('DOMAIN_STAFF'));
            else if(request()->isMethod('post')) return response_success($return);
        }
        else return response_error([]);

    }




    // 【线索】SELECT2 User 员工
    public function operate_item_select2_user()
    {
        return $this->repo->operate_item_select2_user(request()->all());
    }
    // 【线索】SELECT2 Team 团队
    public function operate_item_select2_team()
    {
        return $this->repo->operate_item_select2_team(request()->all());
    }
    // 【线索】SELECT2 Client 项目
    public function operate_select2_project()
    {
        return $this->repo->operate_select2_project(request()->all());
    }
    // 【线索】SELECT2 Client 客户
    public function operate_select2_customer()
    {
        return $this->repo->operate_select2_customer(request()->all());
    }







    /*
     * 客户管理
     */
    // 【客户】返回-列表-视图
    public function view_user_customer_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_customer_list_datatable(request()->all());
    }
    // 【客户】【修改记录】返回-列表-视图（全部任务）
    public function view_user_customer_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_customer_modify_record_datatable(request()->all());
    }

    // 【客户】添加
    public function operate_user_customer_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_customer_save(request()->all());
    }
    // 【客户】编辑
    public function operate_user_customer_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_customer_save(request()->all());
    }

    // 【客户】修改-文本-text-信息
    public function operate_customer_info_text_set()
    {
        return $this->repo->operate_customer_info_text_set(request()->all());
    }
    // 【客户】修改-时间-time-信息
    public function operate_customer_info_time_set()
    {
        return $this->repo->operate_customer_info_time_set(request()->all());
    }
    // 【客户】修改-选项-option-信息
    public function operate_customer_info_option_set()
    {
        return $this->repo->operate_customer_info_option_set(request()->all());
    }
    // 【客户】添加-附件-attachment-信息
    public function operate_customer_info_attachment_set()
    {
        return $this->repo->operate_customer_info_attachment_set(request()->all());
    }
    // 【客户】删除-附件-attachment-信息
    public function operate_customer_info_attachment_delete()
    {
        return $this->repo->operate_customer_info_attachment_delete(request()->all());
    }
    // 【客户】获取-附件-attachment-信息
    public function operate_customer_get_attachment_html()
    {
        return $this->repo->operate_customer_get_attachment_html(request()->all());
    }

    // 【客户】登录
    public function operate_user_customer_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Client::select('*')->find($user_id);
        if($user)
        {
            Auth::guard('dk_customer')->login($user,true);

            $return['user'] = $user;

            if(request()->isMethod('get')) return redirect(env('DOMAIN_CLIENT'));
            else if(request()->isMethod('post')) return response_success($return);
        }
        else return response_error([]);

    }

    // 【客户】修改-密码
    public function operate_user_customer_password_admin_change()
    {
        return $this->repo->operate_user_customer_password_admin_change(request()->all());
    }
    // 【客户】修改-密码
    public function operate_user_customer_password_admin_reset()
    {
        return $this->repo->operate_user_customer_password_admin_reset(request()->all());
    }

    // 【客户】启用
    public function operate_user_customer_admin_enable()
    {
        return $this->repo->operate_user_customer_admin_enable(request()->all());
    }
    // 【客户】禁用
    public function operate_user_customer_admin_disable()
    {
        return $this->repo->operate_user_customer_admin_disable(request()->all());
    }



    // 【客户】【财务往来记录-充值】返回-列表-视图（全部任务）
    public function view_user_customer_finance_recharge_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_finance_recharge_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_customer_recharge_record_datatable(request()->all());
    }

    // 【客户】【财务往来记录-充值】添加-财务数据-保存数据（充值）
    public function operate_user_customer_finance_recharge_create()
    {
        return $this->repo->operate_user_customer_finance_recharge_create(request()->all());
    }
    // 【客户】【财务往来记录-充值】修改-财务数据-保存数据（充值）
    public function operate_user_customer_finance_recharge_edit()
    {
        return $this->repo->operate_user_customer_finance_recharge_edit(request()->all());
    }


    // 【客户】【财务往来记录-使用】返回-列表-视图（全部任务）
    public function view_user_customer_funds_using_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_funds_using_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_company_funds_using_record_datatable(request()->all());
    }









    /*
     * 部门管理
     */
    // 【部门】SELECT2 Leader 负责人
    public function operate_department_select2_leader()
    {
        return $this->repo->operate_department_select2_leader(request()->all());
    }
    // 【部门】SELECT2 Superior 上级部门
    public function operate_department_select2_superior_department()
    {
        return $this->repo->operate_department_select2_superior_department(request()->all());
    }

    // 【部门】返回-列表-视图（全部任务）
    public function view_department_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_department_list_for_all_datatable(request()->all());
    }
    // 【部门】【修改记录】返回-列表-视图（全部任务）
    public function view_department_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_department_modify_record_datatable(request()->all());
    }

    // 【部门】添加
    public function operate_department_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_create();
        else if (request()->isMethod('post')) return $this->repo->operate_department_save(request()->all());
    }
    // 【部门】编辑
    public function operate_department_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_department_save(request()->all());
    }

    // 【部门】修改-文本-text-信息
    public function operate_department_info_text_set()
    {
        return $this->repo->operate_department_info_text_set(request()->all());
    }
    // 【部门】修改-时间-time-信息
    public function operate_department_info_time_set()
    {
        return $this->repo->operate_department_info_time_set(request()->all());
    }
    // 【部门】修改-选项-option-信息
    public function operate_department_info_option_set()
    {
        return $this->repo->operate_department_info_option_set(request()->all());
    }
    // 【部门】添加-附件-attachment-信息
    public function operate_department_info_attachment_set()
    {
        return $this->repo->operate_department_info_attachment_set(request()->all());
    }
    // 【部门】删除-附件-attachment-信息
    public function operate_department_info_attachment_delete()
    {
        return $this->repo->operate_department_info_attachment_delete(request()->all());
    }
    // 【部门】获取-附件-attachment-信息
    public function operate_department_get_attachment_html()
    {
        return $this->repo->operate_department_get_attachment_html(request()->all());
    }

    // 【部门】删除
    public function operate_department_admin_delete()
    {
        return $this->repo->operate_department_admin_delete(request()->all());
    }
    // 【部门】恢复
    public function operate_department_admin_restore()
    {
        return $this->repo->operate_department_admin_restore(request()->all());
    }
    // 【部门】永久删除
    public function operate_department_admin_delete_permanently()
    {
        return $this->repo->operate_department_admin_delete_permanently(request()->all());
    }

    // 【部门】启用
    public function operate_department_admin_enable()
    {
        return $this->repo->operate_department_admin_enable(request()->all());
    }
    // 【部门】禁用
    public function operate_department_admin_disable()
    {
        return $this->repo->operate_department_admin_disable(request()->all());
    }








    /*
     * USER-STAFF 用户-员工管理
     *
     */
    // 【用户】SELECT2 District
    public function operate_user_select2_choice()
    {
        return $this->repo->operate_user_select2_choice(request()->all());
    }

    // 【用户】SELECT2 Superior 上级
    public function operate_user_select2_superior()
    {
        return $this->repo->operate_user_select2_superior(request()->all());
    }

    // 【用户】SELECT2 Superior 上级
    public function operate_user_select2_department()
    {
        return $this->repo->operate_user_select2_department(request()->all());
    }








    // 【用户-员工】返回-列表-视图
    public function view_user_staff_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_staff_list_datatable(request()->all());
    }
    // 【用户-员工】【修改记录】返回-列表-视图（全部任务）
    public function view_user_staff_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_staff_modify_record_datatable(request()->all());
    }

    // 【用户-员工】添加
    public function operate_user_staff_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }
    // 【用户-员工】编辑
    public function operate_user_staff_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }

    // 【客户】修改-文本-text-信息
    public function operate_staff_info_text_set()
    {
        return $this->repo->operate_staff_info_text_set(request()->all());
    }
    // 【客户】修改-时间-time-信息
    public function operate_staff_info_time_set()
    {
        return $this->repo->operate_staff_info_time_set(request()->all());
    }
    // 【客户】修改-选项-option-信息
    public function operate_staff_info_option_set()
    {
        return $this->repo->operate_staff_info_option_set(request()->all());
    }
    // 【客户】添加-附件-attachment-信息
    public function operate_staff_info_attachment_set()
    {
        return $this->repo->operate_staff_info_attachment_set(request()->all());
    }
    // 【客户】删除-附件-attachment-信息
    public function operate_staff_info_attachment_delete()
    {
        return $this->repo->operate_staff_info_attachment_delete(request()->all());
    }
    // 【客户】获取-附件-attachment-信息
    public function operate_staff_get_attachment_html()
    {
        return $this->repo->operate_staff_get_attachment_html(request()->all());
    }

    // 【用户-员工】登录
    public function operate_user_staff_login()
    {
        $user_id = request()->get('user_id');
        $user = User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_admin_2')->login($user,true);

            $return['user'] = $user;

            if(request()->isMethod('get')) return redirect(env('DOMAIN_STAFF'));
            else if(request()->isMethod('post')) return response_success($return);
        }
        else return response_error([]);

    }
    // 【用户-员工】修改-密码
    public function operate_user_staff_password_admin_change()
    {
        return $this->repo->operate_user_staff_password_admin_change(request()->all());
    }
    // 【用户-员工】修改-密码
    public function operate_user_staff_password_admin_reset()
    {
        return $this->repo->operate_user_staff_password_admin_reset(request()->all());
    }

    // 【用户-员工】管理员-删除
    public function operate_user_staff_admin_delete()
    {
        return $this->repo->operate_user_staff_admin_delete(request()->all());
    }
    // 【用户-员工】管理员-恢复
    public function operate_user_staff_admin_restore()
    {
        return $this->repo->operate_user_staff_admin_restore(request()->all());
    }
    // 【用户-员工】管理员-永久删除
    public function operate_user_staff_admin_delete_permanently()
    {
        return $this->repo->operate_user_staff_admin_delete_permanently(request()->all());
    }

    // 【用户-员工】启用
    public function operate_user_staff_admin_enable()
    {
        return $this->repo->operate_user_staff_admin_enable(request()->all());
    }
    // 【用户-员工】禁用
    public function operate_user_staff_admin_disable()
    {
        return $this->repo->operate_user_staff_admin_disable(request()->all());
    }

    // 【用户-员工】解锁
    public function operate_user_staff_admin_unlock()
    {
        return $this->repo->operate_user_staff_admin_unlock(request()->all());
    }

    // 【用户-员工】晋升
    public function operate_user_staff_admin_promote()
    {
        return $this->repo->operate_user_staff_admin_promote(request()->all());
    }
    // 【用户-员工】降职
    public function operate_user_staff_admin_demote()
    {
        return $this->repo->operate_user_staff_admin_demote(request()->all());
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
     * 地域管理
     */

    // 【地域】SELECT2 Superior 上级
    public function operate_district_select2_city()
    {
        return $this->repo->operate_district_select2_city(request()->all());
    }
    // 【地域】SELECT2 Superior 上级
    public function operate_district_select2_district()
    {
        return $this->repo->operate_district_select2_district(request()->all());
    }

    // 【地域】返回-列表-视图（全部任务）
    public function view_item_district_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_district_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_district_list_datatable(request()->all());
    }


    // 【地域】添加
    public function operate_item_district_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_district_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_district_save(request()->all());
    }
    // 【地域】编辑
    public function operate_item_district_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_district_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_district_save(request()->all());
    }


    // 【地域】删除
    public function operate_item_district_admin_delete()
    {
        return $this->repo->operate_item_district_admin_delete(request()->all());
    }
    // 【地域】恢复
    public function operate_item_district_admin_restore()
    {
        return $this->repo->operate_item_district_admin_restore(request()->all());
    }
    // 【地域】永久删除
    public function operate_item_district_admin_delete_permanently()
    {
        return $this->repo->operate_item_district_admin_delete_permanently(request()->all());
    }

    // 【地域】启用
    public function operate_item_district_admin_enable()
    {
        return $this->repo->operate_item_district_admin_enable(request()->all());
    }
    // 【地域】禁用
    public function operate_item_district_admin_disable()
    {
        return $this->repo->operate_item_district_admin_disable(request()->all());
    }








    /*
     * 项目管理
     */
    // 【项目】返回-列表-视图（全部任务）
    public function view_item_project_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_project_list_datatable(request()->all());
    }
    // 【项目】【修改记录】返回-列表-视图（全部任务）
    public function view_item_project_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_project_modify_record_datatable(request()->all());
    }

    // 【项目】添加
    public function operate_item_project_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_project_save(request()->all());
    }
    // 【项目】编辑
    public function operate_item_project_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_project_save(request()->all());
    }

    // 【项目】修改-文本-text-信息
    public function operate_item_project_info_text_set()
    {
        return $this->repo->operate_item_project_info_text_set(request()->all());
    }
    // 【项目】修改-时间-time-信息
    public function operate_item_project_info_time_set()
    {
        return $this->repo->operate_item_project_info_time_set(request()->all());
    }
    // 【项目】修改-选项-option-信息
    public function operate_item_project_info_option_set()
    {
        return $this->repo->operate_item_project_info_option_set(request()->all());
    }
    // 【项目】添加-附件-attachment-信息
    public function operate_item_project_info_attachment_set()
    {
        return $this->repo->operate_item_project_info_attachment_set(request()->all());
    }
    // 【项目】删除-附件-attachment-信息
    public function operate_item_project_info_attachment_delete()
    {
        return $this->repo->operate_item_project_info_attachment_delete(request()->all());
    }
    // 【项目】获取-附件-attachment-信息
    public function operate_item_project_get_attachment_html()
    {
        return $this->repo->operate_item_project_get_attachment_html(request()->all());
    }

    // 【项目】删除
    public function operate_item_project_admin_delete()
    {
        return $this->repo->operate_item_project_admin_delete(request()->all());
    }
    // 【项目】恢复
    public function operate_item_project_admin_restore()
    {
        return $this->repo->operate_item_project_admin_restore(request()->all());
    }
    // 【项目】永久删除
    public function operate_item_project_admin_delete_permanently()
    {
        return $this->repo->operate_item_project_admin_delete_permanently(request()->all());
    }

    // 【项目】启用
    public function operate_item_project_admin_enable()
    {
        return $this->repo->operate_item_project_admin_enable(request()->all());
    }
    // 【项目】禁用
    public function operate_item_project_admin_disable()
    {
        return $this->repo->operate_item_project_admin_disable(request()->all());
    }













    /*
     * 线索
     */
    // 【线索】返回-列表-视图（全部任务）
    public function view_item_telephone_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_telephone_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_telephone_list_datatable(request()->all());
    }
    // 【线索-修改记录】返回-列表-视图（全部任务）
    public function view_item_telephone_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_telephone_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_telephone_modify_record_datatable(request()->all());
    }


    // 【线索】添加
    public function operate_item_telephone_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_telephone_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_telephone_save(request()->all());
    }
    // 【线索】编辑
    public function operate_item_telephone_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_telephone_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_telephone_save(request()->all());
    }

    // 【线索】导入
    public function operate_item_telephone_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_telephone_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_telephone_import_save(request()->all());
    }












    /*
     * 线索
     */
    // 【线索】返回-列表-视图（全部任务）
    public function view_item_clue_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_clue_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_clue_list_datatable(request()->all());
    }
    // 【线索-修改记录】返回-列表-视图（全部任务）
    public function view_item_clue_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_clue_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_clue_modify_record_datatable(request()->all());
    }


    // 【线索】添加
    public function operate_item_clue_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_clue_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_clue_save(request()->all());
    }
    // 【线索】编辑
    public function operate_item_clue_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_clue_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_clue_save(request()->all());
    }

    // 【线索】导入
    public function operate_item_clue_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_clue_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_clue_import_save(request()->all());
    }


    // 【线索】获取-详情
    public function operate_item_order_get()
    {
        return $this->repo->operate_item_order_get(request()->all());
    }
    // 【线索】获取-详情
    public function operate_item_order_get_html()
    {
        return $this->repo->operate_item_order_get_html(request()->all());
    }
    // 【线索】获取-附件
    public function operate_item_order_get_attachment_html()
    {
        return $this->repo->operate_item_order_get_attachment_html(request()->all());
    }


    // 【线索】删除
    public function operate_item_order_delete()
    {
        return $this->repo->operate_item_order_delete(request()->all());
    }
    // 【线索】发布
    public function operate_item_order_publish()
    {
        return $this->repo->operate_item_order_publish(request()->all());
    }
    // 【线索】完成
    public function operate_item_order_complete()
    {
        return $this->repo->operate_item_order_complete(request()->all());
    }
    // 【线索】弃用
    public function operate_item_order_abandon()
    {
        return $this->repo->operate_item_order_abandon(request()->all());
    }
    // 【线索】复用
    public function operate_item_order_reuse()
    {
        return $this->repo->operate_item_order_reuse(request()->all());
    }
    // 【线索】验证
    public function operate_item_order_verify()
    {
        return $this->repo->operate_item_order_verify(request()->all());
    }
    // 【线索】审核
    public function operate_item_order_inspect()
    {
        return $this->repo->operate_item_order_inspect(request()->all());
    }
    // 【线索】交付
    public function operate_item_order_deliver_get_delivered()
    {
        return $this->repo->operate_item_order_deliver_get_delivered(request()->all());
    }
    // 【线索】交付
    public function operate_item_order_deliver()
    {
        return $this->repo->operate_item_order_deliver(request()->all());
    }
    // 【线索】批量-交付
    public function operate_item_order_bulk_deliver()
    {
        return $this->repo->operate_item_order_bulk_deliver(request()->all());
    }
    // 【线索】分发
    public function operate_item_order_distribute()
    {
        return $this->repo->operate_item_order_distribute(request()->all());
    }


    // 【线索】上架
    public function operate_item_clue_put_on_shelf()
    {
        return $this->repo->operate_item_clue_put_on_shelf(request()->all());
    }
    // 【线索】批量-上架
    public function operate_item_clue_put_on_shelf_by_bulk()
    {
        return $this->repo->operate_item_clue_put_on_shelf_by_bulk(request()->all());
    }
    // 【线索】下架
    public function operate_item_clue_put_off_shelf()
    {
        return $this->repo->operate_item_clue_put_off_shelf(request()->all());
    }



    // 【线索】修改-文本-信息
    public function operate_item_order_info_text_set()
    {
        return $this->repo->operate_item_order_info_text_set(request()->all());
    }
    // 【线索】修改-时间-信息
    public function operate_item_order_info_time_set()
    {
        return $this->repo->operate_item_order_info_time_set(request()->all());
    }
    // 【线索】修改-option-信息
    public function operate_item_order_info_option_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【线索】修改-radio-信息
    public function operate_item_order_info_radio_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【线索】修改-select-信息
    public function operate_item_order_info_select_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【线索】添加-attachment-信息
    public function operate_item_order_info_attachment_set()
    {
        return $this->repo->operate_item_order_info_attachment_set(request()->all());
    }
    // 【线索】删除-attachment-信息
    public function operate_item_order_info_attachment_delete()
    {
        return $this->repo->operate_item_order_info_attachment_delete(request()->all());
    }
    // 【线索】修改-客户信息
    public function operate_item_order_info_customer_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }
    // 【线索】修改-车辆信息
    public function operate_item_order_info_project_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }












    /*
     * 上架管理
     */
    // 【上架管理】返回-列表-视图（全部任务）
    public function view_item_choice_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_choice_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_choice_list_datatable(request()->all());
    }
    // 【线索-修改记录】返回-列表-视图（全部任务）
    public function view_item_choice_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_choice_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_choice_modify_record_datatable(request()->all());
    }


    // 【上架管理】删除
    public function operate_item_choice_delete()
    {
        return $this->repo->operate_item_choice_delete(request()->all());
    }
    // 【上架管理】导出
    public function operate_item_choice_exported()
    {
        return $this->repo->operate_item_delivery_exported(request()->all());
    }
    // 【上架管理】批量-更改导出状态
    public function operate_item_choice_bulk_exported()
    {
        return $this->repo->operate_item_delivery_bulk_exported(request()->all());
    }








    /*
     * 交付管理
     */
    // 【交付管理】返回-列表-视图（全部任务）
    public function view_item_distribution_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_distribution_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_distribution_list_datatable(request()->all());
    }


    // 【交付管理】删除
    public function operate_item_distribution_delete()
    {
        return $this->repo->operate_item_distribution_delete(request()->all());
    }
    // 【交付管理】导出
    public function operate_item_distribution_exported()
    {
        return $this->repo->operate_item_distribution_exported(request()->all());
    }
    // 【线索】批量-交付
    public function operate_item_distribution_bulk_exported()
    {
        return $this->repo->operate_item_distribution_bulk_exported(request()->all());
    }





    /*
     * Finance 财务
     */
    // 【财务】返回-列表-视图（充值）
    public function view_finance_funds_recharge_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_funds_recharge_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_funds_recharge_list_datatable(request()->all());
    }
    // 【财务】返回-列表-视图（消费）
    public function view_finance_funds_using_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_funds_using_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_funds_using_list_datatable(request()->all());
    }
    // 【财务】返回-列表-视图（日报）
    public function view_finance_daily_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_daily_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_daily_list_datatable(request()->all());
    }



    /*
     * Finance 财务
     */

    // 【客户】【修改记录】返回-列表-视图（全部任务）
    public function view_finance_daily_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_daily_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_daily_modify_record_datatable(request()->all());
    }


    // 【财务】添加
    public function operate_finance_daily_list_build()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_daily_list_build();
        else if (request()->isMethod('post')) return $this->repo->operate_finance_daily_list_build(request()->all());
    }


    // 【线索】修改-文本-信息
    public function operate_finance_daily_info_text_set()
    {
        return $this->repo->operate_finance_daily_info_text_set(request()->all());
    }
    // 【线索】修改-时间-信息
    public function operate_finance_daily_info_time_set()
    {
        return $this->repo->operate_finance_daily_info_time_set(request()->all());
    }
    // 【线索】修改-option-信息
    public function operate_finance_daily_info_option_set()
    {
        return $this->repo->operate_finance_daily_info_option_set(request()->all());
    }
    // 【线索】修改-radio-信息
    public function operate_finance_daily_info_radio_set()
    {
        return $this->repo->operate_finance_daily_info_option_set(request()->all());
    }
    // 【线索】修改-select-信息
    public function operate_finance_daily_info_select_set()
    {
        return $this->repo->operate_finance_daily_info_option_set(request()->all());
    }








    /*
     * Call 拨号
     */
    // 【财务】返回-列表-视图（充值）
    public function view_call_call_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_call_call_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_call_call_list_datatable(request()->all());
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


    // 【统计】客服看板
    public function view_statistic_rank()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_rank(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_rank(request()->all());
    }
    public function view_statistic_rank_by_staff()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_rank_by_staff(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_rank_by_staff(request()->all());
    }
    public function view_statistic_rank_by_department()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_rank_by_department(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_rank_by_department(request()->all());
    }


    // 【统计】客服近7天表现
    public function view_statistic_recent()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_recent(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_recent(request()->all());
    }


    // 【统计】交付看板
    public function view_statistic_delivery()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_delivery(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_delivery(request()->all());
    }
    // 【统计】交付看板
    public function view_statistic_delivery_by_customer()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_delivery_by_customer(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_delivery_by_customer(request()->all());
    }
    // 【统计】交付看板
    public function view_statistic_delivery_by_project()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_delivery_by_project(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_delivery_by_project(request()->all());
    }
    // 【统计】项目看板
    public function view_statistic_project()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_project(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_project(request()->all());
    }
    // 【统计】部门看板
    public function view_statistic_department()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_department(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_department(request()->all());
    }
    // 【统计】客服看板
    public function view_statistic_customer_service()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_customer_service(request()->all());
//        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_customer_service(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_customer_service_by_group(request()->all());
    }
    // 【统计】质检看板
    public function view_statistic_inspector()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_inspector(request()->all());
//        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_inspector(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_inspector_by_group(request()->all());
    }
    // 【统计】运营看板
    public function view_statistic_deliverer()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_deliverer(request()->all());
//        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_deliverer(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_deliverer_by_group(request()->all());
    }


    // 【统计】部门看板
    public function view_staff_statistic_customer_service()
    {
        if(request()->isMethod('get')) return $this->repo->view_staff_statistic_customer_service(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_staff_customer_service(request()->all());
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
    // 【统计】订单-导出
    public function operate_statistic_export_for_order_by_ids()
    {
        $this->repo->operate_statistic_export_for_order_by_ids(request()->all());
    }



    // 【内容】【全部】返回-列表-视图
    public function view_record_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_list_for_all_datatable(request()->all());
    }

    /*
     * Record 记录
     */
    // 【统计】导出
    public function view_record_visit_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_visit_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_visit_list_datatable(request()->all());
    }



    // 【记录】
    public function record_for_user_visit($post_data)
    {
        $record = new DK_Choice_Record_Visit();

        $browseInfo = getBrowserInfo();
        $post_data["browser_info"] = $browseInfo['browser_info'];
        $post_data["referer"] = $browseInfo['referer'];
        $type = $browseInfo['type'];
        if($type == "Mobile") $post_data["open_device_type"] = 1;
        else if($type == "PC") $post_data["open_device_type"] = 2;
        $post_data["open_device_name"] = $browseInfo['device_name'];
        $post_data["open_system"] = $browseInfo['system'];
        $post_data["open_browser"] = $browseInfo['browser'];
        $post_data["open_app"] = $browseInfo['app'];
        $post_data["open_NetType"] = $browseInfo['open_NetType'];
        $post_data["open_is_spider"] = $browseInfo['is_spider'];

        $post_data["ip"] = Get_IP();
        $bool = $record->fill($post_data)->save();
        if($bool) return true;
        else return false;
    }








    // 【API】okcc
    public function operate_api_okcc_receivingResult()
    {
        $this->repo->operate_api_okcc_receivingResult(request()->all());
    }


}
