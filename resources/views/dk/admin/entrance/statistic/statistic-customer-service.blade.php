@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '客服看板' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '客服看板' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '客服看板' }}</span>@endsection
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">客服看板(<span class="statistic-title">全部</span>)</h3>
            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-customer-service">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="customer-service-time-type" value="" readonly>

                        <select class="form-control form-filter select2-container select2-project" name="customer-service-project" style="width:160px;">
                            <option value="-1">选择项目</option>
                        </select>

                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-customer-service">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="customer-service-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-customer-service">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-customer-service-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按天查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-customer-service">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="customer-service-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-customer-service">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-customer-service-by-day">
                            <i class="fa fa-search"></i> 按日查询
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-customer-service">
                            <i class="fa fa-search"></i> 全部查询
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-customer-service">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                    <table class='table table-striped- table-bordered table-hover order-column' id='datatable_ajax'>
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
    .tableArea table { min-width:1600px; }
    .datatable-search-row .input-group .time-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker, .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group select { width:100px; text-align:center; }
    .datatable-search-row .input-group .select2-container { width:120px; }

    .bg-inspected { background:#CBFB9D; }
    .bg-delivered { background:#8FEBE5; }
    .bg-group { background:#F6C5FC; }
    .bg-district { background:#E2FCAB; }
    .bg-journey { background:#C3FAF7; }
</style>
@endsection




@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
@include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-customer-service-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
                // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/statistic/statistic-customer-service') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="customer-service-id"]').val();
                        d.name = $('input[name="customer-service-name"]').val();
                        d.title = $('input[name="customer-service-title"]').val();
                        d.keyword = $('input[name="customer-service-keyword"]').val();
                        d.status = $('select[name="customer-service-status"]').val();
                        d.time_type = $('input[name="customer-service-time-type"]').val();
                        d.time_month = $('input[name="customer-service-month"]').val();
                        d.time_date = $('input[name="customer-service-date"]').val();
                        d.project = $('select[name="customer-service-project"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                // "fixedColumns": {
                    {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
                    {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
                // },
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
                        "title": "大区经理",
                        "data": "department_district_id",
                        "className": "vertical-middle",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.department_district_er.leader == null ? '未知' : '<a href="javascript:void(0);">' + row.department_district_er.name + '</a>' + '<br>' + '<a href="javascript:void(0);">' + row.department_district_er.leader.username + '</a>';
                        }
                    },
                    {
                        "title": "部门主管",
                        "data": "department_group_id",
                        "className": "vertical-middle",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.department_group_er.leader == null ? '未知' : '<a href="javascript:void(0);">' + row.department_group_er.name + '</a>' + '<br>' + '<a href="javascript:void(0);">' + row.department_group_er.leader.username + '</a>';
                        }
                    },
                    // {s
                    //     "title": "ID",
                    //     "data": "id",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": true,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "姓名",
                        "data": "username",
                        "className": "text-center",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + row.id + '" target="_blank">'+data+' ('+row.id+')'+'</a>';
                        }
                    },
                    {
                        "title": "客服<br>报单量",
                        "data": "order_count_for_all",
                        "className": "bg-inspected",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                           return data;
                        }
                    },


                    // {
                    //     "title": "交付<br>总量",
                    //     "data": "order_count_for_delivered",
                    //     "className": "bg-delivered",
                    //     "width": "100px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data
                    //     }
                    // },
                    {
                        "title": "交付<br>有效量",
                        "data": "order_count_for_delivered_effective",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "交付<br>实际产量",
                        "data": "order_count_for_delivered_actual",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "交付<br>已交付",
                        "data": "order_count_for_delivered_completed",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "交付<br>隔日交付",
                        "data": "order_count_for_delivered_tomorrow",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "交付<br>内部交付",
                        "data": "order_count_for_delivered_inside",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "交付<br>重复",
                        "data": "order_count_for_delivered_repeated",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "交付<br>驳回",
                        "data": "order_count_for_delivered_rejected",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "交付<br>有效率",
                        "data": "order_rate_for_delivered_effective",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },


                    // {
                    //     "title": "审核<br>有效量",
                    //     "data": "order_count_for_effective",
                    //     "className": "bg-inspected",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "审核<br>通过量",
                        "data": "order_count_for_accepted",
                        "className": "bg-inspected",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    // {
                    //     "title": "审核<br>内部通过",
                    //     "data": "order_count_for_accepted_inside",
                    //     "className": "bg-inspected",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "审核<br>拒绝量",
                        "data": "order_count_for_refused",
                        "className": "bg-inspected",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            return data;
                        }
                    },
                    // {
                    //     "title": "审核<br>重复量",
                    //     "data": "order_count_for_repeated",
                    //     "className": "bg-inspected",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "审核<br>通过率",
                        "data": "order_rate_for_accepted",
                        "className": "bg-inspected",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },


                    {
                        "title": "主管<br>客服<br>报单量",
                        "data": "group_count_for_all",
                        "className": "text-center vertical-middle bg-group",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    // {
                    //     "title": "主管<br>交付<br>总量",
                    //     "data": "group_count_for_delivered",
                    //     "className": "text-center vertical-middle bg-group",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data
                    //     }
                    // },
                    {
                        "title": "主管<br>交付<br>有效量",
                        "data": "group_count_for_delivered_effective",
                        "className": "text-center vertical-middle bg-group",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "主管<br>交付<br>实际产量",
                        "data": "group_count_for_delivered_actual",
                        "className": "text-center vertical-middle bg-group",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "主管<br>交付<br>有效率",
                        "data": "group_rate_for_delivered_effective",
                        "className": "text-center vertical-middle bg-group",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },
                    // {
                    //     "title": "主管<br>审核<br>有效量",
                    //     "data": "group_count_for_effective",
                    //     "className": "text-center vertical-middle bg-group",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "主管<br>审核<br>通过量",
                        "data": "group_count_for_accepted",
                        "className": "text-center vertical-middle bg-group",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "主管<br>审核<br>通过率",
                        "data": "group_rate_for_accepted",
                        "className": "text-center vertical-middle bg-group",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },


                    {
                        "title": "经理<br>客服<br>报单量",
                        "data": "district_count_for_all",
                        "className": "text-center vertical-middle bg-district",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    // {
                    //     "title": "经理<br>交付<br>总量",
                    //     "data": "district_count_for_delivered",
                    //     "className": "text-center vertical-middle bg-district",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data
                    //     }
                    // },
                    {
                        "title": "经理<br>交付<br>有效量",
                        "data": "district_count_for_delivered_effective",
                        "className": "text-center vertical-middle bg-district",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "经理<br>交付<br>实际产量",
                        "data": "district_count_for_delivered_actual",
                        "className": "text-center vertical-middle bg-district",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data
                        }
                    },
                    {
                        "title": "经理<br>交付<br>有效率",
                        "data": "district_rate_for_delivered_effective",
                        "className": "text-center vertical-middle bg-district",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },
                    // {
                    //     "title": "经理<br>审核<br>有效量",
                    //     "data": "district_count_for_effective",
                    //     "className": "text-center vertical-middle bg-district",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "经理<br>审核<br>通过量",
                        "data": "district_count_for_accepted",
                        "className": "text-center vertical-middle bg-district",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "经理<br>审核<br>通过率",
                        "data": "district_rate_for_accepted",
                        "className": "text-center vertical-middle bg-district",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return data + " %";
                            return data
                        }
                    },
                ],
                "drawCallback": function (settings) {

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
                    {
                        targets: [0], //要合并的列数（第1，2，3列）
                        createdCell: function (td, cellData, rowData, row, col) {
                            //重要的操作可以合并列的代码
                            var rowspan = rowData.district_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        },
                        "data": "department_district_id",
                        "render": function (data, type, full) {
                            // return "<span title='" + data + "'>" + data + "</span>";
                            return row.department_district_er == null ? '未知' : '<a href="javascript:void(0);">'+row.department_district_er.leader.username+'</a>';
                        }
                    },
                    {
                        targets: [1], //要合并的列数（第1，2，3列）
                        createdCell: function (td, cellData, rowData, row, col) {
                            //重要的操作可以合并列的代码
                            var rowspan = rowData.group_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        },
                        "data": "department_group_id",
                        "render": function (data, type, full) {
                            return row.department_group_er == null ? '未知' : '<a href="javascript:void(0);">'+row.department_group_er.leader.username+'</a>';
                        }
                    },
                    {
                        targets: [15,16,17,18,19,20],
                        createdCell: function (td, cellData, rowData, row, col) {
                            var rowspan = rowData.group_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        }
                    },
                    {
                        targets: [21,22,23,24,25,26],
                        createdCell: function (td, cellData, rowData, row, col) {
                            var rowspan = rowData.district_merge;
                            if (rowspan > 1) {
                                $(td).attr('rowspan', rowspan)
                            }
                            if (rowspan == 0) {
                                $(td).remove();
                            }
                        }
                    }
                ],
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


