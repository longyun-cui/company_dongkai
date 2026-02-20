<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Project extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_common__project";
    protected $fillable = [
        'active', 'status',
        'category', 'type',
        'item_active', 'item_status',
        'item_category', 'item_type',
        'project_category', 'project_type',

        'owner_id', 'creator_id', 'updater_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'is_published', 'is_verified', 'is_completed',
        'publisher_id', 'verifier_id', 'completer_id',
        'published_at', 'verified_at', 'completed_at',

        'name', 'username', 'nickname', 'true_name', 'short_name', 'alias_name',
        'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3', 'attachment', 'portrait_img',

        'client_id',

        'location_city',
        'is_distributive',
        'daily_goal',


        'contact', 'contact_name', 'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_email', 'linkman_wx_id', 'linkman_wx_qr_code_img', 'linkman_address',

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




    // 客户
    function client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','client_id','id');
    }


    // 【多对多】关联的部门
    function pivot__project_department()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Department','dk_pivot__department_project','project_id','department_id');
//            ->withTimestamps();
    }
    // 【多对多】关联的部门（客服部）
    function pivot__project_department__csd()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Department','dk_pivot__department_project','project_id','department_id')
            ->wherePivot('department_category', 41);
    }
    // 【多对多】关联的部门（质检部）
    function pivot__project_department__qid()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Department','dk_pivot__department_project','project_id','department_id')
            ->wherePivot('department_category', 51);
    }
    // 【多对多】关联的部门（复核部）
    function pivot__project_department__ad()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Department','dk_pivot__department_project','project_id','department_id')
            ->wherePivot('department_category', 61);
    }
    // 【多对多】关联的部门（运营部）
    function pivot__project_department__od()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Department','dk_pivot__department_project','project_id','department_id')
            ->wherePivot('department_category', 71);
    }




    // 【多对多】关联的团队
    function pivot__project_team()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Team','dk_pivot__team_project','project_id','team_id');
//            ->withTimestamps();
    }
    // 【多对多】关联的团队（客服部）
    function pivot__project_team__csd()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Team','dk_pivot__team_project','project_id','team_id')
            ->wherePivot('team_category', 41);
    }
    // 【多对多】关联的团队（质检部）
    function pivot__project_team__qid()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Team','dk_pivot__team_project','project_id','team_id')
            ->wherePivot('team_category', 51);
    }
    // 【多对多】关联的团队（复核部）
    function pivot__project_team__ad()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Team','dk_pivot__team_project','project_id','team_id')
            ->wherePivot('team_category', 61);
    }
    // 【多对多】关联的团队（运营部）
    function pivot__project_team__od()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Team','dk_pivot__team_project','project_id','team_id')
            ->wherePivot('team_category', 71);
    }




    // 【多对多】关联的员工
    function pivot__project_staff()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','dk_pivot__staff_project','project_id','staff_id');
//            ->withTimestamps();
    }
    // 【多对多】关联的员工（客服部）
    function pivot__project_staff__csd()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','dk_pivot__staff_project','project_id','staff_id')
            ->wherePivot('staff_category', 41);
    }
    // 【多对多】关联的员工（质检部）
    function pivot__project_staff__qid()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','dk_pivot__staff_project','project_id','staff_id')
            ->wherePivot('staff_category', 51);
    }
    // 【多对多】关联的员工（复核部）
    function pivot__project_staff__ad()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','dk_pivot__staff_project','project_id','staff_id')
            ->wherePivot('staff_category', 61);
    }
    // 【多对多】关联的员工（运营部）
    function pivot__project_staff__od()
    {
        return $this->belongsToMany('App\Models\DK\DK_Common\DK_Common__Staff','dk_pivot__staff_project','project_id','staff_id')
            ->wherePivot('staff_category', 71);
    }


    // 【一对一】审核员
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','inspector_id','id');
    }




}
