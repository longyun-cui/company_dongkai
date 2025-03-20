<?php
namespace App\Models\DK_Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Client_Trade_Record extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_client_trade_record";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'form', 'sort',

        'item_active', 'item_status', 'item_category', 'item_type',

        'trade_active', 'trade_status', 'trade_category', 'trade_type',

        'is_confirmed',
        'authenticator_id',

        'owner_active',
        'owner_id',
        'user_id',

        'creator_id',
        'updater_id',
        'deleter_id',
        'authenticator_id',
        'verifier_id',
        'inspector_id',
        'deliverer_id',

        'belong_id',
        'source_id',
        'object_id',
        'p_id',
        'parent_id',

        'create_type',
        'admin_id',
        'menu_id',

        'item_id',
        'delivery_id',
        'follow_id',

        'company_id',
        'channel_id',
        'business_id',

        'project_id',

        'client_id',

        'assign_time',
        'assign_date',

        'transaction_datetime',
        'transaction_date',

        'transaction_num',
        'transaction_count',
        'transaction_amount',

        'transaction_pay_type',
        'transaction_pay_account',
        'transaction_receipt_account',
        'transaction_order_number',

        'total_funds',
        'balance_funds',
        'available_funds',
        'init_freeze_funds',
        'freeze_funds',

        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'mobile', 'address',
        'visit_num', 'share_num', 'favor_num', 'comment_num',

        'confirmed_at',
        'completed_at',

        'last_operation_datetime',
        'last_operation_date'
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
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','creator_id','id');
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
    // 删除人
    function deleter_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','deleter_id','id');
    }
    // 确认人
    function authenticator_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','authenticator_id','id');
    }



    // 订单
    function delivery_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Pivot_Client_Delivery','delivery_id','id');
    }

    // 【反向一对多】管理者
    function client_staff_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','client_staff_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\DK_Finance\DK_Finance_Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\DK_Finance\DK_Finance_Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\DK_Finance\DK_Finance_Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\DK_Finance\DK_Finance_Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\DK_Finance\DK_Finance_User','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\DK_Finance\DK_Finance_Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\DK_Finance\DK_Finance_Menu','pivot_menu_item','item_id','menu_id');
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
