<script>

    function Datatable_for_Reconciliation_Project_Statistic_Daily($tableId, $eChartId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": ($(document).height() - 448)+"px",
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/reconciliation/v1/operate/project/statistic-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.project_id = $tableSearch.find('input[name="project-daily-project-id"]').val();
                    d.time_type = $tableSearch.find('input[name="project-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="project-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="project-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="project-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="project-daily-ended"]').val();

                },
            },
            "columnDefs": [
                {
                    // "targets": [10, 11, 15, 16],
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }
            ],
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 6 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
            },
            "columns": [
                {
                    "title": "日期",
                    "data": "date_day",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        // $(nTd).addClass('_bold');
                    },
                    render: function(data, type, row, meta) {
                        return row.date_day;
                    }
                },
                {
                    "title": "交付量",
                    "data": "delivery_quantity",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "合作单价",
                    "data": "cooperative_unit_price",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(data == '--') return '--';
                        return parseFloat(data);
                    }
                },
                {
                    "title": "营收",
                    "data": "revenue",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "渠道佣金",
                    "data": "channel_commission",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "渠道佣金",
                    "data": "channel_commission",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "成本",
                    "data": "daily_cost",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "利润",
                    "data": "profit",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "坏账",
                    "data": "funds_bad_debt_total",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "应收款",
                    "data": "funds_should_settled_total",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "已收款",
                    "data": "funds_already_settled_total",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.date_day == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('project-daily-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                // 每日交付量
                var $project_res = new Array();
                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $project_res[($rowData.day - 1)] = { value:$rowData.delivery_quantity, name:$rowData.day };
                });

                var $option_project_statistics = {
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
                        data:['订单量']
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
                            data: $project_res
                        }
                    ]
                };
                var $myChart_statistics = echarts.init(document.getElementById($eChartId));
                $myChart_statistics.setOption($option_project_statistics);

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>