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
                <p>管理员</p>
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
        <ul class="sidebar-menu tree"data-widget="tree">

            {{--机构基本信息--}}
            <li class="header">header</li>
            <!-- Optionally, you can add icons to the links -->


            <li class="treeview active">
                <a href=""><i class="fa fa-th"></i> <span>GPS</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/gps/navigation') }}"><i class="fa fa-circle-o text-blue"></i>Navigation</a></li>
                    <li><a href="{{ url('/gps/test-list') }}"><i class="fa fa-circle-o text-blue"></i>Test</a></li>
                    <li><a href="{{ url('/gps/tool-list') }}"><i class="fa fa-circle-o text-blue"></i>Tool</a></li>
                    <li><a href="{{ url('/gps/template-list') }}"><i class="fa fa-circle-o text-blue"></i>Template</a></li>
                </ul>
            </li>




            {{--UI--}}
            <li class="header">UI</li>

            <li class="treeview">
                <a target="_blank" href="{{ url('/gps/UI/index') }}">
                    <i class="fa fa-cube text-red"></i><span>Index</span>
                </a>
            </li>
            <li class="treeview">
                <a target="_blank" href="{{ url('/gps/UI/item-list') }}">
                    <i class="fa fa-cube text-red"></i><span>Item</span>
                </a>
            </li>




            {{--Developing--}}
            <li class="header">Developing</li>

            <li class="treeview">
                <a target="_blank" href="{{ url('/admin') }}">
                    <i class="fa fa-cube text-red"></i><span>Admin</span>
                </a>
            </li>
            <li class="treeview">
                <a target="_blank" href="{{ url('/developing') }}">
                    <i class="fa fa-cube text-red"></i><span>Developing</span>
                </a>
            </li>



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>