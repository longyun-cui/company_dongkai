<div class="row datatable-body datatable-wrapper order-dental-rejected-list-clone"
     data-order-category="1"
     data-datatable-item-category="dental"
     data-item-name="口腔工单"
>


    <div class="col-md-12 datatable-search-row datatable-search-box">


        <div class="pull-right">

            {{--ID--}}
            <input type="text" class="search-filter form-filter filter-sm filter-keyup" name="order-id" placeholder="ID" value="" />

            {{--电话号码--}}
            <input type="text" class="search-filter form-filter filter-smd filter-keyup" name="order-client-phone" placeholder="电话号码" value="" />

            {{--发布日期--}}
            <input type="text" class="search-filter form-filter filter-smd filter-keyup date-picker-c" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-start" placeholder="开始日期" value="" readonly="readonly" />--}}
{{--            <input type="text" class="search-filter form-filter filter-md filter-keyup date-picker-c" name="order-ended" placeholder="结束日期" value="" readonly="readonly" />--}}




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
                <div class="tableArea full margin-top-8px">
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


</div>