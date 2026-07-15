<div class="row datatable-body datatable-wrapper order-dental-distribute-list-clone"
     data-order-category="1"
     data-datatable-item-category="dental"
     data-item-name="口腔工单"
>


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-left">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-smd filter-keyup" name="order-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />--}}
            <input type="text" class="search-filter form-filter filter-smd filter-keyup date-picker-c" name="order-start" placeholder="开始日期" value="" readonly="readonly" />
            <input type="text" class="search-filter form-filter filter-smd filter-keyup date-picker-c" name="order-ended" placeholder="结束日期" value="" readonly="readonly" />



            {{--选择交付项目--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
            <select class="search-filter form-filter filter-md select2-box-c select2--project-c-"
                    name="order-delivered-project"
                    data-client-category="1"
            >
                <option value="-1">选择交付项目</option>
                @if(!empty($project_list__for__dental) && count($project_list__for__dental) > 0)
                    @foreach($project_list__for__dental as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>
            @endif



            {{--可分发--}}
            @if(in_array($me->staff_category,[0,1,9,71]))
                <select class="search-filter form-filter filter-smd select2-box-c" name="order-distribute-type">
                    <option value="1">可分发</option>
                </select>
            @endif


            {{--城市--}}
            <select class="search-filter form-filter filter-smd select2-box-c select--location-city-c"
                    name="order-city"
            >
                <option value="-1">选择城市</option>
                @if(!empty($location_city_list) && count($location_city_list) > 0)
                    @foreach($location_city_list as $v)
                        <option value="{{ $v->location_city }}">{{ $v->location_city }}</option>
                    @endforeach
                @endif
            </select>

            {{--行政区--}}
            <select class="search-filter form-filter filter-xxl select2-box-c- select2--location-district-c"
                    name="order-district[]"
                    data-item-category="11"
                    multiple="multiple"
            >
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


            <button type="button" onclick="" class="btn btn-default btn-filter order--bulk-export-summit" data-order-category="1">
                <i class="fa fa-download"></i> 批量·导出
            </button>


            @if(in_array($me->staff_position,[0,1,9,31]))
            <button type="button" onclick="" class="btn btn-default btn-filter order--bulk-ai-convert-summit" data-order-category="1">
                <i class="fa fa-download"></i> 批量·AI转文字
            </button>
            @endif


            @if(in_array($me->staff_position,[0,1,9,31]))
            <button type="button" onclick="" class="btn btn-default btn-filter order--bulk-ai-inspect-summit" data-order-category="1">
                <i class="fa fa-download"></i> 批量·AI质检
            </button>
            @endif


            <button type="button" class="btn btn-default btn-filter order--bulk-delivering-summit--by-fool">
                <i class="fa fa-share"></i> 批量·一键交付
            </button>


            {{--交付项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2--project-c-"
                    name="order--bulk-export--delivered-project"
                    data-project-category="1"
            >
                <option value="0">选择交付项目</option>
                @if(!empty($project_list__for__dental) && count($project_list__for__dental) > 0)
                    @foreach($project_list__for__dental as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>

            {{--交付客户--}}
            <select class="search-filter form-filter filter-lg select2-box-c select2--client-c-"
                    name="order--bulk-export--delivered-client"
                    data-client-category="1"
            >
                <option value="0">选择交付客户</option>
                @if(!empty($client_list__for__dental) && count($client_list__for__dental) > 0)
                    @foreach($client_list__for__dental as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>

            {{--交付结果--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="order--bulk-export--delivered-result">
                <option value="-1">选择交付结果</option>
                @foreach(config('dk.common-config.delivered_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>

            {{--交付说明--}}
            <input type="text" class="search-filter filter-lg form-filter" name="order--bulk-export--delivered-description" placeholder="交付说明">


            <button type="button" class="btn btn-default btn-filter order--bulk-delivering--summit">
                <i class="fa fa-share"></i> 批量·交付
            </button>

            <button type="button" class="btn btn-default btn-filter order--bulk-distributing--summit">
                <i class="fa fa-share"></i> 批量·分发
            </button>


        </div>

    </div>
    @endif


</div>