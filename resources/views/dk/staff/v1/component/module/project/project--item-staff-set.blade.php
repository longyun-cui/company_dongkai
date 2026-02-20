{{--项目-编辑--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--project--item-staff-set">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">编辑项目</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--project--item-staff-set">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="project" data-default="project">


                {{--专属员工--}}
                <div class="form-group">
                    <label class="control-label col-md-2">专属员工</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-multiple-reset select2--staff" multiple="multiple"
                                name="staff_list[]"
                                id="select2--staff--for--project--item-staff-set"
                                data-modal="#modal--for--project--item-staff-set"
                                data-item-category=""
                                data-item-type=""
                                data-staff-category=""
                                data-staff-type=""
                                data-team-id=""
                        >
                        </select>
                    </div>
                </div>


            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--project--item-staff-set">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>