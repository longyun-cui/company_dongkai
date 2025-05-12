@extends(env('TEMPLATE_DK_CLIENT').'layout.layout')


@section('head_title')
    {{--@if(in_array(env('APP_ENV'),['local']))L.@endif--}}
    {{ $head_title or '客户系统' }}
@endsection




@section('header','客户系统')
@section('description')客户系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">


        <div class="nav-tabs-custom" id="index-nav-box">

            {{--nav--}}
            <ul class="nav nav-tabs">
                <li class="nav-item active" id="home">
                    <a href="#tab-home" data-toggle="tab" aria-expanded="true" id="home-default">首页</a>
                </li>
            </ul>


            {{--content--}}
            <div class="tab-content">

                <div class="tab-pane active" id="tab-pane-width" style="width:100%;">
                    &nbsp;
                </div>

                @if($me->client_er->user_category == 1)
                    @include(env('TEMPLATE_DK_CLIENT').'component.home.home-for-dental')
                @elseif($me->client_er->user_category == 31)
                    @if(in_array($me->user_type,[0,1,9,11]))
                        @include(env('TEMPLATE_DK_CLIENT').'component.home.home-for-luxury-admin')
                    @elseif(in_array($me->user_type,[81,84,88]))
                        @include(env('TEMPLATE_DK_CLIENT').'component.home.home-for-luxury-staff')
                    @endif
                @endif


            </div>

        </div>


    </div>
</div>




<div class="component-container _none">

    @include(env('TEMPLATE_DK_CLIENT').'component.department.department-list')
    @include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-list')
    @include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-list')

    @if($me->client_er->user_category == 1)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list')
    @elseif($me->client_er->user_category == 11)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-aesthetic-list')
    @elseif($me->client_er->user_category == 31)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-luxury-list')
    @endif

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-daily')

    @include(env('TEMPLATE_DK_CLIENT').'component.trade.trade-list')

    @include(env('TEMPLATE_DK_CLIENT').'component.finance.finance-daily')


    @include(env('TEMPLATE_DK_CLIENT').'component.statistic.production.staff.statistic-staff-rank')
    @include(env('TEMPLATE_DK_CLIENT').'component.statistic.production.staff.statistic-staff-daily')

</div>


    @include(env('TEMPLATE_DK_CLIENT').'component.department.department-edit')
    @include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-edit')
    @include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-edit')

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-follow-record')


@endsection




