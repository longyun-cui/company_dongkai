@extends(env('TEMPLATE_SUPER_FRONT').'layout.layout')


@section('head_title','超级后台')


@section('header','超级后台')
@section('description','超级后台')
@section('breadcrumb')
    <li><a href="{{url('/admin')}}"><i class="fa fa-dashboard"></i>首页</a></li>
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