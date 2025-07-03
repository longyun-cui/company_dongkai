<div class="row datatable-body datatable-wrapper call-record-list-clone" data-datatable-item-category="call-record-list">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">



            {{--电话号码--}}
            <textarea class="form-control" name="call-record-list-phone" rows="5" cols="100%" placeholder="电话号码，每行一个号码"></textarea>

            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="call-record-list-start" placeholder="开始日期" value="{{ date('Y-m-d') }}" readonly="readonly" />
            <input type="text" class="search-filter form-filter filter-md filter-keyup date_picker-c" name="call-record-list-ended" placeholder="结束日期" value="{{ date('Y-m-d') }}" readonly="readonly" />

            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 查询
            </button>

            {{--            <button type="button" class="btn btn-default btn-filter filter-empty">--}}
            {{--                <i class="fa fa-remove"></i> 清空--}}
            {{--            </button>--}}

            <button type="button" class="btn btn-default btn-filter filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>

            <button type="button" class="btn btn-default btn-filter filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>


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