{{--编辑-部门--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--mac-address-item-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-32px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加MAC地址</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--mac-address-item-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="mac-address" data-default="mac-address">

                {{--公司--}}
                @if(in_array($me->staff_position, [0,1,9,11]))
                    <div class="form-group company-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 公司&部门</label>
                        <div class="col-md-9 ">
                            @if(in_array($me->staff_position, [0,1,9]))
                                <div class="col-sm-6 col-md-6 padding-0">
                                    <select class="form-control select2-reset select2--company"
                                            name="company_id"
                                            id="select2--company--for--mac-address-item-edit"
                                            data-modal="#modal--for--mac-address-item-edit"
                                            data-item-category=""
                                            data-item-type=""
                                            data-department-category="#department-category--for--mac-address-item-edit"
                                            data-department-target="#select2--department--for--mac-address-item-edit"
                                            data-team-target="#select2--team--for--mac-address-item-edit"
                                    >
                                        <option data-id="" value="">选择公司</option>
                                    </select>
                                </div>
                            @endif
                            <div class="col-sm-6 col-md-6 padding-0 department-box">
                                <select class="form-control select2-reset select2--department"
                                        name="department_id"
                                        id="select2--department--for--mac-address-item-edit"
                                        data-modal="#modal--for--mac-address-item-edit"
                                        data-query-scope="all"
                                        data-item-category=""
                                        data-item-type=""
                                        data-department-category="41"
                                        data-department-type=""
                                        data-team-target="#select2--team--for--mac-address-item-edit"
                                >
                                    <option data-id="0" value="0">选择部门</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif
                {{--团队--}}
                @if(in_array($me->staff_position, [0,1,11,31,41]))
                    <div class="form-group team-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 所属团队</label>
                        <div class="col-md-9 ">
                            @if(in_array($me->staff_position, [0,1,11,31]))
                                <div class="col-sm-6 col-md-6 padding-0 team-box">
                                    <select class="form-control select2-reset select2--team"
                                            name="team_id"
                                            id="select2--team--for--mac-address-item-edit"
                                            data-modal="#modal--for--mac-address-item-edit"
                                            data-item-category=""
                                            data-item-type="11"
                                            data-query-scope="all"
                                            data-team-category=""
                                            data-team-type="11"
                                            data-team-target="#select2---team-group--for--mac-address-item-edit"
                                    >
                                        <option data-id="0" value="0">选择团队</option>
                                    </select>
                                </div>
                            @endif
                            <div class="col-sm-6 col-md-6 padding-0 group-box">
                                <select class="form-control select2-reset select2--team"
                                        name="team_group_id"
                                        id="select2---team-group--for--mac-address-item-edit"
                                        data-modal="#modal--for--mac-address-item-edit"
                                        data-item-category=""
                                        data-item-type="31"
                                        data-query-scope="all"
                                >
                                    <option data-id="0" value="0">选择小组</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif


                {{--登录工号--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> Mac地址</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="mac_address" placeholder="Mac地址" value="">
                    </div>
                </div>
                {{--客户名--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户名</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="api_customerName" placeholder="客户名" value="">
                    </div>
                </div>
                {{--用户名--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 用户名</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="api_userName" placeholder="用户名" value="">
                    </div>
                </div>
                {{--密码--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 密码</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="api_password" placeholder="密码" value="">
                    </div>
                </div>
                {{--外呼系统坐席ID--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外呼系统坐席ID</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <input type="text" class="form-control" name="api_mac-addressNo" placeholder="API坐席ID，没有添0" value="">--}}
{{--                    </div>--}}
{{--                </div>--}}
                {{--描述--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-9 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                    </div>
                </div>
                

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--mac-address-item-edit">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>