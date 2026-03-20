{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">


            {{--团队列表--}}
            @if(in_array($me->staff_position,[0,1,9]))
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="team-list"
                   data-title='团队列表'
                   data-content='<i class="fa fa-columns text-red"></i> <span>团队列表</span>'
                   data-icon='<i class="fa fa-sitemap text-white"></i>'

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-team-list"
                   data-datatable-target="team-list"
                   data-datatable-clone-object="team-list-clone"
                >
                    <i class="fa fa-sitemap text-white"></i>
                    <span>团队列表</span>
                </a>
            </li>
            @endif
            {{--员工列表--}}
            @if(in_array($me->staff_position,[0,1,9]))
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="staff-list"
                   data-title='员工列表'
                   data-content='<i class="fa fa-user text-white"></i> 员工列表'
                   data-icon='<i class="fa fa-user text-blue"></i>'

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-staff-list"
                   data-datatable-target="staff-list"
                   data-datatable-clone-object="staff-list-clone"
                >
                    <i class="fa fa-user text-white"></i>
                    <span>员工列表</span>
                </a>
            </li>
            @endif

            {{--联系渠道--}}
            @if($me->client_er->client_category == 31)
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="contact-list"
                   data-title='<i class="fa fa-chain text-red"></i> <span>联系渠道</span>'
                   data-content="联系渠道"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-contact-list"
                   data-datatable-target="contact-list"
                   data-datatable-clone-object="contact-list-clone"
                >
                    <i class="fa fa-chain text-red"></i>
                    <span>联系渠道</span>
                </a>
            </li>
            @endif


            {{--交付列表--}}
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="delivery-list"
                   data-title='<i class="fa fa-file-text text-yellow"></i> <span>交付列表</span>'
                   data-content="交付列表"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-delivery-list"
                   data-datatable-target="delivery-list"
                   data-datatable-clone-object="delivery-list-clone"
                >
                    <i class="fa fa-file-text text-yellow"></i>
                    <span>交付列表</span>
                </a>
            </li>
            {{--交付日报--}}
            @if(in_array($me->staff_position,[0,1,9]))
                <li class="treeview">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="delivery-daily"
                       data-title='<i class="fa fa-bar-chart text-yellow"></i> <span>交付日报</span>'
                       data-content="交付日报"

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-delivery-daily"
                       data-datatable-target="delivery-daily"
                       data-datatable-clone-object="delivery-daily-clone"

                       data-chart-id="eChart-delivery-daily"
                    >
                        <i class="fa fa-bar-chart text-yellow"></i>
                        <span>交付日报</span>
                    </a>
                </li>
            @endif
            {{--成交记录--}}
            @if(in_array($me->staff_position,[0,1,9]))
            <li class="treeview _none">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="trade-list"
                   data-title='<i class="fa fa-cny text-aqua"></i> <span>成交记录</span>'
                   data-content="成交记录"

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-trade-list"
                   data-datatable-target="trade-list"
                   data-datatable-clone-object="datatable-trade-list-clone"
                >
                    <i class="fa fa-cny text-aqua"></i>
                    <span>成交记录</span>
                </a>
            </li>
            @endif



        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>