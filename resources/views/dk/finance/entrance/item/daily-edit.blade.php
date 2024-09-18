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

                {{--提交日期--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 日期</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="assign_date" placeholder="日期" readonly="readonly"
{{--                               @if(!empty($data->assign_date)) value="{{ date("Y-m-d",$data->assign_time) }}"--}}
                                @if(!empty($data->assign_date)) value="{{ $data->assign_date }}"
                                @else value="{{ date("Y-m-d") }}"
                                @endif
                        >
                    </div>
                </div>


                {{--外呼后台--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 外呼后台</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=outbound_background placeholder="外呼后台" value="{{ $data->outbound_background or '' }}">
                    </div>
                </div>


                {{--出勤人力--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 出勤人力</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=attendance_manpower placeholder="出勤人力" value="{{ $data->attendance_manpower or '' }}">
                    </div>
                </div>
                {{--交付量--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 交付量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=delivery_quantity placeholder="交付量" value="{{ $data->delivery_quantity or '' }}">
                    </div>
                </div>
                {{--交付量--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 无效交付量</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=delivery_quantity_of_invalid placeholder="无效交付量" value="{{ $data->delivery_quantity_of_invalid or '' }}">
                    </div>
                </div>
                {{--当日总话费--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 当日总话费</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=call_charge_daily_cost placeholder="当日总话费" value="{{ $data->call_charge_daily_cost or '' }}">
                    </div>
                </div>


                {{--人力日薪--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 人力日薪</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=manpower_daily_wage placeholder="人力日薪" value="{{ $data->manpower_daily_wage or '' }}">
                    </div>
                </div>
                {{--话费系数--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 话费系数</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=call_charge_coefficient placeholder="话费系数" value="{{ $data->call_charge_coefficient or '' }}">
                    </div>
                </div>
                {{--物料系数--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 物料系数</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=material_coefficient placeholder="物料系数" value="{{ $data->material_coefficient or '' }}">
                    </div>
                </div>
                {{--税费系数--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 税费系数</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name=taxes_coefficient placeholder="税费系数" value="{{ $data->taxes_coefficient or '' }}">
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




                {{--头像--}}
                <div class="form-group _none">
                    <label class="control-label col-md-2">头像</label>
                    <div class="col-md-8 fileinput-group">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail">
                                @if(!empty($data->portrait_img))
                                    <img src="{{ url(env('DOMAIN_CDN').'/'.$data->portrait_img) }}" alt="" />
                                @endif
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail">
                            </div>
                            <div class="btn-tool-group">
                                <span class="btn-file">
                                    <button class="btn btn-sm btn-primary fileinput-new">选择图片</button>
                                    <button class="btn btn-sm btn-warning fileinput-exists">更改</button>
                                    <input type="file" name="portrait" />
                                </span>
                                <span class="">
                                    <button class="btn btn-sm btn-danger fileinput-exists" data-dismiss="fileinput">移除</button>
                                </span>
                            </div>
                        </div>
                        <div id="titleImageError" style="color: #a94442"></div>

                    </div>
                </div>

                {{--启用--}}
                @if($operate == 'create')
                    <div class="form-group form-type _none">
                        <label class="control-label col-md-2">启用</label>
                        <div class="col-md-8">
                            <div class="btn-group">

                                <button type="button" class="btn">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="active" value="0" checked="checked"> 暂不启用
                                        </label>
                                    </div>
                                </button>
                                <button type="button" class="btn">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="active" value="1"> 启用
                                        </label>
                                    </div>
                                </button>

                            </div>
                        </div>
                    </div>
                @endif

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


        $('input[name=assign_date]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });

        $('input[name=should_departure]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('input[name=should_arrival]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });




        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/daily-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);

                        location.href = "{{ url('/item/daily-list') }}";

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
