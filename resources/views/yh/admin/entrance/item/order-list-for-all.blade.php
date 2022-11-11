@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
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
                    <a href="{{ url('/item/order-create') }}">
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


            <div class="box-body datatable-body item-main-body" id="item-main-body">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="id" placeholder="ID" />

                        <select class="form-control form-filter" name="staff" style="width:96px;">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="client" style="width:96px;">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="car" style="width:96px;">
                            <option value ="-1">选择车辆</option>
                            @foreach($car_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-status" style="width:96px;">
                            <option value ="-1">订单状态</option>
                            <option value ="0">未发布</option>
                            <option value ="1">待发车</option>
                            <option value ="9">进行中</option>
                            <option value ="81">已到达</option>
                            <option value ="100">已结束</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped- table-bordered table-hover' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
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
<div class="modal fade modal-main-body" id="modal-info-detail-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
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
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>


{{--修改-基本信息--}}
<div class="modal fade modal-main-body" id="modal-info-set-body">
    <div class="col-md-6 col-md-offset-3" id="info-edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">修改订单【<span class="info-set-title"></span>】</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-set-form">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="info-set-operate" value="item-order-info-set" readonly>
                            <input type="hidden" name="info-set-order-id" value="0" readonly>
                            <input type="hidden" name="info-set-operate-type" value="add" readonly>
                            <input type="hidden" name="info-set-column-key" value="" readonly>


                            <div class="form-group">
                                <label class="control-label col-md-2 info-set-column-name"></label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control" name="info-set-column-value" autocomplete="off" placeholder="" value="">
                                </div>
                            </div>


                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="btn btn-success" id="item-info-set-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-info-set-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>
{{--修改-时间信息--}}
<div class="modal fade modal-main-body" id="modal-info-time-set-body">
    <div class="col-md-6 col-md-offset-3" id="info-time-edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">修改订单【<span class="info-time-set-title"></span>】</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="info-time-set-operate" value="item-order-info-set" readonly>
                            <input type="hidden" name="info-time-set-order-id" value="0" readonly>
                            <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                            <input type="hidden" name="info-time-set-column-key" value="" readonly>
                            <input type="hidden" name="info-time-set-time-type" value="" readonly>


                            <div class="form-group">
                                <label class="control-label col-md-2 info-time-set-column-name"></label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime">
                                    <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date">
                                </div>
                            </div>


                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="btn btn-success" id="item-info-time-set-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-info-time-set-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>




{{--行程记录--}}
<div class="modal fade modal-main-body" id="modal-travel-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">行程记录</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-travel-set-modal">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="operate" value="order-travel-set" readonly>
                            <input type="hidden" name="order_id" value="0" readonly>

                            {{--应出发时间--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">应出发时间</label>
                                <div class="col-md-8 ">
                                    <div><b class="item-travel-should-departure-time"></b></div>
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
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>

{{--设置行程时间--}}
<div class="modal fade modal-main-body" id="modal-travel-set-body">
    <div class="col-md-4 col-md-offset-4" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">设置行程时间</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered " id="modal-travel-set-form">
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
                                <button type="button" class="btn btn-success" id="item-travel-set-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-travel-set-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>




{{--财务记录--}}
<div class="modal fade modal-main-body" id="modal-finance-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn-" style="background:#fff;">
        <div class="box box-info- form-container datatable-body item-main-body" id="item-content-body">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">财务记录</h3>
                <div class="caption">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right item-finance-create-show"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body item-main-body" id="item-main-body">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="finance-title" placeholder="费用类型" />

                        <select class="form-control form-filter" name="finance-item_type" style="width:96px;">
                            <option value ="-1">选择</option>
                            <option value ="1">收入</option>
                            <option value ="11">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success finance-filter-submit" id="finance-filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default finance-filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

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

{{--添加财务记录--}}
<div class="modal fade modal-main-body" id="modal-finance-create-body">
    <div class="col-md-4 col-md-offset-4" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">添加财务记录</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered " id="modal-finance-create-form">
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
                                    <input type="text" class="form-control form-filter form_date" name="finance-create-transaction-date" />
                                </div>
                            </div>
                            {{--费用说明--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">费用类型</label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control" name="finance-create-transaction-title" placeholder="费用类型" value="">
                                </div>
                            </div>
                            {{--金额--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">金额</label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control" name="finance-create-transaction-amount" placeholder="金额" value="">
                                </div>
                            </div>
                            {{--支付方式--}}
                            <div class="form-group income-show-">
                                <label class="control-label col-md-2">支付方式</label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control" name="finance-create-transaction-type" placeholder="支付方式" value="">
                                </div>
                            </div>
                            {{--交易账号--}}
                            <div class="form-group income-show-">
                                <label class="control-label col-md-2">交易账号</label>
                                <div class="col-md-8 ">
                                    <input type="text" class="form-control" name="finance-create-transaction-account" placeholder="交易账号" value="">
                                </div>
                            </div>
                            {{--收款账号--}}
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
                                <button type="button" class="btn btn-success" id="item-finance-create-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-finance-create-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>




{{--修改记录--}}
<div class="modal fade modal-main-body" id="modal-modify-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn-" style="background:#fff;">
        <div class="box box-info- form-container datatable-body item-main-body" id="item-content-body-">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">修改记录</h3>
                <div class="caption">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right item-finance-create-show"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body item-main-body" id="item-main-body">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="attribute" style="width:96px;">
                            <option value ="-1">选择属性</option>
                            <option value ="amount">金额</option>
                            <option value ="11">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success modify-filter-submit" id="modify-filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default modify-filter-cancel">
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




@section('custom-style')
<style>
    .tableArea table {
        min-width: 4400px;
    }
</style>
@endsection




@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50, 100, 200], ["50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/order-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="id"]').val();
                        d.keyword = $('input[name="keyword"]').val();
                        d.website = $('input[name="website"]').val();
                        d.staff = $('select[name="staff"]').val();
                        d.client = $('select[name="client"]').val();
                        d.car = $('select[name="car"]').val();
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
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
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "className": "",
                        "width": "40px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        },
                    },
                    {
                        "width": "180px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_detail = '';
                            var $html_travel = '';
                            var $html_finance = '';
                            var $html_record = '';
                            var $html_delete = '';

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
                                $html_delete = '<a class="btn btn-xs bg-black item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_publish = '<a class="btn btn-xs btn-default disabled">发布</a>';
                                $html_publish = '';
                                $html_edit = '<a class="btn btn-xs btn-default disabled">编辑</a>';
                                $html_edit = '';
                                $html_detail = '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">详情</a>';
                                $html_travel = '<a class="btn btn-xs bg-olive item-travel-show" data-id="'+data+'">行程</a>';
                                $html_finance = '<a class="btn btn-xs bg-orange item-finance-show" data-id="'+data+'">财务</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-record-show" data-id="'+data+'">记录</a>';
                            }

//                            if(row.deleted_at == null)
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
//                            }
//                            else
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
//                            }

                            var html =
//                                    $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                                $html_edit+
                                $html_publish+
//                                $html_detail+
                                $html_travel+
                                $html_finance+
                                $html_record+
                                $html_delete+
//                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                    '';
                            return html;

                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "订单状态",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;

                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(row.is_published == 0)
                            {
                                return '<small class="btn-xs bg-teal">未发布</small>';
                            }

                            var $travel_status_html = '';
                            var $travel_result_html = '';
                            var $travel_result_time = '';
//
                            if(row.travel_status == "待发车")
                            {
                                $travel_status_html = '<small class="btn-xs bg-aqua">待发车</small>';
                            }
                            else if(row.travel_status == "进行中")
                            {
                                $travel_status_html = '<small class="btn-xs bg-blue">进行中</small>';
                            }
                            else if(row.travel_status == "已到达")
                            {
                                $travel_status_html = '<small class="btn-xs bg-olive">已到达</small>';
                            }
                            else if(row.travel_status == "待收账")
                            {
                                $travel_status_html = '<small class="btn-xs bg-orange">待收账</small>';
                            }
                            else if(row.travel_status == "已收账")
                            {
                                $travel_status_html = '<small class="btn-xs bg-maroon">已收账</small>';
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
                        "className": "",
                        "width": "80px",
                        "title": "状态",
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

                            return $travel_status_html + $travel_result_html;

                        }
                    },
                    {
                        "className": "text-center",
                        "width": "200px",
                        "title": "行程",
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "客户",
                        "data": "client_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return row.client_er == null ? '未知' : '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.short_name+'</a>';
                            if(row.client_er == null) return '未知';
                            else {
                                if(row.client_er.short_name)
                                {
                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.short_name+'</a>';
                                }
                                else
                                {
                                    return '<a target="_blank" href="/user/'+row.client_er.id+'">'+row.client_er.username+'</a>';
                                }
                            }
                        }
                    },
                    {
                        "className": "",
                        "width": "100px",
                        "title": "派车日期",
                        "data": 'assign_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                                $(nTd).attr('data-name','派车日期');
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
                        "width": "50px",
                        "title": "运价",
                        "data": "amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','amount').attr('data-value',data);
                                $(nTd).attr('data-name','运价');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "50px",
                        "title": "油卡",
                        "data": "oil_card_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','oil_card_amount').attr('data-value',data);
                                $(nTd).attr('data-name','油卡');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "width": "50px",
//                        "title": "开票额",
//                        "data": "invoice_amount",
//                        "orderable": false,
//                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
//                            {
//                                $(nTd).addClass('order-info-set-text');
//                                $(nTd).attr('data-id',row.id).attr('data-key','invoice_amount').attr('data-value',data);
//                                $(nTd).attr('data-name','开票额');
//                                if(data) $(nTd).attr('data-operate-type','edit');
//                                else $(nTd).attr('data-operate-type','add');
//                            }
//                        },
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "width": "50px",
                        "title": "票点",
                        "data": "invoice_point",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','invoice_point').attr('data-value',data);
                                $(nTd).attr('data-name','票点');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "50px",
                        "title": "信息费",
                        "data": "information_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','information_fee').attr('data-value',data);
                                $(nTd).attr('data-name','信息费');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "50px",
                        "title": "客管费",
                        "data": "customer_management_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','customer_management_fee').attr('data-value',data);
                                $(nTd).attr('data-name','客户管理费');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "60px",
                        "title": "时效扣款",
                        "data": "time_limitation_deduction",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','time_limitation_deduction').attr('data-value',data);
                                $(nTd).attr('data-name','时效扣款');
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
                        "width": "50px",
                        "title": "应收款",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-green');
                                $(nTd).attr('data-id',row.id).attr('data-type','');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $receivable = parseInt(row.amount) + parseInt(row.oil_card_amount) - parseInt(row.information_fee) - parseInt(row.time_limitation_deduction);
                            return $receivable;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "欠款",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $receivable = parseInt(row.amount) - parseInt(row.time_limitation_deduction) - parseInt(row.income_total);
                                if($receivable > 0)
                                {
                                    $(nTd).addClass('color-red _bold');
                                    $(nTd).attr('data-id',row.id).attr('data-type','');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $receivable = parseInt(row.amount) - parseInt(row.time_limitation_deduction) - parseInt(row.income_total);
                            return $receivable;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "已收款",
                        "data": "income_total",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-blue');
                                $(nTd).addClass('item-finance-income-show');
                                $(nTd).attr('data-id',row.id).attr('data-type','income');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "已支出",
                        "data": "expenditure_total",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-purple');
                                $(nTd).addClass('item-finance-expenditure-show');
                                $(nTd).attr('data-id',row.id).attr('data-type','expenditure');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "利润·实时",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                var $profit = parseInt(row.income_total) - parseInt(row.expenditure_total);
                                if($profit > 0) $(nTd).addClass('color-green');
                                else if($profit < 0) $(nTd).addClass('color-red');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $profit = 0;
                            $profit = parseInt(row.income_total) - parseInt(row.expenditure_total);
                            return $profit;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "里程",
                        "data": "travel_distance",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '';
                            else return data;
                        }
                    },
//                    {
//                        "className": "text-center",
//                        "width": "80px",
//                        "title": "需求类型",
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
                    {
                        "width": "120px",
                        "title": "线路",
                        "data": "route",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','route').attr('data-value',data);
                                $(nTd).attr('data-name','线路');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "width": "120px",
//                        "title": "固定线路",
//                        "data": "fixed_route",
//                        "orderable": false,
//                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
//                            {
//                                $(nTd).addClass('order-info-set-text');
//                                $(nTd).attr('data-id',row.id).attr('data-key','fixed_route').attr('data-value',data);
//                                $(nTd).attr('data-name','固定线路');
//                                if(data) $(nTd).attr('data-operate-type','edit');
//                                else $(nTd).attr('data-operate-type','add');
//                            }
//                        },
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
//                    {
//                        "width": "120px",
//                        "title": "临时线路",
//                        "data": "temporary_route",
//                        "orderable": false,
//                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0)
//                            {
//                                $(nTd).addClass('order-info-set-text');
//                                $(nTd).attr('data-id',row.id).attr('data-key','temporary_route').attr('data-value',data);
//                                $(nTd).attr('data-name','临时线路');
//                                if(data) $(nTd).attr('data-operate-type','edit');
//                                else $(nTd).attr('data-operate-type','add');
//                            }
//                        },
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "行程",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data == null ? '--' : data;
                            var $stopover_html = '';
                            if(row.stopover_place) $stopover_html = '--' + row.stopover_place;
                            return row.departure_place + $stopover_html + '--' + row.destination_place;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "类型",
                        "data": "car_owner_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1)
                            {
                                return '<small class="btn-xs bg-green">自有</small>';
                            }
                            else if(data == 21)
                            {
                                return '<small class="btn-xs bg-blue">外请·调车</small>';
                            }
                            else if(data == 41)
                            {
                                return '<small class="btn-xs bg-purple">外配·配货</small>';
                            }
                            else return "有误";
                        }
                    },
                    {
                        "className": "text-left font-12px",
                        "width": "80px",
                        "title": "车辆",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','outside_car').attr('data-value',row.outside_car);
                                    $(nTd).attr('data-name','车辆');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var car_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.car_er != null) car_html = '<a href="javascript:void(0);">'+row.car_er.name+'</a>';
                            }
                            else
                            {
                                if(row.outside_car) car_html = row.outside_car;
                            }
                            return car_html;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "80px",
                        "title": "车挂",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','outside_trailer').attr('data-value',row.outside_trailer);
                                    $(nTd).attr('data-name','车挂');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var trailer_html = '';
                            if(row.car_owner_type == 1)
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
                        "width": "60px",
                        "title": "主驾",
                        "data": "driver_name",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','主驾姓名').attr('data-key','driver_name').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "100px",
                        "title": "主驾电话",
                        "data": "driver_phone",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','主驾电话').attr('data-key','driver_phone').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "60px",
                        "title": "副驾",
                        "data": "copilot_name",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','副驾姓名').attr('data-key','copilot_name').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "100px",
                        "title": "副驾电话",
                        "data": "copilot_phone",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','副驾电话').attr('data-key','copilot_phone').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "类型",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','trailer_type').attr('data-value',row.trailer_type);
                                    $(nTd).attr('data-name','车挂类型');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var reurn_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.trailer_er != null && row.trailer_er.trailer_type) reurn_html = row.trailer_er.trailer_type;
                            }
                            else
                            {
                                if(row.trailer_type && row.trailer_type != 0) reurn_html = row.trailer_type;
                            }
                            return reurn_html;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "尺寸",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','trailer_length').attr('data-value',row.trailer_length);
                                    $(nTd).attr('data-name','车挂尺寸');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var reurn_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.trailer_er != null && row.trailer_er.trailer_length) reurn_html = row.trailer_er.trailer_length;
                            }
                            else
                            {
                                if(row.trailer_length && row.trailer_length != 0) reurn_html = row.trailer_length;
                            }
                            return reurn_html;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "容积",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','trailer_volume').attr('data-value',row.trailer_volume);
                                    $(nTd).attr('data-name','车挂容积');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.trailer_er != null && row.trailer_er.trailer_volume) $return_html = row.trailer_er.trailer_volume;
                            }
                            else
                            {
                                if(row.trailer_volume && row.trailer_volume != 0) $return_html = row.trailer_volume;
                            }
                            return $return_html;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "载重",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','trailer_weight').attr('data-value',row.trailer_weight);
                                    $(nTd).attr('data-name','车挂载重');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.trailer_er != null && row.trailer_er.trailer_weight) $return_html = row.trailer_er.trailer_weight;
                            }
                            else
                            {
                                if(row.trailer_weight && row.trailer_weight != 0) $return_html = row.trailer_weight;
                            }
                            return $return_html;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "40px",
                        "title": "轴数",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                if(row.car_owner_type > 1)
                                {
                                    $(nTd).addClass('order-info-set-text');
                                    $(nTd).attr('data-id',row.id).attr('data-key','trailer_axis_count').attr('data-value',row.trailer_axis_count);
                                    $(nTd).attr('data-name','车挂轴数');
                                    if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            var reurn_html = '';
                            if(row.car_owner_type == 1)
                            {
                                if(row.trailer_er != null && row.trailer_er.trailer_axis_count) reurn_html = row.trailer_er.trailer_axis_count;
                            }
                            else
                            {
                                if(row.trailer_axis_count) reurn_html = row.trailer_axis_count;
                            }
                            return reurn_html;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "60px",
                        "title": "出发地",
                        "data": "departure_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','departure_place').attr('data-value',data);
                                $(nTd).attr('data-name','出发地');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "60px",
                        "title": "目的地",
                        "data": "destination_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','destination_place').attr('data-value',data);
                                $(nTd).attr('data-name','目的地');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "60px",
                        "title": "经停地",
                        "data": "stopover_place",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-key','stopover_place').attr('data-value',data);
                                $(nTd).attr('data-name','经停地');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data == null ? '--' : data;
                        }
                    },
                    {
                        "className": "order-info-time-edit",
                        "width": "120px",
                        "title": "应出发时间",
                        "data": 'should_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','should_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','应出发时间');
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "应到达时间",
                        "data": 'should_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','should_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','应到达时间');
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "实际出发",
                        "data": 'actual_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','actual_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','实际出发时间');
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "经停点-到达时间",
                        "data": 'stopover_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','stopover_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','经停到达时间');
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "经停点-出发时间",
                        "data": 'stopover_departure_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','stopover_departure_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','经停出发时间');
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "实际到达",
                        "data": 'actual_arrival_time',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
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

                                $(nTd).addClass('order-info-set-time');
                                $(nTd).attr('data-id',row.id).attr('data-key','actual_arrival_time').attr('data-value',$time_value);
                                $(nTd).attr('data-name','实际到达时间');
                                $(nTd).attr('data-time-type','datetime');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published != 0)
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
//                    {
//                        "className": "font-12px",
//                        "width": "180px",
//                        "title": "行程时间",
//                        "data": 'id',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            var $should_departure_time_html = '';
//                            var $should_arrival_time_html = '';
//                            var $actual_departure_time_html = '';
//                            var $stopover_arrival_time_html = '';
//                            var $stopover_departure_time_html = '';
//                            var $actual_arrival_time_html = '';
//
//                            var $currentYear = new Date().getFullYear();
//
//
//                            // 经停到达时间
//                            if(row.stopover_arrival_time)
//                            {
//                                var $stopover_arrival_time = new Date(row.stopover_arrival_time*1000);
//                                var $stopover_arrival_year = $stopover_arrival_time.getFullYear();
//                                var $stopover_arrival_month = ('00'+($stopover_arrival_time.getMonth()+1)).slice(-2);
//                                var $stopover_arrival_day = ('00'+($stopover_arrival_time.getDate())).slice(-2);
//                                var $stopover_arrival_hour = ('00'+$stopover_arrival_time.getHours()).slice(-2);
//                                var $stopover_arrival_minute = ('00'+$stopover_arrival_time.getMinutes()).slice(-2);
//                                var $stopover_arrival_second = ('00'+$stopover_arrival_time.getSeconds()).slice(-2);
//
//                                if($stopover_arrival_year == $currentYear)
//                                {
//                                    $stopover_arrival_time_html = '<a href="javascript:void(0);">'+'(到达)'+'&nbsp;&nbsp;'+$stopover_arrival_month+'-'+$stopover_arrival_day+'&nbsp;'+$stopover_arrival_hour+':'+$stopover_arrival_minute+'</a>'+'<br>';
//                                }
//                                else
//                                {
//                                    $stopover_arrival_time_html = '<a href="javascript:void(0);">'+'(到达)'+'&nbsp;&nbsp;&nbsp;'+$stopover_arrival_year+'-'+$stopover_arrival_month+'-'+$stopover_arrival_day+'&nbsp;'+$stopover_arrival_hour+':'+$stopover_arrival_minute+'</a>'+'<br>';
//                                }
//                            }
//
//                            // 经停出发时间
//                            if(row.stopover_departure_time)
//                            {
//                                var $stopover_departure_time = new Date(row.stopover_departure_time*1000);
//                                var $stopover_departure_year = $stopover_departure_time.getFullYear();
//                                var $stopover_departure_month = ('00'+($stopover_departure_time.getMonth()+1)).slice(-2);
//                                var $stopover_departure_day = ('00'+($stopover_departure_time.getDate())).slice(-2);
//                                var $stopover_departure_hour = ('00'+$stopover_departure_time.getHours()).slice(-2);
//                                var $stopover_departure_minute = ('00'+$stopover_departure_time.getMinutes()).slice(-2);
//                                var $stopover_departure_second = ('00'+$stopover_departure_time.getSeconds()).slice(-2);
//
//                                if($stopover_arrival_year == $currentYear)
//                                {
//                                    $stopover_departure_time_html = '<a href="javascript:void(0);">'+'(出发)'+'&nbsp;&nbsp;'+$stopover_departure_month+'-'+$stopover_departure_day+'&nbsp;'+$stopover_departure_hour+':'+$stopover_departure_minute+'</a>'+'<br>';
//                                }
//                                else
//                                {
//                                    $stopover_departure_time_html = '<a href="javascript:void(0);">'+'(出发)'+'&nbsp;&nbsp;'+$stopover_departure_year+'-'+$stopover_departure_month+'-'+$stopover_departure_day+'&nbsp;'+$stopover_departure_hour+':'+$stopover_departure_minute+'</a>'+'<br>';
//                                }
//                            }
//
//                            return $should_departure_time_html + $should_arrival_time_html +
//                                $actual_departure_time_html + $stopover_arrival_time_html +
//                                $stopover_departure_time_html + $actual_arrival_time_html;
//                        }
//                    },
                    {
                        "width": "120px",
                        "title": "单号",
                        "data": "order_number",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','单号').attr('data-key','order_number').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "收款人",
                        "data": "payee_name",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','收款人').attr('data-key','payee_name').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "车货源",
                        "data": "car_supply",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','车货源').attr('data-key','car_supply').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "安排人",
                        "data": "arrange_people",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','安排人').attr('data-key','arrange_people').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "车辆负责人",
                        "data": "car_managerial_people",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','车辆负责人').attr('data-key','car_managerial_people').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "重量",
                        "data": "weight",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','重量').attr('data-key','weight').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "80px",
                        "title": "GPS",
                        "data": "GPS",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','GPS').attr('data-key','GPS').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "100px",
                        "title": "回单状态",
                        "data": "receipt_status",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','回单状态').attr('data-key','receipt_status').attr('data-value',data);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "100px",
                        "title": "回单地址",
                        "data": "receipt_address",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('order-info-set-text');
                                $(nTd).attr('data-id',row.id).attr('data-name','回单地址').attr('data-key','receipt_address').attr('data-value',data);
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
                        "title": "创建时间",
                        "data": 'created_at',
                        "orderable": true,
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
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    ajax_datatable.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
                    });
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
        TableDatatablesAjax.init();
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
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="keyword"]').val();
                        d.item_type = $('select[name="finance-item_type"]').val();
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
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
                        "className": "",
                        "width": "60px",
                        "title": "类型",
                        "data": "item_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.item_type == 1) return '<small class="btn-xs bg-olive">收入</small>';
                            else if(row.item_type == 21) return '<small class="btn-xs bg-orange">支出</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "费用说明",
                        "data": "title",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "金额",
                        "data": "transaction_amount",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if((data > 0) && (data <= 10)) return '<samll class="text-red">'+data+'</samll>';
                            else return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "支付方式",
                        "data": "transaction_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "交易账户",
                        "data": "transaction_account",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "交易单号",
                        "data": "transaction_order",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "说明",
                        "data": "description",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "支付时间",
                        "data": "transaction_time",
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
                            if($year == $currentYear) return $month+'-'+$day;
                            else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
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

                    ajax_datatable_finance.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{csrf_token()}}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
                    });

