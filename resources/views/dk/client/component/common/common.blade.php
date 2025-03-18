{{--跟进-follow--}}
<div class="modal fade modal-main-body" id="modal-body-for-follow-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">工单跟进【<span class="follow-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-follow-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="follow-set-operate" value="item-order-follow-option-set" readonly>
                    <input type="hidden" name="follow-set-operate-type" value="add" readonly>
                    <input type="hidden" name="follow-set-order-id" value="0" readonly>
                    <input type="hidden" name="follow-set-column-key" value="" readonly>


                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付结果</label>
                        <div class="col-md-8 " id="follow-set-distributed-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付订单</label>
                        <div class="col-md-8 " id="follow-set-distributed-order-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付客户</label>
                        <div class="col-md-8 " id="follow-set-distributed-client-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择项目</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box select2-project" name="follow-set-project-id" style="width:48%;" id="">
                                <option value="-1">选择项目</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择客户</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="follow-set-client-id" style="width:48%;" id="">
                                <option value="-1">选择客户</option>
                                @foreach($client_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">交付结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="follow-set-follow-result" style="width:48%;" id="">
                                <option value="-1">交付结果</option>
                                @foreach(config('info.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">录音地址</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="deliver-set-recording-address" autocomplete="off" placeholder="" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">交付说明</label>
                        <div class="col-md-8 ">
                            <textarea class="form-control" name="deliver-set-delivered-description" rows="4" cols="100%"></textarea>
                        </div>
                    </div>
                    <div class="form-group _none">
                        <label class="control-label col-md-2">是否允许分发</label>
                        <div class="col-md-8 ">
                            <div class="btn-group">

                                <button type="button" class="btn">
                            <span class="radio">
                                <label>
                                    <input type="radio" name="deliver-set-is_distributive_condition" value="0" class="info-set-column" checked="checked"> 否
                                </label>
                            </span>
                                </button>

                                <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="deliver-set-is_distributive_condition" value="1" class="info-set-column"> 是
                                    </label>
                                </span>
                                </button>

                            </div>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-deliver-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-deliver-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




