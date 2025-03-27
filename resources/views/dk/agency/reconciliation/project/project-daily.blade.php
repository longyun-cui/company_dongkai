<div class="row datatable-body datatable-wrapper project-statistic-daily-clone" data-datatable-item-category="project-daily">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="hidden" name="project-daily-project-id" class="project-id" value="" readonly>
            <input type="hidden" name="project-daily-time-type" class="time-type" value="month" readonly>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="project-daily-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker-c" name="project-daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="project-daily-month">
                <i class="fa fa-chevron-right"></i>
            </button>


            <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit-for-order">
                <i class="fa fa-search"></i> 搜索
            </button>
            <button type="button" class="btn btn-default btn-filter filter-empty" id="filter-empty-for-order">
                <i class="fa fa-remove"></i> 清空
            </button>
            <button type="button" class="btn btn-default btn-filter filter-refresh" id="filter-refresh-for-order">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>
            <button type="button" class="btn btn-default btn-filter filter-cancel" id="filter-cancel-for-order">
                <i class="fa fa-undo"></i> 重置
            </button>


            <div class="pull-left clear-both">
            </div>

        </div>
    </div>


    <div class="col-md-12 datatable-body" style="margin-top:20px;margin-bottom:20px;">
        <div class="eChart" id="" style="width:100%;height:320px;"></div>
    </div>


    <div class="col-md-12 datatable-body">
        <div class="tableArea full">
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