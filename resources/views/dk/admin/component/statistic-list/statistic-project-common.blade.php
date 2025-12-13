{{--修改-属性-信息--}}
<div class="modal fade modal-main-body modal-wrapper" id="modal-for-item-field-set-of-statistic-project-daily">
    <div class="modal-content col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">
                    【修改】
                    <span class="item-field-set-item-name">项目日报</span>
                    【<span class="item-field-set-item-id"></span>】
                </h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="form-for-item-field-set-of-statistic-project-daily">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="operate-category" value="field-set" readonly>
                    <input type="hidden" name="operate-type" value="add" readonly>
                    <input type="hidden" name="item-category" value="" data-default="" readonly>
                    <input type="hidden" name="item-type" value="" data-default="" readonly>
                    <input type="hidden" name="item-id" value="0" readonly>
                    <input type="hidden" name="column-type" value="" readonly>
                    <input type="hidden" name="column-key" value="" readonly>
                    <input type="hidden" name="column-key2" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 field-set-column-name"></label>
                        <div class="col-md-8 ">

                            {{--// text--}}
                            <input type="text" class="form-control column-value" name="item-field-set-text-value" autocomplete="off" placeholder="" value="">
                            {{--text--}}
                            <textarea class="form-control column-value" name="item-field-set-textarea-value" rows="6" cols="100%"></textarea>


                            {{--// datetime--}}
                            <input type="text" class="form-control form-filter column-value time_picker" name="item-field-set-datetime-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            {{--// date--}}
                            <input type="text" class="form-control form-filter column-value date_picker" name="item-field-set-date-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">


                            {{--// radio--}}
                            <div  class="form-control- form-filter- btn-group radio-wrapper">
                            </div>


                            {{--// select--}}
                            <select class="form-control column-value select-primary" name="item-field-set-select-value" data-class="" style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                            <select class="form-control column-value select-assistant" name="item-field-set-select-value2" data-class="" style="width:48%;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>

                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="edit-submit-for-item-field-set-of-statistic-project-daily">
                            <i class="fa fa-check"></i> 提交
                        </button>
                        <button type="button" class="btn btn-default" id="edit-cancel-for-item-field-set-of-statistic-project-daily">
                            取消
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>