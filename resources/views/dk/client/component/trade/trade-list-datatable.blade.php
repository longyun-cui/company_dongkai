<script>

    function Datatable_for_Trade_List($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 200, 500], ["10", "50", "200", "500"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "iDisplayStart": 0,
            "iDisplayLength": 10,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": ($(document).height() - 448)+"px",
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/v1/operate/trade/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="trade-id"]').val();
                    d.order_id = $tableSearch.find('input[name="trade-order-id"]').val();
                    d.name = $tableSearch.find('input[name="trade-name"]').val();
                    d.title = $tableSearch.find('input[name="trade-title"]').val();
                    d.keyword = $tableSearch.find('input[name="trade-keyword"]').val();
                    d.remark = $tableSearch.find('input[name="trade-remark"]').val();
                    d.description = $tableSearch.find('input[name="trade-description"]').val();
                    d.department_district = $tableSearch.find('select[name="trade-department-district[]"]').val();
                    d.client = $tableSearch.find('select[name="trade-client"]').val();
                    d.project = $tableSearch.find('select[name="trade-project"]').val();
                    d.status = $tableSearch.find('select[name="trade-status"]').val();
                    d.trade_type = $tableSearch.find('select[name="trade-trade-type"]').val();
                    d.order_type = $tableSearch.find('select[name="trade-type"]').val();
                    d.client_type = $tableSearch.find('select[name="trade-client-type"]').val();
                    d.client_name = $tableSearch.find('input[name="trade-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="trade-client-phone"]').val();

                    d.is_confirmed = $('select[name="trade-is-confirmed"]').val();

                    d.delivered_status = $tableSearch.find('select[name="trade-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="trade-delivered-result[]"]').val();

                    d.time_type = $tableSearch.find('input[name="trade-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="trade-month"]').val();
                    d.time_date = $tableSearch.find('input[name="trade-date"]').val();
                    d.date_start = $tableSearch.find('input[name="trade-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="trade-ended"]').val();

                },
            },
            "columnDefs": [
                {
                    // "targets": [10, 11, 15, 16],
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }
            ],
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" id="check-review-all">',
                    "data": "id",
                    "width": "40px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-item-id="'+row.id+'"></label>';
                    }
                },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                {
                    "title": "ID",
                    "data": "id",
                    "className": "",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_operate');
                            $(nTd).attr('data-id',row.id).attr('data-name','操作');
                            $(nTd).attr('data-key','order_operate').attr('data-value',row.id);
                            $(nTd).attr('data-content',JSON.stringify(row.content_decode));
                        }
                    },
                    render: function(data, type, row, meta) {

                        var $html_confirm = '<a class="btn btn-xs bg-default item-modal-show-for-confirm" data-id="'+data+'">确认</a>';
                        var $html_delete = '<a class="btn btn-xs bg-default item-modal-show-for-delete" data-id="'+data+'">删除</a>';


                        var $html =
                            $html_confirm+
                            $html_delete+
                            // $html_record+
                            '';
                        return $html;

                    }
                },
                {
                    "title": "工单",
                    "data": "delivery_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);">'+data+'</a>';
                    }
                },
                {
                    "title": "交易时间",
                    "data": 'transaction_datetime',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
//                            return data;
                        if(!data) return '';
                        var $date = new Date(data);
                        var $year = $date.getFullYear();
                        var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        var $day = ('00'+($date.getDate())).slice(-2);
                        var $hour = ('00'+$date.getHours()).slice(-2);
                        var $minute = ('00'+$date.getMinutes()).slice(-2);
                        var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                    }
                },
                {
                    "title": "成交数量",
                    "data": "transaction_count",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "成交金额",
                    "data": "transaction_amount",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "品牌",
                    "data": "title",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "备注",
                    "data": "description",
                    "className": "",
                    "width": "",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "className": "text-center",
                    "width": "120px",
                    "title": "创建者",
                    "data": "creator_id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                    }
                },
                {
                    "className": "font-12px",
                    "width": "120px",
                    "title": "创建时间",
                    "data": 'created_at',
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        var $date = new Date(data*1000);
                        var $year = $date.getFullYear();
                        var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        var $day = ('00'+($date.getDate())).slice(-2);
                        var $hour = ('00'+$date.getHours()).slice(-2);
                        var $minute = ('00'+$date.getMinutes()).slice(-2);
                        var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('trade-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>