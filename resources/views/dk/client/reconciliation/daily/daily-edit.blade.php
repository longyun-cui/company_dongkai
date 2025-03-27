{{--编辑-每日-结算--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-reconciliation-daily-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加项目</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-reconciliation-daily-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="reconciliation-daily" data-default="reconciliation-daily">




                {{--项目类型--}}
{{--                <div class="form-group form-category">--}}
{{--                    <label class="control-label col-md-2">项目种类</label>--}}
{{--                    <div class="col-md-8">--}}
{{--                        <div class="btn-group">--}}

{{--                            <button type="button" class="btn radio-btn radio-item-category">--}}
{{--                            <span class="radio">--}}
{{--                                <label>--}}
{{--                                    <input type="radio" name="item_category" value="1" checked="checked"> 口腔--}}
{{--                                </label>--}}
{{--                            </span>--}}
{{--                            </button>--}}

{{--                            <button type="button" class="btn radio-btn radio-item-category">--}}
{{--                            <span class="radio">--}}
{{--                                <label>--}}
{{--                                    <input type="radio" name="item_category" value="11"> 医美--}}
{{--                                </label>--}}
{{--                            </span>--}}
{{--                            </button>--}}

{{--                            <button type="button" class="btn radio-btn radio-item-category">--}}
{{--                            <span class="radio">--}}
{{--                                <label>--}}
{{--                                    <input type="radio" name="item_category" value="31"> 二手奢侈品--}}
{{--                                </label>--}}
{{--                            </span>--}}
{{--                            </button>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}


                {{--项目名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-project select2-reset" name="project_id" id="select2-project" style="width:100%;">
                            <option data-id="-1" value="-1">选择项目</option>
                        </select>
                    </div>
                </div>

                {{--交付日期--}}
                <div class="form-group">
                    <label class="control-label col-md-2">交付日期</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control form-filter date_picker" name="assign_date" data-default="{{ date('Y-m-d') }}" readonly="readonly" />
                    </div>
                </div>

                {{--交付量--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 交付量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="delivery_quantity" placeholder="交付量" value="">
                    </div>
                </div>

                {{--渠道佣金--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 渠道佣金</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="channel_commission" placeholder="渠道佣金" value="">
                    </div>
                </div>

                {{--成本--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 成本</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="daily_cost" placeholder="成本" value="">
                    </div>
                </div>




                {{--描述--}}
                <div class="form-group">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
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
                        <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-reconciliation-daily">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>




{{--添加-结算-记录--}}
<div class="modal fade modal-wrapper" id="modal-for-reconciliation-daily-settle-create">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">项目充值</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-reconciliation-daily-settle-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="reconciliation-daily" data-default="reconciliation-daily">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="settle" data-default="settle">

                    {{--交易类型--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">交易类型</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
{{--                            <button type="button" class="btn radio">--}}
{{--                                <label>--}}
{{--                                    <input type="radio" name="finance-create-type" value=1> 充值--}}
{{--                                </label>--}}
{{--                            </button>--}}
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="finance-create-type" value=21 checked="checked"> 结算
                                </label>
                            </button>
                        </div>
                    </div>
                    {{--交易日期--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 结算日期</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter date_picker" name="transaction-datetime" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" readonly="readonly" />
                        </div>
                    </div>
                    {{--金额--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 金额</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="transaction-amount" placeholder="输入金额" value="">
                        </div>
                    </div>
                    {{--支付方式--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-2">结算方式</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <input type="text" class="form-control" name="transaction-pay-type" placeholder="支付方式" value="" list="_transaction_pay_type">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <datalist id="_transaction_pay_type">--}}
{{--                        <option value="" />--}}
{{--                        <option value="支付宝" />--}}
{{--                        <option value="银行卡" />--}}
{{--                        <option value="现金" />--}}
{{--                        <option value="其他" />--}}
{{--                    </datalist>--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 结算方式</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="transaction-pay-type" value=1 checked="checked"> 余额结算
                                </label>
                            </button>
{{--                            <select class="form-control select-select2 select2-box-c" name="transaction-pay-type" id="" style="width:100%;">--}}
{{--                                <option value="">选择结算方式</option>--}}
{{--                                <option value="1">余额结算</option>--}}
{{--                                <option value="11">现金结算</option>--}}
{{--                                <option value="101">其他</option>--}}
{{--                            </select>--}}
                        </div>
                    </div>
                    {{--付款账号--}}
                    <div class="form-group income-show- _none">
                        <label class="control-label col-md-2">付款账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keyword" name="transaction-pay-account" placeholder="付款账号" value="" list="_transaction_pay_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_pay_account">
                    </datalist>
                    {{--收款账号--}}
                    <div class="form-group income-show- _none">
                        <label class="control-label col-md-2">收款账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keyword" name="transaction-receipt-account" placeholder="收款账号" value="" list="_transaction_receipt_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_receipt_account">
                    </datalist>
                    {{--交易单号--}}
                    <div class="form-group  _none">
                        <label class="control-label col-md-2">交易单号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="transaction-order-number" placeholder="交易单号" value="">
                        </div>
                    </div>
                    {{--备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">备注</label>
                        <div class="col-md-8 ">
                            <textarea class="form-control" name="transaction-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-reconciliation-daily-settle-create">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>