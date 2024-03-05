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


            {{--客户管理--}}
            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="header">客户管理</li>

            <li class="treeview {{ $menu_active_of_client_list_for_all or '' }}">
                <a href="{{ url('/user/client-list-for-all') }}">
                    <i class="fa fa-user-secret text-red"></i>
                    <span>客户列表</span>
                </a>
            </li>
            @endif


            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="header">部门管理</li>

            <li class="treeview {{ $menu_active_of_department_list_for_all or '' }}">
                <a href="{{ url('/department/department-list-for-all') }}">
                    <i class="fa fa-columns text-blue"></i>
                    <span>部门列表</span>
                </a>
            </li>
            @endif


            {{--员工管理--}}
            @if(in_array($me->user_type,[0,1,9,11,81]))
            <li class="header">员工管理</li>

            <li class="treeview {{ $menu_active_of_staff_list_for_all or '' }}">
                <a href="{{ url('/user/staff-list-for-all') }}">
                    <i class="fa fa-user text-blue"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif





            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="treeview {{ $menu_active_of_project_list_for_all or '' }} _none1">
                <a href="{{ url('/item/project-list-for-all')}}">
                    <i class="fa fa-cube text-green"></i>
                    <span>项目列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="treeview {{ $menu_active_of_order_list_for_all or '' }}">
                <a href="{{ url('/item/order-list-for-all')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>工单列表</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header">数据统计</li>

            @if(in_array($me->user_type,[0,1,9,11,71,77,81]))
            <li class="treeview {{ $menu_active_of_statistic_project or '' }}">
                <a href="{{ url('/statistic/statistic-project') }}">
                    <i class="fa fa-area-chart text-teal"></i> <span>项目看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="treeview {{ $menu_active_of_statistic_department or '' }}">
                <a href="{{ url('/statistic/statistic-department') }}">
                    <i class="fa fa-area-chart text-teal"></i> <span>部门看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,81,84]))
            <li class="treeview {{ $menu_active_of_statistic_customer_service or '' }}">
                <a href="{{ url('/statistic/statistic-customer-service') }}">
                    <i class="fa fa-bar-chart text-purple"></i> <span>客服看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71]))
            <li class="treeview {{ $menu_active_of_statistic_inspector or '' }}">
                <a href="{{ url('/statistic/statistic-inspector') }}">
                    <i class="fa fa-bar-chart text-purple"></i> <span>审核员看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,81,84]))
            <li class="treeview {{ $menu_active_of_statistic_rank or '' }}">
                <a href="{{ url('/statistic/statistic-rank') }}">
                    <i class="fa fa-line-chart text-maroon"></i> <span>员工排名</span>
                </a>
            </li>
            @endif

            <li class="treeview {{ $menu_active_of_statistic_index or '' }}">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>数据统计</span>
                </a>
            </li>

            @if(in_array($me->user_type,[0,1,9,11,71,77]))
            <li class="treeview {{ $menu_active_of_statistic_export or '' }}">
                <a href="{{ url('/statistic/statistic-export') }}">
                    <i class="fa fa-download text-default"></i> <span>数据导出</span>
                </a>
            </li>
            @endif



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>