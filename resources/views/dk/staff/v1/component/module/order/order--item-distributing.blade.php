{{--交付--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal--for--order--item-distributing">
    <div class="modal-content col-md-8 col-md-offset-2 margin-top-16px margin-bottom-64px bg-white">


        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-8px">
                <h3 class="box-title">交付记录 <span class="id-box"></span></h3>
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

                <table class='table table-striped table-bordered' id='datatable--for--order--item-distributing--of--delivery-record-list'>
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
                <h3 class="box-title">分发-订单<span class="id-box"></span></h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form--for--order--item-distributing">
                <div class="box-body  info-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate" value="order--item-distributing" readonly>
                    <input type="hidden" name="item_id" value="0" readonly>


                    {{--编辑订单--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">选择项目</label>
                        <div class="col-md-9 ">
                            <select class="form-control select2-reset select2--project"
                                    name="project_id"
                                    id="select2--project--for--order--item-distributing"
                                    data-modal="#modal--for--order--item-distributing"
                                    data-item-category="1"
                            >
                                <option data-id="0" value="0">选择项目</option>
                            </select>
                        </div>
                    </div>
                    {{--编辑订单--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">选择客户</label>
                        <div class="col-md-9 ">
                            <select class="form-control select2-reset select2--client"
                                    name="client_id"
                                    id="select2--project--for--order--item-distributing"
                                    data-modal="#modal--for--order--item-distributing"
                                    data-item-category="1"
                            >
                                <option data-id="0" value="0">选择客户</option>
                            </select>
                        </div>
                    </div>
                    {{--编辑订单--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">交付结果</label>
                        <div class="col-md-9 ">
                            <select class="form-control modal--select2 select2-reset"
                                    name="order-item-distributing--delivered-result"
                                    id=""
                                    data-modal="#modal--for--order--item-distributing"
                            >
                                <option value="">交付结果</option>
                                @foreach(config('dk.common-config.delivered_result') as $v)
                                    <option value="{{ $v }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--编辑订单--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="control-label col-md-2">录音地址</label>--}}
{{--                        <div class="col-md-9 ">--}}
{{--                            <input type="text" class="form-control" name="order-item-distributing--recording-address" autocomplete="off" placeholder="" value="">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    {{--编辑订单--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">交付说明</label>
                        <div class="col-md-9 ">
                            <textarea class="form-control" name="order-item-distributing--description" rows="4" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <button type="button" class="btn btn-success edit-submit"
                                id="item-submit--for--order--item-distributing"
                                data-modal-id="modal--for--order--item-distributing"
                                data-form-id="form--for--order--item-distributing"
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