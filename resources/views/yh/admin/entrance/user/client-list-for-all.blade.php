@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '全部客户' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')全部客户 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/admin') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">{{ $title_text or '客户列表' }}</h3>

                @if(in_array($me->user_type,[0,1,9,11]))
                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/user/client-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加客户</button>
                    </a>
                </div>
                @endif

            </div>

            <div class="box-body datatable-body item-main-body" id="item-main-body">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="username" placeholder="用户名" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>


                <div class="tableArea">
                <table class='table table-striped table-bordered- table-hover main-table' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>

            </div>

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endsection




@section('custom-style')
    <style>
        .tableArea .main-table {
            min-width: 1000px;
        }
    </style>
@endsection




@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50, 100, 200], ["50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/user/client-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.username = $('input[name="username"]').val();
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
                    {
                        "className": "font-12px",
                        "width": "50px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "操作",
                        "data": "id",
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


                            if(row.user_status == 1)
                            {
                                $html_able =
                                    '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.user_category == 1)
                            {
                                $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                            }
                            else
                            {
                                $html_edit = '<a class="btn btn-xs btn-primary item-admin-edit-submit" data-id="'+data+'">编辑</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            var html =
                                $html_edit+
                                $html_able+
//                                $html_delete+
//                                '<a class="btn btn-xs bg-olive item-login-submit" data-id="'+data+'">登录</a>'+
//                                '<a class="btn btn-xs bg-purple item-statistic-link" data-id="'+data+'">统计</a>'+
                                '';
                            return html;
                        }
                    },
                    {
                        "width": "60px",
                        "title": "状态",
                        "data": "active",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.user_status == 1)
                            {
                                return '<small class="btn-xs btn-success">正常</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs btn-danger">禁用</small>';
                            }
                        }
                    },
//                    {
//                        "className": "font-12px",
//                        "width": "80px",
//                        "title": "用户类型",
//                        "data": 'user_category',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            if(data == 0) return 'admin';
//                            else if(data == 1) return '<small class="btn-xs bg-primary">个人用户</small>';
//                            else if(data == 9) return '<small class="btn-xs bg-olive">轻博</small>';
//                            else if(data == 11)  // 员工
//                            {
//                                if(row.user_type == 0) return '<small class="btn-xs bg-purple">管理员</small>';
//                                else if(row.user_type == 11) return '<small class="btn-xs bg-purple">总经理</small>';
//                                else if(row.user_type == 21) return '<small class="btn-xs bg-purple">HR经理</small>';
//                                else if(row.user_type == 22) return '<small class="btn-xs bg-purple">HR</small>';
//                                else if(row.user_type == 41) return '<small class="btn-xs bg-purple">销售主管</small>';
//                                else if(row.user_type == 61) return '<small class="btn-xs bg-purple">销售组长</small>';
//                                else if(row.user_type == 88) return '<small class="btn-xs bg-purple">销售员</small>';
//                                else return '<small class="btn-xs bg-purple">组织</small>';
//                            }
//                            else if(data == 88) return '<small class="btn-xs bg-purple">赞助商</small>';
//                            else return "有误";
//                        }
//                    },
                    {
                        "className": "text-left",
                        "width": "200px",
                        "title": "客户名称",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+row.username+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "简称",
                        "data": "short_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "所属公司",
                        "data": "company",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "手机",
                        "data": "mobile",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "data": 'id',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return row.menu == null ? '未分类' : row.menu.title;
////                            var html = '';
////                            $.each(data,function( key, val ) {
////                                html += '<a href="/org-admin/item/menu?id='+this.id+'">'+this.title+'</a><br>';
////                            });
////                            return html;
//                        }
//                    },
                    {
                        "className": "text-center",
                        "width": "",
                        "title": "备注",
                        "data": "remark",
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
                            return row.creator.true_name;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "创建时间",
                        "data": 'created_at',
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
@include(env('TEMPLATE_YH_ADMIN').'entrance.user.client-script')
@endsection
