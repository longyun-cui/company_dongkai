@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local']))[l]@endif A.{{ $head_title or '首页' }} - 管理员后台系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员后台系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
    admin.index
@endsection




@section('custom-script')
<script>
    $(function() {
    });
</script>
@endsection