@extends(env('TEMPLATE_YH_SUPER').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    S.{{ $title_text or '记录列表' }} - 超级管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '记录列表' }} - 超级管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">{{ $title_text or '记录列表' }}</h3>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-record-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="record-id" placeholder="ID" />
                        <input type="text" class="form-control form-filter filter-keyup" name="record-name" placeholder="标题" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-record">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-record">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea overflow-none-">
                <table class='table table-striped table-bordered- table-hover' id='datatable_ajax'>
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




{{--显示-附件-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-attachment">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">定价【<span class="attachment-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>



            {{--attachment--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="">
            <div class="box-body attachment-box">

            </div>
            </form>


            <div class="box-header with-border margin-top-16px margin-bottom-16px-">
                <h4 class="box-title">【添加附件】</h4>
            </div>

            {{--上传附件--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="modal-attachment-set-form">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="attachment-set-operate" value="item-pricing-attachment-set" readonly>
                <input type="hidden" name="attachment-set-order-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-pricing-attachment-set" readonly>
                <input type="hidden" name="order_id" value="0" readonly>
                <input type="hidden" name="operate_type" value="add" readonly>
                <input type="hidden" name="column_key" value="attachment" readonly>


                <div class="form-group">
                    <label class="control-label col-md-2">附件名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="attachment_name" autocomplete="off" placeholder="附件名称" value="">
                    </div>
                </div>

                <div class="form-group">

                    <label class="control-label col-md-2" style="clear:left;">选择图片</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                            <span class="btn-file">
                                <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                <input type="file" name="attachment_file" />
                            </span>
                                <span class="">
                                <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                            </span>
                            </div>
                        </div>
                        <div id="titleImageError" style="color: #a94442"></div>

                    </div>

                </div>

            </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-attachment-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-attachment-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--修改-基本-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改定价【<span class="info-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-text-set-operate" value="item-pricing-info-text-set" readonly>
                    <input type="hidden" name="info-text-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-text-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="info-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="info-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-时间-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改定价【<span class="info-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-time-set-operate" value="item-pricing-info-time-set" readonly>
                    <input type="hidden" name="info-time-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-time-set-column-key" value="" readonly>
                    <input type="hidden" name="info-time-set-time-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime">
                            <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date">
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-radio-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-radio-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改定价【<span class="info-radio-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-radio-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-radio-set-operate" value="item-pricing-info-option-set" readonly>
                    <input type="hidden" name="info-radio-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-radio-set-operate-type" value="edit" readonly>
                    <input type="hidden" name="info-radio-set-column-key" value="" readonly>


                    <div class="form-group radio-box">
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-radio-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-radio-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-select-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-select-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改定价【<span class="info-select-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-select-set-operate" value="item-pricing-info-option-set" readonly>
                    <input type="hidden" name="info-select-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-select-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-select-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-select-set-column-name"></label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-client" name="info-select-set-column-value" style="width:240px;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-select-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-select-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--修改列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-modify-list">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-modify-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value ="-1">选择属性</option>
                            <option value ="amount">金额</option>
                            <option value ="11">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable_ajax_record'>
                    <thead>
                        <tr role='row' class='heading'>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection




@section('custom-css')
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
<style>
    .tableArea table { min-width:800px; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
</style>
@endsection




@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[100, 200, 500], ["100", "200", "500"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/record-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="record-id"]').val();
                        d.name = $('input[name="record-name"]').val();
                        d.title = $('input[name="record-title"]').val();
                        d.keyword = $('input[name="record-keyword"]').val();
                        d.status = $('select[name="record-status"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value',row.attachment_list);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "对象",
                        "data": "operate_object",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','对象');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','对象');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 11) return '<small class="btn-xs bg-blue">管理员</small>';
                            else if(data == 21) return '<small class="btn-xs bg-blue">员工</small>';
                            else if(data == 25) return '<small class="btn-xs bg-blue">驾驶员</small>';
                            else if(data == 31) return '<small class="btn-xs bg-green">客户</small>';
                            else if(data == 41) return '<small class="btn-xs bg-green">车辆</small>';
                            else if(data == 51) return '<small class="btn-xs bg-green">线路</small>';
                            else if(data == 61) return '<small class="btn-xs bg-green">包油油耗</small>';
                            else if(data == 71) return '<small class="btn-xs bg-yellow">订单</small>';
                            else if(data == 77) return '<small class="btn-xs bg-yellow"><i class="fa fa-refresh"></i> 环线</small>';
                            else if(data == 88) return '<small class="btn-xs bg-red">财务</small>';
                            else return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "操作",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
                            else if(data == 99) return '<small class="btn-xs bg-olive">确认</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else if(data == 109) return '<small class="btn-xs bg-fuchsia">导出</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "实例",
                        "data": "item_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        },
                        render: function(data, type, row, meta) {
                            if(row.operate_object == 71)
                            {
                                var $domain = "{{ env('DOMAIN_YH_ADMIN') }}";
                                return '<a target="_blank" href="'+ $domain +'/item/order-list-for-all?order_id='+ row.order_id +'" data-id="'+ row.order_id +'">'+ row.order_id +'</a><br>';
                            }
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.operate_object == 11)
                            {

                            }
                            else if(row.operate_object == 21)
                            {

                            }
                            else if(row.operate_object == 25)
                            {
                                if(row.operate_category == 1 || row.operate_category == 72)
                                {
                                    if(data == "name") return '名称';

                                    else if(data == "driver_name") return '主驾姓名';
                                    else if(data == "driver_phone") return '主驾电话';
                                    else if(data == "driver_title") return '主驾职称';
                                    else if(data == "driver_entry_time") return '主驾入职时间';
                                    else if(data == "driver_leave_time") return '主驾离职时间';
                                    else if(data == "driver_ID") return '主驾身份证号';
                                    else if(data == "driver_ID_front") return '主驾身份证正页';
                                    else if(data == "driver_ID_back") return '主驾身份证副页';
                                    else if(data == "driver_licence") return '主驾驾驶证';
                                    else if(data == "driver_certification") return '主驾资格证';
                                    else if(data == "emergency_contact_name") return '主驾紧急联系人';
                                    else if(data == "emergency_contact_phone") return '主驾紧急联系电话';
                                    else if(data == "job_performance") return '主驾工作表现';

                                    else if(data == "sub_driver_name") return '副驾姓名';
                                    else if(data == "sub_driver_phone") return '副驾电话';
                                    else if(data == "sub_driver_title") return '副驾职称';
                                    else if(data == "sub_driver_entry_time") return '副驾入职时间';
                                    else if(data == "sub_driver_leave_time") return '副驾离职时间';
                                    else if(data == "sub_driver_ID") return '副驾身份证号';
                                    else if(data == "sub_driver_ID_front") return '副驾身份证正页';
                                    else if(data == "sub_driver_ID_back") return '副驾身份证副页';
                                    else if(data == "sub_driver_licence") return '副驾驾驶证';
                                    else if(data == "sub_driver_certification") return '副驾资格证';
                                    else if(data == "sub_contact_name") return '副驾紧急联系人';
                                    else if(data == "sub_contact_phone") return '副驾紧急联系电话';
                                    else if(data == "sub_job_performance") return '副驾副驾工作表现';

                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else if(row.operate_object == 31)
                            {

                            }
                            else if(row.operate_object == 41)
                            {
                                if(row.operate_category == 1)
                                {
                                    if(data == "name") return '车牌号';
                                    else if(data == "driver_id") return '驾驶员';
                                    else if(data == "trailer_type") return '类型';
                                    else if(data == "trailer_length") return '尺寸';
                                    else if(data == "trailer_volume") return '容积';
                                    else if(data == "trailer_weight") return '载重';
                                    else if(data == "trailer_axis_count") return '轴数';
                                    else if(data == "linkman_name") return '司机';
                                    else if(data == "linkman_phone") return '电话';
                                    else if(data == "car_type") return '车辆类型';
                                    else if(data == "car_owner") return '所有人';
                                    else if(data == "car_function") return '使用性质';
                                    else if(data == "car_brand") return '品牌';
                                    else if(data == "car_identification_number") return '车辆识别代码';
                                    else if(data == "engine_number") return '发动机号';
                                    else if(data == "locomotive_wheelbase") return '车头轴距';
                                    else if(data == "main_fuel_tank") return '主油箱';
                                    else if(data == "auxiliary_fuel_tank") return '副油箱';
                                    else if(data == "total_mass") return '总质量';
                                    else if(data == "curb_weight") return '整备质量';
                                    else if(data == "load_weight") return '核定载重';
                                    else if(data == "traction_mass") return '准牵引质量';
                                    else if(data == "overall_size") return '外廓尺寸';
                                    else if(data == "purchase_date") return '购买日期';
                                    else if(data == "purchase_price") return '购买价格';
                                    else if(data == "sale_date") return '出售日期';
                                    else if(data == "sale_price") return '出售价格';
                                    else if(data == "registration_date") return '注册日期';
                                    else if(data == "issue_date") return '发证日期';
                                    else if(data == "inspection_validity") return '检验有效期';
                                    else if(data == "transportation_license_validity") return '运输证-年检';
                                    else if(data == "transportation_license_change_time") return '运输证-换证';
                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else if(row.operate_object == 51)
                            {
                                if(row.operate_category == 1)
                                {
                                    if(data == "title") return '标题';
                                    else if(data == "amount_with_cash") return '运价(现金)';
                                    else if(data == "amount_with_invoice") return '运价(带票)';
                                    else if(data == "travel_distance") return '里程';
                                    else if(data == "time_limitation_prescribed") return '时效';
                                    else if(data == "departure_place") return '出发地';
                                    else if(data == "stopover_place") return '经停地';
                                    else if(data == "destination_place") return '目的地';
                                    else if(data == "contract_start_date") return '合同开始日期';
                                    else if(data == "contract_ended_date") return '合同结束日期';
                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else if(row.operate_object == 61)
                            {
                                if(row.operate_category == 1)
                                {
                                    if(data == "title") return '标题';
                                    else if(data == "price1") return '包油（升）';
                                    else if(data == "price2") return '空放（升）';
                                    else if(data == "price3") return '空放-200+（升）';
                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else if(row.operate_object == 71)
                            {
                                if(row.operate_category == 1)
                                {
                                    if(data == "client_id") return '客户';
                                    else if(data == "route_id") return '固定线路';
                                    else if(data == "circle_id") return '环线';
                                    else if(data == "pricing_id") return '包油价';
                                    else if(data == "car_id") return '车辆';
                                    else if(data == "trailer_id") return '车挂';
                                    else if(data == "outside_car") return '车辆';
                                    else if(data == "outside_trailer") return '车挂';
                                    else if(data == "driver_id") return '驾驶员';
                                    else if(data == "travel_distance") return '里程数';
                                    else if(data == "time_limitation_prescribed") return '时效';
                                    else if(data == "amount") return '金额';
                                    else if(data == "deposit") return '定金';
                                    else if(data == "oil_card_amount") return '油卡';
                                    else if(data == "outside_car_price") return '请车价';
                                    else if(data == "invoice_amount") return '开票金额';
                                    else if(data == "invoice_point") return '票点';
                                    else if(data == "customer_management_fee") return '客户管理费';
                                    else if(data == "information_fee") return '信息费';
                                    else if(data == "time_limitation_deduction") return '时效扣款';
                                    else if(data == "administrative_fee") return '管理费';
                                    else if(data == "ETC_price") return 'ETC费用';
                                    else if(data == "oil_amount") return '万金油量（升）';
                                    else if(data == "oil_unit_price") return '油价（元）';
                                    else if(data == "assign_time") return '安排时间';
                                    else if(data == "container_type") return '箱型';
                                    else if(data == "subordinate_company") return '所属公司';
                                    else if(data == "route") return '路线';
                                    else if(data == "route_fixed") return '固定路线';
                                    else if(data == "route_temporary") return '临时路线';
                                    else if(data == "_route") return '固定路线';
                                    else if(data == "temporary_route") return '临时路线';
                                    else if(data == "departure_place") return '出发地';
                                    else if(data == "destination_place") return '目的地';
                                    else if(data == "stopover_place") return '经停点';
                                    else if(data == "should_departure_time") return '应出发时间';
                                    else if(data == "should_arrival_time") return '应到达时间';
                                    else if(data == "actual_departure_time") return '实际出发时间';
                                    else if(data == "actual_arrival_time") return '实际到达时间';
                                    else if(data == "stopover_departure_time") return '实际出发时间';
                                    else if(data == "stopover_arrival_time") return '实际到达时间';
                                    else if(data == "driver_name") return '主驾姓名';
                                    else if(data == "driver_phone") return '主驾电话';
                                    else if(data == "copilot_name") return '副驾姓名';
                                    else if(data == "copilot_phone") return '副驾电话';
                                    else if(data == "trailer_type") return '车挂类型';
                                    else if(data == "trailer_length") return '车挂尺寸';
                                    else if(data == "trailer_volume") return '车挂容积';
                                    else if(data == "trailer_weight") return '车辆载重';
                                    else if(data == "trailer_axis_count") return '轴数';
                                    else if(data == "GPS") return 'GPS';
                                    else if(data == "receipt_address") return '回单地址';
                                    else if(data == "receipt_status") return '回单状态';
                                    else if(data == "is_delay") return '是否压车';
                                    else if(data == "order_number") return '单号';
                                    else if(data == "payee_name") return '收款人';
                                    else if(data == "arrange_people") return '安排人';
                                    else if(data == "car_managerial_people") return '车辆负责员';
                                    else if(data == "car_supply") return '车货源';
                                    else if(data == "weight") return '重量';
                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else if(row.operate_object == 77)
                            {
                                if(data == "title") return '标题';
                                else if(data == "car_id") return '车辆';
                                else if(data == "start_time") return '开始时间';
                                else if(data == "ended_time") return '结束时间';
                                else if(data == "remark") return '备注';
                                else return '有误';
                            }
                            else if(row.operate_object == 88)
                            {
                                if(row.operate_category == 1)
                                {
                                    if(data == "title") return '名目';
                                    else if(data == "transaction_type") return '支付方式';
                                    else if(data == "transaction_receipt_account") return '收款账户';
                                    else if(data == "transaction_payment_account") return '支出账户';
                                    else if(data == "transaction_order") return '交易单号';
                                    else if(data == "transaction_time") return '交易日期';
                                    else if(data == "remark") return '备注';
                                    else return '有误';
                                }
                                else if(row.operate_category == 71)
                                {
                                    return '';

                                    if(row.operate_type == 1) return '添加';
                                    else if(row.operate_type == 91) return '删除';

                                    if(data == "attachment") return '附件';
                                }
                                else return '';
                            }
                            else return '';


                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_name == 'is_delay')
                            {
                                if(data == 1) return '正常';
                                else if(data == 9) return '压车';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                // if(data == 0) return '';
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_name == 'is_delay')
                            {
                                if(data == 1) return '正常';
                                else if(data == 9) return '压车';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "IP",
                        "data": "ip",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
                        "title": "操作时间",
                        "data": "created_at",
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

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
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


<script>
    var TableDatatablesAjax_record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_record = $('#datatable_ajax_record');
            dt_record.DataTable().destroy();
            var ajax_datatable_record = dt_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/pricing-modify-record?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
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
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "50px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "类型",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_category == 1)
                            {
                                if(data == "title") return '标题';
                                else if(data == "price1") return '包油（升）';
                                else if(data == "price2") return '空放（升）';
                                else if(data == "price3") return '空放-200+（升）';
                                else if(data == "remark") return '备注';
                                else return '有误';
                            }
                            else if(row.operate_category == 71)
                            {
                                return '';

                                if(row.operate_type == 1) return '添加';
                                else if(row.operate_type == 91) return '删除';

                                if(data == "attachment") return '附件';
                            }
                            else return '';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.before_client_er == null) return '';
                                else
                                {
                                    if(row.before_client_er.short_name != null)
                                    {
                                        return '<a href="javascript:void(0);">'+row.before_client_er.short_name+'</a>';
                                    }
                                    else return '<a href="javascript:void(0);">'+row.before_client_er.username+'</a>';
                                }
                            }
                            else if(row.column_name == 'route_id')
                            {
                                if(row.before_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'pricing_id')
                            {
                                if(row.before_pricing_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_pricing_er.title+'</a>';
                            }
                            else if(row.column_name == 'car_id' || row.column_name == 'trailer_id')
                            {
                                if(row.before_car_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_car_er.name+'</a>';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                var $currentYear = new Date().getFullYear();
                                if($year == $currentYear)
                                {
                                    if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $month+'-'+$day;
                                }
                                else
                                {
                                    if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                }
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
                        "title": "操作时间",
                        "data": "created_at",
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

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                    ajax_datatable_record.$('.tooltips').tooltip({placement: 'top', html: true});
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

//                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
//                        checkboxClass: 'icheckbox_minimal-blue',
//                        radioClass   : 'iradio_minimal-blue'
//                    });
                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_record.on('click', '.modify-filter-submit', function () {
                ajax_datatable_record.ajax.reload();
            });

            dt_record.on('click', '.modify-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_record.ajax.reload();
            });


//            dt_record.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_record
        }
    }();
    //        $(function () {
    //            TableDatatablesAjax_record.init();
    //        });
</script>
@include(env('TEMPLATE_YH_SUPER').'entrance.item.record-script')
@endsection
