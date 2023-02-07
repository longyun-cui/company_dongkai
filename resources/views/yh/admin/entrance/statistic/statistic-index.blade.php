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

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">综合统计</h3>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-statistic-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="statistic-month" placeholder="选择月份" readonly="readonly" />

                        <select class="form-control form-filter" name="statistic-staff" style="width:96px;">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="statistic-client" style="width:96px;">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="statistic-route" style="width:96px;">
                            <option value="-1">选择线路</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        {{--<select class="form-control form-filter" name="order-car" style="width:96px;">--}}
                        {{--<option value ="-1">选择车辆</option>--}}
                        {{--@foreach($car_list as $v)--}}
                        {{--<option value ="{{ $v->id }}">{{ $v->name }}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        <select class="form-control form-filter order-list-select2-car" name="statistic-car" style="width:96px;">
                            <option value="-1">选择车辆</option>
                        </select>

                        <select class="form-control form-filter" name="statistic-pricing" style="width:96px;">
                            <option value="-1">选择定价</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="statistic-type" style="width:96px;">
                            <option value ="-1">订单类型</option>
                            <option value ="1">自有</option>
                            <option value ="11">空单</option>
                            <option value ="41">外配·配货</option>
                            <option value ="61">外请·调车</option>
                        </select>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-statistic">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-statistic">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>


            <div class="box-body">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div id="eChart-order-statistics" style="width:100%;height:280px;"></div>
                    </div>
                </div>
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div id="eChart-finance-statistics" style="width:100%;height:280px;"></div>
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


{{--网站总流量统计--}}
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">员工对比</h3>
            </div>

            {{--总电话量-对比--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="echart-all-comparison" style="width:100%;height:240px;"></div>
                    </div>
                </div>
            </div>
            {{--通话量-对比--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="echart-dialog-comparison" style="width:100%;height:240px;"></div>
                    </div>
                </div>
            </div>
            {{--加微信-对比--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="echart-wx-comparison" style="width:100%;height:240px;"></div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
            </div>

        </div>
        <!-- END PORTLET-->
    </div>
</div>


{{--转化率--}}
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">转化率</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="echart-all-rate" style="width:100%;height:320px;"></div>
                    </div>
                    <div class="col-md-6">
                        <div id="echart-today-rate" style="width:100%;height:320px;"></div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
            </div>

        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endsection




@section('custom-js')
    <script src="{{ asset('/lib/js/echarts-3.7.2.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
$(function() {

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

});
</script>

<script>
    $(function(){


        // 【数据分析】【显示】
        $(".main-content").on('click', ".filter-submit-for-analysis", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                {{--url: "{{ url('/statistic/comprehensive-analysis') }}",--}}
                url: "{{ url('/statistic/statistic-index') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    keyword: $('input[name="order-keyword"]').val(),
                    staff: $('select[name="order-staff"]').val(),
                    client: $('select[name="order-client"]').val(),
                    car: $('select[name="order-car"]').val(),
                    route: $('select[name="order-route"]').val(),
                    pricing: $('select[name="order-pricing"]').val(),
                    status: $('select[name="order-status"]').val(),
                    order_type: $('select[name="order-type"]').val(),
                    operate:"statistic-index",
                    item_id: $id
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });


            var $overview = $data.overview;
            var $option_overview = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['收入', '支出', '利润']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            show: false
                        },
                        data: $overview.title
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '支出',
                        type: 'bar',
                        stack: 'Total',
                        label: {
                            show: true,
                            position: 'inside'
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: $overview.expenses
                    },
                    {
                        name: '收入',
                        type: 'bar',
                        stack: 'Total',
                        label: {
                            show: true,
                            position: 'inside'
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: $overview.income
                    },
                    {
                        name: '利润',
                        type: 'bar',
                        label: {
                            show: true,
                            position: 'inside'
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: $overview.profit
                    }
                ]
            };
            var $myChart_overview = echarts.init(document.getElementById('echart-overview'));
            $myChart_overview.setOption($option_overview);


            // 支出占比
            var $expenditure_rate = $data.expenditure_rate;
            var $option_expenditure_rate = {
                title : {
                    text: '支出占比',
                    subtext: '支出占比',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient : 'vertical',
                    x : 'left',
                    data: $expenditure_rate
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {
                            show: true,
                            type: ['pie', 'funnel'],
                            option: {
                                funnel: {
                                    x: '25%',
                                    width: '50%',
                                    funnelAlign: 'left',
                                    max: 1548
                                }
                            }
                        },
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                calculable : true,
                series : [
                    {
                        name:'支出占比',
                        type:'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data: $expenditure_rate
                    }
                ]
            };
            var $myChart_expenditure_rate = echarts.init(document.getElementById('echart-expenditure-rate'));
            $myChart_expenditure_rate.setOption($option_expenditure_rate);




            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-analysis').modal('show');
        });



    });
</script>

@endsection


