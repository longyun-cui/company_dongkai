<div class="row datatable-body datatable-wrapper statistic-caller-rank-clone" data-datatable-item-category="statistic-caller-rank">


    <div class="col-md-12 datatable-search-row datatable-search-box select2-wrapper">


        <div class="pull-right">


            <input type="hidden" name="statistic-caller-rank-time-type" class="time-type" value="" readonly>

            @if($me->user_type == 1)
                <select class="form-control form-filter" name="statistic-caller-rank-object-type" style="width:88px;">
                    <option value="staff">员工</option>
                    <option value="department">部门</option>
                </select>
            @endif

            @if(in_array($me->staff_category,[0,1,9,61]))
                <select class="search-filter form-filter filter-xl select2-box-c" name="statistic-caller-rank-staff-type" style="width:88px;">
                    <option value="99">客服</option>
                    @if(in_array($me->user_type,[0,1,9,61]))
                        <option value="61">小组</option>
                    @endif
                    @if(in_array($me->user_type,[0,1,9,11]))
                        <option value="41">团队</option>
                    @endif
                </select>
            @endif

            @if(in_array($me->staff_category,[0,1,9,61]))
                <select class="search-filter form-filter filter-xl select2-department-team select2-box-change"
                        name="statistic-caller-rank-team"
                        data-target=".select2-department-group"
                >
                    <option value="-1">选择团队</option>
                    @if(!empty($team_list))
                        @foreach($team_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    @endif
                </select>
            @endif

{{--            @if(in_array($me->user_type,[0,1,9,11,61,81]))--}}
{{--                <select class="search-filter form-filter filter-xl select2-department-group select2-box-c- select2-district-c-"--}}
{{--                        name="statistic-caller-rank-group"--}}
{{--                        data-target=".select2-department-team"--}}
{{--                >--}}
{{--                    <option data-id="-1" value="-1">选择小组</option>--}}
{{--                </select>--}}
{{--            @endif--}}

            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="statistic-caller-rank-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup dateh-picker-c" name="statistic-caller-rank-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="statistic-caller-rank-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="date">
                <i class="fa fa-search"></i> 按日查询
            </button>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="statistic-caller-rank-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month-picker-c" name="statistic-caller-rank-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="statistic-caller-rank-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段导出--}}
            <input type="text" class="search-filter filter-keyup dateh-picker-c" name="statistic-caller-rank-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter filter-keyup dateh-picker-c" name="statistic-caller-rank-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

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
        <div class="box box-primary box-solid-" style="box-shadow:0 0;">

            <div class="box-header with-border- margin-top-16px padding-top-8px _none">
                <h3 class="box-title datatable-title"></h3>
            </div>

            <div class="box-body no-padding">
                <div class="tableArea full margin-top-8px">
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