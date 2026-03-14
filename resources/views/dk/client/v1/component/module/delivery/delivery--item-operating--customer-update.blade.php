{{--更新-客户状态--}}
<div class="modal fade modal-wrapper" id="modal--for--delivery--item-operating--customer-update">
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

            <form action="" method="post" class="form-horizontal form-bordered " id="form--for--delivery--item-operating--customer-update">
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
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--delivery--item-operating--customer-update"
                                data-modal-id="modal--for--delivery--item-operating--customer-update"
                                data-form-id="form--for--delivery--item-operating--customer-update"
                        >
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

