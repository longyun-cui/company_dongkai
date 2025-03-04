<script>

    function Datatable_for_DeliveryDaily($tableId)
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
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": ($(document).height() - 448)+"px",
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/delivery/delivery-daily1') }}",
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
                    d.assign = $tableSearch.find('input[name="delivery-daily-assign"]').val();
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

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>