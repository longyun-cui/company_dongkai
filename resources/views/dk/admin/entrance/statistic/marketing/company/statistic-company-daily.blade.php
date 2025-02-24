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


            <div class="col-md-12 datatable-search-row" id="datatable-search-for-statistic-company-daily">


                <div class="pull-left">

                    <input type="hidden" name="statistic-company-daily-time-type" class="time-type" value="" readonly>


                    <select class="search-filter form-filter filter-lg select2-box select2-company" name="statistic-company-daily-company">
                        <option value="-1">选择公司</option>
                        @foreach($company_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>

                    <select class="search-filter form-filter filter-lg select2-box select2-channel" name="statistic-company-daily-channel">
                        <option value="-1">选择渠道</option>
                        @foreach($channel_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>

                    <select class="search-filter form-filter filter-lg select2-box select2-business" name="statistic-company-daily-business">
                        <option value="-1">选择商务</option>
                        @foreach($business_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>


                    {{--按月查看--}}
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-company-daily-month">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                    <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-company-daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-company-daily-month">
                        <i class="fa fa-chevron-right"></i>
                    </button>


                    <button type="button" class="btn btn-success btn-filter filter-submit">
                        <i class="fa fa-search"></i> 查询
                    </button>

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


            <div class="box-body datatable-body">

                <div class="tableArea">
                    <table class='table table-striped- table-bordered table-hover order-column' id='datatable-for-statistic-company-daily'>
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

    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.marketing.company.statistic-company-daily-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.statistic.marketing.company.statistic-company-daily-script')

@endsection