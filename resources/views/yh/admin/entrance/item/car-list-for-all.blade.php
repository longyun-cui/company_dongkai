@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '车辆列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')车辆列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">车辆检验提醒</h3>
            </div>

            <div class="box-body" style="font-size:13px;">
                @foreach($car_list as $car)
                    <div style="clear:both;line-height:24px;">
                        <span class="pull-left" style="width:120px;">
                            @if($car->item_type == 1) <text class="text-green">[车辆]</text>
                            @elseif($car->item_type == 21) <text class="text-purple">[车挂]</text>
                            @endif
                            <b class="font-14px- text-red-">{{ $car->name }}</b>
                        </span>

                        <span class="pull-left" style="width:200px;">
                            <text class="text-blue">[行驶证]</text> 检验期
                            @if($car->inspection_validity_is)
                            <b class="font-16px text-red">{{ $car->inspection_validity }}</b>
                            @else
                            <b class="font-16px">{{ $car->inspection_validity }}</b>
                            @endif
                        </span>


                        <span class="pull-left" style="width:200px;">
                            <text class="text-green">[运输证]</text> 年检
                            @if($car->transportation_license_validity_is)
                            <b class="font-16px text-red">{{ $car->transportation_license_validity }}</b>
                            @else
                            <b class="font-16px">{{ $car->transportation_license_validity }}</b>
                            @endif
                        </span>


                        <span class="pull-left" style="width:200px;">
                            换证
                            @if($car->transportation_license_change_time_is)
                            <b class="font-16px text-red">{{ $car->transportation_license_change_time }}</b>
                            @else
                            <b class="font-16px">{{ $car->transportation_license_change_time }}</b>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="box-footer">

            </div>

        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">车辆列表</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/item/car-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加车辆</button>
                    </a>
                </div>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-car-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="car-name" placeholder="车牌" />

                        {{--<select class="form-control form-filter" name="owner" style="width:96px;">--}}
                            {{--<option value ="-1">选择员工</option>--}}
                            {{--@foreach($sales as $v)--}}
                                {{--<option value ="{{ $v->id }}">{{ $v->true_name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}

                        <select class="form-control form-filter" name="work_status" style="width:96px;">
                            <option value ="-1">全部状态</option>
                            <option value ="0">空闲</option>
                            <option value ="1">工作中</option>
                            <option value ="9">待发车</option>
                            <option value ="19">非工作状态</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped- table-bordered table-hover main-table' id='datatable_ajax'>
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




{{--修改-基本信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改车辆【<span class="info-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-text-set-operate" value="item-car-info-text-set" readonly>
                    <input type="hidden" name="info-text-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-text-set-column-key" value="" readonly>

                    <div class="form-group">
                        <label class="control-label col-md-2 info-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="info-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="info-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-时间信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-time-set-operate" value="item-car-info-text-set" readonly>
                    <input type="hidden" name="info-time-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-time-set-column-key" value="" readonly>
                    <input type="hidden" name="info-time-set-time-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-radio-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-radio-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-radio-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-radio-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-radio-set-operate" value="item-order-info-option-set" readonly>
                    <input type="hidden" name="info-radio-set-order-id" value="0" readonly>
                    <input type="hidden" name="info-radio-set-operate-type" value="edit" readonly>
                    <input type="hidden" name="info-radio-set-column-key" value="" readonly>


                    <div class="form-group radio-box">
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-radio-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-radio-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-select-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-select-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-select-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-select-set-operate" value="item-car-info-option-set" readonly>
                    <input type="hidden" name="info-select-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-select-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-select-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-select-set-column-name"></label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-client" name="info-select-set-column-value" style="width:240px;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-select-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-select-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{----}}
<div class="option-container _none">

    <div id="trailer_type-option-list">
        <option value="0">选择箱型</option>
        <option value="直板">直板</option>
        <option value="高栏">高栏</option>
        <option value="平板">平板</option>
        <option value="冷藏">冷藏</option>
    </div>

    <div id="trailer_length-option-list">
        <option value="0">选择车挂尺寸</option>
        <option value="9.6">9.6</option>
        <option value="12.5">12.5</option>
        <option value="15">15</option>
        <option value="16.5">16.5</option>
        <option value="17.5">17.5</option>
    </div>

    <div id="trailer_volume-option-list">
        <option value="0">选择承载方数</option>
        <option value="125">125</option>
        <option value="130">130</option>
        <option value="135">135</option>
    </div>

    <div id="trailer_weight-option-list">
        <option value="0">选择承载重量</option>
        <option value="13">13吨</option>
        <option value="20">20吨</option>
        <option value="25">25吨</option>
    </div>

    <div id="trailer_axis_count-option-list">
        <option value="0">选择轴数</option>
        <option value="1">1轴</option>
        <option value="2">2轴</option>
        <option value="3">3轴</option>
    </div>




    <div id="receipt_need-option-list">

        <label class="control-label col-md-2">是否需要回单</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="0" class="info-set-column"> 不需要
                        </label>
                    </span>
                </button>

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="1" class="info-set-column"> 需要
                        </label>
                    </span>
                </button>

            </div>
        </div>

    </div>

</div>
@endsection




@section('custom-style')
    <style>
        .tableArea .main-table {
            min-width: 3400px;
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
                "aLengthMenu": [[100, 200, -1], ["100", "200", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/car-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="car-id"]').val();
                        d.name = $('input[name="car-name"]').val();
                        d.title = $('input[name="car-title"]').val();
                        d.keyword = $('input[name="car-keyword"]').val();
                        d.status = $('select[name="car-status"]').val();
                        d.work_status = $('select[name="work_status"]').val();
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
                        "className": "f",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "160px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.is_me == 1 && row.active == 0)
                            {
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                            }
                            else
                            {
                                $html_publish = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">发布</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            var html =
                                '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                                $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                                //                                    $html_publish+
                                $html_delete+
//                                    '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                    '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
//                                    '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "width": "80px",
                        "title": "状态",
                        "data": "item_status",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.item_status == 1)
                            {
                                return '<small class="btn-xs btn-success">启用</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs btn-danger">禁用</small>';
                            }
                        }
                    },
                    {
                        "width": "60px",
                        "title": "类型",
                        "data": 'item_type',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-olive">车辆</small>';
                            else if(data == 21) return '<small class="btn-xs bg-purple">车挂</small>';
                            else return "有误";
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "80px",
                        "title": "车牌号",
                        "data": "name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "工作状态",
                        "data": 'work_status',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '<small class="btn-xs bg-red">空闲</small>';
                            else if(data == 1) return '<small class="btn-xs bg-olive">工作中</small>';
                            else if(data == 9) return '<small class="btn-xs bg-blue">待发车</small>';
                            else return "--";
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "当前位置",
                        "data": "current_place",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "未来位置",
                        "data": "future_place",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "到达时间",
                        "data": 'future_time',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return "";

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
                        "width": "40px",
                        "title": "类型",
                        "data": "trailer_type",
                        "orderable": false,
                        "sortable": true,
                        "sorting": ['asc','desc'],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','trailer_type').attr('data-value',data);
                                $(nTd).attr('data-column-name','类型');
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
                        "width": "40px",
                        "title": "尺寸",
                        "data": "trailer_length",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','trailer_length').attr('data-value',data);
                                $(nTd).attr('data-column-name','尺寸');
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
                        "width": "40px",
                        "title": "容积",
                        "data": "trailer_volume",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','trailer_volume').attr('data-value',data);
                                $(nTd).attr('data-column-name','容积');
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
                        "width": "40px",
                        "title": "载重",
                        "data": "trailer_weight",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','trailer_weight').attr('data-value',data);
                                $(nTd).attr('data-column-name','载重');
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
                        "width": "40px",
                        "title": "轴数",
                        "data": "trailer_axis_count",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','trailer_axis_count').attr('data-value',data);
                                $(nTd).attr('data-column-name','轴数');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data) return data;
                            else return "";
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "司机",
                        "data": "linkman_name",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','linkman_name').attr('data-value',data);
                                $(nTd).attr('data-column-name','司机');
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
                        "className": "text-center",
                        "width": "120px",
                        "title": "电话",
                        "data": "linkman_phone",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','linkman_phone').attr('data-value',data);
                                $(nTd).attr('data-column-name','电话');
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
                        "className": "text-center",
                        "width": "160px",
                        "title": "车辆类型",
                        "data": "car_type",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','car_type').attr('data-value',data);
                                $(nTd).attr('data-column-name','车辆类型');
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
                        "width": "100px",
                        "title": "所有人",
                        "data": "car_owner",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','car_owner').attr('data-value',data);
                                $(nTd).attr('data-column-name','所有人');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "使用性质",
                        "data": "car_function",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','car_function').attr('data-value',data);
                                $(nTd).attr('data-column-name','使用性质');
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
                        "className": "text-center",
                        "width": "160px",
                        "title": "品牌",
                        "data": "car_brand",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','car_brand').attr('data-value',data);
                                $(nTd).attr('data-column-name','品牌');
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
                        "className": "text-center",
                        "width": "100px",
                        "title": "车辆识别代码",
                        "data": "car_identification_number",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','car_identification_number').attr('data-value',data);
                                $(nTd).attr('data-column-name','车辆识别代码');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "发动机号",
                        "data": "engine_number",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','engine_number').attr('data-value',data);
                                $(nTd).attr('data-column-name','发动机号');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "车头轴距",
                        "data": "locomotive_wheelbase",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','locomotive_wheelbase').attr('data-value',data);
                                $(nTd).attr('data-column-name','车头轴距');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "主油箱",
                        "data": "main_fuel_tank",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','main_fuel_tank').attr('data-value',data);
                                $(nTd).attr('data-column-name','主油箱');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "副油箱",
                        "data": "auxiliary_fuel_tank",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','auxiliary_fuel_tank').attr('data-value',data);
                                $(nTd).attr('data-column-name','副油箱');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "总质量",
                        "data": "total_mass",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','total_mass').attr('data-value',data);
                                $(nTd).attr('data-column-name','总质量');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "整备质量",
                        "data": "curb_weight",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','curb_weight').attr('data-value',data);
                                $(nTd).attr('data-column-name','整备质量');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "核定载重",
                        "data": "load_weight",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','load_weight').attr('data-value',data);
                                $(nTd).attr('data-column-name','核定载重');
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
                        "className": "text-center",
                        "width": "80px",
                        "title": "准牵引质量",
                        "data": "traction_mass",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','traction_mass').attr('data-value',data);
                                $(nTd).attr('data-column-name','准牵引质量');
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "外廓尺寸",
                        "data": "overall_size",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','overall_size').attr('data-value',data);
                                $(nTd).attr('data-column-name','外廓尺寸');
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
                        "className": "text-center",
                        "width": "100px",
                        "title": "购买日期",
                        "data": "purchase_date",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','purchase_date').attr('data-value',data);
                                $(nTd).attr('data-column-name','购买日期');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "100px",
                        "title": "注册日期",
                        "data": "registration_date",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','registration_date').attr('data-value',data);
                                $(nTd).attr('data-column-name','注册日期');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "100px",
                        "title": "发证日期",
                        "data": "issue_date",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','issue_date').attr('data-value',data);
                                $(nTd).attr('data-column-name','发证日期');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "100px",
                        "title": "检验有效期",
                        "data": "inspection_validity",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','inspection_validity').attr('data-value',data);
                                $(nTd).attr('data-column-name','检验有效期');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "100px",
                        "title": "运输证-年检",
                        "data": "transportation_license_validity",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','transportation_license_validity').attr('data-value',data);
                                $(nTd).attr('data-column-name','运输证-年检');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "100px",
                        "title": "运输证-换证",
                        "data": "transportation_license_change_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name',row.name);
                                $(nTd).attr('data-key','transportation_license_change_time').attr('data-value',data);
                                $(nTd).attr('data-column-name','运输证-换证');
                                $(nTd).attr('data-time-type','date');
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
                        "width": "60px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "创建时间",
                        "data": 'created_at',
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "更新时间",
                        "data": 'updated_at',
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
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    ajax_datatable.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{csrf_token()}}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
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
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.car-script')
@endsection
