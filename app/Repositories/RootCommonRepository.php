<?php
namespace App\Repositories;

use App\Models\RootModule;
use App\Models\RootMenu;
use App\Models\RootItem;
use App\Models\RootTemplate;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache;
use QrCode;

class RootCommonRepository {

    private $model;
    public function __construct()
    {
//        $this->model = new Root();
    }


    public function set_cache_root_is_refresh()
    {
        $cache_key_root_is_refresh = config('root.cache.key.root_is_refresh');
        Cache::put($cache_key_root_is_refresh, 1, 60*24*7);
    }


}