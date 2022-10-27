<?php
namespace App\Models\DEF;
use Illuminate\Database\Eloquent\Model;

class Def_Pivot_Module_Menu extends Model
{
    //
    protected $table = "root_pivot_module_menu";
    protected $fillable = [
        'sort', 'type', 'admin_id', 'module_id', 'menu_id'
    ];
    protected $dateFormat = 'U';

//    protected $dates = ['created_at','updated_at'];
//    public function getDates()
//    {
//        return array(); // 原形返回；
//        return array('created_at','updated_at');
//    }


    function module()
    {
        return $this->belongsTo('App\Models\RootModule','module_id','id');
    }

    function menu()
    {
        return $this->belongsTo('App\Models\RootMenu','menu_id','id');
    }



}
