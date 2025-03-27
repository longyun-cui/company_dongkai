<?php
namespace App\Http\Controllers\DK;

use App\Models\DK\DK_Client;
use App\Models\DK_Client\DK_Client_User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;

use App\Repositories\DK\DKAgencyReconciliationRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKAgencyReconciliationController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKAgencyReconciliationRepository;
    }





    // 账号唯一登录
    public function check_is_only_me()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_agency')->check())
        {
            $token = request('_token');
            if(Auth::guard('dk_agency')->user()->admin_token == $token)
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

            $view_blade = env('TEMPLATE_DK_CC').'entrance.login';
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
            $admin = DK_Company::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->user_status == 1)
                {

                    if($admin->login_error_num >= 3)
                    {
                        return response_error([],'账户or密码不正确啊！');
                    }

                    if(!in_array($admin->user_type,[0,1,9,11]))
                    {
                        return response_error([],'该账号没有权限！');
                    }

                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_agency')->login($admin,true);
                        else Auth::guard('dk_agency')->login($admin);
                        Auth::guard('dk_agency')->user()->login_error_num = 0;
                        Auth::guard('dk_agency')->user()->admin_token = $token;
                        Auth::guard('dk_agency')->user()->save();

                        if(Auth::guard('dk_agency')->user()->id > 10000)
                        {
                            $record["creator_id"] = Auth::guard('dk_cc')->user()->id;
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
        Auth::guard('dk_agency')->user()->admin_token = '';
        Auth::guard('dk_agency')->user()->save();
        Auth::guard('dk_agency')->logout();
        return redirect('/');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_agency')->logout();
        return redirect('/');
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
