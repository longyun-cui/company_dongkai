@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '路线列表' }}</a></li>
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

                {{--费用--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 运价（现金）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="amount_with_cash" placeholder="运价（现金）" value="{{ $data->amount_with_cash or 0 }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 运价（带票）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="amount_with_invoice" placeholder="运价（现金）" value="{{ $data->amount_with_invoice or 0 }}">
                    </div>
                </div>

                {{--箱型--}}
                <div class="form-group outside-car">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择箱型</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="trailer_length" id="select2-container">
                            <option value="0">选择</option>
                            <option value="7.6">7.6</option>
                            <option value="9.6">9.6</option>
                            <option value="12.5">12.5</option>
                            <option value="15">15</option>
                            <option value="16.5">16.5</option>
                        </select>
                    </div>
                </div>

                {{--里程--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 里程</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="travel_distance" placeholder="里程" value="{{ $data->travel_distance or 0 }}">
                    </div>
                </div>

                {{--时效--}}
                <div class="form-group">
                    <label class="control-label col-md-2">时效（小时）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="time_limitation_prescribed" placeholder="时效" value="{{ $data->time_limitation_prescribed or 0 }}">
                    </div>
                </div>

                {{--出发地--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 出发地</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="departure_place" placeholder="出发地" value="{{ $data->departure_place or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="destination_place" placeholder="目的地" value="{{ $data->destination_place or '' }}">
                        </div>
                    </div>
                </div>
                {{--目的地--}}
                {{--<div class="form-group">--}}
                    {{--<label class="control-label col-md-2"><sup class="text-red">*</sup> 目的地</label>--}}
                    {{--<div class="col-md-8 ">--}}
                        {{--<input type="text" class="form-control" name="destination_place" placeholder="目的地" value="{{ $data->destination_place or '' }}">--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--经停地--}}
                <div class="form-group">
                    <label class="control-label col-md-2">经停地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="stopover_place" placeholder="经停地" value="{{ $data->stopover_place or '' }}">
                    </div>
                </div>

                {{--合同有效期--}}
                <div class="form-group">
                    <label class="control-label col-md-2">合同有效期</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date_picker" name="contract_start_date" placeholder="开始日期" value="{{ $data->contract_start_date or '' }}" readonly="readonly">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control date_picker" name="contract_ended_date" placeholder="结束日期" value="{{ $data->contract_ended_date or '' }}" readonly="readonly">
                        </div>
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
                url: "{{ url('/item/route-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/item/route-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


    });
</script>
@endsection
