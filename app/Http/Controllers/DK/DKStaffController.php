<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK_Staff\DK_Staff__User;

use App\Repositories\DK\DKStaffRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__IndexRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__CompanyRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__DepartmentRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__TeamRepository;
use App\Repositories\DK\DK_Staff\DK_Staff__StaffRepository;
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

    private $motorcade_repo;
    private $car_repo;
    private $driver_repo;

    private $client_repo;
    private $project_repo;

    private $order_repo;

    public function __construct()
    {
        $this->repo = new DK_Staff__IndexRepository;

        $this->company_repo = new DK_Staff__CompanyRepository;
        $this->department_repo = new DK_Staff__DepartmentRepository;
        $this->team_repo = new DK_Staff__TeamRepository;
        $this->staff_repo = new DK_Staff__StaffRepository;

        $this->order_repo = new DK_Staff__OrderRepository;
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
            $user = DK_Staff__User::withTrashed()->where('login_number',$login_number)->first();

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
    // 【团队】删除
    public function o1__staff__item_delete()
    {
        return $this->staff_repo->o1__staff__item_delete(request()->all());
    }
    // 【团队】恢复
    public function o1__staff__item_restore()
    {
        return $this->staff_repo->o1__staff__item_restore(request()->all());
    }
    // 【团队】彻底删除
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
    // 【员工】操作记录
    public function o1__staff__item_operation_record_list__datatable_query()
    {
        return $this->staff_repo->o1__staff__item_operation_record_list__datatable_query(request()->all());
    }





}

