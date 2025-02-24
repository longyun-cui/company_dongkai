<script>

    $(function () {
        Table_Datatable_Ajax_Statistic_Company_Daily.init();
    });

    var Table_Datatable_Ajax_Statistic_Company_Daily = function ()
    {
        var datatable_Ajax_Statistic_Company_Daily = function ()
        {

            var dt_statistic_company_daily = $('#datatable-for-statistic-company-daily');

            var ajax_datatable_statistic_company_daily = dt_statistic_company_daily.DataTable({

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
                    'url': "{{ url('/statistic/statistic-company-daily') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="statistic-company-id"]').val();
                        d.name = $('input[name="statistic-company-name"]').val();
                        d.title = $('input[name="statistic-company-title"]').val();
                        d.keyword = $('input[name="statistic-company-keyword"]').val();
                        d.status = $('select[name="statistic-company-status"]').val();
                        d.time_type = $('input[name="statistic-company-daily-time-type"]').val();
                        d.time_month = $('input[name="statistic-company-daily-month"]').val();
                        d.time_date = $('input[name="statistic-company-daily-date"]').val();
                        d.date_start = $('input[name="statistic-company-daily-start"]').val();
                        d.date_ended = $('input[name="statistic-company-daily-ended"]').val();
                        d.company = $('select[name="statistic-company-daily-company"]').val();
                        d.channel = $('select[name="statistic-company-daily-channel"]').val();
                        d.business = $('select[name="statistic-company-daily-business"]').val();
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

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                },
                "columnDefs": [
                ],
                "language": { url: '/common/dataTableI18n' },
            });

        };
        return {
            init: datatable_Ajax_Statistic_Company_Daily
        }
    }();

</script>