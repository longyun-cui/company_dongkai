<?php
namespace App\Repositories\DK;

use App\Models\DK\DK_User;
use App\Models\DK\DK_UserExt;

use App\Models\DK\DK_District;
use App\Models\DK\DK_Project;
use App\Models\DK\DK_Department;
use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Team_Project;
use App\Models\DK\DK_Order;

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Client_Funds_Recharge;
use App\Models\DK\DK_Client_Funds_Using;

use App\Models\DK\DK_Pivot_Client_Delivery;


use App\Models\DK_Choice\DK_Choice_User;
use App\Models\DK_Choice\DK_Choice_Customer;
use App\Models\DK_Choice\DK_Choice_Funds_Recharge;
use App\Models\DK_Choice\DK_Choice_Funds_Using;

use App\Models\DK_Choice\DK_Choice_Project;
use App\Models\DK_Choice\DK_Choice_District;
use App\Models\DK_Choice\DK_Choice_Clue;
use App\Models\DK_Choice\DK_Choice_Record;
use App\Models\DK_Choice\DK_Choice_Record_Visit;
use App\Models\DK_Choice\DK_Choice_Call_Record;

use App\Models\DK_Choice\DK_Choice_Telephone_Bill;



use App\Models\DK_Choice\DK_Choice_Pivot_Customer_Choice;

use App\Models\DK_Customer\DK_Customer_User;
use App\Models\DK_Customer\DK_Customer_Finance_Daily;



use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;

class DKTestRepository {

    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_Choice_User;
        $this->modelItem = new DK_Choice_Clue;

        $this->view_blade_403 = env('TEMPLATE_DK_ADMIN_2').'entrance.errors.403';
        $this->view_blade_404 = env('TEMPLATE_DK_ADMIN_2').'entrance.errors.404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }



}