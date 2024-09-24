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





            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="header">业务管理</li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
                <li class="treeview {{ $menu_active_of_delivery_list or '' }}">
                    <a href="{{ url('/item/delivery-list')}}">
                        <i class="fa fa-file-text text-yellow"></i>
                        <span>交付列表</span>
                    </a>
                </li>
            @endif

            @if(in_array($me->user_type,[0,1,9,11,71,77,81,84,88]))
            <li class="treeview {{ $menu_active_of_order_list_for_all or '' }} _none">
                <a href="{{ url('/item/order-list-for-all')}}">
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>工单列表</span>
                </a>
            </li>
            @endif




            {{--数据统计--}}
            <li class="header">数据统计</li>

            <li class="treeview {{ $menu_active_of_statistic_index or '' }} _none">
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