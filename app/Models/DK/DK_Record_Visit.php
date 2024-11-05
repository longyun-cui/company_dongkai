<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;

class DK_Record_Visit extends Model
{
    //
    protected $table = "dk_admin_record_by_visit";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'form', 'sort',

        'record_active',
        'record_status',
        'record_object',
        'record_category',
        'record_type',
        'record_module',

        'operate_object',
        'operate_category',
        'operate_type',

        'owner_active',
        'owner_id',
        'creator_id',
        'user_id',
        'belong_id',
        'source_id',
        'object_id',
        'visitor_id',

        'p_id',
        'parent_id',
        'org_id',
        'admin_id',
        'item_id',
        'order_id',


        'column', 'column_type', 'column_name',

        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'time_point', 'time_type', 'start_time', 'end_time', 'address',

        'page_type', 'page_module', 'page_num',
        'from',
        'referer',
        'open',
        'open_device_type',
        'open_device_name',
        'open_device_version',
        'open_system',
        'open_browser',
        'open_app',
        'open_NetType',
        'open_is_spider',

        'shared_location',

        'ip', 'ip_info', 'browser_info',
        'visit_num', 'share_num', 'favor_num', 'comment_num',
        'published_at',


    ];
    protected $dateFormat = 'U';


    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\Models\DK\K_User','owner_id','id');
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
    // 用户
    function object()
    {
        return $this->belongsTo('App\Models\DK\DK_User','object_id','id');
    }


    // 内容
    function item()
    {
        return $this->belongsTo('App\Models\DK\DK_Item','item_id','id');
    }


}
