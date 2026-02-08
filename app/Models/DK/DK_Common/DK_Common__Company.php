<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Company extends Authenticatable
{
    use SoftDeletes;
    //
    protected $table = "dk_common__company";
    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type', 'item_group',
        'company_active', 'company_status', 'company_result',
        'company_category', 'company_type', 'company_group',

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

        'superior_company_id',
        'leader_id',

        'finance_recharge_total',
        'finance_consumption_total',
        'funds_bad_debt_total',
        'funds_should_settled_total',
        'funds_already_settled_total',
        'funds_balance',
        'funds_balance_prompt_threshold',

        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',

        'admin_token',
        'login_error_num',
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




    // 【一对多】下级公司
    function subordinate_company_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Company','superior_company_id','id');
    }

    // 【反向一对多】上级公司
    function superior_company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','superior_company_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','leader_id','id');
    }


    // 【一对多】公司员工
    function company_staff_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','company_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\Common\WL_Common_Attachment','item_id','id');
    }


}
