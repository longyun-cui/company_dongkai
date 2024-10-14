@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '代理月报' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')代理月报 - 财务系统 - {{ config('info.info.short_name') }}@endsection
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
                    <span class="statistic-time-title"></span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-finance">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="monthly-time-type" value="" readonly>

                        <select class="form-control form-filter select-select2 select2-box monthly-channel" name="monthly-channel" style="width:160px;">
                            <option value="-1">选择代理</option>
                            @if(!empty($channel_list))
                                @foreach($channel_list as $v)
                                    @if(!empty($channel_id))
                                        @if($v->id == $channel_id)
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


                        {{--全部查询--}}
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-monthly">
                            <i class="fa fa-search"></i> 查询
                        </button>
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-finance">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-finance">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-finance">
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


        @if(in_array($me->user_type,[0,1,9,11,31]))
        <div class="box box-primary bg-white">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">
                    <span class="statistic-title-">代理列表</span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-channel">
                <div class="tableArea">
                    <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax_channel'>
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
        @endif

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
        .bg-monthly-customer { background:#C3FAF7; }

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
@include(env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-monthly-by-channel-script')
<script>
    var TableDatatablesAjax_channel = function () {
        var datatableAjax_channel = function () {

            var dt = $('#datatable_ajax_channel');
            var ajax_datatable_channel = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-monthly-by-channel') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="monthly-id"]').val();
                        d.name = $('input[name="monthly-name"]').val();
                        d.title = $('input[name="monthly-title"]').val();
                        d.keyword = $('input[name="monthly-keyword"]').val();
                        d.remark = $('input[name="monthly-remark"]').val();
                        d.description = $('input[name="monthly-description"]').val();
                        d.status = $('select[name="monthly-status"]').val();
                        d.company_category = $('select[name="monthly-category"]').val();
                        d.company_type = $('select[name="monthly-type"]').val();
                        d.work_status = $('select[name="work_status"]').val();
                        d.company = $('select[name="monthly-company"]').val();
                        d.channel = $('select[name="monthly-channel"]').val();
                        d.business = $('select[name="monthly-business"]').val();
                        d.project = $('select[name="monthly-project"]').val();
                        d.time_type = $('input[name="monthly-time-type"]').val();
                        d.month = $('input[name="monthly-month"]').val();
                        d.date = $('input[name="monthly-date"]').val();
                        d.assign_start = $('input[name="monthly-start"]').val();
                        d.assign_ended = $('input[name="monthly-ended"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                    "rightColumns": "0"
                },
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
                        "title": "月份",
                        "data": "formatted_year_month",
                        "className": "",
                        "width": "50px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "人力数量",
                        "data": "total_of_attendance_manpower",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "交付量",
                        "data": "total_of_delivery_quantity",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(data);
                        }
                    },
                    {
                        "title": "无效量",
                        "data": "total_of_delivery_quantity_of_invalid",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data));
                        }
                    },
                    {
                        "title": "有效量",
                        "data": "total_of_delivery_quantity_of_effective",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data));
                        }
                    },
                    {
                        "title": "话费",
                        "data": "total_of_call_charge_daily_cost",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "工单成本",
                        "data": "total_of_daily_cost",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "渠道成本",
                        "data": "total_of_channel_cost",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return formatWithCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "总成本",
                        "data": "total_of_all_cost",
                        "className": "all_cost",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "总营收",
                        "data": "total_of_revenue",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "已收款",
                        "data": "total_of_already_settled",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "坏账",
                        "data": "total_of_bad_debt",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return moneyAddCommas(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "待收款",
                        "data": "formatted_year_month",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $a = parseFloat(row.total_of_revenue - row.total_of_already_settled - row.total_of_bad_debt).toFixed(2);
                            if($a == 0) return "--";
                            return moneyAddCommas($a);
                        }
                    },
                    {
                        "title": "总利润",
                        "data": "formatted_year_month",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(['总计','合计'].includes(row.id))
                            {
                                $(nTd).addClass('_bold text-green');
                            }
                        },
                        render: function(data, type, row, meta) {
                            // 应结算金额
                            var $funds_should_settled_total = parseFloat(row.total_of_revenue);
                            // 工单成本
                            var $total_of_daily_cost = parseFloat(row.total_of_daily_cost);
                            // 渠道费用
                            var $total_of_channel_cost = parseFloat(row.total_of_channel_cost);
                            // 坏账金额
                            var $funds_bad_debt_total = parseFloat(row.total_of_bad_debt);

                            var $profile = parseFloat($funds_should_settled_total - $total_of_daily_cost - $total_of_channel_cost - $funds_bad_debt_total).toFixed(2);
                            if(parseFloat($profile) < 0) return '<b class="text-red">' + parseFloat($profile) + '</b>';
                            else  return '<b class="text-green">' + parseFloat($profile) + '</b>';

                        }
                    },
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });


                    var $obj = new Object();
                    if($('select[name="monthly-channel"]').val() > 0) $obj.channel_id = $('select[name="monthly-channel"]').val();
                    // console.log($('select[name="monthly-project"]').val());

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != {{ $length or -1 }}) $obj.length = $page_length;
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
                        $url = "{{ url('/statistic/statistic-monthly-by-channel') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable_channel.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable_channel.ajax.reload();
            });

        };
        return {
            init: datatableAjax_channel
        }
    }();
    $(function () {
        TableDatatablesAjax_channel.init();
    });
</script>
@endsection


