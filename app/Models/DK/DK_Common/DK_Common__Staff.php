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
        'sub_team_id',
        'group_id',

        'leader_id',
        'team_leader_id',
        'sub_team_leader_id',
        'group_leader_id',

        'superior_id', // 上司，上级

        'position', // 职位
        'level', // 职级


        'QQ_number',
        'wx_id',
        'wx_qr_code_img',
        'wb_name',
        'wb_address',
        'website',
        'address',

        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',

    ];

    protected $datas = ['deleted_at'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dateFormat = 'U';



    // 所属代理商
    function ext()
    {
        return $this->hasOne('App\Models\DK\DK_Common\DK_Common__StaffExt','user_id','id');
    }


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

    // 成员
    function members()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','parent_id','id');
    }

    // 粉丝
    function fans()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','parent_id','id');
    }

    // 名下客户
    function clients()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Staff','parent_id','id');
    }

    // 与我相关的内容
    function fans_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Pivot_User_Relation','relation_user_id','id');
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
    function sub_team_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','sub_team_id','id');
    }
    // 团队-小组
    function group_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','group_id','id');
    }


    // 【一对一】负责人
    function leader()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','leader_id','id');
    }




    // 内容
    function items()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Item','owner_id','id');
    }
    // 内容
    function ad_list()
    {
        return $this->hasMany('App\Models\DK\DK_Common\DK_Common__Item','owner_id','id');
    }

    // 广告
    function ad()
    {
        return $this->hasOne('App\Models\DK\DK_Common\DK_Common__Item','id','advertising_id');
    }

    // 介绍
    function introduction()
    {
        return $this->hasOne('App\Models\DK\DK_Common\DK_Common__Item','id','introduction_id');
    }

    // 与我相关的内容
    function pivot_item()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Item','pivot_user_item','user_id','item_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }




    //
    function pivot_user()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_user','user_1_id','user_2_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_relation()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_relation','mine_user_id','relation_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_sponsor_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_relation','mine_user_id','relation_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_org_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_relation','relation_user_id','mine_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_follow_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','pivot_user_relation','relation_user_id','mine_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }




    // 关联资金
    function fund()
    {
        return $this->hasOne('App\Models\MT\Fund','user_id','id');
    }




    // 名下站点
    function sites()
    {
        return $this->hasMany('App\Models\MT\SEOSite','create_user_id','id');
    }

    // 名下关键词
    function keywords()
    {
        return $this->hasMany('App\Models\MT\SEOKeyword','create_user_id','id');
    }

    function children_keywords()
    {
        return $this->hasManyThrough(
            'App\Models\MT\SEOKeyword',
            'App\Models\MT\User',
            'pid', // 用户表外键...
            'createuserid', // 文章表外键...
            'id', // 国家表本地键...
            'id' // 用户表本地键...
        );
    }


}
