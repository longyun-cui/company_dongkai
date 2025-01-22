@extends(env('TEMPLATE_DK_CLIENT').'layout.layout')


@section('head_title')
    {{ $title_text or '工单列表' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')工单列表 - 客户系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">

            <div class="box-header with-border _none" style="padding:6px 10px;margin:4px;">

                <h3 class="box-title">工单列表</h3>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-delivery-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" value="{{ $order_id or '' }}" style="width:88px;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="交付日期" value="{{ $assign or '' }}" readonly="readonly" style="width:80px;text-align:center;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                            <i class="fa fa-chevron-right"></i>
                        </button>

                        <input type="text" class="form-control form-filter filter-keyup" name="order-client-name" placeholder="客户姓名" value="{{ $client_name or '' }}" style="width:100px;" />
                        <input type="text" class="form-control form-filter filter-keyup" name="order-client-phone" placeholder="客户电话" value="{{ $client_phone or '' }}" style="width:100px;" />

                        <select class="form-control form-filter select2-box select2-district" name="order-district[]" multiple="multiple" style="width:160px;">
                            <option value="-1">选择区域</option>
                        </select>

{{--                        <select class="form-control form-filter" name="order-exported-status" style="width:100px;">--}}
{{--                            <option value="-1">导出状态</option>--}}
{{--                            <option value="0" @if($exported_status == 0) selected="selected" @endif>待导出</option>--}}
{{--                            <option value="1" @if($exported_status == 1) selected="selected" @endif>已导出</option>--}}
{{--                        </select>--}}

                        @if($me->client_er->is_api_scrm == 1)
                        <select class="form-control form-filter" name="order-is-api-pushed" style="width:100px;">
                            <option value="-1">api推送</option>
                            <option value="0" @if($is_api_pushed == 0) selected="selected" @endif>未推送</option>
                            <option value="1" @if($is_api_pushed == 1) selected="selected" @endif>已推送</option>
                        </select>
                        @endif

                        <select class="form-control form-filter" name="order-assign-status" style="width:100px;">
                            <option value="-1">分配状态</option>
                            <option value="0" @if($assign_status == 0) selected="selected" @endif>待分配</option>
                            <option value="1" @if($assign_status == 1) selected="selected" @endif>已分配</option>
                        </select>

{{--                        <select class="form-control form-filter" name="order-is-wx" style="width:88px;">--}}
{{--                            <option value="-1">是否+V</option>--}}
{{--                            <option value="1" @if($is_wx == "1") selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_wx == "0") selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="order-is-repeat" style="width:88px;">--}}
{{--                            <option value="-1">是否重复</option>--}}
{{--                            <option value="1" @if($is_repeat >= 1) selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_repeat == 0) selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <input type="text" class="form-control form-filter filter-keyup" name="order-description" placeholder="通话小结" value="" style="width:120px;" />--}}

                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-order">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-order">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-order">
                            <i class="fa fa-undo"></i> 重置
                        </button>


                        <div class="pull-left clear-both">
                        </div>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                </div>

            </div>


            @if(in_array($me->user_type,[0,1,9,11]))
            <div class="box-footer" style="padding:4px 10px;">
                <div class="row" style="margin:2px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter _none">
                                <option value="-1">请选择操作类型</option>
                                <option value="启用">启用</option>
                                <option value="禁用">禁用</option>
                                <option value="删除">删除</option>
                                <option value="彻底删除">彻底删除</option>
                            </select>
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-operate"><i class="fa fa-check"></i> 批量操作</span>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出</span>

