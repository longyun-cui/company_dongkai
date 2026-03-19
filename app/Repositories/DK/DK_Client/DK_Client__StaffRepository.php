<?php
namespace App\Repositories\DK\DK_Client;

use App\Models\DK\DK_Client\DK_Client__Team;
use App\Models\DK\DK_Client\DK_Client__Staff;
use App\Models\DK\DK_Client\DK_Client__Record__by_Operation;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DK_Client__StaffRepository {

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


    /*
     * 员工管理
     */


    /*
     * 员工-管理 Staff
     */
    // 【员工】返回-列表-数据
    public function o1__staff__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $query = DK_Client__Staff::withTrashed()->select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'team_er'=>function($query) { $query->select(['id','name']); },
                'team_sub_er'=>function($query) { $query->select(['id','name']); },
                'team_group_er'=>function($query) { $query->select(['id','name']); },
                'leader'=>function($query) { $query->select(['id','name']); }
            ])
//            ->when(in_array($me->staff_category, [0]), function ($query) {
//                return $query->whereIn('staff_category',[1,11,21,31,41,51,61,71,81,99]);
//            })
//            ->when($me->staff_category == 0, function ($query) use($me) {
//                return $query->whereIn('staff_category',[1,9,11,21,31,41,51,61,71,81,99]);
//            })
//            ->when($me->staff_category == 1, function ($query) use($me) {
//                return $query->whereIn('staff_category',[9,11,21,31,41,51,61,71,81,99]);
//            })
//            ->when($me->staff_category == 41, function ($query) use($me) {
//                return $query->whereIn('staff_category',[41]);
//            })
//            ->when($me->staff_category == 51, function ($query) use($me) {
//                return $query->whereIn('staff_category',[51]);
//            })
//            ->when($me->staff_category == 61, function ($query) use($me) {
//                return $query->whereIn('staff_category',[61]);
//            })
//            ->when($me->staff_category == 71, function ($query) use($me) {
//                return $query->whereIn('staff_category',[71]);
//            })
//            ->when($me->staff_category == 81, function ($query) use($me) {
//                return $query->whereIn('staff_category',[81]);
//            })
//            ->when($me->staff_category == 88, function ($query) use($me) {
//                return $query->whereIn('staff_category',[88]);
//            })
            ->where('active',1)
            ->where('id','<>',$me->id)
            ->where('client_id',$me->client_id);


        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['login_number'])) $query->where('login_number', 'like', "%{$post_data['login_number']}%");
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

        // 员工类型
        if(!empty($post_data['staff_category']))
        {
            $staff_category_int = intval($post_data['staff_category']);
            if(!in_array($staff_category_int,[-1,0]))
            {
                $query->where('staff_category', $staff_category_int);
            }
        }
        // 员工职位
        if(!empty($post_data['staff_position']))
        {
            $staff_position_int = intval($post_data['staff_position']);
            if(!in_array($staff_position_int,[-1,0]))
            {
                $query->where('staff_position', $staff_position_int);
            }
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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【员工】获取 GET
    public function o1__staff__item_get($post_data)
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

        $item = DK_Client__Staff::withTrashed()
            ->with([
                'team_er'=>function($query) { $query->select('id','name'); },
                'team_group_er'=>function($query) { $query->select('id','name'); }
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【员工】保存数据
    public function o1__staff__item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
//            'login_number.required' => '请输入登录工号！',
            'name.required' => '请输入姓名！',
//            'company_id.required' => '请选择所属公司！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'login_number' => 'required',
            'name' => 'required',
//            'company_id' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
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
            $is_exist = DK_Client__Staff::select('*')
                ->where('client_id',$me->client_id)
                ->where('name',$post_data['name'])
                ->first();
            if($is_exist) return response_error([],"该【工号】已存在！");

            $mine = new DK_Client__Staff;
            $post_data["item_status"] = 1;
//            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
//            $post_data['name'] = $post_data['name'];

        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client__Staff::find($operate_id);
            if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
            if($mine->name != $post_data['name'])
            {
                $is_exist = DK_Client__Staff::select('*')
                    ->where('client_id',$me->client_id)
                    ->where('name',$post_data['name'])
                    ->where('id','!=',$operate_id)
                    ->first();
                if($is_exist) return response_error([],"用户名重复，请更换再试一次！");
            }
        }
        else return response_error([],"参数有误！");


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


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
//                    $user_ext = new DK_Client__Staff_Ext;
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
            else throw new Exception("DK_Client__Staff--insert--fail");

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


    // 【员工】删除
    public function o1__staff__item_delete($post_data)
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
        if($operate != 'staff--item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 11;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
//        $record_data["creator_company_id"] = $me->company_id;
//        $record_data["creator_department_id"] = $me->department_id;
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
            if(!$bool) throw new Exception("DK_Client__Staff--delete--fail");
            else
            {
                $staff_operation_record = new DK_Client__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Client__Record__by_Operation--insert--fail");
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
    // 【员工】恢复
    public function o1__staff__item_restore($post_data)
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
        if($operate != 'staff--item-restore') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 12;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
//        $record_data["creator_company_id"] = $me->company_id;
//        $record_data["creator_department_id"] = $me->department_id;
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
            if(!$bool) throw new Exception("DK_Client__Staff--restore--fail");
            else
            {
                $staff_operation_record = new DK_Client__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Client__Record__by_Operation--insert--fail");
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
    // 【员工】彻底删除
    public function o1__staff__item_delete_permanently($post_data)
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
        if($operate != 'staff--item-delete-permanently') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 13;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
//        $record_data["creator_company_id"] = $me->company_id;
//        $record_data["creator_department_id"] = $me->department_id;
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
            if(!$bool) throw new Exception("DK_Client__Staff--delete--fail");
            else
            {
                $staff_operation_record = new DK_Client__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Client__Record__by_Operation--insert--fail");
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


    // 【员工】启用
    public function o1__staff__item_enable($post_data)
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
        if($operate != 'staff--item-enable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 21;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
//        $record_data["creator_company_id"] = $me->company_id;
//        $record_data["creator_department_id"] = $me->department_id;
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
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");
            else
            {
                $staff_operation_record = new DK_Client__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Client__Record__by_Operation--insert--fail");
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
    // 【员工】禁用
    public function o1__staff__item_disable($post_data)
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
        if($operate != 'staff--item-disable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'staff';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 22;
        $record_data["item_id"] = $item_id;
        $record_data["staff_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
//        $record_data["creator_company_id"] = $me->company_id;
//        $record_data["creator_department_id"] = $me->department_id;
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
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");
            else
            {
                $staff_operation_record = new DK_Client__Record__by_Operation;
                $bool_sop = $staff_operation_record->fill($record_data)->save();
                if(!$bool_sop) throw new Exception("DK_Client__Record__by_Operation--insert--fail");
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


    // 【员工-管理】管理员-重置密码
    public function o1__staff__item_password_reset($post_data)
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
        if($operate != 'staff--item-password-reset') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[0,1,9,11,31,41,51,61,71])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->staff_position <= $me->staff_position) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode('12345678');
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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
    // 【员工-管理】管理员-修改密码
    public function o1__staff__item_password_change($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'password.required' => '请输入密码！',
            'password_confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'password' => 'required',
            'password_confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        if($operate != 'staff--item-password-change') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[0,1,9,11,31,41,51,61,71])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->staff_position <= $me->staff_position) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["password"];
        $confirm = $post_data["password_confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode($password);
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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


    // 【员工】登录
    public function o1__staff__item_login($post_data)
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
        if($operate != 'staff--item-login') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[0,1,9,11,31,41,51,61,71])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");

        $token = request()->get('_token');
        Auth::guard('dk_staff_user')->login($mine,true);
        Auth::guard('dk_staff_user')->user()->only_token = $token;
        Auth::guard('dk_staff_user')->user()->save();
        return response_success();

    }


    // 【员工】【操作记录】返回-列表-数据
    public function o1__staff__item_operation_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Client__Record__by_Operation::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['staff_id'=>$id]);

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
    
    
    
    
    
    // 【员工-管理】返回-列表-数据
    public function o1__staff__list__datatable_query1($post_data)
    {
        $this->get_me();
        $me = $this->me;
        dd($me->client_id);

        $query = DK_Client__Staff::withTrashed()->select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); }
            ])
            ->whereIn('staff_category',[99])
            ->where('client_id',$me->client_id)
            ->where('id','!=',$me->id);

        if($me->user_type == 11)
        {
            $query->whereIn('user_type',[41,61,66,71,77,81,84,88]);
        }
        else if($me->user_type == 61)
        {
            $query->whereIn('user_type',[66]);
        }
        else if($me->user_type == 41)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[71,77,81,84,88]);
        }
        else if($me->user_type == 71)
        {
            $query->where('department_district_id',$me->department_district_id)
                ->whereIn('user_type',[77]);
        }
        else if($me->user_type == 81)
        {
            $query->where('department_id',$me->department_id)
                ->whereIn('user_type',[84,88]);
        }
        else if($me->user_type == 84)
        {
            $query->where('department_id',$me->department_id)
                ->whereIn('user_type',[88]);
        }

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");


        // 部门-大区
        if(!empty($post_data['department']))
        {
            if(!in_array($post_data['department'],[-1,0,'-1','0']))
            {
                $query->where('department_id', $post_data['department']);
            }
        }

        // 员工类型
        if(!empty($post_data['user_type']))
        {
            if(!in_array($post_data['user_type'],[-1,0]))
            {
                $query->where('user_type', $post_data['user_type']);
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
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
//        dd($total);
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }
    // 【员工-管理】保存数据
    public function v1_operate_for_staff_item_save($post_data)
    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误！',
            'name.required' => '请输入用户名！',
//            'mobile.required' => '请输入电话！',
//            'mobile.unique' => '电话已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'name' => 'required',
//            'mobile' => 'required',
//            'mobile' => 'required|unique:dk_user,mobile',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $this->get_me();
        $me = $this->me;
        if(!in_array($me->staff_position,[0,1,9])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];

        if($operate_type == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $is_name_exist = DK_Client__Staff::where('username',$post_data['username'])->first();
            if($is_name_exist) return response_error([],"用户名已存在！");

            $is_mobile_exist = DK_Client__Staff::where('mobile',$post_data['mobile'])->first();
            if($is_mobile_exist) return response_error([],"手机号（工号）已存在！");

            $mine = new DK_Client__Staff;
            $post_data["user_status"] = 0;
            $post_data["user_category"] = 11;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("12345678");
            $post_data["client_id"] = $me->client_id;
            $post_data["creator_id"] = $me->id;
        }
        else if($operate_type == 'edit') // 编辑
        {
            $mine = DK_Client__Staff::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
            if($mine->mobile != $post_data['mobile'])
            {
                $is_mobile_exist = DK_Client__Staff::where('mobile',$post_data['mobile'])->first();
                if($is_mobile_exist) return response_error([],"手机号（工号）重复，请更换工号再试一次！");
            }
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
            unset($mine_data['operate_id']);
            unset($mine_data['category']);
            unset($mine_data['type']);

            if(in_array($me->user_type,[41,61,71,81]))
            {
                $mine_data['department_district_id'] = $me->department_district_id;
            }
//            if($me->user_type == 81)
//            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//            }

            if($post_data["user_type"] == 71 || $post_data["user_type"] == 77)
            {
//                $mine_data['department_district_id'] = $me->department_district_id;
//                unset($mine_data['department_district_id']);
                unset($mine_data['department_group_id']);
            }
            else if($post_data["user_type"] == 81)
            {
                unset($mine_data['department_group_id']);
            }


            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {
                if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
                {
//                    $user_ext = new DK_Client__StaffExt;
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
                        $portrait_path = "dk/unique/portrait_for_user/".date('Y-m-d');
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
            else throw new Exception("insert--user--fail");

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
    // 【员工-管理】获取数据
    public function v1_operate_for_staff_item_get($post_data)
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

        $item = DK_Client__Staff::with([
                'department_er'=>function($query) { $query->select(['id','name']); }
            ])->withTrashed()->find($id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }


    // 【员工-管理】管理员-删除
    public function operate_staff_delete_by_admin($post_data)
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
        if($operate != 'item-delete-by-admin') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();  // 普通删除
            if(!$bool) throw new Exception("DK_Client__Staff--delete--fail");

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
    // 【员工-管理】管理员-恢复
    public function operate_staff_restore_by_admin($post_data)
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
        if($operate != 'item-restore-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能恢复你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("DK_Client__Staff--restore--fail");

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
    // 【员工-管理】管理员-彻底删除
    public function operate_staff_delete_permanently_by_admin($post_data)
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
        if($operate != 'item-delete-permanently-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->id == $me->id) return response_error([],"你不能删除你自己！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine_copy = $mine;
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("DK_Client__Staff--delete--fail");

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


    // 【员工-管理】管理员-重置密码
    public function operate_staff_password_reset_by_admin($post_data)
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
        if($operate != 'item-password-reset-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21,81])) return response_error([],"你没有该操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode('12345678');
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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
    // 【员工-管理】管理员-修改密码
    public function operate_staff_password_change_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'item_id.required.',
            'password.required' => '请输入密码！',
            'password_confirm.required' => '请输入确认密码！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'item_id' => 'required',
            'password' => 'required',
            'password_confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $operate = $post_data["operate"];
        if($operate != 'item-password-change-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,21])) return response_error([],"你没有该操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::withTrashed()->find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        $password = $post_data["password"];
        $confirm = $post_data["password_confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->password = password_encode($password);
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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


    // 【员工-管理】管理员-启用
    public function operate_staff_enable_by_admin($post_data)
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
        if($operate != 'item-enable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->user_status = 1;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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
    // 【员工-管理】管理员-禁用
    public function operate_staff_disable_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'user_id.required.',
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
        if($operate != 'item-disable-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,81,84])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->user_status = 9;
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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


    // 【员工-管理】管理员-晋升
    public function operate_staff_promote_by_admin($post_data)
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
        if($operate != 'item-promote-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->user_type = 84;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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
    // 【员工-管理】管理员-降职
    public function operate_staff_demote_by_admin($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => 'user_id.required.',
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
        if($operate != 'item-demote-by-admin') return response_error([],"参数【operate】有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19,41])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Client__Staff::find($id);
        if(!$mine) return response_error([],"该【员工】不存在，刷新页面重试！");
        if($mine->client_id != $me->client_id) return response_error([],"归属错误，刷新页面重试！");
        if($mine->user_type <= $me->user_type) return response_error([],"你不能操作比你职级更高或同级的员工！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $mine->user_type = 88;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Client__Staff--update--fail");

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