<script>

    function Datatable_Statistic_Project_Detail($tableId, $eChartId)
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
            "sDom": '<t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic-list/statistic-project-detail') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.project_id = $tableSearch.find('input[name="statistic-list-project-detail-project-id"]').val();
                    d.id = $tableSearch.find('input[name="statistic-list-project-detail-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-list-project-detail-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-list-project-detail-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-list-project-detail-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-list-project-detail-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-list-project-detail-time-type"]').val();
                    d.assign_month = $tableSearch.find('input[name="statistic-list-project-detail-month"]').val();
                    d.assign_date = $tableSearch.find('input[name="statistic-list-project-detail-date"]').val();
                    d.assign_start = $tableSearch.find('input[name="statistic-list-project-detail-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="statistic-list-project-detail-ended"]').val();
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
//                 {
//                     "title": "ID",
//                     "data": "id",
//                     "className": "text-center",
//                     "width": "80px",
//                     "orderable": true,
//                     "orderSequence": ["asc", "desc"],
//                     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                         if(row.id == "统计")
//                         {
//                             $(nTd).addClass('_bold');
//                         }
//                     },
//                     render: function(data, type, row, meta) {
//                         return data;
//                     }
//                 },
                {
                    "title": "日期",
                    "data": "statistic_date",
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
                    "title": "项目名称",
                    "data": "project_id",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": true,
                    "orderSequence": ["asc", "desc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.project_er == null)
                        {
                            return data;
                        }
                        else
                        {
                            if(row.project_er.alias_name)
                            {
                                var $project_name = row.project_er.name+' ('+row.project_er.alias_name+')';
                                return '<a class="statistic-project-detail-control" data-id="'+data+'" data-title="'+$project_name+'">'+$project_name+'</a>';
                            }
                            else
                            {
                                var $project_name = row.project_er.name;
                                return '<a class="statistic-project-detail-control" data-id="'+data+'" data-title="'+$project_name+'">'+$project_name+'</a>';
                            }
                        }
                    }
                },
                {
                    "title": "当日出单",
                    "data": "production_published_num",
                    "className": "bg-published",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                // {
                //     "title": "审核量",
                //     "data": "production_inspected_num",
                //     "className": "bg-published",
                //     "width": "100px",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "有效出单",
                    "data": "production_accepted_num",
                    "className": "bg-published _bold",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "郊区通过",
                    "data": "production_accepted_suburb_num",
                    "className": "bg-published",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "内部通过",
                    "data": "production_accepted_inside_num",
                    "className": "bg-published",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "交付总量",
                    "data": "marketing_delivered_num",
                    "className": "bg-delivered _bold",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "前日存单",
                    "data": "marketing_yesterday_num",
                    "className": "bg-delivered",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "当日产出",
                    "data": "marketing_today_num",
                    "className": "bg-delivered",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "分发量",
                    "data": "marketing_distribute_num",
                    "className": "bg-delivered",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data
                    }
                },
                {
                    "title": "隔日交付<br>(当日存单)",
                    "data": "marketing_tomorrow_num",
                    "className": "bg-delivered",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data
                    }
                },
                {
                    "title": "特殊交付",
                    "data": "marketing_special_num",
                    "className": "bg-delivered",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
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
                    "title": "备注",
                    "data": "description",
                    "className": "text-center",
                    "width": "",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                        // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                        // else return '';
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