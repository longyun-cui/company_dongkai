<div class="row datatable-body datatable-wrapper delivery-daily-clone">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="hidden" name="delivery-daily-time-type" class="time-type" value="month" readonly>


            <select class="search-filter form-filter select2-box-c" name="delivery-daily-client" style="width:200px;">
                <option value="-1">全部项目</option>
                @if(!empty($client_list) && count($client_list) > 0)
                    @foreach($client_list as $v)
                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                    @endforeach
                @endif
            </select>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="delivery-daily-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker-c" name="delivery-daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="delivery-daily-month">
                <i class="fa fa-chevron-right"></i>
            </button>


            <button type="button" class="btn btn-default btn-filter filter-submit" data-time-type="month">
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




    <div class="col-md-12 datatable-body" style="margin-top:40px;">
        <div class="eChart" id="" style="width:100%;min-width:1000px;height:320px;"></div>
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