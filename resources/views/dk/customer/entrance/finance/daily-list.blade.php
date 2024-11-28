@extends(env('TEMPLATE_DK_CUSTOMER').'layout.layout')


@section('head_title')
    {{ $title_text or '财务日报' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header')<span class="statistic-title">{{ $title_text or '财务日报' }}</span>@endsection
@section('description')
    <b class="box-title">
        <span class="statistic-time-type-title">【全部】</span>
        <span class="statistic-time-title"></span>
    </b>
@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">


            <div class="box-body datatable-body item-main-body" id="datatable-for-daily-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="daily-time-type" value="all" readonly>


                        {{--按天查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-daily">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-daily">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-date">
                            <i class="fa fa-search"></i> 按日查询
                        </button>


                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-daily">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-daily">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按时间段查看--}}
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-start" placeholder="起始日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-ended" placeholder="结束日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-period" style="width:100px;">
                            <i class="fa fa-search"></i> 按时间段查询
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-all">
                            <i class="fa fa-search"></i> 全部搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
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
                <table class='table table-striped table-bordered table-hover daily-column' id='datatable_ajax'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                </div>

            </div>


            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
            <div class="box-footer _none" style="padding:4px 10px;">
                <div class="row" style="margin:2px 0;">
                    <div class="col-md-offset-0 col-md-9 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>

                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出</span>


                            <input type="text" name="bulk-operate-delivered-description" class="form-control form-filter pull-right" placeholder="交付说明" style="width:50%;">

{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-operate"><i class="fa fa-check"></i> 批量操作</span>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delivered"><i class="fa fa-share"></i> 批量交付</span>
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
                <input type="hidden" name="attachment-set-operate" value="item-daily-attachment-set" readonly>
                <input type="hidden" name="attachment-set-daily-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-daily-attachment-set" readonly>
                <input type="hidden" name="order_id" value="0" readonly>
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
    .tableArea table { min-width:100%; }
    /*.tableArea table { width:100% !important; min-width:1380px; }*/
    /*.tableArea table tr th, .tableArea table tr td { white-space:nowrap; }*/

    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .datatable-search-row .input-group .time-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker, .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group select { width:100px; text-align:center; }
    .datatable-search-row .input-group .select2-container { width:120px; }

    .select2-container { height:100%; bdaily-radius:0; float:left; }
    .select2-container .select2-selection--single { bdaily-radius:0; }

    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-route { background:#8FEBE5; }
    .bg-income { background:#FFEBE5; }
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
                "aLengthMenu": [[ @if(!in_array($length,[-1])) {{ $length.',' }} @endif -1], [ @if(!in_array($length,[-1, 50, 100, 200])) {{ $length.',' }} @endif "全部"]],
                // "deferRender": false, // 启用缓存
                "processing": true, // 显示处理状态
                "serverSide": true, // 服务器模式
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 20 }},
                "ajax": {
                    'url': "{{ url('/finance/finance-daily-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="daily-id"]').val();
                        d.remark = $('input[name="daily-remark"]').val();
                        d.description = $('input[name="daily-description"]').val();
                        d.name = $('input[name="daily-name"]').val();
                        d.title = $('input[name="daily-title"]').val();
                        d.keyword = $('input[name="daily-keyword"]').val();
                        d.order_type = $('select[name="daily-type"]').val();
                        d.status = $('select[name="daily-status"]').val();
                        d.staff = $('select[name="daily-staff"]').val();
                        d.company = $('select[name="daily-company"]').val();
                        d.channel = $('select[name="daily-channel"]').val();
                        d.business = $('select[name="daily-business"]').val();
                        d.project = $('select[name="daily-project"]').val();
                        d.time_type = $('input[name="daily-time-type"]').val();
                        d.month = $('input[name="daily-month"]').val();
                        d.date = $('input[name="daily-date"]').val();
                        d.assign = $('input[name="daily-assign"]').val();
                        d.assign_start = $('input[name="daily-start"]').val();
                        d.assign_ended = $('input[name="daily-ended"]').val();
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
                "scrollY": ($(document).height() - 360)+"px",
                "scrollCollapse": true,
                "fixedColumns": {
                    @if($me->department_district_id == 0)
                        "leftColumns": "@if($is_mobile_equipment) 0 @else 0 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @else
                        "leftColumns": "@if($is_mobile_equipment) 0 @else 0 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @endif
                },
                "showRefresh": true,
                "columnDefs": [
{{--                    @if(!in_array($me->user_type,[0,1,11]))--}}
{{--                    {--}}
{{--                        "targets": [0,7,8,9,10],--}}
{{--                        "visible": false,--}}
{{--                    }--}}
{{--                    @endif--}}
                ],
                "columns": [
                   // {
                   //     "title": "选择",
                   //     "width": "32px",
                   //     "data": "id",
                   //     "orderable": false,
                   //     render: function(data, type, row, meta) {
                   //         return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                   //     }
                   // },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "title": "日期",
                        "data": 'formatted_date',
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "消费次数",
                        "data": "total_of_daily_count",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data < 0) return '<b class="text-red">' + parseFloat(data) + '</b>';
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "消费金额",
                        "data": "total_of_daily_cost",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="daily-id"]').val())  $obj.order_id = $('input[name="daily-id"]').val();
                    if($('input[name="daily-assign"]').val())  $obj.assign = $('input[name="daily-assign"]').val();
                    // if($('input[name="daily-start"]').val())  $obj.assign_start = $('input[name="daily-start"]').val();
                    // if($('input[name="daily-ended"]').val())  $obj.assign_ended = $('input[name="daily-ended"]').val();
                    if($('select[name="daily-type"]').val() > 0)  $obj.order_type = $('select[name="daily-type"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != {{ $length or 20 }}) $obj.length = $page_length;
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
                        $url = "{{ url('/finance/daily-list') }}";
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
        if($id) $('input[name="daily-id"]').val($id);
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
                    'url': "/item/daily-modify-record?id="+$id,
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
                            else if(data == 95) return '<small class="btn-xs bg-green">交付</small>';
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
                                if(data == "assign_date") return '日期';
                                else if(data == "project_id") return '项目';
                                else if(data == "outbound_background") return '外呼后台';
                                else if(data == "attendance_manpower") return '出席人力';
                                else if(data == "delivery_quantity") return '交付量';
                                else if(data == "call_charge_daily_cost") return '当日话费';
                                else if(data == "manpower_daily_wage") return '人力日薪';
                                else if(data == "call_charge_coefficient") return '话费系数';
                                else if(data == "material_coefficient") return '物料系数';
                                else if(data == "taxes_coefficient") return '税费系数';
                                else if(data == "description") return '备注';
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
                            else if(row.operate_category == 95)
                            {
                                if(data == "client_id") return '客户';
                                else if(data == "delivered_result") return '交付结果';
                                else return '交付';
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
                                return data;
                            }
                            if(row.column_type == 'timestamp_datetime' || row.column_type == 'dtimestamp_ate')
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
                                        if(row.column_type == 'timestamp_datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'timestamp_date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'timestamp_datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'timestamp_date') return $year+'-'+$month+'-'+$day;
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
                                return data;
                            }
                            if(row.column_type == 'timestamp_datetime' || row.column_type == 'timestamp_date')
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
                                    if(row.column_type == 'timestamp_datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'timestamp_date') return $month+'-'+$day;
                                }
                                else
                                {
                                    if(row.column_type == 'timestamp_datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'timestamp_date') return $year+'-'+$month+'-'+$day;
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
@include(env('TEMPLATE_DK_CUSTOMER').'entrance.finance.daily-list-script')
@endsection
