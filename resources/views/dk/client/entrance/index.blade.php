@extends(env('TEMPLATE_DK_CLIENT').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--订单统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>财务统计</h4>

            <div class="callout-body">
                <span>累计充值 <text class="text-black font-24px">{{ $funds_recharge_total }}</text> 元</span>
                <span>累计消费 <text class="text-teal font-24px">{{ $funds_consumption_total }}</text> 元</span>
                <span>余额 <text class="text-red font-24px">{{ $funds_balance }}</text> 元</span>
            </div>

        </div>
    </div>
</div>


{{--订单统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>工单统计</h4>

            <div class="callout-body">
                <span>总计 <text class="text-black font-24px">{{ $order_count_for_all or '' }}</text> 单</span>
                <span>本月 <text class="text-teal font-24px">{{ $order_count_for_month or '' }}</text> 单</span>
                <span>今日 <text class="text-red font-24px">{{ $order_count_for_today or '' }}</text> 单</span>
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