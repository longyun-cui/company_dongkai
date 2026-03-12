{{--添加-跟进记录--}}
<div class="modal fade modal-wrapper" id="modal--for--delivery--item-operating--vfollow-create">
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

            <form action="" method="post" class="form-horizontal form-bordered " id="form--for--delivery--item-operating--follow-create">
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
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--delivery--item-operating--follow-create"
                                data-modal-id="modal--for--delivery--item-operating--follow-create"
                                data-form-id="form--for--delivery--item-operating--follow-create"
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

