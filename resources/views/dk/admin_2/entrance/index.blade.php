@extends(env('TEMPLATE_DK_ADMIN_2').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--订单统计--}}
<div class="row _none">
    <div class="col-md-12">

        <div class="box _none-">

            <div class="box-header">
                <h3 class="box-title">工单统计</h3>
            </div>

            <div class="box-body">


                {{--总计--}}
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title comprehensive-month-title">总计</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-user text-red margin-right-4px"></i>
                                        <span>总计</span>
                                        <span class="pull-right"><b class="badge bg-green">{{ $order_count->order_count_for_all or '0' }}</b></span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-clock-o text-red margin-right-4px"></i>
                                        <span>发布</span>
                                        <span class="pull-right"><b class="badge bg-aqua">{{ $order_count->order_count_for_put_on or '0' }}</b></span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar text-green margin-right-4px"></i>
                                        <span>已接单</span>
                                        <span class="pull-right"><b class="badge bg-blue">{{ $order_count->order_count_for_taken or '0' }}</b></span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-calendar-check-o text-green margin-right-4px"></i>
                                        <span>成交</span>
                                        <span class="pull-right"><b class="badge bg-orange">{{ $order_count->order_count_for_deal or '0' }}</b></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>


    </div>
</div>
<div class="row">
    <div class="col-md-12">

        <div class="box _none">
            <div class="box-header">
                <h3 class="box-title">Application Buttons</h3>
            </div>
            <div class="box-body">
                <a class="btn btn-app">
                    <span class="badge bg-yellow">{{ $order_count->order_count_for_all or '0' }}</span>
                    <i class="fa fa-bullhorn"></i> 总计
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-green">{{ $order_count->order_count_for_put_on or '0' }}</span>
                    <i class="fa fa-barcode"></i> 导入
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-purple">{{ $order_count->order_count_for_taken or '0' }}</span>
                    <i class="fa fa-users"></i> 已接单
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-teal">{{ $order_count->order_count_for_deal or '0' }}</span>
                    <i class="fa fa-inbox"></i> 已成交
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-aqua">12</span>
                    <i class="fa fa-envelope"></i> Inbox
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-red">531</span>
                    <i class="fa fa-heart-o"></i> Likes
                </a>
            </div>
        </div>

        <div class="callout callout-success- bg-white">

            <h4>工单统计</h4>

            <div class="callout-body">

                <span>【总计】</span>
                <span>总计 <text class="text-black font-24px">{{ $order_count->order_count_for_all or '0' }}</text> 单</span>
                <span>已发布 <text class="text-black font-24px">{{ $order_count->order_count_for_put_on or '0' }}</text> 单</span>
                <span>已接单 <text class="text-black font-24px">{{ $order_count->order_count_for_taken or '0' }}</text> 单</span>
                <span>已成交 <text class="text-black font-24px">{{ $order_count->order_count_for_deal or '0' }}</text> 单</span>
            </div>

            <div class="box box-info margin-top-32px">

                {{--<div class="box-header">--}}
                {{--</div>--}}

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="eChart-order-statistics" style="width:100%;height:320px;"></div>
                        </div>
                    </div>
                </div>

                {{--<div class="box-footer">--}}
                {{--</div>--}}

            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
    .btn-app>.badge { position: absolute; top: -6px; right: -10px; font-size: 12px; font-weight: 400; }
</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {


        // 每日订单量
        var $order_this_month_res = new Array();
        $.each({!! $statistics_order_this_month_data !!},function(key,v){
            $order_this_month_res[(v.day - 1)] = { value:v.sum, name:v.day };
        });
        var $order_last_month_res = new Array();
        $.each({!! $statistics_order_last_month_data !!},function(key,v){
            $order_last_month_res[(v.day - 1)] = { value:v.sum, name:v.day };
        });

        var $option_order_statistics = {
            title: {
                text: '每日订单量统计【本月/上月】'
            },
            tooltip : {
                trigger: 'axis',
                axisPointer: {
                    type: 'line',
                    label: {
                        backgroundColor: '#6a7985'
                    }
                }
            },
            legend: {
                data:['订单量']
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    axisLabel : { interval:0 },
                    data : [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                    ]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'本月',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $order_this_month_res
                },
                {
                    name:'上月',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $order_last_month_res
                }
            ]
        };
        var $myChart_order_statistics = echarts.init(document.getElementById('eChart-order-statistics'));
        $myChart_order_statistics.setOption($option_order_statistics);

    });
</script>
@endsection