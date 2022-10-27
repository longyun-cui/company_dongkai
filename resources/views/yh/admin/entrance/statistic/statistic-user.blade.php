@extends(env('TEMPLATE_YH_ADMIN').'layout.layout')


@section('head_title')
    @if(in_array(env('APP_ENV'),['local'])){{ $local or '【l】' }}@endif{{ $head_title or '统计' }} - 管理员后台系统 - 兆益信息
@endsection




@section('header','')
@section('description','管理员后台系统 - 兆益信息')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url('//statistic-index') }}"><i class="fa fa-bar-chart"></i>流量统计</a></li>
    {{--<li><a href="{{ url('/user/user-list-for-all') }}"><i class="fa fa-list"></i>用户列表</a></li>--}}
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
{{--网站总流量统计--}}
<div class="row">
    <div class="col-md-12">

        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">总量统计</h3>
            </div>
            {{--总访问量--}}
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="echart-all" style="width:100%;height:320px;"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


{{--总访问比例--}}
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">转化率</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-6">
                        <div id="echart-all-rate" style="width:100%;height:320px;"></div>
                    </div>

                    <div class="col-md-6">
                        <div id="echart-today-rate" style="width:100%;height:320px;"></div>
                    </div>

                </div>
            </div>

            <div class="box-footer">
            </div>

        </div>
    </div>
</div>
@endsection




@section('custom-js')
    <script src="{{ asset('/lib/js/echarts-3.7.2.min.js') }}"></script>
@endsection
@section('custom-script')
<script>
    $(function(){

        // 电话总量
        var $all_res = new Array();
        $.each({!! $all !!},function(key,v){
            $all_res[(v.day - 1)] = { value:v.count, name:v.day };
//            $all_res.push({ value:v.sum, name:v.date });
        });
        // 通话量
        var $dialog_res = new Array();
        $.each({!! $dialog !!},function(key,v){
            $dialog_res[(v.day - 1)] = { value:v.count, name:v.day };
        });
        // 加微信量
        var $plus_wx_res = new Array();
        $.each({!! $plus_wx !!},function(key,v){
            $plus_wx_res[(v.day - 1)] = { value:v.count, name:v.day };
        });

        var option_all = {
            title: {
                text: '电话量'
            },
            tooltip : {
                trigger: 'axis',
                axisPointer: {
                    type: 'line',
                    label: {
                        backgroundColor: '#6a7985'
                    }
                }
            },
            legend: {
                data:['电话总量','通话量','加微信量']
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    axisLabel : { interval:0 },
                    data : [
                        1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31
                        {{--@foreach($all as $v)--}}
                        {{--@if (!$loop->last) '{{$v->date}}', @else '{{$v->date}}' @endif--}}
                        {{--@endforeach--}}
                    ]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'电话总量',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $all_res
                    {{--data:[--}}
                    {{--@foreach($all as $v)--}}
                    {{--@if (!$loop->last)--}}
                    {{--{ value:'{{ $v->count }}', name:'{{ $v->day }}' },--}}
                    {{--@else--}}
                    {{--{ value:'{{ $v->count }}', name:'{{ $v->day }}' }--}}
                    {{--@endif--}}
                    {{--@endforeach--}}
                    {{--]--}}
                },
                {
                    name:'通话量',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $dialog_res
                },
                {
                    name:'加微信量',
                    type:'line',
                    label: {
                        normal: {
                            show: true,
                            position: 'top'
                        }
                    },
                    itemStyle : { normal: { label : { show: true } } },
                    data: $plus_wx_res
                }
            ]
        };
        var myChart_all = echarts.init(document.getElementById('echart-all'));
        myChart_all.setOption(option_all);

        // 总转化率
        var option_all_rate = {
            title : {
                text: '转化率',
                subtext: '转化率',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient : 'vertical',
                x : 'left',
                data: [
                    @foreach($all_rate as $v)
                        @if (!$loop->last) '{{ $v->name }}', @else '{{ $v->name }}' @endif
                    @endforeach
                ]
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {
                        show: true,
                        type: ['pie', 'funnel'],
                        option: {
                            funnel: {
                                x: '25%',
                                width: '50%',
                                funnelAlign: 'left',
                                max: 1548
                            }
                        }
                    },
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            series : [
                {
                    name:'访问来源',
                    type:'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data: [
                        @foreach($all_rate as $v)
                            @if (!$loop->last)
                                { value:'{{ $v->count }}', name:'{{ $v->name }}' },
                            @else
                                { value:'{{ $v->count }}', name:'{{ $v->name }}' }
                            @endif
                        @endforeach
                    ]
                }
            ]
        };
        var myChart_all_rate = echarts.init(document.getElementById('echart-all-rate'));
        myChart_all_rate.setOption(option_all_rate);

        // 今日转化率
        var option_today_rate = {
            title : {
                text: '今日转化率',
                subtext: '今日转化率',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient : 'vertical',
                x : 'left',
                data: [
                    @foreach($today_rate as $v)
                        @if (!$loop->last) '{{ $v->name }}', @else '{{ $v->name }}' @endif
                    @endforeach
                ]
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {
                        show: true,
                        type: ['pie', 'funnel'],
                        option: {
                            funnel: {
                                x: '25%',
                                width: '50%',
                                funnelAlign: 'left',
                                max: 1548
                            }
                        }
                    },
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            series : [
                {
                    name:'访问来源',
                    type:'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data: [
                        @foreach($today_rate as $v)
                            @if (!$loop->last)
                                { value:'{{ $v->count }}', name:'{{ $v->name }}' },
                            @else
                                { value:'{{ $v->count }}', name:'{{ $v->name }}' }
                            @endif
                        @endforeach
                    ]
                }
            ]
        };
        var myChart_today_rate = echarts.init(document.getElementById('echart-today-rate'));
        myChart_today_rate.setOption(option_today_rate);

    });
</script>

@endsection


