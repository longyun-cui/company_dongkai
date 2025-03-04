<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{url('/')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>录</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>录单系统</b></span>
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
                            @yield('title')
                            <span class="sr-only">@yield('title')</span>
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


                {{--<!-- Add Menu -->--}}
                <li class="dropdown tasks-menu add-menu _none">
                    <!-- Menu toggle button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i>
                    </a>
                    <ul class="dropdown-menu">


                        {{--客户管理--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header">客户</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header">
                                <a href="{{ url('/user/client-create') }}">
                                    <i class="fa fa-plus text-red"></i> 添加客户
                                </a>
                            </li>
                        @endif


                        {{--部门管理--}}
                        @if(in_array($me->user_type,[0,1,9,11,41]))
                        <li class="header">部门</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41]))
                        <li class="header">
                            <a href="{{ url('/department/department-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加部门
                            </a>
                        </li>
                        @endif


                        {{--员工管理--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
                            <li class="header">员工</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,61,41,81]))
                        <li class="header">
                            <a href="{{ url('/user/staff-create') }}">
                                <i class="fa fa-plus text-red"></i> 添加员工
                            </a>
                        </li>
                        @endif


                        {{--业务管理--}}
                        @if(in_array($me->user_type,[0,1,9,11,61,84,88]))
                        <li class="header">业务</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,61]))
                            <li class="header">
                                <a href="{{ url('/district/district-create') }}">
                                    <i class="fa fa-plus text-blue"></i> 添加地域
                                </a>
                            </li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,61]))
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

