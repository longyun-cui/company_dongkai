@extends(env('DK_STAFF__TEMPLATE').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '员工系统' }}
@endsection




@section('title')<span class="box-title">员工系统</span>@endsection
@section('header')<span class="box-title">员工系统</span>@endsection
{{--@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection--}}
{{--@section('breadcrumb')--}}
{{--    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>--}}
{{--    <li><a href="#"><i class="fa "></i>Here</a></li>--}}
{{--@endsection--}}
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="nav-tabs-custom" id="index-nav-box" style="width:100%;">


            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="active" id="home">
                    <a href="#tab-home" data-toggle="tab" aria-expanded="true" id="home-default">
                        <i class="fa fa-home text-green"></i> 首页
                    </a>
                </li>
            </ul>


            {{--content--}}
            <div class="tab-content" style="width:100%;">

                <div class="tab-pane active" id="tab-home" style="width:100%;">
                </div>

            </div>


        </div>

    </div>
</div>


<div class="component-container _none">

    {{--公司--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company-list')
    {{--部门--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department-list')
    {{--团队--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team-list')
    {{--员工--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff-list')


    {{--地域--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location-list')


    {{--客户--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client-list')
    {{--项目--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list')


    {{--工单--}}
    @if(in_array($me->staff_category,[0,1,9]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list')
    @elseif($me->staff_category == 41)
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list--for--CSD')
    @elseif($me->staff_category == 51)
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list--for--QID')--}}
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list')
    @elseif($me->staff_category == 61)
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list--for--CSD')--}}
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list')
    @elseif($me->staff_category == 71)
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list--for--OD')--}}
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list')
    @else
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list')
    @endif


    {{--交付--}}
    @if(in_array($me->staff_category,[0,1,9,71]))
    @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-dental.delivery-dental-list')
    @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-aesthetic.delivery-aesthetic-list')
    @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-luxury.delivery-luxury-list')
    @endif




    {{--交付--}}
    {{--生产--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.project.statistic-production-project')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.department.statistic-production-department')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.team.statistic-production-team')

    {{--员工--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-overview')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-rank')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-recent')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-daily')

    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-inspector-overview')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-deliverer-overview')

    {{--交付--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.project.statistic-marketing-project')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.client.statistic-marketing-client')

    {{--销售--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.company.statistic-company-overview')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.company.statistic-company-daily')


    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-daily')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-show')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-detail')

    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-daily')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-show')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-detail')


    @include(env('DK_STAFF__TEMPLATE').'component.statistic-call.statistic-task-analysis')

</div>


    {{--公司--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company--item-operation-record')
    {{--部门--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department--item-operation-record')
    {{--团队--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team--item-operation-record')
    {{--员工--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff--item-operation-record')


    {{--地域--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location--item-operation-record')


    {{--客户--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client--item-operation-record')
    {{--项目--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project--item-team-set')
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project--item-staff-set')
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project--item-operation-record')


    {{--工单--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-import--by-txt')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-aesthetic.order-aesthetic-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-luxury.order-luxury-edit')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-operation-record')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-inspecting')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-appealing')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-appealed-handling')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-delivering')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-distributing')


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


    {{--公司--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.company.company--item-operation-record-datatable')
    {{--部门--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.department.department--item-operation-record-datatable')
    {{--团队--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.team.team--item-operation-record-datatable')
    {{--员工--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.staff.staff--item-operation-record-datatable')


    {{--地域--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.location.location--item-operation-record-datatable')


    {{--客户--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client-edit-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client-list-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.client.client--item-operation-record-datatable')




    {{--项目--}}
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-edit-script')

{{--    @if(in_array($me->staff_category,[0,1,9,71]))--}}
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable')--}}
{{--    @elseif(in_array($me->staff_category,[41]))--}}
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--d-CSD')--}}
{{--    @elseif(in_array($me->staff_category,[51]))--}}
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--d-QID')--}}
{{--    @elseif(in_array($me->staff_category,[61]))--}}
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--d-AD')--}}
{{--    @else--}}
{{--        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--d-CSD')--}}
{{--    @endif--}}

    @if(in_array($me->staff_category,[0,1,9,71]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable')
    @else
        @if($me->staff_position == 31)
            @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--p-director')
        @elseif($me->staff_position == 41)
            @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-datatable--for--p-manager')
        @endif
    @endif

    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.project.project--item-operation-record-datatable')




    {{--工单--}}
{{--    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-edit-script')--}}
    @if(in_array($me->staff_category,[0,1,9]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--admin')
    @elseif(in_array($me->staff_category,[41]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--CSD')
    @elseif(in_array($me->staff_category,[51]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--QID')
    @elseif(in_array($me->staff_category,[61]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--AD')
    @elseif(in_array($me->staff_category,[71]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--OD')
    @else
        @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-dental.order-dental-list-datatable--for--CSD')
    @endif
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order-list-script')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-operation-record-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.module.order.order--item-delivery-record-datatable')


    {{--交付--}}
    @if(in_array($me->staff_category,[0,1,9,71]))
        @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-list-script')
        @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-dental.delivery-dental-list-datatable')
        @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-aesthetic.delivery-aesthetic-list-datatable')
        @include(env('DK_STAFF__TEMPLATE').'component.module.delivery.delivery-luxury.delivery-luxury-list-datatable')
    @endif




    {{--统计--}}
    {{--生产--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.project.statistic-production-project-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.department.statistic-production-department-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.team.statistic-production-team-datatable')

    {{--员工--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-overview-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-rank-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-recent-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-caller-daily-datatable')

    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-inspector-overview-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.production.staff.statistic-deliverer-overview-datatable')

    {{--交付--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.project.statistic-marketing-project-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.client.statistic-marketing-client-datatable')

    {{--销售--}}
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.company.statistic-company-overview-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic.marketing.company.statistic-company-daily-datatable')




    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-daily-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-daily-script')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-show-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-project.statistic-project-detail-datatable')


    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-daily-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-daily-script')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-show-datatable')
    @include(env('DK_STAFF__TEMPLATE').'component.statistic-list.statistic-client.statistic-client-detail-datatable')


    @include(env('DK_STAFF__TEMPLATE').'component.statistic-call.statistic-task-analysis-datatable')


@endsection