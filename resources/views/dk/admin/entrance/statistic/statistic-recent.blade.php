@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '近期有效量' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '近期有效量' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '近期有效量' }}</span>@endsection
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
                <h3 class="box-title">
                    【<span class="statistic-title">客服</span>】
                    <span class="statistic-time-type-title">近期</span>有效量
                    <span class="statistic-time-title"></span>
                </h3>
            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-rank">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="rank-time-type" value="all" readonly>


                        @if($me->user_type == 1)
                        <select class="form-control form-filter" name="rank-object-type" style="width:88px;">
                            <option value="staff">员工</option>
                            <option value="department">部门</option>
                        </select>
                        @endif

{{--                        @if(in_array($me->user_type,[0,1,9,11,81]))--}}
{{--                        <select class="form-control form-filter" name="rank-staff-type" style="width:88px;">--}}
{{--                            <option value="88">客服</option>--}}
{{--                            @if(in_array($me->user_type,[0,1,9,11,81]))--}}
{{--                            <option value="84">主管</option>--}}
{{--                            @endif--}}
{{--                            @if(in_array($me->user_type,[0,1,9,11]))--}}
{{--                            <option value="81">经理</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                        @endif--}}

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <select class="form-control form-filter select-select2 rank-department-district" name="rank-department-district" style="width:100px;">
                            <option value="-1">选择大区</option>
                            @if(!empty($department_district_list))
                            @foreach($department_district_list as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                            @endif
                        </select>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,81]))
                        <select class="form-control form-filter select-select2 rank-department-group" name="rank-department-group" style="width:100px;">
                            <option data-id="-1" value="-1">选择小组</option>
                            @if(!empty($department_group_list))
                                @foreach($department_group_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @endif

                        {{--按月查看--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-rank">--}}
{{--                            <i class="fa fa-chevron-left"></i>--}}
{{--                        </button>--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="rank-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-rank">--}}
{{--                            <i class="fa fa-chevron-right"></i>--}}
{{--                        </button>--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank-by-month">--}}
{{--                            <i class="fa fa-search"></i> 按月排名--}}
{{--                        </button>--}}


                        {{--按天查看--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-rank">--}}
{{--                            <i class="fa fa-chevron-left"></i>--}}
{{--                        </button>--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="rank-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-rank">--}}
{{--                            <i class="fa fa-chevron-right"></i>--}}
{{--                        </button>--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank-by-day">--}}
{{--                            <i class="fa fa-search"></i> 按天排名--}}
{{--                        </button>--}}


                        {{--全部查询--}}
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-rank">
                            <i class="fa fa-search"></i> 搜索
                        </button>
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
@include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.statistic-rank-script')
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
                    'url': "{{ url('/statistic/statistic-recent') }}",
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
                "order": [2,'desc'],
                "orderCellsTop": true,
                "fixedColumns": {
                    "leftColumns": "1"
                },
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
                        "title": "姓名",
                        "data": "id",
                        "className": "text-center",
                        "width": "100px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            {
                                // this.column(2)
                                $(nTd).addClass('modal-show-for-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','姓名');
                                $(nTd).attr('data-key','username').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.username) return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + data + '" target="_blank">'+row.username+' ('+row.id+')'+'</a>';
                            return '<a href="/staff-statistic/statistic-customer-service?staff_id=' + data + '" target="_blank">'+row.username+' ('+row.id+')'+'</a>';
                        }
                    },
                    {
                        "title": "部门",
                        "data": "id",
                        "className": "text-center",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        },
                        render: function(data, type, row, meta) {
                            var $district_name = row.department_district_er == null ? '' : row.department_district_er.name;
                            var $group_name = row.department_group_er == null ? '' : (' - ' + row.department_group_er.name);
                            return $district_name + $group_name;

                        }
                    },
                    {
                        "title": "6天前",
                        "data": "order_6",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "5天前",
                        "data": "order_5",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "4天前",
                        "data": "order_4",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "3天前",
                        "data": "order_3",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {

                            return data;
                        }
                    },
                    {
                        "title": "前天",
                        "data": "order_2",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {

                            return data;
                        }
                    },
                    {
                        "title": "昨天",
                        "data": "order_1",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {

                            return data;
                        }
                    },
                    {
                        "title": "今天",
                        "data": "order_0",
                        "className": "bg-delivered",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {

                            return data;
                        }
                    },
                    // {
                    //     "title": "交付<br>有效率",
                    //     "data": "order_rate_for_delivered_effective",
                    //     "className": "bg-inspected",
                    //     "width": "100px",
                    //     "orderable": true,
                    //     "orderSequence": ["desc", "asc"],
                    //     render: function(data, type, row, meta) {
                    //         if(data) return data + " %";
                    //         return data
                    //     }
                    // }

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
                    {
                        "targets": [2],
                        "orderData": [2,8,7]
                    },
                    {
                        "targets": [3],
                        "orderData": [3,8,7]
                    },
                    {
                        "targets": [4],
                        "orderData": [4,8,7]
                    },
                    {
                        "targets": [7],
                        "orderData": [7,8,2]
                    },
                    {
                        "targets": [8],
                        "orderData": [8,7,2]
                    }
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


