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

                <div class="tab-pane active" id="tab-pane-width" style="width:100%;">
                    &nbsp;
                </div>

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


@endsection