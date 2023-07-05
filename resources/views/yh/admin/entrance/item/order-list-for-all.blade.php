@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '订单列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')订单列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">订单列表</h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
{{--                    <a class="item-create-link">--}}
{{--                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加订单</button>--}}
{{--                    </a>--}}
                    <a class="item-create-show">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加订单</button>
                    </a>
                </div>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-order-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" value="{{ $order_id or '' }}" style="width:88px;" />

                        <select class="form-control form-filter" name="order-staff" style="width:88px;">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}" @if($v->id == $staff_id) selected="selected" @endif>{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-client" style="width:88px;">
                            <option value ="-1">选择客户</option>
                            <option value="0">[未指定]</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}" @if($v->id == $client_id) selected="selected" @endif>{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter select2-container order-select2-circle" name="order-circle" style="width:100px;">
                            @if($circle_id > 0)
                                <option value="-1">选择环线</option>
                                <option value="{{ $circle_id }}" selected="selected">{{ $circle_name }}</option>
                            @else
                                <option value="-1">选择环线</option>
                            @endif
                        </select>

                        <select class="form-control form-filter" name="order-route" style="width:88px;">
                            <option value="-1">选择线路</option>
                            <option value="-9">[临时线路]</option>
                            <option value="0">[未指定]</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}" @if($v->id == $route_id) selected="selected" @endif>{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-pricing" style="width:88px;">
                            <option value="-1">选择定价</option>
                            <option value="0">[未指定]</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}" @if($v->id == $pricing_id) selected="selected" @endif>{{ $v->title }}</option>
                            @endforeach
                        </select>

                        {{--<select class="form-control form-filter" name="order-car" style="width:88px;">--}}
                            {{--<option value ="-1">选择车辆</option>--}}
                            {{--@foreach($car_list as $v)--}}
                                {{--<option value ="{{ $v->id }}">{{ $v->name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
{{--                        <select class="form-control form-filter order-list-select2-car" name="order-car" style="width:120px;">--}}
{{--                            @if($car_id > 0)--}}
{{--                                <option value="-1">选择车辆</option>--}}
{{--                                <option value="{{ $car_id }}" selected="selected">{{ $car_name }}</option>--}}
{{--                            @else--}}
{{--                                <option value="-1">选择车辆</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}


                        <select class="form-control form-filter select2-container order-select2-car" name="order-car" style="width:100px;">
                            @if($car_id > 0)
                                <option value="-1">选择车辆</option>
                                <option value="{{ $car_id }}" selected="selected">{{ $car_name }}</option>
                            @else
                                <option value="-1">选择车辆</option>
                            @endif
                        </select>
                        <select class="form-control form-filter select2-container order-select2-trailer" name="order-trailer" style="width:100px;">
                            @if($trailer_id > 0)
                                <option value="-1">选择车挂</option>
                                <option value="{{ $trailer_id }}" selected="selected">{{ $trailer_name }}</option>
                            @else
                                <option value="-1">选择车挂</option>
                            @endif
                        </select>


                        <select class="form-control form-filter select2-container order-select2-driver" name="order-driver" style="width:100px;">
                            @if($driver_id > 0)
                                <option value="-1">选择驾驶员</option>
                                <option value="{{ $driver_id }}" selected="selected">{{ $driver_name }}</option>
                            @else
                                <option value="-1">选择驾驶员</option>
                            @endif
                        </select>

                        <select class="form-control form-filter" name="order-type" style="width:88px;">
                            <option value ="-1">订单类型</option>
                            <option value ="1" @if($order_type == "1") selected="selected" @endif>自有</option>
                            <option value ="11" @if($order_type == "11") selected="selected" @endif>空单</option>
                            <option value ="41" @if($order_type == "41") selected="selected" @endif>外配·配货</option>
                            <option value ="61" @if($order_type == "61") selected="selected" @endif>外请·调车</option>
                        </select>

                        <select class="form-control form-filter" name="order-status" style="width:88px;">
                            <option value ="-1">订单状态</option>
                            <option value ="未发布">未发布</option>
                            <option value ="待发车">待发车</option>
                            <option value ="进行中">进行中</option>
                            <option value ="已到达">已到达</option>
                            <option value ="待收款">待收款</option>
                            <option value ="已收款">已收款</option>
                            <option value ="已结束">已结束</option>
                            <option value ="弃用">弃用</option>
                        </select>

                        <select class="form-control form-filter" name="order-is-delay" style="width:88px;">
                            <option value="-1">是否压车</option>
                            <option value="1" @if($is_delay == "1") selected="selected" @endif>正常</option>
                            <option value="9" @if($is_delay == "9") selected="selected" @endif>压车</option>
                        </select>

                        <select class="form-control form-filter" name="order-receipt-status" style="width:88px;">
                            <option value="-1">回单状态</option>
                            <option value="199">需要回单</option>
                            <option value="1">等待回单</option>
                            <option value="21">邮寄中</option>
                            <option value="41">已签收，等待确认</option>
                            <option value="100">已完成</option>
                            <option value="101">回单异常</option>
                        </select>

                        <input type="text" class="form-control form-filter filter-keyup" name="order-remark" placeholder="备注" value="" style="width:88px;" />


                        <div class="pull-left clear-both">
                            <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="派车时间" value="{{ $assign or '' }}" readonly="readonly" style="width:88px;" />
                            <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                                <i class="fa fa-chevron-right"></i>
                            </button>

                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-start" placeholder="起始日期" value="{{ $start or '' }}" readonly="readonly" style="width:88px;" />
                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-ended" placeholder="终止日期" value="{{ $ended or '' }}" readonly="readonly" style="width:88px;" />

                            <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                                <i class="fa fa-remove"></i> 清空重选
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-order">
                                <i class="fa fa-search"></i> 搜索
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-order">
                                <i class="fa fa-circle-o-notch"></i> 刷新
                            </button>
                            <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-order">
                                <i class="fa fa-undo"></i> 重置
                            </button>

                            <button type="button" class="form-control btn btn-flat bg-red" id="order-show-for-finance">
                                <i class="fa fa-rmb"></i> 财务显示
                            </button>
                            <button type="button" class="form-control btn btn-flat bg-purple" id="order-show-for-brief">
                                <i class="fa fa-ellipsis-h"></i> 简略显示
                            </button>
                            <button type="button" class="form-control btn btn-flat bg-olive" id="order-show-for-full">
                                <i class="fa fa-bars"></i> 完整显示
                            </button>
                        </div>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax'>
                    <thead>
{{--                        <tr>--}}
{{--                            <th colspan="14" rowspan="2">基本信息</th>--}}
{{--                            <th colspan="11" rowspan="2">基本信息</th>--}}
{{--                            <th colspan="24" class="bg-fee">费用信息</th>--}}
{{--                            <th colspan="8" rowspan="2" class="bg-finance">财务信息</th>--}}
{{--                            <th colspan="9" rowspan="2" class="bg-empty">空单</th>--}}
{{--                            <th colspan="7" rowspan="2" class="bg-journey">行程</th>--}}
{{--                            <th colspan="10" rowspan="2">其他信息</th>--}}
{{--                            <th colspan="3" rowspan="2">时间与操作</th>--}}
{{--                        </tr>--}}
                        <tr>
                            <th colspan="13">基本信息</th>
                            <th colspan="8" class="bg-route">线路</th>
                            <th colspan="3" class="bg-">派车</th>
                            <th colspan="6" class="bg-fee">运费</th>
                            <th colspan="3" class="bg-fee-2">扣款</th>
                            <th colspan="6" class="bg-fee">收款</th>
                            <th colspan="15" class="bg-fee-2">支出</th>
                            <th colspan="5" class="bg-fee">外请车</th>
                            <th colspan="6"  class="bg-finance">财务信息</th>
{{--                            <th colspan="9" class="bg-empty">空单</th>--}}
                            <th colspan="12" class="bg-journey">行程</th>
                            <th colspan="10">其他信息</th>
                            <th colspan="2">时间</th>
                            <th rowspan="2">操作</th>
                        </tr>
                        <tr role='row'>
                            {{--基本信息--}}
                            <th>ID</th>
                            <th>订单状态</th>
                            <th>订单类型</th>
                            <th>订单</th>
                            <th>派车日期</th>
                            <th>车辆</th>
                            <th>车挂</th>
                            <th>驾驶员</th>
                            <th>客户</th>
                            <th>创建人</th>
                            <th>审核人</th>
                            <th>是否压车</th>
                            <th>备注</th>

                            {{--路线--}}
{{--                            <th>线路</th>--}}
                            <th>固定线路</th>
                            <th>临时线路</th>
                            <th>里程</th>
                            <th>华东空</th>
                            <th>里程</th>
                            <th>华南空</th>
                            <th>里程</th>
                            <th>环线</th>

                            {{--派车--}}
                            <th>收款人</th>
                            <th>车货源</th>
                            <th>安排人</th>

                            <th>包油油耗</th>

                            {{--运费--}}
                            <th>运价</th>
                            <th>油卡</th>
                            <th>定金</th>
                            <th>信息费</th>
                            <th>信息费报销</th>


                            {{--扣款--}}
                            <th>客管费</th>
                            <th>时效扣款</th>
                            <th>其他异常</th>

                            {{--收款--}}
                            <th>实收</th>
                            <th>日期</th>
                            <th>尾款</th>
                            <th>日期</th>

                            {{--支出--}}
                            <th>主单-ETC</th>
                            <th>主单-现金</th>
                            <th>主单-油费</th>

                            <th>华东-ETC</th>
                            <th>华东-现金</th>
                            <th>华东-油费</th>

                            <th>华南-ETC</th>
                            <th>华南-现金</th>
                            <th>华南-油费</th>

                            <th>船费</th>
                            <th>尿素费</th>
                            <th>维修费</th>
                            <th>工资</th>
                            <th>其他费用</th>
                            <th>支出总计</th>

{{--                            <th>万金油(升)</th>--}}
{{--                            <th>油价(元)</th>--}}
{{--                            <th>包邮费</th>--}}
{{--                            <th>客管费</th>--}}
{{--                            <th>开票额</th>--}}
{{--                            <th>票点</th>--}}
{{--                            <th>其他费用</th>--}}

                            {{--请车--}}
                            <th>请车价</th>
                            <th>到付</th>
                            <th>日期</th>
                            <th>尾款</th>
                            <th>日期</th>

                            {{--收支结果--}}
                            <th>应收款</th>
                            <th>已收款</th>
                            <th>欠款</th>
                            <th>收款结果</th>
                            <th>已支出</th>
{{--                            <th>待入账</th>--}}
{{--                            <th>待出账</th>--}}
                            <th>利润·预估</th>
                            <th>利润·实时</th>
                            <th>利润率</th>

{{--                            <th>空单-固定</th>--}}
{{--                            <th>空单-临时</th>--}}
{{--                            <th>空-里程</th>--}}
{{--                            <th>空-包油价</th>--}}
{{--                            <th>空-包油金额</th>--}}
{{--                            <th>空-加油方式</th>--}}
{{--                            <th>空-加油金额</th>--}}
{{--                            <th>空-过路-现金</th>--}}
{{--                            <th>空-过路-ETC</th>--}}

                            <th>出发地</th>
                            <th>经停地</th>
                            <th>目的地</th>
                            <th>里程</th>
                            <th>时效</th>
                            <th>应出发时间</th>
                            <th>应到达时间</th>
                            <th>实际出发</th>
                            <th>经停-到达</th>
                            <th>经停-出发</th>
                            <th>实际到达</th>
                            <th>行程</th>


                            <th>主驾</th>
                            <th>主驾电话</th>
                            <th>副驾</th>
                            <th>副驾电话</th>
{{--                            <th>状态</th>--}}

                            <th>单号</th>
                            <th>GPS</th>
                            <th>是否回单</th>
                            <th>回单状态</th>
                            <th>回单地址</th>
                            <th>附件</th>
                            <th>备注</th>

                            <th>创建时间</th>
{{--                            <th>修改时间</th>--}}
                        </tr>
                    </thead>

{{--                    <tfoot>--}}
{{--                    </tfoot>--}}

                    <tbody>
                    </tbody>
                </table>
                </div>

            </div>


            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value ="-1">请选择操作类型</option>
                                <option value ="启用">启用</option>
                                <option value ="禁用">禁用</option>
                                <option value ="删除">删除</option>
                                <option value ="彻底删除">彻底删除</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>




{{--显示-基本信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-detail">
    <div class="col-md-8 col-md-offset-2" id="" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">订单【<span class="info-detail-title"></span>】详情</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="work-order" readonly>
                    <input type="hidden" name="id" value="0" readonly>

                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--金额--}}
                    <div class="form-group item-detail-amount">
                        <label class="control-label col-md-2">金额</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text">333</span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--车辆类型--}}
                    <div class="form-group item-detail-car_owner_type">
                        <label class="control-label col-md-2">车辆所属</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="car_owner_type"></div>
                    </div>
                    {{--车牌--}}
                    <div class="form-group item-detail-car">
                        <label class="control-label col-md-2">车牌</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--车挂--}}
                    <div class="form-group item-detail-trailer">
                        <label class="control-label col-md-2">车挂</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--箱型--}}
                    <div class="form-group item-detail-container_type">
                        <label class="control-label col-md-2">箱型</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="container_type"></div>
                    </div>
                    {{--所属公司--}}
                    <div class="form-group item-detail-subordinate_company">
                        <label class="control-label col-md-2">所属公司</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="subordinate_company"></div>
                    </div>
                    {{--回单状态--}}
                    <div class="form-group item-detail-receipt_status">
                        <label class="control-label col-md-2">回单状态</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="receipt_status"></div>
                    </div>
                    {{--回单地址--}}
                    <div class="form-group item-detail-receipt_address">
                        <label class="control-label col-md-2">回单地址</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="receipt_address"></div>
                    </div>
                    {{--GPS--}}
                    <div class="form-group item-detail-GPS">
                        <label class="control-label col-md-2">GPS</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="GPS"></div>
                    </div>
                    {{--固定线路--}}
                    <div class="form-group item-detail-fixed_route">
                        <label class="control-label col-md-2">固定线路</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="fixed_route"></div>
                    </div>
                    {{--临时线路--}}
                    <div class="form-group item-detail-temporary_route">
                        <label class="control-label col-md-2">临时线路</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="temporary_route"></div>
                    </div>
                    {{--单号--}}
                    <div class="form-group item-detail-order_number">
                        <label class="control-label col-md-2">单号</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="order_number"></div>
                    </div>
                    {{--收款人--}}
                    <div class="form-group item-detail-payee_name">
                        <label class="control-label col-md-2">收款人</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="payee_name"></div>
                    </div>
                    {{--安排人--}}
                    <div class="form-group item-detail-arrange_people">
                        <label class="control-label col-md-2">安排人</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="arrange_people"></div>
                    </div>
                    {{--车货源--}}
                    <div class="form-group item-detail-car_supply">
                        <label class="control-label col-md-2">车货源</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="car_supply"></div>
                    </div>
                    {{--车辆管理人--}}
                    <div class="form-group item-detail-car_managerial_people">
                        <label class="control-label col-md-2">车辆管理人</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="car_managerial_people"></div>
                    </div>
                    {{--主驾--}}
                    <div class="form-group item-detail-driver">
                        <label class="control-label col-md-2">主驾</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="driver"></div>
                    </div>
                    {{--副驾--}}
                    <div class="form-group item-detail-copilot">
                        <label class="control-label col-md-2">副驾</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="copilot"></div>
                    </div>
                    {{--重量--}}
                    <div class="form-group item-detail-weight">
                        <label class="control-label col-md-2">重量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="weight"></div>
                    </div>




                    {{--说明--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">说明</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="">这是一段说明。</span>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer _none">
                <div class="row _none">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-site-submit"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel" id="item-site-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--显示-附件-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-attachment">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单【<span class="attachment-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>



            {{--attachment--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="">
            <div class="box-body attachment-box">

            </div>
            </form>


            <div class="box-header with-border margin-top-16px margin-bottom-16px-">
                <h4 class="box-title">【添加附件】</h4>
            </div>

            {{--上传附件--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="modal-attachment-set-form">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="attachment-set-operate" value="item-order-attachment-set" readonly>
                <input type="hidden" name="attachment-set-order-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-order-attachment-set" readonly>
                <input type="hidden" name="order_id" value="0" readonly>
                <input type="hidden" name="operate_type" value="add" readonly>
                <input type="hidden" name="column_key" value="attachment" readonly>


                <div class="form-group">
                    <label class="control-label col-md-2">附件名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="attachment_name" autocomplete="off" placeholder="附件名称" value="">
                    </div>
                </div>

                {{--多图上传--}}
                <div class="form-group">

                    <label class="control-label col-md-2">图片上传</label>

                    <div class="col-md-8">
                        <input id="multiple-images" type="file" class="file-multiple-images" name="multiple_images[]" multiple >
                    </div>

                </div>

                {{--多图上传--}}
                <div class="form-group _none">

                    <label class="control-label col-md-2" style="clear:left;">选择图片</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                            <span class="btn-file">
                                <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                <input type="file" name="attachment_file" />
                            </span>
                                <span class="">
                                <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                            </span>
                            </div>
                        </div>
                        <div id="titleImageError" style="color: #a94442"></div>

                    </div>

                </div>

            </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-attachment-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-attachment-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--修改-基本-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-text-set-operate" value="item-order-info-text-set" readonly>
                    <input type="hidden" name="info-text-set-order-id" value="0" readonly>
                    <input type="hidden" name="info-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-text-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="info-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="info-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-时间-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-time-set-operate" value="item-order-info-time-set" readonly>
                    <input type="hidden" name="info-time-set-order-id" value="0" readonly>
                    <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-time-set-column-key" value="" readonly>
                    <input type="hidden" name="info-time-set-time-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-radio-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-radio-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-radio-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-radio-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-radio-set-operate" value="item-order-info-option-set" readonly>
                    <input type="hidden" name="info-radio-set-order-id" value="0" readonly>
                    <input type="hidden" name="info-radio-set-operate-type" value="edit" readonly>
                    <input type="hidden" name="info-radio-set-column-key" value="" readonly>


                    <div class="form-group radio-box">
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-radio-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-radio-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-select-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-select-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-select-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-select-set-operate" value="item-order-info-option-set" readonly>
                    <input type="hidden" name="info-select-set-order-id" value="0" readonly>
                    <input type="hidden" name="info-select-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-select-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-select-set-column-name"></label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-client" name="info-select-set-column-value" style="width:360px;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-select-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-select-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--option--}}
<div class="option-container _none">

    <div id="trailer_type-option-list">
        <option value="0">选择箱型</option>
        <option value="直板">直板</option>
        <option value="高栏">高栏</option>
        <option value="平板">平板</option>
        <option value="冷藏">冷藏</option>
    </div>

    <div id="trailer_length-option-list">
        <option value="0">选择车挂尺寸</option>
        <option value="9.6">9.6</option>
        <option value="12.5">12.5</option>
        <option value="15">15</option>
        <option value="16.5">16.5</option>
        <option value="17.5">17.5</option>
    </div>

    <div id="trailer_volume-option-list">
        <option value="0">选择承载方数</option>
        <option value="125">125</option>
        <option value="130">130</option>
        <option value="135">135</option>
    </div>

    <div id="trailer_weight-option-list">
        <option value="0">选择承载重量</option>
        <option value="13">13吨</option>
        <option value="20">20吨</option>
        <option value="25">25吨</option>
    </div>

    <div id="trailer_axis_count-option-list">
        <option value="0">选择轴数</option>
        <option value="1">1轴</option>
        <option value="2">2轴</option>
        <option value="3">3轴</option>
    </div>


    {{--订单类型--}}
    <div id="car_owner_type-option-list">
        <option value="0">选择订单类型</option>
        <option value="1">自有</option>
        <option value="11">空单</option>
        <option value="41">外配·配货</option>
        <option value="61">外请·调车</option>
    </div>


    {{--是否压车--}}
    <div id="is_delay-option-list">
        <label class="control-label col-md-2">是否需要回单</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="is_delay" value="1" class="info-set-column"> 正常
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="is_delay" value="9" class="info-set-column"> 压车
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>


    {{--是否需要回单--}}
    <div id="receipt_need-option-list">
        <label class="control-label col-md-2">是否需要回单</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="0" class="info-set-column"> 不需要
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="1" class="info-set-column"> 需要
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>
    {{--回单状态--}}
    <div id="receipt_status-option-list">
        <option value="-1">选择回单状态</option>
        <option value="1">等待回单</option>
        <option value="21">邮寄中</option>
        <option value="41">已签收，等待确认</option>
        <option value="100">已完成</option>
        <option value="101">回单异常</option>
    </div>

</div>




{{--行程记录--}}
<div class="modal fade modal-main-body" id="modal-body-for-travel-detail">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">行程记录</h3>
                <div class="box-tools- pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="modal-form-for-travel-detail">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order-travel-set" readonly>
                    <input type="hidden" name="order_id" value="0" readonly>

                    {{--应出发时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">应出发时间</label>
                        <div class="col-md-8 ">
                            <div><span class="item-travel-should-departure-time"></span></div>
                        </div>
                    </div>
                    {{--应出发时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">应到达时间</label>
                        <div class="col-md-8 ">
                            <div class="item-travel-should-arrival-time"></div>
                        </div>
                    </div>
                    {{--实际出发时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">实际出发时间</label>
                        <div class="col-md-8 ">
                            <div class="item-travel-actual-departure-time"></div>
                        </div>
                    </div>
                    {{--经停到达时间--}}
                    <div class="form-group item-travel-stopover-container">
                        <label class="control-label col-md-2">经停到达时间</label>
                        <div class="col-md-8 ">
                            <div class="item-travel-stopover-arrival-time"></div>
                        </div>
                    </div>
                    {{--经停出发时间--}}
                    <div class="form-group item-travel-stopover-container">
                        <label class="control-label col-md-2">经停出发时间</label>
                        <div class="col-md-8 ">
                            <div class="item-travel-stopover-departure-time"></div>
                        </div>
                    </div>
                    {{--实际到达时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">实际到达时间</label>
                        <div class="col-md-8 ">
                            <div class="item-travel-actual-arrival-time"></div>
                        </div>
                    </div>
                    {{--说明--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">说明</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="">这是一段说明。</span>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row _none">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-site-submit"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel" id="item-site-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{--设置行程时间--}}
<div class="modal fade modal-main-body" id="modal-body-for-travel-set">
    <div class="col-md-4 col-md-offset-4 margin-top-64px margin-bottom-64px bg-white">

            <div class="box- box-info- form-container">

                <div class="box-header with-border margin-top-16px margin-bottom-16px">
                    <h3 class="box-title">设置行程时间</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                <form action="" method="post" class="form-horizontal form-bordered " id="modal-form-for-travel-set">
                    <div class="box-body">

                        {{ csrf_field() }}
                        <input type="hidden" name="travel-set-operate" value="item-order-travel-set" readonly>
                        <input type="hidden" name="travel-set-order-id" value="0" readonly>
                        <input type="hidden" name="travel-set-object-type" value="0" readonly>



                        {{--订单ID--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">订单ID</label>
                            <div class="col-md-8 control-label" style="text-align:left;">
                                <span class="travel-set-order-id"></span>
                            </div>
                        </div>
                        {{--设置对象--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">设置对象</label>
                            <div class="col-md-8 control-label" style="text-align:left;">
                                <span class="travel-set-object-title"></span>
                            </div>
                        </div>
                        {{--选择时间--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">选择时间</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control form-filter form_datetime" name="travel-set-time" />
                            </div>
                        </div>
                        {{--备注--}}
                        <div class="form-group _none">
                            <label class="control-label col-md-2">备注</label>
                            <div class="col-md-8 ">
                                {{--<input type="text" class="form-control" name="description" placeholder="描述" value="">--}}
                                <textarea class="form-control" name="travel-set-description" rows="3" cols="100%"></textarea>
                            </div>
                        </div>


                    </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="button" class="btn btn-success" id="item-submit-for-travel-set"><i class="fa fa-check"></i> 提交</button>
                            <button type="button" class="btn btn-default" id="item-cancel-for-travel-set">取消</button>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>




{{--财务列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-list">
    <div class="col-md-10 col-md-offset-1 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">财务记录</h3>

                <div class="box-tools- pull-right">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right modal-show-for-finance-create">
                            <i class="fa fa-plus"></i> 添加记录
                        </button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-finance-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="finance-title" placeholder="费用类型" />

                        <select class="form-control form-filter" name="finance-finance_type" style="width:96px;">
                            <option value ="-1">选择</option>
                            <option value ="1">收入</option>
                            <option value ="21">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-finance">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered' id='datatable_ajax_finance'>
                    <thead>
                        <tr role='row' class='heading'>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
                </div>

            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{--添加财务记录--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-create">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加财务记录</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-form-for-finance-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="finance-create-operate" value="finance-create-record" readonly>
                    <input type="hidden" name="finance-create-order-id" value="0" readonly>



                    {{--订单ID--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">订单ID</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="finance-create-order-id"></span>
                        </div>
                    </div>
                    {{--关键词--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">关键词</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="finance-create-order-title"></span>
                        </div>
                    </div>
                    {{--交易类型--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">交易类型</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                        <input type="radio" name="finance-create-type" value=1 checked="checked"> 收入
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                        <input type="radio" name="finance-create-type" value=21> 支出
                                </label>
                            </button>
                        </div>
                    </div>
                    {{--交易日期--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">交易日期</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter form_date" name="finance-create-transaction-date" readonly="readonly" />
                        </div>
                    </div>
                    {{--费用名目--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">费用名目</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-title" placeholder="费用名目" value="" list="_transaction_title">
                        </div>
                    </div>
                    <datalist id="_transaction_title">
                        <option value="运费" />
                        <option value="油卡" />
                        <option value="信息费" />
                        <option value="ETC" />
                        <option value="ETC主单" />
                        <option value="ETC空单" />
                        <option value="油费" />
                        <option value="过路费" />
                        <option value="修车费" />
                        <option value="尿素费" />
                    </datalist>
                    {{--金额--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">金额</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-amount" placeholder="金额" value="">
                        </div>
                    </div>
                    {{--支付方式--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">支付方式</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-type" placeholder="支付方式" value="" list="_transaction_type">
                        </div>
                    </div>
                    <datalist id="_transaction_type">
                        <option value="微信" />
                        <option value="支付宝" />
                        <option value="银行卡" />
                        <option value="现金" />
                        <option value="ETC帐户挂" />
                        <option value="ETC挂" />
                        <option value="万金油" />
                        <option value="APP" />
                    </datalist>
                    {{--收款账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">收款账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keyword" name="finance-create-transaction-receipt-account" placeholder="收款账号" value="" list="_transaction_receipt_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_receipt_account">
                        <option value="崔微信：13325937059" class="" />
                        <option value="邓微信：18157357678" class="" />
                        <option value="范微信：13352338286" class="" />
                        <option value="施微信：13375736765" class="" />
                        <option value="工行7001-金13472749605" class="" />
                        <option value="农商3641-金13472749605" class="" />
                        <option value="中行5872-金13472749605" class="" />
                        <option value="工行9753-朱13356085499" class="" />
                        <option value="工行3612-邓18157357678" class="" />
                    </datalist>
                    {{--支出账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">支出账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keywords" name="finance-create-transaction-payment-account" placeholder="支出账号" value="" list="_transaction_payment_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_payment_account">
                        <option value="" class="etc_account" />
                        <option value="崔微信：13325937059" class="" />
                        <option value="邓微信：18157357678" class="" />
                        <option value="范微信：13352338286" class="" />
                        <option value="施微信：13375736765" class="" />
                        <option value="工行7001-金13472749605" class="" />
                        <option value="农商3641-金13472749605" class="" />
                        <option value="中行5872-金13472749605" class="" />
                        <option value="工行9753-朱13356085499" class="" />
                        <option value="工行3612-邓18157357678" class="" />
                    </datalist>
                    {{--交易账号--}}
                    {{--<div class="form-group income-show-">--}}
                        {{--<label class="control-label col-md-2">交易账号</label>--}}
                        {{--<div class="col-md-8 ">--}}
                            {{--<input type="text" class="form-control" name="finance-create-transaction-account" placeholder="交易账号" value="">--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--交易单号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">交易单号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-order" placeholder="交易单号" value="">
                        </div>
                    </div>
                    {{--备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">备注</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="">--}}
                            <textarea class="form-control" name="finance-create-transaction-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-finance-create"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-finance-create">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--修改-财务-文本-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改财务记录【<span class="finance-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-finance-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="finance-text-set-operate" value="item-finance-info-text-set" readonly>
                    <input type="hidden" name="finance-text-set-finance-id" value="0" readonly>
                    <input type="hidden" name="finance-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="finance-text-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 finance-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="finance-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-finance-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-finance-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-财务-时间-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改财务记录【<span class="finance-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-finance-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="finance-time-set-operate" value="item-finance-info-time-set" readonly>
                    <input type="hidden" name="finance-time-set-finance-id" value="0" readonly>
                    <input type="hidden" name="finance-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="finance-time-set-column-key" value="" readonly>
                    <input type="hidden" name="finance-time-set-time-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 finance-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="finance-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter date_picker" name="finance-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-finance-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-finance-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--添加订单--}}
<div class="modal fade modal-main-body" id="modal-body-for-order-create">
    <div class="col-md-9 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加订单</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            @include(env('TEMPLATE_YH_ADMIN').'component.order-create')

        </div>
    </div>
</div>




{{--修改列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-modify-list">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-modify-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value ="-1">选择属性</option>
                            <option value ="amount">金额</option>
                            <option value ="11">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable_ajax_record'>
                    <thead>
                        <tr role='row' class='heading'>
                            <th></th>
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

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
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
    .tableArea table { min-width:7000px; }
    .tableArea table#datatable_ajax_finance { min-width:1600px; }
    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-income { background:#8FEBE5; }
    .bg-route { background:#FFEBE5; }
    .bg-finance { background:#E2FCAB; }
    .bg-empty { background:#F6C5FC; }
    .bg-journey { background:#F5F9B4; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[15, 50, 200, 500, -1], ["15", "50", "200", "500", "全部"]],
                "aLengthMenu": [[ @if(!in_array($length,[15, 50, 100, 200])) {{ $length.',' }} @endif 15, 50, 100, 200], [ @if(!in_array($length,[15, 50, 100, 200])) {{ $length.',' }} @endif "15", "50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 15 }},
                "ajax": {
                    'url': "{{ url('/item/order-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="order-id"]').val();
                        d.remark = $('input[name="order-remark"]').val();
                        d.assign = $('input[name="order-assign"]').val();
                        d.assign_start = $('input[name="order-start"]').val();
                        d.assign_ended = $('input[name="order-ended"]').val();
                        d.name = $('input[name="order-name"]').val();
                        d.title = $('input[name="order-title"]').val();
                        d.keyword = $('input[name="order-keyword"]').val();
                        d.staff = $('select[name="order-staff"]').val();
                        d.client = $('select[name="order-client"]').val();
                        d.circle = $('select[name="order-circle"]').val();
                        d.car = $('select[name="order-car"]').val();
                        d.trailer = $('select[name="order-trailer"]').val();
                        d.route = $('select[name="order-route"]').val();
                        d.pricing = $('select[name="order-pricing"]').val();
                        d.driver = $('select[name="order-driver"]').val();
                        d.status = $('select[name="order-status"]').val();
                        d.order_type = $('select[name="order-type"]').val();
                        d.is_delay = $('select[name="order-is-delay"]').val();
                        d.receipt_status = $('select[name="order-receipt-status"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                },
                "showRefresh": true,
                "columnDefs": [
                    {
                        // "targets": [10, 11, 15, 16],
                        "targets": [],
                        "visible": false,
                        "searchable": false
                    }
                ],
                "columns": [
//                    {
//                        "title": "选择",
//                        "width": "32px",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "title": "ID",
                        "className": "",
                        "width": "50px",
                        "data": "id",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "订单状态",
                        "className": "",
                        "width": "60px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value',row.attachment_list);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
//                            return data;

                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.item_status == 97)
                            {
                                return '<small class="btn-xs bg-navy">已弃用</small>';
                            }

                            if(row.is_published == 0)
                            {
                                return '<small class="btn-xs bg-teal">未发布</small>';
                            }
                            else
                            {
                                if(row.is_completed == 1)
                                {
                                    return '<small class="btn-xs bg-olive">已结束</small>';
                                }
                            }

                            var $travel_status_html = '';
                            var $travel_result_html = '';
                            var $travel_result_time = '';
//
                            if(row.travel_status == "待发车")
                            {
                                $travel_status_html = '<small class="btn-xs bg-yellow">待发车</small>';
                            }
                            else if(row.travel_status == "进行中")
                            {
                                $travel_status_html = '<small class="btn-xs bg-blue">进行中</small>';
                            }
                            else if(row.travel_status == "已到达")
                            {
                                if(row.travel_result == "待收款") $travel_status_html = '<small class="btn-xs bg-orange">待收款</small>';
                                if(row.travel_result == "已收款") $travel_status_html = '<small class="btn-xs bg-maroon">已收款</small>';
                                else $travel_status_html = '<small class="btn-xs bg-olive">已到达</small>';
                            }
                            else if(row.travel_status == "待收款")
                            {
                                $travel_status_html = '<small class="btn-xs bg-maroon">待收款</small>';
                            }
                            else if(row.travel_status == "已收款")
                            {
                                $travel_status_html = '<small class="btn-xs bg-purple">已收款</small>';
                            }
                            else if(row.travel_status == "已完成")
                            {
                                $travel_status_html = '<small class="btn-xs bg-olive">已完成</small>';
                            }
//
//
//                            if(row.travel_result == "正常")
//                            {
//                                $travel_result_html = '<small class="btn-xs bg-olive">正常</small>';
//                            }
//                            else if(row.travel_result == "超时")
//                            {
//                                $travel_result_html = '<small class="btn-xs bg-red">超时</small><br>';
//                                $travel_result_time = '<small class="btn-xs bg-gray">'+row.travel_result_time+'</small>';
//                            }
//                            else if(row.travel_result == "已超时")
//                            {
//                                $travel_result_html = '<small class="btn-xs btn-danger">已超时</small>';
//                            }
//
                            return $travel_status_html + $travel_result_html + $travel_result_time;

                        }
                    },
                    {
                        "title": "订单类型",
                        "className": "",
                        "width": "72px",
                        "data": "car_owner_type",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','订单类型');
                                $(nTd).attr('data-key','car_owner_type').attr('data-value',data);
                                $(nTd).attr('data-column-name','订单类型');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 1)
                            {
                                return '<small class="btn-xs bg-green">自有</small>';
                            }
                            else if(data == 11)
                            {
                                return '<small class="btn-xs bg-teal">空单</small>';
                            }
                            else if(data == 41)
                            {
                                return '<small class="btn-xs bg-blue">外配·配货</small>';
                            }
                            else if(data == 61)
                            {
                                return '<small class="btn-xs bg-purple">外请·调车</small>';
                            }
                            else return "有误";
                        }
                    },
                    {
                        "title": "订单",
                        "className": "text-center",
                        "width": "180px",
                        "data": 'id',
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = '';
                            if(row.assign_time)
                            {
                                var $date = new Date((row.assign_time)*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                var $currentYear = new Date().getFullYear();
                                if($year == $currentYear) $time = $month+'-'+$day;
                                else $time = $year+'-'+$month+'-'+$day;
                            }
                            else $time = '--';


                            var $client = '';
                            if(row.client_er == null)
                            {
                                if(row.car_owner_type == 11) $client = '--';
                                else $client = '未指定';
                            }
                            else {
                                if(row.client_er.short_name)
                                {
//                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.short_name+'</a>';
                                    $client = '<a href="javascript:void(0);">'+row.client_er.short_name+'</a>';
                                }
                                else
                                {
//                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.username+'</a>';
                                    $client = '<a href="javascript:void(0);">'+row.client_er.username+'</a>';
                                }
                            }


                            var car_html = '';
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.car_er != null) car_html = '<a href="javascript:void(0);">'+row.car_er.name+'</a>';
                            }
                            else
                            {
                                if(row.outside_car) car_html = row.outside_car;
                            }
                            if(row.car_er != null) car_html = '<a href="javascript:void(0);">'+row.car_er.name+'</a>';

                            return $time + ' - ' + $client + ' - ' + car_html;
                        }
                    },
                    {
                        "title": "派车日期",
                        "className": "text-center",
                        "width": "72px",
                        "data": 'assign_time',
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $assign_time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    $assign_time_value = $year+'-'+$month+'-'+$day;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','派车日期');
                                $(nTd).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                                $(nTd).attr('data-column-name','派车日期');
                                $(nTd).attr('data-time-type','date');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day;
                            else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "title": "客户",
                        "className": "",
                        "width": "80px",
                        "data": "client_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','客户');
                                $(nTd).attr('data-key','client_id').attr('data-value',data);
                                if(row.client_er == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    if(row.client_er.short_name) $(nTd).attr('data-option-name',row.client_er.short_name);
                                    else $(nTd).attr('data-option-name',row.client_er.username);
                                }
                                $(nTd).attr('data-column-name','客户');
                                if(row.client_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.client_er == null)
                            {
                                if(row.car_owner_type == 11) return '--';
                                else return '未指定';
                            }
                            else {
                                if(row.client_er.short_name)
                                {
//                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.short_name+'</a>';
                                    return '<a href="javascript:void(0);">'+row.client_er.short_name+'</a>';
                                }
                                else
                                {
//                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.username+'</a>';
                                    return '<a href="javascript:void(0);">'+row.client_er.username+'</a>';
                                }
                            }
                        }
                    },
                    {
                        "title": "车辆",
                        "className": "",
                        "width": "72px",
                        "data": "car_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                                {
                                    $(nTd).addClass('modal-show-for-info-select2-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车辆');
                                    $(nTd).attr('data-key','car_id').attr('data-value',row.car_id);
                                    if(row.car_er == null) $(nTd).attr('data-option-name','未指定');
                                    else $(nTd).attr('data-option-name',row.car_er.name);
                                    $(nTd).attr('data-column-name','车辆');
                                    if(row.car_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                                else if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车辆');
                                    $(nTd).attr('data-key','outside_car').attr('data-value',row.outside_car);
                                    $(nTd).attr('data-column-name','车辆');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var car_html = '';
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.car_er != null) car_html = '<a href="javascript:void(0);">'+row.car_er.name+'</a>';
                            }
                            else
                            {
                                if(row.outside_car) car_html = row.outside_car;
                            }
                            if(row.car_er != null) car_html = '<a href="javascript:void(0);">'+row.car_er.name+'</a>';
                            return car_html;
                        }
                    },
                    {
                        "title": "车挂",
                        "className": "",
                        "width": "72px",
                        "data": "trailer_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                                {
                                    $(nTd).addClass('modal-show-for-info-select2-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂');
                                    $(nTd).attr('data-key','trailer_id').attr('data-value',row.trailer_id);
                                    if(row.trailer_er == null) $(nTd).attr('data-option-name','未指定');
                                    else $(nTd).attr('data-option-name',row.trailer_er.name);
                                    $(nTd).attr('data-column-name','车挂');
                                    if(row.trailer_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                                else if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂');
                                    $(nTd).attr('data-key','outside_trailer').attr('data-value',row.outside_trailer);
                                    $(nTd).attr('data-column-name','车挂');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var trailer_html = '';
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.trailer_er != null) trailer_html = '<a href="javascript:void(0);">'+row.trailer_er.name+'</a>';
                            }
                            else
                            {
                                if(row.outside_trailer) trailer_html = row.outside_trailer;
                            }
                            return trailer_html;
                        }
                    },
                    {
                        "title": "驾驶员",
                        "className": "",
                        "width": "60px",
                        "data": "driver_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                                {
                                    $(nTd).addClass('modal-show-for-info-select2-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','驾驶员');
                                    $(nTd).attr('data-key','driver_id').attr('data-value',data);
                                    if(row.driver_er == null) $(nTd).attr('data-option-name','未指定');
                                    else $(nTd).attr('data-option-name',row.driver_er.driver_name);
                                    $(nTd).attr('data-column-name','驾驶员');
                                    if(row.driver_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                                else if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','主驾姓名');
                                    $(nTd).attr('data-key','driver_name').attr('data-value',row.driver_name);
                                    $(nTd).attr('data-column-name','主驾姓名');
                                    $(nTd).attr('data-text-type','text');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {

                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.driver_er == null) return '--';
                                else return '<a href="javascript:void(0);">'+row.driver_er.driver_name+'</a>';
                            }
                            else if(row.car_owner_type == 61)
                            {
                                return row.driver_name;
                            }
                            else return data;
                        }
                    },
                    {
                        "title": "创建人",
                        "className": "",
                        "width": "60px",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "title": "审核人",
                        "className": "",
                        "width": "60px",
                        "data": "verifier_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.verifier == null ? '--' : '<a href="javascript:void(0);">'+row.verifier.true_name+'</a>';
                        }
                    },
                    {
                        "title": "是否压车",
                        "className": "",
                        "width": "60px",
                        "data": "is_delay",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-radio-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','是否压车');
                                $(nTd).attr('data-key','is_delay').attr('data-value',data);
                                $(nTd).attr('data-column-name','是否压车');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs btn-success">正常</small>';
                            else if(data == 9) return '<small class="btn-xs btn-danger">压车</small>';
                            else return '--';
                        }
                    },
                    {
                        "title": "备注",
                        "className": "text-left",
                        "width": "200px",
                        "data": "remark",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','备注');
                                $(nTd).attr('data-key','remark').attr('data-value',data);
                                $(nTd).attr('data-column-name','备注');
                                $(nTd).attr('data-text-type','textarea');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
//                            if(data) return '<small class="btn-xs bg-yellow">查看</small>';
//                            else return '';
                        }
                    },

                    // {
                    //     "title": "线路",
                    //     "className": "bg-route",
                    //     "width": "120px",
                    //     "data": "route_type",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(data == 1)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select2-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','固定线路');
                    //                 $(nTd).attr('data-key','route_id').attr('data-value',row.route_id);
                    //                 if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                    //                 else $(nTd).attr('data-option-name',row.route_er.title);
                    //                 $(nTd).attr('data-column-name','固定线路');
                    //                 if(row.route_id) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //             else if(data == 11)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-text-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                    //                 $(nTd).attr('data-key','route_temporary').attr('data-value',row.route_temporary);
                    //                 if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                    //                 $(nTd).attr('data-column-name','临时线路');
                    //                 if(row.route_id) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data == 1)
                    //         {
                    //             if(row.route_er == null) return '--';
                    //             else return '<a href="javascript:void(0);">'+row.route_er.title+'</a>';
                    //         }
                    //         else if(data == 11)
                    //         {
                    //             if(row.route_temporary) return '[临]' + row.route_temporary;
                    //             else return '[临时]';
                    //         }
                    //         else return '有误';
                    //     }
                    // },
                    {
                        "title": "固定线路",
                        "className": "bg-route",
                        "width": "120px",
                        "data": "route_id",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','固定线路');
                                $(nTd).attr('data-key','route_id').attr('data-value',row.route_id);
                                if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.route_er.title);
                                $(nTd).attr('data-column-name','固定线路');
                                if(row.route_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.route_er == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.route_er.title+'</a>';
                        }
                    },
                    {
                        "title": "临时线路",
                        "className": "bg-route",
                        "width": "120px",
                        "data": "route_temporary",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                                $(nTd).attr('data-key','route_temporary').attr('data-value',data);
                                $(nTd).attr('data-column-name','临时线路');
                                if(row.route_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.route_temporary) return '' + row.route_temporary;
                            else return '';
                        }
                    },
                    {
                        "title": "里程",
                        "className": "bg-route",
                        "width": "50px",
                        "data": "travel_main_distance",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','里程');
                                $(nTd).attr('data-key','travel_main_distance').attr('data-value',data);
                                $(nTd).attr('data-column-name','里程');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "华东空单",
                        "className": "bg-route",
                        "width": "120px",
                        "data": "route_east",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华东空单');
                                $(nTd).attr('data-key','route_east').attr('data-value',data);
                                $(nTd).attr('data-column-name','华东空单');
                                if(row.route_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "里程",
                        "className": "bg-route",
                        "width": "50px",
                        "data": "travel_east_distance",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','里程-华东空单');
                                $(nTd).attr('data-key','travel_east_distance').attr('data-value',data);
                                $(nTd).attr('data-column-name','里程-华东空单');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "华南空单",
                        "className": "bg-route",
                        "width": "120px",
                        "data": "route_south",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华南空单');
                                $(nTd).attr('data-key','route_south').attr('data-value',data);
                                $(nTd).attr('data-column-name','华南空单');
                                if(row.route_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "里程",
                        "className": "bg-route",
                        "width": "50px",
                        "data": "travel_south_distance",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','里程-华南空单');
                                $(nTd).attr('data-key','travel_south_distance').attr('data-value',data);
                                $(nTd).attr('data-column-name','里程-华南空单');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "环线",
                        "className": "bg-route",
                        "width": "72px",
                        "data": "circle_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','环线');
                                $(nTd).attr('data-car',row.car_id);
                                $(nTd).attr('data-key','circle_id').attr('data-value',data);
                                if(row.circle_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.circle_er.title);
                                $(nTd).attr('data-column-name','环线');
                                if(row.pricing_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.circle_er == null)
                            {
                                return '--';
                            }
                            else {
                                return '<a href="javascript:void(0);">'+row.circle_er.title+'</a>';
                            }
                        }
                    },

                    // {
                    //     "title": "主驾电话",
                    //     "className": "",
                    //     "width": "100px",
                    //     "data": "driver_phone",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','主驾电话');
                    //             $(nTd).attr('data-key','driver_phone').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','主驾电话');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //         // if(data) return data;
                    //         // if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                    //         // {
                    //         //     if(row.car_er != null) return row.car_er.linkman_phone;
                    //         //     else return data;
                    //         // }
                    //         // else return data;
                    //     }
                    // },

                    {
                        "title": "收款人",
                        "className": "",
                        "width": "80px",
                        "data": "payee_name",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','收款人');
                                $(nTd).attr('data-key','payee_name').attr('data-value',data);
                                $(nTd).attr('data-column-name','收款人');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "车货源",
                        "className": "",
                        "width": "80px",
                        "data": "car_supply",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车货源');
                                $(nTd).attr('data-key','car_supply').attr('data-value',data);
                                $(nTd).attr('data-column-name','车货源');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "安排人",
                        "className": "",
                        "width": "80px",
                        "data": "arrange_people",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','安排人');
                                $(nTd).attr('data-key','arrange_people').attr('data-value',data);
                                $(nTd).attr('data-column-name','安排人');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },

                    {
                        "title": "包油油耗",
                        "className": "bg-fee",
                        "width": "100px",
                        "data": "pricing_id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','包油价');
                                $(nTd).attr('data-key','pricing_id').attr('data-value',data);
                                if(row.pricing_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.pricing_er.title);
                                $(nTd).attr('data-column-name','包油价');
                                if(row.pricing_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.pricing_er == null)
                            {
                                return '--';
                            }
                            else {
                                var $price = "--";
                                if(row.car_owner_type == 11)
                                {
                                    if(row.travel_distance >= 200) $price = row.pricing_er.price3;
                                    else $price = row.pricing_er.price2;
                                }
                                else $price = row.pricing_er.price1;
                                return '<a href="javascript:void(0);">'+row.pricing_er.title+' ('+$price+')</a>';
                            }
                        }
                    },

                    {
                        "title": "运价",
                        "className": "bg-fee _bold",
                        "width": "50px",
                        "data": "amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','运价');
                                $(nTd).attr('data-key','amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','运价');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
//                            else
//                            {
//                                $(nTd).addClass('alert-published-first');
//                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "油卡",
                        "className": "bg-fee",
                        "width": "50px",
                        "data": "oil_card_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','油卡');
                                $(nTd).attr('data-key','oil_card_amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','油卡');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "订金",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "deposit",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','订金');
                                $(nTd).attr('data-key','deposit').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','订金');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "信息费",
                        "className": "bg-fee",
                        "width": "50px",
                        "data": "information_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','信息费');
                                $(nTd).attr('data-key','information_fee').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','信息费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "信息费报销",
                        "className": "bg-fee",
                        "width": "50px",
                        "data": "information_fee_description",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','信息费报销');
                                $(nTd).attr('data-key','information_fee_description').attr('data-value',data);
                                $(nTd).attr('data-column-name','信息费报销');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },

                    // {
                    //     "title": "管理费",
                    //     "className": "bg-fee-2",
                    //     "width": "60px",
                    //     "data": "administrative_fee",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','管理费');
                    //             $(nTd).attr('data-key','administrative_fee').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','管理费');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "开票额",
                    //     "className": "bg-fee-2",
                    //     "width": "50px",
                    //     "data": "invoice_amount",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_published != 0 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','开票额');
                    //             $(nTd).attr('data-key','invoice_amount').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','开票额');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "票点",
                    //     "className": "bg-fee-2",
                    //     "width": "50px",
                    //     "data": "invoice_point",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','票点');
                    //             $(nTd).attr('data-key','invoice_point').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','票点');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    {
                        "title": "客管费",
                        "className": "bg-deduction",
                        "width": "50px",
                        "data": "customer_management_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','客户管理费');
                                $(nTd).attr('data-key','customer_management_fee').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','客户管理费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "时效扣款",
                        "className": "bg-deduction",
                        "width": "60px",
                        "data": "time_limitation_deduction",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','时效扣款');
                                $(nTd).attr('data-key','time_limitation_deduction').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','时效扣款');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "其他异常",
                        "className": "bg-deduction",
                        "width": "50px",
                        "data": "others_deduction",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','其他异常');
                                $(nTd).attr('data-key','others_deduction').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','其他异常');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },

                    // 收款
                    {
                        "title": "实收",
                        "className": "bg-income",
                        "width": "50px",
                        "data": "income_real_first_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','实收金额');
                                $(nTd).attr('data-key','income_real_first_amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','实收金额');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "日期",
                        "className": "bg-income",
                        "width": "80px",
                        "data": "income_real_first_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','实收日期');
                                $(nTd).attr('data-key','income_real_first_time').attr('data-value',data);
                                $(nTd).attr('data-column-name','实收日期');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "尾款",
                        "className": "bg-income",
                        "width": "50px",
                        "data": "income_real_final_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','尾款金额');
                                $(nTd).attr('data-key','income_real_final_amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','尾款金额');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "日期",
                        "className": "bg-income",
                        "width": "80px",
                        "data": "income_real_final_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','尾款日期');
                                $(nTd).attr('data-key','income_real_final_time').attr('data-value',data);
                                $(nTd).attr('data-column-name','尾款日期');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "欠款",
                        "className": "bg-finance",
                        "width": "50px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $receivable = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.income_total);

                                var $income = parseFloat(row.income_real_first_amount) + parseFloat(row.income_real_final_amount);

                                var $debt = parseFloat($receivable) - parseFloat($income);

                                if($debt > 0)
                                {
                                    $(nTd).addClass('color-red _bold');
                                    $(nTd).addClass('item-show-for-finance');
                                    $(nTd).attr('data-id',row.id).attr('data-type','all');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            // if(row.is_published == 0) return '--';
                            // var $to_be_collected = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.income_total);
                            // return parseFloat($to_be_collected);

                            if(row.is_published == 0) return '';
                            var $receivable = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.others_deduction);

                            var $income = parseFloat(row.income_real_first_amount) + parseFloat(row.income_real_final_amount);

                            var $debt = parseFloat($receivable) - parseFloat($income);
                            return parseFloat($debt);
                        }
                    },
                    {
                        "title": "收款结果",
                        "className": "bg-finance",
                        "width": "60px",
                        "data": "income_result",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','收款结果');
                                $(nTd).attr('data-key','income_result').attr('data-value',data);
                                $(nTd).attr('data-column-name','收款结果');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },

                    // {
                    //     "title": "报销",
                    //     "className": "bg-fee-2",
                    //     "width": "60px",
                    //     "data": "reimbursable_amount",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','报销费用');
                    //             $(nTd).attr('data-key','reimbursable_amount').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','报销费用');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    //
                    // {
                    //     "title": "万金油(升)",
                    //     "className": "bg-fee-2",
                    //     "width": "60px",
                    //     "data": "oil_amount",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','万金油(升)');
                    //             $(nTd).attr('data-key','oil_amount').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','万金油(升)');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "油价(元)",
                    //     "className": "bg-fee-2",
                    //     "width": "60px",
                    //     "data": "oil_unit_price",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','油价(元)');
                    //             $(nTd).attr('data-key','oil_unit_price').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','油价(元)');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "包邮费",
                    //     "className": "bg-fee-2",
                    //     "width": "60px",
                    //     "data": "oil_fee",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','包邮费');
                    //             $(nTd).attr('data-key','oil_fee').attr('data-value',parseFloat(data));
                    //             $(nTd).attr('data-column-name','包邮费');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },

                    // 支出
                    {
                        "title": "主单-ETC",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "toll_main_etc",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','主单-ETC');
                                $(nTd).attr('data-key','toll_main_etc').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','主单-ETC');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "主单-现金",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "toll_main_cash",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','主单-现金');
                                $(nTd).attr('data-key','toll_main_cash').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','主单-现金');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "主单-油费",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "oil_main_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','主单-油费');
                                $(nTd).attr('data-key','oil_main_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','主单-油费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },

                    {
                        "title": "华东-ETC",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "toll_east_etc",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华东-ETC');
                                $(nTd).attr('data-key','toll_east_etc').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华东-ETC');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "华东-现金",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "toll_east_cash",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华东-现金');
                                $(nTd).attr('data-key','toll_east_cash').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华东-现金');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "华东-油费",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "oil_east_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华东-油费');
                                $(nTd).attr('data-key','oil_east_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华东-油费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },

                    {
                        "title": "华南-ETC",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "toll_south_etc",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华南-ETC');
                                $(nTd).attr('data-key','toll_south_etc').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华南-ETC');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "华南-现金",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "toll_south_cash",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华南-现金');
                                $(nTd).attr('data-key','toll_south_cash').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华南-现金');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "华南-油费",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "data": "oil_south_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','华南-油费');
                                $(nTd).attr('data-key','oil_south_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','华南-油费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },

                    {
                        "title": "船费",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "shipping_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','船费');
                                $(nTd).attr('data-key','shipping_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','船费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "尿素",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "urea_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','尿素');
                                $(nTd).attr('data-key','urea_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','尿素');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "维修费",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "maintenance_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','维修费');
                                $(nTd).attr('data-key','maintenance_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','维修费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "工资",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "salary_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','工资');
                                $(nTd).attr('data-key','salary_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','工资');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "其他费用",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "others_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','其他费用');
                                $(nTd).attr('data-key','others_cost').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','其他费用');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "支出总计",
                        "className": "bg-fee-2",
                        "width": "50px",
                        "data": "others_cost",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '';
                            var $total = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc) + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost) + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost) + parseFloat(row.salary_cost) + parseFloat(row.others_cost);
                            return parseFloat($total);
                        }
                    },

                    // 外请车
                    {
                        "title": "外请-车费",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "outside_car_price",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外请-车费');
                                $(nTd).attr('data-key','outside_car_price').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','请车价');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "到付",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "outside_car_first_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外请-到付金额');
                                $(nTd).attr('data-key','outside_car_first_amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','外请-到付金额');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "日期",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "outside_car_first_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外请-到付日期');
                                $(nTd).attr('data-key','outside_car_first_time').attr('data-value',data);
                                $(nTd).attr('data-column-name','外请-到付日期');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "尾款",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "outside_car_final_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外请车-尾款金额');
                                $(nTd).attr('data-key','outside_car_final_amount').attr('data-value',parseFloat(data));
                                $(nTd).attr('data-column-name','外请车-尾款金额');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "日期",
                        "className": "bg-fee",
                        "width": "60px",
                        "data": "outside_car_final_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外请车-尾款日期');
                                $(nTd).attr('data-key','outside_car_final_time').attr('data-value',data);
                                $(nTd).attr('data-column-name','外请车-尾款日期');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },

                    // 财务信息
                    {
                        "title": "应收款",
                        "className": "bg-finance",
                        "width": "50px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                // $(nTd).addClass('color-green');
                                $(nTd).addClass('item-show-for-finance');
                                $(nTd).attr('data-id',row.id).attr('data-type','all');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0)
                            {
                                return '--';
                            }
                            var $receivable = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.others_deduction);
                            return parseFloat($receivable);
                        }
                    },
                    {
                        "title": "已收款",
                        "className": "bg-finance",
                        "width": "50px",
                        "data": "income_total",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                // $(nTd).addClass('color-blue');
                                $(nTd).addClass('item-show-for-finance-income');
                                $(nTd).attr('data-id',row.id).attr('data-type','income');
                            }
                        },
                        render: function(data, type, row, meta) {
                            // if(row.is_published == 0) return '--';
                            // return parseFloat(data);

                            if(row.is_published == 0) return '';
                            var $income = parseFloat(row.income_real_first_amount) + parseFloat(row.income_real_final_amount);
                            return parseFloat($income);
                        }
                    },
                    {
                        "title": "已支出",
                        "className": "bg-finance",
                        "width": "50px",
                        "data": "expenditure_total",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                // $(nTd).addClass('color-purple');
                                $(nTd).addClass('item-show-for-finance-expenditure');
                                $(nTd).attr('data-id',row.id).attr('data-type','expenditure');
                            }
                        },
                        render: function(data, type, row, meta) {
                            // if(row.is_published == 0) return '--';
                            // return parseFloat(data);

                            if(row.is_published == 0) return '';
                            var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                + parseFloat(row.salary_cost) + parseFloat(row.others_cost);
                            return parseFloat($expenditure);
                        }
                    },
                    // {
                    //     "title": "待入账",
                    //     "className": "bg-finance",
                    //     "width": "50px",
                    //     "data": "income_to_be_confirm",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_published != 0)
                    //         {
                    //             $(nTd).addClass('color-red _bold');
                    //             $(nTd).addClass('item-show-for-finance-income');
                    //             $(nTd).attr('data-id',row.id).attr('data-type','income');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(row.is_published == 0) return '--';
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "待出账",
                    //     "className": "bg-finance",
                    //     "width": "50px",
                    //     "data": "expenditure_to_be_confirm",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_published != 0)
                    //         {
                    //             $(nTd).addClass('color-red _bold');
                    //             $(nTd).addClass('item-show-for-finance-expenditure');
                    //             $(nTd).attr('data-id',row.id).attr('data-type','expenditure');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(row.is_published == 0) return '--';
                    //         return parseFloat(data);
                    //     }
                    // },
                    {
                        "title": "利润·预估",
                        "className": "bg-finance",
                        "width": "80px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $receivable = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.income_total);

                                var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                    + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                    + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                    + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                    + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                                var $profit = parseFloat($receivable) - parseFloat($expenditure);
                                if($profit > 0) $(nTd).addClass('color-green');
                                else if($profit < 0) $(nTd).addClass('color-red');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            var $profit = 0;

                            var $receivable = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.customer_management_fee) - parseFloat(row.income_total);

                            var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                            var $profit = parseFloat($receivable) - parseFloat($expenditure);
                            return parseFloat($profit.toFixed(2));
                        }
                    },
                    {
                        "title": "预估·利润率",
                        "className": "bg-finance",
                        "width": "80px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $income = parseFloat(row.amount) + parseFloat(row.oil_card_amount);
                                var $deduction = parseFloat(row.time_limitation_deduction) + parseFloat(row.customer_management_fee) + parseFloat(row.others_deduction);
                                var $receivable = $income - $deduction;

                                var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                    + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                    + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                    + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                    + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                                var $profit = parseFloat($receivable) - parseFloat($expenditure);
                                if($profit > 0) $(nTd).addClass('color-green');
                                else if($profit < 0) $(nTd).addClass('color-red');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            var $profit = 0;

                            var $income = parseFloat(row.amount) + parseFloat(row.oil_card_amount);
                            var $deduction = parseFloat(row.time_limitation_deduction) + parseFloat(row.customer_management_fee) + parseFloat(row.others_deduction);
                            var $receivable = $income - $deduction;

                            var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                            var $profit = parseFloat($receivable) - parseFloat($expenditure);
                            return ($profit * 100/$income).toFixed(2) + '%';
                        }
                    },
                    {
                        "title": "利润·实时",
                        "className": "bg-finance",
                        "width": "80px",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $income = parseFloat(row.income_real_first_amount) + parseFloat(row.income_real_final_amount);

                                var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                    + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                    + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                    + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                    + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                                var $profit = parseFloat($income) - parseFloat($expenditure);
                                if($profit > 0) $(nTd).addClass('color-green');
                                else if($profit < 0) $(nTd).addClass('color-red');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            var $profit = 0;

                            var $income = parseFloat(row.income_real_first_amount) + parseFloat(row.income_real_final_amount);

                            var $expenditure = parseFloat(row.toll_main_etc) + parseFloat(row.toll_east_etc) + parseFloat(row.toll_south_etc)
                                + parseFloat(row.toll_main_cash) + parseFloat(row.toll_east_cash) + parseFloat(row.toll_south_cash)
                                + parseFloat(row.oil_main_cost) + parseFloat(row.oil_east_cost) + parseFloat(row.oil_south_cost)
                                + parseFloat(row.shipping_cost) + parseFloat(row.urea_cost) + parseFloat(row.maintenance_cost)
                                + parseFloat(row.salary_cost) + parseFloat(row.others_cost);

                            var $profit = parseFloat($income) - parseFloat($expenditure);
                            return parseFloat($profit.toFixed(2));
                        }
                    },

                    // {
                    //     "className": "",
                    //     "width": "60px",
                    //     "title": "空单(线路类型)",
                    //     "data": "empty_route_type",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-radio-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单(线路类型)');
                    //             $(nTd).attr('data-key','empty_route_type').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单(线路类型)');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data == 1) return '<small class="btn-xs btn-success">固</small>';
                    //         else if(data == 11) return '<small class="btn-xs btn-danger">临</small>';
                    //         else return '--';
                    //     }
                    // },
                    // {
                    //     "title": "空单-固定",
                    //     "className": "bg-empty",
                    //     "width": "120px",
                    //     "data": "empty_route_id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-select2-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单固定线路');
                    //             $(nTd).attr('data-key','empty_route_id').attr('data-value',row.empty_route_id);
                    //             if(row.empty_route_er == null) $(nTd).attr('data-option-name','未指定');
                    //             else $(nTd).attr('data-option-name',row.empty_route_er.title);
                    //             $(nTd).attr('data-column-name','空单固定线路');
                    //             if(row.empty_route_id) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //
                    //             // if(data == 1)
                    //             // {
                    //             // }
                    //             // else if(data == 11)
                    //             // {
                    //             //     $(nTd).addClass('modal-show-for-info-text-set');
                    //             //     $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                    //             //     $(nTd).attr('data-key','route_temporary').attr('data-value',row.route_temporary);
                    //             //     if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                    //             //     $(nTd).attr('data-column-name','临时线路');
                    //             //     if(row.route_temporary) $(nTd).attr('data-operate-type','edit');
                    //             //     else $(nTd).attr('data-operate-type','add');
                    //             // }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(row.empty_route_er == null) return '--';
                    //         else return '<a href="javascript:void(0);">'+row.empty_route_er.title+'</a>';
                    //
                    //         // if(data == 1)
                    //         // {
                    //         // }
                    //         // else if(data == 11)
                    //         // {
                    //         //     if(row.route_temporary) return '[临]' + row.route_temporary;
                    //         //     else return '[临时]';
                    //         // }
                    //         // else return '有误';
                    //     }
                    // },
                    // {
                    //     "title": "空单-临时",
                    //     "className": "bg-empty",
                    //     "width": "80px",
                    //     "data": "empty_route_temporary",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单');
                    //             $(nTd).attr('data-key','empty_route').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data) return data;
                    //         else return '--';
                    //
                    //     }
                    // },
                    // {
                    //     "title": "空-里程",
                    //     "className": "bg-empty",
                    //     "width": "60px",
                    //     "data": "empty_distance",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-里程数');
                    //             $(nTd).attr('data-key','empty_distance').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-里程数');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "title": "空-包油",
                    //     "className": "bg-empty",
                    //     "width": "60px",
                    //     "data": "empty_oil_price",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-包油价');
                    //             $(nTd).attr('data-key','empty_oil_price').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-包油价');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "空-包油金额",
                    //     "className": "bg-empty",
                    //     "width": "60px",
                    //     "data": "empty_oil_amount",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-包油金额');
                    //             $(nTd).attr('data-key','empty_oil_amount').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-包油金额');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "空-加油方式",
                    //     "className": "bg-empty",
                    //     "width": "80px",
                    //     "data": "empty_refueling_pay_type",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-加油方式');
                    //             $(nTd).attr('data-key','empty_refueling_pay_type').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-加油方式');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         if(data) return data;
                    //         else return '--';
                    //     }
                    // },
                    // {
                    //     "title": "空-加油金额",
                    //     "className": "bg-empty",
                    //     "width": "80px",
                    //     "data": "empty_refueling_charge",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-加油费');
                    //             $(nTd).attr('data-key','empty_refueling_charge').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-加油费');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "空-过路-现金",
                    //     "className": "bg-empty",
                    //     "width": "80px",
                    //     "data": "empty_toll_cash",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-过路费（现金）');
                    //             $(nTd).attr('data-key','empty_toll_cash').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-过路费（现金）');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },
                    // {
                    //     "title": "空-过路-ETC",
                    //     "className": "bg-empty",
                    //     "width": "80px",
                    //     "data": "empty_toll_ETC",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','空单-过路费（ETC）');
                    //             $(nTd).attr('data-key','empty_toll_ETC').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','空单-过路费（ETC）');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return parseFloat(data);
                    //     }
                    // },

//                    {
//                        "title": "需求类型",
//                        "className": "",
//                        "width": "80px",
//                        "data": "order_type",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            if(data == 1)
//                            {
//                                return '<small class="btn-xs bg-green">自有</small>';
//                            }
//                            else if(data == 11)
//                            {
//                                return '<small class="btn-xs bg-blue">调车</small>';
//                            }
//                            else if(data == 21)
//                            {
//                                return '<small class="btn-xs bg-purple">配货</small>';
//                            }
//                            else if(data == 31)
//                            {
//                                return '<small class="btn-xs bg-orange">合同单项</small>';
//                            }
//                            else if(data == 41)
//                            {
//                                return '<small class="btn-xs bg-red">合同双向</small>';
//                            }
//                            else return "";
//                        }
//                    },

//                    {
//                        "title": "线路",
//                        "className": "",
//                        "width": "120px",
//                        "data": "route",
//                        "orderable": false,
//                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_completed != 1 && row.item_status != 97)
//                            {
//                                $(nTd).addClass('modal-show-for-info-text-set');
//                                $(nTd).attr('data-id',row.id).attr('data-name','线路');
//                                $(nTd).attr('data-key','route').attr('data-value',data);
//                                $(nTd).attr('data-column-name','线路');
//                                $(nTd).attr('data-text-type','text');
//                                if(data) $(nTd).attr('data-operate-type','edit');
//                                else $(nTd).attr('data-operate-type','add');
//                            }
//                        },
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
//                    {
//                        "title": "行程",
//                        "className": "",
//                        "width": "120px",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
////                            return data == null ? '--' : data;
//                            var $stopover_html = '';
//                            if(row.stopover_place) $stopover_html = '--' + row.stopover_place;
//                            return row.departure_place + $stopover_html + '--' + row.destination_place;
//                        }
//                    },
                    // {
                    //     "className": "text-center",
                    //     "width": "40px",
                    //     "title": "类型",
                    //     "data": "id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(row.car_owner_type == 61)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','车挂类型');
                    //                 $(nTd).attr('data-key','trailer_type').attr('data-value',row.trailer_type);
                    //                 $(nTd).attr('data-column-name','车挂类型');
                    //                 if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         var reurn_html = '';
                    //         if(row.car_owner_type == 1)
                    //         {
                    //             if(row.trailer_er != null && row.trailer_er.trailer_type) reurn_html = row.trailer_er.trailer_type;
                    //         }
                    //         else
                    //         {
                    //             if(row.trailer_type && row.trailer_type != 0) reurn_html = row.trailer_type;
                    //         }
                    //         return reurn_html;
                    //     }
                    // },
                    // {
                    //     "className": "text-center",
                    //     "width": "40px",
                    //     "title": "尺寸",
                    //     "data": "id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(row.car_owner_type == 61)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','车挂尺寸');
                    //                 $(nTd).attr('data-key','trailer_length').attr('data-value',row.trailer_length);
                    //                 $(nTd).attr('data-column-name','车挂尺寸');
                    //                 if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         var reurn_html = '';
                    //         if(row.car_owner_type == 1)
                    //         {
                    //             if(row.trailer_er != null && row.trailer_er.trailer_length) reurn_html = row.trailer_er.trailer_length;
                    //         }
                    //         else
                    //         {
                    //             if(row.trailer_length && row.trailer_length != 0) reurn_html = row.trailer_length;
                    //         }
                    //         return reurn_html;
                    //     }
                    // },
                    // {
                    //     "className": "text-center",
                    //     "width": "40px",
                    //     "title": "容积",
                    //     "data": "id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(row.car_owner_type == 61)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','车挂容积');
                    //                 $(nTd).attr('data-key','trailer_volume').attr('data-value',row.trailer_volume);
                    //                 $(nTd).attr('data-column-name','车挂容积');
                    //                 if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         var $return_html = '';
                    //         if(row.car_owner_type == 1)
                    //         {
                    //             if(row.trailer_er != null && row.trailer_er.trailer_volume) $return_html = row.trailer_er.trailer_volume;
                    //         }
                    //         else
                    //         {
                    //             if(row.trailer_volume && row.trailer_volume != 0) $return_html = row.trailer_volume;
                    //         }
                    //         return $return_html;
                    //     }
                    // },
                    // {
                    //     "className": "text-center",
                    //     "width": "40px",
                    //     "title": "载重",
                    //     "data": "id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(row.car_owner_type == 61)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','车挂载重');
                    //                 $(nTd).attr('data-key','trailer_weight').attr('data-value',row.trailer_weight);
                    //                 $(nTd).attr('data-column-name','车挂载重');
                    //                 if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         var $return_html = '';
                    //         if(row.car_owner_type == 1)
                    //         {
                    //             if(row.trailer_er != null && row.trailer_er.trailer_weight) $return_html = row.trailer_er.trailer_weight;
                    //         }
                    //         else
                    //         {
                    //             if(row.trailer_weight && row.trailer_weight != 0) $return_html = row.trailer_weight;
                    //         }
                    //         return $return_html;
                    //     }
                    // },
                    // {
                    //     "className": "text-center",
                    //     "width": "40px",
                    //     "title": "轴数",
                    //     "data": "id",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             if(row.car_owner_type == 61)
                    //             {
                    //                 $(nTd).addClass('modal-show-for-info-select-set');
                    //                 $(nTd).attr('data-id',row.id).attr('data-name','车挂轴数');
                    //                 $(nTd).attr('data-key','trailer_axis_count').attr('data-value',row.trailer_axis_count);
                    //                 $(nTd).attr('data-column-name','车挂轴数');
                    //                 if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                    //                 else $(nTd).attr('data-operate-type','add');
                    //             }
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         var reurn_html = '';
                    //         if(row.car_owner_type == 1)
                    //         {
                    //             if(row.trailer_er != null && row.trailer_er.trailer_axis_count) reurn_html = row.trailer_er.trailer_axis_count;
                    //         }
                    //         else
                    //         {
                    //             if(row.trailer_axis_count) reurn_html = row.trailer_axis_count;
                    //         }
                    //         return reurn_html;
                    //     }
                    // },

                    {
                        "title": "出发地",
                        "className": "text-center",
                        "width": "60px",
                        "data": "departure_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','出发地');
                                $(nTd).attr('data-key','departure_place').attr('data-value',data);
                                $(nTd).attr('data-column-name','出发地');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "title": "经停地",
                        "className": "text-center",
                        "width": "60px",
                        "data": "stopover_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','经停地');
                                $(nTd).attr('data-key','stopover_place').attr('data-value',data);
                                $(nTd).attr('data-column-name','经停地');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "title": "目的地",
                        "className": "text-center",
                        "width": "60px",
                        "data": "destination_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','目的地');
                                $(nTd).attr('data-key','destination_place').attr('data-value',data);
                                $(nTd).attr('data-column-name','目的地');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "title": "时效",
                        "className": "text-center",
                        "width": "60px",
                        "data": "time_limitation_prescribed",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','时效');
                                $(nTd).attr('data-key','time_limitation_prescribed').attr('data-value',data);
                                $(nTd).attr('data-column-name','时效');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
                    {
                        "title": "状态",
                        "className": "",
                        "width": "80px",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;

                            if(row.deleted_at != null)
                            {
                                return '';
                            }

                            if(row.is_published == 0)
                            {
                                return '';
                            }


                            var $travel_status_html = '';
                            var $travel_result_html = '';



                            if(row.travel_result == "正常")
                            {
                                $travel_result_html = '<small class="btn-xs bg-olive">正常</small>';
                            }
                            else if(row.travel_result == "超时")
                            {
                                $travel_result_html = '<small class="btn-xs bg-red">超时</small><br>';
                            }
                            else if(row.travel_result == "发车超时")
                            {
                                $travel_result_html = '<small class="btn-xs btn-danger">发车超时</small>';
                            }
                            else if(row.travel_result == "待收款")
                            {
                                $travel_result_html = '<small class="btn-xs bg-orange">待收款</small>';
                            }
                            else if(row.travel_result == "已收款")
                            {
                                $travel_result_html = '<small class="btn-xs bg-blue">已收款</small>';
                            }


                            if(row.is_completed == 1)
                            {
                                $travel_result_html = '<small class="btn-xs bg-grey">已结束</small>';
                            }

                            return $travel_status_html + $travel_result_html;

                        }
                    },
                    {
                        "title": "应出发时间",
                        "className": "order-info-time-edit bg-journey",
                        "width": "100px",
                        "data": 'should_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','应出发时间');
                                $(nTd).attr('data-key','should_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','应出发时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                            }
                        }
                    },
                    {
                        "title": "应到达时间",
                        "className": "bg-journey",
                        "width": "100px",
                        "data": 'should_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','应到达时间');
                                $(nTd).attr('data-key','should_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','应到达时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                            }

                        }
                    },
                    {
                        "title": "实际出发",
                        "className": "bg-journey",
                        "width": "100px",
                        "data": 'actual_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','实际出发时间');
                                $(nTd).attr('data-key','actual_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','实际出发时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                        }
                    },
                    {
                        "title": "经停-到达",
                        "className": "bg-journey",
                        "width": "100px",
                        "data": 'stopover_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','经停到达时间');
                                $(nTd).attr('data-key','stopover_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','经停到达时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);


                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                        }
                    },
                    {
                        "title": "经停-出发",
                        "className": "bg-journey",
                        "width": "100px",
                        "data": 'stopover_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','经停出发时间');
                                $(nTd).attr('data-key','stopover_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','经停出发时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);


                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$hour;
                            }
                        }
                    },
                    {
                        "title": "实际到达",
                        "className": "bg-journey",
                        "width": "100px",
                        "data": 'actual_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                                }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','实际到达时间');
                                $(nTd).attr('data-key','actual_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','实际到达时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                            }
                            if(!data) return '';

                            var $time = new Date(data*1000);
                            var $year = $time.getFullYear();
                            var $month = ('00'+($time.getMonth()+1)).slice(-2);
                            var $day = ('00'+($time.getDate())).slice(-2);
                            var $hour = ('00'+$time.getHours()).slice(-2);
                            var $minute = ('00'+$time.getMinutes()).slice(-2);
                            var $second = ('00'+$time.getSeconds()).slice(-2);

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear)
                            {
                                return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                            }
                        }
                    },
                    {
                        "title": "行程",
                        "className": "bg-journey",
                        "width": "200px",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $journey_time = '';
                            var $travel_departure_overtime_time = '';
                            var $travel_arrival_overtime_time = '';

                            if(row.travel_journey_time) $journey_time = '<small class="btn-xs bg-gray">行程 '+row.travel_journey_time+'</small><br>';
                            if(row.travel_departure_overtime_time) $travel_departure_overtime_time = '<small class="btn-xs bg-red">发车超时 '+row.travel_departure_overtime_time+'</small><br>';
                            if(row.travel_arrival_overtime_time) $travel_arrival_overtime_time = '<small class="btn-xs bg-red">到达超时 '+row.travel_arrival_overtime_time+'</small><br>';

                            return $journey_time + $travel_departure_overtime_time + $travel_arrival_overtime_time;
                        }
                    },



                    {
                        "title": "主驾",
                        "className": "",
                        "width": "60px",
                        "data": "driver_name",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','主驾姓名');
                                $(nTd).attr('data-key','driver_name').attr('data-value',data);
                                $(nTd).attr('data-column-name','主驾姓名');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(data) return data;
                            // if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            // {
                            //     if(row.car_er != null) return row.car_er.linkman_name;
                            //     else return data;
                            // }
                            // else return data;
                        }
                    },
                    {
                        "title": "主驾电话",
                        "className": "",
                        "width": "100px",
                        "data": "driver_phone",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','主驾电话');
                                $(nTd).attr('data-key','driver_phone').attr('data-value',data);
                                $(nTd).attr('data-column-name','主驾电话');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(data) return data;
                            // if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            // {
                            //     if(row.car_er != null) return row.car_er.linkman_phone;
                            //     else return data;
                            // }
                            // else return data;
                        }
                    },
                    {
                        "title": "副驾",
                        "className": "",
                        "width": "60px",
                        "data": "copilot_name",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','副驾姓名');
                                $(nTd).attr('data-key','copilot_name').attr('data-value',data);
                                $(nTd).attr('data-column-name','副驾姓名');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "副驾电话",
                        "className": "",
                        "width": "100px",
                        "data": "copilot_phone",
                        "orderable": false,
                        "visible" : true,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','副驾电话');
                                $(nTd).attr('data-key','copilot_phone').attr('data-value',data);
                                $(nTd).attr('data-column-name','副驾电话');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },

                    {
                        "title": "单号",
                        "className": "",
                        "width": "120px",
                        "data": "order_number",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','单号');
                                $(nTd).attr('data-key','order_number').attr('data-value',data);
                                $(nTd).attr('data-column-name','单号');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    // {
                    //     "title": "车负责人",
                    //     "className": "",
                    //     "width": "80px",
                    //     "data": "car_managerial_people",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','车辆负责人');
                    //             $(nTd).attr('data-key','car_managerial_people').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','车辆负责人');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    // {
                    //     "className": "",
                    //     "width": "80px",
                    //     "title": "重量",
                    //     "data": "weight",
                    //     "orderable": false,
                    //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    //         if(row.is_completed != 1 && row.item_status != 97)
                    //         {
                    //             $(nTd).addClass('modal-show-for-info-text-set');
                    //             $(nTd).attr('data-id',row.id).attr('data-name','重量');
                    //             $(nTd).attr('data-key','weight').attr('data-value',data);
                    //             $(nTd).attr('data-column-name','重量');
                    //             $(nTd).attr('data-text-type','text');
                    //             if(data) $(nTd).attr('data-operate-type','edit');
                    //             else $(nTd).attr('data-operate-type','add');
                    //         }
                    //     },
                    //     render: function(data, type, row, meta) {
                    //         return data;
                    //     }
                    // },
                    {
                        "title": "GPS",
                        "className": "",
                        "width": "80px",
                        "data": "GPS",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','GPS');
                                $(nTd).attr('data-key','GPS').attr('data-value',data);
                                $(nTd).attr('data-column-name','GPS');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "是否回单",
                        "className": "",
                        "width": "60px",
                        "data": "receipt_need",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-radio-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','是否回单');
                                $(nTd).attr('data-key','receipt_need').attr('data-value',data);
                                $(nTd).attr('data-column-name','是否回单');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs btn-danger">需要</small>';
                            else return '--';
                        }
                    },
                    {
                        "title": "回单状态",
                        "className": "",
                        "width": "80px",
                        "data": "receipt_status",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.receipt_need == 1)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','回单状态');
                                    $(nTd).attr('data-key','receipt_status').attr('data-value',data);
                                    $(nTd).attr('data-column-name','回单状态');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.receipt_need == 1)
                            {
                                if(data == 0) return '<small class="btn-xs bg-orange">等待回单</small>';
                                else if(data == 1) return '<small class="btn-xs bg-orange">等待回单</small>';
                                else if(data == 21) return '<small class="btn-xs bg-aqua">邮寄中</small>';
                                else if(data == 41) return '<small class="btn-xs bg-blue">已签收</small>';
                                else if(data == 100) return '<small class="btn-xs bg-olive">已完成</small>';
                                else if(data == 101) return '<small class="btn-xs bg-red">回单异常</small>';
                                else return '<small class="btn-xs bg-red">有误</small>';
                            }
                            else return '--';

                        }
                    },
                    {
                        "title": "回单地址",
                        "className": "",
                        "width": "100px",
                        "data": "receipt_address",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.receipt_need == 1)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','回单地址');
                                    $(nTd).attr('data-key','receipt_address').attr('data-value',data);
                                    $(nTd).attr('data-column-name','回单地址');
                                    $(nTd).attr('data-text-type','text');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.receipt_need == 1) return data;
                            else return '--';
                        }
                    },
                    {
                        "title": "附件",
                        "className": "",
                        "width": "80px",
                        "data": "attachment_list_count",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','receipt_status').attr('data-value',data);
                                $(nTd).attr('data-column-name','附件');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data > 0) return '<small class="btn-xs bg-purple">有附件</small>';
                            else return '--';

                            // if(!data) return '--';
                            // if(data.length == 0) return '--';
                            // else if(data.length > 0) return '<small class="btn-xs bg-purple">有附件</small>';
                            // else return '--';

//                            var html = '';
//                            $.each(data,function( index, element ) {
////                                console.log( index, element, this );
//                                html += '<a target="_blank" href="/people?id='+this.id+'">'+this.attachment_name+'</a><br>';
//                            });
//                            return html;
                        }
                    },
                    {
                        "title": "创建时间",
                        "className": "",
                        "width": "100px",
                        "data": 'created_at',
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        }
                    },
                    {
                        "title": "修改时间",
                        "className": "",
                        "width": "100px",
                        "data": 'updated_at',
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        }
                    },
                    {
                        "title": "操作",
                        "className": "",
                        "width": "180px",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_travel = '';
                            var $html_finance = '';
                            var $html_record = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_abandon = '';
                            var $html_completed = '';
                            var $html_verified = '';

                            var $car_etc = '';
                            if(row.car_er != null) var $car_etc = row.car_er.ETC_account;

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

//                            if(row.is_me == 1 && row.item_active == 0)
                            if(row.is_published == 0)
                            {
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                                $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';
                                $html_verified = '<a class="btn btn-xs btn-default disabled">审核</a>';
                                $html_delete = '<a class="btn btn-xs bg-black item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_detail = '<a class="btn btn-xs bg-primary item-modal-show-for-detail" data-id="'+data+'">详情</a>';
//                                $html_travel = '<a class="btn btn-xs bg-olive item-modal-show-for-travel" data-id="'+data+'">行程</a>';
                                $html_finance = '<a class="btn btn-xs bg-orange item-modal-show-for-finance" data-id="'+data+'" data-etc="'+$car_etc+'">财务</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                                if(row.is_completed == 1)
                                {
                                    $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                    $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                }
                                else
                                {
                                    var $to_be_collected = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.income_total);
                                    if($to_be_collected > 0)
                                    {
                                        $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                    }
                                    else $html_completed = '<a class="btn btn-xs bg-blue item-complete-submit" data-id="'+data+'">完成</a>';

                                    if(row.item_status == 97)
                                    {
                                        // $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                        $html_abandon = '<a class="btn btn-xs bg-teal item-reuse-submit" data-id="'+data+'">复用</a>';
                                    }
                                    else $html_abandon = '<a class="btn btn-xs bg-gray item-abandon-submit" data-id="'+data+'">弃用</a>';
                                }

                                // 审核
                                if(row.verifier_id == 0)
                                {
                                    $html_verified = '<a class="btn btn-xs bg-teal item-verify-submit" data-id="'+data+'">审核</a>';
                                }
                                else
                                {
                                    $html_verified = '<a class="btn btn-xs bg-aqua-gradient disabled">已审</a>';
                                }

                            }



//                            if(row.deleted_at == null)
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
//                            }
//                            else
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
//                            }

                            var $more_html =
                                '<div class="btn-group">'+
                                '<button type="button" class="btn btn-xs btn-success" style="padding:2px 8px; margin-right:0;">操作</button>'+
                                '<button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding:2px 6px; margin-left:-1px;">'+
                                '<span class="caret"></span>'+
                                '<span class="sr-only">Toggle Dropdown</span>'+
                                '</button>'+
                                '<ul class="dropdown-menu" role="menu">'+
                                '<li><a href="#">Action</a></li>'+
                                '<li><a href="#">删除</a></li>'+
                                '<li><a href="#">弃用</a></li>'+
                                '<li class="divider"></li>'+
                                '<li><a href="#">Separate</a></li>'+
                                '</ul>'+
                                '</div>';

                            var $html =
//                                    $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                                $html_completed+
                                $html_edit+
                                $html_publish+
                                //                                $html_detail+
                                $html_travel+
                                $html_finance+
                                $html_record+
                                $html_verified+
                                $html_delete+
                                $html_abandon+
//                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
//                                $more_html+
                                '';
                            return $html;

                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="order-id"]').val())  $obj.order_id = $('input[name="order-id"]').val();
                    if($('input[name="order-assign"]').val())  $obj.assign = $('input[name="order-assign"]').val();
                    if($('input[name="order-start"]').val())  $obj.assign_start = $('input[name="order-start"]').val();
                    if($('input[name="order-ended"]').val())  $obj.assign_ended = $('input[name="order-ended"]').val();
                    if($('select[name="order-staff"]').val() > 0)  $obj.staff_id = $('select[name="order-staff"]').val();
                    if($('select[name="order-client"]').val() > 0)  $obj.client_id = $('select[name="order-client"]').val();
                    if($('select[name="order-circle"]').val() > 0)  $obj.circle_id = $('select[name="order-circle"]').val();
                    if($('select[name="order-route"]').val() > 0)  $obj.route_id = $('select[name="order-route"]').val();
                    if($('select[name="order-pricing"]').val() > 0)  $obj.pricing_id = $('select[name="order-pricing"]').val();
                    if($('select[name="order-car"]').val() > 0)  $obj.car_id = $('select[name="order-car"]').val();
                    if($('select[name="order-trailer"]').val() > 0)  $obj.trailer_id = $('select[name="order-trailer"]').val();
                    if($('select[name="order-driver"]').val() > 0)  $obj.driver_id = $('select[name="order-driver"]').val();
                    if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
                    if($('select[name="order-is-delay"]').val() > 0)  $obj.is_delay = $('select[name="order-is-delay"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != 15) $obj.length = $page_length;
                    var $page_start = this.api().context[0]._iDisplayStart; // 当前页开始
                    var $pagination = ($page_start / $page_length) + 1; //得到页数值 比页码小1
                    if($pagination > 1) $obj.page = $pagination;


                    if(JSON.stringify($obj) != "{}")
                    {
                        var $url = url_build('',$obj);
                        history.replaceState({page: 1}, "", $url);
                    }
                    else
                    {
                        $url = "{{ url('/item/order-list-for-all') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();

    $(function () {

        var $id = $.getUrlParam('id');
        if($id) $('input[name="order-id"]').val($id);
        TableDatatablesAjax.init();
        // $('#datatable_ajax').DataTable().init().fnPageChange(3);
    });
</script>


<script>
    var TableDatatablesAjax_finance = function ($id) {
        var datatableAjax_finance = function ($id,$type) {

            var dt_finance = $('#datatable_ajax_finance');
            dt_finance.DataTable().destroy();
            var ajax_datatable_finance = dt_finance.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/order-finance-record?id="+$id+"&type="+$type,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="finance-name"]').val();
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="finance-keyword"]').val();
                        d.finance_type = $('select[name="finance-finance_type"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "",
                        "width": "40px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "120px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $html_confirm = '';
                            var $html_delete = '';

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';

                                if(row.is_confirmed == 1)
                                {
                                    $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';

                                    // if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id)
                                    if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id || "{{ $me->id or 0 }}" == row.confirmer_id || "{{ $me->id }}" == row.creator_id)
                                    {
                                        $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';
                                    }
                                    else $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
                                }
                                else
                                {
                                    $html_confirm = '<a class="btn btn-xs bg-green item-finance-confirm-submit" data-id="'+data+'">确认</a>';
                                }
                            }
                            else
                            {
                                $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';
                                $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
//                                $html_delete = '<a class="btn btn-xs bg-grey item-finance-restore-submit" data-id="'+data+'">恢复</a>';
                            }


                            var html =
                                $html_confirm+
                                $html_delete+
//                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "width": "60px",
                        "title": "状态",
                        "data": "is_confirmed",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.deleted_at == null)
                            {
                                if(data == 1) return '<small class="btn-xs btn-success">已确认</small>';
                                else return '<small class="btn-xs btn-danger">待确认</small>';
                            }
                            else return '<small class="btn-xs bg-black">已删除</small>';
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "类型",
                        "data": "finance_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.finance_type == 1) return '<small class="btn-xs bg-olive">收入</small>';
                            else if(row.finance_type == 21) return '<small class="btn-xs bg-orange">支出</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "交易时间",
                        "data": "transaction_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day;
                                }

                                $(nTd).addClass('modal-show-for-finance-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交易时间');
                                $(nTd).attr('data-key','transaction_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','交易时间');
                                $(nTd).attr('data-time-type','date');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day;
                            else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "确认者",
                        "data": "confirmer_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.confirmer == null ? '' : '<a href="javascript:void(0);">'+row.confirmer.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "确认时间",
                        "data": "confirmed_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;

                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "金额",
                        "data": "transaction_amount",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.finance_type == 1) return '<b class="text-olive">'+parseFloat(data)+'</b>';
                            else if(row.finance_type == 21) return '<b class="text-red">'+parseFloat(data)+'</b>';
                            else return parseFloat(data);
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "费用名目",
                        "data": "title",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','费用名目');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','费用名目');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "支付方式",
                        "data": "transaction_type",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','支付方式');
                                $(nTd).attr('data-key','transaction_type').attr('data-value',data);
                                $(nTd).attr('data-column-name','支付方式');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "收款账户",
                        "data": "transaction_receipt_account",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','收款账户');
                                $(nTd).attr('data-key','transaction_receipt_account').attr('data-value',data);
                                $(nTd).attr('data-column-name','收款账户');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "支出账户",
                        "data": "transaction_payment_account",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','支出账户');
                                $(nTd).attr('data-key','transaction_payment_account').attr('data-value',data);
                                $(nTd).attr('data-column-name','支出账户');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "className": "",
