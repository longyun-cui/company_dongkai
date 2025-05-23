<div class="row datatable-body datatable-wrapper statistic-call-order-city-clone" data-datatable-item-category="statistic-order-city" style="width:100%">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-order-daily-time-type" class="time-type" value="month" readonly>


            <select class="search-filter form-filter filter-md select2-box-c" name="statistic-order-city">
                <option value="-1">选择城市</option>
                @if(!empty($district_city_list) && count($district_city_list) > 0)
                    @foreach($district_city_list as $v)
                        <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                    @endforeach
                @endif
            </select>

            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-order-city">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-order-city" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-order-city">
                <i class="fa fa-chevron-right"></i>
            </button>


            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 查询
            </button>

{{--            <button type="button" class="btn btn-default btn-filter filter-empty">--}}
{{--                <i class="fa fa-remove"></i> 清空--}}
{{--            </button>--}}

            <button type="button" class="btn btn-default btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>

            <button type="button" class="btn btn-default btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


        </div>


    </div>


    <div class="col-md-12 datatable-body _none" style="margin-top:40px;">
        <div class="eChart" id="" style="width:100%;min-width:480px;height:320px;"></div>
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