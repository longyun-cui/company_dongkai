<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class YH_Route extends Model
{
    use SoftDeletes;
    //
    protected $table = "yh_route";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'form', 'sort',
        'item_active', 'item_status', 'item_result', 'item_category', 'item_type', 'item_form',
        'owner_active', 'is_show', 'is_published', 'is_completed',
        'owner_id', 'creator_id', 'updater_id', 'publisher_id', 'completer_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'org_id', 'admin_id',
        'item_id', 'menu_id',
        'order_category', 'order_type',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'amount', 'amount_with_cash', 'amount_with_invoice',
        'driver_fine',
        'income_total', 'expenditure_total', 'income_to_be_confirm', 'expenditure_to_be_confirm',
        'travel_distance', 'time_limitation_prescribed',
        'route', 'fixed_route', 'temporary_route',
        'contract_start_date', 'contract_ended_date',
        'client_id',
        'car_owner_type', 'car_id', 'trailer_id', 'container_id', 'container_type', 'outside_car', 'outside_trailer',
        'trailer_type', 'trailer_length', 'trailer_volume', 'trailer_weight', 'trailer_axis_count',
        'departure_place', 'destination_place', 'stopover_place', 'stopover_place_1', 'stopover_place_2',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'visit_num', 'share_num', 'favor_num', 'comment_num',
        'published_at', 'completed_at'
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
        return $this->belongsTo('App\Models\YH\DK_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\YH\DK_User','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\YH\DK_User','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\YH\DK_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\YH\DK_User','user_id','id');
    }


    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Client','client_id','id');
    }


    // 车辆
    function car_er()
    {
        return $this->belongsTo('App\Models\YH\DKProject','car_id','id');
    }
    // 车挂
    function trailer_er()
    {
        return $this->belongsTo('App\Models\YH\DKProject','trailer_id','id');
    }
    // 车厢
    function container_er()
    {
        return $this->belongsTo('App\Models\YH\DKProject','container_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\YH\YH_Attachment','order_id','id');
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
        return $this->belongsToMany('App\Models\YH\DK_User','pivot_user_item','item_id','user_id');
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




    /**
     * 自定义更新
     */
//    public function update_batch_in($setColumn,$setValue,$whereColumn,$whereValue)
//    {
//        $sql ="UPDATE ".$this->table." SET ".$setColumn." = ".$setValue." WHERE ".$whereColumn." = ".$whereValue;
//        return DB::update(DB::raw($sql);
//    }
    
    
}
