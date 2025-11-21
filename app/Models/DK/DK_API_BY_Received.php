<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_API_BY_Received extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_admin_api_by_received";
    protected $fillable = [
        'active', 'status',
        'category', 'type', 'form',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type', 'item_form',
        'api_active', 'api_status', 'api_result',
        'api_category', 'api_type', 'api_form',
        'api_module', 'api_group',

        'owner_id',
        'creator_id',
        'team_api_id',

        'username', 'nickname', 'true_name', 'short_name',
        'name', 'title', 'subtitle', 'content', 'description', 'tag', 'remark', 'label', 'custom',

        'client_type',
        'client_name',
        'client_phone',
        'client_intention',
        'is_wx',

        'dialog_content',
        'recording_address',

        'location_city',
        'location_district',

        'received_date',

        'inspector_id', 'inspected_status', 'inspected_result', 'inspected_result_code', 'inspected_description', 'inspected_at', 'inspected_date',

        'portrait_img_src',
        'cover_pic_src',
        'attachment_name',
        'attachment_src',
        'unique_path',
        'file_path',
        'link_url',
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
    // 创建者
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
    // 审核者
    function inspector()
    {
        return $this->belongsTo('App\Models\DK\DK_User','inspector_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }





}
