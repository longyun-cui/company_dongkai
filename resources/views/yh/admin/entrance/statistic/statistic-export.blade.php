@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '数据导出' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description','数据导出 - 管理员后台系统 - 兆益信息')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--导出--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">订单导出</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="export-for-order">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">


{{--                        <select class="form-control form-filter" name="order-staff" style="width:88px;">--}}
{{--                            <option value ="-1">选择员工</option>--}}
{{--                            @foreach($staff_list as $v)--}}
{{--                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="order-client" style="width:88px;">--}}
{{--                            <option value ="-1">选择客户</option>--}}
{{--                            @foreach($client_list as $v)--}}
{{--                                <option value ="{{ $v->id }}">{{ $v->username }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <select class="form-control form-filter select2-container select2-project" name="order-project" style="width:120px;">
                            <option value="-1">选择项目</option>
                        </select>

                        <select class="form-control form-filter" name="order-inspected-result" style="width:100px;">
                            <option value ="-1">审核结果</option>
                            <option value ="通过">通过</option>
                            <option value ="拒绝">拒绝</option>
                            <option value ="重复">重复</option>
                            <option value ="内部通过">内部通过</option>
                        </select>

{{--                        <select class="form-control form-filter" name="order-channel" style="width:88px;">--}}
{{--                            <option value ="-1">去到来源</option>--}}
{{--                            @foreach(config('info.channel_source') as $v)--}}
{{--                                <option value ="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="order-city" style="width:88px;">--}}
{{--                            <option value ="-1">所在城市</option>--}}
{{--                            @foreach(config('info.location_city') as $v)--}}
{{--                                <option value ="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <button type="button" class="form-control btn btn-flat btn-default filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="latest">
                            <i class="fa fa-download"></i> 最新导出
                        </button>


                        {{--按时间段导出--}}
                        <input type="text" class="form-control form-filter filter-keyup time_picker" name="export-start" placeholder="起始时间" readonly="readonly" value="" data-default="" style="width:120px;text-align:center;" />
                        <input type="text" class="form-control form-filter filter-keyup time_picker" name="export-ended" placeholder="终止时间" readonly="readonly" value="" data-default="" style="width:120px;text-align:center;" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="" style="width:100px;">
                            <i class="fa fa-download"></i> 按时间段导出
                        </button>


                        {{--按天导出--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-export">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="export-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-export">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="day">
                            <i class="fa fa-download"></i> 按日导出
                        </button>


                        {{--按月导出--}}
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-export">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month-picker month_picker" name="export-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-export">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="month">
                            <i class="fa fa-download"></i> 按月导出
                        </button>


                        <div class="month-picker-box clear-both">
                        </div>

                        <div class="month-picker-box clear-both-">
                        </div>


                        <div class="month-picker-box clear-both">
                        </div>


                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                </div>
            </div>

        </div>
    </div>
</div>


{{--导出记录--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">

                <h3 class="box-title">{{ $title_text or '导出记录' }}</h3>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-record-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="record-id" placeholder="ID" />
                        <input type="text" class="form-control form-filter filter-keyup" name="record-name" placeholder="标题" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-record">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-record">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea overflow-none-">
                    <table class='table table-striped table-bordered- table-hover' id='datatable_ajax'>
                        <thead>
                        <tr role='row' class='heading'>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>


            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12 _none">
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
@endsection
@section('custom-style')
<style>
    .myChart { width:100%;height:240px; }
    .datatable-search-row .input-group .time-picker-btn,
    .datatable-search-row .input-group .month-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker,
    .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group button { width:80px; }
    .datatable-search-row .input-group select { width:100px; }
    .datatable-search-row .input-group .select2-container { width:120px; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
@include(env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-export-script')
<script>
    $(function(){

        // 初始化
        // $("#filter-submit-for-comprehensive").click();

    });
</script>


<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[100, 200, 500], ["100", "200", "500"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/record-list-for-all?item_type=record') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="record-id"]').val();
                        d.name = $('input[name="record-name"]').val();
                        d.title = $('input[name="record-title"]').val();
                        d.keyword = $('input[name="record-keyword"]').val();
                        d.status = $('select[name="record-status"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value',row.attachment_list);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "对象",
                        "data": "operate_object",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','对象');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','对象');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 11) return '<small class="btn-xs bg-blue">管理员</small>';
                            else if(data == 21) return '<small class="btn-xs bg-blue">员工</small>';
                            else if(data == 25) return '<small class="btn-xs bg-blue">驾驶员</small>';
                            else if(data == 31) return '<small class="btn-xs bg-green">客户</small>';
                            else if(data == 41) return '<small class="btn-xs bg-green">车辆</small>';
                            else if(data == 51) return '<small class="btn-xs bg-green">线路</small>';
                            else if(data == 61) return '<small class="btn-xs bg-green">包油油耗</small>';
                            else if(data == 71) return '<small class="btn-xs bg-yellow">工单</small>';
                            else if(data == 77) return '<small class="btn-xs bg-yellow"><i class="fa fa-refresh"></i> 环线</small>';
                            else if(data == 88) return '<small class="btn-xs bg-red">财务</small>';
                            else return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "操作",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 0) return '<small class="btn-xs bg-blue">访问</small>';
                            else if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
                            else if(data == 99) return '<small class="btn-xs bg-olive">确认</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else if(data == 109) return '<small class="btn-xs bg-fuchsia">导出</small>';
                            else return '有误';
                        }
                    },
                    {
                        "title": "导出类型",
                        "data": "operate_type",
                        "className": "font-12px",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '自定义时间导出';
                            else if(data == 11) return '按月导出';
                            else if(data == 99) return '最新导出';
                            else return '';
                        }
                    },
                    {
                        "title": "导出范围",
                        "data": "title",
                        "className": "font-12px",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "起始时间",
                        "data": "before",
                        "className": "font-12px",
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_name == 'is_delay')
                            {
                                if(data == 1) return '正常';
                                else if(data == 9) return '压车';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                // if(data == 0) return '';
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "title": "终止时间",
                        "data": "after",
                        "className": "font-12px",
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "操作时间",
                        "data": "created_at",
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "IP",
                        "data": "ip",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    }
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