//                        "width": "120px",
//                        "title": "交易账户",
//                        "data": "transaction_account",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "交易单号",
                        "data": "transaction_order",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交易单号');
                                $(nTd).attr('data-key','transaction_order').attr('data-value',data);
                                $(nTd).attr('data-column-name','交易单号');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "200px",
                        "title": "备注",
                        "data": "description",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','备注');
                                $(nTd).attr('data-key','description').attr('data-value',data);
                                $(nTd).attr('data-column-name','备注');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "操作时间",
                        "data": "created_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    },
//                    {
//                        "width": "120px",
//                        "title": "操作",
//                        'data': 'id',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
////                            var $date = row.transaction_date.trim().split(" ")[0];
//                            var html = '';
////                                '<a class="btn btn-xs item-enable-submit" data-id="'+value+'">启用</a>'+
////                                '<a class="btn btn-xs item-disable-submit" data-id="'+value+'">禁用</a>'+
////                                '<a class="btn btn-xs item-download-qrcode-submit" data-id="'+value+'">下载二维码</a>'+
////                                '<a class="btn btn-xs item-statistics-submit" data-id="'+value+'">流量统计</a>'+
////                                    '<a class="btn btn-xs" href="/item/edit?id='+value+'">编辑</a>'+
////                                '<a class="btn btn-xs item-edit-submit" data-id="'+value+'">编辑</a>'+
//
//                            return html;
//                        }
//                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_finance.on('click', '.finance-filter-submit', function () {
                ajax_datatable_finance.ajax.reload();
            });

            dt_finance.on('click', '.finance-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt_finance).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_finance.ajax.reload();
            });


