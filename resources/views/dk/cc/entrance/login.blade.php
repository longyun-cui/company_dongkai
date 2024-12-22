@extends(env('TEMPLATE_DK_CC').'layout.auth')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))@endif{{ $title_text or '管理员登陆' }} - {{ config('info.info.short_name') }}
@endsection




@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="/admin"><b>{{ config('info.info.short_name') }}</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">调度系统登陆</p>

        <form action="/login" method="post" id="form-admin-login">
            {{ csrf_field() }}
            {{--<div class="form-group has-feedback">--}}
                {{--<input type="email" class="form-control" name="email" placeholder="邮箱">--}}
                {{--<span class="glyphicon glyphicon-envelope form-control-feedback"></span>--}}
            {{--</div>--}}
            <div class="form-group has-feedback">
                <input type="text" class="form-control" name="mobile" placeholder="手机">
                <span class="glyphicon glyphicon-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" placeholder="密码">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember" value="1"> 记住我
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="button" class="btn btn-primary btn-block btn-flat" id="admin-login-submit">登陆</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <div class="social-auth-links text-center" style="display: none">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> 微信登陆</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> 支付宝登陆</a>
        </div>
        <!-- /.social-auth-links -->

        <a href="#">忘记密码</a><br>

    </div>
    <!-- /.login-box-body -->
</div>
@endsection




@section('script')
<script>
    $(function() {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

        // 提交表单
        $("#admin-login-submit").on('click', function() {

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在登录</span>',
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
                url: "/login",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "/";
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.closeAll('loading');
                    layer.msg("服务器错误");
                },
                complete: function (jqXHR, textStatus) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    layer.closeAll('loading');
                }
            };

            $("#form-admin-login").ajaxSubmit(options);
        });
    });
</script>
@endsection
