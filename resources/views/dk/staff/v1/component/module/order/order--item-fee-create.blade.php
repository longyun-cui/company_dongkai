{{--添加-费用-信息--}}
<div class="modal fade modal-wrapper" id="modal--for--order-item-fee-create">
    <div class="col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">费用记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="">

                <div class="row col-md-12 datatable-search-row _none">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="order-fee-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="order-fee-attribute" style="width:96px;">
                            <option value="-1">选择属性</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit--for--order-item-fee-record">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel--for--order-item-fee-record">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable--for--order-item-fee-record-list'>
                    <thead>
                    <tr role='row' class='heading'>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

        </div>


        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">添加费用</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form--for--order-item-fee-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" name="operate[item_category]" value="order" data-default="order">
                    <input readonly type="hidden" name="operate[item_type]" value="fee" data-default="fee">

                    {{--交易类型--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 费用类型</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-type" value="1"> 收入
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-type" value="99" checked="checked" data-default="default"> 费用
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-type" value="101"> 订单扣款
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-type" value="111"> 司机罚款
                                </label>
                            </button>
                        </div>
                    </div>
                    {{--时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 时间</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control form-filter time-picker" name="fee-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--品牌--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 名目</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control" name="fee-title" placeholder="请输入名目" value="" list="_fee_title">
                        </div>
                    </div>
                    <datalist id="_fee_title">
                        <option value="油费" />
                        <option value="过路费" />
                        <option value="尿酸" />
                        <option value="迪奥" />
                        <option value="其他" />
                    </datalist>
                    {{--金额--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 金额</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control" name="fee-amount" placeholder="请输入金额" value="">
                        </div>
                    </div>
                    {{--说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">说明</label>
                        <div class="col-md-9 ">
                            <textarea class="form-control" name="fee-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--记录类型--}}
                    <div class="form-group fee-record-type-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 记录类型</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-record-type" value=1 checked="checked"> 普通记录
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="fee-record-type" value=81> 财务入账
                                </label>
                            </button>
                            <button type="button" class="btn radio advance-box">
                                <label>
                                    <input type="radio" name="fee-record-type" value=49> 垫付
                                </label>
                            </button>
                            <button type="button" class="btn radio collection-box">
                                <label>
                                    <input type="radio" name="fee-record-type" value=41> 代收
                                </label>
                            </button>
                        </div>
                    </div>
                    {{--交易时间--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 交易时间</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control form-filter time-picker" name="fee-transaction-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--支付方式--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2">支付方式</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control" name="fee-transaction-payment-method" placeholder="支付方式" value="" list="_payment_method">
                        </div>
                    </div>
                    <datalist id="_payment_method">
                        <option value="微信" />
                        <option value="支付宝" />
                        <option value="银行卡" />
                        <option value="现金" />
                        <option value="其他" />
                    </datalist>
                    {{--付款账号--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2">付款账号</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control search-input" name="fee-transaction-account-from" placeholder="付款账号" value="" list="_fee_account_from" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_fee_account_from">
                    </datalist>
                    {{--收款账号--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2">收款账号</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control search-input" name="fee-transaction-account-to" placeholder="收款账号" value="" list="_fee_account_to" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_fee_account_to">
                    </datalist>
                    {{--交易单号--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2">交易单号</label>
                        <div class="col-md-9 ">
                            <input type="text" class="form-control" name="fee-transaction-reference-no" placeholder="交易单号" value="">
                        </div>
                    </div>
                    {{--交易说明--}}
                    <div class="form-group payment-show">
                        <label class="control-label col-md-2">交易说明</label>
                        <div class="col-md-9 ">
                            <textarea class="form-control" name="fee-transaction-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit--for--order-item-fee-create"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>