{{--编辑-部门--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-department-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加部门</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form-for-department-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="department" data-default="department">


                {{--类别--}}
                <div class="form-group form-category" style="height:70px;">
                    <label class="control-label col-md-2">类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->user_type, [0,1,11]))
                            <button type="button" class="btn radio-btn radio-department-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="department_type" value="11" checked="checked"> 大区
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41]))
                            <button type="button" class="btn radio-btn radio-department-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="department_type" value="21"> 组
                                    </label>
                                </span>
                            </button>
                            @endif

                        </div>
                    </div>
                </div>




                {{--项目名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 部门名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="name" placeholder="部门名称" value="">
                    </div>
                </div>


                {{--上级部门--}}
                <div class="form-group select2-superior-box" style="height:70px;">
                    <label class="control-label col-md-2">选择上级部门</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-box" name="superior_department_id" id="select2-superior-department" style="width:100%;">
                            <option data-id="-1" value="-1">选择上级部门</option>
                        </select>
                    </div>
                </div>


                {{--负责人--}}
                <div class="form-group" style="height:70px;">
                    <label class="control-label col-md-2">选择负责人</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-box" name="leader_id" id="select2-leader" data-type="director" style="width:100%;">
                            <option data-id="-1" value="-1">选择负责人</option>
                        </select>
                    </div>
                </div>


                {{--描述--}}
                <div class="form-group">
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
                        <button type="button" class="btn btn-success edit-submit" id="edit-submit-for-department">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>