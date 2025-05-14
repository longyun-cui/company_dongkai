<script>

    function Datatable_Statistic_Call_List($tableId, $eChartId)
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
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic/statistic-call-list') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-call-list-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-call-list-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-call-list-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-call-list-keyword"]').val();
                    d.phone = $tableSearch.find('input[name="statistic-call-list-phone"]').val();
                    d.status = $tableSearch.find('select[name="statistic-call-list-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-call-list-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-call-list-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-call-list-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-call-list-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-call-list-ended"]').val();
                },
            },
            // "fixedColumns": {
            {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
            {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
            // },
            "columns": [
                {
                    "title": "日期",
                    "data": "call_date",
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
                    "title": "时间",
                    "data": "starttime",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "通话时长",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "城市代码",
                    "data": "region",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
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
            "columnDefs": [
            ],
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>