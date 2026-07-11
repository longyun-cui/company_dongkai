<?php
namespace App\Jobs\DK;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Order__Operation_Record;
use App\Models\DK\DK_Common\DK_Common__Order__AI_Inspected__Record;
use App\Models\DK\DK_Common\DK_Common__Order__AI_Converted__Record;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Staff;
use App\Models\DK\DK_Common\DK_Common__Team;

use App\Repositories\DK\DK_Staff\DK_Staff__CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class DK_Push_Oder_to_Vos_Data_Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $order_id;
    protected $commonRepository;

//    public $queue = 'queue_vip';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        //
        $this->order_id = $order_id;
        $this->commonRepository = new DK_Staff__CommonRepository;

        $this->onQueue('queue_push_to_vos_data');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order_id = $this->order_id;
        $push_response = $this->commonRepository->o1__api__push__entry_order__to__vos_data($order_id);

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            DB::commit();
            return;
        }
        catch (Exception $e)
        {
            DB::rollback();
            return;
        }
    }
}
