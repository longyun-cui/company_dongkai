<form action="" method="post" class="form-horizontal form-bordered" id="form-for-department-edit">
<div class="box-body">

    {{ csrf_field() }}
    <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
    <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
    <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
    <input readonly type="hidden" class="form-control" name="operate[item_type]" value="department" data-default="department">

    {{--名称--}}
    <div class="form-group wx_box">
        <label class="control-label col-md-2">名称</label>
        <div class="col-md-8 ">
            <input type="text" class="form-control" name="name" placeholder="名称" value="" data-default="">
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
            <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-department"><i class="fa fa-check"></i> 提交</button>
            <button type="button" class="btn btn-default edit-cancel">取消</button>
        </div>
    </div>
</div>
