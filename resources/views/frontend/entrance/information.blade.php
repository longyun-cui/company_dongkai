@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title'){{$information->title or ''}}-{{ config('company.info.short_name') }}@endsection
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

    @include('frontend.module.information-detail', ['data'=>$information])

@endsection




{{--script--}}
@section('custom-script')

    <script>
        $(function() {
        });
    </script>

@endsection