//            dt_finance.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_finance
        }
    }();
    //    $(function () {
    //        TableDatatablesAjax_finance.init();
    //    });
</script>


<script>
    var TableDatatablesAjax_record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_record = $('#datatable_ajax_record');
            dt_record.DataTable().destroy();
            var ajax_datatable_record = dt_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/order-modify-record?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "类型",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_category == 1)
                            {
                                if(data == "client_id") return '客户';
                                else if(data == "route_id") return '固定线路';
                                else if(data == "circle_id") return '环线';
                                else if(data == "pricing_id") return '包油价';
                                else if(data == "car_id") return '车辆';
                                else if(data == "trailer_id") return '车挂';
                                else if(data == "outside_car") return '车辆';
                                else if(data == "outside_trailer") return '车挂';
                                else if(data == "driver_id") return '驾驶员';
                                else if(data == "travel_distance") return '里程数';
                                else if(data == "time_limitation_prescribed") return '时效';
                                else if(data == "assign_time") return '安排时间';
                                else if(data == "amount") return '金额';
                                else if(data == "deposit") return '定金';
                                else if(data == "oil_card_amount") return '油卡';
                                else if(data == "invoice_amount") return '开票金额';
                                else if(data == "invoice_point") return '票点';
                                else if(data == "reimbursable_amount") return '报销费';
                                else if(data == "customer_management_fee") return '客户管理费';
                                else if(data == "time_limitation_deduction") return '时效扣款';
                                else if(data == "information_fee") return '信息费';
                                else if(data == "administrative_fee") return '管理费';
                                else if(data == "oil_amount") return '万金油量（升）';
                                else if(data == "oil_unit_price") return '油价（元）';
                                else if(data == "oil_fee") return '包邮费';
                                else if(data == "ETC_price") return 'ETC费用';
                                else if(data == "others_fee") return '其他费用';
                                else if(data == "income_real_first_amount") return '实收金额';
                                else if(data == "income_real_first_time") return '实收日期';
                                else if(data == "income_real_final_amount") return '实收尾款金额';
                                else if(data == "income_real_final_time") return '实收尾款日期';
                                else if(data == "outside_car_price") return '外请车-费用';
                                else if(data == "outside_car_first_amount") return '外请车-到付金额';
                                else if(data == "outside_car_first_time") return '外请车-到付日期';
                                else if(data == "outside_car_final_amount") return '外请车-尾款金额';
                                else if(data == "outside_car_final_time") return '外请车-尾款日期';
                                else if(data == "container_type") return '箱型';
                                else if(data == "subordinate_company") return '所属公司';
                                else if(data == "route") return '线路';
                                else if(data == "fixed_route") return '固定线路';
                                else if(data == "temporary_route") return '临时线路';
                                else if(data == "departure_place") return '出发地';
                                else if(data == "destination_place") return '目的地';
                                else if(data == "stopover_place") return '经停点';
                                else if(data == "should_departure_time") return '应出发时间';
                                else if(data == "should_arrival_time") return '应到达时间';
                                else if(data == "actual_departure_time") return '实际出发时间';
                                else if(data == "actual_arrival_time") return '实际到达时间';
                                else if(data == "stopover_departure_time") return '实际出发时间';
                                else if(data == "stopover_arrival_time") return '实际到达时间';
                                else if(data == "driver_name") return '主驾姓名';
                                else if(data == "driver_phone") return '主驾电话';
                                else if(data == "copilot_name") return '副驾姓名';
                                else if(data == "copilot_phone") return '副驾电话';
                                else if(data == "trailer_type") return '车挂类型';
                                else if(data == "trailer_length") return '车挂尺寸';
                                else if(data == "trailer_volume") return '车挂容积';
                                else if(data == "trailer_weight") return '车辆载重';
                                else if(data == "trailer_axis_count") return '轴数';
                                else if(data == "empty_route") return '空单-线路';
                                else if(data == "empty_route_type") return '空单-线路类型';
                                else if(data == "empty_route_id") return '空单-固定线路';
                                else if(data == "empty_route_temporary") return '空单-临时线路';
                                else if(data == "GPS") return 'GPS';
                                else if(data == "receipt_address") return '回单地址';
                                else if(data == "receipt_status") return '回单状态';
                                else if(data == "is_delay") return '是否压车';
                                else if(data == "order_number") return '单号';
                                else if(data == "payee_name") return '收款人';
                                else if(data == "arrange_people") return '安排人';
                                else if(data == "car_managerial_people") return '车辆负责员';
                                else if(data == "car_supply") return '车货源';
                                else if(data == "weight") return '重量';
                                else if(data == "remark") return '备注';
                                else return '有误';
                            }
                            else if(row.operate_category == 71)
                            {
                                return '';

                                if(row.operate_type == 1) return '添加';
                                else if(row.operate_type == 91) return '删除';

                                if(data == "attachment") return '附件';
                            }
                            else return '';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.before_client_er == null) return '';
                                else
                                {
                                    if(row.before_client_er.short_name != null)
                                    {
                                        return '<a href="javascript:void(0);">'+row.before_client_er.short_name+'</a>';
                                    }
                                    else return '<a href="javascript:void(0);">'+row.before_client_er.username+'</a>';
                                }
                            }
                            else if(row.column_name == 'circle_id')
                            {
                                if(row.before_circle_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_circle_er.title+'</a>';
                            }
                            else if(row.column_name == 'route_id')
                            {
                                if(row.before_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'emtpy_route_id')
                            {
                                if(row.before_emtpy_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_emtpy_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'pricing_id')
                            {
                                if(row.before_pricing_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_pricing_er.title+'</a>';
                            }
                            else if(row.column_name == 'car_id' || row.column_name == 'trailer_id')
                            {
                                if(row.before_car_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_car_er.name+'</a>';
                            }
                            else if(row.column_name == 'driver_id')
                            {
                                if(row.before_driver_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_driver_er.driver_name+'</a>';
                            }

                            if(row.column_name == 'is_delay')
                            {
                                if(data == 1) return '正常';
                                else if(data == 9) return '压车';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.after_client_er == null) return '';
                                else
                                {
                                    if(row.after_client_er.short_name)
                                    {
                                        return '<a href="javascript:void(0);">'+row.after_client_er.short_name+'</a>';
                                    }
                                    else return '<a href="javascript:void(0);">'+row.after_client_er.username+'</a>';
                                }
                            }
                            else if(row.column_name == 'circle_id')
                            {
                                if(row.after_circle_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_circle_er.title+'</a>';
                            }
                            else if(row.column_name == 'route_id')
                            {
                                if(row.after_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'empty_route_id')
                            {
                                if(row.after_empty_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_empty_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'pricing_id')
                            {
                                if(row.after_pricing_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_pricing_er.title+'</a>';
                            }
                            else if(row.column_name == 'car_id' || row.column_name == 'trailer_id')
                            {
                                if(row.after_car_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_car_er.name+'</a>';
                            }
                            else if(row.column_name == 'driver_id')
                            {
                                if(row.after_driver_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_driver_er.driver_name+'</a>';
                            }

                            if(row.column_name == 'is_delay')
                            {
                                if(data == 1) return '正常';
                                else if(data == 9) return '压车';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                var $currentYear = new Date().getFullYear();
                                if($year == $currentYear)
                                {
                                    if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $month+'-'+$day;
                                }
                                else
                                {
                                    if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                }
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
                        "title": "操作时间",
                        "data": "created_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_record.on('click', '.modify-filter-submit', function () {
                ajax_datatable_record.ajax.reload();
            });

            dt_record.on('click', '.modify-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_record.ajax.reload();
            });


//            dt_record.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_record
        }
    }();
//        $(function () {
//            TableDatatablesAjax_record.init();
//        });
</script>
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.order-script')
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.order-script-for-info')
@include(env('TEMPLATE_YH_ADMIN').'entrance.item.order-script-for-finance')

@include(env('TEMPLATE_YH_ADMIN').'component.order-create-script')
@endsection
