{{--main--}}
<section class="section-container bg-white">
    <div class="row">

        <header class="module-row module-header-container with-border-bottom text-center">
            <div class="wow slideInLeft module-title-row color-1 border-light title-h2"><b>热门租琴</b></div>
        </header>

        <div class="module-row module-body-container">
            @foreach($items as $v)
                <div class="col-lg-4 col-md-6 col-sm-6 item-col">
                    <a class="zoom- clearfix" target="_blank" href="{{ url('/item/'.$v->id) }}">
                        <div class="item-container model-left-right padding-8px bg-light">

                            <figure class="image-container padding-top-1-4-">
                                <div class="image-box">
                                    <img data-action="zoom-" src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Property Image">
                                    {{--<span class="btn btn-warning">热销中</span>--}}
                                </div>
                            </figure>

                            <figure class="text-container">
                                <div class="text-box">
                                    <div class="text-title-row"><b>{{ $v->title or '' }}</b></div>
                                    <div class="text-description-row">
                                        <div>
                                            <i class="fa fa-cny"></i> <span class="font-18px color-red"><b>{{ $v->custom->price or '' }}</b></span>
                                        </div>
                                        <div>
                                            <span>押金：<i class="fa fa-cny"></i> {{ $v->custom->deposit or '' }} </span>
                                        </div>
                                    </div>
                                </div>
                            </figure>

                        </div>
                    </a>
                </div>

            @endforeach
        </div>

        <footer class="module-row module-footer-container text-center">
            <a href="{{ url('/rent-out/list') }}" class="view-more">查看更多 <i class="fa fa-hand-o-right"></i></a>
        </footer>

    </div>
</section>