@extends(env('TEMPLATE_DK_CUSTOMER').'layout.layout')


@section('head_title')
    {{ $title_text or '消费记录' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')<b>{{ $title_text or '消费记录' }}</b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">


            <div class="box-body datatable-body item-main-body" id="datatable-for-finance-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="finance_id" placeholder="消费ID" style="width:88px;" />
{{--                        <input type="text" class="form-control form-filter item-search-keyup" name="title" placeholder="名目" style="width:88px;" />--}}
                        <input type="text" class="form-control form-filter item-search-keyup" name="transaction_amount" placeholder="金额" style="width:88px;" />

{{--                        <select class="form-control form-filter" name="finance-type" style="width:96px;">--}}
{{--                            <option value ="-1">全部收支</option>--}}
{{--                            <option value ="1">收入</option>--}}
{{--                            <option value ="21">支出</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="finance-confirm" style="width:96px;">--}}
{{--                            <option value ="-1">全部记录</option>--}}
{{--                            <option value ="0">待确认</option>--}}
{{--                            <option value ="1">已确认</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="finance-staff" style="width:88px;">--}}
{{--                            <option value ="-1">选择渠道</option>--}}
{{--                            @foreach($channel_list as $v)--}}
{{--                                <option value ="{{ $v->id }}" @if($v->id == $channel_id) selected="selected" @endif>{{ $v->name }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <div1 class="clear-both-">
{{--                            <input type="text" class="form-control form-filter date_picker" name="transaction_time" placeholder="交易日期" readonly="readonly" style="width:80px;" />--}}
                            <input type="text" class="form-control form-filter date_picker" name="transaction_start" placeholder="起始日期" readonly="readonly" style="width:80px;" />
                            <input type="text" class="form-control form-filter date_picker" name="transaction_ended" placeholder="终止日期" readonly="readonly" style="width:80px;" />

                            <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-finance">
                                <i class="fa fa-remove"></i> 清空重选
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-finance">
                                <i class="fa fa-circle-o-notch"></i> 刷新
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-finance">
                                <i class="fa fa-undo"></i> 重置
                            </button>
                        </div1>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered- table-hover main-table' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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


{{--修改列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-modify-list">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-modify-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable_ajax_record'>
                    <thead>
                    <tr role='row' class='heading'>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection




@section('custom-style')
    <style>
        .tableArea .main-table {
            min-width:100%;
        }
    </style>
@endsection




@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50, 100, 200], ["50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/finance/funds-using-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.finance_id = $('input[name="finance_id"]').val();
                        d.transaction_time = $('input[name="transaction_time"]').val();
                        d.title = $('input[name="title"]').val();
                        d.transaction_amount = $('input[name="transaction_amount"]').val();
                        d.transaction_type = $('input[name="transaction_type"]').val();
                        d.transaction_receipt_account = $('input[name="transaction_receipt_account"]').val();
                        d.transaction_payment_account = $('input[name="transaction_payment_account"]').val();
                        d.transaction_order = $('input[name="transaction_order"]').val();
                        d.transaction_start = $('input[name="transaction_start"]').val();
                        d.transaction_ended= $('input[name="transaction_ended"]').val();
                        d.finance_type = $('select[name="finance-type"]').val();
                        d.finance_confirm = $('select[name="finance-confirm"]').val();
                        d.staff = $('select[name="finance-staff"]').val();
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
//                "scrollY": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 6 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
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
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "类型",
                        "data": "finance_object",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 71)
                            {
                                if(row.clue_er == null) return '线索';
                                else
                                {
                                    var $result_html = '';
                                    if(row.clue_er.sale_type == 1) $result_html = '<small class="btn-xs bg-blue">普通·线索</small>';
                                    else if(row.clue_er.sale_type == 11) $result_html = '<small class="btn-xs bg-green">优选·线索</small>';
                                    else if(row.clue_er.sale_type == 66) $result_html = '<small class="btn-xs bg-yellow">独享·线索</small>';
                                    else $result_html = '<small class="btn-xs bg-black">error</small>';
                                    return $result_html;
                                }
                            }
                            else if(data == 77) return '<small class="btn-xs bg-purple">话单</small>';
                            else return '--';

                        }
                    },
                    {
                        "title": "项目",
                        "data": "finance_object",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 71) return '<a href="javascript:void(0);">'+row.clue_id+'</a>';
                            else if(data == 77) return '<a href="javascript:void(0);">'+row.telephone_id+'</a>';
                            else return '--';

                        }
                    },
                    {
                        "title": "金额",
                        "data": "transaction_amount",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.finance_type == 1) return '<b class="text-olive">'+parseFloat(data)+'</b>';
                            else if(row.finance_type == 21) return '<b class="text-red">'+parseFloat(data)+'</b>';
                            else return parseFloat(data);
                        }
                    },
                    {
                        "title": "备注",
                        "data": "remark",
                        "className": "",
                        "width": "",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_confirmed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','备注');
                                $(nTd).attr('data-key','remark').attr('data-value',data);
                                $(nTd).attr('data-column-name','备注');
                                $(nTd).attr('data-text-type','textarea');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                            // else return '';
                        }
                    },
                    {
                        "title": "创建者",
                        "data": "creator_id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "消费时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "128px",
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    }
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


<script>
    var TableDatatablesAjax_record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_record = $('#datatable_ajax_record');
            dt_record.DataTable().destroy();
            var ajax_datatable_record = dt_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/finance/finance-modify-record?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "类型",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
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
                            else if(data == 99) return '<small class="btn-xs bg-olive">确认</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_category == 1)
                            {
                                if(data == "title") return '名目';
                                else if(data == "transaction_type") return '支付方式';
                                else if(data == "transaction_receipt_account") return '收款账户';
                                else if(data == "transaction_payment_account") return '支出账户';
                                else if(data == "transaction_order") return '交易单号';
                                else if(data == "transaction_time") return '交易日期';
                                else if(data == "remark") return '备注';
                                else return '有误';
                            }
                            else if(row.operate_category == 71)
                            {
                                return '';

                                if(row.operate_type == 1) return '添加';
                                else if(row.operate_type == 91) return '删除';

                                if(data == "attachment") return '附件';
                            }
                            else return '';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(data)
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
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.column_type == 'datetime' || row.column_type == 'date')
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
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
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
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_record.on('click', '.modify-filter-submit', function () {
                ajax_datatable_record.ajax.reload();
            });

            dt_record.on('click', '.modify-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_record.ajax.reload();
            });


//            dt_record.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_record
        }
    }();
    //        $(function () {
    //            TableDatatablesAjax_record.init();
    //        });
</script>
@include(env('TEMPLATE_DK_CUSTOMER').'entrance.finance.funds-using-list-script')
@endsection
