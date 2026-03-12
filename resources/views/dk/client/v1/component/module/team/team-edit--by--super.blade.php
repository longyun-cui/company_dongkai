{{--编辑-部门--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--team-item-edit--by--super">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">API设置</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--team-item-edit--by--super">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="edit" data-default="edit">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="team" data-default="team">


                {{--api--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 服务器ID&名称</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="serverFrom_id" placeholder="API_服务器ID" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="serverFrom_name" placeholder="API_服务器名称" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 账户&客户名称</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_account" placeholder="API_对接账户" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_name" placeholder="API_客户名称" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 用户名&密码</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_user_name" placeholder="API_用户名" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_password" placeholder="API_对接密码" value="">
                        </div>
                    </div>
                </div>


                {{--api2--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 服务器ID&名称_2</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="serverFrom_id_2" placeholder="API_服务器ID" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="serverFrom_name_2" placeholder="API_服务器名称" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 账户&客户名称_2</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_account_2" placeholder="API_对接账户" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_name_2" placeholder="API_客户名称" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 用户名&密码_2</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_user_name_2" placeholder="API_用户名" value="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="api_customer_password_2" placeholder="API_对接密码" value="">
                        </div>
                    </div>
                </div>


                {{--排序--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 排序</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="rank" placeholder="排序" value="">
                    </div>
                </div>


            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--team-item-edit--by--super">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>