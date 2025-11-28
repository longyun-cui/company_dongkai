<?php
namespace App\Http\Controllers\DK;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\DK\DK_User;
use App\Models\DK\YH_Item;

use App\Jobs\TestJob;

use App\Models\DK_A\DK_Pool_Task;

use App\Repositories\DK\DKTestRepository;

use Response, Auth, Validator, DB, Exception;
use QrCode, Excel;

class DKTestController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
        $this->repo = new DKTestRepository;
    }

    /*
     * 首页
     */
	public function test_job()
	{
	    $id = request('id');
        $task = DK_Pool_Task::find($id);
        if($task)
        {
            TestJob::dispatch($id);
        }
        else return;
	}

    public function test_array()
    {
        $key = request('key','key');
        $value = request('value','value');

        if(array_key_exists($key,config('info.by_inspected_result')))
        {
            echo 'array_key_exists'.' - yes.'.'<br>';
        }
        else
        {
            echo 'array_key_exists'.' - no.'.'<br>';
        }

        if(in_array($value,config('info.by_inspected_result')))
        {
            echo 'in_array'.' - yes.'.'<br>';
        }
        else
        {
            echo 'in_array'.' - no.'.'<br>';
        }
    }


}