@section('custom-style')
<style>
    .main-content .callout .callout-body span { margin-right:12px; }

    .toggle-button {
        position: relative;
        width: 50px;
        height: 25px;
        background-color: #ccc;
        border: none;
        border-radius: 15px;
    }

    .toggle-handle {
        position: absolute;
        top: 0;
        width: 25px;
        height: 25px;
        background-color: #fff;
        border-radius: 50%;
    }

    .toggle-button.toggle-button-on { background-color: #66a3cc; transition: background-color 0.1s; }
    .toggle-button.toggle-button-off { background-color: #dddddd; transition: background-color 0.1s; }

    .toggle-button.toggle-button-on .toggle-handle { right: 0; background-color: #20e28b; transition: right 0.1s; }
    .toggle-button.toggle-button-off .toggle-handle { left: 0; background-color: #e00000; transition: left 0.1s; }
</style>
@endsection



@section('custom-js')
    <script src="{{ asset('/resource/component/js/echarts-5.4.1.min.js') }}"></script>
@endsection
@section('custom-script')

    @include(env('TEMPLATE_DK_CLIENT').'component.department.department-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'component.staff.staff-list-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'component.contact.contact-list-datatable')


    @if($me->client_er->user_category == 1)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list-datatable')
    @elseif($me->client_er->user_category == 11)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-aesthetic-list-datatable')
    @elseif($me->client_er->user_category == 31)
        @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-luxury-list-datatable')
    @endif


    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-follow-record-datatable')

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-daily-datatable')

    @include(env('TEMPLATE_DK_CLIENT').'component.delivery.delivery-list-script')

    @include(env('TEMPLATE_DK_CLIENT').'component.trade.trade-list-datatable')

    @include(env('TEMPLATE_DK_CLIENT').'component.finance.finance-daily-datatable')


    @include(env('TEMPLATE_DK_CLIENT').'component.statistic.production.staff.statistic-staff-rank-datatable')
    @include(env('TEMPLATE_DK_CLIENT').'component.statistic.production.staff.statistic-staff-daily-datatable')


<script>
    $(function() {

        // 【开关】
        $(".main-content").on('click', "#toggle-button-for-take-order", function() {
            var $that = $(this);
            var $toggle_box = $(this).parents('.toggle-box');

            if($(this).hasClass('toggle-button-on'))
            {
                //
                $.post(
                    "{{ url('/v1/operate/user/field-set') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate_category: "field-set",
                        column_key: "is_take_order",
                        column_value: 0
                    },
                    'json'
                )
                    .done(function($response, status, jqXHR) {
                        console.log('done');
                        $response = JSON.parse($response);
                        if(!$response.success)
                        {
                            if($response.msg) layer.msg($response.msg);
                        }
                        else
                        {
                            $that.toggleClass('toggle-button-on toggle-button-off');
                            var handle = $(this).find('.toggle-handle');
                            // handle.toggleClass('toggle-on toggle-off');

                            layer.msg('已关闭接单');
                            $toggle_box.find('.toggle-handle-text').html('【已关闭】');
                            handle.animate({'left': '0'}, 'fast');
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log('fail');
                        layer.msg('服务器错误！');

                    })
                    .always(function(jqXHR, status) {
                        console.log('always');
                        layer.closeAll('loading');
                    });
            }
            else
            {
                //
                $.post(
                    "{{ url('/v1/operate/user/field-set') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate_category: "field-set",
                        column_key: "is_take_order",
                        column_value: 1
                    },
                    'json'
                )
                    .done(function($response, status, jqXHR) {
                        console.log('done');
                        $response = JSON.parse($response);
                        if(!$response.success)
                        {
                            if($response.msg) layer.msg($response.msg);
                        }
                        else
                        {
                            $that.toggleClass('toggle-button-on toggle-button-off');
                            var handle = $(this).find('.toggle-handle');
                            // handle.toggleClass('toggle-on toggle-off');

                            layer.msg('已开启接单');
                            $toggle_box.find('.toggle-handle-text').html('【开启中】');
                            handle.animate({'left': '25px'}, 'fast');
                            $('.menu-of-clue-preferential').show();
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log('fail');
                        layer.msg('服务器错误！');

                    })
                    .always(function(jqXHR, status) {
                        console.log('always');
                        layer.closeAll('loading');
                    });
            }
        });

        // 【开关】
        $(".main-content").on('click', "#toggle-button-for-automatic-dispatching", function() {
            var $that = $(this);
            var $toggle_box = $(this).parents('.toggle-box');

            if($(this).hasClass('toggle-button-on'))
            {
                //
                $.post(
                    "{{ url('/v1/operate/parent-client/field-set') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate_category: "field-set",
                        column_key: "is_automatic_dispatching",
                        column_value: 0
                    },
                    'json'
                )
                    .done(function($response, status, jqXHR) {
                        console.log('done');
                        $response = JSON.parse($response);
                        if(!$response.success)
                        {
                            if($response.msg) layer.msg($response.msg);
                        }
                        else
                        {
                            $that.toggleClass('toggle-button-on toggle-button-off');
                            var handle = $(this).find('.toggle-handle');
                            // handle.toggleClass('toggle-on toggle-off');

                            layer.msg('已关闭自动派单');
                            $toggle_box.find('.toggle-handle-text').html('【已关闭】');
                            handle.animate({'left': '0'}, 'fast');
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log('fail');
                        layer.msg('服务器错误！');

                    })
                    .always(function(jqXHR, status) {
                        console.log('always');
                        layer.closeAll('loading');
                    });

            }
            else
            {
                //
                $.post(
                    "{{ url('/v1/operate/parent-client/field-set') }}",
                    {
                        _token: $('meta[name="_token"]').attr('content'),
                        operate_category: "field-set",
                        column_key: "is_automatic_dispatching",
                        column_value: 1
                    },
                    'json'
                )
                    .done(function($response, status, jqXHR) {
                        console.log('done');
                        $response = JSON.parse($response);
                        if(!$response.success)
                        {
                            if($response.msg) layer.msg($response.msg);
                        }
                        else
                        {
                            $that.toggleClass('toggle-button-on toggle-button-off');
                            var handle = $(this).find('.toggle-handle');
                            // handle.toggleClass('toggle-on toggle-off');

                            layer.msg('已开启自动派单');
                            $toggle_box.find('.toggle-handle-text').html('【开启中】');
                            handle.animate({'left': '0'}, 'fast');
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log('fail');
                        layer.msg('服务器错误！');

                    })
                    .always(function(jqXHR, status) {
                        console.log('always');
                        layer.closeAll('loading');
                    });
            }
        });

        // 【一键派单】
        $(".main-content").on('click', "#admin-summit-for-automatic-dispatching", function() {
            var $that = $(this);
            var $toggle_box = $(this).parents('.toggle-box');


            layer.msg('确定"一键派单"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    //
                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在提交</span>',
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
                        "{{ url('/v1/operate/delivery/automatic-dispatching-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "automatic-dispatching-by-admin"
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('done');
                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                if($response.msg) layer.msg($response.msg);
                            }
                            else
                            {
                                layer.msg('已派单');
                            }
                        })
                        .fail(function(jqXHR, status, error) {
                            console.log('fail');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('always');
                            layer.closeAll('loading');
                        });

                }
            });


        });

    });
</script>

@endsection