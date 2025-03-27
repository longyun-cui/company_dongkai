@extends(env('TEMPLATE_DK_CLIENT').'reconciliation.layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '对账系统' }}
@endsection




@section('header','Admin')
@section('description')对账系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">


        <div class="nav-tabs-custom" id="index-nav-box">

            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="nav-item active" id="home">
                    <a href="#tab-home" data-toggle="tab" aria-expanded="true"><i class="fa fa-home text-black"></i> 首页</a>
                </li>
            </ul>


            {{--content--}}
            <div class="tab-content">

                @include(env('TEMPLATE_DK_CLIENT').'reconciliation.home.home')

            </div>

        </div>


    </div>
</div>




<div class="component-container _none">

    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-list')
    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-daily')

    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.daily.daily-list')

    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.trade.trade-list')

</div>



    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-edit')
    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.daily.daily-edit')


    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.operation.operation-record')


    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.common.common')


@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }

    .toggle-button {
        position: relative;
        width: 50px;
        height: 25px;
        background-color: #ccc;
        border: none;
        border-radius: 15px;
    }

    .toggle-handle {
        position: absolute;
        top: 0;
        width: 25px;
        height: 25px;
        background-color: #fff;
        border-radius: 50%;
    }

    .toggle-button.toggle-button-on { background-color: #66a3cc; transition: background-color 0.1s; }
    .toggle-button.toggle-button-off { background-color: #dddddd; transition: background-color 0.1s; }

    .toggle-button.toggle-button-on .toggle-handle { right: 0; background-color: #20e28b; transition: right 0.1s; }
    .toggle-button.toggle-button-off .toggle-handle { left: 0; background-color: #e00000; transition: left 0.1s; }
</style>
@endsection



@section('custom-js')
@endsection
@section('custom-script')

    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-daily-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.project.project-edit-script')

    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.daily.daily-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.daily.daily-edit-script')


    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.trade.trade-list-datatable')


    @include(env('TEMPLATE_DK_CLIENT').'reconciliation.operation.operation-record-datatable')


<script>
    $(function() {


    });
</script>
@endsection