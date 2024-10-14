@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '日报列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')日报列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body" style="margin-bottom:0;">

            <div class="box-header with-border" style="padding:6px 10px;margin:4px;">

                <h3 class="box-title">
                    <span class="statistic-title">日报列表</span>
                    <span class="statistic-time-type-title"></span>
                    <span class="statistic-time-title">（全部）</span>
                </h3>

                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
{{--                    <a class="item-create-link">--}}
{{--                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加订单</button>--}}
{{--                    </a>--}}
{{--                    <a class="item-create-show">--}}
{{--                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加工单</button>--}}
{{--                    </a>--}}
                    <a href="{{ url('/item/daily-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加日报</button>
                    </a>
                </div>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-daily-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="hidden" name="daily-time-type" value="all" readonly>

                        <input type="text" class="form-control form-filter filter-keyup" name="daily-id" placeholder="ID" value="{{ $order_id or '' }}" style="width:88px;" />

                        @if(in_array($me->user_type,[0,9,11,31]))
                            <select class="form-control form-filter select-select2 select2-box daily-company" name="daily-company" style="width:120px;">
                                <option value="-1">选择公司</option>
                                @if(!empty($company_list))
                                    @foreach($company_list as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select class="form-control form-filter select-select2 select2-box daily-channel" name="daily-channel" style="width:120px;">
                                <option value="-1">选择代理</option>
                                @if(!empty($channel_list))
                                    @foreach($channel_list as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select class="form-control form-filter select-select2 select2-box daily-business" name="daily-business" style="width:120px;">
                                <option value="-1">选择商务</option>
                                @if(!empty($business_list))
                                    @foreach($business_list as $v)
                                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                        <select class="form-control form-filter select-select2 select2-box daily-select2-project" name="daily-project" style="width:160px;">
                            @if($project_id > 0)
                                <option value="-1">选择项目</option>
                                <option value="{{ $project_id }}" selected="selected">{{ $project_name }}</option>
                            @else
                                <option value="-1">选择项目</option>
                            @endif
                        </select>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-all">
                            <i class="fa fa-search"></i> 全部搜索
                        </button>


                        {{--按日查看--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">--}}
{{--                            <i class="fa fa-chevron-left"></i>--}}
{{--                        </button>--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-assign" placeholder="日期" value="{{ $assign or '' }}" readonly="readonly" style="width:100px;text-align:center;" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">--}}
{{--                            <i class="fa fa-chevron-right"></i>--}}
{{--                        </button>--}}


                        {{--按天查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-daily">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-daily">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-date">
                            <i class="fa fa-search"></i> 按日查询
                        </button>


                        {{--按月查看--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-pre-for-daily">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month_picker" name="daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn month-pick-next-for-daily">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-month">
                            <i class="fa fa-search"></i> 按月查询
                        </button>


                        {{--按时间段查看--}}
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-start" placeholder="起始日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-ended" placeholder="结束日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit-" id="filter-submit-for-daily-by-period" style="width:100px;">
                            <i class="fa fa-search"></i> 按时间段查询
                        </button>


                        {{--按时间段查看--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-start" placeholder="起始日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="daily-ended" placeholder="结束日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-daily-by-period" style="width:100px;">--}}
{{--                            <i class="fa fa-search"></i> 按时间段查询--}}
{{--                        </button>--}}
                        <button type="button" class="form-control btn btn-flat bg-teal filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
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
                <table class='table table-striped table-bordered table-hover daily-column' id='datatable_ajax'>
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
                    <div class="col-md-offset-0 col-md-9 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>

                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出</span>


                            <input type="text" name="bulk-operate-delivered-description" class="form-control form-filter pull-right" placeholder="交付说明" style="width:50%;">

{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-operate"><i class="fa fa-check"></i> 批量操作</span>--}}
{{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delivered"><i class="fa fa-share"></i> 批量交付</span>
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



{{--显示-审核信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-detail-inspected">
    <div class="col-md-8 col-md-offset-2" id="" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">审核-订单【<span class="info-detail-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-inspected-modal">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="daily-inspect" readonly>
                    <input type="hidden" name="detail-inspected-daily-id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-detail-project">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="project"></div>
                    </div>
                    {{--客户--}}
                    <div class="form-group item-detail-client">
                        <label class="control-label col-md-2">客户</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <label class="col-md-2"></label>
                    </div>
                    {{--电话--}}
                    <div class="form-group item-detail-phone">
                        <label class="control-label col-md-2">电话</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--是否+V--}}
                    <div class="form-group item-detail-is-wx">
                        <label class="control-label col-md-2">是否+V</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--微信号--}}
                    <div class="form-group item-detail-wx-id">
                        <label class="control-label col-md-2">微信号</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate="driver"></div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group item-detail-city-district">
                        <label class="control-label col-md-2">所在城市</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group item-detail-teeth-count">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-8 ">
                            <span class="item-detail-text"></span>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group item-detail-description">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                        </div>
                    </div>
                    {{--审核结果--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">审核结果</label>
                        <div class="col-md-8 ">
                            <select class="form-control select-select2-" name="detail-inspected-result" id="" style="width:100%;">
                                <option value="-1">选择审核结果</option>
                                @foreach(config('info.inspected_result') as $v)
                                    <option value ="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--审核说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">审核说明</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="detail-inspected-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success item-summit-for-detail-inspected" id=""><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default item-cancel-for-detail-inspected" id="">取消</button>
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
                <input type="hidden" name="attachment-set-operate" value="item-daily-attachment-set" readonly>
                <input type="hidden" name="attachment-set-daily-id" value="0" readonly>
                <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                <input type="hidden" name="attachment-set-column-key" value="" readonly>

                <input type="hidden" name="operate" value="item-daily-attachment-set" readonly>
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




{{--修改-基本-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-text-set-operate" value="item-daily-info-text-set" readonly>
                    <input type="hidden" name="info-text-set-daily-id" value="0" readonly>
                    <input type="hidden" name="info-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-text-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="info-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="info-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-时间-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-time-set-operate" value="item-daily-info-time-set" readonly>
                    <input type="hidden" name="info-time-set-daily-id" value="0" readonly>
                    <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-time-set-column-key" value="" readonly>
                    <input type="hidden" name="info-time-set-time-type" value="" readonly>
                    <input type="hidden" name="info-time-set-time-data-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-radio-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-radio-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-radio-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-radio-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-radio-set-operate" value="item-daily-info-option-set" readonly>
                    <input type="hidden" name="info-radio-set-daily-id" value="0" readonly>
                    <input type="hidden" name="info-radio-set-operate-type" value="edit" readonly>
                    <input type="hidden" name="info-radio-set-column-key" value="" readonly>


                    <div class="form-group radio-box">
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-radio-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-radio-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-select-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-select-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改订单【<span class="info-select-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-select-set-operate" value="item-daily-info-option-set" readonly>
                    <input type="hidden" name="info-select-set-daily-id" value="0" readonly>
                    <input type="hidden" name="info-select-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-select-set-column-key" value="" readonly>
                    <input type="hidden" name="info-select-set-column-key2" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-select-set-column-name"></label>
                        <div class="col-md-8 ">
                            <select class="form-control select-primary" name="info-select-set-column-value" style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                            <select class="form-control select-assistant" name="info-select-set-column-value2" style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-select-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-select-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--option--}}
<div class="option-container _none">


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





    {{--代理来源--}}
    <div id="option-list-for-channel-source">
        <option value="-1">选择代理来源</option>
        @foreach(config('info.channel_source') as $v)
            <option value="{{ $v }}">{{ $v }}</option>
        @endforeach
    </div>

    {{--客户意向--}}
    <div id="option-list-for-client-intention">
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
    .tableArea table { min-width:1380px; }
    /*.tableArea table { width:100% !important; min-width:1380px; }*/
    /*.tableArea table tr th, .tableArea table tr td { white-space:nowrap; }*/

    .datatable-search-row .input-group .date-picker-btn { width:30px; }
    .table-hover>tbody>tr:hover td { background-color: #bbccff; }

    .datatable-search-row .input-group .time-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker, .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group select { width:100px; text-align:center; }
    .datatable-search-row .input-group .select2-container { width:120px; }

    .select2-container { height:100%; bdaily-radius:0; float:left; }
    .select2-container .select2-selection--single { bdaily-radius:0; }

    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-route { background:#8FEBE5; }
    .bg-income { background:#FFEBE5; }
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
                // "deferRender": false, // 启用缓存
                "processing": true, // 显示处理状态
                "serverSide": true, // 服务器模式
                "searching": false,
                "iDisplayStart": {{ ($page - 1) * $length }},
                "iDisplayLength": {{ $length or 20 }},
                "ajax": {
                    'url': "{{ url('/item/daily-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="daily-id"]').val();
                        d.remark = $('input[name="daily-remark"]').val();
                        d.description = $('input[name="daily-description"]').val();
                        d.name = $('input[name="daily-name"]').val();
                        d.title = $('input[name="daily-title"]').val();
                        d.keyword = $('input[name="daily-keyword"]').val();
                        d.order_type = $('select[name="daily-type"]').val();
                        d.status = $('select[name="daily-status"]').val();
                        d.staff = $('select[name="daily-staff"]').val();
                        d.company = $('select[name="daily-company"]').val();
                        d.channel = $('select[name="daily-channel"]').val();
                        d.business = $('select[name="daily-business"]').val();
                        d.project = $('select[name="daily-project"]').val();
                        d.time_type = $('input[name="daily-time-type"]').val();
                        d.month = $('input[name="daily-month"]').val();
                        d.date = $('input[name="daily-date"]').val();
                        d.assign = $('input[name="daily-assign"]').val();
                        d.assign_start = $('input[name="daily-start"]').val();
                        d.assign_ended = $('input[name="daily-ended"]').val();
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
                "scrollX": true,
                // "scrollY": false,
                "scrollY": ($(document).height() - 360)+"px",
                "scrollCollapse": true,
                "fixedColumns": {

                    @if($me->department_district_id == 0)
                        "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @else
                        "leftColumns": "@if($is_mobile_equipment) 1 @else 6 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
                    @endif
                },
                "showRefresh": true,
                "columnDefs": [
{{--                    @if(!in_array($me->user_type,[0,1,11]))--}}
{{--                    {--}}
{{--                        "targets": [0,7,8,9,10],--}}
{{--                        "visible": false,--}}
{{--                    }--}}
{{--                    @endif--}}
                ],
                "columns": [
                   // {
                   //     "title": "选择",
                   //     "width": "32px",
                   //     "data": "id",
                   //     "orderable": false,
                   //     render: function(data, type, row, meta) {
                   //         return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                   //     }
                   // },
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
                        "width": "50px",
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
                            if(data == "合计") return data;

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_abandon = '';
                            var $html_completed = '';
                            var $html_verified = '';
                            var $html_inspected = '';
                            var $html_detail_inspected = '';
                            var $html_push = '';
                            var $html_deliver = '';


                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

//                            if(row.is_me == 1 && row.item_active == 0)
                            if(row.is_published == 0)
                            {
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                                $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';
                                $html_verified = '<a class="btn btn-xs btn-default disabled">验证</a>';
                                $html_delete = '<a class="btn btn-xs bg-black item-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                if(row.inspected_status == 1 && row.inspected_result == '二次待审')
                                {
                                    $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                                    $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                                }
                                $html_detail = '<a class="btn btn-xs bg-primary item-modal-show-for-detail" data-id="'+data+'">详情</a>';
                                $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';


                                if(row.is_completed == 1)
                                {
                                    $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                    $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                }
                                else
                                {
                                    if(row.item_status == 97)
                                    {
                                        // $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                        $html_abandon = '<a class="btn btn-xs bg-teal item-reuse-submit" data-id="'+data+'">复用</a>';
                                    }
                                    else $html_abandon = '<a class="btn btn-xs bg-gray item-abandon-submit" data-id="'+data+'">弃用</a>';
                                }


                            }

//                            if(row.deleted_at == null)
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
//                            }
//                            else
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
//                            }

                            var $more_html =
                                '<div class="btn-group">'+
                                '<button type="button" class="btn btn-xs btn-success" style="padding:2px 8px; margin-right:0;">操作</button>'+
                                '<button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding:2px 6px; margin-left:-1px;">'+
                                '<span class="caret"></span>'+
                                '<span class="sr-only">Toggle Dropdown</span>'+
                                '</button>'+
                                '<ul class="dropdown-menu" role="menu">'+
                                '<li><a href="#">Action</a></li>'+
                                '<li><a href="#">删除</a></li>'+
                                '<li><a href="#">弃用</a></li>'+
                                '<li class="divider"></li>'+
                                '<li><a href="#">Separate</a></li>'+
                                '</ul>'+
                                '</div>';

                            var $html =
//                                    $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
//                                 $html_completed+
//                                 $html_edit+
//                                 $html_publish+
                                // $html_detail+
                                // $html_verified+
                                // $html_detail_inspected+
                                // $html_inspected+
                                // $html_delete+
                                // $html_push+
                                // $html_deliver+
                                $html_record+
                                // $html_abandon+
                                //                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                //                                '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                //                                $more_html+
                                '';
                            return $html;

                        }
                    },
                    {
                        "title": "项目",
                        "data": "project_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','项目');
                                $(nTd).attr('data-key','project_id').attr('data-value',data);
                                if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    $(nTd).attr('data-option-name',row.project_er.name);
                                }
                                $(nTd).attr('data-column-name','项目');
                                if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.project_er == null)
                            {
                                return '--';
                            }
                            else
                            {
                                return '<a target="_blank" href="/statistic/statistic-monthly-by-project?project_id='+data+'">'+row.project_er.name+'</a>';
                            }
                        }
                    },
                    {
                        "title": "所属渠道",
                        "data": "project_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.project_er == null)
                            {
                                return '--';
                            }
                            else
                            {
                                if(row.project_er.channel_er == null)
                                {
                                    return '--';
                                }
                                else return '<a target="_blank" href="/statistic/statistic-monthly-by-channel?channel_id='+row.project_er.channel_er.id+'">'+row.project_er.channel_er.name+'</a>';
                            }
                        }
                    },
                    {
                        "title": "商务",
                        "data": "project_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.project_er == null)
                            {
                                return '--';
                            }
                            else
                            {
                                if(row.project_er.business_or == null)
                                {
                                    return '--';
                                }
                                else return '<a href="javascript:void(0);">'+row.project_er.business_or.username+'</a>';
                            }
                        }
                    },
                    {
                        "title": "日期",
                        "data": 'assign_date',
                        "className": "text-center",
                        "width": "80px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                // var $assign_time_value = '';
                                // if(data)
                                // {
                                //     var $date = new Date(data*1000);
                                //     var $year = $date.getFullYear();
                                //     var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                //     var $day = ('00'+($date.getDate())).slice(-2);
                                //     $assign_time_value = $year+'-'+$month+'-'+$day;
                                // }

                                $(nTd).addClass('modal-show-for-info-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','日期');
                                $(nTd).attr('data-key','assign_date').attr('data-value',data);
                                $(nTd).attr('data-column-name','日期');
                                $(nTd).attr('data-time-type','date');
                                $(nTd).attr('data-time-data-type','date');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(!data) return '';

                            // var $date = new Date(data*1000);
                            // var $year = $date.getFullYear();
                            // var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            // var $day = ('00'+($date.getDate())).slice(-2);
                            // var $hour = ('00'+$date.getHours()).slice(-2);
                            // var $minute = ('00'+$date.getMinutes()).slice(-2);
                            // var $second = ('00'+$date.getSeconds()).slice(-2);
                            //
                            // var $currentYear = new Date().getFullYear();
                            // if($year == $currentYear) return $month+'-'+$day;
                            // else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "title": "外呼后台",
                        "data": "outbound_background",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','外呼后台');
                                $(nTd).attr('data-key','outbound_background').attr('data-value',data);
                                $(nTd).attr('data-column-name','外呼后台');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "出勤人力",
                        "data": "attendance_manpower",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','出勤人力');
                                $(nTd).attr('data-key','attendance_manpower').attr('data-value',data);
                                $(nTd).attr('data-column-name','出勤人力');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "交付量",
                        "data": "delivery_quantity",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交付量');
                                $(nTd).attr('data-key','delivery_quantity').attr('data-value',data);
                                $(nTd).attr('data-column-name','交付量');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data < 0) return '<b class="text-red">' + parseFloat(data) + '</b>';
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "无效交付量",
                        "data": "delivery_quantity_of_invalid",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','无效交付量');
                                $(nTd).attr('data-key','delivery_quantity_of_invalid').attr('data-value',data);
                                $(nTd).attr('data-column-name','无效交付量');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data != 0) return '<b class="text-red">' + parseFloat(data) + '</b>';
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均交付",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";
                            var $delivery_quantity = parseFloat(row.delivery_quantity);
                            var $attendance_manpower = parseFloat(row.attendance_manpower);
                            var $per_value = parseFloat(($delivery_quantity) / ($attendance_manpower)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "人力成本",
                        "data": "manpower_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);
                            // var $manpower_cost = parseFloat(($manpower_daily_wage) * ($attendance_manpower)).toFixed(2);
                            // return parseFloat($manpower_cost);
                            return parseFloat(data);

                        }
                    },
                    {
                        "title": "当日话费",
                        "data": "call_charge_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','当日话费');
                                $(nTd).attr('data-key','call_charge_daily_cost').attr('data-value',data);
                                $(nTd).attr('data-column-name','当日话费');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均话费",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";
                            var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            var $attendance_manpower = parseFloat(row.attendance_manpower);
                            var $per_value = parseFloat(($call_charge_daily_cost) / ($attendance_manpower)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "单均话费",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.delivery_quantity == 0) return "--";
                            var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            var $delivery_quantity = parseFloat(row.delivery_quantity);
                            var $per_value = parseFloat(($call_charge_daily_cost) / ($delivery_quantity)).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "物料量",
                        "data": "material_daily_quantity",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            // var $material_quantity = parseFloat(($call_charge_daily_cost) / ($call_charge_coefficient)).toFixed(2);
                            // return parseFloat($material_quantity);
                            // return parseFloat(data);
                            // return parseInt(data);
                            return Math.round(data);
                        }
                    },
                    {
                        "title": "物料成本",
                        "data": "material_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = parseFloat(($call_charge_coefficient) * ($call_charge_daily_cost) * ($material_coefficient)).toFixed(2);
                            // return parseFloat($material_cost);
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "税费成本",
                        "data": "taxes_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = parseFloat(($delivery_quantity) * ($taxes_coefficient)).toFixed(2);
                            // return parseFloat($taxes_cost);
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总成本",
                        "data": "total_daily_cost",
                        "className": "bg-fee-2",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            // return parseFloat($total_cost);

                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "人均成本",
                        "data": "total_daily_cost",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.attendance_manpower == 0) return "--";

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            //
                            // var $per_value = parseFloat($total_cost / $attendance_manpower).toFixed(2);
                            // return parseFloat($per_value);

                            var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            var $per_value = parseFloat(data / $attendance_manpower).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "单均成本",
                        "data": "total_daily_cost",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.delivery_quantity == 0) return "--";

                            // // 人力成本
                            // var $attendance_manpower = parseFloat(row.attendance_manpower);  // 出勤人力数
                            // var $manpower_daily_wage = parseFloat(row.manpower_daily_wage);
                            // var $manpower_cost = $manpower_daily_wage * $attendance_manpower;
                            //
                            // // 话费成本
                            // var $call_charge_daily_cost = parseFloat(row.call_charge_daily_cost);
                            //
                            // // 物料成本
                            // var $call_charge_coefficient = parseFloat(row.call_charge_coefficient);
                            // var $call_charge_daily_cost1 = parseFloat(row.call_charge_daily_cost);
                            // var $material_coefficient = parseFloat(row.material_coefficient);
                            // var $material_cost = $call_charge_coefficient * $call_charge_daily_cost1 * $material_coefficient;
                            //
                            // // 税费成本
                            // var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            // var $taxes_coefficient = parseFloat(row.taxes_coefficient);
                            // var $taxes_cost = $delivery_quantity * $taxes_coefficient;
                            //
                            // var $total_cost = parseFloat($manpower_cost + $call_charge_daily_cost + $material_cost + $taxes_cost).toFixed(2);
                            //
                            // var $per_value = parseFloat($total_cost / $delivery_quantity).toFixed(2);
                            // return parseFloat($per_value);

                            var $delivery_quantity = parseFloat(row.delivery_quantity);  // 交付量
                            var $per_value = parseFloat(data / $delivery_quantity).toFixed(2);
                            return parseFloat($per_value);
                        }
                    },
                    {
                        "title": "人力日薪",
                        "data": 'manpower_daily_wage',
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','人力日薪');
                                $(nTd).attr('data-key','manpower_daily_wage').attr('data-value',data);
                                $(nTd).attr('data-column-name','人力日薪');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
                           return parseFloat(data);
                        }
                    },
                    {
                        "title": "话费系数",
                        "data": "call_charge_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','话费系数');
                                $(nTd).attr('data-key','call_charge_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','话费系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "物料系数",
                        "data": "material_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','物料系数');
                                $(nTd).attr('data-key','material_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','物料系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "税费系数",
                        "data": "taxes_coefficient",
                        "className": "bg-income",
                        "width": "60px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','税费系数');
                                $(nTd).attr('data-key','taxes_coefficient').attr('data-value',data);
                                $(nTd).attr('data-column-name','税费系数');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "说明",
                        "data": "description",
                        "className": "bg-income",
                        "width": "160px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "创建人",
                        "data": "creator_id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '--' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            if(data == "--") return data;
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
                    if($('input[name="daily-id"]').val())  $obj.order_id = $('input[name="daily-id"]').val();
                    if($('input[name="daily-assign"]').val())  $obj.assign = $('input[name="daily-assign"]').val();
                    if($('input[name="daily-start"]').val())  $obj.assign_start = $('input[name="daily-start"]').val();
                    if($('input[name="daily-ended"]').val())  $obj.assign_ended = $('input[name="daily-ended"]').val();
                    if($('select[name="daily-type"]').val() > 0)  $obj.order_type = $('select[name="daily-type"]').val();
                    if($('select[name="daily-company"]').val() > 0)  $obj.company_id = $('select[name="daily-company"]').val();
                    if($('select[name="daily-channel"]').val() > 0)  $obj.channel_id = $('select[name="daily-channel"]').val();
                    if($('select[name="daily-business"]').val() > 0)  $obj.business_id = $('select[name="daily-project"]').val();
                    if($('select[name="daily-project"]').val() > 0)  $obj.project_id = $('select[name="daily-project"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != {{ $length or 20 }}) $obj.length = $page_length;
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
                        $url = "{{ url('/item/daily-list') }}";
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
        if($id) $('input[name="daily-id"]').val($id);
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
                    'url': "/item/daily-modify-record?id="+$id,
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
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "类型",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 91) return '<small class="btn-xs bg-yellow">验证</small>';
                            else if(data == 92) return '<small class="btn-xs bg-yellow">审核</small>';
                            else if(data == 95) return '<small class="btn-xs bg-green">交付</small>';
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_category == 1)
                            {
                                if(data == "assign_date") return '日期';
                                else if(data == "project_id") return '项目';
                                else if(data == "outbound_background") return '外呼后台';
                                else if(data == "attendance_manpower") return '出席人力';
                                else if(data == "delivery_quantity") return '交付量';
                                else if(data == "call_charge_daily_cost") return '当日话费';
                                else if(data == "manpower_daily_wage") return '人力日薪';
                                else if(data == "call_charge_coefficient") return '话费系数';
                                else if(data == "material_coefficient") return '物料系数';
                                else if(data == "taxes_coefficient") return '税费系数';
                                else if(data == "description") return '备注';
                                else if(data == "inspected_description") return '审核说明';
                                else return '有误';
                            }
                            else if(row.operate_category == 71)
                            {
                                return '';

                                if(row.operate_type == 1) return '添加';
                                else if(row.operate_type == 91) return '删除';

                                if(data == "attachment") return '附件';
                            }
                            else if(row.operate_category == 95)
                            {
                                if(data == "client_id") return '客户';
                                else if(data == "delivered_result") return '交付结果';
                                else return '交付';
                            }
                            else return '';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.before_client_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_client_er.username+'</a>';
                            }
                            else if(row.column_name == 'project_id')
                            {
                                if(row.before_project_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_project_er.name+'</a>';
                            }

                            if(row.column_name == 'is_wx')
                            {
                                if(data == 0) return '否';
                                else if(data == 1) return '是';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                return data;
                            }
                            if(row.column_type == 'timestamp_datetime' || row.column_type == 'dtimestamp_ate')
                            {
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'timestamp_datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'timestamp_date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'timestamp_datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'timestamp_date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.after_client_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_client_er.username+'</a>';
                            }
                            else if(row.column_name == 'project_id')
                            {
                                if(row.after_project_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_project_er.name+'</a>';
                            }

                            if(row.column_name == 'is_wx')
                            {
                                if(data == 0) return '否';
                                else if(data == 1) return '是';
                                else return '--';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                return data;
                            }
                            if(row.column_type == 'timestamp_datetime' || row.column_type == 'timestamp_date')
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                var $currentYear = new Date().getFullYear();
                                if($year == $currentYear)
                                {
                                    if(row.column_type == 'timestamp_datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'timestamp_date') return $month+'-'+$day;
                                }
                                else
                                {
                                    if(row.column_type == 'timestamp_datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'timestamp_date') return $year+'-'+$month+'-'+$day;
                                }
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
                        "title": "操作时间",
                        "data": "created_at",
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
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
@include(env('TEMPLATE_DK_FINANCE').'entrance.item.daily-list-script')
@endsection
