@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
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

                {{--自有车辆--}}
                {{--@if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))--}}
                    <div class="form-group inside-car">
                        <label class="control-label col-md-2">车辆</label>
                        <div class="col-md-8 ">
                            <select class="form-control" name="car_id" id="select2-car">
                                @if($operate == 'edit' && $data->car_id)
                                    <option data-id="{{ $data->car_id or 0 }}" value="{{ $data->car_id or 0 }}">{{ $data->car_er->name }}</option>
                                @else
                                    <option data-id="0" value="0">选择车辆</option>
                                @endif
                            </select>
                        </div>
                    </div>
                {{--@endif--}}

                {{--订单--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">选择订单</label>--}}
{{--                    <div class="col-md-8 ">--}}
{{--                        <select name="order_list[]" id="select2-order-list" style="width:100%;" multiple="multiple">--}}
{{--                            --}}{{--<option value="{{$data->people_id or 0}}">{{$data->people->name or '请选择作者'}}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

                {{--时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 时间</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <div class="input-group readonly-picker">
                                <input type="text" class="form-control date_picker" name="start" placeholder="开始时间" readonly="readonly"
                                       @if(!empty($data->start_time)) value="{{ date("Y-m-d",$data->start_time) }}" @endif
                                >
                                <span class="input-group-addon"><a class="readonly-clear-this"><i class="fa fa-trash"></i></a></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <div class="input-group readonly-picker">
                                <input type="text" class="form-control date_picker" name="ended" placeholder="结束时间" readonly="readonly"
                                       @if(!empty($data->ended_time)) value="{{ date("Y-m-d",$data->ended_time) }}" @endif
                                >
                                <span class="input-group-addon"><a class="readonly-clear-this"><i class="fa fa-trash"></i></a></span>
                            </div>
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
@endsection




@section('custom-script')
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 添加or编辑
        $("#item-edit-submit").on('click', function() {
            if($.getUrlParam('referrer')) console.log($.getUrlParam('referrer'));
            if(document.referrer) console.log(document.referrer);
            var options = {
                url: "{{ url('/item/circle-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);

                        if($.getUrlParam('referrer')) location.href = decodeURIComponent($.getUrlParam('referrer'));
                        if(document.referrer) location.href = document.referrer;
                        location.href = "{{ url('/item/circle-list-for-all') }}";
                        // history.go(-1);
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        $('#select2-order-list').select2({
            ajax: {
                url: "/item/circle_select2_order_list",
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
//                    console.log(data);
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
        $('#select2-car').select2({
            ajax: {
                url: "{{ url('/item/circle_select2_car') }}",
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
