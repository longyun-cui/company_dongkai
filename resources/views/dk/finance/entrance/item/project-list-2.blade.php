@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '项目列表' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')项目列表 - 管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:4px 0;">

                <h3 class="box-title">{{ $title_text or '项目列表' }}</h3>

                @if(in_array($me->user_type,[0,1,9,11,19]))
                <div class="caption pull-right">
                    <i class="icon-pin font-blue"></i>
                    <span class="caption-subject font-blue sbold uppercase"></span>
                    <a href="{{ url('/item/project-create') }}">
                        <button type="button" onclick="" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加项目</button>
                    </a>
                </div>
                @endif

            </div>


            <div class="box-body datatable-body item-main-body" id="datatable-for-project-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="project-name" placeholder="名称" />

                        {{--<select class="form-control form-filter" name="owner" style="width:96px;">--}}
                            {{--<option value ="-1">选择员工</option>--}}
                            {{--@foreach($sales as $v)--}}
                                {{--<option value ="{{ $v->id }}">{{ $v->true_name }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}

{{--                        <select class="form-control form-filter" name="work_status" style="width:96px;">--}}
{{--                            <option value ="-1">全部状态</option>--}}
{{--                            <option value ="0">空闲</option>--}}
{{--                            <option value ="1">工作中</option>--}}
{{--                            <option value ="9">待发车</option>--}}
{{--                            <option value ="19">非工作状态</option>--}}
{{--                        </select>--}}

                        <select class="form-control form-filter" name="project-status" style="width:96px;">
                            <option value ="-1">全部</option>
                            <option value ="1">启用</option>
                            <option value ="9">禁用</option>
                        </select>

                        <select class="form-control form-filter _none" name="project-result" style="width:96px;">
                            <option value ="-1">全部</option>
                            <option value ="已收款">已收款</option>
                            <option value ="待收款">待收款</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                <table class='table table-striped table-bordered- table-hover main-table' id='datatable_ajax'>
                    <thead>
                        <tr role='row' class='heading'>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>

            </div>


            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-6 col-sm-9 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operate-status" class="form-control form-filter">
                                <option value ="-1">请选择操作类型</option>
                                <option value ="启用">启用</option>
                                <option value ="禁用">禁用</option>
                                <option value ="删除">删除</option>
                                <option value ="彻底删除">彻底删除</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operate-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-9">
                        <button type="button" onclick="" class="btn btn-primary _none"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>




{{--修改-基本信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-text-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改项目【<span class="info-text-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-text-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-text-set-operate" value="item-project-info-text-set" readonly>
                    <input type="hidden" name="info-text-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-text-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-text-set-column-key" value="" readonly>

                    <div class="form-group">
                        <label class="control-label col-md-2 info-text-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="info-text-set-column-value" autocomplete="off" placeholder="" value="">
                            <textarea class="form-control" name="info-textarea-set-column-value" rows="6" cols="100%"></textarea>
                        </div>
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-text-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-text-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-时间信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-time-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-time-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-time-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-time-set-operate" value="item-project-info-text-set" readonly>
                    {{--<input type="hidden" name="info-time-set-operate" value="item-project-info-time-set" readonly>--}}
                    <input type="hidden" name="info-time-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-time-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-time-set-column-key" value="" readonly>
                    <input type="hidden" name="info-time-set-time-type" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-time-set-column-name"></label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter time_picker" name="info-time-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="datetime" readonly="readonly">
                            <input type="text" class="form-control form-filter date_picker" name="info-date-set-column-value" autocomplete="off" placeholder="" value="" data-time-type="date" readonly="readonly">
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-time-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-time-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-radio-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-radio-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-radio-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-radio-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-radio-set-operate" value="item-project-info-option-set" readonly>
                    <input type="hidden" name="info-radio-set-project-id" value="0" readonly>
                    <input type="hidden" name="info-radio-set-operate-type" value="edit" readonly>
                    <input type="hidden" name="info-radio-set-column-key" value="" readonly>


                    <div class="form-group radio-box">
                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-radio-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-radio-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
{{--修改-select-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-info-select-set">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改【<span class="info-select-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-info-select-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="info-select-set-operate" value="item-project-info-option-set" readonly>
                    <input type="hidden" name="info-select-set-item-id" value="0" readonly>
                    <input type="hidden" name="info-select-set-operate-type" value="add" readonly>
                    <input type="hidden" name="info-select-set-column-key" value="" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2 info-select-set-column-name"></label>
                        <div class="col-md-8 ">
                            <select class="form-control select2-client" name="info-select-set-column-value" style="width:240px;" id="">
                                <option data-id="0" value="0">未指定</option>
                            </select>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-info-select-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-info-select-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--显示-附件-信息--}}
<div class="modal fade modal-main-body" id="modal-body-for-attachment">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">车辆【<span class="attachment-set-title"></span>】</h3>
                <div class="box-tools pull-right">
                </div>
            </div>



            {{--attachment--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="">
                <div class="box-body attachment-box">

                </div>
            </form>


            <div class="box-header with-border margin-top-16px margin-bottom-16px-">
                <h4 class="box-title">【添加附件】</h4>
            </div>

            {{--上传附件--}}
            <form action="" method="post" class="form-horizontal form-bordered " id="modal-attachment-set-form">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="attachment-set-operate" value="item-project-attachment-set" readonly>
                    <input type="hidden" name="attachment-set-project-id" value="0" readonly>
                    <input type="hidden" name="attachment-set-operate-type" value="add" readonly>
                    <input type="hidden" name="attachment-set-column-key" value="" readonly>

                    <input type="hidden" name="operate" value="item-project-attachment-set" readonly>
                    <input type="hidden" name="item_id" value="0" readonly>
                    <input type="hidden" name="operate_type" value="add" readonly>
                    <input type="hidden" name="column_key" value="attachment" readonly>


                    <div class="form-group">
                        <label class="control-label col-md-2">附件名称</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="attachment_name" autocomplete="off" placeholder="附件名称" value="">
                        </div>
                    </div>

                    {{--多图上传--}}
                    <div class="form-group">

                        <label class="control-label col-md-2">图片上传</label>

                        <div class="col-md-8">
                            <input id="multiple-images" type="file" class="file-multiple-images" name="multiple_images[]" multiple >
                        </div>

                    </div>

                    {{--多图上传--}}
                    <div class="form-group _none">

                        <label class="control-label col-md-2" style="clear:left;">选择图片</label>
                        <div class="col-md-8 fileinput-group">

                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail">
                                </div>
                                <div class="btn-tool-group">
                            <span class="btn-file">
                                <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                <input type="file" name="attachment_file" />
                            </span>
                                    <span class="">
                                <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                            </span>
                                </div>
                            </div>
                            <div id="titleImageError" style="color: #a94442"></div>

                        </div>

                    </div>

                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-attachment-set"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-attachment-set">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{--option--}}
<div class="option-container _none">

    <div id="option-list-for-abc">
        <option value="0">选择箱型</option>
        <option value="直板">直板</option>
        <option value="高栏">高栏</option>
        <option value="平板">平板</option>
        <option value="冷藏">冷藏</option>
    </div>





    <div id="option-list-for-receipt_need">

        <label class="control-label col-md-2">是否需要回单</label>
        <div class="col-md-8">
            <div class="btn-group">

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="0" class="info-set-column"> 不需要
                        </label>
                    </span>
                </button>

                <button type="button" class="btn">
                    <span class="radio">
                        <label>
                            <input type="radio" name="receipt_need" value="1" class="info-set-column"> 需要
                        </label>
                    </span>
                </button>

            </div>
        </div>

    </div>

</div>





{{--财务列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-list">
    <div class="col-md-10 col-md-offset-1 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">财务记录</h3>

                <div class="box-tools- pull-right">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right modal-show-for-finance-create _none">
                            <i class="fa fa-plus"></i> 添加记录
                        </button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-finance-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="finance-title" placeholder="费用类型" />

                        <select class="form-control form-filter" name="finance-finance_type" style="width:96px;">
                            <option value ="-1">选择</option>
                            <option value ="1">充值</option>
                            <option value ="91">退款</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-finance">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-finance">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <div class="tableArea">
                    <table class='table table-striped table-bordered' id='datatable_ajax_finance'>
                        <thead>
                        <tr role='row' class='heading'>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{--添加财务记录--}}
<div class="modal fade modal-main-body" id="modal-body-for-finance-create">
    <div class="col-md-6 col-md-offset-3 margin-top-64px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px">
                <h3 class="box-title">添加结算</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered " id="modal-form-for-finance-create">
                <div class="box-body">

                    {{ csrf_field() }}
                    <input type="hidden" name="finance-create-operate" value="finance-create-record" readonly>
                    <input type="hidden" name="finance-create-project-id" value="0" readonly>



                    {{--项目ID--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="finance-create-project-name"></span>
                        </div>
                    </div>
                    {{--关键词--}}
                    <div class="form-group _none">
                        <label class="control-label col-md-2">关键词</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <span class="finance-create-order-title"></span>
                        </div>
                    </div>
                    {{--交易类型--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 结算类型</label>
                        <div class="col-md-8 control-label" style="text-align:left;">
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="finance-create-type" value=1 checked="checked"> 结算
                                </label>
                            </button>
                            <button type="button" class="btn radio">
                                <label>
                                    <input type="radio" name="finance-create-type" value=21> 退费
                                </label>
                            </button>
                        </div>
                    </div>
                    {{--日期--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 日期</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control form-filter form_date" name="finance-create-transaction-date" readonly="readonly" />
                        </div>
                    </div>
                    {{--金额--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 金额</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-amount" placeholder="金额" value="">
                        </div>
                    </div>
                    {{--名目--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 名目</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-title" placeholder="名目" value="" list="_transaction_title">
                        </div>
                    </div>
                    <datalist id="_transaction_title">
                        <option value="结算" />
                        <option value="退费" />
                        <option value="其他" />
                    </datalist>
                    {{--支付方式--}}
                    <div class="form-group">
                        <label class="control-label col-md-2"><sup class="text-red">*</sup> 支付方式</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-type" placeholder="支付方式" value="" list="_transaction_type">
                        </div>
                    </div>
                    <datalist id="_transaction_type">
                        <option value="余额结算" />
                        <option value="微信" />
                        <option value="支付宝" />
                        <option value="银行卡" />
                        <option value="现金" />
                        <option value="其他" />
                    </datalist>
                    {{--收款账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">收款账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keyword" name="finance-create-transaction-receipt-account" placeholder="收款账号" value="" list="_transaction_receipt_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_receipt_account">
                        <option value="账户" class="" />
                    </datalist>
                    {{--支出账号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">支出账号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control search-input" id="keywords" name="finance-create-transaction-payment-account" placeholder="支出账号" value="" list="_transaction_payment_account" autocomplete="on">
                        </div>
                    </div>
                    <datalist id="_transaction_payment_account">
                        <option value="账户" class="" />
                    </datalist>
                    {{--交易账号--}}
                    {{--<div class="form-group income-show-">--}}
                    {{--<label class="control-label col-md-2">交易账号</label>--}}
                    {{--<div class="col-md-8 ">--}}
                    {{--<input type="text" class="form-control" name="finance-create-transaction-account" placeholder="交易账号" value="">--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--交易单号--}}
                    <div class="form-group income-show-">
                        <label class="control-label col-md-2">交易单号</label>
                        <div class="col-md-8 ">
                            <input type="text" class="form-control" name="finance-create-transaction-order" placeholder="交易单号" value="">
                        </div>
                    </div>
                    {{--备注--}}
                    <div class="form-group">
                        <label class="control-label col-md-2">备注</label>
                        <div class="col-md-8 ">
                            {{--<input type="text" class="form-control" name="description" placeholder="描述" value="">--}}
                            <textarea class="form-control" name="finance-create-transaction-description" rows="3" cols="100%"></textarea>
                        </div>
                    </div>


                </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="item-submit-for-finance-create"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" class="btn btn-default" id="item-cancel-for-finance-create">取消</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>




{{--修改列表--}}
<div class="modal fade modal-main-body" id="modal-body-for-modify-list">
    <div class="col-md-8 col-md-offset-2 margin-top-32px margin-bottom-64px bg-white">

        <div class="box- box-info- form-container">

            <div class="box-header with-border margin-top-16px margin-bottom-16px">
                <h3 class="box-title">修改记录</h3>
                <div class="box-tools pull-right caption _none">
                    <a href="javascript:void(0);">
                        <button type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> 添加记录</button>
                    </a>
                </div>
            </div>

            <div class="box-body datatable-body" id="datatable-for-modify-list">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter filter-keyup" name="modify-keyword" placeholder="关键词" />

                        <select class="form-control form-filter" name="modify-attribute" style="width:96px;">
                            <option value ="-1">选择属性</option>
                            <option value ="amount">金额</option>
                            <option value ="11">支出</option>
                        </select>

                        <button type="button" class="form-control btn btn-flat btn-success filter-submit" id="filter-submit-for-modify">
                            <i class="fa fa-search"></i> 搜索
                        </button>
                        <button type="button" class="form-control btn btn-flat btn-default filter-cancel" id="filter-cancel-for-modify">
                            <i class="fa fa-circle-o-notch"></i> 重置
                        </button>

                    </div>
                </div>

                <table class='table table-striped table-bordered' id='datatable_ajax_record'>
                    <thead>
                    <tr role='row' class='heading'>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer _none">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-all"></span>
                            <input type="text" class="form-control" name="bulk-detect-rank" id="bulk-detect-rank" placeholder="指定排名">
                            <span class="input-group-addon btn btn-default" id="set-rank-bulk-submit"><i class="fa fa-check"></i>提交</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection




@section('custom-css')
@endsection
@section('custom-style')
<style>
    /*.tableArea table { width:100% !important; min-width:100%; }*/
    .tableArea table { min-width:100%; }
    .tableArea table tr th, .tableArea table tr td { white-space:nowrap; }

    .select2-container { height:100%; border-radius:0; float:left; }
    .select2-container .select2-selection--single { border-radius:0; }
    .bg-fee-2 { background:#C3FAF7; }
    .bg-fee { background:#8FEBE5; }
    .bg-deduction { background:#C3FAF7; }
    .bg-route { background:#8FEBE5; }
    .bg-income { background:#FFEBE5; }
    .bg-finance { background:#E2FCAB; }
    .bg-empty { background:#F6C5FC; }
    .bg-journey { background:#F5F9B4; }
</style>
@endsection




@section('custom-js')
@endsection
@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[100, 200, -1], ["100", "200", "全部"]],
                "processing": true,
                "serverSide": false,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/item/project-list-2') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.id = $('input[name="project-id"]').val();
                        d.name = $('input[name="project-name"]').val();
                        d.title = $('input[name="project-title"]').val();
                        d.keyword = $('input[name="project-keyword"]').val();
                        d.item_status = $('select[name="project-status"]').val();
                        d.item_result = $('select[name="project-result"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "scrollX": true,
//                "scrollY": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
                    "rightColumns": "0"
                },
                "columns": [
//                    {
//                        "width": "40px",
//                        "title": "选择",
//                        "data": "id",
//                        'orderable': false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "40px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        'orderable': false
//                    },
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-attachment');
                                $(nTd).attr('data-id',row.id).attr('data-name','附件');
                                $(nTd).attr('data-key','attachment_list').attr('data-value','');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "操作",
                        "data": 'id',
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            var $html_edit = '';
                            var $html_detail = '';
                            var $html_record = '';
                            var $html_able = '';
                            var $html_delete = '';
                            var $html_publish = '';
                            var $html_settle = '';
                            var $html_abandon = '';

                            if(row.item_status == 1)
                            {
                                $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                            }
                            else
                            {
                                $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                            }

                            if(row.is_me == 1 && row.active == 0)
                            {
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                            }
                            else
                            {
                                $html_publish = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">发布</a>';
                            }

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
                            }
                            else
                            {
                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
                            }

                            // $html_settle = '<a class="btn btn-xs bg-orange item-modal-show-for-settle" data-id="'+data+'">结算</a>';
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                            var html =
                                // '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                                $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
//                                    $html_publish+
//                                 $html_settle+
                                $html_record+
                                // $html_delete+
//                                    '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
//                                    '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
//                                    '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "title": "状态",
                        "data": "item_status",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.deleted_at != null)
                            {
                                return '<small class="btn-xs bg-black">已删除</small>';
                            }

                            if(data == 1)
                            {
                                return '<small class="btn-xs btn-success">启用</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs btn-danger">禁用</small>';
                            }
                        }
                    },
                    {
                        "title": "项目名称",
                        "data": "name",
                        "className": "text-center project-name",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','项目名称');
                                $(nTd).attr('data-key','name').attr('data-value',data);
                                $(nTd).attr('data-column-name','项目名称');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0);">'+data+'</a>';
                        }
                    },
                    {
                        "title": "所属代理",
                        "data": "channel_id",
                        "className": "text-center",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','所属代理');
                                $(nTd).attr('data-key','channel_id').attr('data-value',data);
                                if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    $(nTd).attr('data-option-name',row.channel_er.name);
                                }
                                $(nTd).attr('data-column-name','所属代理');
                                if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.channel_er == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.channel_er.name+'</a>';
                        }
                    },
                    {
                        "title": "商务人员",
                        "data": "business_id",
                        "className": "text-center",
                        "width": "120px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','商务人员');
                                $(nTd).attr('data-key','business_id').attr('data-value',data);
                                if(row.business_or == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    $(nTd).attr('data-option-name',row.business_or.username);
                                }
                                $(nTd).attr('data-column-name','商务人员');
                                if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(row.business_or == null) return '--';
                            else return '<a href="javascript:void(0);">'+row.business_or.username+'</a>';
                        }
                    },
                    {
                        "title": "总交付量",
                        "data": "total_delivery_quantity",
                        "className": "text-center bg-journey",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总有效量",
                        "data": "total_delivery_quantity_of_effective",
                        "className": "text-center bg-journey",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "总成本",
                        "data": "total_cost",
                        "className": "text-center bg-route",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "渠道费用",
                        "data": "channel_cost",
                        "className": "text-center bg-income",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "应结算",
                        "data": "should_settled",
                        "className": "text-center bg-deduction",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "利润",
                        "data": "id",
                        "className": "text-center bg-finance",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        render: function(data, type, row, meta) {
                            var $should_settled = parseFloat(row.should_settled);

                            var $total_cost = row.total_cost;
                            var $channel_cost = row.channel_cost;

                            var $profile = $should_settled - $total_cost - $channel_cost;
                            return parseFloat($profile);
                        }
                    },
                    {
                        "title": "已结算",
                        "data": "settled_amount",
                        "className": "item-show-for-settle bg-empty",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(true)
                            {
                                $(nTd).attr('data-id',row.id).attr('data-name','已结算');
                                $(nTd).attr('data-key','settled_amount').attr('data-value',data);
                                $(nTd).attr('data-column-name','已结算');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(data == 0) return "--";
                            return parseFloat(data);
                        }
                    },
                    {
                        "title": "待收款",
                        "data": "id",
                        "className": "text-center bg-empty",
                        "width": "60px",
                        "orderable": true,
                        "orderSequence": ["desc", "asc"],
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(parseFloat(row.should_settled - row.already_settled) >  0)
                            {
                                $(nTd).addClass('_bold').addClass('text-red');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return parseFloat(row.should_settled - row.already_settled);
                        }
                    },
                    {
                        "title": "说明",
                        "data": "description",
                        "className": "text-center",
                        "width": "160px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','说明');
                                $(nTd).attr('data-key','description').attr('data-value',data);
                                $(nTd).attr('data-column-name','说明');
                                $(nTd).attr('data-text-type','textarea');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                            // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                            // else return '';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "80px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "120px",
                        "title": "更新时间",
                        "data": 'updated_at',
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    },
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });
                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();
    $(function () {
        TableDatatablesAjax.init();
    });
</script>


<script>
    var TableDatatablesAjax_finance = function ($id) {
        var datatableAjax_finance = function ($id,$type) {

            var dt_finance = $('#datatable_ajax_finance');
            dt_finance.DataTable().destroy();
            var ajax_datatable_finance = dt_finance.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/project/project-funds-using-record?id="+$id+"&type="+$type,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="finance-name"]').val();
                        d.title = $('input[name="finance-title"]').val();
                        d.keyword = $('input[name="finance-keyword"]').val();
                        d.finance_type = $('select[name="finance-finance_type"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "",
                        "width": "40px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "width": "120px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $html_confirm = '';
                            var $html_delete = '';

                            if(row.deleted_at == null)
                            {
                                $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';

                                if(row.is_confirmed == 1)
                                {
                                    $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';

                                    // if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id)
                                    if(row.confirmer_id == 0 || row.confirmer_id == row.creator_id || "{{ $me->id or 0 }}" == row.confirmer_id || "{{ $me->id }}" == row.creator_id)
                                    {
                                        $html_delete = '<a class="btn btn-xs bg-navy item-finance-delete-submit" data-id="'+data+'">删除</a>';
                                    }
                                    else $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
                                }
                                else
                                {
                                    $html_confirm = '<a class="btn btn-xs bg-green item-finance-confirm-submit" data-id="'+data+'">确认</a>';
                                }
                            }
                            else
                            {
                                $html_confirm = '<a class="btn btn-xs btn-default disabled">确认</a>';
                                $html_delete = '<a class="btn btn-xs btn-default disabled">删除</a>';
//                                $html_delete = '<a class="btn btn-xs bg-grey item-finance-restore-submit" data-id="'+data+'">恢复</a>';
                            }


                            var html =
                                $html_confirm+
                                $html_delete+
                                //                                '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                                //                                '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                '';
                            return html;

                        }
                    },
                    {
                        "width": "60px",
                        "title": "状态",
                        "data": "is_confirmed",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.deleted_at == null)
                            {
                                if(data == 1) return '<small class="btn-xs btn-success">已确认</small>';
                                else return '<small class="btn-xs btn-danger">待确认</small>';
                            }
                            else return '<small class="btn-xs bg-black">已删除</small>';
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "类型",
                        "data": "finance_type",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(row.finance_type == 1) return '<small class="btn-xs bg-olive">充值</small>';
                            else if(row.finance_type == 91) return '<small class="btn-xs bg-orange">退款</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "交易时间",
                        "data": "transaction_time",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                var $time_value = '';
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    $time_value = $year+'-'+$month+'-'+$day;
                                }

                                $(nTd).addClass('modal-show-for-finance-time-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交易时间');
                                $(nTd).attr('data-key','transaction_time').attr('data-value',$time_value);
                                $(nTd).attr('data-column-name','交易时间');
                                $(nTd).attr('data-time-type','date');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day;
                            else return $year+'-'+$month+'-'+$day;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "创建者",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "确认者",
                        "data": "confirmer_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.confirmer == null ? '' : '<a href="javascript:void(0);">'+row.confirmer.username+'</a>';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "100px",
                        "title": "确认时间",
                        "data": "confirmed_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '';

                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;

                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "金额",
                        "data": "transaction_amount",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.finance_type == 1) return '<b class="text-olive">'+parseFloat(data)+'</b>';
                            else if(row.finance_type == 21) return '<b class="text-red">'+parseFloat(data)+'</b>';
                            else return parseFloat(data);
                        }
                    },
                    {
                        "className": "",
                        "width": "80px",
                        "title": "费用名目",
                        "data": "title",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','费用名目');
                                $(nTd).attr('data-key','title').attr('data-value',data);
                                $(nTd).attr('data-column-name','费用名目');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "60px",
                        "title": "支付方式",
                        "data": "transaction_type",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','支付方式');
                                $(nTd).attr('data-key','transaction_type').attr('data-value',data);
                                $(nTd).attr('data-column-name','支付方式');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "收款账户",
                        "data": "transaction_receipt_account",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','收款账户');
                                $(nTd).attr('data-key','transaction_receipt_account').attr('data-value',data);
                                $(nTd).attr('data-column-name','收款账户');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "支出账户",
                        "data": "transaction_payment_account",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','支出账户');
                                $(nTd).attr('data-key','transaction_payment_account').attr('data-value',data);
                                $(nTd).attr('data-column-name','支出账户');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
//                    {
//                        "className": "",
//                        "width": "120px",
//                        "title": "交易账户",
//                        "data": "transaction_account",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return data;
//                        }
//                    },
                    {
                        "className": "",
                        "width": "160px",
                        "title": "交易单号",
                        "data": "transaction_order",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','交易单号');
                                $(nTd).attr('data-key','transaction_order').attr('data-value',data);
                                $(nTd).attr('data-column-name','交易单号');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "200px",
                        "title": "备注",
                        "data": "description",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            // if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-finance-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','备注');
                                $(nTd).attr('data-key','description').attr('data-value',data);
                                $(nTd).attr('data-column-name','备注');
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "操作时间",
                        "data": "created_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    },
//                    {
//                        "width": "120px",
//                        "title": "操作",
//                        'data': 'id',
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
////                            var $date = row.transaction_date.trim().split(" ")[0];
//                            var html = '';
////                                '<a class="btn btn-xs item-enable-submit" data-id="'+value+'">启用</a>'+
////                                '<a class="btn btn-xs item-disable-submit" data-id="'+value+'">禁用</a>'+
////                                '<a class="btn btn-xs item-download-qrcode-submit" data-id="'+value+'">下载二维码</a>'+
////                                '<a class="btn btn-xs item-statistics-submit" data-id="'+value+'">流量统计</a>'+
////                                    '<a class="btn btn-xs" href="/item/edit?id='+value+'">编辑</a>'+
////                                '<a class="btn btn-xs item-edit-submit" data-id="'+value+'">编辑</a>'+
//
//                            return html;
//                        }
//                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_finance.on('click', '.finance-filter-submit', function () {
                ajax_datatable_finance.ajax.reload();
            });

            dt_finance.on('click', '.finance-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt_finance).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_finance.ajax.reload();
            });


//            dt_finance.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_finance
        }
    }();
    //    $(function () {
    //        TableDatatablesAjax_finance.init();
    //    });
</script>


<script>
    var TableDatatablesAjax_record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_record = $('#datatable_ajax_record');
            dt_record.DataTable().destroy();
            var ajax_datatable_record = dt_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[20, 50, 200], ["20", "50", "200"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/item/project-modify-record?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
//
//                        d.created_at_from = $('input[name="created_at_from"]').val();
//                        d.created_at_to = $('input[name="created_at_to"]').val();
//                        d.updated_at_from = $('input[name="updated_at_from"]').val();
//                        d.updated_at_to = $('input[name="updated_at_to"]').val();

                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "50px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "类型",
                        "data": "operate_category",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            if(data == 1)
                            {
                                if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                                else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                                else return '有误';
                            }
                            else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                            else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                            else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                            else if(data == 71)
                            {
                                if(row.operate_type == 1)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                                }
                                else if(row.operate_type == 91)
                                {
                                    return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                                }
                                else return '';

                            }
                            else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                            else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                            else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                            else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "80px",
                        "title": "属性",
                        "data": "column_name",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_category == 1)
                            {
                                if(data == "name") return '项目名称';
                                if(data == "channel_id") return '所属代理';
                                if(data == "business_id") return '商务人员';
                                else if(data == "delivery_invalid_quantity") return '无效单量';
                                else if(data == "channel_unit_price") return '代理单价';
                                else if(data == "cooperative_unit_price") return '合作单价';
                                else if(data == "remark") return '备注';
                                else return '有误';
                            }
                            else if(row.operate_category == 71)
                            {
                                return '';

                                if(row.operate_type == 1) return '添加';
                                else if(row.operate_type == 91) return '删除';

                                if(data == "attachment") return '附件';
                            }
                            else return '';
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改前",
                        "data": "before",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'driver_id')
                            {
                                if(row.before_driver_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.before_driver_er.driver_name+'</a>';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                if(data)
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            if(data == 0) return '';
                            return data;
                        }
                    },
                    {
                        "className": "font-12px",
                        "width": "240px",
                        "title": "修改后",
                        "data": "after",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.column_name == 'client_id')
                            {
                                if(row.after_client_er == null) return '';
                                else
                                {
                                    if(row.after_client_er.short_name)
                                    {
                                        return '<a href="javascript:void(0);">'+row.after_client_er.short_name+'</a>';
                                    }
                                    else return '<a href="javascript:void(0);">'+row.after_client_er.username+'</a>';
                                }
                            }
                            else if(row.column_name == 'route_id')
                            {
                                if(row.after_route_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_route_er.title+'</a>';
                            }
                            else if(row.column_name == 'pricing_id')
                            {
                                if(row.after_pricing_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_pricing_er.title+'</a>';
                            }
                            else if(row.column_name == 'car_id' || row.column_name == 'trailer_id')
                            {
                                if(row.after_car_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_car_er.name+'</a>';
                            }
                            else if(row.column_name == 'driver_id')
                            {
                                if(row.after_driver_er == null) return '';
                                else return '<a href="javascript:void(0);">'+row.after_driver_er.driver_name+'</a>';
                            }

                            if(row.column_type == 'datetime' || row.column_type == 'date')
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);

                                var $currentYear = new Date().getFullYear();
                                if($year == $currentYear)
                                {
                                    if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $month+'-'+$day;
                                }
                                else
                                {
                                    if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                    else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                }
                            }

                            if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                            {
                                var $cdn = "{{ env('DOMAIN_CDN') }}";
                                var $src = $cdn = $cdn + "/" + data;
                                return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                            }

                            return data;
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "108px",
                        "title": "操作时间",
                        "data": "created_at",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                    ajax_datatable_record.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                .replace('@action_name', action_name)
                                .replace('@nickname', nickname)
                                .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                    '/admin/medsci/certificate/user/verify',
                                    {
                                        action: action,
                                        id: certificate_id,
                                        _token: '{{csrf_token()}}'
                                    },
                                    function(json){
                                        if(json['response_code'] == 'success') {
                                            layer.msg('操作成功!', {time: 3500});
                                            ajax_datatable.ajax.reload();
                                        } else {
                                            layer.alert(json['response_data'], {time: 10000});
                                        }
                                    }, 'json');
                            }
                        });
                    });

//                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
//                        checkboxClass: 'icheckbox_minimal-blue',
//                        radioClass   : 'iradio_minimal-blue'
//                    });
                },
                "language": { url: '/common/dataTableI18n' },
            });


            dt_record.on('click', '.modify-filter-submit', function () {
                ajax_datatable_record.ajax.reload();
            });

            dt_record.on('click', '.modify-filter-cancel', function () {
                $('textarea.form-filter, input.form-filter, select.form-filter', dt).each(function () {
                    $(this).val("");
                });

//                $('select.form-filter').selectpicker('refresh');
                $('select.form-filter option').attr("selected",false);
                $('select.form-filter').find('option:eq(0)').attr('selected', true);

                ajax_datatable_record.ajax.reload();
            });


//            dt_record.on('click', '#all_checked', function () {
////                layer.msg(this.checked);
//                $('input[name="detect-record"]').prop('checked',this.checked);//checked为true时为默认显示的状态
//            });


        };
        return {
            init: datatableAjax_record
        }
    }();
    //        $(function () {
    //            TableDatatablesAjax_record.init();
    //        });
</script>
@include(env('TEMPLATE_DK_FINANCE').'entrance.item.project-list-script')
@endsection