{{--                        @if(env('APP_ENV') == 'local' && in_array($me->user_type,[0,1,9]))--}}
                        <li class="header">
                            <a href="{{ url('/item/order-import') }}">
                                <i class="fa fa-file-excel-o text-yellow"></i> 导入工单
                            </a>
                        </li>
{{--                        @endif--}}

                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>




                {{--统计--}}
                <li class="dropdown tasks-menu add-menu _none-">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        统计 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu right" role="menu">



                        <li class="header" style="margin-bottom:9px;">统计</li>

                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-delivery-by-client') }}">
                                <i class="fa fa-area-chart text-teal"></i> <span>交付(客户)看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41,61,66,81,84]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-delivery') }}">
                                <i class="fa fa-area-chart text-teal"></i> <span>交付(项目)看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-project') }}">
                                <i class="fa fa-area-chart text-teal"></i> <span>项目看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-department') }}">
                                <i class="fa fa-area-chart text-teal"></i> <span>部门看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-customer-service') }}">
                                <i class="fa fa-bar-chart text-maroon"></i> <span>客服看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-rank') }}">
                                <i class="fa fa-line-chart text-maroon"></i> <span>客服排名</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,61,41,81,84]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-recent') }}">
                                <i class="fa fa-area-chart text-maroon"></i> <span>近期成果</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,41,61,71]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-inspector') }}">
                                <i class="fa fa-bar-chart text-purple"></i> <span>质检看板</span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11,61]))
                        <li>
                            <a target="_blank" href="{{ url('/statistic/statistic-deliverer') }}">
                                <i class="fa fa-bar-chart text-blue"></i> <span>运营看板</span>
                            </a>
                        </li>
                        @endif


                        {{--财务统计--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="divider"></li>
                            <li class="header" style="margin-bottom:9px;">销售统计</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li>
                                <a href="{{ url('/statistic/statistic-company-overview') }}">
                                    <i class="fa fa-pie-chart text-orange"></i> <span>公司概览</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endif
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li>
                                <a href="{{ url('/statistic/statistic-company-daily') }}">
                                    <i class="fa fa-pie-chart text-orange"></i> <span>公司日报</span>
                                </a>
                            </li>
                        @endif


                        {{--财务统计--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="divider"></li>
                            <li class="header" style="margin-bottom:9px;">财务统计</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header">
                                <a href="{{ url('/finance/daily-list') }}">
                                    <i class="fa fa-pie-chart text-orange"></i> <span>财务日报</span>
                                </a>
                            </li>
                        @endif



                    </ul>
                </li>



                {{--生产统计--}}
                @if(in_array($me->user_type,[0,1,9,11,61,66]))
                    <li class="dropdown tasks-menu add-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            生产统计 <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu right" role="menu">


                            <li class="divider"></li>

                            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-caller-overview"
                                       data-title='<i class="fa fa-bar-chart text-orange"></i> 坐席统计'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-caller-overview"
                                       data-datatable-target="statistic-caller-overview"
                                       data-datatable-clone-object="statistic-caller-overview-clone"
                                    >
                                        <i class="fa fa-bar-chart text-orange"></i> <span>坐席统计</span>
                                    </a>
                                </li>
                                <li class="divider"></li>
                            @endif
                            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-caller-rank"
                                       data-title='<i class="fa fa-line-chart text-orange"></i> 坐席排名'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-caller-rank"
                                       data-datatable-target="statistic-caller-rank"
                                       data-datatable-clone-object="statistic-caller-rank-clone"
                                    >
                                        <i class="fa fa-line-chart text-orange"></i> <span>坐席排名</span>
                                    </a>
                                </li>
                                <li class="divider"></li>
                            @endif
                            @if(in_array($me->user_type,[0,1,9,11,41,81,84]))
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-caller-recent"
                                       data-title='<i class="fa fa-bar-chart text-orange"></i> 近期成果'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-caller-recent"
                                       data-datatable-target="statistic-caller-recent"
                                       data-datatable-clone-object="statistic-caller-recent-clone"
                                    >
                                        <i class="fa fa-area-chart text-orange"></i> <span>近期成果</span>
                                    </a>
                                </li>
                            @endif
                            @if(in_array($me->user_type,[0,1,9,11,41,61,71]))
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-inspector-overview"
                                       data-title='<i class="fa fa-pie-chart text-orange"></i> 质检统计'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-inspector-overview"
                                       data-datatable-target="statistic-inspector-overview"
                                       data-datatable-clone-object="statistic-inspector-overview-clone"
                                    >
                                        <i class="fa fa-pie-chart text-purple"></i> <span>质检统计</span>
                                    </a>
                                </li>
                                <li class="divider"></li>
                            @endif

                            @if(in_array($me->user_type,[0,1,9,11,61]))
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-deliverer-overview"
                                       data-title='<i class="fa fa-bar-chart text-orange"></i> 运营看板'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-deliverer-overview"
                                       data-datatable-target="statistic-deliverer-overview"
                                       data-datatable-clone-object="statistic-deliverer-overview-clone"
                                    >
                                        <i class="fa fa-bar-chart text-red"></i> <span>运营统计</span>
                                    </a>
                                </li>
                                <li class="divider"></li>
                            @endif

                            @if(in_array($me->user_type,[0,1,9,11,41,81,84,71,77,61,66]))
                                <li>
                                    <a href="javascript:void(0);" class="tab-control datatable-control"
                                       data-type="create"
                                       data-unique="y"
                                       data-id="statistic-production-project"
                                       data-title='<i class="fa fa-area-chart text-teal"></i> <span>项目看板</span>'
                                       data-content=''

                                       data-datatable-type="create"
                                       data-datatable-unique="y"
                                       data-datatable-id="datatable-statistic-production-project"
                                       data-datatable-target="statistic-production-project"
                                       data-datatable-clone-object="statistic-production-project-clone"
                                    >
                                        <i class="fa fa-area-chart text-teal"></i> <span>项目看板</span>
                                    </a>
                                </li>
                                <li class="divider"></li>
                            @endif




                        </ul>
                    </li>
                @endif



                {{--销售统计--}}
                @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="dropdown tasks-menu add-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        销售统计 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu right" role="menu">


                        <li class="divider"></li>
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li>
                                <a href="javascript:void(0);" class="tab-control datatable-control"
                                   data-type="create"
                                   data-unique="y"
                                   data-id="statistic-company-overview"
                                   data-title='<i class="fa fa-pie-chart text-orange"></i> 公司概览'
                                   data-content=''

                                   data-datatable-type="create"
                                   data-datatable-unique="y"
                                   data-datatable-id="datatable-statistic-company-overview"
                                   data-datatable-target="statistic-company-overview"
                                   data-datatable-clone-object="statistic-company-overview-clone"
                                >
                                    <i class="fa fa-pie-chart text-orange"></i> <span>公司概览</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endif
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li>
                                <a href="javascript:void(0);" class="tab-control datatable-control"
                                   data-type="create"
                                   data-unique="y"
                                   data-id="statistic-company-daily"
                                   data-title='<i class="fa fa-bar-chart text-orange"></i> 公司日报'
                                   data-content=''

                                   data-datatable-type="create"
                                   data-datatable-unique="y"
                                   data-datatable-id="datatable-statistic-company-daily"
                                   data-datatable-target="statistic-company-daily"
                                   data-datatable-clone-object="statistic-company-daily-clone"

                                   data-chart-id="eChart-statistic-company-daily"
                                >
                                    <i class="fa fa-bar-chart text-orange"></i> <span>公司日报</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endif
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li>
                                <a href="javascript:void(0);" class="tab-control datatable-control"
                                   data-type="create"
                                   data-unique="y"
                                   data-id="statistic-marketing-client"
                                   data-title='<i class="fa fa-bar-chart text-orange"></i> 客户交付'
                                   data-content=''

                                   data-datatable-type="create"
                                   data-datatable-unique="y"
                                   data-datatable-id="datatable-statistic-marketing-client"
                                   data-datatable-target="statistic-marketing-client"
                                   data-datatable-clone-object="statistic-marketing-client-clone"
                                >
                                    <i class="fa fa-area-chart text-orange"></i> <span>客户交付</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endif
                        @if(in_array($me->user_type,[0,1,9,11,61,66]))
                            <li>
                                <a href="javascript:void(0);" class="tab-control datatable-control"
                                   data-type="create"
                                   data-unique="y"
                                   data-id="statistic-marketing-project"
                                   data-title='<i class="fa fa-bar-chart text-orange"></i> 项目交付'
                                   data-content=''

                                   data-datatable-type="create"
                                   data-datatable-unique="y"
                                   data-datatable-id="datatable-statistic-marketing-project"
                                   data-datatable-target="statistic-marketing-project"
                                   data-datatable-clone-object="statistic-marketing-project-clone"
                                >
                                    <i class="fa fa-area-chart text-orange"></i> <span>项目交付</span>
                                </a>
                            </li>
                        @endif


                        {{--财务统计--}}
                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="divider _none"></li>
                            <li class="header _none" style="margin-bottom:9px;">财务统计</li>
                        @endif

                        @if(in_array($me->user_type,[0,1,9,11]))
                            <li class="header _none">
                                <a href="{{ url('/finance/daily-list') }}">
                                    <i class="fa fa-pie-chart text-orange"></i> <span>财务日报</span>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endif



                    </ul>
                </li>
                @endif




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
                <li class="dropdown notifications-menu">
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
                            <img src="{{ url(env('DOMAIN_CDN').'/'.$me->portrait_img) }}" class="user-image" alt="User">
                        @else
                            <img src="/AdminLTE/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        @endif
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ $me->true_name or '' }}</span>
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