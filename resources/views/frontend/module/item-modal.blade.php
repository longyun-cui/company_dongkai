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
    <div style="margin-top:32px;margin-bottom:32px;padding-top:32px;">

        <div class="modal-dialog">

            {{--专车券--}}
            <div class="dialog dialog-zckf" id="dialog-zc" style="display:none;">
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
                                <form id="form-grab-zc">
                                    {{csrf_field()}}
                                    <input type="text" name="mobile" class="ipt mobile-ipt" id="grab-zc-mobile" placeholder="输入预约手机号" value="">
                                </form>
                                <div class="error-msg"><span>请输入正确格式的手机号</span></div>
                            </div>
                        </div>
                        <div class="btn-area">
                            <button class="btn btn3 new_common_free_submit" id="grab-zc-submit">抢专车券</button>
                        </div>
                    </div>
                </div>
            </div>

            {{--价格动态--}}
            <div class="dialog " id="dialog-jg" style="display:none;">
                <!--关闭按钮-->
                <a class="dialog-close ly-close" href="Javascript:void(0)" data-sub-status="2"><span class="icon icon-close"></span></a>
                <div class="dialog-bd">
                    <div class="content form-box">
                        <div class="title"><span class="pro-name">{{ $data->title or '' }}</span> 价格变动趋势</div>
                        <p class="con-info">价格变动频繁，订阅楼盘价格通知，涨价降价我们将第一时间通知您，帮你找准买房最佳时机！</p>
                        <div class="ipt-area">
                            <div>
                                <form id="form-grab-jg">
                                    {{csrf_field()}}
                                    <input type="hidden" name="item_id" value="{{ $item->id or 0 }}">
                                    <input type="text" name="mobile" class="ipt mobile-ipt" id="grab-jg-mobile" placeholder="请输入订阅手机号" value="">
                                </form>
                                <div class="error-msg"><span style="display: none;">请输入正确格式的手机号</span></div>
                            </div>
                        </div>
                        <div class="btn-area">
                            <button class="btn btn3 new_common_free_submit" id="grab-jg-submit">立即订阅</button>
                        </div>
                    </div>
                </div>
            </div>

            {{--开盘--}}
            <div class="dialog " id="dialog-item" style="display:none;">
                <a class="dialog-close ly-close" href="javascript:void(0)" data-sub-status="2"><span class="icon icon-close"></span></a>
                <div class="dialog-bd">
                    <div class="dialog-cons-wrap form-box">
                        <p class="con-txt">我们会第一时间为你提供报价</p>
                        <div class="ipt-area">
                            <div>
                                <form id="form-grab-item">
                                    {{csrf_field()}}
                                    <input type="hidden" name="item_id" value="{{ $item->id or 0 }}">
                                    <input type="text" name="name" class="ipt mobile-ipt" id="grab-item-name" placeholder="请输入姓名">
                                    <input type="text" name="mobile" class="ipt mobile-ipt" id="grab-item-mobile" placeholder="请输入手机号">
                                </form>
                                <p class="ipt-text">我们承诺，严格保障您的个人信息安全</p>
                                <div class="error-msg"><span style="display: none;">请输入正确格式的手机号</span></div>
                            </div>
                        </div>
                        <div class="btn-area">
                            <button class="btn btn3 new_common_free_submit" id="grab-item-submit">提交</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>




