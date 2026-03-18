<?php
namespace App\Models\DK\DK_Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Client__Team extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_client__team";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'sort',
        'item_active', 'item_status',
        'item_category', 'item_type',
        'team_active', 'team_status',
        'team_category', 'team_type',

        'owner_active',
        'owner_id',
        'creator_id',
        'updater_id',
        'deleter_id',

        'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'org_id',
        'admin_id',
        'item_id',
        'menu_id',

        'client_id',
        'leader_id',
        'superior_department_id',

        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',

        'contact', 'contact_name', 'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_email', 'linkman_wx_id', 'linkman_wx_qr_code_img', 'linkman_address',

        'link_url', 'cover_pic', 'attachment_name', 'attachment_src', 'tag',
        'time_point', 'time_type', 'start_time', 'end_time', 'address',
        'visit_num', 'share_num', 'favor_num', 'comment_num',
        'published_at'
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
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','creator_id','id');
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




    // 【一对多】下级部门
    function subordinate_department_list()
    {
        return $this->hasMany('App\Models\DK\DK_Client\DK_Client__Team','superior_team_id','id');
    }

    // 【反向一对多】上级部门
    function superior_department_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Team','superior_team_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK\DK_Client\DK_Client__Staff','leader_id','id');
    }




}
