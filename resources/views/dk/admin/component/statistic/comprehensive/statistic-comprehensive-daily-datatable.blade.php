<script>

    function Datatable_Statistic_Comprehensive_Daily($tableId, $eChartId)
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
                'url': "{{ url('/v1/operate/statistic/statistic-comprehensive-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-comprehensive-daily-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-comprehensive-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-comprehensive-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-comprehensive-daily-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-comprehensive-daily-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-comprehensive-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-comprehensive-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-comprehensive-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-comprehensive-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-comprehensive-daily-ended"]').val();
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
                    "title": "出席人力",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.attendance_manpower) return row.attendance_manpower;
                        else return '';
                    }
                },
                {
                    "title": "通话次数",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.cnt) return row.cnt;
                        else return '';
                    }
                },
                {
                    "title": "通话分钟",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.minutes) return row.minutes;
                        else return '';
                    }
                },
                {
                    "title": "提单量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_count_for_all) return row.order_count_for_all;
                        else return '';
                    }
                },
                {
                    "title": "人均提单",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_count_for_all_per) return row.order_count_for_all_per;
                        else return '';
                    }
                },
                {
                    "title": "通过量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_count_for_accepted) return row.order_count_for_accepted;
                        else return '';
                    }
                },
                {
                    "title": "人均通过",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_count_for_accepted_per) return row.order_count_for_accepted_per;
                        else return '';
                    }
                },
                {
                    "title": "通过率",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_rate_for_accepted) return row.order_rate_for_accepted + '%';
                        else return '';
                    }
                },
                {
                    "title": "有效量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_count_for_effective) return row.order_count_for_effective;
                        else return '';
                    }
                },
                {
                    "title": "有效率",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_rate_for_effective) return row.order_rate_for_effective + '%';
                        else return '';
                    }
                },
                {
                    "title": "交付量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_all) return row.delivery_count_for_all;
                        else return '';
                    }
                },
                {
                    "title": "分发量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_distributed) return row.delivery_count_for_distributed;
                        else return '';
                    }
                }
            ],
            "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });
                // 每日交付量
                var $res_total = new Array();
                var $res_accepted = new Array();
                var $res_effective = new Array();
                var $res_delivery_all = new Array();
                var $res_delivery_distributed = new Array();

                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $res_total[($rowData.day - 1)] = { value:$rowData.order_count_for_all, name:$rowData.day };
                    $res_accepted[($rowData.day - 1)] = { value:$rowData.order_count_for_accepted, name:$rowData.day };
                    $res_effective[($rowData.day - 1)] = { value:$rowData.order_count_for_effective, name:$rowData.day };
                    $res_delivery_all[($rowData.day - 1)] = { value:$rowData.delivery_count_for_all, name:$rowData.day };
                    $res_delivery_distributed[($rowData.day - 1)] = { value:$rowData.delivery_count_for_distributed, name:$rowData.day };
                });

                var $option_statistics = {
                    title: {
                        text: '每日交付量'
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
                        data:['提单量','通过量','有效量','交付量','分发量']
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
                            name:'提单量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_total
                        },
                        {
                            name:'通过量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_accepted
                        },
                        {
                            name:'有效量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_effective
                        },
                        {
                            name:'交付量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_delivery_all
                        },
                        {
                            name:'分发量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_delivery_distributed
                        }
                    ]
                };
                var $myChart_statistics = echarts.init(document.getElementById($eChartId));
                $myChart_statistics.setOption($option_statistics);

            },
            "columnDefs": [
            ],
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>