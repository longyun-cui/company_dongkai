<div class="row datatable-body datatable-wrapper datatable-trade-list-clone" data-datatable-item-category="trade">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="hidden" class="time-type" name="trade-time-type" value="all" readonly>


            <input type="text" class="search-filter form-filter filter-keyup" name="trade-id" placeholder="ID" value="" />
{{--            <input type="text" class="search-filter form-filter filter-keyup" name="trade-client-phone" placeholder="客户电话" value="" />--}}



            @if(in_array($me->user_type, [0,1,9,11,19]))
            <select class="search-filter form-filter" name="trade-is-confirmed">
                <option value="-1">全部</option>
                <option value="0">待确认</option>
                <option value="1">已确认</option>
            </select>
            @endif


            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="trade-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter date_picker-c search-date" name="trade-date" placeholder="交付日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="trade-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="date">
                <i class="fa fa-search"></i> 按日查询
            </button>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="trade-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker-c search-month" name="trade-month" placeholder="交付月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="trade-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段导出--}}
            <input type="text" class="search-filter form-filter date_picker-c search-start" name="trade-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter form-filter date_picker-c search-ended" name="trade-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>






            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 全部搜索
            </button>
            <button type="button" class="btn btn-info btn-filter filter-empty">
                <i class="fa fa-remove"></i> 清空
            </button>
            <button type="button" class="btn btn-primary btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>
            <button type="button" class="btn btn-warning btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


            <div class="pull-left clear-both">
            </div>

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