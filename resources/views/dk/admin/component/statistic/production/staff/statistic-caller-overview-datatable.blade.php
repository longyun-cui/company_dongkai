<script>
    function Table_Datatable_Ajax_Statistic_Caller_Overview($tableId)
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
                'url': "{{ url('/v1/operate/statistic/production/caller-overview') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="caller-id"]').val();
                    d.name = $tableSearch.find('input[name="caller-name"]').val();
                    d.title = $tableSearch.find('input[name="caller-title"]').val();
                    d.keyword = $tableSearch.find('input[name="caller-keyword"]').val();
                    d.status = $tableSearch.find('select[name="caller-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-caller-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-caller-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-caller-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-caller-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-caller-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-caller-project"]').val();

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
                    "title": "大区经理",
                    "data": "department_district_id",
                    "className": "vertical-middle",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.department_district_er.leader == null ? '未知' : '<a href="javascript:void(0);">' + row.department_district_er.name + '</a>' + '<br>' + '<a href="javascript:void(0);">' + row.department_district_er.leader.username + '</a>';
                    }
                },
                {
                    "title": "部门主管",
                    "data": "department_group_id",
                    "className": "vertical-middle",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.department_group_er.leader == null ? '未知' : '<a href="javascript:void(0);">' + row.department_group_er.name + '</a>' + '<br>' + '<a href="javascript:void(0);">' + row.department_group_er.leader.username + '</a>';
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
                    "title": "姓名",
                    "data": "username",
                    "className": "text-center",
                    "width": "160px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        // return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + row.id + '" target="_blank">'+data+' ('+row.id+')'+'</a>';
                        return '<a class="caller-control" data-id="'+row.id+'" data-title="'+data+'">'+data+' ('+row.id+')'+'</a>';
                    }
                },
                {
                    "title": "客服<br>报单量",
                    "data": "order_count_for_all",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },




                // {
                //     "title": "审核<br>有效量",
                //     "data": "order_count_for_effective",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "审核<br>通过量",
                    "data": "order_count_for_accepted",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                // {
                //     "title": "审核<br>内部通过",
                //     "data": "order_count_for_accepted_inside",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "审核<br>拒绝量",
                    "data": "order_count_for_refused",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                // {
                //     "title": "审核<br>重复量",
                //     "data": "order_count_for_repeated",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //
                //         return data;
                //     }
                // },
                {
                    "title": "审核<br>通过率",
                    "data": "order_rate_for_accepted",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                },


                {
                    "title": "主管<br>客服<br>报单量",
                    "data": "group_count_for_all",
                    "className": "text-center vertical-middle bg-group",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                // {
                //     "title": "主管<br>交付<br>总量",
                //     "data": "group_count_for_delivered",
                //     "className": "text-center vertical-middle bg-group",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data
                //     }
                // },
                // {
                //     "title": "主管<br>审核<br>有效量",
                //     "data": "group_count_for_effective",
                //     "className": "text-center vertical-middle bg-group",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "主管<br>审核<br>通过量",
                    "data": "group_count_for_accepted",
                    "className": "text-center vertical-middle bg-group",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "主管<br>审核<br>通过率",
                    "data": "group_rate_for_accepted",
                    "className": "text-center vertical-middle bg-group",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                },


                {
                    "title": "经理<br>客服<br>报单量",
                    "data": "district_count_for_all",
                    "className": "text-center vertical-middle bg-district",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                // {
                //     "title": "经理<br>审核<br>有效量",
                //     "data": "district_count_for_effective",
                //     "className": "text-center vertical-middle bg-district",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "经理<br>审核<br>通过量",
                    "data": "district_count_for_accepted",
                    "className": "text-center vertical-middle bg-district",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "经理<br>审核<br>通过率",
                    "data": "district_rate_for_accepted",
                    "className": "text-center vertical-middle bg-district",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                },
            ],
            "columnDefs": [
                {
                    targets: [0], //要合并的列数（第1，2，3列）
                    createdCell: function (td, cellData, rowData, row, col) {
                        //重要的操作可以合并列的代码
                        var rowspan = rowData.district_merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
                    },
                    "data": "department_district_id",
                    "render": function (data, type, full) {
                        // return "<span title='" + data + "'>" + data + "</span>";
                        return row.department_district_er == null ? '未知' : '<a href="javascript:void(0);">'+row.department_district_er.leader.username+'</a>';
                    }
                },
                {
                    targets: [1], //要合并的列数（第1，2，3列）
                    createdCell: function (td, cellData, rowData, row, col) {
                        //重要的操作可以合并列的代码
                        var rowspan = rowData.group_merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
                    },
                    "data": "department_group_id",
                    "render": function (data, type, full) {
                        return row.department_group_er == null ? '未知' : '<a href="javascript:void(0);">'+row.department_group_er.leader.username+'</a>';
                    }
                },
                {
                    targets: [7,8,9],
                    createdCell: function (td, cellData, rowData, row, col) {
                        var rowspan = rowData.group_merge;
                        if (rowspan > 1) {
                            $(td).attr('rowspan', rowspan)
                        }
                        if (rowspan == 0) {
                            $(td).remove();
                        }
                    }
                },
                {
                    targets: [10,11,12],
                    createdCell: function (td, cellData, rowData, row, col) {
                        var rowspan = rowData.district_merge;
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