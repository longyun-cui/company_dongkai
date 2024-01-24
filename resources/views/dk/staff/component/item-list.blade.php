@foreach($item_list as $num => $item)
<div class="item-piece item-option item-wrapper border-color-1 radius-4px {{ $getType or 'items' }}"
     data-item="{{ $item->id or 0}}"
     data-id="{{ $item->id or 0}}"
     data-item-id="{{ $item->id or 0}}"
     data-getType="{{ $getType or 'items' }}"
>

    <div class="item-container model-left-right image-right bg-white">


        {{--头部--}}
        <figure class="item-header-block">
            <div class="text-box">

                <div class="item-title-row">
                    <a class="clearfix zoom" target="_self" href="{{ url('/item/'.$item->id) }}">
                        <span class="multi-ellipsis-2"><b>{{ $item->title or '' }}</b></span>
                    </a>
                </div>

            </div>
        </figure>


        {{--主体--}}
        <figure class="item-body-block">

            {{--图片--}}
            @if(!empty($item->cover_picture))
                {{--@if(@getimagesize(env('DOMAIN_CDN').'/'.$item->cover_picture))--}}
                <figure class="image-container pull-right">
                    <div class="image-box">
                        <img data-action="zoom" src="{{ $item->cover_picture or '' }}" alt="Property Image">
                        {{--<img data-action="zoom" src="{{ env('DOMAIN_CDN').'/'.$item->cover_pic }}" alt="Property Image">--}}
                        {{--<span class="btn btn-warning">热销中</span>--}}
                    </div>
                </figure>
            @endif

            {{--文本--}}
            <figure class="text-container pull-left @if(!empty($item->cover_picture)) with-image @else without-image @endif">
                <div class="text-box with-border-top">

                    {{--基本信息--}}
                    <div class="item-info-row">

                        {{--是否发布--}}
                        {{--@if($item->is_published == 0)--}}
                            {{--<lable class="info-tags bg-yellow">--}}
                                {{--<i class="icon ion-paper-airplane"></i> 待发布--}}
                            {{--</lable>--}}
                        {{--@elseif($item->item_active == 1)--}}
                        {{--@endif--}}

                        {{--广告--}}
                        @if($item->item_type == 1)
                        @elseif($item->item_type == 11)
                            <span class="info-tags text-default- bg-olive pull-left-">产品</span>
                        @elseif($item->item_type == 41)
                            <span class="info-tags text-default- bg-blue pull-left-">培训</span>
                        @elseif($item->item_type == 99)
                            <span class="info-tags text-default- bg-red pull-left-">公告</span>
                        @else
                            {{--<span class="info-tags text-default pull-left-">其他</span>--}}
                        @endif


                        {{----}}
                        <a href="{{ url('/user/'.$item->owner->id) }}" style="color:#ff7676;font-size:13px;">
                        <span class="item-user-portrait _none">
                            <img src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">
                            {{--<img src="/common/images/bg/background-image.png" data-src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">--}}
                        </span>
                        </a>
                        <a href="{{ url('/user/'.$item->owner->id) }}">
                            {{ $item->owner->username or '' }}
                        </a>
                        <span class=""> • {{ date_show($item->updated_at->timestamp) }}</span>

                        {{--待发布--}}
                        {{--@if($item->is_published == 0)--}}
                        {{--<span class=""> • </span>--}}
                        {{--<a href="{{ url('/org/mine/item/item-edit?item-id='.$item->id) }}">编辑</a>--}}
                        {{--<span class=""> • </span>--}}
                        {{--<a href="javascript:void(0)" class="item-publish-this">发布</a>--}}
                        {{--@endif--}}

                    </div>


                    @if($item->time_type == 1)
                    <div class="row-sm margin-top-4px">
                        <div class="text-row text-time-row text-description-row multi-ellipsis-1">
                            @if(!empty($item->start_time))
                                <span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b></span>
                            @endif
                            @if(!empty($item->end_time))
                                <span class="font-12px"> 至 </span>
                                <span class="label label-danger end-time-inn">
                                    <b>{{ time_show($item->end_time) }}</b>
                                    @if(empty($item->start_time))(截止)@endif
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(!empty($item->address))
                        <div class="text-row text-info-row multi-ellipsis-1 margin-top-4px margin-bottom-4px">
                            <i class="icon ion-location text-blue" style="width:16px;text-align:center;"></i>
                            <span class="">{{ $item->address or '' }}</span>
                        </div>
                    @endif

                    <div class="row-sm margin-top-4px">
                        <div class="text-description-row multi-ellipsis-3">
                            @if(!empty($item->description))
                                {{--{{ $item->description or '' }}--}}
                                {!! $item->description or '' !!}
                                <br>
                            @endif
                            {{ $item->content_show or '' }}
                        </div>
                    </div>

                    <div class="text-title-row multi-ellipsis-1 _none">
                        <span class="info-tags text-danger">该组织•贴片广告</span>
                    </div>

                </div>


                <div class="text-box with-border-top text-center clearfix _none">
                    <a target="_self" href="{{ url('/item/'.$item->id) }}">
                        <button class="btn btn-default btn-flat btn-3d btn-clicker" data-hover="点击查看" style="border-radius:0;">
                            <strong>查看详情</strong>
                        </button>
                    </a>
                </div>




            </figure>
        </figure>


        {{--尾部--}}
        <figure class="item-footer-block">

            <div class="item-info-row">

                {{--浏览--}}
                <a class="tool-button" href="{{ url('/item/'.$item->id) }}" role="button">
                    <span>
                        阅读 @if($item->visit_num){{ $item->visit_num }} @endif
                    </span>
                </a>
                {{--评论--}}
                <a class="tool-button comment-toggle" href="{{ url('/item/'.$item->id) }}" role="button">
                    <span>
                        评论 @if($item->comment_num) {{ $item->comment_num }} @endif
                    </span>
                </a>
                {{--点赞--}}
                {{--<small class="tool-button operate-btn favor-btn" data-num="{{ $item->favor_num or 0 }}" role="button">--}}
                    {{--@if(!empty($me))--}}
                        {{--@if($item->pivot_item_relation->contains('relation_type', 11))--}}
                            {{--<a class="remove-this-favor">--}}
                                {{--<i class="fa fa-heart text-red"></i>--}}
                                {{--<span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--<a class="add-this-favor">--}}
                                {{--<i class="fa fa-heart-o"></i>--}}
                                {{--<span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>--}}
                            {{--</a>--}}
                        {{--@endif--}}
                    {{--@else--}}
                        {{--<a class="add-this-favor">--}}
                            {{--<i class="fa fa-heart-o"></i>--}}
                            {{--<span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>--}}
                        {{--</a>--}}
                    {{--@endif--}}
                {{--</small>--}}


                {{--收藏--}}
                {{--<small class="tool-button operate-btn collection-btn" data-num="{{ $item->collection_num or 0 }}" role="button">--}}
                    {{--@if(!empty($me))--}}
                        {{--@if($item->pivot_item_relation->contains('relation_type', 21))--}}
                            {{--<a class="remove-this-collection">--}}
                                {{--<i class="fa fa-star text-red"></i>--}}
                                {{--<span class="num">@if($item->collection_num){{ $item->collection_num }}@endif</span>--}}
                            {{--</a>--}}
                        {{--@else--}}
                            {{--<a class="add-this-collection">--}}
                                {{--<i class="fa fa-star-o"></i>--}}
                                {{--<span class="num">@if($item->collection_num){{ $item->collection_num }}@endif</span>--}}
                            {{--</a>--}}
                        {{--@endif--}}
                    {{--@else--}}
                        {{--<a class="add-this-collection">--}}
                            {{--<i class="fa fa-star-o"></i>--}}
                            {{--<span class="num">@if($item->collection_num){{ $item->collection_num }}@endif</span>--}}
                        {{--</a>--}}
                    {{--@endif--}}
                {{--</small>--}}

                {{--分享--}}
                <a class="tool-button _none" role="button">
                    <i class="fa fa-share"></i> @if($item->share_num) {{ $item->share_num }} @endif
                </a>


            </div>

        </figure>


        <figure class="text-container clearfix _none">
            <div class="text-box">
                <div class="text-title-row multi-ellipsis-1">
                    <a href="{{ url('/user/'.$item->owner->id) }}" style="color:#ff7676;font-size:13px;">
                        <span class="item-user-portrait">
                            {{--<img src="/common/images/bg/background-image.png" data-src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">--}}
                            <img src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">
                        </span>
                        {{ $item->owner->username or '' }}
                    </a>
                    {{--<span class="text-muted disabled pull-right"><small>{{ date_show($item->updated_at->timestamp) }}</small></span>--}}
                    <span class="text-muted disabled pull-right"><small>{{ date_show($item->published_at) }}</small></span>
                </div>
            </div>
        </figure>


        @if(!empty($item->cover_pic))
        <figure class="image-container pull-right padding-top-2-5- _none">
            <div class="image-box">
                <a class="clearfix zoom-" target="_self" href="{{ url('/item/'.$item->id) }}">
                    {{--<img class="grow" src="/common/images/bg/background-image.png" data-src="{{ env('DOMAIN_CDN').'/'.$item->cover_pic }}" alt="Cover">--}}
                    <img class="grow" src="{{ env('DOMAIN_CDN').'/'.$item->cover_pic }}" alt="Cover">
                    {{--@if(!empty($item->cover_pic))--}}
                    {{--<img class="grow" src="{{ url(env('DOMAIN_CDN').'/'.$item->cover_pic) }}">--}}
                    {{--@else--}}
                    {{--<img class="grow" src="{{ url('/common/images/notexist.png') }}">--}}
                    {{--@endif--}}
                </a>
                {{--<span class="btn btn-warning">热销中</span>--}}
                <span class="paste-tag-inn _none">
                    @if($item->time_type == 1)
                        @if(!empty($item->start_time))
                            <span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b></span>
                        @endif
                        @if(!empty($item->end_time))
                            <span style="font-size:12px;">至</span>
                            <span class="label label-danger end-time-inn"><b>{{ time_show($item->end_time) }}(结束)</b></span>
                        @endif
                    @endif
                </span>
            </div>
        </figure>
        @endif


        <figure class="text-container clearfix _none">
            <div class="text-box with-border-top">

                <div class="text-row text-title-row multi-ellipsis-1 margin-top-4px margin-bottom-4px">
                    <a href="{{ url('/item/'.$item->id) }}"><b>{{ $item->title or '' }}</b></a>
                </div>

                <div class="text-info-row">
                    <a class="clearfix zoom" target="_self" href="{{ url('/item/'.$item->id) }}">
                        <a href="{{ url('/user/'.$item->owner->id) }}">
                            {{ $item->owner->username or '' }}
                        </a>
                        <span class=""> • {{ date_show($item->updated_at->timestamp) }}</span>
                    </a>
                </div>

                {{--@if(empty($item->cover_pic))--}}
                @if($item->time_type == 1)
                <div class="text-row text-time-row multi-ellipsis-1">
                        @if(!empty($item->start_time))
                            <span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b></span>
                        @endif
                        @if(!empty($item->end_time))
                            <span class="font-12px"> 至 </span>
                            <span class="label label-danger end-time-inn">
                                <b>{{ time_show($item->end_time) }}</b>
                                @if(empty($item->start_time))(截止)@endif
                            </span>
                        @endif
                </div>
                @endif
                {{--@endif--}}

                @if(!empty($item->address))
                    <div class="text-row text-info-row multi-ellipsis-1 margin-bottom-4px">
                        <i class="icon ion-location text-blue" style="width:16px;text-align:center;"></i>
                        <span class="">{{ $item->address or '' }}</span>
                    </div>
                @endif

                <div class="text-title-row multi-ellipsis-1 _none">
                    <span class="info-tags text-danger">该组织•贴片广告</span>
                </div>

            </div>

            <div class="text-box with-border-top clearfix">

                <div class="text-row text-tool-row">

                    {{--浏览--}}
                    <a class="tool-button" href="{{ url('/item/'.$item->id) }}" role="button">
                        <span>
                            <i class="fa fa-eye"></i> @if($item->visit_num){{ $item->visit_num }} @endif
                        </span>
                    </a>

                    {{--点赞&$收藏--}}
                    <small class="tool-button operate-btn favor-btn" data-num="{{ $item->favor_num or 0 }}" role="button">
                    @if(Auth::check())
                            @if($item->pivot_item_relation->contains('relation_type', 1))
                            <a class="remove-this-favor">
                                <i class="fa fa-heart text-red"></i>
                                <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                            </a>
                            @else
                                <a class="add-this-favor">
                                <i class="fa fa-heart-o"></i>
                                <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                            </a>
                            @endif
                        @else
                            <a class="add-this-favor">
                            <i class="fa fa-heart-o"></i>
                            <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                        </a>
                        @endif
                    </small>

                    {{--分享--}}
                    <a class="tool-button _none" role="button">
                        <i class="fa fa-share"></i> @if($item->share_num) {{ $item->share_num }} @endif
                    </a>

                    {{--评论--}}
                    <a class="tool-button comment-toggle" href="{{ url('/item/'.$item->id) }}" role="button">
                        <span>
                            <i class="fa fa-commenting-o"></i> @if($item->comment_num) {{ $item->comment_num }} @endif
                        </span>
                    </a>

                </div>

            </div>


            <div class="text-box with-border-top text-center clearfix _none">
                <a target="_self" href="{{ url('/item/'.$item->id) }}">
                    <button class="btn btn-default btn-flat btn-3d btn-clicker" data-hover="点击查看" style="border-radius:0;">
                        <strong>查看详情</strong>
                    </button>
                </a>
            </div>




        </figure>

    </div>




    <div class="boxe panel-default- box-default item-entity-container _none">

        <div class="box-body item-row item-info-row" style="padding:4px 2px;margin-bottom:4px;border-bottom:1px solid #eee;">
            <a href="{{ url('/user/'.$item->owner->id) }}">
                <span class="item-user-portrait"><img src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt=""></span>
                {{ $item->owner->username or '' }}
            </a>
            <span class="text-muted disabled pull-right"><small>{{ date_show($item->updated_at->timestamp) }}</small></span>
        </div>

        <div class="box-body item-row item-title-row">
            <span>
                <a href="{{ url('/item/'.$item->id) }}"><c>{{ $item->title or '' }}</c></a>
            </span>
        </div>

        <div class="box-body item-row item-info-row _none">
            @if($item->item_type == 88)
                <span class="info-tags text-danger pull-left">广告</span>
            @endif
            @if($item->item_type == 11 || $item->time_type == 1)
                <span class="info-tags text-default pull-left">活动</span>
            @endif
            <span class="pull-left">
                <a href="{{ url('/user/'.$item->owner->id) }}">
                    <span class="item-user-portrait _none">
                        {{--<img src="{{ url(env('DOMAIN_CDN').'/'.$item->owner->portrait_img) }}" alt="">--}}
                    </span>
                    {{ $item->owner->username or '' }}
                </a>
            </span>
            <span class="pull-right"><a class="show-menu" role="button"></a></span>
            <span class=" text-muted disabled"> • {{ time_show($item->updated_at->timestamp) }}</span>
            {{--<span class=" text-muted disabled"> • {{ $item->updated_at->format('Y-m-d H:i') }}</span>--}}
            <span class=" text-muted disabled"> • 浏览 <span class="text-blue">{{ $item->visit_num }}</span> 次</span>
        </div>

        @if($item->time_type == 1)
            <div class="box-body item-row item-time-row text-muted">
                <div class="colo-md-12">
                    @if(!empty($item->start_time))
                    <span class="label label-success start-time-inn"><b>{{ time_show($item->start_time) }}</b> (开始)</span>
                    @endif
                    @if(!empty($item->end_time))
                    <span style="font-size:12px;">&nbsp;&nbsp;至&nbsp;&nbsp;</span>
                    <span class="label label-danger end-time-inn"><b>{{ time_show($item->end_time) }} (结束)</b></span>
                    @endif
                </div>
            </div>
        @endif

        @if(!empty($item->description))
            <div class="box-body item-row item-description-row text-muted">
                <div class="colo-md-12"> {{ $item->description or '' }} </div>
            </div>
        @endif

        {{--@if(!empty($item->content))--}}
            {{--<div class="box-body item-row item-content-row _none">--}}
                {{--<article class="colo-md-12"> {!! $item->content or '' !!} </article>--}}
            {{--</div>--}}
        {{--@endif--}}


        {{--tools--}}
        <div class="box-body item-row item-tools-row item-tools-container">

            {{--浏览--}}
            <a class="tool-button" role="button">
                浏览 {{ $item->visit_num }}
            </a>

            {{--点赞&$收藏--}}
            <span class="tool-button operate-btn favor-btn" data-num="{{ $item->favor_num or 0 }}" role="button">
                @if(Auth::check())
                    @if($item->pivot_item_relation->contains('relation_type', 1))
                        <a class="remove-this-favor">
                            <i class="fa fa-heart text-red"></i>
                            <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                        </a>
                    @else
                        <a class="add-this-favor">
                            <i class="fa fa-heart-o"></i>
                            <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                        </a>
                    @endif
                @else
                    <a class="add-this-favor">
                        <i class="fa fa-heart-o"></i>
                        <span class="num">@if($item->favor_num){{ $item->favor_num }}@endif</span>
                    </a>
                @endif
            </span>

            {{--分享--}}
            <a class="tool-button _none" role="button">
                <i class="fa fa-share"></i> @if($item->share_num) {{ $item->share_num }} @endif
            </a>

            {{--评论--}}
            <a class="tool-button comment-toggle" role="button">
                <i class="fa fa-commenting-o"></i> @if($item->comment_num) {{ $item->comment_num }} @endif
            </a>

        </div>


        {{--添加评论--}}
        <div class="box-body item-row comment-container"  style="display:none;">

            <div class="box-body comment-input-container">
            <form action="" method="post" class="form-horizontal form-bordered item-comment-form">

                {{csrf_field()}}
                <input type="hidden" name="item_id" value="{{ $item->id }}" readonly>
                <input type="hidden" name="type" value="1" readonly>

                <div class="form-group">
                    <div class="col-md-12">
                        <div><textarea class="form-control" name="content" rows="2" placeholder="请输入你的评论"></textarea></div>
                    </div>
                </div>

                @if($item->type == 2)
                <div class="form-group form-type">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="support" value="0" checked="checked"> 只评论
                                    </label>
                                </div>
                            </button>
                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="support" value="1"> 支持正方
                                    </label>
                                </div>
                            </button>
                            <button type="button" class="btn">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="support" value="2"> 支持反方
                                    </label>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-group form-type _none">
                    <div class="col-md-12">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_anonymous"> 匿名
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12 ">
                        <button type="button" class="btn btn-block btn-flat btn-primary comment-submit">提交</button>
                    </div>
                </div>

            </form>
            </div>

            @if($item->type == 2)
            <div class="box-body comment-choice-container">
                <div class="form-group form-type">
                    <div class="btn-group">
                        <button type="button" class="btn">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="comments-get-{{encode($item->id)}}" checked="checked"
                                           class="comments-get comments-get-default" data-getSort="all"> 全部评论
                                </label>
                            </div>
                        </button>
                        <button type="button" class="btn">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="comments-get-{{encode($item->id)}}" class="comments-get" data-getSort="positive">
                                    <b class="text-primary">只看【正方 <i class="fa fa-thumbs-o-up"></i>】</b>
                                </label>
                            </div>
                        </button>
                        <button type="button" class="btn">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="comments-get-{{encode($item->id)}}" class="comments-get" data-getSort="negative">
                                    <b class="text-danger">只看【反方 <i class="fa fa-thumbs-o-up"></i>】</b>
                                </label>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            @else
                <input type="hidden" class="comments-get comments-get-default" data-type="all">
            @endif

            {{--评论列表--}}
            <div class="box-body comment-entity-container">

                {{--@include('frontend.component.commentEntity.items')--}}
                <div class="comment-list-container">
                    {{--@if($data->type == 1)--}}
                    {{--@foreach($data->communications as $comment)--}}
                    {{--@include('frontend.component.comment')--}}
                    {{--@endforeach--}}
                    {{--@endif--}}
                </div>

                <div class="col-md-12" style="margin-top:16px;padding:0;">
                    <a href="{{ url('/item/'.$item->id) }}">
                        <button type="button" class="btn btn-block btn-flat btn-more" data-getType="all">更多</button>
                    </a>
                </div>

                {{--@include('frontend.component.commentEntity.topic')--}}

            </div>

        </div>

    </div>


</div>
@endforeach

