<script>

    function Datatable_Statistic_Call_Daily($tableId, $eChartId)
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
                'url': "{{ url('/v1/operate/statistic/statistic-call-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-call-daily-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-call-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-call-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-call-daily-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-call-daily-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-call-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-call-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-call-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-call-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-call-daily-ended"]').val();
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
                    "title": "8秒内",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.cnt_8) return row.cnt_8;
                        else return '';
                    }
                },
                {
                    "title": "有效通话",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.cnt) return row.cnt - row.cnt_8;
                        else return '';
                    }
                },
                {
                    "title": "次数 / 人",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.cnt_per_for_manpower) return row.cnt_per_for_manpower;
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
                    "title": "有效分钟",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.minutes) return row.minutes - row.cnt_8;
                        else return '';
                    }
                },
                {
                    "title": "分钟 / 人",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.minutes_per_for_manpower) return row.minutes_per_for_manpower;
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
                var $res_cnt = new Array();
                var $res_minutes = new Array();

                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $res_cnt[($rowData.day - 1)] = { value:$rowData.cnt, name:$rowData.day };
                    $res_minutes[($rowData.day - 1)] = { value:$rowData.minutes, name:$rowData.day };
                });

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
                var $myChart_statistics = echarts.init(document.getElementById($eChartId), null, {
                    width: $('tab-pane-width').width(),   // 最高优先级
                    height: 320
                });
                $myChart_statistics.setOption($option_statistics);

            },
            "columnDefs": [
            ],
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>