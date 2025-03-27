<script>


    (function ($) {
        $.getUrlParam = function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]); return null;
        }
    })(jQuery);


    $(function() {




        $.post(
            "/is_ip_login",
            {
                _token: $('meta[name="_token"]').attr("content")
            },
            function(result)
            {
                if(result.result != 'access')
                {
                    // layer.msg('该账户在其他设备登录或退出，即将跳转登录页面！');
                    layer.msg('IP禁止登录！');
                    setTimeout(function(){
                        location.href = "{{ url('/logout_without_token') }}";
                    }, 600);
                }
            }
        );


        // 【清空只读文本框】
        $(".main-content").on('click', ".readonly-clear-this", function() {
            var $that = $(this);
            var $parent = $that.parents('.readonly-picker');
            $parent.find('input').val('');
        });




        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: 'YYYY-MM-DD HH:mm', // 同时包含日期和时间
            sideBySide: true,           // 并排显示日期和时间选择器
            stepping: 1,               // 时间步长（可选，例如15分钟）
            ignoreReadonly: true
        });


        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });
        $('.month_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM",
            ignoreReadonly: true
        });


        $('.form_datetime').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $(".form_date").datepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            ignoreReadonly: true
        });




        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });


        //
        $('.item-select2-project').select2({
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





    function filter(str)
    {
        // 特殊字符转义
        str += ''; // 隐式转换
        str = str.replace(/%/g, '%25');
        str = str.replace(/\+/g, '%2B');
        str = str.replace(/ /g, '%20');
        str = str.replace(/\//g, '%2F');
        str = str.replace(/\?/g, '%3F');
        str = str.replace(/&/g, '%26');
        str = str.replace(/\=/g, '%3D');
        str = str.replace(/#/g, '%23');
        return str;
    }


    function formateObjToParamStr(paramObj)
    {
        const sdata = [];
        for (let attr in paramObj)
        {
            sdata.push('${attr}=${filter(paramObj[attr])}');
        }
        return sdata.join('&');
    }


    function url_build(path, params)
    {
        var url = "" + path;
        var _paramUrl = "";
        // url 拼接 a=b&c=d
        if(params)
        {
            _paramUrl = Object.keys(params).map(function (k) {
                return [encodeURIComponent(k), encodeURIComponent(params[k])].join("=");
            }).join("&");
            _paramUrl = "?" + _paramUrl
        }
        return url + _paramUrl
    }


    function go_back()
    {
        var $url = window.location.href;  // 返回完整 URL (https://www.runoob.com/html/html-tutorial.html?id=123)
        var $origin = window.location.origin;  // 返回基础 URL (https://www.runoob.com/)
        var $domain = document.domain;  // 返回域名部分 (www.runoob.com)
        var $pathname = window.location.pathname;  // 返回路径部分 (/html/html-tutorial.html)
        var $search= window.location.search;  // 返回参数部分 (?id=123)
    }


    // date 代表指定的日期，格式：2018-09-27
    // day 传-1表始前一天，传1表始后一天
    // JS获取指定日期的前一天，后一天
    function getNextDate(date, day)
    {
        var dd = new Date(date);
        dd.setDate(dd.getDate() + day);
        var y = dd.getFullYear();
        var m = dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1;
        var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
        return y + "-" + m + "-" + d;
    };


    // console.log($(window).height());  // 浏览器当前窗口可视区域高度
    // console.log($(document).height());  // 浏览器当前窗口文档的高度
    // console.log($(document.body).height());  // 浏览器当前窗口文档 body 的高度
    // console.log($(document.body).outerHeight(true));  // 文档body 的总高度 （border padding margin)





</script>