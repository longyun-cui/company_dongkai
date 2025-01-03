@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '财务报表' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')财务报表 - 财务系统 - {{ config('info.info.short_name') }}@endsection
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

            <div class="box-body datatable-body item-main-body" id="statistic-for-finance">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="finance-time-type" value="" readonly>

                        @if(in_array($me->user_type,[0,9,11,31]))
                        <select class="form-control form-filter select-select2 select2-box finance-company" name="finance-company" style="width:120px;">
                            <option value="-1">选择公司</option>
                            @if(!empty($company_list))
                                @foreach($company_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select class="form-control form-filter select-select2 select2-box finance-channel" name="finance-channel" style="width:120px;">
                            <option value="-1">选择代理</option>
                            @if(!empty($channel_list))
                                @foreach($channel_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select class="form-control form-filter select-select2 select2-box finance-business" name="finance-business" style="width:120px;">
                            <option value="-1">选择商务</option>
                            @if(!empty($business_list))
                                @foreach($business_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                                @endforeach
                            @endif
                        </select>
                        @endif
                        <select class="form-control form-filter select-select2 select2-box finance-project" name="finance-project" style="width:160px;">
                            <option value="-1">选择项目</option>
                            @if(!empty($project_list))
                                @foreach($project_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>


                        {{--全部查询--}}
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance-by-all">
                            <i class="fa fa-search"></i> 全部查询
                        </button>


                        {{--按月查询--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-finance">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="finance-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-finance">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按天查询--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-finance">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="finance-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-finance">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance-by-date">
                            <i class="fa fa-search"></i> 按日查询
                        </button>


                        {{--按时间段查询--}}
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="finance-start" placeholder="起始日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="finance-ended" placeholder="结束日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance-by-period" style="width:100px;">
                            <i class="fa fa-search"></i> 按时间段查询
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


        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">财务概览</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>累计充值</th>
                            <th>退款</th>
                            <th>应结算</th>
                            <th>已结算</th>
                            <th>坏账</th>
                            <th>待收款</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b class="recharge_total"></b></td>
                                <td><b class="recharge_refund_total"></b></td>
                                <td><b class="recharge_balance"></b></td>
                                <td><b class="using_settled_total"></b></td>
                                <td><b class="using_bad_total"></b></td>
                                <td><b class="using_refund_total"></b></td>
                                <td><b class="recharge_balance"></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix _none">
                <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div>
            <!-- /.box-footer -->
        </div>


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


        <div class="box box-primary bg-white">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">
                    <span class="statistic-title-">项目列表</span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-project">
                <div class="tableArea">
                    <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax_project'>
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
        .bg-finance-customer { background:#C3FAF7; }

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
@include(env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-finance-script')
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
                    'url': "{{ url('/statistic/statistic-get-data-for-finance-of-channel-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="finance-id"]').val();
                        d.name = $('input[name="finance-name"]').val();
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="finance-keyword"]').val();
                        d.remark = $('input[name="finance-remark"]').val();
                        d.description = $('input[name="finance-description"]').val();
                        d.status = $('select[name="finance-status"]').val();
                        d.company_category = $('select[name="finance-category"]').val();
                        d.company_type = $('select[name="finance-type"]').val();
                        d.work_status = $('select[name="work_status"]').val();
                        d.company = $('select[name="finance-company"]').val();
                        d.channel = $('select[name="finance-channel"]').val();
                        d.business = $('select[name="finance-business"]').val();
                        d.project = $('select[name="finance-project"]').val();
                        d.time_type = $('input[name="finance-time-type"]').val();
                        d.month = $('input[name="finance-month"]').val();
                        d.date = $('input[name="finance-date"]').val();
                        d.assign_start = $('input[name="finance-start"]').val();
                        d.assign_ended = $('input[name="finance-ended"]').val();
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
                        "title": "ID",
                        "data": "id",
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
                        "title": "所属公司",
                        "data": "superior_company_id",
                        "className": "",
                        "width":"100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.company_category == 1)
                            {
                                return '<a href="javascript:void(0);">'+row.name+'</a>';
                            }
                            else if(row.company_category == 11)
                            {
                                if(row.superior_company_er) {
                                    return '<a href="javascript:void(0);">'+row.superior_company_er.name+'</a>';
                                }
                                else return '--';
                            }
                            else return '--';
                        }
                    },
                    {
                        "title": "名称",
                        "data": "name",
                        "className": "text-center company-name",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.company_category == 1)
                            {
                                return '--';
                            }
                            else if(row.company_category == 11)
                            {
                                return '<a href="javascript:void(0);">'+data+'</a>';
                            }
                            else return '--';
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
                        "title": "无效交付量",
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
                        "data": "total_of_total_cost",
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
                        "data": "id",
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
                        "data": "total_of_profile",
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
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

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


<script>
    var TableDatatablesAjax_project = function () {
        var datatableAjax_project = function () {

            var dt = $('#datatable_ajax_project');
            var ajax_datatable_project = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[100, 200, -1], ["100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-get-data-for-finance-of-project-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="finance-id"]').val();
                        d.name = $('input[name="finance-name"]').val();
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="finance-keyword"]').val();
                        d.remark = $('input[name="finance-remark"]').val();
                        d.description = $('input[name="finance-description"]').val();
                        d.company = $('select[name="finance-company"]').val();
                        d.channel = $('select[name="finance-channel"]').val();
                        d.business = $('select[name="finance-business"]').val();
                        d.project = $('select[name="finance-project"]').val();
                        d.time_type = $('input[name="finance-time-type"]').val();
                        d.month = $('input[name="finance-month"]').val();
                        d.date = $('input[name="finance-date"]').val();
                        d.assign_start = $('input[name="finance-start"]').val();
                        d.assign_ended = $('input[name="finance-ended"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
//                 "scrollY": false,
                "scrollY": ($(window).height() - 300)+"px",
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
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
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment-');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value','');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                     {
//                         "title": "状态",
//                         "data": "item_status",
//                         "width": "60px",
//                         "orderable": false,
//                         render: function(data, type, row, meta) {
// //                            return data;
//                             if(row.deleted_at != null)
//                             {
//                                 return '<small class="btn-xs bg-black">已删除</small>';
//                             }
//
//                             if(data == 1)
//                             {
//                                 return '<small class="btn-xs btn-success">启用</small>';
//                             }
//                             else
//                             {
//                                 return '<small class="btn-xs btn-danger">禁用</small>';
//                             }
//                         }
//                     },
                    {
                        "title": "项目名称",
                        "data": "name",
                        "className": "text-center project-name",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "所属代理",
                        "data": "channel_id",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        },
                        render: function(data, type, row, meta) {
                            if(row.channel_er == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.channel_er.name+'</a>';
                        }
                    },
                    {
                        "title": "商务人员",
                        "data": "business_id",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.business_or == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.business_or.username+'</a>';
                        }
                    },
//                     {
//                         "title": "团队",
//                         "data": "pivot_project_team",
//                         "className": "text-center",
//                         "width": "160px",
//                         "orderable": false,
//                         "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                         },
//                         render: function(data, type, row, meta) {
//                             var html = '';
//                             $.each(data,function( key, val ) {
// //                                console.log( key, val, this );
//                                 html += '<a href="javascript:void(0);">'+this.name+'</a> &nbsp;';
//                             });
//                             return html;
//                         }
//                     },
                    {
                        "title": "总交付量",
                        "data": "total_delivery_quantity",
                        "className": "text-center bg-journey",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总无效量",
                        // "data": "delivery_invalid_quantity",
                        "data": "total_delivery_quantity_of_invalid",
                        "className": "text-center bg-journey",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set-');
                                $(nTd).attr('data-id',row.id).attr('data-name','总无效量');
                                $(nTd).attr('data-key','delivery_invalid_quantity').attr('data-value',data);
                                $(nTd).attr('data-column-name','总无效量');
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
                        "title": "总有效量",
                        "data": "delivery_effective_quantity",
                        "className": "text-center bg-journey",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.total_delivery_quantity - row.total_delivery_quantity_of_invalid;
                        }
                    },
                    {
                        "title": "工单成本",
                        "data": "total_cost",
                        "className": "text-center bg-route",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "单均成本",
                        "data": "id",
                        "className": "text-center bg-route",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $delivery_effective_quantity = row.total_delivery_quantity - row.delivery_invalid_quantity;
                            var $delivery_effective_quantity = row.total_delivery_quantity - row.total_delivery_quantity_of_invalid;
                            var $per = 0;
                            if($delivery_effective_quantity > 0)
                            {
                                $per = parseFloat(row.total_cost / $delivery_effective_quantity).toFixed(2);
                            }

                            return $per;
                        }
                    },
                    {
                        "title": "渠道单价",
                        "data": "channel_unit_price",
                        "className": "text-center bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','渠道单价');
                                $(nTd).attr('data-key','channel_unit_price').attr('data-value',data);
                                $(nTd).attr('data-column-name','渠道单价');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "渠道费用",
                        "data": "channel_cost",
                        "className": "text-center bg-income",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0)
                            {
                                var $channel_cost = row.channel_unit_price * row.total_delivery_quantity;
                                return parseFloat($channel_cost);
                            }
                            else return data;
                        }
                    },
                    {
                        "title": "合作单价",
                        "data": "cooperative_unit_price",
                        "className": "text-center bg-deduction",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','合作单价');
                                $(nTd).attr('data-key','cooperative_unit_price').attr('data-value',data);
                                $(nTd).attr('data-column-name','合作单价');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "应结算",
                        "data": "cooperative_cost",
                        "className": "text-center bg-deduction",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0)
                            {
                                // // var $delivery_effective_quantity = row.total_delivery_quantity - row.delivery_invalid_quantity;
                                // var $delivery_effective_quantity = row.total_delivery_quantity - row.total_delivery_quantity_of_invalid;
                                // var $settlement_amount = row.cooperative_unit_price * $delivery_effective_quantity;
                                // if($settlement_amount == 0)  return "--";
                                // else return parseFloat($settlement_amount);
                                return "--";
                            }
                            else return data;
                        }
                    },
                    {
                        "title": "已结算",
                        "data": "funds_already_settled_total",
                        "className": "item-show-for-settle bg-empty",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(true)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','已结算');
                                $(nTd).attr('data-key','funds_already_settled_total').attr('data-value',data);
                                $(nTd).attr('data-column-name','已结算');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "坏账",
                        "data": 'funds_bad_debt_total',
                        "className": "item-show-for-settle bg-empty",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
                            if(!data) return "--";
                            else if(data == 0 || data == 0.00) return "--";
                            else return parseFloat(data);
                        }
                    },
                    {
                        "title": "待收款",
                        "data": "balance",
                        "className": "text-center bg-empty",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // 应结算金额
                            // // var $delivery_effective_quantity = row.total_delivery_quantity - row.delivery_invalid_quantity;
                            // var $delivery_effective_quantity = row.total_delivery_quantity - row.total_delivery_quantity_of_invalid;
                            // var $settlement_amount = parseFloat(row.cooperative_unit_price * $delivery_effective_quantity);
                            var $settlement_amount = row.cooperative_cost;
                            // 已结算金额
                            var $funds_already_settled_total = parseFloat(row.funds_already_settled_total);
                            // 坏账金额
                            var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);

                            var $balance = parseFloat($settlement_amount - $funds_already_settled_total - $funds_bad_debt_total);
                            if(parseFloat($balance) == 0) return '--';
                            else if(parseFloat($balance) > 0) return '<b class="text-red">' + parseFloat($balance) + '</b>';
                            else return parseFloat($balance);
                        }
                    },
                    {
                        "title": "利润",
                        "data": "id",
                        "className": "text-center bg-finance",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // 应结算金额
                            // // var $delivery_effective_quantity = row.total_delivery_quantity - row.delivery_invalid_quantity;
                            // var $delivery_effective_quantity = row.total_delivery_quantity - row.total_delivery_quantity_of_invalid;
                            // var $settlement_amount = row.cooperative_unit_price * $delivery_effective_quantity;
                            var $settlement_amount = row.cooperative_cost;
                            // 总成本
                            var $total_cost = row.total_cost;
                            // 渠道费用
                            // var $channel_cost = row.channel_unit_price * row.total_delivery_quantity;
                            // var $channel_cost = row.channel_cost;
                            var $channel_cost = 0;
                            if(row.channel_cost == 0)
                            {
                                $channel_cost = parseFloat(row.channel_unit_price * row.total_delivery_quantity);
                            }
                            else $channel_cost = row.channel_cost;

                            var $profile = parseFloat($settlement_amount - $total_cost - $channel_cost).toFixed(2);
                            if(parseFloat($profile) < 0) return '<b class="text-red">' + parseFloat($profile) + '</b>';
                            else  return '<b class="text-green">' + parseFloat($profile) + '</b>';
                        }
                    },
                ],
                // "footerCallback": function ( row, data, start, end, display ) {
                //     var data = ajax_datatable.search();
                //     var ds = ajax_datatable.search(data).context[0].aiDisplay;
                //     var Sum = 0;
                //
                //     $.each(ds, function (i, e) {
                //         var data = ajax_datatable.row(e).data();
                //         Sum = Number(Sum) + Number(data.total_delivery_quantity_of_invalid);
                //     });
                //     $(".Sum").html(Sum);
                // },
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });
                },

                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable_project.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable_project.ajax.reload();
            });

        };
        return {
            init: datatableAjax_project
        }
    }();
    $(function () {
        TableDatatablesAjax_project.init();
    });
</script>
@endsection


