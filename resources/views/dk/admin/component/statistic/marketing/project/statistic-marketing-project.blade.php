<div class="row datatable-body datatable-wrapper statistic-marketing-project-clone" data-datatable-item-category="statistic-marketing-project">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-project-time-type" class="time-type" value="" readonly>

            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="statistic-project-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup date_picker" name="statistic-project-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="statistic-project-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="date">
                <i class="fa fa-search"></i> 按日查询
            </button>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-project-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-project-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-project-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段查看--}}
            <input type="text" class="search-filter filter-keyup date_picker" name="statistic-project-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter filter-keyup date_picker" name="statistic-project-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>


            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 全部查询
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


        </div>


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