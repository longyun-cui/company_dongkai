@extends(env('TEMPLATE_DK_ADMIN_2').'layout.layout')


@section('head_title')
    {{ $title_text or '上架列表' }} - 自选系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')<b>{{ $title_text or '上架列表' }}</b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">


            <div class="box-body datatable-body item-main-body" id="datatable-for-choice-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="choice-id" placeholder="ID" value="{{ $choice_id or '' }}" style="width:88px;" />
                        <input type="text" class="form-control form-filter filter-keyup" name="clue-id" placeholder="线索ID" value="{{ $clue_id or '' }}" style="width:88px;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="choice-assign" placeholder="发布日期" value="{{ $assign or '' }}" readonly="readonly" style="width:80px;text-align:center;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                            <i class="fa fa-chevron-right"></i>
                        </button>


                        <select class="form-control form-filter select-select2 select2-box choice-project" name="choice-project" style="width:160px;">
                            <option value="-1">选择项目</option>
                            @if(!empty($project_list))
                                @foreach($project_list as $v)
                                    @if(!empty($project_id))
                                        @if($v->id == $project_id)
                                            <option value="{{ $v->id }}" selected="selected">{{ $v->name }}</option>
                                        @else
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>


                        <select class="form-control form-filter select-select2 select2-box choice-customer" name="choice-customer" style="width:160px;">
                            <option value="-1">选择客户</option>
                            @if(!empty($customer_list))
                                @foreach($customer_list as $v)
                                    @if(!empty($customer_id))
                                        @if($v->id == $customer_id)
                                            <option value="{{ $v->id }}" selected="selected">{{ $v->username }}</option>
                                        @else
                                            <option value="{{ $v->id }}">{{ $v->username }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>


                        <select class="form-control form-filter" name="choice-choice-type" style="width:88px;">
                            <option value="-1">交付类型</option>
                            <option value="95">交付</option>
                            <option value="96">分发</option>
                        </select>


{{--                        <select class="form-control form-filter" name="choice-delivered-status" style="width:88px;">--}}
{{--                            <option value="-1">交付状态</option>--}}
{{--                            <option value="待交付" @if("待审核" == $delivered_status) selected="selected" @endif>待交付</option>--}}
{{--                            <option value="已交付" @if("已审核" == $delivered_status) selected="selected" @endif>已交付</option>--}}
{{--                        </select>--}}

                        <input type="text" class="form-control form-filter filter-keyup" name="choice-client-name" placeholder="客户姓名" value="{{ $client_name or '' }}" style="width:88px;" />
                        <input type="text" class="form-control form-filter filter-keyup" name="choice-client-phone" placeholder="客户电话" value="{{ $client_phone or '' }}" style="width:88px;" />

{{--                        <select class="form-control form-filter" name="choice-is-wx" style="width:88px;">--}}
{{--                            <option value="-1">是否+V</option>--}}
{{--                            <option value="1" @if($is_wx == "1") selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_wx == "0") selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="choice-is-repeat" style="width:88px;">--}}
{{--                            <option value="-1">是否重复</option>--}}
{{--                            <option value="1" @if($is_repeat >= 1) selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_repeat == 0) selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <input type="text" class="form-control form-filter filter-keyup" name="choice-description" placeholder="通话小结" value="" style="width:120px;" />--}}

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
                <table class='table table-striped table-bordered table-hover choice-column' id='datatable_ajax'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                </div>

            </div>


            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <div class="box-footer _none" style="padding:4px 10px;">
                <div class="row" style="margin:2px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出Excel</span>

                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value="-1">请选导出状态</option>
                                <option value="1">已导出</option>
                                <option value="0">待导出</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-exported"><i class="fa fa-check"></i> 批量更改导出状态</span>
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
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
<div class="modal fade modal-main-body" id="modal-body-for-follow">
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
                    <input type="hidden" name="operate" value="choice-inspect" readonly>
                    <input type="hidden" name="follow-choice-id" value="0" readonly>

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

