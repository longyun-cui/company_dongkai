<div class="row datatable-body datatable-wrapper delivery-list-clone" data-datatable-item-category="delivery" data-item-name="交付">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class=" pull-left">

            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-play"></i> 启用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-stop"></i> 禁用</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-download"></i> 导出</button>
            <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-trash-o"></i> 批量删除</button>

        </div>


        <div class="pull-right">



            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-id" placeholder="ID" value="" />
            <input type="text" class="search-filter form-filter filter-keyup" name="delivery-order-id" placeholder="工单ID" value="" />

            <input type="text" class="search-filter form-filter filter-md filter-keyup" name="order-client-phone" placeholder="客户电话" value="" />


            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-pre-for-order">
                <i class="fa fa-chevron-left"></i>
            </button>
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker" name="order-assign" placeholder="交付日期" value="" readonly="readonly" />
            <button type="button" class="search-filter btn btn-flat btn-default date-picker-btn date-pick-next-for-order">
                <i class="fa fa-chevron-right"></i>
            </button>


            <select class="search-filter form-filter select2-box-c delivery-project" name="delivery-project">
                <option value="-1">选择项目</option>
                @if(!empty($project_list))
                    @foreach($project_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>


            <select class="search-filter form-filter select2-box-c delivery-client" name="delivery-client">
                <option value="-1">选择客户</option>
                @if(!empty($client_list))
                    @foreach($client_list as $v)
                        <option value="{{ $v->id }}">{{ $v->username }}</option>
                    @endforeach
                @endif
            </select>


            <select class="search-filter form-filter" name="order-delivery-type" style="width:88px;">
                <option value="-1">交付类型</option>
                <option value="95">交付</option>
                <option value="96">分发</option>
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
        <div class="tableArea table-delivery">
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