<form action="" method="post" class="form-horizontal form-bordered" id="form-for-staff-edit">
<div class="box-body">

    {{ csrf_field() }}
    <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="staff" data-default="staff">



    {{--类别--}}
    <div class="form-group form-category">
        <label class="control-label col-md-2">类型</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="user_type" value="84" checked="checked"> 主管
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                                <input type="radio" name="user_type" value="88"> 员工
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>

    {{--手机--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 登录手机</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="mobile" placeholder="登录手机" value="">
        </div>
    </div>

    {{--用户名--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 用户名</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="username" placeholder="用户名" value="">
        </div>
    </div>


    {{--部门--}}
    <div class="form-group">
        <label class="control-label col-md-2"><sup class="text-red">*</sup> 部门</label>
        <div class="col-md-8 ">
            <select class="form-control select2-box-c" name="department_id" data-type="department">
                <option value="-1">选择团队</option>
                @if(count($department_list) > 0)
                    @foreach($department_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    {{--备注--}}
    <div class="form-group _none">
        <label class="control-label col-md-2">备注</label>
        <div class="col-md-8 ">
            <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
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
            <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-staff"><i class="fa fa-check"></i> 提交</button>
            <button type="button" class="btn btn-default edit-cancel">取消</button>
        </div>
    </div>
</div>
