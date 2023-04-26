<?php
namespace App\Models\YH;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class YH_Order extends Model
{
    use SoftDeletes;
    //
    protected $table = "yh_order";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'form', 'sort',
        'item_active', 'item_status', 'item_result', 'item_category', 'item_type', 'item_form',
        'owner_active', 'is_show', 'is_published', 'is_completed',
        'owner_id', 'creator_id', 'updater_id', 'publisher_id', 'completer_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'create_type',
        'org_id', 'admin_id',
        'item_id', 'menu_id',
        'order_category', 'order_type',
        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'amount', 'deposit', 'oil_card_amount',
        'invoice_amount', 'invoice_point',
        'reimbursable_amount', 'customer_management_fee', 'time_limitation_deduction',
        'administrative_fee',
        'driver_fine',
        'information_fee',
        'ETC_price', 'oil_amount', 'oil_unit_price', 'oil_fee',
        'income_real_first_amount', 'income_real_first_time', 'income_real_final_amount', 'income_real_final_time',
        'outside_car_price', 'outside_car_first_amount', 'outside_car_first_time', 'outside_car_final_amount', 'outside_car_final_time',
        'income_total', 'expenditure_total', 'income_to_be_confirm', 'expenditure_to_be_confirm',
        'travel_distance', 'time_limitation_prescribed',
        'circle_id',
        'route_type', 'route_id', 'route', 'route_fixed', 'route_temporary',
        'pricing_id',
        'client_id',
        'car_owner_type', 'car_id', 'trailer_id', 'container_id', 'container_type',
        'outside_car', 'outside_trailer',
        'trailer_type', 'trailer_length', 'trailer_volume', 'trailer_weight', 'trailer_axis_count',
        'driver_id', 'sub_driver_id',
        'departure_place', 'destination_place', 'stopover_place', 'stopover_place_1', 'stopover_place_2',
        'assign_time',
        'should_departure_time', 'should_arrival_time',
        'actual_departure_time', 'actual_arrival_time',
        'stopover_departure_time', 'stopover_arrival_time',
        'stopover_1_departure_time', 'stopover_1_arrival_time',
        'stopover_2_departure_time', 'stopover_2_arrival_time',
        'empty_route', 'empty_route_type', 'empty_route_id', 'empty_route_temporary', 'empty_distance', 'empty_oil_price', 'empty_oil_amount', 'empty_refueling_pay_type', 'empty_refueling_charge', 'empty_toll_cash', 'empty_toll_ETC',
        'receipt_status', 'receipt_need', 'receipt_address', 'GPS', 'is_delay',
        'subordinate_company', 'order_number', 'payee_name', 'arrange_people', 'car_supply', 'car_managerial_people',
        'driver', 'copilot', 'driver_name', 'copilot_name', 'driver_phone', 'copilot_phone', 'weight',
        'company', 'fund', 'mobile', 'city', 'address',
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


    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Client','client_id','id');
    }


    // 环线
    function circle_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Circle','circle_id','id');
    }


    // 固定线路
    function route_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Route','route_id','id');
    }
    // 固定线路
    function empty_route_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Route','empty_route_id','id');
    }


    // 定价
    function pricing_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Pricing','pricing_id','id');
    }


    // 车辆
    function car_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Car','car_id','id');
    }
    // 车挂
    function trailer_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Car','trailer_id','id');
    }
    // 车厢
    function container_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Car','container_id','id');
    }


    // 司机
    function driver_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Driver','driver_id','id');
    }
    // 副驾
    function sub_driver_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Driver','driver_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\YH\YH_Attachment','order_id','id');
    }




    // 财务记录
    function finance_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
    }
    function finance_income_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
    }
    function finance_expense_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
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




    /**
     * 自定义更新
     */
//    public function update_batch_in($setColumn,$setValue,$whereColumn,$whereValue)
//    {
//        $sql ="UPDATE ".$this->table." SET ".$setColumn." = ".$setValue." WHERE ".$whereColumn." = ".$whereValue;
//        return DB::update(DB::raw($sql);
//    }
    
    
}
