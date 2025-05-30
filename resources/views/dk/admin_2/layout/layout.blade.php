<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="shortcut icon" type="image/ico" href="{{ env('FAVICON_DK_CHOICE') }}">
    <link rel="shortcut icon" type="image/png" href="{{ env('FAVICON_DK_CHOICE') }}">
    <link rel="icon" sizes="16x16 32x32 64x64" href="{{ env('FAVICON_DK_CHOICE') }}">
    <link rel="icon" type="image/png" sizes="196x196" href="{{ env('FAVICON_DK_CHOICE') }}">

    <title>@yield('head_title')</title>
    <meta name="_token" content="{{ csrf_token() }}"/>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.6 -->
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}
    {{--<link rel="stylesheet" href="/AdminLTE/bootstrap/css/bootstrap.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/AdminLTE/bootstrap/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">--}}
    {{--<link href="https://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/font-awesome-4.5.0.min.css') }}">

    <!-- Ionicons -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/ionicons/2.0.1/css/ionicons.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/ionicons-2.0.1.min.css') }}">

    <!-- Theme style -->
    {{--<link rel="stylesheet" href="/AdminLTE/dist/css/AdminLTE.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/AdminLTE/dist/css/AdminLTE.min.css') }}">
    {{--<link href="https://cdn.bootcss.com/admin-lte/2.3.11/css/AdminLTE.min.css" rel="stylesheet">--}}
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect.
    -->
    {{--<link rel="stylesheet" href="/AdminLTE/dist/css/skins/skin-blue.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/AdminLTE/dist/css/skins/skin-blue.min.css') }}">

    {{--<link rel="stylesheet" href="/AdminLTE/plugins/iCheck/all.css">--}}
    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/iCheck/all.css') }}">
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/iCheck/1.0.2/skins/all.css">--}}
    {{--<link rel="stylesheet" href="{{ asset('/resource/component/css/iCheck-1.0.2-skins-all.css') }}">--}}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    {{--<!--[if lt IE 9]>--}}
    {{--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>--}}
    {{--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>--}}
    {{--<![endif]-->--}}
    {{--<link href="https://cdn.bootcss.com/bootstrap-modal/2.2.6/css/bootstrap-modal.min.css" rel="stylesheet">--}}


    <link rel="stylesheet" href="{{ asset('/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/resource/component/css/jquery.dataTables-1.13.1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/resource/component/css/fixedColumns.dataTables.min.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap-fileinput/4.4.8/css/fileinput.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/fileinput-4.4.8.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/resource/component/css/fileinput-only.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/bootstrap-datetimepicker-4.17.47.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/resource/component/css/bootstrap-datepicker-1.9.0.min.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/bootstrap-switch-3.3.4.min.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/Swiper/4.2.2/css/swiper.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/swiper-4.2.2.min.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/lightcase/2.5.0/css/lightcase.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/lightcase-2.5.0.min.css') }}">

    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">


    <link rel="stylesheet" href="{{ asset('/resource/common/css/common.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('/resource/common/css/AdminLTE/index.css') }}">

    @yield('css')
    @yield('style')
    @yield('custom-css')
    @yield('custom-style')

    {{--layout-style--}}
    @include(env('TEMPLATE_DK_ADMIN_2').'layout.layout-style')

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse-">
<div class="wrapper">


    {{--main-header--}}
    @include(env('TEMPLATE_DK_ADMIN_2').'layout.main-header')

    {{--main-sidebar--}}
    @include(env('TEMPLATE_DK_ADMIN_2').'layout.main-sidebar')

    {{--main-content--}}
    @include(env('TEMPLATE_DK_ADMIN_2').'layout.main-content')

    {{--main-footer--}}
{{--    @include(env('TEMPLATE_DK_ADMIN_2').'layout.main-footer')--}}

    {{--control-sidebar--}}
    @include(env('TEMPLATE_DK_ADMIN_2').'layout.control-sidebar')


</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

{{--<!-- jQuery 2.2.3 -->--}}
{{--<script src="/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js"></script>--}}
<script src="{{ asset('/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

{{--<!-- Bootstrap 3.3.6 -->--}}
{{--<script src="/AdminLTE/bootstrap/js/bootstrap.min.js"></script>--}}
<script src="{{ asset('/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>

{{--<!-- AdminLTE App -->--}}
{{--<script src="/AdminLTE/dist/js/app.min.js"></script>--}}
<script src="{{ asset('/AdminLTE/dist/js/app.min.js') }}"></script>

<script src="{{ asset('AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/jquery.dataTables-1.13.1.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/dataTables.fixedColumns.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/iCheck/1.0.2/icheck.min.js"></script>--}}
{{--<script src="{{ asset('/resource/component/js/icheck-1.0.2.min.js') }}"></script>--}}
{{--<script src="/AdminLTE/plugins/iCheck/icheck.min.js"></script>--}}
<script src="{{ asset('/AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/jqueryui/1.12.1/jquery-ui.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/jquery-ui-1.12.1.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/bootstrap-modal/2.2.6/js/bootstrap-modal.min.js"></script>--}}


<script src="{{ asset('/resource/component/js/layer-3.5.1/layer.js') }}"></script>


{{--<script src="https://cdn.bootcss.com/bootstrap-fileinput/4.4.8/js/fileinput.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/fileinput-4.4.8.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/fileinput-only-1.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/jquery.form/4.2.2/jquery.form.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/jquery.form-4.2.2.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/moment.js/2.19.0/moment.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/moment-2.19.0.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/moment-2.19.0-locale-zh-cn.js') }}"></script>
<script src="{{ asset('/resource/component/js/moment-2.19.0-locale-ko.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>--}}
{{--<script src="https://cdn.bootcdn.net/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/bootstrap-datetimepicker-4.17.47.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/bootstrap-datetimepicker.zh-CN.js') }}" charset="UTF-8"></script>
<script src="{{ asset('/resource/component/js/bootstrap-datepicker-1.9.0.min.js') }}"></script>
<script src="{{ asset('/resource/component/js/bootstrap-datepicker-1.9.0.zh-CN.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/bootstrap-switch-3.3.4.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/lightcase/2.5.0/js/lightcase.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/lightcase-2.5.0.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/Swiper/4.2.2/js/swiper.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/swiper-4.2.2.min.js') }}"></script>

{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/resource/component/js/select2-4.0.5.min.js') }}"></script>

{{--<script src="{{ asset('/resource/component/js/echarts-3.7.2.min.js') }}"></script>--}}
<script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>


{{--layout-script--}}
@include(env('TEMPLATE_DK_ADMIN_2').'layout.layout-script')


@yield('js')
@yield('script')
@yield('custom-js')
@yield('custom-script')


</body>
</html>
