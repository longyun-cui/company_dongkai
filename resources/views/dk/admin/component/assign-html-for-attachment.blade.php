@foreach($item_list as $num => $item)
<div class="form-group attachment-option">
    <label class="control-label col-md-2">附件</label>

    <div class="col-md-8 fileinput-group">
        <div class="info-img-block">

            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$item->attachment_src) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$item->attachment_src) }}" alt="" />
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-8 col-md-offset-2" style="clear:left;">
        <div class="info-text-block">

            <span>{{ $item->attachment_name or '未命名' }}</span>
            <a class="tool-button operate-btn delete-btn item-attachment-delete-this" data-id="{{ $item->id }}" data-item-id="{{ $item->item_id }}" role="button">删除</a>

        </div>
    </div>
</div>
@endforeach