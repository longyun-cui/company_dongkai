@extends(env('DK_CLIENT__TEMPLATE').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '客户系统' }}
@endsection




@section('title')<span class="box-title">客户系统</span>@endsection
@section('header')<span class="box-title">客户系统</span>@endsection
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

    {{--员工--}}
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff-list')


    {{--交付--}}
    @if($me->client_er->client_category == 1)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-dental.delivery-dental-list')
    @elseif($me->client_er->client_category == 11)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-aesthetic.delivery-aesthetic-list')
    @elseif($me->client_er->client_category == 31)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-luxury.delivery-luxury-list')
    @endif

    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery-statistic.delivery-daily')


</div>


    {{--员工--}}
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff-edit')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff--item-operation-record')

    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery--item-operating--customer-update')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery--item-operating--callback-update')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery--item-operating--come-update')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery--item-operating--follow-create')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery--item-operating--trade-create')


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


    {{--员工--}}
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff-list-datatable')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff-list-script')
    @include(env('DK_CLIENT__TEMPLATE').'component.module.staff.staff--item-operation-record-datatable')


    {{--交付--}}
    @if($me->client_er->client_category == 1)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-dental.delivery-dental-list-datatable')
    @elseif($me->client_er->client_category == 11)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-aesthetic.delivery-aesthetic-list-datatable')
    @elseif($me->client_er->client_category == 31)
        @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-luxury.delivery-luxury-list-datatable')
    @endif
    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery.delivery-list-script')

    @include(env('DK_CLIENT__TEMPLATE').'component.module.delivery-statistic.delivery-daily-datatable')


@endsection