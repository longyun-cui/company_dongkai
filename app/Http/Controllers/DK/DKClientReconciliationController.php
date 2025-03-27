<?php
namespace App\Http\Controllers\DK;

use App\Models\DK\DK_Client;
use App\Models\DK_Client\DK_Client_User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;

use App\Repositories\DK\DKClientReconciliationRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKClientReconciliationController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKClientReconciliationRepository;
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
    public function view_reconciliation_index()
    {
        return $this->repo->view_reconciliation_index();
    }


    // 返回主页视图
    public function view_admin_404()
    {
        return $this->repo->view_admin_404();
    }





    public function reconciliation_v1_operate_select2_project()
    {
        return $this->repo->reconciliation_v1_operate_select2_project(request()->all());
    }




    // 【项目-管理】datatable
    public function reconciliation_v1_operate_for_project_datatable_list_query()
    {
        return $this->repo->reconciliation_v1_operate_for_project_datatable_list_query(request()->all());
    }
    // 【项目-管理】获取
    public function reconciliation_v1_operate_for_project_item_get()
    {
        return $this->repo->reconciliation_v1_operate_for_project_item_get(request()->all());
    }
    // 【项目-管理】编辑-保存
    public function reconciliation_v1_operate_for_project_item_save()
    {
        return $this->repo->reconciliation_v1_operate_for_project_item_save(request()->all());
    }


    // 【生产-统计】员工日报
    public function reconciliation_v1_operate_for_project_statistic_daily()
    {
        return $this->repo->reconciliation_v1_operate_for_project_statistic_daily(request()->all());
    }








    // 【每日结算-管理】datatable
    public function reconciliation_v1_operate_for_daily_datatable_list_query()
    {
        return $this->repo->reconciliation_v1_operate_for_daily_datatable_list_query(request()->all());
    }
    // 【项目-管理】获取
    public function reconciliation_v1_operate_for_daily_item_get()
    {
        return $this->repo->reconciliation_v1_operate_for_daily_item_get(request()->all());
    }
    // 【项目-管理】编辑-保存
    public function reconciliation_v1_operate_for_daily_item_save()
    {
        return $this->repo->reconciliation_v1_operate_for_daily_item_save(request()->all());
    }




    // 【通用】删除
    public function reconciliation_v1_operate_for_universal_item_delete_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_item_delete_by_admin(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_item_delete_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】恢复
    public function reconciliation_v1_operate_for_universal_item_restore_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_item_restore_by_admin(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_item_restore_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】彻底删除
    public function reconciliation_v1_operate_for_universal_item_delete_permanently_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_item_delete_permanently_by_admin(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_item_delete_permanently_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }

    // 【通用】启用
    public function reconciliation_v1_operate_for_universal_item_enable_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_item_enable_by_admin(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_item_enable_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }
    // 【通用】禁用
    public function reconciliation_v1_operate_for_universal_item_disable_by_admin()
    {
        $item_category = request('item_category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_item_disable_by_admin(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_item_disable_by_admin(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }


    // 【通用】字段修改
    public function reconciliation_v1_operate_for_universal_field_set()
    {
        $item_category = request('item-category','');

        if($item_category == 'reconciliation-project')
        {
            return $this->repo->reconciliation_v1_operate_for_project_field_set(request()->all());
        }
        else if($item_category == 'reconciliation-daily')
        {
            return $this->repo->reconciliation_v1_operate_for_daily_field_set(request()->all());
        }
        else
        {
            return response_fail([]);
        }
    }


    // 【项目-管理】充值
    public function reconciliation_v1_operate_for_project_item_recharge_save()
    {
        return $this->repo->reconciliation_v1_operate_for_project_item_recharge_save(request()->all());
    }


    // 【项目-管理】充值
    public function reconciliation_v1_operate_for_daily_item_settle_save()
    {
        return $this->repo->reconciliation_v1_operate_for_daily_item_settle_save(request()->all());
    }




    // 【交易-管理】datatable
    public function reconciliation_v1_operate_for_trade_datatable_list_query()
    {
        return $this->repo->reconciliation_v1_operate_for_trade_datatable_list_query(request()->all());
    }
    // 【交易-管理】获取
    public function reconciliation_v1_operate_for_trade_item_get()
    {
        return $this->repo->reconciliation_v1_operate_for_trade_item_get(request()->all());
    }
    // 【交易-管理】编辑-保存
    public function reconciliation_v1_operate_for_trade_item_save()
    {
        return $this->repo->reconciliation_v1_operate_for_trade_item_save(request()->all());
    }

    // 【交易-管理】删除
    public function reconciliation_v1_operate_for_trade_item_delete()
    {
        return $this->repo->reconciliation_v1_operate_for_trade_item_delete(request()->all());
    }
    // 【交易-管理】确认
    public function reconciliation_v1_operate_for_trade_item_confirm()
    {
        return $this->repo->reconciliation_v1_operate_for_trade_item_confirm(request()->all());
    }



    // 【通用】操作记录
    public function reconciliation_v1_operate_for_item_operation_record_datatable_query()
    {
        return $this->repo->reconciliation_v1_operate_for_item_operation_record_datatable_query(request()->all());
    }



}
