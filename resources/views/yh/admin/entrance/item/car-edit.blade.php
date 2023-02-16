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
                                                        <input type="radio" name="item_type" value="21" checked="checked"> 车挂
                                                    @else
                                                        <input type="radio" name="item_type" value="21"> 车挂
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
                                    <option value="17.5" @if($operate == 'edit' && $data->trailer_length == '17.5')selected="selected"@endif>17.5</option>
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


                        {{--驾驶员--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">选择驾驶员</label>
                            <div class="col-md-8 ">
                                <select class="form-control" name="driver_id" id="select2-driver">
                                    @if($operate == 'edit' && $data->driver_id)
                                        <option data-id="{{ $data->driver_id or 0 }}" value="{{ $data->driver_id or 0 }}">{{ $data->driver_er->driver_name }}</option>
                                    @else
                                        <option data-id="0" value="0">未指定</option>
                                    @endif
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
                        {{--车辆类型--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">车辆类型</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="car_type" placeholder="车辆类型" value="{{ $data->car_type or '' }}">
                            </div>
                        </div>
                        {{--所有人--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">所有人</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="car_owner" placeholder="所有人" value="{{ $data->car_owner or '' }}">
                            </div>
                        </div>
                        {{--住址--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">住址</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="address" placeholder="住址" value="{{ $data->address or '' }}">
                            </div>
                        </div>
                        {{--使用性质--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">使用性质</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="car_function" placeholder="使用性质" value="{{ $data->car_function or '' }}">
                            </div>
                        </div>
                        {{--品牌--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">品牌</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="car_brand" placeholder="品牌" value="{{ $data->car_brand or '' }}">
                            </div>
                        </div>
                        {{--车辆识别代码--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">车辆识别代码</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="car_identification_number" placeholder="车辆识别代码" value="{{ $data->car_identification_number or '' }}">
                            </div>
                        </div>
                        {{--发动机号--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">发动机号</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="engine_number" placeholder="发动机号" value="{{ $data->engine_number or '' }}">
                            </div>
                        </div>
                        {{--车头轴距--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">车头轴距</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="locomotive_wheelbase" placeholder="车头轴距" value="{{ $data->locomotive_wheelbase or '' }}">
                            </div>
                        </div>
                        {{--主油厢--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">主油厢</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="main_fuel_tank" placeholder="主油厢" value="{{ $data->main_fuel_tank or '' }}">
                            </div>
                        </div>
                        {{--副油厢--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">副油厢</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="auxiliary_fuel_tank" placeholder="副油厢" value="{{ $data->auxiliary_fuel_tank or '' }}">
                            </div>
                        </div>
                        {{--总质量--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">总质量</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="total_mass" placeholder="总质量" value="{{ $data->total_mass or '' }}">
                            </div>
                        </div>
                        {{--整备质量--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">整备质量</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="curb_weight" placeholder="整备质量" value="{{ $data->curb_weight or '' }}">
                            </div>
                        </div>
                        {{--核定载质量--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">核定载重质量</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="load_weight" placeholder="核定载重质量" value="{{ $data->load_weight or '' }}">
                            </div>
                        </div>
                        {{--准牵引总质量--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">准牵引总质量</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="traction_mass" placeholder="准牵引总质量" value="{{ $data->traction_mass or '' }}">
                            </div>
                        </div>
                        {{--外廓尺寸--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">外廓尺寸</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="overall_size" placeholder="外廓尺寸" value="{{ $data->overall_size or '' }}">
                            </div>
                        </div>
                        {{--购买日期--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">购买日期</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="purchase_date" placeholder="购买日期" value="{{ $data->purchase_date or '' }}" readonly="readonly">
                            </div>
                        </div>
                        {{--注册日期--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">注册日期</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="registration_date" placeholder="注册日期" value="{{ $data->registration_date or '' }}" readonly="readonly">
                            </div>
                        </div>
                        {{--发证日期--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">发证日期</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="issue_date" placeholder="发证日期" value="{{ $data->issue_date or '' }}" readonly="readonly">
                            </div>
                        </div>
                        {{--检验有效期--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">检验有效期</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="inspection_validity" placeholder="检验有效期" value="{{ $data->inspection_validity or '' }}" readonly="readonly">
                            </div>
                        </div>
                        {{--运输证-年检时间--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">运输证-年检时间</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="transportation_license_validity" placeholder="运输证-年检时间" value="{{ $data->transportation_license_validity or '' }}" readonly="readonly">
                            </div>
                        </div>
                        {{--运输证-换证期--}}
                        <div class="form-group">
                            <label class="control-label col-md-2">运输证-换证期</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control date_picker" name="transportation_license_change_time" placeholder="运输证-换证期" value="{{ $data->transportation_license_change_time or '' }}" readonly="readonly">
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




@section('custom-js')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
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


        //
        $('#select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
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
            templateSelection: function(data, container) {
                $(data.element).attr("data-name",data.driver_name);
                $(data.element).attr("data-phone",data.driver_phone);
                $(data.element).attr("data-sub-name",data.sub_driver_name);
                $(data.element).attr("data-sub-phone",data.sub_driver_phone);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        // $("#select2-driver").on("select2:select",function(){
        //     var $id = $(this).val();
        //     if($id > 0)
        //     {
        //         $('input[name=driver_name]').val($(this).find('option:selected').attr('data-name'));
        //         $('input[name=driver_phone]').val($(this).find('option:selected').attr('data-phone'));
        //         $('input[name=copilot_name]').val($(this).find('option:selected').attr('data-sub-name'));
        //         $('input[name=copilot_phone]').val($(this).find('option:selected').attr('data-sub-phone'));
        //     }
        // });



    });
</script>
@endsection
