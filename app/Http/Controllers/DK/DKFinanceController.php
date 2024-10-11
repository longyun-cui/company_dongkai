<?php
namespace App\Http\Controllers\DK;

use App\Models\DK\DK_Client;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK_Finance\DK_Finance_User;

use App\Repositories\DK\DKFinanceRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKFinanceController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKFinanceRepository;
    }





    // 账号唯一登录
    public function check_is_only_me()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_finance_user')->check())
        {
            $token = request('_token');
            if(Auth::guard('dk_finance_user')->user()->admin_token == $token)
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
            $view_blade = env('TEMPLATE_DK_FINANCE').'entrance.login';
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
            $admin = DK_Finance_User::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {
                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_finance_user')->login($admin,true);
                        else Auth::guard('dk_finance_user')->login($admin);
                        Auth::guard('dk_finance_user')->user()->admin_token = $token;
                        Auth::guard('dk_finance_user')->user()->save();
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
        Auth::guard('dk_finance_user')->user()->admin_token = '';
        Auth::guard('dk_finance_user')->user()->save();
        Auth::guard('dk_finance_user')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_finance_user')->logout();
        return redirect('/login');
    }




    // 返回主页视图
    public function view_finance_index()
    {
        if(Auth::guard("dk_finance_user")->check())
        {
            $me = Auth::guard("dk_finance_user")->user();
            if(in_array($me->user_type,[0,1,9,11,31]))
            {
                return $this->repo->view_finance_index_0();
            }
            else if(in_array($me->user_type,[41]))
            {
                return $this->repo->view_finance_index_for_agent();
            }
        }
    }
    // 返回主页视图
    public function view_statistic_channel_settled()
    {
        return $this->repo->view_statistic_channel_settled();
    }


    // 返回主页视图
    public function view_finance_404()
    {
        return $this->repo->view_finance_404();
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
        $user = DK_Finance_User::select('*')->find($user_id);
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





    /*
     * SELECT2
     */
    // SELECT2 User
    public function operate_select2_user()
    {
        return $this->repo->operate_select2_user(request()->all());
    }
    // SELECT2 User
    public function operate_select2_company()
    {
        return $this->repo->operate_select2_company(request()->all());
    }
    // SELECT2 User
    public function operate_select2_project()
    {
        return $this->repo->operate_select2_project(request()->all());
    }



    /*
     * 客户管理
     */
    // 【客户管理】添加
    public function operate_user_user_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_user_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_user_save(request()->all());
    }
    // 【客户管理】编辑
    public function operate_user_user_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_user_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_user_save(request()->all());
    }


    // 【客户管理】修改-密码
    public function operate_user_user_password_admin_change()
    {
        return $this->repo->operate_user_user_password_admin_change(request()->all());
    }
    // 【客户管理】修改-密码
    public function operate_user_user_password_admin_reset()
    {
        return $this->repo->operate_user_user_password_admin_reset(request()->all());
    }


    // 【客户管理】启用
    public function operate_user_user_admin_enable()
    {
        return $this->repo->operate_user_user_admin_enable(request()->all());
    }
    // 【客户管理】禁用
    public function operate_user_user_admin_disable()
    {
        return $this->repo->operate_user_user_admin_disable(request()->all());
    }


    // 【客户管理】返回-列表-视图
    public function view_user_user_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_user_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_user_list_datatable(request()->all());
    }


    // 【客户管理】客户-登录
    public function operate_user_user_login_1()
    {
        $user_id = request()->get('user_id');
        $user = DK_Client::select('*')->find($user_id);
        if($user)
        {
            Auth::guard('dk_user')->login($user,true);

            $return['user'] = $user;

            if(request()->isMethod('get')) return redirect(env('DOMAIN_CLIENT'));
            else if(request()->isMethod('post')) return response_success($return);
        }
        else return response_error([]);

    }








    /*
     * 部门管理
     */
    // 【用户】SELECT2 Leader 负责人
    public function operate_company_select2_leader()
    {
        return $this->repo->operate_company_select2_leader(request()->all());
    }
    // 【用户】SELECT2 Superior 上级部门
    public function operate_company_select2_superior_company()
    {
        return $this->repo->operate_company_select2_superior_company(request()->all());
    }


    // 【部门管理】添加
    public function operate_company_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_create();
        else if (request()->isMethod('post')) return $this->repo->operate_company_save(request()->all());
    }
    // 【部门管理】编辑
    public function operate_company_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_company_save(request()->all());
    }


    // 【部门管理】修改-文本-text-信息
    public function operate_company_info_text_set()
    {
        return $this->repo->operate_company_info_text_set(request()->all());
    }
    // 【部门管理】修改-时间-time-信息
    public function operate_company_info_time_set()
    {
        return $this->repo->operate_company_info_time_set(request()->all());
    }
    // 【部门管理】修改-选项-option-信息
    public function operate_company_info_option_set()
    {
        return $this->repo->operate_company_info_option_set(request()->all());
    }
    // 【部门管理】添加-附件-attachment-信息
    public function operate_company_info_attachment_set()
    {
        return $this->repo->operate_company_info_attachment_set(request()->all());
    }
    // 【部门管理】删除-附件-attachment-信息
    public function operate_company_info_attachment_delete()
    {
        return $this->repo->operate_company_info_attachment_delete(request()->all());
    }
    // 【部门管理】获取-附件-attachment-信息
    public function operate_company_get_attachment_html()
    {
        return $this->repo->operate_company_get_attachment_html(request()->all());
    }


    // 【部门管理】删除
    public function operate_company_admin_delete()
    {
        return $this->repo->operate_company_admin_delete(request()->all());
    }
    // 【部门管理】恢复
    public function operate_company_admin_restore()
    {
        return $this->repo->operate_company_admin_restore(request()->all());
    }
    // 【部门管理】永久删除
    public function operate_company_admin_delete_permanently()
    {
        return $this->repo->operate_company_admin_delete_permanently(request()->all());
    }

    // 【部门管理】启用
    public function operate_company_admin_enable()
    {
        return $this->repo->operate_company_admin_enable(request()->all());
    }
    // 【部门管理】禁用
    public function operate_company_admin_disable()
    {
        return $this->repo->operate_company_admin_disable(request()->all());
    }


    // 【部门管理】返回-列表-视图（全部任务）
    public function view_company_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_company_list_datatable(request()->all());
    }
    // 【部门管理】【修改记录】返回-列表-视图（全部任务）
    public function view_company_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_company_modify_record_datatable(request()->all());
    }




    // 【订单管理-财务往来记录】返回-列表-视图（全部任务）
    public function view_company_recharge_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_recharge_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_company_recharge_record_datatable(request()->all());
    }

    // 【订单管理】添加-财务记录
    public function operate_company_finance_recharge_create()
    {
        return $this->repo->operate_company_finance_recharge_create(request()->all());
    }
    // 【订单管理】修改-财务记录
    public function operate_company_finance_recharge_edit()
    {
        return $this->repo->operate_company_finance_recharge_edit(request()->all());
    }


    // 【项目管理-使用记录】返回-列表-视图（全部任务）
    public function view_company_funds_using_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_company_funds_using_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_company_funds_using_record_datatable(request()->all());
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
    public function operate_user_staff_login()
    {
        $user_id = request()->get('user_id');
        $user = User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_finance_user')->login($user,true);

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


    // 【用户-员工管理】管理员-删除
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


    // 【用户-员工管理】晋升
    public function operate_user_staff_admin_promote()
    {
        return $this->repo->operate_user_staff_admin_promote(request()->all());
    }
    // 【用户-员工管理】降职
    public function operate_user_staff_admin_demote()
    {
        return $this->repo->operate_user_staff_admin_demote(request()->all());
    }




    // 【员工管理】【全部用户】返回-列表-视图
    public function view_staff_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_staff_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_staff_list_for_all_datatable(request()->all());
    }









    /*
     * 项目管理
     */
    // 【项目管理】返回-列表-视图（全部任务）
    public function view_item_project_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_project_list_datatable(request()->all());
    }
    // 【项目管理】返回-列表-视图（全部任务）
    public function view_item_project_list_2()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_list_2(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_project_list_datatable_2(request()->all());
    }
    // 【项目管理】【修改记录】返回-列表-视图（全部任务）
    public function view_item_project_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_project_modify_record_datatable(request()->all());
    }




    // 【项目管理-使用记录】返回-列表-视图（全部任务）
    public function view_project_funds_using_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_project_funds_using_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_project_funds_using_record_datatable(request()->all());
    }

    // 【项目管理】添加-使用记录
    public function operate_project_funds_using_create()
    {
        return $this->repo->operate_project_funds_using_create(request()->all());
    }
    // 【项目管理】修改-使用记录
    public function operate_project_funds_using_edit()
    {
        return $this->repo->operate_project_funds_using_edit(request()->all());
    }




    // 【项目管理】添加
    public function operate_item_project_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_project_save(request()->all());
    }
    // 【项目管理】编辑
    public function operate_item_project_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_project_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_project_save(request()->all());
    }


    // 【车辆管理】修改-文本-text-信息
    public function operate_item_project_info_text_set()
    {
        return $this->repo->operate_item_project_info_text_set(request()->all());
    }
    // 【车辆管理】修改-时间-time-信息
    public function operate_item_project_info_time_set()
    {
        return $this->repo->operate_item_project_info_time_set(request()->all());
    }
    // 【车辆管理】修改-选项-option-信息
    public function operate_item_project_info_option_set()
    {
        return $this->repo->operate_item_project_info_option_set(request()->all());
    }
    // 【车辆管理】添加-附件-attachment-信息
    public function operate_item_project_info_attachment_set()
    {
        return $this->repo->operate_item_project_info_attachment_set(request()->all());
    }
    // 【车辆管理】删除-附件-attachment-信息
    public function operate_item_project_info_attachment_delete()
    {
        return $this->repo->operate_item_project_info_attachment_delete(request()->all());
    }
    // 【车辆管理】获取-附件-attachment-信息
    public function operate_item_project_get_attachment_html()
    {
        return $this->repo->operate_item_project_get_attachment_html(request()->all());
    }


    // 【项目管理】删除
    public function operate_item_project_admin_delete()
    {
        return $this->repo->operate_item_project_admin_delete(request()->all());
    }
    // 【项目管理】恢复
    public function operate_item_project_admin_restore()
    {
        return $this->repo->operate_item_project_admin_restore(request()->all());
    }
    // 【项目管理】永久删除
    public function operate_item_project_admin_delete_permanently()
    {
        return $this->repo->operate_item_project_admin_delete_permanently(request()->all());
    }

    // 【项目管理】启用
    public function operate_item_project_admin_enable()
    {
        return $this->repo->operate_item_project_admin_enable(request()->all());
    }
    // 【项目管理】禁用
    public function operate_item_project_admin_disable()
    {
        return $this->repo->operate_item_project_admin_disable(request()->all());
    }








    // 【订单管理】SELECT2 User 员工
    public function operate_item_select2_user()
    {
        return $this->repo->operate_item_select2_user(request()->all());
    }
    // 【订单管理】SELECT2 Team 团队
    public function operate_item_select2_company()
    {
        return $this->repo->operate_item_select2_company(request()->all());
    }
    // 【订单管理】SELECT2 Client 项目
    public function operate_item_select2_project()
    {
        return $this->repo->operate_item_select2_project(request()->all());
    }
    // 【订单管理】SELECT2 Client 客户
    public function operate_item_select2_user_1()
    {
        return $this->repo->operate_item_select2_user_1(request()->all());
    }




    /*
     * 订单管理
     */
    // 【订单管理】返回-列表-视图（全部任务）
    public function view_item_daily_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_daily_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_daily_list_datatable(request()->all());
    }


    // 【订单管理】添加
    public function operate_item_daily_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_daily_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_daily_save(request()->all());
    }
    // 【订单管理】编辑
    public function operate_item_daily_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_daily_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_daily_save(request()->all());
    }

    // 【订单管理】导入
    public function operate_item_daily_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_daily_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_daily_import_save(request()->all());
    }


    // 【订单管理】获取-详情
    public function operate_item_daily_get()
    {
        return $this->repo->operate_item_daily_get(request()->all());
    }
    // 【订单管理】获取-详情
    public function operate_item_daily_get_html()
    {
        return $this->repo->operate_item_daily_get_html(request()->all());
    }
    // 【订单管理】获取-附件
    public function operate_item_daily_get_attachment_html()
    {
        return $this->repo->operate_item_daily_get_attachment_html(request()->all());
    }


    // 【订单管理】删除
    public function operate_item_daily_delete()
    {
        return $this->repo->operate_item_daily_delete(request()->all());
    }
    // 【订单管理】发布
    public function operate_item_daily_publish()
    {
        return $this->repo->operate_item_daily_publish(request()->all());
    }
    // 【订单管理】完成
    public function operate_item_daily_complete()
    {
        return $this->repo->operate_item_daily_complete(request()->all());
    }
    // 【订单管理】弃用
    public function operate_item_daily_abandon()
    {
        return $this->repo->operate_item_daily_abandon(request()->all());
    }
    // 【订单管理】复用
    public function operate_item_daily_reuse()
    {
        return $this->repo->operate_item_daily_reuse(request()->all());
    }
    // 【订单管理】验证
    public function operate_item_daily_verify()
    {
        return $this->repo->operate_item_daily_verify(request()->all());
    }
    // 【订单管理】审核
    public function operate_item_daily_inspect()
    {
        return $this->repo->operate_item_daily_inspect(request()->all());
    }
    // 【订单管理】交付
    public function operate_item_daily_deliver()
    {
        return $this->repo->operate_item_daily_deliver(request()->all());
    }
    // 【订单管理】批量-交付
    public function operate_item_daily_bulk_deliver()
    {
        return $this->repo->operate_item_daily_bulk_deliver(request()->all());
    }



    // 【订单管理】修改-文本-信息
    public function operate_item_daily_info_text_set()
    {
        return $this->repo->operate_item_daily_info_text_set(request()->all());
    }
    // 【订单管理】修改-时间-信息
    public function operate_item_daily_info_time_set()
    {
        return $this->repo->operate_item_daily_info_time_set(request()->all());
    }
    // 【订单管理】修改-option-信息
    public function operate_item_daily_info_option_set()
    {
        return $this->repo->operate_item_daily_info_option_set(request()->all());
    }
    // 【订单管理】修改-radio-信息
    public function operate_item_daily_info_radio_set()
    {
        return $this->repo->operate_item_daily_info_option_set(request()->all());
    }
    // 【订单管理】修改-select-信息
    public function operate_item_daily_info_select_set()
    {
        return $this->repo->operate_item_daily_info_option_set(request()->all());
    }
    // 【订单管理】添加-attachment-信息
    public function operate_item_daily_info_attachment_set()
    {
        return $this->repo->operate_item_daily_info_attachment_set(request()->all());
    }
    // 【订单管理】删除-attachment-信息
    public function operate_item_daily_info_attachment_delete()
    {
        return $this->repo->operate_item_daily_info_attachment_delete(request()->all());
    }




    // 【订单管理-修改记录】返回-列表-视图（全部任务）
    public function view_item_daily_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_daily_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_daily_modify_record_datatable(request()->all());
    }








    /*
     * 结算管理
     */
    // 【结算管理】返回-列表-视图（全部任务）
    public function view_item_settled_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_settled_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_settled_list_datatable(request()->all());
    }


    // 【结算管理-修改记录】返回-列表-视图（全部任务）
    public function view_item_settled_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_settled_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_settled_modify_record_datatable(request()->all());
    }


    // 【结算管理】添加
    public function operate_item_settled_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_settled_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_settled_save(request()->all());
    }
    // 【结算管理】编辑
    public function operate_item_settled_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_settled_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_settled_save(request()->all());
    }

    // 【结算管理】导入
    public function operate_item_settled_import()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_settled_import();
        else if (request()->isMethod('post')) return $this->repo->operate_item_settled_import_save(request()->all());
    }




    // 【结算管理】修改-文本-信息
    public function operate_item_settled_info_text_set()
    {
        return $this->repo->operate_item_settled_info_text_set(request()->all());
    }
    // 【结算管理】修改-时间-信息
    public function operate_item_settled_info_time_set()
    {
        return $this->repo->operate_item_settled_info_time_set(request()->all());
    }
    // 【结算管理】修改-option-信息
    public function operate_item_settled_info_option_set()
    {
        return $this->repo->operate_item_settled_info_option_set(request()->all());
    }
    // 【结算管理】修改-radio-信息
    public function operate_item_settled_info_radio_set()
    {
        return $this->repo->operate_item_settled_info_option_set(request()->all());
    }
    // 【结算管理】修改-select-信息
    public function operate_item_settled_info_select_set()
    {
        return $this->repo->operate_item_settled_info_option_set(request()->all());
    }
    // 【结算管理】添加-attachment-信息
    public function operate_item_settled_info_attachment_set()
    {
        return $this->repo->operate_item_settled_info_attachment_set(request()->all());
    }
    // 【结算管理】删除-attachment-信息
    public function operate_item_settled_info_attachment_delete()
    {
        return $this->repo->operate_item_settled_info_attachment_delete(request()->all());
    }




    // 【结算管理-使用记录】返回-列表-视图（全部任务）
    public function view_settled_funds_using_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_settled_funds_using_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_settled_funds_using_record_datatable(request()->all());
    }

    // 【结算管理】添加-使用记录
    public function operate_settled_funds_using_create()
    {
        return $this->repo->operate_settled_funds_using_create(request()->all());
    }
    // 【结算管理】修改-使用记录
    public function operate_settled_funds_using_edit()
    {
        return $this->repo->operate_settled_funds_using_edit(request()->all());
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




    // 【统计】项目报表
    public function view_statistic_project()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_project(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_project(request()->all());
    }
    //
    public function get_statistic_data_for_project_of_daily_list_datatable()
    {
        return $this->repo->get_statistic_data_for_project_of_daily_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_project_of_chart()
    {
        return $this->repo->get_statistic_data_for_project_of_chart(request()->all());
    }


    // 【统计】公司&渠道报表
    public function view_statistic_company()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_company(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_company(request()->all());
    }
    //
    public function get_statistic_data_for_company_of_project_list_datatable()
    {
        return $this->repo->get_statistic_data_for_company_of_project_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_company_of_chart()
    {
        return $this->repo->get_statistic_data_for_company_of_chart(request()->all());
    }


    // 【统计】渠道报表
    public function view_statistic_channel()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_company(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_company(request()->all());
    }
    //
    public function get_statistic_data_for_channel_of_project_list_datatable()
    {
        return $this->repo->get_statistic_data_for_company_of_project_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_channel_of_chart()
    {
        return $this->repo->get_statistic_data_for_company_of_chart(request()->all());
    }




    // 【统计】财务报表
    public function view_statistic_finance()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_finance(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_finance(request()->all());
    }
    //
    public function get_statistic_data_for_finance_of_dealings()
    {
        return $this->repo->get_statistic_data_for_finance_of_dealings(request()->all());
    }
    //
    public function get_statistic_data_for_finance_of_channel_list_datatable()
    {
        return $this->repo->get_statistic_data_for_finance_of_channel_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_finance_of_project_list_datatable()
    {
        return $this->repo->get_statistic_data_for_finance_of_project_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_finance_of_daily_list_datatable()
    {
        return $this->repo->get_statistic_data_for_finance_of_daily_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_finance_of_daily_chart()
    {
        return $this->repo->get_statistic_data_for_finance_of_daily_chart(request()->all());
    }




    // 【统计】业务报表
    public function view_statistic_service()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_service(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_service(request()->all());
    }
    //
    public function get_statistic_data_for_service_of_project_list_datatable()
    {
        return $this->repo->get_statistic_data_for_service_of_project_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_service_of_daily_list_datatable()
    {
        return $this->repo->get_statistic_data_for_service_of_daily_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_service_of_daily_chart()
    {
        return $this->repo->get_statistic_data_for_service_of_daily_chart(request()->all());
    }




    // 【统计】公司概览
    public function view_statistic_company_overview()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_company_overview(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_company_overview(request()->all());
    }
    //
    public function get_statistic_data_for_company_overview_of_channel_list_datatable()
    {
        return $this->repo->get_statistic_data_for_company_overview_of_channel_list_datatable(request()->all());
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
        if(request()->isMethod('get')) return $this->repo->view_record_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_list_datatable(request()->all());
    }



    // 【记录】资金-充值记录
    public function view_record_funds_recharge_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_funds_recharge_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_funds_recharge_list_datatable(request()->all());
    }
    // 【记录】资金-使用记录
    public function view_record_funds_using_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_funds_using_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_funds_using_list_datatable(request()->all());
    }




}
