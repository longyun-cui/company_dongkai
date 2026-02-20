<div class="row datatable-body datatable-wrapper department-list-clone" data-datatable-item-category="department" data-item-name="部门">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class=" pull-left">

            @if(in_array($me->user_type,[0,1,9,11,19]))
            <button type="button" onclick="" class="btn btn-filter modal-show--for--department-item-create"
                    data-form-id="form--for--department-item-edit"
                    data-modal-id="modal--for--department-item-edit"
                    data-title="添加部门"
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


            <input type="text" class="search-filter form-filter filter-keyup" name="department-id" placeholder="ID" />

            <input type="text" class="search-filter form-filter filter-keyup" name="department-name" placeholder="名称" />

            <select class="search-filter form-filter select2-box-c" name="department-category">
                <option value ="-1">全部</option>
                <option value ="41">客服</option>
                <option value ="51">质检</option>
                <option value ="61">复核</option>
                <option value ="71">运营</option>
                <option value ="88">销售</option>
            </select>

            <select class="search-filter form-filter select2-box-c" name="department-status">
                <option value ="-1">全部</option>
                <option value ="1">启用</option>
                <option value ="9">禁用</option>
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

            <div class="box-header">
            </div>

        </div>
    </div>


</div>