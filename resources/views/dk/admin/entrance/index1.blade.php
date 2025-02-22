@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
{{--@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection--}}
{{--@section('breadcrumb')--}}
{{--    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>--}}
{{--    <li><a href="#"><i class="fa "></i>Here</a></li>--}}
{{--@endsection--}}
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="nav-tabs-custom">


            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="active" id="home"><a href="#tab-home" data-toggle="tab" aria-expanded="true">首页</a></li>
            </ul>


            {{--content--}}
            <div class="tab-content">

                <div class="tab-pane active" id="tab-home">
                    @component(env('TEMPLATE_DK_ADMIN').'page.home')
                    @endcomponent
                </div>

                <div class="tab-pane" id="tab-order-list">
                    @include(env('TEMPLATE_DK_ADMIN').'page.service.order.order-list')
                    @include(env('TEMPLATE_DK_ADMIN').'page.service.order.order-operate-record')
                </div>

                <div class="tab-pane" id="tab-department-list">
                    @include(env('TEMPLATE_DK_ADMIN').'page.company.department.department-list')
                    @include(env('TEMPLATE_DK_ADMIN').'page.company.department.department-operate-record')
                </div>

            </div>


        </div>

    </div>
</div>


<div class="component-container _none">

{{--        @component(env('TEMPLATE_DK_ADMIN').'page.department-list')--}}


{{--    @include(env('TEMPLATE_DK_ADMIN').'component.order-create')--}}

</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
    .btn-app>.badge { position: absolute; top: -6px; right: -10px; font-size: 12px; font-weight: 400; }
    .tableArea table { min-width:1380px; }
    .tableArea.full table { width:100% !important; min-width:1200px; }
    .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }
</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')

    @include(env('TEMPLATE_DK_ADMIN').'page.service.order.order-list-datatable-script')
    @include(env('TEMPLATE_DK_ADMIN').'page.service.order.order-operate-record-datatable-script')
    @include(env('TEMPLATE_DK_ADMIN').'page.service.order.order-list-script')

    @include(env('TEMPLATE_DK_ADMIN').'page.company.department.department-list-datatable-script')
    @include(env('TEMPLATE_DK_ADMIN').'page.company.department.department-operate-record-datatable-script')
    @include(env('TEMPLATE_DK_ADMIN').'page.company.department.department-list-script')

@endsection