{{--项目-编辑--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--ai-item-inspecting">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">AI质检</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--ai-item-inspecting">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="ai" data-default="ai">


                {{--AI审核模型--}}
                <div class="form-group">
                    <label class="control-label col-md-2">录音地址</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="recording_address" placeholder="录音地址" value="">
                    </div>
                </div>
                {{--AI审核提示词--}}
                <div class="form-group">
                    <label class="control-label col-md-2">审核提示词</label>
                    <div class="col-md-9 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="prompt_text" rows="5" cols="100%"></textarea>
                    </div>
                </div>
                {{--AI审核提示词--}}
                <div class="form-group">
                    <label class="control-label col-md-2">审核结果</label>
                    <div class="col-md-9 ">
                        <span class="result-box"></span>
                    </div>
                </div>


            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--ai-item-inspecting">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>