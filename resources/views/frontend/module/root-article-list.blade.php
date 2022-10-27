{{--最新资讯--}}
<section class="module-container bg-light">
    <div class="container main-container">

        <header class="module-row module-header-container text-center">
            <div class="wow slideInLeft module-title-row title-with-double-line color-1 border-dark title-h2"><b>云飞资讯</b></div>
            <div class="wow slideInRight module-subtitle-row color-5 title-h4"><b>随时了解最新资讯，掌握钢琴小贴士</b></div>
        </header>

        <div class="module-row module-body-container section-block-container bg-white">
            <div class="col-lg-4 col-md-6 col-md-6">
                <section class="section-container article-image-container">
                    <img data-action="zoom-" src="http://cdn.local-yunfei.com/research/common/2018-07-06/5b3f7d157ecfc1530887445.jpg" alt="Property Image">
                </section>
            </div>
            <div class="col-lg-4 col-md-6 col-md-6">
                @include('frontend.module.section-article-list', ['items'=>$items])
            </div>
            <div class="col-lg-4 col-md-6 col-md-6">
                @include('frontend.module.section-article1-list', ['items'=>[]])
            </div>
        </div>

    </div>
</section>