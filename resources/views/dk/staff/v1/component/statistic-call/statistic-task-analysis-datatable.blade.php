<script>
    function Datatable__for__Statistic_Call_Task_Analysis($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true, // 开启处理提示
            "language": {
                url: '/common/dataTableI18n',
                // processing: '<div class="spinner"></div> 加载中...' // 自定义处理提示，包含动画
                processing: '<div class="spinner"></div> 正在加载数据，请稍候...',
            },
            "serverSide": false,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_info_box"i>  <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": true,
            // "scrollY": ($(document).height() - 400)+"px",
            "scrollCollapse": true,
            "ajax": {
                'url': "{{ url('/o1/statistic-call/statistic-task-analysis') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-call-task-analysis-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-call-task-analysis-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-call-task-analysis-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-call-task-analysis-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-call-task-analysis-status"]').val();
                    d.result = $tableSearch.find('select[name="statistic-call-task-analysis-result"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-call-task-analysis-time-type"]').val();
                    d.assign_month = $tableSearch.find('input[name="statistic-call-task-analysis-month"]').val();
                    d.assign_date = $tableSearch.find('input[name="statistic-call-task-analysis-date"]').val();
                    d.assign_start = $tableSearch.find('input[name="statistic-call-task-analysis-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="statistic-call-task-analysis-ended"]').val();
                    d.assign_client = $tableSearch.find('select[name="statistic-call-task-analysis-client"]').val();
                },
                "beforeSend": function() {
                    // 显示加载提示，例如使用一个div，或者调用layer.load等
                    // $('#loading').show();
                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在加载</span>',
                        success: function (layer) {
                            layer.find('.layui-layer-content').css({
                                'padding-top': '40px',
                                'width': '100px',
                            });
                            layer.find('.loadtip').css({
                                'font-size':'20px',
                                'margin-left':'-18px'
                            });
                        }
                    });
                },
                "error": function() {
                    layer.msg("请求失败！");
                },
                "complete": function() {
                    // 隐藏加载提示
                    // $('#loading').hide();
                    layer.closeAll('loading');
                }
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
                            $(nTd).addClass('text-red').addClass('_bold');
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
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "通话数",
                    "data": "call_count",
                    "type": "num",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '--';
                            return data;
                        }
                        else if (type === 'sort')
                        {
                            // 排序时返回数值
                            return data;
                        }
                        else
                        {
                            // 过滤等其他操作使用原始值
                            return data;
                        }
                    }
                },
                {
                    "title": "通话时长",
                    "data": "call_time_sum",
                    "type": "num",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '--';
                            return data;
                        }
                        else if (type === 'sort')
                        {
                            // 排序时返回数值
                            return data;
                        }
                        else
                        {
                            // 过滤等其他操作使用原始值
                            return data;
                        }
                    }
                },
                {
                    "title": "成单数",
                    "data": "order_count",
                    "type": "num",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '--';
                            return data;
                        }
                        else if (type === 'sort')
                        {
                            // 排序时返回数值
                            return data;
                        }
                        else
                        {
                            // 过滤等其他操作使用原始值
                            return data;
                        }
                    }
                },
                {
                    "title": "成单率",
                    "data": null,
                    "type": "num",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!row.order_count) return '--';
                        // return data;

                        // return parseFloat(row.order_count / row.call_count * 1000).toFixed(1) + "‰";
                        // return parseFloat(row.order_count / row.call_count).toFixed(4);

                        // 计算成单率（千分比）
                        var $rate = parseFloat(row.order_count / row.call_count * 1000);

                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!row.order_count) return '--';
                            else return $rate.toFixed(1) + " ‰";
                        }
                        else if (type === 'sort')
                        {
                            // 排序时返回数值
                            return $rate;
                        }
                        else
                        {
                            // 过滤等其他操作使用原始值
                            return $rate;
                        }
                    }
                },
                {
                    "title": "单均话费",
                    "data": "order_count",
                    "type": "num",
                    "className": "bg-published",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.taskId == "统计")
                        {
                            $(nTd).addClass('text-red').addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(data > 0) return parseFloat((parseInt(row.call_time_sum) * 0.13) / data).toFixed(2);
                            else return '--';
                            // if(!data) return '--';
                            // return parseFloat((parseInt(row.call_time_sum) * 0.13) / data).toFixed(2);
                        }
                        else if (type === 'sort')
                        {
                            // 排序时返回数值
                            if(data > 0) return parseFloat((parseInt(row.call_time_sum) * 0.13) / data).toFixed(2);
                            else return data;
                        }
                        else
                        {
                            // 过滤等其他操作使用原始值
                            if(data > 0) return parseFloat((parseInt(row.call_time_sum) * 0.13) / data).toFixed(2);
                            else return data;
                        }
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
        });

    }
</script>