@extends(env('TEMPLATE_DK_CUSTOMER').'layout.layout')


@section('head_title')
    {{ $title_text or '我的话单' }} - 客户系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description'){{ $title_text or '我的话单' }} - 客户系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">

            <div class="box-header with-border" style="padding:6px 10px;margin:4px;">

                <h3 class="box-title">{{ $title_text or '我的话单' }}</h3>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-telephone-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="telephone-id" placeholder="ID" value="{{ $telephone_id or '' }}" style="width:88px;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="telephone-assign" placeholder="创建日期" value="{{ $assign or '' }}" readonly="readonly" style="width:100px;text-align:center;" />
                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                            <i class="fa fa-chevron-right"></i>
                        </button>

{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="telephone-delivered_date" placeholder="交付日期" value="" readonly="readonly" style="width:100px;text-align:center;" />--}}


{{--                        <select class="form-control form-filter select2-box telephone-select2-project" name="telephone-project" style="width:120px;">--}}
{{--                            @if($project_id > 0)--}}
{{--                                <option value="-1">选择项目</option>--}}
{{--                                <option value="{{ $project_id }}" selected="selected">{{ $project_name }}</option>--}}
{{--                            @else--}}
{{--                                <option value="-1">选择项目</option>--}}
{{--                            @endif--}}
{{--                        </select>--}}

