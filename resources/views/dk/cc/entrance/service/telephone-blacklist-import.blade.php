@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{ $title_text or '导入电话黑名单' }} - {{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}
@endsection




@section('title')<span class="box-title">{{ $title_text or '导入电话黑名单' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '导入电话黑名单' }}</span>@endsection
@section('description'){{ config('info.system.'.$system) }} - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($operate_list_link) }}"><i class="fa fa-list"></i>{{ $operate_list_text or '团队列表' }}</a></li>
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
                <input type="hidden" name="operate[type]" value="{{ $operate_type or '' }}" readonly>
                <input type="hidden" name="operate[id]" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="operate[item_category]" value="{{ $category_item_category or 'service' }}" readonly>
                <input type="hidden" name="operate[item_type]" value="{{ $category_item_type or 'telephone' }}" readonly>


                {{--attachment 附件--}}
                <div class="form-group">
                    <label class="control-label col-md-2">文件上传</label>
                    <div class="col-md-8">
                        <input id="multiple-file" type="file" class="file-upload" name="txt-file" >
{{--                        <input id="multiple-files" type="file" class="file-upload" name="multiple-excel-file" multiple >--}}
                    </div>
                </div>

                {{--启用--}}
                @if($operate_type == 'create')
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
                url: "/service/telephone-blacklist-import",
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




        //
        $('#select2-client').select2({
            ajax: {
                url: "{{ url('/item/item_select2_client') }}",
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

        //
        $('#select2-project').select2({
            ajax: {
                url: "{{ url('/item/item_select2_project') }}",
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
