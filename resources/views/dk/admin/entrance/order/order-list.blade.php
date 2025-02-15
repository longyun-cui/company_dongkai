@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '工单列表' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '工单列表' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '工单列表' }}</span>@endsection
@section('description')<b>工单列表 - 管理员系统 - {{ config('info.info.short_name') }}</b>@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary main-list-body" style="margin-bottom:0;">


            <div class="col-md-12 datatable-search-row" id="datatable-search-for-order-list" style="margin-top:8px;">


                <div class=" pull-left">

                    <button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>
{{--                    <button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
{{--                    <button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}
                    <button type="button" onclick="" class="btn btn-default btn-filter" id="bulk-submit-for-export">
                        <i class="fa fa-download"></i> 批量导出
                    </button>
{{--                    <button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


                    @if(in_array($me->department_district_id,[0]))
                    @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                    <div class="dropdown filter-menu">
                        <button type="button" class="btn btn-default btn-filter dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-search"></i> 批量交付
                        </button>

                        <div class="dropdown-menu box box-danger" style="top:-4px; left:92px; right:auto; ">

                            <div class="box-header with-border- _none">
                                筛选
                            </div>

                            {{--交付项目--}}
                            <div class="box-body">
                                <label class="col-md-3">交付项目</label>
                                <div class="col-md-9 filter-body">
                                    <select name="bulk-operate-delivered-project" class="form-control form-filter select2-box order-select2-project">
                                        <option value="-1">选择交付项目</option>
                                        {{--@foreach($project_list as $v)--}}
                                        {{--<option value="{{ $v->id }}">{{ $v->name }}</option>--}}
                                        {{--@endforeach--}}
                                    </select>
                                </div>
                            </div>

                            {{--交付客户--}}
                            <div class="box-body">
                                <label class="col-md-3">交付客户</label>
                                <div class="col-md-9 filter-body">
                                    <select name="bulk-operate-delivered-client" class="form-control form-filter select2-box">
                                        <option value="-1">交付客户</option>
                                        @foreach($client_list as $v)
                                            <option value="{{ $v->id }}">{{ $v->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{--交付结果--}}
                            <div class="box-body">
                                <label class="col-md-3">交付结果</label>
                                <div class="col-md-9 filter-body">
                                    <select name="bulk-operate-delivered-result" class="form-control form-filter select2-box">
                                        <option value="-1">选择交付结果</option>
                                        @foreach(config('info.delivered_result') as $v)
                                            <option value="{{ $v }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{--交付说明--}}
                            <div class="box-body">
                                <label class="col-md-3">交付说明</label>
                                <div class="col-md-9 filter-body">
                                    <input type="text" name="bulk-operate-delivered-description" class="form-control form-filter pull-right" placeholder="交付说明">
                                </div>
                            </div>




                            <div class="box-footer" style="text-align: center;">

                                <button type="button" class="btn btn-default btn-filter" id="bulk-submit-for-delivered">
                                    <i class="fa fa-share"></i> 批量交付
                                </button>
                                <button type="button" class="btn bg-default btn-filter _none">
                                    <i class="fa fa-remove"></i> 重 置
                                </button>

                            </div>
                        </div>
                    </div>
                    @endif
                    @endif

                </div>


                <div class="pull-right">


                    <div class="nav navbar-nav">

                        <div class="dropdown filter-menu" data-bs-auto-close="outside">
                            <button type="button" class="btn btn-default btn-filter dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-search"></i> 搜索
                            </button>

                            <div class="dropdown-menu box box-danger" style="top:-12px;right:68px;">

                                <div class="box-header with-border- _none">
                                    筛选
                                </div>


                                {{--ID--}}
                                <div class="box-body">
                                    <label class="col-md-3">ID</label>
                                    <div class="col-md-9 filter-body">
                                        <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" value="" />
                                    </div>
                                </div>

                                {{--电话号码--}}
                                <div class="box-body">
                                    <label class="col-md-3">电话号码</label>
                                    <div class="col-md-9 filter-body">
                                        <input type="text" class="form-control form-filter filter-keyup" name="order-client-phone" placeholder="电话号码" value="{{ $client_phone or '' }}" />
                                    </div>
                                </div>

                                {{--发布日期--}}
                                <div class="box-body">
                                    <label class="col-md-3">发布日期</label>
                                    <div class="col-md-9 filter-body">
                                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="发布日期" value="{{ $assign or '' }}" readonly="readonly" />
                                    </div>
                                </div>

                                {{--交付日期--}}
                                <div class="box-body">
                                    <label class="col-md-3">交付日期</label>
                                    <div class="col-md-9 filter-body">
                                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-delivered_date" placeholder="发布日期" value="{{ $assign or '' }}" readonly="readonly" />
                                    </div>
                                </div>

                                {{--创建方式--}}
                                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                                <div class="box-body">
                                    <label class="col-md-3">创建方式</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-created-type">
                                            <option value="-1">创建方式</option>
                                            <option value="1">人工</option>
                                            <option value="9">导入</option>
                                            <option value="99">API</option>
                                        </select>
                                    </div>
                                </div>
                                @endif

                                {{--选择团队--}}
                                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                                <div class="box-body">
                                    <label class="col-md-3">团队</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-department-district[]" id="order-department-district" multiple="multiple">
                                            <option value="-1">选择团队</option>
                                            @foreach($department_district_list as $v)
                                                <option value="{{ $v->id }}" @if($v->id == $department_district_id) selected="selected" @endif>
                                                    {{ $v->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                {{--选择员工--}}
                                @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                                <div class="box-body">
                                    <label class="col-md-3">员工</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box order-select2-staff" name="order-staff">
                                            <option value="-1">选择员工</option>
                                            @foreach($staff_list as $v)
                                                <option value="{{ $v->id }}" @if($v->id == $staff_id) selected="selected" @endif>
                                                    {{ $v->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                {{--选择客户--}}
                                @if(in_array($me->user_type,[0,1,9,11,61,66]))
                                <div class="box-body">
                                    <label class="col-md-3">客户</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box order-select2-client" name="order-client">
                                            <option value="-1">选择客户</option>
                                            @foreach($client_list as $v)
                                            <option value="{{ $v->id }}" @if($v->id == $client_id) selected="selected" @endif>
                                                {{ $v->username }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif

                                {{--选择项目--}}
                                <div class="box-body">
                                    <label class="col-md-3">项目</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box order-select2-project" name="order-project">
                                            @if($project_id > 0)
                                                <option value="-1">选择项目</option>
                                                <option value="{{ $project_id }}" selected="selected">{{ $project_name }}</option>
                                            @else
                                                <option value="-1">选择项目</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{--审核状态--}}
                                <div class="box-body">
                                    <label class="col-md-3">审核状态</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-inspected-status">
                                            <option value="-1">审核状态</option>
                                            @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
                                                <option value="待发布" @if("待发布" == $inspected_status) selected="selected" @endif>待发布</option>
                                            @endif
                                            <option value="待审核" @if("待审核" == $inspected_status) selected="selected" @endif>待审核</option>
                                            <option value="已审核" @if("已审核" == $inspected_status) selected="selected" @endif>已审核</option>
                                        </select>
                                    </div>
                                </div>

                                {{--审核结果--}}
                                <div class="box-body">
                                    <label class="col-md-3">审核结果</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-inspected-result[]" multiple="multiple">
                                            <option value="-1">审核结果</option>
                                            @foreach(config('info.inspected_result') as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{--交付状态--}}
                                <div class="box-body">
                                    <label class="col-md-3">交付状态</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-delivered-status">
                                            <option value="-1">交付状态</option>
                                            <option value="待交付" @if("待交付" == $delivered_status) selected="selected" @endif>待交付</option>
                                            {{--<option value="已交付" @if("已交付" == $delivered_status) selected="selected" @endif>已交付</option>--}}
                                            <option value="已操作" @if("已操作" == $delivered_status) selected="selected" @endif>已操作</option>
                                        </select>
                                    </div>
                                </div>

                                {{--交付结果--}}
                                <div class="box-body">
                                    <label class="col-md-3">交付结果</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box" name="order-delivered-result[]" multiple="multiple">
                                            <option value="-1">交付结果</option>
                                            @foreach(config('info.delivered_result') as $v)
                                                <option value="{{ $v }}">{{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{--城市--}}
                                <div class="box-body">
                                    <label class="col-md-3">城市</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box select2-district-city" name="order-city" id="order-city" data-target="#order-district">
                                            <option value="-1">选择城市</option>
                                            @if(!empty($district_city_list) && count($district_city_list) > 0)
                                            @foreach($district_city_list as $v)
                                                <option value="{{ $v->district_city }}" @if($district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{--行政区--}}
                                <div class="box-body">
                                    <label class="col-md-3">行政区</label>
                                    <div class="col-md-9 filter-body">
                                        <select class="form-control form-filter select2-box select2-district-district" name="order-district[]" id="order-district" data-target="order-city" multiple="multiple">
                                            <option value="-1">选择区域</option>
                                            @if(!empty($district_district_list) && count($district_district_list) > 0)
                                            @foreach($district_district_list as $v)
                                                <option value="{{ $v }}" @if($district_district == $v) selected="selected" @endif>{{ $v }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>




                                <div class="box-footer" style="text-align: center;">

                                    <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit-for-order">
                                        <i class="fa fa-search"></i> 搜 索
                                    </button>
                                    <button type="button" class="btn bg-default btn-filter filter-empty" id="filter-empty-for-order">
                                        <i class="fa fa-remove"></i> 重 置
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>

                    <button type="button" class="btn btn-default btn-filter filter-refresh" id="filter-refresh-for-order">
                        <i class="fa fa-circle-o-notch"></i> 刷新
                    </button>

                    <button type="button" class="btn btn-default btn-filter filter-cancel" id="filter-cancel-for-order">
                        <i class="fa fa-undo"></i> 重置
                    </button>

                    <div class="btn-group- pull-left">
                    </div>


                </div>


            </div>


            <div class="box-body datatable-body" id="">

                <div class="tableArea">
                <table class='table table-striped table-bordered table-hover order-column' id='datatable-for-order-list'>
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
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
    @include(env('TEMPLATE_DK_ADMIN').'entrance.order.order-list-style')
@endsection




@section('custom-js')
@endsection
@section('custom-script')


    @include(env('TEMPLATE_DK_ADMIN').'entrance.order.order-list-datatable')

    @include(env('TEMPLATE_DK_ADMIN').'entrance.order.order-list-script')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.order.order-list-script-for-info')

    @include(env('TEMPLATE_DK_ADMIN').'entrance.order.order-operation-datatable')

    @include(env('TEMPLATE_DK_ADMIN').'component.order-create-script')


@endsection
