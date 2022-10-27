{{--图片展示--}}
<section class="section-container bg-white">
    <div class="row">

        <header class="module-row module-header-container with-border-bottom text-center">
            <div class="wow slideInLeft module-title-row color-1 border-light title-h2"><b>最新动态</b></div>
            <a class="pull-right print-btn _none" href="javascript:window.print()">Print This Property <i class="fa fa-print"></i></a>
        </header>

        <div class="module-row module-body-container property-contents" id="">
            {{{ $data->custom->recent_news or '' }}}
        </div>

    </div>
</section>