<div class="row datatable-body datatable-wrapper statistic-export-clone" data-datatable-item-category="statistic-export" data-item-name="导出">


    {{--录单--}}
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box box-primary box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">录单•导出</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="">
                        <div class="col-md-12 datatable-search-row filter-box">
                            <div class="pull-right">


                                <input type="hidden" name="export-time-type" class="time-type" value="" readonly>


                                <select class="search-filter form-filter filter-lg select2-box-c" name="export-item-category">
                                    <option value="">选择工单类型</option>
                                    <option value="1">口腔</option>
                                    <option value="11">医美</option>
                                    <option value="31">二奢</option>
                                </select>

                                <select class="search-filter form-filter filter-lg select2-reset select2-project-c" name="export-project">
                                    <option value="-1">选择项目</option>
                                </select>

                                <select class="search-filter form-filter filter-lg select2-reset select2-client-c" name="export-client">
                                    <option value="-1">选择客户</option>
                                </select>

                                <select class="search-filter form-filter filter-lg select2-reset select2-box-c" name="export-inspected-result">
                                    <option value ="-1">审核结果</option>
                                    <option value ="通过">通过</option>
                                    <option value ="拒绝">拒绝</option>
                                    <option value ="重复">重复</option>
                                    <option value ="内部通过">内部通过</option>
                                </select>


                                {{--<button type="button" class="btn btn-default btn-filter filter-submit filter-submit-for-order" data-type="latest">--}}
                                {{--<i class="fa fa-download"></i> 最新导出--}}
                                {{--</button>--}}


                                {{--按天导出--}}
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="export-date">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="export-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="export-date">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-filter filter-submit-for-order-export" data-type="date" data-time-type="date">
                                    <i class="fa fa-download"></i> 按日导出
                                </button>


                                {{--按月导出--}}
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="export-month">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <input type="text" class="search-filter form-filter filter-keyup month-picker month_picker-c" name="export-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="export-month">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-filter filter-submit-for-order-export" data-type="month" data-time-type="month">
                                    <i class="fa fa-download"></i> 按月导出
                                </button>


                                {{--按时间段导出--}}
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="export-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="export-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />

                                <button type="button" class="btn btn-primary btn-filter filter-submit-for-order-export" data-type="period" data-time-type="period">
                                    <i class="fa fa-download"></i> 按时间段导出
                                </button>


                                <button type="button" class="btn btn-default btn-filter filter-empty-for-export">
                                    <i class="fa fa-remove"></i> 清空重选
                                </button>


                                <div class="month-picker-box clear-both">
                                </div>


                                <div class="month-picker-box clear-both">
                                </div>


                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    {{--交付--}}
    @if(in_array($me->user_type,[0,1,9,11,19,61,66]))
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title comprehensive-month-title">交付•导出</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-stacked">
                    <li class="">
                        <div class="col-md-12 datatable-search-row filter-box">
                            <div class="pull-right">


                                <input type="hidden" name="delivery-export-time-type" class="time-type" value="" readonly>


                                <select class="search-filter form-filter filter-lg select2-box-c" name="delivery-export-item-category">
                                    <option value="">选择工单类型</option>
                                    <option value="1">口腔</option>
                                    <option value="11">医美</option>
                                    <option value="31">二奢</option>
                                </select>

                                <select class="search-filter form-filter filter-lg select2-reset select2-project-c" name="delivery-export-project">
                                    <option value="-1">选择项目</option>
                                </select>

                                <select class="search-filter form-filter filter-lg select2-reset select2-client-c" name="delivery-export-client">
                                    <option value="-1">选择客户</option>
                                </select>


                                {{--按天导出--}}
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="delivery-export-date">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="delivery-export-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="delivery-export-date">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-filter filter-submit-for-delivery-export" data-type="date" data-time-type="date">
                                    <i class="fa fa-download"></i> 按日导出
                                </button>


                                {{--按月导出--}}
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="delivery-export-month">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                                <input type="text" class="search-filter form-filter filter-keyup month-picker month_picker-c" name="delivery-export-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
                                <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="delivery-export-month">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-filter filter-submit-for-delivery-export" data-type="month" data-time-type="month">
                                    <i class="fa fa-download"></i> 按月导出
                                </button>


                                {{--按时间段导出--}}
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="delivery-export-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />
                                <input type="text" class="search-filter form-filter filter-keyup date_picker-c" name="delivery-export-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />

                                <button type="button" class="btn btn-success btn-filter filter-submit-for-delivery-export" data-type="period" data-time-type="period">
                                    <i class="fa fa-download"></i> 按时间段导出
                                </button>


                                <button type="button" class="btn btn-default btn-filter filter-empty-for-export">
                                    <i class="fa fa-remove"></i> 清空重选
                                </button>


                                <div class="month-picker-box clear-both">
                                </div>


                                <div class="month-picker-box clear-both">
                                </div>


                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif


    {{--去重--}}
    @if(in_array($me->user_type,[0,1,9,11]))
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="box box-danger box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title comprehensive-month-title">去重•导出</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <ul class="nav nav-stacked">
                        <li class="">
                            <div class="col-md-12 datatable-search-row filter-box">
                                <div class="pull-right">


                                    <input type="hidden" name="duplicate-export-time-type" class="time-type" value="all" readonly>


                                    <select class="search-filter form-filter filter-lg select2-box-c" name="duplicate-export-item-category">
                                        <option value="">选择工单类型</option>
                                        <option value="1">口腔</option>
                                        <option value="11">医美</option>
                                        <option value="31">二奢</option>
                                    </select>

                                    <select class="search-filter form-filter filter-lg select2-reset select2-project-c" name="duplicate-export-project">
                                        <option value="-1">选择项目</option>
                                    </select>

