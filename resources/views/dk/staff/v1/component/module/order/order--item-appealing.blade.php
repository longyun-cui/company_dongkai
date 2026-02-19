{{--处理--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order--item-appealing">
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

                <table class='table table-striped table-bordered' id='datatable--for--order--item-appealing--of--operation-record-list'>
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
                <h3 class="box-title">申诉-订单<span class="id-box"></span></h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order--item-appealing">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order--item-appealing" readonly>
                    <input type="hidden" name="item_id" value="0" readonly>

                    {{--申诉说明--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 申诉说明</label>
                        <div class="col-md-9 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                            <textarea class="form-control" name="order--item-appealing--description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>
                    {{--录音地址--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">录音地址</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="order--item-appealing--url" placeholder="录音地址 带http">
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--order--item-appealing"
                                data-modal-id="modal--for--order--item-appealing"
                                data-form-id="form--for--order--item-appealing"
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