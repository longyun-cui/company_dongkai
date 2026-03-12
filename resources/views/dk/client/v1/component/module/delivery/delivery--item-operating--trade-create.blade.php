{{--添加-成交记录--}}
<div class="modal fade modal-wrapper" id="modal--for--delivery--item-operating--trade-create">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">添加成交记录</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form--for--delivery--item-operating--trade-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="delivery" data-default="delivery">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="trade" data-default="trade">

                    {{--交易类型--}}
                    <div class="form-group _none">
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
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 成交时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="transaction-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--品牌--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 品牌</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="transaction-title" placeholder="输入品牌" value="" list="_transaction_title">
                        </div>
                    </div>
{{--                    <datalist id="_transaction_title">--}}
{{--                        <option value="爱马仕" />--}}
{{--                        <option value="香奈儿" />--}}
{{--                        <option value="LV" />--}}
{{--                        <option value="迪奥" />--}}
{{--                        <option value="其他" />--}}
{{--                    </datalist>--}}
                    {{--数量--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 数量</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="transaction-count" placeholder="输入数量" value="">
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
                    <div class="form-group">
                        <label class="control-label col-md-2">支付方式</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="transaction-pay-type" placeholder="支付方式" value="" list="_transaction_pay_type">
                        </div>
                    </div>
                    <datalist id="_transaction_pay_type">
                        <option value="微信" />
                        <option value="支付宝" />
                        <option value="银行卡" />
                        <option value="现金" />
                        <option value="其他" />
                    </datalist>
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
                        <button type="button" class="btn btn-success" id="item-submit-for-delivery-trade-create"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

