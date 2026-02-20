<script>


    if(sessionStorage.getItem('session_unique_id'))
    {
        sessionStorage.setItem('session_unique_id',1);
    }

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
                    console.log('DataTable 已初始化');
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
            // 1. 格式控制是否显示时间
            format: 'YYYY-MM-DD HH:mm',  // 包含HH:mm自动启用时间选择
            // format: 'YYYY-MM-DD',      // 只显示日期

            // 2. 显示模式
            sideBySide: true,           // ✅ 并排显示日期和时间选择器
            inline: false,              // 内联模式

            // 3. 工具栏按钮
            showTodayButton: true,      // 今天按钮
            showClear: true,            // 清除按钮
            showClose: true,            // 关闭按钮

            // 4. 语言
            locale: moment.locale('zh-cn'),          // 中文

            // 6. 工具栏位置
            toolbarPlacement: 'bottom', // 'top' 或 'bottom'

            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            // 1. 格式控制是否显示时间
            format: 'YYYY-MM-DD',  // 包含HH:mm自动启用时间选择
            // format: 'YYYY-MM-DD',      // 只显示日期

            // 2. 显示模式
            sideBySide: true,           // ✅ 并排显示日期和时间选择器
            inline: false,              // 内联模式

            // 3. 工具栏按钮
            showTodayButton: true,      // 今天按钮
            showClear: true,            // 清除按钮
            showClose: true,            // 关闭按钮

            // 4. 语言
            locale: moment.locale('zh-cn'),          // 中文

            // 6. 工具栏位置
            toolbarPlacement: 'bottom', // 'top' 或 'bottom'

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
                    cache: false
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



    // select2
    $(function() {


        $(document).on('click', '.dropdown-menu.non-auto-hide', function(e) {
            e.stopPropagation();
        });

        $(document).on('scroll', '.modal-body', function(e) {
            e.stopPropagation();
        });

        $('.modal').on('scroll', function(e) {
            e.stopPropagation();
        });

        // 通用
        $('.modal--select2').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


        });

        // 公司
        $('.select2--company').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--company') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


            $that.change(function() {

                var $company_id = $(this).val();

                var $department_target = $(this).data('department-target');
                $($department_target).val(null).trigger('change');
                $($department_target).data('company-id',$company_id);

                var $team_target = $(this).data('team-target');
                $($team_target).val(null).trigger('change');
                $($team_target).data('company-id',$company_id);

                var $staff_target = $(this).data('staff-target');
                $($staff_target).val(null).trigger('change');
                $($staff_target).data('company-id',$company_id);

            });
        });

        // 公司
        $('.select2--company').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--company') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


            $that.change(function() {

                var $company_id = $(this).val();

                var $department_target = $(this).data('department-target');
                $($department_target).val(null).trigger('change');
                $($department_target).data('company-id',$company_id);

                var $team_target = $(this).data('team-target');
                $($team_target).val(null).trigger('change');
                $($team_target).data('company-id',$company_id);

                var $staff_target = $(this).data('staff-target');
                $($staff_target).val(null).trigger('change');
                $($staff_target).data('company-id',$company_id);

            });
        });
        // 部门
        $('.select2--department').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--department') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            // department_category: $(this.data('department-category')).find('input[type="radio"]:checked').val(),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            department_category: this.data('department-category'),
                            department_type: this.data('department-type'),
                            company_id: this.data('company-id'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


            $that.change(function() {

                var $department_id = $(this).val();

                var $team_target = $(this).data('team-target');
                $($team_target).val(null).trigger('change');
                $($team_target).data('department-id',$department_id);

                var $staff_target = $(this).data('staff-target');
                $($staff_target).val(null).trigger('change');
                $($staff_target).data('department-id',$department_id);
            });
        });
        // 团队
        $('.select2--team').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--team') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            team_category: this.data('team-category'),
                            team_type: this.data('team-type'),
                            department_id: this.data('department-id'),
                            superior_team_id: this.data('superior-team-id'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


            $that.change(function() {

                var $team_id = $(this).val();

                var $team_target = $(this).data('team-target');
                $($team_target).val(null).trigger('change');
                $($team_target).data('superior-team-id',$team_id);

                var $staff_target = $(this).data('staff-target');
                $($staff_target).val(null).trigger('change');
                $($staff_target).data('team-id',$team_id);
            });
        });
        // 员工
        $('.select2--staff').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--staff') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            staff_category: this.data('staff-category'),
                            staff_type: this.data('staff-type'),
                            department_id: this.data('department-id'),
                            team_id: this.data('team-id'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });
        });


        $(".main-content").on('change', ".select2--location-city", function() {

            var $city_value = $(this).val();

            var $location_district_target = $(this).data('location-district-target');
            $($location_district_target).val(null).trigger('change');
            $($location_district_target).data('location-city',$city_value);

        });
        // 地区
        $('.select2--location').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--location') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            location_city: this.data('location-city'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });


            $that.change(function() {

                if($that.data('item-category') == 1)
                {
                    var $city_value = $(this).val();

                    var $location_district_target = $(this).data('location-district-target');
                    $($location_district_target).val(null).trigger('change');
                    $($location_district_target).data('location-city',$city_value);
                }
            });
        });


        // 客户
        $('.select2--client').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--client') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            client_category: this.data('client-category'),
                            client_type: this.data('client-type'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });
        });
        // 项目
        $('.select2--project').each(function() {
            var $that = $(this);

            var $dropdownParent = $(document.body);
            var $modalSelector = $that.data('modal');
            if ($modalSelector)
            {
                $dropdownParent = $($modalSelector);
            }

            $that.select2({
                ajax: {
                    url: "{{ url('/o1/select2/select2--project') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: this.data('item-category'),
                            item_type: this.data('item-type'),
                            project_category: this.data('project-category'),
                            project_type: this.data('project-type'),
                            keyword: params.term,
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
                escapeMarkup: function (markup) { return markup; },
                dropdownParent: $dropdownParent.find('.modal-content'),
                minimumInputLength: 0,
                theme: 'classic'
            });
        });


    });





    // 表单初始化
    function form_reset($form_id)
    {
        console.log($form_id+'.form_reset');
        var $form = $($form_id);
        // $form.find('.select2-container').remove();
        // input
        $form.find('textarea.form-control, input.form-control').each(function () {
            $(this).val("");
            $(this).val($(this).data('default'));

        });

        // radio
        $form.find('input[type="radio"][data-default="default"]').prop('checked', true).trigger('change');

        // select
        $form.find('select option').prop("selected",false);
        $form.find('select').find('option:eq(0)').prop('selected', true).trigger('change');


        // $form.find(".select2-box").val(-1).trigger("change");
        // $form.find(".select2-box").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box");
        $.each( $form.find(".select2-box"), function(index, element) {
            select2FirstOptionSelected(element);
        });

        // $form.find(".select2-box-c").val(-1).trigger("change");
        // $form.find(".select2-box-c").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box-c");
        $.each( $form.find(".select2-box-c"), function(index, element) {
            select2FirstOptionSelected(element);
        });


        $.each( $form.find(".select2-reset"), function(index, element) {
            select2FirstOptionSelected(element);
        });


        $.each( $form.find(".select2-multiple-reset"), function(index, element) {
            select2MultipleOptionClear(element);
        });

        // $form.find(".select2-multiple-reset").val([]).trigger('change');
        // $form.find(".select2-multiple-reset").val(null).trigger('change');
        // $form.find(".select2-multiple-reset").empty().trigger('change');

    }


    //
    function selectFirstOption(selector)
    {
        var $select = $(selector);
        var firstVal = $select.find('option:first').val();
        if(firstVal)
        {
            console.log('selectFirstOption is');
            $select.val(firstVal).trigger('change');
        }
        else
        {
            console.log('selectFirstOption not');
            // $select.val([]).trigger('change');
            $select.val(null).trigger('change');
        }
    }

    //
    function select2FirstOptionSelected(dom)
    {
        var $dom = $(dom);
        var firstVal = $dom.find('option:first').val();
        if(firstVal)
        {
            $dom.val(firstVal).trigger('change');
        }
        else
        {
            $dom.val(null).trigger('change');
        }
    }

    //
    function select2MultipleOptionClear(dom)
    {
        var $dom = $(dom);
        $dom.val([]).trigger('change');
        $dom.val(null).trigger('change');
        $dom.empty().trigger('change');
    }





    //
    function form_reset_init($form)
    {
        console.log('form_reset_init');

        $form.find('.textarea-reset, .input-reset').each(function () {
            $(this).val("");
            $(this).val($(this).data('default'));
        });

        $form.find('.radio-reset').first().prop('checked', true);
        // $form.find('.radio-reset').first().attr('checked', 'checked');

        $form.find('.select-reset option').prop('selected',false);
        $form.find('.select-reset').find('option:eq(0)').prop('selected', true);
        // $form.find('select-reset option').attr('selected','');
        // $form.find('select-reset').find('option:eq(0)').attr('selected', 'selected');

        $.each( $form.find(".select2-reset"), function(index, element) {
            select2FirstOptionSelected(element);
        });

        $form.find(".select2-multi-reset").val([]).trigger('change');
        $form.find(".select2-multi-reset").val(null).trigger('change');
        $form.find(".select2-multi-reset").empty().trigger('change');
    }






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