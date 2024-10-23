<?php
namespace App\Http\Controllers\DK;

use App\Models\DK\DK_Client;
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

        if(Auth::guard('dk_client')->check())
        {
            $me = Auth::guard('dk_client')->user();
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

        if(Auth::guard('dk_client')->check())
        {
            $me = Auth::guard('dk_client')->user();

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
            $admin = DK_Client::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {
                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_client')->login($admin,true);
                        else Auth::guard('dk_client')->login($admin);
                        Auth::guard('dk_client')->user()->admin_token = $token;
                        Auth::guard('dk_client')->user()->save();
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
        Auth::guard('dk_client')->user()->admin_token = '';
        Auth::guard('dk_client')->user()->save();
        Auth::guard('dk_client')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_client')->logout();
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
    public function view_department_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_department_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_department_list_for_all_datatable(request()->all());
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
    public function view_staff_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_staff_list_datatable(request()->all());
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











    /*
     * 订单管理
     */
    // 【订单管理】返回-列表-视图（全部任务）
    public function view_item_order_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_list_for_all_datatable(request()->all());
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




    // 【订单管理-修改记录】返回-列表-视图（全部任务）
    public function view_item_order_modify_record()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_order_modify_record(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_order_modify_record_datatable(request()->all());
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




}
