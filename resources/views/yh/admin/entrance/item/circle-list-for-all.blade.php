@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '环线列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '环线列表' }} - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">{{ $title_text or '环线列表' }}</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/item/circle-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加环线</button>
                    </a>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-circle-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="circle-id" placeholder="ID" />
                        <input type="text" class="form-control form-filter filter-keyup" name="circle-name" placeholder="标题" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-circle">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-circle">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped- table-bordered table-hover' id='datatable_ajax'>
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
                <input type="hidden" name="attachment-set-operate" value="item-circle-attachment-set" readonly>
                <input type="hidden" name="attachment-set-order-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-circle-attachment-set" readonly>
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
                    <input type="hidden" name="info-text-set-operate" value="item-circle-info-text-set" readonly>
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
                    <input type="hidden" name="info-time-set-operate" value="item-circle-info-time-set" readonly>
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
                    <input type="hidden" name="info-radio-set-operate" value="item-circle-info-option-set" readonly>
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
                    <input type="hidden" name="info-select-set-operate" value="item-circle-info-option-set" readonly>
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




{{--分析--}}
<div class="modal fade modal-main-body" id="modal-body-for-analysis">
    <div class="col-md-10 col-md-offset-1 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">财务分析</h3>

                <div class="box-tools- pull-right _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right modal-show-for-finance-create">
                            <i class="fa fa-plus"></i> 添加记录
                        </button>
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="echart-overview" style="width:800px;height:400px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="echart-expenditure-rate" style="width:480px;height:400px;"></div>
                    </div>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-finance-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="finance-title" placeholder="费用类型" />

                        <select class="form-control form-filter" name="finance-finance_type" style="width:96px;">
                            <option value ="-1">选择</option>
                            <option value ="1">收入</option>
                            <option value ="21">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-finance">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                    <table class='table table-striped table-bordered' id='datatable_ajax_finance'>
                        <thead>
                        <tr role='row' class='heading'>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
                </div>

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
    .tableArea table { min-width:1380px; }
    .tableArea table#datatable_ajax_finance { min-width:1800px; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
</style>
@endsection




@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/resource/component/js/select2-4.0.5.min.js') }}"></script>
    {{--<script src="{{ asset('/resource/component/js/echarts-3.7.2.min.js') }}"></script>--}}
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
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
                    'url': "{{ url('/item/circle-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="circle-id"]').val();
                        d.name = $('input[name="circle-name"]').val();
                        d.title = $('input[name="circle-title"]').val();
                        d.keyword = $('input[name="circle-keyword"]').val();
                        d.status = $('select[name="circle-status"]').val();
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
                        "width": "100px",
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
                                '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
//                                $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
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
                        "className": "text-center",
                        "width": "120px",
                        "title": "标题",
                        "data": "title",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','标题');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','标题');
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
                        "className": "text-center",
                        "width": "100px",
                        "title": "车辆",
                        "data": "car_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车辆');
                                $(nTd).attr('data-key','car_id').attr('data-value',row.car_id);
                                if(row.car_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.car_er.name);
                                $(nTd).attr('data-column-name','车辆');
                                if(row.car_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return row.car_er == null ? '--' : '<a href="javascript:void(0);">'+row.car_er.name+'</a>';
                        }
                    },
                    {
                        "width": "200px",
                        "title": "订单",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','订单');
                                $(nTd).attr('data-key','remark').attr('data-value',data);
                                $(nTd).attr('data-column-name','备注');
                                $(nTd).attr('data-text-type','textarea');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $html = '';
                            $.each(data,function( key, val ) {
//                                console.log( key, val, this );
                                var $id = this.id;
                                var $title = (this.title) ? this.title : '';
                                var $departure  = (this.departure_place) ? this.departure_place : '';
                                var $destination  = (this.destination_place) ? this.destination_place : '';

                                var $date = new Date(this.assign_time*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $assign = $year+'-'+$month+'-'+$day;

                                var $text = $id + " " + $title + " (" + $departure + "-" + $destination + ") (" + $assign + ")";

                                $html += '<a href="javascript:void(0);" data-id="'+$id+'">'+$text+'</a><br>';
                            });
                            return $html;
//                            return row.people == null ? '未知' :
//                                '<a target="_blank" href="/people?id='+row.people.encode_id+'">'+row.people.name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "总里程",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attribute-list');
                                $(nTd).attr('data-id',row.id).attr('data-name','总里程');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','总里程');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $amount = 0;
                            $.each(data,function( key, val ) {
                                $amount += parseInt(this.travel_distance);
                            });
                            return $amount + ' km';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "收入",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attribute-list');
                                $(nTd).attr('data-id',row.id).attr('data-name','标题');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','标题');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $amount = 0;
                            $.each(data,function( key, val ) {
                                $amount += parseInt(this.amount) + parseInt(this.oil_card_amount) - parseInt(this.time_limitation_deduction);
                            });
                            return $amount;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "支出",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attribute-list');
                                $(nTd).attr('data-id',row.id).attr('data-name','标题');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','标题');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $amount = 0;
                            $.each(data,function( key, val ) {
                                $amount += parseInt(this.expenditure_total);
                            });
                            return $amount;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "利润",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attribute-list');
                                $(nTd).attr('data-id',row.id).attr('data-name','利润');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','利润');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $amount = 0;
                            $.each(data,function( key, val ) {
                                $amount += parseInt(this.amount) + parseInt(this.oil_card_amount) - parseInt(this.time_limitation_deduction) - parseInt(this.expenditure_total);
                            });
                            return $amount;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "利润率",
                        "data": "pivot_order_list",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-analysis');
                                $(nTd).attr('data-id',row.id).attr('data-name','标题');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','标题');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $amount = 0;
                            var $profit = 0;
                            $.each(data,function( key, val ) {
                                $amount += parseInt(this.amount) + parseInt(this.oil_card_amount) - parseInt(this.time_limitation_deduction);
                                $profit += parseInt(this.amount) + parseInt(this.oil_card_amount) - parseInt(this.time_limitation_deduction) - parseInt(this.expenditure_total);
                            });
                            return (($profit/$amount).toFixed(2)*100) + '%';
                        }
                    },
                    {
                        "width": "60px",
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
                            if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                            else return '';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
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
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
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
                                        _token: '{{ csrf_token() }}'
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


<script>
    var TableDatatablesAjax_finance = function ($id) {
        var datatableAjax_finance = function ($id,$type) {

            var dt_finance = $('#datatable_ajax_finance');
            dt_finance.DataTable().destroy();
            var ajax_datatable_finance = dt_finance.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/circle-finance-record?id="+$id+"&type="+$type,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="finance-name"]').val();
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="finance-keyword"]').val();
                        d.finance_type = $('select[name="finance-finance_type"]').val();
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
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "",
                        "width": "40px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "120px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $html_confirm = '';
                            var $html_delete = '';

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';

                                if(row.is_confirmed == 1)
                                {
                                    $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';

                                    if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id)
                                    {
                                        $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';
                                    }
                                    else $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
                                }
                                else
                                {
                                    $html_confirm = '<a class="btn btn-xs bg-green item-finance-confirm-submit" data-id="'+data+'">确认</a>';
                                }
                            }
                            else
                            {
                                $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';
                                $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
//                                $html_delete = '<a class="btn btn-xs bg-grey item-finance-restore-submit" data-id="'+data+'">恢复</a>';
                            }


                            var html =
                                $html_confirm+
                                $html_delete+
