@extends(env('TEMPLATE_YH_STAFF').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or '【l】' }}@endif{{ $head_title or '首页' }} - 员工系统 - {{ config('info.info.short_name') }}
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

    <div class="main-body-section main-section main-body-left-section section-wrapper page-root">
        <div class="container-box pull-left margin-bottom-16px">


            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="{{ $menu_active_of_unfinished or '' }}"><a href="/?task-list-type=unfinished" data-toggle="tab-">待完成</a></li>
                    <li class="{{ $menu_active_of_finished or '' }}"><a href="/?task-list-type=finished" data-toggle="tab-">已完成</a></li>
                    <li class="{{ $menu_active_of_custom or '' }}"><a href="/?task-list-type=all" data-toggle="tab-">{{ $custom_menu_title or '' }}</a></li>
                </ul>
                <div class="tab-content" style="width:100%; padding:10px 0;float:left;">
                    <div class="active tab-pane" id="all">
                        @if(!empty($item_list) && count($item_list))
                            @include(env('TEMPLATE_YH_STAFF').'component.item-list-for-task',['item_list'=>$item_list])
                        @endif
                    </div>
                    {{--<div class="tab-pane" id="timeline">--}}
                    {{--</div>--}}

                    {{--<div class="tab-pane" id="settings">--}}
                    {{--</div>--}}
                </div>
            </div>

            {!! $item_list->appends($condition)->links() !!}

            {{--@include(env('TEMPLATE_ROOT_FRONT').'component.left-tag')--}}

            <div class="container-box pull-left margin-bottom-16px">
                {{--@include(env('TEMPLATE_ROOT_FRONT').'component.item-list',['item_list'=>$item_list])--}}
            </div>

            <div class="container-box pull-left margin-bottom-16px">
                {{--@include(env('TEMPLATE_ROOT_FRONT').'component.user-list',['user_list'=>$user_list])--}}
            </div>

            {{--{!! $item_list->links() !!}--}}

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
    <link rel="stylesheet" href="{{ asset('/custom/zy-staff/css/item.css') }}">
    <link rel="stylesheet" href="{{ asset('/custom/zy-staff/css/frontend.css') }}">
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