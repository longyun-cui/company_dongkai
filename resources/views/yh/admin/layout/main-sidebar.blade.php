{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel _none">
            <div class="pull-left image">
                <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ $me->nickname or '' }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form _none">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

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


            {{--用户管理--}}
            <li class="header">员工管理</li>

            @if(in_array($me->user_type,[0,1,9,11,21,22]))
            <li class="treeview {{ $menu_active_of_staff_list_for_all or '' }}">
                <a href="{{ url('/user/staff-list-for-all') }}">
                    <i class="fa fa-user text-blue"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif

            <li class="treeview {{ $menu_active_of_driver_list_for_all or '' }}">
                <a href="{{ url('/user/driver-list-for-all') }}">
                    <i class="fa fa-user text-blue"></i>
                    <span>驾驶员列表</span>
                </a>
            </li>





            <li class="header">业务管理</li>

            <li class="treeview {{ $menu_active_of_client_list_for_all or '' }}">
                <a href="{{ url('/user/client-list-for-all')}}">
                    <i class="fa fa-user text-green"></i>
                    <span>客户列表</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_car_list_for_all or '' }}">
                <a href="{{ url('/item/car-list-for-all')}}">
                    <i class="fa fa-truck text-green"></i>
                    <span>车辆列表</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_route_list_for_all or '' }}">
                <a href="{{ url('/item/route-list-for-all')}}">
                    <i class="fa fa-map text-green"></i>
                    <span>线路列表</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_pricing_list_for_all or '' }}">
                <a href="{{ url('/item/pricing-list-for-all')}}">
                    <i class="fa fa-rmb text-green"></i>
                    <span>定价列表</span>
                </a>
            </li>





            <li class="header">订单管理</li>

            <li class="treeview {{ $menu_active_of_order_list_for_all or '' }}">
                <a href="{{ url('/item/order-list-for-all')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>订单列表</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_circle_list_for_all or '' }}">
                <a href="{{ url('/item/circle-list-for-all')}}">
                    <i class="fa fa-refresh text-yellow"></i>
                    <span>环线列表</span>
                </a>
            </li>




            <li class="header">财务管理</li>

            <li class="treeview {{ $menu_active_of_finance_list_for_all or '' }}">
                <a href="{{ url('/finance/finance-list-for-all')}}">
                    <i class="fa fa-list text-red"></i>
                    <span>全部记录</span>
                </a>
            </li>
            {{--<li class="treeview {{ $menu_active_of_finance_list_for_income or '' }}">--}}
                {{--<a href="{{ url('/finance/finance-list-for-income')}}">--}}
                    {{--<i class="fa fa-list text-red"></i>--}}
                    {{--<span>支出记录</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            {{--<li class="treeview {{ $menu_active_of_finance_list_for_expense or '' }}">--}}
                {{--<a href="{{ url('/finance/finance-list-for-expense')}}">--}}
                    {{--<i class="fa fa-list text-red"></i>--}}
                    {{--<span>收入记录</span>--}}
                {{--</a>--}}
            {{--</li>--}}




            {{--数据统计--}}
            <li class="header">数据统计</li>

            <li class="treeview {{ $menu_active_of_statistic_index or '' }}">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-bar-chart text-green"></i> <span>数据统计</span>
                </a>
            </li>

            @if(in_array($me->user_type,[0,1,9,11,21,22,81,82,88]))
            <li class="treeview {{ $menu_active_of_statistic_export or '' }}">
                <a href="{{ url('/statistic/statistic-export') }}">
                    <i class="fa fa-download text-green"></i> <span>数据导出</span>
                </a>
            </li>
            @endif
{{--            <li class="treeview {{ $menu_active_of_statistic_list_for_client or '' }}">--}}
{{--                <a href="{{ url('/statistic/statistic-list-for-client') }}">--}}
{{--                    <i class="fa fa-bar-chart text-green"></i> <span>客户统计</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li class="treeview {{ $menu_active_of_statistic_list_for_car or '' }}">--}}
{{--                <a href="{{ url('/statistic/statistic-list-for-car') }}">--}}
{{--                    <i class="fa fa-bar-chart text-green"></i> <span>车辆统计</span>--}}
{{--                </a>--}}
{{--            </li>--}}



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>