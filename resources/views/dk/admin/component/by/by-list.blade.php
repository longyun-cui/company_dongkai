<div class="row datatable-body datatable-wrapper by-list-clone" data-datatable-item-category="ai" data-item-name="百应">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="by-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="by-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="by-assign" placeholder="发布日期" value="" readonly="readonly" />--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="by-start" placeholder="开始日期" value="" readonly="readonly" />
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="by-ended" placeholder="结束日期" value="" readonly="readonly" />


            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="1" name="by-project">
                <option value="-1">选择项目</option>
                @foreach($project_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>


            {{--审核状态--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="by-inspected-status">
                <option value="-1">审核状态</option>
                <option value="0">待审核</option>
                <option value="1">已审核</option>
            </select>

            {{--审核结果--}}
            <select class="search-filter form-filter filter-xl select2-box-c" name="by-inspected-result[]" multiple="multiple">
                <option value="-1">审核结果</option>
                @foreach(config('info.inspected_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>


            {{--城市--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2-district-city" name="by-city" id="by-city" data-target="#by-district">
                <option value="-1">选择城市</option>
                @if(!empty($district_city_list) && count($district_city_list) > 0)
                    @foreach($district_city_list as $v)
                        <option value="{{ $v->district_city }}">{{ $v->district_city }}</option>
                    @endforeach
                @endif
            </select>

            {{--行政区--}}
            <select class="search-filter form-filter filter-xxl select2-box-c select2-district-district" name="by-district[]" id="by-district" data-target="by-city" multiple="multiple">
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

        </div>


    </div>


    <div class="col-md-12 datatable-body">
        <div class="tableArea table-by">
            <table class='table table-striped table-bordered table-hover by-column'>
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


            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


            @if(in_array($me->department_district_id,[0]))
                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))

                    {{--交付项目--}}
                    <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="1" name="bulk-operate-delivered-project">
                        <option value="-1">选择指派项目</option>
                        {{--@foreach($project_list as $v)--}}
                        {{--<option value="{{ $v->id }}">{{ $v->name }}</option>--}}
                        {{--@endforeach--}}
                    </select>


                    {{--交付结果--}}
                    <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-inspected-result">
                        <option value="-1">选择审核结果</option>
                        @foreach(config('info.inspected_result') as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>

                    {{--交付说明--}}
                    <input type="text" class="search-filter filter-lg form-filter" name="bulk-operate-inspected-description" placeholder="审核说明">


                    <button type="button" class="btn btn-default btn-filter" id="bulk-submit-for-inspected">
                        <i class="fa fa-share"></i> 批量审核
                    </button>

                @endif
            @endif

        </div>

    </div>
    @endif
    @endif


</div>