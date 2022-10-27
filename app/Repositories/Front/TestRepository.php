<?php
namespace App\Repositories\Front;

use App\Models\RootModule;
use App\Models\RootMenu;
use App\Models\RootItem;
use App\Models\RootMessage;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache;
use QrCode;

class TestRepository {

    private $model;
    private $repo;
    public function __construct()
    {
//        $this->model = new OrgOrganization;
    }


    // root
    public function view_index()
    {
//        $info = json_decode(json_encode(config('mitong.company.info')));
//        $menus = RootMenu::where(['active'=>1])->orderby('order', 'asc')->get();

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->limit(8)->get();
        foreach($rent_items as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $wholesale_items = RootItem::where(['category'=>12, 'active'=>1])->orderby('id', 'desc')->limit(4)->get();
        foreach($wholesale_items as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $cooperation_items = RootItem::where(['category'=>9, 'active'=>1])->orderby('id', 'desc')->get();
        $coverage_items = RootItem::where(['category'=>31, 'active'=>1])->orderby('id', 'desc')->get();

        $html = view('frontend.test.index')->with([
                'rent_items'=>$rent_items,
                'wholesale_items'=>$wholesale_items,
                'cooperation_items'=>$cooperation_items,
                'coverage_items'=>$coverage_items
            ])->__toString();
        return $html;
    }



}