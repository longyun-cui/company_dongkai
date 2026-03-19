<?php
namespace App\Repositories\DK\DK_Client;

use App\Models\DK\DK_Common\DK_Common__Delivery;
use App\Models\DK\DK_Client\DK_Client__Staff;
use App\Models\DK\DK_Client\DK_Client__Delivery__Operation_Record;
use App\Models\DK\DK_Client\DK_Client__Trade_Record;

use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Contact;

use App\Models\DK_Client\DK_Client_Follow_Record;
use App\Models\DK_Client\DK_Client_Trade_Record;


use App\Models\DK\DK_Client;


use App\Jobs\DK_Client\AutomaticDispatchingJob;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DK_Client__DeliveryRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_Client__Staff;

        $this->view_blade_403 = env('DK_CLIENT__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_CLIENT__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_client__user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_client__user")->user();
            $this->me->load('client_er');
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 【交付管理】返回-列表-数据
    public function query_last_delivery()
    {
        $this->get_me();
        $me = $this->me;

        $last_delivery = DK_Common__Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                return $query->where('department_id', $me->department_id);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            })
            ->orderBy('id','desc')
            ->first();
        if($last_delivery) return response_success(['last_delivery'=>$last_delivery]);
        else return response_success([]);
    }





    // 【交付】返回-列表-数据
    public function o1__delivery__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_category,[0,1,9,99])) return response_error([],"你没有操作权限！");

        $query = DK_Common__Delivery::select('*')
            ->with([
                'order_er',
                'client_staff_er'=>function($query) { $query->select('id','name'); },
            ])
            ->where('client_id',$me->client_id);


        if(in_array($me->staff_position,[99]))
        {
            $query->where('client_staff_id',$me->id);
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

        // 导出状态
        if(isset($post_data['exported_status']))
        {
            if(!in_array($post_data['exported_status'],[-1,'-1']))
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }

        // 分配状态
        if(isset($post_data['assign_status']))
        {
            if(!in_array($post_data['assign_status'],[-1,'-1']))
            {
//                $query->where('assign_status', $post_data['assign_status']);
                if($post_data['assign_status'] == 0)
                {
                    $query->where('client_staff_id', 0);
                }
                else if($post_data['assign_status'] == 1)
                {
                    $query->where('client_staff_id', '>', 0);
                }
            }
        }


        // 回访状态
        if(isset($post_data['is_callback']))
        {
            if(!in_array($post_data['is_callback'],[-1,'-1']))
            {
                $query->where('is_callback', $post_data['is_callback']);
            }
        }
        // 回访时间
        if(!empty($post_data['callback_date'])) $query->where('callback_date', $post_data['callback_date']);


        // 上门状态
        if(isset($post_data['is_come']))
        {
            if(!in_array($post_data['is_come'],[-1,'-1']))
            {
                $query->where('is_come', $post_data['is_come']);
            }
        }
        // 上门时间
        if(!empty($post_data['come_date'])) $query->where('come_date', $post_data['come_date']);


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


        return datatable_response($list, $draw, $total);
    }
    // 【交付】返回-列表-数据
    public function v1_operate_for_delivery_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->with([
                'order_er'=>function($query) {
                    $query->with(['creator'=>function($query) { $query->select('id','username'); }]);
                },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
                'client_contact_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            })
            ->when((in_array($me->user_type,[81,84]) && $me->client_er->user_category != 31), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->where('delivered_date', $post_data['assign']);



        // 交付客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,'-1']))
            {
                $query->where('client_id', $post_data['client']);
            }
        }

        // 上门状态
        if(isset($post_data['is_wx']))
        {
            if(in_array($post_data['is_wx'],[0,1]))
            {
//                if($post_data['is_wx'] == 0) $query->where('client_contact_id', 0);
//                else if($post_data['is_wx'] == 1) $query->where('client_contact_id', '>', 0);
                if($post_data['is_wx'] == 0) $query->where('is_wx', 0);
                else if($post_data['is_wx'] == 1) $query->where('is_wx', 1);
            }
        }

        // 联系渠道
        if(isset($post_data['contact']))
        {
            if(count($post_data['contact']) > 0)
            {
                $query->whereIn('client_contact_id',$post_data['contact']);
            }
        }


        // 回访状态
        if(isset($post_data['is_callback']))
        {
            if(!in_array($post_data['is_callback'],[-1,'-1']))
            {
                $query->where('is_callback', $post_data['is_callback']);
            }
        }
        // 回访时间
        if(!empty($post_data['callback_date'])) $query->where('callback_date', $post_data['callback_date']);


        // 上门状态
        if(isset($post_data['is_come']))
        {
            if(!in_array($post_data['is_come'],[-1,'-1']))
            {
                $query->where('is_come', $post_data['is_come']);
            }
        }
        // 上门时间
        if(!empty($post_data['come_date'])) $query->where('come_date', $post_data['come_date']);



        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('delivered_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        // 患者类型
        if(isset($post_data['client_type']))
        {
            if(!in_array($post_data['client_type'],[-1,'-1']))
            {
                $query->where('client_type', $post_data['client_type']);
            }
        }

        // 导出状态
        if(isset($post_data['exported_status']))
        {
            if(!in_array($post_data['exported_status'],[-1,'-1']))
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }

        // 分配状态
        if(isset($post_data['assign_status']))
        {
            if(!in_array($post_data['assign_status'],[-1,'-1']))
            {
//                $query->where('assign_status', $post_data['assign_status']);
                if($post_data['assign_status'] == 0)
                {
                    $query->where('client_staff_id', 0);
                }
                else if($post_data['assign_status'] == 1)
                {
                    $query->where('client_staff_id', '>', 0);
                }
            }
        }

//        dd($post_data['is_api_pushed']);
        // 是否api推送
        if(isset($post_data['is_api_pushed']))
        {
            if(!in_array($post_data['is_api_pushed'],[-1,'-1']))
            {
                $query->where('is_api_pushed', $post_data['is_api_pushed']);
            }
        }


        // 区域
        if(isset($post_data['city']))
        {
            if(count($post_data['city']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_city',$post_data['city']);
                });
            }
        }
        // 区域
        if(isset($post_data['district']))
        {
            if(count($post_data['district']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_district',$post_data['district']);
                });
            }
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }

    // 【交付】获取 GET
    public function o1__delivery__item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Delivery::withTrashed()
            ->with([
                'order_er'=>function($query) { $query->select('*'); }
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【交付】获取数据
    public function v1_operate_for_delivery_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Delivery::with([
            'client_contact_er'=>function($query) { $query->select(['id','name']); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }



    // 【工单】【操作记录】返回-列表-数据
    public function o1__delivery__item_operation_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client__Delivery__Operation_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['delivery_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


        $total = $query->count();

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
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
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }
    // 【工单-管理】【操作记录】返回-列表-数据
    public function v1_operate_for_delivery_item_follow_record_datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client_Follow_Record::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->where(['delivery_id'=>$id]);
//            ->where(['record_object'=>21,'operate_object'=>61,'item_id'=>$id]);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


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
            $list[$k]->encode_id = encode($v->id);

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【交付】编辑-质量评价
    public function o1__delivery__item__quality_evaluate__save($post_data)
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

        // 判断参数是否合法
        $operate = $post_data["operate"];
        if($operate != 'delivery--item--quality-evaluate') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $order_quality = $post_data["order_quality"];
        if(!in_array($order_quality,config('info.order_quality'))) return response_error([],"质量结果非法！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_category,[0,1,9,11,19,99])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $item = DK_Common__Delivery::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");
//        dd($me->client_id);

        if($item->client_id != $me->client_id) return response_error([],"该【工单】不是你的，你不能操作！");
//        if(in_array($me->staff_category,[99]) && $item->client_staff_id != $me->id) return response_error([],"该【工单】不是你的啊，你不能操作！");



        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $before = $item->order_quality;




        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '质量评价';
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '时间';
            $record_row['field'] = 'quality_evaluate_time';
            $record_row['before'] = '';
            $record_row['after'] = $datetime;
            $record_content[] = $record_row;
        }
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '结果';
            $record_row['field'] = 'quality_evaluate_result';

            $record_row['before'] = $item->order_quality;;
            $record_row['after'] = $order_quality;

            $record_content[] = $record_row;
        }


        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $id;
        $record_data["order_id"] = $item->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 91;
        $record_data["content"] = json_encode($record_content);

        $record_data["before"] = $before;
        $record_data["after"] = $order_quality;

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->order_quality = $order_quality;
            $bool = $item->save();
            if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
            else
            {
                $record = new DK_Client__Delivery__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Client__Delivery__Operation_Record--record--fail");
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

    // 【交付】编辑-客户信息
    public function o1__delivery__item__customer_update__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'is_wx.required' => '请选择是否加微信！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'is_wx' => 'required',
            'is_wx' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Common__Delivery::with([
            'client_contact_er'=>function($query) { $query->select(['id','name']); }
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $datetime = date('Y-m-d H:i:s');


        $follow_update = [];

        $is_wx = $post_data["is_wx"];
        if($is_wx != $mine->is_wx)
        {
            $update['field'] = 'is_wx';
            $update['before'] = $mine->is_wx;
            $update['after'] = $is_wx;
            $follow_update[] = $update;

            $mine->is_wx = $is_wx;
        }

        $customer_remark = $post_data["customer_remark"];
        if($customer_remark != $mine->customer_remark)
        {
            $update['field'] = 'customer_remark';
            $update['before'] = $mine->customer_remark;
            $update['after'] = $customer_remark;
            $follow_update[] = $update;

            $mine->customer_remark = $customer_remark;
        }

        $client_contact_id = $post_data["client_contact_id"];
        $contact = DK_Client_Contact::select('id','name')->find($client_contact_id);
        if(!$contact) return response_error([],"【联系渠道】不存在，请刷新页面重试！");
        if($client_contact_id != $mine->client_contact_id)
        {
            $update['field'] = 'client_contact_id';
            if($mine->client_contact_er)
            {
                $update['before'] = $mine->client_contact_er->name;
            }
            else
            {
                $update['before'] = '';
            }
            $update['before_id'] = $mine->client_contact_id;
            $update['after'] = $contact->name;
            $update['after_id'] = $client_contact_id;
            $follow_update[] = $update;

            $mine->client_contact_id = $client_contact_id;
        }


        $follow = new DK_Client_Follow_Record;

        $follow_data["follow_category"] = 1;
        $follow_data["follow_type"] = 11;
        $follow_data["client_id"] = $me->client_id;
        $follow_data["delivery_id"] = $operate_id;
        $follow_data["creator_id"] = $me->id;
        $follow_data["custom_text_1"] = json_encode($follow_update);
        $follow_data["follow_datetime"] = $datetime;
        $follow_data["follow_date"] = $datetime;


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $follow->fill($follow_data)->save();
            if($bool)
            {
//                $mine->timestamps = false;
                $mine->last_operation_datetime = $datetime;
                $mine->last_operation_date = $datetime;
                $bool_d = $mine->save();
            }
            else throw new Exception("DK_Client_Follow_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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
    // 【交付】编辑-回访信息
    public function o1__delivery__item__callback_update__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'follow-datetime.required' => '请输入跟进时间！',
//            'is_come.required' => '请选择上门状态！',
            'callback-datetime.required' => '请选择回访时间！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'callback-datetime' => 'required',
//            'is_come' => 'required',
            'callback-datetime' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Common__Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '回访';

            $record_content[] = $record_row;
        }
        // 回访时间
        $callback_datetime = $post_data['callback-datetime'];
        if(!empty($callback_datetime))
        {
            $record_row = [];
            $record_row['title'] = '回访时间';
            $record_row['field'] = 'callback_description';
            $record_row['before'] = '';
            $record_row['after'] = $callback_datetime;

            $record_content[] = $record_row;

            $mine->callback_datetime = $callback_datetime;
            $mine->callback_date = $callback_datetime;
        }
        // 回访详情
        $callback_description = $post_data['callback-description'];
        if(!empty($callback_description))
        {
            $record_row = [];
            $record_row['title'] = '回访详情';
            $record_row['field'] = 'callback_description';
            $record_row['before'] = '';
            $record_row['after'] = $callback_description;

            $record_content[] = $record_row;
        }

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $operate_id;
        $record_data["order_id"] = $mine->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 96;
//        $record_data["follow_datetime"] = $post_data['follow-datetime'];
//        $record_data["follow_date"] = $post_data['follow-datetime'];
        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $mine->timestamps = false;
            $mine->last_operation_datetime = $datetime;
            $mine->last_operation_date = $datetime;
            $bool_d = $mine->save();
            if($bool_d)
            {
                $record = new DK_Client__Delivery__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Client__Delivery__Operation_Record--record--fail");
            }
            else throw new Exception("DK_Common__Delivery--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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
    // 【交付】编辑-上门信息
    public function o1__delivery__item__come_update__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'follow-datetime.required' => '请输入跟进时间！',
            'is_come.required' => '请选择上门状态！',
//            'come-datetime.required' => '请选择上门时间！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'follow-datetime' => 'required',
            'is_come' => 'required',
//            'come-datetime' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $mine = DK_Common__Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '编辑上门状态';

            $record_content[] = $record_row;
        }
        // 跟进时间
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '跟进时间';
            $record_row['field'] = 'follow_time';
            $record_row['before'] = '';
            $record_row['after'] = $post_data['follow-datetime'];

            $record_content[] = $record_row;
        }
        // 上门状态
        $is_come = $post_data["is_come"];
//        if($is_come != $mine->is_come)
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '上门状态';
            $record_row['field'] = 'is_come';
            $record_row['code'] = $is_come;

            if($mine->is_come == 0) $record_row['before'] = '否';
            else if($mine->is_come == 9) $record_row['before'] = '预约上门';
            else if($mine->is_come == 11) $record_row['before'] = '已上门';
            else $record_row['before'] = $mine->is_come;

            if($is_come == 0) $record_row['after'] = '否';
            else if($is_come == 9) $record_row['after'] = '预约上门';
            else if($is_come == 11) $record_row['after'] = '已上门';
            else $record_row['after'] = $is_come;

            $record_content[] = $record_row;

            $mine->is_come = $is_come;
        }
        // 上门时间
        $come_datetime = $post_data['come-datetime'];
        if(!empty($come_datetime))
        {
            $record_row = [];
            $record_row['title'] = '上门时间';
            $record_row['field'] = 'come_datetime';
            $record_row['before'] = '';
            $record_row['after'] = $come_datetime;

            $record_content[] = $record_row;

            $mine->come_date = $come_datetime;
            $mine->come_datetime = $come_datetime;
        }
        // 上门备注
        $come_description = $post_data['come-description'];
        if(!empty($come_description))
        {
            $record_row = [];
            $record_row['title'] = '上门详情';
            $record_row['field'] = 'come_description';
            $record_row['before'] = '';
            $record_row['after'] = $come_description;

            $record_content[] = $record_row;
        }

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $operate_id;
        $record_data["order_id"] = $mine->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 98;
        $record_data["follow_datetime"] = $post_data['follow-datetime'];
        $record_data["follow_date"] = $post_data['follow-datetime'];
        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->last_operation_datetime = $datetime;
            $mine->last_operation_date = $datetime;
            $bool_d = $mine->save();
            if($bool_d)
            {
                $record = new DK_Client__Delivery__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Client__Delivery__Operation_Record--record--fail");
            }
            else throw new Exception("DK_Common__Delivery--update--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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
    // 【交付】添加-跟进
    public function o1__delivery__item__follow_create__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'follow-datetime.required' => '请输入跟进时间！',
//            'name.required' => '请输入联系渠道名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'follow-datetime' => 'required',
//            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $mine = DK_Common__Delivery::with([])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '跟进记录';
            $record_content[] = $record_row;
        }
        // 跟进时间
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '跟进时间';
            $record_row['field'] = 'follow_time';
            $record_row['before'] = '';
            $record_row['after'] = $post_data['follow-datetime'];
            $record_content[] = $record_row;
        }
        // 跟进说明
        if(!empty($post_data['follow-description']))
        {
            $record_row = [];
            $record_row['title'] = '跟进详情';
            $record_row['field'] = 'follow_description';

            $record_row['before'] = '';
            $record_row['after'] = $post_data['follow-description'];

            $record_content[] = $record_row;
        }

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $operate_id;
        $record_data["order_id"] = $mine->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 91;
        $record_data["operate_type"] = 1;
        $record_data["follow_datetime"] = $post_data['follow-datetime'];
        $record_data["follow_date"] = $post_data['follow-datetime'];
        $record_data["content"] = json_encode($record_content);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $mine->timestamps = false;
            $mine->follow_latest_description = $post_data['follow-description'];
            $mine->follow_datetime = $post_data['follow-datetime'];
            $mine->follow_date = $post_data['follow-datetime'];
            $mine->last_operation_datetime = $datetime;
            $mine->last_operation_date = $datetime;
            $bool_d = $mine->save();
            if(!$bool_d) throw new Exception("DK_Common__Delivery--update--fail");
            if($bool_d)
            {
                $record = new DK_Client__Delivery__Operation_Record;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("DK_Client__Delivery__Operation_Record--record--fail");
            }
            else throw new Exception("DK_Common__Delivery--update--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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
    // 【交付】添加-成交
    public function o1__delivery__item__trade_create__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'transaction-datetime.required' => '请输入成交时间！',
            'transaction-count.required' => '请输入成交数量！',
            'transaction-amount.required' => '请输入成交金额！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'transaction-datetime' => 'required',
            'transaction-count' => 'required',
            'transaction-amount' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19,81,84,88])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        $mine = DK_Common__Delivery::with([
        ])->withTrashed()->find($operate_id);
        if(!$mine) return response_error([],"不存在警告，请刷新页面重试！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        $trade_data = [];
        $trade_data["trade_category"] = 1;
        $trade_data["trade_type"] = 1;
        $trade_data["client_id"] = $me->client_id;
        $trade_data["delivery_id"] = $operate_id;
        $trade_data["order_id"] = $mine->order_id;
        $trade_data["creator_id"] = $me->id;

        $record_content = [];

        if(true)
        {
            $record_row = [];
            $record_row['title'] = '操作';
            $record_row['field'] = 'item_operation';
            $record_row['before'] = '';
            $record_row['after'] = '添加成交记录';

            $record_content[] = $record_row;
        }


        // 交易名目
        $transaction_title = $post_data['transaction-title'];
        $trade_data["title"] = $transaction_title;
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '交易名目';
            $record_row['field'] = 'transaction_title';
            $record_row['before'] = '';
            $record_row['after'] = $transaction_title;

            $record_content[] = $record_row;
        }

        // 交易时间
        $transaction_datetime = $post_data['transaction-datetime'];
        $trade_data["transaction_date"] = $transaction_datetime;
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '成交时间';
            $record_row['field'] = 'transaction_datetime';
            $record_row['before'] = '';
            $record_row['after'] = $transaction_datetime;

            $record_content[] = $record_row;
        }

        // 交易数量
        $transaction_count = $post_data['transaction-count'];
        $trade_data["transaction_count"] = $transaction_count;
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '成交数量';
            $record_row['field'] = 'transaction_count';
            $record_row['before'] = '';
            $record_row['after'] = $transaction_count;

            $record_content[] = $record_row;
        }

        // 交易金额
        $transaction_amount = $post_data['transaction-amount'];
        $trade_data["transaction_amount"] = $transaction_amount;
        if(true)
        {
            $record_row = [];
            $record_row['title'] = '成交金额';
            $record_row['field'] = 'transaction_amount';
            $record_row['before'] = '';
            $record_row['after'] = $transaction_amount;

            $record_content[] = $record_row;
        }

        // 交易方式
        $transaction_payment_type = $post_data['transaction-payment-type'];
        $trade_data["transaction_payment_type"] = $transaction_payment_type;
        if(!empty($transaction_payment_type))
        {
            $record_row = [];
            $record_row['title'] = '交易方式';
            $record_row['field'] = 'transaction_payment_type';

            $record_row['before'] = '';
            $record_row['after'] = $transaction_payment_type;

            $record_content[] = $record_row;
        }

        // 付款账号
        $transaction_payer_account = isset($post_data['transaction-payer-account']) ? $post_data['transaction-payer-account'] : NULL;
        $trade_data["transaction_payer_account"] = $transaction_payer_account;
        if(!empty($transaction_payer_account))
        {
            $record_row = [];
            $record_row['title'] = '付款账号';
            $record_row['field'] = 'transaction_payer_account';

            $record_row['before'] = '';
            $record_row['after'] = $transaction_payer_account;

            $record_content[] = $record_row;
        }

        // 收款账号
        $transaction_payee_account = isset($post_data['transaction-payee-account']) ? $post_data['transaction-payee-account'] : NULL;
        $trade_data["transaction_payee_account"] = $transaction_payee_account;
        if(!empty($transaction_payee_account))
        {
            $record_row = [];
            $record_row['title'] = '收款账号';
            $record_row['field'] = 'transaction_payee_account';

            $record_row['before'] = '';
            $record_row['after'] = $transaction_payee_account;

            $record_content[] = $record_row;
        }

        // 交易单号
        $transaction_order_number = isset($post_data['transaction-order-number']) ? $post_data['transaction-order-number'] : NULL;
        $trade_data["transaction_order_number"] = $transaction_order_number;
        if(!empty($transaction_order_number))
        {
            $record_row = [];
            $record_row['title'] = '交易单号';
            $record_row['field'] = 'transaction_order_number';

            $record_row['before'] = '';
            $record_row['after'] = $transaction_order_number;

            $record_content[] = $record_row;
        }

        // 交易说明
        $transaction_description = $post_data['transaction-description'];
        $trade_data["description"] = $transaction_description;
        if(!empty($transaction_description))
        {
            $record_row = [];
            $record_row['title'] = '交易说明';
            $record_row['field'] = 'transaction_description';

            $record_row['before'] = '';
            $record_row['after'] = $transaction_description;

            $record_content[] = $record_row;
        }

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 1;
        $record_data["record_category"] = 1;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["delivery_id"] = $operate_id;
        $record_data["order_id"] = $mine->order_id;
        $record_data["operate_object"] = 1;
        $record_data["operate_category"] = 81;
        $record_data["operate_type"] = 1;
