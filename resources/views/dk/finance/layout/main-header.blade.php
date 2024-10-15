<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{url('/')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>F</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>财务系统</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">


                {{--<!-- Add Menu -->--}}
                <li class="dropdown tasks-menu add-menu">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i>
                    </a>
                    <ul class="dropdown-menu">

                        {{--添加--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">添加</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header">
                                <a href="{{ url('/user/user-create') }}">
                                    <i class="fa fa-plus text-red"></i> 添加客户
                                </a>
                            </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">
                            <a href="{{ url('/company/company-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加公司
                            </a>
                        </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li class="header">
                            <a href="{{ url('/item/project-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加项目
                            </a>
                        </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,31]))
                        <li class="header">
                            <a href="{{ url('/item/daily-create') }}">
                                <i class="fa fa-plus text-green"></i> 添加日报
                            </a>
                        </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,31]))
                        <li class="header _none">
                            <a href="{{ url('/item/daily-import') }}">
                                <i class="fa fa-file-excel-o text-blue"></i> 导入日报
                            </a>
                        </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header">
                                <a href="{{ url('/item/settled-create') }}">
                                    <i class="fa fa-plus text-blue"></i> 添加自定义报表
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
                        <i class="fa fa-qrcode"></i>
{{--                        <span class="label label-warning">10</span>--}}
                        <span class="hidden-xs">售后中心</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">
                            <i class="fa fa-user-plus"></i>
                            售后问题，售后中心为你服务
                        </li>
                        <li>
                            <!-- Inner Menu: contains the notifications -->
                            <ul class="menu">
                                <li><!-- start notification -->
                                    <a href="javascript:void(0);">
                                        <img src="/custom/dk/qr_code4.jpg" class="qr-code-image" alt="QR_code Image">
                                    </a>
                                </li>
                                <!-- end notification -->
                            </ul>
                        </li>
                        <li class="footer"><a href="javascript:void(0);"><i class="fa fa-weixin"></i>扫码添加微信</a></li>
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


                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        @if(!empty($me->portrait_img))
                            <img class="user-image" src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" alt="User">
                        @else
                            <img class="user-image" src="/AdminLTE/dist/img/user2-160x160.jpg" alt="User Image">
                        @endif
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ $me->username or '' }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            @if(!empty($me->portrait_img))
                                <img src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" class="user-image" alt="User">
                            @else
                                <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            @endif

                            <p>
                                {{ $me->username or '' }} - Web Developer
                                <small>Member since Nov. 2012</small>
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