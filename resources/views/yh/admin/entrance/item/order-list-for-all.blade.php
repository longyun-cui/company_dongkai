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


            <div class="box-body datatable-body item-main-body" id="datatable-for-order-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" />

                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="派车时间" readonly="readonly" />

                        <select class="form-control form-filter" name="order-staff" style="width:96px;">
                            <option value ="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-client" style="width:96px;">
                            <option value ="-1">选择客户</option>
                            @foreach($client_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        {{--<select class="form-control form-filter" name="order-car" style="width:96px;">--}}
                            {{--<option value ="-1">选择车辆</option>--}}
                            {{--@foreach($car_list as $v)--}}
                                {{--<option value ="{{ $v->id }}">{{ $v->name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                        <select class="form-control form-filter order-list-select2-car" name="order-car" style="width:96px;">
                            <option value="-1">选择车辆</option>
                        </select>

                        <select class="form-control form-filter" name="order-route" style="width:96px;">
                            <option value="-1">选择线路</option>
                            @foreach($route_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-pricing" style="width:96px;">
                            <option value="-1">选择定价</option>
                            @foreach($pricing_list as $v)
                                <option value ="{{ $v->id }}">{{ $v->title }}</option>
                            @endforeach
                        </select>

                        <select class="form-control form-filter" name="order-type" style="width:96px;">
                            <option value ="-1">订单类型</option>
                            <option value ="1">自有</option>
                            <option value ="11">空单</option>
                            <option value ="41">外配·配货</option>
                            <option value ="61">外请·调车</option>
                            {{--<option value ="已结束">已结束</option>--}}
                        </select>

                        <select class="form-control form-filter" name="order-status" style="width:96px;">
                            <option value ="-1">订单状态</option>
                            <option value ="未发布">未发布</option>
                            <option value ="待发车">待发车</option>
                            <option value ="进行中">进行中</option>
                            <option value ="已到达">已到达</option>
                            {{--<option value ="已结束">已结束</option>--}}
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-order">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-order">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped- table-bordered table-hover order-column' id='datatable_ajax'>
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

                <div class="form-group">

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
                            <select class="form-control select2-client" name="info-select-set-column-value" style="width:240px;" id="">
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



    {{--回单状态--}}
    <div id="receipt_status-option-list">
        <option value="-1">选择回单状态</option>
        <option value="1">等待回单</option>
        <option value="21">邮寄中</option>
        <option value="41">已签收，等待确认</option>
        <option value="100">已完成</option>
        <option value="101">回单异常</option>
    </div>

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
                            <input type="text" class="form-control" name="finance-create-transaction-title" placeholder="费用名目" value="">
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
                    <div class="form-group">
                        <label class="control-label col-md-2">支付方式</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-type" placeholder="支付方式" value="">
                        </div>
                    </div>
                    {{--收款账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">收款账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-receipt-account" placeholder="收款账号" value="">
                        </div>
                    </div>
                    {{--支出账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">支出账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-payment-account" placeholder="支出账号" value="">
                        </div>
                    </div>
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
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
<style>
    .tableArea table { min-width:4800px; }
    .tableArea table#datatable_ajax_finance { min-width:1600px; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
</style>
@endsection



@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[12, 50, 100, 200], ["12", "50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/order-list-for-all') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="order-id"]').val();
                        d.assign = $('input[name="order-assign"]').val();
                        d.name = $('input[name="order-name"]').val();
                        d.title = $('input[name="order-title"]').val();
                        d.keyword = $('input[name="order-keyword"]').val();
                        d.staff = $('select[name="order-staff"]').val();
                        d.client = $('select[name="order-client"]').val();
                        d.car = $('select[name="order-car"]').val();
                        d.route = $('select[name="order-route"]').val();
                        d.pricing = $('select[name="order-pricing"]').val();
                        d.status = $('select[name="order-status"]').val();
                        d.order_type = $('select[name="order-type"]').val();
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
                    "leftColumns": 7,
                    "rightColumns": 1
                },
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
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
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
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
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

                            if(row.item_status == 97)
                            {
                                return '<small class="btn-xs bg-navy">已弃用</small>';
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
                            else if(row.travel_status == "已完成")
                            {
                                $travel_status_html = '<small class="btn-xs bg-grey">已完成</small>';
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
                        "width": "100px",
                        "title": "派车日期",
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
                        "className": "text-center",
                        "width": "100px",
                        "title": "客户",
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
                        "className": "text-center",
                        "width": "120px",
                        "title": "线路",
                        "data": "route_type",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(data == 1)
                                {
                                    $(nTd).addClass('modal-show-for-info-select2-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','固定线路');
                                    $(nTd).attr('data-key','route_id').attr('data-value',row.route_id);
                                    if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                                    else $(nTd).attr('data-option-name',row.route_er.title);
                                    $(nTd).attr('data-column-name','固定线路');
                                    if(row.client_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                                else if(data == 11)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                                    $(nTd).attr('data-key','route_temporary').attr('data-value',row.route_temporary);
                                    if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                                    $(nTd).attr('data-column-name','临时线路');
                                    if(row.client_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 1)
                            {
                                if(row.route_er == null) return '--';
                                else return '<a href="javascript:void(0);">'+row.route_er.title+'</a>';
                            }
                            else if(data == 11)
                            {
                                return '[临]'+ row.route_temporary;
                            }
                            else return '有误';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "车辆",
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
                        "className": "text-center",
                        "width": "80px",
                        "title": "车挂",
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
                        "className": "",
                        "width": "60px",
                        "title": "主驾",
                        "data": "driver_name",
                        "orderable": false,
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
                            if(data) return data;
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.car_er != null) return row.car_er.linkman_name;
                                else return data;
                            }
                            else return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "100px",
                        "title": "主驾电话",
                        "data": "driver_phone",
                        "orderable": false,
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
                            if(data) return data;
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                if(row.car_er != null) return row.car_er.linkman_phone;
                                else return data;
                            }
                            else return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "出发地",
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "经停地",
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "目的地",
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "里程",
                        "data": "travel_distance",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','里程');
                                $(nTd).attr('data-key','travel_distance').attr('data-value',data);
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
                        "className": "text-center",
                        "width": "60px",
                        "title": "时效",
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
                        "className": "text-center",
                        "width": "100px",
                        "title": "订单类型",
                        "data": "car_owner_type",
                        "orderable": false,
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


                            if(row.is_completed == 1)
                            {
                                $travel_result_html = '<small class="btn-xs bg-grey">已结束</small>';
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
                        "className": "",
                        "width": "50px",
                        "title": "应收款",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-green');
                                $(nTd).addClass('item-show-for-finance');
                                $(nTd).attr('data-id',row.id).attr('data-type','all');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0)
                            {
                                return '--';
                            }
                            var $receivable = parseInt(row.amount) + parseInt(row.oil_card_amount) - parseInt(row.time_limitation_deduction);
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
                                    $(nTd).addClass('item-show-for-finance');
                                    $(nTd).attr('data-id',row.id).attr('data-type','all');
                                }
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            var $to_be_collected = parseInt(row.amount) + parseInt(row.oil_card_amount) - parseInt(row.time_limitation_deduction) - parseInt(row.income_total);
                            return $to_be_collected;
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
                                $(nTd).addClass('item-show-for-finance-income');
                                $(nTd).attr('data-id',row.id).attr('data-type','income');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "待入账",
                        "data": "income_to_be_confirm",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-red _bold');
                                $(nTd).addClass('item-show-for-finance-income');
                                $(nTd).attr('data-id',row.id).attr('data-type','income');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
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
                                $(nTd).addClass('item-show-for-finance-expenditure');
                                $(nTd).attr('data-id',row.id).attr('data-type','expenditure');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "待出账",
                        "data": "expenditure_to_be_confirm",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_published != 0)
                            {
                                $(nTd).addClass('color-red _bold');
                                $(nTd).addClass('item-show-for-finance-income');
                                $(nTd).attr('data-id',row.id).attr('data-type','income');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.is_published == 0) return '--';
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
                            if(row.is_published == 0) return '--';
                            var $profit = 0;
                            $profit = parseInt(row.income_total) - parseInt(row.expenditure_total);
                            return $profit;
                        }
                    },
                    {
                        "className": "_bold",
                        "width": "50px",
                        "title": "运价",
                        "data": "amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','运价');
                                $(nTd).attr('data-key','amount').attr('data-value',data);
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
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "油卡",
                        "data": "oil_card_amount",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','油卡');
                                $(nTd).attr('data-key','oil_card_amount').attr('data-value',data);
                                $(nTd).attr('data-column-name','油卡');
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
                        "className": "text-center",
                        "width": "120px",
                        "title": "包油价",
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
                                return '<a href="javascript:void(0);">'+row.pricing_er.title+'</a>';
                            }
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "请车价",
                        "data": "outside_car_price",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','请车价');
                                $(nTd).attr('data-key','outside_car_price').attr('data-value',data);
                                $(nTd).attr('data-column-name','请车价');
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
                        "title": "时效扣款",
                        "data": "time_limitation_deduction",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','时效扣款');
                                $(nTd).attr('data-key','time_limitation_deduction').attr('data-value',data);
                                $(nTd).attr('data-column-name','时效扣款');
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
                        "width": "50px",
                        "title": "信息费",
                        "data": "information_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','信息费');
                                $(nTd).attr('data-key','information_fee').attr('data-value',data);
                                $(nTd).attr('data-column-name','信息费');
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
                        "width": "50px",
                        "title": "客管费",
                        "data": "customer_management_fee",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','客户管理费');
                                $(nTd).attr('data-key','customer_management_fee').attr('data-value',data);
                                $(nTd).attr('data-column-name','客户管理费');
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
//                        "width": "50px",
//                        "title": "开票额",
//                        "data": "invoice_amount",
//                        "orderable": false,
//                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                            if(row.is_published != 0 && row.item_status != 97)
//                            {
//                                $(nTd).addClass('modal-show-for-info-text-set');
//                                $(nTd).attr('data-id',row.id).attr('data-name','开票额');
//                                $(nTd).attr('data-key','invoice_amount').attr('data-value',data);
//                                $(nTd).attr('data-column-name','开票额');
//                                $(nTd).attr('data-text-type','text');
//                                if(data) $(nTd).attr('data-operate-type','edit');
//                                else $(nTd).attr('data-operate-type','add');
//                            }
//                        },
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "className": "",
                        "width": "50px",
                        "title": "票点",
                        "data": "invoice_point",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','票点');
                                $(nTd).attr('data-key','invoice_point').attr('data-value',data);
                                $(nTd).attr('data-column-name','票点');
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
//                    {
//                        "width": "120px",
//                        "title": "线路",
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
//                        "className": "text-center",
//                        "width": "120px",
//                        "title": "行程",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
////                            return data == null ? '--' : data;
//                            var $stopover_html = '';
//                            if(row.stopover_place) $stopover_html = '--' + row.stopover_place;
//                            return row.departure_place + $stopover_html + '--' + row.destination_place;
//                        }
//                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "副驾",
                        "data": "copilot_name",
                        "orderable": false,
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
                        "className": "",
                        "width": "100px",
                        "title": "副驾电话",
                        "data": "copilot_phone",
                        "orderable": false,
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
                        "className": "text-center",
                        "width": "40px",
                        "title": "类型",
                        "data": "id",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂类型');
                                    $(nTd).attr('data-key','trailer_type').attr('data-value',row.trailer_type);
                                    $(nTd).attr('data-column-name','车挂类型');
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
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂尺寸');
                                    $(nTd).attr('data-key','trailer_length').attr('data-value',row.trailer_length);
                                    $(nTd).attr('data-column-name','车挂尺寸');
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
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂容积');
                                    $(nTd).attr('data-key','trailer_volume').attr('data-value',row.trailer_volume);
                                    $(nTd).attr('data-column-name','车挂容积');
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
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂载重');
                                    $(nTd).attr('data-key','trailer_weight').attr('data-value',row.trailer_weight);
                                    $(nTd).attr('data-column-name','车挂载重');
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
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                if(row.car_owner_type == 61)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','车挂轴数');
                                    $(nTd).attr('data-key','trailer_axis_count').attr('data-value',row.trailer_axis_count);
                                    $(nTd).attr('data-column-name','车挂轴数');
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
                        "className": "order-info-time-edit",
                        "width": "120px",
                        "title": "应出发时间",
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "应到达时间",
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "实际出发",
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "经停点-到达时间",
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "经停点-出发时间",
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
                        "className": "font-12px",
                        "width": "120px",
                        "title": "实际到达",
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
                        "className": "",
                        "width": "120px",
                        "title": "单号",
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
                    {
                        "className": "",
                        "width": "80px",
                        "title": "收款人",
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
                        "className": "",
                        "width": "100px",
                        "title": "车货源",
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
                        "className": "",
                        "width": "80px",
                        "title": "安排人",
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
                        "className": "",
                        "width": "80px",
                        "title": "车辆负责人",
                        "data": "car_managerial_people",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车辆负责人');
                                $(nTd).attr('data-key','car_managerial_people').attr('data-value',data);
                                $(nTd).attr('data-column-name','车辆负责人');
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
                        "width": "80px",
                        "title": "重量",
                        "data": "weight",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','重量');
                                $(nTd).attr('data-key','weight').attr('data-value',data);
                                $(nTd).attr('data-column-name','重量');
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
                        "width": "80px",
                        "title": "GPS",
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
                        "className": "",
                        "width": "100px",
                        "title": "是否回单",
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
                        "className": "",
                        "width": "100px",
                        "title": "回单地址",
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
                        "className": "",
                        "width": "100px",
                        "title": "回单状态",
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
                                else if(data == 21) return '<small class="btn-xs bg-blue">邮寄中</small>';
                                else if(data == 41) return '<small class="btn-xs bg-blue">已签收</small>';
                                else if(data == 100) return '<small class="btn-xs bg-olive">已完成</small>';
                                else if(data == 101) return '<small class="btn-xs bg-red">回单异常</small>';
                            }
                            else return '--';

                        }
                    },
                    {
                        "className": "",
                        "width": "100px",
                        "title": "附件",
                        "data": "attachment_list",
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
                            if(!data) return '--';
                            if(data.length == 0) return '--';
                            else if(data.length > 0) return '<small class="btn-xs bg-purple">有附件</small>';
                            else return '--';
//                            var html = '';
//                            $.each(data,function( key, val ) {
////                                console.log( key, val, this );
//                                html += '<a target="_blank" href="/people?id='+this.id+'">'+this.attachment_name+'</a><br>';
//                            });
//                            return html;
                        }
                    },
                    {
                        "className": "",
                        "width": "200px",
                        "title": "备注",
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
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "修改时间",
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
                        "className": "text-center",
                        "width": "160px",
                        "title": "操作",
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
                                $html_delete = '<a class="btn btn-xs bg-gray item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_detail = '<a class="btn btn-xs bg-primary item-modal-show-for-detail" data-id="'+data+'">详情</a>';
//                                $html_travel = '<a class="btn btn-xs bg-olive item-modal-show-for-travel" data-id="'+data+'">行程</a>';
                                $html_finance = '<a class="btn btn-xs bg-orange item-modal-show-for-finance" data-id="'+data+'">财务</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                                if(row.is_completed == 1)
                                {
                                    $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                    $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                }
                                else
                                {
                                    var $to_be_collected = parseInt(row.amount) + parseInt(row.oil_card_amount) - parseInt(row.time_limitation_deduction) - parseInt(row.income_total);
                                    if($to_be_collected > 0)
                                    {
                                        $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                    }
                                    else $html_completed = '<a class="btn btn-xs bg-blue item-complete-submit" data-id="'+data+'">完成</a>';

                                    if(row.item_status == 97)
                                    {
                                        $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                    }
                                    else $html_abandon = '<a class="btn btn-xs bg-gray item-abandon-submit" data-id="'+data+'">弃用</a>';
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

                                    if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id)
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
                        "width": "80px",
                        "title": "确认者",
                        "data": "confirmer_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.confirmer == null ? '' : '<a href="javascript:void(0);">'+row.confirmer.true_name+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
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
                            if((data > 0) && (data <= 10)) return '<samll class="text-red">'+data+'</samll>';
                            else return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "费用名目",
                        "data": "title",
                        "orderable": false,
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
                                else if(data == "car_id") return '车辆';
                                else if(data == "route_id") return '固定线路';
                                else if(data == "pricing_id") return '包油价';
                                else if(data == "trailer_id") return '车挂';
                                else if(data == "outside_car") return '车辆';
                                else if(data == "outside_trailer") return '车挂';
                                else if(data == "travel_distance") return '里程数';
                                else if(data == "time_limitation_prescribed") return '时效';
                                else if(data == "amount") return '金额';
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
                                else if(data == "trailer_type") return '车挂类型';
                                else if(data == "trailer_length") return '车挂尺寸';
                                else if(data == "trailer_volume") return '车挂容积';
                                else if(data == "trailer_weight") return '车辆载重';
                                else if(data == "trailer_axis_count") return '轴数';
                                else if(data == "GPS") return 'GPS';
                                else if(data == "receipt_address") return '回单地址';
                                else if(data == "receipt_status") return '回单状态';
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
                            else if(row.column_name == 'route_id')
                            {
                                if(row.before_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_route_er.title+'</a>';
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
                            else if(row.column_name == 'route_id')
                            {
                                if(row.after_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_route_er.title+'</a>';
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
                        "width": "80px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
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
