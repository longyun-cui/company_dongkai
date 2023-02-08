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

                        <button type="button" class="form-control btn btn-flat btn-default month_picker_pre" style="width:30px;">
                            <i class="fa fa-chevron-left"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="statistic-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" style="text-align:center;width:90px;" />

                        <button type="button" class="form-control btn btn-flat btn-default month_picker_next" style="width:30px;">
                            <i class="fa fa-chevron-right"></i>
                        </button>


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

                <div class="box-header with-border" style="margin:16px 0;text-align:center;">
                    <h3 class="box-title statistic-title"></h3>
                </div>

                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div id="myChart-for-order" style="width:100%;height:240px;"></div>
                    </div>
                </div>
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-12">
                        <div id="myChart-for-finance" style="width:100%;height:240px;"></div>
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
                        <div id="myChart-all-rate" style="width:100%;height:320px;"></div>
                    </div>
                    <div class="col-md-6">
                        <div id="myChart-today-rate" style="width:100%;height:320px;"></div>
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
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function(){


        // 【数据分析】【显示】
        $(".main-content").on('click', "#filter-submit-for-statistic", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            $('.statistic-title').html($('input[name="statistic-month"]').val()+'月');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                {{--url: "{{ url('/statistic/comprehensive-analysis') }}",--}}
                url: "{{ url('/statistic/statistic-index') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    month: $('input[name="statistic-month"]').val(),
                    staff: $('select[name="statistic-staff"]').val(),
                    client: $('select[name="statistic-client"]').val(),
                    car: $('select[name="statistic-car"]').val(),
                    route: $('select[name="statistic-route"]').val(),
                    pricing: $('select[name="statistic-pricing"]').val(),
                    status: $('select[name="statistic-status"]').val(),
                    order_type: $('select[name="order-type"]').val(),
                    operate:"statistic-index",
                    item_id: $id
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                        console.log($data);
                    }
                }
            });


            // 每日订单量
            // 本月
            var $order_this_month_res = new Array();
            $.each($data.statistics_data_for_order_this_month,function(key,v){
                $order_this_month_res[(v.day - 1)] = { value:v.sum, name:v.day };
            });
            // 上月
            var $order_last_month_res = new Array();
            $.each($data.statistics_data_for_order_last_month,function(key,v){
                $order_last_month_res[(v.day - 1)] = { value:v.sum, name:v.day };
            });

            var $statistics_option_for_order = {
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
                    data:['本月','上月']
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
            var $myChart_for_order = echarts.init(document.getElementById('myChart-for-order'));
            $myChart_for_order.setOption($statistics_option_for_order);




            // 每日收入
            var $income_res = new Array();
            $.each($data.statistics_data_for_income,function(key,v){
                $income_res[(v.day - 1)] = { value:v.sum, name:v.day };
//            $income_res.push({ value:v.sum, name:v.date });
            });
            // 每日支出
            var $payout_res = new Array();
            $.each($data.statistics_data_for_payout,function(key,v){
                $payout_res[(v.day - 1)] = { value:v.sum, name:v.day };
            });

            var $statistics_option_for_finance = {
                title: {
                    text: '每日财务统计【收入总额/支出总额】'
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
            var $myChart_for_finance = echarts.init(document.getElementById('myChart-for-finance'));
            $myChart_for_finance.setOption($statistics_option_for_finance);


        });

        // 【前一月】
        $(".main-content").on('click', ".month_picker_pre", function() {

            var $the_month = $('input[name="statistic-month"]').val();
            var $date = new Date($the_month);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $pre_year = $year;
            var $pre_month = $month;

            if(parseInt($month) == 0)
            {
                $pre_year = $year - 1;
                $pre_month = 12;
            }

            if($pre_month < 10) $pre_month = '0'+$pre_month;

            var $pre_month_str = $pre_year+'-'+$pre_month;
            $('input[name="statistic-month"]').val($pre_month_str);
            $("#filter-submit-for-statistic").click();

        });
        // 【后一月】
        $(".main-content").on('click', ".month_picker_next", function() {

            var $the_month_str = $('input[name="statistic-month"]').val();
            var $date = new Date($the_month_str);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $next_year = $year;
            var $next_month = $month;

            if(parseInt($month) == 11)
            {
                $next_year = $year + 1;
                $next_month = 1;
            }
            else $next_month = $month + 2;

            if($next_month < 10) $next_month = '0'+$next_month;

            var $next_month_str = $next_year+'-'+$next_month;
            $('input[name="statistic-month"]').val($next_month_str);
            $("#filter-submit-for-statistic").click();

        });


        $("#filter-submit-for-statistic").click();


    });
</script>

@endsection


