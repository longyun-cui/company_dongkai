{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ $me->name or '' }}</p>
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


            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="n"
                   data-id="delivery-list"
                   data-title="客资列表"
                   data-datatable-type="create"
                   data-datatable-unique="n"
                   data-datatable-id="datatable-delivery-list"
                   data-datatable-target="delivery-list"
                   data-datatable-clone-object="delivery-list-clone"
                >
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>工单列表</span>
                </a>
            </li>
            <li class="treeview">
                <a class="tab-control" data-id="department-list" data-title="团队列表" data-content="团队">
                    <i class="fa fa-columns text-aqua"></i>
                    <span>团队列表</span>
                </a>
            </li>
            <li class="treeview">
                <a class="tab-control" data-id="order-list" data-title="工单列表" data-content="gongdan">
                    <i class="fa fa-columns text-aqua"></i>
                    <span>工单列表</span>
                </a>
            </li>




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