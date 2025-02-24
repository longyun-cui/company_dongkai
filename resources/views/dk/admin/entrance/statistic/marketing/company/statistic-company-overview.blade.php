@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '公司看板' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '公司看板' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '公司看板' }}</span>@endsection
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary main-list-body">


            <div class="col-md-12 datatable-search-row" id="datatable-search-for-statistic-company" style="margin-top:8px;">


                <div class="pull-left">

                    <input type="hidden" name="statistic-company-time-type" class="time-type" value="" readonly>



                    <button type="button" class="btn btn-success btn-filter filter-submit">
                        <i class="fa fa-search"></i> 全部查询
                    </button>


                    {{--按天查看--}}
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="statistic-company-date">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <input type="text" class="search-filter form-filter filter-keyup date_picker" name="statistic-company-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="statistic-company-date">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="date">
                        <i class="fa fa-search"></i> 按日查询
                    </button>


                    {{--按月查看--}}
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-company-month">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-company-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-company-month">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                        <i class="fa fa-search"></i> 按月查询
                    </button>

                    {{--按时间段导出--}}
                    <input type="text" class="search-filter filter-keyup date_picker" name="statistic-company-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
                    <input type="text" class="search-filter filter-keyup date_picker" name="statistic-company-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

                    <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                        <i class="fa fa-search"></i> 按时间段搜索
                    </button>




{{--                    <button type="button" class="btn btn-success btn-filter filter-submit" id="filter-submit-for-order">--}}
{{--                        <i class="fa fa-search"></i> 搜索--}}
{{--                    </button>--}}

                    <button type="button" class="btn btn-info btn-filter filter-empty" id="">
                        <i class="fa fa-remove"></i> 清空
                    </button>

                    <button type="button" class="btn btn-primary btn-filter filter-refresh" id="">
                        <i class="fa fa-circle-o-notch"></i> 刷新
                    </button>

                    <button type="button" class="btn btn-warning btn-filter filter-cancel" id="">
                        <i class="fa fa-undo"></i> 重置
                    </button>

                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="statistic-for-statistic-company">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">


                    </div>
                </div>

                <div class="tableArea">
                    <table class='table table-striped table-bordered table-hover order-column' id='datatable-for-statistic-company-overview'>
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
@endsection




@section('custom-css')
@endsection
@section('custom-style')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.marketing.company.statistic-company-style')
@endsection




@section('custom-js')
@endsection
@section('custom-script')

    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.marketing.company.statistic-company-overview-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.marketing.company.statistic-company-overview-script')

@endsection