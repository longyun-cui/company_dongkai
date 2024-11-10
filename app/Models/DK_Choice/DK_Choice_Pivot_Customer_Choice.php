<?php
namespace App\Models\DK_Choice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Choice_Pivot_Customer_Choice extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_choice_pivot_customer_choice";
    protected $fillable = [
        'pivot_active', 'pivot_category', 'pivot_type',
        'relation_active', 'relation_category', 'relation_type',
        'delivery_type',

        'user_id',
        'client_id',
        'customer_id',
        'project_id',
        'item_id',
        'order_id',
        'clue_id',
        'client_staff_id',
        'customer_staff_id',

        'client_name',
        'client_phone',

        'is_exported',
        'exported_status',

        'assign_status',

        'sale_category',
        'sale_type',
        'sale_status',
        'sale_result',

        'location_city',
        'location_district',

        'location_city',
        'location_district',

        'taker_id',
        'taken_at',

        'purchaser_id',
        'purchased_at',

        'delivered_status',
        'delivered_result',

        'creator_id',
        'updater_id'
    ];
    protected $dateFormat = 'U';

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
//        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_User','creator_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_User','user_id','id');
    }




    // 审核人
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_User','user_id','id');
    }


    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Client','client_id','id');
    }

    // 客户员工
    function client_staff_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','client_staff_id','id');
    }

    // 客户
    function customer_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Customer','customer_id','id');
    }

    // 客户员工
    function customer_staff_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','customer_staff_id','id');
    }

    // 接单人
    function taker_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','taker_id','id');
    }

    // 购买人
    function purchaser_er()
    {
        return $this->belongsTo('App\Models\DK_Customer\DK_Customer_User','purchaser_id','id');
    }


    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Project','project_id','id');
    }

    // 工单
    function order_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Order','order_id','id');
    }

    // 线索
    function clue_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Clue','clue_id','id');
    }



}
