@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑用户' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '驾驶员列表' }}</a></li>
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
                <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="category" value="{{ $category or 'user' }}" readonly>
                <input type="hidden" name="type" value="{{ $type or 'user' }}" readonly>


                {{--主驾 姓名 & 手机号--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 主驾姓名&手机号</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_name" placeholder="主驾姓名" value="{{ $data->driver_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_phone" placeholder="主驾手机号" value="{{ $data->driver_phone or '' }}">
                        </div>
                    </div>
                </div>
                {{--主驾-身份证号--}}
                <div class="form-group">
                    <label class="control-label col-md-2">主驾身份证号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="driver_ID" placeholder="主驾身份证号" value="{{ $data->driver_ID or '' }}">
                    </div>
                </div>
                {{--主驾-职称&入职时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2">主驾-职称&入职时间</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_title" placeholder="主驾职称" value="{{ $data->driver_title or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date_picker" name="driver_entry_time" placeholder="主驾入职时间" value="{{ $data->driver_entry_time or '' }}" readonly="readonly">
                        </div>
                    </div>
                </div>
                {{--主驾-紧急联系人&电话--}}
                <div class="form-group">
                    <label class="control-label col-md-2">主驾-紧急联系人&电话</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="emergency_contact_name" placeholder="主驾紧急联系人" value="{{ $data->emergency_contact_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="emergency_contact_phone" placeholder="主驾紧急联系电话" value="{{ $data->emergency_contact_phone or '' }}">
                        </div>
                    </div>
                </div>


                {{--null--}}
                <div class="form-group"></div>


                {{--副驾-姓名&手机号--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾-姓名&手机号</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="sub_driver_name" placeholder="副驾姓名" value="{{ $data->sub_driver_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="sub_driver_phone" placeholder="副驾电话" value="{{ $data->sub_driver_phone or '' }}">
                        </div>
                    </div>
                </div>
                {{--副驾-身份证号--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾-身份证号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="sub_driver_ID" placeholder="副驾身份证号" value="{{ $data->sub_driver_ID or '' }}">
                    </div>
                </div>
                {{--副驾-职称&入职时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾-职称&入职时间</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="sub_driver_title" placeholder="副驾职称" value="{{ $data->sub_driver_title or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date_picker" name="sub_driver_entry_time" placeholder="副驾入职时间" value="{{ $data->sub_driver_entry_time or '' }}" readonly="readonly">
                        </div>
                    </div>
                </div>
                {{--副驾-紧急联系人&电话--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾-紧急联系人&电话</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="sub_contact_name" placeholder="副驾紧急联系人" value="{{ $data->sub_contact_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="sub_contact_phone" placeholder="副驾紧急联系电话" value="{{ $data->sub_contact_phone or '' }}">
                        </div>
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


                {{--主驾-驾驶证--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">主驾-驾驶证</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->driver_licence))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_licence) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_licence) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="driver_licence_file" multiple- >
                    </div>
                </div>
                {{--主驾-资格证--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">主驾-资格证</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->driver_certification))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_certification) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_certification) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="driver_certification_file" multiple- >
                    </div>
                </div>
                {{--主驾-身份证-正页--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">主驾-身份证-正页</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->driver_ID_front))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="driver_ID_front_file" multiple- >
                    </div>
                </div>
                {{--主驾-身份证-副页--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">主驾-身份证-副页</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->driver_ID_back))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_back) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_back) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="driver_ID_back_file" multiple- >
                    </div>
                </div>

                {{--副驾-驾驶证--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">副驾-驾驶证</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->sub_driver_licence))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_licence) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_licence) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="sub_driver_licence_file" multiple- >
                    </div>
                </div>
                {{--副驾-资格证--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">副驾-资格证</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->sub_driver_certification))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_certification) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_certification) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="sub_driver_certification_file" multiple- >
                    </div>
                </div>
                {{--副驾-身份证-正页--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">副驾-身份证-正页</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->sub_driver_ID_front))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_front) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_front) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="sub_driver_ID_front_file" multiple- >
                    </div>
                </div>
                {{--副驾-身份证-副页--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">副驾-身份证-副页</label>
                    <div class="col-md-8 fileinput-group">

                        @if(!empty($data->sub_driver_ID_back))
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_back) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_back) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                        <input type="file" class="file-multiple-images" name="sub_driver_ID_back_file" multiple- >
                    </div>
                </div>


                {{--多图上传--}}
                <div class="form-group">
                    <label class="control-label col-md-2">图片资料</label>
                    <div class="col-md-8 fileinput-group">
                        @if(!empty($data->attachment_list) && count($data->attachment_list) > 0)
                            @foreach($data->attachment_list as $img)
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail">
                                        <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$img->attachment_src) }}">
                                            <img src="{{ url(env('DOMAIN_CDN').'/'.$img->attachment_src) }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                        @endif
                    </div>

                    <div class="col-md-8 col-md-offset-2 ">
                        <input id="multiple-images" type="file" class="file-multiple-images" name="multiple_images[]" multiple >
                    </div>
                </div>


                {{--单图上传--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">头像</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                                @if(!empty($data->image_src))
                                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->image_src) }}">
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$$data->image_src) }}" alt="" />
                                    </a>
                                @endif
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                                <span class="btn-file">
                                    <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                    <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                    <input type="file" name="image_src_" />
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




@section('custom-script')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
<script>
    $(function() {

        $(".file-multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/user/driver-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/user/driver-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });

    });
</script>
@endsection
