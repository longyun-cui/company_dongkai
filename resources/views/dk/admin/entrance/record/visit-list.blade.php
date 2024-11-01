@extends(env('TEMPLATE_DK_ADMIN').'layout.layout')


@section('head_title','记录列表')


@section('header','')
@section('description','SUPER - 朝鲜族社群平台 - 如未科技')
@section('breadcrumb')
    <li><a href="{{url('/admin')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info main-list-body">

            <div class="box-header with-border" style="margin:16px 0;">

                <h3 class="box-title">记录列表</h3>

                <div class="pull-right _none">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

            </div>


            <div class="box-body datatable-body item-main-body" id="item-main-body">

                <div class="row col-md-12 datatable-search-row">
                    <div class="input-group">

                        <input type="text" class="form-control form-filter item-search-keyup" name="title" placeholder="标题" />

                        <select class="form-control form-filter" name="record_type" style="width:100px;">
                            <option value="-1">全部操作</option>
                            <option value="1" @if($record_type == '1') selected="selected" @endif>访问</option>
                            <option value="2" @if($record_type == '2') selected="selected" @endif>分享</option>
                            <option value="3" @if($record_type == '3') selected="selected" @endif>查询</option>
                            <option value="Others" @if($record_type == 'Others') selected="selected" @endif>其他</option>
                        </select>

                        <select class="form-control form-filter" name="open_device_type" style="width:100px;">
                            <option value="-1">全部设备</option>
                            <option value="1" @if($open_device_type == '1') selected="selected" @endif>移动端</option>
                            <option value="2" @if($open_device_type == '2') selected="selected" @endif>PC端</option>
                            <option value="Others" @if($open_device_type == 'Others') selected="selected" @endif>其他</option>
                        </select>

                        <select class="form-control form-filter" name="open_system" style="width:100px;">
                            <option value="-1">全部系统</option>
                            <option value="1" @if($open_system == '1') selected="selected" @endif>默认</option>
                            <option value="Android" @if($open_system == 'Android') selected="selected" @endif>Android</option>
                            <option value="iPhone" @if($open_system == 'iPhone') selected="selected" @endif>iPhone</option>
                            <option value="iPad" @if($open_system == 'iPad') selected="selected" @endif>iPad</option>
                            <option value="Mac" @if($open_system == 'Mac') selected="selected" @endif>Mac</option>
                            <option value="Windows" @if($open_system == 'Windows') selected="selected" @endif>Windows</option>
                            <option value="Others" @if($open_system == 'Others') selected="selected" @endif>其他</option>
                        </select>

                        <select class="form-control form-filter" name="open_browser" style="width:80px;">
                            <option value="-1">全部浏览器</option>
                            <option value="1" @if($open_browser == '1') selected="selected" @endif>默认</option>
                            <option value="Chrome" @if($open_browser == 'Chrome') selected="selected" @endif>Chrome</option>
                            <option value="Firefox" @if($open_browser == 'Firefox') selected="selected" @endif>Firefox</option>
                            <option value="Safari" @if($open_browser == 'Safari') selected="selected" @endif>Safari</option>
                            <option value="Others" @if($open_browser == 'Others') selected="selected" @endif>其他</option>
                        </select>

                        <select class="form-control form-filter" name="open_app" style="width:80px;">
                            <option value="-1">全部APP</option>
                            <option value="1" @if($open_app == '1') selected="selected" @endif>默认</option>
                            <option value="WeChat" @if($open_app == 'WeChat') selected="selected" @endif>微信</option>
                            <option value="QQ" @if($open_app == 'QQ') selected="selected" @endif>QQ</option>
                            <option value="Alipay" @if($open_app == 'Alipay') selected="selected" @endif>支付宝</option>
                            <option value="Douyin" @if($open_app == 'Douyin') selected="selected" @endif>抖音</option>
                            <option value="Others" @if($open_app == 'Others') selected="selected" @endif>其他</option>
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

            <div class="box-footer">
                <div class="row" style="margin:16px 0;">
                    <div class="col-md-offset-0 col-md-4 col-sm-8 col-xs-12">
                        {{--<button type="button" class="btn btn-primary"><i class="fa fa-check"></i> 提交</button>--}}
                        {{--<button type="button" onclick="history.go(-1);" class="btn btn-default">返回</button>--}}
                        <div class="input-group">
                            <span class="input-group-addon"><input type="checkbox" id="check-review-all"></span>
                            <select name="bulk-operat-status" class="form-control form-filter">
                                <option value="0">请选择</option>
                                <option value="待审核">待审核</option>
                                <option value="优化中">优化中</option>
                                <option value="合作停">合作停</option>
                                <option value="">被拒绝</option>
                            </select>
                            <span class="input-group-addon btn btn-default" id="operat-bulk-submit"><i class="fa fa-check"></i> 批量操作</span>
                            <span class="input-group-addon btn btn-default" id="delete-bulk-submit"><i class="fa fa-trash-o"></i> 批量删除</span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer">
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




