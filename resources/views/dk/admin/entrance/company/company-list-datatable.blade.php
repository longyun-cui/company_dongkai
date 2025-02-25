<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
                "aLengthMenu": [[50, 200, 500, -1], ["50", "200", "500", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "pagingType": "simple_numbers",
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
                "scrollCollapse": true,
                "ajax": {
                    'url': "{{ url('/company/company-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="company-id"]').val();
                        d.name = $('input[name="company-name"]').val();
                        d.title = $('input[name="company-title"]').val();
                        d.keyword = $('input[name="company-keyword"]').val();
                        d.username = $('input[name="company-username"]').val();
                        d.status = $('select[name="company-status"]').val();
                        d.company_category = $('select[name="company-category"]').val();
                        d.company_type = $('select[name="company-type"]').val();
                        d.work_status = $('select[name="work_status"]').val();
                    },
                },
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                    "rightColumns": "0"
                },
                "columns": [
                    {
                        "title": '<input type="checkbox" id="check-review-all">',
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
                            if(row.is_completed != 1)
                            {
                                $(nTd).addClass('modal-show-for-attachment-');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value','');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "操作",
                        "data": 'id',
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_recharge = '';
                            var $html_recharge_record = '';
                            var $html_able = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_abandon = '';
                            var $html_login = '';

                            @if(in_array($me->user_type,[0,1,9,11]))
                            $html_login = '<a class="btn btn-xs btn-primary- item-admin-login-submit" data-id="'+data+'">登录</a>';
                            @endif

                            if(row.user_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger- item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success- item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black- item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey- item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }
                            if(row.company_category == 1)
                            {
                                $html_recharge = '<a class="btn btn-xs bg-default" data-id="'+data+'">充值</a>';
                            }
                            else
                            {
                                $html_recharge = '<a class="btn btn-xs bg-orange- item-modal-show-for-recharge" data-id="'+data+'">充值</a>';
                            }

                            $html_recharge_record = '<a class="btn btn-xs bg-orange- item-modal-show-for-recharge-record" data-id="'+data+'">财务记录</a>';

                            $html_record = '<a class="btn btn-xs bg-purple- item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                            var html =
                                '<a class="btn btn-xs btn-primary- item-edit-link" data-id="'+data+'">编辑</a>'+
                                $html_able+
                                // <a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                                // $html_publish+
                                $html_recharge+
                                // $html_recharge_record+
                                $html_record+
                                '<a class="btn btn-xs bg-maroon- item-password-admin-reset-submit" data-id="'+data+'">重置密码</a>'+
                                $html_login+
                                // $html_delete+
                                // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                // '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                // '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "title": "状态",
                        "data": "user_status",
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
                    {
                        "title": "公司类型",
                        "data": 'company_category',
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-primary">公司</small>';
                            else if(data == 11) return '<small class="btn-xs bg-purple">渠道</small>';
                            else if(data == 21) return '<small class="btn-xs bg-yellow">商务</small>';
                            else return "有误";
                        }
                    },
                    {
                        "title": "渠道类型",
                        "data": 'company_type',
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.company_category == 1)
                            {
                                return '--';
                            }
                            else if(row.company_category == 11)
                            {
                                if(data == 1) return '<small class="btn-xs bg-primary">直营</small>';
                                else if(data == 11) return '<small class="btn-xs bg-purple">代理</small>';
                                else return "有误";
                            }
                            else if(row.company_category == 21)
                            {
                                return '--';
                            }
                            else return "有误";
                        }
                    },
                    {
                        "title": "名称",
                        "data": "name",
                        "className": "text-center company-name",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','名称');
                                $(nTd).attr('data-key','name').attr('data-value',data);
                                $(nTd).attr('data-column-name','名称');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            if(row.company_category == 1)
                            {
                                return '--';
                            }
                            else if(row.company_category == 11)
                            {
                                return '<a target="_blank" href="/statistic/statistic-monthly-by-channel?channel_id='+row.id+'">'+data+'</a>';
                            }
                            else if(row.company_category == 21)
                            {
                                return '<a target="_blank" href="/statistic/statistic-monthly-by-channel?channel_id='+row.id+'">'+data+'</a>';
                            }
                            else return '';
                        }
                    },
                    {
                        "title": "所属公司",
                        "data": "superior_company_id",
                        "className": "",
                        "width":"100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.company_category == 1)
                            {
                                return '<a href="javascript:void(0);">'+row.name+'</a>';
                            }
                            else if(row.company_category == 11)
                            {
                                if(row.superior_company_er) {
                                    return '<a href="javascript:void(0);">'+row.superior_company_er.name+'</a>';
                                }
                                else return '--';
                            }
                            else if(row.company_category == 21)
                            {
                                if(row.superior_company_er) {
                                    return '<a href="javascript:void(0);">'+row.superior_company_er.name+'</a>';
                                }
                                else return '--';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "登录手机",
                        "data": "mobile",
                        "className": "text-center company-mobile",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','登录手机');
                                $(nTd).attr('data-key','mobile').attr('data-value',data);
                                $(nTd).attr('data-column-name','登录手机');
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
                        "title": "累充金额",
                        "data": "funds_recharge_total",
                        "className": "item-show-for-recharge",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(true)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','累充金额');
                                $(nTd).attr('data-key','funds_recharge_total').attr('data-value',data);
                                $(nTd).attr('data-column-name','累充金额');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "已结算",
                        "data": "funds_already_settled_total",
                        "className": "item-show-for-using",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(true)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','已结算');
                                $(nTd).attr('data-key','funds_already_settled_total').attr('data-value',data);
                                $(nTd).attr('data-column-name','已结算');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "余额",
                        "data": "funds_balance",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $balance = parseFloat(row.funds_recharge_total - row.funds_already_settled_total);
                            if($balance == 0) return "--";
                            return $balance;
                        }
                    },
                    {
                        "title": "消费金额",
                        "data": "funds_consumption_total",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "提示阈值",
                        "data": "funds_balance_prompt_threshold",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','提示阈值');
                                $(nTd).attr('data-key','funds_balance_prompt_threshold').attr('data-value',data);
                                $(nTd).attr('data-column-name','提示阈值');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "备注",
                        "data": "remark",
                        "className": "text-center",
                        "width": "",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1)
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
                        "className": "text-center",
                        "width": "80px",
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
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "更新时间",
                        "data": 'updated_at',
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

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();
    $(function () {
        TableDatatablesAjax.init();
    });
</script>