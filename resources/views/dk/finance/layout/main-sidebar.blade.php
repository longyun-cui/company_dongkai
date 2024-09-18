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


            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11]))
                <li class="header">用户管理</li>

                <li class="treeview {{ $menu_active_of_user_list or '' }}">
                    <a href="{{ url('/user/user-list') }}">
                        <i class="fa fa-user text-red"></i>
                        <span>用户管理</span>
                    </a>
                </li>
            @endif


            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11,31]))
            <li class="header">管理</li>

            <li class="treeview {{ $menu_active_of_company_list or '' }}">
                <a href="{{ url('/company/company-list') }}">
                    <i class="fa fa-columns text-green"></i>
                    <span>公司与渠道</span>
                </a>
            </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31]))
                <li class="treeview {{ $menu_active_of_recharge_list or '' }} _none">
                    <a href="{{ url('/finance/recharge-list')}}">
                        <i class="fa fa-cny text-yellow"></i>
                        <span>充值记录</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31]))
                <li class="treeview {{ $menu_active_of_project_list or '' }}">
                    <a href="{{ url('/item/project-list')}}">
                        <i class="fa fa-cube text-green"></i>
                        <span>项目列表</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31]))
                <li class="treeview {{ $menu_active_of_settle_list or '' }} _none">
                    <a href="{{ url('/finance/settle-list')}}">
                        <i class="fa fa-cny text-yellow"></i>
                        <span>结算记录</span>
                    </a>
                </li>
            @endif




            @if(in_array($me->user_type,[0,1,9,11,31,41]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31,41]))
            <li class="treeview {{ $menu_active_of_daily_list or '' }}">
                <a href="{{ url('/item/daily-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>日报列表</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header">数据统计</li>

            @if(in_array($me->user_type,[0,1,9,11,31,41]))
                <li class="treeview {{ $menu_active_of_statistic_project or '' }}">
                    <a href="{{ url('/statistic/statistic-project') }}">
                        <i class="fa fa-bar-chart text-aqua"></i> <span>项目报表</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31,41]))
                <li class="treeview {{ $menu_active_of_statistic_company or '' }}">
                    <a href="{{ url('/statistic/statistic-company') }}">
                        <i class="fa fa-bar-chart text-aqua"></i> <span>公司&渠道报表</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,31]))
            <li class="treeview {{ $menu_active_of_statistic_channel or '' }} _none">
                <a href="{{ url('/statistic/statistic-channel') }}">
                    <i class="fa fa-bar-chart text-aqua"></i> <span>渠道报表</span>
                </a>
            </li>
            @endif

            <li class="treeview {{ $menu_active_of_statistic_index or '' }} _none">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-area-chart text-aqua"></i> <span>数据统计</span>
                </a>
            </li>

            <li class="treeview {{ $menu_active_of_statistic_index or '' }} _none">
                <a href="{{ url('/statistic/statistic-index') }}">
                    <i class="fa fa-pie-chart text-aqua"></i> <span>数据统计</span>
                </a>
            </li>



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>