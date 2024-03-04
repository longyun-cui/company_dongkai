<?php
namespace App\Models\DK;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_Pivot_Team_Project extends Model
{
    use SoftDeletes;
    //
    protected $table = "dk_pivot_team_project";
    protected $fillable = [
        'pivot_active', 'pivot_category', 'pivot_type',
        'relation_active', 'relation_category', 'relation_type',
        'team_id', 'project_id', 'creator_id', 'updater_id'
    ];
    protected $dateFormat = 'U';

    protected $dates = ['created_at','updated_at','deleted_at'];
//    public function getDates()
//    {
//        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    // 审核人
    function team_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Department','team_id','id');
    }

    // 项目
    function project_er()
    {
        return $this->belongsTo('App\Models\DK\DK_Project','project_id','id');
    }



}
