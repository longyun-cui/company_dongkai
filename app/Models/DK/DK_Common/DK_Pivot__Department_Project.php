<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Pivot__Department_Project extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_pivot__department_project";
    protected $fillable = [
        'pivot_active',
        'pivot_status',
        'pivot_category',
        'pivot_type',

        'relation_active',
        'relation_status',
        'relation_category',
        'relation_type',

        'department_category',
        'department_type',
        'department_id',

        'project_category',
        'project_type',
        'project_id',

        'creator_id', 'updater_id'
    ];
    protected $dateFormat = 'U';

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
//        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 部门
    function department_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Department','department_id','id');
    }

    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','project_id','id');
    }



}
