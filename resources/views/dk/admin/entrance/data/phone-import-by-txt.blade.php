@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '订单列表' }}</a></li>
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

                {{--选择项目--}}
                <div class="form-group">
                    <label class="control-label col-md-2">选择数据表</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="table_id">
                            <option value="">选择项目</option>
                            <option value="pool_bj">pool_bj</option>
                        </select>
                    </div>
                </div>


                {{--attachment 附件--}}
                <div class="form-group">
                    <label class="control-label col-md-2">文件上传</label>
                    <div class="col-md-8">
                        <input id="multiple-file" type="file" class="file-upload" name="txt-file" multiple- >
{{--                        <input id="multiple-files" type="file" class="file-upload" name="multiple-excel-file" multiple >--}}
                    </div>
                </div>

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


        $(".file-upload").fileinput({
            allowedFileExtensions : [ 'txt' ],
            showUpload: false
        });


        $("#edit-item-submit").on('click', function() {

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在上传</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            var options = {
                url: "/data/phone-import-by-txt",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {

                    layer.closeAll('loading');

                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        layer.msg("添加" + data.data.count + "条数据！");

                        $(".fileinput-remove-button").click();

                        // location.href = "/item/order-list-for-all";
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.closeAll('loading');
                    layer.msg("服务器错误");
                },
                complete: function (jqXHR, textStatus) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    layer.closeAll('loading');
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });



    });
</script>
@endsection
