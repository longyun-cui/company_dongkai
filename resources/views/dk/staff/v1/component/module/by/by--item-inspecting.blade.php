{{--审核--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--by--item-inspecting">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white" id="">


        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="modal--for--by--item-inspecting">
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
                            <select class="form-control modal-select2-" name="by-inspected-result" id="" style="width:100%;">
                                <option value="-1">选择审核结果</option>
                                @foreach(config('dk.common-config.by_inspected_result') as $k => $v)
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
                            <select class="form-control select2-reset select2--project"
                                    name="by_project_id"
                                    id="select2--project--for--by--item-inspecting"
                                    data-modal="#modal--for--by--item-inspecting"
                                    data-item-category="1"
                            >
                                <option data-id="" value="">选择项目</option>
                            </select>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
                        <div class="col-md-9 ">
                            <div class="col-sm-6 col-md-6 padding-0">
                                <select class="form-control modal--select2 select2-reset select2--location-city"
                                        name="by_location_city"
                                        id="select--location-city--for--by--item-inspecting"
                                        data-modal="#modal--for--by--item-inspecting"
                                        data-item-category="1"
                                        data-location-district-target="#select2--location-district--for--by--item-inspecting"
                                >
                                    <option value="">选择城市</option>
                                    @if(!empty($location_city_list) && count($location_city_list) > 0)
                                        @foreach($location_city_list as $v)
                                            <option value="{{ $v->location_city }}">{{ $v->location_city }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-6 padding-0">
                                <select class="form-control select2-reset select2--location"
                                        name="by_location_district"
                                        id="select2--location-district--for--by--item-inspecting"
                                        data-modal="#modal--for--by--item-inspecting"
                                        data-item-category="11"
                                        data-target="#select--location-city--for--by--item-inspecting"
                                >
                                    <option value="">选择区域</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{--患者类型--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 患者类型</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="by_client_type"
                                    data-modal="#modal--for--by--item-inspecting"
                            >
                                <option value="">选择患者类型</option>
                                @foreach(config('dk.common-config.dental_type') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 牙齿数量</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset dental-field-1"
                                    name="by_teeth_count"
                                    data-modal="#modal--for--by--item-inspecting"
                            >
                                <option value="">选择牙齿数量</option>
                                @foreach(config('dk.common-config.teeth_count') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--客户意向--}}
                    <div class="form-group by-inspected-accepted-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户意向</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="by_client_intention"
                                    data-modal="#modal--for--by--item-inspecting"
                            >
                                <option value="">选择客户意向</option>
                                @foreach(config('dk.common-config.client_intention') as $k => $v)
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