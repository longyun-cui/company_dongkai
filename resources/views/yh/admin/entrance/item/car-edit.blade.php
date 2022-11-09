@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))L.@endif
    {{ $title_text or '编辑车辆' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '车辆列表' }}</a></li>
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




                        {{--车辆类型--}}
                        <div class="form-group form-category">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 车辆类型</label>
                            <div class="col-md-8">
                                <div class="btn-group">

                                    @if(in_array($me->user_type, [0,1,9,11,21,22]))
                                        @if($operate == 'create' || ($operate == 'edit' && $data->item_type == 1))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    <input type="radio" name="item_type" value="1" checked="checked"> 车辆
                                                    {{--<input type="radio" name="item_type" value=11--}}
                                                    {{--@if($operate == 'edit' && $data->user_type == 11) checked="checked" @endif--}}
                                                    {{--> 总经理--}}
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                    @if(in_array($me->user_type, [0,1,9,11,21,22]))
                                        @if($operate == 'create' || ($operate == 'edit' && $data->item_type == 21))
                                        <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate == 'edit' && $data->item_type == 21)
                                                        <input type="radio" name="item_type" value=21 checked="checked"> 车挂
                                                    @else
                                                        <input type="radio" name="item_type" value=21> 车挂
                                                    @endif
                                                </label>
                                            </span>
                                        </button>
                                        @endif
                                    @endif

                                </div>
                            </div>
                        </div>

                        {{--车牌号--}}
                        <div class="form-group">
                            <label class="control-label col-md-2"><sup class="text-red">*</sup> 车牌号</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="name" placeholder="车牌号" value="{{ $data->name or '' }}">
                            </div>
                        </div>

                        {{--箱型--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">箱型</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="trailer_type" id="">
                                    <option value="0">选择箱型</option>
                                    <option value="直板" @if($operate == 'edit' && $data->trailer_type == '直板')selected="selected"@endif>直板</option>
                                    <option value="高栏" @if($operate == 'edit' && $data->trailer_type == '高栏')selected="selected"@endif>高栏</option>
                                    <option value="平板" @if($operate == 'edit' && $data->trailer_type == '平板')selected="selected"@endif>平板</option>
                                    <option value="冷藏" @if($operate == 'edit' && $data->trailer_type == '冷藏')selected="selected"@endif>冷藏</option>
                                </select>
                            </div>
                        </div>

                        {{--车挂尺寸--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">车挂尺寸</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="trailer_length" id="">
                                    <option value="0">选择车挂尺寸</option>
                                    <option value="9.6" @if($operate == 'edit' && $data->trailer_length == '9.6')selected="selected"@endif>9.6</option>
                                    <option value="12.5" @if($operate == 'edit' && $data->trailer_length == '12.5')selected="selected"@endif>12.5</option>
                                    <option value="15" @if($operate == 'edit' && $data->trailer_length == '15')selected="selected"@endif>15</option>
                                    <option value="16.5" @if($operate == 'edit' && $data->trailer_length == '16.5')selected="selected"@endif>16.5</option>
                                </select>
                            </div>
                        </div>
                        {{--承载方数--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">承载方数</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="trailer_volume" id="">
                                    <option value="0">选择承载方数</option>
                                    <option value="125" @if($operate == 'edit' && $data->trailer_volume == 125)selected="selected"@endif>125</option>
                                    <option value="130" @if($operate == 'edit' && $data->trailer_volume == 130)selected="selected"@endif>130</option>
                                    <option value="135" @if($operate == 'edit' && $data->trailer_volume == 135)selected="selected"@endif>135</option>
                                </select>
                            </div>
                        </div>
                        {{--承载重量--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">承载重量</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="trailer_weight" id="">
                                    <option value="0">选择承载重量</option>
                                    <option value="13" @if($operate == 'edit' && $data->trailer_weight == 13)selected="selected"@endif>13吨</option>
                                    <option value="20" @if($operate == 'edit' && $data->trailer_weight == 20)selected="selected"@endif>20吨</option>
                                    <option value="25" @if($operate == 'edit' && $data->trailer_weight == 25)selected="selected"@endif>25吨</option>
                                </select>
                            </div>
                        </div>
                        {{--轴数--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">轴数</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="trailer_axis_count" id="">
                                    <option value="0">选择轴数</option>
                                    <option value="1" @if($operate == 'edit' && $data->trailer_axis_count == 1)selected="selected"@endif>1轴</option>
                                    <option value="2" @if($operate == 'edit' && $data->trailer_axis_count == 2)selected="selected"@endif>2轴</option>
                                    <option value="3" @if($operate == 'edit' && $data->trailer_axis_count == 3)selected="selected"@endif>3轴</option>
                                </select>
                            </div>
                        </div>



                        {{--司机--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">司机</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="linkman_name" placeholder="司机" value="{{ $data->linkman_name or '' }}">
                            </div>
                        </div>
                        {{--手机--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">手机</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="linkman_phone" placeholder="手机" value="{{ $data->linkman_phone or '' }}">
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
            $("#edit-item-submit").on('click', function() {
                var options = {
                    url: "{{ url('/item/car-edit') }}",
                    type: "post",
                    dataType: "json",
                    // target: "#div2",
                    success: function (data) {
                        if(!data.success) layer.msg(data.msg);
                        else
                        {
                            layer.msg(data.msg);
                            location.href = "{{ url('/item/car-list-for-all') }}";
                        }
                    }
                };
                $("#form-edit-item").ajaxSubmit(options);
            });

        });
    </script>
@endsection
