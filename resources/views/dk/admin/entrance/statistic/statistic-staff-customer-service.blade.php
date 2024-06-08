@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $staff->username or '员工看板' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '员工看板' }} - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info main-list-body">

                <div class="box-header with-border" style="margin:4px 0;">
                    <h3 class="box-title">
                        <span class="statistic-title-">员工</span>
                        【<span class="statistic-title-">{{ $staff->username or '' }}</span>】
                        <span class="statistic-time-type-title"></span>
                        <span class="statistic-time-title">{{ date('Y-m').'月' }}</span>
                    </h3>
                </div>


                <div class="box-body datatable-body item-main-body" id="statistic-for-rank">

                    <div class="row col-md-12 datatable-search-row">
                        <div class="input-group">

                            {{--按月查看--}}
                            <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-rank">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <input type="text" class="form-control form-filter filter-keyup month_picker" name="rank-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                            <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-rank">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank-by-month">
                                <i class="fa fa-search"></i> 按月排名
                            </button>


                            {{--按天查看--}}
{{--                            <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-rank">--}}
{{--                                <i class="fa fa-chevron-left"></i>--}}
{{--                            </button>--}}
{{--                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="rank-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                            <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-rank">--}}
{{--                                <i class="fa fa-chevron-right"></i>--}}
{{--                            </button>--}}
{{--                            <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank-by-day">--}}
{{--                                <i class="fa fa-search"></i> 按天查询--}}
{{--                            </button>--}}


                            {{--全部查询--}}
{{--                            <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank">--}}
{{--                                <i class="fa fa-search"></i> 总量排名--}}
{{--                            </button>--}}
                            <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-rank">
                                <i class="fa fa-circle-o-notch"></i> 重置
                            </button>

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


                <div class="box-footer _none">
                    <div class="row" style="margin:16px 0;">
                        <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                            {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                            {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                            <div class="input-group">
                                <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                                <select name="bulk-operate-status" class="form-control form-filter">
                                    <option value ="-1">请选择操作类型</option>
                                    <option value ="启用">启用</option>
                                    <option value ="禁用">禁用</option>
                                    <option value ="删除">删除</option>
                                    <option value ="彻底删除">彻底删除</option>
                                </select>
                                <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                                <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                            </div>
                        </div>
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
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
    <style>
        .myChart { width:100%;height:240px; }
        .tableArea table { min-width:1360px; }
        .datatable-search-row .input-group .time-picker-btn { width:30px; }
        .datatable-search-row .input-group .month_picker, .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
        .datatable-search-row .input-group select { width:100px; text-align:center; }
        .datatable-search-row .input-group .select2-container { width:120px; }

        .bg-inspected { background:#CBFB9D; }
        .bg-delivered { background:#8FEBE5; }
        .bg-group { background:#E2FCAB; }
        .bg-district { background:#F6C5FC; }
        .bg-journey { background:#C3FAF7; }
    </style>
@endsection




@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-department-script')
    <script>
        var TableDatatablesAjax = function () {
            var datatableAjax = function () {

                var dt = $('#datatable_ajax');
                var ajax_datatable = dt.DataTable({
                    // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                    "aLengthMenu": [[-1], ["全部"]],
                    "processing": true,
                    "serverSide": false,
                    "searching": false,
                    "ajax": {
                        'url': "{{ url('/staff-statistic/statistic-customer-service?staff_id='.$staff->id) }}",
                        "type": 'POST',
                        "dataType" : 'json',
                        "data": function (d) {
                            d._token = $('meta[name="_token"]').attr('content');
                            d.id = $('input[name="rank-id"]').val();
                            d.name = $('input[name="rank-name"]').val();
                            d.title = $('input[name="rank-title"]').val();
                            d.keyword = $('input[name="rank-keyword"]').val();
                            d.status = $('select[name="rank-status"]').val();
                            d.time_type = $('input[name="rank-time-type"]').val();
                            d.time_month = $('input[name="rank-month"]').val();
                            d.time_date = $('input[name="rank-date"]').val();
                            d.rank_object_type = $('select[name="rank-object-type"]').val();
                            d.rank_staff_type = $('select[name="rank-staff-type"]').val();
                            d.department_district = $('select[name="rank-department-district"]').val();
                            d.department_group = $('select[name="rank-department-group"]').val();
                        },
                    },
                    "paging": false,
                    "pagingType": "simple_numbers",
                    "order": [0,'asc'],
                    "orderCellsTop": true,
                    "columns": [
//                    {
//                        "title": "选择",
//                        "data": "id",
//                        "width": "32px",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "title": "序号",
//                        "data": null,
//                        "width": "32px",
//                        "targets": 0,
//                        "orderable": false
//                    },
                        {
                            "title": "日期",
                            "data": "date_day",
                            "className": "text-center",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return row.date_day;
                            }
                        },


                        {
                            "title": "报单量",
                            "data": "order_count_for_all",
                            "className": "bg-inspected",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },


                        // {
                        //     "title": "交付<br>总量",
                        //     "data": "order_count_for_delivered",
                        //     "className": "bg-delivered",
                        //     "width": "80px",
                        //     "orderable": true,
                        //     "orderSequence": ["desc", "asc"],
                        //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        //         if(row.id == "统计")
                        //         {
                        //             $(nTd).addClass('_bold');
                        //         }
                        //     },
                        //     render: function(data, type, row, meta) {
                        //         return data;
                        //     }
                        // },
                        {
                            "title": "有效量",
                            "data": "order_count_for_delivered_effective",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "实际产出",
                            "data": "order_count_for_delivered_actual",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "已交付",
                            "data": "order_count_for_delivered_completed",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "隔日交付",
                            "data": "order_count_for_delivered_tomorrow",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "内部交付",
                            "data": "order_count_for_delivered_inside",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "重复",
                            "data": "order_count_for_delivered_repeated",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "拒绝",
                            "data": "order_count_for_delivered_rejected",
                            "className": "bg-delivered",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "有效交付率",
                            "data": "order_rate_for_delivered_effective",
                            "className": "bg-delivered",
                            "width": "100px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(data) return data + " %";
                                return data
                            }
                        },


                        {
                            "title": "审核通过量",
                            "data": "order_count_for_accepted",
                            "className": "bg-inspected",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "审核拒绝量",
                            "data": "order_count_for_refused",
                            "className": "bg-inspected",
                            "width": "80px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "审核通过率",
                            "data": "order_rate_for_accepted",
                            "className": "bg-inspected",
                            "width": "100px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.id == "统计")
                                {
                                    $(nTd).addClass('_bold');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(data) return data + " %";
                                return data
                            }
                        }
                    ],
                    "drawCallback": function (settings) {

                        // console.log(this.api());

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });


                        $('.lightcase-image').lightcase({
                            maxWidth: 9999,
                            maxHeight: 9999
                        });

                    },
                    "columnDefs": [
                        // {
                        //     "targets": [2],
                        //     "orderData": [2,3]
                        // },
                        // {
                        //     "targets": [3],
                        //     "orderData": [3,2]
                        // },
                        // {
                        //     "targets": [4],
                        //     "orderData": [4,5]
                        // },
                        // {
                        //     "targets": [5],
                        //     "orderData": [5,4]
                        // },
                        // {
                        //     "targets": [6],
                        //     "orderData": [6,2]
                        // }
                    ],
                    "language": { url: '/common/dataTableI18n' },
                });

                dt.on('click', '.order-all', function () {
                    // ajax_datatable.column(2).order('desc,asc');
                    // ajax_datatable.sort([2,'desc|asc']).page(0).draw(false);
                    // ajax_datatable.sort([2,'desc|asc']);
                    // ajax_datatable.column(2)
                    //     .data()
                    //     .sort().page().draw(false);
                    // var $data = ajax_datatable
                    //     .column(2)
                    //     .order('asc');
                    // console.log($data);
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


