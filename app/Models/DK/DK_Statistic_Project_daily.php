<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Statistic_Project_daily extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_admin_statistic_for_project_daily";
    protected $fillable = [
        'active', 'status', 'item_active', 'item_status', 'item_category', 'item_type', 'category', 'type', 'sort',
        'owner_active',
        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'project_id',

        'statistic_date',

        'delivered_all_num',

        'is_confirmed',
        'completer_id',
        'confirmed_at',
        'confirmed_date',

        'production_published_num',
        'production_inspected_num',
        'production_accepted_num',
        'production_accepted_suburb_num',
        'production_accepted_inside_num',
        'production_repeated_num',
        'production_refused_num',

        'marketing_delivered_num',
        'marketing_today_num',
        'marketing_yesterday_num',
        'marketing_tomorrow_num',
        'marketing_distribute_num',
        'marketing_special_num',

        'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
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
        return $this->belongsTo('App\Models\DK\DK_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_User','creator_id','id');
    }
    // 更新者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_User','updater_id','id');
    }
    // 完成者
    function completer()
    {
        return $this->belongsTo('App\Models\DK\DK_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }




    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','project_id','id');
    }




    // 【一对一】审核员
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK\DK_User','inspector_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\DK\YH_Attachment','item_id','id');
    }
}
