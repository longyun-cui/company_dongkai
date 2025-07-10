<script>
    $(document).ready(function() {

        // 通用标签控制逻辑
        $(".wrapper").on('click', ".tab-control", function() {

            const $btn = $(this);
            const $unique = $btn.data('unique');

            // $(".nav-header-title").html($btn.data('title'));

            if($unique == 'y')
            {
                const $config = {
                    type: $btn.data('type'),
                    unique: $btn.data('unique'),
                    id: $btn.data('id'),
                    title: $btn.data('title'),
                    content: $btn.data('content') || '默认内容'
                };

                const $tabLink = $('a[href="#'+ $config.id +'"]');
                const $tabPane = $('#'+$config.id);

                if($tabPane.length)
                {
                    // 存在则激活
                    console.log('Tab已存在！');
                    $tabLink.tab('show');
                }
                else
                {
                    // 创建新标签页
                    console.log('Tab不存在！');
                    createTab($config);
                    // 激活新标签页
                    $('a[href="#'+$config.id+'"]').tab('show');
                }
            }
            else
            {
                let $session_unique_id = sessionStorage.getItem('session_unique_id');
                sessionStorage.setItem('session_unique_id',parseInt($session_unique_id) + 1);
                $session_unique_id = sessionStorage.getItem('session_unique_id');

                const $btn = $(this);
                const $config = {
                    type: $btn.data('type'),
                    unique: $btn.data('unique'),
                    id: $btn.data('id') + '-' + $session_unique_id,
                    title: $btn.data('title'),
                    content: $btn.data('content') || '默认内容'
                };

                const $tabLink = $('a[href="#'+ $config.id +'"]');
                const $tabPane = $('#'+$config.id);

                if($tabPane.length)
                {
                    // 存在则激活
                    console.log('Tab存在');
                    $tabLink.tab('show');
                }
                else
                {
                    // 创建新标签页
                    console.log('Tab不存在');
                    createTab($config);
                    // 激活新标签页
                    $('a[href="#'+$config.id+'"]').tab('show');
                }
            }

        });

        // 关闭标签页处理（事件委托）
        $('.nav-tabs').on('click', '.close-tab', function(e) {
            e.preventDefault();     // 阻止链接默认行为
            e.stopPropagation();    // 阻止事件冒泡
            const $targetTab = $(this).closest('.nav-item');
            const $tabId = $targetTab.find('a').attr('href');

            // 移除对应内容
            $($tabId).remove();
            $targetTab.remove();

            // 自动激活剩余第一个标签页
            $('.nav-tabs .nav-item:first-child a').tab('show');
        });


        // 通用标签控制逻辑
        $(".wrapper").on('click', ".caller-control", function() {

            const $that = $(this);
            const $id = $that.data('id');
            const $title = $that.data('title');
            const $caller_daily_id = 'caller-daily-' + $id;
            const $datatable_id = 'datatable-caller-daily-' + $id;
            const $datatable_clone_object = 'statistic-caller-daily-clone';
            const $datatable_target = $caller_daily_id;
            const $chart_id = "eChart-caller-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            const $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $caller_daily_id,
                title: $that.data('title'),
                content: $that.data('content') || '默认内容'
            };

            const $tabLink = $('a[href="#'+ $caller_daily_id +'"]');
            const $tabPane = $('#' + $caller_daily_id);

            if($tabPane.length)
            {
                // 存在则激活
                console.log('已存在！');
                $tabLink.tab('show');
            }
            else
            {
                // 创建新标签页
                console.log('不存在！');
                createTab($config);
                // 激活新标签页
                $('a[href="#' + $caller_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$config.id))
            {
                console.log($config.id);
                console.log('DataTable 已存在！');
            }
            else
            {
                console.log('DataTable 未初始化！');

                let $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                $clone.find('table').attr('id',$datatable_id);
                $clone.find('input[name="statistic-caller-daily-staff-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$caller_daily_id).prepend($clone);
                $('#'+$caller_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$caller_daily_id).find('.time_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$caller_daily_id).find('.date_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$caller_daily_id).find('.month_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                Datatable_Statistic_Caller_Daily('#'+$datatable_id,$chart_id);
            }

        });


        // 通用标签控制逻辑
        $(".wrapper").on('click', ".datatable-control", function() {

            const $btn = $(this);
            const $id = $btn.data('datatable-id');
            const $unique = $btn.data('datatable-unique');
            const $reload = $btn.data('datatable-reload');

            if($unique == 'y')
            {
                var $config = {
                    type: $btn.data('datatable-type'),
                    unique: $btn.data('datatable-unique'),
                    id: $btn.data('datatable-id'),
                    target: $btn.data('datatable-target'),
                    clone_object: $btn.data('datatable-clone-object'),

                    chart_id: $btn.data('chart-id')
                };

            }
            else
            {
                let $session_unique_id = sessionStorage.getItem('session_unique_id');

                var $config = {
                    type: $btn.data('datatable-type'),
                    unique: $btn.data('datatable-unique'),
                    id: $btn.data('datatable-id') + '-' + $session_unique_id,
                    target: $btn.data('datatable-target') + '-' + $session_unique_id,
                    clone_object: $btn.data('datatable-clone-object'),

                    chart_id: $btn.data('chart-id')
                };
            }


            if($.fn.DataTable.isDataTable('#'+$config.id))
            {
                console.log($config.id);
                console.log('DataTable 已存在！');
                if($reload == 'y')
                {
                    $('#'+$config.id).DataTable().ajax.reload(null,false);
                }
            }
            else
            {
                console.log('DataTable 未初始化！');

                let $clone = $('.'+$config.clone_object).clone(true);
                $clone.removeClass($config.clone_object);
                $clone.addClass('datatable-wrapper');
                $clone.find('table').attr('id',$config.id);

                console.log($config.chart_id);
                $clone.find('.eChart').attr('id',$config.chart_id);

                $('#'+$config.target).prepend($clone);

                $('#'+$config.target).find('.time_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.date_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.month_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                $('#'+$config.target).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').change(function() {

                    var $that = $(this);
                    var $target = $that.data('target');

                    var $select2_wrapper = $that.parents('.select2-wrapper');

                    console.log($select2_wrapper.find($target).val());
                    // $form.find(".select2-box").val(-1).trigger("change");
                    // $form.find(".select2-box").val("-1").trigger("change");
                    // $select2_wrapper.find($target).val(-1).trigger("change");
                    // $select2_wrapper.find($target).find('-1').trigger("change");
                    $select2_wrapper.find($target).find('option:eq(0)').prop('selected', true).trigger("change");
                });



                // select2
                $('#'+$config.target).find('.select2-department-group').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_department') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                keyword: params.term, // search term
                                page: params.page,
                                type: 'group',
                                superior_id: $(this).parents('.select2-wrapper').find($(this).data('target')).val()
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
                $('#'+$config.target).find('.select2-project-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_project') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                item_category: this.data('item-category'),
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
                $('#'+$config.target).find('.select2-client-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_client') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                user_category: this.data('user-category'),
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




                if($id == "datatable-list")
                {
                }
                else if($id == "datatable-delivery-list")
                {
                    Datatable_for_DeliveryList('#'+$config.id);
                }
                else if($id == "datatable-delivery-daily")
                {
                    Datatable_for_DeliveryDaily('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-department-list")
                {
                    Datatable_for_DepartmentList('#'+$config.id);
                }
                else if($id == "datatable-staff-list")
                {
                    Datatable_for_StaffList('#'+$config.id);
                }
                else if($id == "datatable-company-list")
                {
                    Datatable_for_CompanyList('#'+$config.id);
                }
                else if($id == "datatable-client-list")
                {
                    Datatable_for_ClientList('#'+$config.id);
                }
                else if($id == "datatable-project-list")
                {
                    Datatable_for_ProjectList('#'+$config.id);
                }
                else if($id == "datatable-location-list")
                {
                    Datatable_for_LocationList('#'+$config.id);
                }
                else if($id == "datatable-order-list")
                {
                    Datatable_for_OrderList($config.id);
                }
                else if($id == "datatable-order-aesthetic-list")
                {
                    Datatable_for_Order_Aesthetic_List($config.id);
                }
                else if($id == "datatable-order-luxury-list")
                {
                    Datatable_for_Order_Luxury_List($config.id);
                }
                else if($id == "datatable-delivery-list")
                {
                    Datatable_for_DeliveryList('#'+$config.id);
                }
                else if($id == "datatable-delivery-aesthetic-list")
                {
                    Datatable_for_Delivery_Aesthetic_List('#'+$config.id);
                }
                else if($id == "datatable-delivery-luxury-list")
                {
                    Datatable_for_Delivery_Luxury_List('#'+$config.id);
                }
                else if($id == "datatable-finance-daily")
                {
                    Datatable_for_FinanceDaily('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-statistic-company-overview")
                {
                    Table_Datatable_Ajax_Statistic_Company_Overview('#'+$config.id);
                }
                else if($id == "datatable-statistic-company-daily")
                {
                    Table_Datatable_Ajax_Statistic_Company_Daily('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-statistic-marketing-project")
                {
                    Table_Datatable_Ajax_Statistic_Marketing_Project('#'+$config.id);
                }
                else if($id == "datatable-statistic-marketing-client")
                {
                    Table_Datatable_Ajax_Statistic_Marketing_Client('#'+$config.id);
                }
                else if($id == "datatable-statistic-caller-overview")
                {
                    Table_Datatable_Ajax_Statistic_Caller_Overview('#'+$config.id);
                }
                else if($id == "datatable-statistic-caller-rank")
                {
                    Table_Datatable_Ajax_Statistic_Caller_Rank('#'+$config.id);
                }
                else if($id == "datatable-statistic-caller-recent")
                {
                    Table_Datatable_Ajax_Statistic_Caller_Recent('#'+$config.id);
                }
                else if($id == "datatable-statistic-inspector-overview")
                {
                    Table_Datatable_Ajax_Statistic_Inspector_Overview('#'+$config.id);
                }
                else if($id == "datatable-statistic-deliverer-overview")
                {
                    Table_Datatable_Ajax_Statistic_Deliverer_Overview('#'+$config.id);
                }
                else if($id == "datatable-statistic-comprehensive-daily")
                {
                    Datatable_Statistic_Comprehensive_Daily('#'+$config.id, $config.chart_id);
                }
                else if($id == "statistic-comprehensive-overview")
                {
                    // 初始化
                    $("#filter-submit-for-comprehensive").click();
                }
                else if($id == "datatable-statistic-export")
                {
                    Datatable_for_ExportList('#'+$config.id);
                }
                else if($id == "datatable-statistic-production-project")
                {
                    Table_Datatable_Ajax_Statistic_Production_Project('#'+$config.id);
                }
                else if($id == "datatable-statistic-production-department")
                {
                    Table_Datatable_Ajax_Statistic_Production_Department('#'+$config.id);
                }
                else if($id == "datatable-statistic-call-list")
                {
                    Datatable_Statistic_Call_List('#'+$config.id);
                }
                else if($id == "datatable-statistic-call-daily-month")
                {
                    Datatable_Statistic_Call_Daily_Month('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-statistic-call-order-daily-month")
                {
                    Datatable_Statistic_Call_Order_Daily_Month('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-statistic-call-order-city")
                {
                    Datatable_Statistic_Call_Order_City('#'+$config.id, $config.chart_id);
                }
                else if($id == "datatable-call-record-list")
                {
                    Datatable_Call_Record_List('#'+$config.id);
                }

            }


        });


        // 通用标签控制逻辑
        $(".wrapper").on('click', ".comprehensive-control", function() {

            const $btn = $(this);
            const $id = $btn.data('comprehensive-id');
            const $unique = $btn.data('comprehensive-unique');
            const $reload = $btn.data('comprehensive-reload');

            if($unique == 'y')
            {
                var $config = {
                    type: $btn.data('comprehensive-type'),
                    unique: $btn.data('comprehensive-unique'),
                    id: $btn.data('comprehensive-id'),
                    target: $btn.data('comprehensive-target'),
                    clone_object: $btn.data('comprehensive-clone-object'),

                    chart_id: $btn.data('chart-id')
                };

            }
            else
            {
                let $session_unique_id = sessionStorage.getItem('session_unique_id');

                var $config = {
                    type: $btn.data('comprehensive-type'),
                    unique: $btn.data('comprehensive-unique'),
                    id: $btn.data('comprehensive-id') + '-' + $session_unique_id,
                    target: $btn.data('comprehensive-target') + '-' + $session_unique_id,
                    clone_object: $btn.data('comprehensive-clone-object'),

                    chart_id: $btn.data('chart-id')
                };
            }


            if($('#'+$config.id).length)
            {
                console.log('comprehensive 已存在！');
            }
            else
            {
                console.log('comprehensive 未初始化！');

                let $clone = $('.'+$config.clone_object).clone(true);
                $clone.removeClass($config.clone_object);
                $clone.addClass('comprehensive-wrapper');
                $clone.attr('id',$config.id);

                $clone.find('.eChart').attr('id',$config.chart_id);

                $('#'+$config.target).prepend($clone);

                $('#'+$config.target).find('.time_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.date_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.month_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                $('#'+$config.target).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').change(function() {

                    var $that = $(this);
                    var $target = $that.data('target');

                    var $select2_wrapper = $that.parents('.select2-wrapper');

                    console.log($select2_wrapper.find($target).val());
                    // $form.find(".select2-box").val(-1).trigger("change");
                    // $form.find(".select2-box").val("-1").trigger("change");
                    // $select2_wrapper.find($target).val(-1).trigger("change");
                    // $select2_wrapper.find($target).find('-1').trigger("change");
                    $select2_wrapper.find($target).find('option:eq(0)').prop('selected', true).trigger("change");
                });



                // select2
                $('#'+$config.target).find('.select2-department-group').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_department') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                keyword: params.term, // search term
                                page: params.page,
                                type: 'group',
                                superior_id: $(this).parents('.select2-wrapper').find($(this).data('target')).val()
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
                $('#'+$config.target).find('.select2-project-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_project') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                item_category: this.data('item-category'),
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
                $('#'+$config.target).find('.select2-client-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_client') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                user_category: this.data('user-category'),
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




                if($id == "comprehensive-list")
                {
                }
                else if($id == "statistic-comprehensive")
                {
                    statistic_get_data_for_comprehensive('#'+$config.target);
                }
                else if($id == "statistic-comprehensive-overview")
                {
                    // 初始化
                    $("#filter-submit-for-comprehensive").click();
                }

            }


        });


        // 通用标签控制逻辑
        $(".wrapper").on('click', ".call-daily-overview-control", function() {

            const $btn = $(this);
            const $id = $btn.data('control-id');
            const $unique = $btn.data('control-unique');
            const $reload = $btn.data('control-reload');

            if($unique == 'y')
            {
                var $config = {
                    type: $btn.data('control-type'),
                    unique: $btn.data('control-unique'),
                    id: $btn.data('control-id'),
                    target: $btn.data('control-target'),
                    clone_object: $btn.data('control-clone-object'),

                    chart_id: $btn.data('chart-id')
                };

            }
            else
            {
                let $session_unique_id = sessionStorage.getItem('session_unique_id');

                var $config = {
                    type: $btn.data('control-type'),
                    unique: $btn.data('control-unique'),
                    id: $btn.data('control-id') + '-' + $session_unique_id,
                    target: $btn.data('control-target') + '-' + $session_unique_id,
                    clone_object: $btn.data('control-clone-object'),

                    chart_id: $btn.data('chart-id')
                };
            }


            if($('#'+$config.id).length)
            {
                console.log('control 已存在！');
            }
            else
            {
                console.log('control 未初始化！');

                let $clone = $('.'+$config.clone_object).clone(true);
                $clone.removeClass($config.clone_object);
                $clone.addClass('control-wrapper');
                $clone.attr('id',$config.id);

                $clone.find('.eChart').attr('id',$config.chart_id);

                $('#'+$config.target).prepend($clone);

                $('#'+$config.target).find('.time_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.date_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$config.target).find('.month_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                $('#'+$config.target).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').select2({
                    theme: 'classic'
                });
                $('#'+$config.target).find('.select2-box-change').change(function() {

                    var $that = $(this);
                    var $target = $that.data('target');

                    var $select2_wrapper = $that.parents('.select2-wrapper');

                    console.log($select2_wrapper.find($target).val());
                    // $form.find(".select2-box").val(-1).trigger("change");
                    // $form.find(".select2-box").val("-1").trigger("change");
                    // $select2_wrapper.find($target).val(-1).trigger("change");
                    // $select2_wrapper.find($target).find('-1').trigger("change");
                    $select2_wrapper.find($target).find('option:eq(0)').prop('selected', true).trigger("change");
                });



                // select2
                $('#'+$config.target).find('.select2-department-group').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_department') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                keyword: params.term, // search term
                                page: params.page,
                                type: 'group',
                                superior_id: $(this).parents('.select2-wrapper').find($(this).data('target')).val()
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
                $('#'+$config.target).find('.select2-project-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_project') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                item_category: this.data('item-category'),
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
                $('#'+$config.target).find('.select2-client-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_client') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
                                user_category: this.data('user-category'),
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




                if($id == "comprehensive-list")
                {
                }
                else if($id == "statistic-call-daily-overview")
                {
                    statistic_get_data_for_call_daily_overview('#'+$config.target);
                }
                else if($id == "statistic-comprehensive-overview")
                {
                    // 初始化
                    $("#filter-submit-for-comprehensive").click();
                }

            }


        });


    });


    // 创建标签页函数
    function createTab($config)
    {
        // 导航标签模板
        const navItem =
            '<li class="nav-item">'
                +'<a class="nav-link" href="#'+ $config.id +'" data-toggle="tab">'
                    + $config.title
                    +' <i class="fa fa-close ml-2 close-tab"></i>'
                +'</a>'
            +'</li>';

        // 内容面板模板
        const contentPane = '<div class="tab-pane fade" id="'+ $config.id +'"></div>';

        // 添加元素
        $('#index-nav-box').find('.nav-tabs').append(navItem);
        $('#index-nav-box').find('.tab-content').append(contentPane);

        // 自动激活第一个标签页
        if($('.nav-tabs .nav-item').length === 1)
        {
            $('.nav-tabs .nav-item:first-child a').addClass('active');
            $('.tab-content .tab-pane:first-child').addClass('show active');
        }
    }


</script>