<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Location extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_common__location";
    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type', 'item_group',
        'location_active', 'location_status', 'location_result',
        'location_category', 'location_type', 'location_group',

        'owner_id', 'creator_id', 'updater_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'name', 'username', 'nickname', 'true_name', 'short_name', 'alias_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img', 'cover_pic',

        'contact', 'contact_name', 'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_email', 'linkman_wx_id', 'linkman_wx_qr_code_img', 'linkman_address',


        'location_province', 'location_city', 'location_district', 'postcode', 'area_code',

        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',
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
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','creator_id','id');
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




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Attachment','item_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Menu','pivot_menu_item','item_id','menu_id');
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
