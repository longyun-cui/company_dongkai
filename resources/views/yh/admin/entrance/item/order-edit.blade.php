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

                {{--需求类型--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="order_type">
                            <option value="0">选择需求类型</option>
                            <option value="1" @if($operate == 'edit' && $data->order_type == 1)selected="selected"@endif>自有</option>
                            <option value="11" @if($operate == 'edit' && $data->order_type == 11)selected="selected"@endif>调车</option>
                            <option value="21" @if($operate == 'edit' && $data->order_type == 21)selected="selected"@endif>配货</option>
                            <option value="31" @if($operate == 'edit' && $data->order_type == 31)selected="selected"@endif>合同单向</option>
                            <option value="41" @if($operate == 'edit' && $data->order_type == 41)selected="selected"@endif>合同双向</option>
                        </select>
                    </div>
                </div>

                {{--需求类型--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 需求类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            <input type="radio" name="car_owner_type" value="1" checked="checked"> 自有
                                            {{--<input type="radio" name="user_type" value=11--}}
                                            {{--@if($operate == 'edit' && $data->user_type == 11) checked="checked" @endif--}}
                                            {{--> 总经理--}}
                                        </label>
                                    </span>
                                </button>
                            @endif

                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 21))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->car_owner_type == 21)
                                                <input type="radio" name="car_owner_type" value=21 checked="checked"> 外请（调车）
                                            @else
                                                <input type="radio" name="car_owner_type" value=21> 外请（调车）
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif

                            @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 41))
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            @if($operate == 'edit' && $data->car_owner_type == 41)
                                                <input type="radio" name="car_owner_type" value=41 checked="checked"> 外配（配货）
                                            @else
                                                <input type="radio" name="car_owner_type" value=41> 外配（配货）
                                            @endif
                                        </label>
                                    </span>
                                </button>
                            @endif

                        </div>
                    </div>
                </div>

                {{--自有车辆--}}
                @if($operate == 'create' || ($operate == 'edit' && $data->car_owner_type == 1))
                <div class="form-group inside-car">
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
                @endif

                {{--外请或外派车辆--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外部车辆</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="outside_car" placeholder="外部车车牌" value="{{ $data->outside_car or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="outside_trailer" placeholder="外部车挂" value="{{ $data->outside_trailer or '' }}">
                        </div>
                    </div>
                </div>
                @endif

                {{--主驾--}}
                <div class="form-group">
                    <label class="control-label col-md-2">主驾</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_name" placeholder="姓名" value="{{ $data->driver_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="driver_phone" placeholder="电话" value="{{ $data->driver_phone or '' }}">
                        </div>
                    </div>
                </div>
                {{--副驾--}}
                <div class="form-group">
                    <label class="control-label col-md-2">副驾</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="copilot_name" placeholder="姓名" value="{{ $data->copilot_name or '' }}">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="copilot_phone" placeholder="电话" value="{{ $data->copilot_phone or '' }}">
                        </div>
                    </div>
                </div>

                {{--箱型--}}
                <div class="form-group outside-car _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 选择箱型</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="container_type" id="select2-container">
                            <option value="0">选择</option>
                            <option value="9.6">9.6</option>
                            <option value="12.5">12.5</option>
                            <option value="15">15</option>
                            <option value="16.5">16.5</option>
                        </select>
                    </div>
                </div>


                {{--箱型--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
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
                @endif
                {{--车挂尺寸--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
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
                @endif
                {{--承载方数--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
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
                @endif
                {{--承载重量--}}
                @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
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
                @endif
                {{--轴数--}}
                    @if($operate == 'create' || ($operate == 'edit' && ($data->car_owner_type == 21 || $data->car_owner_type == 41)))
                <div class="form-group outside-car" @if($operate == 'create') style="display:none" @endif>
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
                @endif


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

                {{--线路--}}
                <div class="form-group">
                    <label class="control-label col-md-2">线路</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="route" placeholder="线路" value="{{ $data->route or '' }}">
                    </div>
                </div>

                {{--固定线路--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">固定线路</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="fixed_route" placeholder="固定线路" value="{{ $data->fixed_route or '' }}">
                    </div>
                </div>
                {{--临时线路--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">临时线路</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="temporary_route" placeholder="临时线路" value="{{ $data->temporary_route or '' }}">
                    </div>
                </div>




                {{--所属公司--}}
                <div class="form-group">
                    <label class="control-label col-md-2">所属公司</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="subordinate_company" placeholder="所属公司" value="{{ $data->subordinate_company or '' }}">
                    </div>
                </div>
                {{--回单状态--}}
                <div class="form-group">
                    <label class="control-label col-md-2">回单状态</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="receipt_status" placeholder="回单状态" value="{{ $data->receipt_status or '' }}">
                    </div>
                </div>
                {{--回单地址--}}
                <div class="form-group">
                    <label class="control-label col-md-2">回单地址</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="receipt_address" placeholder="回单地址" value="{{ $data->receipt_address or '' }}">
                    </div>
                </div>
                {{--GPS--}}
                <div class="form-group">
                    <label class="control-label col-md-2">GPS</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="GPS" placeholder="GPS" value="{{ $data->GPS or '' }}">
                    </div>
                </div>


                {{--单号--}}
                <div class="form-group">
                    <label class="control-label col-md-2">单号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="order_number" placeholder="单号" value="{{ $data->order_number or '' }}">
                    </div>
                </div>
                {{--收款人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">收款人</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="payee_name" placeholder="收款人" value="{{ $data->payee_name or '' }}">
                    </div>
                </div>
                {{--安排人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">安排人</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="arrange_people" placeholder="安排人" value="{{ $data->arrange_people or '' }}">
                    </div>
                </div>
                {{--车货源--}}
                <div class="form-group">
                    <label class="control-label col-md-2">车货源</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="car_supply" placeholder="车货源" value="{{ $data->car_supply or '' }}">
                    </div>
                </div>
                {{--车辆管理人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">车辆管理人</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="car_managerial_people" placeholder="车辆管理人" value="{{ $data->car_managerial_people or '' }}">
                    </div>
                </div>
                {{--车辆管理人--}}
                <div class="form-group">
                    <label class="control-label col-md-2">重量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="weight" placeholder="重量" value="{{ $data->weight or '' }}">
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


        // 【选择车辆所属】
        $("#form-edit-item").on('click', "input[name=car_owner_type]", function() {
            // checkbox
//            if($(this).is(':checked')) {
//                $('.time-show').show();
//            } else {
//                $('.time-show').hide();
//            }
            // radio
            var $value = $(this).val();
            if($value == 1 || $value == 41) {
                $('.inside-car').show();
                $('.outside-car').hide();
            } else {
                $('.outside-car').show();
                $('.inside-car').hide();
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
