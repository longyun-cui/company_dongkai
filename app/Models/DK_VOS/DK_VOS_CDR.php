<?php
namespace App\Models\DK_VOS;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class DK_VOS_CDR extends Authenticatable
{
    use Notifiable;
//    use SoftDeletes;

//    protected $connection = 'mysql0';
//    protected $connection = 'mysql_def';

    protected $table = "a_cdr";
//    protected $table = "dk_vos";

    protected $fillable = [
        'active', 'status', 'user_active', 'user_status',
        'user_group', 'user_category', 'user_type',
        'group', 'category', 'type',

        'starttime',
        'call_date',
        'phone',
        'holdtime',
        'region'
    ];

//    protected $datas = ['deleted_at'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dateFormat = 'U';



    // 所属代理商
    function ext()
    {
        return $this->hasOne('App\Models\DK\DK_ClientExt','user_id','id');
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







}
