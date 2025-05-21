<script>

    function Datatable_Statistic_Call_Order_Daily_Month($tableId, $eChartId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic/call/statistic-order-daily-month') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-order-daily-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-order-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-order-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-order-daily-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-order-daily-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-order-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-order-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-order-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-order-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-order-daily-ended"]').val();
                    d.city = $tableSearch.find('select[name="statistic-order-city"]').val();
                },
            },
            // "fixedColumns": {
            {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
            {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
            // },
            "columns": [
                {
                    "title": "日期",
                    "data": "date_day",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.date_day;
                    }
                },
                {
                    "title": "成单量",
                    "data": "count",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "通话总数",
                    "data": "sum_all",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "8秒内",
                    "data": "sum_call_cnt_8",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "9-15秒",
                    "data": "sum_call_cnt_9_15",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "16-25秒",
                    "data": "sum_call_cnt_16_25",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "26-45秒",
                    "data": "sum_call_cnt_26_45",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "46-90秒",
                    "data": "sum_call_cnt_46_90",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "91秒以上",
                    "data": "sum_call_cnt_91",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "8秒内/单",
                    "data": "per_call_cnt_8",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "9-15秒/单",
                    "data": "per_call_cnt_9_15",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "16-25秒/单",
                    "data": "per_call_cnt_16_25",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "26-45秒/单",
                    "data": "per_call_cnt_26_45",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "46-90秒/单",
                    "data": "per_call_cnt_46_90",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "91秒上/单",
                    "data": "per_call_cnt_91",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                }
            ],
            "draworderback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });
                // 每日交付量
                var $res_cnt = new Array();
                var $res_minutes = new Array();

                // this.api().rows().every(function() {
                //     var $rowData = this.data();
                //     $res_cnt[($rowData.day - 1)] = { value:$rowData.cnt, name:$rowData.day };
                //     $res_minutes[($rowData.day - 1)] = { value:$rowData.minutes, name:$rowData.day };
                // });

                var $option_statistics = {
                    title: {
                        text: '每日通话'
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
                        data:['通话量','分钟数']
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
                            name:'通话量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_cnt
                        },
                        {
                            name:'分钟数',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_minutes
                        },
                    ]
                };
                console.log($('#tab-pane-width').width());
                // var $myChart_statistics = echarts.init(document.getElementById($eChartId));
                var $myChart_statistics = echarts.init(document.getElementById($eChartId), null, {
                    width: $('#tab-pane-width').width(),   // 最高优先级
                    height: 320
                });
                // $myChart_statistics.setOption($option_statistics);

            },
            "columnDefs": [
            ],
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>