{{--客户--}}
<div class="form-group item-detail-client">
    <label class="control-label col-md-2">客户</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->client_er->username or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="client"></div>
</div>
{{--金额--}}
<div class="form-group item-detail-amount">
    <label class="control-label col-md-2">金额</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->amount or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="amount" data-name="金额" data-value="{{ $data->amount or '' }}">
        @if(empty($data->amount)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--车辆所属--}}
<div class="form-group item-detail-car_owner_type">
    <label class="control-label col-md-2">车辆所属</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->car_owner_type_name or '' }}</span>
    </div>
    {{--<div class="col-md-2 item-detail-operate" data-key="car_owner_type" data-name="车辆所属" data-value="">--}}
        {{--@if(empty($data->car_owner_type)) <a href="javascript:void(0);" data-type="add">添加</a>--}}
        {{--@else <a href="javascript:void(0);" data-type="edit">修改</a>--}}
        {{--@endif--}}
    {{--</div>--}}
</div>
{{--车牌--}}
<div class="form-group item-detail-car">
    <label class="control-label col-md-2">车牌</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->car or '' }}</span>
    </div>
    {{--<div class="col-md-2 item-detail-operate" data-key="car" data-name="车牌" data-value="">--}}
        {{--@if(empty($data->car)) <a href="javascript:void(0);" data-type="add">添加</a>--}}
        {{--@else <a href="javascript:void(0);" data-type="edit">修改</a>--}}
        {{--@endif--}}
    {{--</div>--}}
</div>
{{--车挂--}}
<div class="form-group item-detail-trailer">
    <label class="control-label col-md-2">车挂</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->trailer or '' }}</span>
    </div>
    {{--<div class="col-md-2 item-detail-operate" data-key="trailer" data-name="车挂" data-value="">--}}
        {{--@if(empty($data->trailer)) <a href="javascript:void(0);" data-type="add">添加</a>--}}
        {{--@else <a href="javascript:void(0);" data-type="edit">修改</a>--}}
        {{--@endif--}}
    {{--</div>--}}
</div>
{{--箱型--}}
<div class="form-group item-detail-container_type">
    <label class="control-label col-md-2">箱型</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->container_type or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="container_type" data-name="箱型" data-value="{{ $data->container_type or '' }}">
        @if(empty($data->container_type)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--所属公司--}}
<div class="form-group item-detail-subordinate_company">
    <label class="control-label col-md-2">所属公司</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->subordinate_company or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="subordinate_company" data-name="所属公司" data-value="{{ $data->subordinate_company or '' }}">
        @if(empty($data->subordinate_company)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--回单状态--}}
<div class="form-group item-detail-receipt_status">
    <label class="control-label col-md-2">回单状态</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->receipt_status or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="receipt_status" data-name="回单状态" data-value="{{ $data->receipt_status or '' }}">
        @if(empty($data->receipt_status)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--回单地址--}}
<div class="form-group item-detail-receipt_address">
    <label class="control-label col-md-2">回单地址</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->receipt_address or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="receipt_address" data-name="回单地址" data-value="{{ $data->receipt_address or '' }}">
        @if(empty($data->receipt_address)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--GPS--}}
<div class="form-group item-detail-GPS">
    <label class="control-label col-md-2">GPS</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->GPS or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="GPS" data-name="GPS" data-value="{{ $data->GPS or '' }}">
        @if(empty($data->GPS)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--固定线路--}}
<div class="form-group item-detail-fixed_route">
    <label class="control-label col-md-2">固定线路</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->fixed_route or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="fixed_route" data-name="固定线路" data-value="{{ $data->fixed_route or '' }}">
        @if(empty($data->fixed_route)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--临时线路--}}
<div class="form-group item-detail-temporary_route">
    <label class="control-label col-md-2">临时路线</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->temporary_route or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="temporary_route" data-name="临时路线" data-value="{{ $data->temporary_route or '' }}">
        @if(empty($data->temporary_route)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--单号--}}
<div class="form-group item-detail-order_number">
    <label class="control-label col-md-2">单号</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->order_number or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="order_number" data-name="单号" data-value="{{ $data->order_number or '' }}">
        @if(empty($data->order_number)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--收款人--}}
<div class="form-group item-detail-payee_name">
    <label class="control-label col-md-2">收款人</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->payee_name or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="payee_name" data-name="收款人" data-value="{{ $data->payee_name or '' }}">
        @if(empty($data->payee_name)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--安排人--}}
<div class="form-group item-detail-arrange_people">
    <label class="control-label col-md-2">安排人</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->arrange_people or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="arrange_people" data-name="安排人" data-value="{{ $data->arrange_people or '' }}">
        @if(empty($data->arrange_people)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--车货源--}}
<div class="form-group item-detail-car_supply">
    <label class="control-label col-md-2">车货源</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->car_supply or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="car_supply" data-name="车货源" data-value="{{ $data->car_supply or '' }}">
        @if(empty($data->car_supply)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--车辆管理人--}}
<div class="form-group item-detail-car_managerial_people">
    <label class="control-label col-md-2">车辆管理人</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->car_managerial_people or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="car_managerial_people" data-name="车辆管理人" data-value="{{ $data->car_managerial_people or '' }}">
        @if(empty($data->car_managerial_people)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--主驾--}}
<div class="form-group item-detail-driver">
    <label class="control-label col-md-2">主驾</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->driver or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="driver" data-name="主驾" data-value="{{ $data->driver or '' }}">
        @if(empty($data->driver)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--副驾--}}
<div class="form-group item-detail-copilot">
    <label class="control-label col-md-2">副驾</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->copilot or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="copilot" data-name="副驾" data-value="{{ $data->copilot or '' }}">
        @if(empty($data->copilot)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>
{{--重量--}}
<div class="form-group item-detail-weight">
    <label class="control-label col-md-2">重量</label>
    <div class="col-md-8 ">
        <span class="item-detail-text">{{ $data->weight or '' }}</span>
    </div>
    <div class="col-md-2 item-detail-operate" data-key="weight" data-name="重量" data-value="{{ $data->weight or '' }}">
        @if(empty($data->weight)) <a href="javascript:void(0);" data-type="add">添加</a>
        @else <a href="javascript:void(0);" data-type="edit">修改</a>
        @endif
    </div>
</div>