//        $record_data["follow_datetime"] = $post_data['follow-datetime'];
//        $record_data["follow_date"] = $post_data['follow-datetime'];
        $record_data["content"] = json_encode($record_content);

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $trade = new DK_Client__Trade_Record;
            $bool_t = $trade->fill($trade_data)->save();
            if($bool_t)
            {
                $mine = DK_Common__Delivery::lockForUpdate()->withTrashed()->find($operate_id);
//
////                $mine->timestamps = false;
                $mine->transaction_num += 1;
                $mine->transaction_count += $post_data['transaction-count'];
                $mine->transaction_amount += $post_data['transaction-amount'];
                $mine->transaction_date = $post_data['transaction-datetime'];
                $mine->transaction_datetime = $post_data['transaction-datetime'];

//                $mine->last_operation_date = $datetime;
//                $mine->last_operation_datetime = $datetime;
                $bool_d = $mine->save();
                if(!$bool_d) throw new Exception("DK_Common__Delivery--update--fail");


                $record_data['trade_id'] = $trade->id;
                $record = new DK_Client__Delivery__Operation_Record;

                $bool_r = $record->fill($record_data)->save();
                if($bool_r)
                {
                    $trade->operation_id = $record->id;
                    $bool_t_2 = $trade->save();
                    if(!$bool_t_2) throw new Exception("DK_Client__Trade_Record--update--fail");
                }
                else throw new Exception("DK_Client__Delivery__Operation_Record--record--fail");

            }
            else throw new Exception("DK_Client__Trade_Record--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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









    // 【交付】批量-分配状态
    public function o1__delivery__bulk__assign_status($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'assign_status.required' => 'assign_status.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'assign_status' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery--bulk--assign-status') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[0,1,9])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
        $assign_status = $post_data["assign_status"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");


        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['assign_status'] = $assign_status;
//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $mine = DK_Common__Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");


                $before = $mine->assign_status;

                $mine->assign_status = $assign_status;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
                else
                {
                    $record = new DK_Client__Delivery__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 1;
                    $record_data["record_category"] = 1;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["delivery_id"] = $id;
                    $record_data["order_id"] = $mine->order_id;
                    $record_data["operate_object"] = 1;
                    $record_data["operate_category"] = 99;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "assign_status";

                    $record_data["before"] = $before;
                    $record_data["after"] = $assign_status;

                    $record_content = [];

                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '操作';
                        $record_row['field'] = 'item_operation';
                        $record_row['before'] = '';
                        $record_row['after'] = '更改分配状态';

                        $record_content[] = $record_row;
                    }
                    // 跟进时间
                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '时间';
                        $record_row['field'] = 'operation_time';
                        $record_row['before'] = '';
                        $record_row['after'] = $datetime;

                        $record_content[] = $record_row;
                    }
                    // 分配状态
                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '分配状态';
                        $record_row['field'] = 'assign_status';
                        $record_row['code'] = $assign_status;

                        if($before == 0) $record_row['before'] = '待分配';
                        else if($before == 1) $record_row['before'] = '已分配';
                        else $record_row['before'] = $assign_status;

                        if($assign_status == 0) $record_row['after'] = '待分配';
                        else if($assign_status == 1) $record_row['after'] = '已分配';
                        else $record_row['after'] = $assign_status;

                        $record_content[] = $record_row;
                    }

                    $record_data["content"] = json_encode($record_content);

                    $bool_record = $record->fill($record_data)->save();
                    if(!$bool_record) throw new Exception("DK_Client__Delivery__Operation_Record--insert--fail");
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
    // 【交付】批量-指派员工
    public function o1__delivery__bulk__assign_staff($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
            'staff_id.required' => 'staff_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
            'staff_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'delivery--bulk--assign-staff') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[0,1,9,31,41,51,61,71])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
        $client_staff_id = (int)$post_data["staff_id"];
        if($client_staff_id <= 0) return response_error([],"请选择员工！");

        $client_staff_er = DK_Client__Staff::where('active',1)->find($client_staff_id);
        if($client_staff_er)
        {
            if($client_staff_er->item_status != 1) return response_error([],"该员工已被禁用！");
            if($client_staff_er->owner_status__for__team != 1) return response_error([],"该员工所在团队已被禁用！");
            if($client_staff_er->owner_status__for__team_group != 1) return response_error([],"该员工所在小组已被禁用！");
        }
        else return response_error([],"选择员工不存在！");



        $time = time();
        $date = date("Y-m-d");
        $datetime = date('Y-m-d H:i:s');


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $delivered_para['client_staff_id'] = $client_staff_id;
//            $bool = DK_Order::whereIn('id',$ids_array)->update($delivered_para);
//            if(!$bool) throw new Exception("item--update--fail");
//            else
//            {
//            }

            foreach($ids_array as $key => $id)
            {
                $mine = DK_Common__Delivery::withTrashed()->find($id);
                if(!$mine) throw new Exception("该【交付】不存在，刷新页面重试！");
                if($mine->client_id != $me->client_id) throw new Exception("归属错误，刷新页面重试！");

                $before = $mine->client_staff_id;

                $mine->client_staff_id = $client_staff_id;
                $mine->assign_status = 1;
                $bool = $mine->save();
                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
                else
                {
                    $record = new DK_Client__Delivery__Operation_Record;

                    $record_data["ip"] = Get_IP();
                    $record_data["record_object"] = 1;
                    $record_data["record_category"] = 1;
                    $record_data["record_type"] = 1;
                    $record_data["creator_id"] = $me->id;
                    $record_data["delivery_id"] = $id;
                    $record_data["order_id"] = $mine->order_id;
                    $record_data["operate_object"] = 1;
                    $record_data["operate_category"] = 98;
                    $record_data["operate_type"] = 1;
                    $record_data["column_name"] = "client_staff_id";

                    $record_data["before"] = $before;
                    $record_data["after"] = $client_staff_id;
                    $record_content = [];

                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '操作';
                        $record_row['field'] = 'item_operation';
                        $record_row['before'] = '';
                        $record_row['after'] = '指派员工';

                        $record_content[] = $record_row;
                    }
                    // 跟进时间
                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '时间';
                        $record_row['field'] = 'operation_time';
                        $record_row['before'] = '';
                        $record_row['after'] = $datetime;

                        $record_content[] = $record_row;
                    }
                    // 分配状态
                    if(true)
                    {
                        $record_row = [];
                        $record_row['title'] = '员工';
                        $record_row['field'] = 'assign_status';
                        $record_row['code'] = $before;

                        if($before == 0) $record_row['before'] = '';
                        else
                        {
                            $before_staff_er = DK_Client__Staff::withTrashed()->find($before);
                            if($before_staff_er) $record_row['before'] = $before_staff_er->name.'('.$before.')';
                            else $record_row['before'] = '('.$before.')';
                        }

                        $record_row['after'] = $client_staff_er->name.'('.$client_staff_id.')';

                        $record_content[] = $record_row;
                    }

                    $record_data["content"] = json_encode($record_content);

                    $bool_record = $record->fill($record_data)->save();
                    if(!$bool_record) throw new Exception("DK_Client__Delivery__Operation_Record--insert--fail");
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
    // 【交付】批量-API-推送
    public function o1__delivery__bulk__api_push($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'ids.required' => 'ids.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'ids' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'bulk-api-push') return response_error([],"参数[operate]有误！");
        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);


        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");


        $url = "https://qw-openapi-tx.dustess.com/auth/v1/access_token/token";

        $curl_data['ClientID'] = env('API_SCRM_ClientID');
        $curl_data['ClientSecret'] = env('API_SCRM_ClientSecret');
        $curl_data = json_encode($curl_data);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data); // post的变量
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            return response_fail([],'token请求失败');
        }
        else
        {
            $result = json_decode($result);
            if($result->success)
            {
                $token = $result->data->accessToken;
            }
        }
        curl_close($ch);


        if(!empty($token))
        {
            $delivery_list = DK_Common__Delivery::withTrashed()
                ->with('order_er')
                ->whereIn('id',$ids_array)->get();
//        dd($delivery_list->toArray());

            $customer_list = [];
            foreach($delivery_list as $key => $item)
            {
                if($item->is_api_pushed == 0)
                {
                    $customer = [];

                    $customer['source'] = "2r4";

                    $customer['pool'] = env('API_SCRM_Pool');
                    $customer['remark'] = $item->order_er->client_name;
                    $customer['prov_city'] = $item->order_er->location_city.'-'.$item->order_er->location_district;


                    $mobile['type'] = "mobile";
                    $mobile['display'] = "手机号";
                    $mobile['tel'] = $item->order_er->client_phone;
                    $customer['mobiles'][] = $mobile;

                    if(!empty($item->order_er->wx_id))
                    {
                        $wx['type'] = "wx_id";
                        $wx['display'] = "微信号";
                        $wx['tel'] = $item->order_er->wx_id;
                        $customer['mobiles'][] = $wx;
                    }

                    $customer['description'] = $item->order_er->description;

                    // 自定义字段
                    $custom_fields = [];

                    $delivery_time['id'] = 'delivery_time';
                    $delivery_time['type'] = 'text';
                    $delivery_time['string_value'] = $item->created_at->format('Y-m-d');
                    $custom_fields[] = $delivery_time;

                    $teeth_count['id'] = 'teeth_count';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $teeth_count['id'] = 'field1';
                    $teeth_count['type'] = 'text';
                    $teeth_count['string_value'] = $item->order_er->teeth_count;
                    $custom_fields[] = $teeth_count;

                    $customer['custom_fields'] = $custom_fields;

                    $customer['description'] = $item->order_er->description;

                    $customer_list[] = $customer;
                }
            }


            if(count($customer_list) > 0)
            {
                $api_push_data['customer_list'] = $customer_list;
                $api_push_data_json = json_encode($api_push_data);

                $push_url = "https://qw-openapi-tx.dustess.com/customer/v1/batchAddCustomer?accessToken=".$token;

                $push_ch = curl_init();
                curl_setopt($push_ch, CURLOPT_URL, $push_url);
                curl_setopt($push_ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Accept: application/json"));
                curl_setopt($push_ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($push_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($push_ch, CURLOPT_POST, true); // post数据
                curl_setopt($push_ch, CURLOPT_POSTFIELDS, $api_push_data_json); // post的变量
                $push_result = curl_exec($push_ch);
                if(curl_errno($push_ch))
                {
                    return response_fail([],'api推送请求失败！');
                }
                else
                {
                    $push_result_decode = json_decode($push_result);
                    if($push_result_decode->success)
                    {
                    }
                    else
                    {
                        return response_fail(['data'=>$push_result],'推送数据失败！');
                    }
                }
                curl_close($push_ch);
            }
            else return response_fail(['count'=>count($customer_list)],'工单已推送过，本次未推送数据！');

        }
        else return response_fail([],'token不存在！');


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $delivered_update['is_api_pushed'] = 1;
            $delivered_update['is_api_pusher_id'] = $me->id;
            $delivered_update['is_api_pushed_at'] = time();
            $bool = DK_Common__Delivery::withTrashed()->whereIn('id',$ids_array)
                ->update($delivered_update);
            if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
            else
            {
                $record = new DK_Client__Delivery__Operation_Record;

                $record_data["ip"] = Get_IP();
                $record_data["record_object"] = 21;
                $record_data["record_category"] = 11;
                $record_data["record_type"] = 1;
                $record_data["creator_id"] = $me->id;
                $record_data["operate_object"] = 91;
                $record_data["operate_category"] = 111;
                $record_data["operate_type"] = 1;
                $record_data["column_name"] = "ids";

                $record_data["title"] = $ids;
                $record_data["content"] = $push_result;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }

//            foreach($ids_array as $key => $id)
//            {
//                $item = DK_Common__Delivery::withTrashed()->find($id);
//                if(!$item) return response_error([],"该【交付】不存在，刷新页面重试！");
//
//
////                $before = $item->client_staff_id;
//
//                $item->is_api_pushed = 1;
//                $bool = $item->save();
//                if(!$bool) throw new Exception("item--update--fail");
//                else
//                {
////                    $record = new DK_Client__Delivery__Operation_Record;
////
////                    $record_data["ip"] = Get_IP();
////                    $record_data["record_object"] = 21;
////                    $record_data["record_category"] = 11;
////                    $record_data["record_type"] = 1;
////                    $record_data["creator_id"] = $me->id;
////                    $record_data["order_id"] = $id;
////                    $record_data["operate_object"] = 91;
////                    $record_data["operate_category"] = 99;
////                    $record_data["operate_type"] = 1;
////                    $record_data["column_name"] = "client_staff_id";
////
////                    $record_data["before"] = $before;
////                    $record_data["after"] = $client_staff_id;
////
////                    $bool_1 = $record->fill($record_data)->save();
////                    if(!$bool_1) throw new Exception("insert--record--fail");
//                }
//
//            }


            DB::commit();
            return response_success(['count'=>count($customer_list)]);
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






    // 【交付】交付日报
    public function o1__statistic__delivery_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Common__Delivery::select('delivered_date')
            ->addSelect(DB::raw("
                    delivered_date as date_day,
                    DAY(delivered_date) as day,
                    count(*) as delivery_count
                "))
            ->where('client_id',$me->client_id)
            ->groupBy('delivered_date');


        // 客户
//        if(!empty($post_data['client']) && !in_array($post_data['client'],[-1,0]))
//        {
//            $query->where('client_id', $post_data['client']);
//        }


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);


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
        else $query->orderBy("delivered_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
        dd($list->toArray());


        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }






    // 【交付】交付列表
    public function get_datatable_delivery_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->with([
                'client_staff_er',
                'order_er'
            ])
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            })
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('client_staff_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            });



        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['order_id'])) $query->where('order_id', $post_data['order_id']);
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");

        if(!empty($post_data['client_name'])) $query->where('client_name', $post_data['client_name']);
        if(!empty($post_data['client_phone'])) $query->where('client_phone', $post_data['client_phone']);

        if(!empty($post_data['assign'])) $query->where('delivered_date', $post_data['assign']);

        if(!empty($post_data['quality'])) $query->where('order_quality', $post_data['quality']);



        // 客户
        if(isset($post_data['client']))
        {
            if(!in_array($post_data['client'],[-1,'-1']))
            {
                $query->where('client_id', $post_data['client']);
            }
        }


        //  员工
        if(isset($post_data['staff']))
        {
            if(!in_array($post_data['staff'],[-1,0,'-1','0']))
            {
                $query->where('client_staff_id', $post_data['staff']);
            }
        }



        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('delivered_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('delivered_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('delivered_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }


        // 患者类型
        if(isset($post_data['client_type']))
        {
            if(!in_array($post_data['client_type'],[-1,'-1']))
            {
                $query->where('client_type', $post_data['client_type']);
            }
        }

        // 导出状态
        if(isset($post_data['exported_status']))
        {
            if(!in_array($post_data['exported_status'],[-1,'-1']))
            {
                $query->where('exported_status', $post_data['exported_status']);
            }
        }

        // 分配状态
        if(isset($post_data['assign_status']))
        {
//            if(!in_array($post_data['assign_status'],[-1,'-1']))
//            {
//                $query->where('assign_status', $post_data['assign_status']);
//            }
            if(!in_array($post_data['assign_status'],[-1,'-1']))
            {
//                $query->where('assign_status', $post_data['assign_status']);
                if($post_data['assign_status'] == 0)
                {
                    $query->where('assign_status', 0);
                    $query->where('client_staff_id', 0);
                }
                else if($post_data['assign_status'] == 1)
                {
                    $query->where(function ($query) {
                        $query->where('assign_status', 1)->orWhere('client_staff_id', '>', 0);
                    });
                }
            }
        }

//        dd($post_data['is_api_pushed']);
        // 是否api推送
        if(isset($post_data['is_api_pushed']))
        {
            if(!in_array($post_data['is_api_pushed'],[-1,'-1']))
            {
                $query->where('is_api_pushed', $post_data['is_api_pushed']);
            }
        }


        // 区域
        if(isset($post_data['city']))
        {
            if(count($post_data['city']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_city',$post_data['city']);
                });
            }
        }
        // 区域
        if(isset($post_data['district']))
        {
            if(count($post_data['district']) > 0)
            {
                $query->whereHas('order_er', function($query) use($post_data) {
                    $query->whereIn('location_district',$post_data['district']);
                });
            }
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
//            $list[$k]->encode_id = encode($v->id);
//            $list[$k]->content_decode = json_decode($v->content);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }

    // 【交付】交付日报
    public function get_datatable_delivery_daily($post_data)
    {
        $this->get_me();
        $me = $this->me;


        // 交付统计
        $query = DK_Common__Delivery::select('company_id','channel_id','business_id','delivered_date')
            ->where('client_id',$me->client_id)
            ->addSelect(DB::raw("
                    delivered_date as date_day,
                    DAY(delivered_date) as day,
                    count(*) as delivery_count
                "))
            ->groupBy('delivered_date')
            ->when($me->company_category == 1, function ($query) use ($me) {
                return $query->where('company_id', $me->id);
            })
            ->when($me->company_category == 11, function ($query) use ($me) {
                return $query->where('channel_id', $me->id);
            })
            ->when($me->company_category == 21, function ($query) use ($me) {
                return $query->where('business_id', $me->id);
            });


        // 客户
        if(!empty($post_data['client']) && !in_array($post_data['client'],[-1,0]))
        {
            $query->where('client_id', $post_data['client']);
        }


        $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
        $the_month_timestamp = strtotime($the_month);

        $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
        $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
        $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
        $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
        $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
        $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

        $query->whereBetween('delivered_date',[$the_month_start_date,$the_month_ended_date]);


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
        else $query->orderBy("delivered_date", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());


        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }


    // 【交付】导出
    public function operate_delivery_export_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Common__Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'client_er'=>function($query) { $query->select('id','username'); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

//            $cellData[$k]['client_er_name'] = $v['client_er']['username'];


            if($v['order_er']['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['order_er']['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['order_er']['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['description'] = $v['order_er']['description'];

//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
//            'client_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client__Delivery__Operation_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>16,
                    'H'=>10,
                    'I'=>10,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');





    }


















    // 【工单-管理】字段修改
    public function v1_operate_for_user_field_set($post_data)
    {
        $messages = [
            'operate_category.required' => 'operate_category.required.',
            'column_key.required' => 'column_key.required.',
            'column_value.required' => 'column_value.required.',
        ];
        $v = Validator::make($post_data, [
            'operate_category' => 'required',
            'column_key' => 'required',
            'column_value' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate_category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
//        $id = $post_data["item-id"];
//        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

//        $operate_type = $post_data["operate-type"];

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
//        if($item->owner_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");



        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($column_key == 'is_take_order')
            {
                if(($column_value == 1) && ($me->is_take_order_date != date('Y-m-d')))
                {
                    $me->is_take_order_date = date('Y-m-d');
                    $me->is_take_order_today = 0;
                }
                $me->is_take_order_datetime = date('Y-m-d H:i:s');
            }
            $me->$column_key = $column_value;
            $bool = $me->save();
            if(!$bool) throw new Exception("DK_Client_User--update--fail");
            else
            {
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

    // 【工单-管理】字段修改
    public function v1_operate_for_parent_client_field_set($post_data)
    {
        $messages = [
            'operate_category.required' => 'operate_category.required.',
            'column_key.required' => 'column_key.required.',
            'column_value.required' => 'column_value.required.',
        ];
        $v = Validator::make($post_data, [
            'operate_category' => 'required',
            'column_key' => 'required',
            'column_value' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate_category = $post_data["operate_category"];
        if($operate_category != 'field-set') return response_error([],"参数[operate]有误！");
//        $id = $post_data["item-id"];
//        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

//        $operate_type = $post_data["operate-type"];

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");


        $parent_client = DK_Client::find($me->client_id);
        if(!$parent_client) return response_error([],"所属客户不存在！");

        $column_key = $post_data["column_key"];
        $column_value = $post_data["column_value"];



        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $parent_client->$column_key = $column_value;
            $bool = $parent_client->save();
            if(!$bool) throw new Exception("DK_Client--update--fail");
            else
            {
            }

            DB::commit();

            if($column_key == 'is_automatic_dispatching' && $column_value == 1)
            {
                AutomaticDispatchingJob::dispatch($me->client_id);
            }

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


    // 【交付管理】自动-分配
    public function v1_operate_for_delivery_automatic_dispatching_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'ids.required' => 'ids.required.',
//            'staff_id.required' => 'staff_id.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'ids' => 'required',
//            'staff_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'automatic-dispatching-by-admin') return response_error([],"参数[operate]有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        // 判断操作参数是否合法
//        $client_staff_id = $post_data["staff_id"];
//        if(!in_array($operate_result,config('info.delivered_result'))) return response_error([],"交付结果参数有误！");

        $staff_list = DK_Client_User::select('id','client_id','is_take_order','is_take_order_date','is_take_order_datetime')
            ->where('client_id',$me->client_id)
            ->where('is_take_order',1)
            ->where('is_take_order_date',date('Y-m-d'))
            ->orderBy('is_take_order_datetime','asc')
            ->get();

        $delivery_list = DK_Common__Delivery::select('*')
            ->where('client_id',$me->client_id)
            ->where('client_staff_id',0)
            ->get();

        $staff_list = $staff_list->values(); // 重置索引确保从0开始
        $staffCount = $staff_list->count();
        if($staffCount == 0) return response_error([],"暂时没有接单员工！");
        $deliveryCount = $delivery_list->count();
        if($deliveryCount == 0) return response_error([],"暂时没有未分配工单！");

        $clientId = $me->client_id;

        // 使用原子锁避免并发冲突
        // 创建原子锁（设置最大等待时间和自动释放时间）
        $lock = Cache::lock("client:{$clientId}:assign_lock", 10); // 锁最多持有10秒
//        if (!$lock->get())
//        {
//            abort(423, '系统正在分配任务中，请稍后重试');
//        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            // 尝试获取锁，最多等待5秒
            $lock->block(5); // 这里会阻塞直到获取锁或超时

//            $staffIndex = 0;
//            foreach ($delivery_list as $delivery)
//            {
//                $staff = $staff_list[$staffIndex % $staffCount];
//                $delivery->client_staff_id = $staff->id;
//                $delivery->save(); // 触发模型事件（如有）
//                $staffIndex++;
//            }


            // 从缓存获取上次位置（不存在则初始化为0）
            $lastIndex = Cache::get("client:{$clientId}:last_staff_index", 0);
            $currentIndex = $lastIndex % $staffCount;
            $newIndex = $currentIndex;

            foreach ($delivery_list as $delivery) {
                $staff = $staff_list[$currentIndex];
                $delivery->client_staff_id = $staff->id;
                $delivery->save();

                // 计算下一个索引
                $currentIndex = ($currentIndex + 1) % $staffCount;
                $newIndex = $currentIndex; // 记录最后的下一个位置
            }

            // 将新位置写入缓存（有效期10小时）
            Cache::put(
                "client:{$clientId}:last_staff_index",
                $newIndex,
                now()->addHours(10)
            );


//            $num = 0;
//            foreach($delivery_list as $key => $delivery)
//            {
//                $bool = $mine->save();
//                if(!$bool) throw new Exception("DK_Common__Delivery--update--fail");
//                else
//                {
//                    $record = new DK_Client__Delivery__Operation_Record;
//
//                    $record_data["ip"] = Get_IP();
//                    $record_data["record_object"] = 21;
//                    $record_data["record_category"] = 11;
//                    $record_data["record_type"] = 1;
//                    $record_data["creator_id"] = $me->id;
//                    $record_data["order_id"] = $id;
//                    $record_data["operate_object"] = 91;
//                    $record_data["operate_category"] = 99;
//                    $record_data["operate_type"] = 1;
//                    $record_data["column_name"] = "client_staff_id";
//
//                    $record_data["before"] = $before;
//                    $record_data["after"] = $client_staff_id;
//
//                    $bool_1 = $record->fill($record_data)->save();
//                    if(!$bool_1) throw new Exception("DK_Client__Delivery__Operation_Record--insert--fail");
//                }
//            }


            DB::commit();
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
            $msg = '操作失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }
        finally
        {
//            $lock->release();
            optional($lock)->release(); // 确保无论如何都释放锁
        }

    }












    /*
     * 联系渠道管理
     */
    // 【交易-员工管理】返回-列表-数据
    public function v1_operate_for_trade_datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $query = DK_Client_Trade_Record::select('*')
            ->withTrashed()
            ->with([
                'delivery_er',
                'creator'=>function($query) { $query->select(['id','username','true_name']); },
                'deleter_er'=>function($query) { $query->select(['id','username','true_name']); },
                'authenticator_er'=>function($query) { $query->select(['id','username','true_name']); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
            ])
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                $staff_list = DK_Client_User::select('id')->where('department_id',$me->department_id)->get()->pluck('id')->toArray();
                return $query->whereIn('creator_id', $staff_list);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('creator_id', $me->id);
            });


        if(!empty($post_data['username'])) $query->where('username', 'like', "%{$post_data['username']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");


        // 类型 [|]
        if(!empty($post_data['trade_type']))
        {
            if(!in_array($post_data['trade_type'],[-1,0,'-1','0']))
            {
                $query->where('trade_type', $post_data['trade_type']);
            }
        }
        // 是否确认 [|]
        if(!empty($post_data['is_confirmed']))
        {
            if(!in_array($post_data['is_confirmed'],[-1,'-1']))
            {
                $query->where('is_confirmed', $post_data['is_confirmed']);
            }
        }




        $time_type  = isset($post_data['time_type']) ? $post_data['time_type']  : '';
        if($time_type == 'date')
        {
            $the_day  = isset($post_data['time_date']) ? $post_data['time_date']  : date('Y-m-d');

            $query->whereDate('transaction_date',$the_day);
        }
        else if($time_type == 'month')
        {
            $the_month  = isset($post_data['time_month']) ? $post_data['time_month']  : date('Y-m');
            $the_month_timestamp = strtotime($the_month);

            $the_month_start_date = date('Y-m-01',$the_month_timestamp); // 指定月份-开始日期
            $the_month_ended_date = date('Y-m-t',$the_month_timestamp); // 指定月份-结束日期
            $the_month_start_datetime = date('Y-m-01 00:00:00',$the_month_timestamp); // 本月开始时间
            $the_month_ended_datetime = date('Y-m-t 23:59:59',$the_month_timestamp); // 本月结束时间
            $the_month_start_timestamp = strtotime($the_month_start_datetime); // 指定月份-开始时间戳
            $the_month_ended_timestamp = strtotime($the_month_ended_datetime); // 指定月份-结束时间戳

            $query->whereBetween('transaction_date',[$the_month_start_date,$the_month_ended_date]);
        }
        else if($time_type == 'period')
        {
            if(!empty($post_data['date_start'])) $query->whereDate('transaction_date', '>=', $post_data['date_start']);
            if(!empty($post_data['date_ended'])) $query->whereDate('transaction_date', '<=', $post_data['date_ended']);
        }
        else
        {
        }



        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 10;

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach($list as $k => $v)
        {
        }

        return datatable_response($list, $draw, $total);
    }
    // 【交易-管理】保存数据
    public function v1_operate_for_trade_item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'name.required' => '请输入联系渠道名称！',
//            'name.unique' => '该部门号已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_department,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,11,19])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Client_Contact::select('id')->where('name',$post_data["name"])->where('client_id',$me->client_id)->count();
            if($is_exist) return response_error([],"该【名称】已存在，请勿重复添加！");

            $mine = new DK_Client_Contact;
            $post_data["active"] = 1;
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client_Trade_Record::find($operate_id);
            if(!$mine) return response_error([],"该【联系渠道】不存在，刷新页面重试！");
        }
        else return response_error([],"参数有误！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if(!empty($post_data['custom']))
            {
                $post_data['custom'] = json_encode($post_data['custom']);
            }

            $mine_data = $post_data;
            unset($mine_data['operate']);

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
            }
            else throw new Exception("DK_Client_Contact--insert--fail");

            DB::commit();
            return response_success(['id'=>$mine->id]);
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
    // 【交易-管理】获取数据
    public function v1_operate_for_trade_item_get($post_data)
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

        $this->get_me();
        $me = $this->me;

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Client_Trade_Record::with([
            'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); }
        ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【交易-管理】管理员-删除
    public function v1_operate_for_trade_item_delete($post_data)
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
        if($operate != 'trade-item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84,88])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Trade_Record::find($item_id);
        if(!$mine) return response_error([],"该【交易】不存在，刷新页面重试！");

        if($mine->is_confirmed == 1) return response_error([],"该【交易】已确认，不能删除！");

        $delivery = DK_Common__Delivery::find($mine->delivery_id);
        if(!$delivery) return response_error([],"该【工单】不存在，刷新页面重试！");

//        if($mine->creator_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
//        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");
        if($me->user_type == 88 && $mine->creator_id != $me->id) return response_error([],"你没有权限删除其他人的交易！");
        if(in_array($me->user_type,[81,84]))
        {
            $staff = DK_Client_User::find($mine->creator_id);
            if($staff->department_id != $me->department_id) return response_error([],"你没有权限删除其他团队的交易！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->deleter_id = $me->id;
            $bool = $mine->save();  // 先更新
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client_Trade_Record--delete--fail");

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

    // 【交易-管理】管理员-删除
    public function v1_operate_for_trade_item_confirm($post_data)
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



        $datetime = date('Y-m-d H:i:s');
        $time = time();

        $operate = $post_data["operate"];
        if($operate != 'trade-item-confirm') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client_Trade_Record::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【交易】不存在，刷新页面重试！");

        if($mine->is_confirmed == 1) return response_error([],"该【交易】已确认，不能重复确认！");

        $delivery = DK_Common__Delivery::find($mine->delivery_id);
        if(!$delivery) return response_error([],"该【工单】不存在，刷新页面重试！");

//        if($mine->creator_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
//        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
//        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");
//        if($me->user_type == 88 && $mine->creator_id != $me->id) return response_error([],"你没有权限删除其他人的交易！");
        if(in_array($me->user_type,[81,84]))
        {
            $staff = DK_Client_User::find($mine->creator_id);
            if($staff->department_id != $me->department_id) return response_error([],"你没有权限确认其他团队的交易！");
        }


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $mine->timestamps = false;
            $mine->is_confirmed = 1;
            $mine->authenticator_id = $me->id;
            $mine->confirmed_at = $time;
            $bool = $mine->save();
            if($bool)
            {

                $the_delivery = DK_Common__Delivery::lockForUpdate()->withTrashed()->find($mine->delivery_id);

//                $mine->timestamps = false;
                $the_delivery->transaction_num += 1;
                $the_delivery->transaction_count += $mine->transaction_count;
                $the_delivery->transaction_amount += $mine->transaction_amount;
                $the_delivery->last_operation_datetime = $mine->transaction_datetime;
                $the_delivery->transaction_date = $mine->transaction_date;
                $bool_d = $the_delivery->save();
//                $mine->last_operation_datetime = $datetime;
//                $mine->last_operation_date = $datetime;
                if(!$bool_d) throw new Exception("DK_Common__Delivery--update--fail");
            }
            else
            {
                throw new Exception("DK_Client_Trade_Record--update--fail");
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




    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_dental_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Common__Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            $cellData[$k]['order_quality'] = $v['order_quality'];

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }


//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];

            if($v['order_er']['client_type'] == 1) $cellData[$k]['client_type'] = "种植牙";
            else if($v['order_er']['client_type'] == 2) $cellData[$k]['client_type'] = "矫正";
            else if($v['order_er']['client_type'] == 3) $cellData[$k]['client_type'] = "正畸";
            else $cellData[$k]['client_type'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'order_quality'=>'工单质量',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'client_type'=>'患者类型',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client__Delivery__Operation_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【工单】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>16,
                    'L'=>16,
                    'M'=>40,
                    'N'=>40,
                    'O'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_aesthetic_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Common__Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }


//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['field_1'] == 1) $cellData[$k]['field_1'] = "脸部";
            else if($v['order_er']['field_1'] == 21) $cellData[$k]['field_1'] = "植发";
            else if($v['order_er']['field_1'] == 31) $cellData[$k]['field_1'] = "身体";
            else if($v['order_er']['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

//            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client__Delivery__Operation_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【医美】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30,
                    'N'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }
    // 【数据导出】工单
    public function v1_operate_statistic_export_for_delivery_luxury_by_ids($post_data)
    {
        $this->get_me();
        $me = $this->me;


        $ids = $post_data['ids'];
        $ids_array = explode("-", $ids);

        $record_operate_type = 100;
        $record_column_type = 'ids';
        $record_before = '';
        $record_after = '';
        $record_title = $ids;

        // 工单
        $query = DK_Common__Delivery::select('*')
            ->with([
                'order_er'=>function($query) { $query->select('*'); },
                'project_er'=>function($query) { $query->select('id','name'); },
                'client_staff_er'=>function($query) { $query->select(['id','username','true_name']); },
            ])
            ->whereIn('id',$ids_array);



        $data = $query->orderBy('id','desc')->get();
        $data = $data->toArray();
//        $data = $data->groupBy('car_id')->toArray();
//        dd($data);

        $cellData = [];
        foreach($data as $k => $v)
        {
            $cellData[$k]['id'] = $v['id'];

//            $cellData[$k]['creator_name'] = $v['creator']['true_name'];
            $cellData[$k]['created_time'] = date('Y-m-d H:i:s', $v['created_at']);

            if($v['assign_status'] == 1) $cellData[$k]['assign_status'] = "已分配";
            else $cellData[$k]['assign_status'] = "未分配";

            if($v['client_staff_er'])
            {
                $cellData[$k]['assign_status'] = "已分配";
                $cellData[$k]['client_staff_er_name'] = $v['client_staff_er']['username'];
            }
            else
            {
                if($v['assign_status'] != 1)
                {
                    $cellData[$k]['assign_status'] = "未分配";
                }
                $cellData[$k]['client_staff_er_name'] = '';
            }

//            $cellData[$k]['project_er_name'] = $v['project_er']['name'];


            if($v['order_er']['field_1'] == 1) $cellData[$k]['field_1'] = "鞋帽服装";
            else if($v['order_er']['field_1'] == 2) $cellData[$k]['field_1'] = "包";
            else if($v['order_er']['field_1'] == 3) $cellData[$k]['field_1'] = "手表";
            else if($v['order_er']['field_1'] == 4) $cellData[$k]['field_1'] = "珠宝";
            else if($v['order_er']['field_1'] == 99) $cellData[$k]['field_1'] = "其他";
            else $cellData[$k]['field_1'] = "未选择";

            $cellData[$k]['client_name'] = $v['order_er']['client_name'];
            $cellData[$k]['client_phone'] = $v['order_er']['client_phone'];


            // 微信号 & 是否+V
            $cellData[$k]['wx_id'] = $v['order_er']['wx_id'];
//            if($v['is_wx'] == 1) $cellData[$k]['is_wx'] = '是';
//            else $cellData[$k]['is_wx'] = '--';

            $cellData[$k]['location_city'] = $v['order_er']['location_city'];
            $cellData[$k]['location_district'] = $v['order_er']['location_district'];

//            $cellData[$k]['teeth_count'] = $v['order_er']['teeth_count'];

            $cellData[$k]['follow_latest_description'] = $v['follow_latest_description'];

            $cellData[$k]['description'] = $v['order_er']['description'];
//            $cellData[$k]['recording_address'] = $v['order_er']['recording_address'];
            if(!empty($v['order_er']['recording_address_list']))
            {
                $cellData[$k]['recording_address'] = env('DOMAIN_DK_CLIENT').'/data/order-detail?order_id='.medsci_encode($v['order_id'],'2024').'&phone='.$v['client_phone'];
            }
            else
            {
                $cellData[$k]['recording_address'] = '';
            }

            // 是否重复
//            if($v['is_repeat'] >= 1) $cellData[$k]['is_repeat'] = '是';
//            else $cellData[$k]['is_repeat'] = '--';

            // 审核
//            $cellData[$k]['inspector_name'] = $v['inspector']['true_name'];
//            $cellData[$k]['inspected_time'] = date('Y-m-d H:i:s', $v['inspected_at']);
//            $cellData[$k]['inspected_result'] = $v['inspected_result'];
        }


        $title_row = [
            'id'=>'ID',
//            'creator_name'=>'创建人',
            'created_time'=>'交付时间',
            'assign_status'=>'是否分配',
            'client_staff_er_name'=>'分派员工',
//            'project_er_name'=>'项目',
//            'channel_source'=>'渠道来源',
            'field_1'=>'品类',
            'client_name'=>'客户姓名',
            'client_phone'=>'客户电话',
            'wx_id'=>'微信号',
//            'is_wx'=>'是否+V',
            'location_city'=>'所在城市',
            'location_district'=>'行政区',
//            'teeth_count'=>'牙齿数量',
            'follow_latest_description'=>'最新跟进状态',
            'description'=>'通话小结',
            'recording_address'=>'录音地址',
//            'is_repeat'=>'是否重复',
//            'inspector_name'=>'审核人',
//            'inspected_time'=>'审核时间',
//            'inspected_result'=>'审核结果',
        ];
        array_unshift($cellData, $title_row);


        $record = new DK_Client__Delivery__Operation_Record;

        $record_data["ip"] = Get_IP();
        $record_data["record_object"] = 31;
        $record_data["record_category"] = 11;
        $record_data["record_type"] = 1;
        $record_data["creator_id"] = $me->id;
        $record_data["operate_object"] = 71;
        $record_data["operate_category"] = 109;
        $record_data["operate_type"] = $record_operate_type;
        $record_data["column_type"] = $record_column_type;
        $record_data["before"] = $record_before;
        $record_data["after"] = $record_after;
        $record_data["title"] = $record_title;

        $record->fill($record_data)->save();




        $title = '【二奢】'.date('Ymd.His').'_by_ids';

        $file = Excel::create($title, function($excel) use($cellData) {
            $excel->sheet('全部工单', function($sheet) use($cellData) {
                $sheet->rows($cellData);
                $sheet->setWidth(array(
                    'A'=>10,
                    'B'=>20,
                    'C'=>16,
                    'D'=>16,
                    'E'=>16,
                    'F'=>16,
                    'G'=>16,
                    'H'=>16,
                    'I'=>16,
                    'J'=>16,
                    'K'=>40,
                    'L'=>30,
                    'M'=>30,
                    'N'=>30
                ));
                $sheet->setAutoSize(false);
                $sheet->freezeFirstRow();
            });
        })->export('xls');

    }


}