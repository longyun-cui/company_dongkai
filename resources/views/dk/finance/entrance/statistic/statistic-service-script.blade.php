<script>
    $(function() {

        // 【搜索】
        $(".main-content").on('click', "#filter-submit-for-service", function() {

            $("#statistic-for-service").find('input[name=service]').val('all');
            var $staff_type_title = $('select[name=service]').find("option:selected").text();
            $(".statistic-title").html($staff_type_title);
            $(".statistic-time-type-title").html('总量');
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【搜索】【回车】
        $("#statistic-for-service").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#statistic-for-service").find(".filter-submit").click();
            }
        });
        // 【清空重选】
        $("#statistic-for-service").on('click', ".filter-empty", function() {
            $("#statistic-for-service").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $(".select2-box").val(-1).trigger("change");
            $(".select2-box").select2("val", "");

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-service").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-service").find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【刷新】
        $("#statistic-for-service").on('click', ".filter-refresh", function() {
            $('#datatable_ajax').DataTable().ajax.reload(null,false);
        });
        // 【重置】
        $("#statistic-for-service").on('click', ".filter-cancel", function() {
            $("#statistic-for-service").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-service").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-service").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $("#statistic-for-service").find('input[name=service-time-type]').val('');

            var $month_dom = $('input[name="service-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

            var $date_dom = $('input[name="service-date"]');
            var $date_default = $date_dom.attr('data-default')
            $date_dom.val($date_default);

            $(".statistic-title").html("全部");

            // $("#filter-submit-for-service").click();
            $('#datatable_ajax_daily').DataTable().ajax.reload();
            $('#datatable_ajax_project').DataTable().ajax.reload();

        });


        // 【业务报表】按【月】搜索
        $(".main-content").on('click', "#filter-submit-for-service-by-month", function() {

            $("#statistic-for-service").find('input[name=service-time-type]').val('month');

            var $statistic_title = '';

            var $company = $('select[name=service-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=service-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=service-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=service-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按月');
            var $month_dom = $('input[name="service-month"]');
            var $the_month_str = $month_dom.val();
            $(".statistic-time-title").html('（'+$the_month_str+'月）');

            $('#datatable_ajax_daily').DataTable().ajax.reload();
            $('#datatable_ajax_project').DataTable().ajax.reload();

            statistic_get_data_for_service_daily_chart("month",$the_month_str,
                "myChart-for-delivery-quantity",
                "myChart-for-cost-total",
                "myChart-for-cost-per-capita",
                "myChart-for-cost-unit-average"
            );
        });
        // 【业务报表】按【天】搜索
        $(".main-content").on('click', "#filter-submit-for-service-by-date", function() {

            $("#statistic-for-service").find('input[name=service-time-type]').val('date');

            var $statistic_title = '';

            var $company = $('select[name=service-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=service-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=service-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=service-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按天');
            var $date_dom = $('input[name="service-date"]');
            var $the_date_str = $date_dom.val();
            $(".statistic-time-title").html('（'+$the_date_str+'）');

            $('#datatable_ajax_daily').DataTable().ajax.reload();
            $('#datatable_ajax_project').DataTable().ajax.reload();

            statistic_get_data_for_service_daily_chart("date",$the_date_str,
                "myChart-for-delivery-quantity",
                "myChart-for-cost-total",
                "myChart-for-cost-per-capita",
                "myChart-for-cost-unit-average"
            );
        });
        // 【业务报表】按【时间段】搜索
        $(".main-content").on('click', "#filter-submit-for-service-by-period", function() {

            $("#statistic-for-service").find('input[name=service-time-type]').val('period');

            var $statistic_title = '';

            var $company = $('select[name=service-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=service-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=service-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=service-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按时间段查询');
            var $date_start = $('input[name="service-start"]');
            var $date_ended = $('input[name="service-ended"]');
            var $the_date_str = $date_start.val() + " - " + $date_ended.val();
            $(".statistic-time-title").html('（'+$the_date_str+'）');

            $('#datatable_ajax_daily').DataTable().ajax.reload();
            $('#datatable_ajax_project').DataTable().ajax.reload();

            statistic_get_data_for_service_daily_chart("date",$the_date_str,
                "myChart-for-delivery-quantity",
                "myChart-for-cost-total",
                "myChart-for-cost-per-capita",
                "myChart-for-cost-unit-average"
            );
        });




        // 【业务报表】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-service", function() {

            var $month_dom = $('input[name="service-month"]');
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

            $("#filter-submit-for-service-by-month").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });
        // 【业务报表】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-service", function() {

            var $month_dom = $('input[name="service-month"]');
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

            $("#filter-submit-for-service-by-month").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });

        // 【业务报表】【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-service", function() {

            var $date_dom = $('input[name="service-date"]');
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


            $("#filter-submit-for-service-by-date").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });
        // 【业务报表】【后一天】
        $(".main-content").on('click', ".date-pick-next-for-service", function() {

            var $date_dom = $('input[name="service-date"]');
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

            $("#filter-submit-for-service-by-date").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });




        $(".service-company").on("select2:select", function() {
            $(".service-channel").val(-1).trigger("change");
            $(".service-business").val(-1).trigger("change");
            $(".service-project").val(-1).trigger("change");
        });
        $(".service-channel").on("select2:select", function() {
            $(".service-company").val(-1).trigger("change");
            $(".service-business").val(-1).trigger("change");
            $(".service-project").val(-1).trigger("change");
        });
        $(".service-business").on("select2:select", function() {
            $(".service-company").val(-1).trigger("change");
            $(".service-channel").val(-1).trigger("change");
            $(".service-project").val(-1).trigger("change");
        });
        $(".service-project").on("select2:select", function() {
            $(".service-company").val(-1).trigger("change");
            $(".service-business").val(-1).trigger("change");
            $(".service-channel").val(-1).trigger("change");
        });


    });




    function statistic_get_data_for_service_daily_chart($time_type, $time, $target_delivery_quantity, $target_cost_total ,$target_per_capita ,$target_unit_average)
    {
        var $company = $('select[name="service-company"]').val();
        var $channel = $('select[name="service-channel"]').val();
        var $project = $('select[name="service-project"]').val();

        if($time_type == "month")
        {
            var $month_arr = $time.split('-');
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
            var $the_time_str = $pre_year+'.'+$pre_month+'月';
        }
        else if($time_type == "date")
        {
            var $the_time_str = $time;
        }


        var $data = new Object();
        $.ajax({
            type:"post",
            dataType:'json',
            async:false,
            url: "{{ url('/statistic/statistic-get-data-for-service-of-daily-chart') }}",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                company: $company,
                channel: $channel,
                project: $project,
                time_type: $time_type,
                time: $time,
                operate:"statistic-service"
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


        // 每日交付量
        var $delivery_quantity_res = new Array();
        $.each($data.statistics_data,function(key,v){
            $delivery_quantity_res[(v.day - 1)] = { value:(v.delivery_quantity_total), name:v.day };
        });
        // echarts show
        var $delivery_quantity_chart = {
            title: {
                text: '每日交付量',
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
                data: [$the_time_str],
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
                    name: $the_time_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $delivery_quantity_res
                }
            ]
        };
        var $myChart_for_delivery_quantity = echarts.init(document.getElementById($target_delivery_quantity));
        $myChart_for_delivery_quantity.setOption($delivery_quantity_chart);


        // 每日成本
        var $cost_total_res = new Array();
        $.each($data.statistics_data,function(key,v){
            $cost_total_res[(v.day - 1)] = { value:parseFloat(parseFloat(v.total_daily_cost_total).toFixed(2)), name:v.day };
        });
        // echarts show
        var $cost_total_chart = {
            title: {
                text: '每日成本',
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
                data: [$the_time_str],
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
                    name: $the_time_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $cost_total_res
                }
            ]
        };
        var $myChart_for_cost_total = echarts.init(document.getElementById($target_cost_total));
        $myChart_for_cost_total.setOption($cost_total_chart);


        // 人均成本
        var $per_capita_res = new Array();
        $.each($data.statistics_data,function(key,v){
            $per_capita_res[(v.day - 1)] = { value:parseFloat(parseFloat(v.per_capita).toFixed(2)), name:v.day };
        });
        // echarts show
        var $per_capita_chart = {
            title: {
                text: '人均成本',
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
                data: [$the_time_str],
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
                    name: $the_time_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $per_capita_res
                }
            ]
        };
        var $myChart_for_per_capita = echarts.init(document.getElementById($target_per_capita));
        $myChart_for_per_capita.setOption($per_capita_chart);


        // 单均成本
        var $unit_average_res = new Array();
        $.each($data.statistics_data,function(key,v){
            $unit_average_res[(v.day - 1)] = { value:v.unit_average, name:v.day };
            $unit_average_res[(v.day - 1)] = { value:parseFloat(parseFloat(v.unit_average).toFixed(2)), name:v.day };
        });
        // echarts show
        var $unit_average_chart = {
            title: {
                text: '单均成本',
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
                data: [$the_time_str],
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
                    name: $the_time_str,
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $unit_average_res
                }
            ]
        };
        var $myChart_for_unit_average = echarts.init(document.getElementById($target_unit_average));
        $myChart_for_unit_average.setOption($unit_average_chart);




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
    }




</script>