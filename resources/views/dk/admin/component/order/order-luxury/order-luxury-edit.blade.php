{{--编辑-工单--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-order-luxury-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加（奢侈品）工单</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-order-luxury-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="order" data-default="order">
                <input readonly type="hidden" class="form-control" name="operate[order_category]" value="luxury" data-default="luxury">



                {{--自定义标题--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="">
                    </div>
                </div>
                {{--班次--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 班次</label>
                    <div class="col-md-8 ">
                        <select class="form-control select-select2 select2-box-c" name="field_2" id="" style="width:100%;">
                            <option value="">选择班次</option>
                            <option value ="1">白班</option>
                            <option value ="9">夜班</option>
                        </select>
                    </div>
                </div>
                {{--项目--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-box-c select2-project" name="project_id" id="order-edit-select2-project" data-item-category="31" style="width:100%;">
                            <option data-id="-1" value="-1">选择项目</option>
                        </select>
                    </div>
                </div>

                {{--客户信息--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户信息</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="client_name" placeholder="客户姓名" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="client_phone" placeholder="客户电话" value="" data-default="">
                        </div>
                    </div>
                </div>

                {{--所在城市--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select-select2 select2-box-c select2-district-city" name="location_city" id="select-city-2" data-target="#select-district-2" style="width:100%;">
                                <option value="">选择城市</option>
                                @if(!empty($district_city_list) && count($district_city_list) > 0)
                                    @foreach($district_city_list as $v)
                                        <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select-select2 select2-box-c select2-district-district" name="location_district" id="select-district-2" data-target="#select-city-2" style="width:100%;">
                                <option value="">选择区域</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{--患者类型--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 品类</label>
                    <div class="col-md-8 ">
                        <select class="form-control select-select2 select2-box-c" name="field_1" id="" style="width:100%;">
                            <option value="">选择品类</option>
                            @foreach(config('info.luxury_type') as $k => $v)
                                <option value ="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{--客户意向--}}
{{--                <div class="form-group _none">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户意向</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <select class="form-control select-select2 select2-box-c" name="client_intention" id="" style="width:100%;">--}}
{{--                            <option value="">选择客户意向</option>--}}
{{--                            @foreach(config('info.client_intention') as $k => $v)--}}
{{--                                <option value ="{{ $k }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}


                {{--是否+V--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 是否+V</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-4 col-md-4 padding-0">
                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="is_wx" value="0" checked="checked"> 否</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="is_wx" value="1"> 是</label>
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
                {{--微信号--}}
                <div class="form-group wx_box">
                    <label class="control-label col-md-2">微信号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="wx_id" placeholder="微信号" value="" data-default="">
                    </div>
                </div>
                {{--渠道来源--}}
                {{--    <div class="form-group">--}}
                {{--        <label class="control-label col-md-2"><sup class="text-red">*</sup> 渠道来源</label>--}}
                {{--        <div class="col-md-8 ">--}}
                {{--            <select class="form-control" name="channel_source" id="">--}}
                {{--                <option value="">选择渠道</option>--}}
                {{--                @foreach(config('info.channel_source') as $v)--}}
                {{--                    <option value ="{{ $v }}">{{ $v }}</option>--}}
                {{--                @endforeach--}}
                {{--            </select>--}}
                {{--        </div>--}}
                {{--    </div>--}}

                {{--录音地址--}}
                <div class="form-group">
                    <label class="control-label col-md-2">录音地址</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="recording_address" placeholder="录音地址" value="" data-default="">
                    </div>
                </div>
                {{--通话小结--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                    </div>
                </div>

                {{--通话小结--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                    <div class="col-md-8 ">
                        <p>要求：准确，全面，丰富</p>
                        <p>范本：用户当前</p>
                    </div>
                </div>


                {{--班次--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 班次</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <div class="col-sm-4 col-md-4 padding-0">--}}
{{--                            <div class="btn-group">--}}

{{--                                <button type="button" class="btn">--}}
{{--                                    <span class="radio">--}}
{{--                                        <label><input type="radio" name="field_2" value="1" checked="checked"> 白班</label>--}}
{{--                                    </span>--}}
{{--                                </button>--}}
{{--                                <button type="button" class="btn">--}}
{{--                                    <span class="radio">--}}
{{--                                        <label><input type="radio" name="field_2" value="9"> 夜班</label>--}}
{{--                                    </span>--}}
{{--                                </button>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-order-luxury">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>