@extends(env('TEMPLATE_YH_SUPER').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or 'L.' }}@endif
    S.{{ $title_text or '首页' }} - 超级管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Super')
@section('description','超级管理员系统 - 豫好物流')
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
    super.index
@endsection




@section('custom-script')
<script>
    $(function() {
    });
</script>
@endsection
