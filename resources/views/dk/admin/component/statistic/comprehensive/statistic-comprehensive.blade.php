{{--综合概览2--}}
<div class="row module-wrapper comprehensive-wrapper statistic-comprehensive-clone" data-item-category="statistic-comprehensive"  data-function="statistic_get_data_for_comprehensive">


    <div class="col-md-12 datatable-search-row search-wrapper ">
        <div class="pull-right">


            <input type="hidden" name="statistic-comprehensive-time-type" class="time-type" value="date" readonly>

{{--            <select class="search-filter form-filter filter-xl select2-project-c" name="comprehensive-project">--}}
{{--                <option value="-1">选择项目</option>--}}
{{--            </select>--}}

            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                <select class="search-filter form-filter filter-xl select2-box-c" multiple="multiple" name="comprehensive-department-district[]">
                    <option value="-1">选择团队</option>
                    @foreach($department_district_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            @endif

            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" name="statistic-comprehensive-project">
                <option value="-1">选择项目</option>
                @foreach($project_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>

            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre-c" data-target="statistic-comprehensive-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="statistic-comprehensive-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next-c" data-target="statistic-comprehensive-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit-c" data-time-type="date" data-function="">
                <i class="fa fa-search"></i> 按日查询
            </button>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre-c" data-target="statistic-comprehensive-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-comprehensive-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next-c" data-target="statistic-comprehensive-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit-c" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段导出--}}
            <input type="text" class="search-filter filter-keyup date_picker-c" name="statistic-comprehensive-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter filter-keyup date_picker-c" name="statistic-comprehensive-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

            <button type="button" class="btn btn-success btn-filter filter-submit-c" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>


            <button type="button" class="btn btn-success btn-filter filter-submit-c" data-submit-default="default">
                <i class="fa fa-search"></i> 全部查询
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


    <div class="col-md-12">

        <div class="box-header with-border" style="margin:4px 0;">
            <h3 class="box-title comprehensive-title">【口腔统计】</h3>
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
                        <li class="order_published_dental_for_published">
                            <a href="javascript:void(0);">报单量<span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_all">
                            <a href="javascript:void(0);">审核量<span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_accepted">
                            <a href="javascript:void(0);">通过<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_effective_rate">
                            <a href="javascript:void(0);">通过率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_repeated">
                            <a href="javascript:void(0);">重复<span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_refused">
                            <a href="javascript:void(0);">拒绝<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_appealed">
                            <a href="javascript:void(0);">申诉<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_appealed_success">
                            <a href="javascript:void(0);">申诉·成功<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_appealed_fail">
                            <a href="javascript:void(0);">申诉·失败<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_effective">
                            <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_published_dental_for_inspected_effective_rate">
                            <a href="javascript:void(0);">有效率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{--审核--}}
        <div class="col-xs-12 col-sm-6 col-md-3">

            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">质检工作量</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_inspected_dental_for_inspected_all">
                            <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        <li class="order_inspected_dental_for_inspected_accepted">
                            <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_inspected_dental_for_inspected_accepted_discount">
                            <a href="javascript:void(0);">折扣通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_inspected_dental_for_inspected_accepted_suburb">
                            <a href="javascript:void(0);">郊区通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
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


        {{--运营--}}
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">运营工作量</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_all">
                                总交付量（工作单量） <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_all_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_all_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_completed">
                                已交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_completed_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_completed_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_inside">
                                内部交付 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_inside_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="order_delivered_dental_for_inside_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                        </li>

                        <li class="order_delivered_dental_for_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_delivered_dental_for_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_delivered_dental_for_rejected">
                            <a href="javascript:void(0);">驳回量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        {{--                                <li class="deliverer_of_all_for_effective">--}}
                        {{--                                    <a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
                        {{--                                </li>--}}
                        {{--                                <li class="deliverer_of_all_for_effective_rate">--}}
                        {{--                                    <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
                        {{--                                </li>--}}
                    </ul>
                </div>
            </div>
        </div>


        {{--甲方--}}
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">甲方交付</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="delivery_dental_for_all">
                            <a href="javascript:void(0);">总交付量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
{{--                        <li class="deliverer_of_all_for_completed_by_same_day">--}}
{{--                            <a href="javascript:void(0);">-- 当日工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
{{--                        </li>--}}
{{--                        <li class="deliverer_of_all_for_completed_by_other_day">--}}
{{--                            <a href="javascript:void(0);">-- 昨转今工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
{{--                        </li>--}}


                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="delivery_dental_for_distributed">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

    </div>


</div>