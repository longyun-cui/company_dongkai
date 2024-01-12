@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '员工列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')员工列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">员工列表</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/user/staff-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加员工</button>
                    </a>
                </div>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
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


                <!-- datatable start -->
                <table class='table table-striped table-bordered- table-hover' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->


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


<div class="modal fade" id="modal-password-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">修改【<span class="user-name"></span>】的密码</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-password-admin-change-modal">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="operate" value="staff-password-admin-change" readonly>
                            <input type="hidden" name="user_id" value="0" readonly>

                            {{--类别--}}


                            {{--用户ID--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">新密码</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <input type="password" class="form-control form-filter" name="user-password" value="">
                                    6-20位英文、数字、下划线构成
                                </div>
                            </div>
                            {{--用户名--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">确认密码</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <input type="password" class="form-control form-filter" name="user-password-confirm" value="">
                                </div>
                            </div>


                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="btn btn-success" id="item-password-admin-change-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-password-admin-change-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>
@endsection




@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
               // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
               // "aLengthMenu": [[-1], ["全部"]],
               // "aLengthMenu": [[200, -1], ["200", "全部"]],
                "aLengthMenu": [[50, 100, 200, -1], ["50", "100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/user/staff-list-for-all') }}",
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
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
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
                                // '<a class="btn btn-xs bg-maroon item-password-admin-change-show" data-id="'+data+'" data-name="'+row.username+'">修改密码</a>'+
                                '<a class="btn btn-xs bg-maroon item-password-admin-reset-submit" data-id="'+data+'">重置密码</a>'+
                                $html_able+
                                $html_delete+
//                                '<a class="btn btn-xs bg-olive item-login-submit" data-id="'+data+'">登录</a>'+
//                                '<a class="btn btn-xs bg-purple item-statistic-link" data-id="'+data+'">统计</a>'+
                                '';
                            return html;
                        }
                    },
                    {
                        "title": "状态",
                        "width": "80px",
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
                    {
                        "title": "员工职位",
                        "data": 'user_type',
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-black">BOSS</small>';
                            else if(data == 11) return '<small class="btn-xs btn-danger">总经理</small>';
                            else if(data == 21) return '<small class="btn-xs bg-purple">人事经理</small>';
                            else if(data == 22) return '<small class="btn-xs bg-purple">人事</small>';
                            else if(data == 41) return '<small class="btn-xs bg-orange">财务经理</small>';
                            else if(data == 42) return '<small class="btn-xs bg-orange">财务</small>';
                            else if(data == 71) return '<small class="btn-xs bg-purple">质检</small><small class="btn-xs btn-danger">经理</small>';
                            else if(data == 77) return '<small class="btn-xs bg-purple">质检员</small>';
                            else if(data == 81) return '<small class="btn-xs bg-olive">客服</small><small class="btn-xs btn-danger">经理</small>';
                            else if(data == 84) return '<small class="btn-xs bg-olive">客服</small><small class="btn-xs bg-olive">主管</small>';
                            else if(data == 88) return '<small class="btn-xs bg-olive">客服</small>';
                            else return "有误";
                        }
                    },
                    {
                        "title": "登录工号",
                        "data": "mobile",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "姓名",
                        "data": "id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+row.true_name+'</a>';
                            if(row.true_name) return row.true_name;
                            else return '--';
                        }
                    },
                    {
                        "title": "用户名",
                        "data": "id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+row.nickname+'</a>';
                            if(row.username) return row.username;
                            else return '--';
                        }
                    },
                    {
                        "title": "大区",
                        "data": "department_district_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.department_district_er) {
                                return '<a href="javascript:void(0);">'+row.department_district_er.name+'</a>';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "小组",
                        "data": "department_group_id",
                        "className": "",
                        "width":"80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.department_group_er) {
                                return '<a href="javascript:void(0);">'+row.department_group_er.name+'</a>';
                            }
                            else return '--';
                        }
                    },
                    {
                       "title": "上级领导",
                       "data": "id",
                       "className": "",
                       "width":"100px",
                       "orderable": false,
                       render: function(data, type, row, meta) {
                           if(row.superior) {
                               return '<a href="javascript:void(0);">'+row.superior.true_name+'</a>';
                           }
                           else return '--';
                       }
                    },
                    {
                        "title": "备注",
                        "data": "remark",
                        "className": "text-center",
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
                        "title": "创建人",
                        "data": "creator_id",
                        "className": "font-12px",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '未知';
                            return row.creator.true_name;
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
                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
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
@include(env('TEMPLATE_YH_ADMIN').'entrance.user.staff-script')
@endsection
