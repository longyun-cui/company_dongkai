<script>
    function Table_Datatable_Statistic_Call_Task_Analysis($tableId)
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
            "scrollX": true,
            "scrollY": ($(document).height() - 400)+"px",
            "scrollCollapse": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic-call/statistic-task-analysis/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="rank-id"]').val();
                    d.name = $tableSearch.find('input[name="rank-name"]').val();
                    d.title = $tableSearch.find('input[name="rank-title"]').val();
                    d.keyword = $tableSearch.find('input[name="rank-keyword"]').val();
                    d.status = $tableSearch.find('select[name="rank-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-call-task-analysis-time-type"]').val();
                    d.assign_month = $tableSearch.find('input[name="statistic-call-task-analysis-month"]').val();
                    d.assign_date = $tableSearch.find('input[name="statistic-call-task-analysis-date"]').val();
                    d.assign_start = $tableSearch.find('input[name="statistic-call-task-analysis-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="statistic-call-task-analysis-ended"]').val();
                    d.assign_client = $tableSearch.find('select[name="statistic-call-task-analysis-client"]').val();
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
                    "title": "日期",
                    "data": "call_date",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "任务ID",
                    "data": "taskId",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": true,
                    "orderSequence": ["asc", "desc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "任务名称",
                    "data": "taskName",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": true,
                    "orderSequence": ["asc", "desc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "通话数",
                    "data": "call_count",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                },
                {
                    "title": "通话时长",
                    "data": "call_time_sum",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                },
                {
                    "title": "成单数",
                    "data": "order_count",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                },
                {
                    "title": "成单率",
                    "data": "order_count",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return parseFloat(data / row.call_count * 1000).toFixed(1) + " ‰";
                    }
                },
                // {
                //     "title": "备注",
                //     "data": "description",
                //     "className": "text-center",
                //     "width": "",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id != "统计" && row.is_confirmed != 1)
                //         {
                //             $(nTd).addClass('modal-show-for-item-field-set-of-statistic-client-daily');
                //
                //             $(nTd).attr('data-id',row.id);
                //             $(nTd).attr('data-name','备注');
                //             $(nTd).attr('data-key','description');
                //             $(nTd).attr('data-value',data);
                //
                //             $(nTd).attr('data-column-type','textarea');
                //             $(nTd).attr('data-column-name','备注');
                //
                //             if(data) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //         // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                //         // else return '';
                //     }
                // },
                {
                    "title": "操作",
                    "data": 'id',
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_record = '';
                        var $html_delete = '';
                        var $html_complete = '';

                        if(row.is_confirmed != 1)
                        {
                            $html_complete = '<a class="btn btn-xs item-confirm-submit-of-statistic-client-daily" data-id="'+data+'">确认</a>';
                        }
                        else
                        {
                            // $html_complete = '<a class="btn btn-xs disabled">确认</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs item-delete-submit-of-statistic-client-daily" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs item-restore-submit-of-statistic-client-daily" data-id="'+data+'">恢复</a>';
                        }

                        $html_record = '<a class="btn btn-xs modal-show-for-record-of-statistic-client-daily" data-id="'+data+'">记录</a>';

                        var html =
                            // '<a class="btn btn-xs modal-show-for-edit-of-statistic-client-daily" data-id="'+data+'">编辑</a>'+
                            $html_complete+
                            $html_delete+
                            $html_record+
                            '';
                        return html;

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