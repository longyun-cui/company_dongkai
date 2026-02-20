<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Common__Order extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_common__order";
    protected $fillable = [
        'active', 'status',
        'category', 'type', 'form',
        'item_active', 'item_status', 'item_result',
        'item_category', 'item_type', 'item_form',
        'item_module', 'item_group',

        'owner_active',

        'owner_id',
        'creator_id', 'updater_id',

        'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'created_type',
        'order_category',
        'order_type',
        'order_quality',

        'org_id', 'admin_id',
        'item_id', 'menu_id',

        'api_id',

        'api_staffNo',
        'call_record_id',

        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'tag', 'custom', 'custom2', 'custom3',
        'link_url', 'cover_pic', 'attachment_name', 'attachment_src',
        'visit_num', 'share_num', 'favor_num', 'comment_num',

        'creator_company_id',
        'creator_department_id',
        'creator_team_id',
        'creator_team_sub_id',
        'creator_team_group_id',
        'creator_team_unit_id',

        'work_shift',


        'client_id',
        'project_id',


        'department_district_id',
        'department_group_id',
        'department_manager_id',
        'department_supervisor_id',

        'team_district',

        'location_province',
        'location_province_code',
        'location_city',
        'location_city_code',
        'location_district',
        'location_district_code',

        'assign_time',
        'is_distributive_condition',
        'client_type',
        'client_name',
        'client_phone',
        'client_intention',
        'teeth_count',
        'channel_source',
        
        'is_wx',
        'wx_id',

        'recording_address',
        'recording_address_list',
        'recording_quality',

        'is_repeat',
        'receipt_status',
        'receipt_need',
        'receipt_address',
        'GPS',

        'field_1',
        'field_2',
        'field_3',
        'field_4',
        'field_5',
        'field_6',
        'field_7',
        'field_8',
        'field_9',

        'api_is_pushed',



        'publisher_id',
        'is_published',
        'published_status',
        'published_result',
        'published_date',
        'published_at',

        'verifier_id',
        'is_verified',
        'verified_status',
        'verified_result',
        'verified_date',
        'verified_at',

        'completer_id',
        'is_completed',
        'completed_status',
        'completed_result',
        'completed_date',
        'completed_at',

        'inspector_id',
        'inspected_status',
        'inspected_result',
        'inspected_result_code',
        'inspected_description',
        'inspected_date',
        'inspected_at',

        'appellant_id',
        'appealed_status',
        'appealed_result',
        'appealed_result_code',
        'appealed_url',
        'appealed_description',
        'appealed_date',
        'appealed_at',

        'appealed_handler_id',
        'appealed_handled_description',
        'appealed_handled_date',
        'appealed_handled_at',

        'deliverer_id',
        'delivered_id',
        'delivered_client_id',
        'delivered_project_id',
        'delivered_status',
        'delivered_result',
        'delivered_result_code',
        'delivered_description',
        'delivered_date',
        'delivered_at',

        'pusher_id',
        'pushed_at',

        'created_date'
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
    // 更改者
    function updater()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','updater_id','id');
    }
    // 验证者
    function verifier()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','verifier_id','id');
    }
    // 审核者
    function inspector()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','inspector_id','id');
    }
    // 交付者
    function deliverer()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','deliverer_id','id');
    }
    // 完成者
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

    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','project_id','id');
    }




    // 交付客户
    function delivered_client_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Client','delivered_client_id','id');
    }

    // 交付项目
    function delivered_project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','delivered_project_id','id');
    }




    // 部门经理
    function department_manager_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','department_manager_id','id');
    }

    // 小组主管
    function department_supervisor_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','department_supervisor_id','id');
    }

    
    
    // 所属公司
    function creator_company_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Company','creator_company_id','id');
    }

    // 所属部门
    function creator_department_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Department','creator_department_id','id');
    }

    // 部门-大区
    function creator_team_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','creator_team_id','id');
    }

    // 部门-大区
    function creator_team_group_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Team','creator_team_group_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\YH\YH_Attachment','order_id','id');
    }



}
