{{--项目-编辑--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--project-item-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加项目</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--project-item-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="project" data-default="project">




                {{--项目类型--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2">项目种类</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            <button type="button" class="btn radio-btn radio-item-category">
                            <span class="radio">
                                <label>
                                    <input type="radio" name="project_category" value="1" checked="checked"> 口腔
                                </label>
                            </span>
                            </button>

                            <button type="button" class="btn radio-btn radio-item-category">
                            <span class="radio">
                                <label>
                                    <input type="radio" name="project_category" value="11"> 医美
                                </label>
                            </span>
                            </button>

                            <button type="button" class="btn radio-btn radio-item-category">
                            <span class="radio">
                                <label>
                                    <input type="radio" name="project_category" value="31"> 二奢
                                </label>
                            </span>
                            </button>

                        </div>
                    </div>
                </div>


                {{--项目名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="name" placeholder="项目名称" value="">
                    </div>
                </div>
                {{--真实名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2">真实名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="alias_name" placeholder="真实名称" value="">
                    </div>
                </div>


                {{--城市--}}
                <div class="form-group">
                    <label class="control-label col-md-2">城市</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="location_city" placeholder="城市" value="">
                    </div>
                </div>


                {{--客户--}}
                <div class="form-group">
                    <label class="control-label col-md-2">客户</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-reset select2--client"
                                name="client_id"
                                id="select2--client--for--project-item-edit"
                                data-modal="#modal--for--project-item-edit"
                                data-client-category=""
                        >
                            <option data-id="-1" value="-1">选择客户</option>
                        </select>
                    </div>
                </div>

                {{--团队--}}
                <div class="form-group">
                    <label class="control-label col-md-2">团队</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-multiple-reset select2--team" multiple="multiple"
                                name="teams[]"
                                id="select2--teams--for--project-item-edit"
                                data-modal="#modal--for--project-item-edit"
                                data-item-category=""
                                data-item-type="11"
                                data-team-target=""
                        >
                        </select>
                    </div>
                </div>

                {{--质检员--}}
                <div class="form-group">
                    <label class="control-label col-md-2">质检</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-multiple-reset select2--staff" multiple="multiple"
                                name="peoples[]"
                                id="select2--peoples--for--project-item-edit"
                                data-modal="#modal--for--project-item-edit"
                                data-staff-category="51"
                                data-staff-type="11"
                        >
                        </select>
                    </div>
                </div>


                <div class="form-group form-type">
                    <label class="control-label col-md-2">是否允许分发</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="is_distributive" value="0" checked="checked"> 否
                                    </label>
                                </div>
                            </button>
                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="is_distributive" value="1"> 是
                                    </label>
                                </div>
                            </button>

                        </div>
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
{{--                <div class="form-group form-type _none">--}}
{{--                    <label class="control-label col-md-2">启用</label>--}}
{{--                    <div class="col-md-8">--}}
{{--                        <div class="btn-group">--}}

{{--                            <button type="button" class="btn">--}}
{{--                                <div class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="active" value="0" checked="checked"> 暂不启用--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn">--}}
{{--                                <div class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="active" value="1"> 启用--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </button>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--project-item-edit">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>