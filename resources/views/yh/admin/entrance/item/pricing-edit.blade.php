@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '定价列表' }}</a></li>
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
                <input type="hidden" name="operate_category" value="{{ $operate_category or 'ITEM' }}" readonly>
                <input type="hidden" name="operate_type" value="{{ $operate_type or 'route' }}" readonly>


                {{--标题--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 标题</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="title" placeholder="标题" value="{{ $data->title or '' }}">
                    </div>
                </div>

                {{--包油--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 包油（升）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="price1" placeholder="包油（升）" value="{{ $data->price1 or '' }}">
                    </div>
                </div>

                {{--空放--}}
                <div class="form-group">
                    <label class="control-label col-md-2">空放（升）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="price2" placeholder="空放（升）" value="{{ $data->price2 or '' }}">
                    </div>
                </div>

                {{--空放200+--}}
                <div class="form-group">
                    <label class="control-label col-md-2">空放200+（升）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="price3" placeholder="空放200+（升）" value="{{ $data->price3 or '' }}">
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


            </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-edit-submit"><i class="fa fa-check"></i> 提交</button>
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
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">
@endsection




@section('custom-script')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 添加or编辑
        $("#item-edit-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/pricing-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/item/pricing-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


    });
</script>
@endsection
