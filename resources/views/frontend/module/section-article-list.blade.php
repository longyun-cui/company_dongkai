{{--图片展示--}}
<section class="section-container article-list-container bg-white">
    <div class="row">

        <header class="module-row module-header-container with-border-bottom text-center">
            <div class="wow slideInLeft module-title-row color-1 border-light title-h3"><b>云资讯</b></div>
            <a class="pull-right print-btn" href="/">更多 <i class="fa fa-angle-double-right"></i></a>
        </header>

        <div class="module-row module-body-container property-contents" id="">
            <ul>
                @foreach($items as $v)
                    <a href="{{ url('/item/'.$v->id) }}" class="title-h5">
                        <li class="wobble-horizontal">
                            <span class="title-inn pull-left row-ellipsis"><i class="fa fa-file-o"></i> {{ $v->title or '' }}</span>
                            <span class="time-inn pull-right text-right">11-27</span>
                        </li>
                    </a>
                @endforeach
            </ul>
        </div>

    </div>
</section>