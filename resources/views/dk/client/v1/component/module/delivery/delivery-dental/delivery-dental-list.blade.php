<div class="row datatable-body datatable-wrapper delivery-list-clone"
     data-datatable-item-category="delivery"
     data-item-name="交付"
     data-order-category="1"
>


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class=" pull-left">

            <input type="hidden" class="time-type" name="delivery-time-type" value="" readonly>

            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-play"></i> 启用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-stop"></i> 禁用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-download"></i> 导出</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-trash-o"></i> 批量删除</button>

        </div>


        <div class="pull-right">


            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-id" placeholder="ID" value="" />

            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="客户电话" value="" />


            @if($me->client_er->is_api_scrm == 1)
            <select class="search-filter form-filter filter-md select2-box-c" name="delivery-is-api-pushed">
                <option value="-1">API推送</option>
                <option value="0">未推送</option>
                <option value="1">已推送</option>
            </select>
            @endif


            <select class="search-filter form-filter filter-md select2-box-c" name="delivery-quality">
                <option value="">选择质量</option>
                <option value="有效">有效</option>
                <option value="无效">无效</option>
                <option value="重单">重单</option>
                <option value="待联系">待联系</option>
                <option value="无法联系">无法联系</option>
            </select>


            @if(in_array($me->staff_position,[0,1,9]))
            <select class="search-filter form-filter filter-md select2-box-c" name="delivery-assign-status">
                <option value="-1">分配状态</option>
                <option value="0">待分配</option>
                <option value="1">已分配</option>
            </select>
            @endif


            @if(in_array($me->staff_position,[0,1,9]))
            <select class="search-filter form-filter filter-lg select2-box-c" name="delivery-staff">
                <option value="-1">选择员工</option>
                @if(!empty($staff_list) && count($staff_list) > 0)
                @foreach($staff_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
                @endif
            </select>
            @endif


            <select class="search-filter form-filter filter-md select2-box-c" name="delivery-client-type">
                <option value="-1">患者类型</option>
                @foreach(config('info.client_type') as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                @endforeach
            </select>


{{--            <select class="search-filter form-filter filter-md select2-box-c" name="order-delivery-result">--}}
{{--                <option value="-1">交付结果</option>--}}
{{--                <option value="正常交付">正常交付</option>--}}
{{--                <option value="折扣交付">折扣交付</option>--}}
{{--                <option value="郊区交付">郊区交付</option>--}}
{{--            </select>--}}

{{--            <input type="text" class="search-filter form-filter date-picker-c search-date" name="delivery-callback-date" placeholder="回访日期" readonly="readonly" value="" data-default="" />--}}


            <select class="search-filter form-filter filter-md select2-box-c" name="delivery-is-come">
                <option value="-1">上门状态</option>
                <option value="0">不上门</option>
                <option value="9">预约中</option>
                <option value="11">已上门</option>
            </select>

            <input type="text" class="search-filter form-filter date-picker-c search-date" name="delivery-come-date" placeholder="上门日期" readonly="readonly" value="" data-default="" />


            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="delivery-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter date-picker-c search-date" name="delivery-date" placeholder="选择日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next date-next" data-target="delivery-date">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="date">
                <i class="fa fa-search"></i> 按日查询
            </button>


            {{--按月查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre month-pre" data-target="delivery-month">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-keyup month-picker-c search-month" name="delivery-month" placeholder="选择月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="delivery-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>


            {{--按时间段导出--}}
            <input type="text" class="search-filter form-filter date-picker-c search-start" name="delivery-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-01') }}" data-default="{{ date('Y-m-01') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter form-filter date-picker-c search-ended" name="delivery-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>




            <button type="button" class="btn btn-default btn-filter filter-submit" data-time-type="all">
                <i class="fa fa-search"></i> 全部搜索
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
                <div class="tableArea table-delivery full- margin-top-8px">
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


    <div class="col-md-12 datatable-search-row">
        <div class="pull-left">


            <button class="btn btn-default btn-filter">
                <input type="checkbox" class="check-review-all">
            </button>


            <button class="btn btn-default btn-filter bulk-submit--for--delivery--export" data-order-category="1">
                <i class="fa fa-download"></i> 批量导出
            </button>


            @if($me->client_er->is_api_scrm == 1)
                <button class="btn btn-default btn-filter bulk-submit-for-api-push">
                    <i class="fa fa-share-square"></i> 批量API推送
                </button>
            @endif


            @if(in_array($me->staff_position,[0,1,9]))
            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-select--for--delivery--assign-status">
                <option value="-1">请选分配状态</option>
                <option value="1">已分配</option>
                <option value="0">待分配</option>
            </select>
            @endif

            @if(in_array($me->staff_position,[0,1,9]))
            <button class="btn btn-default btn-filter bulk-submit--for--delivery--assign-status">
                <i class="fa fa-check"></i> 批量更改分配状态
            </button>
            @endif


            @if(in_array($me->staff_position,[0,1,9]))
            <select class="search-filter form-filter filter-lg select2-box-c select2-staff-c-" name="bulk-select--for--delivery--staff-id">
                <option value="-1">选择员工</option>
                @if(!empty($staff_list) && count($staff_list) > 0)
                @foreach($staff_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
                @endif
            </select>
            @endif

            @if(in_array($me->staff_position,[0,1,9]))
            <button class="btn btn-default btn-filter bulk-submit--for--delivery--assign-staff">
                <i class="fa fa-check"></i> 批量指派员工
            </button>
            @endif


        </div>
    </div>


</div>