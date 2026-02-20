@extends(env('TEMPLATE_DK_SUPER').'layout.layout')


@section('head_title')
    {{ $title_text or '员工列表' }} - SUPER - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '员工列表' }} - SUPER - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/admin')}}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">全部用户</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/user/user-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加用户</button>
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

                        <input type="text" class="form-control form-filter item-search-keyup" name="staff-mobile" placeholder="登录工号" />
                        <input type="text" class="form-control form-filter item-search-keyup" name="staff-username" placeholder="用户名" />

                        <select class="form-control form-filter select2-box" name="staff-department-district" style="width:88px;">
                            <option value="-1">选择大区</option>
{{--                            @foreach($department_district_list as $v)--}}
{{--                                <option value="{{ $v->id }}">{{ $v->name }}</option>--}}
{{--                            @endforeach--}}
                        </select>


                        <select class="form-control form-filter" name="staff-user-type" style="width:88px;">
                            <option value="-1">全部人员</option>
                            <option value="-9">【管理岗】</option>
                            <option value="-11">【全部管理岗】</option>
                            <option value="-41">【团队管理岗】</option>
                            <option value="-8">【客服】</option>
                            <option value="-7">【质检】</option>
                            <option value="-6">【运营】</option>
                            <option value="11">总经理</option>
                            <option value="41">团队·总监</option>
                            <option value="88">客服</option>
                            <option value="84">客服主管</option>
                            <option value="81">客服经理</option>
                            <option value="71">质检经理</option>
                            <option value="77">质检员</option>
                            <option value="61">运营经理</option>
                            <option value="66">运营人员</option>
                            <option value="91">三方审核</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>


                <!-- datatable start -->
                <table class='table table-striped table-bordered table-hover' id='datatable_ajax'>
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
                        <h3 class="box-title">修改密码</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-change-password-modal">
                        <div class="box-body">

                            {{csrf_field()}}
                            <input type="hidden" name="operate" value="change-password" readonly>
                            <input type="hidden" name="id" value="0" readonly>

                            {{--类别--}}


                            {{--用户ID--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">新密码</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <input type="password" class="form-control form-filter" name="user-password" value="">
                                    6-20位英文、数值、下划线构成
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
                                <button type="button" class="btn btn-success" id="item-change-password-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-change-password-cancel">取消</button>
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
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50, 100, 200], ["50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/staff/staff--list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.mobile = $('input[name="staff-mobile"]').val();
                        d.username = $('input[name="staff-username"]').val();
                        d.department_district = $('select[name="staff-department-district"]').val();
                        d.user_type = $('select[name="staff-user-type"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
                    {
                        "title": '<input type="checkbox" class="check-review-all">',
                        "width": "60px",
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
                        "title": "状态",
                        "width": "80px",
                        "data": "item_status",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<i class="fa fa-times-circle text-black"></i> 已删除';
                            }

                            if(row.item_status == 1)
                            {
                                return '<i class="fa fa-circle-o text-green"></i> 正常';
                            }
                            else if(row.item_status == 99)
                            {
                                return '<i class="fa fa-lock text-orange"></i> 锁定';
                            }
                            else
                            {
                                return '<i class="fa fa-ban text-red"></i> 禁用';
                            }
                        }
                    },
                    {
                        "title": "登录工号",
                        "data": "login_number",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "姓名",
                        "data": "name",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+data+'</a>';
                            if(data) return data;
                            else return '--';
                        }
                    },
