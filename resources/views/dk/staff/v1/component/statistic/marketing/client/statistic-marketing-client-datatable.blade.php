<script>
    function Datatable__for__Statistic_Marketing_Client($tableId)
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
                'url': "{{ url('/o1/statistic/marketing/client') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="client-id"]').val();
                    d.name = $tableSearch.find('input[name="client-name"]').val();
                    d.title = $tableSearch.find('input[name="client-title"]').val();
                    d.keyword = $tableSearch.find('input[name="client-keyword"]').val();
                    d.user_status = $tableSearch.find('select[name="statistic-client-user-status"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-client-time-type"]').val();
                    d.assign_month = $tableSearch.find('input[name="statistic-client-month"]').val();
                    d.assign_date = $tableSearch.find('input[name="statistic-client-date"]').val();
                    d.assign_start = $tableSearch.find('input[name="statistic-client-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="statistic-client-ended"]').val();

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
                    "title": "客户ID",
                    "data": "id",
                    "className": "",
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
                    "title": "客户名称",
                    "data": "username",
                    "className": "",
                    "width": "160px",
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
                // {
                //     "title": "客服<br>报单量",
                //     "data": "order_count_for_all",
                //     "className": "bg-service-customer",
                //     "width": "80px",
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


                // {
                //     "title": "交付<br>总量",
                //     "data": "order_count_for_delivered",
                //     "className": "bg-delivered",
                //     "width": "60px",
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
                    "title": "今日交付",
                    "data": "order_count_for_delivered_today",
                    "className": "bg-delivered _bold",
                    "width": "80px",
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
                    "title": "有效产出",
                    "data": "order_count_for_delivered_effective",
                    "className": "bg-delivered _bold",
                    "width": "80px",
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
                    "title": "已交付",
                    "data": "order_count_for_delivered_completed",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "内部交付",
                    "data": "order_count_for_delivered_inside",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "隔日交付",
                    "data": "order_count_for_delivered_tomorrow",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "重复",
                    "data": "order_count_for_delivered_repeated",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "驳回",
                    "data": "order_count_for_delivered_rejected",
                    "className": "bg-delivered",
                    "width": "80px",
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
                    "title": "有效率",
                    "data": "order_rate_for_delivered_effective",
                    "className": "bg-inspected",
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
                        if(data) return data + " %";
                        return data
                    }
                },
                {
                    "title": "产出率",
                    "data": "order_rate_for_delivered_actual",
                    "className": "bg-inspected",
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
                        if(data) return data + " %";
                        return data
                    }
                },


                // {
                //     "title": "审核<br>有效量",
                //     "data": "order_count_for_effective",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
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


                // {
                //     "title": "备注",
                //     "data": "remark",
                //     "className": "text-left",
                //     "width": "",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //         // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                //         // else return '';
                //     }
                // }
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