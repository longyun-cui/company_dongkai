@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }}
@endsection




@section('title')<span class="box-title">首页</span>@endsection
@section('header')<span class="box-title">首页</span>@endsection
{{--@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection--}}
{{--@section('breadcrumb')--}}
{{--    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>--}}
{{--    <li><a href="#"><i class="fa "></i>Here</a></li>--}}
{{--@endsection--}}
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="nav-tabs-custom" id="index-nav-box">


            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="active" id="home">
                    <a href="#tab-home" data-toggle="tab" aria-expanded="true">
                        <i class="fa fa-home text-green"></i> 首页
                    </a>
                </li>
            </ul>


            {{--content--}}
            <div class="tab-content">

                <div class="tab-pane active" id="tab-home">
                </div>

            </div>


        </div>

    </div>
</div>


@include(env('TEMPLATE_DK_ADMIN').'component.common')

@include(env('TEMPLATE_DK_ADMIN').'component.department.department-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.staff.staff-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.company.company-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.client.client-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.project.project-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.location.location-edit')
@include(env('TEMPLATE_DK_ADMIN').'component.order.order-edit')


<div class="component-container _none">

    @include(env('TEMPLATE_DK_ADMIN').'component.department.department-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.staff.staff-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.company.company-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.client.client-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.project.project-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.location.location-list')

    @include(env('TEMPLATE_DK_ADMIN').'component.order.order-list')
    @include(env('TEMPLATE_DK_ADMIN').'component.delivery.delivery-list')


    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.comprehensive.statistic-index')

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.export.statistic-export')

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.company.statistic-company-overview')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.company.statistic-company-daily')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.project.statistic-marketing-project')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.client.statistic-marketing-client')


    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-overview')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-rank')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-recent')

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-inspector-overview')

{{--    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-deliverer-overview')--}}

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.project.statistic-production-project')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.department.statistic-production-department')

</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
    .btn-app>.badge { position: absolute; top: -6px; right: -10px; font-size: 12px; font-weight: 400; }
    .tableArea table { min-width:1380px; }
    .tableArea.full table { width:100% !important; min-width:1200px; }
    .tableArea table tr th,
    .tableArea table tr td { white-space:nowrap; }
    .white-space-normal { white-space:normal !important; }
</style>
@endsection



@section('custom-js')
@endsection
@section('custom-script')


    @include(env('TEMPLATE_DK_ADMIN').'component.department.department-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.department.department-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.staff.staff-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.staff.staff-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.company.company-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.company.company-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.client.client-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.client.client-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.project.project-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.project.project-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.location.location-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.location.location-edit-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.order.order-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.order.order-list-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.delivery.delivery-list-datatable')


    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.comprehensive.statistic-index-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.export.statistic-export-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.export.statistic-export-script')

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.company.statistic-company-overview-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.company.statistic-company-daily-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.project.statistic-marketing-project-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.marketing.client.statistic-marketing-client-datatable')


    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-overview-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-rank-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-caller-recent-datatable')


{{--    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.staff.statistic-deliverer-overview-datatable')--}}

    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.project.statistic-production-project-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'component.statistic.production.department.statistic-production-department-datatable')



@endsection