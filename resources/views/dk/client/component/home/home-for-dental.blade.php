<div class="row tab-pane active" id="tab-home">

    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="box box-primary box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">财务统计</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="">
                        <a href="javascript:void(0);">
                            累计充值
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $funds_recharge_total or '0' }}</text> 元
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            累计消费
                            <span class="pull-right">
                                                <text class="text-blue font-20px">{{ $funds_consumption_total or '0' }}</text> 元
                                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            余额
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $funds_balance or '0' }}</text> 元
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">工单统计</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="">
                        <a href="javascript:void(0);">
                            总计
                            <span class="pull-right">
                                <text class="text-black font-20px">{{ $order_count_for_all or '' }}</text> 单
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            本月
                            <span class="pull-right">
                               <text class="text-green font-20px">{{ $order_count_for_month or '' }}</text> 单
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            今日
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $order_count_for_today or '' }}</text> 单
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>