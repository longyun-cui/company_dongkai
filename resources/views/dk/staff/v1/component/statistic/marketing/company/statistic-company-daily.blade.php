<div class="row datatable-body datatable-wrapper statistic-company-daily-clone" data-datatable-item-category="statistic-company-daily">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-company-daily-time-type" class="time-type" value="" readonly>


            <select class="search-filter form-filter filter-lg select2-box-c select2-company" name="statistic-company-daily-company">
                <option value="-1">选择公司</option>
                @if(!empty($company_list) && count($company_list) > 0)
                @foreach($company_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
                @endif
            </select>

            <select class="search-filter form-filter filter-lg select2-box-c select2-channel" name="statistic-company-daily-channel">
                <option value="-1">选择渠道</option>
                @if(!empty($channel_list) && count($channel_list) > 0)
                @foreach($channel_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
                @endif
            </select>

            <select class="search-filter form-filter filter-lg select2-box-c select2-business" name="statistic-company-daily-business">
                <option value="-1">选择商务</option>
                @if(!empty($business_list) && count($business_list) > 0)
                @foreach($business_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
                @endif
            </select>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-company-daily-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month_picker" name="statistic-company-daily-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-company-daily-month">
                <i class="fa fa-chevron-right"></i>
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


    <div class="col-md-12 datatable-body" style="margin-top:40px;">
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