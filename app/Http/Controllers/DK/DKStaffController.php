<?php
namespace App\Http\Controllers\DK;

use App\Repositories\DK\DK_Staff\DK_Staff__DeliveryRepository;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_Common\DK_Common__Staff;

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
    }

    /*
     * 登录 & 退出
     */
    // 登陆
    public function login()
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

    // 退出
    public function logout()
    {
        Auth::guard('dk_staff_user')->logout();
        return redirect('/login');
    }




    /*
     * 首页
     */
	public function view_staff_index()
	{
        return $this->repo->view_staff_index();
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
    // 【项目】编辑-保存
    public function o1__project__item_save()
    {
        return $this->project_repo->o1__project__item_save(request()->all());
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





}

