@extends(env('TEMPLATE_DK_FINANCE').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑内容' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '订单列表' }}</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:4px 0;">
                <h3 class="box-title">{{ $title_text or '' }}</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="operate_category" value="{{ $operate_category or 'ITEM' }}" readonly>
                <input type="hidden" name="operate_type" value="{{ $operate_type or 'order' }}" readonly>


                {{--类别--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2">类型</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            @if(in_array($me->user_type, [0,1,11]))
                                @if($operate == 'create' || ($operate == 'edit' && $data->created_type == 1))
                                    <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate == 'create')
                                                        <input type="radio" name="created_type" value="1" checked="checked"> 创建
                                                    @elseif($operate == 'edit' && $data->created_type == 1)
                                                        <input type="radio" name="created_type" value="1" checked="checked"> 创建
                                                    @else
                                                        <input type="radio" name="created_type" value="1" checked="checked"> 创建
                                                    @endif
                                                </label>
                                            </span>
                                    </button>
                                @endif
                            @endif

                            @if(in_array($me->user_type, [0,1,11,41]))
                                @if($operate == 'create' || ($operate == 'edit' && $data->created_type == 11))
                                    <button type="button" class="btn">
                                            <span class="radio">
                                                <label>
                                                    @if($operate == 'edit' && $data->created_type == 11)
                                                        <input type="radio" name="created_type" value="11" checked="checked"> 自定义补录
                                                    @else
                                                        <input type="radio" name="created_type" value="11"> 自定义补录
                                                    @endif
                                                </label>
                                            </span>
                                    </button>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>


                {{--自定义标题--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 自定义标题</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="title" placeholder="自定义订单标题" value="{{ $data->title or '' }}">
                    </div>
                </div>

                {{--项目--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 项目</label>
                    <div class="col-md-8 ">
                        <select class="form-control" name="project_id" id="select2-project">
                            @if($operate == 'edit' && $data->project_id)
                                <option data-id="{{ $data->project_id or 0 }}" value="{{ $data->project_id or 0 }}">{{ $data->project_er->title }}</option>
                            @else
                                <option data-id="0" value="0">未指定</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{--开始日期--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 开始日期</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control date_picker" name="assign_start" placeholder="日期" readonly="readonly"
                               {{--@if(!empty($data->assign_date)) value="{{ date("Y-m-d",$data->assign_time) }}"--}}
                                @if(!empty($data->assign_start)) value="{{ $data->assign_start }}"
                                @else value="{{ date("Y-m-d") }}"
                                @endif
                        >
                    </div>
                </div>

                {{--结束日期--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 结束日期</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control date_picker" name="assign_ended" placeholder="日期" readonly="readonly"
                               {{--@if(!empty($data->assign_date)) value="{{ date("Y-m-d",$data->assign_time) }}"--}}
                               @if(!empty($data->assign_ended)) value="{{ $data->assign_ended }}"
                               @else value="{{ date("Y-m-d") }}"
                                @endif
                        >
                    </div>
                </div>

                {{--交付量--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 交付量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="delivery_quantity" placeholder="交付量" value="{{ $data->delivery_quantity or '' }}">
                    </div>
                </div>
                {{--有效交付量--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 有效交付量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="delivery_quantity_of_effective" placeholder="有效交付量" value="{{ $data->delivery_quantity_of_effective or '' }}">
                    </div>
                </div>
                {{--总成本--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 总成本</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="total_cost" placeholder="总成本" value="{{ $data->total_cost or '' }}">
                    </div>
                </div>


                {{--渠道单价--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 渠道单价</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="channel_unit_price" placeholder="渠道单价" value="{{ $data->channel_unit_price or '' }}">
                    </div>
                </div>
                {{--渠道成本--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 渠道成本</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="channel_cost" placeholder="渠道成本" value="{{ $data->channel_cost or '' }}">
                    </div>
                </div>
                {{--合作单价--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 合作单价</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price" placeholder="合作单价" value="{{ $data->cooperative_unit_price or '' }}">
                    </div>
                </div>
                {{--利润分成比例--}}
                <div class="form-group custom-box">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 利润分成比例</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="profit_proportion" placeholder="利润分成比例" value="{{ $data->profit_proportion or '' }}">
                    </div>
                </div>


                {{--备注--}}
                <div class="form-group">
                    <label class="control-label col-md-2">备注</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
                    </div>
                </div>

                {{--备注--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">备注</label>
                    <div class="col-md-8 ">
                        <textarea class="form-control" name="remark" rows="3" cols="100%">{{ $data->remark or '' }}</textarea>
                    </div>
                </div>


            </div>
            </form>

            <div class="box-footer">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="button" class="btn btn-success" id="edit-item-submit"><i class="fa fa-check"></i> 提交</button>
                        <button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endsection




@section('custom-css')
@endsection




@section('custom-script')
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        var $created_type = $("input[name=created_type]").val();
        if($created_type == 1)
        {
            $('.custom-box').hide();
        }
        else if($created_type == 11)
        {
            $('.custom-box').show();
        }

        // 【选择部门类型】
        $("#form-edit-item").on('click', "input[name=created_type]", function() {
            // radio
            var $value = $(this).val();
            if($value == 1)
            {
                $('.custom-box').hide();
            }
            else if($value == 11)
            {
                $('.custom-box').show();
            }
            else
            {
                $('.custom-box').hide();
            }
        });




        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/settled-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);

                        location.href = "{{ url('/item/settled-list') }}";

                        {{--if($.getUrlParam('referrer')) location.href = decodeURIComponent($.getUrlParam('referrer'));--}}
                        {{--else if(document.referrer) location.href = document.referrer;--}}
                        {{--else location.href = "{{ url('/item/order-list-for-all') }}";--}}

                        // history.go(-1);
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });




        //
        $('#select2-project').select2({
            ajax: {
                url: "{{ url('/item/item_select2_project') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });






    });
</script>
@endsection
