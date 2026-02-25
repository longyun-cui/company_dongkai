<script>
    function Datatable__for__Statistic_Marketing_Project_Team($tableId)
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
                'url': "{{ url('/o1/statistic/marketing/project-team') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-marketing-project-team-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-marketing-project-team-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-marketing-project-team-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-marketing-project-team-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-marketing-project-team-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-marketing-project-team-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-marketing-project-team-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-marketing-project-team-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-marketing-project-team-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-marketing-project-team-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-marketing-project-team-project"]').val();

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
                    "title": "项目",
                    "data": "delivered_project_id",
                    "className": "vertical-middle",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.delivered_project_er == null ? '未知' : '<a href="javascript:void(0);">' + row.delivered_project_er.name + '</a>';
                    }
                },
                {
                    "title": "团队",
                    "data": "creator_team_team_id",
                    "className": "vertical-middle",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator_team_er == null ? '未知' : '<a href="javascript:void(0);">' + row.creator_team_er.name + '</a>';
                    }
                },
                {
                    "title": "团队交付量",
                    "data": "count__for__order_today_all",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                },
                {
                    "title": "项目交付量",
                    "data": "sum__for__order_today_all",
                    "className": "vertical-middle",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                }
            ],
            "columnDefs": [
                {
                    targets: [0], //要合并的列数（第1，2，3列）
                    "data": "id",
                    createdCell: function (td, cellData, rowData, row, col) {
                        //重要的操作可以合并列的代码
                        var rowspan = rowData.project_merge;
                        if (rowspan >= 1) {
                            $(td).attr('rowspan', rowspan);
                            // 添加一个数据属性标记这是合并块的起始行
                            $(td).closest('tr').attr('data-merge-start', 'true')
                                .attr('data-merge-size', rowspan);
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                            // $(td).html('').css('visibility', 'hidden');
                        }
                    },
                    "render": function (data, type, full) {
                        return row.name;
                    }
                },
                {
                    targets: [3], //要合并的列数（第1，2，3列）
                    "data": "count__for__order_today_all",
                    createdCell: function (td, cellData, rowData, row, col) {
                        //重要的操作可以合并列的代码
                        var rowspan = rowData.project_merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
                    },
                    "render": function (data, type, full) {
                        return row.project_count__for__all;
                    }
                }
            ],
            "drawCallback": function (settings) {

                // 每次表格绘制后，重新计算条纹
                // reApplyStripes($tableId);
                // 每次表格绘制后，重新计算合并块的条纹
                reApplyMergeStripes($tableId);

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "initComplete": function(settings, json) {
                // 初始化完成后，重新计算条纹
                // reApplyStripes($tableId);
                reApplyMergeStripes($tableId);
            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>