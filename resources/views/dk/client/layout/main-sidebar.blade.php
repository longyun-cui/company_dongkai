{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">

            <!-- Optionally, you can add icons to the links -->

            <li class="treeview _none">
                <a href="">
                    <i class="fa fa-th"></i> <span>Super</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{url('/'.config('common.super.admin.prefix').'/softorg/index')}}">
                            <i class="fa fa-circle-o text-aqua"></i>基本信息
                        </a>
                    </li>
                    <li>
                        <a href="{{url('/'.config('common.super.admin.prefix').'/softorg/edit')}}">
                            <i class="fa fa-circle-o text-aqua"></i>编辑基本信息
                        </a>
                    </li>
                </ul>
            </li>


            {{--员工管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61,41]))
                <li class="header">人员管理</li>
            @endif

            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11,41]))
            <li class="treeview {{ $menu_active_of_department_list_for_all or '' }}">
                <a href="{{ url('/department/department-list') }}">
                    <i class="fa fa-columns text-red"></i>
                    <span>部门列表</span>
                </a>
            </li>
            @endif

            {{--员工管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
            <li class="treeview {{ $menu_active_of_staff_list_for_all or '' }}">
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
                <li class="treeview {{ $menu_active_of_delivery_list or '' }}">
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

            <li class="treeview {{ $menu_active_of_statistic_delivery_by_daily or '' }}">
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
            <li class="header">财务统计</li>

            <li class="treeview {{ $menu_active_of_finance_daily_list or '' }}">
                <a href="{{ url('/finance/daily-list') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>财务日报</span>
                </a>
            </li>



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>