<div class="row tab-pane active" id="tab-home">

    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">项目统计</h3>
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
                                <text class="text-black font-20px">{{ $project->project_count_for_all or '' }}</text> 个
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            启用项目
                            <span class="pull-right">
                               <text class="text-green font-20px">{{ $project->project_count_for_enable or '' }}</text> 个
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            总充值
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $project->project_sum_for_recharge or 0 }}</text> 元
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            总消费
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $project->project_sum_for_consumption or 0 }}</text> 元
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="box box-primary box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">交付统计</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="">
                        <a href="javascript:void(0);">
                            今日交付
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $daily->today_quantity or '0' }}</text> 单
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            当月交付
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $daily->month_quantity or '0' }}</text> 单
                            </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="javascript:void(0);">
                            累计交付
                            <span class="pull-right">
                                <text class="text-blue font-20px">{{ $daily->total_quantity or '0' }}</text> 单
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>