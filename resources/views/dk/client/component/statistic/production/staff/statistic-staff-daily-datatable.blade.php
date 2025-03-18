<script>

    function Datatable_for_Statistic_Staff_Daily($tableId, $eChartId)
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
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic/production/staff-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.staff_id = $tableSearch.find('input[name="statistic-staff-daily-staff-id"]').val();
                    d.id = $tableSearch.find('input[name="statistic-staff-daily-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-staff-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-staff-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-staff-daily-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-staff-daily-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-staff-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-staff-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-staff-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-staff-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-staff-daily-ended"]').val();
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
                    "title": "工单量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_all) return row.delivery_count_for_all;
                        else return '0';
                    }
                },
                {
                    "title": "+V量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_wx) return row.delivery_count_for_wx;
                        else return '0';
                    }
                },
                {
                    "title": "预约量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_come_9) return row.delivery_count_for_come_9;
                        else return '0';
                    }
                },
                {
                    "title": "上门量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_come_11) return row.delivery_count_for_come_11;
                        else return '0';
                    }
                },
                {
                    "title": "交易次数",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_transaction_num) return row.delivery_count_for_transaction_num;
                        else return '0';
                    }
                },
                {
                    "title": "交易数量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_transaction_count) return row.delivery_count_for_transaction_count;
                        else return '0';
                    }
                },
                {
                    "title": "交易金额",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_transaction_amount) return row.delivery_count_for_transaction_amount;
                        else return '0';
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
                var $res_wx = new Array();
                var $res_come = new Array();
                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $res_total[($rowData.day - 1)] = { value:$rowData.delivery_count_for_all, name:$rowData.day };
                    $res_wx[($rowData.day - 1)] = { value:$rowData.delivery_count_for_wx, name:$rowData.day };
                    $res_come[($rowData.day - 1)] = { value:$rowData.delivery_count_for_come_11, name:$rowData.day };
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
                        data:['交付量']
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
                            name:'+V量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_wx
                        },
                        {
                            name:'上门量',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res_come
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