{{--最新资讯--}}
<section class="module-container bg-dark" id="home-property-for-rent-listing">
    <div class="container main-container">

        <header class="module-row module-header-container text-center">
            <div class="wow slideInLeft module-title-row title-with-double-line color-e border-light title-h2"><b>钢琴Tips</b></div>
            <div class="wow slideInRight module-subtitle-row color-b title-h4"><b>随时了解最新资讯，掌握钢琴小贴士</b></div>
        </header>

        <div class="module-row module-body-container root-slider">
            @foreach($items as $v)
                <div class="col-lg-4 col-md-6">
                    <article class="property clearfix">

                        <figure class="feature-image">
                            <a class="clearfix" href="{{ url('/item/'.$v->id) }}">
                                <img src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Property Image">
                            </a>
                        </figure>

                        <div class="property-contents">
                            <header class="property-header clearfix">
                                <div class="pull-left">
                                    <h6 class="entry-title"><a href="{{ url('/item') }}">{{ $v->title or '' }}</a></h6>
                                    <span class="property-location _none"><i class="fa fa-map-marker"></i> 14 Tottenham Road, London</span>
                                </div>
                            </header>
                            <div class="property-meta clearfix">
                                <a href="{{ url('/item/'.$v->id) }}" class="btn btn-default btn-price pull-right btn-3d" data-hover="查看详情"><strong>查看详情</strong></a>
                                {{--<span><i class="fa fa-arrows-alt"></i> 3060 SqFt</span>--}}
                                {{--<span><i class="fa fa-bed"></i> 3 Beds</span>--}}
                                {{--<span><i class="fa fa-bathtub"></i> 3 Baths</span>--}}
                                {{--<span><i class="fa fa-cab"></i> Yes</span>--}}
                            </div>
                        </div>

                    </article>
                </div>
            @endforeach
        </div>

        <footer class="module-row module-footer-container text-center">
            <a href="{{ url('/coverage/list') }}" class="view-more">查看更多 <i class="fa fa-hand-o-right"></i></a>
        </footer>

    </div>
</section>