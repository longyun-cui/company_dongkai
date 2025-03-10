<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Pivot_Client_Delivery extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_pivot_client_delivery";
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

        'delivered_date',

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
        return $this->belongsTo('App\Models\DK\DK_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_User','creator_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }




    // 审核人
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }

    // 交付项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','project_id','id');
    }

    // 交付客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Client','client_id','id');
    }

    // 客户员工
    function client_staff_er()
    {
        return $this->belongsTo('App\Models\DK_Client\DK_Client_User','client_staff_id','id');
    }

    // 原始项目
    function original_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','original_project_id','id');
    }

    // 订单
    function order_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Order','order_id','id');
    }


    // 公司
    function company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Company','company_id','id');
    }
    // 渠道
    function channel_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Company','channel_id','id');
    }
    // 商务
    function business_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Company','business_id','id');
    }



}
