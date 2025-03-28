<script>
    function Datatable_for_Reconciliation_Project_List($tableId)
    {

        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[50, 100, 200, -1], ["50", "100", "200", "全部"]],
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
                'url': "{{ url('/reconciliation/v1/operate/project/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="project-id"]').val();
                    d.name = $tableSearch.find('input[name="project-name"]').val();
                    d.title = $tableSearch.find('input[name="project-title"]').val();
                    d.keyword = $tableSearch.find('input[name="project-keyword"]').val();
                    d.item_status = $tableSearch.find('select[name="project-item-status"]').val();
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
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "width": "160px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_record = '';
                        var $html_able = '';
                        var $html_delete = '';
                        var $html_abandon = '';

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs btn-danger- item-disable-by-admin-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs btn-success- item-enable-by-admin-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-by-admin-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs bg-grey- item-restore-by-admin-submit" data-id="'+data+'">恢复</a>';
                        }

                        $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';
                        var $html_trade = '<a class="btn btn-xs bg-default item-modal-show-for-recharge-create" data-id="'+data+'">充值</a>';

                        var $html_operation_record = '<a class="btn btn-xs bg-default item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';

                        var html =
                            // '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                            '<a class="btn btn-xs btn-primary- project-edit-show" data-id="'+data+'">编辑</a>'+
                            $html_able+
                            $html_trade+
                            $html_operation_record+
                            // $html_delete+
                            // $html_record+
                            // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                            // '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                            // '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                            '';
                        return html;

                    }
                },
                {
                    "title": "状态",
                    "data": "item_status",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        if(row.deleted_at != null)
                        {
                            return '<small class="btn-xs bg-black">已删除</small>';
                        }

                        if(data == 1)
                        {
                            return '<small class="btn-xs btn-success">启用</small>';
                        }
                        else
                        {
                            return '<small class="btn-xs btn-danger">禁用</small>';
                        }
                    }
                },
                // {
                //     "title": "项目种类",
                //     "data": "item_category",
                //     "width": "60px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         if(data == 1)
                //         {
                //             return '<small class="btn-xs bg-orange">口腔</small>';
                //         }
                //         if(data == 11)
                //         {
                //             return '<small class="btn-xs bg-red">医美</small>';
                //         }
                //         if(data == 31)
                //         {
                //             return '<small class="btn-xs bg-purple">奢侈品</small>';
                //         }
                //         else
                //         {
                //             return '未知类型';
                //         }
                //     }
                // },
                {
                    "title": "项目名称",
                    "data": "name",
                    "className": "text-center",
                    "width": "160px",
                    "orderable": false,
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
                        return '<a class="project-control" data-id="'+row.id+'" data-title="'+data+'">'+data+'</a>';
                    }
                },
                {
                    "title": "合作单价",
                    "data": "cooperative_unit_price",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','合作单价');
                        $(nTd).attr('data-key','cooperative_unit_price').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "累计充值",
                    "data": "funds_recharge_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','累计充值');
                        $(nTd).attr('data-key','funds_recharge_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "累计营收",
                    "data": "funds_revenue_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','累计消费');
                        $(nTd).attr('data-key','funds_revenue_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(parseFloat(data).toFixed(2));
                    }
                },
                {
                    "title": "总坏账",
                    "data": "funds_bad_debt_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','总坏账');
                        $(nTd).attr('data-key','funds_bad_debt_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(parseFloat(data).toFixed(2));
                    }
                },
                {
                    "title": "实际消费",
                    "data": "funds_consumption_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','实际消费');
                        $(nTd).attr('data-key','funds_consumption_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(parseFloat(data).toFixed(2));
                    }
                },
                {
                    "title": "余额",
                    "data": "id",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','余额');
                        $(nTd).attr('data-key','balance').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        var $balance = parseFloat(row.funds_recharge_total - row.funds_consumption_total);
                        if($balance > 500) return $balance;
                        else return '<b class="text-red">'+$balance+'</b>';

                    }
                },
                {
                    "title": "总佣金",
                    "data": "channel_commission_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','渠道佣金');
                        $(nTd).attr('data-key','channel_commission_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(parseFloat(data).toFixed(2));

                    }
                },
                {
                    "title": "总成本",
                    "data": "daily_cost_total",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','成本');
                        $(nTd).attr('data-key','daily_cost_total').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(parseFloat(data).toFixed(2));

                    }
                },
                {
                    "title": "利润",
                    "data": "id",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id).attr('data-name','利润');
                        $(nTd).attr('data-key','profit').attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        var $profit = parseFloat(parseFloat(row.funds_consumption_total - row.channel_commission_total - row.daily_cost_total).toFixed(2));
                        if($profit > 0) return '<b class="text-green">'+$profit+'</b>';
                        else return '<b class="text-red">'+$profit+'</b>';

                    }
                },
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
                        return row.creator == null ? '未知' : '<a target="_blank" href="javascript:void(0);">'+row.creator.name+'</a>';
                    }
                },
                {
                    "title": "更新时间",
                    "data": 'updated_at',
                    "className": "font-12px",
                    "width": "120px",
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
                },
            ],
            "drawCallback": function (settings) {

                console.log('project-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>