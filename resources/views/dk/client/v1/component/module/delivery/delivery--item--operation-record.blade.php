{{--跟进记录--}}
<div class="modal fade- modal-main-body modal-wrapper" id="modal--for--delivery--item-operation-record-list">
    <div class="col-md-10 col-md-offset-1 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">


            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">操作记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);" class="item-modal-show-for-follow-create">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="">

                <div class="row col-md-12 datatable-search-row _none">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value="-1">选择属性</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable--for--delivery--item-operation-record-list'>
                    <thead>
                    <tr role='row' class='heading'>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

