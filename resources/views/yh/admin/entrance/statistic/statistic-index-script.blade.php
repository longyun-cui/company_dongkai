<script>
    $(function() {

        // 【综合概览】【查询】
        $(".main-content").on('click', "#filter-submit-for-comprehensive", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $date = $('input[name="comprehensive-date"]').val();
            var $month = $('input[name="comprehensive-month"]').val();
            $('.statistic-title').html($month+'月');

            var $project_id = $('select[name="comprehensive-project"]').val();
            var $project_text = $('select[name="comprehensive-project"]').find("option:selected").text();

            var $obj = new Object();
            if($('input[name="comprehensive-date"]').val())  $obj.date = $('input[name="comprehensive-date"]').val();
            if($('input[name="comprehensive-month"]').val())  $obj.month = $('input[name="comprehensive-month"]').val();
            if($('select[name="comprehensive-project"]').val() > 0)  $obj.project_id = $('select[name="comprehensive-project"]').val();

            if(JSON.stringify($obj) != "{}")
            {
                var $url = url_build('',$obj);
                history.replaceState({page: 1}, "", $url);
            }
            else
            {
                $url = "{{ url('/statistic/statistic-index') }}";
                if(window.location.search) history.replaceState({page: 1}, "", $url);
            }

            $('.comprehensive-day-title').html($date+'日');
            $('.comprehensive-month-title').html($month+'月');
            if($project_id > 0)  $('.comprehensive-title').html('【'+$project_text+'】');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/statistic/statistic-get-data-for-comprehensive') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    date: $date,
                    month: $month,
                    project: $project_id,
                    operate:"statistic-get"
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

            $(".order_count_for_all").find('b').html($data.order_count_for_all);
            $(".order_count_for_inspected").find('b').html($data.order_count_for_inspected);
            $(".order_count_for_accepted").find('b').html($data.order_count_for_accepted);
            $(".order_count_for_accepted_inside").find('b').html($data.order_count_for_accepted_inside);
            $(".order_count_for_refused").find('b').html($data.order_count_for_refused);
            $(".order_count_for_repeated").find('b').html($data.order_count_for_repeated);
            $(".order_count_for_rate").find('b').html($data.order_count_for_rate);

            $(".order_count_of_today_for_all").find('b').html($data.order_count_of_today_for_all);
            $(".order_count_of_today_for_inspected").find('b').html($data.order_count_of_today_for_inspected);
            $(".order_count_of_today_for_accepted").find('b').html($data.order_count_of_today_for_accepted);
            $(".order_count_of_today_for_accepted_inside").find('b').html($data.order_count_of_today_for_accepted_inside);
            $(".order_count_of_today_for_refused").find('b').html($data.order_count_of_today_for_refused);
            $(".order_count_of_today_for_repeated").find('b').html($data.order_count_of_today_for_repeated);
            $(".order_count_of_today_for_rate").find('b').html($data.order_count_of_today_for_rate);

            $(".order_count_of_month_for_all").find('b').html($data.order_count_of_month_for_all);
            $(".order_count_of_month_for_inspected").find('b').html($data.order_count_of_month_for_inspected);
            $(".order_count_of_month_for_accepted").find('b').html($data.order_count_of_month_for_accepted);
            $(".order_count_of_month_for_accepted_inside").find('b').html($data.order_count_of_month_for_accepted_inside);
            $(".order_count_of_month_for_refused").find('b').html($data.order_count_of_month_for_refused);
            $(".order_count_of_month_for_repeated").find('b').html($data.order_count_of_month_for_repeated);
            $(".order_count_of_month_for_rate").find('b').html($data.order_count_of_month_for_rate);

            // statistic_get_data_for_order($month, "myChart-for-comprehensive-order", "myChart-for-comprehensive-order-quantity", "myChart-for-comprehensive-order-income");
            // statistic_get_data_for_finance($month, "myChart-for-comprehensive-finance");

        });
        // 【综合概览】【重置】
        $("#statistic-for-comprehensive").on('click', ".filter-cancel", function() {
            $("#statistic-for-comprehensive").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-comprehensive").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-comprehensive").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="comprehensive-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

            var $date_dom = $('input[name="comprehensive-date"]');
            var $date_default = $date_dom.attr('data-default')
            $date_dom.val($date_default);

            $('.comprehensive-title').html('【综合概览】');

            $("#filter-submit-for-comprehensive").click();

        });

        // 【综合概览】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-comprehensive", function() {

            var $month_dom = $('input[name="comprehensive-month"]');
            var $the_month = $month_dom.val();
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
            $month_dom.val($pre_month_str);
            $("#filter-submit-for-comprehensive").click();

        });
        // 【综合概览】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-comprehensive", function() {

            var $month_dom = $('input[name="comprehensive-month"]');
            var $the_month_str = $month_dom.val();

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
            $month_dom.val($next_month_str);
            $("#filter-submit-for-comprehensive").click();

        });

        // 【综合概览】【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-comprehensive", function() {

            var $date_dom = $('input[name="comprehensive-date"]');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $yesterday_time = $time - (24*60*60*1000);

            var $yesterday = new Date($yesterday_time);
            var $yesterday_year = $yesterday.getFullYear();
            var $yesterday_month = ('00'+($yesterday.getMonth()+1)).slice(-2);
            var $yesterday_day = ('00'+($yesterday.getDate())).slice(-2);

            var $yesterday_date_str = $yesterday_year + '-' + $yesterday_month + '-' + $yesterday_day;
            $date_dom.val($yesterday_date_str);


            $("#filter-submit-for-comprehensive").click();

        });
        // 【综合概览】【后一天】
        $(".main-content").on('click', ".date-pick-next-for-comprehensive", function() {

            var $date_dom = $('input[name="comprehensive-date"]');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $tomorrow_time = $time + (24*60*60*1000);

            var $tomorrow = new Date($tomorrow_time);
            var $tomorrow_year = $tomorrow.getFullYear();
            var $tomorrow_month = ('00'+($tomorrow.getMonth()+1)).slice(-2);
            var $tomorrow_day = ('00'+($tomorrow.getDate())).slice(-2);

            var $tomorrow_date_str = $tomorrow_year + '-' + $tomorrow_month + '-' + $tomorrow_day;
            $date_dom.val($tomorrow_date_str);

            $("#filter-submit-for-comprehensive").click();

        });




        // 【订单统计】【查询】
        $(".main-content").on('click', "#filter-submit-for-order", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="comprehensive-month"]').val();
            $('.statistic-title').html($month+'月');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                {{--url: "{{ url('/statistic/comprehensive-analysis') }}",--}}
                url: "{{ url('/statistic/statistic-index') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    month: $month,
                    operate:"statistic-index"
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
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
            var $myChart_for_order = echarts.init(document.getElementById('myChart-for-comprehensive-order'));
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
            var $myChart_for_finance = echarts.init(document.getElementById('myChart-for-comprehensive-finance'));
            $myChart_for_finance.setOption($statistics_option_for_finance);


        });
        // 【订单统计】【重置】
        $("#statistic-for-order").on('click', ".filter-cancel", function() {
            $("#statistic-for-comprehensive").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-comprehensive").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-comprehensive").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="comprehensive-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
            $("#filter-submit-for-comprehensive").click();

        });

        // 【订单统计】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-order", function() {

            var $month_dom = $('input[name="order-month"]');
            var $the_month = $month_dom.val();
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
            $month_dom.val($pre_month_str);
            $("#filter-submit-for-order").click();

        });
        // 【订单统计】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-order", function() {

            var $month_dom = $('input[name="order-month"]');
            var $the_month_str = $month_dom.val();

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
            $month_dom.val($next_month_str);
            $("#filter-submit-for-order").click();

        });





        // 【分量分析】
        $(".main-content").on('click', "#filter-submit-for-component", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="component-month"]').val();
            $('.statistic-title').html($month+'月');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                {{--url: "{{ url('/statistic/comprehensive-analysis') }}",--}}
                url: "{{ url('/statistic/statistic-index?type=component') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    month: $month,
                    staff: $('select[name="component-staff"]').val(),
                    client: $('select[name="component-client"]').val(),
                    car: $('select[name="component-car"]').val(),
                    route: $('select[name="component-route"]').val(),
                    pricing: $('select[name="component-pricing"]').val(),
                    order_type: $('select[name="component-type"]').val(),
                    operate:"statistic-index"
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
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
            var $myChart_for_order = echarts.init(document.getElementById('myChart-for-car-order'));
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
            var $myChart_for_finance = echarts.init(document.getElementById('myChart-for-car-finance'));
            $myChart_for_finance.setOption($statistics_option_for_finance);


        });
        // 【重置】
        $("#statistic-for-component").on('click', ".filter-cancel", function() {
            $("#statistic-for-component").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-component").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-component").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="component-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

        });

        // 【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-component", function() {

            var $month_dom = $('input[name="component-month"]');
            var $the_month = $month_dom.val();
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
            $month_dom.val($pre_month_str);
            $("#filter-submit-for-component").click();

        });
        // 【后一月】
        $(".main-content").on('click', ".month-pick-next-for-component", function() {

            var $month_dom = $('input[name="component-month"]');
            var $the_month_str = $month_dom.val();

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
            $month_dom.val($next_month_str);
            $("#filter-submit-for-component").click();

        });




        // 【财务统计】【查询】
        $(".main-content").on('click', "#filter-submit-for-finance", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="finance-month"]').val();
            $('.statistic-title').html($month+'月');




        });
        // 【财务统计】【重置】
        $("#statistic-for-order").on('click', ".filter-cancel", function() {
            $("#statistic-for-comprehensive").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-comprehensive").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-comprehensive").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="comprehensive-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
            $("#filter-submit-for-comprehensive").click();

        });

        // 【财务统计】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-finance", function() {

            var $month_dom = $('input[name="finance-month"]');
            var $the_month = $month_dom.val();
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
            $month_dom.val($pre_month_str);
            $("#filter-submit-for-finance").click();

        });
        // 【财务统计】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-finance", function() {

            var $month_dom = $('input[name="finance-month"]');
            var $the_month_str = $month_dom.val();

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
            $month_dom.val($next_month_str);
            $("#filter-submit-for-finance").click();

        });







        //
        $('.select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        //
        $('.select2-trailer').select2({
            ajax: {
                url: "{{ url('/item/order_select2_trailer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        //
        $('.select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
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


    function statistic_get_data_for_finance($month, $target)
    {
        var $data = new Object();
        $.ajax({
            type:"post",
            dataType:'json',
            async:false,
            url: "{{ url('/statistic/statistic-get-data-for-finance') }}",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                month: $month,
                operate:"statistic-get-data-for-finance"
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
                text: '每日收支【收入总额/支出总额】',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'line',
                    label: {
                        backgroundColor: '#6a7985'
                    },
                    animation: false
                }
            },
            legend: {
                data: ['收入','支出'],
                left: 10
            },
            toolbox: {
                feature: {
                    dataZoom: {
                        yAxisIndex: 'none'
                    },
                    restore: {},
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
            // dataZoom: [
            //     {
            //         show: true,
            //         realtime: true,
            //         start: 30,
            //         end: 70,
            //         xAxisIndex: [0, 1]
            //     },
            //     {
            //         type: 'inside',
            //         realtime: true,
            //         start: 30,
            //         end: 70,
            //         xAxisIndex: [0, 1]
            //     }
            // ],
            grid: [
                {
                    left: '8%',
                    right: '2%',
                    top: '12%',
                    // bottom: '45%',
                    height: '35%',
                    // containLabel: true
                },
                {
                    left: '8%',
                    right: '2%',
                    top: '55%',
                    height: '35%',
                    // bottom: '8%',
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
                },
                {
                    gridIndex: 1,
                    type: 'category',
                    boundaryGap: false,
                    axisLabel: { interval:0 },
                    data: [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                    ],
                    position: 'top'
                }
            ],
            yAxis: [
                {
                    name: '收入',
                    type: 'value'
                },
                {
                    gridIndex: 1,
                    name: '支出',
                    type: 'value',
                    inverse: true
                }
            ],
            series: [
                {
                    name: '收入',
                    type: 'line',
                    symbolSize: 8,
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $income_res
                },
                {
                    name: '支出',
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
                    data: $payout_res
                }
            ]
        };
        var $myChart_for_finance = echarts.init(document.getElementById($target));
        $myChart_for_finance.setOption($statistics_option_for_finance);
    }




</script>