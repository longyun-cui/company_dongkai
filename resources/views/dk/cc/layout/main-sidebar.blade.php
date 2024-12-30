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


            {{--公司管理--}}
            @if(in_array($me->user_type,[0,1,9,11,41]))
            <li class="header">公司管理</li>

            <li class="treeview {{ $menu_active_of_company_team_list or '' }}">
                <a href="{{ url('/company/team-list') }}">
                    <i class="fa fa-columns text-light-blue"></i>
                    <span>团队列表</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
            <li class="treeview {{ $menu_active_of_company_staff_list or '' }}">
                <a href="{{ url('/company/staff-list') }}">
                    <i class="fa fa-user text-light-blue"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif




            {{--客户管理--}}
            @if(in_array($me->user_type,[0,1,9,11,61]))
                <li class="header">客户管理</li>

                <li class="treeview {{ $menu_active_of_client_list_for_all or '' }}">
                    <a href="{{ url('/client/client-list') }}">
                        <i class="fa fa-user-secret text-red"></i>
                        <span>客户列表</span>
                    </a>
                </li>
            @endif




            @if(in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61]))
            <li class="treeview {{ $menu_active_of_service_location_list or '' }} _none">
                <a href="{{ url('/service/location-list')}}">
                    <i class="fa text-green fa-location-arrow"></i>
                    <span>地域管理</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,41,61,71,81]))
            <li class="treeview {{ $menu_active_of_service_project_list or '' }} _none">
                <a href="{{ url('/service/project-list')}}">
                    <i class="fa text-green fa-cube"></i>
                    <span>项目管理</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <li class="treeview {{ $menu_active_of_service_telephone_list or '' }}">
                <a href="{{ url('/service/telephone-list')}}">
                    <i class="fa fa-file-text text-green"></i>
                    <span>电话数据</span>
                </a>
            </li>
            @endif
            @if(in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88]))
            <li class="treeview {{ $menu_active_of_service_task_list or '' }}">
                <a href="{{ url('/service/task-list')}}">
                    <i class="fa fa-file-text text-green"></i>
                    <span>任务管理</span>
                </a>
            </li>
            @endif





            @if(in_array($me->user_type,[0,1,9,11,41,61,66,71,77,81,84,88]))
                <li class="header">外呼管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="treeview {{ $menu_active_of_call_record_list or '' }}">
                    <a href="{{ url('/service/call-record-list')}}">
                        <i class="fa fa-file-text text-yellow"></i>
                        <span>通话记录</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="treeview {{ $menu_active_of_call_statistic_list or '' }}">
                    <a href="{{ url('/service/call-statistic-list')}}">
                        <i class="fa fa-file-text text-yellow"></i>
                        <span>统计列表</span>
                    </a>
                </li>
            @endif




            @if(in_array($me->user_type,[0,1,9,11]))
            {{--财务统计--}}
            <li class="header _none">财务统计</li>

            <li class="treeview {{ $menu_active_of_finance_daily_list or '' }} _none">
                <a href="{{ url('/finance/daily-list') }}">
                    <i class="fa fa-pie-chart text-orange"></i> <span>财务日报</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header _none">数据统计</li>


            <li class="treeview {{ $menu_active_of_statistic_index or '' }} _none">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>数据统计</span>
                </a>
            </li>

            @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
{{--            @if($me->department_district_id == 0)--}}
            <li class="treeview {{ $menu_active_of_statistic_export or '' }} _none">
                <a href="{{ url('/statistic/statistic-export') }}">
                    <i class="fa fa-download text-default"></i> <span>数据导出</span>
                </a>
            </li>
            @endif




            @if(in_array($me->user_type,[0,1,9,11]))
            {{--数据统计--}}
            <li class="header _none">记录</li>

            <li class="treeview {{ $menu_active_of_record_visit_list or '' }} _none">
                <a href="{{ url('/record/visit-list') }}">
                    <i class="fa fa-download text-default"></i> <span>访问记录</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_record_operation_list or '' }} _none">
                <a href="{{ url('/record/operation-list') }}">
                    <i class="fa fa-download text-default"></i> <span>操作记录</span>
                </a>
            </li>
            @endif



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>