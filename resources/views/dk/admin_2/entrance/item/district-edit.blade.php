@extends(env('TEMPLATE_DK_ADMIN_2').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑项目' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '项目列表' }}</a></li>
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




                        {{--城市--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 城市</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="district_city" placeholder="城市" value="{{ $data->district_city or '' }}">
                            </div>
                        </div>

                        {{--地区--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 地区</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="district_district" placeholder="地区" value="{{ $data->district_district or '' }}">
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
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">
@endsection




@section('custom-js')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {

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

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/district/district-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/district/district-list') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });



    });
</script>
@endsection
