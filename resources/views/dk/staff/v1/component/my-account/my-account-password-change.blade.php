{{--编辑-客户--}}
<div class="row datatable-body datatable-wrapper password-change-clone" data-datatable-item-category="password" data-item-name="密码">
        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">修改密码 (或更改初始密码)</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="password" data-default="password">


                {{--原密码--}}
                <div class="form-group">
                    <label class="control-label col-md-2">原密码</label>
                    <div class="col-md-8 ">
                        <input type="input" class="form-control" name="password_pre" placeholder="原密码" value="">
                    </div>
                </div>
                {{--新密码--}}
                <div class="form-group">
                    <label class="control-label col-md-2">新密码</label>
                    <div class="col-md-8 ">
                        <input type="password" class="form-control" name="password_new" placeholder="新密码" value="">
                    </div>
                </div>
                {{--确认密码--}}
                <div class="form-group">
                    <label class="control-label col-md-2">确认密码</label>
                    <div class="col-md-8 ">
                        <input type="password" class="form-control" name="password_confirm" placeholder="确认密码" value="">
                    </div>
                </div>


            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
</div>