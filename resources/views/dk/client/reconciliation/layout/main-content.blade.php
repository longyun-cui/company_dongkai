{{--<!-- Content Wrapper. Contains page content -->--}}
<div class="content-wrapper">
    {{--<!-- Content Header (Page header) -->--}}
    <section class="content-header _none">
        <h1>
            @yield('header')
            <small>@yield('description')</small>
        </h1>
        <ol class="breadcrumb">
            @yield('breadcrumb')
        </ol>
    </section>

    {{--<!-- Main content -->--}}
    <section class="content main-content" style="padding:8px 15px">
        @yield('content') {{--Your Page Content Here--}}
    </section>
    {{--<!-- /.content -->--}}
</div>
{{--<!-- /.content-wrapper -->--}}