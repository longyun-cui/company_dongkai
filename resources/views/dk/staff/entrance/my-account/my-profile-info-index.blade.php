@extends(env('TEMPLATE_ZY_STAFF').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))YH_@endif{{ $head_title or '基本资料' }} - 员工系统 - 兆益信息
@endsection
@section('meta_author')@endsection
@section('meta_title')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




@section('sidebar')
    {{--@include(env('TEMPLATE_ZY_STAFF').'component.sidebar.sidebar-root')--}}
@endsection
@section('header','')
@section('description','基本资料 - 员工系统 - 兆益信息')
@section('breadcrumb')
@endsection
@section('content')
<div class="container">

    {{--左侧--}}
    <div class="main-body-section main-body-left-section section-wrapper page-item">
        <div class="main-body-left-container bg-white">


            <div class="box box-info form-container">

                <div class="box-header with-border" style="margin: 15px 0;">
                    <h3 class="box-title">基本资料</h3>
                    <div class="pull-right">
                        <a href="{{ url('/my-account/my-profile-info-edit') }}">
                            <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-edit"></i> 编辑资料</button>
                        </a>
                    </div>
                </div>

                <form class="form-horizontal form-bordered">
                <div class="box-body">
                    {{--手机号--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-3">登录手机号：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->mobile or '' }}</label></div>
                        </div>
                    </div>
                    {{--昵称--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">用户名：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->username or '' }}</label></div>
                        </div>
                    </div>
                    {{--真实名称--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-3">真实名称：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->true_name or '' }}</label></div>
                        </div>
                    </div>
                    {{--描述/简介--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">描述/简介：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->description or '' }}</label></div>
                        </div>
                    </div>
                    {{--邮箱--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">邮箱：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->email or '' }}</label></div>
                        </div>
                    </div>
                    {{--QQ--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">QQ：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->QQ_number or '' }}</label></div>
                        </div>
                    </div>
                    {{--微信号--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">微信号：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->wx_id or '' }}</label></div>
                        </div>
                    </div>
                    {{--微信二维码--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">微信二维码：</label>
                        <div class="col-md-8 ">
                            <div class="info-img-block" style="width:120px;height:120px;overflow:hidden;">
                                <img src="{{ url(env('DOMAIN_CDN').'/'.$data->wx_qr_code_img) }}" alt="">
                            </div>
                        </div>
                    </div>
                    {{--微博名称--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">微博名称：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->wb_name or '' }}</label></div>
                        </div>
                    </div>
                    {{--微博地址--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">微博地址：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->wb_address or '' }}</label></div>
                        </div>
                    </div>
                    {{--网站--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">网站：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->website or '' }}</label></div>
                        </div>
                    </div>
                    {{--地址--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">地址：</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->contact_address or '' }}</label></div>
                        </div>
                    </div>
                    {{--portrait--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">头像：</label>
                        <div class="col-md-8 ">
                            <div class="info-img-block" style="width:120px;height:120px;overflow:hidden;">
                                <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt="">
                            </div>
                        </div>
                    </div>
                    {{--修改密码--}}
                    <div class="form-group">
                        <label class="control-label col-md-3">修改密码：</label>
                        <div class="col-md-8 ">
                            <a class="btn btn-danger _left" href="{{ url('/my-account/my-password-change') }}"><i class="fa fa-lock"></i> 修改密码</a>
                        </div>
                    </div>
                </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-3">
                            <a href="{{ url('/my-account/my-profile-info-edit') }}">
                                <button type="button" onclick="" class="btn btn-success"><i class="fa fa-edit"></i> 编辑信息</button>
                            </a>
                            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>


    {{--右侧--}}
    <div class="main-body-section main-body-right-section section-wrapper hidden-xs">
{{--        @include(env('TEMPLATE_ZY_STAFF').'component.right-side.right-root')--}}
    </div>

</div>
@endsection




@section('style')
    <style>
        .info-img-block {width: 100px;height: 100px;overflow: hidden;}
        .info-img-block img {width: 100%;}
    </style>
@endsection




@section('js')
<script>
    $(function() {
        // with plugin options
        $("#input-id").fileinput({'showUpload':false, 'previewFileType':'any'});
    });
</script>
@endsection
