@extends(env('TEMPLATE_DK_ADMIN_2').'layout.layout')


@section('head_title')
    {{ $title_text or '编辑客户' }} - 管理员系统 - {{ config('info.info.short_name') }}
@endsection




@section('header','')
@section('description')管理员系统 - {{ config('info.info.short_name') }}@endsection
@section('breadcrumb')
    <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="{{ url($list_link) }}"><i class="fa fa-list"></i>{{ $list_text or '客户列表' }}</a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info form-container">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">{{ $title_text or '' }}</h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-item">
            <div class="box-body">

                {{ csrf_field() }}
                <input type="hidden" name="operate" value="{{ $operate or '' }}" readonly>
                <input type="hidden" name="operate_id" value="{{ $operate_id or 0 }}" readonly>
                <input type="hidden" name="category" value="{{ $category or 'user' }}" readonly>
                <input type="hidden" name="type" value="{{ $type or 'user' }}" readonly>




                {{--客户名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 客户名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="username" placeholder="客户名称" value="{{ $data->username or '' }}">
                    </div>
                </div>


                {{--合作单价--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 线索单价（普通单）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price_1" placeholder="线索单价（普通单）" value="{{ $data->cooperative_unit_price_1 or 0 }}">
                    </div>
                </div>
                {{--合作单价--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 线索单价（优选单）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price_2" placeholder="线索单价（优选单）" value="{{ $data->cooperative_unit_price_2 or 0 }}">
                    </div>
                </div>
                {{--合作单价--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 线索单价（指派单）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price_3" placeholder="线索单价（指派单）" value="{{ $data->cooperative_unit_price_3 or 0 }}">
                    </div>
                </div>
                {{--合作单价--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 电话单价（电话单）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="cooperative_unit_price_of_telephone" placeholder="电话单价（电话单）" value="{{ $data->cooperative_unit_price_of_telephone or 0 }}">
                    </div>
                </div>


                {{--拨号时长--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 拨号时长（线索）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="call_time_limit_for_clue" placeholder="电话单价（线索）" value="{{ $data->call_time_limit_for_clue or 0 }}">
                    </div>
                </div>
                {{--拨号时长--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 拨号时长（电话单）</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="call_time_limit_for_telephone" placeholder="电话单价（电话单）" value="{{ $data->call_time_limit_for_telephone or 0 }}">
                    </div>
                </div>


                {{--api--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> API_ID</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="api_id" placeholder="API_ID" value="{{ $data->api_id or '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> API_Password</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="api_password" placeholder="API_Password" value="{{ $data->api_password or '' }}">
                    </div>
                </div>


                {{--管理员名称--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 管理员名称</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="customer_admin_name" placeholder="管理员名称" value="{{ $data->customer_admin_name or '' }}">
                    </div>
                </div>
                {{--管理员手机--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 管理员登录手机</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="customer_admin_mobile" placeholder="管理员登录手机" value="{{ $data->customer_admin_mobile or '' }}">
                    </div>
                </div>
                {{--管理员手机--}}
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 管理员分机号</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="customer_admin_api_agent_id" placeholder="管理员分机号" value="{{ $data->customer_admin_api_agent_id or '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-2"><sup class="text-red">*</sup> 所在城市</label>
                    <div class="col-md-8 ">
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select-select2 select2-district-city" name="district_city" id="select-city-1" data-target="#select-district-1" style="width:100%;">
                                <option value="">选择城市</option>
                                @if(!empty($district_city_list) && count($district_city_list) > 0)
                                    @foreach($district_city_list as $v)
                                        <option value="{{ $v->district_city }}" @if(!empty($data->district_city) && $data->district_city == $v->district_city) selected="selected" @endif>{{ $v->district_city }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 padding-0">
                            <select class="form-control select-select2 select2-district-district" name="district_district[]" id="select-district-1" data-target="#select-city-1" multiple="multiple" style="width:100%;">
{{--                                <option value="">选择区域</option>--}}
                            </select>
                        </div>
                    </div>
                </div>


                {{--描述--}}
                <div class="form-group">
                    <label class="control-label col-md-2">描述</label>
                    <div class="col-md-8 ">
                        {{--<input type="text" class="form-control" name="description" placeholder="描述" value="{{$data->description or ''}}">--}}
                        <textarea class="form-control" name="description" rows="3" cols="100%">{{ $data->description or '' }}</textarea>
                    </div>
                </div>


                {{--启用ip--}}
                <div class="form-group form-category">
                    <label class="control-label col-md-2">启用ip</label>
                    <div class="col-md-8">
                        <div class="btn-group">

                            <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->is_ip == 0)
                                            <input type="radio" name="is_ip" value="0" checked="checked"> 否
                                        @else
                                            <input type="radio" name="is_ip" value="0" checked="checked"> 否
                                        @endif
                                    </label>
                                </span>
                            </button>

                            <button type="button" class="btn">
                                <span class="radio">
                                    <label>
                                        @if($operate == 'edit' && $data->is_ip == 1)
                                            <input type="radio" name="is_ip" value="1" checked="checked"> 是
                                        @else
                                            <input type="radio" name="is_ip" value="1"> 是
                                        @endif
                                    </label>
                                </span>
                            </button>


                        </div>
                    </div>
                </div>
                {{--ip白名单--}}
                <div class="form-group">
                    <label class="control-label col-md-2">ip白名单</label>
                    <div class="col-md-8 ">
                        <input type="text" class="form-control" name="ip_whitelist" placeholder="ip白名单" value="{{ $data->ip_whitelist or '' }}">
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
    {{--<link rel="stylesheet" href="https://cdn.bootcss.com/select2/4.0.5/css/select2.min.css">--}}
    <link rel="stylesheet" href="{{ asset('/lib/css/select2-4.0.5.min.css') }}">
@endsection




@section('custom-script')
{{--<script src="https://cdn.bootcss.com/select2/4.0.5/js/select2.min.js"></script>--}}
<script src="{{ asset('/lib/js/select2-4.0.5.min.js') }}"></script>
<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">耐心等待中</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            var options = {
                url: "{{ url('/user/customer-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/user/customer-list') }}";
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误');
                },
                complete: function (jqXHR, textStatus) {
                    layer.closeAll('loading');
                }
            };
            $("#form-edit-item").ajaxSubmit(options);

        });




    });
</script>
@endsection
