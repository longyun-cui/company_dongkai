<script>

    function Datatable_for_DeliveryProject($tableId)
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
                'url': "{{ url('/delivery/delivery-project') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="delivery-project-id"]').val();
                    d.name = $tableSearch.find('input[name="delivery-project-name"]').val();
                    d.title = $tableSearch.find('input[name="delivery-project-title"]').val();
                    d.keyword = $tableSearch.find('input[name="delivery-project-keyword"]').val();
                    d.status = $tableSearch.find('select[name="delivery-project-status"]').val();
                    d.time_type = $tableSearch.find('input[name="delivery-project-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="delivery-project-month"]').val();
                    d.time_date = $tableSearch.find('input[name="delivery-project-date"]').val();
                    d.date_start = $tableSearch.find('input[name="delivery-project-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="delivery-project-ended"]').val();
                    d.client = $tableSearch.find('select[name="delivery-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project-project"]').val();
                    d.delivered_result = $tableSearch.find('select[name="delivery-project-delivered-result[]"]').val();
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
                    "title": "序号",
                    "width": "40px",
                    "data": null,
                    "targets": 0,
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold').addClass('text-red');
                            $(nTd).html('统计');
                        }
                        else $(nTd).html(iRow + 1);
                    }
                },
                {
                    "title": "项目",
                    "data": "client_id",
                    "className": "text-center",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return data;
                        }
                        if(row.client_er) return row.client_er.username;
                        else return '--';
                    }
                },
                {
                    "title": "交付量",
                    "data": "delivery_count",
                    "className": "",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(row.delivery_count) return row.delivery_count;
                        else return '';
                    }
                }
            ],
            "drawCallback": function (settings) {

               // let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
               // this.api().column(0).nodes().each(function(cell, i) {
               //     console.log(cell);
               //     cell.innerHTML =  startIndex + i + 1;
               // });

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>