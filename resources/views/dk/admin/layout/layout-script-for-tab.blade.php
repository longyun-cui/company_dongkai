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
                    console.log('已存在！');
                    $tabLink.tab('show');
                }
                else
                {
                    // 创建新标签页
                    console.log('不存在！');
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
                    console.log('存在');
                    $tabLink.tab('show');
                }
                else
                {
                    // 创建新标签页
                    console.log('不存在');
                    createTab($config);
                    // 激活新标签页
                    $('a[href="#'+$config.id+'"]').tab('show');
                }
            }

        });

        // 关闭标签页处理（事件委托）
        $('.nav-tabs').on('click', '.close-tab', function(e) {
            e.stopPropagation();
            const $targetTab = $(this).closest('.nav-item');
            const $tabId = $targetTab.find('a').attr('href');

            // 移除对应内容
            $($tabId).remove();
            $targetTab.remove();

            // 自动激活剩余第一个标签页
            $('.nav-tabs .nav-item:first-child a').tab('show');
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

                $clone.find('.eChart').attr('id',$config.chart_id);

                $('#'+$config.target).prepend($clone);
                $('#'+$config.target).find('.select2-box-c').select2({
                    theme: 'classic'
                });
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

                // select2
                $('#'+$config.target).find('.select2-district-c').select2({
                    ajax: {
                        url: "{{ url('/select2/select2_district') }}",
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
                $('#'+$config.target).find('.select2-project-c').select2({
                    ajax: {
                        url: "{{ url('/v1/operate/select2/select2_project') }}",
                        type: 'post',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: $('meta[name="_token"]').attr('content'),
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
                    Datatable_for_OrderList('#'+$config.id);
                }
                else if($id == "datatable-delivery-list")
                {
                    Datatable_for_DeliveryList('#'+$config.id);
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