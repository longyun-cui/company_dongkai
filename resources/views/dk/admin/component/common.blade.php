{{--显示-信息--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-phone-pool-info">
    <div class="modal-content col-md-6 col-md-offset-3 margin-top-32 margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">
                    【<span class="field-set-item-name">订单</span>】
                    【<span class="field-set-item-id"></span>】
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-phone-pool-info-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-inspect" readonly>
                    <input type="hidden" name="detail-inspected-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-quality">
                        <label class="control-label col-md-2">评分</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="quality"></div>
                    </div>
                    {{--成单数--}}
                    <div class="form-group item-detail-order_cnt">
                        <label class="control-label col-md-2">成单数</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--成单时间--}}
                    <div class="form-group item-detail-order_date">
                        <label class="control-label col-md-2">成单时间</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--通话次数--}}
                    <div class="form-group item-detail-call_cnt">
                        <label class="control-label col-md-2">通话次数</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--1-8秒--}}
                    <div class="form-group item-detail-call_cnt_1_8">
                        <label class="control-label col-md-2">1-8秒</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--9秒以上--}}
                    <div class="form-group item-detail-call_cnt_9_above">
                        <label class="control-label col-md-2">9秒以上</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--最近提取时间--}}
                    <div class="form-group item-detail-last_extraction_date">
                        <label class="control-label col-md-2">最近提取时间</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>

                </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-default" id="modal-cancel-for-field-info">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--修改-属性-信息--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-field-set">
    <div class="modal-content col-md-6 col-md-offset-3 margin-top-64 margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">
                    修改
                    【<span class="field-set-item-name">订单</span>】
                    【<span class="field-set-item-id"></span>】
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-field-set">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate-category" value="field-set" readonly>
                    <input type="hidden" name="operate-type" value="add" readonly>
                    <input type="hidden" name="item-category" value="" data-default="" readonly>
                    <input type="hidden" name="item-type" value="" data-default="" readonly>
                    <input type="hidden" name="item-id" value="0" readonly>
                    <input type="hidden" name="column-type" value="" readonly>
                    <input type="hidden" name="column-key" value="" readonly>
                    <input type="hidden" name="column-key2" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 field-set-column-name"></label>
                        <div class="col-md-8 ">

                            <input type="text" class="form-control column-value" name="field-set-text-value" autocomplete="off" placeholder="" value="">

                            <textarea class="form-control column-value" name="field-set-textarea-value" rows="6" cols="100%"></textarea>

                            <input type="text" class="form-control form-filter column-value time_picker" name="field-set-datetime-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter column-value date_picker" name="field-set-date-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">

                            <div  class="form-control- form-filter- btn-group radio-wrapper">

                            </div>

                            <select class="form-control column-value select-primary" name="field-set-select-value" data-class="" style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                            <select class="form-control column-value select-assistant" name="field-set-select-value2" data-class=""  style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>

                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="edit-submit-for-field-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="edit-cancel-for-field-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--审核--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-detail-inspected">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white" id="">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-inspected-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-inspect" readonly>
                    <input type="hidden" name="detail-inspected-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-project">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--电话--}}
                    <div class="form-group item-detail-phone">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--是否+V--}}
                    <div class="form-group item-detail-is-wx">
                        <label class="control-label col-md-2">是否+V</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--微信号--}}
                    <div class="form-group item-detail-wx-id">
                        <label class="control-label col-md-2">微信号</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="driver"></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group item-detail-city-district">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-detail-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-detail-description">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-detail-recording">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                            <a class="btn btn-xs item-inspected-get-recording-list-submit">获取录音</a>
                        </div>
                    </div>
                    {{--播放速度--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">播放速度</label>
                        <div class="col-md-8 ">
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
                        <div class="col-md-8 ">
                            <select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">
                                <option value="-1">选择审核结果</option>
                                @foreach(config('info.inspected_result') as $v)
                                    <option value ="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--审核结果--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">录音质量</label>
                        <div class="col-md-8 ">
{{--                            <select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">--}}
{{--                                <option value="-1">选择录音质量</option>--}}
{{--                                <option value ="0" selected="selected">合格</option>--}}
{{--                                <option value ="1">优秀</option>--}}
{{--                                <option value ="9">问题</option>--}}
{{--                            </select>--}}

                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-quality" value="0" checked="checked"> 合格</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-quality" value="1"> 优秀</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-quality" value="9"> 问题</label>
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                    {{--审核说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">审核说明</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="detail-inspected-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success item-summit-for-detail-inspected" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default item-cancel-for-detail-inspected" id="">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--申诉--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-detail-appealed">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white" id="">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">申诉-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-appealed-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-appeal" readonly>
                    <input type="hidden" name="detail-appealed-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-project">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--电话--}}
                    <div class="form-group item-detail-phone">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group item-detail-city-district">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-detail-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-detail-description">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-detail-recording">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                            <a class="btn btn-xs item-inspected-get-recording-list-submit">获取录音</a>
                        </div>
                    </div>
                    {{--审核说明--}}
                    <div class="form-group item-inspected-description">
                        <label class="control-label col-md-2">审核说明</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音地址--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">录音地址</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="detail-appealed-url" placeholder="录音地址 带http">
                        </div>
                    </div>
                    {{--申诉说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">申诉说明</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="detail-appealed-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success modal-summit-for-detail-appealed" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel-for-detail-appealed" id="">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--申诉处理--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-detail-appealed-handled">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white" id="">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">申诉-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-appealed-handled-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-appeal-handle" readonly>
                    <input type="hidden" name="detail-appealed-handled-order-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-project">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--电话--}}
                    <div class="form-group item-detail-phone">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group item-detail-city-district">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-detail-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-detail-description">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-detail-recording">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                            <a class="btn btn-xs item-inspected-get-recording-list-submit">获取录音</a>
                        </div>
                    </div>
                    {{--审核说明--}}
                    <div class="form-group item-inspected-description">
                        <label class="control-label col-md-2">审核说明</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--申诉录音--}}
                    <div class="form-group item-appealed-url">
                        <label class="control-label col-md-2">申诉录音</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--申诉说明--}}
                    <div class="form-group item-appealed-description">
                        <label class="control-label col-md-2">申诉说明</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--处理结果--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">处理结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select-select2-" name="detail-appealed-handled-result" id="" style="width:100%;">
                                <option value="-1">选择处理结果</option>
                                @foreach(config('info.appealed_handled_result') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--申诉说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">处理说明</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="detail-appealed-handled-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success modal-summit-for-detail-appealed-handled" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel-for-detail-appealed-handled" id="">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--交付-deliver--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-deliver-set">
    <div class="modal-content col-md-6 col-md-offset-3 margin-top-16px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单交付【<span class="deliver-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-deliver-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="deliver-set-operate" value="item-order-deliver-option-set" readonly>
                    <input type="hidden" name="deliver-set-operate-type" value="add" readonly>
                    <input type="hidden" name="deliver-set-order-id" value="0" readonly>
                    <input type="hidden" name="deliver-set-column-key" value="" readonly>


                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付结果</label>
                        <div class="col-md-8 " id="deliver-set-distributed-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付订单</label>
                        <div class="col-md-8 " id="deliver-set-distributed-order-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付客户</label>
                        <div class="col-md-8 " id="deliver-set-distributed-client-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择项目</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box select2-project" name="deliver-set-project-id" style="width:48%;" id="">
                                <option value="-1">选择项目</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择客户</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="deliver-set-client-id" style="width:48%;" id="">
                                <option value="-1">选择客户</option>
                                @foreach($client_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">交付结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="deliver-set-delivered-result" style="width:48%;" id="">
                                <option value="-1">交付结果</option>
                                @foreach(config('info.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">录音地址</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="deliver-set-recording-address" autocomplete="off" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">交付说明</label>
                        <div class="col-md-8 ">
                            <textarea class="form-control" name="deliver-set-delivered-description" rows="4" cols="100%"></textarea>
                        </div>
                    </div>
                    <div class="form-group _none">
                        <label class="control-label col-md-2">是否允许分发</label>
                        <div class="col-md-8 ">
                            <div class="btn-group">

                                <button type="button" class="btn">
                            <span class="radio">
                                <label>
                                    <input type="radio" name="deliver-set-is_distributive_condition" value="0" class="info-set-column" checked="checked"> 否
                                </label>
                            </span>
                                </button>

                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="deliver-set-is_distributive_condition" value="1" class="info-set-column"> 是
                                    </label>
                                </span>
                                </button>

                            </div>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-deliver-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-deliver-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--分发-distribute--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-body-for-distribute-set">
    <div class="modal-content col-md-6 col-md-offset-3 margin-top-16px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单分发【<span class="distribute-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-distribute-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="distribute-set-operate" value="item-order-distribute-option-set" readonly>
                    <input type="hidden" name="distribute-set-operate-type" value="add" readonly>
                    <input type="hidden" name="distribute-set-order-id" value="0" readonly>
                    <input type="hidden" name="distribute-set-column-key" value="" readonly>


                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付结果</label>
                        <div class="col-md-8 " id="distribute-set-distributed-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付订单</label>
                        <div class="col-md-8 " id="distribute-set-distributed-order-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已分发客户</label>
                        <div class="col-md-8 " id="distribute-set-distributed-client-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择项目</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box select2-project" name="distribute-set-project-id" style="width:48%;" id="">
                                <option value="-1">选择项目</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择客户</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="distribute-set-client-id" style="width:48%;" id="">
                                <option value="-1">选择客户</option>
                                @foreach($client_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">分发结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="distribute-set-delivered-result" style="width:48%;" id="">
                                <option value="-1">交付结果</option>
                                @foreach(config('info.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-distribute-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-distribute-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--【百应】审核--}}
<div class="modal fade modal-main-body modal-wrapper" id="by-modal-body-for-item-inspected">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white" id="">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="by-form-inspected-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="by-item-inspect" readonly>
                    <input type="hidden" name="by-inspected-item-id" value="0" readonly>

                    {{--客户--}}
                    <div class="form-group item-inspected-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-9">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--所在城市--}}
{{--                    <div class="form-group item-inspected-city-district">--}}
{{--                        <label class="control-label col-md-2">所在城市</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <span class="item-detail-text"></span>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2 item-detail-operate" data-operate=""></div>--}}
{{--                    </div>--}}
                    {{--牙齿数量--}}
                    <div class="form-group item-inspected-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-9 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-inspected-description">
                        <label class="control-label col-md-2">对话</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-inspected-recording">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--播放速度--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">播放速度</label>
                        <div class="col-md-9 ">
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
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 审核结果</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal-select2" name="by-inspected-result" id="" style="width:100%;">
                                <option value="-1">选择审核结果</option>
                                @foreach(config('info.by_inspected_result') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--审核结果--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 录音质量</label>
                        <div class="col-md-9 ">
                            {{--<select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">--}}
                            {{--<option value="-1">选择录音质量</option>--}}
                            {{--<option value ="0" selected="selected">合格</option>--}}
                            {{--<option value ="1">优秀</option>--}}
                            {{--<option value ="9">问题</option>--}}
                            {{--</select>--}}

                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" class="radio-reset" name="by-recording-quality" value="0" checked="checked"> 合格</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" class="radio-reset" name="by-recording-quality" value="1"> 优秀</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" class="radio-reset" name="by-recording-quality" value="9"> 问题</label>
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
                            <textarea class="form-control textarea-reset" name="by-inspected-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--客户信息--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户信息</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control input-reset" name="by_client_name" placeholder="客户姓名" value="" data-default="">
                        </div>
                    </div>
                    {{--项目--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                        <div class="col-md-9 ">
                            <select class="form-control select2-reset select2-project" name="by_project_id" id="by-item-inspected-select2-project" data-item-category="1" style="width:100%;">
                                <option data-id="-1" value="-1">选择项目</option>
                            </select>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
                        <div class="col-md-9 ">
                            <div class="col-sm-6 col-md-6 padding-0">
                                <select class="form-control modal-select2 select2-reset select2-district-city" name="by_location_city" id="by-select-city-5" data-target="#by-select-district-5" style="width:100%;">
                                    <option value="">选择城市</option>
                                    @if(!empty($district_city_list) && count($district_city_list) > 0)
                                        @foreach($district_city_list as $v)
                                            <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-6 padding-0">
                                <select class="form-control modal-select2 select2-reset select2-district-district" name="by_location_district" id="by-select-district-5" data-target="#by-select-city-5" style="width:100%;">
                                    <option value="">选择区域</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{--患者类型--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 患者类型&牙齿数量</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal-select2 select2-reset" name="by_client_type" id="" style="width:100%;">
                                <option value="">选择患者类型</option>
                                @foreach(config('info.client_type') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 牙齿数量</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal-select2 select2-reset" name="by_teeth_count" id="" style="width:100%;">
                                <option value="">选择牙齿数量</option>
                                @foreach(config('info.teeth_count') as $v)
                                    <option value ="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--客户意向--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户意向</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal-select2 select2-reset" name="by_client_intention" id="" style="width:100%;">
                                <option value="">选择客户意向</option>
                                @foreach(config('info.client_intention') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                        <div class="col-md-9 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control textarea-reset" name="by_description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success by-modal-summit-for-item-inspected" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default by-modal-cancel-for-item-inspected" id="">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--option--}}
<div class="option-container _none">

        {{--城市列表--}}
        {{--    <div id="location-city-option-list">--}}
        {{--        <option value="">选择城市</option>--}}
        {{--        @foreach(config('info.location_city') as $k => $v)--}}
        {{--            <option value ="{{ $k }}" data-index="{{ $loop->index }}">{{ $k }}</option>--}}
        {{--        @endforeach--}}
        {{--    </div>--}}
        <div id="location-city-option-list">
            <option value="">选择城市</option>
            @if(!empty($district_city_list) && count($district_city_list) > 0)
                @foreach($district_city_list as $v)
                    <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                @endforeach
            @endif
        </div>


        {{--是否+V--}}
                <div class="btn-group" id="option-list-for-is-wx">

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="field-set-radio-value" value="0" class="info-set-column"> 否
                        </label>
                    </span>
                    </button>
                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="field-set-radio-value" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                    </button>

                </div>


    {{--是否+V--}}
                <div class="btn-group" id="option-list-for-field_2">

                    <button type="button" class="btn">
                        <span class="radio">
                            <label>
                                <input type="radio" name="field-set-radio-value" value="1" class="info-set-column"> 白班
                            </label>
                        </span>
                    </button>
                    <button type="button" class="btn">
                        <span class="radio">
                            <label>
                                <input type="radio" name="field-set-radio-value" value="9" class="info-set-column"> 夜班
                            </label>
                        </span>
                    </button>

                </div>

        {{--是否分发--}}
                <div class="btn-group" id="option-list-for-is-distributive">

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="field-set-radio-value" value="0" class="info-set-column"> 否
                        </label>
                    </span>
                    </button>
                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="field-set-radio-value" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                    </button>

                </div>

        <div id="option-list-for-is_distributive_condition-2">
            <label class="control-label col-md-2">是否是否分发</label>
            <div class="col-md-8">
                <div class="btn-group">

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="0" class="info-set-column" checked="checked"> 否
                        </label>
                    </span>
                    </button>
                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                    </button>

                </div>
            </div>
        </div>



        {{--分发状态--}}
        <div id="option-list-for-is_distributive_condition">
            <option value="0">选择分发状态</option>
            <option value="1">允许</option>
            <option value="9">禁止</option>
        </div>



        {{--选择客户--}}
        <div id="option-list-for-client">
            <option value="-1">选择客户</option>
            @foreach($client_list as $v)
                <option value="{{ $v->id }}">{{ $v->username }}</option>
            @endforeach
        </div>

        {{--审核结果--}}
        <div id="option-list-for-inspected-result">
            <option value="-1">审核结果</option>
            @foreach(config('info.inspected_result') as $v)
                <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
        </div>

        {{--交付结果--}}
        <div id="option-list-for-delivered-result">
            <option value="-1">交付结果</option>
            @foreach(config('info.delivered_result') as $v)
                <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
        </div>

        {{--牙齿数量--}}
        <div id="option-list-for-teeth-count">
            <option value="-1">选择牙齿数量</option>
            @foreach(config('info.teeth_count') as $v)
                <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
        </div>

        {{--渠道来源--}}
        <div id="option-list-for-channel-source">
            <option value="-1">选择渠道来源</option>
            @foreach(config('info.channel_source') as $v)
                <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
        </div>

        {{--客户意向--}}
        <div id="option-list-for-client-intention">
            <option value="-1">选择客户意向</option>
            @foreach(config('info.client_intention') as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </div>

        {{--患者类型--}}
        <div id="option-list-for-client-type">
            <option value="-1">选择患者类型</option>
            @foreach(config('info.client_type') as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </div>

    </div>


