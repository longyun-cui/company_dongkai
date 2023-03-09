@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '数据导出' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description','数据导出 - 管理员后台系统 - 兆益信息')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--订单--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">订单导出</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="export-for-order">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">


                        <select class="form-control form-filter" name="order-staff" style="width:88px;">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-client" style="width:88px;">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter select2-container select2-circle" name="order-circle" style="width:100px;">
                            <option value="-1">选择环线</option>
                        </select>

                        <select class="form-control form-filter" name="order-route" style="width:88px;">
                            <option value="-1">选择线路</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-pricing" style="width:88px;">
                            <option value="-1">选择定价</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter select2-container select2-car" name="order-car" style="width:100px;">
                            <option value="-1">选择车辆</option>
                        </select>

                        <select class="form-control form-filter select2-container select2-trailer" name="order-trailer" style="width:100px;">
                            <option value="-1">选择车挂</option>
                        </select>

                        <select class="form-control form-filter select2-container select2-driver" name="order-driver" style="width:100px;">
                            <option value="-1">选择驾驶员</option>
                        </select>

                        <select class="form-control form-filter" name="order-type" style="width:88px;">
                            <option value ="-1">订单类型</option>
                            <option value ="1">自有</option>
                            <option value ="11">空单</option>
                            <option value ="41">外配·配货</option>
                            <option value ="61">外请·调车</option>
                        </select>

                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-start" placeholder="起始日期" readonly="readonly" value="" data-default="" style="width:88px;" />
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-ended" placeholder="终止日期" readonly="readonly" value="" data-default="" style="width:88px;" />


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="">
                            <i class="fa fa-download"></i> 导出
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>


                        <div class="month-picker-box clear-both">
                            <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <input type="text" class="form-control form-filter filter-keyup month-picker month_picker" name="order-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                            <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="month">
                                <i class="fa fa-download"></i> 按月导出
                            </button>
                        </div>


                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                </div>
            </div>

        </div>
    </div>
</div>


{{--财务--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">财务导出</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="export-for-finance">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">


                        <input type="text" class="form-control form-filter" name="finance-order_id" placeholder="订单ID" style="width:88px;" />
                        <input type="text" class="form-control form-filter" name="finance-title" placeholder="名目" style="width:88px;" />
                        <input type="text" class="form-control form-filter" name="finance-transaction_type" placeholder="支付方式" style="width:88px;" />
                        <input type="text" class="form-control form-filter" name="finance-transaction_receipt_account" placeholder="收款账户" style="width:88px;" />
                        <input type="text" class="form-control form-filter" name="finance-transaction_payment_account" placeholder="支出账户" style="width:88px;" />
                        <input type="text" class="form-control form-filter" name="finance-transaction_order" placeholder="交易单号" style="width:88px;" />

                        <select class="form-control form-filter" name="finance-type">
                            <option value ="-1">全部收支</option>
                            <option value ="1">收入</option>
                            <option value ="21">支出</option>
                        </select>

                        <input type="text" class="form-control form-filter date_picker" name="finance-start" placeholder="起始日期" readonly="readonly" value="" data-default="" style="width:80px;" />
                        <input type="text" class="form-control form-filter date_picker" name="finance-ended" placeholder="终止日期" readonly="readonly" value="" data-default="" style="width:80px;" />


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-finance" data-type="">
                            <i class="fa fa-download"></i> 导出
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-empty" id="filter-empty-for-finance">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>


                        <div class="month-picker-box clear-both">
                            <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <input type="text" class="form-control form-filter filter-keyup month-picker month_picker" name="finance-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                            <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-finance" data-type="month">
                                <i class="fa fa-download"></i> 按月导出
                            </button>
                        </div>


                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                </div>
            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-css')
@endsection
@section('custom-style')
<style>
    .myChart { width:100%;height:240px; }
    .datatable-search-row .input-group .month-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker { width:90px;text-align:center; }
    .datatable-search-row .input-group select { width:100px; }
    .datatable-search-row .input-group .select2-container { width:120px; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
@include(env('TEMPLATE_YH_ADMIN').'entrance.statistic.statistic-export-script')
<script>
    $(function(){

        // 初始化
        // $("#filter-submit-for-comprehensive").click();

    });
</script>
@endsection


