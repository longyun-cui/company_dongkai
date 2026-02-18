<div class="row datatable-body datatable-wrapper statistic-caller-recent-clone" data-datatable-item-category="statistic-caller-recent">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-caller-recent-time-type" class="time-type" value="" readonly>

            @if($me->user_type == 1)
                <select class="form-control form-filter" name="statistic-caller-recent-object-type" style="width:88px;">
                    <option value="staff">员工</option>
                    <option value="department">部门</option>
                </select>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,81]))
                <select class="search-filter form-filter filter-xl select2-box-c" name="statistic-caller-recent-staff-type" style="width:88px;">
                    <option value="88">客服</option>
                    @if(in_array($me->user_type,[0,1,9,11,81]))
                        <option value="84">主管</option>
                    @endif
                    @if(in_array($me->user_type,[0,1,9,11]))
                        <option value="81">经理</option>
                    @endif
                </select>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]))
                <select class="search-filter form-filter filter-xl select2-box-c" name="statistic-caller-recent-department-district">
                    <option value="-1">选择大区</option>
                    @if(!empty($department_district_list))
                        @foreach($department_district_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    @endif
                </select>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,81]))
{{--                <select class="search-filter form-filter filter-xl select2-box-c" name="statistic-caller-recent-department-group">--}}
{{--                    <option data-id="-1" value="-1">选择小组</option>--}}
{{--                    @if(!empty($department_group_list))--}}
{{--                        @foreach($department_group_list as $v)--}}
{{--                            <option value="{{ $v->id }}">{{ $v->name }}</option>--}}
{{--                        @endforeach--}}
{{--                    @endif--}}
{{--                </select>--}}
            @endif


            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 查询
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