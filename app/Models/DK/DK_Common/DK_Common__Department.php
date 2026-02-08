<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Department extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_common__department";
    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type',  'item_group',
        'department_active', 'department_status', 'department_result',
        'department_category', 'department_type', 'department_group',

        'login_number', 'password', 'wx_union_id',

        'owner_id', 'creator_id', 'updater_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id', 'admin_id',

        'name', 'username', 'nickname', 'true_name', 'short_name', 'alias_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3',
        'attachment',
        'attachment_name',
        'attachment_scr',
        'portrait_img',
        'cover_pic',

        'contact', 'contact_name', 'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_email', 'linkman_wx_id', 'linkman_wx_qr_code_img', 'linkman_address',

        'company_id',
        'superior_department_id',
        'leader_id',

        'name', 'username', 'nickname', 'true_name', 'short_name', 'alias_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img', 'cover_pic',


        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',
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
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','creator_id','id');
    }
    // 创作者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','updater_id','id');
    }
    // 创作者
    function completer()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','user_id','id');
    }




    // 创作者
    function company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','company_id','id');
    }

    // 【一对多】下级部门
    function subordinate_department_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Department','superior_department_id','id');
    }

    // 【反向一对多】上级部门
    function superior_department_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Department','superior_department_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','leader_id','id');
    }


    // 【一对多】部门员工
    function department_staff_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','department_id','id');
    }




    // 工单
    function order_list_for_department()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Order','department_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\DK\YH_Attachment','item_id','id');
    }


}
