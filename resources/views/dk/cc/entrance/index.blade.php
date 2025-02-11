@extends(env('TEMPLATE_DK_CC').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '首页' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','Admin')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">


        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-home" data-toggle="tab" aria-expanded="true">首页</a></li>
                <li class=""><a href="#timeline" data-toggle="tab" aria-expanded="false">Timeline</a></li>
                <li class=""><a href="#settings" data-toggle="tab" aria-expanded="false">Settings</a></li>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="tab-home">

                    <div class="row">
                        @foreach($team_list as $team)
                        <div class="col-lg-3 col-xs-6">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3><sup style="font-size: 20px">{{ $team->name or '' }}</sup></h3>
                                    <p>&nbsp;</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="javascript:void(0);" class="small-box-footer item-admin-login-okcc" data-id="{{ $team->id }}">
                                    登录 <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                <div class="tab-pane" id="timeline">
                </div>

                <div class="tab-pane" id="settings">
                </div>

            </div>
            <!-- /.tab-content -->
        </div>


    </div>
</div>
@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }
    .btn-app>.badge { position: absolute; top: -6px; right: -10px; font-size: 12px; font-weight: 400; }
</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function() {


        // 【登录】
        $(".main-content").on('click', ".item-admin-login-okcc", function() {
            var $that = $(this);


            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">耐心等待中</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            $.post(
                "{{ url('/company/team-login-okcc') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "company-team-admin-login-okcc",
                    item_id: $that.attr('data-id')
                },
                'json'
            )
                .done(function($response) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        var $server = $response.data.server;
                        var $token = $response.data.token;
                        var $url = $server + '/service/index.php?m=common&c=loginTransition&f=login&token=' + $token;

                        console.log($url);
                        window.open($url, '_blank');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    console.log(jqXHR);
                    console.log(textStatus);
                    layer.closeAll('loading');
                });

        });

    });
</script>
@endsection