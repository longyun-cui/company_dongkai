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

use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__TestRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->view_blade_403 = env('DK_STAFF__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_STAFF__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_staff_user")->check())
        {
            $admin = Auth::guard("dk_staff_user")->user();

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




    // 返回【主页】视图
    public function view__staff__test_index()
    {
        $this->get_me();
        $me = $this->me;
        dd($me->toArray());
    }


    // 返回【主页】视图
    public function view__staff__test__temp()
    {
        $this->get_me();
        $me = $this->me;

        $team_count = DK_Common__Order::select('creator_team_id','creator_team_group_id')
            ->addSelect(DB::raw("
                    count(IF(is_published = 1, TRUE, NULL)) as order_count__for__all,
                    count(IF(is_published = 1 AND inspected_status = 1, TRUE, NULL)) as order_count__for__inspected,
                    count(IF(inspected_result in ('通过','折扣通过'), TRUE, NULL)) as order_count__for__effective,
                    count(IF(inspected_result in ('通过','折扣通过','郊区通过','内部通过'), TRUE, NULL)) as order_count__for__accepted,
                    count(IF(inspected_result = '通过', TRUE, NULL)) as order_count__for__accepted_normal,
                    count(IF(inspected_result = '折扣通过', TRUE, NULL)) as order_count__for__accepted_discount,
                    count(IF(inspected_result = '郊区通过', TRUE, NULL)) as order_count__for__accepted_suburb,
                    count(IF(inspected_result = '内部通过', TRUE, NULL)) as order_count__for__accepted_inside,
                    count(IF(inspected_result = '重复', TRUE, NULL)) as order_count__for__repeated,
                    count(IF(inspected_result = '拒绝' or inspected_result = '不合格', TRUE, NULL)) as order_count__for__refused
                "))
            ->groupBy('creator_team_id','creator_team_group_id')
            ->orderBy('creator_team_id')
            ->orderBy('creator_team_group_id')
            ->get();


        $twoDimensionalArray = $team_count->groupBy('creator_team_id')
            ->map(function ($teamGroup) {
                return $teamGroup->keyBy('creator_team_group_id');
            })
            ->toArray();
        dd($twoDimensionalArray);
    }


}