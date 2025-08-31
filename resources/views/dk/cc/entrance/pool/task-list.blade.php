@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{ $title_text or '任务列表' }} - {{ config('info.system.cc') }} - {{ config('info.info.short_name') }}
@endsection




@section('header')<span class="box-title">{{ $title_text or '任务列表' }}</span>@endsection
@section('description')<b></b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">


                <div class="row col-md-12 datatable-search-row" id="search-row-for-task-list">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="task-title" placeholder="名称" />

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




                    @if(in_array($me->user_type,[0,1,9,11,19]))
                        <div class="caption pull-right _none">
                            <i class="icon-pin font-blue"></i>
                            <span class="caption-subject font-blue sbold uppercase"></span>
                            <a href="{{ url('/service/task-create') }}">
                                <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加任务</button>
                            </a>
                        </div>
                    @endif


                </div>


            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-task-list">


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

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value ="-1">选择属性</option>
                            <option value ="amount">金额</option>
                            <option value ="11">支出</option>
                        </select>

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




@section('custom-css')
@endsection
@section('custom-style')
<style>
    .tableArea table { width:100% !important; min-width:1200px; }
    .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }
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
                "aLengthMenu": [[50, 200, 500], ["50", "200", "500"]],
                // "aLengthMenu": [[-1], ["全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/pool/task-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="task-id"]').val();
                        d.name = $('input[name="task-name"]').val();
                        d.title = $('input[name="task-title"]').val();
                        d.keyword = $('input[name="task-keyword"]').val();
                        d.status = $('select[name="task-status"]').val();
                        d.task_type = $('select[name="task-type"]').val();
                        d.work_status = $('select[name="work_status"]').val();
                    },
                },
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
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
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
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
                    {
                        "title": "任务名称",
                        "data": "name",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "电话池",
                        "data": "pool_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "城市",
                        "data": "region_name",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    // {
                    //     "title": "省份",
                    //     "data": "provinceCode",
                    //     "className": "provinceCode",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         $(nTd).attr('data-id',row.id).attr('data-name','省份');
                    //         $(nTd).attr('data-key','provinceCode').attr('data-value',data);
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return '<a href="javascript:void(0);">'+row.provinceName+'</a>';
                    //     }
                    // },
                    // {
                    //     "title": "城市",
                    //     "data": "cityCode",
                    //     "className": "cityCode",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<a href="javascript:void(0);">'+row.cityName+'</a>';
                    //     }
                    // },
                    // {
                    //     "title": "行政区",
                    //     "data": "areaCode",
                    //     "className": "areaCode",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return '<a href="javascript:void(0);">'+row.areaName+'</a>';
                    //     }
                    // },
                    // {
                    //     "title": "标签",
                    //     "data": "tag",
                    //     "className": "tag",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //         // return '<a href="javascript:void(0);">'+data+'</a>';
                    //     }
                    // },
                    {
                        "title": "提取数量",
                        "data": "extraction_telephone_count",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                            // return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "文件数量",
                        "data": "extraction_file_num",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                            // return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "文件大小",
                        "data": "extraction_file_size",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                            // return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "创建者",
                        "data": "creator_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": 'created_at',
                        "className": "font-12px",
                        "width": "120px",
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
                        "title": "操作",
                        "data": 'id',
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_able = '';
                            var $html_delete = '';
                            var $html_download = '';

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



                            if(row.is_completed == 1)
                            {
                                $html_download = '<a class="btn btn-xs btn-default item-download-submit" data-id="'+data+'">下载</a>';
                            }
                            else
                            {
                                $html_download = '<a class="btn btn-xs btn-default disabled">下载</a>';
                            }

                            var html =
                                $html_download+
                                // '<a class="btn btn-xs btn-primary- item-edit-link" data-id="'+data+'">编辑</a>'+
                                // $html_able+
                                // $html_delete+
                                // $html_record+
                                // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                // '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                '';
                            return html;

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
@include(env('TEMPLATE_DK_CC').'entrance.pool.task-list-script')
@endsection
