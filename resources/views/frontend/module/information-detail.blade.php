{{--information-detail--}}
<div id="property-single">

    <style>
        .slick-slide img { width: 100%; height: 480px; object-fit:cover; }
    </style>
    <div id="main-slider">
        <div class="slide"><img src="{{ url('/common/images/banner01.jpeg') }}" alt="Slide"></div>
        <div class="slide"><img src="{{ url('/common/images/banner02.jpeg') }}" alt="Slide"></div>
        <div class="slide"><img src="{{ url('/common/images/banner03.jpeg') }}" alt="Slide"></div>
        {{--<div class="slide"><img src="{{ url('/templates/moban2030/assets/images/slider/1.jpg') }}" alt="Slide"></div>--}}
        {{--<div class="slide"><img src="{{ url('/templates/moban2030/assets/images/slider/2.jpg') }}" alt="Slide"></div>--}}
        {{--<div class="slide"><img src="{{ url('/templates/moban2030/assets/images/slider/3.jpg') }}" alt="Slide"></div>--}}
        {{--<div class="slide"><img src="{{ url('/templates/moban2030/assets/images/slider/4.jpg') }}" alt="Slide"></div>--}}
    </div>

    <div class="container">
        <div class="row">

            {{--left--}}
            <div class="col-lg-8 col-md-7 _none">

                <section class="property-meta-wrapper common">
                    <h3 class="entry-title">{{ $data->title or '' }}</h3>

                    <div class="property-single-meta">
                        <div class="row">
                            <div class="col-sm-5 col-xs-12 pull-right" style="margin: 8px 0">
                                <img src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$data->cover_pic }}" alt="Agent Image">
                            </div>
                            <div class="col-sm-7 col-xs-12 pull-left" style="margin: 8px 0">
                                <ul class="clearfix">
                                    <li><span>参考单价 :</span> {{ $data->custom->average or '' }} </li>
                                    <li><span>参考总价 :</span> {{ $data->custom->total or '' }} </li>
                                    <li><span>楼盘户型 :</span> {{ $data->custom->type or '' }} </li>
                                    <li><span>楼盘地址 :</span> {{ $data->custom->position or '' }} </li>
                                    <li><span>开盘时间 :</span> {{ $data->custom->start_time or '' }} </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <style>
                        .free-car { width:100%; border: 1px solid #eee; padding: 16px; margin-top: 16px; border-radius: 4px; z-index: 1000; }
                        .free-car .car-top { width:auto; padding: 8px 0; border-bottom: 1px solid #f4f4f4; }
                        .free-car .car-bottom { width:auto; padding: 8px 0; margin: 16px 0 8px; }
                        .free-car .num { color: #ff6d6f; }
                        .free-car .ticket { padding: 12px 24px; border-radius: 2px; font-size:20px; color: #ff6d6f; border: 2px solid #ff6d6f; }
                        .free-car .ticket:hover { color: #22f3ae; border: 2px solid #22f3ae; }
                    </style>

                    <!--免费专车-->
                    <div class="row">
                        <div class="free-car">
                            <div class="car-top">
                                <p class="tit"><b>免费专车</b></p>
                                <p class="describe">全城免费专车接送看房，人均节省<span class="num">827元</span>路费</p>
                            </div>
                            <div class="car-bottom">
                                <a class="ticket grab-ticket" href="javascript:void(0);" data-toggle="modal" data-target="#grab-modal">
                                    <i class="fa fa-car"></i> <b>抢专车券</b>
                                </a> &nbsp; &nbsp;
                                <span class="num">{{ $ticket_total or 257 }}人</span>已抢
                            </div>
                        </div>
                    </div>

                </section>


                {{--楼盘详情--}}
                <section class="property-contents common _none">
                    <div class="entry-title clearfix">
                        <h4 class="pull-left"> 楼盘详情 </h4>
                        <a class="pull-right print-btn _none" href="javascript:window.print()">Print This Property <i class="fa fa-print"></i></a>
                    </div>
                    <div>
                        {{--{!! $data->content or '' !!}--}}
                    </div>
                </section>


                {{----}}
                <section class="property-single-features common clearfix _none">
                    <h4 class="entry-title">Property Features</h4>
                    <ul class="property-single-features-list clearfix">
                        <li>Air Conditioning</li>
                        <li>Cable TV</li>
                        <li>Cot</li>
                        <li class="disabled">Fan</li>
                        <li>Lift</li>
                        <li>Parking</li>
                        <li>Separate Shower</li>
                        <li>Office/den</li>
                        <li class="disabled">Air Conditioning</li>
                        <li>Cable TV</li>
                        <li>Cot</li>
                        <li>Fan</li>
                        <li>Lift</li>
                        <li class="disabled">Parking</li>
                        <li>Cot</li>
                        <li>Fan</li>
                        <li>Lift</li>
                        <li>Parking</li>
                        <li>Separate Shower</li>
                        <li class="disabled">Office/den</li>
                        <li>Air Conditioning</li>
                        <li>Cable TV</li>
                    </ul>
                </section>

                <section class="property-video common _none">
                    <h4 class="entry-title">Video Tour</h4>
                    {{--<iframe src="" allowfullscreen></iframe>--}}
                </section>

                <section class="property-nearby-places common _none">
                    <h4 class="entry-title">Near By Places</h4>
                    <div id="nearby-places-map"></div>
                </section>

                {{--经纪人--}}
                <section class="property-agent common _none">
                    <h4 class="entry-title">联系经纪人</h4>
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="agent-box clearfix">
                                <div class="row">
                                    <div class="col-sm-5 col-xs-5">
                                        <a href="javascript:void(0);" class="agent-image">
                                            <img src="{{ url('/templates/moban2030/assets/images/agents/2.jpg') }}" alt="Agent Image">
                                        </a>
                                    </div>
                                    <div class="col-sm-7 col-xs-7">
                                        <cite class="agent-name">Michelle Ramirez</cite>
                                        <small class="designation">Company Agent at Realtory Inc. </small>
                                        <ul class="agent-social-handlers clearfix">
                                            <li><a href="javascript:void(0);"><i class="fa fa-facebook-square"></i> Facebook</a></li>
                                            <li><a href="javascript:void(0);"><i class="fa fa-twitter-square"></i> Twitter</a></li>
                                            <li><a href="javascript:void(0);"><i class="fa fa-pinterest-square"></i> Pinterest</a></li>
                                            <li><a href="javascript:void(0);"><i class="fa fa-google-plus-square"></i> Google Plus</a></li>
                                        </ul>
                                        <a href="{{ url('/') }}" class="btn btn-warning">View Profile</a>
                                    </div>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. luctus ligula ac faucibus faucibus. Cras in nisi in turpis eleifend vehicula at at massa. Vivamus aliquet porttitor odio.</p>
                            </div>
                            <div class="widget address-widget clearfix">
                                <ul>
                                    <li><i class="fa fa-map-marker"></i> 4 Tottenham Road, London, England.</li>
                                    <li><i class="fa fa-phone"></i> (123) 45678910</li>
                                    <li><i class="fa fa-envelope"></i> huycoi.art@gmail.com</li>
                                    <li><i class="fa fa-fax"></i> +84 962 216 601</li>
                                    <li><i class="fa fa-clock-o"></i> Mon - Sat: 9:00 - 18:00</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="agent-contact-form">
                                <form id="agent-form" class="agent-form" method="post">
                                    <input type="text" name="name" placeholder="您的姓名" class="required" >
                                    <input type="text" name="phone" placeholder="电话" class="required">
                                    <input type="text" name="email" placeholder="邮箱" class="email required">
                                    <textarea name="message" cols="30" rows="5" class="required" placeholder="留言"></textarea>
                                    <button class="btn btn-default btn-lg btn-3d" type="button" data-hover="提交">提交</button>
                                    <div class="error-container"></div>
                                    <div class="message-container"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

            </div>


            {{--right--}}
            <div class="col-lg-4 col-md-5 _none">
                <div id="property-sidebar">


                    {{----}}
                    <section class="widget adv-search-widget clearfix _none">
                        <h5 class="title hide">Search your Place</h5>
                        <div id="advance-search-widget" class="clearfix">
                            <form action="single-property.html#" id="adv-search-form">
                                <div id="widget-tabs">
                                    <ul class="tab-list clearfix">
                                        <li><a class="btn" href="single-property.html#tab-1">Sale</a></li>
                                        <li><a class="btn" href="single-property.html#tab-2">Rent</a></li>
                                        <li><a class="btn" href="single-property.html#tab-3">office for rent</a></li>
                                    </ul>
                                    <div id="tab-1" class="tab-content current">
                                        <fieldset class="clearfix">
                                            <div>
                                                <label for="main-location">All Location</label>
                                                <select name="location" id="main-location">
                                                    <option value="">All Cities</option>
                                                    <option value="chicago"> Chicago</option>
                                                    <option value="los-angeles"> Los Angeles</option>
                                                    <option value="miami"> Miami</option>
                                                    <option value="new-york"> New York</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-sub-location">Sub Location</label>
                                                <select name="sub-location" id="property-sub-location">
                                                    <option value="">All Areas</option>
                                                    <option value="brickell" > Brickell</option>
                                                    <option value="brickyard" > Brickyard</option>
                                                    <option value="bronx" > Bronx</option>
                                                    <option value="brooklyn" > Brooklyn</option>
                                                    <option value="coconut-grove" > Coconut Grove</option>
                                                    <option value="downtown" > Downtown</option>
                                                    <option value="eagle-rock" > Eagle Rock</option>
                                                    <option value="englewood" > Englewood</option>
                                                    <option value="hermosa" > Hermosa</option>
                                                    <option value="hollywood" > Hollywood </option>
                                                    <option value="lincoln-park" > Lincoln Park</option>
                                                    <option value="manhattan" > Manhattan</option>
                                                    <option value="midtown" > Midtown</option>
                                                    <option value="queens" > Queens</option>
                                                    <option value="westwood" > Westwood </option>
                                                    <option value="wynwood" > Wynwood</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-status">All Status</label>
                                                <select id="property-status" name="status">
                                                    <option value="">All Status</option>
                                                    <option value="for-rent"> For Rent</option>
                                                    <option value="for-sale"> For Sale</option>
                                                    <option value="foreclosures"> Foreclosures</option>
                                                    <option value="new-costruction"> New Costruction</option>
                                                    <option value="new-listing"> New Listing</option>
                                                    <option value="open-house"> Open House</option>
                                                    <option value="reduced-price"> Reduced Price</option>
                                                    <option value="resale"> Resale</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-type">All Types</label>
                                                <select id="property-type" name="type" >
                                                    <option value="">All Types</option>
                                                    <option value="apartment"> Apartment</option>
                                                    <option value="condo"> Condo</option>
                                                    <option value="farm"> Farm</option>
                                                    <option value="loft"> Loft</option>
                                                    <option value="lot"> Lot</option>
                                                    <option value="multi-family-home"> Multi Family Home</option>
                                                    <option value="single-family-home"> Single Family Home</option>
                                                    <option value="townhouse"> Townhouse</option>
                                                    <option value="villa"> Villa</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-beds">No.Beds</label>
                                                <select name="bedrooms" id="property-beds">
                                                    <option value="">Beds</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-baths">No.Baths</label>
                                                <select name="bathrooms" id="property-baths">
                                                    <option value="">Bathrooms</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-min-area">Min Area(sqft)</label>
                                                <input type="text" name="min-area" id="property-min-area">
                                            </div>
                                            <div>
                                                <label for="property-max-area">Max Area(sqft)</label>
                                                <input type="text" name="max-area" id="property-max-area">
                                            </div>
                                            <div>
                                                <label for="property-min-price">Min Prices</label>
                                                <select name="min-price" id="property-min-price">
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="1000">$1000</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-max-price">Max Prices</label>
                                                <select name="max-price" id="property-max-price" >
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                    <option value="10000000">$10000000</option>
                                                </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div id="tab-2" class="tab-content">
                                        <fieldset class="clearfix">
                                            <div>
                                                <label for="main-location">All Location</label>
                                                <select name="location" id="main-location">
                                                    <option value="">All Cities</option>
                                                    <option value="chicago"> Chicago</option>
                                                    <option value="los-angeles"> Los Angeles</option>
                                                    <option value="miami"> Miami</option>
                                                    <option value="new-york"> New York</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-sub-location">Sub Location</label>
                                                <select name="sub-location" id="property-sub-location">
                                                    <option value="">All Areas</option>
                                                    <option value="brickell" > Brickell</option>
                                                    <option value="brickyard" > Brickyard</option>
                                                    <option value="bronx" > Bronx</option>
                                                    <option value="brooklyn" > Brooklyn</option>
                                                    <option value="coconut-grove" > Coconut Grove</option>
                                                    <option value="downtown" > Downtown</option>
                                                    <option value="eagle-rock" > Eagle Rock</option>
                                                    <option value="englewood" > Englewood</option>
                                                    <option value="hermosa" > Hermosa</option>
                                                    <option value="hollywood" > Hollywood </option>
                                                    <option value="lincoln-park" > Lincoln Park</option>
                                                    <option value="manhattan" > Manhattan</option>
                                                    <option value="midtown" > Midtown</option>
                                                    <option value="queens" > Queens</option>
                                                    <option value="westwood" > Westwood </option>
                                                    <option value="wynwood" > Wynwood</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-status">All Status</label>
                                                <select id="property-status" name="status">
                                                    <option value="">All Status</option>
                                                    <option value="for-rent"> For Rent</option>
                                                    <option value="for-sale"> For Sale</option>
                                                    <option value="foreclosures"> Foreclosures</option>
                                                    <option value="new-costruction"> New Costruction</option>
                                                    <option value="new-listing"> New Listing</option>
                                                    <option value="open-house"> Open House</option>
                                                    <option value="reduced-price"> Reduced Price</option>
                                                    <option value="resale"> Resale</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-type">All Types</label>
                                                <select id="property-type" name="type" >
                                                    <option value="">All Types</option>
                                                    <option value="apartment"> Apartment</option>
                                                    <option value="condo"> Condo</option>
                                                    <option value="farm"> Farm</option>
                                                    <option value="loft"> Loft</option>
                                                    <option value="lot"> Lot</option>
                                                    <option value="multi-family-home"> Multi Family Home</option>
                                                    <option value="single-family-home"> Single Family Home</option>
                                                    <option value="townhouse"> Townhouse</option>
                                                    <option value="villa"> Villa</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-beds">No.Beds</label>
                                                <select name="bedrooms" id="property-beds">
                                                    <option value="">Beds</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-baths">No.Baths</label>
                                                <select name="bathrooms" id="property-baths">
                                                    <option value="">Bathrooms</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-min-area">Min Area(sqft)</label>
                                                <input type="text" name="min-area" id="property-min-area">
                                            </div>
                                            <div>
                                                <label for="property-max-area">Max Area(sqft)</label>
                                                <input type="text" name="max-area" id="property-max-area">
                                            </div>
                                            <div>
                                                <label for="property-min-price">Min Prices</label>
                                                <select name="min-price" id="property-min-price">
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="1000">$1000</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-max-price">Max Prices</label>
                                                <select name="max-price" id="property-max-price" >
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                    <option value="10000000">$10000000</option>
                                                </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div id="tab-3" class="tab-content">
                                        <fieldset class="clearfix">
                                            <div>
                                                <label for="main-location">All Location</label>
                                                <select name="location" id="main-location">
                                                    <option value="">All Cities</option>
                                                    <option value="chicago"> Chicago</option>
                                                    <option value="los-angeles"> Los Angeles</option>
                                                    <option value="miami"> Miami</option>
                                                    <option value="new-york"> New York</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-sub-location">Sub Location</label>
                                                <select name="sub-location" id="property-sub-location">
                                                    <option value="">All Areas</option>
                                                    <option value="brickell" > Brickell</option>
                                                    <option value="brickyard" > Brickyard</option>
                                                    <option value="bronx" > Bronx</option>
                                                    <option value="brooklyn" > Brooklyn</option>
                                                    <option value="coconut-grove" > Coconut Grove</option>
                                                    <option value="downtown" > Downtown</option>
                                                    <option value="eagle-rock" > Eagle Rock</option>
                                                    <option value="englewood" > Englewood</option>
                                                    <option value="hermosa" > Hermosa</option>
                                                    <option value="hollywood" > Hollywood </option>
                                                    <option value="lincoln-park" > Lincoln Park</option>
                                                    <option value="manhattan" > Manhattan</option>
                                                    <option value="midtown" > Midtown</option>
                                                    <option value="queens" > Queens</option>
                                                    <option value="westwood" > Westwood </option>
                                                    <option value="wynwood" > Wynwood</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-status">All Status</label>
                                                <select id="property-status" name="status">
                                                    <option value="">All Status</option>
                                                    <option value="for-rent"> For Rent</option>
                                                    <option value="for-sale"> For Sale</option>
                                                    <option value="foreclosures"> Foreclosures</option>
                                                    <option value="new-costruction"> New Costruction</option>
                                                    <option value="new-listing"> New Listing</option>
                                                    <option value="open-house"> Open House</option>
                                                    <option value="reduced-price"> Reduced Price</option>
                                                    <option value="resale"> Resale</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-type">All Types</label>
                                                <select id="property-type" name="type" >
                                                    <option value="">All Types</option>
                                                    <option value="apartment"> Apartment</option>
                                                    <option value="condo"> Condo</option>
                                                    <option value="farm"> Farm</option>
                                                    <option value="loft"> Loft</option>
                                                    <option value="lot"> Lot</option>
                                                    <option value="multi-family-home"> Multi Family Home</option>
                                                    <option value="single-family-home"> Single Family Home</option>
                                                    <option value="townhouse"> Townhouse</option>
                                                    <option value="villa"> Villa</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-beds">No.Beds</label>
                                                <select name="bedrooms" id="property-beds">
                                                    <option value="">Beds</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-baths">No.Baths</label>
                                                <select name="bathrooms" id="property-baths">
                                                    <option value="">Bathrooms</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                    <option value="any">Any</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-min-area">Min Area(sqft)</label>
                                                <input type="text" name="min-area" id="property-min-area">
                                            </div>
                                            <div>
                                                <label for="property-max-area">Max Area(sqft)</label>
                                                <input type="text" name="max-area" id="property-max-area">
                                            </div>
                                            <div>
                                                <label for="property-min-price">Min Prices</label>
                                                <select name="min-price" id="property-min-price">
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="1000">$1000</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="property-max-price">Max Prices</label>
                                                <select name="max-price" id="property-max-price" >
                                                    <option value="any" selected="selected">Any</option>
                                                    <option value="5000">$5000</option>
                                                    <option value="10000">$10000</option>
                                                    <option value="50000">$50000</option>
                                                    <option value="100000">$100000</option>
                                                    <option value="200000">$200000</option>
                                                    <option value="300000">$300000</option>
                                                    <option value="400000">$400000</option>
                                                    <option value="500000">$500000</option>
                                                    <option value="600000">$600000</option>
                                                    <option value="700000">$700000</option>
                                                    <option value="800000">$800000</option>
                                                    <option value="900000">$900000</option>
                                                    <option value="1000000">$1000000</option>
                                                    <option value="1500000">$1500000</option>
                                                    <option value="2000000">$2000000</option>
                                                    <option value="2500000">$2500000</option>
                                                    <option value="5000000">$5000000</option>
                                                    <option value="10000000">$10000000</option>
                                                </select>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <button type="submit" class="btn btn-default btn-lg text-center btn-3d" data-hover="Search Property">Search Property</button>
                                </div>
                            </form>
                        </div>
                    </section>


                    {{----}}
                    <section class="widget recent-properties clearfix">
                        <h4 class="title" style="margin-bottom:32px;padding-bottom:16px;font-size:24px;line-height:24px; border-bottom:1px solid #eee;"><b>其他资讯</b></h4>
                        @foreach($informations as $v)
                        <div class="property clearfix">
                            <a href="{{ url('/information/'.$v->id) }}" class="feature-image zoom-">
                                <img data-action="zoom-" src="{{ config('common.host.'.env('APP_ENV').'.cdn').'/'.$v->cover_pic }}" alt="Property Image">
                            </a>
                            <div class="property-contents">
                                <h6 class="entry-title"><a href="{{ url('/information/'.$v->id) }}">{{ $v->title or '' }}</a></h6>
                                <span class="btn-price">单价：{{ $v->custom->average or '' }}</span>
                                <span class="btn-price">总价：{{ $v->custom->total or '' }}</span>
                                <div class="property-meta clearfix _none">
                                    <span><i class="fa fa-arrows-alt"></i> 3060 SqFt</span>
                                    <span><i class="fa fa-bed"></i> 3 Beds</span>
                                    <span><i class="fa fa-bathtub"></i> 3 Baths</span>
                                    <span><i class="fa fa-cab"></i> Yes</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </section>


                    {{----}}
                    <section class="widget property-taxonomies clearfix _none">
                        <h5 class="title">Recent Status</h5>
                        <ul class="clearfix">
                            <li><a href="single-property.html#">For Rent </a><span class="pull-right">29</span></li>
                            <li><a href="single-property.html#">For Sale </a><span class="pull-right">35</span></li>
                            <li><a href="single-property.html#">Office For Rent </a><span class="pull-right">07</span></li>
                        </ul>
                    </section>


                    {{----}}
                    <section class="widget property-taxonomies clearfix _none">
                        <h5 class="title">Recent Type</h5>
                        <ul class="clearfix">
                            <li><a href="single-property.html#">Apartment </a><span class="pull-right">30</span></li>
                            <li><a href="single-property.html#">Loft </a><span class="pull-right">05</span></li>
                            <li><a href="single-property.html#">Single Family Home </a><span class="pull-right">28</span></li>
                            <li><a href="single-property.html#">Vila </a><span class="pull-right">37</span></li>
                        </ul>
                    </section>


                </div>
            </div>


            {{--left--}}
            <div class="col-lg-12 col-md-12">
                {{--楼盘详情--}}
                <section class="property-contents common">
                    <div class="entry-title clearfix">
                        <h2 class="text-center"> {{ $data->title or '' }} </h2>
                        <a class="pull-right print-btn _none" href="javascript:window.print()">Print This Property <i class="fa fa-print"></i></a>
                    </div>
                    <div>
                        {!! $data->content or '' !!}
                    </div>
                </section>
            </div>


        </div>
    </div>

</div>

<style>

    .modal-open .modal { display: block; }

    .dialog{position:relative;width:500px;border-radius:15px;background-color:#fff;overflow:hidden; }
    .dialog .dialog-close{position:absolute;top:10px;right:12px}
    .dialog .dialog-close:hover{text-decoration:none}
    .dialog .dialog-close .icon{font-size:22px;color:#a1a1a1}
    .dialog .ipt-area{overflow:hidden}
    .dialog .ipt-area .ipt{width:370px;height:32px;padding:0 14px;border:solid 1px #eaeaea}
    .dialog .ipt-area .error-msg{clear:both;text-align:center;color:#ff6d6f;width:100%;height:30px;line-height:30px;overflow:hidden}
    .dialog .ipt-area .error-msg span{display:none}
    .dialog .btn3{width:250px;padding:0;border-radius:4px;background-color:#48b3e2}
    .dialog .btn3:hover{text-decoration:none;background-color:#64c2eb}

    .dialog .ipt-area input::-webkit-input-placeholder{color:#b7b7b7}

    .dialog .chart-area{width:100%;height:196px;background-color:#48b3e2}
    .dialog .chart-area #chart{width:100%;height:100%}
    .dialog .content{text-align:center;padding:18px 50px 28px 50px;background-color:#fff}
    .dialog .content .con-info{font-size:14px;color:#666;text-align:left;line-height:24px;margin-bottom:10px}
    .dialog .dialog-close{z-index:1000}

    .dialog .con-txt{text-align:left;font-size:14px;color:#666;margin-bottom:24px}
    .dialog .ico-edu{width:60px;height:60px;background:url(../images/ico-edu.png) no-repeat;background-size:100% 100%}
    .dialog .ico-train{width:60px;height:60px;background:url(../images/ico-train.png) no-repeat;background-size:100% 100%}
    .dialog .ico-server{width:60px;height:60px;background:url(../images/ico-zan-s.png) no-repeat;background-size:100% 100%}

    .dialog .dialog-cons-wrap .btn-area{text-align:center}
    .dialog .ipt-area .ipt-text{color:#666;margin-top:20px}

    .btn, .btn2, .btn3, .btn4 {
        background: #ff6d6f none repeat scroll 0 0;
        border: medium none;
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        overflow: hidden;
    }

    .dialog .btn3{width:250px;height:34px;padding:0;border-radius:4px;background-color:#48b3e2}
    .dialog .btn3:hover{text-decoration:none;background-color:#64c2eb}

    .consultant-brand{padding:35px 10px 30px;background-color:#48b3e2;overflow:hidden}
    .consultant-brand>li{float:left;text-align:center;width:160px;margin:0}
    .consultant-brand>li>p{color:#fff;font-size:14px}
    .consultant-brand>li>p.font-b{font-size:20px}

    .consultant-brand ul{overflow:hidden}
    .consultant-brand ul li{float:left;width:33.3%;text-align:center}
    .consultant-brand ul li .ico-tick{background:url(../images/ico-ww-d.png) no-repeat;vertical-align:sub;width:15px;height:15px;border-radius:50%}
    .consultant-brand ul li .text{font-size:14px;color:#fff}

    .dialog-zckf .zc,.dialog-zckf .zc img{width:420px;height:100px}
    .dialog-zckf h3{font-size:18px;color:#fff;margin-bottom:18px;text-align:center;font-weight:400}
    .dialog-zckf .consultant-brand{padding:26px 40px 30px 40px}
    .dialog-zckf .dialog-close .icon-close{color:#fff}

    .dialog-cons-wrap{padding:30px 50px 36px}

    .dialog .dialog-close{position: absolute;top: 10px;right: 12px;z-index:1000}
    .dialog-zckf .dialog-close .icon-close{color:#fff}
    @media screen and (max-width: 768px) {
        .dialog { width:100%; }
        .dialog-zckf .zc,.dialog-zckf .zc img{width:100%;height:auto}
        .dialog .ipt-area .ipt{width:100%;}
    }

</style>



<div class="modal fade in" id="grab-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-dismiss="modal" aria-hidden="true" data-keyboard="false">
    <div style="margin-top:64px;margin-bottom:64px;padding-top:32px;">

        <div class="modal-dialog">

            <div class="dialog dialog-zckf grab-clone-body">
                <a class="dialog-close ly-close" href="javascript:void(0)">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <span class="icon icon-close fa fa-close"></span>
                    </button>
                </a>
                <div class="dialog-bd">
                    <div class="consultant-brand">
                        <h3>时间地点行程您做主、把关提醒服务我来帮</h3>
                        <div class="zc">
                            <img src="{{ asset('/templates/moban2030/assets/others/zckf.gif') }}" alt="专车看房">
                        </div>
                    </div>
                    <div class="dialog-cons-wrap form-box">
                        <p class="con-txt">预约居理专车看房，楼下接您，随时出发，不花一分钱</p>
                        <div class="ipt-area">
                            <div>
                                <form id="form-grab-ticket">
                                    {{csrf_field()}}
                                    <input type="text" name="mobile" class="ipt mobile-ipt" id="grab-mobile" placeholder="输入预约手机号" value="">
                                </form>
                                <div class="error-msg"><span>请输入正确格式的手机号</span></div>
                            </div>
                        </div>
                        <div class="btn-area">
                            <button class="btn btn3 new_common_free_submit" id="grab-submit">抢专车券</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>




