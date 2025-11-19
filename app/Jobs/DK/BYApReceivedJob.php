<?php

namespace App\Jobs\DK;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK\DK_Client;
use App\Models\DK\DK_Pivot_User_Project;
use App\Models\DK\DK_Pivot_Client_Delivery;

use App\Models\DK_Client\DK_Client_Department;
use App\Models\DK_Client\DK_Client_User;
use App\Models\DK_Client\DK_Client_Contact;

use App\Models\DK\DK_API_BY_Received;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class BYApReceivedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $by_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($by_id)
    {
        //
        $this->$by_id = $by_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $by_id = $this->by_id;
        $by = DK_API_BY_Received::find($by_id);
        if($by)
        {
            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                DB::commit();
            }
            catch (Exception $e)
            {
                DB::rollback();
                $msg = $e->getMessage();
                return;
            }

        }
        else return;
    }
}
