<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Staff extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

//    protected $connection = 'mysql0';
//    protected $connection = 'mysql_def';

    protected $table = "dk_common__staff";

    protected $fillable = [
        'active', 'status', 'result',
        'category', 'type', 'group',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type',  'item_group',
        'staff_active', 'staff_status', 'staff_result',
        'staff_category', 'staff_type',  'user_group',

        'staff_position',

        'login_number', 'password', 'wx_union_id',

        'owner_id', 'creator_id', 'updater_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',
        'superior_id',

        'name', 'username', 'nickname', 'true_name', 'short_name', 'alias_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img', 'cover_pic',

        'contact', 'contact_name', 'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_email', 'linkman_wx_id', 'linkman_wx_qr_code_img', 'linkman_address',

        'district_category',
        'district_type',
        'district_id',


        'company_id',
        'department_id',
        'team_id',
        'team_sub_id',
        'team_group_id',
        'team_unit_id',

        'leader_id',
        'team_leader_id',
        'team_sub_leader_id',
        'team_group_leader_id',
        'team_unit_leader_id',

        'superior_id', // 上司，上级

        'position', // 职位
        'level', // 职级


        'QQ_number',
        'wx_id',
        'wx_qr_code_img',
        'wb_name',
        'wb_address',
        'xhs_id',
        'website',
        'address',

        'api_serverFrom_id',
        'api_serverFrom_name',
        'api_customer_account',
        'api_staffNo',

        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',

    ];

    protected $datas = ['deleted_at'];

    protected $hidden = [
        'password', 'remember_token', 'only_token',
    ];

    protected $dateFormat = 'U';




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
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','user_id','id');
    }




    // 所属代理商
    function parent()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','parent_id','id');
    }

    // 名下代理商
    function children()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','parent_id','id');
    }




    // 公司
    function company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','company_id','id');
    }

    // 部门
    function department_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Department','department_id','id');
    }


    // 团队-团队
    function team_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','team_id','id');
    }
    // 团队-分部
    function team_sub_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','team_sub_id','id');
    }
    // 团队-小组
    function team_group_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','team_group_id','id');
    }
    // 团队-小组
    function team_unit_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','team_unit_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','leader_id','id');
    }




}
