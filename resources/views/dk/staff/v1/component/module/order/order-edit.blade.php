{{--编辑-工单--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order-item-edit">
    <div class="modal-content col-md-10 col-md-offset-1 margin-top-16px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加工单</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order-item-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="order" data-default="order">



                {{--自定义标题--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="">
                    </div>
                </div>

                {{--派车日期 & 任务日期--}}
                <div class="form-group" >
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 派车日期 & 任务日期</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date-picker" name="assign_date" placeholder="派车日期" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" readonly="readonly">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date-picker" name="task_date" placeholder="任务日期" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" readonly="readonly">
                        </div>
                    </div>
                </div>

                {{--订单类型--}}
                <div class="form-group form-category" style="height:70px;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 订单类型</label>
                    <div class="col-md-9">
                        <div class="btn-group">

                            <button type="button" class="btn radio-btn radio-department-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="order_type" value="1" checked="checked"> 固定线路
                                    </label>
                                </span>
                            </button>

                            <button type="button" class="btn radio-btn radio-department-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="order_type" value="11"> 配货
                                    </label>
                                </span>
                            </button>

                        </div>
                    </div>
                </div>

                {{--客户--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <select class="form-control select2--client" name="client_id" id="order-edit-select2-client-" style="width:100%;">--}}
{{--                            <option data-id="-1" value="-1">选择客户</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
                {{--项目--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                    <div class="col-md-9 ">
                        <select class="form-control select2--project" name="project_id" id="select2--project--for-order-item-edit">
                            <option data-id="" value="">选择项目</option>
                        </select>
                    </div>
                </div>
                {{--出发地 & 目的地--}}
                <div class="form-group" >
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 出发地 & 目的地</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="transport_departure_place" placeholder="出发地" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="transport_destination_place" placeholder="目的地" value="" data-default="">
                        </div>
                    </div>
                </div>
                {{--里程 & 时效--}}
                <div class="form-group" >
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 里程 & 时效</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="transport_distance" placeholder="里程" value="0" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="transport_time_limitation" placeholder="时效" value="0" data-default="">
                        </div>
                    </div>
                </div>

                {{--运费 & 油卡 & 信息费--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 运费 & 油卡 & 信息费</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="freight_amount" placeholder="金额" value="0" data-default="0">
                        </div>
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="oil_card_amount" placeholder="油卡" value="0" data-default="0">
                        </div>
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="information_fee" placeholder="信息费" value="0" data-default="0">
                        </div>
                    </div>
                </div>

                {{--车辆所属--}}
                <div class="form-group form-category" style="height:70px;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 车辆所属</label>
                    <div class="col-md-9">
                        <div class="btn-group">

                            <button type="button" class="btn radio-btn radio-car-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="car_owner_type" value="1" checked="checked"> 自有车
                                    </label>
                                </span>
                            </button>

                            <button type="button" class="btn radio-btn radio-department-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="car_owner_type" value="11"> 外请车
                                    </label>
                                </span>
                            </button>

                        </div>
                    </div>
                </div>

                {{--自有车辆--}}
                <div class="form-group internal-car">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自有车辆</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select2-reset select2--car"
                                    name="car_id"
                                    id="select2--car--for-order-item-edit"
                                    data-car-type="1"
                            >
                                <option value="0">选择车辆</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select2-reset select2--car"
                                    name="trailer_id"
                                    id="select2--trailer--for-order-item-edit"
                                    data-car-type="21"
                            >
                                <option value="0">选择车挂</option>
                            </select>
                        </div>
                    </div>
                </div>
                {{--自家司机--}}
                <div class="form-group internal-car">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自家司机</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select2-reset select2--driver"
                                    name="driver_id"
                                    id="select2--driver--for--order-item-edit"
                            >
                                <option value="0">选择主驾</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select2-reset select2--driver"
                                    name="copilot_id"
                                    id="select2--copilot--for--order-item-edit"
                            >
                                <option value="0">选择副驾</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{--外请车--}}
                <div class="form-group external-car" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外请车</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="external_car" placeholder="车辆" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="external_trailer" placeholder="车挂" value="" data-default="">
                        </div>
                    </div>
                </div>
                {{--外请司机--}}
                <div class="form-group external-car" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外请司机</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="driver_name" placeholder="主驾" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="driver_phone" placeholder="主驾电话" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="copilot_name" placeholder="副驾" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                            <input type="text" class="form-control" name="copilot_phone" placeholder="副驾电话" value="" data-default="">
                        </div>
                    </div>
                </div>

                {{--班次--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 班次</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <select class="form-control modal-select2 select2-reset" name="field_2" id="" style="width:100%;">--}}
{{--                            <option value="">选择班次</option>--}}
{{--                            <option value ="1">白班</option>--}}
{{--                            <option value ="9">夜班</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

                {{--安排人 & 收款人 & 车货源--}}
                <div class="form-group">
                    <label class="control-label col-md-2">安排人 & 收款人 & 车货源</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="arrange_people" placeholder="安排人" value="" data-default="">
                        </div>
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="payee_name" placeholder="收款人" value="" data-default="">
                        </div>
                        <div class="col-sm-4 col-md-4 padding-0">
                            <input type="text" class="form-control" name="car_supply" placeholder="车货源" value="" data-default="">
                        </div>
                    </div>
                </div>

                {{--安排人--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">安排人</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <input type="text" class="form-control" name="arrange_people" placeholder="安排人" value="" data-default="">--}}
{{--                    </div>--}}
{{--                </div>--}}
                {{--收款人--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">收款人</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <input type="text" class="form-control" name="payee_name" placeholder="收款人" value="" data-default="">--}}
{{--                    </div>--}}
{{--                </div>--}}
                {{--车货源--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">车货源</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <input type="text" class="form-control" name="car_supply" placeholder="车货源" value="" data-default="">--}}
{{--                    </div>--}}
{{--                </div>--}}


                {{--备注--}}
                <div class="form-group">
                    <label class="control-label col-md-2">备注</label>
                    <div class="col-md-9 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                    </div>
                </div>

                {{--通话小结--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                    <div class="col-md-9 ">
                        <p>要求：准确，全面，丰富</p>
                        <p>范本：用户当前3颗后槽牙齿缺失，已经缺失半年，2颗下牙松动，之前没了解过种牙，好说话，要求下午3点前回电，同意医生助理联系</p>
                    </div>
                </div>


                {{--班次--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 班次</label>--}}
{{--                    <div class="col-md-9 ">--}}
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
                    <div class="col-md-9 col-md-offset-3">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--order-item-edit">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>