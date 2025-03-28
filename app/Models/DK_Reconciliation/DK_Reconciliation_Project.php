<?php
namespace App\Models\DK_Reconciliation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Reconciliation_Project extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_reconciliation_project";
    protected $fillable = [
        'active', 'status', 'item_active', 'item_status', 'item_category', 'item_type', 'category', 'type', 'sort',
        'owner_active',
        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'org_id', 'admin_id',
        'item_id', 'menu_id',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'is_distributive',

        'company_id',
        'channel_id',
        'business_id',

        'client_id',

        'inspector_id',

        'cooperative_unit_price',
        'funds_revenue_total',
        'funds_consumption_total',
        'funds_bad_debt_total',
        'funds_consumption_total',

        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',

        'time_point', 'time_type', 'start_time', 'end_time', 'address',
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
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
//        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','creator_id','id');
        return $this->belongsTo('App\Models\DK\DK_Company','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','user_id','id');
    }




    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_Client','client_id','id');
    }




    // 【一对一】审核员
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','inspector_id','id');
    }

    // 【多对多】审核人关联的项目
    function pivot_project_user()
    {
        return $this->belongsToMany('App\Models\DK_Client\DK_Client_User','dk_pivot_user_project','project_id','user_id');
//            ->wherePivot('relation_type', 1);
//            ->withTimestamps();
    }

    // 【多对多】审核人关联的项目
    function pivot_project_team()
    {
        return $this->belongsToMany('App\Models\DK_Client\DK_Client_Department','dk_pivot_team_project','project_id','team_id');
//            ->wherePivot('relation_type', 1);
//            ->withTimestamps();
    }




    // 每日结算
    function daily_list()
    {
        return $this->hasMany('App\Models\DK_Reconciliation\DK_Reconciliation_Daily','project_id','id');
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
        return $this->belongsToMany('App\Models\DK_Client\DK_Client_User','pivot_user_item','item_id','user_id');
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
