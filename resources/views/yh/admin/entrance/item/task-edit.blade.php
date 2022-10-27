@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or '【l】' }}@endif{{ $a or 'A.' }}{{ $title_text or '任务编辑' }} - 管理员后台系统 - 兆益信息
@endsection




@section('sidebar')
    @include(env('TEMPLATE_STAFF_FRONT').'component.sidebar.sidebar-root')
@endsection
@section('header','')
@section('description','')
@section('content')
<div style="display:none;">
    <input type="hidden" id="" value="{{ $encode or '' }}" readonly>
</div>

<div class="container">

    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 container-body-left">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box box-info form-container">

                    <div class="box-header with-border" style="margin:8px 0;">
                        <h3 class="box-title">{{ $title_text }}</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-info">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                            <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                            {{--<input type="hidden" name="category" value="{{ $category or 'item' }}" readonly>--}}
                            <input type="hidden" name="type" value="{{ $type or 'item' }}" readonly>

                            {{--类别--}}
                            <div class="form-group form-category">
                                <label class="control-label col-md-2">类别</label>
                                <div class="col-md-8">
                                    <div class="btn-group">

                                        @if($operate == 'edit')
                                        <button type="button" class="btn">
                                            <label>
                                                <input type="radio" name="item_type" value="{{ $data->item_type or 0 }}" checked="checked">
                                                @if($data->item_type == 1) 售前
                                                @elseif($data->item_type == 2) 回收
                                                @elseif($data->item_type == 3) 调琴
                                                @elseif($data->item_type == 4) 搬运
                                                @elseif($data->item_type == 5) 异常
                                                @elseif($data->item_type == 9) 售后
                                                @endif
                                            </label>
                                        </button>
                                        @elseif($operate == 'create')
                                        <button type="button" class="btn _none">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="1"> 售前
                                                </label>
                                            </span>
                                        </button>
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="2" checked="checked"> 回收
                                                </label>
                                            </span>
                                        </button>
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="3"> 调琴
                                                </label>
                                            </span>
                                        </button>
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="4"> 搬运
                                                </label>
                                            </span>
                                        </button>
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="5"> 异常
                                                </label>
                                            </span>
                                        </button>
                                        <button type="button" class="btn _none">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="9"> 售后
                                                </label>
                                            </span>
                                        </button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            {{--型号--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">型号</label>
                                <div class="col-md-9 ">
                                    <input type="text" class="form-control" name="custom[model]" placeholder="型号" value="{{ $data->custom->model or '' }}">
                                </div>
                            </div>
                            {{--手机--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">手机</label>
                                <div class="col-md-9 ">
                                    <input type="text" class="form-control" name="custom[mobile]" placeholder="手机" value="{{ $data->custom->mobile or '' }}">
                                </div>
                            </div>
                            {{--地点--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">地点</label>
                                <div class="col-md-9 ">
                                    <input type="text" class="form-control" name="custom[address]" placeholder="地点" value="{{ $data->custom->address or '' }}">
                                </div>
                            </div>
                            {{--描述--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">描述</label>
                                <div class="col-md-9 ">
                                    {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{ $data->description or '' }}">--}}
                                    <textarea class="form-control" name="description" rows="3" cols="" placeholder="描述">{{ $data->description or '' }}</textarea>
                                </div>
                            </div>
                            {{--发布--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">发布</label>
                                <div class="col-md-9 ">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="item_active" value="1"> 现在发布
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{--头像--}}
                            <div class="form-group _none">
                                <label class="control-label col-md-2">头像</label>
                                <div class="col-md-9 fileinput-group">

                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail">
                                            @if(!empty($data->cover_pic))
                                                <img src="{{ url(env('DOMAIN_CDN').'/'.$data->cover_pic) }}" alt="" />
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail">
                                        </div>
                                        <div class="btn-tool-group">
                                <span class="btn-file">
                                    <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                    <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                    <input type="file" name="cover" />
                                </span>
                                            <span class="">
                                    <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                                </span>
                                        </div>
                                    </div>
                                    <div id="titleImageError" style="color: #a94442"></div>

                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row" style="margin:8px 0;">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" onclick="" class="btn btn-primary" id="edit-info-submit"><i class="fa fa-check"></i>提交</button>
                                <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 hidden-xs hidden-sm container-body-right">

        @include(env('TEMPLATE_STAFF_FRONT').'component.right-side.right-root')

    </div>

</div>
@endsection


@section('style')
<style>
    .box-footer a {color:#777;cursor:pointer;}
    .box-footer a:hover {color:orange;cursor:pointer;}
    .comment-container {border-top:2px solid #ddd;}
    .comment-choice-container {border-top:2px solid #ddd;}
    .comment-choice-container .form-group { margin-bottom:0;}
    .comment-entity-container {border-top:2px solid #ddd;}
    .comment-piece {border-bottom:1px solid #eee;}
    .comment-piece:first-child {}
</style>
@endsection

@section('js')
<script>
    $(function() {

        $("#edit-info-submit").on('click', function() {
            var options = {
                url: "/item/task-edit",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        if($("input[name=item_active]:checked").val() == 1) location.href = "/";
                        else location.href = "/?task-list-type=unpublished";
                    }
                }
            };
            $("#form-edit-info").ajaxSubmit(options);
        });
    });
</script>
@endsection
