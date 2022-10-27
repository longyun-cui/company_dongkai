{{--图片展示--}}
<section class="section-container bg-white">
    <div class="row">

        <header class="module-row module-header-container with-border-bottom text-center">
            <div class="wow slideInLeft module-title-row color-1 border-light title-h2"><b>图片展示</b></div>
            <a class="pull-right print-btn _none" href="javascript:window.print()">Print This Property <i class="fa fa-print"></i></a>
        </header>

        <div class="module-row module-body-container property-contents lightcase-images-show" id="lightcase-images-show">
            @if(count($data->custom2) > 0)
                @foreach($data->custom2 as $img)
                    <article class="property clearfix">
                        <figure class="feature-image" style="height:240px; max-height:240px; overflow:hidden;">
                            <a class="clearfix zoom lightcase-image" data-rel="lightcase" href="{{url(config('common.host.'.env('APP_ENV').'.cdn').'/'.$img->img)}}">
                                <img data-action="zoom-" src="{{url(config('common.host.'.env('APP_ENV').'.cdn').'/'.$img->img)}}" alt="" />
                            </a>
                        </figure>
                    </article>
                @endforeach
            @endif
        </div>

    </div>
</section>