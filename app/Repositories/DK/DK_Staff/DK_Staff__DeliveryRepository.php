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

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__DeliveryRepository {

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




    // 【交付】返回-列表-数据
    public function o1__delivery__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,71])) return response_error([],"你没有操作权限！");

        $query = DK_Common__Delivery::select('*')
            ->with([
                'inspector_er',
                'original_project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'project_er'=>function($query) { $query->select('id','name','alias_name'); },
                'client_er'=>function($query) { $query->select('id','name'); },
                'company_er'=>function($query) { $query->select('id','name'); },
                'channel_er'=>function($query) { $query->select('id','name'); },
                'business_er'=>function($query) { $query->select('id','name'); },
                'order_er',
                'creator'=>function($query) { $query->select('id','name'); }
            ]);


        if(in_array($me->staff_position,[99]))
        {
            $query->where('creator_id',$me->id);
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->where("delivered_date", $post_data['assign']);


        // 交付时间
        if(!empty($post_data['assign_start']) && !empty($post_data['assign_ended']))
        {
            $query->whereDate("delivered_date", '>=', $post_data['assign_start']);
            $query->whereDate("delivered_date", '<=', $post_data['assign_ended']);
        }
        else if(!empty($post_data['assign_start']))
        {
            $query->where("delivered_date", $post_data['assign_start']);
        }
        else if(!empty($post_data['assign_ended']))
        {
            $query->where("delivered_date", $post_data['assign_ended']);
        }

        // 工单种类 []
        if(isset($post_data['order_category']))
        {
            $order_category = (int)$post_data['order_category'];
            if(!in_array($order_category,[-1,0]))
            {
                $query->where('order_category', $order_category);
            }
        }

        // 客户
        if(isset($post_data['client']))
        {
            $client = (int)$post_data['client'];
            if(!in_array($client,[-1]))
            {
                $query->where('client_id', $client);
            }
        }

        // 项目
        if(isset($post_data['project']))
        {
            $project = (int)$post_data['project'];
            if(!in_array($project,[-1]))
            {
                $query->where('project_id', $project);
            }
        }

        // 交付类型
        if(!empty($post_data['delivery_type']))
        {
            $delivery_type = (int)$post_data['delivery_type'];
            if(!in_array($delivery_type,[-1]))
            {
                $query->where('delivery_type', $delivery_type);
            }
        }

        // 交付结果
        if(!empty($post_data['delivered_result']))
        {
            // 单选
            if(!in_array($post_data['delivered_result'],[-1]))
            {
                $query->where('delivered_result', $post_data['delivered_result']);
            }
            // 多选
//            if(count($post_data['delivered_result']))
//            {
//                $query->whereIn('delivered_result', $post_data['delivered_result']);
//            }
        }

        // 患者类型
        if(isset($post_data['client_type']))
        {
            $client_type = (int)$post_data['client_type'];
            if(!in_array($client_type,[-1]))
            {
                $query->where('client_type', $client_type);
            }
        }


        $total = $query->count();

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;
        if($limit > 100) $limit = 100;

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

        if($limit == -1) $list = $query->skip($skip)->take(100)->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);

            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());


//        if($me->id > 10000)
//        {
//            $record["creator_id"] = $me->id;
//            $record["record_category"] = 1; // record_category=1 browse/share
//            $record["record_type"] = 1; // record_type=1 browse
//            $record["page_type"] = 1; // page_type=1 default platform
//            $record["page_module"] = 2; // page_module=2 other
//            $record["page_num"] = ($skip / $limit) + 1;
//            $record["open"] = "delivery-list";
//            $record["from"] = request('from',NULL);
//            $this->record_for_user_visit($record);
//        }


        return datatable_response($list, $draw, $total);
    }


    // 【交付】删除
    public function o1__delivery__item_delete($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $operate = $post_data["operate"];
        if($operate != 'delivery--item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $item = DK_Common__Delivery::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $order_id  = $item->order_id;
        $order = DK_Common__Order::withTrashed()->find($order_id);
        if(!$order) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->staff_category,[0,1,9,71])) return response_error([],"用户类型错误！");


        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $item_id;
        $record_data["order_id"] = $item->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 71;
        $record_data["operate_type"] = 101;
        $record_data["column_name"] = "delivered_result";

        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '员工操作';
            $record_row['field'] = 'deliver_delete';
            $record_row['before'] = '';
            if($item->delivery_type == 1) $record_row['after'] = '交付删除';
            else if($item->delivery_type == 11) $record_row['after'] = '分发删除';

            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作时间';
            $record_row['field'] = 'delivered_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '交付id';
            $record_row['field'] = 'delivered_id';
            $record_row['code'] = '';
            $record_row['before'] = '';
            $record_row['after'] = $item_id;
            $record_content[] = $record_row;
        }

        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($item->delivery_type == 1)
            {
                $order->delivered_status = 101;
                $order->delivered_result = '交付撤回';
                $order->delivered_project_id = 0;
                $order->delivered_client_id = 0;
                $bool_order = $order->save();  // 普通删除
                if(!$bool_order) throw new Exception("DK_Common__Order--update--fail");
            }

            $item->timestamps = false;
//            $bool = $item->forceDelete();  // 永久删除
            $bool = $item->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Common__Delivery--delete--fail");
            else
            {
                $record = new DK_Common__Order__Operation_Record;

                $bool_record = $record->fill($record_data)->save();
                if(!$bool_record) throw new Exception("DK_Common__Order__Operation_Record--insert--fail");
            }

            DB::commit();

            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }


    // 【交付-管理】导出状态
    public function v1_operate_for_delivery_exported($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-exported') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Delivery::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
        if(in_array($me->user_type,[66]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->is_exported = 1;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            else
            {
                $record = new DK_Common__Order_Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["order_id"] = $id;
                $record_data["operate_object"] = 91;
                $record_data["operate_category"] = 99;
                $record_data["operate_type"] = 1;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }
    // 【交付-管理】批量-导出状态
    public function v1_operate_for_delivery_bulk_exported($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'operate_result.required' => 'operate_result.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'operate_result' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery-exported-bulk') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $operate_result = $post_data["operate_result"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_para['is_exported'] = $operate_result;

//            $bool = DK_Common__Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $item = DK_Common__Delivery::withTrashed()->find($id);
                if(!$item) return response_error([],"该内容不存在，刷新页面重试！");


                $before = $item->is_exported;

                $item->is_exported = $operate_result;
                $bool = $item->save();
                if(!$bool) throw new Exception("item--update--fail");
                else
                {
                    $record = new DK_Common__Order_Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 21;
                    $record_data["record_category"] = 11;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["order_id"] = $id;
                    $record_data["operate_object"] = 91;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "is_exported";

                    $record_data["before"] = $before;
                    $record_data["after"] = $operate_result;

//                $record_data["before_client_id"] = $before;
//                $record_data["after_client_id"] = $client_id;

                    $bool_1 = $record->fill($record_data)->save();
                    if(!$bool_1) throw new Exception("insert--record--fail");
                }

            }


            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }

    }



}