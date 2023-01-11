@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))L.@endif
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--车辆统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">
            <h4>车辆统计</h4>
            <div class="callout-body">
                <span>总计 <text class="text-purple- font-24px">{{ $car_all_count or 0 }}</text> 辆</span>
                <span>车辆 <text class="text-green font-24px">{{ $car_car_count or 0 }}</text> 辆</span>
                <span>车挂 <text class="text-purple font-24px">{{ $car_trailer_count or 0 }}</text> 辆</span>
                <span>工作中 <text class="text-green font-24px">{{ $car_working_count or '' }}</text> 辆</span>
                <span>待发车 <text class="text-blue font-24px">{{ $car_waiting_for_departure_count or 0 }}</text> 辆</span>
                <span>空闲 <text class="text-red font-24px">{{ $car_idle_count or '' }}</text> 辆</span>
            </div>
        </div>
    </div>
</div>


{{--订单统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>订单统计</h4>

            <div class="callout-body">
                <span>总计 <text class="text-black font-24px">{{ $order_all_count or 0 }}</text> 单</span>
                <span>待发布 <text class="text-teal font-24px">{{ $order_unpublished_count or 0 }}</text> 单</span>
                <span>待发车 <text class="text-aqua font-24px">{{ $order_waiting_for_departure_count or 0 }}</text> 单</span>
                <span>进行中 <text class="text-blue font-24px">{{ $order_working_count or 0 }}</text> 单</span>
                <span>待收款 <text class="text-orange font-24px">{{ $order_waiting_for_receipt_count or '' }}</text> 单</span>
                <span>已收款 <text class="text-blue font-24px">{{ $order_received_count or '' }}</text> 单</span>
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


{{--财务统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>财务统计</h4>

            <div class="callout-body">
                <span>本月收入 <text class="text-green font-24px">{{ $finance_this_month_income or 0 }}</text> 元</span>
                <span>本月支出 <text class="text-orange font-24px">{{ $finance_this_month_payout or 0 }}</text> 元</span>
                <span>净收入 <text class="text-red font-24px">{{ $finance_this_month_income - $finance_this_month_payout }}</text> 元</span>
                <br>
                <span>上月收入 <text class="text-green font-24px">{{ $finance_last_month_income or 0 }}</text> 元</span>
                <span>上月支出 <text class="text-orange font-24px">{{ $finance_last_month_payout or 0 }}</text> 元</span>
                <span>净收入 <text class="text-red font-24px">{{ $finance_last_month_income - $finance_last_month_payout }}</text> 元</span>
            </div>

            <div class="box box-info margin-top-32px">

                {{--<div class="box-header">--}}
                {{--</div>--}}

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="eChart-finance-statistics" style="width:100%;height:320px;"></div>
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

        // 每日收入
        var $income_res = new Array();
        $.each({!! $statistics_income_data !!},function(key,v){
            $income_res[(v.day - 1)] = { value:v.sum, name:v.day };
//            $income_res.push({ value:v.sum, name:v.date });
        });
        // 每日支出
        var $payout_res = new Array();
        $.each({!! $statistics_payout_data !!},function(key,v){
            $payout_res[(v.day - 1)] = { value:v.sum, name:v.day };
        });

        var option_finance_statistics = {
            title: {
                text: '当月财务【每日收入总额/每日支出总额】统计'
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
                data:['收入','支出']
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
                    name:'收入',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $income_res
                },
                {
                    name:'支出',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $payout_res
                }
            ]
        };
        var myChart_finance_statistics = echarts.init(document.getElementById('eChart-finance-statistics'));
        myChart_finance_statistics.setOption(option_finance_statistics);


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
                text: '本月/上月【每日订单量】统计'
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