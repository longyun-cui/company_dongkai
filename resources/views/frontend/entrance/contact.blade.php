@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title')联系我们-{{ config('company.info.short_name') }}@endsection
@section('meta_author')@endsection
@section('meta_title')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




{{--header--}}
@section('component-header')
    @include('frontend.component.header')
@endsection


{{--footer--}}
@section('component-footer')
    @include('frontend.component.footer')
@endsection




{{--custom-content--}}
@section('custom-content')

    <div id="site-banner" class="text-center clearfix">
        <div class="container">
            <h1 class="title wow slideInLeft">联系我们</h1>
            <ol class="breadcrumb wow slideInRight">
                <li><a href="index.html">主页</a></li>
                <li class="active">联系我们</li>
            </ol>
        </div>
    </div>
    <div id="contact-page" class="contact-page-var-one">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-md-7 col-md-8">
                    <div class="contact-form-wrapper">
                        <h3 class="entry-title">联系我们</h3>
                        <div class="contents">
                            <p>我们会尽快联系您！</p>
                        </div>
                        <form class="contact-form" id="form-contact">
                            <p class="form-author common">
                                <input id="contact-name" name="name" type="text" placeholder="您的姓名 *" aria-required="true" required="required">
                            </p>
                            <p class="form-phone common">
                                {{csrf_field()}}
                                <input id="contact-mobile" name="mobile" type="text" placeholder="您的电话 *" aria-required="true" required="required" >
                            </p>
                            <p class="form-comment">
                                <textarea id="contact-content" name="content" placeholder="留言" cols="45" rows="7" ></textarea>
                            </p>
                            <p class="form-submit">
                                <button type="button" id="contact-submit" class="submit-btn btn btn-default btn-lg btn-3d" data-hover="提交">提交</button>
                                <span id="ajax-loader"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i><span class="sr-only">Loading...</span></span>
                            </p>
                            <div id="error-container"></div>
                            <div id="message-container"></div>
                        </form>
                    </div>
                </div>
                <div class="col-sm-6 col-md-5 col-lg-4">
                    <div class="contact-page-contents clearfix">
                        <div class="row">
                            <div class="col-md-12">
                                <i class="fa fa-map-marker"></i>
                                <div class="contents">
                                    <h6 class="title">地址</h6>
                                    <address> {{ config('company.info.address') }} </address>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <i class="fa fa-phone"></i>
                                <div class="contents">
                                    <h5 class="title">联系我们</h5>
                                    <ul>
                                        <li>电话 : <a href="tel:{{ config('company.info.telephone') }}">{{ config('company.info.telephone') }}</a></li>
                                        <li>联系人 : {{ config('company.info.linkman') }}</li>
                                        <li>手机 : <a href="tel:{{ config('company.info.mobile') }}">{{ config('company.info.mobile') }}</a></li>
                                        <li>邮箱 : <a href="mailto:{{ config('company.info.email') }}">{{ config('company.info.email') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="map-canvas"></div>
                </div>
            </div>
        </div>
    </div>

@endsection




{{--script--}}
@section('custom-script')

    <script>
        $(function() {
            // 添加or编辑
            $("#contact-submit").on('click', function() {

                var form = $("#form-contact");
                var name = $("#contact-name");
                var mobile = $("#contact-mobile");
                var content = $("#contact-content");

                var name_val = name.val();
                var mobile_val = mobile.val();

                if(name == "")
                {
                    layer.msg("名字不能为空");
                    name.focus();
                    return false;
                }

                var filter=/^1[3|4|5|7|8][0-9]\d{8}$/;
                if(!filter.test(mobile.val()))
                {
                    layer.msg("请输入正确的手机号!");
                    mobile.focus();
                    mobile.value="";
                    return false;
                }

                var options = {
                    url: "{{url('/message/contact')}}",
                    type: "post",
                    dataType: "json",
                    // target: "#div2",
                    success: function (data) {
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg(data.msg);
                            name.val('');
                            mobile.val('');
                            content.val('');
                            {{--location.href = "{{url('/admin/item/list')}}";--}}
                                return true;
                        }
                    }
                };
                form.ajaxSubmit(options);
            });
        });
    </script>

@endsection
