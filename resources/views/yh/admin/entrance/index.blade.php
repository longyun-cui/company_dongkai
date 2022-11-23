@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))L.@endif
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{----}}
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="callout callout-green">
                <h4>车辆统计</h4>
                <div>
                    <span style="margin-right:12px;">
                        车辆 <span class="text-purple font-24px">{{ $car_all_count or 0 }}</span> 个
                    </span>

                    <span style="margin-right:12px;">
                        牵引车 <span class="text-purple font-24px">{{ $car_car_count or 0 }}</span> 辆
                    </span>

                    <span style="margin-right:12px;">
                        车挂 <span class="text-purple font-24px">{{ $car_trailer_count or 0 }}</span> 辆
                    </span>

                    <span style="margin-right:12px;">
                        工作中 <span class="text-green font-24px">{{ $car_working_count or '' }}</span> 辆
                    </span>

                    <span style="margin-right:12px;">
                        待发车 <span class="text-blue font-24px">{{ $car_waiting_for_departure_count or 0 }}</span> 元
                    </span>

                    <span style="margin-right:12px;">
                        空闲 <span class="text-red font-24px">{{ $car_idle_count or '' }}</span> 辆
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




@section('custom-script')
<script>
    $(function() {
    });
</script>
@endsection