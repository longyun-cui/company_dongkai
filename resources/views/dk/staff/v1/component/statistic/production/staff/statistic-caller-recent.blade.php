<div class="row datatable-body datatable-wrapper statistic-caller-recent-clone" data-datatable-item-category="statistic-caller-recent">


    <div class="col-md-12 datatable-search-row  datatable-search-box">


        <div class="pull-right">


            <input type="hidden" name="statistic-caller-recent-time-type" class="time-type" value="" readonly>

            @if(in_array($me->staff_category,[0,1,9,61]))
            <select class="search-filter form-filter filter-xl select2-box-c" name="statistic-caller-recent--team">
                <option value="0">选择团队</option>
                @if(!empty($team_list))
                    @foreach($team_list as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                @endif
            </select>
            @endif

s
            <button type="button" class="btn btn-success btn-filter filter-submit">
                <i class="fa fa-search"></i> 查询
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