{{--                            <select name="bulk-operate-exported-status" class="form-control form-filter">--}}
{{--                                <option value="-1">请选导出状态</option>--}}
{{--                                <option value="1">已导出</option>--}}
{{--                                <option value="0">待导出</option>--}}
{{--                            </select>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-exported-status"><i class="fa fa-check"></i> 批量更改导出状态</span>--}}

                            @if($me->client_er->is_api_scrm == 1)
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-api-push">
                                <i class="fa fa-share-square"></i> 批量推送
                            </span>
                            @endif

                            <select name="bulk-operate-assign-status" class="form-control form-filter">
                                <option value="-1">请选分配状态</option>
                                <option value="1">已分配</option>
                                <option value="0">待分配</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-assign-status"><i class="fa fa-check"></i> 批量更改导出状态</span>

                            <select name="bulk-operate-staff-id" class="form-control form-filter">
                                <option value="-1">选择员工</option>
                                @foreach($staff_list as $v)
                                    <option value="{{ $v->id }}" @if($v->id == $staff_id) selected="selected" @endif>{{ $v->username }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-assign-staff"><i class="fa fa-check"></i> 批量分配</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif


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



{{--显示-基本信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info">
    <div class="col-md-8 col-md-offset-2" id="" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:16px 0 0;">
                <h3 class="box-title">跟进-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-inspected-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-inspect" readonly>
                    <input type="hidden" name="follow-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-project">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--电话--}}
{{--                    <div class="form-group item-detail-phone">--}}
{{--                        <label class="control-label col-md-2">电话</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <span class="item-detail-text"></span>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2 item-detail-operate" data-operate=""></div>--}}
{{--                    </div>--}}
                    {{--微信号--}}
{{--                    <div class="form-group item-detail-wx-id">--}}
{{--                        <label class="control-label col-md-2">微信号</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <span class="item-detail-text"></span>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2 item-detail-operate" data-operate="driver"></div>--}}
{{--                    </div>--}}
                    {{--所在城市--}}
                    <div class="form-group item-detail-city-district">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-detail-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-detail-description">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text" style="white-space:pre-line;"></span>
                        </div>
                    </div>
                    {{--跟进详情--}}
                    <div class="form-group item-detail-follow">
                        <label class="control-label col-md-2">跟进详情</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text" style="white-space:pre-line;"></span>
                        </div>
                    </div>
                    {{--跟进--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">跟进</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="follow-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success item-summit-for-follow" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default item-cancel-for-follow" id="">取消</button>
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
                            <option value="-1">选择属性</option>
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
@endsection
@section('custom-style')
<style>
    /*.tableArea table { min-width:1380px; }*/
    .tableArea table { width:100% !important; min-width:1380px; }
    .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }

    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-income { background:#8FEBE5; }
    .bg-route { background:#FFEBE5; }
    .bg-finance { background:#E2FCAB; }
    .bg-empty { background:#F6C5FC; }
    .bg-journey { background:#F5F9B4; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[ @if(!in_array($length,[10, 50, 100, 200, 500, 1000])) {{ $length.',' }} @endif 10, 50, 100, 200, 500, 1000], [ @if(!in_array($length,[10, 50, 100, 200, 500, 1000])) {{ $length.',' }} @endif "10", "50", "100", "200", "500", "1000"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 10 }},
                "ajax": {
                    'url': "{{ url('/item/delivery-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="order-id"]').val();
                        d.remark = $('input[name="order-remark"]').val();
                        d.description = $('input[name="order-description"]').val();
                        d.assign = $('input[name="order-assign"]').val();
                        d.assign_start = $('input[name="order-start"]').val();
                        d.assign_ended = $('input[name="order-ended"]').val();
                        d.name = $('input[name="order-name"]').val();
                        d.title = $('input[name="order-title"]').val();
                        d.keyword = $('input[name="order-keyword"]').val();
                        d.staff = $('select[name="order-staff"]').val();
                        d.project = $('select[name="order-project"]').val();
                        d.status = $('select[name="order-status"]').val();
                        d.order_type = $('select[name="order-type"]').val();
                        d.client_name = $('input[name="order-client-name"]').val();
                        d.client_phone = $('input[name="order-client-phone"]').val();
                        d.is_wx = $('select[name="order-is-wx"]').val();
                        d.is_repeat = $('select[name="order-is-repeat"]').val();
                        d.assign_status = $('select[name="order-assign-status"]').val();
                        d.exported_status = $('select[name="order-exported-status"]').val();
                        d.is_api_pushed = $('select[name="order-is-api-pushed"]').val();
                        d.city = $('select[name="order-city[]"]').val();
                        d.district = $('select[name="order-district[]"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
                // "scrollY": false,
                "scrollY": ($(document).height() - 350)+"px",
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 6 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                },
                "showRefresh": true,
                "columnDefs": [
                    {
                        // "targets": [10, 11, 15, 16],
                        "targets": [],
                        "visible": false,
                        "searchable": false
                    }
                ],
                "columns": [
                   {
                       "title": "选择",
                       "width": "32px",
                       "data": "id",
                       "orderable": false,
                       render: function(data, type, row, meta) {
                           return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-order-id="'+row.order_id+'"></label>';
                       }
                   },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "50px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "操作",
                        "data": 'id',
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('order_operate');
                                $(nTd).attr('data-id',row.id).attr('data-name','操作');
                                $(nTd).attr('data-key','order_operate').attr('data-value',row.id);
                                $(nTd).attr('data-content',JSON.stringify(row.content_decode));
                            }
                        },
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_follow = '<a class="btn btn-xs bg-blue item-modal-show-for-follow" data-id="'+data+'">客户跟进</a>';
                            var $html_quality = '<a class="btn btn-xs bg-olive item-quality-evaluate-submit" data-id="'+data+'">质量评估</a>';



                            var $more_html =
                                '<div class="btn-group">'+
                                '<button type="button" class="btn btn-xs btn-success" style="padding:2px 8px; margin-right:0;">操作</button>'+
                                '<button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding:2px 6px; margin-left:-1px;">'+
                                '<span class="caret"></span>'+
                                '<span class="sr-only">Toggle Dropdown</span>'+
                                '</button>'+
                                '<ul class="dropdown-menu" role="menu">'+
                                '<li><a href="#">Action</a></li>'+
                                '<li><a href="#">删除</a></li>'+
                                '<li><a href="#">弃用</a></li>'+
                                '<li class="divider"></li>'+
                                '<li><a href="#">Separate</a></li>'+
                                '</ul>'+
                                '</div>';

                            var $html =
                                // $html_follow+
                                $html_quality+
                                // $html_record+
                                '';
                            return $html;

                        }
                    },
                    // {
                    //     "title": "导出状态",
                    //     "data": "exported_status",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('exported_status');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','导出状态');
                    //             $(nTd).attr('data-key','exported_status').attr('data-value',row.id);
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data == 0) return '<small class="btn-xs btn-warning">未导出</small>';
                    //         else if(data == 1) return '<small class="btn-xs btn-success">已导出</small>';
                    //         return data;
                    //     }
                    // },
                    @if($me->client_er->is_api_scrm == 1)
                    {
                        "title": "API推送",
                        "data": "is_api_pushed",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('is_api_pushed');
                                $(nTd).attr('data-id',row.id).attr('data-name','分配状态');
                                $(nTd).attr('data-key','is_api_pushed').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs btn-info">未推送</small>';
                            else if(data == 1) return '<small class="btn-xs btn-success">已推送</small>';
                            return data;
                        }
                    },
                    @endif
                    {
                        "title": "工单质量",
                        "data": "order_quality",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('order_quality');
                                $(nTd).attr('data-id',row.id).attr('data-name','工单质量');
                                $(nTd).attr('data-key','order_quality').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == "有效") return '<small class="btn-xs btn-success">有效</small>';
                            else if(data == "无效") return '<small class="btn-xs btn-danger">无效</small>';
                            else if(data == "重单") return '<small class="btn-xs btn-info">重单</small>';
                            else if(data == "无法联系") return '<small class="btn-xs btn-warning">无法联系</small>';
                            return data;
                        }
                    },
                    {
                        "title": "交付时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
//                            return data;
                            if(!data) return '';
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
                    {
                        "title": "分配状态",
                        "data": "assign_status",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('assign_status');
                                $(nTd).attr('data-id',row.id).attr('data-name','分配状态');
                                $(nTd).attr('data-key','assign_status').attr('data-value',row.id);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs btn-warning">待分配</small>';
                            else if(data == 1) return '<small class="btn-xs btn-success">已分配</small>';
                            return data;
                        }
                    },
                    {
                        "title": "分派员工",
                        "data": "client_staff_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('client_staff');
                                $(nTd).attr('data-id',row.id).attr('data-name','分派员工');
                                $(nTd).attr('data-key','client_staff_id').attr('data-value',row.id);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.client_staff_er == null)
                            {
                                return '未指定';
                            }
                            else {
                                return '<a href="javascript:void(0);">'+row.client_staff_er.username+'</a>';
                            }
                        }
                    },
                    // {
                    //     "title": "工单ID",
                    //     "data": "order_id",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(data) return data;
                    //         return "--";
                    //     }
                    // },
                    // {
                    //     "title": "项目",
                    //     "data": "project_id",
                    //     "className": "",
                    //     "width": "120px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(row.project_er == null)
                    //         {
                    //             return '未指定';
                    //         }
                    //         else {
                    //             return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                    //         }
                    //     }
                    // },
                    {
                        "title": "客户姓名",
                        "data": "order_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er) return row.order_er.client_name;
                            return "--";
                        }
                    },
                    {
                        "title": "客户电话",
                        "data": "client_phone",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "微信号",
                        "data": "order_id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er) return row.order_er.wx_id;
                            return "--";
                        }
                    },
                    {
                        "title": "牙齿数量",
                        "data": "order_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er) return row.order_er.teeth_count;
                            return "--";
                        }
                    },
                    // {
                    //     "title": "所在城市",
                    //     "data": "order_id",
                    //     "className": "",
                    //     "width": "120px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(row.order_er)
                    //         {
                    //             if(row.order_er.location_city)
                    //             {
                    //                 if(row.order_er.location_district)
                    //                 {
                    //                     return row.order_er.location_city + ' - ' + row.order_er.location_district;
                    //                 }
                    //                 else return row.order_er.location_city;
                    //             }
                    //             else return '--';
                    //         }
                    //         else return '--';
                    //     }
                    // },
                    {
                        "title": "所在城市",
                        "data": "order_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er)
                            {
                                if(row.order_er.location_city)
                                {
                                    return row.order_er.location_city;
                                }
                                else return '--';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "所在区域",
                        "data": "order_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er)
                            {
                                if(row.order_er.location_district)
                                {
                                    return row.order_er.location_district;
                                }
                                else return '--';
                            }
                            else return '--';
                        }
                    },
                    // {
                    //     "title": "渠道来源",
                    //     "data": "order_id",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(row.order_er) return row.order_er.channel_source;
                    //         return "--";
                    //     }
                    // },
                    {
                        "title": "通话小结",
                        "data": "order_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er)
                            {
                                if(row.order_er.description) return '<small class="btn-xs bg-yellow">双击查看</small>';
                                else return "--";
                            }
                            else return "--";
                        }
                    },
                    {
                        "title": "录音播放",
                        "data": "order_id",
                        "className": "",
                        "width": "360px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er)
                            {
                                // if(row.order_er.recor;ding_address) return '<a target="_blank" href="'+row.order_er.recording_address+'">录音地址</a>';
                                // else return "--"
                                if(row.order_er.recording_address)
                                {
                                    return '<audio controls controlsList="nodownload" style="width:360px;height:20px;"><source src="'+row.order_er.recording_address+'" type="audio/mpeg"></audio>';
                                }
                                else return "--";
                            }
                            else return "--";
                        }
                    },
                    {
                        "title": "更改时间",
                        "data": 'updated_at',
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
//                            return data;
                            if(!data) return '';
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
                    }
                ],
                "drawCallback": function (settings) {

                    var $firstRowData = ajax_datatable.row(0).data();
                    var $last_delivery_id = localStorage.getItem('last_delivery_id');
                    if($last_delivery_id)
                    {
                        if($firstRowData.id > $last_delivery_id)
                        {
                            localStorage.setItem('last_delivery_id',$firstRowData.id);
                            $('.notification-dom').hide();
            }
                    }
                    else
                    {
                        localStorage.setItem('last_delivery_id',$firstRowData.id);
                        $('.notification-dom').hide();
                    }
                    // console.log($firstRowData);

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="order-id"]').val())  $obj.order_id = $('input[name="order-id"]').val();
                    if($('input[name="order-assign"]').val())  $obj.assign = $('input[name="order-assign"]').val();
                    if($('input[name="order-start"]').val())  $obj.assign_start = $('input[name="order-start"]').val();
                    if($('input[name="order-ended"]').val())  $obj.assign_ended = $('input[name="order-ended"]').val();
                    if($('select[name="order-department-district"]').val() > 0)  $obj.department_district_id = $('select[name="order-department-district"]').val();
                    if($('select[name="order-staff"]').val() > 0)  $obj.staff_id = $('select[name="order-staff"]').val();
                    if($('select[name="order-client"]').val() > 0)  $obj.client_id = $('select[name="order-client"]').val();
                    if($('select[name="order-project"]').val() > 0)  $obj.project_id = $('select[name="order-project"]').val();
                    if($('input[name="order-client-name"]').val())  $obj.client_name = $('input[name="order-client-name"]').val();
                    if($('input[name="order-client-phone"]').val())  $obj.client_phone = $('input[name="order-client-phone"]').val();
                    if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
                    if($('select[name="order-is-wx"]').val() > 0)  $obj.is_delay = $('select[name="order-is-wx"]').val();
                    if($('select[name="order-is-repeat"]').val() > 0)  $obj.is_delay = $('select[name="order-is-repeat"]').val();
                    if($('select[name="order-assign-status"]').val() != -1)  $obj.assign_status = $('select[name="order-assign-status"]').val();
                    // if($('select[name="order-exported-status"]').val() != -1)  $obj.exported_status = $('select[name="order-exported-status"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != 10) $obj.length = $page_length;
                    var $page_start = this.api().context[0]._iDisplayStart; // 当前页开始
                    var $pagination = ($page_start / $page_length) + 1; //得到页数值 比页码小1
                    if($pagination > 1) $obj.page = $pagination;


                    if(JSON.stringify($obj) != "{}")
                    {
                        var $url = url_build('',$obj);
                        history.replaceState({page: 1}, "", $url);
                    }
                    else
                    {
                        $url = "{{ url('/item/delivery-list') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }

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

        var $id = $.getUrlParam('id');
        if($id) $('input[name="order-id"]').val($id);
        TableDatatablesAjax.init();
        // $('#datatable_ajax').DataTable().init().fnPageChange(3);
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
                    'url': "/item/order-modify-record?id="+$id,
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
                        "width": "60px",
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
                            else if(data == 91) return '<small class="btn-xs bg-yellow">验证</small>';
                            else if(data == 92) return '<small class="btn-xs bg-yellow">审核</small>';
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
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
                                if(data == "client_id") return '客户';
                                else if(data == "project_id") return '项目';
                                else if(data == "client_name") return '客户电话';
                                else if(data == "client_phone") return '客户电话';
                                else if(data == "is_wx") return '是否+V';
                                else if(data == "wx_id") return '微信号';
                                else if(data == "teeth_count") return '牙齿数量';
                                else if(data == "location_city") return '城市区域';
                                else if(data == "channel_source") return '渠道来源';
                                else if(data == "description") return '通话小结';
                                else if(data == "inspected_description") return '审核说明';
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
                                else return '<a href="javascript:void(0);">'+row.before_client_er.username+'</a>';
                            }
                            else if(row.column_name == 'project_id')
                            {
                                if(row.before_project_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_project_er.name+'</a>';
                            }

                            if(row.column_name == 'is_wx')
                            {
                                if(data == 0) return '否';
                                else if(data == 1) return '是';
                                else return '--';
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
                            if(row.column_name == 'client_id')
                            {
                                if(row.after_client_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_client_er.username+'</a>';
                            }
                            else if(row.column_name == 'project_id')
                            {
                                if(row.after_project_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_project_er.name+'</a>';
                            }

                            if(row.column_name == 'is_wx')
                            {
                                if(data == 0) return '否';
                                else if(data == 1) return '是';
                                else return '--';
                            }

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
@include(env('TEMPLATE_DK_CLIENT').'entrance.item.delivery-list-script')
@endsection
