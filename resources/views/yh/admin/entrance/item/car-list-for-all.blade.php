@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '车辆列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')车辆列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">车辆列表</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/item/car-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加车辆</button>
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

                        <input type="text" class="form-control form-filter item-search-keyup" name="name" placeholder="车牌" />

                        {{--<select class="form-control form-filter" name="owner" style="width:96px;">--}}
                            {{--<option value ="-1">选择员工</option>--}}
                            {{--@foreach($sales as $v)--}}
                                {{--<option value ="{{ $v->id }}">{{ $v->true_name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}

                        <select class="form-control form-filter" name="finished" style="width:96px;">
                            <option value ="-1">全部</option>
                            <option value ="0">待完成</option>
                            <option value ="1">已完成</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped- table-bordered table-hover main-table' id='datatable_ajax'>
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
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value ="-1">请选择操作类型</option>
                                <option value ="启用">启用</option>
                                <option value ="禁用">禁用</option>
                                <option value ="删除">删除</option>
                                <option value ="彻底删除">彻底删除</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-style')
    <style>
        .tableArea .main-table {
            min-width: 3000px;
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
                "aLengthMenu": [[100, 200, -1], ["100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/car-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="name"]').val();
                        d.title = $('input[name="title"]').val();
                        d.keyword = $('input[name="keyword"]').val();
                        d.website = $('input[name="website"]').val();
                        d.owner = $('select[name="owner"]').val();
                        d.status = $('select[name="status"]').val();
                        d.finished = $('select[name="finished"]').val();
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
//                    {
//                        "width": "40px",
//                        "title": "选择",
//                        "data": "id",
//                        'orderable': false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "40px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        'orderable': false
//                    },
                    {
                        "className": "font-12px",
                        "width": "40px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "160px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.is_me == 1 && row.active == 0)
                            {
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                            }
                            else
                            {
                                $html_publish = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">发布</a>';
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
                                $html_able+
                                //                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                                '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                                //                                    $html_publish+
                                $html_delete+
                                //                                    '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                //                                    '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                //                                    '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "width": "60px",
                        "title": "类型",
                        "data": 'item_type',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-olive">车辆</small>';
                            else if(data == 21) return '<small class="btn-xs bg-purple">车挂</small>';
                            else return "有误";
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "100px",
                        "title": "车牌号",
                        "data": "name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "工作状态",
                        "data": 'car_status',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs bg-grey">待运</small>';
                            else if(data == 1) return '<small class="btn-xs bg-orange">工作中</small>';
                            else return "--";
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "类型",
                        "data": "trailer_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "尺寸",
                        "data": "trailer_length",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "容积",
                        "data": "trailer_volume",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "载重",
                        "data": "trailer_weight",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "轴数",
                        "data": "trailer_axis_count",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data;
                            else return "";
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "司机",
                        "data": "linkman_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "电话",
                        "data": "linkman_phone",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "车辆类型",
                        "data": "car_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "所有人",
                        "data": "car_owner",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "使用性质",
                        "data": "car_function",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "品牌",
                        "data": "car_brand",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "车辆识别代码",
                        "data": "car_identification_number",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "发动机号",
                        "data": "engine_number",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "车头轴距",
                        "data": "locomotive_wheelbase",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "主油箱",
                        "data": "main_fuel_tank",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "副油箱",
                        "data": "auxiliary_fuel_tank",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "总质量",
                        "data": "total_mass",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "整备质量",
                        "data": "curb_weight",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "核定载重质量",
                        "data": "load_weight",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "准牵引总质量",
                        "data": "traction_mass",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "外廓尺寸",
                        "data": "overall_size",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "购买日期",
                        "data": "purchase_date",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "注册日期",
                        "data": "registration_date",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "发证日期",
                        "data": "issue_date",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "检验有效期",
                        "data": "inspection_validity",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
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
                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    },
//                    {
//                        "className": "font-12px",
//                        "width": "96px",
//                        "title": "修改时间",
//                        "data": 'updated_at',
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
////                            return data;
//                            var $date = new Date(data*1000);
//                            var $year = $date.getFullYear();
//                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
//                            var $day = ('00'+($date.getDate())).slice(-2);
//                            var $hour = ('00'+$date.getHours()).slice(-2);
//                            var $minute = ('00'+$date.getMinutes()).slice(-2);
//                            var $second = ('00'+$date.getSeconds()).slice(-2);
////                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
////                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
//                        }
//                    },
                    {
                        "width": "60px",
                        "title": "状态",
                        "data": "item_status",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.item_status == 1)
                            {
                                return '<small class="btn-xs btn-success">启用</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs btn-danger">禁用</small>';
                            }
                        }
                    },
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

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
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.car-script')
@endsection
