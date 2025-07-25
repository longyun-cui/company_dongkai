<?php
namespace App\Models\DK_A;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Pool_Task extends Model
{
    use SoftDeletes;
    //
    protected $table = "a_pool_task";
    protected $fillable = [
        'active', 'status',
        'category', 'type', 'form', 'sort',
        'item_active', 'item_status', 'item_category', 'item_type',
        'task_active', 'task_status', 'task_category', 'task_type',

        'owner_id',
        'creator_id',

        'username', 'nickname', 'true_name', 'short_name',
        'name', 'title', 'subtitle', 'content', 'description', 'tag', 'remark', 'label', 'custom',
        'portrait_img_src',
        'cover_pic_src',
        'attachment_name',
        'attachment_src',
        'unique_path',
        'file_path',
        'link_url',

        'project_id',
        'team_id',

        'pool_id',
        'region_name',

        'provinceCode',
        'cityCode',
        'areaCode',

        'extraction_telephone_count',
        'extraction_file_num',
        'extraction_file_size',

        'is_published', 'published_at',
        'is_completed', 'completed_at'
    ];
    protected $dateFormat = 'U';

    protected $hidden = ['content','custom'];

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
////        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','user_id','id');
    }




    // 【一对多】下级部门
    function subordinate_department_list()
    {
        return $this->hasMany('App\Models\DK_CC\DK_CC_Department','superior_department_id','id');
    }

    // 【反向一对多】上级部门
    function superior_department_er()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_Department','superior_department_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK_CC\DK_CC_User','leader_id','id');
    }


    // 【一对多】部门（大区）员工
    function department_district_staff_list()
    {
        return $this->hasMany('App\Models\DK_CC\DK_CC_User','department_district_id','id');
    }
    // 【一对多】部门（大区）员工
    function department_group_staff_list()
    {
        return $this->hasMany('App\Models\DK_CC\DK_CC_User','department_group_id','id');
    }




    // 【多对多】审核人关联的项目
    function pivot_department_user()
    {
        return $this->belongsToMany('App\Models\DK_CC\DK_CC_User','dk_pivot_user_department','department_id','user_id');
//            ->wherePivot('relation_type', 1);
//            ->withTimestamps();
    }


    // 多对多 审核人关联的项目
    function pivot_department_project()
    {
        return $this->belongsToMany('App\Models\DK_CC\DK_CC_Project','dk_pivot_team_project','team_id','project_id');
//            ->wherePivot('relation_type', 1);
//            ->withTimestamps();
    }


    // 工单
    function order_list_for_district()
    {
        return $this->hasMany('App\Models\DK_CC\DK_CC_Order','department_district_id','id');
    }
    // 工单
    function order_list_for_group()
    {
        return $this->hasMany('App\Models\DK_CC\DK_CC_Order','department_group_id','id');
    }




    // 驾驶员
    function driver_er()
    {
        return $this->belongsTo('App\Models\DK\YH_Driver','driver_id','id');
    }




    // 车辆订单
    function car_order_list()
    {
        return $this->hasMany('App\Models\DK\YH_Order','car_id','id');
    }
    // 车挂订单
    function trailer_order_list()
    {
        return $this->hasMany('App\Models\DK\YH_Order','trailer_id','id');
    }
    // 车辆订单【当前】
    function car_order_list_for_current()
    {
        return $this->hasMany('App\Models\DK\YH_Order','car_id','id');
    }
    // 车挂订单【当前】
    function trailer_order_list_for_current()
    {
        return $this->hasMany('App\Models\DK\YH_Order','trailer_id','id');
    }
    // 车辆订单【已完成】
    function car_order_list_for_completed()
    {
        return $this->hasMany('App\Models\DK\YH_Order','car_id','id');
    }
    // 车挂订单【已完成】
    function trailer_order_list_for_completed()
    {
        return $this->hasMany('App\Models\DK\YH_Order','trailer_id','id');
    }
    // 车辆订单【未来】
    function car_order_list_for_future()
    {
        return $this->hasMany('App\Models\DK\YH_Order','car_id','id');
    }
    // 车挂订单【未来】
    function trailer_order_list_for_future()
    {
        return $this->hasMany('App\Models\DK\YH_Order','trailer_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\DK\YH_Attachment','item_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\DK\YH_Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\DK\YH_Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\DK\YH_Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\DK\YH_Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\DK_CC\DK_CC_User','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\DK\YH_Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\DK\YH_Menu','pivot_menu_item','item_id','menu_id');
    }


    /**
     * 获得此文章的所有评论。
     */
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'itemable');
    }

    /**
     * 获得此文章的所有标签。
     */
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }
}
