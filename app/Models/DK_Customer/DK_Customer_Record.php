<?php
namespace App\Models\DK_Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Customer_Record extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_customer_record";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'sort',
        'record_active', 'record_status', 'record_object', 'record_category', 'record_type', 'record_module',
        'operate_object', 'operate_category', 'operate_type',
        'owner_active',
        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'org_id', 'admin_id',
        'item_id', 'order_id',
        'column', 'column_type', 'column_name',
        'before', 'after',
        'before_id', 'after_id',
        'before_client_id', 'after_client_id',
        'before_project_id', 'after_project_id',
        'before_empty_route_id', 'after_empty_route_id',
        'before_car_id', 'after_car_id',
        'before_driver_id', 'after_driver_id',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'time_point', 'time_type', 'start_time', 'end_time', 'address',
        'ip',
        'visit_num', 'share_num', 'favor_num', 'comment_num',
        'published_at'
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
        return $this->belongsTo('App\Models\DK\DK_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_User','creator_id','id');
    }
    // 创作者（客户）
    function client_creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Client','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_User','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK\DK_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }


    // 客户
    function before_client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Client','before_client_id','id');
    }
    // 客户
    function after_client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Client','after_client_id','id');
    }
    // 项目
    function before_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','before_project_id','id');
    }
    // 项目
    function after_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','after_project_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\YH\YH_Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\Dk\DK_User','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\YH\YH_Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\YH\YH_Menu','pivot_menu_item','item_id','menu_id');
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
