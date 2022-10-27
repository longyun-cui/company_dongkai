@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title'){{ config('company.info.short_name') }}@endsection
@section('meta_author')@endsection
@section('meta_title')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection



{{--header--}}
@section('component-header')
    @include('frontend.component.header')
@endsection


{{--footer--}}
@section('component-footer')
    @include('frontend.component.footer')
@endsection




{{--custom-content--}}
@section('custom-content')

    {{--banner--}}
    @include('frontend.component.banner-for-root')

    <div class="page-root">

        @include('frontend.module.root-rent-out', ['items'=>$rent_items])

        @include('frontend.module.root-cooperation', ['items'=>$cooperation_items])

        @include('frontend.module.root-second-wholesale', ['items'=>$wholesale_items])

        @include('frontend.module.root-recycle')

        @include('frontend.module.root-advantage')

        @include('frontend.module.root-coverage', ['items'=>$coverage_items])

        @include('frontend.module.root-qrcode')

    </div>

@endsection



{{--footer--}}
@section('custom-style')
<style>
</style>
@endsection