<div class="row datatable-body datatable-wrapper statistic-list-project-detail-clone" data-datatable-item-category="statistic-list-project-detail">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-list-project-detail-project-id" class="project-id" value="" readonly>
            <input type="hidden" name="statistic-list-project-detail-time-type" class="time-type" value="month" readonly>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-list-project-detail-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-list-project-detail-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-list-project-detail-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段查看--}}
            <input type="text" class="search-filter filter-keyup date_picker-c" name="statistic-list-project-detail-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter filter-keyup date_picker-c" name="statistic-list-project-detail-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>


{{--            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="all">--}}
{{--                <i class="fa fa-search"></i> 全部查询--}}
{{--            </button>--}}

            <button type="button" class="btn btn-default btn-filter filter-empty">
                <i class="fa fa-remove"></i> 清空
            </button>

            <button type="button" class="btn btn-default btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>

            <button type="button" class="btn btn-default btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


        </div>


    </div>


    <div class="col-md-12 datatable-body _none" style="margin-top:40px;">
        <div class="eChart" id="" style="width:100%;min-width:100px;height:320px;"></div>
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