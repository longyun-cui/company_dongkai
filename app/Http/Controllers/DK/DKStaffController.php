<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Staff\DK_Staff__Record__by_Visit;

use App\Repositories\DK\DK_Staff\DK_Staff__CommonRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__IndexRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__CompanyRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__DepartmentRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__TeamRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__StaffRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__LocationRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__ClientRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__ProjectRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__OrderRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__DeliveryRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__ExportRepository;

use App\Repositories\DK\DK_Staff\DK_Staff__StatisticRepository;


use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKStaffController extends Controller
{
    //
    private $service;
    private $repo;

    private $common_repo;

    private $company_repo;
    private $department_repo;
    private $team_repo;
    private $staff_repo;

    private $location_repo;

    private $client_repo;
    private $project_repo;

    private $order_repo;
    private $delivery_repo;

    private $export_repo;

    private $statistic_repo;

    public function __construct()
    {
        $this->repo = new DK_Staff__IndexRepository;

        $this->common_repo = new DK_Staff__CommonRepository;

        $this->company_repo = new DK_Staff__CompanyRepository;
        $this->department_repo = new DK_Staff__DepartmentRepository;
        $this->team_repo = new DK_Staff__TeamRepository;
        $this->staff_repo = new DK_Staff__StaffRepository;

        $this->location_repo = new DK_Staff__LocationRepository;

        $this->client_repo = new DK_Staff__ClientRepository;
        $this->project_repo = new DK_Staff__ProjectRepository;

        $this->order_repo = new DK_Staff__OrderRepository;
        $this->delivery_repo = new DK_Staff__DeliveryRepository;

        $this->export_repo = new DK_Staff__ExportRepository;

        $this->statistic_repo = new DK_Staff__StatisticRepository;
    }

    /*
     * 登录 & 退出
     */
    // 登陆
    public function login1()
    {
        if(request()->isMethod('get'))
        {
            $view_blade = env('DK_STAFF__TEMPLATE').'login';
            return view($view_blade);
        }
        else if(request()->isMethod('post'))
        {
            $where['email'] = request()->get('email');
            $where['mobile'] = request()->get('mobile');
            $where['password'] = request()->get('password');

//            $email = request()->get('email');
//            $admin = OrgAdministrator::whereEmail($email)->first();

            $login_number = request()->get('login_number');
            $user = DK_Common__Staff::withTrashed()->where('login_number',$login_number)->first();

            if($user)
            {
                if($user->deleted_at == null)
                {
                    if($user->item_status == 1)
                    {
                        $password = request()->get('password');
                        if(password_check($password,$user->password))
                        {
                            $remember = request()->get('remember');
                            if($remember) Auth::guard('dk_staff_user')->login($user,true);
                            else Auth::guard('dk_staff_user')->login($user,true);
                            return response_success();
                        }
                        else return response_error([],'账户or密码不正确！');
                    }
                    else return response_error([],'账户已禁用！');
                }
                else return response_error([],'账户已删除！');
            }
            else return response_error([],'账户不存在！');
        }
    }

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

            $view_blade = env('DK_STAFF__TEMPLATE').'login';
            return view($view_blade);
        }
        else if(request()->isMethod('post'))
        {
            $where['email'] = request()->get('email');
            $where['mobile'] = request()->get('mobile');
            $where['password'] = request()->get('password');

//            $email = request()->get('email');
//            $admin = SuperAdministrator::whereEmail($email)->first();

//            $mobile = request()->get('mobile');
            $login_number = request()->get('login_number');
            $staff = DK_Common__Staff::withTrashed()->where('login_number',$login_number)->first();


            if($staff)
            {
                if($staff->item_status == 1)
                {
                    if($staff->login_error_num >= 3)
                    {
                        return response_error([],'账户or密码不正确啊啊啊！');
                    }

                    $token = request()->get('_token');
                    $password = request()->get('password');
                    if(password_check($password,$staff->password))
                    {
                        $remember = request()->get('remember');
                        if($remember) Auth::guard('dk_staff_user')->login($staff,true);
                        else Auth::guard('dk_staff_user')->login($staff);
                        Auth::guard('dk_staff_user')->user()->login_error_num = 0;
                        Auth::guard('dk_staff_user')->user()->only_token = $token;
                        Auth::guard('dk_staff_user')->user()->save();

                        if(Auth::guard('dk_staff_user')->user()->id > 10000)
                        {
                            $record["creator_id"] = Auth::guard('dk_staff_user')->user()->id;
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
                        $record["user_id"] = $staff->id;
                        $record["record_category"] = 99; // record_category=1 browse/search/share/login
                        $record["record_type"] = 99; // record_type=1 browse
                        $record["page_type"] = 1; // page_type=9 login
                        $record["page_module"] = 1; // page_module=1 login page
                        $record["page_num"] = 0;
                        $record["open"] = "login";
                        $record["from"] = request('from',NULL);
                        $this->record_for_user_visit($record);

                        $staff->increment('login_error_num');
                        if($staff->login_error_num >= 3)
                        {
                            $staff->staff_status = 99;
                            $staff->only_token = '';
                            $staff->save();
                        }
                        return response_error([],'账户or密码不正确！');
                    }
                }
                else if($staff->item_status == 99)
                {
                    $record["user_id"] = $staff->id;
                    $record["record_category"] = 99; // record_category=1 browse/search/share/login
                    $record["record_type"] = 99; // record_type=1 browse
                    $record["page_type"] = 1; // page_type=9 login
                    $record["page_module"] = 1; // page_module=1 login page
                    $record["page_num"] = 0;
                    $record["open"] = "login";
                    $record["from"] = request('from',NULL);
                    $this->record_for_user_visit($record);

                    $staff->increment('login_error_num');
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
        Auth::guard('dk_staff_user')->logout();
        return redirect('/login');
    }

    // 退出
    public function logout_without_token()
    {
        Auth::guard('dk_staff_user')->logout();
        return redirect('/login');
    }


    // 【记录】
    public function record_for_user_visit($post_data)
    {
        $record = new DK_Staff__Record__by_Visit();

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


    // 账号唯一登录
    public function check_is_only_me()
    {
        $result['message'] = 'failed';
        $result['result'] = 'denied';

        if(Auth::guard('dk_staff_user')->check())
        {
            $token = request('_token');
            if(Auth::guard('dk_staff_user')->user()->only_token == $token)
            {
                $result['message'] = 'success';
                $result['result'] = 'access';
            }
        }

        return Response::json($result);
    }




    // 【基本信息】修改-密码
    public function o1__my_account__password_change()
    {
        if(request()->isMethod('get')) return $this->common_repo->o1__my_account__password_change__view();
        else if (request()->isMethod('post')) return $this->common_repo->o1__my_account__password_change__save(request()->all());
    }




    /*
     * 首页
     */
	public function view__staff__index()
	{
        return $this->repo->view__staff__index();
	}
    public function view__staff__403()
    {
        return $this->repo->view__staff__403();
    }
    public function view__staff__404()
    {
        return $this->repo->view__staff__404();
    }










    // 公司
    public function o1__select2__company()
    {
        return $this->common_repo->o1__select2__company(request()->all());
    }
    // 部门
    public function o1__select2__department()
    {
        return $this->common_repo->o1__select2__department(request()->all());
    }
    // 团队
    public function o1__select2__team()
    {
        return $this->common_repo->o1__select2__team(request()->all());
    }
    // 员工
    public function o1__select2__staff()
    {
        return $this->common_repo->o1__select2__staff(request()->all());
    }
    // 地区
    public function o1__select2__location()
    {
        return $this->common_repo->o1__select2__location(request()->all());
    }
    // 客户
    public function o1__select2__client()
    {
        return $this->common_repo->o1__select2__client(request()->all());
    }
    // 项目
    public function o1__select2__project()
    {
        return $this->common_repo->o1__select2__project(request()->all());
    }
    // 订单
    public function o1__select2__order()
    {
        return $this->common_repo->o1__select2__order(request()->all());
    }












    // 【公司】datatable
    public function o1__company__list__datatable_query()
    {
        return $this->company_repo->o1__company__list__datatable_query(request()->all());
    }
    // 【公司】获取
    public function o1__company__item_get()
    {
        return $this->company_repo->o1__company__item_get(request()->all());
    }
    // 【公司】编辑-保存
    public function o1__company__item_save()
    {
        return $this->company_repo->o1__company__item_save(request()->all());
    }
    // 【公司】删除
    public function o1__company__item_delete()
    {
        return $this->company_repo->o1__company__item_delete(request()->all());
    }
    // 【公司】恢复
    public function o1__company__item_restore()
    {
        return $this->company_repo->o1__company__item_restore(request()->all());
    }
    // 【公司】彻底删除
    public function o1__company__item_delete_permanently()
    {
        return $this->company_repo->o1__company__item_delete_permanently(request()->all());
    }
    // 【公司】启用
    public function o1__company__item_enable()
    {
        return $this->company_repo->o1__company__item_enable(request()->all());
    }
    // 【公司】禁用
    public function o1__company__item_disable()
    {
        return $this->company_repo->o1__company__item_disable(request()->all());
    }
    // 【公司】操作记录
    public function o1__company__item_operation_record_list__datatable_query()
    {
        return $this->company_repo->o1__company__item_operation_record_list__datatable_query(request()->all());
    }








    // 【部门】datatable
    public function o1__department__list__datatable_query()
    {
        return $this->department_repo->o1__department__list__datatable_query(request()->all());
    }
    // 【部门】获取
    public function o1__department__item_get()
    {
        return $this->department_repo->o1__department__item_get(request()->all());
    }
    // 【部门】编辑-保存
    public function o1__department__item_save()
    {
        return $this->department_repo->o1__department__item_save(request()->all());
    }
    // 【部门】删除
    public function o1__department__item_delete()
    {
        return $this->department_repo->o1__department__item_delete(request()->all());
    }
    // 【部门】恢复
    public function o1__department__item_restore()
    {
        return $this->department_repo->o1__department__item_restore(request()->all());
    }
    // 【部门】彻底删除
    public function o1__department__item_delete_permanently()
    {
        return $this->department_repo->o1__department__item_delete_permanently(request()->all());
    }
    // 【部门】启用
    public function o1__department__item_enable()
    {
        return $this->department_repo->o1__department__item_enable(request()->all());
    }
    // 【部门】禁用
    public function o1__department__item_disable()
    {
        return $this->department_repo->o1__department__item_disable(request()->all());
    }
    // 【部门】操作记录
    public function o1__department__item_operation_record_list__datatable_query()
    {
        return $this->department_repo->o1__department__item_operation_record_list__datatable_query(request()->all());
    }








    // 【团队】datatable
    public function o1__team__list__datatable_query()
    {
        return $this->team_repo->o1__team__list__datatable_query(request()->all());
    }
    // 【团队】获取
    public function o1__team__item_get()
    {
        return $this->team_repo->o1__team__item_get(request()->all());
    }
    // 【团队】编辑-保存
    public function o1__team__item_save()
    {
        return $this->team_repo->o1__team__item_save(request()->all());
    }
    // 【团队】删除
    public function o1__team__item_delete()
    {
        return $this->team_repo->o1__team__item_delete(request()->all());
    }
    // 【团队】恢复
    public function o1__team__item_restore()
    {
        return $this->team_repo->o1__team__item_restore(request()->all());
    }
    // 【团队】彻底删除
    public function o1__team__item_delete_permanently()
    {
        return $this->team_repo->o1__team__item_delete_permanently(request()->all());
    }
    // 【团队】启用
    public function o1__team__item_enable()
    {
        return $this->team_repo->o1__team__item_enable(request()->all());
    }
    // 【团队】禁用
    public function o1__team__item_disable()
    {
        return $this->team_repo->o1__team__item_disable(request()->all());
    }
    // 【团队】操作记录
    public function o1__team__item_operation_record_list__datatable_query()
    {
        return $this->team_repo->o1__team__item_operation_record_list__datatable_query(request()->all());
    }








    // 【员工】datatable
    public function o1__staff__list__datatable_query()
    {
        return $this->staff_repo->o1__staff__list__datatable_query(request()->all());
    }
    // 【员工】获取
    public function o1__staff__item_get()
    {
        return $this->staff_repo->o1__staff__item_get(request()->all());
    }
    // 【员工】编辑-保存
    public function o1__staff__item_save()
    {
        return $this->staff_repo->o1__staff__item_save(request()->all());
    }
    // 【员工】删除
    public function o1__staff__item_delete()
    {
        return $this->staff_repo->o1__staff__item_delete(request()->all());
    }
    // 【员工】恢复
    public function o1__staff__item_restore()
    {
        return $this->staff_repo->o1__staff__item_restore(request()->all());
    }
    // 【员工】彻底删除
    public function o1__staff__item_delete_permanently()
    {
        return $this->staff_repo->o1__staff__item_delete_permanently(request()->all());
    }
    // 【员工】启用
    public function o1__staff__item_enable()
    {
        return $this->staff_repo->o1__staff__item_enable(request()->all());
    }
    // 【员工】禁用
    public function o1__staff__item_disable()
    {
        return $this->staff_repo->o1__staff__item_disable(request()->all());
    }
    // 【员工】登录
    public function o1__staff__item_password_reset()
    {
        return $this->staff_repo->o1__staff__item_password_reset(request()->all());
    }
    // 【员工】登录
    public function o1__staff__item_password_change()
    {
        return $this->staff_repo->o1__staff__item_password_change(request()->all());
    }
    // 【员工】登录
    public function o1__staff__item_login()
    {
        return $this->staff_repo->o1__staff__item_login(request()->all());
    }
    // 【员工】操作记录
    public function o1__staff__item_operation_record_list__datatable_query()
    {
        return $this->staff_repo->o1__staff__item_operation_record_list__datatable_query(request()->all());
    }








    // 【地区】datatable
    public function o1__location__list__datatable_query()
    {
        return $this->location_repo->o1__location__list__datatable_query(request()->all());
    }
    // 【地区】获取
    public function o1__location__item_get()
    {
        return $this->location_repo->o1__location__item_get(request()->all());
    }
    // 【地区】编辑-保存
    public function o1__location__item_save()
    {
        return $this->location_repo->o1__location__item_save(request()->all());
    }
    // 【地区】删除
    public function o1__location__item_delete()
    {
        return $this->location_repo->o1__location__item_delete(request()->all());
    }
    // 【地区】恢复
    public function o1__location__item_restore()
    {
        return $this->location_repo->o1__location__item_restore(request()->all());
    }
    // 【地区】彻底删除
    public function o1__location__item_delete_permanently()
    {
        return $this->location_repo->o1__location__item_delete_permanently(request()->all());
    }
    // 【地区】启用
    public function o1__location__item_enable()
    {
        return $this->location_repo->o1__location__item_enable(request()->all());
    }
    // 【地区】禁用
    public function o1__location__item_disable()
    {
        return $this->location_repo->o1__location__item_disable(request()->all());
    }
    // 【地区】登录
    public function o1__location__item_login()
    {
        return $this->location_repo->o1__location__item_login(request()->all());
    }
    // 【地区】操作记录
    public function o1__location__item_operation_record_list__datatable_query()
    {
        return $this->location_repo->o1__location__item_operation_record_list__datatable_query(request()->all());
    }








    // 【客户】datatable
    public function o1__client__list__datatable_query()
    {
        return $this->client_repo->o1__client__list__datatable_query(request()->all());
    }
    // 【客户】获取
    public function o1__client__item_get()
    {
        return $this->client_repo->o1__client__item_get(request()->all());
    }
    // 【客户】编辑-保存
    public function o1__client__item_save()
    {
        return $this->client_repo->o1__client__item_save(request()->all());
    }
    // 【客户】删除
    public function o1__client__item_delete()
    {
        return $this->client_repo->o1__client__item_delete(request()->all());
    }
    // 【客户】恢复
    public function o1__client__item_restore()
    {
        return $this->client_repo->o1__client__item_restore(request()->all());
    }
    // 【客户】彻底删除
    public function o1__client__item_delete_permanently()
    {
        return $this->client_repo->o1__client__item_delete_permanently(request()->all());
    }
    // 【客户】启用
    public function o1__client__item_enable()
    {
        return $this->client_repo->o1__client__item_enable(request()->all());
    }
    // 【客户】禁用
    public function o1__client__item_disable()
    {
        return $this->client_repo->o1__client__item_disable(request()->all());
    }
    // 【客户】操作记录
    public function o1__client__item_operation_record_list__datatable_query()
    {
        return $this->client_repo->o1__client__item_operation_record_list__datatable_query(request()->all());
    }




    // 【项目】datatable
    public function o1__project__list__datatable_query()
    {
        return $this->project_repo->o1__project__list__datatable_query(request()->all());
    }
    // 【项目】获取
    public function o1__project__item_get()
    {
        return $this->project_repo->o1__project__item_get(request()->all());
    }
    // 【项目】获取
    public function o1__project__item_team__get()
    {
        return $this->project_repo->o1__project__item_team__get(request()->all());
    }
    // 【项目】获取
    public function o1__project__item_staff__get()
    {
        return $this->project_repo->o1__project__item_staff__get(request()->all());
    }
    // 【项目】编辑-保存
    public function o1__project__item_save()
    {
        return $this->project_repo->o1__project__item_save(request()->all());
    }
    // 【项目】编辑-保存
    public function o1__project__item_team_set__save()
    {
        return $this->project_repo->o1__project__item_team_set__save(request()->all());
    }
    // 【项目】删除
    public function o1__project__item_delete()
    {
        return $this->project_repo->o1__project__item_delete(request()->all());
    }
    // 【项目】恢复
    public function o1__project__item_restore()
    {
        return $this->project_repo->o1__project__item_restore(request()->all());
    }
    // 【项目】彻底删除
    public function o1__project__item_delete_permanently()
    {
        return $this->project_repo->o1__project__item_delete_permanently(request()->all());
    }
    // 【项目】启用
    public function o1__project__item_enable()
    {
        return $this->project_repo->o1__project__item_enable(request()->all());
    }
    // 【项目】禁用
    public function o1__project__item_disable()
    {
        return $this->project_repo->o1__project__item_disable(request()->all());
    }
    // 【项目】操作记录
    public function o1__project__item_operation_record_list__datatable_query()
    {
        return $this->project_repo->o1__project__item_operation_record_list__datatable_query(request()->all());
    }








    /*
     * ORDER - 工单
     */
    // 【工单】datatable
    public function o1__order__list__datatable_query()
    {
        return $this->order_repo->o1__order__list__datatable_query(request()->all());
    }
    // 【工单】获取
    public function o1__order__item_get()
    {
        return $this->order_repo->o1__order__item_get(request()->all());
    }
    // 【工单】保存
    public function o1__order__item_save()
    {
        return $this->order_repo->o1__order__item_save(request()->all());
//        return $this->order_repo->o1__order_dental__item_save(request()->all());
//        $order_category = request('order_category','');
//        if($order_category == 1)
//        {
//            return $this->order_repo->o1__order_dental__item_save(request()->all());
//        }
//        else if($order_category == 11)
//        {
//            return $this->order_repo->o1__order_aesthetic__item_save(request()->all());
//        }
//        else if($order_category == 31)
//        {
//            return $this->order_repo->o1__order_luxury__item_save(request()->all());
//        }
//        else
//        {
//            return $this->order_repo->o1__order__item_save(request()->all());
//        }
    }
    // 【工单】【口腔】保存
    public function o1__order_dental__item_save()
    {
        return $this->order_repo->o1__order_dental__item_save(request()->all());
    }
    // 【工单】【医美】保存
    public function o1__order_aesthetic__item_save()
    {
        return $this->order_repo->o1__order_aesthetic__item_save(request()->all());
    }
    // 【工单】【二奢】保存
    public function o1__order_luxury__item_save()
    {
        return $this->order_repo->o1__order_luxury__item_save(request()->all());
    }


    // 【工单】导入
    public function o1__order__import__by_txt()
    {
        return $this->order_repo->o1__order__import__by_txt(request()->all());
    }




    // 【工单】发布
    public function o1__order__item_publish()
    {
        return $this->order_repo->o1__order__item_publish(request()->all());
    }
    // 【工单】审核
    public function o1__order__item_inspecting_save()
    {
        return $this->order_repo->o1__order__item_inspecting_save(request()->all());
    }
    // 【工单】申诉
    public function o1__order__item_appealing_save()
    {
        return $this->order_repo->o1__order__item_appealing_save(request()->all());
    }
    // 【工单】申诉处理
    public function o1__order__item_appealed_handling_save()
    {
        return $this->order_repo->o1__order__item_appealed_handling_save(request()->all());
    }
    // 【工单】交付
    public function o1__order__item_delivering_save()
    {
        return $this->order_repo->o1__order__item_delivering_save(request()->all());
    }
    // 【工单】批量交付
    public function o1__order__bulk_delivering_save()
    {
        return $this->order_repo->o1__order__item_delivering_save(request()->all());
    }
    // 【工单】一件交付
    public function o1__order__item_delivering_save__by_fool()
    {
        return $this->order_repo->o1__order__item_delivering_save__by_fool(request()->all());
    }
    // 【工单】一件批量交付
    public function o1__order__bulk_delivering_save__by_fool()
    {
        return $this->order_repo->o1__order__bulk_delivering_save__by_fool(request()->all());
    }


    // 【工单】获取录音
    public function o1__order__item_get_call_record__by_api()
    {
        return $this->order_repo->o1__order__item_get_call_record__by_api(request()->all());
    }


    // 【工单】【全部操作】操作记录
    public function o1__order__item_operation_record_list__datatable_query()
    {
        return $this->order_repo->o1__order__item_operation_record_list__datatable_query(request()->all());
    }
    // 【工单】【行程】列表
    public function o1__order__item_delivery_record_list__datatable_query()
    {
        return $this->order_repo->o1__order__item_delivery_record_list__datatable_query(request()->all());
    }


    // 【工单】【跟进】保存
    public function o1__order__item_follow_save()
    {
        return $this->order_repo->o1__order__item_follow_save(request()->all());
    }
    // 【工单】【行程】保存
    public function o1__order__item_inspect_save()
    {
        return $this->order_repo->o1__order__item_journey_save(request()->all());
    }
    // 【工单】【费用】保存
    public function o1__order__item_fee_save()
    {
        return $this->order_repo->o1__order__item_fee_save(request()->all());
    }
    // 【工单】【交费易】保存
    public function o1__order__item_trade_save()
    {
        return $this->order_repo->o1__order__item_trade_save(request()->all());
    }








    /*
     * DELIVERY - 交付
     */
    // 【工单】datatable
    public function o1__delivery__list__datatable_query()
    {
        return $this->delivery_repo->o1__delivery__list__datatable_query(request()->all());
    }








    /*
     * EXPORT - 导出
     */
    // 【工单】datatable
    public function o1__export__list__datatable_query()
    {
        return $this->export_repo->o1__export__list__datatable_query(request()->all());
    }

    // 【数据-导出】工单-下载
    public function o1__export__order__export__by_ids()
    {
        $order_category = request('order_category',0);

        if($order_category == 1)
        {
            return $this->export_repo->o1__export__order_dental__export__by_ids(request()->all());
        }
        else if($order_category == 11)
        {
            return $this->export_repo->o1__export__order_aesthetic__export__by_ids(request()->all());
        }
        else if($order_category == 31)
        {
            return $this->export_repo->o1__export__order_luxury__export__by_ids(request()->all());
        }
        else
        {
            return $this->export_repo->o1__export__order__export__by_ids(request()->all());
        }
    }











    /*
     * 统计
     */
    // 【统计】【生产】项目看板
    public function o1__statistic__production__project()
    {
        return $this->statistic_repo->o1__statistic__production__project(request()->all());
    }
    // 【统计】【生产】部门看板
    public function o1__statistic__production__department()
    {
        return $this->statistic_repo->o1__statistic__production__department(request()->all());
    }
    // 【统计】【生产】团队看板
    public function o1__statistic__production__team()
    {
        return $this->statistic_repo->o1__statistic__production__team(request()->all());
    }


    // 【统计】【员工】坐席概览
    public function o1__statistic__production__caller_overview()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_production_caller_overview(request()->all());
    }
    // 【统计】【员工】坐席排名
    public function o1__statistic__production__caller_rank()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_production_caller_rank(request()->all());
    }
    // 【统计】【员工】坐席近期
    public function o1__statistic__production__caller_recent()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_production_caller_recent(request()->all());
    }
    // 【统计】【员工】坐席日报
    public function o1__statistic__production__caller_daily()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_production_caller_daily(request()->all());
    }


    // 【统计】【交付】项目
    public function o1__statistic__marketing__project()
    {
        return $this->statistic_repo->o1__statistic__marketing__project(request()->all());
    }
    // 【统计】【交付】客户
    public function o1__statistic__marketing__client()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_marketing_client(request()->all());
    }


    // 【统计】【销售统计】公司概览
    public function o1__statistic__marketing____company_overview()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_company_overview(request()->all());
    }
    // 【统计】【销售统计】公司日报
    public function o1__statistic__marketing____company_daily()
    {
        return $this->statistic_repo->o1__get_statistic_data_of_company_daily(request()->all());
    }








    // 【统计】【项目】统计日报列表
    public function o1__statistic__project_daily__list__datatable_query()
    {
        return $this->statistic_repo->o1__statistic__project_daily__list__datatable_query(request()->all());
    }
    // 【统计】【项目】项目统计日报-生成
    public function o1__statistic__project_daily__create()
    {
        return $this->statistic_repo->o1__statistic__project_daily__create(request()->all());
    }
    // 【统计】【项目】字段修改
    public function o1__statistic__project_daily__item_field_set()
    {
        return $this->statistic_repo->o1__statistic__project_daily__item_field_set(request()->all());
    }
    // 【统计】【项目】确认
    public function o1__statistic__project_daily__item_confirm()
    {
        return $this->statistic_repo->o1__statistic__project_daily__item_confirm(request()->all());
    }
    // 【统计】【项目】删除
    public function o1__statistic__project_daily__item_delete()
    {
        return $this->statistic_repo->o1__statistic__project_daily__item_delete(request()->all());
    }
    // 【统计】项目统计
    public function o1__statistic__project_show()
    {
        return $this->statistic_repo->o1__statistic__project__show(request()->all());
    }
    // 【统计】项目统计
    public function o1__statistic__project_detail()
    {
        return $this->statistic_repo->o1__statistic__project__detail(request()->all());
    }


    // 【统计】【客户】统计日报列表
    public function o1__statistic__client_daily__list__datatable_query()
    {
        return $this->statistic_repo->o1__statistic__client_daily__list__datatable_query(request()->all());
    }
    // 【统计】【客户】统计日报列表-生成
    public function o1__statistic__client_daily__create()
    {
        return $this->statistic_repo->o1__statistic__client_daily__create(request()->all());
    }
    // 【统计】【客户】字段修改
    public function o1__statistic__client_daily__item_field_set()
    {
        return $this->statistic_repo->o1__statistic__client_daily__item_field_set(request()->all());
    }
    // 【统计】【客户】确认
    public function o1__statistic__client_daily__item_confirm()
    {
        return $this->statistic_repo->o1__statistic__client_daily__item_confirm(request()->all());
    }
    // 【统计】【客户】删除
    public function o1__statistic__client_daily__item_delete()
    {
        return $this->statistic_repo->o1__statistic__client_daily__item_delete(request()->all());
    }
    // 【统计】客户统计
    public function o1__statistic__client__show()
    {
        return $this->statistic_repo->o1__statistic__client__show(request()->all());
    }
    // 【统计】客户统计
    public function o1__statistic__client__detail()
    {
        return $this->statistic_repo->o1__statistic__client__detail(request()->all());
    }



    // 【统计】【通话统计】
    public function o1_statistic__call_task_analysis__datatable_query()
    {
        return $this->statistic_repo->o1_statistic__call_task_analysis__datatable_query(request()->all());
    }

}

