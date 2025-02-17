<script>


    (function ($) {
        $.getUrlParam = function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]); return null;
        }
    })(jQuery);


    $(function() {

        var $city;


        $.post(
            "/is_only_me",
            {
                _token: $('meta[name="_token"]').attr("content")
            },
            function(result){
                if(result.result != 'access')
                {
                    // layer.msg('该账户在其他设备登录或退出，即将跳转登录页面！');
                    layer.msg('登录失效，请重新登录！');
                    setTimeout(function(){
                        location.href = "{{ url('/logout_without_token') }}";
                    }, 600);
                }
            }
        );


        $('.select2-box').select2({
            theme: 'classic'
        });


        // 【】
        $(".wrapper").on('click', ".menu-tab-show", function() {

            var $that = $(this);
            var $tab = $that.attr('data-tab');
            var $title = $that.attr('data-title');

            if($('#'+$tab).length)
            {
                // 元素存在
                $(".nav-tabs").find('li').removeClass('active');
                $(".nav-tabs").find('#'+$tab).addClass('active');

                // $(".tab-content").find('.tab-pane').removeClass('active');
                // $(".tab-content").find('#tab-'+$target).addClass('active');

            }
            else
            {
                // 元素不存在
                $(".nav-tabs").find('li').removeClass('active');
                var $nav_html = '<li class="active" id="'+$tab+'"><a href="#tab-'+$tab+'" data-toggle="tab" aria-expanded="true">'+$title+'</a></li>';
                $(".nav-tabs").append($nav_html);

                //
                // $(".tab-content").find('.tab-pane').removeClass('active');
                // var $pane_html = '<div class="tab-pane active" id="tab-'+$target+'">1</div>';
                // $(".tab-content").append($pane_html);
            }

            if($('#tab-'+$tab).length)
            {

                $(".tab-content").find('.tab-pane').removeClass('active');
                $(".tab-content").find('#tab-'+$tab).addClass('active');

                if ($.fn.DataTable.isDataTable('#datatable-for-'+$tab))
                {
                    console.log('DataTable 已初始化1');
                }
                else
                {
                    console.log('DataTable 未初始化');
                    if($tab == 'order-list')
                    {
                        Table_DatatableAjax_order_list.init();
                    }
                    else if($tab == 'department-list')
                    {

                        Table_DatatableAjax_department_list.init();
                    }
                    // $('#datatable-for-'+$tab).DataTable().init();
                    // ('#datatable-for-'+$tab.split("-").join("_"));
                }

            }
            else
            {
                $(".tab-content").find('.tab-pane').removeClass('active');
                var $pane_html = '<div class="tab-pane active" id="tab-'+$tab+'">1</div>';
                $(".tab-content").append($pane_html);
            }


        });


        $('.datatable-search-row .dropdown-menu .box-body').on('click', function(event) {
            // $(this).show();
            event.stopPropagation(); // 阻止事件冒泡
        });


        // 【清空只读文本框】
        $(".main-content").on('click', ".readonly-clear-this", function() {
            var $that = $(this);
            var $parent = $that.parents('.readonly-picker');
            $parent.find('input').val('');
        });


        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
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



        var $district_list = [
            ['东城区','西城区','海淀区','朝阳区','丰台区','门头沟区','石景山区','房山区','通州区','顺义区','昌平区','大兴区','怀柔区','平谷区','延庆区','密云区','其他'],
            ['和平区','河东区','河西区','南开区','河北区','红桥区','滨海新区','东丽区','西青区','津南区','北辰区','武清区','宝坻区','宁河区','静海区','蓟州区','其他'],
            ['黄浦区','徐汇区','长宁区','静安区','普陀区','虹口区','杨浦区','闵行区','宝山区','嘉定区','浦东新区','金山区','松江区','青浦区','奉贤区','崇明区','其他'],
            ['越秀区','荔湾区','海珠区','天河区','白云区','黄埔区','南沙区','番禺区','花都区','从化区','增城区','其他'],
            ['福田区','罗湖区','南山区','盐田区','宝安区','龙岗区','龙华区','坪山区','光明区','大鹏新区','其他'],

            ['玄武区','秦淮区','建邺区','鼓楼区','浦口区','栖霞区','雨花台区','江宁区','六合区','溧水区','高淳区','江北新区','其他'],
            ['海曙区','江北区','北仑区','镇海区','鄞州区','奉化区','象山县','宁海县','余姚市','慈溪市','其他'],
            // ['渝中区','大渡口区','江北区','沙坪坝区','九龙坡区','南岸区','北碚区','渝北区','巴南区','其他'],
            ['渝中区','大渡口区','江北区','沙坪坝区','九龙坡区','南岸区','北碚 bèi 区','渝北区','巴南区','万州区','涪fú陵区','永川区','璧山区','大足区','綦qí江区','江津区','合川区','黔qián江区','长寿区','南川区','铜梁区','潼tóng南区','荣昌区','开州区','梁平区','武隆区','城口县','丰都县','垫江县','忠县','云阳县','奉节县','巫山县','巫溪县','石柱土家族自治县','秀山土家族苗族自治县','酉阳土家族苗族自治县','彭水苗族土家族自治县','其他'],
            ['锦江区','青羊区','金牛区','武侯区','成华区','龙泉驿区','新都区','郫都区','温江区','双流区','青白江区','新津区','都江堰市','彭州市','邛崃市','崇州市','简阳市','金堂县','大邑县','蒲江县','其他'],

            ['上城区','拱墅区','西湖区','滨江区','萧山区','余杭区','临平区','钱塘区','富阳区','临安区','建德市','桐庐县','淳安县','其他'],
            ['姑苏区','虎丘区','吴中区','相城区','吴江区','工业园区','常熟市','张家港市','昆山市','太仓市','其他'],
            ['江岸区','江汉区','硚口区','汉阳区','武昌区','青山区','洪山区','东西湖区','汉南区','蔡甸区','江夏区','黄陂区','新洲区','其他'],
            ['新城区','碑林区','莲湖区','雁塔区','灞桥区','未央区','阎良区','临潼区','长安区','高陵区','鄠邑区','蓝田县','周至县','其他'],
            ['中原区','二七区','管城回族区','金水区','上街区','惠济区','中牟县','巩义市','荥阳市','新密市','新郑市','登封市航空实验区','郑东新区','经济开发区','高新技术产业开发区','其他'],
            ['芙蓉区','天心区','岳麓区','开福区','雨花区','望城区','浏阳市','宁乡市','长沙县','其他'],
            ['云岩区','南明区','花溪区','乌当区','白云区','观山湖区','修文县','息烽县','开阳县','清镇市','其他'],
            ['东湖区','西湖区','青云谱区','青山湖区','新建区','红谷滩区','南昌县','进贤县','安义县','其他'],
            ['和平区','沈河区','铁西区','皇姑区','大东区','浑南区','于洪区','沈北新区','苏家屯区','辽中区','新民市','法库县','康平县','其他'],
            ['历下区','市中区','槐荫区','天桥区','历城区','长清区','章丘区','济阳区','莱芜区','钢城区','平阴县','商河县','其他'],
            ['平城区','云冈区','新荣区','云州区','左云县','阳高县','天镇县','浑源县','广灵县','灵丘县','其他'],

            ['金坛区','武进区','新北区','天宁区','钟楼区','溧阳市','其他'],
            ['鹿城','龙湾','瓯海','洞头','瑞安','乐清','龙港','永嘉','平阳','苍南','文成','泰顺','其他'],
            ['越城区','柯桥区','上虞区','新昌县','诸暨市','嵊州市','其他'],
            ['椒江区','黄岩区','路桥区','天台县','仙居县','三门县','临海市','温岭市','玉环市','其他'],
            ['定海区','普陀区','岱山县','溗泗县','其他'],
            ['麒麟区','宣威市','沾益区','马龙区','师宗县','富源县','陆良县','罗平县','会泽县','其他'],
            ['镜湖区','鸠江区','弋江区','湾沚区','繁昌区','无为市','南陵县','其他'],
            ['花山区','雨山区','博望区','当涂县','含山县','和县','其他'],
            ['清新区','清城区','东城区','新城区','阳山县','佛冈县','连南县','连山县','英德市','连州市','其他'],
            ['碧江区','万山区','松桃苗族自治县','玉屏侗族自治县','印江土家族苗族自治县','沿河土家族自治县','江口县','石阡县','思南县','德江县','大龙经济开发区','高新技术产业开发区','其他'],
            ['自流井区','贡井区','大安区','沿滩区','荣县','富顺县','其他'],
            ['其他']
        ];

        $("#select-city").change(function() {

            var $city_value = $("#select-city").val();
            var $city_index = $("#select-city").find('option:selected').attr('data-index');
            $("#select-district").html('<option value="">选择区划</option>');
            $.each($district_list[$city_index], function($i,$val) {
                $("#select-district").append('<option value="' + $val + '">' + $val + '</option>');
            });
            $('#select-district').select2();

            $('#custom-city').val($city_value);
            $('#custom-district').val('');

        });
        $("#select-district").change(function() {

            var $district_value = $("#select-district").val();
            $('#custom-district').val($district_value);
        });


        $("#select-city-1").change(function() {

            var $city_value = $(this).val();

            $('#custom-city').val($city_value);
            $('#custom-district').val('');

        });
        $("#select-district-1").change(function() {

            var $district_value = $(this).val();
            $('#custom-district').val($district_value);
        });

        $('#select-city').select2({
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#select-district').select2({
            minimumInputLength: 0,
            theme: 'classic'
        });

        $('.select-select2').select2({
            dropdownParent: $('#modal-body-for-order-create'),
            minimumInputLength: 0,
            theme: 'classic'
        });



        $(".select2-district-city").change(function() {

            var $city_value = $(this).val();
            var $target = $(this).attr('data-target');

            $($target).val(null).trigger('change');

            $($target).select2({
                ajax: {
                    url: "/district/district_select2_district?district_city=" + $city_value,
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

        // $('.select2-district-district').select2({
        //     ajax: {
        //         url: "/district/district_select2_district?district_city=" + $city,
        //         dataType: 'json',
        //         delay: 250,
        //         data: function (params) {
        //             return {
        //                 keyword: params.term, // search term
        //                 page: params.page
        //             };
        //         },
        //         processResults: function (data, params) {
        //
        //             params.page = params.page || 1;
        //             return {
        //                 results: data,
        //                 pagination: {
        //                     more: (params.page * 30) < data.total_count
        //                 }
        //             };
        //         },
        //         cache: true
        //     },
        //     escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        //     minimumInputLength: 0,
        //     theme: 'classic'
        // });




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




    // function copyToClipboard(text)
    // {
    //     // 创建一个隐藏的textarea元素
    //     var textarea = document.createElement("textarea");
    //
    //     // 设置要复制的文本内容
    //     textarea.value = text;
    //
    //     // 添加该元素到页面上（但不显示）
    //     document.body.appendChild(textarea);
    //
    //     // 选中并复制文本
    //     textarea.select();
    //     document.execCommand('copy');
    //
    //     // 移除该元素
    //     document.body.removeChild(textarea);
    //
    //     console.log('已经写入：'+text)
    // }
    // copyToClipboard('123321');
    // copyToClipboard('135');


</script>