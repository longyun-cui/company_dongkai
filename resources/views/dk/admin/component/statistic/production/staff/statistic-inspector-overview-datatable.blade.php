<script>
    function Table_Datatable_Ajax_Statistic_Inspector_Overview($tableId)
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
                'url': "{{ url('/v1/operate/statistic/production/inspector-overview') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="inspector-id"]').val();
                    d.name = $tableSearch.find('input[name="inspector-name"]').val();
                    d.title = $tableSearch.find('input[name="inspector-title"]').val();
                    d.keyword = $tableSearch.find('input[name="inspector-keyword"]').val();
                    d.status = $tableSearch.find('select[name="inspector-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-inspector-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-inspector-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-inspector-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-inspector-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-inspector-ended"]').val();

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
//                     {
//                         "title": "经理",
//                         "data": "superior_id",
//                         "className": "vertical-middle",
//                         "width": "100px",
//                         "orderable": false,
//                         render: function(data, type, row, meta) {
//                             if(row.user_type == 71) return '<a href="javascript:void(0);">'+row.username+'</a>';
//                             return row.superior == null ? '未知' : '<a href="javascript:void(0);">'+row.superior.username+'</a>';
//                         }
//                     },
                {
                    "title": "团队",
                    "data": "department_district_id",
                    "className": "vertical-middle",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.department_district_er == null ? '<a href="javascript:void(0);">总部</a>' : '<a href="javascript:void(0);">'+row.department_district_er.name+'</a>';
                    }
                },
                // {
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
                    "title": "姓名",
                    "data": "username",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        // return data + ' (' + row.mobile + ')';
                        return '<a href="javascript:void(0);">' + data + ' (' + row.mobile + ')' + '</a>';
                    }
                },
                {
                    "title": "审核量",
                    "data": "order_count_for_inspected",
                    "className": "font-12px",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "总审核量",
                    "data": "order_sum_for_inspected",
                    "className": "text-center vertical-middle",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }
            ],
            "columnDefs": [
                {
                    targets: [0], //要合并的列数（第1，2，3列）
                    createdCell: function (td, cellData, rowData, row, col) {
                        //重要的操作可以合并列的代码
                        var rowspan = rowData.merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
                    }
                },
                {
                    targets: [3],
                    createdCell: function (td, cellData, rowData, row, col) {
                        var rowspan = rowData.merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
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