<div class="row datatable-body item-main-body">


    <div class="col-md-12 datatable-search-row" id="datatable-search-for-department-list">


        <div class=" pull-left">

            <button type="button" onclick="" class="btn btn-default"><i class="fa fa-plus"></i> 添加</button>
            <button type="button" onclick="" class="btn btn-default"><i class="fa fa-play"></i> 启用</button>
            <button type="button" onclick="" class="btn btn-default"><i class="fa fa-stop"></i> 禁用</button>
            <button type="button" onclick="" class="btn btn-default"><i class="fa fa-download"></i> 导出</button>
            <button type="button" onclick="" class="btn btn-default"><i class="fa fa-trash-o"></i> 批量删除</button>

        </div>


        <div class="navbar-custom-menu pull-right">


            <div class="input-group pull-left _none">

                <input type="text" class="form-control form-filter item-search-keyup" name="team-title" placeholder="名称">


                <select class="form-control form-filter" name="team-status" style="width:96px;">
                    <option value="1">启用</option>
                    <option value="-1">全部</option>
                    <option value="9">禁用</option>
                </select>

            </div>


            <div class="nav navbar-nav">

                <div class="dropdown filter-menu" data-bs-auto-close="outside" style="position: relative;float:left;">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="margin-right:4px;">
                        <i class="fa fa-search"></i> 搜索
                    </button>

                    <div class="dropdown-menu box box-success" style="position: absolute;width:400px;top:-4px;left:auto;right:72px;padding:4px;">

                        <div class="box-header with-border- _none">
                            筛选
                        </div>

                        <div class="divider _none"></div>

                        <div class="box-body">
                            <label class="col-md-3">项目</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box select2-container" name="project_id">
                                    <option data-id="0" value="0">未指定</option>
                                </select>
                            </div>
                        </div>
                        <div class="divider"></div>

                        <div class="box-body">
                            <label class="col-md-3">发布日期</label>
                            <div class="col-md-9 filter-body">
                                <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-assign" placeholder="发布日期" value="" readonly="readonly" />
                            </div>
                        </div>
                        <div class="divider"></div>

                        <div class="box-body">
                            <label class="col-md-3">交付日期</label>
                            <div class="col-md-9 filter-body">
                                <input type="text" class="form-control form-filter filter-keyup date_picker" name="order-delivered_date" placeholder="交付日期" value="" readonly="readonly" />
                            </div>
                        </div>
                        <div class="divider"></div>


                        {{--选择员工--}}
                        <div class="box-body">
                            <label class="col-md-3">员工</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box order-select2-staff" name="order-staff">
                                    <option value="-1">选择员工</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                        <div class="divider"></div>


                        {{--选择员工--}}
                        <div class="box-body">
                            <label class="col-md-3">员工</label>
                            <div class="col-md-9 filter-body">
                                <select class="form-control form-filter select2-box" name="order-department-district[]" id="order-department-district" multiple="multiple"  style="width:100px;">
                                    <option value="-1">选择团队</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                        <div class="divider"></div>



                        {{--选择员工--}}

                        <div class="box-footer" style="text-align: center;">

                            <button type="button" class="btn btn-default filter-submit" id="filter-submit">
                                <i class="fa fa-search"></i> 搜 索
                            </button>
                            <button type="button" class="btn bg-default filter-empty">
                                <i class="fa fa-remove"></i> 重 置
                            </button>

                        </div>
                    </div>
                </div>

            </div>

            <button type="button" class="btn btn-default filter-refresh">
                <i class="fa fa-circle-o-notch"></i> 刷新
            </button>

            <button type="button" class="btn btn-default filter-cancel">
                <i class="fa fa-undo"></i> 重置
            </button>

            <div class="btn-group- pull-left">
            </div>


        </div>


    </div>


    <div class="col-md-12 tableArea full">
        <table class='table table-striped table-bordered- table-hover main-table' id='datatable-for-department-list'>
            <thead>
            <tr role='row' class='heading'>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>


</div>



