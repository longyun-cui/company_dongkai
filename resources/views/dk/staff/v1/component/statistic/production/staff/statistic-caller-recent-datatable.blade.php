<script>
    function Datatable__for__Statistic_Caller_Recent($tableId)
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
                'url': "{{ url('/o1/statistic/production/caller-recent') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="caller-recent-id"]').val();
                    d.name = $tableSearch.find('input[name="caller-recent-name"]').val();
                    d.title = $tableSearch.find('input[name="caller-recent-title"]').val();
                    d.keyword = $tableSearch.find('input[name="caller-recent-keyword"]').val();
                    d.status = $tableSearch.find('select[name="caller-recent-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-caller-recent-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-caller-recent-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-caller-recent-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-caller-recent-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-caller-recent-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-caller-project"]').val();
                    d.recent_object_type = $tableSearch.find('select[name="statistic-caller-recent-object-type"]').val();
                    d.recent_staff_type = $tableSearch.find('select[name="statistic-caller-recent-staff-type"]').val();
                    d.team = $tableSearch.find('select[name="statistic-caller-recent-team"]').val();
                    d.team_group = $tableSearch.find('select[name="statistic-caller-recent-team-group"]').val();

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
                        return '<a class="caller-control" data-id="'+row.id+'" data-title="'+data+'">'+data+' ('+row.id+')'+'</a>';
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
                    "title": "今天",
                    "data": "order_0",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "昨天",
                    "data": "order_1",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "前天",
                    "data": "order_2",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "3天前",
                    "data": "order_3",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "4天前",
                    "data": "order_4",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "5天前",
                    "data": "order_5",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "6天前",
                    "data": "order_6",
                    "className": "bg-delivered",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                // {
                //     "title": "交付<br>有效率",
                //     "data": "order_rate_for_delivered_effective",
                //     "className": "bg-inspected",
                //     "width": "100px",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     render: function(data, type, row, meta) {
                //         if(data) return data + " %";
                //         return data
                //     }
                // }

            ],
            "columnDefs": [
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