{{--                        @if(in_array($me->user_type,[0,1,9,11,61,66]))--}}
{{--                            <select class="form-control form-filter select2-box telephone-select2-customer" name="telephone-customer" style="width:160px;">--}}
{{--                                <option value="-1">选择客户</option>--}}
{{--                                @foreach($customer_list as $v)--}}
{{--                                    <option value="{{ $v->id }}" @if($v->id == $customer_id) selected="selected" @endif>{{ $v->username }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        @endif--}}


{{--                        <select class="form-control form-filter select2-box" name="telephone-delivered-result[]" multiple="multiple" style="width:100px;">--}}
{{--                            <option value="-1">交付结果</option>--}}
{{--                            @foreach(config('info.delivered_result') as $v)--}}
{{--                                <option value="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        <input type="text" class="form-control form-filter filter-keyup" name="telephone-customer-name" placeholder="客户姓名" value="{{ $customer_name or '' }}" style="width:88px;" />--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup" name="telephone-customer-phone" placeholder="客户电话" value="{{ $customer_phone or '' }}" style="width:88px;" />--}}


{{--                        <select class="form-control form-filter select2-box select2-district-city" name="telephone-city" id="telephone-city" data-target="#telephone-district" style="width:120px;height:100%;">--}}
{{--                            <option value="-1">选择城市</option>--}}
{{--                            @if(!empty($district_city_list) && count($district_city_list) > 0)--}}
{{--                            @foreach($district_city_list as $v)--}}
{{--                                <option value="{{ $v->district_city }}" @if($district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>--}}
{{--                            @endforeach--}}
{{--                            @endif--}}
{{--                        </select>--}}
{{--                        <select class="form-control form-filter select2-box select2-district-district" name="telephone-district[]" id="telephone-district" data-target="telephone-city" multiple="multiple" placeholder="选择区域" style="width:160px;">--}}
{{--                            <option value="-1">选择区域</option>--}}
{{--                            @if(!empty($district_district_list) && count($district_district_list) > 0)--}}
{{--                            @foreach($district_district_list as $v)--}}
{{--                                <option value="{{ $v }}" @if($district_district == $v) selected="selected" @endif>{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                            @endif--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="telephone-is-wx" style="width:88px;">--}}
{{--                            <option value="-1">是否+V</option>--}}
{{--                            <option value="1" @if($is_wx == "1") selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_wx == "0") selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="telephone-is-repeat" style="width:88px;">--}}
{{--                            <option value="-1">是否重复</option>--}}
{{--                            <option value="1" @if($is_repeat >= 1) selected="selected" @endif>是</option>--}}
{{--                            <option value="0" @if($is_repeat == 0) selected="selected" @endif>否</option>--}}
{{--                        </select>--}}

{{--                        <input type="text" class="form-control form-filter filter-keyup" name="telephone-description" placeholder="通话小结" value="" style="width:120px;" />--}}

                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-order">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-primary filter-refresh" id="filter-refresh-for-order">
                            <i class="fa fa-circle-o-notch"></i> 刷新
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-warning filter-cancel" id="filter-cancel-for-order">
                            <i class="fa fa-undo"></i> 重置
                        </button>


                        <div class="pull-left clear-both">
                        </div>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered table-hover telephone-column' id='datatable_ajax'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                </div>

            </div>


            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
            <div class="box-footer _none" style="padding:4px 10px;">
                <div class="row" style="margin:2px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter _none">
                                <option value="-1">请选择操作类型</option>
                                <option value="启用">启用</option>
                                <option value="禁用">禁用</option>
                                <option value="删除">删除</option>
                                <option value="彻底删除">彻底删除</option>
                            </select>
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-operate"><i class="fa fa-check"></i> 批量操作</span>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出</span>

{{--                            <select name="bulk-operate-exported-status" class="form-control form-filter">--}}
{{--                                <option value="-1">请选导出状态</option>--}}
{{--                                <option value="1">已导出</option>--}}
{{--                                <option value="0">待导出</option>--}}
{{--                            </select>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-exported-status"><i class="fa fa-check"></i> 批量更改导出状态</span>--}}

                            <select name="bulk-operate-assign-status" class="form-control form-filter">
                                <option value="-1">请选分配状态</option>
                                <option value="1">已分配</option>
                                <option value="0">待分配</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-assign-status"><i class="fa fa-check"></i> 批量更改导出状态</span>

                            <select name="bulk-operate-staff-id" class="form-control form-filter">
                                <option value="-1">选择员工</option>
                                @foreach($staff_list as $v)
                                    <option value="{{ $v->id }}" @if($v->id == $staff_id) selected="selected" @endif>{{ $v->username }}</option>
                                @endforeach
                            </select>
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-assign-staff"><i class="fa fa-check"></i> 批量分配</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


{{--显示-附件-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-attachment">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单【<span class="attachment-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>



            {{--attachment--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="">
            <div class="box-body attachment-box">

            </div>
            </form>


            <div class="box-header with-border margin-top-16px margin-bottom-16px-">
                <h4 class="box-title">【添加附件】</h4>
            </div>

            {{--上传附件--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="modal-attachment-set-form">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="attachment-set-operate" value="item-telephone-attachment-set" readonly>
                <input type="hidden" name="attachment-set-telephone-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-telephone-attachment-set" readonly>
                <input type="hidden" name="order_id" value="0" readonly>
                <input type="hidden" name="operate_type" value="add" readonly>
                <input type="hidden" name="column_key" value="attachment" readonly>


                <div class="form-group">
                    <label class="control-label col-md-2">附件名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="attachment_name" autocomplete="off" placeholder="附件名称" value="">
                    </div>
                </div>

                {{--多图上传--}}
                <div class="form-group">

                    <label class="control-label col-md-2">图片上传</label>

                    <div class="col-md-8">
                        <input id="multiple-images" type="file" class="file-multiple-images" name="multiple_images[]" multiple >
                    </div>

                </div>

                {{--多图上传--}}
                <div class="form-group _none">

                    <label class="control-label col-md-2" style="clear:left;">选择图片</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                            <span class="btn-file">
                                <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                <input type="file" name="attachment_file" />
                            </span>
                                <span class="">
                                <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                            </span>
                            </div>
                        </div>
                        <div id="titleImageError" style="color: #a94442"></div>

                    </div>

                </div>

            </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-attachment-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-attachment-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--上架-put-on--}}
<div class="modal fade modal-main-body" id="modal-body-for-put-on-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">线索上架【<span class="put-on-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-put-on-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="put-on-set-operate" value="item-telephone-put-on-option-set" readonly>
                    <input type="hidden" name="put-on-set-operate-type" value="add" readonly>
                    <input type="hidden" name="put-on-set-telephone-id" value="0" readonly>
                    <input type="hidden" name="put-on-set-column-key" value="" readonly>


                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付结果</label>
                        <div class="col-md-8 " id="put-on-set-distributed-list">
                        </div>
                    </div>
                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付订单</label>
                        <div class="col-md-8 " id="put-on-set-distributed-telephone-list">
                        </div>
                    </div>
                    <div class="form-group _none">
                        <label class="control-label col-md-2">已分发客户</label>
                        <div class="col-md-8 " id="put-on-set-distributed-customer-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">上架类型</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="put-on-set-sale-type" style="width:48%;" id="">
{{--                                <option value="-1">交付结果</option>--}}
{{--                                @foreach(config('info.delivered_result') as $v)--}}
{{--                                    <option value="{{ $v }}">{{ $v }}</option>--}}
{{--                                @endforeach--}}
                                <option value="-1">选择类型</option>
                                <option value="1">一般</option>
                                <option value="11">优选</option>
                                <option value="66">专享</option>
                            </select>
                        </div>
                    </div>
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-2">选择客户</label>--}}
{{--                        <div class="col-md-8 ">--}}
{{--                            <select class="form-control select2-box" name="put-on-set-customer-id" style="width:48%;" id="">--}}
{{--                                <option value="-1">选择客户</option>--}}
{{--                                @foreach($customer_preferential_list as $v)--}}
{{--                                    <option value="{{ $v->id }}">{{ $v->username }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-put-on-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-put-on-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--分发-distribute--}}
<div class="modal fade modal-main-body" id="modal-body-for-distribute-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">订单分发【<span class="distribute-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-distribute-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="distribute-set-operate" value="item-telephone-distribute-option-set" readonly>
                    <input type="hidden" name="distribute-set-operate-type" value="add" readonly>
                    <input type="hidden" name="distribute-set-telephone-id" value="0" readonly>
                    <input type="hidden" name="distribute-set-column-key" value="" readonly>


                    <div class="form-group _none">
                        <label class="control-label col-md-2">已交付结果</label>
                        <div class="col-md-8 " id="distribute-set-distributed-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已交付订单</label>
                        <div class="col-md-8 " id="distribute-set-distributed-telephone-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">已分发客户</label>
                        <div class="col-md-8 " id="distribute-set-distributed-customer-list">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">选择客户</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="distribute-set-customer-id" style="width:48%;" id="">
                                <option value="-1">选择客户</option>
                                @foreach($customer_list as $v)
                                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2">分发结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-box" name="distribute-set-delivered-result" style="width:48%;" id="">
                                <option value="-1">交付结果</option>
                                @foreach(config('info.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-distribute-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-distribute-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--option--}}
<div class="option-container _none">

    {{--城市列表--}}
{{--    <div id="location-city-option-list">--}}
{{--        <option value="">选择城市</option>--}}
{{--        @foreach(config('info.location_city') as $k => $v)--}}
{{--            <option value ="{{ $k }}" data-index="{{ $loop->index }}">{{ $k }}</option>--}}
{{--        @endforeach--}}
{{--    </div>--}}
    <div id="location-city-option-list">
        <option value="">选择城市</option>
        @if(!empty($district_city_list) && count($district_city_list) > 0)
            @foreach($district_city_list as $v)
                <option value="{{ $v->district_city }}" @if($district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>
            @endforeach
        @endif
    </div>


    {{--是否+V--}}
    <div id="option-list-for-is-wx">
        <label class="control-label col-md-2">是否+V</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="is_wx" value="0" class="info-set-column"> 否
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="is_wx" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>
    {{--是否+V--}}
    <div id="option-list-for-is_distributive_condition">
        <label class="control-label col-md-2">是否符合分发</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="0" class="info-set-column"> 否
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>
    <div id="option-list-for-is_distributive_condition-2">
        <label class="control-label col-md-2">是否符合分发</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="0" class="info-set-column" checked="checked"> 否
                        </label>
                    </span>
                </button>
                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="option_is_distributive_condition" value="1" class="info-set-column"> 是
                        </label>
                    </span>
                </button>

            </div>
        </div>
    </div>



    {{--选择客户--}}
    <div id="option-list-for-customer">
        <option value="-1">选择客户</option>
        @foreach($customer_list as $v)
            <option value="{{ $v->id }}">{{ $v->username }}</option>
        @endforeach
    </div>

    {{--审核结果--}}
    <div id="option-list-for-inspected-result">
        <option value="-1">审核结果</option>
        @foreach(config('info.inspected_result') as $v)
            <option value="{{ $v }}">{{ $v }}</option>
        @endforeach
    </div>

    {{--交付结果--}}
    <div id="option-list-for-delivered-result">
        <option value="-1">交付结果</option>
        @foreach(config('info.delivered_result') as $v)
            <option value="{{ $v }}">{{ $v }}</option>
        @endforeach
    </div>

    {{--牙齿数量--}}
    <div id="option-list-for-teeth-count">
        <option value="-1">选择牙齿数量</option>
        @foreach(config('info.teeth_count') as $v)
            <option value="{{ $v }}">{{ $v }}</option>
        @endforeach
    </div>

    {{--渠道来源--}}
    <div id="option-list-for-channel-source">
        <option value="-1">选择渠道来源</option>
        @foreach(config('info.channel_source') as $v)
            <option value="{{ $v }}">{{ $v }}</option>
        @endforeach
    </div>

    {{--客户意向--}}
    <div id="option-list-for-customer-intention">
        <option value="-1">选择客户意向</option>
        @foreach(config('info.client_intention') as $k => $v)
            <option value="{{ $k }}">{{ $v }}</option>
        @endforeach
    </div>

</div>




{{--修改列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-modify-list">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-modify-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value="-1">选择属性</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable_ajax_record'>
                    <thead>
                        <tr role='row' class='heading'>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection




@section('custom-css')
@endsection
@section('custom-style')
<style>
    .tableArea table { min-width:100%; }
    /*.tableArea table { width:100% !important; min-width:1380px; }*/
    /*.tableArea table tr th, .tableArea table tr td { white-space:nowrap; }*/

    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .select2-container { height:100%; btelephone-radius:0; float:left; }
    .select2-container .select2-selection--single { btelephone-radius:0; }

    .select2-container--classic .select2-selection--multiple  { height:34px; btelephone-radius:0; }

    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-income { background:#8FEBE5; }
    .bg-route { background:#FFEBE5; }
    .bg-finance { background:#E2FCAB; }
    .bg-empty { background:#F6C5FC; }
    .bg-journey { background:#F5F9B4; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[ @if(!in_array($length,[20,50, 100, 200])) {{ $length.',' }} @endif 20,50, 100, 200], [ @if(!in_array($length,[20,50, 100, 200])) {{ $length.',' }} @endif "20", "50", "100", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 20 }},
                "ajax": {
                    'url': "{{ url('/mine/telephone-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="telephone-id"]').val();
                        d.remark = $('input[name="telephone-remark"]').val();
                        d.description = $('input[name="telephone-description"]').val();
                        d.delivered_date = $('input[name="telephone-delivered_date"]').val();
                        d.assign = $('input[name="telephone-assign"]').val();
                        d.assign_start = $('input[name="telephone-start"]').val();
                        d.assign_ended = $('input[name="telephone-ended"]').val();
                        d.name = $('input[name="telephone-name"]').val();
                        d.title = $('input[name="telephone-title"]').val();
                        d.keyword = $('input[name="telephone-keyword"]').val();
                        d.department_district = $('select[name="telephone-department-district[]"]').val();
                        d.staff = $('select[name="telephone-staff"]').val();
                        d.project = $('select[name="telephone-project"]').val();
                        d.customer = $('select[name="telephone-customer"]').val();
                        d.status = $('select[name="telephone-status"]').val();
                        d.order_type = $('select[name="telephone-type"]').val();
                        d.customer_name = $('input[name="telephone-customer-name"]').val();
                        d.customer_phone = $('input[name="telephone-customer-phone"]').val();
                        d.is_wx = $('select[name="telephone-is-wx"]').val();
                        d.is_repeat = $('select[name="telephone-is-repeat"]').val();
                        d.inspected_status = $('select[name="telephone-inspected-status"]').val();
                        d.inspected_result = $('select[name="telephone-inspected-result[]"]').val();
                        d.delivered_status = $('select[name="telephone-delivered-status"]').val();
                        d.delivered_result = $('select[name="telephone-delivered-result[]"]').val();
                        d.district_city = $('select[name="telephone-city"]').val();
                        d.district_district = $('select[name="telephone-district[]"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "sDom": '<i><l><p><t>',
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
                "scrollY": ($(document).height() - 448)+"px",
                "scrollCollapse": true,
                "fixedColumns": {

                    "leftColumns": "@if($is_mobile_equipment) 1 @else 2 @endif",
                    "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                },
                "showRefresh": true,
                "columnDefs": [
                    {
                        // "targets": [0,4,8,9,10,11],
                        // "visible": false,
                    }
                ],
                "columns": [
                   {
                       "title": "选择",
                       "width": "32px",
                       "data": "id",
                       "orderable": false,
                       render: function(data, type, row, meta) {
                           return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                       }
                   },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "60",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "操作",
                        "data": 'id',
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_abandon = '';
                            var $html_completed = '';
                            var $html_verified = '';
                            var $html_detail_inspected = '';
                            var $html_push = '';
                            var $html_call = '';
                            var $html_deliver = '';
                            var $html_distribute = '';

                            var $html_purchase = '<a class="btn btn-xs bg-blue item-purchase-submit" data-id="'+data+'">购买</a>';

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }


                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                            //
                            if("{{ in_array($me->user_type,[0,1,11,61,66]) }}")
                            {

                                if(row.sale_status == 1)
                                {
                                    // $html_push = '<a class="btn btn-xs bg-teal item-modal-show-for-deliver" data-id="'+data+'" data-key="customer_id">交付</a>';
                                    // $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-submit" data-id="'+data+'">交付</a>';
                                    $html_call = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">上架</a>';
                                    $html_call = '<a class="btn btn-xs bg-yellow item-call-submit" data-id="'+data+'">下架</a>';
                                }
                                else
                                {
                                    // $html_deliver = '<a class="btn btn-xs bg-green disabled- item-deliver-submit" data-id="'+data+'">再交4</a>';
                                    $html_call = '<a class="btn btn-xs bg-green item-put-on-show" data-id="'+data+'">上架</a>';
                                }
                            }

                            $html_call = '<a class="btn btn-xs bg-green item-call-submit" data-id="'+data+'">拨号</a>';


                            var $html =
//                                    $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
//                                 $html_completed+
                                $html_edit+
                                $html_publish+
                                // $html_detail+
                                $html_detail_inspected+
                                $html_push+
                                $html_call+
                                $html_deliver+
                                // $html_distribute+
                                // $html_delete+
                                $html_record+
// $html_abandon+
//                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
//                                $more_html+
                                '';
                            return $html;

                        }
                    },
                    // {
                    //     "title": "销售状态",
                    //     "data": "item_status",
                    //     "className": "",
                    //     "width": "72px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         var $result_html = '';
                    //         if(data == 0) $result_html = '<small class="btn-xs bg-teal">待上架</small>';
                    //         else if(data == 1) $result_html = '<small class="btn-xs bg-blue">已上架</small>';
                    //         else if(data == 9) $result_html = '<small class="btn-xs bg-black">已下架</small>';
                    //         else $result_html = '<small class="btn-xs bg-black">error</small>';
                    //         return $result_html;
                    //     }
                    // },
                    // {
                    //     "title": "销售类型",
                    //     "data": "item_type",
                    //     "className": "",
                    //     "width": "72px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         var $result_html = '';
                    //         if(data == 0) $result_html = '<small class="btn-xs bg-teal">未上架</small>';
                    //         else if(data == 1) $result_html = '<small class="btn-xs bg-blue">一般</small>';
                    //         else if(data == 11) $result_html = '<small class="btn-xs bg-green">优选</small>';
                    //         else if(data == 66) $result_html = '<small class="btn-xs bg-yellow">独享</small>';
                    //         else $result_html = '<small class="btn-xs bg-black">error</small>';
                    //         return $result_html;
                    //     }
                    // },
                    {
                        "title": "购买结果",
                        "data": "sale_result",
                        "className": "",
                        "width": "72px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $result_html = '';
                            if(data == 0) $result_html = '<small class="btn-xs bg-teal">待购买</small>';
                            else if(data == 1) $result_html = '<small class="btn-xs bg-blue">已接单</small>';
                            else if(data == 9) $result_html = '<small class="btn-xs bg-yellow">已购买</small>';
                            else $result_html = '<small class="btn-xs bg-black">error</small>';
                            return $result_html;
                        }
                    },
                    {
                        "title": "分派员工",
                        "data": "customer_staff_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('customer_staff');
                                $(nTd).attr('data-id',row.id).attr('data-name','分派员工');
                                $(nTd).attr('data-key','customer_staff_id').attr('data-value',row.id);
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.customer_staff_er == null)
                            {
                                return '未指定';
                            }
                            else {
                                return '<a href="javascript:void(0);">'+row.customer_staff_er.username+'</a>';
                            }
                        }
                    },
                    {
                        "title": "拨号次数",
                        "data": "call_num",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "最近拨打时间",
                        "data": "last_call_time",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "电话",
                        "data": "telephone",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.sale_result == 9) return data;
                            return "****";
                        }
                    },
                    // {
                    //     "title": "创建人",
                    //     "data": "creator_id",
                    //     "className": "",
                    //     "width": "80px",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                    //     }
                    // },
                    {
                        "title": "创建时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    var $obj = new Object();
                    if($('input[name="telephone-id"]').val())  $obj.telephone_id = $('input[name="telephone-id"]').val();
                    if($('input[name="telephone-assign"]').val())  $obj.assign = $('input[name="telephone-assign"]').val();
                    if($('input[name="telephone-start"]').val())  $obj.assign_start = $('input[name="telephone-start"]').val();
                    if($('input[name="telephone-ended"]').val())  $obj.assign_ended = $('input[name="telephone-ended"]').val();
                    if($('select[name="telephone-department-district"]').val() > 0)  $obj.department_district_id = $('select[name="telephone-department-district"]').val();
                    if($('select[name="telephone-staff"]').val() > 0)  $obj.staff_id = $('select[name="telephone-staff"]').val();
                    if($('select[name="telephone-customer"]').val() > 0)  $obj.customer_id = $('select[name="telephone-customer"]').val();
                    if($('select[name="telephone-project"]').val() > 0)  $obj.project_id = $('select[name="telephone-project"]').val();
                    if($('input[name="telephone-customer-name"]').val())  $obj.customer_name = $('input[name="telephone-customer-name"]').val();
                    if($('input[name="telephone-customer-phone"]').val())  $obj.customer_phone = $('input[name="telephone-customer-phone"]').val();
                    if($('select[name="telephone-type"]').val() > 0)  $obj.order_type = $('select[name="telephone-type"]').val();
                    if($('select[name="telephone-is-wx"]').val() > 0)  $obj.is_delay = $('select[name="telephone-is-wx"]').val();
                    if($('select[name="telephone-is-repeat"]').val() > 0)  $obj.is_delay = $('select[name="telephone-is-repeat"]').val();
                    if($('select[name="telephone-inspected-status"]').val() != -1)  $obj.inspected_status = $('select[name="telephone-inspected-status"]').val();
                    if($('select[name="telephone-delivered-status"]').val() != -1)  $obj.delivered_status = $('select[name="telephone-delivered-status"]').val();
                    // if($('select[name="telephone-city"]').val() != -1)  $obj.district_city = $('select[name="telephone-city"]').val();
                    // if($('select[name="telephone-district"]').val() != -1)  $obj.district_district = $('select[name="telephone-district"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != 20) $obj.length = $page_length;
                    var $page_start = this.api().context[0]._iDisplayStart; // 当前页开始
                    var $pagination = ($page_start / $page_length) + 1; //得到页数值 比页码小1
                    if($pagination > 1) $obj.page = $pagination;


                    if(JSON.stringify($obj) != "{}")
                    {
                        var $url = url_build('',$obj);
                        history.replaceState({page: 1}, "", $url);
                    }
                    else
                    {
                        $url = "{{ url('/item/telephone-list') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();

    $(function () {

        var $id = $.getUrlParam('id');
        if($id) $('input[name="telephone-id"]').val($id);
        TableDatatablesAjax.init();
        // $('#datatable_ajax').DataTable().init().fnPageChange(3);
    });
</script>


<script>
    var TableDatatablesAjax_record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_record = $('#datatable_ajax_record');
            dt_record.DataTable().destroy();
            var ajax_datatable_record = dt_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/telephone-modify-record?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "拨号结果",
                        "data": "call_result",
                        "className": "",
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data) return '<small class="btn-xs bg-green">拨号成功</small>';
                            else return '<small class="btn-xs bg-orange">拨号失败</small>';
                        }
                    },
                    {
                        "title": "拨号说明",
                        "data": "call_result_msg",
                        "className": "",
                        "width": "240px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "title": "操作人",
                        "data": "creator_id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            // if(row.record_object == 19)
                            // {
                            //     return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            // }
                            // else if(row.record_object == 89)
                            // {
                            //     return row.customer_staff_er == null ? '未知' : '<a href="javascript:void(0);">'+row.customer_staff_er.username+'</a>';
                            // }
                            // else return '--';
                        }
                    },
                    {
                        "title": "操作时间",
                        "data": "created_at",
                        "className": "",
                        "width": "108px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_record.on('click', '.modify-filter-submit', function () {
                ajax_datatable_record.ajax.reload();
            });

            dt_record.on('click', '.modify-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_record.ajax.reload();
            });


//            dt_record.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_record
        }
    }();
//        $(function () {
//            TableDatatablesAjax_record.init();
//        });
</script>
@include(env('TEMPLATE_DK_CUSTOMER').'entrance.mine.telephone-list-script')
@endsection
