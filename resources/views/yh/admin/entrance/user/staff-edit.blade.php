@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑用户' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '用户列表' }}</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">{{ $title_text or '' }}</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="category" value="{{ $category or 'user' }}" readonly>
                <input type="hidden" name="type" value="{{ $type or 'user' }}" readonly>


                {{--类别--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2">类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->user_type, [0,1]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 11))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            <input type="radio" name="user_type" value="11" checked="checked"> 总经理
                                            {{--<input type="radio" name="user_type" value=11--}}
                                            {{--@if($operate == 'edit' && $data->user_type == 11) checked="checked" @endif--}}
                                            {{--> 总经理--}}
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11,81]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 88))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 88)
                                                <input type="radio" name="user_type" value="88" checked="checked"> 客服
                                            @else
                                                <input type="radio" name="user_type" value="88" checked="checked"> 客服
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11,81]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 84))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 84)
                                                <input type="radio" name="user_type" value="84" checked="checked"> 客服主管
                                            @else
                                                <input type="radio" name="user_type" value="84"> 客服主管
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 81))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 81)
                                                <input type="radio" name="user_type" value="81" checked="checked"> 客服经理
                                            @else
                                                <input type="radio" name="user_type" value="81"> 客服经理
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 77))
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->user_type == 77)
                                            <input type="radio" name="user_type" value="77" checked="checked"> 质检员
                                        @else
                                            <input type="radio" name="user_type" value="77"> 质检员
                                        @endif
                                    </label>
                                </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 71))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 71)
                                                <input type="radio" name="user_type" value="71" checked="checked"> 质检经理
                                            @else
                                                <input type="radio" name="user_type" value="71"> 质检经理
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                        </div>
                    </div>
                </div>

                {{--上级--}}
                <div class="form-group superior-box" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 上级</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="superior_id" id="select2-superior" data-type="">
                            @if($operate == 'edit' && $data->superior_id)
                                <option data-id="{{ $data->superior_id or 0 }}" value="{{ $data->superior_id or 0 }}">{{ $data->superior->true_name }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{--部门-大区--}}
                @if(in_array($me->user_type, [0,1,11]))
                <div class="form-group department-box department-district-box" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 大区</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="department_district_id" id="select2-department-district" data-type="">
                            @if($operate == 'edit' && $data->department_district_id)
                                <option data-id="{{ $data->department_district_id or 0 }}" value="{{ $data->department_district_id or 0 }}">{{ $data->department_district_er->name }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>
                @endif

                {{--部门-小组--}}
                <div class="form-group department-box department-group-box" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 小组</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="department_group_id" id="select2-department-group" data-type="">
                            @if($operate == 'edit' && $data->department_group_id)
                                <option data-id="{{ $data->department_group_id or 0 }}" value="{{ $data->department_group_id or 0 }}">{{ $data->department_group_er->name }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>


                {{--手机--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 登录手机</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="mobile" placeholder="登录手机" value="{{ $data->mobile or '' }}">
                    </div>
                </div>
                {{--真实姓名--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 真实姓名</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="true_name" placeholder="真实姓名" value="{{ $data->true_name or '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">用户名</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="username" placeholder="用户名" value="{{ $data->username or '' }}">
                    </div>
                </div>
                {{--描述--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
                    </div>
                </div>

                {{--头像--}}
                <div class="form-group">
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
                @if($operate == 'create')
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
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection




@section('custom-script')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/select2-4.0.5.min.js') }}"></script>
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/user/staff-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/user/staff-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        // 【选择用户类型】
        $("#form-edit-item").on('click', "input[name=user_type-]", function() {
            // checkbox
//            if($(this).is(':checked'))
//            {
//                $('.time-show').show();
//            }
//            else
//            {
//                $('.time-show').hide();
//            }

            // $("#select2-superior").find("option[value=0]").attr("selected",true);
            // radio
            var $value = $(this).val();
            // if($value == 77 || $value == 84 || $value == 88)
            if($value == 77)
            {
                $('.superior-box').show();
            }
            else
            {
                $('.superior-box').hide();
            }

            if($value == 77)
            {
                $('#select2-superior').prop('data-type','inspector');
            }
            else if($value == 84)
            {
                $('#select2-superior').prop('data-type','customer_service_supervisor');
            }
            else if($value == 88)
            {
                $('#select2-superior').prop('data-type','customer_service');
            }
            else
            {
                $('#select2-superior').prop('data-type','');
            }
            console.log($('#select2-superior').prop('data-type'));

            //
            $('#select2-superior').select2({
                ajax: {
                    url: "{{ url('/user/user_select2_superior') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: params.page,
                            type: $('#select2-superior').prop('data-type')
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

        //
        $('#select2-superior').select2({
            ajax: {
                url: "{{ url('/user/user_select2_superior') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-superior').prop('data-type')
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

        // var $user_type = $("input[name=user_type]").val();
        //
        // if($user_type == 77 || $user_type == 84 || $user_type == 88)
        // {
        //     $('.superior-box').show();
        // }
        // else
        // {
        //     $('.superior-box').hide();
        // }
        //
        // if($user_type == 77)
        // {
        //     $('#select2-superior').prop('data-type','inspector');
        // }
        // else if($user_type == 84)
        // {
        //     $('#select2-superior').prop('data-type','customer_service_supervisor');
        // }
        // else if($user_type == 88)
        // {
        //     $('#select2-superior').prop('data-type','customer_service');
        // }
        // else
        // {
        //     $('#select2-superior').prop('data-type','');
        // }

        // console.log($("input[name=user_type]").val())
        // console.log($('#select2-superior').prop('data-type'));
        // console.log($("#select2-superior").find('option:checked').val());






        // 【选择用户类型】
        $("#form-edit-item").on('click', "input[name=user_type]", function() {

            // radio
            var $value = $(this).val();
            if($value == 81 || $value == 84 || $value == 88)
            {
                $('.department-box').show();
                $('.superior-box').hide();

                if($value == 81)
                {
                    $('.department-group-box').hide();
                }
                else if($value == 84)
                {
                    $('.department-group-box').show();
                }
                else if($value == 88)
                {
                    $('.department-group-box').show();
                }
            }
            else
            {
                $('.department-box').hide();
                if($value == 77)
                {
                    $('.superior-box').show();
                }
                else $('.superior-box').hide();
            }
        });

        var $user_type = $("input[name=user_type]").val();

        if($user_type == 81 || $user_type == 84 || $user_type == 88)
        {
            $('.department-box').show();
            $('.superior-box').hide();
        }
        else
        {
            $('.department-box').hide();
        }

        if($user_type == 81)
        {
            $('.department-group-box').hide();
        }
        else if($user_type == 84)
        {
            $('.department-group-box').show();
        }
        else if($user_type == 88)
        {
            $('.department-group-box').show();
        }
        else if($user_type == 77)
        {
            $('.superior-box').show();
        }


        //
        $('#select2-department-district').select2({
            ajax: {
                url: "{{ url('/user/user_select2_department?type=district') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
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
        $("#select2-department-district").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                //
                // 清空原有选项 得到select标签对象 Jquery写法
                // var $select = $('#select2-department-group')[0];
                // $select.length = 0;

                $('#select2-department-group').html(''); // 清空原有选项

                // 去除选中值
                // $('#select2-department-group').val(null).trigger('change');
                // $('#select2-department-group').val("").trigger('change');

                $('#select2-department-group').select2({
                    ajax: {
                        url: "{{ url('/user/user_select2_department?type=group') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page,
                                superior_id: $id
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
            }
        });


        $('#select2-department-group').select2({
            ajax: {
                url: "{{ url('/user/user_select2_department?type=group') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        @if($me->user_type == 81)
                        superior_id: {{ $me->department_district_id or 0 }}
                        @else
                        superior_id: {{ $data->department_district_id or 0 }}
                        @endif
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
