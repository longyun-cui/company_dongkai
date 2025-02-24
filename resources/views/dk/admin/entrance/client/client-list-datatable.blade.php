<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
                "aLengthMenu": [[10, 50, 200, 500, -1], ["10", "50", "200", "500", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/user/client-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="client-id"]').val();
                        d.name = $('input[name="client-name"]').val();
                        d.username = $('input[name="client-username"]').val();
                        d.title = $('input[name="client-title"]').val();
                        d.keyword = $('input[name="client-keyword"]').val();
                        d.item_status = $('select[name="client-status"]').val();
                        d.client_type = $('select[name="client-type"]').val();
                        d.client_work_status = $('select[name="client-work-status"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
                "order": [],
                "orderCellsTop": true,
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
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "font-12px",
                        "width": "60px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "操作",
                        "data": "id",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html =
                                '<div class="btn-group">'+
                                '<button type="button" class="btn btn-sm btn-warning btn-flat">Action</button>'+
                                '<button type="button" class="btn btn-sm btn-warning btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="true">'+
                                '<span class="caret"></span>'+
                                '<span class="sr-only">Toggle Dropdown</span>'+
                                '</button>'+
                                '<ul class="dropdown-menu" role="menu">'+
                                '<li><a href="#">Action</a></li>'+
                                '<li><a href="#">Another action</a></li>'+
                                '<li><a href="#">Something else here</a></li>'+
                                '<li class="divider"></li>'+
                                '<li><a href="#">Separated link</a></li>'+
                                '</ul>'+
                                '</div>';

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_abandon = '';
                            var $html_completed = '';
                            var $html_verified = '';
                            var $html_inspected = '';
                            var $html_detail_inspected = '';
                            var $html_push = '';
                            var $html_deliver = '';
                            var $html_distribute = '';
                            var $html_recharge = '';
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

                            if(row.user_category == 1)
                            {
                                $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                            }
                            else
                            {
                                $html_edit = '<a class="btn btn-xs btn-primary- item-admin-edit-submit" data-id="'+data+'">编辑</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black- item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey- item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            @if(in_array($me->user_type,[0,1,11,19]))
                                $html_record = '<a class="btn btn-xs bg-purple- item-modal-show-for-modify" data-id="'+data+'">记录</a>';
                                $html_recharge = '<a class="btn btn-xs bg-orange- item-modal-show-for-recharge" data-id="'+data+'">充值</a>';
                                    @endif

                            var html =
                                $html_edit+
                                '<a class="btn btn-xs bg-maroon- item-password-admin-reset-submit" data-id="'+data+'">重置密码</a>'+
                                $html_able+
                                $html_recharge+
                                $html_record+
                                $html_login+
                                // $html_delete+
                                // '<a class="btn btn-xs bg-olive item-login-submit" data-id="'+data+'">登录</a>'+
                                // '<a class="btn btn-xs bg-purple item-statistic-link" data-id="'+data+'">统计</a>'+
                                '';
                            return html;
                        }
                    },
                    {
                        "title": "状态",
                        "data": "active",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.user_status == 1)
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
                        "title": "客户名称",
                        "data": "username",
                        "className": "client-name",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "所属公司",
                        "data": "company_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set-');
                                $(nTd).attr('data-id',row.id).attr('data-name','所属公司');
                                $(nTd).attr('data-key','company_id').attr('data-value',data);
                                $(nTd).attr('data-column-name','所属公司');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.company_er) return '<a href="javascript:void(0);">'+row.company_er.name+'</a>';
                            else return '--';
                        }
                    },
                    {
                        "title": "所属渠道",
                        "data": "channel_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set-');
                                $(nTd).attr('data-id',row.id).attr('data-name','对接渠道');
                                $(nTd).attr('data-key','channel_id').attr('data-value',data);
                                $(nTd).attr('data-column-name','对接渠道');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.channel_er) return '<a href="javascript:void(0);">'+row.channel_er.name+'</a>';
                            else return '--';
                        }
                    },
                    {
                        "title": "对接商务",
                        "data": "business_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set-');
                                $(nTd).attr('data-id',row.id).attr('data-name','对接商务');
                                $(nTd).attr('data-key','business_id').attr('data-value',data);
                                $(nTd).attr('data-column-name','对接商务');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.business_er) return '<a href="javascript:void(0);">'+row.business_er.name+'</a>';
                            else return '--';
                        }
                    },
                    @if(in_array($me->user_type,[0,1,11,19]))
                    {
                        "title": "合作单价",
                        "data": "cooperative_unit_price",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','合作单价');
                                $(nTd).attr('data-key','cooperative_unit_price').attr('data-value',data);
                                $(nTd).attr('data-column-name','合作单价');
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
                        "title": "累计充值",
                        "data": "funds_recharge_total",
                        "className": "item-show-for-recharge",
                        "width": "80px",
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
                            return parseFloat(data);
                        }
                    },
                    @endif
                    {
                        "title": "管理员名称",
                        "data": "client_admin_name",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "管理员登录手机",
                        "data": "client_admin_mobile",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "启用ip",
                        "data": "is_ip",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs btn-danger">否</small>';
                            else if(data == 1) return '<small class="btn-xs btn-success">是</small>';
                            return "--";
                        }
                    },
                    {
                        "title": "ip白名单",
                        "data": "ip_whitelist",
                        "className": "",
                        "width": "200px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','ip白名单');
                                $(nTd).attr('data-key','ip_whitelist').attr('data-value',data);
                                $(nTd).attr('data-column-name','ip白名单');
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
                        "title": "备注",
                        "data": "remark",
                        "className": "text-left",
                        "width": "",
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
                        "className": "font-12px",
                        "width": "80px",
                        "title": "创建人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '未知';
                            // return row.creator.true_name;
                            return '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": 'created_at',
                        "className": "font-12px",
                        "width": "120px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
//                            return data;
//                            newDate = new Date();
//                            newDate.setTime(data * 1000);
//                            return newDate.toLocaleString('chinese',{hour12:false});
//                            return newDate.toLocaleDateString();

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

                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    }
                ],
                "drawCallback": function (settings) {
                    ajax_datatable.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{csrf_token()}}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
                    });
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