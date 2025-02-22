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


            <li class="treeview _none">
                <a class="menu-tab-show" data-tab="department-list" data-title="团队列表">
                    <i class="fa fa-columns text-blue"></i>
                    <span>团队列表</span>
                </a>
            </li>
            <li class="treeview _none">
                <a class="menu-tab-show" data-tab="order-list" data-title="工单列表">
                    <i class="fa fa-columns text-blue"></i>
                    <span>工单列表</span>
                </a>
            </li>






            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11,41]))
                <li class="header">公司管理</li>
            @endif


            @if(in_array($me->user_type,[0,1,9,11,41]))
            <li class="treeview {{ $menu_active_of_department_list or '' }}">
                <a href="{{ url('/department/department-list') }}">
                    <i class="fa fa-columns text-blue"></i>
                    <span>部门列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
            <li class="treeview {{ $menu_active_of_staff_list or '' }}">
                <a href="{{ url('/user/staff-list') }}">
                    <i class="fa fa-user text-blue"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif



            {{--客户管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61]))
                <li class="header">销售管理</li>
            @endif
            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="treeview {{ $menu_active_of_company_list or '' }}">
                <a href="{{ url('/company/company-list') }}">
                    <i class="fa fa-user-secret text-red"></i>
                    <span>公司列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61]))
            <li class="treeview {{ $menu_active_of_client_list or '' }}">
                <a href="{{ url('/user/client-list') }}">
                    <i class="fa fa-user-secret text-red"></i>
                    <span>客户列表</span>
                </a>
            </li>
            @endif





            @if(in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61]))
            <li class="treeview {{ $menu_active_of_district_list or '' }}">
                <a href="{{ url('/district/district-list')}}">
                    <i class="fa fa-location-arrow text-green"></i>
                    <span>地域列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,61,71,81]))
            <li class="treeview {{ $menu_active_of_project_list or '' }}">
                <a href="{{ url('/item/project-list')}}">
                    <i class="fa fa-cube text-green"></i>
                    <span>项目列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88]))
            <li class="treeview {{ $menu_active_of_order_list or '' }}">
                <a href="{{ url('/item/order-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>工单列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <li class="treeview {{ $menu_active_of_delivery_list or '' }}">
                <a href="{{ url('/item/delivery-list')}}">
                    <i class="fa fa-share text-yellow"></i>
                    <span>交付列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <li class="treeview {{ $menu_active_of_distribution_list or '' }} _none">
                <a href="{{ url('/item/distribution-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>分发列表</span>
                </a>
            </li>
            @endif




            {{--财务统计--}}
            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="header _none">财务统计</li>

            <li class="treeview {{ $menu_active_of_finance_daily_list or '' }} _none">
                <a href="{{ url('/finance/daily-list') }}">
                    <i class="fa fa-pie-chart text-orange"></i> <span>财务日报</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header">数据统计</li>

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="treeview {{ $menu_active_of_statistic_delivery_by_client or '' }} _none">
                    <a href="{{ url('/statistic/statistic-delivery-by-client') }}">
                        <i class="fa fa-area-chart text-teal"></i> <span>交付看板(客户)</span>
                    </a>
                </li>
            @endif
            @if(in_array($me->user_type,[0,1,9,11,41,61,66,81,84]))
                <li class="treeview {{ $menu_active_of_statistic_delivery or '' }} _none">
                    <a href="{{ url('/statistic/statistic-delivery') }}">
                        <i class="fa fa-area-chart text-teal"></i> <span>交付看板</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66]))
            <li class="treeview {{ $menu_active_of_statistic_project or '' }} _none">
                <a href="{{ url('/statistic/statistic-project') }}">
                    <i class="fa fa-area-chart text-teal"></i> <span>项目看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="treeview {{ $menu_active_of_statistic_department or '' }} _none">
                <a href="{{ url('/statistic/statistic-department') }}">
                    <i class="fa fa-area-chart text-teal"></i> <span>部门看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
            <li class="treeview {{ $menu_active_of_statistic_customer_service or '' }} _none">
                <a href="{{ url('/statistic/statistic-customer-service') }}">
                    <i class="fa fa-bar-chart text-maroon"></i> <span>客服看板</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
            <li class="treeview {{ $menu_active_of_statistic_rank or '' }} _none">
                <a href="{{ url('/statistic/statistic-rank') }}">
                    <i class="fa fa-line-chart text-maroon"></i> <span>客服排名</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,41,81,84]))
                <li class="treeview {{ $menu_active_of_statistic_recent or '' }} _none">
                    <a href="{{ url('/statistic/statistic-recent') }}">
                        <i class="fa fa-area-chart text-maroon"></i> <span>近期成果</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,61,71]))
                <li class="treeview {{ $menu_active_of_statistic_inspector or '' }} _none">
                    <a href="{{ url('/statistic/statistic-inspector') }}">
                        <i class="fa fa-bar-chart text-purple"></i> <span>质检看板</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61]))
                <li class="treeview {{ $menu_active_of_statistic_deliverer or '' }} _none">
                    <a href="{{ url('/statistic/statistic-deliverer') }}">
                        <i class="fa fa-bar-chart text-blue"></i> <span>运营看板</span>
                    </a>
                </li>
            @endif

            <li class="treeview {{ $menu_active_of_statistic_index or '' }}">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>数据统计</span>
                </a>
            </li>

            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
{{--            @if($me->department_district_id == 0)--}}
            <li class="treeview {{ $menu_active_of_statistic_export or '' }}">
                <a href="{{ url('/statistic/statistic-export') }}">
                    <i class="fa fa-download text-default"></i> <span>数据导出</span>
                </a>
            </li>
{{--            @endif--}}
            @endif




            @if(in_array($me->user_type,[0,1,9,11]))
            {{--数据统计--}}
            <li class="header">记录</li>

            <li class="treeview {{ $menu_active_of_record_visit_list or '' }}">
                <a href="{{ url('/record/visit-list') }}">
                    <i class="fa fa-search text-default"></i> <span>访问记录</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_record_operation_list or '' }} _none">
                <a href="{{ url('/record/operation-list') }}">
                    <i class="fa fa-search text-default"></i> <span>操作记录</span>
                </a>
            </li>
            @endif



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>