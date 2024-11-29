@extends(env('TEMPLATE_DK_CUSTOMER').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header'){{ $head_title or '首页' }}@endsection
@section('description')@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--订单统计--}}
<div class="row _none">
    <div class="col-md-12">
        <div class="box box-info callout-success- bg-white">

            <div class="box-header with-border">
                <h3 class="box-title">设置</h3>
            </div>

            <div class="box-body">
{{--                <div style="line-height:29px;">--}}
{{--                    <button id="toggle-button" class="@if($me->customer_er->is_preferential == 1) toggle-button-on @else toggle-button-off @endif pull-left">--}}
{{--                        <span class="toggle-handle"></span>--}}
{{--                    </button>--}}
{{--                    <b style="float:left;height:29px;line-height:29px;margin-left:12px;">--}}
{{--                        <span class="toggle-handle-text">@if($me->customer_er->is_preferential == 1) 开启 @else 关闭 @endif</span>优选接单--}}
{{--                    </b>--}}
{{--                </div>--}}
            </div>

        </div>
    </div>
</div>


{{--订单统计--}}
<div class="row _none">
    <div class="col-md-12">
        <div class="box box-success callout-success- bg-white">

            <div class="box-header with-border">
                <h3 class="box-title">财务统计</h3>
            </div>

            <div class="box-body">
                <span>累计充值 <text class="text-black font-20px">{{ $funds_recharge_total }}</text> 元</span>
                <span>累计消费 <text class="text-teal font-20px">{{ $funds_consumption_total }}</text> 元</span>
                <span>冻结金额 <text class="text-teal font-20px">{{ $funds_obligation_total }}</text> 元</span>
                <span>余额 <text class="text-red font-20px">{{ $funds_balance }}</text> 元</span>
            </div>

        </div>
    </div>
</div>


{{----}}
@if(in_array($me->user_type,[0,1,9,11]))
<div class="row">
    <div class="col-md-12">
        <div class="box box-success callout-success- bg-white">

            <div class="box-body">


                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="box box-primary box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title comprehensive-month-title">单价</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="">
                                    <a href="javascript:void(0);">
                                        普通线索
                                        <span class="pull-right">
                                <text class="text-blue font-20px">{{ $me->customer_er->cooperative_unit_price_1 }}</text> 元/单
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        优选线索
                                        <span class="pull-right">
                                <text class="text-blue font-20px">{{ $me->customer_er->cooperative_unit_price_2 }}</text> 元/单
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        指派线索
                                        <span class="pull-right">
                                <text class="text-blue font-20px">{{ $me->customer_er->cooperative_unit_price_3 }}</text> 元/单
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        话单价格
                                        <span class="pull-right">
                                <text class="text-purple font-20px">{{ $me->customer_er->cooperative_unit_price_of_telephone }}</text> 元/单
                            </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title comprehensive-month-title">财务统计</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="">
                                    <a href="javascript:void(0);">
                                        累计充值
                                        <span class="pull-right">
                                <text class="text-black font-20px">{{ $funds_recharge_total }}</text> 元
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        累计消费
                                        <span class="pull-right">
                                <text class="text-green font-20px">{{ $funds_consumption_total }}</text> 元
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        余额
                                        <span class="pull-right">
                                <text class="text-blue font-20px">{{ $funds_balance }}</text> 元
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        冻结金额
                                        <span class="pull-right">
                                <text class="text-orange font-20px">{{ $funds_obligation_total }}</text> 元
                            </span>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="javascript:void(0);">
                                        可用余额
                                        <span class="pull-right">
                                <text class="text-red font-20px">{{ $funds_available }}</text> 元
                            </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="box box-warning box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title comprehensive-month-title">设置</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="nav nav-stacked">
                                <li class="" style="padding:10px 15px;height:40px;border-bottom:1px solid #f4f4f4;clear:both;">
                                    <div class="" style="">
                                        <span class="pull-left">
                                            <b class="toggle-handle-text">优选接单</b>
                                        </span>
                                        <button id="toggle-button" class="toggle-button pull-right
                                            @if($me->customer_er->is_preferential == 1) toggle-button-on
                                            @else toggle-button-off
                                            @endif
                                        ">
                                            <span class="toggle-handle"></span>
                                        </button>
                                    </div>
                                </li>
                                <li class="" style="padding:10px 15px;height:40px;clear:both;">
                                    <span class="pull-left">
                                        <b class="toggle-handle-text">员工自由接单</b>
                                    </span>
                                    <button id="toggle-button-for-staff-take" class="toggle-button pull-right
                                            @if($me->customer_er->is_staff_take == 1) toggle-button-on
                                            @else toggle-button-off
                                            @endif
                                    ">
                                        <span class="toggle-handle"></span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>
