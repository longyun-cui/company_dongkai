<div class="row datatable-body datatable-wrapper delivery-list-clone">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-id" placeholder="ID" value="" style="width:88px;" />
            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-order-id" placeholder="工单ID" value="" style="width:88px;" />

            {{--<input type="text" class="form-control form-filter filter-keyup" name="order-client-name" placeholder="客户姓名" value="{{ $client_name or '' }}" style="width:88px;" />--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="客户电话" value="" />


            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-pre-for-order" style="width:24px;">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker" name="order-assign" placeholder="交付日期" value="" readonly="readonly" />
            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-next-for-order" style="width:24px;">
                <i class="fa fa-chevron-right"></i>
            </button>


            <select class="search-filter form-filter select-select2 select2-box delivery-project" name="delivery-project" style="width:160px;">
                <option value="-1">选择项目</option>
                @if(!empty($project_list))
                    @foreach($project_list as $v)
                        @if(!empty($project_id))
                            @if($v->id == $project_id)
                                <option value="{{ $v->id }}" selected="selected">{{ $v->name }}</option>
                            @else
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endif
                        @else
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endif
                    @endforeach
                @endif
            </select>


            <select class="search-filter form-filter" name="order-delivery-type" style="width:88px;">
                <option value="-1">交付类型</option>
                <option value="95">交付</option>
                <option value="96">分发</option>
            </select>


            {{--                        <select class="form-control form-filter" name="order-delivered-status" style="width:88px;">--}}
            {{--                            <option value="-1">交付状态</option>--}}
            {{--                            <option value="待交付" @if("待审核" == $delivered_status) selected="selected" @endif>待交付</option>--}}
            {{--                            <option value="已交付" @if("已审核" == $delivered_status) selected="selected" @endif>已交付</option>--}}
            {{--                        </select>--}}


            {{--                        <select class="form-control form-filter" name="order-is-wx" style="width:88px;">--}}
            {{--                            <option value="-1">是否+V</option>--}}
            {{--                            <option value="1" @if($is_wx == "1") selected="selected" @endif>是</option>--}}
            {{--                            <option value="0" @if($is_wx == "0") selected="selected" @endif>否</option>--}}
            {{--                        </select>--}}

            {{--                        <select class="form-control form-filter" name="order-is-repeat" style="width:88px;">--}}
            {{--                            <option value="-1">是否重复</option>--}}
            {{--                            <option value="1" @if($is_repeat >= 1) selected="selected" @endif>是</option>--}}
            {{--                            <option value="0" @if($is_repeat == 0) selected="selected" @endif>否</option>--}}
            {{--                        </select>--}}

            {{--                        <input type="text" class="form-control form-filter filter-keyup" name="order-description" placeholder="通话小结" value="" style="width:120px;" />--}}

            <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit-for-order">
                <i class="fa fa-search"></i> 搜索
            </button>
            <button type="button" class="btn btn-default btn-filter filter-empty" id="filter-empty-for-order">
                <i class="fa fa-remove"></i> 清空
            </button>
            <button type="button" class="btn btn-default btn-filter filter-refresh" id="filter-refresh-for-order">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>
            <button type="button" class="btn btn-default btn-filter filter-cancel" id="filter-cancel-for-order">
                <i class="fa fa-undo"></i> 重置
            </button>


            <div class="pull-left clear-both">
            </div>

        </div>
    </div>


    <div class="col-md-12 datatable-body">
        <div class="tableArea">
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


    <div class="col-md-12 datatable-search-row">

        <div class=" pull-left">

            <button type="button" onclick="" class="btn btn-default btn-filter" id="bulk-submit-for-export">
                <i class="fa fa-download"></i> 批量导出
            </button>
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}



            {{--交付结果--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-delivered-result">
                <option value="-1">选择交付结果</option>
                @foreach(config('info.delivered_result') as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
            </select>

            {{--交付说明--}}
            <input type="text" class="search-filter filter-lg form-filter" name="bulk-operate-delivered-description" placeholder="交付说明">


            <button type="button" class="btn btn-default btn-filter " id="bulk-submit-for-delivered">
                <i class="fa fa-share"></i> 批量交付
            </button>

            <button type="button" class="btn btn-default btn-filter bulk-submit-for-export">
                <i class="fa fa-share"></i> 批量导出Excel
            </button>


            {{--交付结果--}}
            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-status">
                <option value="-1">请选导出状态</option>
                <option value="1">已导出</option>
                <option value="0">待导出</option>
            </select>

            <button type="button" class="btn btn-default btn-filter bulk-submit-for-export">
                <i class="fa fa-share"></i> 批量导出Excel
            </button>


        </div>

    </div>


</div>