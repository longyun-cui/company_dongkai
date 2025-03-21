<?php
namespace App\Models\DK_Choice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Choice_Funds_Using extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_choice_customer_funds_using";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'sort',
        'item_active', 'item_status', 'item_category', 'item_type',

        'finance_active', 'finance_status', 'finance_object', 'finance_category', 'finance_type',
        'purchased_category', 'purchased_type',

        'is_confirmed', 'confirmer_id',
        'owner_active',
        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'create_type',

        'admin_id',
        'menu_id',
        'item_id',
        'order_id',
        'clue_id',
        'choice_id',
        'telephone_id',
        'client_id',

        'customer_id',
        'customer_staff_id',
        'company_id',
        'channel_id',
        'project_id',
        'settled_id',

        'assign_time', 'assign_date',
        'transaction_time', 'transaction_date',
        'transaction_type',
        'transaction_amount', 'transaction_account', 'transaction_receipt_account', 'transaction_payment_account', 'transaction_order',
        'total_funds', 'balance_funds', 'available_funds', 'init_freeze_funds', 'freeze_funds',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'mobile', 'address',
        'visit_num', 'share_num', 'favor_num', 'comment_num',
        'confirmed_at', 'completed_at'
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
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','owner_id','id');
    }
    // 创建者
    function creator()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','creator_id','id');
    }
    // 更新者
    function updater()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','updater_id','id');
    }
    // 完成者
    function completer()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','completer_id','id');
    }
    // 确认者
    function confirmer()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','confirmer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','user_id','id');
    }





    // 公司
    function company_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_Company','company_id','id');
    }
    // 渠道
    function channel_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_Company','channel_id','id');
    }
    // 客户
    function customer_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Customer','customer_id','id');
    }


    // 线索
    function clue_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Clue','clue_id','id');
    }
    // 线索
    function choice_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Pivot_Customer_Choice','choice_id','id');
    }

    // 话单
    function telephone_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Telephone_Bill','telephone_id','id');
    }

    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_Client','client_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\DK_Customer\DK_Customer_Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\DK_Customer\DK_Customer_Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\DK_Customer\DK_Customer_Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\DK_Customer\DK_Customer_User','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\DK_Customer\DK_Customer_Menu','pivot_menu_item','item_id','menu_id');
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
