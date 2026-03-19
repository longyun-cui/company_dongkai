<?php
namespace App\Models\DK\DK_Client;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Client__Record__by_Operation extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_client__record__by_operation";
    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type',  'item_group',
        'record_active', 'record_status', 'record_result',
        'record_category', 'record_type',  'record_group',

        'record_object', 'record_module',

        'operate_object', 'operate_module',
        'operate_category', 'operate_type',

        'owner_active',
        'owner_id',
        'creator_id',
        'creator_company_id',
        'creator_department_id',
        'creator_team_id',
        'user_id',
        'belong_id',
        'source_id',
        'object_id',
        'parent_id',
        'p_id',

        'item_id',
        'company_id',
        'department_id',
        'team_id',
        'staff_id',
        'motorcade_id',
        'car_id',
        'driver_id',
        'client_id',
        'project_id',
        'order_id',

        'column',
        'column_type',
        'column_name',

        'before',
        'after',
        'before_id',
        'after_id',


        'custom_date',
        'custom_datetime',

        'follow_date',
        'follow_datetime',
        'last_operation_date',
        'last_operation_datetime',


        'name', 'username', 'nickname', 'true_name', 'short_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img', 'cover_pic',

        'ip',

        'is_published', 'is_verified', 'is_completed',
        'publisher_id', 'verifier_id', 'completer_id',
        'published_at', 'verified_at', 'completed_at',
    ];
    protected $dateFormat = 'U';

//    protected $hidden = ['content','custom'];
    protected $hidden = [];

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
////        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','creator_id','id');
    }
    // 创作者（客户）
    function client_creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Client','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','user_id','id');
    }


}
