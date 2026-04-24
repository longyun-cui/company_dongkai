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
use App\Models\DK\DK_Common\DK_Common__Order__Operation_Record;
use App\Models\DK\DK_Common\DK_Common__Delivery;

use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;

use App\Models\DK\DK_Common\DK_Common__Order__AI_Inspected__Record;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__AIRepository {

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
     * 客户-管理 Client
     */
    // 【AI】返回-列表-数据
    public function o1__ai__record__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Order__AI_Inspected__Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'order_er'=>function($query) { $query->select(['*']); },
            ])
            ->where('active',1);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);


        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            $item_status_int = intval($post_data['item_status']);
            if(!in_array($item_status_int,[-1,0]))
            {
                $query->where('item_status', $item_status_int);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 50;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->withTrashed()->get();

        foreach ($list as $k => $v)
        {
            if(!empty($v->result))
            {
                $result = json_decode($v->result);
                if(isset($result->choices[0]->message->content))
                {
                    $content = $result->choices[0]->message->content;
                    $content_decode = json_decode($content);
                    if(!$content_decode)
                    {
                        $content_fix = robustJsonFix($content);
                        $content_decode = json_decode($content_fix);
                        if(!$content_decode)
                        {
                            $content_fix_2 = robustJsonFixer($content_fix);
                            $content_decode = json_decode($content_fix_2);
                        }
                    }
                    $list[$k]->content = $content_decode;
                }
                else
                {
                    $list[$k]->content = null;
                }
                if(isset($result->usage))
                {
                    $list[$k]->usage = $result->usage;
                }
                else
                {
                    $list[$k]->usage = null;
                }
            }
            else
            {
                $list[$k]->content = null;
                $list[$k]->usage = null;
            }
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }




}