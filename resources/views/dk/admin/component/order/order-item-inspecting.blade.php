<div class="modal fade modal-main-body modal-wrapper" id="modal-for-order-inspecting">
    <div class="modal-content col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 margin-top-16px margin-bottom-64px bg-white" id="">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <div class="box-body datatable-body" id="">

                <table class='table table-striped table-bordered' id='datatable-for-order-inspecting-phone-delivered-record'>
                    <thead>
                    <tr role='row' class='heading'>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

        </div>


        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-order-inspecting">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-inspect" readonly>
                    <input type="hidden" name="order-inspecting-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-project-box">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户信息</label>
                        <div class="col-md-9 ">
                            <div class="col-sm-3 col-md-3 col-lg-3 padding-0 item-name-box">
                                <span class="item-detail-text"></span>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3 padding-0 item-phone-box">
                                <span class="item-detail-text"></span>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3 padding-0 item-is-wx-box">
                                <span class="item-detail-text"></span>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3 padding-0 item-wx-id-box">
                                <span class="item-detail-text"></span>
                            </div>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group item-city-district-box">
                        <label class="control-label col-md-2">城市</label>
                        <div class="col-md-9 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-teeth-count-box">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-9 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-description-box">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-recording-box">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                            <a class="btn btn-xs item-inspected-get-recording-list-submit">获取录音</a>
                        </div>
                        <div class="col-md-9 col-md-offset-2">
                            <div class="btn-group">
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="0.75"> x0.75</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1" checked="checked"> x1</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1.25"> x1.25</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1.5"> x1.5</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="2"> x2</label>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{--审核结果--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">审核结果</label>
                        <div class="col-md-9 ">
                            <select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">
                                <option value="-1">选择审核结果</option>
                                @foreach(config('info.inspected_result') as $v)
                                    <option value ="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--录音质量--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">录音质量</label>
                        <div class="col-md-9 ">
                            {{--<select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">--}}
                            {{--<option value="-1">选择录音质量</option>--}}
                            {{--option value ="0" selected="selected">合格</option>--}}
                            {{--<option value ="1">优秀</option>--}}
                            {{--<option value ="9">问题</option>--}}
                            {{--</select>--}}

                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="order-inspecting-recording-quality" value="0" checked="checked"> 合格</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="order-inspecting-recording-quality" value="1"> 优秀</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="order-inspecting-recording-quality" value="9"> 问题</label>
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                    {{--审核说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">审核说明</label>
                        <div class="col-md-9 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="order-inspecting-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--编辑订单--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">编辑订单</label>
                        <div class="col-md-9 ">
                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="order-inspecting-edit-item" value="0" checked="checked"> 不编辑</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="order-inspecting-edit-item" value="1"> 编辑</label>
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success item-summit-for-order-inspecting" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default item-cancel-for-order-inspecting" id="">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>