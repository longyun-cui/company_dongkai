@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '渠道报表' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')渠道报表 - 财务系统 - {{ config('info.info.short_name') }}@endsection
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

            <div class="box-body datatable-body item-main-body" id="statistic-for-company">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="company-time-type" value="" readonly>

                        @if(in_array($me->user_type,[0,9,11,31]))
                        <select class="form-control form-filter select-select2 select2-box company-company" name="company-company" style="width:160px;">
                            <option value="-1">选择公司</option>
                            @if(!empty($company_list))
                                @foreach($company_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select class="form-control form-filter select-select2 company-channel" name="company-channel" style="width:160px;">
                            <option value="-1">选择渠道</option>
                            @if(!empty($channel_list))
                                @foreach($channel_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @endif

                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-company">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="company-month" placeholder="选择月份" readonly="readonly" value_="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-company">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-company-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按天查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-company">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="company-date" placeholder="选择日期" readonly="readonly" value_="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-company">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-company-by-day">
                            <i class="fa fa-search"></i> 按日查询
                        </button>


{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-company">--}}
{{--                            <i class="fa fa-search"></i> 全部查询--}}
{{--                        </button>--}}
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-company">
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
                    <span class="statistic-title-">项目列表</span>
                </h3>
            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-channel">

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
@include(env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-company-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[100, 200, -1], ["100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-get-data-for-company-of-project-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="company-id"]').val();
                        d.name = $('input[name="company-name"]').val();
                        d.title = $('input[name="company-title"]').val();
                        d.keyword = $('input[name="company-keyword"]').val();
                        d.remark = $('input[name="company-remark"]').val();
                        d.description = $('input[name="company-description"]').val();
                        d.company = $('select[name="company-company"]').val();
                        d.channel = $('select[name="company-channel"]').val();
                        d.month = $('input[name="company-month"]').val();
                        d.date = $('input[name="company-date"]').val();
                        d.assign_start = $('input[name="company-start"]').val();
                        d.assign_ended = $('input[name="company-ended"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
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
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "所属渠道",
                        "data": "channel_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        },
                        render: function(data, type, row, meta) {
                            if(row.channel_er == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.channel_er.name+'</a>';
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
                        "title": "总成本",
                        "data": "total_cost",
                        "className": "text-center bg-route",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
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
                                var $channel_unit_price = row.channel_unit_price * row.total_delivery_quantity;
                                return parseFloat($channel_unit_price);
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
                            var $channel_unit_price = row.channel_unit_price * row.total_delivery_quantity;

                            var $profile = parseFloat($settlement_amount - $total_cost - $channel_unit_price).toFixed(2);
                            if(parseFloat($profile) < 0) return '<b class="text-red">' + parseFloat($profile) + '</b>';
                            else  return '<b class="text-green">' + parseFloat($profile) + '</b>';
                        }
                    },
                    {
                        "title": "已结算",
                        "data": "settled_amount",
                        "className": "item-show-for-settle bg-empty",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(true)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','已结算');
                                $(nTd).attr('data-key','settled_amount').attr('data-value',data);
                                $(nTd).attr('data-column-name','已结算');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "余额",
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
                            var $settled_amount = parseFloat(row.settled_amount);
                            var $balance = parseFloat($settled_amount - $settlement_amount);
                            if(parseFloat($balance) == 0) return '--';
                            else if(parseFloat($balance) < 0) return '<b class="text-red">' + parseFloat($balance) + '</b>';
                            else return parseFloat($balance);
                        }
                    },
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var data = ajax_datatable.search();
                    var ds = ajax_datatable.search(data).context[0].aiDisplay;
                    var Sum = 0;

                    $.each(ds, function (i, e) {
                        var data = ajax_datatable.row(e).data();
                        Sum = Number(Sum) + Number(data.total_delivery_quantity_of_invalid);
                    });
                    $(".Sum").html(Sum);
                },
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });
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
@endsection


