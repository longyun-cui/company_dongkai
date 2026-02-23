{{--编辑-工单--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order-aesthetic--item-edit">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-64px margin-bottom-64px bg-white">


        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加（医美）工单</h3>
                <div class="box-tools pull-right">
                </div>
            </div>


            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order-aesthetic--item-edit">
            <div class="box-body">

                {{ csrf_field() }}
                <input readonly type="hidden" class="form-control" name="operate[type]" value="create" data-default="create">
                <input readonly type="hidden" class="form-control" name="operate[id]" value="0" data-default="0">
                <input readonly type="hidden" class="form-control" name="operate[item_category]" value="item" data-default="item">
                <input readonly type="hidden" class="form-control" name="operate[item_type]" value="order" data-default="order">
                <input readonly type="hidden" class="form-control" name="operate[order_category]" value="aesthetic" data-default="aesthetic">
                <input readonly type="hidden" class="form-control" name="order_category" value="11" data-default="11">



                {{--自定义标题--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="">
                    </div>
                </div>

                {{--班次--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 班次</label>
                    <div class="col-md-9 ">
                        <select class="form-control modal--select2 select2-reset"
                                name="work_shift"
                                data-modal="#modal--for--order-aesthetic--item-edit"
                        >
                            <option value="">选择班次</option>
                            <option value ="1">白班</option>
                            <option value ="2">夜班</option>
                        </select>
                    </div>
                </div>

                {{--项目--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                    <div class="col-md-9 ">
                        <select class="form-control modal--select2 select2-box-c- select2--project-"
                                name="project_id"
                                data-modal="#modal--for--order-aesthetic--item-edit"
                                data-project-category="11"
                        >
                            <option data-id="" value="">选择项目</option>
                            @if(!empty($project_list__for__aesthetic) && count($project_list__for__aesthetic) > 0)
                                @foreach($project_list__for__aesthetic as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{--客户信息--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户信息</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="client_name" placeholder="客户姓名" value="" data-default="">
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <input type="text" class="form-control" name="client_phone" placeholder="客户电话" value="" data-default="">
                        </div>
                    </div>
                </div>

                {{--所在城市--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control modal--select2 select2-reset select2--location-city"
                                    name="location_city"
                                    id="select--city--for--order-aesthetic--item-edit"
                                    data-modal="#modal--for--order-aesthetic--item-edit"
                                    data-item-category="1"
                                    data-location-district-target="#select2--location--for--order-aesthetic--item-edit"
                            >
                                <option value="">选择城市</option>
                                @if(!empty($location_city_list) && count($location_city_list) > 0)
                                    @foreach($location_city_list as $v)
                                        <option value="{{ $v->location_city }}">{{ $v->location_city }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select2-reset select2--location"
                                    name="location_district"
                                    id="select2--location--for--order-aesthetic--item-edit"
                                    data-modal="#modal--for--order-aesthetic--item-edit"
                                    data-item-category="11"
                                    data-target="#select--city--for--order-aesthetic--item-edit"
                            >
                                <option value="">选择区域</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{--品类--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 品类</label>
                    <div class="col-md-9 ">
                        <select class="form-control modal--select2 select2-reset"
                                name="field_1"
                                data-modal="#modal--for--order-aesthetic--item-edit"
                        >
                            <option value="">选择品类</option>
                            @if(!empty(config('dk.common-config.aesthetic_type')))
                                @foreach(config('dk.common-config.aesthetic_type') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                {{--是否+V--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 是否+V</label>
                    <div class="col-md-9 ">
                        <div class="col-sm-4 col-md-4 padding-0">
                            <div class="btn-group">

                                <button type="button" class="btn">
                                <span class="radio">
                                    <label><input type="radio" name="is_wx" value="0" checked="checked"> 否</label>
                                </span>
                                </button>
                                <button type="button" class="btn">
                                <span class="radio">
                                    <label><input type="radio" name="is_wx" value="1"> 是</label>
                                </span>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
                {{--微信号--}}
                <div class="form-group wx_box">
                    <label class="control-label col-md-2">微信号</label>
                    <div class="col-md-9 ">
                        <input type="text" class="form-control" name="wx_id" placeholder="微信号" value="" data-default="">
                    </div>
                </div>

                {{--录音地址--}}
{{--                <div class="form-group">--}}
{{--                    <label class="control-label col-md-2">录音地址</label>--}}
{{--                    <div class="col-md-9 ">--}}
{{--                        <input type="text" class="form-control" name="recording_address" placeholder="录音地址" value="" data-default="">--}}
{{--                    </div>--}}
{{--                </div>--}}

                {{--通话小结--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                    <div class="col-md-9 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                    </div>
                </div>

                {{--通话小结--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 通话小结</label>
                    <div class="col-md-9 ">
                        <p>要求：准确，全面，丰富</p>
                        <p>范本：用户当前</p>
                    </div>
                </div>

            </div>
            </form>


            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit submit--for--order--item-edit"
                                id="submit--for--order-aesthetic--item-edit"
                                data-modal-id="modal--for--order-aesthetic--item-edit"
                                data-form-id="form--for--order-aesthetic--item-edit"
                        >
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel">取消</button>
                    </div>
                </div>
            </div>


        </div>


    </div>
</div>