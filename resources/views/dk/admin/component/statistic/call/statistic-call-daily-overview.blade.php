<div class="module-wrapper control-wrapper statistic-call-daily-overview-clone" data-item-category="statistic-call"  data-function="statistic_get_data_for_call_daily_overview">


    <div class="row">
        <div class="col-md-12 datatable-search-row search-wrapper ">
            <div class="pull-right">


                <input type="hidden" name="statistic-call-time-type" class="time-type" value="date" readonly>

                {{--            <select class="search-filter form-filter filter-xl select2-project-c" name="call-project">--}}
                {{--                <option value="-1">选择项目</option>--}}
                {{--            </select>--}}

                {{--选择项目--}}
                <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" name="call-project">
                    <option value="-1">选择项目</option>
                    @foreach($project_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>

                {{--按天查看--}}
                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre-c" data-target="statistic-call-date">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="statistic-call-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next-c" data-target="statistic-call-date">
                    <i class="fa fa-chevron-right"></i>
                </button>
                <button type="button" class="btn btn-success btn-filter filter-submit-c" data-time-type="date" data-function="">
                    <i class="fa fa-search"></i> 按日查询
                </button>

                <button type="button" class="btn btn-default btn-filter filter-empty-c">
                    <i class="fa fa-remove"></i> 清空
                </button>

                <button type="button" class="btn btn-default btn-filter filter-refresh-c _none">
                    <i class="fa fa-circle-o-notch"></i> 刷新
                </button>

                <button type="button" class="btn btn-default btn-filter filter-cancel-c _none">
                    <i class="fa fa-undo"></i> 重置
                </button>


            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title call-title">【通话统计】</h3>
            </div>


            {{--坐席--}}
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">坐席报单</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <ul class="nav nav-stacked">
                            <li class="dental_for_all">
                                <a href="javascript:void(0);">报单量<span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                            </li>
                            <li class="dental_for_inspected_all">
                                <a href="javascript:void(0);">审核量<span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                            </li>
                            <li class="dental_for_inspected_accepted">
                                <a href="javascript:void(0);">通过<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{--成单分析--}}
            <div class="col-xs-12 col-sm-6 col-md-3">

                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">成单分析</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="nav nav-stacked">
                            <li class="order_s">
                                <a href="javascript:void(0);">通话量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                                <a href="javascript:void(0);">通话量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            {{--成单分析--}}
            <div class="col-xs-12 col-sm-6 col-md-3">

                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">成单分析2</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="nav nav-stacked">
                            <li class="call_for_all">
                                <a href="javascript:void(0);">通话量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                            </li>
                            <li class="order_inspected_dental_for_inspected_accepted">
                                <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                            <li class="order_inspected_dental_for_inspected_accepted_inside">
                                <a href="javascript:void(0);">内部通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                            <li class="order_inspected_dental_for_inspected_repeated">
                                <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                            </li>
                            <li class="order_inspected_dental_for_inspected_refused">
                                <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>


            {{--甲方--}}
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">通话统计</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="nav nav-stacked">
                            <li class="call_for_all">
                                <a href="javascript:void(0);">通话量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>

                            <li class="call_for_dealt">
                                <a href="javascript:void(0);">成单通话量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>