@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '项目月报' }} - 财务系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')项目月报 - 财务系统 - {{ config('info.info.short_name') }}@endsection
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
                    <span class="statistic-title">{{ $project_name or '' }}</span>
                    <span class="statistic-time-type-title"></span>
                    <span class="statistic-time-title"></span>
                </h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="statistic-for-monthly">


                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="monthly-time-type" value="" readonly>

                        <select class="form-control form-filter select-select2 select2-box monthly-project" name="monthly-project" style="width:160px;">
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


                        {{--全部查询--}}
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-monthly">
                            <i class="fa fa-search"></i> 查询
                        </button>
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-monthly">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-monthly">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-monthly">
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
@include(env('TEMPLATE_DK_FINANCE').'entrance.statistic.statistic-monthly-by-project-script')
<script>
    var TableDatatablesAjax_project = function () {
        var datatableAjax_project = function () {

            var dt = $('#datatable_ajax_project');
            var ajax_datatable_project = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1, 100, 200], ["全部", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-monthly-by-project') }}",
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
                        "data": "formatted_year_month",
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
                        "title": "总交付量",
                        "data": "total_of_delivery_quantity",
                        "className": "bg-journey",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总无效量",
                        // "data": "delivery_invalid_quantity",
                        "data": "total_of_delivery_quantity_of_invalid",
                        "className": "bg-journey",
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
                        "data": "total_of_delivery_quantity_of_invalid",
                        "className": "bg-journey",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.total_of_delivery_quantity - row.total_of_delivery_quantity_of_invalid;
                        }
                    },
                    {
                        "title": "出席人力",
                        "data": "total_of_attendance_manpower",
                        "className": "bg-journey",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "工单成本",
                        "data": "total_cost",
                        "className": "bg-route",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return parseFloat(parseFloat(data).toFixed(2));
                        }
                    },
                    {
                        "title": "单均成本",
                        "data": "formatted_year_month",
                        "className": "bg-route",
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
                        "className": "bg-income",
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
                        "data": "channel_unit_price",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0)
                            {
                                var $channel_cost = row.channel_unit_price * row.total_of_delivery_quantity;
                                return parseFloat($channel_cost);
                            }
                            else return data;
                        }
                    },
                    {
                        "title": "合作单价",
                        "data": "cooperative_unit_price",
                        "className": "bg-deduction",
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
                        "data": "cooperative_unit_price",
                        "className": "bg-deduction",
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
                            // else return data;
                            return data * (row.total_of_delivery_quantity - row.total_of_delivery_quantity_of_invalid);
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
                        "data": "formatted_year_month",
                        "className": "bg-empty",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // 应结算金额
                            var $delivery_quantity_of_effective = row.total_of_delivery_quantity - row.total_of_delivery_quantity_of_invalid;
                            var $funds_should_settled_total = parseFloat(row.cooperative_unit_price * $delivery_quantity_of_effective);
                            // 已结算金额
                            var $funds_already_settled_total = parseFloat(row.funds_already_settled_total);
                            // 坏账金额
                            var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);

                            var $balance = parseFloat($funds_should_settled_total - $funds_already_settled_total - $funds_bad_debt_total);
                            if(parseFloat($balance) == 0) return '--';
                            else if(parseFloat($balance) > 0) return '<b class="text-red">' + parseFloat($balance) + '</b>';
                            else return parseFloat($balance);
                        }
                    },
                    {
                        "title": "利润",
                        "data": "formatted_year_month",
                        "className": "bg-finance",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // 应结算金额
                            var $delivery_quantity_of_effective = row.total_of_delivery_quantity - row.total_of_delivery_quantity_of_invalid;
                            var $funds_should_settled_total = parseFloat(row.cooperative_unit_price * $delivery_quantity_of_effective);
                            // 工单成本
                            var $total_cost = parseFloat(row.total_cost);
                            // 渠道费用
                            var $channel_cost = parseFloat(row.channel_unit_price * row.total_of_delivery_quantity);
                            // 坏账金额
                            var $funds_bad_debt_total = parseFloat(row.funds_bad_debt_total);

                            var $profile = parseFloat($funds_should_settled_total - $total_cost - $channel_cost - $funds_bad_debt_total).toFixed(2);
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


                    var $obj = new Object();
                    if($('select[name="monthly-project"]').val() > 0) $obj.project_id = $('select[name="monthly-project"]').val();
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
                        $url = "{{ url('/statistic/statistic-monthly-by-project') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }
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


