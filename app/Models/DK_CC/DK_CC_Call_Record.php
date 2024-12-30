<?php
namespace App\Models\DK_CC;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

class DK_CC_Call_Record extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_cc_call_record";
    protected $fillable = [
        'active', 'status', 'category', 'type', 'sort', 'form',

        'call_category',
        'call_type',
        'call_group',
        'call_module',

        'call_object',

        'call_active',
        'call_status',
        'call_result',
        'call_result_msg',

        'operate_object', 'operate_category', 'operate_type',

        'owner_active',

        'owner_id', 'creator_id', 'user_id', 'belong_id', 'source_id', 'object_id', 'p_id', 'parent_id',

        'name', 'title', 'subtitle', 'description', 'content', 'remark', 'custom', 'custom2', 'custom3',

        'org_id',
        'admin_id',
        'choice_staff_id',
        'customer_staff_id',

        'item_id',
        'order_id',
        'clue_id',
        'choice_id',
        'customer_id',
        'telephone_id',

        'call_statistic_id',

        'pre_unit_price',
        'call_cost',

        'telephone',

        'area',
        'city',

        'staffNo',
        'callee',
        'caller',
        'session',
        'seq',

        'apiType',
        'service',
        'usageMode',

        'startTime',
        'ringTime',
        'answerTime',
        'byeTime',
        'timeLength',

        'releaseCause',
        'releasePart',
        'typeResult',
        'callResult',
        'callResultMsg',
        'errMsg',
        'errMsgCN',

        'trunkIndex',
        'trunkType',
        'trunkName',
        'trunkPeerIP',
        'trunkUsername',

        'recording_address', 'recordFile',

        'ip',
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
    function staff_or()
    {
        return $this->belongsTo('App\Models\DK\DK_User','staffNo','api_staffNo');
    }

    // 拥有者
    function owner()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','owner_id','id');
    }
    // 创作者
    function creator()
    {
        return $this->belongsTo('App\Models\DK\DK_User','creator_id','id');
    }
    // 更改者
    function updater()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','updater_id','id');
    }
    // 验证者
    function verifier()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','verifier_id','id');
    }
    // 审核者
    function inspector()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','inspector_id','id');
    }
    // 运营者
    function deliverer()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','deliverer_id','id');
    }
    // 完成者
    function completer()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','completer_id','id');
    }
    // 用户
    function user()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','user_id','id');
    }


    // 项目
    function project_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_Project','project_id','id');
    }

    // 大区经理
    function department_manager_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','department_manager_id','id');
    }

    // 小组主管
    function department_supervisor_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_User','department_supervisor_id','id');
    }

    // 部门-大区
    function department_district_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_Department','department_district_id','id');
    }

    // 部门-大区
    function department_group_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_Department','department_group_id','id');
    }




    // 客户
    function client_er()
    {
        return $this->belongsTo('App\ModelsDK\DK_Client','client_id','id');
    }
    function customer_er()
    {
        return $this->belongsTo('App\Models\DK_Choice\DK_Choice_Customer','customer_id','id');
    }




    // 附件
    function attachment_list()
    {
        return $this->hasMany('App\Models\YH\YH_Attachment','order_id','id');
    }




    // 财务记录
    function finance_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
    }
    function finance_income_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
    }
    function finance_expense_list()
    {
        return $this->hasMany('App\Models\YH\YH_Finance','order_id','id');
    }




    // 其他人的
    function pivot_item_relation()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Item','item_id','id');
    }

    // 其他人的
    function others()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Item','item_id','id');
    }

    // 收藏
    function collections()
    {
        return $this->hasMany('App\Models\YH\YH_Pivot_User_Collection','item_id','id');
    }

    // 转发内容
    function forward_item()
    {
        return $this->belongsTo('App\Models\YH\YH_Item','item_id','id');
    }




    // 与我相关的话题
    function pivot_collection_item_users()
    {
        return $this->belongsToMany('App\Models\Dk\DK_User','pivot_user_item','item_id','user_id');
    }




    // 一对多 关联的目录
    function menu()
    {
        return $this->belongsTo('App\Models\YH\YH_Menu','menu_id','id');
    }

    // 多对多 关联的目录
    function menus()
    {
        return $this->belongsToMany('App\Models\YH\YH_Menu','pivot_menu_item','item_id','menu_id');
    }


    /**
     * 获得此文章的所有评论。
     */
    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'itemable');
    }

    /**
     * 获得此文章的所有标签。
     */
    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }




    /**
     * 自定义更新
     */
//    public function update_batch_in($setColumn,$setValue,$whereColumn,$whereValue)
//    {
//        $sql ="UPDATE ".$this->table." SET ".$setColumn." = ".$setValue." WHERE ".$whereColumn." = ".$whereValue;
//        return DB::update(DB::raw($sql);
//    }
    
    
}
