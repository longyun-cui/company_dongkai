<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;

use App\Models\DK\DK_Common\DK_Common__Location;

use App\Models\DK\DK_Common\DK_Common__Client;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Order;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__CommonRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelOrder;
    private $view_blade_403;
    private $view_blade_404;


    public function __construct()
    {
        $this->modelUser = new DK_Common__Staff;
        $this->modelOrder = new DK_Common__Order;

        $this->view_blade_403 = env('TEMPLATE_WL_STAFF').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_WL_STAFF').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_staff_user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_staff_user")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }



    /*
     * select2
     */
    // 公司
    public function o1__select2__company($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Company::select(['id','name as text'])
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'all')
            {
            }
            else if($type == 'company')
            {
                $query->where(['company_category'=>1]);
            }
            else if($type == 'channel')
            {
                $query->where(['company_category'=>11]);
                if(!empty($post_data['company_id']))
                {
                    $query->where('superior_company_id',$post_data['company_id']);
                }
            }
            else if($type == 'business')
            {
                $query->where(['company_category'=>21]);
                if(!empty($post_data['channel_id']))
                {
                    $query->where('superior_company_id',$post_data['channel_id']);
                }
            }
            else
            {
//                $query->where(['department_type'=>11]);
            }
        }
        else
        {
//            $query->where(['department_type'=>11]);
        }

//        if($me->staff_type == 81)
//        {
//            $query->where('id',$me->department_district_id);
//        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'[选择公司]'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    // 部门
    public function o1__select2__department($post_data)
    {
        $query = DK_Common__Department::select(['id','name as text'])
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['department_category']))
        {
            $query->where('department_category',$post_data['department_category']);
        }
        if(!empty($post_data['department_type']))
        {
            $query->where('department_type',$post_data['department_type']);
        }
        if(!empty($post_data['company_id']))
        {
            $query->where('company_id',$post_data['company_id']);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择部门'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    // 团队
    public function o1__select2__team($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Team::select(['id','name as text'])
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }


        if(in_array($me->staff_position,[31,41,51,61,71]))
        {
            $query->where('department_id',$me->department_id);
        }

        if(in_array($me->staff_position,[41,51,61,71]))
        {
            $query->where('superior_team_id',$me->team_id);
        }

        if(in_array($me->staff_position,[61]))
        {
            $query->where('superior_team_group_id',$me->team_group_id);
        }


        // 部门类型
        if(!empty($post_data['department_type']))
        {
            $query->where('department_type',$post_data['department_type']);
        }
        // 部门id
        if(!empty($post_data['department_id']))
        {
            $query->where('department_id',$post_data['department_id']);
        }
        // 团队种类
        if(!empty($post_data['item_category']))
        {
            $query->where('team_category',$post_data['item_category']);
        }
        // 团队类型
        if(!empty($post_data['item_type']))
        {
            $query->where('team_type',$post_data['item_type']);
        }
        // 团队种类
        if(!empty($post_data['team_category']))
        {
            $query->where('team_category',$post_data['team_category']);
        }
        // 团队类型
        if(!empty($post_data['team_type']))
        {
            $query->where('team_type',$post_data['team_type']);
        }
        // 上级团队
        if(!empty($post_data['superior_team_id']))
        {
            $query->where('superior_team_id',$post_data['superior_team_id']);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择团队'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    // 员工
    public function o1__select2__staff($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Staff::select(['id','username as text'])
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }


        if($me->department_id > 0)
        {
            $query->where('department_id',$me->department_id);
        }
        if($me->team_id > 0)
        {
            $query->where('team_id',$me->team_id);
        }


        if(!empty($post_data['staff_category']))
        {
            $staff_category_int = intval($post_data['staff_category']);
            if(!in_array($staff_category_int,[-1,0]))
            {
                $query->where('staff_category',$staff_category_int);
            }
        }
        if(!empty($post_data['staff_type']))
        {
            $staff_type_int = intval($post_data['staff_type']);
            if(!in_array($staff_type_int,[-1,0]))
            {
                $query->where('staff_type',$staff_type_int);
            }
        }


        if(!empty($post_data['type']))
        {
            $type = $post_data['type'];
            if($type == 'inspector') $query->where(['user_type'=>77]);
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择员工'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    // 地区
    public function o1__select2__location($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $item_category = $post_data['item_category'];

        if($item_category == 1)
        {
            $query = DK_Common__Location::select(['id','location_city as text'])
                ->where(['item_status'=>1]);

            if(!empty($post_data['keyword']))
            {
                $keyword = "%{$post_data['keyword']}%";
                $query->where('location_city','like',"%$keyword%");
            }

            $list = $query->orderBy('id','asc')->get()->toArray();
        }
        else if($item_category == 11)
        {
            $location_city = !empty($post_data['location_city']) ? $post_data['location_city'] : '';
            $query = DK_Common__Location::select(['id','location_district as text'])
                ->where('location_city',$location_city)
                ->where(['item_status'=>1]);

            if(!empty($post_data['keyword']))
            {
                $keyword = "%{$post_data['keyword']}%";
                $query->where('location_district','like',"%$keyword%");
            }

            $query_list = $query->orderBy('id','asc')->get()->toArray();

            if(count($query_list) > 0)
            {
                $list = explode("-",$query_list[0]['text']);
                foreach($list as $key => $value)
                {
                    $list[$key] = ['id'=>$value,'text'=>$value];
                }
            }
            else
            {
                $list = [];
            }
        }







//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
//        $unSpecified = ['id'=>-1,'text'=>'[选择地区]'];
//        array_unshift($list,$unSpecified);

        return $list;
    }
    // 客户
    public function o1__select2__client($post_data)
    {
        $query = DK_Common__Client::select(['id','name as text'])
            ->where(['item_status'=>1]);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('username','like',"%$keyword%");
        }

        if(!empty($post_data['client_category']))
        {
            $client_category_int = intval($post_data['client_category']);
            if(!in_array($client_category_int,[-1,0]))
            {
                $query->where('client_category',$client_category_int);
            }
        }
        if(!empty($post_data['client_type']))
        {
            $client_type_int = intval($post_data['client_type']);
            if(!in_array($client_type_int,[-1,0]))
            {
                $query->where('client_type',$client_type_int);
            }
        }

        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择客户'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    // 项目
    public function o1__select2__project($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Project::select(['id','name as text','transport_departure_place','transport_destination_place','transport_distance','transport_time_limitation','freight_amount'])
            ->where('item_status',1);

        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }

        if(!empty($post_data['project_category']))
        {
            $project_category_int = intval($post_data['project_category']);
            if(!in_array($project_category_int,[-1,0]))
            {
                $query->where('project_category',$project_category_int);
            }
        }
        if(!empty($post_data['project_type']))
        {
            $project_type_int = intval($post_data['project_type']);
            if(!in_array($project_type_int,[-1,0,]))
            {
                $query->where('project_type',$project_type_int);
            }
        }


        $list = $query->orderBy('id','asc')->get()->toArray();

//        $unSpecified = ['id'=>0,'text'=>'[未指定]'];
//        array_unshift($list,$unSpecified);
        $unSpecified = ['id'=>-1,'text'=>'选择项目'];
        array_unshift($list,$unSpecified);

        return $list;
    }
    



}