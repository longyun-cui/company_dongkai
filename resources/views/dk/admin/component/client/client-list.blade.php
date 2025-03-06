<div class="row datatable-body datatable-wrapper client-list-clone" data-datatable-item-category="client" data-item-name="客户">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class=" pull-left">

            @if(in_array($me->user_type,[0,1,9,11,19,61]))
            <button type="button" onclick="" class="btn btn-filter btn-success- item-create-modal-show"
                    data-form-id="form-for-client-edit"
                    data-modal-id="modal-for-client-edit"
                    data-title="添加客户"
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


            <input type="text" class="search-filter form-filter filter-keyup" name="client-id" placeholder="ID" />
            <input type="text" class="search-filter form-filter filter-keyup" name="client-username" placeholder="用户名" />

            <select class="search-filter form-filter filter-lg select2-box-c select2-company" name="client-company">
                <option value="-1">选择公司</option>
                @foreach($company_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>

            <select class="search-filter form-filter filter-lg select2-box-c select2-channel" name="client-channel">
                <option value="-1">选择渠道</option>
                @foreach($channel_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>

            <select class="search-filter form-filter filter-lg select2-box-c select2-business" name="client-business">
                <option value="-1">选择商务</option>
                @foreach($business_list as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
            </select>

            <select class="search-filter form-filter form-filter select2-box-c" name="client-user-status">
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