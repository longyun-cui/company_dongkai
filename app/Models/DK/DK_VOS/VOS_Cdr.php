<?php
namespace App\Models\DK\DK_VOS;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class VOS_Cdr extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mysql_vos';

    protected $table = "e_cdr";

    protected $fillable = [
        'callere164',
        'calleraccesse164',
        'calleee164',
        'calleeaccesse164',
        'callerip',
        'callerrtpip',
        'callercodec',
        'callergatewayid',
        'callerproductid',
        'callertogatewaye164',
        'callertype',
        'calleeip',
        'calleertpip',
        'calleecodec',
        'calleegatewayid',
        'calleeproductid',
        'calleetogatewaye164',
        'calleetype',
        'billingmode',
        'calllevel',
        'agentfeetime',
        'starttime',
        'stoptime',
        'callerpdd',
        'calleepdd',
        'holdtime',
        'callerareacode',
        'feetime',
        'fee',
        'tax',
        'suitefee',
        'suitefeetime',
        'incomefee',
        'incometax',
        'customeraccount',
        'customername',
        'calleeareacode',
        'agentfee',
        'agenttax',
        'agentsuitefee',
        'agentsuitefeetime',
        'agentaccount',
        'agentname',
        'flowno',
        'softswitchname',
        'softswitchcallid',
        'callercallid',
        'calleroriginalcallid',
        'calleecallid',
        'calleroriginalinfo',
        'rtpforward',
        'enddirection',
        'endreason',
        'billingtype',
        'cdrlevel',
        'agentcdr_id',
        'sipreasonheader',
        'recordstarttime',
        'transactionid',
    ];

    public $timestamps = false;
//    protected $dateFormat = 'U';

//    protected $datas = ['deleted_at'];

//    protected $hidden = [
//        'password', 'remember_token',
//    ];



    public function setConnectionName($connectionName)
    {
        $this->connection = $connectionName;
    }

    public function setTableName($tableName)
    {
        $this->table = $tableName;
    }



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
    // 用户
    function user()
    {
        return $this->belongsTo('App\Models\DK\DK_User','user_id','id');
    }


    // 上级领导
    function superior()
    {
        return $this->belongsTo('App\Models\DK\DK_User','superior_id','id');
    }
    // 属下员工
    function subordinate_er()
    {
        return $this->hasMany('App\Models\DK\DK_User','superior_id','id');
    }
    // 下级的下级
    function through_subordinate_er()
    {
        return $this->hasManyThrough(
            'App\Models\DK\DK_User',
            'App\Models\DK\DK_User',
            'superior_id',
            'superior_id',
            'id',
            'id'
        );
    }


    // 部门-大区
    function department_district_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Department','department_district_id','id');
    }
    // 部门-小组
    function department_group_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Department','department_group_id','id');
    }


    // 工单
    function order_list()
    {
        return $this->hasMany('App\Models\DK\DK_Order','creator_id','id');
    }

    // 客服工单
    function order_list_for_customer_service()
    {
        return $this->hasMany('App\Models\DK\DK_Order','creator_id','id');
    }
    // 主管工单
    function order_list_for_supervisor()
    {
        return $this->hasMany('App\Models\DK\DK_Order','department_supervisor_id','id');
    }
    // 经理工单
    function order_list_for_manager()
    {
        return $this->hasMany('App\Models\DK\DK_Order','department_manager_id','id');
    }

    // 审核人工单
    function order_list_for_inspector()
    {
        return $this->hasMany('App\Models\DK\DK_Order','inspector_id','id');
    }
    // 审核人工单
    function order_list_for_deliverer()
    {
        return $this->hasMany('App\Models\DK\DK_Order','deliverer_id','id');
    }




    // 多对多 审核人关联的项目
    function pivot_user_project()
    {
        return $this->belongsToMany('App\Models\DK\DK_Project','dk_pivot_user_project','user_id','project_id');
//            ->wherePivot('relation_type', 1);
//            ->withTimestamps();
    }





    // 所属代理商
    function parent()
    {
        return $this->belongsTo('App\Models\DK\DK_User','parent_id','id');
    }

    // 名下代理商
    function children()
    {
        return $this->hasMany('App\Models\DK\DK_User','parent_id','id');
    }

    // 成员
    function members()
    {
        return $this->hasMany('App\Models\DK\DK_User','parent_id','id');
    }

    // 粉丝
    function fans()
    {
        return $this->hasMany('App\Models\DK\DK_User','parent_id','id');
    }

    // 名下客户
    function clients()
    {
        return $this->hasMany('App\Models\DK\DK_User','parent_id','id');
    }

    // 与我相关的内容
    function fans_list()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Relation','relation_user_id','id');
    }




    // 内容
    function items()
    {
        return $this->hasMany('App\Models\YH\YH_Item','owner_id','id');
    }
    // 内容
    function ad_list()
    {
        return $this->hasMany('App\Models\YH\YH_Item','owner_id','id');
    }

    // 广告
    function ad()
    {
        return $this->hasOne('App\Models\YH\YH_Item','id','advertising_id');
    }

    // 介绍
    function introduction()
    {
        return $this->hasOne('App\Models\YH\YH_Item','id','introduction_id');
    }

    // 与我相关的内容
    function pivot_item()
    {
        return $this->belongsToMany('App\Models\YH\YH_Item','pivot_user_item','user_id','item_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }




    //
    function pivot_user()
    {
        return $this->belongsToMany('App\Models\DK\DK_User','pivot_user_user','user_1_id','user_2_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_relation()
    {
        return $this->belongsToMany('App\Models\DK\DK_User','pivot_user_relation','mine_user_id','relation_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_sponsor_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_User','pivot_user_relation','mine_user_id','relation_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_org_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_User','pivot_user_relation','relation_user_id','mine_user_id')
            ->withPivot(['active','relation_active','type','relation_type'])->withTimestamps();
    }

    // 与我相关的内容
    function pivot_follow_list()
    {
        return $this->belongsToMany('App\Models\DK\DK_User','pivot_user_relation','relation_user_id','mine_user_id')
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
