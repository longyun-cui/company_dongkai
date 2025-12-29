<div class="row datatable-body datatable-wrapper statistic-call-task-analysis-clone" data-datatable-item-category="statistic-call-task-analysis">


    <div class="col-md-12 datatable-search-row datatable-search-box search-wrapper">


        <div class=" pull-left">

            <button type="button" onclick="" class="btn btn-filter btn-success- statistic-call-task-analysis-create">
                <i class="fa fa-plus"></i> 生成日报
            </button>

        </div>


        <div class="pull-right">


            <input type="hidden" name="statistic-call-task-analysis-time-type" class="time-type" value="" readonly>

            <input type="text" class="search-filter form-filter filter-keyup" name="statistic-call-task-analysis-keyword" placeholder="关键字" />

            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="statistic-call-task-analysis-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="statistic-call-task-analysis-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="statistic-call-task-analysis-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-default btn-filter filter-submit filter-submit-by-date" data-time-type="date">
                <i class="fa fa-search"></i> 查询
            </button>


            {{--按月查看--}}
{{--            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-call-task-analysis-month">--}}
{{--                <i class="fa fa-chevron-left"></i>--}}
{{--            </button>--}}
{{--            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-call-task-analysis-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />--}}
{{--            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-call-task-analysis-month">--}}
{{--                <i class="fa fa-chevron-right"></i>--}}
{{--            </button>--}}
{{--            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">--}}
{{--                <i class="fa fa-search"></i> 按月查询--}}
{{--            </button>--}}

            {{--按时间段查看--}}
{{--            <input type="text" class="search-filter filter-keyup date_picker" name="statistic-call-task-analysis-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />--}}
{{--            <input type="text" class="search-filter filter-keyup date_picker" name="statistic-call-task-analysis-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">--}}
{{--                <i class="fa fa-search"></i> 按时间段搜索--}}
{{--            </button>--}}


{{--            <button type="button" class="btn btn-success btn-filter filter-submit">--}}
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