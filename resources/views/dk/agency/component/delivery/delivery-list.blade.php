<div class="row datatable-body datatable-wrapper delivery-list-clone">


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class=" pull-left">

            <button type="button" onclick="" class="btn btn-default btn-filter bulk-submit-for-export">
                <i class="fa fa-download"></i> 批量导出
            </button>
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


        </div>


        <div class="pull-right">

            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-id" placeholder="ID" value="" style="width:80px;" />
{{--            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-order-id" placeholder="工单ID" value="" style="width:80px;" />--}}

            {{--<input type="text" class="form-control form-filter filter-keyup" name="order-client-name" placeholder="客户姓名" value="{{ $client_name or '' }}" style="width:88px;" />--}}
            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="delivery-client-phone" placeholder="客户电话" value="" />


            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-pre-for-order" style="width:24px;">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="delivery-assign" placeholder="交付日期" value="" readonly="readonly" />
            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-next-for-order" style="width:24px;">
                <i class="fa fa-chevron-right"></i>
            </button>


            <select class="search-filter form-filter select2-box-c" name="delivery-client" style="width:200px;">
                <option value="-1">全部项目</option>
                @if(!empty($client_list) && count($client_list) > 0)
                    @foreach($client_list as $v)
                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                    @endforeach
                @endif
            </select>


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