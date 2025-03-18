<script>
    function Datatable_for_Statistic_Staff_Rank($tableId)
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
            "sDom": '<t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/statistic/production/staff-rank') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="staff-rank-id"]').val();
                    d.name = $tableSearch.find('input[name="staff-rank-name"]').val();
                    d.title = $tableSearch.find('input[name="staff-rank-title"]').val();
                    d.keyword = $tableSearch.find('input[name="staff-rank-keyword"]').val();
                    d.status = $tableSearch.find('select[name="staff-rank-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-staff-rank-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-staff-rank-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-staff-rank-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-staff-rank-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-staff-rank-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-staff-project"]').val();
                    d.rank_object_type = $tableSearch.find('select[name="statistic-staff-rank-object-type"]').val();
                    d.rank_staff_type = $tableSearch.find('select[name="statistic-staff-rank-staff-type"]').val();
                    d.department_district = $tableSearch.find('select[name="statistic-staff-rank-department-district"]').val();
                    d.department_group = $tableSearch.find('select[name="statistic-staff-rank-department-group"]').val();

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
                    "title": "ID",
                    "data": "id",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["asc", "desc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        return data;

                    }
                },
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
                        if(row.username)
                        {
                            return '<a class="staff-control" data-id="'+row.id+'" data-title="'+row.username+'">'+row.username+'</a>';
                        }
                    }
                },
                {
                    "title": "部门",
                    "data": "id",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        if(row.department_er) {
                            return '<a href="javascript:void(0);">'+row.department_er.name+'</a>';
                        }
                        else return '--';

                    }
                },
                {
                    "title": "工单量",
                    "data": "delivery_count_for_all",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "+V量",
                    "data": "delivery_count_for_wx",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "预约量",
                    "data": "delivery_count_for_come_9",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "上门量",
                    "data": "delivery_count_for_come_11",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "交易次数",
                    "data": "delivery_count_for_transaction_num",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "交易数量",
                    "data": "delivery_count_for_transaction_count",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "交易金额",
                    "data": "delivery_count_for_transaction_amount",
                    "className": "bg-inspected",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
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