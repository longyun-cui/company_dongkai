<div class="row datatable-body datatable-wrapper delivery-list-clone" data-datatable-item-category="delivery">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            <input type="hidden" class="time-type" name="delivery-time-type" value="" readonly>


            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-id" placeholder="ID" value="" />
            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-client-phone" placeholder="客户电话" value="" />


            <select class="search-filter form-filter filter-xl select2-box-c- select2-city-c" name="delivery-city[]" multiple="multiple">
                <option value="-1">全部区域</option>
            </select>

            <select class="search-filter form-filter filter-xl select2-box-c- select2-district-c" name="delivery-district[]" multiple="multiple">
                <option value="-1">全部区域</option>
            </select>


            @if($me->client_er->is_api_scrm == 1)
                <select class="search-filter form-filter" name="delivery-is-api-pushed">
                    <option value="-1">API推送</option>
                    <option value="0">未推送</option>
                    <option value="1">已推送</option>
                </select>
            @endif


            <select class="search-filter form-filter" name="delivery-quality">
                <option value="">选择质量</option>
                <option value="有效">有效</option>
                <option value="无效">无效</option>
                <option value="重单">重单</option>
                <option value="无法联系">无法联系</option>
            </select>

            @if(in_array($me->user_type, [0,1,9,11,19]))
            <select class="search-filter form-filter" name="delivery-assign-status">
                <option value="-1">分配状态</option>
                <option value="0">待分配</option>
                <option value="1">已分配</option>
            </select>
            @endif


            <select class="search-filter form-filter filter-lg select2-box-c" name="delivery-staff">
                <option value="-1">选择员工</option>
                @foreach($staff_list as $v)
                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                @endforeach
            </select>


            <select class="search-filter form-filter" name="delivery-is-wx">
                <option value="-1">是否+V</option>
                <option value="1">是</option>
                <option value="0">否</option>
            </select>

            <select class="search-filter form-filter filter-xl select2-box-c- select2-contact-c" name="delivery-contact[]" multiple="multiple">
                <option value="-1">联系渠道</option>
            </select>


            <input type="text" class="search-filter form-filter date_picker-c search-date" name="delivery-callback-date" placeholder="回访日期" readonly="readonly" value="" data-default="" />


            <select class="search-filter form-filter" name="delivery-is-come">
                <option value="-1">上门状态</option>
                <option value="0">不上门</option>
                <option value="9">预约中</option>
                <option value="11">已上门</option>
            </select>

            <input type="text" class="search-filter form-filter date_picker-c search-date" name="delivery-come-date" placeholder="上门日期" readonly="readonly" value="" data-default="" />




            {{--按天查看--}}
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-pre date-pre" data-target="delivery-date">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter date_picker-c search-date" name="delivery-date" placeholder="交付日期" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />
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
            <input type="text" class="search-filter form-filter filter-keyup month_picker-c search-month" name="delivery-month" placeholder="交付月份" readonly="readonly" value="{{ date('Y-m') }}" data-default="{{ date('Y-m') }}" />
            <button type="button" class="btn btn-default btn-filter time-picker-move picker-move-next month-next" data-target="delivery-month">
                <i class="fa fa-chevron-right"></i>
            </button>
            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="month">
                <i class="fa fa-search"></i> 按月查询
            </button>

            {{--按时间段导出--}}
            <input type="text" class="search-filter form-filter date_picker-c search-start" name="delivery-start" placeholder="起始时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" style="margin-right:-3px;" />
            <input type="text" class="search-filter form-filter date_picker-c search-ended" name="delivery-ended" placeholder="终止时间" readonly="readonly" value="{{ date('Y-m-d') }}" data-default="{{ date('Y-m-d') }}" />

            <button type="button" class="btn btn-success btn-filter filter-submit" data-time-type="period">
                <i class="fa fa-search"></i> 按时间段搜索
            </button>






            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 全部搜索
            </button>
            <button type="button" class="btn btn-info btn-filter filter-empty">
                <i class="fa fa-remove"></i> 清空
            </button>
            <button type="button" class="btn btn-primary btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>
            <button type="button" class="btn btn-warning btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


            <div class="pull-left clear-both">
            </div>

        </div>


    </div>


    <div class="col-md-12 datatable-body">
        <div class="tableArea  table-luxury full-">
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


    @if(in_array($me->user_type, [0,1,9,11,19,84]))
    <div class="col-md-12 datatable-search-row">

        <div class=" pull-left">



            <button class="btn btn-default btn-filter">
                <input type="checkbox" id="check-review-all">
            </button>


            {{--<button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


            @if(in_array($me->user_type, [0,1,9,11,19]))
            <button class="btn btn-default btn-filter bulk-submit-for-export" data-item-category="31">
                <i class="fa fa-download"></i> 批量导出
            </button>
            @endif


            @if($me->client_er->is_api_scrm == 1)
            <button class="btn btn-default btn-filter bulk-submit-for-api-push">
                <i class="fa fa-share-square"></i> 批量API推送
            </button>
            @endif




{{--            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-assign-status">--}}
{{--                <option value="-1">请选分配状态</option>--}}
{{--                <option value="1">已分配</option>--}}
{{--                <option value="0">待分配</option>--}}
{{--            </select>--}}

{{--            <button class="btn btn-default btn-filter bulk-submit-for-assign-status">--}}
{{--                <i class="fa fa-check"></i> 批量更改分配状态--}}
{{--            </button>--}}




            @if(in_array($me->user_type, [0,1,9,11,19,84]))
            <select class="search-filter form-filter filter-lg select2-box-c" name="bulk-operate-staff-id">
                <option value="-1">选择员工</option>
                @foreach($staff_list as $v)
                    <option value="{{ $v->id }}">{{ $v->username }}</option>
                @endforeach
            </select>

            <button class="btn btn-default btn-filter bulk-submit-for-assign-staff">
                <i class="fa fa-check"></i> 批量分配
            </button>
            @endif

        </div>

    </div>
    @endif


</div>