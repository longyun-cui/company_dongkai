@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '统计' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description','统计 - 管理员后台系统 - 兆益信息')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:8px 0;">

                <h3 class="box-title">综合统计</h3>

            </div>


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


            <div class="box-body">

                <div class="box-header with-border" style="margin:0;text-align:center;">
                    <h3 class="box-title statistic-title"></h3>
                </div>
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-comprehensive-order"></div>
                    </div>
                </div>
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-comprehensive-finance"></div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


{{--分量统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">分量统计</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-component">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-component">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="component-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />

                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-component">
                            <i class="fa fa-chevron-right"></i>
                        </button>


                        <select class="form-control form-filter" name="component-staff">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="component-client">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="component-route">
                            <option value="-1">选择线路</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="component-pricing">
                            <option value="-1">选择定价</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter select2-container select2-car" name="component-car">
                            <option value="-1">选择车辆</option>
                        </select>

                        <select class="form-control form-filter" name="component-type">
                            <option value ="-1">订单类型</option>
                            <option value ="1">自有</option>
                            <option value ="11">空单</option>
                            <option value ="41">外配·配货</option>
                            <option value ="61">外请·调车</option>
                        </select>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-component">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-component">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-car-order"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-car-finance"></div>
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


