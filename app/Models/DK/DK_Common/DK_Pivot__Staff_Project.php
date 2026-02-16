<?php
namespace App\Models\DK\DK_Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Pivot__Staff_Project extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_pivot__staff_project";
    protected $fillable = [
        'pivot_active',
        'pivot_status',
        'pivot_category',
        'pivot_type',

        'relation_active',
        'relation_status',
        'relation_category',
        'relation_type',

        'staff_id',
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


    // 审核人
    function inspector_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Staff','staff_id','id');
    }

    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Common\DK_Common__Project','project_id','id');
    }



}
