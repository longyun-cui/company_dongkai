{{--main--}}
<section class="module-container bg-light">
    <div class="container">

        <header class="module-row module-header-container">
            <div class="wow slideInLeft module-title-row color-1 border-light"><b>钢琴出租</b></div>
            <div class="wow slideInRight module-subtitle-row color-6"><b>上海地区最实惠的租琴</b></div>
        </header>

        <div class="module-row module-body-container">
            @foreach($items as $v)
                <div class="col-lg-3 col-md-4 col-sm-6 item-col">
                    <div class="item-container">

                        <figure class="image-container padding-top-3-4">
                            <div class="image-box">
                                <a class="clearfix zoom-" target="_blank"  href="{{ url('/item/'.$v->id) }}">
                                    <img data-action="zoom-" src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Property Image">
                                </a>
                                {{--<span class="btn btn-warning">热销中</span>--}}
                            </div>
                        </figure>

                        <figure class="text-container clearfix">
                            <div class="text-box" style="border-bottom:1px solid #e6e6e6">
                                <div class="text-title-row"><a href="{{ url('/item/'.$v->id) }}"><b>{{ $v->title or '' }}</b></a></div>
                                <div class="text-description-row">
                                    <div>
                                        <i class="fa fa-cny"></i> <span class="font-18px color-red"><b>{{ $v->custom->price or '' }}</b></span>
                                    </div>
                                    <div>
                                        <span>押金：<i class="fa fa-cny"></i> {{ $v->custom->deposit or '' }} </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-box clearfix">
                                <a target="_blank" href="{{ url('/item/'.$v->id) }}">
                                    <button class="btn btn-default btn-price pull-right btn-3d" data-hover="点击查看">
                                        <strong>查看详情</strong>
                                    </button>
                                </a>
                            </div>
                        </figure>

                    </div>
                </div>
            @endforeach
        </div>

        <footer class="module-row module-footer-container text-center">
            {{ $items->links() }}
        </footer>

    </div>
</section>