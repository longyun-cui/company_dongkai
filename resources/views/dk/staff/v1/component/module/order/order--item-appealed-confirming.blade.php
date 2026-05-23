{{--处理--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order--item-appealed-confirming">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white">


        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-4px">
                <h3 class="box-title">操作记录 - 订单<span class="id-box"></span></h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body">

                <table class='table table-striped table-bordered' id='datatable--for--order--item-appealed-confirming--of--operation-record-list'>
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
                <h3 class="box-title">申诉确认 - 订单<span class="id-box"></span></h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order--item-appealed-confirming">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order--item-appealed-confirming" readonly>
                    <input type="hidden" name="item_id" value="0" readonly>

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
                            <a class="btn btn-xs item-recording-list-get--for--order--item-inspecting">获取录音</a>
                        </div>
                        <div class="col-md-9 col-md-offset-2">
                            <div class="btn-group">
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="handling--recording-speed" value="0.75"> x0.75</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="handling--recording-speed" value="1"> x1</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="handling--recording-speed" value="1.25" checked="checked"> x1.25</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="handling--recording-speed" value="1.5"> x1.5</label>
                                    </span>
                                </button>
                                <button type="button" class="btn">
                                    <span class="radio">
                                        <label><input type="radio" name="handling--recording-speed" value="2"> x2</label>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{--申诉说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 申诉说明</label>
                        <div class="col-md-9 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="order--item-appealed-confirming--description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--录音地址--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">录音地址</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="order--item-appealed-confirming--url" placeholder="录音地址 带http">
                        </div>
                    </div>
                    {{--班次--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 申诉确认</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="order--item-appealed-confirming--result"
                                    data-modal="#modal--for--order--item-appealed-confirming"
                            >
                                <option value="">申诉确认</option>
                                <option value ="1">确认</option>
                                <option value ="2">驳回</option>
                            </select>
                        </div>
                    </div>
                    {{--拒绝类型--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 拒绝类型</label>
                        <div class="col-md-9 ">
                            <div class="btn-group">

                                @foreach(config('dk.common-config.rejected_reason') as $k => $v)
                                    <button type="button" class="btn">
                                    <span class="checkbox">
                                        <label>
                                            <input type="checkbox" name="order--item-appealed-confirming--rejected-reason[]" value="{{ $k }}"> {{ $v }}
                                        </label>
                                    </span>
                                    </button>
                                @endforeach

                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--order--item-appealed-confirming"
                                data-modal-id="modal--for--order--item-appealed-confirming"
                                data-form-id="form--for--order--item-appealed-confirming"
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