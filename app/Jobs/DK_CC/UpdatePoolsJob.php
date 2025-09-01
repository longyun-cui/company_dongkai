<?php

namespace App\Jobs\DK_CC;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\DK_A\DK_A_Order;
use App\Models\DK_A\DK_Pool_Task;
use App\Models\DK_A\DK_Pool;

use App\Models\DK_VOS\DK_VOS_CDR;
use App\Models\DK_VOS\DK_VOS_CDR_Current;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon, DateTime;
use QrCode, Excel;


class UpdatePoolsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $pool_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pool_id)
    {
        //
        $this->pool_id = $pool_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pool_id = $this->pool_id;
        $pool = DK_Pool::find($pool_id);
        if($pool)
        {
            $current = DK_VOS_CDR_Current::select('*')->orderBy('id','desc')->first();
            $current_date = $current->call_date;

            $table = $pool->data_table;
            $modal = $pool->data_modal;
            $last_sync_date = $pool->last_sync_date;

            // 获取表名
            $orderTable = (new DK_A_Order)->getTable();
            $cdrCurrentTable = (new DK_VOS_CDR_Current)->getTable();
            $poolTable = $table;

            // 设置日期条件（示例：2023-01-01）
            $startDate = $last_sync_date; // 替换为实际需要的日期

            // 启动数据库事务
            DB::beginTransaction();
            try
            {
                $sql_1 = "
                    UPDATE {$poolTable} p
                    JOIN (
                      SELECT
                        phone,
                        COUNT(*) AS cnt,
                        COUNT(CASE WHEN holdtime BETWEEN 1 AND 6 THEN 1 END) AS cnt_1_6,
                        COUNT(CASE WHEN holdtime BETWEEN 1 AND 8 THEN 1 END) AS cnt_1_8,
                        COUNT(CASE WHEN holdtime BETWEEN 9 AND 15 THEN 1 END) AS cnt_9_15,
                        COUNT(CASE WHEN holdtime BETWEEN 16 AND 25 THEN 1 END) AS cnt_16_25,
                        COUNT(CASE WHEN holdtime BETWEEN 26 AND 45 THEN 1 END) AS cnt_26_45,
                        COUNT(CASE WHEN holdtime BETWEEN 46 AND 90 THEN 1 END) AS cnt_46_90,
                        COUNT(CASE WHEN holdtime >= 7 THEN 1 END) AS cnt_7_above,
                        COUNT(CASE WHEN holdtime >= 9 THEN 1 END) AS cnt_9_above,
                        COUNT(CASE WHEN holdtime >= 91 THEN 1 END) AS cnt_91_above
                      FROM {$cdrCurrentTable}
                      WHERE call_date > '{$startDate}'
                      GROUP BY phone
                    ) c
                    ON p.phone = c.phone
                    
                    SET
                      p.call_cnt = p.call_cnt + c.cnt,
                      p.call_cnt_1_6 = p.call_cnt_1_6 + c.cnt_1_6,
                      p.call_cnt_1_8 = p.call_cnt_1_8 + c.cnt_1_8,
                      p.call_cnt_9_15 = p.call_cnt_9_15 + c.cnt_9_15,
                      p.call_cnt_16_25 = p.call_cnt_16_25 + c.cnt_16_25,
                      p.call_cnt_26_45 = p.call_cnt_26_45 + c.cnt_26_45,
                      p.call_cnt_46_90 = p.call_cnt_46_90 + c.cnt_46_90,
                      p.call_cnt_7_above = p.call_cnt_7_above + c.cnt_7_above,
                      p.call_cnt_9_above = p.call_cnt_9_above + c.cnt_9_above,
                      p.call_cnt_91_above = p.call_cnt_91_above + c.cnt_91_above
                    ";
                DB::statement($sql_1);


                $pool->timestamps = false;
                $pool->last_sync_date = $current_date;
                $pool->save();


                $sql_2 = "
                    UPDATE {$poolTable} pool
                    JOIN (
                        SELECT pool.phone, COUNT(o.order_phone) AS order_count,MAX(o.order_date) as order_date
                        FROM {$poolTable} pool
                        LEFT JOIN {$orderTable} o
                        ON pool.phone = o.order_phone
                        GROUP BY pool.phone
                    ) sub
                    ON pool.phone = sub.phone
                    SET
                    pool.order_cnt = sub.order_count,
                    pool.order_date = sub.order_date
                ";
                DB::statement($sql_2);


                $sql_3 = "
                    UPDATE {$poolTable}
                    SET `quality` =
                    CASE
                        
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` < 2) AND ((`call_cnt_9_15` + `call_cnt_16_25`) >= 1) THEN 60
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` = 2) AND ((`call_cnt_9_15` + `call_cnt_16_25`) >= 1) THEN 50
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` > 2) AND ((`call_cnt_9_15` + `call_cnt_16_25`) >= 1) THEN -60
                        
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` < 2) AND ((`call_cnt_26_45` + `call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN 90
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` = 2) AND ((`call_cnt_26_45` + `call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN 80
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` > 2) AND ((`call_cnt_26_45` + `call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN -40
                        
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` < 2) AND ((`call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN 95
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` = 2) AND ((`call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN 85
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` > 2) AND ((`call_cnt_46_90` + `call_cnt_91_above`) >= 1) THEN -20
                        
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` = 1) AND (`call_cnt_9_above` = 0) THEN 40
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` = 2) AND (`call_cnt_9_above` = 0) THEN 30
                        WHEN `order_cnt` = 0 AND (`call_cnt_1_8` > 2) AND (`call_cnt_9_above` = 0) THEN -80
                        WHEN `order_cnt` = 1 THEN 10
                        ELSE 0
                    END;
                ";
                DB::statement($sql_3);


                $sql_4 = "
                    UPDATE a_pool
                    JOIN (
                      SELECT
                        COUNT(*) AS count_phone_cnt,
                        COALESCE(COUNT(CASE WHEN order_cnt > 0 THEN 1 END), 0) AS count_order_cnt,
                        COALESCE(SUM(call_cnt), 0) AS total_call_cnt,
                        COALESCE(SUM(call_cnt_1_6), 0) AS total_call_cnt_1_6,
                        COALESCE(SUM(call_cnt_1_8), 0) AS total_call_cnt_1_8,
                        COALESCE(SUM(call_cnt_9_15), 0) AS total_call_cnt_9_15,
                        COALESCE(SUM(call_cnt_16_25), 0) AS total_call_cnt_16_25,
                        COALESCE(SUM(call_cnt_26_45), 0) AS total_call_cnt_26_45,
                        COALESCE(SUM(call_cnt_46_90), 0) AS total_call_cnt_46_90,
                        COALESCE(SUM(call_cnt_7_above), 0) AS total_call_cnt_7_above,
                        COALESCE(SUM(call_cnt_9_above), 0) AS total_call_cnt_9_above,
                        COALESCE(SUM(call_cnt_91_above), 0) AS total_call_cnt_91_above,
                        COALESCE(COUNT(CASE WHEN quality = 95 THEN 1 END), 0) AS count_rate_95_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 90 THEN 1 END), 0) AS count_rate_90_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 85 THEN 1 END), 0) AS count_rate_85_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 80 THEN 1 END), 0) AS count_rate_80_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 60 THEN 1 END), 0) AS count_rate_60_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 50 THEN 1 END), 0) AS count_rate_50_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 40 THEN 1 END), 0) AS count_rate_40_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 30 THEN 1 END), 0) AS count_rate_30_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 20 THEN 1 END), 0) AS count_rate_20_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 10 THEN 1 END), 0) AS count_rate_10_cnt,
                        COALESCE(COUNT(CASE WHEN quality = 0 THEN 1 END), 0) AS count_rate_0_cnt,
                        COALESCE(COUNT(CASE WHEN quality = -20 THEN 1 END), 0) AS count_rate_minus_20_cnt,
                        COALESCE(COUNT(CASE WHEN quality = -40 THEN 1 END), 0) AS count_rate_minus_40_cnt,
                        COALESCE(COUNT(CASE WHEN quality = -60 THEN 1 END), 0) AS count_rate_minus_60_cnt,
                        COALESCE(COUNT(CASE WHEN quality = -80 THEN 1 END), 0) AS count_rate_minus_80_cnt
                      FROM {$poolTable}
                    ) AS city_data
                    SET
                      phone_count = city_data.count_phone_cnt,
                      order_count = city_data.count_order_cnt,
                      call_cnt = city_data.total_call_cnt,
                      call_cnt_1_6 = city_data.total_call_cnt_1_6,
                      call_cnt_1_8 = city_data.total_call_cnt_1_8,
                      call_cnt_9_15 = city_data.total_call_cnt_9_15,
                      call_cnt_16_25 = city_data.total_call_cnt_16_25,
                      call_cnt_26_45 = city_data.total_call_cnt_26_45,
                      call_cnt_46_90 = city_data.total_call_cnt_46_90,
                      call_cnt_7_above = city_data.total_call_cnt_7_above,
                      call_cnt_9_above = city_data.total_call_cnt_9_above,
                      call_cnt_91_above = city_data.total_call_cnt_91_above,
                      rate_95_cnt = city_data.count_rate_95_cnt,
                      rate_90_cnt = city_data.count_rate_90_cnt,
                      rate_85_cnt = city_data.count_rate_85_cnt,
                      rate_80_cnt = city_data.count_rate_80_cnt,
                      rate_60_cnt = city_data.count_rate_60_cnt,
                      rate_40_cnt = city_data.count_rate_40_cnt,
                      rate_20_cnt = city_data.count_rate_20_cnt,
                      rate_10_cnt = city_data.count_rate_10_cnt,
                      rate_0_cnt = city_data.count_rate_0_cnt,
                      rate_minus_20_cnt = city_data.count_rate_minus_20_cnt,
                      rate_minus_40_cnt = city_data.count_rate_minus_40_cnt,
                      rate_minus_60_cnt = city_data.count_rate_minus_60_cnt,
                      rate_minus_80_cnt = city_data.count_rate_minus_80_cnt
                    WHERE a_pool.id = {$pool_id}
                ";
                DB::statement($sql_4);


                DB::commit();

            }
            catch (Exception $e)
            {
                DB::rollback();
                $msg = $e->getMessage();

                dd($msg);
                return;
            }

        }
        else return;


    }
}
