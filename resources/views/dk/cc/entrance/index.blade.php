@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">


        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-home" data-toggle="tab" aria-expanded="true">首页</a></li>
                <li class=""><a href="#timeline" data-toggle="tab" aria-expanded="false">Timeline</a></li>
                <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Settings</a></li>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="tab-home">
                </div>

                <div class="tab-pane" id="timeline">
                </div>

                <div class="tab-pane" id="settings">
                </div>

            </div>
            <!-- /.tab-content -->
        </div>


    </div>
</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
    .btn-app>.badge { position: absolute; top: -6px; right: -10px; font-size: 12px; font-weight: 400; }
</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {


    });
</script>
@endsection