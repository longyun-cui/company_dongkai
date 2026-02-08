{{--<!-- Left side column. contains the logo and sidebar -->--}}
<aside class="main-sidebar">

    {{--<!-- sidebar: style can be found in sidebar.less -->--}}
    <section class="sidebar">

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">


            {{--公司列表--}}
            @if(in_array($me->staff_type,[0,1,9,11]))
                <li class="treeview">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="company-list"
                       data-title='公司列表'
                       data-content='<i class="fa fa-copyright text-white"></i> 公司列表'
                       data-icon='<i class="fa fa-copyright text-blue"></i>'

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-company-list"
                       data-datatable-target="company-list"
                       data-datatable-clone-object="company-list-clone"
                    >
                        <i class="fa fa-copyright text-white"></i>
                        <span>公司列表</span>
                    </a>
                </li>
            @endif
            {{--部门列表--}}
            @if(in_array($me->staff_type,[0,1,9,11]))
                <li class="treeview">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="department-list"
                       data-title='部门列表'
                       data-content='<i class="fa fa-columns text-white"></i> 部门列表'
                       data-icon='<i class="fa fa-columns text-blue"></i>'

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-department-list"
                       data-datatable-target="department-list"
                       data-datatable-clone-object="department-list-clone"
                    >
                        <i class="fa fa-columns text-white"></i>
                        <span>部门列表</span>
                    </a>
                </li>
            @endif
            {{--团队列表--}}
            @if(in_array($me->staff_type,[0,1,9,11]))
                <li class="treeview">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="team-list"
                       data-title='团队列表'
                       data-content='<i class="fa fa-sitemap text-white"></i> 团队列表'
                       data-icon='<i class="fa fa-sitemap text-blue"></i>'

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
            @if(in_array($me->staff_type,[0,1,9,11,81,84]))
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




            {{--客户列表--}}
            @if(in_array($me->staff_type,[0,1,9,11,61]))
                <li class="treeview _none-">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="client-list"
                       data-title='客户列表'
                       data-content='<i class="fa fa-user-secret text-white"></i> 客户列表'
                       data-icon='<i class="fa fa-user-secret text-blue"></i>'

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-client-list"
                       data-datatable-target="client-list"
                       data-datatable-clone-object="client-list-clone"
                    >
                        <i class="fa fa-user-secret text-white"></i>
                        <span>客户列表</span>
                    </a>
                </li>
            @endif


            {{--项目列表--}}
            @if(in_array($me->staff_type,[0,1,9,11,41,61,71,81]))
                <li class="treeview _none-">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="project-list"
                       data-title='项目列表'
                       data-content='<i class="fa fa-cube text-white"></i> 项目列表'
                       data-icon='<i class="fa fa-cube text-blue"></i>'

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-project-list"
                       data-datatable-target="project-list"
                       data-datatable-clone-object="project-list-clone"
                    >
                        <i class="fa fa-cube text-white"></i>
                        <span>项目列表</span>
                    </a>
                </li>
            @endif





            {{--口腔工单--}}
            <li class="treeview _none-">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="order-list"
                   data-title='订单'
                   data-content='<i class="fa fa-file-text text-orange"></i> 口腔•工单'
                   data-icon='<i class="fa fa-file-text text-orange"></i>'

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-order-list"
                   data-datatable-target="order-list"
                   data-datatable-clone-object="order-list-clone"
                >
                    <i class="fa fa-file-text text-orange"></i>
                    <span>订单列表</span>
                </a>
            </li>
            {{--交付列表--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="treeview _none-">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="delivery-list"
                       data-title='<i class="fa fa-share text-yellow"></i> 口腔•交付'
                       data-content=''

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-delivery-list"
                       data-datatable-target="delivery-list"
                       data-datatable-clone-object="delivery-list-clone"
                    >
                        <i class="fa fa-share text-yellow"></i>
                        <span>口腔•交付</span>
                    </a>
                </li>
            @endif


            <li class="treeview _none-">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="order-aesthetic-list"
                   data-title='<i class="fa fa-file-text text-red"></i> 医美•工单'
                   data-content=''

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-order-aesthetic-list"
                   data-datatable-target="order-aesthetic-list"
                   data-datatable-clone-object="order-aesthetic-list-clone"
                >
                    <i class="fa fa-file-text text-red"></i>
                    <span>医美•工单</span>
                </a>
            </li>
            {{--交付列表--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66]))
            <li class="treeview">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="delivery-aesthetic-list"
                   data-title='<i class="fa fa-share text-red"></i> 医美•交付'
                   data-content=''

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-delivery-aesthetic-list"
                   data-datatable-target="delivery-aesthetic-list"
                   data-datatable-clone-object="delivery-aesthetic-list-clone"
                >
                    <i class="fa fa-share text-red"></i>
                    <span>医美•交付</span>
                </a>
            </li>
            @endif


            <li class="treeview _none-">
                <a class="tab-control datatable-control"
                   data-type="create"
                   data-unique="y"
                   data-id="order-luxury-list"
                   data-title='<i class="fa fa-file-text text-purple"></i> 奢侈品•工单'
                   data-content=''

                   data-datatable-type="create"
                   data-datatable-unique="y"
                   data-datatable-id="datatable-order-luxury-list"
                   data-datatable-target="order-luxury-list"
                   data-datatable-clone-object="order-luxury-list-clone"
                >
                    <i class="fa fa-file-text text-purple"></i>
                    <span>奢侈品•工单</span>
                </a>
            </li>
            {{--交付列表--}}
            @if(in_array($me->user_type,[0,1,9,11,61,66]))
                <li class="treeview _none-">
                    <a class="tab-control datatable-control"
                       data-type="create"
                       data-unique="y"
                       data-id="delivery-luxury-list"
                       data-title='<i class="fa fa-share text-purple"></i> 奢侈品•交付'
                       data-content=''

                       data-datatable-type="create"
                       data-datatable-unique="y"
                       data-datatable-id="datatable-delivery-luxury-list"
                       data-datatable-target="delivery-luxury-list"
                       data-datatable-clone-object="delivery-luxury-list-clone"
                    >
                        <i class="fa fa-share text-purple"></i>
                        <span>奢侈品•交付</span>
                    </a>
                </li>
            @endif






        </ul>
        <!-- /.sidebar-menu -->
    </section>
    {{--<!-- /.sidebar -->--}}
</aside>