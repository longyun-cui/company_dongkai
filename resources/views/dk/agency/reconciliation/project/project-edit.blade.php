{{--编辑-项目--}}
<div class="modal fade modal-wrapper" id="modal-for-reconciliation-project-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加项目</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-reconciliation-project-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="reconciliation-project" data-default="reconciliation-project">




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
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="name" placeholder="项目名称" value="">
                    </div>
                </div>


                {{--项目名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 合作单价</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price" placeholder="合作单价" value="">
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
                        <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-reconciliation-project">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>





{{--添加-充值记录--}}
<div class="modal fade modal-wrapper" id="modal-for-reconciliation-project-recharge-create">
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

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-reconciliation-project-recharge-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="reconciliation-project" data-default="reconciliation-project">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="recharge" data-default="recharge">

                    {{--交易类型--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">交易类型</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="finance-create-type" value=1 checked="checked"> 充值
                                </label>
                            </button>
{{--                            <button type="button" class="btn radio">--}}
{{--                                <label>--}}
{{--                                    <input type="radio" name="finance-create-type" value=21> 支出--}}
{{--                                </label>--}}
{{--                            </button>--}}
                        </div>
                    </div>
                    {{--交易日期--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 交易时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter date_picker" name="transaction-datetime"  data-default="{{ date('Y-m-d') }}" readonly="readonly" />
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
                        <button type="button" class="btn btn-success" id="item-submit-for-reconciliation-project-recharge-create"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>