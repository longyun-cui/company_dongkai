<div class="row datatable-body- datatable-wrapper statistic-comprehensive-overview-clone" data-datatable-item-category="statistic-comprehensive-overview">


    <div class="col-md-12 datatable-search-row datatable-search-box">
        <div class="pull-right">



            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-btn time-picker-move picker-move-pre date-pick-pre-for-comprehensive">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="comprehensive-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-btn time-picker-move picker-move-next date-pick-next-for-comprehensive">
                <i class="fa fa-chevron-right"></i>
            </button>

            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter month-picker-btn time-picker-move picker-move-pre month-pick-pre-for-comprehensive">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker-c" name="comprehensive-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" style="" />
            <button type="button" class="btn btn-default btn-filter month-picker-btn time-picker-move picker-move-next month-pick-next-for-comprehensive">
                <i class="fa fa-chevron-right"></i>
            </button>

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
            <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" name="comprehensive-project">
                <option value="-1">选择项目</option>
                @foreach($project_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>


            <button type="button" class="btn btn-success btn-filter filter-submit" id="filter-submit-for-comprehensive">
                <i class="fa fa-search"></i> 搜索
            </button>
            <button type="button" class="btn btn-default btn-filter filter-cancel" id="filter-cancel-for-comprehensive">
                <i class="fa fa-circle-o-notch"></i> 重置
            </button>

{{--            <button type="button" class="btn btn-success btn-filter filter-submit">--}}
{{--                <i class="fa fa-search"></i> 全部查询--}}
{{--            </button>--}}

{{--            <button type="button" class="btn btn-default btn-filter filter-empty">--}}
{{--                <i class="fa fa-remove"></i> 清空--}}
{{--            </button>--}}

{{--            <button type="button" class="btn btn-default btn-filter filter-refresh">--}}
{{--                <i class="fa fa-circle-o-notch"></i> 刷新--}}
{{--            </button>--}}

{{--            <button type="button" class="btn btn-default btn-filter filter-cancel">--}}
{{--                <i class="fa fa-undo"></i> 重置--}}
{{--            </button>--}}

        </div>
    </div>


    <div class="col-md-12">

        <div class="box-header with-border" style="margin:4px 0;">
            <h3 class="box-title comprehensive-title">【综合概览】</h3>
        </div>

        {{--日--}}
        <div class="col-xs-12 col-sm-6 col-md-4">

            {{--坐席--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">坐席 （<span class="comprehensive-day-title">今日概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_today_for_published">
                            <a href="javascript:void(0);">报单量（客服提交） <span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_all">
                            <a href="javascript:void(0);">审核量（质检处理） <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_today_for_delivered_all">
                            <a href="javascript:void(0);">交付量（运营处理）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                        </li>
                        @endif
                        <li class="order_of_today_for_delivered_effective">
                            <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_today_for_delivered_completed">
                            <a href="javascript:void(0);">已交付（交付客户）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_discount">
                            <a href="javascript:void(0);">折扣交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_suburb">
                            <a href="javascript:void(0);">郊区交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_inside">
                            <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_repeated">
                            <a href="javascript:void(0);">重复<span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_rejected">
                            <a href="javascript:void(0);">拒绝<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_delivered_effective_rate">
                            <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{--甲方--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">甲方  （<span class="comprehensive-day-title">今日概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="deliverer_of_today_for_completed">
                            <a href="javascript:void(0);">总交付量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_today_for_completed_by_same_day">
                            <a href="javascript:void(0);">-- 当日工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_today_for_completed_by_other_day">
                            <a href="javascript:void(0);">-- 昨转今工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_today_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--运营--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">运营工作量  （<span class="comprehensive-day-title">今日概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_all">
                                总交付量（工作单量） <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_all_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_all_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_completed">
                                已交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_completed_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_completed_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_discount">
                                折扣交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_discount_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_discount_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_suburb">
                                郊区交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_suburb_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_suburb_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_inside">
                                内部交付 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_inside_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_today_for_inside_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                        </li>

                        <li class="deliverer_of_today_for_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_today_for_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_today_for_rejected">
                            <a href="javascript:void(0);">驳回量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        {{--<li class="deliverer_of_today_for_effective">--}}
                        {{--<a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
                        {{--</li>--}}
                        {{--<li class="deliverer_of_today_for_effective_rate">--}}
                        {{--<a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
                        {{--</li>--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_today_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--审核--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">审核工作量  （<span class="comprehensive-day-title">今日概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_today_for_inspected_all">
                            <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_accepted">
                            <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_accepted_discount">
                            <a href="javascript:void(0);">折扣通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_accepted_suburb">
                            <a href="javascript:void(0);">郊区通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_accepted_inside">
                            <a href="javascript:void(0);">内部通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_today_for_inspected_refused">
                            <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>




        {{--月--}}
        <div class="col-xs-12 col-sm-6 col-md-4">

            {{--坐席--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">坐席 （<span class="comprehensive-month-title">当月概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_month_for_published">
                            <a href="javascript:void(0);">报单量（客服） <span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_all">
                            <a href="javascript:void(0);">审核量（质检） <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_month_for_delivered_all">
                            <a href="javascript:void(0);">交付量（运营）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                        </li>
                        @endif
                        <li class="order_of_month_for_delivered_effective">
                            <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_month_for_delivered_completed">
                            <a href="javascript:void(0);">已交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_discount">
                            <a href="javascript:void(0);">折扣交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_suburb">
                            <a href="javascript:void(0);">郊区交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_inside">
                            <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_repeated">
                            <a href="javascript:void(0);">重复<span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_rejected">
                            <a href="javascript:void(0);">拒绝<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_delivered_effective_rate">
                            <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{--甲方--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">甲方  （<span class="comprehensive-month-title">当月概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="deliverer_of_month_for_completed">
                            <a href="javascript:void(0);">总交付量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_month_for_completed_by_same_day">
                            <a href="javascript:void(0);">-- 当日工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_month_for_completed_by_other_day">
                            <a href="javascript:void(0);">-- 昨转今工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_month_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--运营--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">运营工作量  （<span class="comprehensive-month-title">当月概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_all">
                                总交付量（工作单量） <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_all_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_all_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_completed">
                                已交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_completed_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_completed_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_discount">
                                折扣交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_discount_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_discount_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_suburb">
                                郊区交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_suburb_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_suburb_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_inside">
                                内部交付 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_inside_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_month_for_inside_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                        </li>

                        <li class="deliverer_of_month_for_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_month_for_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_month_for_rejected">
                            <a href="javascript:void(0);">驳回量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        {{--<li class="deliverer_of_month_for_effective">--}}
                        {{--<a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
                        {{--</li>--}}
                        {{--<li class="deliverer_of_month_for_effective_rate">--}}
                        {{--<a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
                        {{--</li>--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_month_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--审核--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">审核工作量  （<span class="comprehensive-month-title">当月概览</span>）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_month_for_inspected_all">
                            <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_accepted">
                            <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_accepted_discount">
                            <a href="javascript:void(0);">折扣通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_accepted_suburb">
                            <a href="javascript:void(0);">郊区通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_accepted_inside">
                            <a href="javascript:void(0);">内部通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_month_for_inspected_refused">
                            <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>




        {{--全部--}}
        <div class="col-xs-12 col-sm-6 col-md-4">

            {{--坐席--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">坐席（总量）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_all_for_published">
                            <a href="javascript:void(0);">报单量（客服） <span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_all">
                            <a href="javascript:void(0);">审核量（质检） <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_all_for_delivered_all">
                            <a href="javascript:void(0);">交付量（运营）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                        </li>
                        @endif
                        <li class="order_of_all_for_delivered_effective">
                            <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if($me->department_district_id <= 0)
                        <li class="order_of_all_for_delivered_completed">
                            <a href="javascript:void(0);">已交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_discount">
                            <a href="javascript:void(0);">折扣交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_suburb">
                            <a href="javascript:void(0);">郊区交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_inside">
                            <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_repeated">
                            <a href="javascript:void(0);">重复<span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_rejected">
                            <a href="javascript:void(0);">拒绝<span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_delivered_effective_rate">
                            <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{--甲方--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">甲方  （总量）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="deliverer_of_all_for_completed">
                            <a href="javascript:void(0);">总交付量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_all_for_completed_by_same_day">
                            <a href="javascript:void(0);">-- 当日工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_all_for_completed_by_other_day">
                            <a href="javascript:void(0);">-- 昨转今工单 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_all_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--运营--}}
            @if($me->department_district_id <= 0)
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">运营工作量  （总量）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_all">
                                总交付量（工作单量） <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_all_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_all_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-blue"></b> 单</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_completed">
                                已交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_completed_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_completed_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_discount">
                                折扣交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_discount_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_discount_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_suburb">
                                郊区交付 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_suburb_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_suburb_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-green"></b> 单</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_inside">
                                内部交付 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_inside_by_same_day">
                                -- 当日 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                            <a href="javascript:void(0);" class="deliverer_of_all_for_inside_by_other_day">
                                -- 昨转今 <span class="pull-right"><b class="badge bg-olive"></b> 单</span>
                            </a>
                        </li>

                        <li class="deliverer_of_all_for_tomorrow">
                            <a href="javascript:void(0);">隔日交付（今转明） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_all_for_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="deliverer_of_all_for_rejected">
                            <a href="javascript:void(0);">驳回量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                        {{--<li class="deliverer_of_all_for_effective">--}}
                        {{--<a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
                        {{--</li>--}}
                        {{--<li class="deliverer_of_all_for_effective_rate">--}}
                        {{--<a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
                        {{--</li>--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li class="distributed_of_all_for_all">
                                <a href="javascript:void(0);">总分发量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{--审核--}}
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">审核工作量  （总量）</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="order_of_all_for_inspected_all">
                            <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_accepted">
                            <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_accepted_discount">
                            <a href="javascript:void(0);">折扣通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_accepted_suburb">
                            <a href="javascript:void(0);">郊区通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_accepted_inside">
                            <a href="javascript:void(0);">内部通过量 <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_repeated">
                            <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge bg-orange"></b> 单</span></a>
                        </li>
                        <li class="order_of_all_for_inspected_refused">
                            <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge bg-red"></b> 单</span></a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>


</div>