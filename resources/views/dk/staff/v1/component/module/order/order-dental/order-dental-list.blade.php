<div class="row datatable-body datatable-wrapper order-list-clone" data-datatable-item-category="order" data-item-name="工单">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-left">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-start" placeholder="开始日期" value="" readonly="readonly" />
            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-ended" placeholder="结束日期" value="" readonly="readonly" />

            {{--创建方式--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-created-type">
                    <option value="-1">创建方式</option>
                    <option value="1">人工</option>
                    <option value="9">导入</option>
                    <option value="91">百应AI</option>
                    <option value="99">API</option>
                </select>
            @endif

            {{--选择团队--}}
            @if(in_array($me->staff_category,[0,1,9,51,71]))
                <select class="search-filter form-filter filter-xl select2--team-c"
                        name="order-team-list[]"
                        data-team-category="41"
                        data-team-type="11"
                        id="order-teams" multiple="multiple"
                >
                    <option value="-1">选择团队</option>
                    @if(!empty($team_list) && count($team_list) > 0)
                        @foreach($team_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    @endif
                </select>
            @endif

            {{--选择员工--}}
{{--            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))--}}
{{--                <select class="search-filter form-filter filter-lg select2-box-c select2-staff-c" name="order-staff">--}}
{{--                    <option value="-1">选择员工</option>--}}
{{--                    @if(!empty($staff_list) && count($staff_list) > 0)--}}
{{--                        @foreach($staff_list as $v)--}}
{{--                            <option value="{{ $v->id }}">{{ $v->username }}</option>--}}
{{--                        @endforeach--}}
{{--                    @endif--}}
{{--                </select>--}}
{{--            @endif--}}

            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2--project-c-" data-item-category="1" name="order-project">
                <option value="-1">选择项目</option>
                @if(!empty($project_list) && count($project_list) > 0)
                    @foreach($project_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>

            {{--选择交付项目--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <select class="search-filter form-filter filter-lg select2-box-c select2--project-c-" data-client-category="1" name="order-delivered-project">
                <option value="-1">选择交付项目</option>
                @if(!empty($project_list) && count($project_list) > 0)
                    @foreach($project_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>
            @endif

            {{--选择交付客户--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <select class="search-filter form-filter filter-lg select2--client-c" data-client-category="1" name="order-delivered-client">
                <option value="-1">选择交付项目</option>
                @if(!empty($client_list) && count($client_list) > 0)
                    @foreach($client_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>
            @endif


            {{--可分发--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-distribute-type">
                    <option value="">常规筛选</option>
                    <option value="1">可分发</option>
                </select>
            @endif


            {{--客户类型--}}
            <select class="search-filter form-filter filter-md select2-box-c" name="order-client-type">
                <option value="-1">客户类型</option>
                @foreach(config('dk.common-config.dental_type') as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>


            {{--录音质量--}}
            @if(in_array($me->staff_category,[0,1,9,51,61,71]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-recording-quality">
                    <option value="-1">录音质量</option>
                    <option value="0">合格</option>
                    <option value="1">优秀</option>
                    <option value="9">问题</option>
                </select>
            @endif


            {{--审核状态--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="order-inspected-status">
                <option value="-1">审核状态</option>
                @if(in_array($me->staff_category,[0,1,9,41]))
                    <option value="待发布">待发布</option>
                @endif
                <option value="待审核">待审核</option>
                <option value="已审核">已审核</option>
            </select>

            {{--审核结果--}}
            <select class="search-filter form-filter filter-xl select2-box-c" name="order-inspected-result[]" multiple="multiple">
                <option value="-1">审核结果</option>
                @if($me->department_district_id <= 0)
                    @foreach(config('dk.common-config.inspected_result') as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                @else
                    @foreach(config('dk.common-config.inspected_result_for_team') as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                @endif
            </select>


            {{--申诉状态--}}
            @if(in_array($me->staff_category,[0,1,9,61]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-appealed-status">
                    <option value="">申诉状态</option>
                    @foreach(config('dk.common-config.appealed_status') as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
            @endif


            {{--交付日期--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-delivered_date" placeholder="交付日期" value="" readonly="readonly" />
            @endif
            {{--交付状态--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <select class="search-filter form-filter filter-lg select2-box-c" name="order-delivered-status">
                <option value="-1">交付状态</option>
                <option value="0">未操作</option>
                <option value="1">已交付</option>
                <option value="9">待交付</option>
                <option value="91">不交付</option>
                <option value="99">交付失败</option>
                <option value="101">交付撤回</option>
            </select>
            @endif
            {{--交付结果--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <select class="search-filter form-filter filter-xl select2-box-c" name="order-delivered-result[]" multiple="multiple">
                <option value="-1">交付结果</option>
                @foreach(config('dk.common-config.delivered_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>
            @endif


            {{--城市--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2-district-city" name="order-city" id="order-city" data-target="#order-district">
                <option value="-1">选择城市</option>
                @if(!empty($location_city_list) && count($location_city_list) > 0)
                    @foreach($location_city_list as $v)
                        <option value="{{ $v->location_city }}">{{ $v->location_city }}</option>
                    @endforeach
                @endif
            </select>

            {{--行政区--}}
            <select class="search-filter form-filter filter-xxl select2-box-c select2--location-c" name="order-district[]" id="order-district" data-target="order-city" multiple="multiple">
                <option value="">选择区域</option>
            </select>




            <button type="button" class="btn btn-default btn-filter filter-submit">
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


            @if(in_array($me->staff_category,[0,1,9]))
            <button type="button" onclick="" class="btn btn-filter btn-success modal-show--for--order--item-create"
                    data-form-id="form--for--order-dental--item-edit"
                    data-modal-id="modal--for--order-dental--item-edit"
                    data-title="添加工单"
            >
                <i class="fa fa-plus"></i> 添加
            </button>
            @endif

        </div>


    </div>


    <div class="col-md-12 datatable-body">
        <div class="box box-primary box-solid- margin-bottom-4px" style="box-shadow:0 0;">

            <div class="box-header with-border- margin-top-16px padding-top-8px _none">
                <h3 class="box-title datatable-title"></h3>
            </div>

            <div class="box-body no-padding">
                <div class="tableArea table-order- full- margin-top-8px">
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


    @if(in_array($me->staff_category,[0,1,9,71]))
    <div class="col-md-12 datatable-search-row">

        <div class=" pull-left">

            {{--<button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}


            <button class="btn btn-default btn-filter">
                <input type="checkbox" class="check-review-all">
            </button>


{{--            <button type="button" onclick="" class="btn btn-default btn-filter bulk-submit-for-order-export" data-item-category="1">--}}
{{--                <i class="fa fa-download"></i> 批量导出--}}
{{--            </button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}



{{--            --}}{{--交付项目--}}
{{--            <select class="search-filter form-filter filter-lg select2--project-c"--}}
{{--                    name="bulk-operate-delivered-project"--}}
{{--                    data-project-category="1"--}}
{{--            >--}}
{{--                <option value="-1">选择交付项目</option>--}}
{{--                --}}{{--@foreach($project_list as $v)--}}
{{--                --}}{{--<option value="{{ $v->id }}">{{ $v->name }}</option>--}}
{{--                --}}{{--@endforeach--}}
{{--            </select>--}}

{{--            --}}{{--交付客户--}}
{{--            <select class="search-filter form-filter filter-lg select2--client-c"--}}
{{--                    name="bulk-operate-delivered-client"--}}
{{--                    data-client-category="1"--}}
{{--            >--}}
{{--                <option value="-1">选择交付客户</option>--}}
{{--                @if(!empty($client_list) && count($client_list) > 0)--}}
{{--                    @foreach($client_list as $v)--}}
{{--                        <option value="{{ $v->id }}">{{ $v->username }}</option>--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            </select>--}}

{{--            --}}{{--交付结果--}}
{{--            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-delivered-result">--}}
{{--                <option value="-1">选择交付结果</option>--}}
{{--                @foreach(config('info.delivered_result') as $v)--}}
{{--                    <option value="{{ $v }}">{{ $v }}</option>--}}
{{--                @endforeach--}}
{{--            </select>--}}

{{--            --}}{{--交付说明--}}
{{--            <input type="text" class="search-filter filter-lg form-filter" name="bulk-operate-delivered-description" placeholder="交付说明">--}}


{{--            <button type="button" class="btn btn-default btn-filter" id="bulk-submit-for-delivered">--}}
{{--                <i class="fa fa-share"></i> 批量交付--}}
{{--            </button>--}}


            <button type="button" onclick="" class="btn btn-default btn-filter order--bulk-export-summit" data-order-category="1">
                <i class="fa fa-download"></i> 批量导出
            </button>

            <button type="button" class="btn btn-default btn-filter order--bulk-delivering-summit--by-fool">
                <i class="fa fa-share"></i> 批量一键交付
            </button>


        </div>

    </div>
    @endif


</div>