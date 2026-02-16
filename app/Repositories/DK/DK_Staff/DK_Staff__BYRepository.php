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
use App\Models\DK\DK_Common\DK_Common__Order_Operation_Record;
use App\Models\DK\DK_Common\DK_Common__Delivery;

use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;


use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Statistic;

use App\Models\DK\DK_API_BY_Received;


use App\Jobs\DK_Client\AutomaticDispatchingJob;
use App\Jobs\DK\BYApReceivedJob;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DK_Staff__BYRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {

        $this->view_blade_403 = env('TEMPLATE_DK_ADMIN').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_ADMIN').'entrance.errors.404';

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



    /*
     * 项目-管理 Project
     */
    // 【API】返回-列表-数据
    public function v1_operate_for__BY__datatable_list_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(!in_array($me->user_type,[0,1,9,11,61,66,71,77])) return response_error([],"你没有操作权限！");

        $query = DK_API_BY_Received::select('*')
            ->withTrashed()
            ->with([
                'inspector'=>function($query) { $query->select(['id','name']); }
            ]);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name']))
        {
            $query->where('name', 'like', "%{$post_data['name']}%");
            $name = "%{$post_data['name']}%";
            if($me->department_district_id > 0)
            {
                $query->where('name','like',$name);
            }
            else
            {
                $query->where(function($query) use($name) {
                    $query->where('name','like',$name)->orWhere('alias_name','like',$name);
                });
            }
        }
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['mobile'])) $query->where('mobile', $post_data['mobile']);

        // 状态 [|]
        if(!empty($post_data['api_status']))
        {
            if(!in_array($post_data['api_status'],[-1,0,'-1','0']))
            {
                $query->where('api_status', $post_data['item_status']);
            }
        }
        else
        {
            if(in_array($me->user_type,[11,61,66,71,77])) $query->where('api_status', '>=', 1);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip  = isset($post_data['start']) ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 100;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
//        else $query->orderBy("name", "asc");
        else $query->orderBy("id", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();
//        dd($list->toArray());

        return datatable_response($list, $draw, $total);
    }
    // 【API】获取 GET
    public function v1_operate_for__BY__item_get($post_data)
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

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,61])) return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Project::withTrashed()
            ->with([
                'client_er'=>function($query) { $query->select(['id','name']); },
                'inspector_er'=>function($query) { $query->select(['id','name']); },
                'pivot_project_user',
                'pivot_project_team'
            ])
            ->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【API】保存 SAVE
    public function v1_operate_for__BY__item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_category.required' => '请选择项目种类！',
            'name.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_category' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,11,19,61])) return response_error([],"你没有操作权限！");

        if($operate_type == 'create')
        {
            // 添加 ( $id==0，添加一个项目 )
            $is_exist = DK_Common__Project::select('id')->where('name',$post_data["name"])->count();
            if($is_exist) return response_error([],"该【项目】已存在，请勿重复添加！");

            $mine = new DK_Common__Project;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit')
        {
            // 编辑
            $mine = DK_Common__Project::find($operate_id);
            if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");
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
                if(!empty($post_data["peoples"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $peoples = $post_data["peoples"];
                    foreach($peoples as $p)
                    {
                        $people_insert[$p] = ['creator_id'=>$me->id,'department_id'=>$me->department_district_id,'relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $mine->pivot_project_user()->sync($people_insert);
//                    $mine->pivot_project_user()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $mine->pivot_project_user()->detach();
                }

                if(!empty($post_data["teams"]))
                {
//                    $product->peoples()->attach($post_data["peoples"]);
                    $current_time = time();
                    $teams = $post_data["teams"];
                    foreach($teams as $t)
                    {
                        $team_insert[$t] = ['relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
                    }
                    $mine->pivot_project_team()->sync($team_insert);
//                    $mine->pivot_product_people()->syncWithoutDetaching($people_insert);
                }
                else
                {
                    $mine->pivot_project_team()->detach();
                }
            }
            else throw new Exception("insert--project--fail");

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

    // 【API】预处理
    public function v1_operate_for__BY__item_preprocess($post_data)
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

        $operate = $post_data["operate"];
        if($operate != 'item-preprocess') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_API_BY_Received::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        if($item->api_status > 0)
        {
            return response_error([],"该【工单】已经处理过了！");
        }

//        BYApReceivedJob::dispatch($id);
//        return response_success([],"预处理完成!");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66,71,77])) return response_error([],"你没有操作权限！");


        $item_content = $item->content;
        $item_para = json_decode($item_content);


        if(isset($item_para->client_name)) $update["client_name"] = $item_para->client_name;
        if(isset($item_para->client_phone))
        {
            $client_phone = $item_para->client_phone;
            $update["client_phone"] = $item_para->client_phone;
        }
        else
        {
            $client_phone = '';
            return response_error([],"电话为空！");
        }
        if(isset($item_para->client_intention)) $update["client_intention"] = $item_para->client_intention;
        if(isset($item_para->lable_info))
        {
            if(isset($item_para->lable_info->is_wx))
            {
                if(in_array($item_para->lable_info->is_wx,['是','对'])) $update["is_wx"] = 1;
            }
            if(isset($item_para->lable_info->location_city)) $update["location_city"] = $item_para->lable_info->location_city;
            if(isset($item_para->lable_info->location_district)) $update["location_district"] = $item_para->lable_info->location_district;
        }
        if(isset($item_para->recording_address)) $update["recording_address"] = $item_para->recording_address;
        if(isset($item_para->dialog_content)) $update["dialog_content"] = json_encode($item_para->dialog_content);


        $is_repeat = DK_API_BY_Received::where(['client_phone'=>(int)$client_phone])
            ->where('id','<',$id)
            ->count("*");
//        dd($is_repeat);
        $update["is_repeat"] = $is_repeat;


            // 启动数据库事务
        DB::beginTransaction();
        try
        {
//            $item->is_repeat = $is_repeat;
//            $item->client_name = $client_name;
//            $item->client_phone = $client_phone;
//            $item->client_intention = $client_intention;
//            $item->is_wx = $is_wx;
//            $item->teeth_count = $teeth_count;
//            $item->location_city = $location_city;
//            $item->location_district = $location_district;
//            $item->recording_address = $recording_address;
//            $item->dialog_content = $dialog_content;
////            $item->inspected_at = $time;
////            $item->inspected_date = $date;
//
//            $bool = $item->save();

            $update["api_status"] = 1;
            $bool = $item->fill($update)->save();
            if(!$bool) throw new Exception("DK_API_BY_Received--update--fail");
            else
            {
//                $record = new DK_Common__Order_Operation_Record;
//
//                $record_data["ip"] = Get_IP();
//                $record_data["record_object"] = 21;
//                $record_data["record_category"] = 11;
//                $record_data["record_type"] = 1;
//                $record_data["creator_id"] = $me->id;
//                $record_data["order_id"] = $id;
//                $record_data["operate_object"] = 101;
//                $record_data["operate_category"] = 11;
//                $record_data["operate_type"] = 1;
//                $record_data["process_category"] = 1;
//
//                $bool_1 = $record->fill($record_data)->save();
//                if(!$bool_1) throw new Exception("insert--record--fail");
            }

            DB::commit();

            return response_success([],"预处理完成!");
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
    // 【API】审核
    public function v1_operate_for__BY__item_inspect($post_data)
    {
//        dd($post_data);
//        return response_success([]);
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
        if($operate != 'by-inspect') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = DK_API_BY_Received::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,61,66,71,77])) return response_error([],"你没有操作权限！");
//        if(in_array($me->user_type,[71,87]) && $item->creator_id != $me->id) return response_error([],"该内容不是你的，你不能操作！");

        $inspected_result = $post_data["inspected_result"];
//        if(!in_array($inspected_result,config('info.by_inspected_result'))) return response_error([],"审核结果非法！");
        if(!array_key_exists($inspected_result, config('info.by_inspected_result'))) return response_error([],"审核结果非法，请正确选择审核结果！");
        $inspected_description = $post_data["inspected_description"];
        $recording_quality = $post_data["recording_quality"];


        $before = $item->inspected_result;
        $datetime = date('Y-m-d H:i:s');

        $date = date("Y-m-d");


        $project_id = $post_data["project_id"];
        $client_type = $post_data["client_type"];
        $client_name = $post_data["client_name"];
        $client_intention = $post_data["client_intention"];
        $teeth_count = $post_data["teeth_count"];
        $description = $post_data["description"];
        $location_city = $post_data["location_city"];
        $location_district = $post_data["location_district"];
        $recording_quality = $post_data["recording_quality"];

        $client_phone = $item->client_phone;

        if($inspected_result == 1)
        {


            if(!($project_id > 0)) return response_error([],"请选择项目！");
            $is_repeat = DK_Common__Order::where(['project_id'=>$project_id,'client_phone'=>(int)$client_phone])
                ->when($item->order_id, function ($query) use ($item) {
                    return $query->where('id','<>',$item->order_id);
                })
//                ->where('id','<>',$id)
//                ->where('is_published','>',0)
//                ->where('item_category',1)
                ->count("*");
            if($is_repeat > 0)
            {
                return response_error([],"该电话已存在于该项目，请更换项目！");
            }
            else
            {
                $is_repeat = DK_Common__Delivery::where(['project_id'=>$project_id,'client_phone'=>(int)$client_phone])->count("*");
                if($is_repeat > 0) return response_error([],"该电话已存在于该项目，请更换项目！");
            }
//            dd($is_repeat);

            if(!empty($location_city) && !empty($location_district))
            {
            }
            else
            {
                return response_error([],"请选择城市和区域！");
            }
        }
//        dd($inspected_result);

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            if($inspected_result == 1)
            {
                if($item->order_id > 0)
                {
                    $order = DK_Common__Order::find($item->order_id);
                    if(!$order)
                    {
                        $order = new DK_Common__Order;

                        $order->item_category = 1;
                        $order->created_type = 91;
                        $order->creator_id = $me->id;
                        $order->inspector_id = $me->id;
                        $order->inspected_status = 1;
                        $order->inspected_result = '通过';
                        $order->inspected_at = time();
                        $order->inspected_date = $date;
                        $order->published_at = time();
                        $order->published_date = $date;

                        $recording_file[0] = $item->recording_address;
                        $order->recording_address = $item->recording_address;
                        $order->recording_address_list = json_encode($recording_file);

                    }
                }
                else
                {
                    $order = new DK_Common__Order;

                    $order->item_category = 1;
                    $order->created_type = 91;
                    $order->creator_id = $me->id;
                    $order->inspector_id = $me->id;
                    $order->inspected_status = 1;
                    $order->inspected_result = '通过';
                    $order->inspected_at = time();
                    $order->inspected_date = $date;
                    $order->published_at = time();
                    $order->published_date = $date;

                    $recording_file[0] = $item->recording_address;
                    $order->recording_address = $item->recording_address;
                    $order->recording_address_list = json_encode($recording_file);
                }


                $order->project_id = $project_id;
                $order->client_type = $client_type;
                $order->client_name = $client_name;
                $order->client_phone = $client_phone;
                $order->client_intention = $client_intention;
                $order->teeth_count = $teeth_count;
                $order->description = $description;
                $order->recording_quality = $recording_quality;
                $order->is_published = 1;
                $order->field_2 = 11;



                $bool_o = $order->save();

                $item->order_id = $order->id;
            }

            $item->api_status = 9;
            $item->inspector_id = $me->id;
            $item->inspected_status = 9;
            $item->inspected_result = $inspected_result;
            if($inspected_description) $item->inspected_description = $inspected_description;
            $item->recording_quality = $recording_quality;
            $item->inspected_at = time();
            $item->inspected_date = $date;
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
                $record_data["operate_object"] = 101;
                $record_data["operate_category"] = 92;
                $record_data["operate_type"] = 1;
                $record_data["process_category"] = 1;
                $record_data["description"] = $inspected_description;

                $record_content = [];

                $record_row['title'] = '结果';
                $record_row['field'] = 'inspected_result';
//                $record_row['code'] = $inspected_result;
                $record_row['before'] = $before;
                $record_row['after'] = $inspected_result;
                $record_content[] = $record_row;

                $record_row['field'] = 'inspected_description';
                $record_row['title'] = '说明';
                $record_row['before'] = '';
                $record_row['after'] = $inspected_description;
                $record_content[] = $record_row;

                $record_row['field'] = 'appeal_handle_time';
                $record_row['title'] = '时间';
                $record_row['before'] = '';
                $record_row['after'] = $datetime;
                $record_content[] = $record_row;

                $record_data["content"] = json_encode($record_content);

                $record_data["before"] = $before;
                $record_data["after"] = $inspected_result;

                $bool_1 = $record->fill($record_data)->save();
                if(!$bool_1) throw new Exception("insert--record--fail");
            }



            DB::commit();


            return response_success([],"审核完成!");
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