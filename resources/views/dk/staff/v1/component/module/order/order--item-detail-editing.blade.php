{{--审核--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order--item-detail-editing">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white">


        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-4px">
                <h3 class="box-title">操作记录 <span class="id-box"></span></h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="">

                <div class="row col-md-12 datatable-search-row _none">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="order-inspect-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="order-inspect-attribute">
                            <option value="-1">选择属性</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable--for--order--item-editing--of--operation-record-list'>
                    <thead>
                    <tr role='row' class='heading'>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>

        </div>


        <div class="box- box-info- form-container">

            <div class="box-header with-border" style="margin:8px 0;">
                <h3 class="box-title">审核-订单<span class="id-box"></span></h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order--item-detail-editing">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order--item-editing" readonly>
                    <input type="hidden" name="item_id" value="0" readonly>

                    {{--项目--}}
                    <div class="form-group item-project-box">
                        <label class="control-label col-md-2">项目</label>
                        <div class="col-md-9 ">
                            <select class="form-control select2-reset select2--project disabled"
                                    name="project_id"
                                    id="select2--project--for--order--item-editing"
                                    data-modal="#modal--for--order--item-editing"
                                    data-item-category="1"
                            >
                                <option data-id="" value="">选择项目</option>
                            </select>
                        </div>
                    </div>
                    {{--客户--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户信息</label>
                        <div class="col-md-9 ">
                            <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                                <input type="text" class="form-control" name="client_name" placeholder="客户姓名" value="" data-default="">
                            </div>
                            <div class="col-sm-6 col-md-6 padding-0" style="width:50%;">
                                <input type="text" class="form-control" name="client_phone" placeholder="客户电话" value="" data-default="" readonly>
                            </div>
                        </div>
                    </div>
                    {{--所在城市--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">城市</label>
                        <div class="col-md-9 ">
                            <div class="col-sm-6 col-md-6 padding-0">
                                <select class="form-control modal--select2 select2-reset select2--location-city"
                                        name="location_city"
                                        id="select--location-city--for--order--item-editing"
                                        data-modal="#modal--for--order--item-editing"
                                        data-item-category="1"
                                        data-location-district-target="#select2--location-district--for--order--item-editing"
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
                                        id="select2--location-district--for--order--item-editing"
                                        data-modal="#modal--for--order--item-editing"
                                        data-item-category="11"
                                        data-target="#select--location-city--for--order--item-editing"
                                >
                                    <option value="">选择区域</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 item-detail-operate" data-operate=""></div>
                    </div>
                    {{--患者类型--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">患者类型</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="client_type"
                                    data-modal="#modal--for--order--item-editing"
                            >
                                <option value="">选择患者类型</option>
                                @if(!empty(config('dk.common-config.dental_type')))
                                @foreach(config('dk.common-config.dental_type') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    {{--牙齿数量--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">牙齿数量</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="field_1"
                                    data-modal="#modal--for--order--item-editing"
                            >
                                <option value="">选择牙齿数量</option>
                                @if(!empty(config('dk.common-config.teeth_count')))
                                @foreach(config('dk.common-config.teeth_count') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    {{--客户意向--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">客户意向</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="client_intention"
                                    data-modal="#modal--for--order--item-editing"
                            >
                                <option value="">选择客户意向</option>
                                @if(!empty(config('dk.common-config.client_intention')))
                                @foreach(config('dk.common-config.client_intention') as $k => $v)
                                    <option value ="{{ $k }}">{{ $v }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    {{--通话小结--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">通话小结</label>
                        <div class="col-md-9 ">
                            <textarea class="form-control" name="description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--录音--}}
                    <div class="form-group item-recording-box">
                        <label class="control-label col-md-2">通话录音</label>
                        <div class="col-md-9 control-label" style="text-align:left;">
                            <span class="item-detail-text"></span>
                            <a class="btn btn-xs item-recording-list-get--for--order--item-editing">获取录音</a>
                        </div>
                        <div class="col-md-9 col-md-offset-2">
                            <div class="btn-group">
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="0.75"> x0.75</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1" checked="checked"> x1</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1.25"> x1.25</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="1.5"> x1.5</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="recording-speed" value="2"> x2</label>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--order--item-editing"
                                data-modal-id="modal--for--order--item-editing"
                                data-form-id="form--for--order--item-editing"
                        >
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default edit-cancel" id="">取消</button>
                    </div>
                </div>
            </div>


        </div>


    </div>
</div>