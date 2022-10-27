@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '用户列表' }}</a></li>
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
                <input type="hidden" name="operate_type" value="{{ $operate_type or 'order' }}" readonly>


                {{--选择客户--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择客户</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="client_id" id="select2-client">
                            @if($operate == 'edit' && $data->client_id)
                                <option data-id="{{ $data->client_id or 0 }}" value="{{ $data->client_id or 0 }}">{{ $data->client_er->username }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>
                {{--费用--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 金额</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="amount" placeholder="金额" value="{{ $data->amount or '' }}">
                    </div>
                </div>

                {{--车辆类型--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 车辆类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->user_type, [0,1]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 11))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            <input type="radio" name="user_type" value="11" checked="checked"> 自有
                                            {{--<input type="radio" name="user_type" value=11--}}
                                            {{--@if($operate == 'edit' && $data->user_type == 11) checked="checked" @endif--}}
                                            {{--> 总经理--}}
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 21))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 21)
                                                <input type="radio" name="user_type" value=21 checked="checked"> 外请
                                            @else
                                                <input type="radio" name="user_type" value=21> 外请
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11,21]))
                            @if($operate == 'create' || ($operate == 'edit' && $data->user_type == 22))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->user_type == 22)
                                                <input type="radio" name="user_type" value=22 checked="checked"> 外配
                                            @else
                                                <input type="radio" name="user_type" value=22> 外配
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif
                            @endif

                        </div>
                    </div>
                </div>

                {{--选择自有车辆--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自有车辆</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control" name="car_id" id="select2-car">
                                @if($operate == 'edit' && $data->car_id)
                                    <option data-id="{{ $data->car_id or 0 }}" value="{{ $data->car_id or 0 }}">{{ $data->car_er->name }}</option>
                                @else
                                    <option data-id="0" value="0">选择车辆</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control" name="trailer_id" id="select2-trailer">
                                @if($operate == 'edit' && $data->trailer_id)
                                    <option data-id="{{ $data->trailer_id or 0 }}" value="{{ $data->trailer_id or 0 }}">{{ $data->trailer_er->name }}</option>
                                @else
                                    <option data-id="0" value="0">选择车挂</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{--选择自有车辆--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择车挂</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="trailer_id1" id="select2-trailer">
                            <option data-id="0" value="0">选择车挂</option>
                        </select>
                    </div>
                </div>


                {{--派车时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 派车时间</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="assign_date" placeholder="派车时间"
                               @if(!empty($data->assign_time)) value="{{ date("Y-m-d",$data->assign_time) }}" @endif
                        >
                    </div>
                </div>
                {{--规定时间--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 规定时间</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="should_departure" placeholder="应出发时间"
                                   @if(!empty($data->should_departure_time)) value="{{ date("Y-m-d H:i",$data->should_departure_time) }}" @endif
                            >
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="should_arrival" placeholder="应到达时间"
                                   @if(!empty($data->should_arrival_time)) value="{{ date("Y-m-d H:i",$data->should_arrival_time) }}" @endif
                            >
                        </div>
                    </div>
                </div>

                {{--出发地--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 出发地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="departure_place" placeholder="出发地" value="{{ $data->departure_place or '' }}">
                    </div>
                </div>
                {{--目的地--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 目的地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="destination_place" placeholder="目的地" value="{{ $data->destination_place or '' }}">
                    </div>
                </div>
                {{--经停地--}}
                <div class="form-group">
                    <label class="control-label col-md-2">经停地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="stopover_place" placeholder="经停地" value="{{ $data->stopover_place or '' }}">
                    </div>
                </div>

                {{--经停地--}}
                <div class="form-group">
                    <label class="control-label col-md-2">经停地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="stopover_place1" placeholder="经停地" value="{{ $data->stopover or '' }}">
                    </div>
                </div>
                {{--经停地--}}
                <div class="form-group">
                    <label class="control-label col-md-2">经停地</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="stopover_place2" placeholder="经停地" value="{{ $data->stopover or '' }}">
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


        // 【选择时间】
        $("#form-edit-item").on('click', "input[name=time_type]", function() {
            // checkbox
//            if($(this).is(':checked')) {
//                $('.time-show').show();
//            } else {
//                $('.time-show').hide();
//            }
            // radio
            var $value = $(this).val();
            if($value == 1) {
                $('.time-show').show();
            } else {
                $('.time-show').hide();
            }
        });


        $('input[name=assign_date]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD"
        });

        $('input[name=should_departure]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm"
        });
        $('input[name=should_arrival]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm"
        });

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/order-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/item/order-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        //
        $('#select2-client').select2({
            ajax: {
                url: "{{ url('/item/order_select2_client') }}",
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
        $('#select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
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
        $('#select2-trailer').select2({
            ajax: {
                url: "{{ url('/item/order_select2_trailer') }}",
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
