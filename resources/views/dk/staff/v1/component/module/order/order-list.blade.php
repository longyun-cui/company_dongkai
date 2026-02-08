<div class="row datatable-body datatable-wrapper order-list-clone" data-datatable-item-category="order" data-item-name="工单">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="col-md-2- pull-left">
            <button type="button" onclick="" class="btn btn-filter modal-show--for--order-item-create"
                    data-form-id="form--for--order-item-edit"
                    data-modal-id="modal--for--order-item-edit"
                    data-title="添加工单"
            >
                <i class="fa fa-plus"></i> 添加
            </button>
        </div>


        <div class="pull-right _none-">


            <div class="nav navbar-nav">

                <div class="dropdown filter-menu" data-bs-auto-close="outside">
                    <button type="button" class="btn btn-default btn-filter dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-th-list"></i> 显示列
                    </button>

                    <div class="dropdown-menu non-auto-hide box box-success" style="top:32px;right:4px;">

                        <div class="box-header with-border- _none">
                            筛选
                        </div>



                        {{--发布日期--}}
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-md-3">
                                    <input type="checkbox" class="minimal" checked disabled>
                                </label>
                                <label class="col-md-9 text-align-left">
                                    Minimal skin checkbox
                                </label>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-md-3">
                                    <input type="checkbox" class="minimal" checked>
                                </label>
                                <label class="col-md-9 text-align-left">
                                    Minimal skin checkbox
                                </label>
                            </div>
                        </div>
                        {{--ID--}}
                        <div class="box-body">
                            <label class="col-md-3">ID</label>
                            <div class="col-md-9 filter-body">
                                <input type="text" class="form-control form-filter filter-keyup" name="order-id" placeholder="ID" value="" />
                            </div>
                        </div>

                        {{--发布日期--}}
                        <div class="box-body">
                            <label class="col-md-3">派车日期</label>
                            <div class="col-md-9 filter-body">
                                <input type="text" class="form-control form-filter filter-keyup date-picker-c" name="order-assign-date" placeholder="指派日期" value="" readonly="readonly" />
                            </div>
                        </div>

                        {{--交付日期--}}
                        <div class="box-body">
                            <label class="col-md-3">任务日期</label>
                            <div class="col-md-9 filter-body">
                                <input type="text" class="form-control form-filter filter-keyup date-picker-c" name="order-task-date" placeholder="任务日期" value="" readonly="readonly" />
                            </div>
                        </div>

                        {{--选择团队--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                        <div class="box-body">
                            <label class="col-md-3">团队</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box" name="order-department-district[]" id="order-department-district" multiple="multiple">
                                    <option value="-1">选择团队</option>
                                </select>
                            </div>
                        </div>
                        @endif

                        {{--选择员工--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                        <div class="box-body">
                            <label class="col-md-3">员工</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box select2--staff" name="order-staff">
                                    <option value="-1">选择员工</option>
                                </select>
                            </div>
                        </div>
                        @endif

                        {{--选择客户--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                        <div class="box-body">
                            <label class="col-md-3">客户</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box select2--client" name="order-client">
                                    <option value="-1">选择客户</option>
                                </select>
                            </div>
                        </div>
                        @endif

                        {{--选择项目--}}
                        <div class="box-body">
                            <label class="col-md-3">项目</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box select2--project" name="order-project">
                                </select>
                            </div>
                        </div>

                        {{--审核状态--}}
                        <div class="box-body">
                            <label class="col-md-3">审核状态</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box" name="order-inspected-status">
                                    <option value="-1">审核状态</option>
                                </select>
                            </div>
                        </div>

                        {{--审核结果--}}
                        <div class="box-body">
                            <label class="col-md-3">审核结果</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box" name="order-inspected-result[]" multiple="multiple">
                                    <option value="-1">审核结果</option>
                                </select>
                            </div>
                        </div>



                        <div class="box-footer" style="text-align: center;">

                            <label class="col-md-3"> </label>
                            <div class="col-md-9 filter-body">
                                <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit-for-order-">
                                    <i class="fa fa-search"></i> 搜 索
                                </button>
                                <button type="button" class="btn bg-default btn-filter filter-empty" id="filter-empty-for-order-">
                                    <i class="fa fa-remove"></i> 重 置
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>


        </div>


        <div class="pull-right _none-">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--派车日期--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-assign_date" placeholder="派车日期" value="" readonly="readonly" />

            {{--任务日期--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup month-picker-c" name="order-task_date" placeholder="任务日期" value="" readonly="readonly" />


            {{--选择团队--}}
{{--            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))--}}
{{--            <select class="search-filter form-filter filter-lg select2-team-c" name="order-team">--}}
{{--                <option value="-1">选择团队</option>--}}
{{--            </select>--}}
{{--            @endif--}}

            {{--选择员工--}}
{{--            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))--}}
{{--            <select class="search-filter form-filter filter-lg select2-staff-c" name="order-staff">--}}
{{--                <option value="-1">选择员工</option>--}}
{{--            </select>--}}
{{--            @endif--}}

            {{--选择客户--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <select class="search-filter form-filter filter-lg select2-client-c" name="order-client" data-staff-category-="1">
                <option value="-1">选择客户</option>
            </select>
            @endif

            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-project-c" name="order-project" data-project-category-="1">
                <option value="-1">选择项目</option>
            </select>


            {{--选择车辆--}}
            <select class="search-filter form-filter filter-lg select2--car" name="order-car" data-car-type="1">
                <option value="-1">选择车辆</option>
            </select>

            {{--选择车挂--}}
            <select class="search-filter form-filter filter-lg select2-car-c" name="order-trailer" data-car-type="21">
                <option value="-1">选择车挂</option>
            </select>




            <button type="button" class="btn btn-default btn-filter filter-submit pull-right-">
                <i class="fa fa-search"></i> 搜索
            </button>

            <button type="button" class="btn btn-default btn-filter filter-empty pull-right-">
                <i class="fa fa-remove"></i> 清空
            </button>

            <button type="button" class="btn btn-default btn-filter filter-refresh pull-right-">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>

            <button type="button" class="btn btn-default btn-filter filter-cancel pull-right-">
                <i class="fa fa-undo"></i> 重置
            </button>

        </div>


    </div>


    <div class="col-md-12 datatable-body">
        <div class="box box-success box-solid-" style="box-shadow:0 0;">

            <div class="box-header with-border- _none" style="margin-top:16px;padding-top:8px;">
                <h3 class="box-title comprehensive-month-title">订单统计</h3>
            </div>
            <div class="box-body no-padding">
                <div class="tableArea full table-order" style="margin-top:8px;">
                    <table class='table table-striped table-bordered table-hover order-column' id="datatable-order-list">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="box-header">
            </div>

        </div>
    </div>


    <div class="col-md-12 datatable-search-row">

        <div class=" pull-left">

            {{--<button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}


            <button class="btn btn-default btn-filter">
                <input type="checkbox" class="check-review-all">
            </button>


            <button type="button" onclick="" class="btn btn-default btn-filter bulk-submit-for-order-export" id="" data-item-category="1">
                <i class="fa fa-download"></i> 批量导出
            </button>
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))

                {{--交付项目--}}
                <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="1" name="bulk-operate-delivered-project">
                    <option value="-1">选择交付项目</option>
                    {{--@foreach($project_list as $v)--}}
                    {{--<option value="{{ $v->id }}">{{ $v->name }}</option>--}}
                    {{--@endforeach--}}
                </select>


                {{--交付说明--}}
                <input type="text" class="search-filter filter-lg form-filter" name="bulk-operate-delivered-description" placeholder="交付说明">


                <button type="button" class="btn btn-default btn-filter" id="bulk-submit-for-delivered">
                    <i class="fa fa-share"></i> 批量交付
                </button>

            @endif

        </div>

    </div>

</div>