{{--                                    <select class="search-filter form-filter filter-lg select2-reset select2-client-c" name="duplicate-export-client">--}}
{{--                                        <option value="-1">选择客户</option>--}}
{{--                                    </select>--}}

{{--                                    <select class="search-filter form-filter filter-lg select2-reset select2-box-c" name="duplicate-export-city">--}}
{{--                                        <option value="-1">选择城市</option>--}}
{{--                                        @if(!empty($district_city_list) && count($district_city_list) > 0)--}}
{{--                                            @foreach($district_city_list as $v)--}}
{{--                                                <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}


                                    <select class="search-filter form-filter filter-xl select2-reset select2-district-c _none" name="duplicate-export-district[]" multiple="multiple">
                                        <option value="-1">全部区域</option>
                                    </select>


{{--                                    --}}{{--按天导出--}}
{{--                                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre _none" data-target="duplicate-export-date">--}}
{{--                                        <i class="fa fa-chevron-left"></i>--}}
{{--                                    </button>--}}
{{--                                    <input type="text" class="search-filter form-filter filter-keyup date_picker-c _none" name="duplicate-export-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />--}}
{{--                                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next _none" data-target="duplicate-export-date">--}}
{{--                                        <i class="fa fa-chevron-right"></i>--}}
{{--                                    </button>--}}
{{--                                    <button type="button" class="btn btn-danger btn-filter filter-submit-for-duplicate-export _none" data-time-type="date" data-time-type="date">--}}
{{--                                        <i class="fa fa-download"></i> 按日导出--}}
{{--                                    </button>--}}


{{--                                    --}}{{--按月导出--}}
{{--                                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre _none" data-target="duplicate-export-month">--}}
{{--                                        <i class="fa fa-chevron-left"></i>--}}
{{--                                    </button>--}}
{{--                                    <input type="text" class="search-filter form-filter filter-keyup month-picker month_picker-c _none" name="duplicate-export-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />--}}
{{--                                    <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next _none" data-target="duplicate-export-month">--}}
{{--                                        <i class="fa fa-chevron-right"></i>--}}
{{--                                    </button>--}}
{{--                                    <button type="button" class="btn btn-danger btn-filter filter-submit-for-delivery-export _none" data-time-type="month" data-time-type="month">--}}
{{--                                        <i class="fa fa-download"></i> 按月导出--}}
{{--                                    </button>--}}


{{--                                    --}}{{--按时间段导出--}}
{{--                                    <input type="text" class="search-filter form-filter filter-keyup date_picker-c _none" name="duplicate-export-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />--}}
{{--                                    <input type="text" class="search-filter form-filter filter-keyup date_picker-c _none" name="duplicate-export-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="width:120px;text-align:center;" />--}}

{{--                                    <button type="button" class="btn btn-danger btn-filter filter-submit-for-duplicate-export _none" data-time-type="period" data-time-type="period">--}}
{{--                                        <i class="fa fa-download"></i> 按时间段导出--}}
{{--                                    </button>--}}


                                    <button type="button" class="btn btn-danger btn-filter filter-submit-for-duplicate-export" data-time-type="all" data-time-type="period">
                                        <i class="fa fa-download"></i> 全部导出
                                    </button>

                                    <button type="button" class="btn btn-default btn-filter filter-empty-for-export">
                                        <i class="fa fa-remove"></i> 清空重选
                                    </button>


                                    <div class="month-picker-box clear-both">
                                    </div>


                                    <div class="month-picker-box clear-both">
                                    </div>


                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif


    <div class="col-md-12 datatable-search-row datatable-search-box">
        <div class="pull-right">

            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="record-id" placeholder="ID" />
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="record-name" placeholder="标题" />
            <select class="search-filter form-filter filter-md select2-box-c" name="record-operate-type">
                <option value="-1">导出方式</option>
                <option value="1">自定义时间导出</option>
                <option value="11">按月导出</option>
                <option value="31">按日导出</option>
                <option value="99">最新导出</option>
                <option value="100">ID导出</option>
            </select>

            <select class="search-filter form-filter filter-lg select2-box-c select2-staff-c" name="record-staff">
                <option value="-1">选择员工</option>
                @foreach($staff_list as $v)
                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                @endforeach
            </select>

            <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit-for-record">
                <i class="fa fa-search"></i> 搜索
            </button>
            <button type="button" class="btn btn-default btn-filter filter-cancel" id="filter-cancel-for-record">
                <i class="fa fa-circle-o-notch"></i> 重置
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