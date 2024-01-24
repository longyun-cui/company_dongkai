@extends(env('TEMPLATE_DK_SUPER').'layout.layout')


@section('head_title')
    {{ $title_text or '首页' }} - SUPER - {{ config('info.info.short_name') }}
@endsection




@section('header','Super')
@section('description'){{ $title_text or '首页' }} - SUPER - {{ config('info.info.short_name') }}@endsection
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
