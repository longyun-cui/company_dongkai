<div class="row datatable-body datatable-wrapper staff-list-clone" data-datatable-item-category="staff">


    <div class="col-md-12 datatable-search-row datatable-search-box">

        <div class=" pull-left">



            @if(in_array($me->user_type,[0,1,9,11,19]))
            <button type="button" onclick="" class="btn btn-filter btn-success- item-create-show"
                data-form-id="form-for-staff-edit"
                data-modal-id="modal-for-staff-edit"
                data-title="添加员工"
            >
                <i class="fa fa-plus"></i> 添加
            </button>
            @endif
            {{--<button type="button" onclick="" class="btn btn-success btn-filter item-create-show"><i class="fa fa-plus"></i> 添加</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-play"></i> 启用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-stop"></i> 禁用</button>--}}
            {{--<button type="button" onclick="" class="btn btn-default btn-filter"><i class="fa fa-trash-o"></i> 批量删除</button>--}}


            <button class="btn btn-default btn-filter _none" id="bulk-submit-for-delivery-export">
                <i class="fa fa-download"></i> 批量导出
            </button>


        </div>

        <div class="pull-right">

            <input type="text" class="search-filter form-filter filter-keyup" name="staff-mobile" placeholder="登录手机" />
            <input type="text" class="search-filter form-filter filter-keyup" name="staff-username" placeholder="用户名" />

            @if(in_array($me->user_type,[0,1,9,11]))
                <select class="search-filter form-filter filter-xl select2-box-c" name="staff-department">
                    <option value="-1">选择部门</option>
                    @foreach($department_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            @endif


            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 搜索
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