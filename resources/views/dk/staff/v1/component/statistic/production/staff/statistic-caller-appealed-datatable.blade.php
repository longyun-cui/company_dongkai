<script>
    function Datatable__for__Statistic_Caller_Appealed($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "serverSide": false,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/o1/statistic/production/caller-appealed') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="caller-appealed-id"]').val();
                    d.name = $tableSearch.find('input[name="caller-appealed-name"]').val();
                    d.title = $tableSearch.find('input[name="caller-appealed-title"]').val();
                    d.keyword = $tableSearch.find('input[name="caller-appealed-keyword"]').val();
                    d.status = $tableSearch.find('select[name="caller-appealed-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-caller-appealed-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-caller-appealed-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-caller-appealed-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-caller-appealed-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-caller-appealed-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-caller--project"]').val();
                    d.team = $tableSearch.find('select[name="statistic-caller-appealed--team"]').val();
                    d.group = $tableSearch.find('select[name="statistic-caller-appealed--group"]').val();

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
                    "title": "姓名",
                    "data": "name",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        {
                            // this.column(2)
                            $(nTd).addClass('modal-show-for-text');
                            $(nTd).attr('data-id',row.id).attr('data-name','姓名');
                            $(nTd).attr('data-key','username').attr('data-value',data);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.name)
                        {
                            return '<a class="caller-control" data-id="'+row.id+'" data-title="'+data+'">'+row.name+' ('+row.id+')'+'</a>';
                        }
                    }
                },
                {
                    "title": "部门",
                    "data": "id",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        var $team_name = row.team_er == null ? '' : row.team_er.name;
                        var $group_name = row.team_group_er == null ? '' : (' - ' + row.team_group_er.name);
                        return $team_name + $group_name;

                    }
                },
                {
                    "title": "提交量",
                    "data": "order_count__for__all",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                    "title": "拒绝量",
                    "data": "order_count__for__rejected",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                    "title": "申诉量",
                    "data": "order_count__for__appealed",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                    "title": "驳回",
                    "data": "order_count__for__appealed_rejected",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(data) $(nTd).addClass('_bold').addClass('text-orange');
                    },
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                    "title": "通过",
                    "data": "order_count__for__success",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(data) $(nTd).addClass('_bold').addClass('text-green');
                    },
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                    "title": "失败",
                    "data": "order_count__for__failed",
                    "className": "",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(data) $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if (type === 'display')
                        {
                            // 显示时返回格式化字符串
                            if(!data) return '';
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
                // {
                //     "title": " ",
                //     "data": "id",
                //     "className": "",
                //     "width": "",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     render: function(data, type, row, meta) {
                //         return '';
                //     }
                // },

            ],
            "columnDefs": [
                {
                    "targets": [2],
                    "orderData": [2,3,4]
                },
                {
                    "targets": [3],
                    "orderData": [3,4,3]
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