@extends(env('TEMPLATE_YH_STAFF').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or '【l】' }}@endif{{ $head_title or '编辑基本资料' }} - 员工系统 - 兆益信息
@endsection
@section('meta_author')@endsection
@section('meta_title')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




@section('sidebar')
    {{--@include(env('TEMPLATE_YH_STAFF').'component.sidebar.sidebar-root')--}}
@endsection
@section('header','')
@section('description','编辑基本资料 - 员工系统 - 兆益信息')
@section('breadcrumb')
@endsection
@section('content')
<div class="container">

    {{--左侧--}}
    <div class="main-body-section main-body-left-section section-wrapper page-item">
        <div class="main-body-left-container bg-white">

            <div class="box box-info form-container">

                <div class="box-header with-border" style="margin:16px 0;">
                    <h3 class="box-title">编辑基本资料</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-administrator">
                <div class="box-body">

                    {{ csrf_field() }}

                    {{--用户名--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">用户名</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="username" placeholder="用户名" value="{{ $data->username or '' }}">
                        </div>
                    </div>
                    {{--真实名称--}}
                    <div class="form-group _none">
                        <label class="control-label- col-md-9">真实名称</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="true_name" placeholder="真实名称" value="{{ $data->true_name or '' }}">
                        </div>
                    </div>
                    {{--描述/简介--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">描述 / 简介</label>
                        <div class="col-md-12 ">
                            <textarea class="form-control" name="description" rows="3" placeholder="描述 / 简介">{{ $data->description or ''}}</textarea>
                        </div>
                    </div>

                    {{--联系地址--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">地址</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="contact_address" placeholder="地址" value="{{ $data->contact_address or '' }}">
                        </div>
                    </div>

                    {{--网站--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">网站</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="website" placeholder="网站地址，请携带http或https" value="{{ $data->website or '' }}">
                        </div>
                    </div>

                    {{--手机号--}}
                    {{--<div class="form-group">--}}
                        {{--<label class="control-label- col-md-2">手机号</label>--}}
                        {{--<div class="col-md-12 ">--}}
                            {{--<input type="text" class="form-control" name="mobile" placeholder="请输入手机号" value="{{ $data->mobile or '' }}">--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--邮箱--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">邮箱</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="email" placeholder="邮箱" value="{{ $data->email or '' }}">
                        </div>
                    </div>
                    {{--QQ--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">QQ</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="QQ_number" placeholder="QQ" value="{{ $data->QQ_number or '' }}">
                        </div>
                    </div>
                    {{--微信号--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">微信号</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="wx_id" placeholder="微信号" value="{{ $data->wx_id or '' }}">
                        </div>
                    </div>
                    {{--微信二维码--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">微信二维码</label>
                        <div class="col-md-12 fileinput-group">

                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    @if(!empty($data->wx_qr_code_img))
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->wx_qr_code_img) }}" alt="" />
                                    @endif
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail">
                                </div>
                                <div class="btn-tool-group">
                                    <span class="btn-file">
                                        <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                        <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                        <input type="file" name="wx_qr_code" />
                                    </span>
                                    <span class="">
                                        <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                                    </span>
                                </div>
                            </div>
                            <div id="titleImageError1" style="color: #a94442"></div>

                        </div>
                    </div>
                    {{--微博名称--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">微博名称</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="wb_name" placeholder="微博名称" value="{{ $data->wb_name or '' }}">
                        </div>
                    </div>
                    {{--微博地址--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">微博地址</label>
                        <div class="col-md-12 ">
                            <input type="text" class="form-control" name="wb_address" placeholder="微博地址，请携带http或https" value="{{ $data->wb_address or '' }}">
                        </div>
                    </div>

                    {{--portrait--}}
                    <div class="form-group">
                        <label class="control-label- col-md-9">头像</label>
                        <div class="col-md-12 fileinput-group">

                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    @if(!empty($data->portrait_img))
                                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img.'?'.rand(0,99)) }}" alt="" />
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

                </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-12 col-md-offset-2-">
                            <button type="button" onclick="" class="btn btn-success" id="edit-info-submit"><i class="fa fa-check"></i>提交</button>
                            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{--右侧--}}
    <div class="main-body-section main-body-right-section section-wrapper hidden-xs">
        {{--@include(env('TEMPLATE_YH_STAFF').'component.right-side.right-root')--}}
    </div>

</div>
@endsection




@section('custom-js')
<script>
    $(function() {
        // 添加or修改产品信息
        $("#edit-info-submit").on('click', function() {
            var options = {
                url: "{{ url('/my-account/my-profile-info-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/my-account/my-profile-info-index') }}";
                    }
                }
            };
            $("#form-edit-administrator").ajaxSubmit(options);
        });
    });
</script>
@endsection