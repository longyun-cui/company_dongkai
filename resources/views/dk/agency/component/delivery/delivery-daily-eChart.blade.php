<script>

    function Datatable_for_DeliveryDaily($tableId,$eChartId)
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
                'url': "{{ url('/delivery/delivery-daily') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="delivery-daily-id"]').val();
                    d.order_id = $tableSearch.find('input[name="delivery-daily-order-id"]').val();
                    d.name = $tableSearch.find('input[name="delivery-daily-name"]').val();
                    d.title = $tableSearch.find('input[name="delivery-daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="delivery-daily-keyword"]').val();
                    d.remark = $tableSearch.find('input[name="delivery-daily-remark"]').val();
                    d.description = $tableSearch.find('input[name="delivery-daily-description"]').val();
                    d.time_type = $tableSearch.find('input[name="delivery-daily-time-type"]').val();
                    d.assign = $tableSearch.find('input[name="delivery-daily-assign"]').val();
                    d.time_month = $tableSearch.find('input[name="delivery-daily-month"]').val();
                    d.assign_start = $tableSearch.find('input[name="delivery-daily-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="delivery-daily-ended"]').val();
                    d.client = $tableSearch.find('select[name="delivery-daily-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-daily-project"]').val();
                    d.status = $tableSearch.find('select[name="delivery-daily-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="delivery-daily-delivery-type"]').val();
                    d.client_type = $tableSearch.find('select[name="delivery-daily-client-type"]').val();
                    d.client_name = $tableSearch.find('input[name="delivery-daily-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="delivery-daily-client-phone"]').val();
                    d.delivered_status = $tableSearch.find('select[name="delivery-daily-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="delivery-daily-delivered-result[]"]').val();

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
                    "data": "delivery_count",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).addClass('_bold');
                    },
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
                var $delivery_res = new Array();
                this.api().rows().every(function() {
                    var $rowData = this.data();
                    $delivery_res[($rowData.day - 1)] = { value:$rowData.delivery_count, name:$rowData.day };
                });

                var $option_delivery_statistics = {
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
                            data: $delivery_res
                        }
                    ]
                };
                var $myChart_delivery_statistics = echarts.init(document.getElementById($eChartId));
                $myChart_delivery_statistics.setOption($option_delivery_statistics);

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>