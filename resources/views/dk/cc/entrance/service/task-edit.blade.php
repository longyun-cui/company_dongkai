@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{ $operate_title_text or '添加任务' }} - {{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}
@endsection




@section('title'){{ $operate_title_text or '添加任务' }}@endsection
@section('header'){{ $operate_title_text or '添加任务' }}@endsection
@section('description'){{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($operate_list_link) }}"><i class="fa fa-list"></i>{{ $operate_list_text or '任务列表' }}</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PORTLET-->
            <div class="box box-info form-container">

                <div class="box-header with-border" style="margin:16px 0;">
                    <h3 class="box-title">{{ $title_text or '' }}</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
                    <div class="box-body">

                        {{ csrf_field() }}
                        <input type="hidden" name="operate[type]" value="{{ $operate_type or '' }}" readonly>
                        <input type="hidden" name="operate[id]" value="{{ $operate_id or 0 }}" readonly>
                        <input type="hidden" name="operate[item_category]" value="{{ $category_item_category or 'service' }}" readonly>
                        <input type="hidden" name="operate[item_type]" value="{{ $category_item_type or 'task' }}" readonly>


                        {{--选择城市--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">选择城市</label>
                            <div class="col-md-8 area_select_box">
                                {{--选择所在城市--}}
                                <div class="col-xs-4 col-sm-4 col-md-4 " style="padding:0">
                                    <select name="area_province" class="form-control form-filter select2-box area_select_province" id="area_province">
                                        @if(!empty($data->area_province))
                                            <option value="{{ $data->area_province or '' }}">{{ $data->area_province or '' }}</option>
                                        @else
                                            <option value="">请选择省</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4 " style="padding:0">
                                    <select name="area_city" class="form-control form-filter select2-box area_select_city" id="area_city">
                                        @if(!empty($data->area_city))
                                            <option value="{{ $data->area_city or '' }}">{{ $data->area_city or '' }}</option>
                                        @else
                                            <option value="">请先选择省</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4 " style="padding:0">
                                    <select name="area_district" class="form-control form-filter select2-box area_select_district" id="area_district">
                                        @if(!empty($data->area_district))
                                            <option value="{{ $data->area_district or '' }}">{{ $data->area_district or '' }}</option>
                                        @else
                                            <option value="">请先选择市</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>


                        {{--任务名称--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 任务名称</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="name" placeholder="任务名称" value="{{ $data->name or '' }}">
                            </div>
                        </div>



                        {{--电话总数--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 电话总数</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="telephone_num" placeholder="请输入大于等于1的整数" value="{{ $data->telephone_num or '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 文件数量</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="file_num" placeholder="请输入大于等于1的整数" value="{{ $data->file_num or '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 文件大小</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="file_size" placeholder="请输入大于等于0的整数，0为平均分割" value="{{ $data->file_size or '' }}">
                            </div>
                        </div>


                        {{--描述--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">描述</label>
                            <div class="col-md-8 ">
                                {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                                <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
                            </div>
                        </div>


                        {{--头像--}}
                        <div class="form-group _none">
                            <label class="control-label col-md-2">头像</label>
                            <div class="col-md-8 fileinput-group">

                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail">
                                        @if(!empty($data->portrait_img))
                                            <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt="" />
                                        @endif
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail">
                                    </div>
                                    <div class="btn-tool-group">
                                <span class="btn-file">
                                    <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                    <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                    <input type="file" name="portrait" />
                                </span>
                                        <span class="">
                                    <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                                </span>
                                    </div>
                                </div>
                                <div id="titleImageError" style="color: #a94442"></div>

                            </div>
                        </div>

                        {{--启用--}}
                        @if($operate_type == 'create')
                            <div class="form-group form-type _none">
                                <label class="control-label col-md-2">启用</label>
                                <div class="col-md-8">
                                    <div class="btn-group">

                                        <button type="button" class="btn">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="active" value="0" checked="checked"> 暂不启用
                                                </label>
                                            </div>
                                        </button>
                                        <button type="button" class="btn">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="active" value="1"> 启用
                                                </label>
                                            </div>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="button" class="btn btn-success" id="edit-item-submit"><i class="fa fa-check"></i> 提交</button>
                            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>
@endsection




@section('custom-css')
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">
@endsection




@section('custom-js')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {

        var $team_type = $("input[name=team_type]").val();
        console.log($team_type);
        if($team_type == 11)
        {
            $('#select2-leader').prop('data-type','manager');
            $('.select2-superior-box').hide();
        }
        else if($team_type == 21)
        {
            $('#select2-leader').prop('data-type','supervisor');
            $('.select2-superior-box').show();
        }


        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });


        // 【选择部门类型】
        $("#form-edit-item").on('click', "input[name=team_type]", function() {
            // radio
            var $value = $(this).val();
            if($value == 11)
            {
                $('#select2-leader').prop('data-type','manager');
                $('.select2-superior-box').hide();
            }
            else if($value == 21)
            {
                $('#select2-leader').prop('data-type','supervisor');
                $('.select2-superior-box').show();
            }
            else
            {
                $('#select2-leader').prop('data-type','manager');
                $('.select2-superior-box').hide();
            }

            $('#select2-leader').select2({
                ajax: {
                    url: "{{ url('/department/department_select2_leader') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: params.page,
                            type: $('#select2-leader').prop('data-type')
                        };
                    },
                    processResults: function (data, params) {

                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 0,
                theme: 'classic'
            });
        });


        // 添加or编辑
        $("#edit-item-submit").on('click', function() {

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在上传</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            var options = {
                url: "{{ url('/service/task-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                beforeSubmit: function (data) {
                },
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url($operate_list_link) }}";
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg("服务器错误");
                },
                complete: function (jqXHR, textStatus) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    layer.closeAll('loading');
                }
            };

            $("#form-edit-item").ajaxSubmit(options);
        });


        //
        $('#select2-leader').select2({
            ajax: {
                url: "{{ url('/department/department_select2_leader') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-leader').prop('data-type')
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        //
        $('#select2-superior-department').select2({
            ajax: {
                url: "{{ url('/department/department_select2_superior_department') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-leader').prop('data-type')
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });



    });
</script>
@endsection