@endif


{{--订单统计--}}
<div class="row _none">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>工单统计</h4>

            <div class="callout-body">
                <span>总计 <text class="text-black font-20px">{{ $order_count_for_all or '' }}</text> 单</span>
                <span>本月 <text class="text-teal font-20px">{{ $order_count_for_month or '' }}</text> 单</span>
                <span>今日 <text class="text-red font-20px">{{ $order_count_for_today or '' }}</text> 单</span>
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

    .toggle-button {
        position: relative;
        width: 50px;
        height: 25px;
        background-color: #ccc;
        border: none;
        border-radius: 15px;
    }

    .toggle-handle {
        position: absolute;
        top: 0;
        width: 25px;
        height: 25px;
        background-color: #fff;
        border-radius: 50%;
    }

    .toggle-button.toggle-button-on { background-color: #66a3cc; transition: background-color 0.1s; }
    .toggle-button.toggle-button-off { background-color: #dddddd; transition: background-color 0.1s; }

    .toggle-button.toggle-button-on .toggle-handle { right: 0; background-color: #20e28b; transition: right 0.1s; }
    .toggle-button.toggle-button-off .toggle-handle { left: 0; background-color: #e00000; transition: left 0.1s; }

</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {

        // 【开关】
        $(".main-content").on('click', "#toggle-button", function() {
            $(this).toggleClass('toggle-button-on toggle-button-off');
            var handle = $(this).find('.toggle-handle');
            // handle.toggleClass('toggle-on toggle-off');
            if($(this).hasClass('toggle-button-on'))
            {
                $.post(
                    "{{ url('/setting/setting-customer') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate: "setting-customer-attribute",
                        operate_column: "is_preferential",
                        set_value: 1
                    },
                    function(data){
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg('已开启优选单');
                            $(this).find('.toggle-handle-text').html('开启');
                            handle.animate({'left': '25px'}, 'fast');
                            $('.menu-of-clue-preferential').show();

                        }
                    },
                    'json'
                );
            }
            else
            {
                $.post(
                    "{{ url('/setting/setting-customer') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate: "setting-customer-attribute",
                        operate_column: "is_preferential",
                        set_value: 0
                    },
                    function(data){
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg('已关闭优选单');
                            $(this).find('.toggle-handle-text').html('关闭');
                            handle.animate({'left': '0'}, 'fast');
                            $('.menu-of-clue-preferential').hide();
                        }
                    },
                    'json'
                );
            }
        });

        // 【开关】
        $(".main-content").on('click', "#toggle-button-for-staff-take", function() {
            $(this).toggleClass('toggle-button-on toggle-button-off');
            var handle = $(this).find('.toggle-handle');
            // handle.toggleClass('toggle-on toggle-off');
            if($(this).hasClass('toggle-button-on'))
            {
                $.post(
                    "{{ url('/setting/setting-customer') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate: "setting-customer-attribute",
                        operate_column: "is_staff_take",
                        set_value: 1
                    },
                    function(data){
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg('已开启员工接单');
                            $(this).find('.toggle-handle-text').html('开启');
                            handle.animate({'left': '25px'}, 'fast');

                        }
                    },
                    'json'
                );
            }
            else
            {
                $.post(
                    "{{ url('/setting/setting-customer') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate: "setting-customer-attribute",
                        operate_column: "is_staff_take",
                        set_value: 0
                    },
                    function(data){
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg('已关闭员工接单');
                            $(this).find('.toggle-handle-text').html('关闭');
                            handle.animate({'left': '0'}, 'fast');
                        }
                    },
                    'json'
                );
            }
        });


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