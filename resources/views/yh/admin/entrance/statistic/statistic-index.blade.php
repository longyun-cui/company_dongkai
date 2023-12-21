@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


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

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-comprehensive">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="comprehensive-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" style="" />

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-comprehensive">
                            <i class="fa fa-chevron-right"></i>
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-comprehensive">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-comprehensive">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>


            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">【综合概览】</h3>
            </div>
            <div class="box-body">

                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">今日概览</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="order_count_of_today_for_all">
                                    <a href="javascript:void(0);">工单总量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_today_for_inspected">
                                    <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_today_for_accepted">
                                    <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_today_for_refused">
                                    <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_today_for_repeated">
                                    <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_today_for_rate">
                                    <a href="javascript:void(0);">通过率 <span class="pull-right"><b class="badge- bg-blue-"></b> %</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">当月概览</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="order_count_of_month_for_all">
                                    <a href="javascript:void(0);">工单总量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_month_for_inspected">
                                    <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_month_for_accepted">
                                    <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_month_for_refused">
                                    <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_month_for_repeated">
                                    <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_of_month_for_rate">
                                    <a href="javascript:void(0);">通过率 <span class="pull-right"><b class="badge- bg-blue-"></b> %</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-4">
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
                                <li class="order_count_for_all">
                                    <a href="javascript:void(0);">工单总量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_for_inspected">
                                    <a href="javascript:void(0);">审核量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_for_accepted">
                                    <a href="javascript:void(0);">通过量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_for_refused">
                                    <a href="javascript:void(0);">拒绝量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_for_repeated">
                                    <a href="javascript:void(0);">重复量 <span class="pull-right"><b class="badge- bg-blue-"></b> 单</span></a>
                                </li>
                                <li class="order_count_for_rate">
                                    <a href="javascript:void(0);">通过率 <span class="pull-right"><b class="badge- bg-blue-"></b> %</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
            <div class="box-footer with-border" style="margin:8px 0;">
            </div>




            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">【订单统计】</h3>
            </div>
            <div class="box-body">
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
<div class="row">
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

                        <select class="form-control form-filter" name="order-route">
                            <option value="-1">选择线路</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-pricing">
                            <option value="-1">选择定价</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
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


{{--财务统计--}}
<div class="row _none">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">财务统计</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-finance">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-finance">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="finance-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-finance">
                            <i class="fa fa-chevron-right"></i>
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-finance">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">员工看板</h3>


            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-order-list">

                <div class="tableArea">
                    <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax'>
                        <thead>

                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value ="-1">请选择操作类型</option>
                                <option value ="启用">启用</option>
                                <option value ="禁用">禁用</option>
                                <option value ="删除">删除</option>
                                <option value ="彻底删除">彻底删除</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
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
    .datatable-search-row .input-group .month-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker { width:90px;text-align:center; }
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
@include(env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-index-script')
<script>
    $(function(){

        // 初始化
        $("#filter-submit-for-comprehensive").click();

    });
</script>
@endsection