//                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "width": "60px",
                        "title": "状态",
                        "data": "is_confirmed",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.deleted_at == null)
                            {
                                if(data == 1) return '<small class="btn-xs btn-success">已确认</small>';
                                else return '<small class="btn-xs btn-danger">待确认</small>';
                            }
                            else return '<small class="btn-xs bg-black">已删除</small>';
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "类型",
                        "data": "finance_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.finance_type == 1) return '<small class="btn-xs bg-olive">收入</small>';
                            else if(row.finance_type == 21) return '<small class="btn-xs bg-orange">支出</small>';
                            else return '有误';
                        }
                    },
                    {
                        "width": "240px",
                        "title": "订单",
                        "data": "order_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $id = row.order_er.id;
                            var $title = (row.order_er.title) ? row.order_er.title : '';
                            var $departure  = (row.order_er.departure_place) ? row.order_er.departure_place : '';
                            var $destination  = (row.order_er.destination_place) ? row.order_er.destination_place : '';

                            var $date = new Date(row.order_er.assign_time*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $assign = $year+'-'+$month+'-'+$day;

                            var $text = $id + " " + $title + "&nbsp;&nbsp;[" + $departure + "-" + $destination + "]&nbsp;&nbsp;&nbsp;&nbsp;(" + $assign + ")";

                            $html = '<a href="javascript:void(0);" data-id="'+$id+'">'+$text+'</a><br>';

                            return $html;

                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "交易时间",
                        "data": "transaction_time",
                        "orderable": false,
                        render: function(data, type, row, meta) {
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
                            if($year == $currentYear) return $month+'-'+$day;
                            else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "确认者",
                        "data": "confirmer_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.confirmer == null ? '' : '<a href="javascript:void(0);">'+row.confirmer.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "确认时间",
                        "data": "confirmed_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '';

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
                        "className": "",
                        "width": "80px",
                        "title": "费用名目",
                        "data": "title",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "金额",
                        "data": "transaction_amount",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if((data > 0) && (data <= 10)) return '<samll class="text-red">'+data+'</samll>';
                            else return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "支付方式",
                        "data": "transaction_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "收款账户",
                        "data": "transaction_receipt_account",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "支出账户",
                        "data": "transaction_payment_account",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "className": "",
//                        "width": "120px",
//                        "title": "交易账户",
//                        "data": "transaction_account",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "交易单号",
                        "data": "transaction_order",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "200px",
                        "title": "备注",
                        "data": "description",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "操作时间",
                        "data": "created_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
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
//                    {
//                        "width": "120px",
//                        "title": "操作",
//                        'data': 'id',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
////                            var $date = row.transaction_date.trim().split(" ")[0];
//                            var html = '';
////                                '<a class="btn btn-xs item-enable-submit" data-id="'+value+'">启用</a>'+
////                                '<a class="btn btn-xs item-disable-submit" data-id="'+value+'">禁用</a>'+
////                                '<a class="btn btn-xs item-download-qrcode-submit" data-id="'+value+'">下载二维码</a>'+
////                                '<a class="btn btn-xs item-statistics-submit" data-id="'+value+'">流量统计</a>'+
////                                    '<a class="btn btn-xs" href="/item/edit?id='+value+'">编辑</a>'+
////                                '<a class="btn btn-xs item-edit-submit" data-id="'+value+'">编辑</a>'+
//
//                            return html;
//                        }
//                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    ajax_datatable_finance.$('.tooltips').tooltip({placement: 'top', html: true});
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


            dt_finance.on('click', '.finance-filter-submit', function () {
                ajax_datatable_finance.ajax.reload();
            });

            dt_finance.on('click', '.finance-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt_finance).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_finance.ajax.reload();
            });


//            dt_finance.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_finance
        }
    }();
    //    $(function () {
    //        TableDatatablesAjax_finance.init();
    //    });
</script>
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.circle-script')
@endsection
