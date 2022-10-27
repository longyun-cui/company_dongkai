{{--图片展示--}}
<section class="section-container bg-white">
    <div class="row">

        <header class="module-row module-header-container with-border-bottom text-center">
            <div class="wow slideInLeft module-title-row color-1 border-light title-h2"><b>联系经纪人</b></div>
            <a class="pull-right print-btn _none" href="javascript:window.print()">Print This Property <i class="fa fa-print"></i></a>
        </header>

        <div class="module-row module-body-container property-contents" id="">
            <div class="col-lg-7">
                <div class="agent-box clearfix">
                    <div class="row">
                        <div class="col-sm-4 col-xs-4">
                            <a href="javascript:void(0);" class="agent-image">
                                <img src="{{ url('/templates/moban2030/assets/images/agents/2.jpg') }}" alt="Agent Image">
                            </a>
                        </div>
                        <div class="col-sm-8 col-xs-8">
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
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. luctus ligula ac faucibus faucibus. Cras in nisi in turpis eleifend vehicula at at massa.</p>
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

    </div>
</section>