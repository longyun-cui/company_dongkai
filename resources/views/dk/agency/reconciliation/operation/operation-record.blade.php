{{--工单详情--}}
<div class="modal fade modal-wrapper" id="modal-for-delivery-item-detail">
    <div class="col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">工单详情</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-delivery-follow-create">
                <div class="box-body">


                    {{--订单ID--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">工单ID</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="delivery-id-title"></span>
                        </div>
                    </div>
                    {{--姓名--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">姓名</label>
                        <div class="col-md-8 ">
                            <span class="delivery-client-name-box"></span>
                        </div>
                    </div>
                    {{--电话--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 ">
                            <span class="delivery-client-mobile-box"></span>
                        </div>
                    </div>
                    {{--微信--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">微信</label>
                        <div class="col-md-8 ">
                            <span class="delivery-client-wx-box"></span>
                        </div>
                    </div>
                    {{--意向--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">意向</label>
                        <div class="col-md-8 ">
                            <span class="delivery-client-intention-box"></span>
                        </div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="delivery-teeth-count-box"></span>
                        </div>
                    </div>
                    {{--城市--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">城市</label>
                        <div class="col-md-8 ">
                            <span class="delivery-location-box"></span>
                        </div>
                    </div>
                    {{--通话记录--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">通话记录</label>
                        <div class="col-md-8 ">
                            <span class="delivery-description-box"></span>
                        </div>
                    </div>
                    {{--通话录音--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-8 ">
                            <span class="delivery-recording-address-box"></span>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>





{{--操作记录--}}
<div class="modal fade- modal-main-body modal-wrapper" id="modal-for-operation-record-list">
    <div class="col-md-10 col-md-offset-1 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">操作记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);" class="item-modal-show-for-follow-create">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加跟进记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="">

                <div class="row col-md-12 datatable-search-row _none">
                    <div class="input-group">

                        <input readonly type="hidden" class="form-control" name="item-category" value="edit" data-default="edit">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value="-1">选择属性</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable-operation-record'>
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






{{--添加-跟进记录--}}
<div class="modal fade modal-wrapper" id="modal-for-delivery-follow-create">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">添加跟进记录</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-delivery-follow-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="delivery" data-default="delivery">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="follow" data-default="follow">



                    {{--订单ID--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">工单ID</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="delivery-id-title"></span>
                        </div>
                    </div>
                    {{--关键词--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">关键词</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="delivery-title"></span>
                        </div>
                    </div>
                    {{--跟进时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">跟进时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="follow-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--跟进说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">跟进说明</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="">--}}
                            <textarea class="form-control" name="follow-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="form-submit-for-delivery-follow-create">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--更新-客户状态--}}
<div class="modal fade modal-wrapper" id="modal-for-delivery-customer-update">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">更新客户信息</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-delivery-customer-update">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="delivery" data-default="delivery">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="customer" data-default="customer">


                    {{--订单ID--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">ID</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="delivery-id-title"></span>
                        </div>
                    </div>
                    {{--关键词--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="customer-update-title"></span>
                        </div>
                    </div>
                    {{--是否+V--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">是否+V</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio-btn">
                                <span class="radio">
                                <label>
                                    <input type="radio" name="is_wx" value=0> 否
                                </label>
                                </span>
                            </button>
                            <button type="button" class="btn radio-btn">
                                <span class="radio">
                                <label>
                                    <input type="radio" name="is_wx" value=1> 是
                                </label>
                                </span>
                            </button>
                        </div>
                    </div>
                    {{--联系渠道--}}
                    <div class="form-group" style="height:70px;">
                        <label class="control-label col-md-2">联系渠道</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-contact select2-reset" name="client_contact_id" style="width:100%;">
                                <option data-id="-1" value="-1">选择联系渠道</option>
                            </select>
                        </div>
                    </div>
                    {{--客户备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户名备注</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="customer_remark" placeholder="客户名备注" value="">
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="form-submit-for-delivery-customer-update">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--更新-客户上门--}}
<div class="modal fade modal-wrapper" id="modal-for-delivery-come-update">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">更新上门状态</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-delivery-come-update">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="delivery" data-default="delivery">
                    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="come" data-default="come">




                    {{--订单ID--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">ID</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="delivery-id-title"></span>
                        </div>
                    </div>
                    {{--关键词--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="customer-update-title"></span>
                        </div>
                    </div>
                    {{--跟进时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">跟进时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="follow-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--是否上门--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">是否上门</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio-btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="is_come" value="0"> 否
                                    </label>
                                </span>
                            </button>
                            <button type="button" class="btn radio-btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="is_come" value="9"> 预约上门
                                    </label>
                                </span>
                            </button>
                            <button type="button" class="btn radio-btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="is_come" value="11"> 已上门
                                    </label>
                                </span>
                            </button>
                        </div>
                    </div>
                    {{--上门时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 上门时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="come-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">备注</label>
                        <div class="col-md-8 ">
                            <textarea class="form-control" name="come-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="form-submit-for-delivery-come-update">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--添加-成交记录--}}
<div class="modal fade modal-wrapper" id="modal-for-delivery-trade-create">
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

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-delivery-trade-create">
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
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 交易时间</label>
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
                    <datalist id="_transaction_title">
                        <option value="爱马仕" />
                        <option value="香奈儿" />
                        <option value="LV" />
                        <option value="迪奥" />
                        <option value="其他" />
                    </datalist>
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