<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;
use App\Models\DK\DK_Item;

use App\Models\DK\DK_Customer;
use App\Models\DK_Customer\DK_Customer_User;

use App\Models\DK\DK_Client;
use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Finance\DK_Finance_User;

use App\Repositories\DK\DKSuperRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKSuperController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKSuperRepository;
    }


    // 登陆
    public function login()
    {
        if(request()->isMethod('get'))
        {
            $view_blade = env('TEMPLATE_DK_SUPER').'entrance.login';
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
            $admin = DK_User::whereMobile($mobile)->first();

            if($admin)
            {
                if($admin->active == 1)
                {
                    $password = request()->get('password');
                    if(password_check($password,$admin->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('yh_super')->login($admin,true);
                        else Auth::guard('yh_super')->login($admin,true);
                        return response_success();
                    }
                    else return response_error([],'账户or密码不正确 ');
                }
                else return response_error([],'账户尚未激活，请先去邮箱激活。');
            }
            else return response_error([],'账户不存在');
        }
    }

    // 退出
    public function logout()
    {
        Auth::guard('zy_super')->logout();
        return redirect('/login');
    }




    // 返回主页视图
    public function view_super_index()
    {
        return $this->repo->view_super_index();
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
     * USER 用户
     */
    // 【用户】SELECT2 District
    public function operate_user_select2_district()
    {
        return $this->repo->operate_user_select2_district(request()->all());
    }

    // 【用户】添加
    public function operate_user_user_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_user_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_user_save(request()->all());
    }
    // 【用户】编辑
    public function operate_user_user_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_user_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_user_save(request()->all());
    }

    // 【用户】修改-密码
    public function operate_user_change_password()
    {
        return $this->repo->operate_user_change_password(request()->all());
    }


    // 【用户】登录
    public function operate_user_user_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_User::where('id',$user_id)->first();
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

            Auth::guard('yh_admin')->login($user,true);

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_ADMIN');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_ADMIN'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);

    }
    // 【用户-管理员】登录
    public function operate_user_admin_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('yh_admin')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('yh_admin')->user()->admin_token = $token;
            Auth::guard('yh_admin')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_ADMIN');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_ADMIN'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_staff_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('yh_admin')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('yh_admin')->user()->admin_token = $token;
            Auth::guard('yh_admin')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_ADMIN');
//            dd(env('DOMAIN_DK_ADMIN'));

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_ADMIN'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_client_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Client::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_client')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('dk_client')->user()->admin_token = $token;
            Auth::guard('dk_client')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_CLIENT');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_CLIENT'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_client_staff_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Client_User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_client_staff')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('dk_client_staff')->user()->admin_token = $token;
            Auth::guard('dk_client_staff')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_CLIENT');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_CLIENT'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_finance_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Finance_User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_finance_user')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('dk_finance_user')->user()->admin_token = $token;
            Auth::guard('dk_finance_user')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_FINANCE');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_FINANCE'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_customer_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Customer::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_customer')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('dk_customer')->user()->admin_token = $token;
            Auth::guard('dk_customer')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_CUSTOMER');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_CUSTOMER'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }
    // 【用户-管理员】登录
    public function operate_user_customer_staff_login()
    {
        $user_id = request()->get('user_id');
        $user = DK_Customer_User::where('id',$user_id)->first();
        if($user)
        {
            Auth::guard('dk_customer_staff')->login($user,true);
            $token = request()->get('_token');
            Auth::guard('dk_customer_staff')->user()->admin_token = $token;
            Auth::guard('dk_customer_staff')->user()->save();

            $return['user'] = $user;
            $return['url'] = env('DOMAIN_DK_CUSTOMER');

            if(request()->isMethod('get')) return redirect(env('DOMAIN_DK_CUSTOMER'));
            else if(request()->isMethod('post'))
            {
                return response_success($return);
            }
        }
        else return response_error([]);
    }




    // 【用户】【全部用户】返回-列表-视图
    public function view_user_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_list_for_all_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_staff_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_staff_list_for_all_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_client_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_client_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_client_list_for_all_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_client_staff_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_client_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_client_staff_list_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_finance_user_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_finance_user_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_finance_user_list_for_all_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_customer_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_customer_list_for_all_datatable(request()->all());
    }
    // 【用户】【全部用户】返回-列表-视图
    public function view_user_customer_staff_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_customer_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_customer_staff_list_datatable(request()->all());
    }



    // 【用户】编辑
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


    // 【用户】修改-密码
    public function operate_user_staff_password_super_change()
    {
        return $this->repo->operate_user_staff_password_super_change(request()->all());
    }
    // 【用户】修改-密码
    public function operate_user_staff_password_super_reset()
    {
        return $this->repo->operate_user_staff_password_super_reset(request()->all());
    }








    /*
     * ITEM 内容
     */
    // 【内容】【全部】返回-列表-视图
    public function view_item_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_item_list_for_all_datatable(request()->all());
    }
    // 【内容】【全部】返回-列表-视图
    public function view_record_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_record_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_record_list_for_all_datatable(request()->all());
    }








    /*
     * District 地域管理
     */
    // 【地域】添加
    public function operate_district_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_district_create();
        else if (request()->isMethod('post')) return $this->repo->operate_district_save(request()->all());
    }
    // 【地域】编辑
    public function operate_district_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_district_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_district_save(request()->all());
    }

    // 【地域】【全部】返回-列表-视图
    public function view_district_list_for_all()
    {
        if(request()->isMethod('get')) return $this->repo->view_district_list_for_all(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_district_list_for_all_datatable(request()->all());
    }

    // 【地域】SELECT2
    public function operate_district_select2_parent()
    {
        return $this->repo->operate_district_select2_parent(request()->all());
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
    // 【K】【内容】返回-全部内容-列表-视图
    public function view_statistic_all_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_statistic_all_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_statistic_all_datatable(request()->all());
    }







}
