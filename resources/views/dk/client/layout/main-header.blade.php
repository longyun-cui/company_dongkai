<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{url('/')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>客</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>客户系统</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active-">
                    <a href="javascript:void(0);" style="color:#fff;">
                        <b class="nav-header-title">
                            @yield('title')
                            <span class="sr-only">@yield('title')</span>
                        </b>
                        <span class="nav-header-title-2">@yield('title-2')</span>
                        <span class="nav-header-title-3">@yield('title-3')</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <!-- Add Menu -->
                <li class="dropdown tasks-menu add-menu _none">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i>
                    </a>
                    <ul class="dropdown-menu">

                        {{--部门管理--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">部门</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">
                            <a href="{{ url('/department/department-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加部门
                            </a>
                        </li>
                        @endif


                        {{--员工管理--}}
                        @if(in_array($me->user_type,[0,1,9,11,81]))
                            <li class="header">员工</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,81]))
                        <li class="header">
                            <a href="{{ url('/user/staff-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加员工
                            </a>
                        </li>
                        @endif


                        {{--业务管理--}}
                        @if(in_array($me->user_type,[0,1,9,11,84,88]))
                        <li class="header">业务</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">
                            <a href="{{ url('/item/project-create') }}">
                                <i class="fa fa-plus text-yellow"></i> 添加项目
                            </a>
                        </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,84,88]))
                        <li class="header">
                            <a href="{{ url('/item/order-create') }}">
                                <i class="fa fa-plus text-yellow"></i> 添加工单
                            </a>
                        </li>
                        @endif

                        @if(env('APP_ENV') == 'local' && in_array($me->user_type,[0,1,9]))
                        <li class="header">
                            <a href="{{ url('/item/order-import') }}">
                                <i class="fa fa-file-excel-o text-yellow"></i> 导入工单
                            </a>
                        </li>
                        @endif

                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>

                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu _none">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 4 messages</li>
                        <li>
                            <!-- inner menu: contains the messages -->
                            <ul class="menu">
                                <li><!-- start message -->
                                    <a href="#">
                                        <div class="pull-left">
                                            <!-- User Image -->
                                            <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <!-- Message title and timestamp -->
                                        <h4>
                                            Support Team
                                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                        </h4>
                                        <!-- The message -->
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <!-- end message -->
                            </ul>
                            <!-- /.menu -->
                        </li>
                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>
                <!-- /.messages-menu -->

                <!-- Notifications Menu -->
                <li class="dropdown notifications-menu _none">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li>
                            <!-- Inner Menu: contains the notifications -->
                            <ul class="menu">
                                <li><!-- start notification -->
                                    <a href="#">
                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                    </a>
                                </li>
                                <!-- end notification -->
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li>

                <!-- Tasks Menu -->
                <li class="dropdown tasks-menu _none">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-danger">9</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 9 tasks</li>
                        <li>
                            <!-- Inner menu: contains the tasks -->
                            <ul class="menu">
                                <li><!-- Task item -->
                                    <a href="#">
                                        <!-- Task title and progress text -->
                                        <h3>
                                            Design some buttons
                                            <small class="pull-right">20%</small>
                                        </h3>
                                        <!-- The progress bar -->
                                        <div class="progress xs">
                                            <!-- Change the css width attribute to simulate progress -->
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <!-- end task item -->
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all tasks</a>
                        </li>
                    </ul>
                </li>



                <li class="dropdown- notifications-alert">
                    <!-- Menu toggle button -->
                    <a href="javascript:void(0);" class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="delivery-list"
                       data-title="交付列表"
                       data-content="交付列表"

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-delivery-list"
                       data-datatable-target="delivery-list"
                       data-datatable-clone-object="delivery-list-clone"
                       data-datatable-reload="y"
                    >
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-danger notification-dom" style="width:16px;border-radius:50%;color:#dd4b39 !important;display: none;">•</span>
                    </a>
                </li>

                {{--对账系统--}}
                @if(in_array($me->user_type,[0,1,9,11]) && $me->client_er->is_reconciliation == 1)
                <li class="dropdown- notifications-alert">
                    <!-- Menu toggle button -->
                    <a target="_blank" href="{{ url('/reconciliation') }}">
                        <i class="fa fa-bar-chart text-yellow"></i> <span>对账系统</span>
                        <span class="label label-danger notification-dom" style="width:16px;border-radius:50%;color:#dd4b39 !important;display: none;">•</span>
                    </a>
                </li>
                @endif

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        @if(!empty($me->portrait_img))
                            <img src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" class="user-image" alt="User">
                        @else
                            <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        @endif
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ $me->username or '' }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header" data-id="{{ $me->id or '0' }}" data-client-id="{{ $me->client_id or '0' }}">
                            @if(!empty($me->portrait_img))
                                <img src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" class="user-image" alt="User">
                            @else
                                <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            @endif

                            <p>
                                {{ $me->client_er->username or '' }}
                                <small>{{ $me->username or '' }}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ url('/my-account/my-profile-info-index') }}" class="btn btn-default btn-flat">个人资料</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">退出</a>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- Control Sidebar Toggle Button -->
                <li class="_none">
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>