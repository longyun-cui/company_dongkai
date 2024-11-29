@extends(env('TEMPLATE_DK_ADMIN_2').'layout.layout')


@section('head_title')
    {{ $title_text or '拨号列表' }} - 自选系统 - {{ config('info.info.short_name') }}
@endsection




@section('header')<span class="box-title">{{ $title_text or '拨号列表' }}</span>@endsection
@section('description')<b></b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">


            <div class="box-body datatable-body item-main-body" id="datatable-for-clue-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="clue-id" placeholder="ID" value="{{ $clue_id or '' }}" style="width:80px;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="clue-assign" placeholder="创建日期" value="{{ $assign or '' }}" readonly="readonly" style="width:100px;text-align:center;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                            <i class="fa fa-chevron-right"></i>
                        </button>

{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="clue-delivered_date" placeholder="交付日期" value="" readonly="readonly" style="width:100px;text-align:center;" />--}}


{{--                        <select class="form-control form-filter select2-box clue-select2-project" name="clue-project" style="width:120px;">--}}
{{--                            @if($project_id > 0)--}}
{{--                                <option value="-1">选择项目</option>--}}
{{--                                <option value="{{ $project_id }}" selected="selected">{{ $project_name }}</option>--}}
{{--                            @else--}}
{{--                                <option value="-1">选择项目</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}

                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <select class="form-control form-filter select2-box clue-select2-customer" name="clue-customer" style="width:120px;">
                                <option value="-1">选择客户</option>
                                @foreach($customer_list as $v)
                                    <option value="{{ $v->id }}" @if($v->id == $customer_id) selected="selected" @endif>{{ $v->username }}</option>
                                @endforeach
                            </select>
                        @endif

{{--                        <select class="form-control form-filter" name="clue-delivered-status" style="width:100px;">--}}
{{--                            <option value="-1">交付状态</option>--}}
{{--                            <option value="待交付" @if("待交付" == $delivered_status) selected="selected" @endif>待交付</option>--}}
{{--                            <option value="已交付" @if("已交付" == $delivered_status) selected="selected" @endif>已交付</option>--}}
{{--                            <option value="已操作" @if("已操作" == $delivered_status) selected="selected" @endif>已操作</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter select2-box" name="clue-delivered-result[]" multiple="multiple" style="width:100px;">--}}
{{--                            <option value="-1">交付结果</option>--}}
{{--                            @foreach(config('info.delivered_result') as $v)--}}
{{--                                <option value="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <select class="form-control form-filter select2-box" name="call-result" data-placeholder="通话结果" style="width:100px;">
                            <option value="成功">通话成功</option>
                            <option value="失败">通话失败</option>
                            <option value="全部">全部</option>
                        </select>

{{--                        <input type="text" class="form-control form-filter filter-keyup" name="clue-customer-name" placeholder="客户姓名" value="{{ $customer_name or '' }}" style="width:88px;" />--}}
                        <input type="text" class="form-control form-filter filter-keyup" name="clue-customer-phone" placeholder="客户电话" value="{{ $customer_phone or '' }}" style="width:88px;" />



{{--                        <input type="text" class="form-control form-filter filter-keyup" name="clue-description" placeholder="通话小结" value="" style="width:120px;" />--}}

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
                <table class='table table-striped table-bordered table-hover clue-column' id='datatable_ajax'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
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




@section('custom-css')
@endsection
@section('custom-style')
<style>
    .tableArea table { min-width:100%; }
    /*.tableArea table { width:100% !important; min-width:1380px; }*/
    /*.tableArea table tr th, .tableArea table tr td { white-space:nowrap; }*/

    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .select2-container { height:100%; bclue-radius:0; float:left; }
    .select2-container .select2-selection--single { bclue-radius:0; }

    .select2-container--classic .select2-selection--multiple  { height:34px; bclue-radius:0; }

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
                    'url': "{{ url('/call/call-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="clue-id"]').val();
                        d.remark = $('input[name="clue-remark"]').val();
                        d.description = $('input[name="clue-description"]').val();
                        d.delivered_date = $('input[name="clue-delivered_date"]').val();
                        d.assign = $('input[name="clue-assign"]').val();
                        d.assign_start = $('input[name="clue-start"]').val();
                        d.assign_ended = $('input[name="clue-ended"]').val();
                        d.name = $('input[name="clue-name"]').val();
                        d.title = $('input[name="clue-title"]').val();
                        d.keyword = $('input[name="clue-keyword"]').val();
                        d.department_district = $('select[name="clue-department-district[]"]').val();
                        d.staff = $('select[name="clue-staff"]').val();
                        d.project = $('select[name="clue-project"]').val();
                        d.customer = $('select[name="clue-customer"]').val();
                        d.status = $('select[name="clue-status"]').val();
                        d.order_type = $('select[name="clue-type"]').val();
                        d.customer_name = $('input[name="clue-customer-name"]').val();
                        d.customer_phone = $('input[name="clue-customer-phone"]').val();
                        d.is_wx = $('select[name="clue-is-wx"]').val();
                        d.is_repeat = $('select[name="clue-is-repeat"]').val();
                        d.call_result = $('select[name="call-result"]').val();
                        d.inspected_status = $('select[name="clue-inspected-status"]').val();
                        d.inspected_result = $('select[name="clue-inspected-result[]"]').val();
                        d.delivered_status = $('select[name="clue-delivered-status"]').val();
                        d.delivered_result = $('select[name="clue-delivered-result[]"]').val();
                        d.district_city = $('select[name="clue-city"]').val();
                        d.district_district = $('select[name="clue-district[]"]').val();
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
                "scrollY": ($(document).height() - 300)+"px",
                "scrollCollapse": true,
                "fixedColumns": {

                    "leftColumns": "@if($is_mobile_equipment) 1 @else 2 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                },
                "showRefresh": true,
                "columnDefs": [
                    {
                        // "targets": [0,4,8,9,10,11],
                        // "visible": false,
                    }
                ],
                "columns": [
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
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
                        "title": "拨号结果",
                        "data": "call_result",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1)
                            {
                                if(row.typeResult == "success")
                                {
                                    return '<small class="btn-xs bg-green">拨号成功</small>';
                                }
                                else return '<small class="btn-xs bg-red">通话失败</small>';
                            }
                            else return '<small class="btn-xs bg-orange">拨号失败</small>';
                        }
                    },
                    {
                        "title": "拨号时间",
                        "data": "startTime",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data = '0000-00-00 00:00:00') return '';
                            else return data;
                        }
                    },
                    {
                        "title": "响铃时间",
                        "data": "ringTime",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data = '0000-00-00 00:00:00') return '';
                            else return data;
                        }
                    },
                    {
                        "title": "接通时间",
                        "data": "answerTime",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data = '0000-00-00 00:00:00') return '';
                            else return data;
                        }
                    },
                    {
                        "title": "结束时间",
                        "data": "byeTime",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data = '0000-00-00 00:00:00') return '';
                            else return data;
                        }
                    },
                    {
                        "title": "通话时长",
                        "data": "timeLength",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '--';
                            else return data;
                        }
                    },
                    {
                        "title": "录音播放",
                        "data": "recordFile",
                        "className": "",
                        "width": "160px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                                $(nTd).attr('data-key','recording_address_play').attr('data-value',data);
                            }
                        },
                        render: function(data, type, row, meta) {
                            // return data;
                            if(row.record_url)
                            {
                                return '<audio controls style="width:100px;height:20px;"><source src="'+row.record_url+'" type="audio/mpeg"></audio>';
                            }
                            else return '';
                        }
                    },
                    {
                        "title": "拨号说明",
                        "data": "call_result_msg",
                        "className": "",
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.call_result == 1)
                            {
                                if(row.typeResult == "success")
                                {
                                    return '通话时长：' + row.timeLength + ' 秒';
                                }
                                else
                                {
                                    return '失败原因：' + row.callResultMsg;
                                }
                            }
                            else
                            {
                                if(data == 0) return '';
                                return data;
                            }
                        }
                    },
                    {
                        "title": "操作人",
                        "data": "creator_id",
                        "className": "",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            // if(row.record_object == 19)
                            // {
                            //     return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            // }
                            // else if(row.record_object == 89)
                            // {
                            //     return row.customer_staff_er == null ? '未知' : '<a href="javascript:void(0);">'+row.customer_staff_er.username+'</a>';
                            // }
                            // else return '--';
                        }
                    },
                    {
                        "title": "操作时间",
                        "data": "created_at",
                        "className": "",
                        "width": "120px",
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="clue-id"]').val())  $obj.clue_id = $('input[name="clue-id"]').val();
                    if($('input[name="clue-assign"]').val())  $obj.assign = $('input[name="clue-assign"]').val();
                    if($('input[name="clue-start"]').val())  $obj.assign_start = $('input[name="clue-start"]').val();
                    if($('input[name="clue-ended"]').val())  $obj.assign_ended = $('input[name="clue-ended"]').val();
                    if($('select[name="clue-department-district"]').val() > 0)  $obj.department_district_id = $('select[name="clue-department-district"]').val();
                    if($('select[name="clue-staff"]').val() > 0)  $obj.staff_id = $('select[name="clue-staff"]').val();
                    if($('select[name="clue-customer"]').val() > 0)  $obj.customer_id = $('select[name="clue-customer"]').val();
                    if($('select[name="clue-project"]').val() > 0)  $obj.project_id = $('select[name="clue-project"]').val();
                    if($('input[name="clue-customer-name"]').val())  $obj.customer_name = $('input[name="clue-customer-name"]').val();
                    if($('input[name="clue-customer-phone"]').val())  $obj.customer_phone = $('input[name="clue-customer-phone"]').val();
                    if($('select[name="clue-type"]').val() > 0)  $obj.order_type = $('select[name="clue-type"]').val();
                    if($('select[name="clue-is-wx"]').val() > 0)  $obj.is_delay = $('select[name="clue-is-wx"]').val();
                    if($('select[name="clue-is-repeat"]').val() > 0)  $obj.is_delay = $('select[name="clue-is-repeat"]').val();
                    if($('select[name="clue-sale-status"]').val() != -1)  $obj.sale_status = $('select[name="clue-sale-status"]').val();
                    // if($('select[name="clue-sale-result"]').val() != -1)  $obj.sale_result = $('select[name="clue-sale-result"]').val();
                    // if($('select[name="clue-city"]').val() != -1)  $obj.district_city = $('select[name="clue-city"]').val();
                    // if($('select[name="clue-district"]').val() != -1)  $obj.district_district = $('select[name="clue-district"]').val();

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
                        $url = "{{ url('/item/clue-list') }}";
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
        if($id) $('input[name="clue-id"]').val($id);
        TableDatatablesAjax.init();
        // $('#datatable_ajax').DataTable().init().fnPageChange(3);
    });
</script>
@include(env('TEMPLATE_DK_ADMIN_2').'entrance.call.call-list-script')
@endsection
