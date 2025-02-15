@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '数据导出' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '数据导出' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '数据导出' }}</span>@endsection
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--导出--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">数据导出</h3>
            </div>

            <div class="box-body datatable-body item-main-body" id="export-for-order">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">


{{--                        <select class="form-control form-filter" name="order-staff" style="width:88px;">--}}
{{--                            <option value ="-1">选择员工</option>--}}
{{--                            @foreach($staff_list as $v)--}}
{{--                                <option value ="{{ $v->id }}">{{ $v->true_name }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="order-client" style="width:88px;">--}}
{{--                            <option value ="-1">选择客户</option>--}}
{{--                            @foreach($client_list as $v)--}}
{{--                                <option value ="{{ $v->id }}">{{ $v->username }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <select class="form-control form-filter select2-container select2-client" name="order-client" style="width:120px;">
                            <option value="-1">选择客户</option>
                        </select>

                        <select class="form-control form-filter select2-container select2-project" name="order-project" style="width:120px;">
                            <option value="-1">选择项目</option>
                        </select>

                        <select class="form-control form-filter" name="order-inspected-result" style="width:100px;">
                            <option value ="-1">审核结果</option>
                            <option value ="通过">通过</option>
                            <option value ="拒绝">拒绝</option>
                            <option value ="重复">重复</option>
                            <option value ="内部通过">内部通过</option>
                        </select>

{{--                        <select class="form-control form-filter" name="order-channel" style="width:88px;">--}}
{{--                            <option value ="-1">去到来源</option>--}}
{{--                            @foreach(config('info.channel_source') as $v)--}}
{{--                                <option value ="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

{{--                        <select class="form-control form-filter" name="order-city" style="width:88px;">--}}
{{--                            <option value ="-1">所在城市</option>--}}
{{--                            @foreach(config('info.location_city') as $v)--}}
{{--                                <option value ="{{ $v }}">{{ $v }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}

                        <button type="button" class="form-control btn btn-flat btn-default filter-empty" id="filter-empty-for-order">
                            <i class="fa fa-remove"></i> 清空重选
                        </button>


                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="latest">
                            <i class="fa fa-download"></i> 最新导出
                        </button>


                        {{--按时间段导出--}}
                        <input type="text" class="form-control form-filter filter-keyup time_picker" name="export-start" placeholder="起始时间" readonly="readonly" value="" data-default="" style="width:120px;text-align:center;" />
                        <input type="text" class="form-control form-filter filter-keyup time_picker" name="export-ended" placeholder="终止时间" readonly="readonly" value="" data-default="" style="width:120px;text-align:center;" />

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="" style="width:100px;">
                            <i class="fa fa-download"></i> 按时间段导出
                        </button>


                        {{--按天导出--}}
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-pre-for-export">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup date_picker" name="export-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default time-picker-btn date-pick-next-for-export">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="day">
                            <i class="fa fa-download"></i> 按日导出
                        </button>


                        {{--按月导出--}}
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-pre-for-export">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <input type="text" class="form-control form-filter filter-keyup month-picker month_picker" name="export-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                        <button type="button" class="form-control btn btn-flat btn-default month-picker-btn month-pick-next-for-export">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-success filter-submit filter-submit-for-order" data-type="month">
                            <i class="fa fa-download"></i> 按月导出
                        </button>


                        <div class="month-picker-box clear-both">
                        </div>

                        <div class="month-picker-box clear-both-">
                        </div>


                        <div class="month-picker-box clear-both">
                        </div>


                    </div>
                </div>

            </div>

            <div class="box-body">
                <div class="row">
                </div>
            </div>

        </div>
    </div>
</div>


{{--导出记录--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">

                <h3 class="box-title">{{ $title_text or '导出记录' }}</h3>

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-record-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="record-id" placeholder="ID" />
                        <input type="text" class="form-control form-filter filter-keyup" name="record-name" placeholder="标题" />
                        <select class="form-control form-filter" name="record-operate-type" style="width:100px;">
                            <option value="-1">导出方式</option>
                            <option value="1">自定义时间导出</option>
                            <option value="11">按月导出</option>
                            <option value="31">按日导出</option>
                            <option value="99">最新导出</option>
                            <option value="100">ID导出</option>
                        </select>

                        <select class="form-control form-filter select2-box" name="record-staff" style="width:120px;">
                            <option value="-1">选择员工</option>
                            @foreach($staff_list as $v)
                                <option value="{{ $v->id }}">{{ $v->username }}</option>
                            @endforeach
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-record">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-record">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea overflow-none-">
                    <table class='table table-striped table-bordered- table-hover' id='datatable_ajax'>
                        <thead>
                        <tr role='row' class='heading'>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>


            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12 _none">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value ="-1">请选择操作类型</option>
                                <option value ="启用">启用</option>
                                <option value ="禁用">禁用</option>
                                <option value ="删除">删除</option>
                                <option value ="彻底删除">彻底删除</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
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
    .myChart { width:100%;height:240px; }
    .datatable-search-row .input-group .time-picker-btn,
    .datatable-search-row .input-group .month-picker-btn { width:30px; }
    .datatable-search-row .input-group .month_picker,
    .datatable-search-row .input-group .date_picker { width:100px; text-align:center; }
    .datatable-search-row .input-group button { width:80px; }
    .datatable-search-row .input-group select { width:100px; }
    .datatable-search-row .input-group .select2-container { width:120px; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')

    @include(env('TEMPLATE_DK_ADMIN').'entrance.export.statistic-export-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.export.statistic-export-script')

@endsection


