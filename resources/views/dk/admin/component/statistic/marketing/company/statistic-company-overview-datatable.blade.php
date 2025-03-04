<script>
    function Table_Datatable_Ajax_Statistic_Company_Overview($tableId)
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
                'url': "{{ url('/v1/operate/statistic/company-overview') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-company-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-company-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-company-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-company-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-company-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-company-overview-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-company-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-company-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-company-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-company-ended"]').val();
                    d.project = $tableSearch.find('select[name="statistic-company-project"]').val();
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
                        return '<a href="javascript:void(0);">' + row.company_name + '</a>';
                    }
                },
                {
                    "title": "渠道",
                    "data": "id",
                    "className": "vertical-middle",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);">' + row.channel_name + '</a>';
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
                    "data": "name",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);">' + row.business_name  + '</a>';
                    }
                },
                {
                    "title": "商务交付量",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count) return row.delivery_count;
                        else return '';
                    }
                },
                {
                    "title": "渠道交付量",
                    "data": "id",
                    "className": "text-center vertical-middle",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_channel) return row.delivery_count_for_channel;
                        else return '';
                    }
                },
                {
                    "title": "公司交付量",
                    "data": "id",
                    "className": "text-center vertical-middle",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.delivery_count_for_company) return row.delivery_count_for_company;
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
                    "data": "channel_name",
                    "render": function (data, type, full) {
                        return '<a href="javascript:void(0);">'+row.channel_name+'</a>';
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

    }
</script>