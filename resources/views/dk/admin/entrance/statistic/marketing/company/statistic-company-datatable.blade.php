<script>

    $(function () {
        // Table_Datatable_Ajax_Statistic_Company.init();
    });

    var Table_Datatable_Ajax_Statistic_Company = function ()
    {
        var datatable_Ajax_Statistic_Company = function ()
        {

            var dt_statistic_company = $('#datatable-for-statistic-company');

            var ajax_datatable_statistic_company = dt_statistic_company.DataTable({

                // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-company') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="customer-service-id"]').val();
                        d.name = $('input[name="customer-service-name"]').val();
                        d.title = $('input[name="customer-service-title"]').val();
                        d.keyword = $('input[name="customer-service-keyword"]').val();
                        d.status = $('select[name="customer-service-status"]').val();
                        d.time_type = $('input[name="customer-service-time-type"]').val();
                        d.time_month = $('input[name="customer-service-month"]').val();
                        d.time_date = $('input[name="customer-service-date"]').val();
                        d.project = $('select[name="customer-service-project"]').val();
                    },
                },
                // "fixedColumns": {
                {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
                {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
                // },
                "columns": [
//                    {
//                        "title": "选择",
//                        "data": "id",
//                        "width": "32px",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "title": "序号",
//                        "data": null,
//                        "width": "32px",
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "title": "公司",
                        "data": "id",
                        "className": "vertical-middle",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">' + row.name + '</a>';
                        }
                    },
                    {
                        "title": "渠道",
                        "data": "department_group_id",
                        "className": "vertical-middle",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">' + row.name + '</a>';
                        }
                    },
                    // {s
                    //     "title": "ID",
                    //     "data": "id",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": true,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "商务",
                        "data": "username",
                        "className": "text-center",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">' + row.name + '</a>';
                        }
                    },
                    {
                        "title": "商务交付量",
                        "data": "delivery_count_for_all",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },


                    {
                        "title": "渠道交付量",
                        "data": "channel_count_for_all",
                        "className": "text-center vertical-middle ",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },


                    {
                        "title": "公司交付量",
                        "data": "company_count_for_all",
                        "className": "text-center vertical-middle",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
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
                    {
                        targets: [0], //要合并的列数（第1，2，3列）
                        createdCell: function (td, cellData, rowData, row, col) {
                            //重要的操作可以合并列的代码
                            var rowspan = rowData.company_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        },
                        "data": "name",
                        "render": function (data, type, full) {
                            // return "<span title='" + data + "'>" + data + "</span>";
                            return  '<a href="javascript:void(0);">'+row.name+'</a>';
                        }
                    },
                    {
                        targets: [1], //要合并的列数（第1，2，3列）
                        createdCell: function (td, cellData, rowData, row, col) {
                            //重要的操作可以合并列的代码
                            var rowspan = rowData.channel_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        },
                        "data": "channel_id",
                        "render": function (data, type, full) {
                            return '<a href="javascript:void(0);">'+row.name+'</a>';
                        }
                    },
                    {
                        targets: [4],
                        createdCell: function (td, cellData, rowData, row, col) {
                            var rowspan = rowData.channel_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        }
                    },
                    {
                        targets: [5],
                        createdCell: function (td, cellData, rowData, row, col) {
                            var rowspan = rowData.company_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        }
                    }
                ],
                "language": { url: '/common/dataTableI18n' },
            });

        };
        return {
            init: datatable_Ajax_Statistic_Company
        }
    }();

</script>