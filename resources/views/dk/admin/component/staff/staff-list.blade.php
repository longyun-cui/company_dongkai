<div class="row datatable-body datatable-wrapper staff-list-clone" data-datatable-item-category="staff" data-item-name="员工">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class=" pull-left">

            @if(in_array($me->user_type,[0,1,9,11,19,41,81,61]))
            <button type="button" onclick="" class="btn btn-filter btn-success- item-create-modal-show"
                    data-form-id="form-for-staff-edit"
                    data-modal-id="modal-for-staff-edit"
                    data-title="添加员工"
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


            <input type="text" class="search-filter form-filter filter-keyup" name="staff-id" placeholder="ID" />
            <input type="text" class="search-filter form-filter filter-keyup" name="staff-mobile" placeholder="工号" />
            <input type="text" class="search-filter form-filter filter-keyup" name="staff-username" placeholder="名称" />

            @if(in_array($me->user_type,[0,1,9,11]))
                <select class="search-filter form-filter" name="staff-user-type">
                    <option value="-1">全部人员</option>
                    <option value="41">团队·总经理</option>
                    <option value="88">客服</option>
                    <option value="84">客服主管</option>
                    <option value="81">客服经理</option>
                    <option value="77">质检员</option>
                    <option value="71">质检经理</option>
                    <option value="66">运营人员</option>
                    <option value="61">运营经理</option>
                </select>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]))
                <select class="search-filter form-filter select2-box-c" name="staff-department-district">
                    <option value="-1">选择大区</option>
                    @foreach($department_district_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            @endif

            <select class="search-filter form-filter" name="staff-user-status">
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