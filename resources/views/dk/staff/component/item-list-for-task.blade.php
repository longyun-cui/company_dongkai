@foreach($item_list as $num => $item)
<div class="a-piece item-piece item-option radius-4px {{ $getType or 'items' }}"
     data-item="{{ $item->id or 0 }}"
     data-id="{{ $item->id or 0 }}"
     data-item-id="{{ $item->id or 0 }}"
     data-getType="{{ $getType or 'items' }}"
>

    <div class="item-container bg-white">


        {{--头部--}}
        <figure class="text-container clearfix _none">
            <div class="text-box">
                <div class="text-content-row multi-ellipsis-1">
                    {{--<a href="{{ url('/user/'.$item->creator->id) }}" style="color:#ff7676;font-size:13px;">--}}
                        {{--<span class="item-user-portrait">--}}
                            {{--<img src="/common/images/bg/background-image.png" data-src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">--}}
                            {{--<img src="{{ url(env('DOMAIN_CDN').'/'.$item->creator->portrait_img) }}" alt="">--}}
                        {{--</span>--}}
                        {{--{{ $item->creator->username or '' }}--}}
                    {{--</a>--}}
                    {{--<span class="text-muted disabled pull-right"><small>{{ date_show($item->updated_at->timestamp) }}</small></span>--}}
                    <span class="text-muted disabled pull-right">

                        <small>
                            @if(!empty($item->published_at)) {{ time_show($item->published_at) }}
                            @else {{ time_show($item->updated_at->timestamp) }}
                            @endif
                        </small>

                    </span>
                </div>
            </div>
        </figure>


        {{--有封面图片--}}
        {{--@if(!empty($item->cover_pic))--}}
        {{--<figure class="image-container padding-top-2-5">--}}
            {{--<div class="image-box">--}}
                {{--<a class="clearfix zoom-" target="_self" href="{{ url('/item/'.$item->id) }}">--}}
                    {{--<img class="grow" src="/common/images/bg/background-image.png" data-src="{{ env('DOMAIN_CDN').'/'.$item->cover_pic }}" alt="Cover">--}}
                    {{--<img class="grow" src="{{ env('DOMAIN_CDN').'/'.$item->cover_pic }}" alt="Cover">--}}
                    {{--@if(!empty($item->cover_pic))--}}
                    {{--<img class="grow" src="{{ url(env('DOMAIN_CDN').'/'.$item->cover_pic) }}">--}}
                    {{--@else--}}
                    {{--<img class="grow" src="{{ url('/common/images/notexist.png') }}">--}}
                    {{--@endif--}}
                {{--</a>--}}
                {{--<span class="btn btn-warning">热销中</span>--}}
                {{--<span class="paste-tag-inn">--}}
                    {{--@if($item->time_type == 1)--}}
                        {{--@if(!empty($item->start_time))--}}
                            {{--<span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b></span>--}}
                        {{--@endif--}}
                        {{--@if(!empty($item->end_time))--}}
                            {{--<span style="font-size:12px;">至</span>--}}
                            {{--<span class="label label-danger end-time-inn"><b>{{ time_show($item->end_time) }}(结束)</b></span>--}}
                        {{--@endif--}}
                    {{--@endif--}}
                {{--</span>--}}
            {{--</div>--}}
        {{--</figure>--}}
        {{--@endif--}}


        {{--内容主体--}}
        <figure class="text-container clearfix">
            <div class="text-box with-border-top-">


                {{--已完成--}}
                @if($item->is_completed == 1)
                <div class="text-row text-content-row multi-ellipsis-1- margin-top-4px margin-bottom-4px">
                    <lable class="tag bg-olive">
                        <i class="icon ion-android-checkbox"></i> 已完成 by【{{ $item->completer->true_name or '' }}】{{ time_show($item->completed_at) }}
                    </lable>
                    @if($item->item_result == 0) <lable class="tag bg-info">未标记</lable>
                    @elseif($item->item_result == 1) <lable class="tag bg-olive">通话</lable>
                    @elseif($item->item_result == 19) <lable class="tag bg-olive">通话</lable> <lable class="tag bg-red">加微信</lable>
                    @elseif($item->item_result == 71) <lable class="tag bg-yellow">未接</lable>
                    @elseif($item->item_result == 72) <lable class="tag bg-yellow">拒接</lable>
                    @elseif($item->item_result == 51) <lable class="tag bg-yellow">打错了</lable>
                    @elseif($item->item_result == 99) <lable class="tag bg-yellow">空号</lable>
                    @endif
                </div>
                @endif

                {{--主要内容-公司名称-注册资金--}}
                <div class="text-row text-content-row multi-ellipsis-1- margin-top-4px margin-bottom-4px">

                    {{--是否删除--}}
                    @if($item->deleted_at != null)
                        <lable class="tag bg-black">已删除</lable>
                    @else
                    @endif

                    {{--是否发布--}}
                    @if($item->item_active == 0)
                        <lable class="tag bg-yellow-gradient">
                            <i class="icon ion-paper-airplane"></i> 待发布
                        </lable>
                    @elseif($item->item_active == 1)
                        {{--是否完成--}}
                        @if($item->is_completed == 0)
                            <lable class="tag bg-red-gradient">
                                <i class="icon ion-android-checkbox-outline"></i> 待完成
                            </lable>
                        @elseif($item->is_completed == 1)
                            {{--<lable class="tag bg-olive">--}}
                                {{--<i class="icon ion-android-checkbox"></i> 已完成--}}
                            {{--</lable>--}}
                        @endif
                    @endif

                    @if($item->item_type == 1) <lable class="tag bg-purple-gradient">售前</lable>
                    @elseif($item->item_type == 2) <lable class="tag bg-primary">回收</lable>
                    @elseif($item->item_type == 3) <lable class="tag bg-light-blue">调琴</lable>
                    @elseif($item->item_type == 4) <lable class="tag bg-light-blue">搬运</lable>
                    @elseif($item->item_type == 5) <lable class="tag bg-light-blue">异常</lable>
                    @elseif($item->item_type == 9)  <lable class="tag bg-teal">售后</lable>
                    @endif

                    {{--<a href="{{ url('/item/'.$item->id) }}"><b>{{ $item->title or '' }}</b></a>--}}
                    <b>{{ $item->company or '' }}</b>
                    @if(!empty($item->fund))
                        (<span>{{ $item->fund or '0' }}</span>)
                    @endif

                </div>
                <div class="text-row text-content-row multi-ellipsis-1- margin-top-4px margin-bottom-4px">
                    <b>{{ $item->name or '' }}</b>
                </div>

                {{--时间--}}
                @if(empty($item->cover_pic))
                @if($item->time_type == 1)
                <div class="text-row text-time-row multi-ellipsis-1">
                    @if(!empty($item->start_time))
                        <span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b></span>
                    @endif
                    @if(!empty($item->end_time))
                        <span class="font-12px"> 至 </span>
                        <span class="label label-danger end-time-inn"><b>{{ time_show($item->end_time) }} (结束)</b></span>
                    @endif
                </div>
                @endif
                @endif

                {{--电话号码--}}
                @if(!empty($item->mobile))
                    <div class="text-row text-info-row- multi-ellipsis-1 margin-bottom-4px">
                        <i class="fa fa-mobile-phone text-blue" style="width:16px;line-height:20px;text-align:center;float:left;"></i>
                        <span class="">
                            <a href="tel:{{ $item->mobile or '' }}">{{ $item->mobile or '' }}</a>
                        </span>
                    </div>
                @endif

                {{--地址--}}
                @if(!empty($item->address))
                    <div class="text-row text-info-row multi-ellipsis-1 margin-bottom-4px">
                        <i class="fa fa-location-arrow text-blue" style="width:16px;line-height:20px;text-align:center;float:left;"></i>
                        <span class="">{{ $item->address or '' }}</span>
                    </div>
                @endif

                {{--描述--}}
                @if(!empty($item->description))
                    <div class="text-row text-description-row margin-bottom-4px">
                        <span class="">{{ $item->description or '' }}</span>
                    </div>
                @endif

                {{--备注--}}
                @if(!empty($item->remark))
                    <div class="text-row text-description-row margin-bottom-4px">
                        【备注】<span class="">{{ $item->remark or '' }}</span>
                    </div>
                @endif

                <div class="text-title-row multi-ellipsis-1 _none">
                    <span class="info-tags text-danger">该组织•贴片广告</span>
                </div>

            </div>

            {{--工具栏--}}
            @if(in_array($me_staff->user_type,[0,1,9,11,19,21,22,41,61,88]))
            <div class="text-box with-border-top- clearfix">

                <div class="text-row text-tool-row">

                    {{--已删除--}}
                    @if($item->deleted_at != null)

                        {{--<lable class="tag bg-black">已删除</lable>--}}


                        {{--是否完成--}}
                        @if($item->is_completed == 1)
                            <lable class="tag bg-olive">
                                <i class="icon ion-android-checkbox"></i> 已完成 by 【{{ $item->completer->username or '' }}】{{ time_show($item->completed_at) }}
                            </lable>
                        @else
                            {{--<lable class="tag bg-purple-gradient">--}}
                                {{--<i class="icon ion-android-checkbox-outline"></i> 待完成--}}
                            {{--</lable>--}}
                        @endif

                        {{--删除权限--}}
                        {{--@if(in_array($me_staff->user_type,[0,1,9,11]))--}}
                            {{--<a class="tool-button operate-btn delete-btn task-restore-this" role="button">--}}
                                {{--<i class="icon ion-arrow-return-left"></i> 恢复--}}
                            {{--</a>--}}
                            {{--<a class="tool-button operate-btn delete-btn task-delete-permanently-this" role="button">--}}
                                {{--<i class="icon ion-trash-a"></i> 彻底删除--}}
                            {{--</a>--}}
                        {{--@endif--}}

                    @endif

                    {{--未删除--}}
                    @if($item->deleted_at == null)

                        {{--是否发布--}}
                        @if($item->item_active == 0)
                            <a class="tool-button operate-btn edit-btn task-edit-this" role="button">
                                <i class="icon ion-edit"></i> 编辑
                            </a>
                            <a class="tool-button operate-btn publish-btn task-publish-this" role="button">
                                <i class="icon ion-paper-airplane"></i> 发布
                            </a>
                            {{--<a class="tool-button operate-btn delete-btn task-delete-permanently-this" role="button">--}}
                                {{--<i class="icon ion-trash-a"></i> 删除--}}
                            {{--</a>--}}

                        @elseif($item->item_active == 1)

                            {{--是否完成--}}
                            @if(in_array($me_staff->user_type,[0,1,9,11,19,21,22,41,61,88]))

                                <select class="form-control form-filter" name="result"  style="width:72px;">
                                    <option value="0">结果</option>
                                    <option value="71">未接</option>
                                    <option value="72">拒接</option>
                                    <option value="1">通话</option>
                                    <option value="19">加微信</option>
                                    <option value="51">打错了</option>
                                    <option value="99">空号</option>
                                </select>

                                <a class="tool-button operate-btn complete-btn task-complete-this" role="button">
                                    <i class="icon ion-android-checkbox-outline"></i> 完成
                                </a>

                                {{----}}
                                {{--@if($item->is_completed == 0)--}}
                                {{--@elseif($item->is_completed == 1)--}}
                                    {{--<lable class="tag bg-olive">--}}
                                        {{--<i class="icon ion-android-checkbox"></i> 已完成 by【{{ $item->completer->true_name or '' }}】{{ time_show($item->completed_at) }}--}}
                                    {{--</lable>--}}
                                    {{--@if($item->item_result == 0) <lable class="tag bg-info">未标记</lable>--}}
                                    {{--@elseif($item->item_result == 1) <lable class="tag bg-olive">通话</lable>--}}
                                    {{--@elseif($item->item_result == 19) <lable class="tag bg-olive">加微信</lable>--}}
                                    {{--@elseif($item->item_result == 71) <lable class="tag bg-yellow">未接</lable>--}}
                                    {{--@elseif($item->item_result == 72) <lable class="tag bg-yellow">拒接</lable>--}}
                                    {{--@elseif($item->item_result == 51) <lable class="tag bg-yellow">打错了</lable>--}}
                                    {{--@elseif($item->item_result == 99) <lable class="tag bg-yellow">空号</lable>--}}
                                    {{--@endif--}}
                                {{--@endif--}}

                            @endif

                            {{--删除权限--}}
                            {{--@if(in_array($me_staff->user_type,[0,1,9,11]))--}}
                                {{--<a class="tool-button operate-btn delete-btn task-delete-this" role="button">--}}
                                    {{--<i class="icon ion-trash-a"></i> 删除--}}
                                {{--</a>--}}
                            {{--@endif--}}

                            {{--备注权限--}}
                            @if(in_array($me_staff->user_type,[0,1,9,11,19,21,22,41,61,88]))
                                @if(empty($item->remark))
                                    <a class="tool-button remark-toggle" role="button">
                                        <i class="icon ion-ios-pricetag"></i> 添加备注
                                    </a>
                                @else
                                    <a class="tool-button remark-toggle" role="button">
                                        <i class="icon ion-ios-pricetag"></i> 修改备注
                                    </a>
                                @endif
                            @endif

                        @endif

                    @endif


                    {{--分享--}}
                    <a class="tool-button _none" role="button">
                        <i class="fa fa-share"></i> @if($item->share_num) {{ $item->share_num }} @endif
                    </a>

                    {{--评论--}}
                    <a class="tool-button comment-toggle _none" href="{{ url('/item/'.$item->id) }}" role="button">
                        <span>
                            <i class="icon ion-compose"></i> @if($item->comment_num) {{ $item->comment_num }} @endif
                        </span>
                    </a>

                </div>

            </div>
            @endif


            <div class="text-box with-border-top text-center clearfix _none">
                <a target="_self" href="{{ url('/item/'.$item->id) }}">
                    <button class="btn btn-default btn-flat btn-3d btn-clicker" data-hover="点击查看" style="border-radius:0;">
                        <strong>查看详情</strong>
                    </button>
                </a>
            </div>


            {{--备注--}}
            <div class="box-body item-row remark-container"  style="display:none;">

                <div class="box-body remark-input-container">
                    <form action="" method="post" class="form-horizontal form-bordered item-remark-form">

                        {{ csrf_field() }}
                        <input type="hidden" name="item_id" value="{{ $item->id }}" readonly>
                        <input type="hidden" name="operate" value="item-remark-save" readonly>
                        <input type="hidden" name="type" value="1" readonly>

                        <div class="form-group">
                            <div class="col-md-12">
                                <div><textarea class="form-control" name="content" rows="2" placeholder="请输入你的备注">{!! $item->remark or '' !!}</textarea></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 ">
                                <button type="button" class="btn btn-block btn-flat btn-primary remark-submit">提交</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>


        </figure>

    </div>

</div>
@endforeach




@section('custom-style')
<style>
    .form-control { display:inline-block; height:24px; padding:2px 4px; margin-right:4px; font-size:12px; }
    .text-box { padding:4px 12px; }
</style>
@endsection




@section('script')
    @include(env('TEMPLATE_ZY_STAFF').'entrance.item.task-script')
@endsection