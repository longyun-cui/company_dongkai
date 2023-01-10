<?php
namespace App\Models\YH;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class YH_Pivot_Circle_Order extends Model
{
    use SoftDeletes;
    //
    protected $table = "yh_pivot_circle_order";
    protected $fillable = [
        'relation_active', 'relation_category', 'relation_type', 'circle_id', 'order_id', 'creator_id', 'updater_id'
    ];
    protected $dateFormat = 'U';

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
//        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    function circle_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Circle','circle_id','id');
    }

    function order_er()
    {
        return $this->belongsTo('App\Models\YH\YH_Order','order_id','id');
    }



}
