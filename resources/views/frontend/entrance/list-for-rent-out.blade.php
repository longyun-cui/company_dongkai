@extends('frontend.layout.layout')


{{--html.head--}}
@section('head_title')钢琴出租-{{ config('company.info.short_name') }}@endsection
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
    @include('frontend.module.list-for-rent-out')


    <section id="home-property-listing" class="_none">



        <header class="section-header home-section-header text-center">
            <div class="container">
                <h2 class="wow slideInRight">钢琴出租</h2>
                <p class="wow slideInLeft font-24px"><b>专业从业者帮你找好</b></p>
            </div>
        </header>

        <style>
            #filters { padding: 0; margin:0 4px 32px; list-style: none; }
            #filters li { margin-bottom: 6px; float: left; width: 120px; }
            #filters li span {
                 display: block;
                 padding: 5px 20px;
                 text-align: center;
                 text-decoration: none;
                 color: #666;
                 cursor: pointer;
             }
            #filters li span.active { color: #fff; background: #e95a44; }
        </style>
        <div class="container _none" id="portfolio-list-ctrl">
            <div class="row">
                <ul id="filters" class="clearfix">
                    <li><span class="filter active" data-filter="all">不限区域</span></li>
                    @foreach(config('company.district') as $k => $v)
                        <li><span class="filter" data-filter="{{ $k or '' }}">{{ $v or '' }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="container" id="portfolio-list">
            <div class="row">
                @foreach($items as $v)
                    <div class="col-lg-4 col-sm-6 layout-item-wrap portfolio all {{ $v->custom->district or '' }}">
                        <article class="property layout-item clearfix">
                            <figure class="feature-image" style="height:240px; max-height:240px; overflow:hidden;">
                                <a class="clearfix zoom-" target="_blank"  href="{{ url('/house/'.$v->id) }}">
                                    <img data-action="zoom-" src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Property Image">
                                </a>
                                <span class="btn btn-warning btn-sale">热销中</span>
                            </figure>
                            <div class="property-contents clearfix">
                                <header class="property-header clearfix">
                                    <div class="pull-left">
                                        <h6 class="entry-title"><a href="{{ url('/house/'.$v->id) }}">{{ $v->title or '' }}</a></h6>
                                        <span><i class="fa fa-cny"></i> {{ $v->custom->average or '' }} </span>
                                        <span class="property-location"><i class="fa fa-map-marker"></i> {{ $v->custom->position or '' }} </span>
                                    </div>
                                    <a class="_none" target="_blank" href="{{ url('/house/'.$v->id) }}">
                                        <button class="btn btn-default btn-price pull-right btn-3d" data-hover="点击查看">
                                            <strong>查看详情</strong>
                                        </button>
                                    </a>
                                </header>
                                <div class="property-meta clearfix">
                                    <span class="_none"><i class="fa fa-arrows-alt"></i> 3060 SqFt</span>
                                    <span class="_none"><i class="fa fa-bed"></i> {{ $v->custom->average or '' }} </span>
                                    <span class="_none"><i class="fa fa-bathtub"></i> {{ $v->custom->total or '' }} </span>
                                    <span class="_none"><i class="fa fa-cab"></i> Yes</span>

                                    <a target="_blank" href="{{ url('/house/'.$v->id) }}">
                                        <button class="btn btn-default btn-price pull-right btn-3d" data-hover="点击查看">
                                            <strong>查看详情</strong>
                                        </button>
                                    </a>
                                </div>
                                <div class="contents clearfix">
                                    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. </p>
                                </div>
                                <div class="author-box clearfix">
                                    <a href="index.html#" class="author-img"><img src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Agent Image"></a>
                                    <cite class="author-name">Personal Seller: <a href="/#">Linda Garret</a></cite>
                                    <span class="phone"><i class="fa fa-phone"></i> 00894 692-49-22</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


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
