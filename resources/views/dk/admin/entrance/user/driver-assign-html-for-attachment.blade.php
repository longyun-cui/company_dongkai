{{--主驾-驾驶证--}}
<div class="form-group">
    <label class="control-label col-md-2">主驾-驾驶证</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->driver_licence))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_licence) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_licence) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="driver_licence_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="driver_licence"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--主驾-资格证--}}
<div class="form-group">
    <label class="control-label col-md-2">主驾-资格证</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->driver_certification))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_certification) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_certification) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="driver_certification_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="driver_certification"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--主驾-身份证-正页--}}
<div class="form-group">
    <label class="control-label col-md-2">主驾-身份证-正页</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->driver_ID_front))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="driver_ID_front_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="driver_ID_front"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--主驾-身份证-副页--}}
<div class="form-group">
    <label class="control-label col-md-2">主驾-身份证-副页</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->driver_ID_back))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_back) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_back) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="driver_ID_back_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="driver_ID_back"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>




{{--副驾-驾驶证--}}
<div class="form-group">
    <label class="control-label col-md-2">副驾-驾驶证</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->sub_driver_licence))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_licence) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_licence) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="sub_driver_licence_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="sub_driver_licence"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--副驾-资格证--}}
<div class="form-group">
    <label class="control-label col-md-2">副驾-资格证</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->sub_driver_certification))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_certification) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_certification) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="sub_driver_certification_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="sub_driver_certification"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--副驾-身份证-正页--}}
<div class="form-group">
    <label class="control-label col-md-2">副驾-身份证-正页</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->sub_driver_ID_front))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->driver_ID_front) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="sub_driver_ID_front_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="sub_driver_ID_front"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>
{{--副驾-身份证-副页--}}
<div class="form-group">
    <label class="control-label col-md-2">副驾-身份证-副页</label>

    <div class="col-md-8 fileinput-group">
        @if(!empty($data->sub_driver_ID_back))
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <a class="lightcase-image" data-rel="lightcase" href="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_back) }}">
                        <img src="{{ url(env('DOMAIN_CDN').'/'.$data->sub_driver_ID_back) }}" alt="" />
                    </a>
                </div>
            </div>
        @else
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
        @endif
        <input id="multiple-images" type="file" class="file-multiple-images" name="sub_driver_ID_back_file" multiple- >
    </div>

    <div class="col-md-8 col-md-offset-2 margin-top-4px">
        <button type="button" class="btn btn-success item-submit-for-attachment-set" data-key="sub_driver_ID_back"><i class="fa fa-check"></i> 提交</button>
        <button type="button" class="btn btn-default item-cancel-for-attachment-set">取消</button>
    </div>
</div>