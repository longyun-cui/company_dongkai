@foreach($item_list as $num => $item)
<div class="form-group attachment-option">
    <label class="control-label col-md-2">附件</label>

    <div class="col-md-8">
        <div class="info-img-block">
            {{--<img src="{{ $item->attachment_src or '' }}" alt="">--}}
            <img src="{{ url(env('DOMAIN_CDN').'/'.$item->attachment_src) }}" alt="">
        </div>
    </div>

    <div class="col-md-8 col-md-offset-2" style="clear:left;">
        <div class="info-text-block">

            <span>{{ $item->attachment_name or '未命名' }}</span>
            <a class="tool-button operate-btn delete-btn item-attachment-delete-this" data-id="{{ $item->id }}" data-item="{{ $item->item_id }}" role="button">
                删除
            </a>
        </div>
    </div>
</div>
@endforeach