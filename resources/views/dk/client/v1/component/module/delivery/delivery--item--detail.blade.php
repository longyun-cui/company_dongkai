{{--工单详情--}}
<div class="modal fade modal-wrapper" id="modal--for--delivery--item--detail">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">工单详情</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered">
                <div class="box-body">


                    {{--订单ID--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">工单ID</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="delivery-id-title"></span>
                        </div>
                    </div>
                    {{--姓名--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户姓名</label>
                        <div class="col-md-9 ">
                            <span class="delivery-client-name-box"></span>
                        </div>
                    </div>
                    {{--电话--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户电话</label>
                        <div class="col-md-9 ">
                            <span class="delivery-client-mobile-box"></span>
                        </div>
                    </div>
                    {{--微信--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户微信</label>
                        <div class="col-md-9 ">
                            <span class="delivery-client-wx-box"></span>
                        </div>
                    </div>
                    {{--意向--}}
                    <div class="form-group dental-show">
                        <label class="control-label col-md-2">客户意向</label>
                        <div class="col-md-9 ">
                            <span class="delivery-client-intention-box"></span>
                        </div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group dental-show">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-9 ">
                            <span class="delivery-teeth-count-box"></span>
                        </div>
                    </div>
                    {{--城市--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-9 ">
                            <span class="delivery-location-box"></span>
                        </div>
                    </div>
                    {{--通话记录--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">通话记录</label>
                        <div class="col-md-9 ">
                            <span class="delivery-description-box"></span>
                        </div>
                    </div>
                    {{--通话录音--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-9 ">
                            <span class="delivery-recording-address-box"></span>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

