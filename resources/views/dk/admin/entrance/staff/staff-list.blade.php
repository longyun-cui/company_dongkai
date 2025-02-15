@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title')
    {{ $title_text or '员工列表' }}
@endsection


@section('title')<span class="box-title">{{ $title_text or '员工列表' }}</span>@endsection
@section('header')<span class="box-title">{{ $title_text or '员工列表' }}</span>@endsection
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-primary">


            <div class="col-md-12 datatable-search-row" id="datatable-search-for-staff-list">


                <div class=" pull-left">

                    @if(in_array($me->user_type,[0,1,9,11,19]))
                        <a href="{{ url('/user/staff-create') }}">
                            <button type="button" onclick="" class="btn btn-success btn-filter"><i class="fa fa-plus"></i> 添加</button>
                        </a>
                    @endif
                    <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-play"></i> 启用</button>
                    <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-stop"></i> 禁用</button>
                    <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-download"></i> 导出</button>
                    <button type="button" onclick="" class="btn btn-default btn-filter _none"><i class="fa fa-trash-o"></i> 批量删除</button>

                </div>


                <div class="pull-right">

                    <input type="text" class="search-filter form-filter filter-keyup" name="staff-id" placeholder="ID" />
                    <input type="text" class="search-filter form-filter filter-keyup" name="staff-username" placeholder="名称" />

                    @if(in_array($me->user_type,[0,1,9,11]))
                    <select class="search-filter form-filter" name="staff-user-type">
                        <option value="-1">全部人员</option>
                        <option value="41">团队·总经理</option>
                        <option value="88">客服</option>
                        <option value="84">客服主管</option>
                        <option value="81">客服经理</option>
                        <option value="77">质检员</option>
                        <option value="71">质检经理</option>
                        <option value="66">运营人员</option>
                        <option value="61">运营经理</option>
                    </select>
                    @endif

                    @if(in_array($me->user_type,[0,1,9,11]))
                    <select class="search-filter form-filter select2-box" name="staff-department-district">
                        <option value="-1">选择大区</option>
                        @foreach($department_district_list as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select class="search-filter form-filter" name="staff-status">
                        <option value ="-1">全部</option>
                        <option value ="1">启用</option>
                        <option value ="9">禁用</option>
                    </select>


                    <button type="button" class="btn btn-default btn-filter filter-submit" id="filter-submit">
                        <i class="fa fa-search"></i> 搜索
                    </button>

                    <button type="button" class="btn btn-default btn-filter filter-empty">
                        <i class="fa fa-remove"></i> 清空
                    </button>

                    <button type="button" class="btn btn-default btn-filter filter-refresh">
                        <i class="fa fa-circle-o-notch"></i> 刷新
                    </button>

                    <button type="button" class="btn btn-default btn-filter filter-cancel">
                        <i class="fa fa-undo"></i> 重置
                    </button>


                </div>


            </div>


            <div class="box-body datatable-body">


                <table class='table table-striped table-bordered table-hover' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>


            </div>


        </div>
        <!-- END PORTLET-->
    </div>
</div>


<div class="modal fade" id="modal-password-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">修改【<span class="user-name"></span>】的密码</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-password-admin-change-modal">
                        <div class="box-body">

                            {{ csrf_field() }}
                            <input type="hidden" name="operate" value="staff-password-admin-change" readonly>
                            <input type="hidden" name="user_id" value="0" readonly>

                            {{--类别--}}


                            {{--用户ID--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">新密码</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <input type="password" class="form-control form-filter" name="user-password" value="">
                                    6-20位英文、数字、下划线构成
                                </div>
                            </div>
                            {{--用户名--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">确认密码</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <input type="password" class="form-control form-filter" name="user-password-confirm" value="">
                                </div>
                            </div>


                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="btn btn-success" id="item-password-admin-change-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default" id="item-password-admin-change-cancel">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>
@endsection




@section('custom-script')

    @include(env('TEMPLATE_DK_ADMIN').'entrance.staff.staff-list-datatable')
    @include(env('TEMPLATE_DK_ADMIN').'entrance.staff.staff-list-script')

@endsection