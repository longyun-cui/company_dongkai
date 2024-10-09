@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')财务系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--订单统计--}}
<div class="row">


    @if(in_array($me->user_type,[0,1,11,19,31]))
    <div class="col-md-12">
        <!-- Application buttons -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">公司</h3>
            </div>
            <div class="box-body">

                @foreach($company_list as $v)
                <div class="col-md-3">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-primary">
                            <div class="widget-user-image">
                                <img class="img-circle" src="/AdminLTE/dist/img/user{{ $v->id + 2 }}-128x128.jpg" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">{{ $v->name or '' }}</h3>
                            <h5 class="widget-user-desc">Lead Developer</h5>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $v->funds_recharge_total or '' }}</h5>
                                        <span class="description-text">充值</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $v->funds_recharge_total or '' }}</h5>
                                        <span class="description-text">结算</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $v->funds_recharge_total or '' }}</h5>
                                        <span class="description-text">余额</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->

                        </div>

                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li>
                                    <a target="_blank" href="{{ url('/statistic/statistic-company-overview?company_id='.$v->id) }}">
                                        财务总览 <span class="pull-right badge bg-blue _none">31</span>
                                    </a>
                                </li>
{{--                                <li><a href="#">Tasks <span class="pull-right badge bg-aqua">5</span></a></li>--}}
{{--                                <li><a href="#">Completed Projects <span class="pull-right badge bg-green">12</span></a></li>--}}
{{--                                <li><a href="#">Followers <span class="pull-right badge bg-red">842</span></a></li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    @endif


    @if(in_array($me->user_type,[0,1,11,19,31]))
    <div class="col-md-12">
        <!-- Application buttons -->

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">代理概览</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>代理</th>
                            <th>充值</th>
                            <th>余额</th>
                            <th>应结算</th>
                            <th>已结算</th>
                            <th>待收款</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($channel_list as $v)
                        <tr>
                            <td>
                                <a target="_blank" href="{{ url('/item/settled-list?channel_id='.$v->id) }}">
                                    {{ $v->name or '' }}
                                </a>
                            </td>
                            <td>{{ (float)($v->funds_recharge_total) }}</td>
                            <td>{{ (float)($v->funds_recharge_total - $v->settled_amount) }}</td>
{{--                            <td>{{ (float)($v->funds_recharge_total - $v->should_settled) }}</td>--}}
                            <td>{{ (float)($v->should_settled) }}</td>
                            <td>{{ (float)($v->settled_amount) }}</td>
                            <td>
                                @if($v->should_settled > $v->settled_amount)
                                    <span class="label label-danger">{{ (float)($v->should_settled - $v->settled_amount) }}</span>
                                @else
                                    <span>{{ (float)($v->should_settled - $v->settled_amount) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix _none">
                <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div>
            <!-- /.box-footer -->
        </div>

    </div>
    @endif


    @if(in_array($me->user_type,[41]))
    <div class="col-md-12">
        <!-- Application buttons -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ $me->channel_er->name or '' }}</h3>
            </div>
            <div class="box-body">

                <div class="col-md-3">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-primary">
                            <div class="widget-user-image">
                                @if(!empty($me->portrait_img))
                                    <img class="user-image" src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" alt="User">
                                @else
                                    <img class="user-image" src="/AdminLTE/dist/img/user2-160x160.jpg" alt="User Image">
                                @endif
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">{{ $me->username or '' }}</h3>
                            <h5 class="widget-user-desc">{{ $me->channel_er->name or '' }}</h5>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $me->channel_er->funds_recharge_total or '' }}</h5>
                                        <span class="description-text">充值</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $me->channel_er->settled_amount or '' }}</h5>
                                        <span class="description-text">结算</span>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-4 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header">{{ $me->channel_er->funds_recharge_total - $me->channel_er->settled_amount }}</h5>
                                        <span class="description-text">余额</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->

                        </div>

                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li>
                                    {{--                                        <a target="_blank" href="{{ url('/statistic/statistic-company-overview?company_id='.$v->id) }}">--}}
                                    {{--                                            财务总览 <span class="pull-right badge bg-blue _none">31</span>--}}
                                    {{--                                        </a>--}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif


    <div class="col-md-12 _none">
        <!-- Application buttons -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">公司财务</h3>
            </div>
            <div class="box-body">

                <a class="btn btn-app">
                    <span class="badge bg-yellow">3</span>
                    <i class="fa fa-bullhorn"></i> Notifications
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-green">300</span>
                    <i class="fa fa-barcode"></i> Products
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-purple">891</span>
                    <i class="fa fa-users"></i> Users
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-teal">67</span>
                    <i class="fa fa-inbox"></i> Orders
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
    </div>


    <div class="col-md-12 _none">
        <div class="callout callout-success- bg-white">

            <h4>工单统计</h4>

            <div class="callout-body">
                <span>【总计】</span>
                <span>总计 <text class="text-black font-24px">{{ $order_count->order_count_for_all or '0' }}</text> 单</span>
                <span>导入 <text class="text-black font-24px">{{ $order_count->order_count_for_export or '0' }}</text> 单</span>
                <span>已发布 <text class="text-black font-24px">{{ $order_count->order_count_for_published or '0' }}</text> 单</span>
                <span>待发布 <text class="text-black font-24px">{{ $order_count->order_count_for_unpublished or '0' }}</text> 单</span>
            </div>
            <div class="callout-body">
                <span>【审核】</span>
                <span>已审 <text class="text-blue font-24px">{{ $order_count->order_count_for_inspected or '0' }}</text> 单</span>
                <span>待审 <text class="text-blue font-24px">{{ $order_count->order_count_for_waiting_for_inspect or '0' }}</text> 单</span>
                <span>审核通过 <text class="text-blue font-24px">{{ $order_count->order_count_for_accepted or '0' }}</text> 单</span>
                <span>内部通过 <text class="text-blue font-24px">{{ $order_count->order_count_for_accepted_inside or '0' }}</text> 单</span>
                <span>重复 <text class="text-orange font-24px">{{ $order_count->order_count_for_repeat or '0' }}</text> 单 </span>
                <span>拒绝 <text class="text-red font-24px">{{ $order_count->order_count_for_refused or '0' }}</text> 单</span>
            </div>
            <div class="callout-body">
                <span>【交付】</span>
                <span>交付 <text class="text-green font-24px">{{ $order_count->order_count_for_delivered or '0' }}</text> 单</span>
                <span>已交付 <text class="text-green font-24px">{{ $order_count->order_count_for_delivered_completed or '0' }}</text> 单</span>
                <span>待交付 <text class="text-green font-24px">{{ $order_count->order_count_for_delivered_uncompleted or '0' }}</text> 单</span>
                <span>隔日交付 <text class="text-green font-24px">{{ $order_count->order_count_for_delivered_tomorrow or '0' }}</text> 单</span>
                <span>内部交付 <text class="text-green font-24px">{{ $order_count->order_count_for_delivered_inside or '0' }}</text> 单</span>
                <span>重复 <text class="text-orange font-24px">{{ $order_count->order_count_for_delivered_repeated or '0' }}</text> 单 </span>
                <span>拒绝 <text class="text-red font-24px">{{ $order_count->order_count_for_delivered_rejected or '0' }}</text> 单</span>
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


        {{--// 每日订单量--}}
        {{--var $order_this_month_res = new Array();--}}
        {{--$.each({!! $statistics_order_this_month_data !!},function(key,v){--}}
        {{--    $order_this_month_res[(v.day - 1)] = { value:v.sum, name:v.day };--}}
        {{--});--}}
        {{--var $order_last_month_res = new Array();--}}
        {{--$.each({!! $statistics_order_last_month_data !!},function(key,v){--}}
        {{--    $order_last_month_res[(v.day - 1)] = { value:v.sum, name:v.day };--}}
        {{--});--}}

        {{--var $option_order_statistics = {--}}
        {{--    title: {--}}
        {{--        text: '每日订单量统计【本月/上月】'--}}
        {{--    },--}}
        {{--    tooltip : {--}}
        {{--        trigger: 'axis',--}}
        {{--        axisPointer: {--}}
        {{--            type: 'line',--}}
        {{--            label: {--}}
        {{--                backgroundColor: '#6a7985'--}}
        {{--            }--}}
        {{--        }--}}
        {{--    },--}}
        {{--    legend: {--}}
        {{--        data:['订单量']--}}
        {{--    },--}}
        {{--    toolbox: {--}}
        {{--        feature: {--}}
        {{--            saveAsImage: {}--}}
        {{--        }--}}
        {{--    },--}}
        {{--    grid: {--}}
        {{--        left: '3%',--}}
        {{--        right: '4%',--}}
        {{--        bottom: '3%',--}}
        {{--        containLabel: true--}}
        {{--    },--}}
        {{--    xAxis : [--}}
        {{--        {--}}
        {{--            type : 'category',--}}
        {{--            boundaryGap : false,--}}
        {{--            axisLabel : { interval:0 },--}}
        {{--            data : [--}}
        {{--                1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31--}}
        {{--            ]--}}
        {{--        }--}}
        {{--    ],--}}
        {{--    yAxis : [--}}
        {{--        {--}}
        {{--            type : 'value'--}}
        {{--        }--}}
        {{--    ],--}}
        {{--    series : [--}}
        {{--        {--}}
        {{--            name:'本月',--}}
        {{--            type:'line',--}}
        {{--            label: {--}}
        {{--                normal: {--}}
        {{--                    show: true,--}}
        {{--                    position: 'top'--}}
        {{--                }--}}
        {{--            },--}}
        {{--            itemStyle : { normal: { label : { show: true } } },--}}
        {{--            data: $order_this_month_res--}}
        {{--        },--}}
        {{--        {--}}
        {{--            name:'上月',--}}
        {{--            type:'line',--}}
        {{--            label: {--}}
        {{--                normal: {--}}
        {{--                    show: true,--}}
        {{--                    position: 'top'--}}
        {{--                }--}}
        {{--            },--}}
        {{--            itemStyle : { normal: { label : { show: true } } },--}}
        {{--            data: $order_last_month_res--}}
        {{--        }--}}
        {{--    ]--}}
        {{--};--}}
        {{--var $myChart_order_statistics = echarts.init(document.getElementById('eChart-order-statistics'));--}}
        {{--$myChart_order_statistics.setOption($option_order_statistics);--}}

    });
</script>
@endsection