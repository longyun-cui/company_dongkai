@extends(env('TEMPLATE_DK_CUSTOMER').'layout.layout')


@section('head_title')
    {{ $title_text or '交付日报' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')交付日报 - 客户系统 - {{ config('info.info.short_name') }}@endsection
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
                    <span class="statistic-title">全部</span>
                    <span class="statistic-time-type-title"></span>
                    <span class="statistic-time-title">（全部）</span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-delivery">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="delivery-time-type" value="" readonly>





                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-delivery">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="delivery-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-delivery">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-delivery-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


{{--                        --}}{{--按天查看--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-delivery">--}}
{{--                            <i class="fa fa-chevron-left"></i>--}}
{{--                        </button>--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="delivery-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-delivery">--}}
{{--                            <i class="fa fa-chevron-right"></i>--}}
{{--                        </button>--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-delivery-by-date">--}}
{{--                            <i class="fa fa-search"></i> 按日查询--}}
{{--                        </button>--}}


{{--                        --}}{{--按时间段查看--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="delivery-start" placeholder="起始日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="delivery-ended" placeholder="结束日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-delivery-by-period" style="width:100px;">--}}
{{--                            <i class="fa fa-search"></i> 按时间段查询--}}
{{--                        </button>--}}


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-delivery">
                            <i class="fa fa-search"></i> 全部查询
                        </button>
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-delivery">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-delivery">--}}
{{--                            <i class="fa fa-search"></i> 搜索--}}
{{--                        </button>--}}
                        <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-delivery">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-delivery">
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


        <div class="box box-primary bg-white">


            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">
                    <span class="statistic-title-">日报列表</span>
                </h3>
            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-daily">

                <div class="tableArea">
                    <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax_daily'>
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
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
        .bg-delivery-customer { background:#C3FAF7; }

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
@include(env('TEMPLATE_DK_CUSTOMER').'entrance.statistic.statistic-delivery-script')
<script>
    var TableDatatablesAjax_daily = function () {
        var datatableAjax_daily = function () {

            var dt = $('#datatable_ajax_daily');
            var ajax_datatable_daily = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[ @if(!in_array($length,[50, 100, 200, -1])) {{ $length.',' }} @endif 50, 100, 200, -1], [ @if(!in_array($length,[50, 100, 200, -1])) {{ $length.',' }} @endif "50", "100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 50 }},
                "ajax": {
                    'url': "{{ url('/statistic/statistic-get-data-for-delivery-of-daily-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="delivery-id"]').val();
                        d.name = $('input[name="delivery-name"]').val();
                        d.title = $('input[name="delivery-title"]').val();
                        d.keyword = $('input[name="delivery-keyword"]').val();
                        d.remark = $('input[name="delivery-remark"]').val();
                        d.description = $('input[name="delivery-description"]').val();
                        d.assign_start = $('input[name="delivery-start"]').val();
                        d.assign_ended = $('input[name="delivery-ended"]').val();
                        d.company = $('select[name="delivery-company"]').val();
                        d.channel = $('select[name="delivery-channel"]').val();
                        d.business = $('select[name="delivery-business"]').val();
                        d.project = $('select[name="delivery-project"]').val();
                        d.time_type = $('input[name="delivery-time-type"]').val();
                        d.month = $('input[name="delivery-month"]').val();
                        d.date = $('input[name="delivery-date"]').val();
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
                "scrollY": ($(window).height() - 300)+"px",
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
                        "title": "日期",
                        "data": 'formatted_date',
                        "className": "",
                        "width": "80px",
                        "orderable": false,
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
                        }
                    },
                    {
                        "title": "交付量",
                        "data": "total_of_count",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交付量');
                                $(nTd).attr('data-key','total_of_count').attr('data-value',data);
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
                        "title": "已分配",
                        "data": "total_of_assign",
                        "className": "bg-fee-2",
                        "width": "120px",
                        "orderable": false,
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
                    if($('input[name="delivery-id"]').val())  $obj.order_id = $('input[name="delivery-id"]').val();
                    if($('select[name="delivery-company"]').val() > 0)  $obj.company_id = $('select[name="delivery-company"]').val();
                    if($('select[name="delivery-channel"]').val() > 0)  $obj.channel_id = $('select[name="delivery-channel"]').val();
                    if($('select[name="delivery-project"]').val() > 0)  $obj.project_id = $('select[name="delivery-project"]').val();

                    if($('input[name="delivery-time-type"]').val())  $obj.time_type = $('input[name="delivery-time-type"]').val();
                    if($('input[name="delivery-month"]').val())  $obj.month = $('input[name="delivery-month"]').val();
                    if($('input[name="delivery-date"]').val())  $obj.date = $('input[name="delivery-date"]').val();
                    if($('input[name="delivery-assign"]').val())  $obj.assign = $('input[name="delivery-assign"]').val();
                    if($('input[name="delivery-start"]').val())  $obj.assign_start = $('input[name="delivery-start"]').val();
                    if($('input[name="delivery-ended"]').val())  $obj.assign_ended = $('input[name="delivery-ended"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != {{ $length or 20 }}) $obj.length = $page_length;
                    var $page_start = this.api().context[0]._iDisplayStart; // 当前页开始
                    var $pagination = ($page_start / $page_length) + 1; //得到页数值 比页码小1
                    if($pagination > 1) $obj.page = $pagination;


                    {{--if(JSON.stringify($obj) != "{}")--}}
                    {{--{--}}
                    {{--    var $url = url_build('',$obj);--}}
                    {{--    history.replaceState({page: 1}, "", $url);--}}
                    {{--}--}}
                    {{--else--}}
                    {{--{--}}
                    {{--    $url = "{{ url('/statistic/statistic-delivery') }}";--}}
                    {{--    if(window.location.search) history.replaceState({page: 1}, "", $url);--}}
                    {{--}--}}

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable_daily.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable_daily.ajax.reload();
            });

        };
        return {
            init: datatableAjax_daily
        }
    }();

    $(function () {

        var $id = $.getUrlParam('id');
        if($id) $('input[name="order-id"]').val($id);
        TableDatatablesAjax_daily.init();
        // $('#datatable_ajax').DataTable().init().fnPageChange(3);


        $("#filter-submit-for-delivery-by-month").click();
    });
</script>
@endsection


