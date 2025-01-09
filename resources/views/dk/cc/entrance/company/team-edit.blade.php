@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{ $operate_title_text or '编辑部门' }} - {{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}
@endsection




@section('header'){{ $operate_title_text or '' }}@endsection
@section('description'){{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($operate_list_link) }}"><i class="fa fa-list"></i>{{ $operate_list_text or '团队列表' }}</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PORTLET-->
            <div class="box box-info form-container">

                <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
                    <div class="box-body">

                        {{ csrf_field() }}
                        <input type="hidden" name="operate[type]" value="{{ $operate_type or '' }}" readonly>
                        <input type="hidden" name="operate[id]" value="{{ $operate_id or 0 }}" readonly>
                        <input type="hidden" name="operate[item_category]" value="{{ $category_item_category or 'company' }}" readonly>
                        <input type="hidden" name="operate[item_type]" value="{{ $category_item_type or 'team' }}" readonly>


                        {{--类别--}}
                        <div class="form-group form-category">
                            <label class="control-label col-md-2">类型</label>
                            <div class="col-md-8">
                                <div class="btn-group">

                                    @if(in_array($me->user_type, [0,1,11]))
                                        @if($operate_type == 'create' || ($operate_type == 'edit' && $data->team_type == 11))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate_type == 'edit' && $data->team_type == 11)
                                                        <input type="radio" name="team_type" value="11" checked="checked"> 大区
                                                    @else
                                                        <input type="radio" name="team_type" value="11" checked="checked"> 大区
                                                    @endif
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                    @if(in_array($me->user_type, [0,1,11,41]))
                                        @if($operate_type == 'create' || ($operate_type == 'edit' && $data->team_type == 21))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate_type == 'edit' && $data->team_type == 21)
                                                        <input type="radio" name="team_type" value="21" checked="checked"> 组
                                                    @else
                                                        @if($me->user_type == 41)
                                                            <input type="radio" name="team_type" value="21" checked="checked"> 组
                                                        @else
                                                            <input type="radio" name="team_type" value="21"> 组
                                                        @endif
                                                    @endif
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        </div>




                        {{--项目名称--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 团队名称</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="name" placeholder="团队名称" value="{{ $data->name or '' }}">
                            </div>
                        </div>

                        {{--资费单价--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 资费单价</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="pre_unit_price" placeholder="资费单价" value="{{ $data->pre_unit_price or '' }}">
                            </div>
                        </div>



                        {{--api--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> API_企业名称</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="api_client_name" placeholder="API_客户名称" value="{{ $data->api_client_name or '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> API_用户名</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="api_user_name" placeholder="API_用户名" value="{{ $data->api_user_name or '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> API_ID</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="api_id" placeholder="API_ID" value="{{ $data->api_id or '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> API_Password</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="api_password" placeholder="API_Password" value="{{ $data->api_password or '' }}">
                            </div>
                        </div>


                        {{--排序--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 排序</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="rank" placeholder="排序" value="{{ $data->rank or '' }}">
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
                url: "{{ url('/company/team-edit') }}",
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
