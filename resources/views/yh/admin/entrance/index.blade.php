@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))L.@endif
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--车辆统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">
            <h4>车辆统计</h4>
            <div class="callout-body">
                <span>总计 <text class="text-purple- font-24px">{{ $car_all_count or 0 }}</text> 辆</span>
                <span>车辆 <text class="text-green font-24px">{{ $car_car_count or 0 }}</text> 辆</span>
                <span>车挂 <text class="text-purple font-24px">{{ $car_trailer_count or 0 }}</text> 辆</span>
                <span>工作中 <text class="text-green font-24px">{{ $car_working_count or '' }}</text> 辆</span>
                <span>待发车 <text class="text-blue font-24px">{{ $car_waiting_for_departure_count or 0 }}</text> 辆</span>
                <span>空闲 <text class="text-red font-24px">{{ $car_idle_count or '' }}</text> 辆</span>
            </div>
        </div>
    </div>
</div>


{{--订单统计--}}
<div class="row">
    <div class="col-md-12">
        <div class="callout callout-success- bg-white">
            <h4>订单统计</h4>
            <div class="callout-body">
                <span>总计 <text class="text-black font-24px">{{ $order_all_count or 0 }}</text> 单</span>
                <span>待发布 <text class="text-teal font-24px">{{ $order_unpublished_count or 0 }}</text> 单</span>
                <span>待发车 <text class="text-aqua font-24px">{{ $order_waiting_for_departure_count or 0 }}</text> 单</span>
                <span>进行中 <text class="text-blue font-24px">{{ $order_working_count or 0 }}</text> 单</span>
                <span>待收款 <text class="text-orange font-24px">{{ $order_waiting_for_receipt_count or '' }}</text> 单</span>
                <span>已收款 <text class="text-blue font-24px">{{ $order_received_count or '' }}</text> 单</span>
            </div>
        </div>
    </div>
</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
</style>
@endsection




@section('custom-script')
<script>
    $(function() {
    });
</script>
@endsection