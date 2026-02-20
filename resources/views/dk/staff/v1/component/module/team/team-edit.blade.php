{{--编辑-部门--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--team-item-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-24px margin-bottom-64px bg-white">
        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加团队</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--team-item-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="team" data-default="team">


                {{--类型--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 部门类型</label>
                    <div class="col-md-8">
                        <div class="btn-group" id="department-category--for--team-item-edit">

{{--                            @if(in_array($me->user_type, [0,1,11]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-category">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_category" value="11"> 人事部--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}

{{--                            @if(in_array($me->user_type, [0,1,11]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-category">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_category" value="21"> 行政部--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}

{{--                            @if(in_array($me->user_type, [0,1,11]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-category">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_category" value="31"> 财务部--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}

                            @if(in_array($me->staff_category, [0,1,9,41]))
                            <button type="button" class="btn radio-btn radio-team-category">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_category" value="41" checked="checked" data-default="default"> 客服
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->staff_category, [0,1,9,51]))
                            <button type="button" class="btn radio-btn radio-team-category">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_category" value="51"
                                               @if($me->staff_category == 51) checked="checked" data-default="default" @endif
                                        > 质检
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->staff_category, [0,1,9,61]))
                            <button type="button" class="btn radio-btn radio-team-category">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_category" value="61"
                                               @if($me->staff_category == 61) checked="checked" data-default="default" @endif
                                        > 复核
                                    </label>
                                </span>
                            </button>
                            @endif

                            @if(in_array($me->staff_category, [0,1,9,71]))
                            <button type="button" class="btn radio-btn radio-team-category">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_category" value="71"
                                               @if($me->staff_category == 71) checked="checked" data-default="default" @endif
                                        > 运营
                                    </label>
                                </span>
                            </button>
                            @endif

{{--                            @if(in_array($me->user_type, [0,1,11]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-category">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_category" value="81"> 业务部--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}

                            @if(in_array($me->staff_category, [0,1,9,88]))
                            <button type="button" class="btn radio-btn radio-team-category">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_category" value="88"> 销售渠道
                                    </label>
                                </span>
                            </button>
                            @endif

                        </div>
                    </div>
                </div>


                {{--部门--}}
                @if(in_array($me->staff_position, [0,1,11,21]))
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择部门</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-reset select2--department"
                                name="department_id"
                                id="select2--department--for--team-item-edit"
                                data-modal="#modal--for--team-item-edit"
                                data-item-category=""
                                data-item-type=""
                                data-department-category="#department-category--for--team-item-edit"
                                data-team-target="#select2--team--for--team-item-edit"
                                data-staff-target="#select2--staff--for--team-item-edit"
                        >
                            <option data-id="" value="">选择部门</option>
                        </select>
                    </div>
                </div>
                @endif


                {{--层级--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 团队层级</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->staff_position, [0,1,11,21,31]))
                            <button type="button" class="btn radio-btn radio-team-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_type" value="11" checked="checked" data-default="default"> 团队
                                    </label>
                                </span>
                            </button>
                            @endif
                            @if(in_array($me->staff_position, [0,1,11,21,31,41]))
                            <button type="button" class="btn radio-btn radio-team-type">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="team_type" value="31"> 小组
                                    </label>
                                </span>
                            </button>
                            @endif
{{--                            @if(in_array($me->staff_type, [0,1,11,21]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-type _none-">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_type" value="31"> 小组--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}
{{--                            @if(in_array($me->staff_type, [0,1,11,21]))--}}
{{--                            <button type="button" class="btn radio-btn radio-team-type _none">--}}
{{--                                <span class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="team_type" value="41"> 小队--}}
{{--                                    </label>--}}
{{--                                </span>--}}
{{--                            </button>--}}
{{--                            @endif--}}

                        </div>
                    </div>
                </div>


                {{--团队--}}
                <div class="form-group team-box" style="display:none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择团队</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-reset select2--team"
                                name="superior_team_id"
                                id="select2--team--for--team-item-edit"
                                data-modal="#modal--for--team-item-edit"
                                data-item-category=""
                                data-item-type="11"
                        >
                            <option data-id="" value="">选择团队</option>
                        </select>
                    </div>
                </div>


                {{--名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="name" placeholder="团队名称" value="">
                    </div>
                </div>


                {{--负责人--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">选择负责人</label>
                    <div class="col-md-8 ">
                        <select class="form-control select2-reset select2--staff" name="leader_id"
                                id="select2--staff--for--team-item-edit"
                                data-modal="#modal--for--team-item-edit"
                                data-type="manager"
                        >
                            <option data-id="0" value="0">选择负责人</option>
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
{{--                <div class="form-group form-type _none">--}}
{{--                    <label class="control-label col-md-2">启用</label>--}}
{{--                    <div class="col-md-8">--}}
{{--                        <div class="btn-group">--}}

{{--                            <button type="button" class="btn">--}}
{{--                                <div class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="active" value="0"> 暂不启用--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </button>--}}
{{--                            <button type="button" class="btn">--}}
{{--                                <div class="radio">--}}
{{--                                    <label>--}}
{{--                                        <input type="radio" name="active" value="1" checked="checked"> 启用--}}
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
                        <button type="button" class="btn btn-success edit-submit" id="submit--for--team-item-edit">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>