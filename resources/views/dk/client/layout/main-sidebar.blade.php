{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">



            {{--员工管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61,41]))
                <li class="header _none">人员管理</li>
            @endif

            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11,41]))
            <li class="treeview {{ $menu_active_of_department_list_for_all or '' }} _none">
                <a href="{{ url('/department/department-list') }}">
                    <i class="fa fa-columns text-red"></i>
                    <span>部门列表</span>
                </a>
            </li>
            @endif

            {{--员工管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
            <li class="treeview {{ $menu_active_of_staff_list_for_all or '' }} _none">
                <a href="{{ url('/user/staff-list') }}">
                    <i class="fa fa-user text-red"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif




            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
                <li class="treeview {{ $menu_active_of_delivery_list or '' }} _none">
                    <a href="{{ url('/item/delivery-list') }}">
                        <i class="fa fa-file-text text-yellow"></i>
                        <span>交付列表</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="treeview {{ $menu_active_of_order_list_for_all or '' }} _none">
                <a href="{{ url('/item/order-list-for-all') }}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>工单列表</span>
                </a>
            </li>
            @endif

            <li class="treeview {{ $menu_active_of_statistic_delivery_by_daily or '' }} _none">
                <a href="{{ url('/statistic/statistic-delivery-by-daily') }}">
                    <i class="fa fa-bar-chart text-yellow"></i> <span>交付日报</span>
                </a>
            </li>




            {{--数据统计--}}
            <li class="header _none">数据统计</li>

            <li class="treeview {{ $menu_active_of_statistic_index or '' }} _none">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>数据统计</span>
                </a>
            </li>

            @if(in_array($me->user_type,[0,1,9,11,71,77]))
            <li class="treeview {{ $menu_active_of_statistic_export or '' }} _none">
                <a href="{{ url('/statistic/statistic-export') }}">
                    <i class="fa fa-download text-default"></i> <span>数据导出</span>
                </a>
            </li>
            @endif




            {{--财务统计--}}
            <li class="header _none">财务统计</li>

            <li class="treeview {{ $menu_active_of_finance_daily_list or '' }} _none">
                <a href="{{ url('/finance/daily-list') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>财务日报</span>
                </a>
            </li>


            {{--部门列表--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="department-list"
                   data-title="部门列表"
                   data-content="部门列表"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-department-list"
                   data-datatable-target="department-list"
                   data-datatable-clone-object="department-list-clone"
                >
                    <i class="fa fa-columns text-red"></i>
                    <span>部门列表</span>
                </a>
            </li>


            {{--员工列表--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="staff-list"
                   data-title="员工列表"
                   data-content="员工列表"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-staff-list"
                   data-datatable-target="staff-list"
                   data-datatable-clone-object="staff-list-clone"
                >
                    <i class="fa fa-user text-red"></i>
                    <span>员工列表</span>
                </a>
            </li>


            {{--交付列表--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="delivery-list"
                   data-title="交付列表"
                   data-content="交付列表"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-delivery-list"
                   data-datatable-target="delivery-list"
                   data-datatable-clone-object="delivery-list-clone"
                >
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>交付列表</span>
                </a>
            </li>
            {{--交付日报--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="delivery-daily"
                   data-title="交付日报"
                   data-content="交付日报"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-delivery-daily"
                   data-datatable-target="delivery-daily"
                   data-datatable-clone-object="delivery-daily-clone"

                   data-chart-id="eChart-delivery-daily"
                >
                    <i class="fa fa-bar-chart text-yellow"></i>
                    <span>交付日报</span>
                </a>
            </li>


            {{--财务日报--}}
            <li class="treeview _none">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="finance-daily"
                   data-title="财务日报"
                   data-content="财务日报"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-finance-daily"
                   data-datatable-target="finance-daily"
                   data-datatable-clone-object="finance-daily-clone"

                   data-chart-id="eChart-finance-daily"
                >
                    <i class="fa fa-pie-chart text-green"></i>
                    <span>财务日报</span>
                </a>
            </li>



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>