<?php
namespace App\Models\DEF;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Def_UserExt extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

//    protected $connection = 'mysql0';
//    protected $connection = 'mysql_def';

    protected $table = "def_user_ext";

    protected $fillable = [
        'active', 'status', 'user_active', 'user_status', 'user_category', 'user_group', 'user_type', 'user_id',
        'category', 'group', 'type',
        'parent_id', 'p_id',
        'name', 'username', 'nickname', 'true_name', 'description', 'portrait_img',
        'tag',
        'title', 'description', 'content',
        'mobile', 'telephone', 'email',
        'introduction_id', 'advertising_id',
        'QQ_number',
        'wx_id', 'wx_qr_code_img',
        'wb_name', 'wb_address',
        'website',
        'address',
        'contact_phone', 'contact_email', 'contact_wx_id', 'contact_wx_qr_code_img', 'contact_address',
        'linkman', 'linkman_name', 'linkman_phone', 'linkman_wx_id', 'linkman_wx_qr_code_img',
        'company', 'department', 'position', 'business_description',
        'visit_num', 'share_num', 'favor_num',  'follow_num', 'fans_num',
    ];

    protected $hidden = [
    ];

    protected $dateFormat = 'U';


    public function __construct()
    {
//        parent::__construct();
//
//        if(explode('.',request()->route()->getAction()['domain'])[0] == 'test')
//        {
//            $this->connection = 'mysql_test';
//        }
//        else
//        {
//            $this->connection = 'mysql_def';
//        }
    }




    function user()
    {
        return $this->belongsTo('App\Models\DEF\Def_User','user_id','id');
    }
    // 所属代理商
    function parent()
    {
        return $this->belongsTo('App\Models\DEF\Def_User','parent_id','id');
    }

    // 名下代理商
    function children()
    {
        return $this->hasMany('App\Models\DEF\Def_User','parent_id','id');
    }






}
