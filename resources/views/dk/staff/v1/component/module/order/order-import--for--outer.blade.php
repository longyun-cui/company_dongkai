{{--编辑-工单--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order--import--for--outer"
     tabindex="-1"
     role="dialog"
     data-backdrop="static"
     data-keyboard="false"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
>
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">导入工单</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool edit-cancel" data-widget="remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order--import--for--outer">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="import" data-default="import">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="order" data-default="order">


                {{--选择项目--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">项目</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <select class="form-control select2-reset select2--project"--}}
{{--                                name="project_id"--}}
{{--                                id="select2--project--for--order--import--for--outer"--}}
{{--                                data-modal="#modal--for--order--import--for--outer"--}}
{{--                                data-item-category="-1"--}}
{{--                        >--}}
{{--                            <option data-id="0" value="0">选择项目</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

                {{--attachment 附件--}}
                <div class="form-group">
                    <label class="control-label col-md-2">Excel文件</label>
                    <div class="col-md-9">
                        <input id="multiple-files" type="file" class="excel-file-upload" name="excel-file" multiple- >
                        {{--<input id="multiple-files" type="file" class="file-upload" name="multiple-excel-file" multiple >--}}
                    </div>
                </div>

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit"
                                id="submit--for--order--import--for--outer"
                        >
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">
                            <i class="fa fa-times"></i> 取消
                        </button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>