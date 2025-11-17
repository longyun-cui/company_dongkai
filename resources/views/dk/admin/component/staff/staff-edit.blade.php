{{--编辑-部门--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-staff-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container modal-body">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加员工</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-staff-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="staff" data-default="department">


                {{--类别--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2">类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->user_type, [0,1]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="11"> 总经理
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="41"> 团队·总监
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="81"> 客服经理
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41,81]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="84"> 客服主管
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41,81,84]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="88" checked="checked"> 客服
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="71"> 质检经理
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="77"> 质检员
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="61"> 运营经理
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,61]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="66" @if($me->user_type == 61) checked="checked" @endif> 运营人员
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            <button type="button" class="btn radio-btn radio-user-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="user_type" value="91"> 三方审核
                                    </label>
                                </span>
                            </button>
                            @endif

                        </div>
                    </div>
                </div>

                {{--上级--}}
                <div class="form-group superior-box" style="display:none;">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 上级</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="superior_id" id="select2-superior" data-type="">
                            <option data-id="-1" value="-1">未指定</option>
                        </select>
                    </div>
                </div>

                {{--部门-大区--}}
                @if(in_array($me->user_type, [0,1,11]))
                    <div class="form-group department-box department-district-box">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 大区</label>
                        <div class="col-md-8 ">
                            <select class="form-control" name="department_district_id" id="select2-department-district" data-type="">
                                <option data-id="-1" value="-1">选择大区</option>
                            </select>
                        </div>
                    </div>
                @endif

                {{--部门-小组--}}
                <div class="form-group department-box department-group-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 小组</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="department_group_id" id="select2-department-group" data-type="">
                            <option data-id="-1" value="-1">选择小组</option>
                        </select>
                    </div>
                </div>


                {{--手机--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 登录手机（工号）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="mobile" placeholder="登录手机（工号）" value="">
                    </div>
                </div>
                {{--真实姓名--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 真实姓名</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="true_name" placeholder="真实姓名" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2">用户名</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="username" placeholder="用户名" value="">
                    </div>
                </div>
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">API服务器（外呼系统服务器）</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <input type="text" class="form-control" name="api_serverFrom" placeholder="API服务器" value="">--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外呼系统坐席ID</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="api_staffNo" placeholder="API坐席ID，没有添0" value="">
                    </div>
                </div>
                {{--描述--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                    </div>
                </div>


                {{--头像--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">头像</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                                @if(!empty($data->portrait_img))
                                    <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt="" />
                                @endif
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                            <span class="btn-file">
                                <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                <input type="file" name="portrait" />
                            </span>
                                <span class="">
                                <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                            </span>
                            </div>
                        </div>
                        <div id="titleImageError" style="color: #a94442"></div>

                    </div>
                </div>

                {{--启用--}}
                <div class="form-group form-type _none">
                    <label class="control-label col-md-2">启用</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="active" value="0" checked="checked"> 暂不启用
                                    </label>
                                </div>
                            </button>
                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="active" value="1"> 启用
                                    </label>
                                </div>
                            </button>

                        </div>
                    </div>
                </div>

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-staff">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>