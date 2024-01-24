@extends(env('TEMPLATE_YH_STAFF').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or '【l】' }}@endif{{ $head_title or '内容' }} - 员工系统 - 兆益信息
@endsection
@section('meta_title')@endsection
@section('meta_author')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




@section('sidebar')
    {{--@include(env('TEMPLATE_ROOT_FRONT').'component.sidebar.sidebar-root')--}}
@endsection
@section('header','')
@section('description','')
@section('content')
<div class="container">

    <div class="main-body-section main-section main-body-left-section section-wrapper page-item">
        <div class="container-box pull-left margin-bottom-16px">

            @include(env('TEMPLATE_YH_STAFF').'component.item')

        </div>
    </div>

    <div class="main-body-section side-section main-body-right-section section-wrapper hidden-xs">

        <div class="fixed-to-top">
        {{--@include(env('TEMPLATE_ROOT_FRONT').'component.right-side.right-root')--}}
        {{--@include(env('TEMPLATE_ROOT_FRONT').'component.right-side.right-me')--}}
        </div>

    </div>

</div>
@endsection




@section('custom-css')
@endsection
@section('custom-style')
<style>
</style>
@endsection




@section('custom-js')
    {{--@include(env('TEMPLATE_COMMON_FRONT').'component.item-script')--}}
@endsection
@section('custom-script')
<script>
    $(function() {
//        $('article').readmore({
//            speed: 150,
//            moreLink: '<a href="#">展开更多</a>',
//            lessLink: '<a href="#">收起</a>'
//        });
    });
</script>
@endsection