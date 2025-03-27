{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">




            {{--项目列表--}}
            <li class="treeview _none-">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="reconciliation-project-list"
                   data-title='<i class="fa fa-cube text-green"></i> 项目列表'
                   data-content=''

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-reconciliation-project-list"
                   data-datatable-target="reconciliation-project-list"
                   data-datatable-clone-object="reconciliation-project-list-clone"
                >
                    <i class="fa fa-cube text-green"></i>
                    <span>项目列表</span>
                </a>
            </li>

            {{--项目列表--}}
                <li class="treeview _none-">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="reconciliation-daily-list"
                       data-title='<i class="fa fa-file-text text-yellow"></i> 每日对账'
                       data-content=''

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-reconciliation-daily-list"
                       data-datatable-target="reconciliation-daily-list"
                       data-datatable-clone-object="reconciliation-daily-list-clone"
                    >
                        <i class="fa fa-file-text text-yellow"></i>
{{--                        <i class="fa fa-bar-chart text-yellow"></i>--}}
                        <span>对账列表</span>
                    </a>
                </li>

            {{--成交记录--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="trade-list"
                   data-title='<i class="fa fa-cny text-aqua"></i> <span>成交记录</span>'
                   data-content="成交记录"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-trade-list"
                   data-datatable-target="trade-list"
                   data-datatable-clone-object="datatable-trade-list-clone"
                >
                    <i class="fa fa-cny text-aqua"></i>
                    <span>成交记录</span>
                </a>
            </li>


        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>