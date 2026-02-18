<script>
    function Datatable__for__Statistic_Marketing_Project($tableId)
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
                'url': "{{ url('/o1/statistic/marketing/project') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="rank-id"]').val();
                    d.name = $tableSearch.find('input[name="rank-name"]').val();
                    d.title = $tableSearch.find('input[name="rank-title"]').val();
                    d.keyword = $tableSearch.find('input[name="rank-keyword"]').val();
                    d.status = $tableSearch.find('select[name="rank-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-project-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-project-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-project-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-project-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-project-ended"]').val();
                    d.rank_object_type = $tableSearch.find('select[name="rank-object-type"]').val();
                    d.rank_staff_type = $tableSearch.find('select[name="rank-staff-type"]').val();
                    d.department_district = $tableSearch.find('select[name="rank-department-district"]').val();
                    d.department_group = $tableSearch.find('select[name="rank-department-group"]').val();
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
                    "title": "项目ID",
                    "data": "id",
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
                        return data;
                    }
                },
                {
                    "title": "项目名称",
                    "data": "name",
                    "className": "text-center",
                    "width": "120px",
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
{{--                @if($me->department_district_id == 0)--}}
{{--                {--}}
{{--                    "title": "团队",--}}
{{--                    "data": "pivot_project_team",--}}
{{--                    "className": "text-center",--}}
{{--                    "width": "200px",--}}
{{--                    "orderable": false,--}}
{{--                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {--}}
{{--                        if(row.id == "统计")--}}
{{--                        {--}}
{{--                            $(nTd).addClass('_bold');--}}
{{--                        }--}}
{{--                    },--}}
{{--                    render: function(data, type, row, meta) {--}}
{{--                        var html = '';--}}
{{--                        $.each(data,function( key, val ) {--}}
{{--//                                console.log( key, val, this );--}}
{{--                            html += '<a href="javascript:void(0);">'+this.name+'</a> &nbsp;';--}}
{{--                        });--}}
{{--                        return html;--}}
{{--                    }--}}
{{--                },--}}
{{--                @endif--}}
                {
                    "title": "每日目标",
                    "data": "daily_goal",
                    "className": "text-center text-green",
                    "width": "80px",
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
                    "title": "存单交付<br>(昨转今)",
                    "data": "count__for__order_yesterday_all",
                    "className": "bg-delivered _bold",
                    "width": "80px",
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
                    "title": "当日交付",
                    "data": "count__for__order_today_all",
                    "className": "bg-delivered _bold",
                    "width": "80px",
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
                    "title": "分发交付",
                    "data": "count__for__delivered_distribute",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "交付总量",
                    "data": "count__for__delivered_normal",
                    "className": "bg-delivered _bold",
                    "width": "80px",
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
                    "title": "完成率",
                    "data": "rate__for__completed",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                },
                {
                    "title": "正常交付",
                    "data": "count__for__order_today_normal",
                    "className": "bg-light-grey",
                    "width": "80px",
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
                    "title": "折扣交付",
                    "data": "count__for__order_today_discount",
                    "className": "bg-light-grey",
                    "width": "80px",
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
                    "title": "郊区交付",
                    "data": "count__for__order_today_suburb",
                    "className": "bg-light-grey",
                    "width": "80px",
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
                    "title": "内部交付",
                    "data": "count__for__order_today_inside",
                    "className": "bg-light-grey",
                    "width": "80px",
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
                    "title": "隔日交付<br>(今日存单)",
                    "data": "count__for__order_today_tomorrow",
                    "className": "bg-light-gray",
                    "width": "80px",
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