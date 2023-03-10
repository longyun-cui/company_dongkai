<script>
    $(function() {

        // 【综合概览】【查询】
        $(".main-content").on('click', "#filter-submit-for-comprehensive", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="comprehensive-month"]').val();
            $('.statistic-title').html($month+'月');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/statistic/statistic-get-data-for-comprehensive') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    month: $month,
                    operate:"statistic-get"
                },
                success:function(data){
                    console.log(data);
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });



            $(".order_count_for_all").find('span').html($data.order_count_for_all);
            $(".order_count_for_unpublished").find('span').html($data.order_count_for_unpublished);
            $(".order_count_for_published").find('span').html($data.order_count_for_published);

            $(".amount_sum").find('span').html($data.amount_sum);
            $(".income_receivable_sum").find('span').html($data.income_receivable_sum);
            $(".income_receipts_sum").find('span').html($data.income_receipts_sum);
            $(".income_waiting_sum").find('span').html($data.income_waiting_sum);
            $(".expanse_sum").find('span').html($data.expanse_sum); // 总支出

            $(".finance_income_sum").find('span').html($data.finance_income_sum); // 总支出
            $(".finance_expense_sum").find('span').html($data.finance_expense_sum); // 总支出
            $(".finance_profile_sum").find('span').html($data.finance_profile_sum); // 总支出



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
        // 【订单统计】【后一月】
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







        //
        $('.select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_list_select2_car') }}",
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
</script>