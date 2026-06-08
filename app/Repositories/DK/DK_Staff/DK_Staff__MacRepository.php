<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Mac_Address;
use App\Models\DK\DK_Common\DK_Common__Record__by_Operation;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__MacRepository {

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
     * MAC地址-管理 Staff
     */
    // 【MAC地址】返回-列表-数据
    public function o1__mac_address__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Common__Mac_Address::withTrashed()->select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'company_er'=>function($query) { $query->select(['id','name']); },
                'department_er'=>function($query) { $query->select(['id','name']); },
                'team_er'=>function($query) { $query->select(['id','name']); },
                'team_sub_er'=>function($query) { $query->select(['id','name']); },
                'team_group_er'=>function($query) { $query->select(['id','name']); },
                'leader'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('active',1);

        if(in_array($me->staff_category,[41]))
        {
            $query->where('department_id',$me->department_id);
            if($me->staff_position == 41)
            {
                $query->where('team_id',$me->team_id);
            }
            else if($me->staff_position == 51)
            {
                $query->where('team_id',$me->team_id);
                $query->where('team_sub_id',$me->team_sub_id);
            }
            else if($me->staff_position == 61)
            {
                $query->where('team_id',$me->team_id);
                $query->where('team_group_id',$me->team_group_id);
            }
        }


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['mac_address'])) $query->where('mac_address', 'like', "%{$post_data['mac_address']}%");
        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['remark'])) $query->where('remark', 'like', "%{$post_data['remark']}%");
        if(!empty($post_data['description'])) $query->where('description', 'like', "%{$post_data['description']}%");
        if(!empty($post_data['keyword'])) $query->where('content', 'like', "%{$post_data['keyword']}%");

        // 状态 [|]
        if(!empty($post_data['item_status']))
        {
            $item_status_int = intval($post_data['item_status']);
            if(!in_array($item_status_int,[-1,0]))
            {
                $query->where('item_status', $item_status_int);
            }
        }
        else
        {
//            $query->where('item_status', 1);
        }



        // 公司
        if(!empty($post_data['company']))
        {
            $company_id_int = (int)$post_data['company_id'];
            if(!in_array($company_id_int,[-1,0]))
            {
                $query->where('company_id', $company_id_int);
            }
        }

        // 部门
        if(!empty($post_data['department']))
        {
            $department_id_int = (int)$post_data['department'];
            if(!in_array($department_id_int,[-1,0]))
            {
                $query->where('department_id', $department_id_int);
            }
        }

        // 团队
        if(!empty($post_data['team']))
        {
            $team_id_int = (int)$post_data['team'];
            if(!in_array($team_id_int,[-1,0]))
            {
                $query->where('team_id', $team_id_int);
            }
        }

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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【MAC地址】获取 GET
    public function o1__mac_address__item_get($post_data)
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

        $item = DK_Common__Mac_Address::withTrashed()
            ->with([
                'company_er'=>function($query) { $query->select('id','name'); },
                'department_er'=>function($query) { $query->select('id','name'); },
                'team_er'=>function($query) { $query->select('id','name'); },
                'team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【MAC地址】保存数据
    public function o1__mac_address__item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'mac_address.required' => '请输入MAC地址！',
            'api_customerName.required' => '请输入客户名！',
            'api_userName.required' => '请输入用户名！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'mac_address' => 'required',
            'api_customerName' => 'required',
            'api_userName' => 'required',
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
        if(!in_array($me->staff_position,[0,1,11,21,31,41,51,61,71])) return response_error([],"你没有操作权限！");

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_exist = DK_Common__Mac_Address::where('mac_address',$post_data['mac_address'])->first();
            if($is_exist) return response_error([],"该【MAC地址】已存在！");

            $is_exist = DK_Common__Mac_Address::where('api_userName',$post_data['api_userName'])->first();
            if($is_exist) return response_error([],"该【用户名】已存在！");

            $mine = new DK_Common__Mac_Address;
            $post_data["item_status"] = 1;
//            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["creator_id"] = $me->id;
//            $post_data['name'] = $post_data['name'];

        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Common__Mac_Address::find($operate_id);
            if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");
            if($mine->mac_address != $post_data['mac_address'])
            {
                $is_exist = DK_Common__Mac_Address::where('mac_address',$post_data['mac_address'])->where('id','!=',$operate_id)->first();
                if($is_exist) return response_error([],"该【MAC地址】重复，请更换再试一次！");

                $is_exist = DK_Common__Mac_Address::where('api_userName',$post_data['api_userName'])->where('id','!=',$operate_id)->first();
                if($is_exist) return response_error([],"该【用户名】重复，请更换再试一次！");
            }
        }
        else return response_error([],"参数有误！");


        if($me->staff_position == 11)
        {
            if(!empty($post_data['team_id']))
            {
                if((int)$post_data['team_id'] <= 0) response_error([],"请选择团队！");
            }
            else response_error([],"请选择团队！");
        }
        if($me->staff_position == 31)
        {
            if(!empty($post_data['team_id']))
            {
                if((int)$post_data['team_id'] <= 0) response_error([],"请选择团队！");
            }
            else response_error([],"请选择团队！");
        }
        if($me->staff_position == 41)
        {
        }
        if($me->staff_position == 61)
        {
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_data = $post_data;
            unset($mine_data['operate']);

            if(!empty($mine_data['custom']))
            {
                $mine_data['custom'] = json_encode($mine_data['custom']);
            }

            // 判断部门是否存在
            if(empty($post_data['department_id']))
            {
                unset($mine_data['department_id']);
            }
            else
            {
                $department = DK_Common__Department::find($post_data['department_id']);
                if($department) $mine_data['company_id'] = $department->company_id;
            }

            // 判断团队是否存在
            if(empty($post_data['team_id'])) unset($mine_data['team_id']);
            if(empty($mine_data['team_group_id'])) unset($mine_data['team_group_id']);


            if($me->staff_category == 41)
            {
                if($me->staff_position == 31)
                {
                    $mine_data['company_id'] = $me->company_id;
                    $mine_data['department_id'] = $me->department_id;
                }
                if($me->staff_position == 41)
                {
                    $mine_data['company_id'] = $me->company_id;
                    $mine_data['department_id'] = $me->department_id;
                    $mine_data['team_id'] = $me->team_id;
                }
                if($me->staff_position == 61)
                {
                    $mine_data['company_id'] = $me->company_id;
                    $mine_data['department_id'] = $me->department_id;
                    $mine_data['team_id'] = $me->team_id;
                    $mine_data['team_group_id'] = $me->team_group_id;
                }
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
//                    $user_ext = new DK_Common__Mac_Address_Ext;
//                    $user_ext_create['user_id'] = $mine->id;
//                    $bool_2 = $user_ext->fill($user_ext_create)->save();
//                    if(!$bool_2) throw new Exception("insert--user-ext--failed");
                }

                // 头像
                if(!empty($post_data["portrait"]))
                {
                    // 删除原图片
                    $mine_portrait_img = $mine->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_resource_path($mine_portrait_img)))
                    {
                        unlink(storage_resource_path($mine_portrait_img));
                    }

//                    $result = upload_storage($post_data["portrait"]);
//                    $result = upload_storage($post_data["portrait"], null, null, 'assign');
                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$mine->id,'dk/unique/portrait_for_user','');
                    if($result["result"])
                    {
                        $mine->portrait_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--portrait_img--file--fail");
                }
                else
                {
                    if($operate == 'create')
                    {
                        $portrait_path = "dk_staff/unique/portrait_for_user/".date('Y-m-d');
                        if (!is_dir(storage_resource_path($portrait_path)))
                        {
                            mkdir(storage_resource_path($portrait_path), 0777, true);
                        }
                        copy(storage_resource_path("materials/portrait/user0.jpeg"), storage_resource_path($portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg"));
                        $mine->portrait_img = $portrait_path."/portrait_for_user_by_user_".$mine->id.".jpeg";
                        $mine->save();
                    }
                }

            }
            else throw new Exception("DK_Common__Mac_Address--insert--fail");

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


    // 【MAC地址】删除
    public function o1__mac_address__item_delete($post_data)
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
        if($operate != 'mac-address--item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Mac_Address::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 11;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["creator_team_sub_id"] = $me->team_sub_id;
        $record_data["creator_team_group_id"] = $me->team_group_id;
        $record_data["creator_team_unit_id"] = $me->team_unit_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'deleted_at';
        $operation['title'] = '操作';
        $operation['before'] = '';
        $operation['after'] = '删除';
        $operation_record_data[] = $operation;

        $record_data["content"] = json_encode($operation_record_data);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Common__Mac_Address--delete--fail");
            else
            {
                $staff_operation_record = new DK_Common__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Common__Record__by_Operation--insert--fail");
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
    // 【MAC地址】恢复
    public function o1__mac_address__item_restore($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'operate.required.',
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
        if($operate != 'mac-address--item-restore') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Mac_Address::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 12;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["creator_team_sub_id"] = $me->team_sub_id;
        $record_data["creator_team_group_id"] = $me->team_group_id;
        $record_data["creator_team_unit_id"] = $me->team_unit_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'deleted_at';
        $operation['title'] = '操作';
        $operation['before'] = '';
        $operation['after'] = '恢复';
        $operation_record_data[] = $operation;

        $record_data["content"] = json_encode($operation_record_data);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_Common__Mac_Address--restore--fail");
            else
            {
                $staff_operation_record = new DK_Common__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Common__Record__by_Operation--insert--fail");
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
    // 【MAC地址】彻底删除
    public function o1__mac_address__item_delete_permanently($post_data)
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
        if($operate != 'mac-address--item-delete-permanently') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Mac_Address::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 13;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["creator_team_sub_id"] = $me->team_sub_id;
        $record_data["creator_team_group_id"] = $me->team_group_id;
        $record_data["creator_team_unit_id"] = $me->team_unit_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'deleted_at';
        $operation['title'] = '操作';
        $operation['before'] = '';
        $operation['after'] = '彻底删除';
        $operation_record_data[] = $operation;

        $record_data["content"] = json_encode($operation_record_data);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_Common__Mac_Address--delete--fail");
            else
            {
                $staff_operation_record = new DK_Common__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Common__Record__by_Operation--insert--fail");
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


    // 【MAC地址】启用
    public function o1__mac_address__item_enable($post_data)
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
        if($operate != 'mac-address--item-enable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Mac_Address::find($item_id);
        if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 21;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["creator_team_sub_id"] = $me->team_sub_id;
        $record_data["creator_team_group_id"] = $me->team_group_id;
        $record_data["creator_team_unit_id"] = $me->team_unit_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'deleted_at';
        $operation['title'] = '操作';
        $operation['before'] = '';
        $operation['after'] = '启用';
        $operation_record_data[] = $operation;

        $record_data["content"] = json_encode($operation_record_data);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 1;
            $mine->login_error_num = 0;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Common__Mac_Address--update--fail");
            else
            {
                $staff_operation_record = new DK_Common__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Common__Record__by_Operation--insert--fail");
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
    // 【MAC地址】禁用
    public function o1__mac_address__item_disable($post_data)
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
        if($operate != 'mac-address--item-disable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Mac_Address::find($item_id);
        if(!$mine) return response_error([],"该【MAC地址】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 22;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;
        $record_data["creator_team_sub_id"] = $me->team_sub_id;
        $record_data["creator_team_group_id"] = $me->team_group_id;
        $record_data["creator_team_unit_id"] = $me->team_unit_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'deleted_at';
        $operation['title'] = '操作';
        $operation['before'] = '';
        $operation['after'] = '禁用';
        $operation_record_data[] = $operation;

        $record_data["content"] = json_encode($operation_record_data);


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->item_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Common__Mac_Address--update--fail");
            else
            {
                $staff_operation_record = new DK_Common__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Common__Record__by_Operation--insert--fail");
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


    // 【MAC地址】【操作记录】返回-列表-数据
    public function o1__mac_address__item_operation_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Common__Record__by_Operation::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['staff_id'=>$id]);

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

            if($v->owner_id == $me->id) $list[$k]->is_me = 1;
            else $list[$k]->is_me = 0;
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


}