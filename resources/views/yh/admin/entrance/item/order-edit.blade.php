@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '订单列表' }}</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">{{ $title_text or '' }}</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="operate_category" value="{{ $operate_category or 'ITEM' }}" readonly>
                <input type="hidden" name="operate_type" value="{{ $operate_type or 'order' }}" readonly>


                {{--自定义标题--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="{{ $data->title or '' }}">
                    </div>
                </div>
                {{--客户 & 派车时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户 & 派车时间</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control" name="client_id" id="select2-client">
                                @if($operate == 'edit' && $data->client_id)
                                    <option data-id="{{ $data->client_id or 0 }}" value="{{ $data->client_id or 0 }}">{{ $data->client_er->username }}</option>
                                @else
                                    <option data-id="0" value="0">未指定</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="assign_date" placeholder="派车时间" readonly="readonly"
                                   @if(!empty($data->assign_time)) value="{{ date("Y-m-d",$data->assign_time) }}" @endif
                            >
                        </div>
                    </div>
                </div>


                {{--需求类型--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="order_type">
                            <option value="0">选择需求类型</option>
                            <option value="1" @if($operate == 'edit' && $data->order_type == 1)selected="selected"@endif>自有</option>
                            <option value="11" @if($operate == 'edit' && $data->order_type == 11)selected="selected"@endif>调车</option>
                            <option value="21" @if($operate == 'edit' && $data->order_type == 21)selected="selected"@endif>配货</option>
                            <option value="31" @if($operate == 'edit' && $data->order_type == 31)selected="selected"@endif>合同单向</option>
                            <option value="41" @if($operate == 'edit' && $data->order_type == 41)selected="selected"@endif>合同双向</option>
                        </select>
                    </div>
                </div>

                {{--环线--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 环线</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="circle_id" id="select2-circle">
                            @if($operate == 'edit' && $data->circle_id)
                                <option data-id="{{ $data->circle_id or 0 }}" value="{{ $data->circle_id or 0 }}">{{ $data->circle_er->title }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
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
                                        @if($operate == 'create' || ($operate == 'edit' && $data->route_type == 1))
                                            <input type="radio" name="route_type" value="1" checked="checked"> 固定线路
                                        @else
                                            <input type="radio" name="route_type" value="1"> 固定线路
                                        @endif
                                    </label>
                                </span>
                            </button>
                            {{--@endif--}}

                            {{--@if($operate == 'create' || ($operate == 'edit' && $data->route_type == 11))--}}
                            <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->route_type == 11)
                                            <input type="radio" name="route_type" value=11 checked="checked"> 临时线路
                                        @else
                                            <input type="radio" name="route_type" value=11> 临时线路
                                        @endif
                                    </label>
                                </span>
                            </button>
                            {{--@endif--}}

                        </div>
                    </div>
                </div>


                {{--固定线路--}}
                @if($operate == 'create' || ($operate == 'edit' && $data->route_type == 1))
                    <div class="form-group route-fixed-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 固定线路</label>
                        <div class="col-md-8 ">
                            <select class="form-control" name="route_id" id="select2-route">
                                @if($operate == 'edit' && $data->route_id)
                                    <option data-id="{{ $data->route_id or 0 }}"
                                            value="{{ $data->route_id or 0 }}"
                                            data-price="{{ $data->route_er->amount_with_cash or 0 }}"
                                            data-distance="{{ $data->route_er->travel_distance or 0 }}"
                                            data-prescribed="{{ $data->route_er->time_limitation_prescribed or 0 }}"
                                            data-departure="{{ $data->route_er->departure_place or '' }}"
                                            data-destination="{{ $data->route_er->destination_place or '' }}"
                                            data-stopover="{{ $data->route_er->stopover_place or '' }}"
                                    >
                                        {{ $data->route_er->title }}
                                    </option>
                                @else
                                    <option data-id="0" value="0">未指定</option>
                                @endif
                            </select>
                        </div>
                    </div>
                @endif

                {{--固定线路--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">固定线路</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="route_fixed" placeholder="固定线路" value="{{ $data->route_fixed or '' }}">
                    </div>
                </div>
                {{--临时线路--}}
                <div class="form-group route-temporary-box" style="display:none;">
                    <label class="control-label col-md-2">临时线路</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="route_temporary" placeholder="临时线路" value="{{ $data->route_temporary or '' }}">
                    </div>
                </div>

                {{--需求类型--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

{{--                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))--}}
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))
                                            <input type="radio" name="car_owner_type" value="1" checked="checked"> 自有
                                        @else
                                            <input type="radio" name="car_owner_type" value="1"> 自有
                                        @endif
                                    </label>
                                </span>
                                </button>
{{--                            @endif--}}

{{--                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 11))--}}
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->car_owner_type == 11)
                                            <input type="radio" name="car_owner_type" value=11 checked="checked"> 空单
                                        @else
                                            <input type="radio" name="car_owner_type" value=11> 空单
                                        @endif
                                    </label>
                                </span>
                                </button>
{{--                            @endif--}}

{{--                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 41))--}}
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->car_owner_type == 41)
                                            <input type="radio" name="car_owner_type" value=41 checked="checked"> 外配（配货）
                                        @else
                                            <input type="radio" name="car_owner_type" value=41> 外配（配货）
                                        @endif
                                    </label>
                                </span>
                                </button>
{{--                            @endif--}}

{{--                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 61))--}}
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->car_owner_type == 61)
                                            <input type="radio" name="car_owner_type" value=61 checked="checked"> 外请（调车）
                                        @else
                                            <input type="radio" name="car_owner_type" value=61> 外请（调车）
                                        @endif
                                    </label>
                                </span>
                                </button>
{{--                            @endif--}}

                        </div>
                    </div>
                </div>


                {{--运费金额 & 油卡--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 运费金额 & 油卡</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="amount" placeholder="金额" value="{{ $data->amount or 0 }}" id="order-price"
                                   @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif
                            >
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="oil_card_amount" placeholder="油卡" value="{{ $data->oil_card_amount or 0 }}">
                        </div>
                    </div>
                </div>
                {{--订金--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 订金</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="deposit" placeholder="订金" value="{{ $data->deposit or 0 }}">
                    </div>
                </div>
                {{--请车价 & 管理费--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 请车价 & 管理费</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="outside_car_price" placeholder="请车价" value="{{ $data->outside_car_price or 0 }}" id="order-out-car-price">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="administrative_fee" placeholder="管理费" value="{{ $data->administrative_fee or 0 }}">
                        </div>
                    </div>
                </div>
                {{--信息费 & 客户管理费--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 信息费 & 客户管理费</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="information_fee" placeholder="信息费" value="{{ $data->information_fee or 0 }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="customer_management_fee" placeholder="客户管理费" value="{{ $data->customer_management_fee or 0 }}">
                        </div>
                    </div>
                </div>
                {{--ECT--}}
                <div class="form-group">
                    <label class="control-label col-md-2">ETC费用</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="ETC_price" placeholder="ETC费用" value="{{ $data->ETC_price or 0 }}">
                    </div>
                </div>
                {{--万金油 & 油价--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 万金油(升) & 油价(元)</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="oil_amount" placeholder="万金油(升) " value="{{ $data->oil_amount or 0 }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="oil_unit_price" placeholder="油价(元)" value="{{ $data->oil_unit_price or 0 }}">
                        </div>
                    </div>
                </div>


                {{--包油定价--}}
                <div class="form-group">
                    <label class="control-label col-md-2">包油定价</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="pricing_id" id="select2-pricing">
                            @if($operate == 'edit' && $data->pricing_id)
                                <option data-id="{{ $data->pricing_id or 0 }}" value="{{ $data->pricing_id or 0 }}">{{ $data->pricing_er->title }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>




                {{--外请或外派车辆--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外部车辆</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="outside_car" placeholder="外部车车牌" value="{{ $data->outside_car or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="outside_trailer" placeholder="外部车挂" value="{{ $data->outside_trailer or '' }}">
                        </div>
                    </div>
                </div>
                @endif


                {{--外车·属性--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 61)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
                    <label class="control-label col-md-2">外车·属性</label>
                    <div class="col-md-8 ">
                        <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                            <select class="form-control" name="trailer_type" id="">
                                <option value="0">选择箱型</option>
                                <option value="直板" @if($operate == 'edit' && $data->trailer_type == '直板')selected="selected"@endif>直板</option>
                                <option value="高栏" @if($operate == 'edit' && $data->trailer_type == '高栏')selected="selected"@endif>高栏</option>
                                <option value="平板" @if($operate == 'edit' && $data->trailer_type == '平板')selected="selected"@endif>平板</option>
                                <option value="冷藏" @if($operate == 'edit' && $data->trailer_type == '冷藏')selected="selected"@endif>冷藏</option>
                            </select>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                            <select class="form-control" name="trailer_length" id="">
                                <option value="0">选择车挂尺寸</option>
                                <option value="9.6" @if($operate == 'edit' && $data->trailer_length == '9.6')selected="selected"@endif>9.6</option>
                                <option value="12.5" @if($operate == 'edit' && $data->trailer_length == '12.5')selected="selected"@endif>12.5</option>
                                <option value="15" @if($operate == 'edit' && $data->trailer_length == '15')selected="selected"@endif>15</option>
                                <option value="16.5" @if($operate == 'edit' && $data->trailer_length == '16.5')selected="selected"@endif>16.5</option>
                                <option value="17.5" @if($operate == 'edit' && $data->trailer_length == '17.5')selected="selected"@endif>17.5</option>
                            </select>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                            <select class="form-control" name="trailer_volume" id="">
                                <option value="0">选择承载方数</option>
                                <option value="125" @if($operate == 'edit' && $data->trailer_volume == 125)selected="selected"@endif>125</option>
                                <option value="130" @if($operate == 'edit' && $data->trailer_volume == 130)selected="selected"@endif>130</option>
                                <option value="135" @if($operate == 'edit' && $data->trailer_volume == 135)selected="selected"@endif>135</option>
                            </select>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                            <select class="form-control" name="trailer_weight" id="">
                                <option value="0">选择承载重量</option>
                                <option value="13" @if($operate == 'edit' && $data->trailer_weight == 13)selected="selected"@endif>13吨</option>
                                <option value="20" @if($operate == 'edit' && $data->trailer_weight == 20)selected="selected"@endif>20吨</option>
                                <option value="25" @if($operate == 'edit' && $data->trailer_weight == 25)selected="selected"@endif>25吨</option>
                            </select>
                        </div>
                        <div class="col-xs-6 col-sm-4 col-md-4 padding-0">
                            <select class="form-control" name="trailer_axis_count" id="">
                                <option value="0">选择轴数</option>
                                <option value="1" @if($operate == 'edit' && $data->trailer_axis_count == 1)selected="selected"@endif>1轴</option>
                                <option value="2" @if($operate == 'edit' && $data->trailer_axis_count == 2)selected="selected"@endif>2轴</option>
                                <option value="3" @if($operate == 'edit' && $data->trailer_axis_count == 3)selected="selected"@endif>3轴</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif


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

                {{--自有车辆--}}
                @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))
                <div class="form-group inside-car">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自有车辆</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control" name="car_id" id="select2-car">
                                @if($operate == 'edit' && $data->car_id)
                                    <option data-id="{{ $data->car_id or 0 }}" value="{{ $data->car_id or 0 }}">{{ $data->car_er->name }}</option>
                                @else
                                    <option data-id="0" value="0">选择车辆</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control" name="trailer_id" id="select2-trailer">
                                @if($operate == 'edit' && $data->trailer_id)
                                    <option data-id="{{ $data->trailer_id or 0 }}" value="{{ $data->trailer_id or 0 }}">{{ $data->trailer_er->name }}</option>
                                @else
                                    <option data-id="0" value="0">选择车挂</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                {{--驾驶员--}}
                <div class="form-group">
                    <label class="control-label col-md-2">选择驾驶员</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="driver_id" id="select2-driver">
                            @if($operate == 'edit' && $data->driver_id)
                                <option data-id="{{ $data->driver_id or 0 }}" value="{{ $data->driver_id or 0 }}">{{ $data->driver_er->driver_name or '' }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{--主驾--}}
                <div class="form-group">
                    <label class="control-label col-md-2">主驾</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_name" placeholder="姓名" value="{{ $data->driver_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_phone" placeholder="电话" value="{{ $data->driver_phone or '' }}">
                        </div>
                    </div>
                </div>
                {{--副驾--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="copilot_name" placeholder="姓名" value="{{ $data->copilot_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="copilot_phone" placeholder="电话" value="{{ $data->copilot_phone or '' }}">
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
                            <input type="text" class="form-control" name="should_departure" placeholder="应出发时间" readonly="readonly"
                                   @if(!empty($data->should_departure_time)) value="{{ date("Y-m-d H:i",$data->should_departure_time) }}" @endif
                            >
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="should_arrival" placeholder="应到达时间" readonly="readonly"
                                   @if(!empty($data->should_arrival_time)) value="{{ date("Y-m-d H:i",$data->should_arrival_time) }}" @endif
                            >
                        </div>
                    </div>
                </div>

                {{--出发地 & 目的地--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 出发地 & 目的地</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="departure_place" placeholder="出发地" value="{{ $data->departure_place or '' }}" @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif >
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="destination_place" placeholder="目的地" value="{{ $data->destination_place or '' }}" @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif >
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
                        <input type="text" class="form-control" name="stopover_place" placeholder="经停地" value="{{ $data->stopover_place or '' }}" @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif >
                    </div>
                </div>


                {{--里程 & 时效--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 里程(公里) & 时效(小时)</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="travel_distance" placeholder="里程" value="{{ $data->travel_distance or 0 }}" @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif >
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="time_limitation_prescribed" placeholder="时效" value="{{ $data->time_limitation_prescribed or 0 }}" @if($operate == 'edit' && $data->route_id > 0) readonly="readonly" @endif >
                        </div>
                    </div>
                </div>




                {{--所属公司--}}
                <div class="form-group">
                    <label class="control-label col-md-2">所属公司</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="subordinate_company" placeholder="所属公司" value="{{ $data->subordinate_company or '' }}">
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
                                            @if($operate == 'create' || ($operate == 'edit' && $data->receipt_need == 0))
                                                <input type="radio" name="receipt_need" value="0" checked="checked"> 不需要
                                            @else
                                                <input type="radio" name="receipt_need" value="0"> 不需要
                                            @endif
                                        </label>
                                    </span>
                                </button>

                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->receipt_need == 1)
                                                <input type="radio" name="receipt_need" value="1" checked="checked"> 需要
                                            @else
                                                <input type="radio" name="receipt_need" value="1"> 需要
                                            @endif
                                        </label>
                                    </span>
                                </button>

                            </div>
                        </div>
                        <div class="col-sm-8 col-md-8 padding-0">
                            <input type="text" class="form-control" name="receipt_address" placeholder="回单地址" value="{{ $data->receipt_address or '' }}">
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
                        <input type="text" class="form-control" name="GPS" placeholder="GPS" value="{{ $data->GPS or '' }}">
                    </div>
                </div>


                {{--单号--}}
                <div class="form-group">
                    <label class="control-label col-md-2">单号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="order_number" placeholder="单号" value="{{ $data->order_number or '' }}">
                    </div>
                </div>
                {{--收款人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">收款人</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="payee_name" placeholder="收款人" value="{{ $data->payee_name or '' }}">
                    </div>
                </div>
                {{--安排人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">安排人</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="arrange_people" placeholder="安排人" value="{{ $data->arrange_people or '' }}">
                    </div>
                </div>
                {{--车货源--}}
                <div class="form-group">
                    <label class="control-label col-md-2">车货源</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="car_supply" placeholder="车货源" value="{{ $data->car_supply or '' }}">
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
                        <textarea class="form-control" name="remark" rows="3" cols="100%">{{ $data->remark or '' }}</textarea>
                    </div>
                </div>




                {{--描述--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
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
                @if($operate == 'create')
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
                @endif

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
        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endsection




@section('custom-css')
@endsection




@section('custom-script')
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【选择车辆所属】
        $("#form-edit-item").on('click', "input[name=car_owner_type]", function() {
            // checkbox
//            if($(this).is(':checked'))
//            {
//                $('.time-show').show();
//            }
//            else
//            {
//                $('.time-show').hide();
//            }
            // radio
            var $value = $(this).val();
            if($value == 1 || $value == 11 || $value == 41)
            {
                $('.inside-car').show();
                $('.outside-car').hide();
            }
            else
            {
                $('.outside-car').show();
                $('.inside-car').hide();
            }
        });

        // 【选择线路类型】
        $("#form-edit-item").on('click', "input[name=route_type]", function() {
            // radio
            var $value = $(this).val();
            if($value == 1)
            {
                $('.route-fixed-box').show();
                $('.route-temporary-box').hide();


                var $select2_route_val = $('#select2-route').val();
                console.log($select2_route_val);
                var $select2_route_selected = $('#select2-route').find('option:selected');
                if($select2_route_selected.val() > 0)
                {
                    $('#order-price').attr('readonly','readonly').val($select2_route_selected.attr('data-price'));
                    $('input[name=departure_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-departure'));
                    $('input[name=destination_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-destination'));
                    $('input[name=stopover_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-stopover'));
                    $('input[name=travel_distance]').attr('readonly','readonly').val($select2_route_selected.attr('data-distance'));
                    $('input[name=time_limitation_prescribed]').attr('readonly','readonly').val($select2_route_selected.attr('data-prescribed'));
                }
            }
            else
            {
                $('.route-temporary-box').show();
                $('.route-fixed-box').hide();

                $('#order-price').removeAttr('readonly');
                $('input[name=departure_place]').removeAttr('readonly');
                $('input[name=destination_place]').removeAttr('readonly');
                $('input[name=stopover_place]').removeAttr('readonly');
                $('input[name=travel_distance]').removeAttr('readonly');
                $('input[name=time_limitation_prescribed]').removeAttr('readonly');
            }
        });


        $('input[name=assign_date]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });

        $('input[name=should_departure]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('input[name=should_arrival]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });




        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/order-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);

                        if($.getUrlParam('referrer')) location.href = decodeURIComponent($.getUrlParam('referrer'));
                        else if(document.referrer) location.href = document.referrer;
                        else location.href = "{{ url('/item/order-list-for-all') }}";
                        // history.go(-1);
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });




        //
        $('#select2-client').select2({
            ajax: {
                url: "{{ url('/item/order_select2_client') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-circle').select2({
            ajax: {
                url: "{{ url('/item/order_select2_circle') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-route').select2({
            ajax: {
                url: "{{ url('/item/order_select2_route') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

//                    var $o = [];
//                    var $lt = data;
//                    $.each($lt, function(i,item) {
//                        item.id = item.id;
//                        item.text = item.text;
//                        item.data_id = item.text;
//                        $o.push(item);
//                    });
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            templateSelection: function(data, container) {
                $(data.element).attr("data-price",data.amount_with_cash);
                $(data.element).attr("data-departure",data.departure_place);
                $(data.element).attr("data-destination",data.destination_place);
                $(data.element).attr("data-stopover",data.stopover_place);
                $(data.element).attr("data-distance",data.travel_distance);
                $(data.element).attr("data-prescribed",data.time_limitation_prescribed);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $("#select2-route").on("select2:select",function(){
            var $id = $(this).val();
            var $price = $(this).find('option:selected').attr('data-price');
            if($id > 0)
            {
                $('#order-price').attr('readonly','readonly').val($price);
                $('input[name=departure_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-departure'));
                $('input[name=destination_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-destination'));
                $('input[name=stopover_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-stopover'));
                $('input[name=travel_distance]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-distance'));
                $('input[name=time_limitation_prescribed]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-prescribed'));
            }
            else
            {
                $('#order-price').removeAttr('readonly');
                $('input[name=departure_place]').removeAttr('readonly');
                $('input[name=destination_place]').removeAttr('readonly');
                $('input[name=stopover_place]').removeAttr('readonly');
                $('input[name=travel_distance]').removeAttr('readonly');
                $('input[name=time_limitation_prescribed]').removeAttr('readonly');
            }
        });


        //
        $('#select2-pricing').select2({
            ajax: {
                url: "{{ url('/item/order_select2_pricing') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            templateSelection: function(data, container) {
                if(data.driver_er)
                {
                    $(data.element).attr("data-id",data.driver_id);
                    $(data.element).attr("data-name",data.driver_er.driver_name);
                    $(data.element).attr("data-phone",data.driver_er.driver_phone);
                    $(data.element).attr("data-sub-name",data.driver_er.sub_driver_name);
                    $(data.element).attr("data-sub-phone",data.driver_er.sub_driver_phone);
                }
                else
                {
                    $(data.element).attr("data-id",data.driver_id);
                    $(data.element).attr("data-name",data.linkman_name);
                    $(data.element).attr("data-phone",data.linkman_phone);
                    $(data.element).attr("data-sub-name",'');
                    $(data.element).attr("data-sub-phone",'');
                }
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $("#select2-car").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                $('input[name=driver_name]').val($(this).find('option:selected').attr('data-name'));
                $('input[name=driver_phone]').val($(this).find('option:selected').attr('data-phone'));
                $('input[name=copilot_name]').val($(this).find('option:selected').attr('data-sub-name'));
                $('input[name=copilot_phone]').val($(this).find('option:selected').attr('data-sub-phone'));
            }

            var $driver_id = $(this).find('option:selected').attr('data-id');
            var $driver_name = $(this).find('option:selected').attr('data-name');
            var option = new Option($driver_name, $driver_id);
            option.selected = true;
            console.log(option);
            $("#form-edit-item").find("select[name=driver_id]").append(option);
            $("#form-edit-item").find("select[name=driver_id]").trigger("change");
        });
        //
        $('#select2-trailer').select2({
            ajax: {
                url: "{{ url('/item/order_select2_trailer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            templateSelection: function(data, container) {
                $(data.element).attr("data-name",data.driver_name);
                $(data.element).attr("data-phone",data.driver_phone);
                $(data.element).attr("data-sub-name",data.sub_driver_name);
                $(data.element).attr("data-sub-phone",data.sub_driver_phone);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $("#select2-driver").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                $('input[name=driver_name]').val($(this).find('option:selected').attr('data-name'));
                $('input[name=driver_phone]').val($(this).find('option:selected').attr('data-phone'));
                $('input[name=copilot_name]').val($(this).find('option:selected').attr('data-sub-name'));
                $('input[name=copilot_phone]').val($(this).find('option:selected').attr('data-sub-phone'));
            }
        });

    });
</script>
@endsection
