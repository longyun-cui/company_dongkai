<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Record__by_Operation;
use App\Models\DK\DK_Common\DK_Pivot__Department_Project;
use App\Models\DK\DK_Common\DK_Pivot__Team_Project;
use App\Models\DK\DK_Common\DK_Pivot__Staff_Project;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__ProjectRepository {

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
     * 项目-管理 Project
     */
    // 【项目】返回-列表-数据
    public function o1__project__list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        if(in_array($me->staff_category,[0,1,9,71]) || in_array($me->staff_position,[31,41]))
        {
        }
        else return response_error([],"你没有操作权限！");


        $query = DK_Common__Project::select('*')
            ->withTrashed()
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
                'client_er'=>function($query) { $query->select(['id','name']); },
//                'inspector_er'=>function($query) { $query->select(['id','name']); }
            ])
            ->where('active',1);

        if(!empty($post_data['id'])) $query->where('id', $post_data['id']);
        if(!empty($post_data['name']))
        {
            $query->where(function ($query) use($post_data) {
                $query->where('name', 'like', "%{$post_data['name']}%")->orWhere('alias_name', 'like', "%{$post_data['name']}%");
            });

        }
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
            $query->where('item_status', 1);
        }

        if(in_array($me->staff_category, [41,51,61]))
        {
            if($me->staff_position == 31)
            {
                $department_id = $me->department_id;
                $project_list = DK_Pivot__Department_Project::select('project_id')->where('department_id',$department_id)->get();
                $query->whereIn('id',$project_list);

                $query->with([
                    'pivot__project_team'=>function($query) use($me) { $query->where('dk_pivot__team_project.department_id',$me->department_id); }
                ]);
            }
            else if($me->staff_position == 41)
            {
                $team_id = $me->team_id;
                $project_list = DK_Pivot__Team_Project::select('project_id')->where('team_id',$team_id)->get();
                $query->whereIn('id',$project_list);

                $query->with([
                    'pivot__project_staff'=>function($query) use($me) { $query->where('dk_pivot__staff_project.team_id',$me->team_id); }
                ]);
            }
            else return response_error([],"你没有操作权限！");
        }
        else
        {
            $query->with([
                'pivot__project_department__csd',
                'pivot__project_department__qid',
                'pivot__project_department__ad',
                'pivot__project_team',
                'pivot__project_staff'
            ]);
        }


        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
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


    // 【项目】获取 GET
    public function o1__project__item_get($post_data)
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
        if(in_array($me->staff_category,[0,1,9,71]) || in_array($me->staff_position,[31]))
        {
        }
        else return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Project::withTrashed()
            ->with([
                'client_er'=>function($query) { $query->select(['id','name']); },
                'pivot__project_department__csd',
                'pivot__project_department__qid',
                'pivot__project_department__ad',
                'pivot__project_team',
                'pivot__project_staff'
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【项目】获取 GET
    public function o1__project__item_team__get($post_data)
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
        if(in_array($me->staff_category,[0,1,9,71]) || in_array($me->staff_position,[31]))
        {
        }
        else return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Project::withTrashed()
            ->with([
                'pivot__project_team'=>function($query) use($me) { $query->where('dk_pivot__team_project.department_id',$me->department_id); },
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【项目】获取 GET
    public function o1__project__item_staff__get($post_data)
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
        if(in_array($me->staff_category,[0,1,9,71]) || in_array($me->staff_position,[41]))
        {
        }
        else return response_error([],"你没有操作权限！");

        $operate = $post_data["operate"];
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数[ID]有误！");

        $item = DK_Common__Project::withTrashed()
            ->with([
                'pivot__project_staff'=>function($query) use($me) { $query->where('dk_pivot__staff_project.team_id',$me->team_id); },
            ])
            ->find($item_id);
        if(!$item) return response_error([],"不存在警告，请刷新页面重试！");

        return response_success($item,"");
    }
    // 【项目】保存 SAVE
    public function o1__project__item_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'project_category.required' => '请选择项目种类！',
            'name.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'project_category' => 'required',
            'name' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $time = time();
        $date = date('Y-m-d');


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
//                if(!empty($post_data["peoples"]))
//                {
////                    $product->peoples()->attach($post_data["peoples"]);
//                    $current_time = time();
//                    $peoples = $post_data["peoples"];
//                    foreach($peoples as $p)
//                    {
//                        $people_insert[$p] = ['creator_id'=>$me->id,'team_id'=>$me->team_id,'relation_type'=>1,'created_at'=>$time,'updated_at'=>$time];
//                    }
//                    $mine->pivot__project_staff()->sync($people_insert);
////                    $mine->pivot_project_staff()->syncWithoutDetaching($people_insert);
//                }
//                else
//                {
//                    $mine->pivot_project_staff()->detach();
//                }

                // 客服部门
                if(!empty($post_data["CSD_department_list"]))
                {
                    $department_insert = [];
                    $list = $post_data["CSD_department_list"];
                    foreach($list as $v)
                    {
                        $insert = [];
                        $insert['department_category'] = 41;
                        $insert['department_id'] = $v;
                        $insert['created_at'] = $time;
                        $insert['updated_at'] = $time;
                        $department_insert[$v] = $insert;
                    }
//                    dd($department_insert);
                    $mine->pivot__project_department__csd()->sync($department_insert);
//                    $mine->pivot__project_department__csd()->syncWithoutDetaching($department_insert);
                }
                else
                {
//                    $mine->pivot__project_department()->detach(['department_category'=>41]);
                    $mine->pivot__project_department__csd()->detach();
                }

                // 质检部门
                if(!empty($post_data["QID_department_list"]))
                {
                    $department_insert = [];
                    $list = $post_data["QID_department_list"];
                    foreach($list as $v)
                    {
                        $insert = [];
                        $insert['department_category'] = 51;
                        $insert['department_id'] = $v;
                        $insert['created_at'] = $time;
                        $insert['updated_at'] = $time;
                        $department_insert[$v] = $insert;
                    }
                    $mine->pivot__project_department__qid()->sync($department_insert);
//                    $mine->pivot__project_department__csd()->syncWithoutDetaching($department_insert);
                }
                else
                {
//                    $mine->pivot__project_department()->detach(['department_category'=>41]);
                    $mine->pivot__project_department__qid()->detach();
                }

                // 复核部门
                if(!empty($post_data["AD_department_list"]))
                {
                    $department_insert = [];
                    $list = $post_data["AD_department_list"];
                    foreach($list as $v)
                    {
                        $insert = [];
                        $insert['department_category'] = 61;
                        $insert['department_id'] = $v;
                        $insert['created_at'] = $time;
                        $insert['updated_at'] = $time;
                        $department_insert[$v] = $insert;
                    }
//                    dd($department_insert);
                    $mine->pivot__project_department__ad()->sync($department_insert);
//                    $mine->pivot__project_department__ad()->syncWithoutDetaching($department_insert);
                }
                else
                {
//                    $mine->pivot__project_department()->detach(['department_category'=>41]);
                    $mine->pivot__project_department__ad()->detach();
                }



//                if(!empty($post_data["teams"]))
//                {
////                    $product->peoples()->attach($post_data["peoples"]);
//                    $current_time = time();
//                    $teams = $post_data["teams"];
//                    foreach($teams as $t)
//                    {
//                        $team_insert[$t] = ['relation_type'=>1,'created_at'=>$current_time,'updated_at'=>$current_time];
//                    }
//                    $mine->pivot_project_team()->sync($team_insert);
////                    $mine->pivot_project_team()->syncWithoutDetaching($people_insert);
//                }
//                else
//                {
//                    $mine->pivot_project_team()->detach();
//                }
            }
            else throw new Exception("DK_Common__Project--insert--fail");

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
    // 【项目】保存 SAVE
    public function o1__project__item_team_set__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'project_category.required' => '请选择项目种类！',
//            'name.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'project_category' => 'required',
//            'name' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $time = time();
        $date = date('Y-m-d');


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $this->get_me();
        $me = $this->me;

        if(!in_array($me->staff_position,[31])) return response_error([],"你没有操作权限！");

        // 编辑
        $mine = DK_Common__Project::find($operate_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");

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
                // 团队
                if(!empty($post_data["team_list"]))
                {
                    $team_insert = [];
                    $list = $post_data["team_list"];
                    foreach($list as $v)
                    {
                        $insert = [];
                        $insert['department_id'] = $me->department_id;
                        $insert['team_id'] = $v;
                        $insert['created_at'] = $time;
                        $insert['updated_at'] = $time;
                        $team_insert[$v] = $insert;
                    }
                    $mine->pivot__project_team()->wherePivot('department_id',$me->department_id)->sync($team_insert);
//                    $mine->pivot__project_team()->wherePivot(['department_id'=>$me->department_id])->detach();
//                    $mine->pivot__project_team()->syncWithoutDetaching($team_insert);
                }
                else
                {
                    $mine->pivot__project_team()
                        ->wherePivot('department_id',$me->department_id)
                        ->detach();
                }
            }
            else throw new Exception("DK_Common__Project--insert--fail");

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
    // 【项目】保存 SAVE
    public function o1__project__item_staff_set__save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
//            'project_category.required' => '请选择项目种类！',
//            'name.required' => '请输入项目名称！',
//            'name.unique' => '该项目已存在！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
//            'project_category' => 'required',
//            'name' => 'required',
//            'name' => 'required|unique:dk_project,name',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $time = time();
        $date = date('Y-m-d');


        $operate = $post_data["operate"];
        $operate_type = $operate["type"];
        $operate_id = $operate['id'];


        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->staff_position,[41])) return response_error([],"你没有操作权限！");

        // 编辑
        $mine = DK_Common__Project::find($operate_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");

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
                // 员工
                if(!empty($post_data["staff_list"]))
                {
                    $staff_insert = [];
                    $list = $post_data["staff_list"];
                    foreach($list as $v)
                    {
                        $insert = [];
                        $insert['department_id'] = $me->department_id;
                        $insert['team_id'] = $me->team_id;
                        $insert['staff_id'] = $v;
                        $insert['created_at'] = $time;
                        $insert['updated_at'] = $time;
                        $staff_insert[$v] = $insert;
                    }
                    $mine->pivot__project_staff()
                        ->wherePivot('department_id',$me->department_id)
                        ->wherePivot('team_id',$me->team_id)
                        ->sync($staff_insert);
//                    $mine->pivot__project_staff()->wherePivot(['department_id'=>$me->department_id,'team_id'=>$me->team_id])->detach();
//                    $mine->pivot__project_staff()->wherePivot(['department_id'=>$me->department_id,'team_id'=>$me->team_id])->syncWithoutDetaching($staff_insert);
                }
                else
                {
                    $mine->pivot__project_staff()
                        ->wherePivot('department_id',$me->department_id)
                        ->wherePivot('team_id',$me->team_id)
                        ->detach();
                }
            }
            else throw new Exception("DK_Common__Project--insert--fail");

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


    // 【项目】删除
    public function o1__project__item_delete($post_data)
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
        if($operate != 'project--item-delete') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Project::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'project';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 11;
        $record_data["item_id"] = $item_id;
        $record_data["project_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;

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
            if(!$bool) throw new Exception("DK_Common__Project--delete--fail");
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
    // 【项目】恢复
    public function o1__project__item_restore($post_data)
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
        if($operate != 'project--item-restore') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Project::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'project';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 12;
        $record_data["item_id"] = $item_id;
        $record_data["project_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;

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
            if(!$bool) throw new Exception("DK_Common__Project--restore--fail");
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
    // 【项目】彻底删除
    public function o1__project__item_delete_permanently($post_data)
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
        if($operate != 'project--item-delete-permanently') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Project::withTrashed()->find($item_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'project';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 13;
        $record_data["item_id"] = $item_id;
        $record_data["project_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;

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
            if(!$bool) throw new Exception("DK_Common__Project--delete--fail");
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


    // 【项目】启用
    public function o1__project__item_enable($post_data)
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
        if($operate != 'project--item-enable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Project::find($item_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'project';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 21;
        $record_data["item_id"] = $item_id;
        $record_data["project_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'item_status';
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
            $mine->timestamps = false;
            $bool = $mine->save();
            if(!$bool) throw new Exception("DK_Common__Project--update--fail");
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
    // 【项目】禁用
    public function o1__project__item_disable($post_data)
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
        if($operate != 'project--item-disable') return response_error([],"参数【operate】有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数【ID】有误！");

        $this->get_me();
        $me = $this->me;

        // 判断用户操作权限
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"你没有操作权限！");

        // 判断对象是否合法
        $mine = DK_Common__Project::find($item_id);
        if(!$mine) return response_error([],"该【项目】不存在，刷新页面重试！");


        // 记录
        $operation_record_data = [];

        $record_data["operate_object"] = 'staff';
        $record_data["operate_module"] = 'project';
        $record_data["operate_category"] = 1;
        $record_data["operate_type"] = 22;
        $record_data["item_id"] = $item_id;
        $record_data["project_id"] = $item_id;
        $record_data["creator_id"] = $me->id;
        $record_data["creator_company_id"] = $me->company_id;
        $record_data["creator_department_id"] = $me->department_id;
        $record_data["creator_team_id"] = $me->team_id;

        $operation = [];
        $operation['operation'] = $operate;
        $operation['field'] = 'item_status';
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
            if(!$bool) throw new Exception("DK_Common__Project--update--fail");
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


    // 【项目】【操作记录】返回-列表-数据
    public function o1__project__item_operation_record_list__datatable_query($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $id  = $post_data["id"];
        $query = DK_Common__Record__by_Operation::select('*')
            ->with([
                'creator'=>function($query) { $query->select(['id','name']); },
            ])
            ->where(['project_id'=>$id]);

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


}