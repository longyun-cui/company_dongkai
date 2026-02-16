<?php
namespace App\Http\Controllers\DK\DK_Staff;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;
use App\Models\DK\YH_Item;
use App\Models\DK\YH_Pivot_Item_Relation;

use App\Repositories\DK\DKStaffRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DK_Staff__v1_Controller extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
        $this->repo = new DKStaffRepository;
    }

    /*
     * 登录 & 退出
     */
    // 登陆
    public function login()
    {
        if(request()->isMethod('get'))
        {
            $view_blade = env('TEMPLATE_YH_STAFF').'entrance.login';
            return view($view_blade);
        }
        else if(request()->isMethod('post'))
        {
            $where['email'] = request()->get('email');
            $where['mobile'] = request()->get('mobile');
            $where['password'] = request()->get('password');

//            $email = request()->get('email');
//            $admin = OrgAdministrator::whereEmail($email)->first();

            $mobile = request()->get('mobile');
            $user = DK_User::withTrashed()->whereMobile($mobile)->first();

            if($user)
            {
                if($user->deleted_at == null)
                {
                    if($user->user_status == 1)
                    {
                        $password = request()->get('password');
                        if(password_check($password,$user->password))
                        {
                            $remember = request()->get('remember');
                            if($remember) Auth::guard('yh_staff')->login($user,true);
                            else Auth::guard('yh_staff')->login($user,true);
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
        Auth::guard('yh_staff')->logout();
        return redirect('/login');
    }




    /*
     * 首页
     */
	public function view_staff_index()
	{
        return $this->repo->view_staff_index();
	}


    // 【内容列表】返回-列表-视图
    public function view_item_list()
    {
        return $this->repo->view_item_list(request()->all());
    }


    // 【内容详情】返回-列表-视图
    public function view_item($id=0)
    {
        return $this->repo->view_item(request()->all(),$id);
    }





    // 【K】【基本信息】返回
    public function view_my_profile_info_index()
    {
        return $this->repo->view_my_profile_info_index();
    }
    // 【K】【基本信息】编辑
    public function operate_my_profile_info_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_my_profile_info_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_my_profile_info_save(request()->all());
    }

    // 【K】【基本信息】编辑
    public function operate_my_account_password_change()
    {
        if(request()->isMethod('get')) return $this->repo->view_my_account_password_change();
        else if (request()->isMethod('post')) return $this->repo->operate_my_account_password_change_save(request()->all());
    }



    /*
     * 员工管理
     */
    // 【员工】添加
    public function operate_user_staff_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_create();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }
    // 【员工】编辑
    public function operate_user_staff_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_user_staff_save(request()->all());
    }


    // 【员工】返回-列表-视图
    public function view_user_staff_list()
    {
        if(request()->isMethod('get')) return $this->repo->view_user_staff_list(request()->all());
        else if(request()->isMethod('post')) return $this->repo->get_user_staff_list_datatable(request()->all());
    }

    // 【员工】获取-详情
    public function operate_user_staff_get()
    {
        return $this->repo->operate_user_staff_get(request()->all());
    }
    // 【员工】删除
    public function operate_user_staff_delete()
    {
        return $this->repo->operate_user_staff_delete(request()->all());
    }
    // 【员工】恢复
    public function operate_user_staff_restore()
    {
        return $this->repo->operate_user_staff_restore(request()->all());
    }
    // 【员工】永久删除
    public function operate_user_staff_delete_permanently()
    {
        return $this->repo->operate_user_staff_delete_permanently(request()->all());
    }








    /*
     * 任务管理
     */
    // 【任务】添加
    public function operate_item_task_create()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_task_create();
        else if (request()->isMethod('post')) return $this->repo->operate_item_task_save(request()->all());
    }
    // 【任务】编辑
    public function operate_item_task_edit()
    {
        if(request()->isMethod('get')) return $this->repo->view_item_task_edit();
        else if (request()->isMethod('post')) return $this->repo->operate_item_task_save(request()->all());
    }

    // 【任务】获取-详情
    public function operate_item_task_get()
    {
        return $this->repo->operate_item_task_get(request()->all());
    }
    // 【任务】删除
    public function operate_item_task_delete()
    {
        return $this->repo->operate_item_task_delete(request()->all());
    }
    // 【任务】恢复
    public function operate_item_task_restore()
    {
        return $this->repo->operate_item_task_restore(request()->all());
    }
    // 【任务】永久删除
    public function operate_item_task_delete_permanently()
    {
        return $this->repo->operate_item_task_delete_permanently(request()->all());
    }
    // 【任务】发布
    public function operate_item_task_publish()
    {
        return $this->repo->operate_item_task_publish(request()->all());
    }
    // 【任务】完成
    public function operate_item_task_complete()
    {
        return $this->repo->operate_item_task_complete(request()->all());
    }
    // 【任务】禁用
    public function operate_item_task_disable()
    {
        return $this->repo->operate_item_admin_disable(request()->all());
    }
    // 【任务】启用
    public function operate_item_task_enable()
    {
        return $this->repo->operate_item_admin_enable(request()->all());
    }

    // 【任务】备注编辑
    public function operate_item_task_remark_edit()
    {
        return $this->repo->operate_item_task_remark_save(request()->all());
    }











    // 返回【主页】视图
    public function operate_item_select2_people()
    {
        return $this->repo->operate_item_select2_people(request()->all());
    }




    /*
     * Statistic 统计
     */
    // 【统计】概览
    public function view_statistic_index()
    {
        return $this->repo->view_statistic_index();
    }






}

