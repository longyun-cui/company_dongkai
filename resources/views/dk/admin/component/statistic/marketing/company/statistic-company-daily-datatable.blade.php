<script>

    function Table_Datatable_Ajax_Statistic_Company_Daily($tableId, $eChartId)
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
                'url': "{{ url('/v1/operate/statistic/marketing/company-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-company-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-company-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-company-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-company-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-company-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-company-daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-company-daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-company-daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-company-daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-company-daily-ended"]').val();
                    d.company = $tableSearch.find('select[name="statistic-company-daily-company"]').val();
                    d.channel = $tableSearch.find('select[name="statistic-company-daily-channel"]').val();
                    d.business = $tableSearch.find('select[name="statistic-company-daily-business"]').val();
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
                    "title": "交付量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count) return row.delivery_count;
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
                var $res = new Array();
                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $res[($rowData.day - 1)] = { value:$rowData.delivery_count, name:$rowData.day };
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
                            name:'本月',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            itemStyle : { normal: { label : { show: true } } },
                            data: $res
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