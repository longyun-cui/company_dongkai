<?php
namespace App\Repositories\DK;

use App\Models\DK\DK_User;
use App\Models\DK\YH_Item;
use App\Models\DK\YH_Task;
use App\Models\DK\YH_Pivot_Item_Relation;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade;
use QrCode, Excel;

class DKStaffRepository {

    private $evn;
    private $auth_check;
    private $me;
    private $me_admin;
    private $model;
    private $modelUser;
    private $modelItem;
    private $repo;
    private $service;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_User;
        $this->modelItem = new YH_Item;

        $this->view_blade_404 = env('TEMPLATE_YH_STAFF').'errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("yh_staff")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("yh_staff")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);
    }




    // 返回（后台）主页视图
    public function view_staff_index()
    {
        $this->get_me();
        $me = $this->me;

        $menu_active = 'menu_root_active';

//        $item_query = YH_Item::with(['owner','creator','updater','completer']);
        $item_query = YH_Task::with(['owner','creator','completer']);
        $item_query->where(['item_status'=>1]);
//        $item_query->where(['item_category'=>11]);
        $item_query->where(['owner_id'=>$me->id]);

//        if($me->user_type == 0)
//        {
//            $item_query->withTrashed();
//            $item_query->orderByDesc('updated_at');
//        }
//        else if($me->user_type == 1)
//        {
//            $item_query->orderByDesc('updated_at');
//
//        }
//        else if($me->user_type == 11)
//        {
//
//            $item_query->orderByDesc('updated_at');
//        }
//        else if($me->user_type == 19)
//        {
//            $item_query->orderByDesc('updated_at');
//
//        }
//        else if($me->user_type == 41)
//        {
//            $item_query->where(['item_active'=>1]);
//            $item_query->orderByDesc('published_at')->orderByDesc('updated_at');
//
//        }
//        else
//        {
//        }


        $condition = request()->all();
//
        $task_list_type = request('task-list-type','root');
        if($task_list_type == 'root')
        {
            $condition['task-list-type'] = 'unfinished';
            $parameter_result = http_build_query($condition);
            return redirect('/?'.$parameter_result);
        }
        else if($task_list_type == 'all')
        {
            $item_query->whereIn('item_active',[1]);
            $menu_active = 'menu_active_of_all';
        }
        else if($task_list_type == 'unfinished')
        {
//            $item_query->whereIn('item_active',[1]);
//            $item_query->whereIn('item_status',[1]);
            $item_query->whereNotIn('is_completed',[1]);
            $return['head_title'] = "未完成任务";
            $return['menu_active_of_unfinished'] = 'active';
        }
        else if($task_list_type == 'finished')
        {
//            $item_query->whereIn('item_active',[1]);
//            $item_query->whereIn('item_status',[1]);
            $item_query->where('is_completed',1);
            $item_query->orderByDesc('completed_at');

            $return['head_title'] = "已完成任务";
            $return['menu_active_of_finished'] = 'active';
        }
        else if($task_list_type == 'missed')
        {
            $item_query->where('is_completed',1);
            $item_query->where('item_result',71);

            $return['head_title'] = "未接";
            $return['custom_menu_title'] = "未接";
            $return['menu_active_of_custom'] = 'active';
        }
        else if($task_list_type == 'reject')
        {
            $item_query->where('is_completed',1);
            $item_query->where('item_result',72);
            $item_query->orderByDesc('completed_at');

            $return['head_title'] = "拒接";
            $return['custom_menu_title'] = "拒接";
            $return['menu_active_of_custom'] = 'active';
        }
        else if($task_list_type == 'added')
        {
            $item_query->where('is_completed',1);
            $item_query->where('item_result',19);
            $item_query->orderByDesc('completed_at');

            $return['head_title'] = "已加微信";
            $return['custom_menu_title'] = "已加微信";
            $return['menu_active_of_custom'] = 'active';
        }
        else if($task_list_type == 'remark')
        {
            $item_query->where('is_completed',1);
            $item_query->whereNotNull('remark');
            $item_query->orderByDesc('completed_at');

            $return['head_title'] = "有备注";
            $return['custom_menu_title'] = "有备注";
            $return['menu_active_of_custom'] = 'active';
        }
//        else
//        {
//        }

//        $item_list = $item_query->orderByDesc('published_at')->orderByDesc('updated_at')->paginate(20);
        $item_list = $item_query->paginate(20);
//        dd($item_list->toArray());
        foreach ($item_list as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $return['condition'] = $condition;
        $return['item_list'] = $item_list;
        $view_blade = env('TEMPLATE_YH_STAFF').'entrance.index';
        return view($view_blade)->with($return);
    }


    // 【内容列表】返回-列表-视图
    public function view_item_list($post_data)
    {
        $this->get_me();
        $me = $this->me;

        $menu_active = 'menu_root_active';

//        $item_query = YH_Item::with(['owner','creator','updater','completer']);
        $item_query = YH_Item::with(['owner','creator']);
        $item_query->where(['item_status'=>1]);
        $item_query->where(['item_category'=>11]);


        $condition = request()->all();
//
        $item_list_type = request('item-list-type','root');
        if($item_list_type == 'root')
        {
//            $condition['item-list-type'] = 'all';
//            $parameter_result = http_build_query($condition);
//            return redirect('/?'.$parameter_result);

            $return['head_title'] = "全部内容";
            $return['menu_active_of_all'] = 'active';
        }
        else if($item_list_type == 'all')
        {
//            $item_query->whereIn('item_type',[1,11,21,41,42,99,101]);

            $return['head_title'] = "全部内容";
            $return['menu_active_of_all'] = 'active';
        }
        else if($item_list_type == 'production')
        {
            $item_query->where('item_type',11);

            $return['head_title'] = "产品";
            $return['menu_active_of_production'] = 'active';
        }
        else if($item_list_type == 'training')
        {
            $item_query->where('item_type',41);

            $return['head_title'] = "培训";
            $return['menu_active_of_training'] = 'active';
        }
        else if($item_list_type == 'notice')
        {
            $item_query->where('item_type',99);

            $return['head_title'] = "公告";
            $return['menu_active_of_notice'] = 'active';
        }
        else
        {
        }

        $item_list = $item_query->orderByDesc('published_at')->orderByDesc('updated_at')->paginate(20);
//        dd($item_list->toArray());
        foreach ($item_list as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $return['condition'] = $condition;
        $return['item_list'] = $item_list;
        $view_blade = env('TEMPLATE_YH_STAFF').'entrance.item.item-list';
        return view($view_blade)->with($return);
    }
    // 【内容详情】
    public function view_item($post_data,$id=0)
    {
        $this->get_me();
        $me = $this->me;

        $item = YH_Item::with(['owner','creator'])->find($id);
        if($item)
        {

            $item->timestamps = false;
            $item->increment('visit_num');

//            if($item->item_category != 11)
//            {
//                $error["text"] = '该内容拒绝访问！';
//                return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
//            }
//
//            if($item->item_status != 1)
//            {
//                $error["text"] = '该内容被禁啦！';
//                return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
//            }

//            if($item->owner)
//            {
//                if($item->owner->user_category != 1)
//                {
//                    $error["text"] = '该内容用户有误！';
//                    return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
//                }
//                if($item->owner->user_status != 1)
//                {
//                    $error["text"] = '该内容用户被禁啦！';
//                    return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
//                }
//            }
//            else
//            {
//                $error["text"] = '作者有误！';
//                return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
//            }

            $item->custom_decode = json_decode($item->custom);
        }
        else
        {
            $error["text"] = '内容不存在或者被删除了！';
            return view(env('TEMPLATE_YH_STAFF').'errors.404')->with('error',$error);
        }


        $return['getType'] = 'item';
        $return['item'] = $item;

        $view_blade = env('TEMPLATE_YH_STAFF').'entrance.item.item';
        return view($view_blade)->with($return);
    }




    /*
     * 用户基本信息 - 模块
     */
    // 【基本信息】返回视图
    public function view_my_profile_info_index()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.my-account.my-profile-info-index';
        return view($view_blade)->with($return);
    }

    // 【基本信息】返回-编辑-视图
    public function view_my_profile_info_edit()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.my-account.my-profile-info-edit';
        return view($view_blade)->with($return);
    }
    // 【基本信息】保存数据
    public function operate_my_profile_info_save($post_data)
    {
        $this->get_me();
        $me = $this->me;

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
            $bool = $me->fill($mine_data)->save();
            if($bool)
            {
                // 头像
                if(!empty($post_data["portrait"]))
                {
                    // 删除原文件
                    $mine_portrait_img = $me->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_resource_path($mine_portrait_img)))
                    {
                        unlink(storage_resource_path($mine_portrait_img));
                    }

                    $result = upload_img_storage($post_data["portrait"],'portrait_for_user_by_user_'.$me->id,'zy/unique/portrait/','assign');
                    if($result["result"])
                    {
                        $me->portrait_img = $result["local"];
                        $me->save();
                    }
                    else throw new Exception("upload--portrait_img--file--fail");
                }

            }
            else throw new Exception("insert--item--fail");

            DB::commit();
            return response_success(['id'=>$me->id]);
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

    // 【密码】返回修改视图
    public function view_my_account_password_change()
    {
        $this->get_me();
        $me = $this->me;

        $return['data'] = $me;

        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.my-account.my-account-password-change';
        return view($view_blade)->with($return);
    }
    // 【密码】保存数据
    public function operate_my_account_password_change_save($post_data)
    {
        $messages = [
            'password_pre.required' => '请输入旧密码',
            'password_new.required' => '请输入新密码',
            'password_confirm.required' => '请输入确认密码',
        ];
        $v = Validator::make($post_data, [
            'password_pre' => 'required',
            'password_new' => 'required',
            'password_confirm' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $password_pre = request()->get('password_pre');
        $password_new = request()->get('password_new');
        $password_confirm = request()->get('password_confirm');

        if($password_new == $password_confirm)
        {
            $this->get_me();
            $me = $this->me;

            if(password_check($password_pre,$me->password))
            {
                $me->password = password_encode($password_new);
                $bool = $me->save();
                if($bool) return response_success([], '密码修改成功！');
                else return response_fail([], '密码修改失败！');
            }
            else
            {
                return response_fail([], '原密码有误！');
            }
        }
        else return response_error([],'两次密码输入不一致！');
    }








    /*
     * 用户管理
     */
    // 【用户】【组织】返回-添加-视图
    public function view_user_staff_create()
    {
        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.user.staff-edit';

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return view($view_template.'errors.403');

        $item_type = 'user';
        $item_type_text = '员工';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list';

        $return['me'] = $me;
        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
//        $return['item_type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;
        $return['sidebar_menu_staff_create_active'] = 'active';

        return view($view_blade)->with($return);
    }
    // 【用户】【组织】返回-编辑-视图
    public function view_user_staff_edit()
    {
        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.user.staff-edit';

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return view($view_template.'errors.403');

        $id = request("user-id",0);

        $item_type = 'item';
        $item_type_text = '员工';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/user/staff-list';

        $return['me'] = $me;
        $return['operate_id'] = $id;
        $return['category'] = 'item';
//        $return['item_type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_list'] = $list_link;
        $return['sidebar_menu_staff_create_active'] = 'active';

        if($id == 0)
        {
            $return['operate'] = 'create';
        }
        else
        {
            $mine = User::with(['parent'])->find($id);
            if($mine)
            {
//                if(!in_array($mine->user_type,[11,88])) return view(env('TEMPLATE_YH_STAFF').'errors.404');
                $mine->custom = json_decode($mine->custom);
                $mine->custom2 = json_decode($mine->custom2);
                $mine->custom3 = json_decode($mine->custom3);

                $return['operate'] = 'edit';
                $return['data'] = $mine;
            }
            else return view($view_template.'errors.404');
        }

        return view($view_blade)->with($return);
    }
    // 【用户】【组织】保存数据
    public function operate_user_staff_save($post_data)

    {
//        dd($post_data);
        $messages = [
            'operate.required' => '参数有误',
            'username.required' => '请输入用户名',
            'mobile.required' => '请输入电话',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'username' => 'required',
            'mobile' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }


        $me = Auth::guard('staff')->user();
//        if(!in_array($me->user_category,[0])) return response_error([],"你没有操作权限！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];

        if($operate == 'create') // 添加 ( $id==0，添加一个新用户 )
        {
            $mine = new User;
            $post_data["user_category"] = 1;
            $post_data["active"] = 1;
            $post_data["password"] = password_encode("abcd1234");
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = User::find($operate_id);
            if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
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
            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {

                // 头像
                if(!empty($post_data["portrait"]))
                {
                    $mine_portrait_img = $mine->portrait_img;
                    if(!empty($mine_portrait_img) && file_exists(storage_path("resource/" . $mine_portrait_img)))
                    {
                        unlink(storage_path("resource/" . $mine_portrait_img));
                    }

//                    $result = upload_storage($post_data["portrait"]);
//                    $result = upload_storage($post_data["portrait"], null, null, 'assign');
                    $result = upload_img_storage($post_data["portrait"],'user_'.$mine->id,'staff/unique/portrait/','assign');
                    if($result["result"])
                    {
                        $mine->portrait_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--portrait--fail");
                }
                else
                {
                    if($operate == 'create')
                    {

                        copy(storage_path("resource/unique/portrait/user0.jpeg"), storage_path("resource/staff/unique/portrait/user_".$mine->id.".jpeg"));
                        $mine->portrait_img = "staff/unique/portrait/user_".$mine->id.".jpeg";
                        $mine->save();
                    }
                }

                // 微信二维码
                if(!empty($post_data["wx_qr_code"]))
                {
                    // 删除原图片
                    $mine_wx_qr_code_img = $mine->wechat_qr_code_img;
                    if(!empty($mine_wx_qr_code_img) && file_exists(storage_path("resource/" . $mine_wx_qr_code_img)))
                    {
                        unlink(storage_path("resource/" . $mine_wx_qr_code_img));
                    }

                    $result = upload_storage($post_data["wx_qr_code"]);
                    if($result["result"])
                    {
                        $mine->wx_qr_code_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--wx_qr_code--fail");
                }

                // 联系人微信二维码
                if(!empty($post_data["linkman_wx_qr_code"]))
                {
                    // 删除原图片
                    $mine_linkman_wx_qr_code_img = $mine->linkman_wx_qr_code_img;
                    if(!empty($mine_linkman_wx_qr_code_img) && file_exists(storage_path("resource/" . $mine_linkman_wx_qr_code_img)))
                    {
                        unlink(storage_path("resource/" . $mine_linkman_wx_qr_code_img));
                    }

                    $result = upload_storage($post_data["linkman_wx_qr_code"]);
                    if($result["result"])
                    {
                        $mine->linkman_wx_qr_code_img = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--linkman_wx_qr_code--fail");
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


    // 【用户】【全部】返回-列表-视图
    public function view_user_staff_list($post_data)
    {
        $item_type = 'item-list';
        $item_type_text = '员工';
        $title_text = $item_type_text.'列表';
        $list_text = $item_type_text.'列表';
        $list_link = '/admin/user/user-list-for-all';
        $menu_active = 'sidebar_menu_staff_list_active';

        $user_list = User::withTrashed()->with([
//            'ad',
            ])->withCount([
//            'items as article_count' => function($query) { $query->where(['item_category'=>1,'item_type'=>1]); },
//            'items as activity_count' => function($query) { $query->where(['item_category'=>1,'item_type'=>11]); },
            ])
//            ->where('user_category',1)
            ->where('user_type','>',0)
//            ->where('user_status',1)
//            ->where('active',1)
            ->orderByDesc('id')
            ->paginate(20);

        $return['user_list'] = $user_list;
        $return['title_text'] = $title_text;
        return view(env('TEMPLATE_YH_STAFF').'entrance.user.staff-list')->with($return);
    }
    // 【用户】【全部】返回-列表-数据
    public function get_user_staff_list_datatable($post_data)
    {
        $me = Auth::guard("staff")->user();
        $query = YH_Item::select('*')->withTrashed()
            ->with(['owner','creator'])
            ->where('owner_id','>=',1)
            ->where(['owner_id'=>100,'item_category'=>100])
            ->where('item_type','!=',0);

        if(!empty($post_data['name'])) $query->where('name', 'like', "%{$post_data['name']}%");
        if(!empty($post_data['title'])) $query->where('title', 'like', "%{$post_data['title']}%");
        if(!empty($post_data['tag'])) $query->where('tag', 'like', "%{$post_data['tag']}%");
        if(!empty($post_data['major'])) $query->where('major', 'like', "%{$post_data['major']}%");
        if(!empty($post_data['nation'])) $query->where('nation', 'like', "%{$post_data['nation']}%");

        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("updated_at", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
            $list[$k]->description = replace_blank($v->description);
        }
//        dd($list->toArray());
        return datatable_response($list, $draw, $total);
    }


    // 【用户】获取详情
    public function operate_user_staff_get($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-get') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if($item->owner_id != $me->id) return response_error([],"你没有操作权限！");

        return response_success($item,"");

    }
    // 【用户】删除
    public function operate_user_staff_delete($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'user_id.required' => '请输入ID！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-delete') return response_error([],"参数operate有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $mine = User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");
        if(in_array($mine->user_type,[0,1,9,11])) return response_error([],"该用户不可删除！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->delete();
            if(!$bool) throw new Exception("user--delete--fail");
            DB::commit();

            $user_html = $this->get_the_user_html($mine);
            return response_success(['user_html'=>$user_html]);
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
    // 【用户】恢复
    public function operate_user_staff_restore($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'user_id.required' => '请输入ID！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-restore') return response_error([],"参数operate有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $mine = User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该用户不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $mine->timestamps = false;
            $bool = $mine->restore();
            if(!$bool) throw new Exception("item--restore--fail");
            DB::commit();

            $user_html = $this->get_the_user_html($mine);
            return response_success(['user_html'=>$user_html]);
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
    // 【用户】彻底删除
    public function operate_user_staff_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'user_id.required' => '请输入ID！',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'user_id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'user-delete-permanently') return response_error([],"参数operate有误！");
        $id = $post_data["user_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $mine = User::withTrashed()->find($id);
        if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $bool = $mine->forceDelete();
            if(!$bool) throw new Exception("item--delete--fail");
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





    /*
     * 用户系统
     */
    // 【用户】【修改密码】
    public function operate_user_change_password($post_data)
    {
        $messages = [
            'operate.required' => '参数有误',
            'id.required' => '请输入用户ID',
            'user-password.required' => '请输入密码',
            'user-password-confirm.required' => '请输入确认密码',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'id' => 'required',
            'user-password' => 'required',
            'user-password-confirm' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'change-password') return response_error([],"参数有误！");
        $id = $post_data["id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $me = Auth::guard('atom')->user();
        if($me->user_type != 0) return response_error([],"你没有操作权限");

        $password = $post_data["user-password"];
        $confirm = $post_data["user-password-confirm"];
        if($password != $confirm) return response_error([],"两次密码不一致！");

//        if(!password_is_legal($password)) ;
        $pattern = '/^[a-zA-Z0-9]{1}[a-zA-Z0-9]{5,19}$/i';
        if(!preg_match($pattern,$password)) return response_error([],"密码格式不正确！");


        $user = Doc_User::find($id);
        if(!$user) return response_error([],"该用户不存在，刷新页面重试");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $user->password = password_encode($password);
            $user->save();

            $bool = $user->save();
            if(!$bool) throw new Exception("update--user--fail");

            DB::commit();
            return response_success(['id'=>$user->id]);
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


    // 【select2】
    public function operate_item_select2_people($post_data)
    {
        $query = YH_Item::select(['id','name as text'])->where(['item_category'=>100,'item_type'=>11]);
        if(!empty($post_data['keyword']))
        {
            $keyword = "%{$post_data['keyword']}%";
            $query->where('name','like',"%$keyword%");
        }
        $list = $query->get()->toArray();
        return $list;
    }








    /*
     * 任务管理
     */
    // 【任务】返回-添加-视图
    public function view_item_task_create()
    {
        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.item.task-edit';

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11,19])) return view($view_template.'errors.403');

        $item_type = 'item';
        $item_type_text = '任务';
        $title_text = '添加'.$item_type_text;
        $list_text = $item_type_text;
        $list_link = '/item/item-list-for-'.$item_type;

        $return['operate'] = 'create';
        $return['operate_id'] = 0;
        $return['category'] = 'item';
        $return['type'] = $item_type;
//        $return['item_type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;
        $return['sidebar_menu_task_create_active'] = 'active';

        return view($view_blade)->with($return);
    }
    // 【任务】返回-编辑-视图
    public function view_item_task_edit()
    {
        $view_template = env('TEMPLATE_YH_STAFF');
        $view_blade = $view_template.'entrance.item.task-edit';

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11,19])) return view($view_template.'errors.403');

        $item_id = request("item-id",0);
        $mine = YH_Item::with([])->withTrashed()->find($item_id);
        if(!$mine) return view($view_template.'errors.404');
        if($mine->creator_id != $me->id) return view($view_template.'errors.403');

        $item_type = 'item';
        $item_type_text = '任务';
        $title_text = '编辑'.$item_type_text;
        $list_text = $item_type_text.'列表';
        $list_link = '/item/task-list';

        $return['operate_id'] = $item_id;
        $return['category'] = 'item';
        $return['type'] = $item_type;
//        $return['item_type'] = $item_type;
        $return['item_type_text'] = $item_type_text;
        $return['title_text'] = $title_text;
        $return['list_text'] = $list_text;
        $return['list_link'] = $list_link;
        $return['sidebar_menu_staff_create_active'] = 'active';

        if($item_id == 0)
        {
            $return['operate'] = 'create';
        }
        else
        {
            $mine->custom = json_decode($mine->custom);
            $mine->custom2 = json_decode($mine->custom2);
            $mine->custom3 = json_decode($mine->custom3);

            $return['operate'] = 'edit';
            $return['data'] = $mine;
        }

        return view($view_blade)->with($return);
    }
    // 【任务】保存-数据
    public function operate_item_task_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");


        $operate = $post_data["operate"];
        $operate_id = $post_data["operate_id"];
        $type = $post_data["type"];

        if($operate == 'create') // 添加 ( $id==0，添加一个内容 )
        {
            $mine = new YH_Item;
            $post_data["item_active"]  = isset($post_data['item_active'])  ? $post_data['item_active']  : 0;
            $post_data["item_category"] = 11;
            $post_data["owner_id"] = 100;
            $post_data["creator_id"] = $me->id;
//            if($type == 'object') $post_data["item_type"] = 1;
//            else if($type == 'people') $post_data["item_type"] = 11;
//            else if($type == 'product') $post_data["item_type"] = 22;
//            else if($type == 'event') $post_data["item_type"] = 33;
//            else if($type == 'conception') $post_data["item_type"] = 91;
        }
        else if($operate == 'edit') // 编辑
        {
            $mine = YH_Item::withTrashed()->find($operate_id);
            if(!$mine) return response_error([],"该内容不存在，刷新页面重试！");
            $post_data["updater_id"] = $me->id;
        }
        else return response_error([],"参数【operate】有误！");

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

            if(!empty($post_data['start'])) {
                $mine_data['time_type'] = 1;
                $mine_data['start_time'] = strtotime($post_data['start']);
            }

            if(!empty($post_data['end'])) {
                $mine_data['time_type'] = 1;
                $mine_data['end_time'] = strtotime($post_data['end']);
            }

            $bool = $mine->fill($mine_data)->save();
            if($bool)
            {

                // 封面图片
                if(!empty($post_data["cover"]))
                {
                    // 删除原封面图片
                    $mine_cover_pic = $mine->cover_pic;
                    if(!empty($mine_cover_pic) && file_exists(storage_path("resource/" . $mine_cover_pic)))
                    {
                        unlink(storage_path("resource/" . $mine_cover_pic));
                    }

                    $result = upload_storage($post_data["cover"],'','doc/common');
                    if($result["result"])
                    {
                        $mine->cover_pic = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--cover_pic--fail");
                }

                // 附件
                if(!empty($post_data["attachment"]))
                {
                    // 删除原附件
                    $mine_attachment_src = $mine->attachment;
                    if(!empty($mine_attachment_src) && file_exists(storage_path("resource/" . $mine_attachment_src)))
                    {
                        unlink(storage_path("resource/" . $mine_attachment_src));
                    }

                    $result = upload_file_storage($post_data["attachment"],'','staff/attachment');
                    if($result["result"])
                    {
                        $mine->attachment_name = $result["name"];
                        $mine->attachment_src = $result["local"];
                        $mine->save();
                    }
                    else throw new Exception("upload--attachment_file--fail");
                }

                // 生成二维码
                $qr_code_path = "resource/staff/unique/qr_code/";  // 保存目录
                if(!file_exists(storage_path($qr_code_path)))
                    mkdir(storage_path($qr_code_path), 0777, true);
                // qr_code 图片文件
                $url = env('DOMAIN_STAFF').'/item/'.$mine->id;  // 目标 URL
                $filename = 'qr_code_staff_item_'.$mine->id.'.png';  // 目标 file
                $qr_code = $qr_code_path.$filename;
                QrCode::errorCorrection('H')->format('png')->size(640)->margin(0)->encoding('UTF-8')->generate($url,storage_path($qr_code));

            }
            else throw new Exception("insert--item--fail");

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


    // 【任务】获取详情
    public function operate_item_task_get($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-get') return response_error([],"参数[operate]有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数[ID]有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if($item->owner_id != $me->id) return response_error([],"你没有操作权限！");

        return response_success($item,"");

    }
    // 【任务】删除
    public function operate_item_task_delete($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-delete') return response_error([],"参数operate有误！");
        $item_id = $post_data["item_id"];
        if(intval($item_id) !== 0 && !$item_id) return response_error([],"参数ID有误！");

        $item = YH_Item::withTrashed()->find($item_id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            if($me->user_type == 19 && $item->item_active == 0 && $item->creator_id != $me->id)
            {
                $item_copy = $item;

                $bool = $item->forceDelete();
                if(!$bool) throw new Exception("item--delete--fail");
                DB::commit();

                $this->delete_the_item_files($item_copy);
            }
            else
            {
                $bool = $item->delete();
                if(!$bool) throw new Exception("item--delete--fail");
                DB::commit();
            }

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
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
    // 【任务】恢复
    public function operate_item_task_restore($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-restore') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $bool = $item->restore();
            if(!$bool) throw new Exception("item--restore--fail");
            DB::commit();

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
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
    // 【任务】彻底删除
    public function operate_item_task_delete_permanently($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-delete-permanently') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if(!in_array($me->user_type,[0,1,9,11,19])) return response_error([],"用户类型错误！");
        if($me->user_type == 19 && ($item->item_active != 0 || $item->creator_id != $me->id)) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item_copy = $item;

            $bool = $item->forceDelete();
            if(!$bool) throw new Exception("item--delete--fail");
            DB::commit();

            $this->delete_the_item_files($item_copy);

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
    // 【任务】发布
    public function operate_item_task_publish($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-publish') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('staff')->user();
        if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_active = 1;
            $item->published_at = time();
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            DB::commit();

            $item_html = $this->get_the_item_html($item);
            return response_success(['item_html'=>$item_html]);
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
    // 【任务】完成
    public function operate_item_task_complete($post_data)
    {
        $messages = [
            'operate.required' => '参数有误！',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-complete') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Task::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,41,61,88])) return response_error([],"用户类型错误！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_result = $post_data["result"];
            $item->is_completed = 1;
            $item->completer_id = $me->id;
            $item->completed_at = time();
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("item--update--fail");
            DB::commit();

            $item->custom = json_decode($item->custom);
            $item_array[0] = $item;
            $return['item_list'] = $item_array;
            $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list-for-task')->with($return)->__toString();
            return response_success(['item_html'=>$item_html]);
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

    // 【任务-备注】保存-数据
    public function operate_item_task_remark_save($post_data)
    {
        $messages = [
            'operate.required' => 'operate.required.',
            'item_id.required' => '请输入ID！',
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
        if($operate != 'item-remark-save') return response_error([],"参数operate有误！");
        $id = $post_data["item_id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Task::withTrashed()->find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $this->get_me();
        $me = $this->me;
        if(!in_array($me->user_type,[0,1,9,11,19,21,22,41,61,88])) return response_error([],"用户类型错误！");
//        if($item->creator_id != $me->id) return response_error([],"你没有操作权限！");


        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->timestamps = false;
            $item->remark = $post_data['content'];

            $bool = $item->save();
            if(!$bool) throw new Exception("update--item-remark--fail");
            DB::commit();

            $item_html = $this->get_the_task_html($item);
            return response_success(['item_html'=>$item_html]);
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




    // 【ITEM】管理员封禁
    public function operate_item_admin_disable($post_data)
    {
        $messages = [
            'operate.required' => '参数有误',
            'id.required' => '请输入关键词ID',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-admin-disable') return response_error([],"参数有误！");
        $id = $post_data["id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('atom')->user();
        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 9;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--item--fail");

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
    // 【ITEM】管理员解禁
    public function operate_item_admin_enable($post_data)
    {
        $messages = [
            'operate.required' => '参数有误',
            'id.required' => '请输入关键词ID',
        ];
        $v = Validator::make($post_data, [
            'operate' => 'required',
            'id' => 'required',
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $operate = $post_data["operate"];
        if($operate != 'item-admin-enable') return response_error([],"参数有误！");
        $id = $post_data["id"];
        if(intval($id) !== 0 && !$id) return response_error([],"参数ID有误！");

        $item = YH_Item::find($id);
        if(!$item) return response_error([],"该内容不存在，刷新页面重试！");

        $me = Auth::guard('atom')->user();
        if($me->user_category != 0) return response_error([],"你没有操作权限！");

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $item->item_status = 1;
            $item->timestamps = false;
            $bool = $item->save();
            if(!$bool) throw new Exception("update--item--fail");

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








    // 【内容】返回-内容-HTML
    public function get_the_user_html($item)
    {
        $item->custom = json_decode($item->custom);
        $user_array[0] = $item;
        $return['user_list'] = $user_array;

        // method A
        $item_html = view(env('TEMPLATE_YH_STAFF').'component.user-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】返回-内容-HTML
    public function get_the_item_html($item)
    {
        $item->custom = json_decode($item->custom);
        $item_array[0] = $item;
        $return['item_list'] = $item_array;

        // method A
        $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】返回-内容-HTML
    public function get_the_task_html($item)
    {
        $item->custom = json_decode($item->custom);
        $item_array[0] = $item;
        $return['item_list'] = $item_array;

        // method A
        $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list-for-task')->with($return)->__toString();
//        // method B
//        $item_html = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return)->render();
//        // method C
//        $view = view(env('TEMPLATE_YH_STAFF').'component.item-list')->with($return);
//        $item_html=response($view)->getContent();

        return $item_html;
    }

    // 【内容】删除-内容-附属文件
    public function delete_the_item_files($item)
    {
        $mine_id = $item->id;
        $mine_cover_pic = $item->cover_pic;
        $mine_attachment_src = $item->attachment_src;
        $mine_content = $item->content;

        // 删除二维码
        if(file_exists(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png')))
        {
            unlink(storage_path("resource/unique/qr_code/".'qr_code_item_'.$mine_id.'.png'));
        }

        // 删除原封面图片
        if(!empty($mine_cover_pic) && file_exists(storage_path("resource/" . $mine_cover_pic)))
        {
            unlink(storage_path("resource/" . $mine_cover_pic));
        }

        // 删除原附件
        if(!empty($mine_attachment_src) && file_exists(storage_path("resource/" . $mine_attachment_src)))
        {
            unlink(storage_path("resource/" . $mine_attachment_src));
        }

        // 删除UEditor图片
        $img_tags = get_html_img($mine_content);
        foreach ($img_tags[2] as $img)
        {
            if (!empty($img) && file_exists(public_path($img)))
            {
                unlink(public_path($img));
            }
        }
    }










    /*
     * Statistic 流量统计
     */

    // 【】流量统计
    public function view_statistic_index()
    {
        $this->get_me();
        $me = $this->me;
        $me_id = $me->id;

        $this_month = date('Y-m');
        $this_month_year = date('Y');
        $this_month_month = date('m');
        $last_month = date('Y-m',strtotime('last month'));
        $last_month_year = date('Y',strtotime('last month'));
        $last_month_month = date('m',strtotime('last month'));


        // 电话量
        $query = YH_TASK::select(
            DB::raw("DATE(FROM_UNIXTIME(created_at)) as date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%Y-%m') as month"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%c') as month_0"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(completed_at),'%e') as day"),
            DB::raw('count(*) as count')
        )
            ->groupBy(DB::raw("DATE(FROM_UNIXTIME(completed_at))"))
            ->whereYear(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_year)
            ->whereMonth(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),$this_month_month)
            ->where(['is_completed'=>1,'owner_id'=>$me_id]);

        $all = $query->get()->keyBy('day');
        $dialog = $query->whereIn('item_result',[1,19,51])->get()->keyBy('day');
        $plus_wx = $query->where('item_result',19)->get()->keyBy('day');




        // 总转化率【占比】
        $all_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$me_id])
            ->get();
        foreach($all_rate as $k => $v)
        {
            if($v->item_result == 0) $all_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $all_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $all_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $all_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $all_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $all_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $all_rate[$k]->name = "空号";
            else $all_rate[$k]->name = "其他";
        }


        // 今日转化率【占比】
        $today_rate = YH_TASK::select('item_result',DB::raw('count(*) as count'))
            ->groupBy('item_result')
            ->where(['is_completed'=>1,'owner_id'=>$me_id])
            ->whereDate(DB::raw("DATE(FROM_UNIXTIME(completed_at))"),date('Y-m-d'))
            ->get();
        foreach($today_rate as $k => $v)
        {
            if($v->item_result == 0) $today_rate[$k]->name = "未选择";
            else if($v->item_result == 1) $today_rate[$k]->name = "通话";
            else if($v->item_result == 19)  $today_rate[$k]->name = "加微信";
            else if($v->item_result == 71)  $today_rate[$k]->name = "未接";
            else if($v->item_result == 72)  $today_rate[$k]->name = "拒接";
            else if($v->item_result == 51)  $today_rate[$k]->name = "打错了";
            else if($v->item_result == 99)  $today_rate[$k]->name = "空号";
            else $today_rate[$k]->name = "其他";
        }


        $view_data["all"] = $all;
        $view_data["dialog"] = $dialog;
        $view_data["plus_wx"] = $plus_wx;
        $view_data["all_rate"] = $all_rate;
        $view_data["today_rate"] = $today_rate;

        $view_blade = env('TEMPLATE_YH_STAFF').'entrance.statistic.statistic-index';
        return view($view_blade)->with($view_data);
    }





}