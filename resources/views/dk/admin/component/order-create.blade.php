<form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
<div class="box-body">

    {{ csrf_field() }}
    <input type="hidden" name="operate" value="{{ $operate or 'create' }}" data-default="{{ $operate or 'create' }}" readonly>
    <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" data-default="{{ $operate_id or 0 }}" readonly>
    <input type="hidden" name="operate_category" value="{{ $operate_category or 'ITEM' }}" data-default="{{ $operate_category or 'ITEM' }}" readonly>
    <input type="hidden" name="operate_type" value="{{ $operate_type or 'order' }}" data-default="{{ $operate_type or 'order' }}" readonly>


    {{--自定义标题--}}
    <div class="form-group _none">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="">
        </div>
    </div>
    {{--项目--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
        <div class="col-md-8 ">
            <select class="form-control select2-container" name="project_id" id="select2-project" style="width:100%;">
                <option data-id="0" value="0">未指定</option>
            </select>
        </div>
    </div>

    {{--日期--}}
{{--    <div class="form-group">--}}
{{--        <label class="control-label col-md-2"><sup class="text-red">*</sup> 日期</label>--}}
{{--        <div class="col-md-8 ">--}}
{{--            <input type="text" class="form-control" name="assign_date" placeholder="日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}">--}}
{{--        </div>--}}
{{--    </div>--}}

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
    {{--患者类型--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 患者类型</label>
        <div class="col-md-8 ">
            <select class="form-control select-select2" name="client_type" id="" style="width:100%;">
                <option value="">选择患者类型</option>
                @foreach(config('info.client_type') as $k => $v)
                    <option value ="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>
    {{--客户意向--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户意向</label>
        <div class="col-md-8 ">
            <select class="form-control select-select2" name="client_intention" id="" style="width:100%;">
                <option value="">选择客户意向</option>
                @foreach(config('info.client_intention') as $k => $v)
                    <option value ="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>


    {{--团队大区--}}
{{--    <div class="form-group">--}}
{{--        <label class="control-label col-md-2">团队大区</label>--}}
{{--        <div class="col-md-8 ">--}}
{{--            <select class="form-control" name="team_district" id="">--}}
{{--                <option value="">选择大区</option>--}}
{{--                @foreach(config('info.team_district') as $v)--}}
{{--                    <option value ="{{ $v }}">{{ $v }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
{{--    </div>--}}
    {{--所在城市--}}
{{--    <div class="form-group">--}}
{{--        <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>--}}
{{--        <div class="col-md-8 ">--}}
{{--            <div class="col-sm-6 col-md-6 padding-0">--}}
{{--                <select class="form-control" name="location_city" id="select-city">--}}
{{--                    <option value="">选择城市</option>--}}
{{--                    @foreach(config('info.location_city') as $k => $v)--}}
{{--                        <option value ="{{ $k }}" data-index="{{ $loop->index }}">{{ $k }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="col-sm-6 col-md-6 padding-0">--}}
{{--                <select class="form-control" name="location_district" id="select-district">--}}
{{--                    <option value="">选择区域</option>--}}
{{--                </select>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    {{--所在城市--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <select class="form-control select2-modal-order-create select2-district-city" name="location_city" id="select-city-1" data-target="#select-district-1" style="width:100%;">
                    <option value="">选择城市</option>
                    @if(!empty($district_city_list) && count($district_city_list) > 0)
                        @foreach($district_city_list as $v)
                            <option value="{{ $v->district_city }}" @if($district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <select class="form-control select2-modal-order-create select2-district-district" name="location_district" id="select-district-1" data-target="#select-city-1" style="width:100%;">
                    <option value="">选择区域</option>
                </select>
            </div>
        </div>
    </div>
    {{--其他城市--}}
    <div class="form-group _none">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 其他城市</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                <input type="text" class="form-control" name="custom_location_city" placeholder="所在城市" id="custom-city">
            </div>
            <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                <input type="text" class="form-control" name="custom_location_district" placeholder="所在区域" id="custom-district">
            </div>
        </div>
    </div>
    {{--牙齿数量--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 牙齿数量</label>
        <div class="col-md-8 ">
            <select class="form-control select-select2" name="teeth_count" id="" style="width:100%;">
                <option value="">选择牙齿数量</option>
                @foreach(config('info.teeth_count') as $v)
                    <option value ="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>
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
        <label class="control-label col-md-2">通话小结</label>
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
            <p>范本：用户当前3颗后槽牙齿缺失，已经缺失半年，2颗下牙松动，之前没了解过种牙，好说话，要求下午3点前回电，同意医生助理联系</p>
        </div>
    </div>

    {{--备注--}}
    <div class="form-group _none">
        <label class="control-label col-md-2">备注</label>
        <div class="col-md-8 ">
            <textarea class="form-control" name="remark" rows="3" cols="100%"></textarea>
        </div>
    </div>




    {{--启用--}}
    <div class="form-group form-type _none">
        <label class="control-label col-md-2">启用</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <div class="radio">
                        <label>
                            <input type="radio" name="active" value="0" checked="checked"> 暂不启用
                        </label>
                    </div>
                </button>
                <button type="button" class="btn">
                    <div class="radio">
                        <label>
                            <input type="radio" name="active" value="1"> 启用
                        </label>
                    </div>
                </button>

            </div>
        </div>
    </div>

</div>
</form>

<div class="box-footer">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <button type="button" class="btn btn-success" id="edit-item-submit"><i class="fa fa-check"></i> 提交</button>
            <button type="button" class="btn btn-default" id="edit-item-cancel">取消</button>
{{--            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
        </div>
    </div>
</div>
