<script>

    function Datatable_for_FinanceDaily($tableId, $eChartId)
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
                'url': "{{ url('/finance/finance-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="finance-daily-id"]').val();
                    d.order_id = $tableSearch.find('input[name="finance-daily-order-id"]').val();
                    d.name = $tableSearch.find('input[name="finance-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="finance-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="finance-daily-keyword"]').val();
                    d.remark = $tableSearch.find('input[name="finance-daily-remark"]').val();
                    d.description = $tableSearch.find('input[name="finance-daily-description"]').val();
                    d.assign = $tableSearch.find('input[name="finance-project-assign"]').val();
                    d.assign_start = $tableSearch.find('input[name="finance-daily-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="finance-daily-ended"]').val();
                    d.client = $tableSearch.find('select[name="finance-daily-client"]').val();
                    d.project = $tableSearch.find('select[name="finance-daily-project"]').val();
                    d.status = $tableSearch.find('select[name="finance-daily-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="finance-daily-delivery-type"]').val();
                    d.client_type = $tableSearch.find('select[name="finance-daily-client-type"]').val();
                    d.client_name = $tableSearch.find('input[name="finance-daily-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="finance-daily-client-phone"]').val();
                    d.delivered_status = $tableSearch.find('select[name="finance-daily-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="finance-daily-delivered-result[]"]').val();

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
                    "data": "assign_date",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        // $(nTd).addClass('_bold');
                    },
                    render: function(data, type, row, meta) {
                        return row.assign_date;
                    }
                },
                {
                    "title": "交付量",
                    "data": "delivery_quantity",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).addClass('_bold');
                    },
                    render: function(data, type, row, meta) {
                        if(row.delivery_quantity) return row.delivery_quantity;
                        else return '';
                    }
                },
                {
                    "title": "合作单价",
                    "data": "cooperative_unit_price",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!isNaN(parseFloat(data)) && isFinite(data)) return parseFloat(data);
                        return data;
                    }
                },
                {
                    "title": "每日花费",
                    "data": "total_daily_cost",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('finance-daily-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                // 每日交付量
                var $res = new Array();
                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $res[($rowData.day - 1)] = { value:$rowData.delivery_quantity, name:$rowData.assign_date };
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
                            data: $res
                        }
                    ]
                };
                var $myChart_statistics = echarts.init(document.getElementById($eChartId));
                $myChart_statistics.setOption($option_statistics);

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>