<script>
    function Datatable_for_Reconciliation_Daily_List($tableId)
    {

        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[50, 100, 200, 500, 2000], ["50", "100", "200", "500", "2000"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": false,
//                "scrollY": true,
            "scrollCollapse": true,
            "ajax": {
                'url': "{{ url('/reconciliation/v1/operate/daily/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="daily-id"]').val();
                    d.name = $tableSearch.find('input[name="daily-name"]').val();
                    d.title = $tableSearch.find('input[name="daily-title"]').val();
                    d.keyword = $tableSearch.find('input[name="daily-keyword"]').val();
                    d.item_status = $tableSearch.find('select[name="daily-item-status"]').val();

                    d.project = $tableSearch.find('select[name="daily-project[]"]').val();

                    d.time_type = $tableSearch.find('input[name="daily-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="daily-month"]').val();
                    d.time_date = $tableSearch.find('input[name="daily-date"]').val();
                    d.date_start = $tableSearch.find('input[name="daily-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="daily-ended"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    "width": "40px",
                    "data": "id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                    }
                },
//                    {
//                        "title": "序号",
//                        "data": null,
//                        "width": "40px",
//                        "targets": 0,
//                        'orderable': false
//                    },
                {
                    "title": "ID",
                    "data": "id",
                    "className": "",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return '--';
                        }

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_record = '';
                        var $html_able = '';
                        var $html_delete = '';
                        var $html_abandon = '';

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-by-admin-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs bg-grey- item-restore-by-admin-submit" data-id="'+data+'">恢复</a>';
                        }

                        // $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                        // var $html_trade = '<a class="btn btn-xs bg-default item-modal-show-for-settle-create" data-id="'+data+'">结算</a>';

                        var $html_operation_record = '<a class="btn btn-xs bg-default item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';

                        var html =
                            // '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                            // '<a class="btn btn-xs btn-primary- daily-edit-show" data-id="'+data+'">编辑</a>'+
                            $html_able+
                            // $html_delete+
                            // $html_trade+
                            $html_operation_record+
                            // $html_record+
                            // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                            // '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                            // '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                            '';
                        return html;

                    }
                },
                {
                    "title": "项目",
                    "data": "project_id",
                    "className": "text-center",
                    "width": "100px",
                    "orderable": true,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-id',row.id).attr('data-name','项目名称');
                            $(nTd).attr('data-key','name').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','项目名称');

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return '--';
                        }
                        return row.project_er == null ? '未知' : '<a class="project-control" data-id="'+row.project_id+'" data-title="'+row.project_er.name+'">'+row.project_er.name+'</a>';
                    }
                },
                {
                    "title": "交付日期",
                    "data": "assign_date",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付日期');
                            $(nTd).attr('data-key','daily_goal').attr('data-value',data);
                            $(nTd).attr('data-column-name','交付日期');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "交付量",
                    "data": "delivery_quantity",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-row-index',iRow);

                        $(nTd).attr('data-id',row.id).attr('data-name','交付量');
                        $(nTd).attr('data-key','delivery_quantity').attr('data-value',data);

                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {
                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','交付量');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }

                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "合作单价",
                    "data": "cooperative_unit_price",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-row-index',iRow);

                        $(nTd).attr('data-id',row.id).attr('data-name','交付量');
                        $(nTd).attr('data-key','cooperative_unit_price').attr('data-value',data);

                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {
                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','合作单价');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return '--';
                        }
                        return parseFloat(data);
                    }
                },
                {
                    "title": "营收",
                    "data": "id",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','营收');
                        $(nTd).attr('data-key','revenue').attr('data-value',data);

                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return row.revenue;
                        }
                        return parseFloat(row.delivery_quantity * row.cooperative_unit_price);
                    }
                },
                {
                    "title": "坏账",
                    "data": "funds_bad_debt_total",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-row-index',iRow);

                        $(nTd).attr('data-id',row.id).attr('data-name','坏账');
                        $(nTd).attr('data-key','funds_bad_debt_total').attr('data-value',data);

                        if(row.id == "统计") $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {
                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','坏账');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "应收款",
                    "data": "funds_should_settled_total",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','应收款');
                        $(nTd).attr('data-key','funds_should_settled_total').attr('data-value',data);

                        if(row.id == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return row.funds_should_settled_total;
                        }
                        var $revenue = parseFloat(row.delivery_quantity * row.cooperative_unit_price);
                        var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);
                        return parseFloat($revenue - $funds_bad_debt_total);
                    }
                },
                {
                    "title": "渠道佣金",
                    "data": "channel_commission",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-row-index',iRow);

                        $(nTd).attr('data-id',row.id).attr('data-name','渠道佣金');
                        $(nTd).attr('data-key','channel_commission').attr('data-value',data);


                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {
                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','渠道佣金');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "成本",
                    "data": "daily_cost",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-row-index',iRow);

                        $(nTd).addClass('modal-show-for-field-set');
                        $(nTd).attr('data-id',row.id).attr('data-name','成本');
                        $(nTd).attr('data-key','daily_cost').attr('data-value',data);


                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {
                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','成本');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "利润",
                    "data": "id",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','利润');
                        $(nTd).attr('data-key','profit').attr('data-value',data);

                        if(row.id == "统计") $(nTd).addClass('_bold').addClass('text-red');
                    },
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return row.profit;
                        }
                        var $revenue = parseFloat(row.delivery_quantity * row.cooperative_unit_price);
                        var $channel_commission = parseFloat(row.channel_commission);
                        var $daily_cost = parseFloat(row.daily_cost);
                        var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);
                        var $profit = parseFloat(parseFloat($revenue - $channel_commission - $daily_cost - $funds_bad_debt_total).toFixed(2));
                        if($profit > 0) return '<b class="text-green">'+$profit+'</b>';
                        else return '<b class="text-red">'+$profit+'</b>';
                    }
                },
                // {
                //     "title": "已结算",
                //     "data": "funds_already_settled_total",
                //     "className": "text-center",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         $(nTd).attr('data-id',row.id).attr('data-name','已结算');
                //         $(nTd).attr('data-key','funds_already_settled_total').attr('data-value',data);
                //
                //         if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                //     },
                //     render: function(data, type, row, meta) {
                //         return parseFloat(data);
                //     }
                // },
                // {
                //     "title": "待结算",
                //     "data": "funds_already_settled_total",
                //     "className": "text-center",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         $(nTd).attr('data-id',row.id).attr('data-name','待结算');
                //         $(nTd).attr('data-key','funds_already_settled_total').attr('data-value',data);
                //
                //         if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                //     },
                //     render: function(data, type, row, meta) {
                //         if(row.id == "统计")
                //         {
                //             return row.to_be_settled;
                //         }
                //         var $revenue = parseFloat(row.delivery_quantity * row.cooperative_unit_price);
                //         var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);
                //         var $funds_already_settled_total = parseFloat(row.funds_already_settled_total);
                //         var $to_be_settled = parseFloat($revenue - $funds_bad_debt_total - $funds_already_settled_total);
                //         if($to_be_settled > 0)
                //         {
                //             return '<b class="text-red">'+$to_be_settled+'</b>';
                //         }
                //         else return $to_be_settled;
                //     }
                // },
                {
                    "title": "备注",
                    "data": "remark",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','备注');
                            $(nTd).attr('data-key','remark').attr('data-value',data);
                            $(nTd).attr('data-column-name','备注');
                            $(nTd).attr('data-text-type','textarea');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }

                        if(row.id == "统计")  $(nTd).addClass('_bold').addClass('text-red');
                        else
                        {

                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                        // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                        // else return '';
                    }
                },
                {
                    "title": "创建者",
                    "data": "creator_id",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return '--'
                        }
                        return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.name+'</a>';
                    }
                },
                {
                    "title": "更新时间",
                    "data": 'updated_at',
                    "className": "font-12px",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.id == "统计")
                        {
                            return '--'
                        }
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
                },
            ],
            "drawCallback": function (settings) {

                console.log('daily-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>