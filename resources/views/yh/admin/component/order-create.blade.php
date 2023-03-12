<form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
<div class="box-body">

    {{ csrf_field() }}
    <input type="hidden" name="operate" value="{{ $operate or 'create' }}" readonly>
    <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
    <input type="hidden" name="operate_category" value="{{ $operate_category or 'ITEM' }}" readonly>
    <input type="hidden" name="operate_type" value="{{ $operate_type or 'order' }}" readonly>


    {{--自定义标题--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="">
        </div>
    </div>
    {{--客户 & 派车时间--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户 & 派车时间</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <select class="form-control" name="client_id" id="select2-client" style="width:100%;">
                    <option data-id="0" value="0">未指定</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="assign_date" placeholder="派车时间" readonly="readonly" value="">
            </div>
        </div>
    </div>


    {{--需求类型--}}
    <div class="form-group _none">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
        <div class="col-md-8 ">
            <select class="form-control" name="order_type">
                <option value="0">选择需求类型</option>
                <option value="1">自有</option>
                <option value="11">调车</option>
                <option value="21">配货</option>
                <option value="31">合同单向</option>
                <option value="41">合同双向</option>
            </select>
        </div>
    </div>

    {{--环线--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 环线</label>
        <div class="col-md-8 ">
            <select class="form-control" name="circle_id" id="select2-circle" style="width:100%;">
                <option data-id="0" value="0">未指定</option>
            </select>
        </div>
    </div>

    {{--线路选择--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 线路类型</label>
        <div class="col-md-8 ">
            <div class="btn-group">

                {{--@if($operate == 'create' || ($operate == 'edit' && $data->route_type == 1))--}}
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="route_type" value="1" checked="checked"> 固定线路
                        </label>
                    </span>
                </button>
                {{--@endif--}}

                {{--@if($operate == 'create' || ($operate == 'edit' && $data->route_type == 11))--}}
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="route_type" value=11> 临时线路
                        </label>
                    </span>
                </button>
                {{--@endif--}}

            </div>
        </div>
    </div>


    {{--固定线路--}}
        <div class="form-group route-fixed-box">
            <label class="control-label col-md-2"><sup class="text-red">*</sup> 固定线路</label>
            <div class="col-md-8 ">
                <select class="form-control" name="route_id" id="select2-route" style="width:100%;">
                    <option data-id="0" value="0">未指定</option>
                </select>
            </div>
        </div>

    {{--固定线路--}}
    <div class="form-group _none">
        <label class="control-label col-md-2">固定线路</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="route_fixed" placeholder="固定线路" value="">
        </div>
    </div>
    {{--临时线路--}}
    <div class="form-group route-temporary-box" style="display:none;">
        <label class="control-label col-md-2">临时线路</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="route_temporary" placeholder="临时线路" value="">
        </div>
    </div>

    {{--需求类型--}}
    <div class="form-group form-category">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
        <div class="col-md-8">
            <div class="btn-group">

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="car_owner_type" value="1" checked="checked"> 自有
                        </label>
                    </span>
                    </button>

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="car_owner_type" value=11> 空单
                        </label>
                    </span>
                    </button>

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="car_owner_type" value=41> 外配（配货）
                        </label>
                    </span>
                    </button>

                    <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="car_owner_type" value=61> 外请（调车）
                        </label>
                    </span>
                    </button>

            </div>
        </div>
    </div>


    {{--运费金额 & 油卡--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 运费金额 & 油卡</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="amount" placeholder="金额" value="0" id="order-price" readonly="readonly">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="oil_card_amount" placeholder="油卡" value="0">
            </div>
        </div>
    </div>
    {{--订金--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 订金</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="deposit" placeholder="订金" value="0">
        </div>
    </div>
    {{--请车价 & 管理费--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 请车价 & 管理费</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="outside_car_price" placeholder="请车价" value="0" id="order-out-car-price" readonly="readonly">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="administrative_fee" placeholder="管理费" value="0">
            </div>
        </div>
    </div>
    {{--信息费 & 客户管理费--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 信息费 & 客户管理费</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="information_fee" placeholder="信息费" value="0">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="customer_management_fee" placeholder="客户管理费" value="0">
            </div>
        </div>
    </div>
    {{--ECT--}}
    <div class="form-group">
        <label class="control-label col-md-2">ETC费用</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="ETC_price" placeholder="ETC费用" value="0">
        </div>
    </div>
    {{--万金油 & 油价--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 万金油(升) & 油价(元)</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="oil_amount" placeholder="万金油(升) " value="0">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="oil_unit_price" placeholder="油价(元)" value="0">
            </div>
        </div>
    </div>


    {{--包油定价--}}
    <div class="form-group">
        <label class="control-label col-md-2">包油定价</label>
        <div class="col-md-8 ">
            <select class="form-control" name="pricing_id" id="select2-pricing" style="width:100%;">
                <option data-id="0" value="0">未指定</option>
            </select>
        </div>
    </div>

    {{--自有车辆--}}
    <div class="form-group inside-car">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 自有车辆</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <select class="form-control" name="car_id" id="select2-car" style="width:100%;">
                    <option data-id="0" value="0">选择车辆</option>
                </select>
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <select class="form-control" name="trailer_id" id="select2-trailer" style="width:100%;">
                    <option data-id="0" value="0">选择车挂</option>
                </select>
            </div>
        </div>
    </div>


    {{--驾驶员--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">选择驾驶员</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <select class="form-control" name="driver_id" id="select2-driver">--}}
{{--                            @if($operate == 'edit' && $data->driver_id)--}}
{{--                                <option data-id="{{ $data->driver_id or 0 }}" value="{{ $data->driver_id or 0 }}">{{ $data->driver_er->driver_name }}</option>--}}
{{--                            @else--}}
{{--                                <option data-id="0" value="0">未指定</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}


    {{--外请或外派车辆--}}
    <div class="form-group outside-car" style="display:none">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 外部车辆</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="outside_car" placeholder="外部车车牌" value="">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="outside_trailer" placeholder="外部车挂" value="">
            </div>
        </div>
    </div>


    {{--外车·属性--}}
    <div class="form-group outside-car" style="display:none">
        <label class="control-label col-md-2">外车·属性</label>
        <div class="col-md-8 ">
            <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                <select class="form-control" name="trailer_type" id="">
                    <option value="0">选择箱型</option>
                    <option value="直板">直板</option>
                    <option value="高栏">高栏</option>
                    <option value="平板">平板</option>
                    <option value="冷藏">冷藏</option>
                </select>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                <select class="form-control" name="trailer_length" id="">
                    <option value="0">选择车挂尺寸</option>
                    <option value="9.6">9.6</option>
                    <option value="12.5">12.5</option>
                    <option value="15">15</option>
                    <option value="16.5">16.5</option>
                    <option value="17.5">17.5</option>
                </select>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                <select class="form-control" name="trailer_volume" id="">
                    <option value="0">选择承载方数</option>
                    <option value="125">125</option>
                    <option value="130">130</option>
                    <option value="135">135</option>
                </select>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                <select class="form-control" name="trailer_weight" id="">
                    <option value="0">选择承载重量</option>
                    <option value="13">13吨</option>
                    <option value="20">20吨</option>
                    <option value="25">25吨</option>
                </select>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                <select class="form-control" name="trailer_axis_count" id="">
                    <option value="0">选择轴数</option>
                    <option value="1">1轴</option>
                    <option value="2">2轴</option>
                    <option value="3">3轴</option>
                </select>
            </div>
        </div>
    </div>


    {{--箱型--}}
    {{--@if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))--}}
    {{--<div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>--}}
        {{--<label class="control-label col-md-2">箱型</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<select class="form-control" name="trailer_type" id="">--}}
                {{--<option value="0">选择箱型</option>--}}
                {{--<option value="直板" @if($operate == 'edit' && $data->trailer_type == '直板')selected="selected"@endif>直板</option>--}}
                {{--<option value="高栏" @if($operate == 'edit' && $data->trailer_type == '高栏')selected="selected"@endif>高栏</option>--}}
                {{--<option value="平板" @if($operate == 'edit' && $data->trailer_type == '平板')selected="selected"@endif>平板</option>--}}
                {{--<option value="冷藏" @if($operate == 'edit' && $data->trailer_type == '冷藏')selected="selected"@endif>冷藏</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--车挂尺寸--}}
    {{--@if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))--}}
    {{--<div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>--}}
        {{--<label class="control-label col-md-2">车挂尺寸</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<select class="form-control" name="trailer_length" id="">--}}
                {{--<option value="0">选择车挂尺寸</option>--}}
                {{--<option value="9.6" @if($operate == 'edit' && $data->trailer_length == '9.6')selected="selected"@endif>9.6</option>--}}
                {{--<option value="12.5" @if($operate == 'edit' && $data->trailer_length == '12.5')selected="selected"@endif>12.5</option>--}}
                {{--<option value="15" @if($operate == 'edit' && $data->trailer_length == '15')selected="selected"@endif>15</option>--}}
                {{--<option value="16.5" @if($operate == 'edit' && $data->trailer_length == '16.5')selected="selected"@endif>16.5</option>--}}
                {{--<option value="17.5" @if($operate == 'edit' && $data->trailer_length == '17.5')selected="selected"@endif>17.5</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--承载方数--}}
    {{--@if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))--}}
    {{--<div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>--}}
        {{--<label class="control-label col-md-2">承载方数</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<select class="form-control" name="trailer_volume" id="">--}}
                {{--<option value="0">选择承载方数</option>--}}
                {{--<option value="125" @if($operate == 'edit' && $data->trailer_volume == 125)selected="selected"@endif>125</option>--}}
                {{--<option value="130" @if($operate == 'edit' && $data->trailer_volume == 130)selected="selected"@endif>130</option>--}}
                {{--<option value="135" @if($operate == 'edit' && $data->trailer_volume == 135)selected="selected"@endif>135</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--承载重量--}}
    {{--@if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))--}}
    {{--<div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>--}}
        {{--<label class="control-label col-md-2">承载重量</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<select class="form-control" name="trailer_weight" id="">--}}
                {{--<option value="0">选择承载重量</option>--}}
                {{--<option value="13" @if($operate == 'edit' && $data->trailer_weight == 13)selected="selected"@endif>13吨</option>--}}
                {{--<option value="20" @if($operate == 'edit' && $data->trailer_weight == 20)selected="selected"@endif>20吨</option>--}}
                {{--<option value="25" @if($operate == 'edit' && $data->trailer_weight == 25)selected="selected"@endif>25吨</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--轴数--}}
    {{--@if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))--}}
    {{--<div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>--}}
        {{--<label class="control-label col-md-2">轴数</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<select class="form-control" name="trailer_axis_count" id="">--}}
                {{--<option value="0">选择轴数</option>--}}
                {{--<option value="1" @if($operate == 'edit' && $data->trailer_axis_count == 1)selected="selected"@endif>1轴</option>--}}
                {{--<option value="2" @if($operate == 'edit' && $data->trailer_axis_count == 2)selected="selected"@endif>2轴</option>--}}
                {{--<option value="3" @if($operate == 'edit' && $data->trailer_axis_count == 3)selected="selected"@endif>3轴</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--@endif--}}


    {{--主驾--}}
    <div class="form-group">
        <label class="control-label col-md-2">主驾</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="driver_name" placeholder="姓名" value="">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="driver_phone" placeholder="电话" value="">
            </div>
        </div>
    </div>
    {{--副驾--}}
    <div class="form-group">
        <label class="control-label col-md-2">副驾</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="copilot_name" placeholder="姓名" value="">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="copilot_phone" placeholder="电话" value="">
            </div>
        </div>
    </div>

    {{--箱型--}}
    <div class="form-group outside-car _none">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择箱型</label>
        <div class="col-md-8 ">
            <select class="form-control" name="container_type" id="select2-container">
                <option value="0">选择</option>
                <option value="9.6">9.6</option>
                <option value="12.5">12.5</option>
                <option value="15">15</option>
                <option value="16.5">16.5</option>
            </select>
        </div>
    </div>


    {{--规定时间--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 规定时间</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="should_departure" placeholder="应出发时间" readonly="readonly">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="should_arrival" placeholder="应到达时间" readonly="readonly">
            </div>
        </div>
    </div>

    {{--出发地 & 目的地--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 出发地 & 目的地</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="departure_place" placeholder="出发地" value="">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="destination_place" placeholder="目的地" value="">
            </div>
        </div>
    </div>
    {{--目的地--}}
    {{--<div class="form-group">--}}
        {{--<label class="control-label col-md-2"><sup class="text-red">*</sup> 目的地</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<input type="text" class="form-control" name="destination_place" placeholder="目的地" value="{{ $data->destination_place or '' }}">--}}
        {{--</div>--}}
    {{--</div>--}}
    {{--经停地--}}
    <div class="form-group">
        <label class="control-label col-md-2">经停地</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="stopover_place" placeholder="经停地" value="">
        </div>
    </div>


    {{--里程 & 时效--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 里程(公里) & 时效(小时)</label>
        <div class="col-md-8 ">
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="travel_distance" placeholder="里程" value="0">
            </div>
            <div class="col-sm-6 col-md-6 padding-0">
                <input type="text" class="form-control" name="time_limitation_prescribed" placeholder="时效" value="0">
            </div>
        </div>
    </div>




    {{--所属公司--}}
    <div class="form-group">
        <label class="control-label col-md-2">所属公司</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="subordinate_company" placeholder="所属公司" value="">
        </div>
    </div>

    {{--是否需要回单 & 回单地址--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 是否需要回单 & 回单地址</label>
        <div class="col-md-8 ">
            <div class="col-sm-4 col-md-4 padding-0">
                <div class="btn-group">

                    <button type="button" class="btn">
                        <span class="radio">
                            <label>
                                    <input type="radio" name="receipt_need" value="0" checked="checked"> 不需要
                            </label>
                        </span>
                    </button>

                    <button type="button" class="btn">
                        <span class="radio">
                            <label>
                                    <input type="radio" name="receipt_need" value="1"> 需要
                            </label>
                        </span>
                    </button>

                </div>
            </div>
            <div class="col-sm-8 col-md-8 padding-0">
                <input type="text" class="form-control" name="receipt_address" placeholder="回单地址" value="">
            </div>
        </div>
    </div>
    {{--回单状态--}}
    {{--<div class="form-group _none">--}}
        {{--<label class="control-label col-md-2">回单状态</label>--}}
        {{--<div class="col-md-8 ">--}}
            {{--<input type="text" class="form-control" name="receipt_status" placeholder="回单状态" value="{{ $data->receipt_status or '' }}">--}}
        {{--</div>--}}
    {{--</div>--}}

    {{--GPS--}}
    <div class="form-group">
        <label class="control-label col-md-2">GPS</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="GPS" placeholder="GPS" value="">
        </div>
    </div>


    {{--单号--}}
    <div class="form-group">
        <label class="control-label col-md-2">单号</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="order_number" placeholder="单号" value="">
        </div>
    </div>
    {{--收款人--}}
    <div class="form-group">
        <label class="control-label col-md-2">收款人</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="payee_name" placeholder="收款人" value="">
        </div>
    </div>
    {{--安排人--}}
    <div class="form-group">
        <label class="control-label col-md-2">安排人</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="arrange_people" placeholder="安排人" value="">
        </div>
    </div>
    {{--车货源--}}
    <div class="form-group">
        <label class="control-label col-md-2">车货源</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="car_supply" placeholder="车货源" value="">
        </div>
    </div>
    {{--车辆管理人--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">车辆管理人</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <input type="text" class="form-control" name="car_managerial_people" placeholder="车辆管理人" value="{{ $data->car_managerial_people or '' }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
    {{--重量--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">重量</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <input type="text" class="form-control" name="weight" placeholder="重量" value="{{ $data->weight or '' }}">--}}
{{--                    </div>--}}
    </div>

    {{--备注--}}
    <div class="form-group">
        <label class="control-label col-md-2">备注</label>
        <div class="col-md-8 ">
            <textarea class="form-control" name="remark" rows="3" cols="100%"></textarea>
        </div>
    </div>




    {{--描述--}}
    <div class="form-group _none">
        <label class="control-label col-md-2">描述</label>
        <div class="col-md-8 ">
            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
            <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
        </div>
    </div>

    {{--头像--}}
    <div class="form-group _none">
        <label class="control-label col-md-2">头像</label>
        <div class="col-md-8 fileinput-group">

            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    @if(!empty($data->portrait_img))
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt="" />
                    @endif
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail">
                </div>
                <div class="btn-tool-group">
                    <span class="btn-file">
                        <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                        <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                        <input type="file" name="portrait" />
                    </span>
                    <span class="">
                        <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                    </span>
                </div>
            </div>
            <div id="titleImageError" style="color: #a94442"></div>

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
            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
        </div>
    </div>
</div>
