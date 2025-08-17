<div class="row datatable-body datatable-wrapper order-aesthetic-list-clone" data-datatable-item-category="aesthetic" data-item-name="医美">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-left">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />

            {{--交付日期--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-delivered_date" placeholder="交付日期" value="" readonly="readonly" />

            {{--创建方式--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-created-type">
                    <option value="-1">创建方式</option>
                    <option value="99">API</option>
                    <option value="1">人工</option>
                    <option value="9">导入</option>
                </select>
            @endif

            {{--选择团队--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                <select class="search-filter form-filter filter-xl select2-box-c" name="order-department-district[]" id="order-department-district" multiple="multiple">
                    <option value="-1">选择团队</option>
                    @foreach($department_district_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            @endif

            {{--选择员工--}}
            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                <select class="search-filter form-filter filter-lg select2-box-c select2-staff-c" name="order-staff">
                    <option value="-1">选择员工</option>
                    @foreach($staff_list as $v)
                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                    @endforeach
                </select>
            @endif

            {{--选择客户--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <select class="search-filter form-filter filter-lg select2-box-c- select2-client-c" data-user-category="11" name="order-client">
                    <option value="-1">选择客户</option>
                    @foreach($client_list as $v)
                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                    @endforeach
                </select>
            @endif

            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="11" name="order-project">
                <option value="-1">选择项目</option>
                @foreach($project_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>

            {{--审核状态--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="order-inspected-status">
                <option value="-1">审核状态</option>
                @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
                    <option value="待发布">待发布</option>
                @endif
                <option value="待审核">待审核</option>
                <option value="已审核">已审核</option>
            </select>

            {{--审核结果--}}
            <select class="search-filter form-filter filter-xl select2-box-c" name="order-inspected-result[]" multiple="multiple">
                <option value="-1">审核结果</option>
                @foreach(config('info.inspected_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>
            {{--录音质量--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-recording-quality">
                    <option value="-1">录音质量</option>
                    <option value="0">合格</option>
                    <option value="1">优秀</option>
                    <option value="9">问题</option>
                </select>
            @endif

            {{--交付状态--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="order-delivered-status">
                <option value="-1">交付状态</option>
                <option value="待交付">待交付</option>
                {{--<option value="已交付" @if("已交付" == $delivered_status) selected="selected" @endif>已交付</option>--}}
                <option value="已操作">已操作</option>
            </select>

            {{--交付结果--}}
            <select class="search-filter form-filter filter-xl select2-box-c" name="order-delivered-result[]" multiple="multiple">
                <option value="-1">交付结果</option>
                @foreach(config('info.delivered_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>

            {{--城市--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2-district-city" name="order-city" id="order-city" data-target="#order-district">
                <option value="-1">选择城市</option>
                @if(!empty($district_city_list) && count($district_city_list) > 0)
                    @foreach($district_city_list as $v)
                        <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                    @endforeach
                @endif
            </select>

            {{--行政区--}}
            <select class="search-filter form-filter filter-xxl select2-box-c select2-district-district" name="order-district[]" id="order-district" data-target="order-city" multiple="multiple">
                <option value="-1">选择区域</option>
                @if(!empty($district_district_list) && count($district_district_list) > 0)
                    @foreach($district_district_list as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                @endif
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


            <button type="button" onclick="" class="btn btn-filter btn-success  pull-right item-create-modal-show"
                    data-form-id="form-for-order-aesthetic-edit"
                    data-modal-id="modal-for-order-aesthetic-edit"
                    data-title="添加【医美】工单"
            >
                <i class="fa fa-plus"></i> 添加
            </button>

        </div>


    </div>


    <div class="col-md-12 datatable-body">
        <div class="tableArea table-order">
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


    @if(in_array($me->department_district_id,[0]))
    @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
    <div class="col-md-12 datatable-search-row">

        <div class=" pull-left">

            {{--<button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}


            <button class="btn btn-default btn-filter">
                <input type="checkbox" class="check-review-all">
            </button>


            <button type="button" onclick="" class="btn btn-default btn-filter bulk-submit-for-order-export" id="" data-item-category="11">
                <i class="fa fa-download"></i> 批量导出
            </button>
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


            @if(in_array($me->department_district_id,[0]))
                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))

                    {{--交付项目--}}
                    <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="11" name="bulk-operate-delivered-project">
                        <option value="-1">选择交付项目</option>
                        {{--@foreach($project_list as $v)--}}
                        {{--<option value="{{ $v->id }}">{{ $v->name }}</option>--}}
                        {{--@endforeach--}}
                    </select>

                    {{--交付客户--}}
                    <select class="search-filter form-filter filter-lg select2-box-c- select2-client-c" data-user-category="11" name="bulk-operate-delivered-client">
                        <option value="-1">交付客户</option>
                        @foreach($client_list as $v)
                            <option value="{{ $v->id }}">{{ $v->username }}</option>
                        @endforeach
                    </select>

                    {{--交付结果--}}
                    <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-delivered-result">
                        <option value="-1">选择交付结果</option>
                        @foreach(config('info.delivered_result') as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>

                    {{--交付说明--}}
                    <input type="text" class="search-filter filter-lg form-filter" name="bulk-operate-delivered-description" placeholder="交付说明">


                    <button type="button" class="btn btn-default btn-filter bulk-submit-for-order-delivered" id="">
                        <i class="fa fa-share"></i> 批量交付
                    </button>

                @endif
            @endif

        </div>

    </div>
    @endif
    @endif


</div>