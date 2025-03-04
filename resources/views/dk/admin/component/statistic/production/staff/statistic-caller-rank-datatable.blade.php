<script>
    function Table_Datatable_Ajax_Statistic_Caller_Rank($tableId)
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
                'url': "{{ url('/v1/operate/statistic/production/caller-rank') }}",
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
                    "title": "姓名",
                    "data": "id",
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
                        if(row.username) return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + data + '" target="_blank">'+row.username+' ('+row.id+')'+'</a>';
                        return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + data + '" target="_blank">'+row.username+' ('+row.id+')'+'</a>';
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
                        var $district_name = row.department_district_er == null ? '' : row.department_district_er.name;
                        var $group_name = row.department_group_er == null ? '' : (' - ' + row.department_group_er.name);
                        return $district_name + $group_name;

                    }
                },
                {
                    "title": "客服<br>提交量",
                    "data": "order_count_for_all",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },


                // {
                //     "title": "交付<br>总量",
                //     "data": "order_count_for_delivered",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },


                {
                    "title": "审核<br>有效量",
                    "data": "order_count_for_effective",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "审核<br>通过量",
                    "data": "order_count_for_accepted",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
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
                // {
                //     "title": "内部通过",
                //     "data": "order_count_for_accepted_inside",
                //     "className": "text-center",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                // {
                //     "title": "审核<br>有效率",
                //     "data": "order_rate_for_effective",
                //     "className": "bg-inspected",
                //     "width": "100px",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     render: function(data, type, row, meta) {
                //         if(data) return data + " %";
                //         return data
                //     }
                // },
                {
                    "title": "审核<br>通过率",
                    "data": "order_rate_for_accepted",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                }

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