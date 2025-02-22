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


            {{--部门管理--}}
            @if(in_array($me->user_type,[0,1,9,11]))
            <li class="header">人事管理</li>

            <li class="treeview {{ $menu_active_of_department_list_for_all or '' }}">
                <a href="{{ url('/department/department-list') }}">
                    <i class="fa fa-columns text-red"></i>
                    <span>部门列表</span>
                </a>
            </li>

            <li class="treeview {{ $menu_active_of_staff_list_for_all or '' }}">
                <a href="{{ url('/user/staff-list') }}">
                    <i class="fa fa-user text-red"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif





            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
                <li class="header">话单管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
            <li class="treeview {{ $menu_active_of_item_telephone_list or '' }}">
                <a href="{{ url('/item/telephone-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>话单列表</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_mine_telephone_list or '' }}">
                <a href="{{ url('/mine/telephone-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>已购话单</span>
                </a>
            </li>
            @endif





            @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
            <li class="header">线索管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11]) || (in_array($me->user_type,[81,84,88]) && $me->customer_er->is_staff_take == 1))
            <li class="treeview {{ $menu_active_of_item_clue_list or '' }}">
                <a href="{{ url('/item/clue-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>线索公海</span>
                </a>
            </li>
            @endif
            @if(in_array($me->user_type,[0,1,9,11]) || (in_array($me->user_type,[81,84,88]) && $me->customer_er->is_staff_take == 1))
            @if($me->customer_er->is_preferential == 1)
            <li class="treeview {{ $menu_active_of_item_clue_list_for_preferential or '' }} menu-of-clue-preferential">
                <a href="{{ url('/item/clue-list-for-preferential')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>优选线索</span>
                </a>
            </li>
            @endif
            @endif
            @if(in_array($me->user_type,[0,1,9,11,81,84,88]))
            <li class="treeview {{ $menu_active_of_mine_clue_list or '' }}">
                <a href="{{ url('/mine/clue-list')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>我的线索</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header _none">数据统计</li>

            <li class="treeview {{ $menu_active_of_statistic_delivery_by_daily or '' }} _none">
                <a href="{{ url('/statistic/statistic-delivery-by-daily') }}">
                    <i class="fa fa-bar-chart text-green"></i> <span>交付日报</span>
                </a>
            </li>

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




            @if(in_array($me->user_type, [1,11]))
            {{--财务统计--}}
            <li class="header">财务统计</li>

            <li class="treeview {{ $menu_active_of_finance_funds_recharge_list or '' }}">
                <a href="{{ url('/finance/funds-recharge-list') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>充值记录</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_finance_funds_using_list or '' }}">
                <a href="{{ url('/finance/funds-using-list') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>消费记录</span>
                </a>
            </li>
            <li class="treeview {{ $menu_active_of_finance_daily_list or '' }}">
                <a href="{{ url('/finance/finance-daily-list') }}">
                    <i class="fa fa-pie-chart text-green"></i> <span>财务日报</span>
                </a>
            </li>
            @endif



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>