{{--显示-附件-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-attachment">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单【<span class="attachment-set-title"></span>】</h3>
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
                <input type="hidden" name="attachment-set-operate" value="item-choice-attachment-set" readonly>
                <input type="hidden" name="attachment-set-choice-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-choice-attachment-set" readonly>
                <input type="hidden" name="clue_id" value="0" readonly>
                <input type="hidden" name="operate_type" value="add" readonly>
                <input type="hidden" name="column_key" value="attachment" readonly>


                <div class="form-group">
                    <label class="control-label col-md-2">附件名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="attachment_name" autocomplete="off" placeholder="附件名称" value="">
                    </div>
                </div>

                {{--多图上传--}}
                <div class="form-group">

                    <label class="control-label col-md-2">图片上传</label>

                    <div class="col-md-8">
                        <input id="multiple-images" type="file" class="file-multiple-images" name="multiple_images[]" multiple >
                    </div>

                </div>

                {{--多图上传--}}
                <div class="form-group _none">

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

    .select2-container { height:100%; bchoice-radius:0; float:left; }
    .select2-container .select2-selection--single { bchoice-radius:0; }
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
                "aLengthMenu": [[ @if(!in_array($length,[20,50, 100, 200])) {{ $length.',' }} @endif 20,50, 100, 200], [ @if(!in_array($length,[20,50, 100, 200])) {{ $length.',' }} @endif "20", "50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 20 }},
                "ajax": {
                    'url': "{{ url('/item/choice-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="choice-id"]').val();
                        d.clue_id = $('input[name="clue-id"]').val();
                        d.remark = $('input[name="choice-remark"]').val();
                        d.description = $('input[name="choice-description"]').val();
                        d.assign = $('input[name="choice-assign"]').val();
                        d.assign_start = $('input[name="choice-start"]').val();
                        d.assign_ended = $('input[name="choice-ended"]').val();
                        d.name = $('input[name="choice-name"]').val();
                        d.title = $('input[name="choice-title"]').val();
                        d.keyword = $('input[name="choice-keyword"]').val();
                        d.department_district = $('select[name="choice-department-district[]"]').val();
                        d.customer = $('select[name="choice-customer"]').val();
                        d.project = $('select[name="choice-project"]').val();
                        d.status = $('select[name="choice-status"]').val();
                        d.sales_type = $('select[name="choice-choice-type"]').val();
                        d.order_type = $('select[name="choice-type"]').val();
                        d.client_name = $('input[name="choice-client-name"]').val();
                        d.client_phone = $('input[name="choice-client-phone"]').val();
                        d.is_wx = $('select[name="choice-is-wx"]').val();
                        d.is_repeat = $('select[name="choice-is-repeat"]').val();
                        d.delivered_status = $('select[name="choice-delivered-status"]').val();
                        d.delivered_result = $('select[name="choice-delivered-result[]"]').val();
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
                "scrollY": ($(document).height() - 360)+"px",
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
                           return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-choice-id="'+row.clue_id+'"></label>';
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
                            var $html_delete = '';
                            var $html_exported = '<a class="btn btn-xs bg-blue item-exported-submit" data-id="'+data+'">导出</a>';
                            var $html_follow = '<a class="btn btn-xs bg-blue item-modal-show-for-follow" data-id="'+data+'">客户跟进</a>';
                            var $html_quality = '<a class="btn btn-xs bg-olive item-quality-evaluate-submit" data-id="'+data+'">质量评估</a>';
                            var $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';



                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                // $html_delete = '<a class="btn btn-xs bg-grey item-restore-submit" data-id="'+data+'">恢复</a>';
                                $html_delete = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">删除</a>';
                            }

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
                                $html_exported+
                                $html_delete+
                                // $html_follow+
                                // $html_quality+
                                $html_record+
                                '';
                            return $html;

                        }
                    },
                    // {
                    //     "title": "导出状态",
                    //     "data": "is_exported",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-select-set-');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','交付结果');
                    //             $(nTd).attr('data-key','is_exported').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','审核结果');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data == 0) return '<small class="btn-xs btn-primary">未导出</small>';
                    //         else if(data == 1) return '<small class="btn-xs btn-success">已导出</small>';
                    //         else if(data == -1) return '<small class="btn-xs btn-warning">未选择</small>';
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "工单质量",
                    //     "data": "order_quality",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(data == "有效") return '<small class="btn-xs btn-success">有效</small>';
                    //         else if(data == "无效") return '<small class="btn-xs btn-danger">无效</small>';
                    //         else if(data == "重单") return '<small class="btn-xs btn-info">重单</small>';
                    //         else if(data == "无法联系") return '<small class="btn-xs btn-warning">无法联系</small>';
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "商品状态",
                        "data": "id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.deleted_at == null)
                            {
                                return '<small class="btn-xs bg-green">正常</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }
                        }
                    },
                    {
                        "title": "上架类型",
                        "data": "pivot_type",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return "--";
                            var $result_html = '';
                            if(data == 91) return '<small class="btn-xs bg-green">上架</small>';
                            else if(data == 101) return '<small class="btn-xs bg-orange">分发</small>';
                            return $result_html;
                        }
                    },
                    {
                        "title": "销售类型",
                        "data": "sale_type",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $result_html = '';
                            if(data == 0) $result_html = '<small class="btn-xs bg-teal">未上架</small>';
                            else if(data == 1) $result_html = '<small class="btn-xs bg-blue">一般</small>';
                            else if(data == 11) $result_html = '<small class="btn-xs bg-green">优选</small>';
                            else if(data == 66) $result_html = '<small class="btn-xs bg-yellow">独享</small>';
                            else $result_html = '<small class="btn-xs bg-black">error</small>';
                            return $result_html;
                        }
                    },
                    {
                        "title": "销售结果",
                        "data": "sale_result",
                        "className": "",
                        "width": "72px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $result_html = '';
                            if(data == 0) $result_html = '<small class="btn-xs bg-teal">待接单</small>';
                            else if(data == 1) $result_html = '<small class="btn-xs bg-blue">已接单</small>';
                            else if(data == 9) $result_html = '<small class="btn-xs bg-yellow">已成单</small>';
                            else $result_html = '<small class="btn-xs bg-black">error</small>';
                            return $result_html;
                        }
                    },
                    {
                        "title": "线索ID",
                        "data": "clue_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return '<a target="_blank" href="/item/clue-list?clue_id='+data+'">'+data+'</a>';
                            return "--";
                        }
                    },
                    {
                        "title": "客户",
                        "data": "customer_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.customer_er == null)
                            {
                                return '未指定';
                            }
                            else {
                                return '<a href="javascript:void(0);">'+row.customer_er.username+'</a>';
                            }
                        }
                    },
                    {
                        "title": "项目",
                        "data": "project_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.project_er == null)
                            {
                                return '未指定';
                            }
                            else {
                                return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                            }
                        }
                    },
                    {
                        "title": "客户姓名",
                        "data": "client_name",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
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
                        "data": "clue_id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er) return row.order_er.wx_id;
                            return "--";
                        }
                    },
                    {
                        "title": "客户意向",
                        "data": "clue_id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // if(!data) return '--';
                            // return data;
                            var $result_html = '';
                            if(row.order_er)
                            {
                                var $data = row.order_er.client_intention;
                                if($data == "A类")
                                {
                                    $result_html = '<small class="btn-xs bg-red">'+$data+'</small>';
                                }
                                else if($data == "B类")
                                {
                                    $result_html = '<small class="btn-xs bg-blue">'+$data+'</small>';
                                }
                                else if($data == "C类")
                                {
                                    $result_html = '<small class="btn-xs bg-green">'+$data+'</small>';
                                }
                                else
                                {
                                    $result_html = '--';
                                }
                            }
                            return $result_html;
                        }
                    },
                    {
                        "title": "所在城市",
                        "data": "location_city",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '--';
                            else {
                                if(!row.location_district) return data;
                                else return data+' - '+row.location_district;
                            }
                        }
                    },
                    {
                        "title": "通话小结",
                        "data": "clue_id",
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
                        "title": "录音地址",
                        "data": "clue_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.order_er)
                            {
                                if(row.order_er.recording_address) return '<a target="_blank" href="'+row.order_er.recording_address+'">录音地址</a>';
                                else return "--";
                            }
                            else return "--";
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "发布时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "orderSequence": ["desc", "asc"],
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    },
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="choice-id"]').val())  $obj.choice_id = $('input[name="choice-id"]').val();
                    if($('input[name="clue-id"]').val())  $obj.clue_id = $('input[name="clue-id"]').val();
                    if($('input[name="choice-assign"]').val())  $obj.assign = $('input[name="choice-assign"]').val();
                    if($('input[name="choice-start"]').val())  $obj.assign_start = $('input[name="choice-start"]').val();
                    if($('input[name="choice-ended"]').val())  $obj.assign_ended = $('input[name="choice-ended"]').val();
                    if($('select[name="choice-department-district"]').val() > 0)  $obj.department_district_id = $('select[name="choice-department-district"]').val();
                    if($('select[name="choice-staff"]').val() > 0)  $obj.staff_id = $('select[name="choice-staff"]').val();
                    if($('select[name="choice-client"]').val() > 0)  $obj.client_id = $('select[name="choice-client"]').val();
                    if($('select[name="choice-project"]').val() > 0)  $obj.project_id = $('select[name="choice-project"]').val();
                    if($('input[name="choice-client-name"]').val())  $obj.client_name = $('input[name="choice-client-name"]').val();
                    if($('input[name="choice-client-phone"]').val())  $obj.client_phone = $('input[name="choice-client-phone"]').val();
                    if($('select[name="choice-type"]').val() > 0)  $obj.order_type = $('select[name="choice-type"]').val();
                    if($('select[name="choice-is-wx"]').val() > 0)  $obj.is_delay = $('select[name="choice-is-wx"]').val();
                    if($('select[name="choice-is-repeat"]').val() > 0)  $obj.is_delay = $('select[name="choice-is-repeat"]').val();
                    // if($('select[name="choice-delivered-status"]').val() != -1)  $obj.delivered_status = $('select[name="choice-delivered-status"]').val();
                    if($('select[name="choice-choice-type"]').val() != -1)  $obj.sales_type = $('select[name="choice-choice-type"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != 20) $obj.length = $page_length;
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
                        $url = "{{ url('/item/choice-list') }}";
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
        if($id) $('input[name="choice-id"]').val($id);
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
                    'url': "/item/choice-modify-record?id="+$id,
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
                            else if(data == 81) return '<small class="btn-xs bg-blue">上架</small>';
                            else if(data == 82) return '<small class="btn-xs bg-blue">下架</small>';
                            else if(data == 85) return '<small class="btn-xs bg-green">接单</small>';
                            else if(data == 86) return '<small class="btn-xs bg-yellow">退单</small>';
                            else if(data == 88) return '<small class="btn-xs bg-red">购买</small>';
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
                            else if(row.operate_category == 81)
                            {
                                return '上架';
                            }
                            else if(row.operate_category == 85)
                            {
                                return '接单';
                            }
                            else if(row.operate_category == 86)
                            {
                                return '退单';
                            }
                            else if(row.operate_category == 88)
                            {
                                return '购买';
                            }
                            else if(row.operate_category == 101)
                            {
                                return '删除';
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
                            if(row.record_object == 19)
                            {
                                return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            }
                            else if(row.record_object == 89)
                            {
                                return row.customer_staff_er == null ? '未知' : '<a href="javascript:void(0);">'+row.customer_staff_er.username+'</a>';
                            }
                            else return '--';
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
@include(env('TEMPLATE_DK_ADMIN_2').'entrance.item.choice-list-script')
@endsection
