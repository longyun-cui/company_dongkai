@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))[l]@endif A.{{ $title_text }} - 兆益信息
@endsection


@section('meta_title')@endsection
@section('meta_author')@endsection
@section('meta_description')@endsection
@section('meta_keywords')@endsection




@section('header','')
@section('description', '管理员系统-兆益信息')
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '内容列表' }}</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:16px 0;">
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


                    {{--用户名--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 用户名</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="username" placeholder="用户名" value="{{ $data->username or '' }}">
                        </div>
                    </div>
                    {{--描述--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">描述</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
                        </div>
                    </div>

                    {{--选择员工--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">选择员工</label>
                        <div class="col-md-8 ">
                            <select class="form-control" name="salesman_id" id="select2-sales">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>

                    {{--attachment 附件--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">附件</label>
                        <div class="col-md-8 fileinput-group">

                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="width:480px;min-height:32px;">
                                    <a target="_blank" href="/all/download-item-attachment?item-id={{ $data->id or 0 }}">
                                        {{ $data->attachment_name or '' }}
                                    </a>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="line-height:32px;">
                                </div>
                                <div class="btn-tool-group">
                                    <span class="btn-file">
                                        <button class="btn btn-sm btn-primary fileinput-new">选择附件</button>
                                        <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                        <input type="file" name="attachment" />
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
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/resource/component/css/select2-4.0.5.min.css') }}">
@endsection
@section('custom-style')
<style>
</style>
@endsection


@section('custom-js')
    {{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
    <script src="{{ asset('/resource/component/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {

        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "/item/task-list-import",
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
            $("#form-edit-item").ajaxSubmit(options);
        });


        $('#select2-sales').select2({
            ajax: {
                url: "{{ url('/user/user_select2_sales') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

    });
</script>
@endsection
