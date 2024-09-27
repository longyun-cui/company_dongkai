@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '统计' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '统计' }} - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">


            <div class="box-body datatable-body item-main-body" id="statistic-for-comprehensive">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">



                        {{--按天查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-comprehensive">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="comprehensive-date" placeholder="选择日期" readonly="readonly" value="{{ $date }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-comprehensive">
                            <i class="fa fa-chevron-right"></i>
                        </button>

                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-comprehensive">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="comprehensive-month" placeholder="选择月份" readonly="readonly" value="{{ $month }}" data-default="{{ date('Y-m') }}" style="" />
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-comprehensive">
                            <i class="fa fa-chevron-right"></i>
                        </button>

                        <select class="form-control form-filter select2-container item-select2-project" name="comprehensive-project" style="width:120px;">
                            @if(isset($project_id) && $project_id > 0)
                                <option value="-1">选择项目</option>
                                <option value="{{ $project_id  }}" selected="selected">{{ $project_title }}</option>
                            @else
                                <option value="-1">选择项目</option>
                            @endif
                        </select>

                        @if(in_array($me->user_type,[0,1,9,11,71,77]))
                        <select class="form-control form-filter select2-container select-select2" multiple="multiple" name="comprehensive-department-district[]" style="width:120px;">
                            <option value="-1">选择大区</option>
                            @foreach($department_district_list as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                        @endif


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-comprehensive">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-comprehensive">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>


            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title comprehensive-title">【综合概览】</h3>
            </div>
            <div class="box-body">


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
                                    <a href="javascript:void(0);">报单量（客服） <span class="pull-right"><b class="badge bg-black"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_inspected_all">
                                    <a href="javascript:void(0);">审核量（质检） <span class="pull-right"><b class="badge bg-blue"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_delivered_all">
                                    <a href="javascript:void(0);">交付量（运营）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_delivered_effective">
                                    <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_delivered_completed">
                                    <a href="javascript:void(0);">已交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_delivered_tomorrow">
                                    <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_today_for_delivered_inside">
                                    <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
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
                            </ul>
                        </div>
                    </div>

                    {{--甲方--}}
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
                            </ul>
                        </div>
                    </div>

                    {{--运营--}}
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
{{--                                <li class="deliverer_of_today_for_effective">--}}
{{--                                    <a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
{{--                                </li>--}}
{{--                                <li class="deliverer_of_today_for_effective_rate">--}}
{{--                                    <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>

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
                                <li class="order_of_month_for_delivered_all">
                                    <a href="javascript:void(0);">交付量（运营）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                                </li>
                                <li class="order_of_month_for_delivered_effective">
                                    <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_month_for_delivered_completed">
                                    <a href="javascript:void(0);">已交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_month_for_delivered_tomorrow">
                                    <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_month_for_delivered_inside">
                                    <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
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
                            </ul>
                        </div>
                    </div>

                    {{--甲方--}}
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
                            </ul>
                        </div>
                    </div>

                    {{--运营--}}
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
{{--                                <li class="deliverer_of_month_for_effective">--}}
{{--                                    <a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
{{--                                </li>--}}
{{--                                <li class="deliverer_of_month_for_effective_rate">--}}
{{--                                    <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>

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
                            <h3 class="box-title">总量概览</h3>
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
                                <li class="order_of_all_for_delivered_all">
                                    <a href="javascript:void(0);">交付量（运营）<span class="pull-right"><b class="badge bg-purple"></b> 单</span></a>
                                </li>
                                <li class="order_of_all_for_delivered_effective">
                                    <a href="javascript:void(0);">有效量（客服业绩量） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_all_for_delivered_completed">
                                    <a href="javascript:void(0);">已交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_all_for_delivered_tomorrow">
                                    <a href="javascript:void(0);">隔日交付（今转明）<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
                                </li>
                                <li class="order_of_all_for_delivered_inside">
                                    <a href="javascript:void(0);">内部交付<span class="pull-right"><b class="badge bg-green"></b> 单</span></a>
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
                            </ul>
                        </div>
                    </div>

                    {{--甲方--}}
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">甲方  （总量概览）</h3>
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
                            </ul>
                        </div>
                    </div>

                    {{--运营--}}
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">运营工作量  （总量概览）</h3>
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
{{--                                <li class="deliverer_of_all_for_effective">--}}
{{--                                    <a href="javascript:void(0);">有效交付量（当日工单） <span class="pull-right"><b class="badge bg-green"></b> 单</span></a>--}}
{{--                                </li>--}}
{{--                                <li class="deliverer_of_all_for_effective_rate">--}}
{{--                                    <a href="javascript:void(0);">有效交付率 <span class="pull-right"><b class="badge bg-aqua"></b> %</span></a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>

                    {{--审核--}}
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">审核工作量  （总量概览）</h3>
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

            <div class="box-footer with-border" style="margin:8px 0;">
            </div>








            <div class="box-header with-border _none" style="margin:8px 0;">
                <h3 class="box-title">【订单统计】</h3>
            </div>
            <div class="box-body _none">
                <div class="row">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-comprehensive-order" style="height:600px;"></div>
                    </div>
                </div>
                <div class="row margin-top-32px _none">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-comprehensive-order-quantity"></div>
                    </div>
                </div>
                <div class="row margin-top-32px _none">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-comprehensive-order-income"></div>
                    </div>
                </div>
            </div>
            <div class="box-footer with-border" style="margin:8px 0;">
            </div>




            <div class="box-body _none">

                <div class="box-header with-border" style="margin:0;text-align:center;">
                    <h3 class="box-title statistic-title"></h3>
                </div>

            </div>

            <div class="box-footer with-border" style="margin:8px 0;">
            </div>

        </div>
    </div>
</div>


{{--订单统计--}}
<div class="row _none">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">订单统计</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-order">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-order">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="order-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-order">
                            <i class="fa fa-chevron-right"></i>
                        </button>


                        <select class="form-control form-filter" name="order-staff">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-client">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter select2-container select2-car" name="order-car">
                            <option value="-1">选择车辆</option>
                        </select>

                        <select class="form-control form-filter select2-container select2-trailer" name="order-trailer">
                            <option value="-1">选择车挂</option>
                        </select>

                        <select class="form-control form-filter select2-container select2-driver" name="order-driver">
                            <option value="-1">选择驾驶员</option>
                        </select>

                        <select class="form-control form-filter" name="order-type">
                            <option value ="-1">订单类型</option>
                            <option value ="1">自有</option>
                            <option value ="11">空单</option>
                            <option value ="41">外配·配货</option>
                            <option value ="61">外请·调车</option>
                        </select>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-order">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-order">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-order-quantity"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-order-income"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-css')
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
<style>
    .myChart { width:100%;height:240px; }
    .datatable-search-row .input-group .time-picker-btn { width:30px; }
    .datatable-search-row .input-group .month-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker,
    .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group button { width:80px; }
    .datatable-search-row .input-group select { width:100px; }
    .datatable-search-row .input-group .select2-container { width:120px; }
</style>
@endsection




@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
@include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-index-script')
<script>
    $(function(){

        // 初始化
        $("#filter-submit-for-comprehensive").click();

    });
</script>
@endsection


