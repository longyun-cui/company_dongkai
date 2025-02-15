@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑部门' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '部门列表' }}</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PORTLET-->
            <div class="box box-info form-container">

                <div class="box-header with-border" style="margin:4px 0;">
                    <h3 class="box-title">{{ $title_text or '' }}</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
                    <div class="box-body">

                        {{ csrf_field() }}
                        <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                        <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                        <input type="hidden" name="category" value="{{ $category or 'user' }}" readonly>
                        <input type="hidden" name="type" value="{{ $type or 'user' }}" readonly>


                        {{--类别--}}
                        <div class="form-group form-category">
                            <label class="control-label col-md-2">类型</label>
                            <div class="col-md-8">
                                <div class="btn-group">

                                    @if(in_array($me->user_type, [0,1,11]))
                                        @if($operate == 'create' || ($operate == 'edit' && $data->department_type == 11))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate == 'edit' && $data->department_type == 11)
                                                        <input type="radio" name="department_type" value="11" checked="checked"> 大区
                                                    @else
                                                        <input type="radio" name="department_type" value="11" checked="checked"> 大区
                                                    @endif
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                    @if(in_array($me->user_type, [0,1,11,41]))
                                        @if($operate == 'create' || ($operate == 'edit' && $data->department_type == 21))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate == 'edit' && $data->department_type == 21)
                                                        <input type="radio" name="department_type" value="21" checked="checked"> 组
                                                    @else
                                                        @if($me->user_type == 41)
                                                            <input type="radio" name="department_type" value="21" checked="checked"> 组
                                                        @else
                                                            <input type="radio" name="department_type" value="21"> 组
                                                        @endif
                                                    @endif
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        </div>




                        {{--项目名称--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 部门名称</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="name" placeholder="部门名称" value="{{ $data->name or '' }}">
                            </div>
                        </div>


                        {{--上级部门--}}
                        <div class="form-group select2-superior-box">
                            <label class="control-label col-md-2">选择上级部门</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="superior_department_id" id="select2-superior-department">
                                    @if($operate == 'edit' && $data->superior_department_id)
                                        <option data-id="{{ $data->superior_department_id or 0 }}" value="{{ $data->superior_department_id or 0 }}">{{ $data->superior_department_er->name }}</option>
                                    @else
                                        <option data-id="0" value="0">未指定</option>
                                    @endif
                                </select>
                            </div>
                        </div>


                        {{--负责人--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">选择负责人</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="leader_id" id="select2-leader"
                                        @if($operate == 'edit' && $data->department_type == 21) data-type="supervisor" @else data-type="manager" @endif
                                >
                                    @if($operate == 'edit' && $data->leader_id)
                                        <option data-id="{{ $data->leader_id or 0 }}" value="{{ $data->leader_id or 0 }}">{{ $data->leader->username }}</option>
                                    @else
                                        <option data-id="0" value="0">未指定</option>
                                    @endif
                                </select>
                            </div>
                        </div>


                        {{--描述--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">描述</label>
                            <div class="col-md-8 ">
                                {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                                <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
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
                        @if($operate == 'create')
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
                        @endif

                    </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="button" class="btn btn-success" id="edit-item-submit"><i class="fa fa-check"></i> 提交</button>
                            <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>
@endsection




@section('custom-css')
@endsection
@section('custom-style')
@endsection




@section('custom-js')
@endsection
@section('custom-script')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.department.department-edit-script')
@endsection