//                 {
//                     "title": "用户名",
//                     "data": "id",
//                     "className": "",
//                     "width": "100px",
//                     "orderable": false,
//                     render: function(data, type, row, meta) {
// //                            return '<a target="_blank" href="/user/'+data+'">'+row.nickname+'</a>';
//                         if(row.username)
//                         {
//                             if(row.user_type == 88)
//                             {
//                                 return '<a class="caller-control" data-id="'+row.id+'" data-title="'+data+'">'+data+' ('+row.id+')'+'</a>';
//                             }
//                             else return row.username;
//                         }
//                         else return '--';
//                     }
//                 },
                    {
                        "title": "员工类型",
                        "data": 'staff_category',
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs bg-black">Admin</small>';
                            else if(data == 1) return '<i class="fa fa-genderless text-black"></i> Admin';
                            else if(data == 9) return '<i class="fa fa-genderless text-red"></i> 总裁';
                            else if(data == 11) return '<i class="fa fa-genderless text-blue"></i> 人事';
                            else if(data == 21) return '<i class="fa fa-genderless text-blue"></i> 行政';
                            else if(data == 31) return '<i class="fa fa-genderless text-blue"></i> 财务';
                            else if(data == 41) return '<i class="fa fa-genderless text-blue"></i> 客服';
                            else if(data == 51) return '<i class="fa fa-genderless text-blue"></i> 质检';
                            else if(data == 61) return '<i class="fa fa-genderless text-blue"></i> 复核';
                            else if(data == 71) return '<i class="fa fa-genderless text-blue"></i> 运营';
                            else if(data == 81) return '<i class="fa fa-genderless text-blue"></i> 业务';
                            else if(data == 88) return '<i class="fa fa-genderless text-blue"></i> 销售商务';
                            else return "有误";
                        }
                    },
                    {
                        "title": "员工职位",
                        "data": 'staff_position',
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-black">BOSS</small>';
                            else if(data == 11) return '<i class="fa fa-gear text-red"></i> 公司老总';
                            else if(data == 31) return '<i class="fa fa-diamond text-red"></i> 部门总监';
                            else if(data == 41) return '<i class="fa fa-star text-red"></i> 团队经理';
                            else if(data == 51) return '<i class="fa fa-star-half-o text-red"></i> 分部主管';
                            else if(data == 61) return '<i class="fa fa-star-o text-red"></i> 小组组长';
                            else if(data == 71) return '<i class="fa fa-star-o text-red"></i> 小队队长';
                            else if(data == 88) return '<i class="fa fa-genderless text-red"></i> 业务员';
                            else if(data == 99) return '<i class="fa fa-genderless text-red"></i> 职员';
                            else return "有误";
                        }
                    },
                    {
                        "title": "公司",
                        "data": "company_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.company_er) {
                                return '<a href="javascript:void(0);" class="text-black">'+row.company_er.name+'</a>';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "部门",
                        "data": "department_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.department_er) {
                                return '<a href="javascript:void(0);" class="text-black">'+row.department_er.name+'</a>';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "团队",
                        "data": "team_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $return = '';
                            if(row.team_er)
                            {
                                var $team = row.team_er.name;
                                $return += $team;

                                if(row.team_sub_er)
                                {
                                    var $team_sub_name = row.team_sub_er.name;
                                    $return += ' - ' + $team_sub_name;
                                }

                                if(row.team_group_er)
                                {
                                    var $team_group_name = row.team_group_er.name;
                                    $return += ' - ' + $team_group_name;
                                }

                                return '<a href="javascript:void(0);" class="text-black">'+$return+'</a>';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "API坐席ID",
                        "data": "api_staffNo",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "创建人",
                        "data": "creator_id",
                        "className": "font-12px",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '未知';
                            if(row.creator) return '<a href="javascript:void(0);">'+row.creator.name+'</a>';
                            else return '--';
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": 'created_at',
                        "className": "font-12px",
                        "width": "160px",
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

                            // return $year+'-'+$month+'-'+$day;
                            // return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    },
                    {
                        "title": "操作",
                        "data": "id",
                        "width": "300px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_able = '';
                            var $html_delete = '';
                            var $html_promote = '';
                            var $html_login = '<a class="btn btn-xs staff--item-login--submit" data-id="'+data+'">登录</a>';
                            var $html_password_reset = '<a class="btn btn-xs staff--item-password-reset--submit" data-id="'+data+'">重置密码</a>';
                            var $html_operation_record = '<a class="btn btn-xs modal-show--for--staff--item-operation-record" data-id="'+data+'">记录</a>';

                            if(row.user_category == 1)
                            {
                                $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                            }
                            else
                            {
                                $html_edit = '<a class="btn btn-xs staff--item-edit-submit" data-id="'+data+'">编辑</a>';
                            }

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs staff--item-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs staff--item-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs staff--item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs staff--item-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            // if(row.user_type == 88)
                            // {
                            //     $html_promote = '<a class="btn btn-xs staff--item-promote-submit" data-id="'+data+'">晋升</a>';
                            // }
                            // else if(row.user_type == 84)
                            // {
                            //     $html_promote = '<a class="btn btn-xs staff--item-demote-submit" data-id="'+data+'">降职</a>';
                            // }
                            // else
                            // {
                            //     $html_promote = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">晋升</a>';
                            // }


                            var html =
                                '<a class="btn btn-xs modal-show--for--staff-item-edit" data-id="'+data+'">编辑</a>'+
                                $html_promote+
                                $html_password_reset+
                                $html_able+
                                $html_delete+
                                $html_operation_record+
                                $html_login+
                                // '<a class="btn btn-xs staff--item-statistic" data-id="'+data+'">统计</a>'+
                                // '<a class="btn btn-xs staff--item-login-submit" data-id="'+data+'">登录</a>'+
                                '';
                            return html;
                        }
                    },
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
@include(env('TEMPLATE_DK_SUPER').'entrance.staff.staff-list-script')
@endsection
