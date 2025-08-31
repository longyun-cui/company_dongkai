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
            $cdrTable = (new DK_VOS_CDR_Current)->getTable();
            $poolTable = $table;

            // 设置日期条件（示例：2023-01-01）
            $startDate = $last_sync_date; // 替换为实际需要的日期

            // 启动数据库事务
            DB::beginTransaction();
            try
            {

                // 构建子查询
                $subQuery = DB::table($cdrTable)
                    ->selectRaw("
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
                ")
                    ->where('call_date', '>', $startDate) // 添加日期条件
                    ->groupBy('phone');

                // 执行累加更新操作
                DB::table("$poolTable AS p")
                    ->joinSub($subQuery, 'c', function ($join) {
                        $join->on('p.phone', '=', 'c.phone');
                    })
                    ->update([
                        'p.call_cnt' => DB::raw('p.call_cnt + c.cnt'),
                        'p.call_cnt_1_6' => DB::raw('p.call_cnt_1_6 + c.cnt_1_6'),
                        'p.call_cnt_1_8' => DB::raw('p.call_cnt_1_8 + c.cnt_1_8'),
                        'p.call_cnt_9_15' => DB::raw('p.call_cnt_9_15 + c.cnt_9_15'),
                        'p.call_cnt_16_25' => DB::raw('p.call_cnt_16_25 + c.cnt_16_25'),
                        'p.call_cnt_26_45' => DB::raw('p.call_cnt_26_45 + c.cnt_26_45'),
                        'p.call_cnt_46_90' => DB::raw('p.call_cnt_46_90 + c.cnt_46_90'),
                        'p.call_cnt_7_above' => DB::raw('p.call_cnt_7_above + c.cnt_7_above'),
                        'p.call_cnt_9_above' => DB::raw('p.call_cnt_9_above + c.cnt_9_above'),
                        'p.call_cnt_91_above' => DB::raw('p.call_cnt_91_above + c.cnt_91_above'),
                    ]);

                $pool->last_sync_date = $current_date;
                $pool->save();



                // 构建子查询
                $subQuery_for_Order = DB::table("$poolTable AS pool_sub")
                    ->selectRaw("
                        pool_sub.phone,
                        COUNT(o.order_phone) AS order_count,
                        MAX(o.order_date) AS order_date
                    ")
                    ->leftJoin("$orderTable AS o", 'pool_sub.phone', '=', 'o.order_phone')
                    ->groupBy('pool_sub.phone');

                // 执行更新操作
                DB::table("$poolTable AS pool")
                    ->joinSub($subQuery_for_Order, 'sub', function ($join) {
                        $join->on('pool.phone', '=', 'sub.phone');
                    })
                    ->update([
                        'pool.order_cnt' => DB::raw('sub.order_count'),
                        'pool.order_date' => DB::raw('sub.order_date'),
                    ]);



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
