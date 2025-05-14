<script>
    $(function() {

        // 【综合概览】【查询】
        $(".main-content").on('click', "#filter-submit-for-call-1", function() {

            var that = $(this);
            var $id = that.attr("data-id");

            var $date = $('input[name="call-date"]').val();
            var $month = $('input[name="call-month"]').val();
            $('.statistic-title').html($month+'月');

            var $project_id = $('select[name="call-project"]').val();
            var $project_text = $('select[name="call-project"]').find("option:selected").text();


            $('.call-day-title').html($date+'日');
            $('.call-month-title').html($month+'月');
            if($project_id > 0)  $('.call-title').html('【'+$project_text+'】');

            var index1 = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">等待数据…</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/v1/operate/statistic/statistic-call-daily-overview') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    date: $date,
                    month: $month,
                    project: $project_id,
                    department_district: $('select[name="call-department-district[]"]').val(),
                    operate:"statistic-get"
                },
                success:function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        $data = data.data;
                    }
                }
            });

            $(".order_for_all").find('b').html($data.order_for_all);

            $(".order_for_inspected_all").find('b').html($data.order_for_inspected_all);
            $(".order_for_inspected_accepted").find('b').html($data.order_for_inspected_accepted);
            $(".order_for_inspected_accepted_inside").find('b').html($data.order_for_inspected_accepted_inside);
            $(".order_for_inspected_refused").find('b').html($data.order_for_inspected_refused);
            $(".order_for_inspected_repeated").find('b').html($data.order_for_inspected_repeated);


            $(".order_for_delivered_all").find('b').html($data.order_for_delivered_all);
            $(".order_for_delivered_effective").find('b').html($data.order_for_delivered_effective);
            $(".order_for_delivered_completed").find('b').html($data.order_for_delivered_completed);
            $(".order_for_delivered_inside").find('b').html($data.order_for_delivered_inside);
            $(".order_for_delivered_tomorrow").find('b').html($data.order_for_delivered_tomorrow);
            $(".order_for_delivered_repeated").find('b').html($data.order_for_delivered_repeated);
            $(".order_for_delivered_rejected").find('b').html($data.order_for_delivered_rejected);
            $(".order_rate_for_delivered_effective").find('b').html($data.order_rate_for_delivered_effective);




            // 全部
            $(".distributed_of_all_for_all").find('b').html($data.distributed_of_all_for_all);
            // 客服-报单
            $(".order_of_all_for_published").find('b').html($data.order_of_all_for_published);
            $(".order_of_all_for_inspected_all").find('b').html($data.order_of_all_for_inspected_all);
            $(".order_of_all_for_delivered_all").find('b').html($data.order_of_all_for_delivered_all);
            $(".order_of_all_for_delivered_completed").find('b').html($data.order_of_all_for_delivered_completed);
            $(".order_of_all_for_delivered_inside").find('b').html($data.order_of_all_for_delivered_inside);
            $(".order_of_all_for_delivered_tomorrow").find('b').html($data.order_of_all_for_delivered_tomorrow);
            $(".order_of_all_for_delivered_repeated").find('b').html($data.order_of_all_for_delivered_repeated);
            $(".order_of_all_for_delivered_rejected").find('b').html($data.order_of_all_for_delivered_rejected);
            $(".order_of_all_for_delivered_effective").find('b').html($data.order_of_all_for_delivered_effective);
            $(".order_of_all_for_delivered_effective_rate").find('b').html($data.order_of_all_for_delivered_effective_rate);

            $(".order_of_all_for_accepted").find('b').html($data.order_of_all_for_accepted);
            $(".order_of_all_for_accepted_inside").find('b').html($data.order_of_tall_for_accepted_inside);
            $(".order_of_all_for_refused").find('b').html($data.order_of_all_for_refused);
            $(".order_of_all_for_repeated").find('b').html($data.order_of_all_for_repeated);
            $(".order_of_all_for_rate").find('b').html($data.order_of_all_for_rate);


            // 运营-交付
            $(".deliverer_of_all_for_all").find('b').html($data.deliverer_of_all_for_all);
            $(".deliverer_of_all_for_all_by_same_day").find('b').html($data.deliverer_of_all_for_all_by_same_day);
            $(".deliverer_of_all_for_all_by_other_day").find('b').html($data.deliverer_of_all_for_all_by_other_day);

            $(".deliverer_of_all_for_completed").find('b').html($data.deliverer_of_all_for_completed);
            $(".deliverer_of_all_for_completed_by_same_day").find('b').html($data.deliverer_of_all_for_completed_by_same_day);
            $(".deliverer_of_all_for_completed_by_other_day").find('b').html($data.deliverer_of_all_for_completed_by_other_day);

            $(".deliverer_of_all_for_inside").find('b').html($data.deliverer_of_all_for_inside);
            $(".deliverer_of_all_for_inside_by_same_day").find('b').html($data.deliverer_of_all_for_inside_by_same_day);
            $(".deliverer_of_all_for_inside_by_other_day").find('b').html($data.deliverer_of_all_for_inside_by_other_day);

            $(".deliverer_of_all_for_tomorrow").find('b').html($data.deliverer_of_all_for_tomorrow);
            $(".deliverer_of_all_for_repeated").find('b').html($data.deliverer_of_all_for_repeated);
            $(".deliverer_of_tall_for_rejected").find('b').html($data.deliverer_of_all_for_rejected);
            // $(".deliverer_of_all_for_effective").find('b').html($data.deliverer_of_all_for_effective);
            // $(".deliverer_of_all_for_effective_rate").find('b').html($data.deliverer_of_all_for_effective_rate);

            // 质检-审核
            $(".order_of_all_for_inspected_all").find('b').html($data.order_of_all_for_inspected_all);
            $(".order_of_all_for_inspected_accepted").find('b').html($data.order_of_all_for_inspected_accepted);
            $(".order_of_all_for_inspected_accepted_inside").find('b').html($data.order_of_all_for_inspected_accepted_inside);
            $(".order_of_all_for_inspected_refused").find('b').html($data.order_of_all_for_inspected_refused);
            $(".order_of_all_for_inspected_repeated").find('b').html($data.order_of_all_for_inspected_repeated);



        });
        // 【综合概览】【重置】
        $("#statistic-for-call").on('click', ".filter-cancel", function() {
            $("#statistic-for-call").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-call").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-call").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="call-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

            var $date_dom = $('input[name="call-date"]');
            var $date_default = $date_dom.attr('data-default')
            $date_dom.val($date_default);

            $('.call-title').html('【综合概览】');

            $("#filter-submit-for-call").click();

        });


    });




    function statistic_get_data_for_order($month, $target, $target_quantity ,$target_income)
    {
        var $month_arr = $month.split('-');
        var $the_year = $month_arr[0];
        var $the_month = $month_arr[1];

        var $the_month_str = $the_year+'.'+$the_month+'月';


        var $pre_year = '';
        var $pre_month = '';

        if(parseInt($the_month) == 1)
        {
            $pre_year = parseInt($the_year) - 1;
            $pre_month = 12;
        }
        else
        {
            $pre_year = $the_year;
            $pre_month = parseInt($the_month) - 1;
            if($pre_month < 10) $pre_month = '0' + $pre_month;
        }
        var $pre_month_str = $pre_year+'.'+$pre_month+'月';


        var $data = new Object();
        $.ajax({
            type:"post",
            dataType:'json',
            async:false,
            url: "{{ url('/statistic/statistic-get-data-for-order') }}",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                month: $month,
                operate:"statistic-index"
            },
            success:function(data){
                if(!data.success)
                {
                    layer.msg(data.msg);
                }
                else
                {
                    $data = data.data;
                }
            }
        });


        // 每日订单量
        // 本月
        var $order_this_month_quantity_res = new Array();
        $.each($data.statistics_data_for_order_this_month,function(key,v){
            $order_this_month_quantity_res[(v.day - 1)] = { value:v.quantity, name:v.day };
        });
        // 上月
        var $order_last_month_quantity_res = new Array();
        $.each($data.statistics_data_for_order_last_month,function(key,v){
            $order_last_month_quantity_res[(v.day - 1)] = { value:v.quantity, name:v.day };
        });

        // echarts show
        var $statistics_option_for_order_quantity = {
            title: {
                text: '每日订单量【'+$the_month_str+' / '+$pre_month_str+'】',
                left: 'center'
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
                data: [$the_month_str, $pre_month_str],
                left: 20
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            grid: [
                {
                    left: '8%',
                    right: '4%',
                    bottom: '0%',
                    // containLabel: true
                }
            ],
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    axisLabel: { interval:0 },
                    data: [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                    ]
                }
            ],
            yAxis: [
                {
                    type: 'value'
                }
            ],
            series: [
                {
                    name: $the_month_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_this_month_quantity_res
                },
                {
                    name: $pre_month_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_last_month_quantity_res
                }
            ]
        };
        // var $myChart_for_order_quantity = echarts.init(document.getElementById($target_quantity));
        // $myChart_for_order_quantity.setOption($statistics_option_for_order_quantity);


        // 每日订单金额
        // 本月
        var $order_this_month_income_res = new Array();
        $.each($data.statistics_data_for_order_this_month,function(key,v){
            $order_this_month_income_res[(v.day - 1)] = { value: v.income_sum, name: v.day };
        });
        // 上月
        var $order_last_month_income_res = new Array();
        $.each($data.statistics_data_for_order_last_month,function(key,v){
            $order_last_month_income_res[(v.day - 1)] = { value: v.income_sum, name: v.day };
        });

        // echarts show
        var $statistics_option_for_order = {
            title: {
                text: '每日订单【'+$the_month_str+' / '+$pre_month_str+'】',
                left: 'center'
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
                data: [$the_month_str, $pre_month_str],
                left: 20
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            axisPointer: {
                link: [
                    {
                        xAxisIndex: 'all'
                    }
                ]
            },
            grid: [
                {
                    left: '8%',
                    right: '4%',
                    top: '12%',
                    height: '30%'
                    // containLabel: true
                },
                {
                    left: '8%',
                    right: '4%',
                    top: '50%',
                    height: '40%'
                    // containLabel: true
                }
            ],
            xAxis: [
                {
                    type: 'category',
                    boundaryGap: false,
                    axisLine: { onZero: true },
                    axisLabel: { interval:0 },
                    data: [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                    ]
                },
                {
                    gridIndex: 1,
                    type: 'category',
                    boundaryGap: false,
                    axisLine: { onZero: true },
                    axisLabel: { interval:0 },
                    data: [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                    ],
                    position: 'top'
                }
            ],
            yAxis: [
                {
                    name: '订单量',
                    type: 'value'
                },
                {
                    gridIndex: 1,
                    name: '订单收入',
                    type: 'value',
                    // inverse: true
                }
            ],
            series: [
                {
                    name: $the_month_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_this_month_quantity_res
                },
                {
                    name: $pre_month_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_last_month_quantity_res
                },
                {
                    name: $the_month_str,
                    type: 'line',
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                    symbolSize: 8,
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_this_month_income_res
                },
                {
                    name: $pre_month_str,
                    type: 'line',
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                    symbolSize: 8,
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_last_month_income_res
                }
            ]
        };
        var $myChart_for_order = echarts.init(document.getElementById($target));
        $myChart_for_order.setOption($statistics_option_for_order);
    }



    function statistic_get_data_for_call_daily_overview(target)
    {

        console.log(target);
        var $target = $(target);

        //
        var $index = layer.load(1, {
            shade: [0.3, '#fff'],
            content: '<span class="loadtip">正在提交</span>',
            success: function (layer) {
                layer.find('.layui-layer-content').css({
                    'padding-top': '40px',
                    'width': '100px',
                });
                layer.find('.loadtip').css({
                    'font-size':'20px',
                    'margin-left':'-18px'
                });
            }
        });

        var $time_type = $target.find('input[name="statistic-call-time-type"]').val();
        var $time_date = $target.find('input[name="statistic-call-date"]').val();
        var $time_month = $target.find('input[name="statistic-call-month"]').val();
        var $date_start = $target.find('input[name="statistic-call-start"]').val();
        var $date_ended = $target.find('input[name="statistic-call-ended"]').val();
        var $project_id = $target.find('select[name="statistic-call-project"]').val();
        var $department_district = $target.find('select[name="call-department-district[]"]').val();

        var $data = new Object();
        //
        $.post(
            "{{ url('/v1/operate/statistic/statistic-call-daily-overview') }}",
            {
                _token: $('meta[name="_token"]').attr('content'),
                time_type: $time_type,
                time_date: $time_date,
                time_month: $time_month,
                date_start: $date_start,
                date_ended: $date_ended,
                project: $project_id,
                department_district: $department_district,
                operate:"statistic-get"
            },
            'json'
        )
            .done(function($response, status, jqXHR) {
                console.log('done');
                $response = JSON.parse($response);
                if(!$response.success)
                {
                    if($response.msg) layer.msg($response.msg);
                }
                else
                {
                    $data = $response.data;
                    console.log($data);


                    // 坐席报单
                    $target.find(".dental_for_all").find('b').html($data.order_data.dental_for_all);
                    $target.find(".dental_for_inspected_all").find('b').html($data.order_data.dental_for_inspected_all);
                    $target.find(".dental_for_inspected_accepted").find('b').html($data.order_data.dental_for_inspected_accepted);


                    // 通话统计
                    $target.find(".call_for_all").find('b').html($data.call_data.call_for_all);
                    $target.find(".call_for_deal").find('b').html($data.call_data.call_for_deal);

                }
            })
            .fail(function(jqXHR, status, error) {
                console.log('fail');
                layer.msg('服务器错误！');

            })
            .always(function(jqXHR, status) {
                console.log('always');
                layer.closeAll('loading');
            });
    }



</script>