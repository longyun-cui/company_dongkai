<div class="row datatable-body datatable-wrapper order-list-clone" data-datatable-item-category="order" data-item-name="工单">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class=" pull-left">

            @if(in_array($me->user_type,[0,1,9,11,19]))
                <button type="button" onclick="" class="btn btn-filter btn-success modal-show--for--order--item-create"
                        data-form-id="form--for--order-dental--item-edit"
                        data-modal-id="modal--for--order-dental--item-edit"
                        data-title="添加工单"
                >
                    <i class="fa fa-plus"></i> 添加
                </button>
            @endif
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-play"></i> 启用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-stop"></i> 禁用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-download"></i> 导出</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-trash-o"></i> 批量删除</button>

        </div>


        <div class="pull-right">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-start" placeholder="开始日期" value="" readonly="readonly" />--}}
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="order-ended" placeholder="结束日期" value="" readonly="readonly" />--}}




            {{--选择项目--}}
            <select class="search-filter form-filter filter-lg select2-box-c- select2-project-c" data-item-category="1" name="order-project">
                <option value="-1">选择项目</option>
                @if(!empty($project_list) && count($project_list) > 0)
                    @foreach($project_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>

            {{--客户类型--}}
            <select class="search-filter form-filter filter-md select2-box-c _none" name="order-client-type">
                <option value="-1">客户类型</option>
                @foreach(config('info.client_type') as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>


            {{--审核状态--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="order-inspected-status">
                <option value="-1">审核状态</option>
                <option value="待发布">待发布</option>
                <option value="待审核">待审核</option>
                <option value="已审核">已审核</option>
            </select>

            {{--审核结果--}}
            <select class="search-filter form-filter filter-xl select2-box-c" name="order-inspected-result[]" multiple="multiple">
                <option value="-1">审核结果</option>
                @foreach(config('info.inspected_result_for_team') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>
            {{--申诉状态--}}
            @if(in_array($me->user_type,[0,1,9,11,91]))
                <select class="search-filter form-filter filter-md select2-box-c" name="order-appealed-status">
                    <option value="">申诉状态</option>
                    @foreach(config('info.appealed_status') as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
            @endif




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
        <div class="box box-primary box-solid-" style="box-shadow:0 0;">

            <div class="box-header with-border- margin-top-16px padding-top-8px _none">
                <h3 class="box-title datatable-title"></h3>
            </div>

            <div class="box-body no-padding">
                <div class="tableArea margin-top-8px">
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

            <div class="box-header">
            </div>

        </div>
    </div>


{{--    <div class="col-md-12 datatable-body">--}}
{{--        <div class="tableArea table-order">--}}
{{--            <table class='table table-striped table-bordered table-hover order-column'>--}}
{{--                <thead>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                </tbody>--}}
{{--                <tfoot>--}}
{{--                </tfoot>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--    </div>--}}


</div>