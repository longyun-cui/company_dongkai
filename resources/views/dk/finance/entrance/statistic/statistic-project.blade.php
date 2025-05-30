@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '项目报表' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')项目报表 - 财务系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-primary main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">
                    【<span class="statistic-title-">全部项目</span>】
                    <span class="statistic-time-type-title-"></span>
                    <span class="statistic-time-title">（全部）</span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-project">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="project-time-type" value="" readonly>

                        @if(in_array($me->user_type,[0,9,11,31]))
                        <select class="form-control form-filter select-select2 select2-box project-company" name="project-company" style="width:160px;">
                            <option value="-1">选择公司</option>
                            @if(!empty($company_list))
                                @foreach($company_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select class="form-control form-filter select-select2 select2-box project-channel" name="project-channel" style="width:160px;">
                            <option value="-1">选择渠道</option>
                            @if(!empty($channel_list))
                                @foreach($channel_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @endif
                        <select class="form-control form-filter select-select2 select2-box project-project" name="project-project" style="width:160px;">
                            <option value="-1">选择项目</option>
                            @if(!empty($project_list))
                                @foreach($project_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-project">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="project-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-project">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-project-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按天查看--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-project">--}}
{{--                            <i class="fa fa-chevron-left"></i>--}}
{{--                        </button>--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="project-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-project">--}}
{{--                            <i class="fa fa-chevron-right"></i>--}}
{{--                        </button>--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-project-by-day">--}}
{{--                            <i class="fa fa-search"></i> 按日查询--}}
{{--                        </button>--}}


{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-project">--}}
{{--                            <i class="fa fa-search"></i> 全部查询--}}
{{--                        </button>--}}
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-project">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<div class="row">
    <div class="col-md-12">


        <div class="box box-primary _none">

            <div class="box-header">
                <h3 class="box-title">Application Buttons</h3>
            </div>

            <div class="box-body">
                <a class="btn btn-app">
                    <span class="badge bg-yellow">3</span>
                    <i class="fa fa-bullhorn"></i> Notifications
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-green">300</span>
                    <i class="fa fa-barcode"></i> Products
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-purple">891</span>
                    <i class="fa fa-users"></i> Users
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-teal">67</span>
                    <i class="fa fa-inbox"></i> Orders
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-aqua">12</span>
                    <i class="fa fa-envelope"></i> Inbox
                </a>
                <a class="btn btn-app">
                    <span class="badge bg-red">531</span>
                    <i class="fa fa-heart-o"></i> Likes
                </a>
            </div>

        </div>

        <div class="box box-primary bg-white">


            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">
                    <span class="statistic-title-">日报列表</span>
                </h3>
            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-project">

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




            <div class="box-header with-border">
                <h3 class="box-title">走势图</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-delivery-quantity"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-cost-total"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-cost-per-capita"></div>
                    </div>
                    <div class="col-md-12">
                        <div class="myChart" id="myChart-for-cost-unit-average"></div>
                    </div>

                </div>
            </div>




            <div class="box-header with-border">
                <h3 class="box-title">数据分析</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-4">
                        <div id="myChart-for-overview" style="height:320px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="myChart-for-cost-analysis" style="height:320px;"></div>
                    </div>
                    <div class="col-md-4">
                        <div id="myChart-for-profile-rate" style="height:320px;"></div>
                    </div>

                </div>
            </div>


            <div class="box-footer" style="margin:4px 0;">
            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-css')
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
    <style>
        .myChart { width:100%;height:240px; }

        .tableArea table { width:100% !important; min-width:1200px; }
        .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }

        .datatable-search-row .input-group .time-picker-btn { width:30px; }
        .datatable-search-row .input-group .month_picker, .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
        .datatable-search-row .input-group select { width:100px; text-align:center; }
        .datatable-search-row .input-group .select2-container { width:120px; }

        .bg-inspected { background:#CBFB9D; }
        .bg-delivered { background:#8FEBE5; }
        .bg-group { background:#E2FCAB; }
        .bg-district { background:#F6C5FC; }
        .bg-service-customer { background:#C3FAF7; }

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
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
@include(env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-project-script')
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
                "iDisplayLength": {{ $length or 50 }},
                "ajax": {
                    'url': "{{ url('/statistic/statistic-get-data-for-project-of-daily-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="project-id"]').val();
                        d.name = $('input[name="project-name"]').val();
                        d.title = $('input[name="project-title"]').val();
                        d.keyword = $('input[name="project-keyword"]').val();
                        d.remark = $('input[name="project-remark"]').val();
                        d.description = $('input[name="project-description"]').val();
                        d.assign_start = $('input[name="project-start"]').val();
                        d.assign_ended = $('input[name="project-ended"]').val();
                        d.company = $('select[name="project-company"]').val();
                        d.channel = $('select[name="project-channel"]').val();
                        d.project = $('select[name="project-project"]').val();
                        d.month = $('input[name="project-month"]').val();
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
                "scrollX": true,
                "scrollY": false,
                // "scrollY": ($(document).height() - 448)+"px",
                "scrollCollapse": true,
                "fixedColumns": {

                    @if($me->department_district_id == 0)
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @else
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 5 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @endif
                },
                "showRefresh": true,
                "columnDefs": [
                        {{--                    @if(!in_array($me->user_type,[0,1,11]))--}}
                        @if($me->department_district_id != 0)
                    {
                        "targets": [0,7,8,9,10],
                        "visible": false,
                    }
                    @endif
                ],
                "columns": [
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "40px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "项目",
                        "data": "project_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','项目');
                                $(nTd).attr('data-key','project_id').attr('data-value',data);
                                if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    $(nTd).attr('data-option-name',row.project_er.name);
                                }
                                $(nTd).attr('data-column-name','项目');
                                if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
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
                        "title": "日期",
                        "data": 'assign_date',
                        "className": "text-center",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                // var $assign_time_value = '';
                                // if(data)
                                // {
                                //     var $date = new Date(data*1000);
                                //     var $year = $date.getFullYear();
                                //     var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                //     var $day = ('00'+($date.getDate())).slice(-2);
                                //     $assign_time_value = $year+'-'+$month+'-'+$day;
                                // }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','日期');
                                $(nTd).attr('data-key','assign_date').attr('data-value',data);
                                $(nTd).attr('data-column-name','日期');
                                $(nTd).attr('data-time-type','date');
                                $(nTd).attr('data-time-data-type','date');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(!data) return '';

                            // var $date = new Date(data*1000);
                            // var $year = $date.getFullYear();
                            // var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            // var $day = ('00'+($date.getDate())).slice(-2);
                            // var $hour = ('00'+$date.getHours()).slice(-2);
                            // var $minute = ('00'+$date.getMinutes()).slice(-2);
                            // var $second = ('00'+$date.getSeconds()).slice(-2);
                            //
                            // var $currentYear = new Date().getFullYear();
                            // if($year == $currentYear) return $month+'-'+$day;
                            // else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "title": "外呼后台",
                        "data": "outbound_background",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外呼后台');
                                $(nTd).attr('data-key','outbound_background').attr('data-value',data);
                                $(nTd).attr('data-column-name','外呼后台');
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
                        "title": "出勤人力",
                        "data": "attendance_manpower",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','出勤人力');
                                $(nTd).attr('data-key','attendance_manpower').attr('data-value',data);
                                $(nTd).attr('data-column-name','出勤人力');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "交付量",
                        "data": "delivery_quantity",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交付量');
                                $(nTd).attr('data-key','delivery_quantity').attr('data-value',data);
                                $(nTd).attr('data-column-name','交付量');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data < 0) return '<b class="text-red">' + parseFloat(data) + '</b>';
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "无效交付量",
                        "data": "delivery_quantity_of_invalid",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','无效交付量');
                                $(nTd).attr('data-key','delivery_quantity_of_invalid').attr('data-value',data);
                                $(nTd).attr('data-column-name','无效交付量');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data < 0) return '<b class="text-red">' + parseFloat(data) + '</b>';
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均交付",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";
                            var $delivery_quantity = parseFloat(row.delivery_quantity);
                            var $attendance_manpower = parseFloat(row.attendance_manpower);
                            var $per_value = parseFloat(($delivery_quantity) / ($attendance_manpower)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "人力成本",
                        "data": "manpower_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);
                            // var $manpower_cost = parseFloat(($manpower_daily_wage) * ($attendance_manpower)).toFixed(2);
                            // return parseFloat($manpower_cost);
                            return parseFloat(data);

                        }
                    },
                    {
                        "title": "当日话费",
                        "data": "call_charge_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','当日话费');
                                $(nTd).attr('data-key','call_charge_daily_cost').attr('data-value',data);
                                $(nTd).attr('data-column-name','当日话费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均话费",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";
                            var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            var $attendance_manpower = parseFloat(row.attendance_manpower);
                            var $per_value = parseFloat(($call_charge_daily_cost) / ($attendance_manpower)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "单均话费",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.delivery_quantity == 0) return "--";
                            var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            var $delivery_quantity = parseFloat(row.delivery_quantity);
                            var $per_value = parseFloat(($call_charge_daily_cost) / ($delivery_quantity)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "物料量",
                        "data": "material_daily_quantity",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            // var $material_quantity = parseFloat(($call_charge_coefficient) * ($call_charge_daily_cost)).toFixed(2);
                            // return parseFloat($material_quantity);
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "物料成本",
                        "data": "material_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = parseFloat(($call_charge_coefficient) * ($call_charge_daily_cost) * ($material_coefficient)).toFixed(2);
                            // return parseFloat($material_cost);
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "税费成本",
                        "data": "taxes_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = parseFloat(($delivery_quantity) * ($taxes_coefficient)).toFixed(2);
                            // return parseFloat($taxes_cost);
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总成本",
                        "data": "total_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            // return parseFloat($total_cost);

                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均成本",
                        "data": "total_daily_cost",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            //
                            // var $per_value = parseFloat($total_cost / $attendance_manpower).toFixed(2);
                            // return parseFloat($per_value);

                            var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            var $per_value = parseFloat(data / $attendance_manpower).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "单均成本",
                        "data": "total_daily_cost",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.delivery_quantity == 0) return "--";

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            //
                            // var $per_value = parseFloat($total_cost / $delivery_quantity).toFixed(2);
                            // return parseFloat($per_value);

                            var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            var $per_value = parseFloat(data / $delivery_quantity).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "人力日薪",
                        "data": 'manpower_daily_wage',
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','人力日薪');
                                $(nTd).attr('data-key','manpower_daily_wage').attr('data-value',data);
                                $(nTd).attr('data-column-name','人力日薪');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "话费系数",
                        "data": "call_charge_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','话费系数');
                                $(nTd).attr('data-key','call_charge_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','话费系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "物料系数",
                        "data": "material_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','物料系数');
                                $(nTd).attr('data-key','material_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','物料系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "税费系数",
                        "data": "taxes_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','税费系数');
                                $(nTd).attr('data-key','taxes_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','税费系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    // {
                    //     "title": "备注",
                    //     "data": "description",
                    //     "className": "",
                    //     "width": "200px",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','备注');
                    //             $(nTd).attr('data-key','description').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','备注');
                    //             $(nTd).attr('data-text-type','textarea');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="project-id"]').val())  $obj.order_id = $('input[name="project-id"]').val();
                    if($('input[name="project-assign"]').val())  $obj.assign = $('input[name="project-assign"]').val();
                    if($('input[name="project-start"]').val())  $obj.assign_start = $('input[name="project-start"]').val();
                    if($('input[name="project-ended"]').val())  $obj.assign_ended = $('input[name="project-ended"]').val();
                    if($('select[name="project-project"]').val() > 0)  $obj.project_id = $('select[name="project-project"]').val();
                    if($('input[name="project-month"]').val())  $obj.month = $('input[name="project-month"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != {{ $length or 50 }}) $obj.length = $page_length;
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
                        $url = "{{ url('/statistic/statistic-project') }}";
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


        $("#filter-submit-for-project-by-month").click();
    });
</script>
@endsection


