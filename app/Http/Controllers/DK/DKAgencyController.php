<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;
use App\Models\DK\DK_Company;
use App\Models\DK_Agency\DK_Agency_Record_Visit;

use App\Repositories\DK\DKAgencyRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKAgencyController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new DKAgencyRepository;
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
        Auth::guard('dk_cc')->user()->admin_token = '';
        Auth::guard('dk_cc')->user()->save();
        Auth::guard('dk_cc')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_cc')->logout();
        return redirect('/login');
    }




    // 返回主页视图
    public function view_admin_index()
    {
        return $this->repo->view_admin_index();
    }




    // 【记录】
    public function record_for_user_visit($post_data)
    {
        $record = new DK_Agency_Record_Visit();

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



    /*
     * 交付管理
     */
    // 【交付】返回-列表-视图（全部任务）
    public function get_datatable_delivery_list()
    {
        return $this->repo->get_datatable_delivery_list(request()->all());
    }
    public function get_datatable_delivery_daily()
    {
        return $this->repo->get_datatable_delivery_daily(request()->all());
    }
    public function get_datatable_delivery_project()
    {
        return $this->repo->get_datatable_delivery_project(request()->all());
    }









}
