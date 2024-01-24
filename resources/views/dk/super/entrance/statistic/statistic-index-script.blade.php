<script>
    $(function() {

        // 【综合概览】【查询】
        $(".main-content").on('click', "#filter-submit-for-comprehensive", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="comprehensive-month"]').val();
            $('.statistic-title').html($month+'月');

            statistic_get_data_for_index($month, "myChart-for-comprehensive");

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


    });





    function statistic_get_data_for_index($month, $target)
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
            url: "{{ url('/statistic/statistic-index') }}",
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


        // 每日新增订单量
        // 本月
        var $order_res = new Array();
        $.each($data.statistics_data_for_order,function(key,v){
            $order_res[(v.day - 1)] = { value:v.sum, name:v.day };
        });


        // 每日修改量
        // 本月
        var $record_res = new Array();
        $.each($data.statistics_data_for_record,function(key,v){
            $record_res[(v.day - 1)] = { value: v.sum, name: v.day };
        });




        // echarts show
        var $statistics_option_for_comprehensive = {
            title: {
                text: '新增订单量 / 修改操作量',
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
                    name: '新增订单量',
                    type: 'value'
                },
                {
                    gridIndex: 1,
                    name: '修改操作量',
                    type: 'value',
                    // inverse: true
                }
            ],
            series: [
                {
                    name: '新增订单量',
                    type: 'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle: { normal: { label : { show: true } } },
                    data: $order_res
                },
                {
                    name: '修改操作量',
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
                    data: $record_res
                }
            ]
        };
        var $myChart_for_comprehensive = echarts.init(document.getElementById($target));
        $myChart_for_comprehensive.setOption($statistics_option_for_comprehensive);
    }




</script>