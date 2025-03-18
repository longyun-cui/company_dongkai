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
<div class="row">
    <div class="col-md-12">


        <div class="nav-tabs-custom" id="index-nav-box">

            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="nav-item active" id="home"><a href="#tab-home" data-toggle="tab" aria-expanded="true">首页</a></li>
            </ul>


            {{--content--}}
            <div class="tab-content">

                <div class="row tab-pane active" id="tab-home">

                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="box box-primary box-solid">
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
                                            <text class="text-blue font-20px">{{ $funds_recharge_total }}</text> 元
                                        </span>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="javascript:void(0);">
                                            累计消费
                                            <span class="pull-right">
                                                <text class="text-blue font-20px">{{ $funds_consumption_total }}</text> 元
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
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title comprehensive-month-title">工单统计</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <ul class="nav nav-stacked">
                                    <li class="">
                                        <a href="javascript:void(0);">
                                            总计
                                            <span class="pull-right">
                                                <text class="text-black font-20px">{{ $order_count_for_all or '' }}</text> 单
                                            </span>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="javascript:void(0);">
                                            本月
                                            <span class="pull-right">
                                               <text class="text-green font-20px">{{ $order_count_for_month or '' }}</text> 单
                                            </span>
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="javascript:void(0);">
                                            今日
                                            <span class="pull-right">
                                                <text class="text-blue font-20px">{{ $order_count_for_today or '' }}</text> 单
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
                                    <li class="" style="padding:10px 15px;height:40px;clear:both;">
                                        <span class="pull-left">
                                            <b class="toggle-handle-text">开始接单</b>
                                        </span>
                                        <button id="toggle-button-for-staff-take" class="toggle-button pull-right
                                            @if($me->is_staff_take == 1) toggle-button-on
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
</div>


@include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-follow-record')


<div class="component-container _none">

    @include(env('TEMPLATE_DK_CLIENT').'component.department.department-list')
    @include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-list')
    @include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-list')

    @if($me->client_er->user_category == 1)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list')
    @elseif($me->client_er->user_category == 31)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-luxury-list')
    @endif

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-daily')

    @include(env('TEMPLATE_DK_CLIENT').'component.finance.finance-daily')

</div>



@include(env('TEMPLATE_DK_CLIENT').'component.department.department-edit')
@include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-edit')
@include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-edit')



{{--订单统计--}}
<div class="row _none">
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
<div class="row _none">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">

            <h4>工单统计</h4>

{{--            <div class="callout-body">--}}
{{--                <span>总计 <text class="text-black font-24px">{{ $order_count_for_all or '' }}</text> 单</span>--}}
{{--                <span>本月 <text class="text-teal font-24px">{{ $order_count_for_month or '' }}</text> 单</span>--}}
{{--                <span>今日 <text class="text-red font-24px">{{ $order_count_for_today or '' }}</text> 单</span>--}}
{{--            </div>--}}

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

    @include(env('TEMPLATE_DK_CLIENT').'component.department.department-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-list-datatable')


    @if($me->client_er->user_category == 1)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list-datatable')
    @elseif($me->client_er->user_category == 31)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-luxury-list-datatable')
    @endif


    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-follow-record-datatable')

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-daily-datatable')

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list-script')

    @include(env('TEMPLATE_DK_CLIENT').'component.finance.finance-daily-datatable')


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
        $(".main-content").on('click', "#toggle-button-for-staff-take-", function() {
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
        // $myChart_order_statistics.setOption($option_order_statistics);

    });
</script>

@endsection