<div class="modal fade" id="modal-body">
    <div class="col-md-8 col-md-offset-2" id="edit-ctn" style="margin-top:64px;margin-bottom:64px;background:#fff;">

        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PORTLET-->
                <div class="box- box-info- form-container">

                    <div class="box-header with-border" style="margin:16px 0;">
                        <h3 class="box-title">内容详情</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <form action="" method="post" class="form-horizontal form-bordered" id="form-edit-modal">
                        <div class="box-body">

                            {{csrf_field()}}
                            <input type="hidden" name="operate" value="item-detail" readonly>
                            <input type="hidden" name="id" value="0" readonly>

                            {{--标题--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">标题</label>
                                <div class="col-md-8 ">
                                    <div><b class="item-detail-title"></b></div>
                                </div>
                            </div>
                            {{--内容--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">内容</label>
                                <div class="col-md-8 ">
                                    <div class="item-detail-content"></div>
                                </div>
                            </div>
                            {{--附件--}}
                            <div class="form-group">
                                <label class="control-label col-md-2">附件</label>
                                <div class="col-md-8 ">
                                    <div class="item-detail-attachment"></div>
                                </div>
                            </div>
                            {{--说明--}}
                            <div class="form-group _none">
                                <label class="control-label col-md-2">说明</label>
                                <div class="col-md-8 control-label" style="text-align:left;">
                                    <span class="">这是一段说明。</span>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="box-footer">
                        <div class="row _none">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="button" class="btn btn-success" id="item-site-submit"><i class="fa fa-check"></i> 提交</button>
                                <button type="button" class="btn btn-default modal-cancel" id="item-site-cancel">取消</button>
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




@section('custom-css')
@endsection
@section('custom-style')
    <style>
        .tableArea table { min-width:1360px; }
        /*.tableArea table { width:100% !important; min-width:1380px; }*/
        /*.tableArea table tr th, .tableArea table tr td { white-space:nowrap; }*/

        .datatable-search-row .input-group .date-picker-btn { width:30px; }
        .table-hover>tbody>tr:hover td { background-color: #bbccff; }

        .select2-container { height:100%; border-radius:0; float:left; }
        .select2-container .select2-selection--single { border-radius:0; }
        .bg-fee-2 { background:#C3FAF7; }
        .bg-fee { background:#8FEBE5; }
        .bg-deduction { background:#C3FAF7; }
        .bg-income { background:#8FEBE5; }
        .bg-route { background:#FFEBE5; }
        .bg-finance { background:#E2FCAB; }
        .bg-empty { background:#F6C5FC; }
        .bg-journey { background:#F5F9B4; }
    </style>
@endsection




@section('custom-script')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50, 100, 200], ["50", "10", "200"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "{{ url('/record/visit-list') }}",
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.title = $('input[name="title"]').val();
                        d.record_type = $('select[name="record_type"]').val();
                        d.open_device_type = $('select[name="open_device_type"]').val();
                        d.open_system = $('select[name="open_system"]').val();
                        d.open_browser = $('select[name="open_browser"]').val();
                        d.open_app = $('select[name="open_app"]').val();
//                        d.nickname 	= $('input[name="nickname"]').val();
//                        d.certificate_type_id = $('select[name="certificate_type_id"]').val();
//                        d.certificate_state = $('select[name="certificate_state"]').val();
//                        d.admin_name = $('input[name="admin_name"]').val();
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
                    // {
                    //     "title": "选择",
                    //     "data": "id",
                    //     "width": "40px",
                    //     'orderable': false,
                    //     render: function(data, type, row, meta) {
                    //         return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                    //     }
                    // },
                    // {
                    //     "title": "序号",
                    //     "data": null,
                    //     "width": "40px",
                    //     "targets": 0,
                    //     'orderable': false
                    // },
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "font-12px",
                        "width": "50px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "访问者",
                        "data": "creator_id",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null
                                ? '<small class="btn-xs bg-black">游客</small>'
                                : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "title": "操作类型",
                        "data": "record_category",
                        "width": "80px",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            if(data == 1)
                            {
                                return '<small class="btn-xs bg-primary">访问</small>';
                            }
                            else if(data == 11)
                            {
                                return '<small class="btn-xs bg-purple">查询</small>';
                            }
                            else if(data == 66)
                            {
                                return '<small class="btn-xs bg-olive">分享</small>';
                            }
                            else if(data == 99)
                            {
                                return '<small class="btn-xs bg-purple">登录</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs bg-black">Error</small>';
                            }
                        }
                    },
                    {
                        "title": "操作结果",
                        "data": "page_type",
                        "width": "80px",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            if(row.record_category == 1)
                            {
                                return '<small class="btn-xs bg-primary">访问</small>';
                            }
                            else if(row.record_category == 33)
                            {
                                return '<small class="btn-xs bg-purple">查询</small>';
                            }
                            else if(row.record_category == 66)
                            {
                                return '<small class="btn-xs bg-olive">分享</small>';
                            }
                            else if(row.record_category == 99)
                            {
                                if(row.record_type == 0)
                                {
                                    return '<small class="btn-xs bg-primary">访问</small>';
                                }
                                else if(row.record_type == 1)
                                {
                                    return '<small class="btn-xs bg-olive">登录成功</small>';
                                }
                                else if(row.record_type == 99)
                                {
                                    return '<small class="btn-xs bg-orange">密码错误</small>';
                                }
                                else
                                {
                                    return '<small class="btn-xs bg-black">Error</small>';
                                }
                            }
                            else
                            {
                                return '<small class="btn-xs bg-black">Error</small>';
                            }

                        }
                    },
                    {
                        "title": "访问",
                        "data": "open",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.page_module == 1)
                            {
                                return '<small class="btn-xs bg-default">首页</small>';
                            }
                            else if(row.page_module == 2)
                            {
                                if(data == "order-list") return '<small class="btn-xs bg-blue">工单页</small>';
                                else if(data == "order-list") return '<small class="btn-xs bg-orange">工单页</small>';
                                else if(data == "delivery-list") return '<small class="btn-xs bg-olive">交付页</small>';
                                else return '<small class="btn-xs black">Error</small>';
                            }
                            else if(row.page_module == 3)
                            {
                                return '<small class="btn-xs bg-red">导出页</small>';
                            }
                            else if(row.page_module == 99)
                            {
                                return '<small class="btn-xs bg-olive">登录页</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs bg-black">Error</small>';
                            }
                        }
                    },
                    {
                        "title": "页数",
                        "data": "page_num",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.record_category == 99 && row.record_type == 99)
                            {
                                return row.user == null ? '--' : '<a href="javascript:void(0);">'+row.user.username+'</a>';
                            }
                            else
                            {
                                if(data == 0) return '--';
                                else return data;
                            }
                        }
                    },
                    {
                        "title": "移动端",
                        "data": "open_device_type",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-primary">移动端</small>';
                            else if(data == 2) return '<small class="btn-xs bg-olive">PC端</small>';
                            // else return '<small class="btn-xs bg-black">Error</small>';
                            else return 'Error';
                        }
                    },
                    {
                        "title": "设备",
                        "data": "open_device_name",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            if(data == "Unknown") return '未知';
                            else if(data == "iPhone") return '<small class="btn-xs bg-olive">iPhone</small>';
                            else if(data == "iPad") return '<small class="btn-xs bg-olive">iPad</small>';
                            else if(data == "Mac") return '<small class="btn-xs bg-olive">Mac</small>';
                            else if(data == "Macintosh") return '<small class="btn-xs bg-olive">Mac</small>';
                            else if(data == "HUAWEI") return '<small class="btn-xs bg-primary">华为</small>';
                            else if(data == "HONOR") return '<small class="btn-xs bg-primary">HONOR</small>';
                            else if(data == "MIPhone") return '<small class="btn-xs bg-primary">小米</small>';
                            else if(data == "VIVO") return '<small class="btn-xs bg-primary">VIVO</small>';
                            else if(data == "OPPO") return '<small class="btn-xs bg-primary">OPPO</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "系统",
                        "data": "open_system",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            // if(data == "Unknown") return '<small class="btn-xs bg-black">未知</small>';
                            if(data == "Unknown") return '未知';
                            else if(data == "Android") return '<small class="btn-xs bg-primary">安卓</small>';
                            else if(data == "iPhone") return '<small class="btn-xs bg-olive">iPhone</small>';
                            else if(data == "iPad") return '<small class="btn-xs bg-olive">iPad</small>';
                            else if(data == "Mac") return '<small class="btn-xs bg-olive">Mac</small>';
                            else if(data == "Windows") return '<small class="btn-xs bg-purple">微软</small>';
                            else if(data == "HarmonyOS") return '<small class="btn-xs bg-purple">鸿蒙</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "浏览器",
                        "data": "open_browser",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            // if(data == "Unknown") return '<small class="btn-xs bg-black">未知</small>';
                            if(data == "Unknown") return '未知';
                            else if(data == "Chrome") return '<small class="btn-xs bg-olive">Chrome</small>';
                            else if(data == "Firefox") return '<small class="btn-xs bg-orange">Firefox</small>';
                            else if(data == "Safari") return '<small class="btn-xs bg-primary">Safari</small>';
                            else if(data == "Vivo") return '<small class="btn-xs bg-purple">Vivo</small>';
                            else if(data == "Oppo") return '<small class="btn-xs bg-purple">Oppo</small>';
                            else if(data == "Mi") return '<small class="btn-xs bg-purple">Mi</small>';
                            else if(data == "Miui") return '<small class="btn-xs bg-purple">Miui</small>';
                            else if(data == "Samsung") return '<small class="btn-xs bg-purple">Samsung</small>';
                            else if(data == "honor") return '<small class="btn-xs bg-purple">honor</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "APP",
                        "data": "open_app",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            // if(data == "Unknown") return '<small class="btn-xs bg-black">未知</small>';
                            if(data == "Unknown") return '未知';
                            else if(data == "WeChat") return '<small class="btn-xs bg-olive">微信</small>';
                            else if(data == "QQ") return '<small class="btn-xs bg-orange">QQ</small>';
                            else if(data == "Alipay") return '<small class="btn-xs bg-primary">支付宝</small>';
                            else if(data == "Douyin") return '<small class="btn-xs bg-black">抖音</small>';
                            else if(data == "Baiduboxapp") return '<small class="btn-xs bg-purple">百度APP</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "网络",
                        "data": "open_NetType",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            // if(data == "Unknown") return '<small class="btn-xs bg-black">未知</small>';
                            if(data == "Unknown") return '未知';
                            else if(data == "4G") return '<small class="btn-xs bg-primary">4G</small>';
                            else if(data == "5G") return '<small class="btn-xs bg-primary">5G</small>';
                            else if(data == "WiFi") return '<small class="btn-xs bg-olive">WiFi</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "蜘蛛",
                        "data": "open_is_spider",
                        "className": "",
                        "width": "80px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
                            // if(data == "Unknown") return '<small class="btn-xs bg-black">未知</small>';
                            if(data == "Unknown") return '--';
                            else if(data == "BaiduSpider") return '<small class="btn-xs bg-olive">百度蜘蛛</small>';
                            else if(data == "SogouSpider") return '<small class="btn-xs bg-orange">搜狗蜘蛛</small>';
                            else if(data == "YisouSpider") return '<small class="btn-xs bg-primary">神马蜘蛛</small>';
                            else if(data == "bingbot") return '<small class="btn-xs bg-primary">Bing</small>';
                            else if(data == "AhrefsBot") return '<small class="btn-xs bg-primary">AhrefsBot</small>';
                            else if(data == "spider") return '<small class="btn-xs bg-black">蜘蛛</small>';
                            else if(data == "Bot") return '<small class="btn-xs bg-black">Bot</small>';
                            else return data;
                        }
                    },
                    {
                        "title": "IP",
                        "data": "ip",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
//                            return data;
                            return '<a target="_blank" href="https://www.ip138.com/iplookup.asp?action=2&ip='+data+'">'+data+'</a>';
                        }
                    },
                    {
                        "title": "IP_INFO",
                        "data": "ip_info",
                        "className": "text-left",
                        "width": "200px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                           return data;
                        }
                    },
                    {
                        "title": "访问时间",
                        "data": 'created_at',
                        "className": "",
                        "width": "160px",
                        "orderable": true,
                        render: function(data, type, row, meta) {
//                            return data;
                            var $date = new Date(data*1000);
                            var $year = $date.getFullYear();
                            var $month = ($date.getMonth()+1);
                            var $day = $date.getDate();
//                            var $year = ('0000'+$date.getFullYear()).slice(-2);
//                            var $month = ('00'+($date.getMonth()+1)).slice(-2);
//                            var $day = ('00'+($date.getDate())).slice(-2);
                            var $hour = ('00'+$date.getHours()).slice(-2);
                            var $minute = ('00'+$date.getMinutes()).slice(-2);
                            var $second = ('00'+$date.getSeconds()).slice(-2);
//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    },
                    {
                        "width": "120px",
                        "title": "操作",
                        "data": 'id',
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                // $(nTd).addClass('modal-show-for-info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','info');
                                $(nTd).attr('data-key','id').attr('data-value',data);
                                $(nTd).attr('data-browser-info',row.browser_info);
                                $(nTd).attr('data-text-type','text');
                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {

                            var html =
                                    '<a class="btn btn-xs bg-navy item-admin-delete-submit" data-id="'+data+'">删除</a>'+
                                    '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                                    '';
                            return html;
                        }
                    }
                ],
                "drawCallback": function (settings) {

                    // let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
                    // this.api().column(1).nodes().each(function(cell, i) {
                    //     cell.innerHTML =  startIndex + i + 1;
                    // });

                    var $obj = new Object();
                    if($('input[name="record-id"]').val())  $obj.record_id = $('input[name="record-id"]').val();

                    if($('select[name="record_type"]').val() != '-1')  $obj.record_type = $('select[name="record_type"]').val();
                    if($('select[name="open_device_type"]').val() != '-1')  $obj.open_device_type = $('select[name="open_device_type"]').val();
                    if($('select[name="open_system"]').val() != '-1')  $obj.open_system = $('select[name="open_system"]').val();
                    if($('select[name="open_browser"]').val() != '-1')  $obj.open_browser = $('select[name="open_browser"]').val();
                    if($('select[name="open_app"]').val() != '-1')  $obj.open_app = $('select[name="open_app"]').val();

                    var $page_length = this.api().context[0]._iDisplayLength; // 当前每页显示多少
                    if($page_length != 50) $obj.length = $page_length;
                    var $page_start = this.api().context[0]._iDisplayStart; // 当前页开始
                    var $pagination = ($page_start / $page_length) + 1; //得到页数值 比页码小1
                    if($pagination > 1) $obj.page = $pagination;


                    if(JSON.stringify($obj) != "{}")
                    {
                        var $url = url_build('',$obj);
                        history.replaceState({page: 1}, "", $url);
                    }
                    else
                    {
                        $url = "{{ url('/record/visit-list') }}";
                        if(window.location.search) history.replaceState({page: 1}, "", $url);
                    }


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
@include(env('TEMPLATE_DK_ADMIN').'entrance.record.visit-list-script')
@endsection
