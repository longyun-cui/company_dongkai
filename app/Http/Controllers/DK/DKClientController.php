<?php
namespace App\Http\Controllers\DK;

use App\Models\DK\DK_Client;
use App\Models\DK_Client\DK_Client_User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;

use App\Repositories\DK\DKClientRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKClientController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKClientRepository;
    }





    // 账号唯一登录
    public function check_is_only_me()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_client_staff')->check())
        {
            $me = Auth::guard('dk_client_staff')->user();
            $token = request('_token');

            if($me->admin_token == $token)
            {
                $result['message'] = 'success';
                $result['result'] = 'access';
            }
        }

        return Response::json($result);
    }


    // 账号IP登录
    public function check_is_ip_login()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_client_staff')->check())
        {
            $me = Auth::guard('dk_client_staff')->user();

            // 判断用户是否开启ip登录
            if($me->is_ip == 1)
            {
                $ip = Get_IP();
                $array = explode(' ', $me->ip_whitelist);
                if(in_array($ip, $array))
                {
                    $result['message'] = 'success';
                    $result['result'] = 'access';
                }
            }
            else
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
            $view_blade = env('TEMPLATE_DK_CLIENT').'entrance.login';
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
            $admin = DK_Client_User::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {
                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_client_staff')->login($admin,true);
                        else Auth::guard('dk_client_staff')->login($admin);
                        Auth::guard('dk_client_staff')->user()->admin_token = $token;
                        Auth::guard('dk_client_staff')->user()->save();
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
        Auth::guard('dk_client_staff')->user()->admin_token = '';
        Auth::guard('dk_client_staff')->user()->save();
        Auth::guard('dk_client_staff')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_client_staff')->logout();
        return redirect('/login');
    }



    // 返回主页视图
    public function view_data_voice_record()
    {
        return $this->repo->view_data_voice_record(request()->all());
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




    // 返回主页视图
    public function query_last_delivery()
    {
        return $this->repo->query_last_delivery();
    }






    /*
     * 交付管理
     */
    // 【交付】交付列表
    public function get_datatable_delivery_list()
    {
        return $this->repo->get_datatable_delivery_list(request()->all());
    }
    // 【交付】交付日报
    public function get_datatable_delivery_daily()
    {
        return $this->repo->get_datatable_delivery_daily(request()->all());
    }

    // 【交付】导出
    public function operate_delivery_export_by_ids()
    {
        $this->repo->operate_delivery_export_by_ids(request()->all());
    }




    // 【财务】财务日报
    public function get_datatable_finance_daily()
    {
        return $this->repo->get_datatable_finance_daily(request()->all());
    }




    // 【部门-管理】编辑
    public function operate_item_edit_for_department()
    {
        return $this->repo->operate_department_edit_by_admin(request()->all());
    }
    // 【部门-管理】获取
    public function operate_item_get_for_department()
    {
        return $this->repo->operate_department_get_by_admin(request()->all());
    }




    // 【员工-管理】编辑
    public function operate_staff_edit_by_admin()
    {
        return $this->repo->operate_staff_edit_by_admin(request()->all());
    }
    // 【员工-管理】获取
    public function operate_staff_get_by_admin()
    {
        return $this->repo->operate_staff_get_by_admin(request()->all());
    }




    // 【通用】删除
    public function operate_item_delete_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_delete_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_delete_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】恢复
    public function operate_item_restore_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_restore_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_restore_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】彻底删除
    public function operate_item_delete_permanently_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_delete_permanently_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_delete_permanently_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }

    // 【通用】修改密码
    public function operate_item_password_reset_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_password_reset_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_password_reset_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】修改密码
    public function operate_item_password_change_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_password_change_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_password_change_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }

    // 【通用】启用
    public function operate_item_enable_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_enable_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_enable_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】禁用
    public function operate_item_disable_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'department')
        {
            return $this->repo->operate_department_disable_by_admin(request()->all());
        }
        else if($item_category == 'staff')
        {
            return $this->repo->operate_staff_disable_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }

    // 【通用】晋升
    public function operate_item_promote_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'staff')
        {
            return $this->repo->operate_staff_promote_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】降职
    public function operate_item_demote_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'staff')
        {
            return $this->repo->operate_staff_demote_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }






    // 【订单管理】批量-更改分配状态
    public function operate_bulk_assign_status()
    {
        return $this->repo->operate_bulk_assign_status(request()->all());
    }
    // 【订单管理】批量-分派
    public function operate_bulk_assign_staff()
    {
        return $this->repo->operate_bulk_assign_staff(request()->all());
    }
    // 【订单管理】批量-api-推送
    public function operate_bulk_api_push()
    {
        return $this->repo->operate_bulk_api_push(request()->all());
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
     * 用户基本信息
     */
    // SELECT2
    public function operate_select2_district()
    {
        return $this->repo->operate_select2_district(request()->all());
    }








    /*
     * 部门管理
     */
    // 【用户】SELECT2 Leader 负责人
    public function operate_department_select2_leader()
    {
        return $this->repo->operate_department_select2_leader(request()->all());
    }
    // 【用户】SELECT2 Superior 上级部门
    public function operate_department_select2_superior_department()
    {
        return $this->repo->operate_department_select2_superior_department(request()->all());
    }


    // 【部门管理】添加
    public function operate_department_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_create();
        else if (request()->isMethod('post')) return $this->repo->operate_department_save(request()->all());
    }
    // 【部门管理】编辑
    public function operate_department_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_department_save(request()->all());
    }


    // 【部门管理】修改-文本-text-信息
    public function operate_department_info_text_set()
    {
        return $this->repo->operate_department_info_text_set(request()->all());
    }
    // 【部门管理】修改-时间-time-信息
    public function operate_department_info_time_set()
    {
        return $this->repo->operate_department_info_time_set(request()->all());
    }
    // 【部门管理】修改-选项-option-信息
    public function operate_department_info_option_set()
    {
        return $this->repo->operate_department_info_option_set(request()->all());
    }
    // 【部门管理】添加-附件-attachment-信息
    public function operate_department_info_attachment_set()
    {
        return $this->repo->operate_department_info_attachment_set(request()->all());
    }
    // 【部门管理】删除-附件-attachment-信息
    public function operate_department_info_attachment_delete()
    {
        return $this->repo->operate_department_info_attachment_delete(request()->all());
    }
    // 【部门管理】获取-附件-attachment-信息
    public function operate_department_get_attachment_html()
    {
        return $this->repo->operate_department_get_attachment_html(request()->all());
    }


    // 【部门管理】删除
    public function operate_department_admin_delete()
    {
        return $this->repo->operate_department_admin_delete(request()->all());
    }
    // 【部门管理】恢复
    public function operate_department_admin_restore()
    {
        return $this->repo->operate_department_admin_restore(request()->all());
    }
    // 【部门管理】永久删除
    public function operate_department_admin_delete_permanently()
    {
        return $this->repo->operate_department_admin_delete_permanently(request()->all());
    }

    // 【部门管理】启用
    public function operate_department_admin_enable()
    {
        return $this->repo->operate_department_admin_enable(request()->all());
    }
    // 【部门管理】禁用
    public function operate_department_admin_disable()
    {
        return $this->repo->operate_department_admin_disable(request()->all());
    }


    // 【部门管理】返回-列表-视图（全部任务）
    public function view_department_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_department_list_datatable(request()->all());
    }
    // 【部门管理】【修改记录】返回-列表-视图（全部任务）
    public function view_department_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_department_modify_record_datatable(request()->all());
    }








    /*
     * 员工管理
     */
    // 【员工管理】【全部用户】返回-列表-视图
    public function view_user_staff_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_staff_list_datatable(request()->all());
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
            Auth::guard('yh_admin')->login($user,true);

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







    /*
     * USER-STAFF 用户-员工管理
     *
     */
    // 【用户】SELECT2 District
    public function operate_user_select2_sales()
    {
        return $this->repo->operate_user_select2_sales(request()->all());
    }











    /*
     * 订单管理
     */
    // 【订单管理】返回-列表-视图（全部任务）
    public function view_item_delivery_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_delivery_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_delivery_list_datatable(request()->all());
    }
    // 【内容】质量评估
    public function operate_item_delivery_quality_evaluate()
    {
        return $this->repo->operate_item_delivery_quality_evaluate(request()->all());
    }
    // 【订单管理】批量-更改导出状态
    public function operate_item_delivery_bulk_exported_status()
    {
        return $this->repo->operate_item_delivery_bulk_exported_status(request()->all());
    }
    // 【订单管理】批量-更改导出状态
    public function operate_item_delivery_bulk_assign_status()
    {
        return $this->repo->operate_item_delivery_bulk_assign_status(request()->all());
    }
    // 【订单管理】批量-分派
    public function operate_item_delivery_bulk_assign_staff()
    {
        return $this->repo->operate_item_delivery_bulk_assign_staff(request()->all());
    }
    // 【订单管理】批量-api-推送
    public function operate_item_delivery_bulk_api_push()
    {
        return $this->repo->operate_item_delivery_bulk_api_push(request()->all());
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


    // 【订单管理-修改记录】返回-列表-视图（全部任务）
    public function view_item_order_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_modify_record_datatable(request()->all());
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
    // 【内容】验证
    public function operate_item_order_verify()
    {
        return $this->repo->operate_item_order_verify(request()->all());
    }
    // 【内容】审核
    public function operate_item_order_inspect()
    {
        return $this->repo->operate_item_order_inspect(request()->all());
    }
    // 【内容】跟进
    public function operate_item_order_follow()
    {
        return $this->repo->operate_item_order_follow(request()->all());
    }
    // 【内容】质量评估
    public function operate_item_order_quality_evaluate()
    {
        return $this->repo->operate_item_order_quality_evaluate(request()->all());
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
    public function operate_item_order_info_project_set()
    {
        return $this->repo->operate_item_order_info_option_set(request()->all());
    }

    // 【订单管理】添加-行程记录
    public function operate_item_order_travel_set()
    {
        return $this->repo->operate_item_order_travel_set(request()->all());
    }








    /*
     * Finance 财务
     */
    // 【财务】返回-列表-视图（全部任务）
    public function view_finance_daily_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_finance_daily_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_finance_daily_list_datatable(request()->all());
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




    // 【统计】业务报表
    public function view_statistic_delivery_by_daily()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_delivery_by_daily(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_data_for_delivery_by_daily(request()->all());
    }
    //
    public function get_statistic_data_for_delivery_of_project_list_datatable()
    {
        return $this->repo->get_statistic_data_for_service_of_project_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_delivery_of_daily_list_datatable()
    {
        return $this->repo->get_statistic_data_for_delivery_of_daily_list_datatable(request()->all());
    }
    //
    public function get_statistic_data_for_delivery_of_daily_chart()
    {
        return $this->repo->get_statistic_data_for_delivery_of_daily_chart(request()->all());
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
        $item_category = request('item_category',0);

        if($item_category == 1)
        {
            return $this->repo->operate_statistic_export_for_order_by_ids(request()->all());
        }
        else if($item_category == 11)
        {
            return $this->repo->operate_statistic_export_for_order_by_ids(request()->all());
        }
        else if($item_category == 31)
        {
            return $this->repo->operate_statistic_export_for_order_luxury_by_ids(request()->all());
        }
        else
        {
            return $this->repo->operate_statistic_export_for_order_by_ids(request()->all());
        }

    }



    // 【内容】【全部】返回-列表-视图
    public function view_record_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_list_for_all_datatable(request()->all());
    }




}
