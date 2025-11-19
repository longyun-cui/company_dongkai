@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{ $title_text or '电话数据' }} - {{ config('info.system.cc') }} - {{ config('info.info.short_name') }}
@endsection




@section('header')<span class="box-title">{{ $title_text or '电话数据' }}</span>@endsection
@section('description')<b></b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">


                <div class="row">
                <div class="col-md-12 datatable-search-row" id="search-row-for-telephone-list">

                    <div class="input-group">

{{--                        <input type="text" class="form-control form-filter item-search-keyup" name="telephone-title" placeholder="名称" />--}}

{{--                        <select class="form-control form-filter select2-box" name="telephone-city[]" id="telephone-city" multiple="multiple"  style="width:640px;">--}}
{{--                            <option value="-1">选择城市</option>--}}
{{--                            @foreach($city_list as $k => $v)--}}
{{--                                <option value="{{ $k }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="btn btn-primary filter-refresh">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="btn bg-teal filter-empty">
                            <i class="fa fa-remove"></i> 清空
                        </button>
                        <button type="button" class="btn btn-warning filter-cancel">
                            <i class="fa fa-undo"></i> 重置
                        </button>
                    </div>

{{--                    @if(in_array($me->user_type,[0,1,9,11,19]))--}}
{{--                    <div class="caption pull-right">--}}
{{--                        <i class="icon-pin font-blue"></i>--}}
{{--                        <span class="caption-subject font-blue sbold uppercase"></span>--}}
{{--                        <a href="{{ url('/service/telephone-import') }}">--}}
{{--                            <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 导入电话</button>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                    @endif--}}

                </div>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-telephone-list">


                <div class="tableArea">
                <table class='table table-striped table-bordered table-hover main-table' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr role='row' class='footing'>
                            <td>选择</td>
                            <td>ID</td>
                            <td>城市</td>
                            <td>全部</td>
                            <td>成单</td>
                            <td>95分</td>
                            <td>90分</td>
                            <td>85分</td>
                            <td>80分</td>
                            <td>60分</td>
                            <td>50分</td>
                            <td>40分</td>
                            <td>30分</td>
                            <td>10分</td>
                            <td>0分</td>
                            <td>电话总数</td>
                            <td>80分比例</td>
                            <td>60分比例</td>
                            <td>10分比例</td>
                            <td>0分比例</td>
                            <td>文件名</td>
                            <td>操作</td>
                        </tr>
                    </tfoot>
                </table>
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
{{--显示-审核信息--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-pool-detail">
    <div class="col-md-6 col-md-offset-3" id="" style="margin-top:16px;margin-bottom:64px;background:#fff;">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0 0;">
                <h3 class="box-title">城市详情【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-pool-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="telephone-download-of-score" readonly>
                    <input type="hidden" name="detail-pool-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-row item-detail-95">
                        <label class="control-label col-md-3">95分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="95">下载</a>
                        </div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-row item-detail-90">
                        <label class="control-label col-md-3">90分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="90">下载</a>
                        </div>
                    </div>
                    {{--85分--}}
                    <div class="form-group item-detail-row item-detail-85">
                        <label class="control-label col-md-3">85分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="85">下载</a>
                        </div>
                    </div>
                    {{--80分--}}
                    <div class="form-group item-detail-row item-detail-80">
                        <label class="control-label col-md-3">80分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="80">下载</a>
                        </div>
                    </div>
                    {{--60分--}}
                    <div class="form-group item-detail-row item-detail-60">
                        <label class="control-label col-md-3">60分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="60">下载</a>
                        </div>
                    </div>
                    {{--10分--}}
                    <div class="form-group item-detail-row item-detail-10">
                        <label class="control-label col-md-3">10分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="10">下载</a>
                        </div>
                    </div>
                    {{--10分--}}
                    <div class="form-group item-detail-row item-detail-0">
                        <label class="control-label col-md-3">0分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="0">下载</a>
                        </div>
                    </div>
                    {{--（-20分）--}}
                    <div class="form-group item-detail-row item-detail-minus-20">
                        <label class="control-label col-md-3">-20分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="-20">下载</a>
                        </div>
                    </div>
                    {{--（-40分）--}}
                    <div class="form-group item-detail-row item-detail-minus-40">
                        <label class="control-label col-md-3">-40分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                            <a href="javascript:void(0);" class="item-down-by-score" data-score="-40">下载</a>
                        </div>
                    </div>
                    {{--（-60分）--}}
                    <div class="form-group item-detail-row item-detail-minus-60">
                        <label class="control-label col-md-3">-60分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                        </div>
                    </div>
                    {{--（-80分）--}}
                    <div class="form-group item-detail-row item-detail-minus-80">
                        <label class="control-label col-md-3">-80分</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 个
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate="">
                        </div>
                    </div>
                    {{--1-8秒--}}
                    <div class="form-group item-detail-row item-detail-cnt">
                        <label class="control-label col-md-3">通话次数</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--1-8秒--}}
                    <div class="form-group item-detail-row item-detail-1-8">
                        <label class="control-label col-md-3">1-8秒</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--9-15秒--}}
                    <div class="form-group item-detail-row item-detail-9-15">
                        <label class="control-label col-md-3">9-15秒</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--16-25秒--}}
                    <div class="form-group item-detail-row item-detail-16-25">
                        <label class="control-label col-md-3">16-25秒</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--26-45秒--}}
                    <div class="form-group item-detail-row item-detail-26-45">
                        <label class="control-label col-md-3">26-45秒</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--45-90秒--}}
                    <div class="form-group item-detail-row item-detail-46-90">
                        <label class="control-label col-md-3">46-90秒</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--90秒+--}}
                    <div class="form-group item-detail-row item-detail-91-above">
                        <label class="control-label col-md-3">91秒+</label>
                        <div class="col-md-2 detail-text-row">
                            <span class="detail-text"></span> 通
                        </div>
                        <div class="col-md-3 item-detail-operate" data-operate=""></div>
                    </div>

                </div>
            </form>

            <div class="box-footer _none">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success item-summit-for-detail-inspected" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default item-cancel-for-detail-inspected" id="">取消</button>
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
    .tableArea table { width:100% !important; min-width:1200px; }
    .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }
    .modal-wrapper .item-detail-row .detail-text-row { text-align:right; }
    .modal-wrapper .form-container .form-group { padding: 14px 0; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/pool/telephone-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="task-id"]').val();
                        d.name = $('input[name="task-name"]').val();
                        d.title = $('input[name="telephone-title"]').val();
                        d.keyword = $('input[name="telephone-keyword"]').val();
                        d.status = $('select[name="telephone-status"]').val();
                        d.telephone_type = $('select[name="telephone-type"]').val();
                        d.telephone_city = $('select[name="telephone-city[]"]').val();
                        d.work_status = $('select[name="work_status"]').val();
                    },
                },
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
                // "scrollY": true,
                "scrollY": ($(document).height() - 280)+"px",
                "scrollCollapse": true,
                "fixedColumns": {
                    {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",--}}
                    {{--"rightColumns": "0"--}}
                },
                "columns": [
                   {
                       "title": "选择",
                       "data": "id",
                       "width": "40px",
                       'orderable': false,
                       render: function(data, type, row, meta) {
                           return '<label><input type="checkbox" name="bulk-id" class="minimal"></label>';
                       }
                   },
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
                        "width": "40px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            $(nTd).attr('data-id',row.id).attr('data-name','ID');
                            $(nTd).attr('data-key','info').attr('data-value',data);
                            $(nTd).data('score-95',row.rate_95_cnt);
                            $(nTd).data('score-90',row.rate_90_cnt);
                            $(nTd).data('score-85',row.rate_85_cnt);
                            $(nTd).data('score-80',row.rate_80_cnt);
                            $(nTd).data('score-60',row.rate_60_cnt);
                            $(nTd).data('score-10',row.rate_10_cnt);
                            $(nTd).data('score-0',row.rate_0_cnt);
                            $(nTd).data('score-minus-20',row.rate_minus_20_cnt);
                            $(nTd).data('score-minus-40',row.rate_minus_40_cnt);
                            $(nTd).data('score-minus-60',row.rate_minus_60_cnt);
                            $(nTd).data('score-minus-80',row.rate_minus_80_cnt);
                            $(nTd).data('call-cnt',row.call_cnt);
                            $(nTd).data('call-1-8',row.call_cnt_1_8);
                            $(nTd).data('call-9-15',row.call_cnt_9_15);
                            $(nTd).data('call-16-25',row.call_cnt_16_25);
                            $(nTd).data('call-26-45',row.call_cnt_26_45);
                            $(nTd).data('call-46-90',row.call_cnt_46_90);
                            $(nTd).data('call-91-above',row.call_cnt_91_above);
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "城市",
                        "data": "region_name",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            $(nTd).attr('data-id',row.id).attr('data-name','城市');
                            $(nTd).attr('data-key','region_name').attr('data-value',data);
                        },
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    // {
                    //     "title": "标签",
                    //     "data": "tag",
                    //     "className": "tag",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         $(nTd).attr('data-id',row.id).attr('data-name','标签');
                    //         $(nTd).attr('data-key','tag').attr('data-value',data);
                    //         if(!data) $(nTd).attr('data-value','');
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //         // return '<a href="javascript:void(0);">'+data+'</a>';
                    //     }
                    // },
                    // {
                    //     "title": "标签2",
                    //     "data": "tag_2",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<a href="javascript:void(0);">'+data+'</a>';
                    //     }
                    // },
                    {
                        "title": "全部",
                        "data": "phone_count",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "成单",
                        "data": "order_count",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "95分",
                        "data": "rate_95_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "90分",
                        "data": "rate_90_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "85分",
                        "data": "rate_85_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "80分",
                        "data": "rate_80_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "60分",
                        "data": "rate_60_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "50分",
                        "data": "rate_50_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "40分",
                        "data": "rate_40_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "30分",
                        "data": "rate_30_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "10分",
                        "data": "rate_10_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    {
                        "title": "0分",
                        "data": "rate_0_cnt",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return formatNumberWithCommas(data);
                        }
                    },
                    // {
                    //     "title": "-20分",
                    //     "data": "rate_minus_20_cnt",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return formatNumberWithCommas(data);
                    //     }
                    // },
                    // {
                    //     "title": "-40分",
                    //     "data": "rate_minus_40_cnt",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return formatNumberWithCommas(data);
                    //     }
                    // },
                    // {
                    //     "title": "-60分",
                    //     "data": "rate_minus_60_cnt",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return formatNumberWithCommas(data);
                    //     }
                    // },
                    // {
                    //     "title": "-80分",
                    //     "data": "rate_minus_80_cnt",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return formatNumberWithCommas(data);
                    //     }
                    // },
                    // {
                    //     "title": "黑名单",
                    //     "data": "blacklist_count",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "可用",
                    //     "data": "count_for_1",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "成单",
                    //     "data": "count_for_sold",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "黑名单",
                    //     "data": "count_for_blacklist",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "最近提取",
                    //     "data": "last_task_datetime",
                    //     "className": "",
                    //     "width": "120px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "电话总数",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control telephone_count" placeholder="总数" value="">';
                        }
                    },
                    // {
                    //     "title": "文件数量",
                    //     "data": "id",
                    //     "className": "",
                    //     "width": "40px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<input type="text" class="form-control file_num" placeholder="数量" value="1">';
                    //     }
                    // },
                    // {
                    //     "title": "文件大小",
                    //     "data": "id",
                    //     "className": "",
                    //     "width": "40px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<input type="text" class="form-control file_size" placeholder="0为平均" value="0">';
                    //     }
                    // },
                    // {
                    //     "title": "90分比例",
                    //     "data": "id",
                    //     "className": "",
                    //     "width": "60px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<input type="text" class="form-control telephone_count" placeholder="90分比例" value="0">';
                    //     }
                    // },
                    {
                        "title": "80分比例",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control proportion_of_80_points" placeholder="80分比例" value="0">';
                        }
                    },
                    {
                        "title": "60分比例",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control proportion_of_60_points" placeholder="60分比例" value="0">';
                        }
                    },
                    {
                        "title": "10分比例",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control proportion_of_10_points" placeholder="10分比例" value="0">';
                        }
                    },
                    {
                        "title": "0分比例",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control proportion_of_0_points" placeholder="0分比例" value="0">';
                        }
                    },
                    {
                        "title": "文件名",
                        "data": "id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<input type="text" class="form-control extraction_name" placeholder="文件名" value="">';
                        }
                    },
                    {
                        "title": "操作",
                        "data": "id",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '<a class="btn btn-xs item-detail-modal-show" data-id="'+data+'">详情</a>';
                            var $html_down = '<a class="btn btn-xs item-down-submit" data-id="'+data+'">下载</a>';
                            var $html_record = '';
                            var $html_able = '';
                            var $html_delete = '';
                            var $html_update = '';

                            if("{{ in_array($me->user_type,[0,1]) }}")
                            {
                                var $html_update = '<a class="btn btn-xs item-update-submit" data-id="'+data+'">更新</a>';
                            }


                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger- item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success- item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black- item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                            var html =
                                $html_update+
                                $html_down+
                                $html_detail+
                                // $html_able+
                                // $html_delete+
                                // $html_record+
//                                    '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                    '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                '';
                            return html;

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
@include(env('TEMPLATE_DK_CC').'entrance.pool.telephone-list-script')
@endsection
