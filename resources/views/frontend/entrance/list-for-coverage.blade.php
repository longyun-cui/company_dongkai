@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title')最新资讯-{{ config('company.info.short_name') }}@endsection
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
    @include('frontend.component.banner-for-rent-out-list')

    {{--main--}}
    @include('frontend.module.list-for-coverage')

@endsection
