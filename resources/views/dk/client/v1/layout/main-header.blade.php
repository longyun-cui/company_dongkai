<!-- Main Header -->
<header class="main-header">


    <input type="hidden" name="recording-speed" value="1.25" readonly>

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
                        <b>
                            {{ $me->client_er->name or '' }}
{{--                            <span class="sr-only">@yield('title')</span>--}}
                        </b>
                        @yield('title-2')
                        @yield('title-3')
                    </a>
                </li>
            </ul>
        </div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">


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
                        <span class="hidden-xs">{{ $me->name or '' }}</span>
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
                                {{ $me->name or '' }} <br><br>

                                @if($me->staff_position == 0)
                                @elseif($me->staff_position == 31) 部门总监
                                @elseif($me->staff_position == 41) 团队经理
                                @elseif($me->staff_position == 61) 小组主管
                                @endif

                                @if($me->staff_category == 0)
                                    <small>Super</small>
                                @elseif($me->staff_category == 1)
                                    <small>Boss</small>
                                @elseif($me->staff_category == 41)
                                    <small>客服部</small>
                                @elseif($me->staff_category == 51)
                                    <small>质检部</small>
                                @elseif($me->staff_category == 61)
                                    <small>复核部</small>
                                @elseif($me->staff_category == 71)
                                    <small>运营部</small>
                                @endif
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row _none">
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

                                {{--<a href="{{ url('/my-account/my-profile-info-index') }}" class="btn btn-default btn-flat">个人资料</a>--}}
                                <a href="{{ url('/my-account/my-password-change') }}" class="btn btn-default btn-flat">修改密码</a>

{{--                                <a class="btn btn-default btn-flat tab-control"--}}
{{--                                   data-type="create"--}}
{{--                                   data-unique="y"--}}
{{--                                   data-id="my-profile-info-index"--}}
{{--                                   data-title='个人资料'--}}
{{--                                   data-content=''--}}
{{--                                >--}}
{{--                                    <i class="fa fa-user text-red _none"></i>--}}
{{--                                    <span>修改密码</span>--}}
{{--                                </a>--}}

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