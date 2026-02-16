<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Delivery extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_common__delivery";
    protected $fillable = [
        'pivot_active', 'pivot_category', 'pivot_type',
        'relation_active', 'relation_category', 'relation_type',
        'delivery_type',
        'order_category',

        'user_id',
        'project_id',
        'company_id',
        'channel_id',
        'business_id',
        'client_id',
        'client_staff_id',
        'original_project_id',
        'order_id',

        'client_type',
        'client_phone',

        'is_exported',
        'exported_status',

        'assign_status',

        'delivered_status',
        'delivered_result',

        'is_api_pushed',
        'is_api_pusher_id',
        'is_api_pushed_at',

        'is_vx',
        'customer_remark',
        'client_contact_id',

        'follow_description',
        'follow_datetime',
        'follow_date',

        'follow_latest_description',
        'follow_latest_datetime',
        'follow_latest_date',

        'callback_datetime',
        'callback_date',

        'is_come',
        'come_datetime',
        'come_date',

        'transaction_num',
        'transaction_count',
        'transaction_amount',
        'transaction_datetime',
        'transaction_date',


        'delivered_date',

        'creator_id',
        'updater_id',

        'last_operation_datetime',
        'last_operation_date'
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
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','creator_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','user_id','id');
    }




    // 审核人
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','user_id','id');
    }


    // 原始项目
    function original_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','original_project_id','id');
    }

    // 交付项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','project_id','id');
    }

    // 交付客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','client_id','id');
    }


    // 客户员工
    function client_staff_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','client_staff_id','id');
    }


    // 客户联系人
    function client_contact_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_Contact','client_contact_id','id');
    }

    // 订单
    function order_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Order','order_id','id');
    }


    // 公司
    function company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','company_id','id');
    }
    // 渠道
    function channel_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','channel_id','id');
    }
    // 商务
    function business_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','business_id','id');
    }



}
