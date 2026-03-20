<div class="row datatable-body datatable-wrapper delivery-daily-clone" data-datatable-item-category="delivery-daily">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="hidden" name="delivery-daily-time-type" class="time-type" value="" readonly>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="delivery-daily-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month-picker-c" name="delivery-daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="delivery-daily-month">
                <i class="fa fa-chevron-right"></i>
            </button>


            <button type="button" class="btn btn-default btn-filter filter-submit">
                <i class="fa fa-search"></i> 搜索
            </button>
            <button type="button" class="btn btn-default btn-filter filter-empty">
                <i class="fa fa-remove"></i> 清空
            </button>
            <button type="button" class="btn btn-default btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>
            <button type="button" class="btn btn-default btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


            <div class="pull-left clear-both">
            </div>

        </div>
    </div>


    <div class="col-md-12 datatable-body">
        <div class="box box-primary box-solid- margin-bottom-4px" style="box-shadow:0 0;">

            <div class="box-header with-border- margin-top-16px padding-top-8px _none">
                <h3 class="box-title datatable-title"></h3>
            </div>

            <div class="col-md-12 datatable-body" style="margin-top:20px;margin-bottom:20px;">
                <div class="eChart" id="" style="width:100%;height:320px;"></div>
            </div>

        </div>
    </div>


    <div class="col-md-12 datatable-body">
        <div class="box box-primary box-solid- margin-bottom-4px" style="box-shadow:0 0;">

            <div class="box-header with-border- margin-top-16px padding-top-8px _none">
                <h3 class="box-title datatable-title"></h3>
            </div>

            <div class="box-body no-padding">
                <div class="tableArea table-delivery full margin-top-8px">
                    <table class='table table-striped table-bordered table-hover order-column'>
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>






</div>