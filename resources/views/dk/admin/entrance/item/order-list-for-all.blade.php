@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '工单列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')工单列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info main-list-body" style="margin-bottom:0;">

                <div class="box-header with-border" style="padding:6px 10px;margin:4px;">

                    <h3 class="box-title">工单列表</h3>

                    <div class="caption pull-right">
                        <i class="icon-pin font-blue"></i>
                        <span class="caption-subject font-blue sbold uppercase"></span>
                        {{--                    <a class="item-create-link">--}}
                        {{--                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加订单</button>--}}
                        {{--                    </a>--}}
                        <a class="item-create-show">
                            <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加工单</button>
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


                <div class="box-body datatable-body item-main-body" id="datatable-for-order-list">

                    <div class="row col-md-12 datatable-search-row">
                        <div class="input-group">

                            <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" value="{{ $order_id or '' }}" style="width:88px;" />
                            <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="发布日期" value="{{ $assign or '' }}" readonly="readonly" style="width:100px;text-align:center;" />
                            <button type="button" class="form-control btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                                <i class="fa fa-chevron-right"></i>
                            </button>

                            <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-delivered_date" placeholder="交付日期" value="" readonly="readonly" style="width:100px;text-align:center;" />

                            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                                <select class="form-control form-filter" name="order-created-type" style="width:80px;">
                                    <option value="-1">创建方式</option>
                                    <option value="1">人工</option>
                                    <option value="9">导入</option>
                                    <option value="99">API</option>
                                </select>
                            @endif

                            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                                <select class="form-control form-filter select2-box" name="order-department-district[]" id="order-department-district" multiple="multiple"  style="width:100px;">
                                    <option value="-1">选择团队</option>
                                    @foreach($department_district_list as $v)
                                        <option value="{{ $v->id }}" @if($v->id == $department_district_id) selected="selected" @endif>{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                                <select class="form-control form-filter select2-box order-select2-staff" name="order-staff" style="width:120px;">
                                    <option value="-1">选择员工</option>
                                    @foreach($staff_list as $v)
                                        <option value="{{ $v->id }}" @if($v->id == $staff_id) selected="selected" @endif>{{ $v->username }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                                <select class="form-control form-filter select2-box order-select2-client" name="order-client" style="width:160px;">
                                    <option value="-1">选择客户</option>
                                    @foreach($client_list as $v)
                                        <option value="{{ $v->id }}" @if($v->id == $client_id) selected="selected" @endif>{{ $v->username }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <select class="form-control form-filter select2-box order-select2-project" name="order-project" style="width:120px;">
                                @if($project_id > 0)
                                    <option value="-1">选择项目</option>
                                    <option value="{{ $project_id }}" selected="selected">{{ $project_name }}</option>
                                @else
                                    <option value="-1">选择项目</option>
                                @endif
                            </select>

                            <select class="form-control form-filter" name="order-inspected-status" style="width:100px;">
                                <option value="-1">审核状态</option>
                                @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
                                    <option value="待发布" @if("待发布" == $inspected_status) selected="selected" @endif>待发布</option>
                                @endif
                                <option value="待审核" @if("待审核" == $inspected_status) selected="selected" @endif>待审核</option>
                                <option value="已审核" @if("已审核" == $inspected_status) selected="selected" @endif>已审核</option>
                            </select>

                            <select class="form-control form-filter select2-box" name="order-inspected-result[]" multiple="multiple" style="width:100px;">
                                <option value="-1">审核结果</option>
                                @foreach(config('info.inspected_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>

                            <select class="form-control form-filter" name="order-delivered-status" style="width:100px;">
                                <option value="-1">交付状态</option>
                                <option value="待交付" @if("待交付" == $delivered_status) selected="selected" @endif>待交付</option>
                                {{--                            <option value="已交付" @if("已交付" == $delivered_status) selected="selected" @endif>已交付</option>--}}
                                <option value="已操作" @if("已操作" == $delivered_status) selected="selected" @endif>已操作</option>
                            </select>

                            <select class="form-control form-filter select2-box" name="order-delivered-result[]" multiple="multiple" style="width:100px;">
                                <option value="-1">交付结果</option>
                                @foreach(config('info.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>

                            <input type="text" class="form-control form-filter filter-keyup" name="order-client-name" placeholder="客户姓名" value="{{ $client_name or '' }}" style="width:88px;" />
                            <input type="text" class="form-control form-filter filter-keyup" name="order-client-phone" placeholder="客户电话" value="{{ $client_phone or '' }}" style="width:88px;" />


                            <select class="form-control form-filter select2-box select2-district-city" name="order-city" id="order-city" data-target="#order-district" style="width:120px;height:100%;">
                                <option value="-1">选择城市</option>
                                @if(!empty($district_city_list) && count($district_city_list) > 0)
                                    @foreach($district_city_list as $v)
                                        <option value="{{ $v->district_city }}" @if($district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <select class="form-control form-filter select2-box select2-district-district" name="order-district[]" id="order-district" data-target="order-city" multiple="multiple" placeholder="选择区域" style="width:160px;">
                                <option value="-1">选择区域</option>
                                @if(!empty($district_district_list) && count($district_district_list) > 0)
                                    @foreach($district_district_list as $v)
                                        <option value="{{ $v }}" @if($district_district == $v) selected="selected" @endif>{{ $v }}</option>
                                    @endforeach
                                @endif
                            </select>

                            {{--                        <select class="form-control form-filter" name="order-is-wx" style="width:88px;">--}}
                            {{--                            <option value="-1">是否+V</option>--}}
                            {{--                            <option value="1" @if($is_wx == "1") selected="selected" @endif>是</option>--}}
                            {{--                            <option value="0" @if($is_wx == "0") selected="selected" @endif>否</option>--}}
                            {{--                        </select>--}}

                            {{--                        <select class="form-control form-filter" name="order-is-repeat" style="width:88px;">--}}
                            {{--                            <option value="-1">是否重复</option>--}}
                            {{--                            <option value="1" @if($is_repeat >= 1) selected="selected" @endif>是</option>--}}
                            {{--                            <option value="0" @if($is_repeat == 0) selected="selected" @endif>否</option>--}}
                            {{--                        </select>--}}

                            {{--                        <input type="text" class="form-control form-filter filter-keyup" name="order-description" placeholder="通话小结" value="" style="width:120px;" />--}}

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
                        <table class='table table-striped table-bordered table-hover order-column' id='datatable_ajax'>
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                </div>


                @if(in_array($me->department_district_id,[0]))
                    @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                        <div class="box-footer" style="padding:4px 10px;">
                            <div class="row" style="margin:2px 0;">
                                <div class="col-md-offset-0 col-md-9 col-sm-9 col-xs-12">
                                    {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                                    {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                                    <div class="input-group">
                                        <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>

                                        <span class="input-group-addon btn btn-default" id="bulk-submit-for-export"><i class="fa fa-download"></i> 批量导出</span>

                                        <select name="bulk-operate-delivered-project" class="form-control form-filter select2-box order-select2-project" style="width:20%;height:100%;">
                                            <option value="-1">选择交付项目</option>
                                            {{--                                @foreach($project_list as $v)--}}
                                            {{--                                    <option value="{{ $v->id }}">{{ $v->name }}</option>--}}
                                            {{--                                @endforeach--}}
                                        </select>

                                        <select name="bulk-operate-delivered-client" class="form-control form-filter select2-box" style="width:20%;height:100%;">
                                            <option value="-1">选择交付客户</option>
                                            @foreach($client_list as $v)
                                                <option value="{{ $v->id }}">{{ $v->username }}</option>
                                            @endforeach
                                        </select>

                                        <select name="bulk-operate-delivered-result" class="form-control form-filter select2-box" style="width:20%;height:100%;">
                                            <option value="-1">选择交付结果</option>
                                            @foreach(config('info.delivered_result') as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>

                                        <input type="text" name="bulk-operate-delivered-description" class="form-control form-filter pull-right" placeholder="交付说明" style="width:40%;">

                                        {{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-operate"><i class="fa fa-check"></i> 批量操作</span>--}}
                                        {{--                            <span class="input-group-addon btn btn-default" id="bulk-submit-for-delete"><i class="fa fa-trash-o"></i> 批量删除</span>--}}
                                        <span class="input-group-addon btn btn-default" id="bulk-submit-for-delivered"><i class="fa fa-share"></i> 批量交付</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                        <input type="hidden" name="operate" value="order-inspect" readonly>
                        <input type="hidden" name="detail-inspected-order-id" value="0" readonly>

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
                        <input type="hidden" name="attachment-set-operate" value="item-order-attachment-set" readonly>
                        <input type="hidden" name="attachment-set-order-id" value="0" readonly>
                        <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                        <input type="hidden" name="attachment-set-column-key" value="" readonly>

                        <input type="hidden" name="operate" value="item-order-attachment-set" readonly>
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
                        <input type="hidden" name="info-text-set-operate" value="item-order-info-text-set" readonly>
                        <input type="hidden" name="info-text-set-order-id" value="0" readonly>
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
                        <input type="hidden" name="info-time-set-operate" value="item-order-info-time-set" readonly>
                        <input type="hidden" name="info-time-set-order-id" value="0" readonly>
                        <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                        <input type="hidden" name="info-time-set-column-key" value="" readonly>
                        <input type="hidden" name="info-time-set-time-type" value="" readonly>


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
                        <input type="hidden" name="info-radio-set-operate" value="item-order-info-option-set" readonly>
                        <input type="hidden" name="info-radio-set-order-id" value="0" readonly>
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
                        <input type="hidden" name="info-select-set-operate" value="item-order-info-option-set" readonly>
                        <input type="hidden" name="info-select-set-order-id" value="0" readonly>
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




    {{--交付-deliver--}}
    <div class="modal fade modal-main-body" id="modal-body-for-deliver-set">
        <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

            <div class="box- box-info- form-container">

                <div class="box-header with-border margin-top-16px margin-bottom-16px">
                    <h3 class="box-title">订单交付【<span class="deliver-set-title"></span>】</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                <form action="" method="post" class="form-horizontal form-bordered " id="modal-deliver-select-set-form">
                    <div class="box-body">

                        {{ csrf_field() }}
                        <input type="hidden" name="deliver-set-operate" value="item-order-deliver-option-set" readonly>
                        <input type="hidden" name="deliver-set-operate-type" value="add" readonly>
                        <input type="hidden" name="deliver-set-order-id" value="0" readonly>
                        <input type="hidden" name="deliver-set-column-key" value="" readonly>


                        <div class="form-group _none">
                            <label class="control-label col-md-2">已交付结果</label>
                            <div class="col-md-8 " id="deliver-set-distributed-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">已交付订单</label>
                            <div class="col-md-8 " id="deliver-set-distributed-order-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">已交付客户</label>
                            <div class="col-md-8 " id="deliver-set-distributed-client-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">选择项目</label>
                            <div class="col-md-8 ">
                                <select class="form-control select2-box order-select2-project" name="deliver-set-project-id" style="width:48%;" id="">
                                    <option value="-1">选择项目</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">选择客户</label>
                            <div class="col-md-8 ">
                                <select class="form-control select2-box" name="deliver-set-client-id" style="width:48%;" id="">
                                    <option value="-1">选择客户</option>
                                    @foreach($client_list as $v)
                                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">交付结果</label>
                            <div class="col-md-8 ">
                                <select class="form-control select2-box" name="deliver-set-delivered-result" style="width:48%;" id="">
                                    <option value="-1">交付结果</option>
                                    @foreach(config('info.delivered_result') as $v)
                                        <option value="{{ $v }}">{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">录音地址</label>
                            <div class="col-md-8 ">
                                <input type="text" class="form-control" name="deliver-set-recording-address" autocomplete="off" placeholder="" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">交付说明</label>
                            <div class="col-md-8 ">
                                <textarea class="form-control" name="deliver-set-delivered-description" rows="4" cols="100%"></textarea>
                            </div>
                        </div>
                        <div class="form-group _none">
                            <label class="control-label col-md-2">是否允许分发</label>
                            <div class="col-md-8 ">
                                <div class="btn-group">

                                    <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        <input type="radio" name="deliver-set-is_distributive_condition" value="0" class="info-set-column" checked="checked"> 否
                                    </label>
                                </span>
                                    </button>

                                    <button type="button" class="btn">
                                    <span class="radio">
                                        <label>
                                            <input type="radio" name="deliver-set-is_distributive_condition" value="1" class="info-set-column"> 是
                                        </label>
                                    </span>
                                    </button>

                                </div>
                            </div>
                        </div>


                    </div>
                </form>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="button" class="btn btn-success" id="item-submit-for-deliver-set"><i class="fa fa-check"></i> 提交</button>
                            <button type="button" class="btn btn-default" id="item-cancel-for-deliver-set">取消</button>
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
                        <input type="hidden" name="distribute-set-operate" value="item-order-distribute-option-set" readonly>
                        <input type="hidden" name="distribute-set-operate-type" value="add" readonly>
                        <input type="hidden" name="distribute-set-order-id" value="0" readonly>
                        <input type="hidden" name="distribute-set-column-key" value="" readonly>


                        <div class="form-group _none">
                            <label class="control-label col-md-2">已交付结果</label>
                            <div class="col-md-8 " id="distribute-set-distributed-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">已交付订单</label>
                            <div class="col-md-8 " id="distribute-set-distributed-order-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">已分发客户</label>
                            <div class="col-md-8 " id="distribute-set-distributed-client-list">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">选择项目</label>
                            <div class="col-md-8 ">
                                <select class="form-control select2-box order-select2-project" name="distribute-set-project-id" style="width:48%;" id="">
                                    <option value="-1">选择项目</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">选择客户</label>
                            <div class="col-md-8 ">
                                <select class="form-control select2-box" name="distribute-set-client-id" style="width:48%;" id="">
                                    <option value="-1">选择客户</option>
                                    @foreach($client_list as $v)
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
        <div id="option-list-for-client">
            <option value="-1">选择客户</option>
            @foreach($client_list as $v)
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
        <div id="option-list-for-client-intention">
            <option value="-1">选择客户意向</option>
            @foreach(config('info.client_intention') as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </div>

    </div>




    {{--添加订单--}}
    <div class="modal fade modal-main-body" id="modal-body-for-order-create">
        <div class="col-md-9 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">
            <div class="box- box-info- form-container">

                <div class="box-header with-border margin-top-16px">
                    <h3 class="box-title">添加工单</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>

                @include(env('TEMPLATE_DK_ADMIN').'component.order-create')

            </div>
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

        .select2-container { height:100%; border-radius:0; float:left; }
        .select2-container .select2-selection--single { border-radius:0; }

        .select2-container--classic .select2-selection--multiple  { height:34px; border-radius:0; }

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
                    "aLengthMenu": [[ @if(!in_array($length,[10, 20, 50, 100, 200])) {{ $length.',' }} @endif 10, 20, 50, 100, 200], [ @if(!in_array($length,[10, 20, 50, 100, 200])) {{ $length.',' }} @endif "10", "20", "50", "100", "200"]],
                    "processing": true,
                    "serverSide": true,
                    "searching": false,
                    "iDisplayStart": "{{ ($page - 1) * $length }}",
                    "iDisplayLength": "{{ $length or 10 }}",
                    "ajax": {
                        'url': "{{ url('/item/order-list-for-all') }}",
                        "type": 'POST',
                        "dataType" : 'json',
                        "data": function (d) {
                            d._token = $('meta[name="_token"]').attr('content');
                            d.id = $('input[name="order-id"]').val();
                            d.remark = $('input[name="order-remark"]').val();
                            d.description = $('input[name="order-description"]').val();
                            d.delivered_date = $('input[name="order-delivered_date"]').val();
                            d.assign = $('input[name="order-assign"]').val();
                            d.assign_start = $('input[name="order-start"]').val();
                            d.assign_ended = $('input[name="order-ended"]').val();
                            d.name = $('input[name="order-name"]').val();
                            d.title = $('input[name="order-title"]').val();
                            d.keyword = $('input[name="order-keyword"]').val();
                            d.department_district = $('select[name="order-department-district[]"]').val();
                            d.staff = $('select[name="order-staff"]').val();
                            d.project = $('select[name="order-project"]').val();
                            d.client = $('select[name="order-client"]').val();
                            d.status = $('select[name="order-status"]').val();
                            d.order_type = $('select[name="order-type"]').val();
                            d.client_name = $('input[name="order-client-name"]').val();
                            d.client_phone = $('input[name="order-client-phone"]').val();
                            d.is_wx = $('select[name="order-is-wx"]').val();
                            d.is_repeat = $('select[name="order-is-repeat"]').val();
                            d.created_type = $('select[name="order-created-type"]').val();
                            d.inspected_status = $('select[name="order-inspected-status"]').val();
                            d.inspected_result = $('select[name="order-inspected-result[]"]').val();
                            d.delivered_status = $('select[name="order-delivered-status"]').val();
                            d.delivered_result = $('select[name="order-delivered-result[]"]').val();
                            d.district_city = $('select[name="order-city"]').val();
                            d.district_district = $('select[name="order-district[]"]').val();
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
                    "scrollY": ($(document).height() - 448)+"px",
                    "scrollCollapse": true,
                    "fixedColumns": {

                        @if($me->department_district_id == 0)
                        "leftColumns": "@if($is_mobile_equipment) 1 @else 5 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                        @else
                        "leftColumns": "@if($is_mobile_equipment) 1 @else 7 @endif",
                        "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                        @endif
                    },
                    "showRefresh": true,
                    "columnDefs": [
                            {{--@if(!in_array($me->user_type,[0,1,11]))--}}
                            @if($me->department_district_id != 0)
                        {
                            "targets": [0,5,9,10,11],
                            "visible": false,
                        }
                        @endif
                    ],
                    "columns": [
                        {
                            "title": '<input type="checkbox" id="check-review-all">',
                            "width": "40px",
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
                            "width": "50px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            render: function(data, type, row, meta) {
                                return data;
                            }
                        },
                        {
                            "title": "来源",
                            "data": "created_type",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            render: function(data, type, row, meta) {
                                if(!data) return '--';
                                var $result_html = '';
                                if(data == 1)
                                {
                                    $result_html = '<small class="btn-xs bg-green">人工</small>';
                                }
                                else if(data == 99)
                                {
                                    $result_html = '<small class="btn-xs bg-red">API</small>';
                                }
                                else if(data == 9)
                                {
                                    $result_html = '<small class="btn-xs bg-yellow">导入</small>';
                                }
                                else
                                {
                                    $result_html = '<small class="btn-xs bg-black">有误</small>';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "工单状态",
                            "className": "",
                            "width": "72px",
                            "data": "id",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('order_status');
                                    $(nTd).attr('data-id',row.id).attr('data-name','工单状态');
                                    $(nTd).attr('data-key','order_status').attr('data-value',row.id);
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
//                            return data;

                                if(row.deleted_at != null)
                                {
                                    return '<small class="btn-xs bg-black">已删除</small>';
                                }

                                if(row.item_status == 97)
                                {
                                    return '<small class="btn-xs bg-navy">已弃用</small>';
                                }

                                if(row.is_published == 0)
                                {
                                    return '<small class="btn-xs bg-teal">未发布</small>';
                                }
                                else
                                {
                                    if(row.is_completed == 1)
                                    {
                                        return '<small class="btn-xs bg-olive">已结束</small>';
                                    }
                                }


                                // if(row.client_id > 0)
                                // {
                                //     return '<small class="btn-xs bg-olive">已交付</small>';
                                // }

                                if(row.inspected_at)
                                {

                                    if(row.inspected_status == 1)
                                    {
                                        return '<small class="btn-xs bg-blue">已审核</small>';
                                    }
                                    else if(row.inspected_status == 9)
                                    {
                                        return '<small class="btn-xs bg-aqua">等待再审</small>';
                                    }
                                    else return '--';
                                }
                                else
                                {
                                    if(row.created_type == 9)
                                    {
                                        return '<small class="btn-xs bg-blue">导入</small>';
                                    }
                                    else
                                    {
                                        return '<small class="btn-xs bg-aqua">待审核</small>';
                                    }
                                }

                            }
                        },
                        {
                            "title": "审核结果",
                            "data": "inspected_result",
                            "className": "text-center",
                            "width": "72px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','审核结果');
                                    $(nTd).attr('data-key','inspected_result').attr('data-value',data);
                                    $(nTd).attr('data-column-name','审核结果');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!row.inspected_at) return '--';
                                var $result_html = '';
                                if(data == "通过" || data == "内部通过")
                                {
                                    $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                                }
                                else if(data == "拒绝")
                                {
                                    $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                                }
                                else if(data == "重复")
                                {
                                    $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                                }
                                else
                                {
                                    $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "符合分发",
                            "data": "is_distributive_condition",
                            "className": "",
                            "width": "72px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-radio-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','是否符合分发');
                                    $(nTd).attr('data-key','is_distributive_condition').attr('data-value',data);
                                    $(nTd).attr('data-column-name','是否符合分发');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!row.inspected_at) return '--';
                                var $result_html = '';
                                if(data == 0)
                                {
                                    $result_html = '--';
                                }
                                else if(data == 1)
                                {
                                    $result_html = '<small class="btn-xs bg-red">是</small>';
                                }
                                else
                                {
                                    $result_html = '--';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "交付状态",
                            "data": "delivered_status",
                            "className": "text-center",
                            "width": "72px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','交付状态');
                                    $(nTd).attr('data-key','delivered_status').attr('data-value',data);
                                    $(nTd).attr('data-column-name','交付状态');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!row.delivered_at) return '--';
                                var $result_html = '';
                                if(data == 0)
                                {
                                    $result_html = '<small class="btn-xs bg-teal">待交付</small>';
                                }
                                else if(data == 1)
                                {
                                    $result_html = '<small class="btn-xs bg-blue">已操作</small>';
                                }
                                else
                                {
                                    $result_html = '<small class="btn-xs bg-black">error</small>';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "交付结果",
                            "data": "delivered_result",
                            "className": "text-center",
                            "width": "72px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','交付结果');
                                    $(nTd).attr('data-key','delivered_result').attr('data-value',data);
                                    $(nTd).attr('data-column-name','审核结果');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!row.delivered_at) return '--';
                                var $result_html = '';
                                if(data == "已交付")
                                {
                                    $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                                }
                                else if(data == "待交付")
                                {
                                    $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                                }
                                else if(data == "驳回")
                                {
                                    $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                                }
                                else if(data == "等待再审" || data == "隔日交付")
                                {
                                    $result_html = '<small class="btn-xs bg-yellow">'+data+'</small>';
                                }
                                else
                                {
                                    $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "交付说明",
                            "data": "delivered_description",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','交付说明');
                                    $(nTd).attr('data-key','delivered_description').attr('data-value',data);
                                    $(nTd).attr('data-column-name','交付说明');
                                    $(nTd).attr('data-text-type','textarea');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;
                                if(data) return '<small class="btn-xs bg-yellow">双击查看</small>';
                                else return '';
                            }
                        },
                        {
                            "title": "交付人",
                            "data": "deliverer_id",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','交付人');
                                    $(nTd).attr('data-key','deliverer_name').attr('data-value',data);
                                    $(nTd).attr('data-column-name','交付人');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return row.deliverer == null ? '--' : '<a href="javascript:void(0);">'+row.deliverer.true_name+'</a>';
                            }
                        },
                        {
                            "title": "交付时间",
                            "data": 'delivered_at',
                            "className": "",
                            "width": "120px",
                            "orderable": false,
                            "orderSequence": ["desc", "asc"],
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-time-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','交付时间');
                                    $(nTd).attr('data-key','delivered_at').attr('data-value',data);
                                    $(nTd).attr('data-column-name','交付时间');
                                    $(nTd).attr('data-time-type','date');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!data) return '--';
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
                                if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            }
                        },
                        {
                            "title": "交付客户",
                            "data": "client_id",
                            "className": "",
                            "width": "120px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                // if(row.is_completed != 1 && row.item_status != 97 && row.client_id > 0)
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','客户');
                                    $(nTd).attr('data-key','client_id').attr('data-value',data);
                                    if(row.client_er == null) $(nTd).attr('data-option-name','未指定');
                                    else {
                                        $(nTd).attr('data-option-name',row.client_er.name);
                                    }
                                    $(nTd).attr('data-column-name','客户');
                                    if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(row.client_er == null)
                                {
                                    return '--';
                                }
                                else {
                                    return '<a href="javascript:void(0);">'+row.client_er.username+'</a>';
                                }
                            }
                        },
//                     {
//                         "title": "交付客户日期 ",
//                         "data": 'delivered_time',
//                         "className": "",
//                         "width": "100px",
//                         "orderable": false,
//                         "orderSequence": ["desc", "asc"],
//                         "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                             if(row.is_completed != 1 && row.item_status != 97)
//                             {
//                                 $(nTd).addClass('modal-show-for-info-time-set');
//                                 $(nTd).attr('data-id',row.id).attr('data-name','交付客户日期');
//                                 $(nTd).attr('data-key','delivered_time').attr('data-value',data);
//                                 $(nTd).attr('data-column-name','交付客户日期');
//                                 $(nTd).attr('data-time-type','date');
//                                 if(data) $(nTd).attr('data-operate-type','edit');
//                                 else $(nTd).attr('data-operate-type','add');
//                             }
//                         },
//                         render: function(data, type, row, meta) {
//                             if(!data) return '--';
// //                            return data;
//                             var $date = new Date(data*1000);
//                             var $year = $date.getFullYear();
//                             var $month = ('00'+($date.getMonth()+1)).slice(-2);
//                             var $day = ('00'+($date.getDate())).slice(-2);
//
// //                            return $year+'-'+$month+'-'+$day;
// //                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
// //                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
//
//                             var $currentYear = new Date().getFullYear();
//                             if($year == $currentYear) return $month+'-'+$day;
//                             else return $year+'-'+$month+'-'+$day;
//                         }
//                     },
                        {
                            "title": "创建人",
                            "data": "creator_id",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            render: function(data, type, row, meta) {
                                return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                            }
                        },
                        {
                            "title": "发布时间",
                            "data": 'published_at',
                            "className": "",
                            "width": "120px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            render: function(data, type, row, meta) {
//                            return data;
                                if(!data) return '';
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
                                if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            }
                        },
                        {
                            "title": "项目",
                            "data": "project_id",
                            "className": "",
                            "width": "120px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
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
                                    return '未指定';
                                }
                                else {
                                    return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                                }
                            }
                        },
                        // {
                        //     "title": "提交日期",
                        //     "data": 'assign_time',
                        //     "className": "text-center",
                        //     "width": "72px",
                        //     "orderable": true,
                        //     "orderSequence": ["desc", "asc"],
                        //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        //         if(row.is_completed != 1 && row.item_status != 97)
                        //         {
                        //             var $assign_time_value = '';
                        //             if(data)
                        //             {
                        //                 var $date = new Date(data*1000);
                        //                 var $year = $date.getFullYear();
                        //                 var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        //                 var $day = ('00'+($date.getDate())).slice(-2);
                        //                 $assign_time_value = $year+'-'+$month+'-'+$day;
                        //             }
                        //
                        //             $(nTd).addClass('modal-show-for-info-time-set-');
                        //             $(nTd).attr('data-id',row.id).attr('data-name','提交日期');
                        //             $(nTd).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                        //             $(nTd).attr('data-column-name','提交日期');
                        //             $(nTd).attr('data-time-type','date');
                        //             if(data) $(nTd).attr('data-operate-type','edit');
                        //             else $(nTd).attr('data-operate-type','add');
                        //         }
                        //     },
                        //     render: function(data, type, row, meta) {
                        //         if(!data) return '';
                        //
                        //         var $date = new Date(data*1000);
                        //         var $year = $date.getFullYear();
                        //         var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        //         var $day = ('00'+($date.getDate())).slice(-2);
                        //         var $hour = ('00'+$date.getHours()).slice(-2);
                        //         var $minute = ('00'+$date.getMinutes()).slice(-2);
                        //         var $second = ('00'+$date.getSeconds()).slice(-2);
                        //
                        //         var $currentYear = new Date().getFullYear();
                        //         if($year == $currentYear) return $month+'-'+$day;
                        //         else return $year+'-'+$month+'-'+$day;
                        //     }
                        // },
                        {
                            "title": "是否重复",
                            "data": "is_repeat",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    // $(nTd).addClass('modal-show-for-info-radio-set-');
                                    // $(nTd).attr('data-id',row.id).attr('data-name','是否重复');
                                    $(nTd).attr('data-key','is_repeat').attr('data-value',data);
                                    // $(nTd).attr('data-column-name','是否重复');
                                    // if(data) $(nTd).attr('data-operate-type','edit');
                                    // else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(data == 0) return '--';
                                else return '<small class="btn-xs btn-primary">是</small><small class="btn-xs btn-danger">'+(data+1)+'</small>';
                            }
                        },
                        {
                            "title": "客户姓名",
                            "data": "client_name",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','客户姓名');
                                    $(nTd).attr('data-key','client_name').attr('data-value',data);
                                    $(nTd).attr('data-column-name','客户姓名');
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
                            "title": "客户电话",
                            "data": "client_phone",
                            "className": "",
                            "width": "100px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','客户电话');
                                    $(nTd).attr('data-key','client_phone').attr('data-value',data);
                                    $(nTd).attr('data-column-name','客户电话');
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
                            "title": "患者类型",
                            "data": "client_type",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','患者类型');
                                    $(nTd).attr('data-key','client_type').attr('data-value',data);
                                    $(nTd).attr('data-column-name','患者类型');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // if(!data) return '--';
                                // return data;
                                var $result_html = '';
                                if(data == 0)
                                {
                                    $result_html = '<small class="btn-xs ">未选择</small>';
                                }
                                else if(data == 1)
                                {
                                    $result_html = '<small class="btn-xs bg-blue">种植牙</small>';
                                }
                                else if(data == 2)
                                {
                                    $result_html = '<small class="btn-xs bg-green">矫正</small>';
                                }
                                else if(data == 3)
                                {
                                    $result_html = '<small class="btn-xs bg-red">正畸</small>';
                                }
                                else
                                {
                                    $result_html = '未知类型';
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "客户意向",
                            "data": "client_intention",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','客户意向');
                                    $(nTd).attr('data-key','client_intention').attr('data-value',data);
                                    $(nTd).attr('data-column-name','客户意向');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // if(!data) return '--';
                                // return data;
                                var $result_html = '';
                                if(data == "到店")
                                {
                                    $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                                }
                                else if(data == "A类")
                                {
                                    $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                                }
                                else if(data == "B类")
                                {
                                    $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                                }
                                else if(data == "C类")
                                {
                                    $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                                }
                                else if(data == "A")
                                {
                                    $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                                }
                                else if(data == "B")
                                {
                                    $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                                }
                                else if(data == "C")
                                {
                                    $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                                }
                                else
                                {
                                    $result_html = data;
                                }
                                return $result_html;
                            }
                        },
                        {
                            "title": "牙齿数量",
                            "data": "teeth_count",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','牙齿数量');
                                    $(nTd).attr('data-key','teeth_count').attr('data-value',data);
                                    $(nTd).attr('data-column-name','牙齿数量');
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
                            "title": "是否+V",
                            "data": "is_wx",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-radio-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','是否+V');
                                    $(nTd).attr('data-key','is_wx').attr('data-value',data);
                                    $(nTd).attr('data-column-name','是否+V');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(data == 1) return '<small class="btn-xs btn-primary">是</small>';
                                else return '--';
                            }
                        },
                        {
                            "title": "微信号",
                            "data": "wx_id",
                            "className": "",
                            "width": "100px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','微信号');
                                    $(nTd).attr('data-key','wx_id').attr('data-value',data);
                                    $(nTd).attr('data-column-name','微信号');
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
                            "title": "所在城市",
                            "data": "location_city",
                            "className": "",
                            "width": "120px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                                {
                                    $(nTd).addClass('modal-show-for-info-select2-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','所在城市');
                                    $(nTd).attr('data-key','location_city').attr('data-value',data);
                                    $(nTd).attr('data-key2','location_district').attr('data-value2',row.location_district);
                                    $(nTd).attr('data-column-name','所在城市');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!data) return '--';
                                else {
                                    if(!row.location_district) return data;
                                    else return data+' - '+row.location_district;
                                }
                            }
                        },
                            {{--{--}}
                            {{--    "title": "渠道来源",--}}
                            {{--    "data": "channel_source",--}}
                            {{--    "className": "",--}}
                            {{--    "width": "60px",--}}
                            {{--    "orderable": false,--}}
                            {{--    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {--}}
                            {{--        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))--}}
                            {{--        {--}}
                            {{--            $(nTd).addClass('modal-show-for-info-select-set');--}}
                            {{--            $(nTd).attr('data-id',row.id).attr('data-name','渠道来源');--}}
                            {{--            $(nTd).attr('data-key','channel_source').attr('data-value',data);--}}
                            {{--            $(nTd).attr('data-column-name','渠道来源');--}}
                            {{--            if(data) $(nTd).attr('data-operate-type','edit');--}}
                            {{--            else $(nTd).attr('data-operate-type','add');--}}
                            {{--        }--}}
                            {{--    },--}}
                            {{--    render: function(data, type, row, meta) {--}}
                            {{--        if(!data) return '--';--}}
                            {{--        return data;--}}
                            {{--    }--}}
                            {{--},--}}
                        {
                            "title": "通话小结",
                            "data": "description",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','通话小结');
                                    $(nTd).attr('data-key','description').attr('data-value',data);
                                    $(nTd).attr('data-column-name','通话小结');
                                    $(nTd).attr('data-text-type','textarea');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;
                                if(data) return '<small class="btn-xs bg-yellow">双击查看</small>';
                                else return '';
                            }
                        },
                            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                        {
                            "title": "录音地址",
                            "data": "recording_address",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','录音地址');
                                    $(nTd).attr('data-key','recording_address').attr('data-value',data);
                                    $(nTd).attr('data-column-name','录音地址');
                                    $(nTd).attr('data-text-type','textarea');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;
                                if(data)
                                {
                                    return '<small class="btn-xs bg-yellow">双击查看</small>';
                                }
                                else return '';
                            }
                        },
                        {
                            "title": "录音播放",
                            "data": "recording_address_list",
                            "className": "",
                            "width": "400px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                                    $(nTd).attr('data-key','recording_address_play').attr('data-value',data);
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;
                                if($.trim(data))
                                {
                                    try
                                    {
                                        var $recording_list = JSON.parse(data);

                                        var $return_html = '';
                                        $.each($recording_list, function(index, value)
                                        {

                                            var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio>'
                                            $return_html += $audio_html;
                                        });
                                        return $return_html;
                                    }
                                    catch(e)
                                    {
                                        // console.log(e);
                                        return '';
                                    }
                                }
                                else
                                {
                                    if(row.recording_address)
                                    {
                                        return '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+row.recording_address+'" type="audio/mpeg"></audio>';
                                    }
                                    else return '';
                                }
                            }
                        },
                        {
                            "title": "录音下载",
                            "data": "recording_address_list",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).attr('data-id',row.id).attr('data-name','录音下载');
                                    $(nTd).attr('data-key','recording_address_download').attr('data-value',data);
                                    $(nTd).attr('data-address-list',data);
                                    $(nTd).attr('data-address',row.recording_address);
                                    $(nTd).attr('data-call-record-id',row.call_record_id);
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;

                                if($.trim(data))
                                {
                                    try
                                    {
                                        var $recording_list = JSON.parse(data);
                                        return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                                    }
                                    catch(e)
                                    {
                                        // console.log(e);
                                        return '';
                                    }
                                }
                                else
                                {
                                    if(row.recording_address)
                                    {
                                        return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                                    }
                                    else return '';
                                }

                                if(data || row.recording_address)
                                {
                                    return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                                }
                                else return '';
                            }
                        },
                            @endif
                        {
                            "title": "部门",
                            "data": "department_district_id",
                            "className": "",
                            "width": "120px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','团队大区');
                                    $(nTd).attr('data-key','team_district').attr('data-value',data);
                                    $(nTd).attr('data-column-name','团队大区');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                if(!data) return '--';

                                var $district = row.department_district_er == null ? '' : row.department_district_er.name;
                                var $group = row.department_group_er == null ? '' : ' - ' + row.department_group_er.name;
                                return '<a href="javascript:void(0);">'+$district + $group+'</a>';
                            }
                        },
                        {
                            "title": "部门经理",
                            "data": "department_manager_id",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','团队大区');
                                    $(nTd).attr('data-key','department_manager_id').attr('data-value',data);
                                    $(nTd).attr('data-column-name','团队大区');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return row.department_manager_er == null ? '--' : '<a href="javascript:void(0);">'+row.department_manager_er.true_name+'</a>';
                            }
                        },
                        {
                            "title": "部门主管",
                            "data": "department_supervisor_id",
                            "className": "",
                            "width": "100px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-select-set-');
                                    $(nTd).attr('data-id',row.id).attr('data-name','部门主管');
                                    $(nTd).attr('data-key','department_supervisor_id').attr('data-value',data);
                                    $(nTd).attr('data-column-name','部门经理');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                return row.department_supervisor_er == null ? '--' : '<a href="javascript:void(0);">'+row.department_supervisor_er.true_name+'</a>';
                            }
                        },
                        {
                            "title": "审核人",
                            "data": "inspector_id",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            render: function(data, type, row, meta) {
                                return row.inspector == null ? '--' : '<a href="javascript:void(0);">'+row.inspector.true_name+'</a>';
                            }
                        },
                        {
                            "title": "审核时间",
                            "data": 'inspected_at',
                            "className": "",
                            "width": "120px",
                            "orderable": true,
                            "orderSequence": ["desc", "asc"],
                            render: function(data, type, row, meta) {
                                if(!data) return '--';
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
                                if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            }
                        },
                        {
                            "title": "审核说明",
                            "data": "inspected_description",
                            "className": "",
                            "width": "80px",
                            "orderable": false,
                            "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                                if(row.is_completed != 1 && row.item_status != 97)
                                {
                                    $(nTd).addClass('modal-show-for-info-text-set');
                                    $(nTd).attr('data-id',row.id).attr('data-name','审核说明');
                                    $(nTd).attr('data-key','inspected_description').attr('data-value',data);
                                    $(nTd).attr('data-column-name','审核说明');
                                    $(nTd).attr('data-text-type','textarea');
                                    if(data) $(nTd).attr('data-operate-type','edit');
                                    else $(nTd).attr('data-operate-type','add');
                                }
                            },
                            render: function(data, type, row, meta) {
                                // return data;
                                if(data) return '<small class="btn-xs bg-yellow">双击查看</small>';
                                else return '';
                            }
                        },
                        {
                            "title": "是否推送",
                            "data": "api_is_pushed",
                            "className": "",
                            "width": "60px",
                            "orderable": false,
                            render: function(data, type, row, meta) {
                                if(data == 1) return '<small class="btn-xs btn-primary">是</small>';
                                else return '--';
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
                                if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                            }
                        },
                        {
                            "title": "操作",
                            "data": 'id',
                            "className": "",
                            "width": "180px",
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
                                var $html_inspected = '';
                                var $html_detail_inspected = '';
                                var $html_push = '';
                                var $html_deliver = '';
                                var $html_distribute = '';


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
//                                $html_travel = '<a class="btn btn-xs bg-olive item-modal-show-for-travel" data-id="'+data+'">行程</a>';
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

                                    // 验证
                                    if(row.verifier_id == 0)
                                    {
                                        $html_verified = '<a class="btn btn-xs bg-teal item-verify-submit" data-id="'+data+'">验证</a>';
                                    }
                                    else
                                    {
                                        $html_verified = '<a class="btn btn-xs bg-aqua-gradient disabled">已验</a>';
                                    }

                                    // 审核
                                    if("{{ in_array($me->user_type,[0,1,11,61,66,71,77]) }}")
                                    {
                                        if(row.inspector_id == 0)
                                        {
                                            $html_inspected = '<a class="btn btn-xs bg-teal item-inspect-submit" data-id="'+data+'">审核</a>';
                                            $html_detail_inspected = '<a class="btn btn-xs bg-teal item-modal-show-for-detail-inspected" data-id="'+data+'">审核</a>';
                                        }
                                        else
                                        {
                                            // $html_inspected = '<a class="btn btn-xs bg-aqua-gradient disabled">已审</a>';
                                            $html_inspected = '<a class="btn btn-xs bg-blue item-inspect-submit" data-id="'+data+'">再审</a>';
                                            $html_detail_inspected = '<a class="btn btn-xs bg-blue item-modal-show-for-detail-inspected" data-id="'+data+'">再审</a>';
                                        }

                                        @if($me->department_district_id == 0)
                                        if(row.delivered_status == 0)
                                        {
                                            // $html_push = '<a class="btn btn-xs bg-teal item-modal-show-for-deliver" data-id="'+data+'" data-key="client_id">交付</a>';
                                            // $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-submit" data-id="'+data+'">交付</a>';
                                            $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-show" data-id="'+data+'">交付</a>';
                                        }
                                        else
                                        {
                                            // $html_deliver = '<a class="btn btn-xs bg-green disabled- item-deliver-submit" data-id="'+data+'">再交4</a>';
                                            $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-show" data-id="'+data+'">重交</a>';
                                        }

                                        if(row.project_er == null)
                                        {
                                            $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                                        }
                                        else
                                        {
                                            if(row.project_er.is_distributive == 1)
                                            {

                                                // $html_distribute = '<a class="btn btn-xs bg-green item-distribute-submit" data-id="'+data+'">分发</a>';
                                                $html_distribute = '<a class="btn btn-xs bg-green item-distribute-show" data-id="'+data+'">分发</a>';
                                            }
                                            else
                                            {
                                                $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                                            }
                                        }
                                        @endif
                                            $html_edit = '';
                                        $html_publish = '';
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
                                    $html_edit+
                                    $html_publish+
                                    // $html_detail+
                                    // $html_verified+
                                    $html_detail_inspected+
                                    // $html_inspected+
                                    $html_delete+
                                    $html_push+
                                    $html_deliver+
                                    $html_distribute+
                                    $html_record+
                                    // $html_abandon+
                                    //                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                    //                                '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                    //                                $more_html+
                                    '';
                                return $html;

                            }
                        }
                    ],
                    "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                        var $obj = new Object();
                        if($('input[name="order-id"]').val())  $obj.order_id = $('input[name="order-id"]').val();
                        if($('input[name="order-assign"]').val())  $obj.assign = $('input[name="order-assign"]').val();
                        if($('input[name="order-start"]').val())  $obj.assign_start = $('input[name="order-start"]').val();
                        if($('input[name="order-ended"]').val())  $obj.assign_ended = $('input[name="order-ended"]').val();
                        if($('select[name="order-department-district"]').val() > 0)  $obj.department_district_id = $('select[name="order-department-district"]').val();
                        if($('select[name="order-staff"]').val() > 0)  $obj.staff_id = $('select[name="order-staff"]').val();
                        if($('select[name="order-client"]').val() > 0)  $obj.client_id = $('select[name="order-client"]').val();
                        if($('select[name="order-project"]').val() > 0)  $obj.project_id = $('select[name="order-project"]').val();
                        if($('input[name="order-client-name"]').val())  $obj.client_name = $('input[name="order-client-name"]').val();
                        if($('input[name="order-client-phone"]').val())  $obj.client_phone = $('input[name="order-client-phone"]').val();
                        if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
                        if($('select[name="order-is-wx"]').val() > 0)  $obj.is_delay = $('select[name="order-is-wx"]').val();
                        if($('select[name="order-is-repeat"]').val() > 0)  $obj.is_delay = $('select[name="order-is-repeat"]').val();
                        if($('select[name="order-inspected-status"]').val() != -1)  $obj.inspected_status = $('select[name="order-inspected-status"]').val();
                        if($('select[name="order-delivered-status"]').val() != -1)  $obj.delivered_status = $('select[name="order-delivered-status"]').val();
                        // if($('select[name="order-city"]').val() != -1)  $obj.district_city = $('select[name="order-city"]').val();
                        // if($('select[name="order-district"]').val() != -1)  $obj.district_district = $('select[name="order-district"]').val();

                        var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                        if($page_length != 10) $obj.length = $page_length;
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
                            $url = "{{ url('/item/order-list-for-all') }}";
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
            if($id) $('input[name="order-id"]').val($id);
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
                        'url': "/item/order-modify-record?id="+$id,
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
                                else if(data == 96) return '<small class="btn-xs bg-red">分发</small>';
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
                                    if(data == "client_id") return '客户';
                                    else if(data == "project_id") return '项目';
                                    else if(data == "client_name") return '客户电话';
                                    else if(data == "client_phone") return '客户电话';
                                    else if(data == "client_intention") return '客户意向';
                                    else if(data == "is_wx") return '是否+V';
                                    else if(data == "wx_id") return '微信号';
                                    else if(data == "teeth_count") return '牙齿数量';
                                    else if(data == "location_city") return '城市区域';
                                    else if(data == "channel_source") return '渠道来源';
                                    else if(data == "description") return '通话小结';
                                    else if(data == "inspected_description") return '审核说明';
                                    else if(data == "delivered_description") return '交付说明';
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
                                else if(row.operate_category == 96)
                                {
                                    if(data == "client_id") return '客户';
                                    else if(data == "delivered_result") return '分发结果';
                                    else return '分发';
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
                                            if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                            else if(row.column_type == 'date') return $month+'-'+$day;
                                        }
                                        else
                                        {
                                            if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                            else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
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
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
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
    @include(env('TEMPLATE_DK_ADMIN').'entrance.item.order-list-script')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.item.order-list-script-for-info')

    @include(env('TEMPLATE_DK_ADMIN').'component.order-create-script')
@endsection