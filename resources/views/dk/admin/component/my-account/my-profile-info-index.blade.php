{{--编辑-客户--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-my-profile-info-index">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">基本资料</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form class="form-horizontal form-bordered">
                <div class="box-body">
                    {{--昵称--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">用户名</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->username or '' }}</label></div>
                        </div>
                    </div>
                    {{--真实姓名--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">真实姓名</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->true_name or '' }}</label></div>
                        </div>
                    </div>
                    {{--手机号--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">登录手机</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->mobile or '' }}</label></div>
                        </div>
                    </div>
                    {{--邮箱--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">邮箱</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->email or '' }}</label></div>
                        </div>
                    </div>
                    {{--QQ--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">QQ</label>
                        <div class="col-md-8 ">
                            <div><label class="control-label">{{ $data->QQ_number or '' }}</label></div>
                        </div>
                    </div>
                    {{--portrait--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">头像</label>
                        <div class="col-md-8 ">
                            <div class="info-img-block"><img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt=""></div>
                        </div>
                    </div>
                    {{--修改密码--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">修改密码</label>
                        <div class="col-md-8 ">
                            <a class="btn btn-danger _left" href="{{ url('/my-account/my-password-change') }}">修改密码</a>
                        </div>
                    </div>
                </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit _none">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>