//                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
//                        checkboxClass: 'icheckbox_minimal-blue',
//                        radioClass   : 'iradio_minimal-blue'
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
                        d.searchengine = $('select[name="searchengine"]').val();
                        d.keyword = $('input[name="keyword"]').val();
                        d.website = $('input[name="website"]').val();
                        d.keywordstatus = $('select[name="keywordstatus"]').val();
                        d.rank = $('select[name="inner_rank"]').val();
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
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
                        "width": "32px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "32px",
                        "title": "类型",
                        "data": "operate_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                            else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "40px",
                        "title": "修改属性",
                        "data": "column",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == "amount") return '金额';
                            else if(data == "deposit") return '定金';
                            else if(data == "oil_card_amount") return '油卡';
                            else if(data == "invoice_amount") return '开票金额';
                            else if(data == "invoice_point") return '票点';
                            else if(data == "customer_management_fee") return '客户管理费';
                            else if(data == "information_fee") return '信息费';
                            else if(data == "time_limitation_deduction") return '时效扣款';
                            else if(data == "assign_time") return '安排时间';
                            else if(data == "container_type") return '箱型';
                            else if(data == "subordinate_company") return '所属公司';
                            else if(data == "route") return '路线';
                            else if(data == "fixed_route") return '固定路线';
                            else if(data == "temporary_route") return '临时路线';
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
                            else if(data == "outside_car") return '车辆';
                            else if(data == "outside_trailer") return '车挂';
                            else if(data == "trailer_type") return '车挂类型';
                            else if(data == "trailer_length") return '车挂尺寸';
                            else if(data == "trailer_volume") return '车挂容积';
                            else if(data == "trailer_weight") return '车辆载重';
                            else if(data == "trailer_axis_count") return '轴数';
                            else if(data == "receipt_status") return '回单状态';
                            else if(data == "receipt_address") return '回单地址';
                            else if(data == "GPS") return 'GPS';
                            else if(data == "order_number") return '单号';
                            else if(data == "payee_name") return '收款人';
                            else if(data == "arrange_people") return '安排人';
                            else if(data == "car_managerial_people") return '车辆负责员';
                            else if(data == "car_supply") return '车货源';
                            else if(data == "weight") return '重量';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "48px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {
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

                                    if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                }
                                else return '';
                            }
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "48px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                                else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                            }
                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "48px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "font-12px",
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
                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    },
                    {
                        "title": "操作",
                        'data': 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            var $date = row.transaction_date.trim().split(" ")[0];
                            var html = '';
//                                '<a class="btn btn-xs item-enable-submit" data-id="'+value+'">启用</a>'+
//                                '<a class="btn btn-xs item-disable-submit" data-id="'+value+'">禁用</a>'+
//                                '<a class="btn btn-xs item-download-qrcode-submit" data-id="'+value+'">下载二维码</a>'+
//                                '<a class="btn btn-xs item-statistics-submit" data-id="'+value+'">流量统计</a>'+
                                    {{--'<a class="btn btn-xs" href="/item/edit?id='+value+'">编辑</a>'+--}}
                                //                                '<a class="btn btn-xs item-edit-submit" data-id="'+value+'">编辑</a>'+
                            return html;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    ajax_datatable_record.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{csrf_token()}}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
                    });

//                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
//                        checkboxClass: 'icheckbox_minimal-blue',
//                        radioClass   : 'iradio_minimal-blue'
//                    });
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
@endsection
