<?php
namespace App\Models\DK\DK_Staff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Staff__Record__by_Visit extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_staff__record__by_visit";
    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type',  'item_group',
        'record_active', 'record_status', 'record_result',
        'record_category', 'record_type',  'record_group',

        'record_object', 'record_module',

        'operate_object', 'operate_module',
        'operate_category', 'operate_type',

        'owner_active',
        'owner_id',
        'creator_id',
        'creator_company_id',
        'creator_department_id',
        'creator_team_id',
        'user_id',
        'belong_id',
        'source_id',
        'object_id',
        'parent_id',
        'p_id',

        'item_id',
        'company_id',
        'department_id',
        'team_id',
        'staff_id',
        'client_id',
        'project_id',
        'order_id',

        'column',
        'column_type',
        'column_name',

        'before',
        'after',
        'before_id',
        'after_id',


        'custom_date',
        'custom_datetime',

        'follow_date',
        'follow_datetime',
        'last_operation_date',
        'last_operation_datetime',


        'name', 'username', 'nickname', 'true_name', 'short_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img', 'cover_pic',


        'page_type', 'page_module', 'page_num',
        'from',
        'referer',
        'open',
        'open_device_type',
        'open_device_name',
        'open_device_version',
        'open_system',
        'open_browser',
        'open_app',
        'open_NetType',
        'open_is_spider',

        'shared_location',

        'ip', 'ip_info', 'browser_info',
        'visit_num', 'share_num', 'favor_num', 'comment_num',

        'is_published', 'is_verified', 'is_completed',
        'publisher_id', 'verifier_id', 'completer_id',
        'published_at', 'verified_at', 'completed_at',
    ];
    protected $dateFormat = 'U';

//    protected $hidden = ['content','custom'];
    protected $hidden = [];

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
////        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','creator_id','id');
    }
    // 创作者（客户）
    function client_creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','user_id','id');
    }


    // 客户
    function before_client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','before_client_id','id');
    }
    // 客户
    function after_client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','after_client_id','id');
    }
    // 项目
    function before_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','before_project_id','id');
    }
    // 项目
    function after_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','after_project_id','id');
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
