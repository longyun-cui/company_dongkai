<?php
namespace App\Http\Controllers\DK;

use App\Jobs\DK_CC\DownPhoneJob;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;
use App\Models\DK\DK_Client;
use App\Models\DK\DK_Record_Visit;

use App\Repositories\DK\DK_API\DK_API__StaffClientAppRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKAPIController extends Controller
{
    //
    private $staff_client_app_repo;

    public function __construct()
    {
        $this->staff_client_app_repo = new DK_API__StaffClientAppRepository;
    }







    // 【API】验证接口路径
    public function staff_client_app__verify_mac_address()
    {
        return $this->staff_client_app_repo->staff_client_app__verify_mac_address(request()->all());
    }











}
