<?php
namespace App\Models\YH;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YH_Pricing extends Model
{
    use SoftDeletes;
    //
    protected $table = "yh_pricing";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'sort', 'form',
        'item_active', 'item_status', 'item_category', 'item_type', 'item_sort', 'item_form',
        'owner_active',
        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'org_id', 'admin_id',
        'item_id', 'menu_id',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3',
        'trailer_type', 'trailer_length', 'trailer_volume', 'trailer_weight', 'trailer_axis_count',
        'price1', 'price2', 'price3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src',
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
        return $this->belongsTo('App\Models\YH\YH_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\YH\YH_User','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\YH\YH_User','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\YH\YH_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\YH\YH_User','user_id','id');
    }


    // 车辆订单
    function car_order_list()
    {
        return $this->hasMany('App\Models\YH\YH_Order','car_id','id');
    }
    // 车挂订单
    function trailer_order_list()
    {
        return $this->hasMany('App\Models\YH\YH_Order','trailer_id','id');
    }
    // 车辆订单【当前】
    function car_order_list_for_current()
    {
        return $this->hasMany('App\Models\YH\YH_Order','car_id','id');
    }
    // 车挂订单【当前】
    function trailer_order_list_for_current()
    {
        return $this->hasMany('App\Models\YH\YH_Order','trailer_id','id');
    }
    // 车辆订单【已完成】
    function car_order_list_for_completed()
    {
        return $this->hasMany('App\Models\YH\YH_Order','car_id','id');
    }
    // 车挂订单【已完成】
    function trailer_order_list_for_completed()
    {
        return $this->hasMany('App\Models\YH\YH_Order','trailer_id','id');
    }
    // 车辆订单【未来】
    function car_order_list_for_future()
    {
        return $this->hasMany('App\Models\YH\YH_Order','car_id','id');
    }
    // 车挂订单【未来】
    function trailer_order_list_for_future()
    {
        return $this->hasMany('App\Models\YH\YH_Order','trailer_id','id');
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
        return $this->belongsToMany('App\Models\YH\YH_User','pivot_user_item','item_id','user_id');
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
