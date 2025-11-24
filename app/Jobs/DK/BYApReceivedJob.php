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
        $this->by_id = $by_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $by_id = $this->by_id;
        $item = DK_API_BY_Received::find($by_id);
        if($item)
        {
//            if($item->api_status > 0)
//            {
//                return ;
//            }

            $item_content = $item->content;
            $item_para = json_decode($item_content);

            if(isset($item_para->client_name)) $update["client_name"] = $item_para->client_name;
            if(isset($item_para->client_phone))
            {
                $client_phone = $item_para->client_phone;
                $update["client_phone"] = $item_para->client_phone;
            }
            else
            {
                $client_phone = '';
                return ;
            }
            if(isset($item_para->client_intention)) $update["client_intention"] = $item_para->client_intention;
            if(isset($item_para->lable_info))
            {
                if(isset($item_para->lable_info->is_wx))
                {
                    if(in_array($item_para->lable_info->is_wx,['是','对'])) $update["is_wx"] = 1;
                }
                if(isset($item_para->lable_info->location_city)) $update["location_city"] = $item_para->lable_info->location_city;
                if(isset($item_para->lable_info->location_district)) $update["location_district"] = $item_para->lable_info->location_district;
                if(isset($item_para->lable_info->recording_address)) $update["recording_address"] = $item_para->lable_info->recording_address;
            }
            if(isset($item_para->dialog_content)) $update["dialog_content"] = json_encode($item_para->dialog_content);

            $is_repeat = DK_API_BY_Received::where(['client_phone'=>(int)$client_phone])
                ->where('id','<',$by_id)
                ->count("*");
            $update["is_repeat"] = $is_repeat;

            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                $update["api_status"] = 1;
                $bool = $item->fill($update)->save();
                if(!$bool) throw new Exception("DK_API_BY_Received--update--fail");
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
