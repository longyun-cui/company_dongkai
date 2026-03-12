{{--更新-客户回访--}}
<div class="modal fade modal-wrapper" id="modal--for--delivery--item-operating--callback-update">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">
                    <span class="">更新回访时间</span>
                    <span class="id-title"></span>
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form--for--delivery--item-operating--callback-update">
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
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-2">跟进时间</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <input type="text" class="form-control form-filter time_picker" name="follow-datetime" readonly="readonly" />--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    {{--是否上门--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-2">是否上门</label>--}}
{{--                        <div class="col-md-8 control-label" style="text-align:left;">--}}
{{--                            <button type="button" class="btn radio-btn">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="is_come" value="0"> 否--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn radio-btn">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="is_come" value="9"> 预约上门--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn radio-btn">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="is_come" value="11"> 已上门--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    {{--回访时间--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 回访时间</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="callback-datetime" readonly="readonly" />
                        </div>
                    </div>
                    {{--备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">备注</label>
                        <div class="col-md-8 ">
                            <textarea class="form-control" name="callback-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="form-submit-for-delivery-callback-update">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default modal-cancel">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

