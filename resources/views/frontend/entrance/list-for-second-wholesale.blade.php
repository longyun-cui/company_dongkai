@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title')二手钢琴批发-{{ config('company.info.short_name') }}@endsection
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
    @include('frontend.module.list-for-second-wholesale')


@endsection


@section('custom-css')
    {{--<link href="{{ asset('/templates/jiaoben1627/css/layout.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('/templates/jiaoben1627/css/normalize.css') }}" rel="stylesheet">
@endsection


@section('custom-js')
    <script type="text/javascript" src="{{ asset('/templates/jiaoben1627/js/jquery.easing.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/templates/jiaoben1627/js/jquery.mixitup.min.js') }}"></script>
@endsection


@section('custom-script')
    <script type="text/javascript">
        $(function () {

            var filterList = {

                init: function () {

                    // MixItUp plugin
                    $('#portfolio-list').mixitup({
                        targetSelector: '.portfolio',
                        filterSelector: '.filter',
                        effects: ['fade'],
                        easing: 'snap',
                        // call the hover effect
                        onMixEnd: filterList.hoverEffect()
                    });

                },

                hoverEffect: function () {

                    // Simple parallax effect
                    $('#portfolio-list .portfolio').hover(
                        function () {
                            $(this).find('.label').stop().animate({bottom: 0}, 200, 'easeOutQuad');
                            $(this).find('img').stop().animate({top: -30}, 500, 'easeOutQuad');
                        },
                        function () {
                            $(this).find('.label').stop().animate({bottom: -40}, 200, 'easeInQuad');
                            $(this).find('img').stop().animate({top: 0}, 300, 'easeOutQuad');
                        }
                    );

                }

            };

            // Run the show!
            filterList.init();


        });
    </script>
@endsection
