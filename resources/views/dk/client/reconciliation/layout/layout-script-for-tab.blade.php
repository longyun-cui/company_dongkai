<script>
    $(document).ready(function() {

        // 通用标签控制逻辑
        $(".wrapper").on('click', ".tab-control", function() {

            var $btn = $(this);
            var $unique = $btn.data('unique');

            // $(".nav-header-title").html($btn.data('title'));

            if($unique == 'y')
            {
                var $config = {
                    type: $btn.data('type'),
                    unique: $btn.data('unique'),
                    id: $btn.data('id'),
                    title: $btn.data('title'),
                    content: $btn.data('content') || '默认内容'
                };

                var $tabLink = $('a[href="#'+ $config.id +'"]');
                var $tabPane = $('#'+$config.id);

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
                var $session_unique_id = sessionStorage.getItem('session_unique_id');
                sessionStorage.setItem('session_unique_id',parseInt($session_unique_id) + 1);
                $session_unique_id = sessionStorage.getItem('session_unique_id');

                var $btn = $(this);
                var $config = {
                    type: $btn.data('type'),
                    unique: $btn.data('unique'),
                    id: $btn.data('id') + '-' + $session_unique_id,
                    title: $btn.data('title'),
                    content: $btn.data('content') || '默认内容'
                };

                var $tabLink = $('a[href="#'+ $config.id +'"]');
                var $tabPane = $('#'+$config.id);

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
            var $targetTab = $(this).closest('.nav-item');
            var $tabId = $targetTab.find('a').attr('href');

            // 移除对应内容
            $($tabId).remove();
            $targetTab.remove();

            // 自动激活剩余第一个标签页
            $('.nav-tabs .nav-item:first-child a').tab('show');
        });




        // 通用标签控制逻辑
        $(".wrapper").on('click', ".datatable-control", function() {

            var $btn = $(this);
            var $id = $btn.data('datatable-id');
            var $unique = $btn.data('datatable-unique');
            var $reload = $btn.data('datatable-reload');

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
                var $session_unique_id = sessionStorage.getItem('session_unique_id');

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
                console.log('DataTable 已存在！');
                if($reload == 'y')
                {
                    $('#'+$config.id).DataTable().ajax.reload(null,false);
                }
            }
            else
            {
                console.log('DataTable 未初始化！');

                var $clone = $('.'+$config.clone_object).clone(true);
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

                if($id == "datatable-list")
                {
                }
                else if($id == "datatable-reconciliation-project-list")
                {
                    Datatable_for_Reconciliation_Project_List('#'+$config.id);
                }
                else if($id == "datatable-reconciliation-daily-list")
                {
                    Datatable_for_Reconciliation_Daily_List('#'+$config.id);
                }
                else if($id == "datatable-trade-list")
                {
                    console.log('#'+$config.id);
                    Datatable_for_Reconciliation_Trade_List('#'+$config.id);
                }
            }


        });




        // 通用标签控制逻辑
        $(".wrapper").on('click', ".project-control", function() {

            const $that = $(this);
            const $id = $that.data('id');
            const $title = $that.data('title');
            const $project_daily_id = 'project-daily-' + $id;
            const $datatable_id = 'datatable-project-daily-' + $id;
            const $datatable_clone_object = 'project-statistic-daily-clone';
            const $datatable_target = $project_daily_id;
            const $chart_id = "eChart-project-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            const $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $project_daily_id,
                title: $that.data('title'),
                content: $that.data('content') || '默认内容'
            };

            const $tabLink = $('a[href="#'+ $project_daily_id +'"]');
            const $tabPane = $('#' + $project_daily_id);

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
                $('a[href="#' + $project_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$datatable_id))
            {
                console.log($config.id);
                console.log('DataTable 已存在！');
            }
            else
            {
                console.log($config.id);
                console.log('DataTable 未初始化！');

                let $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                $clone.find('table').attr('id',$datatable_id);
                $clone.find('input[name="project-daily-project-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$project_daily_id).prepend($clone);
                $('#'+$project_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$project_daily_id).find('.time_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$project_daily_id).find('.date_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$project_daily_id).find('.month_picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                Datatable_for_Reconciliation_Project_Statistic_Daily('#'+$datatable_id,$chart_id);
            }

        });




    });


    // 创建标签页函数
    function createTab($config)
    {
        // 导航标签模板
        var navItem =
            '<li class="nav-item">'
                +'<a class="nav-link" href="#'+ $config.id +'" data-toggle="tab">'
                    + $config.title
                    +'<i class="fa fa-close ml-2 close-tab"></i>'
                +'</a>'
            +'</li>';

        // 内容面板模板
        var contentPane = '<div class="tab-pane fade" id="'+ $config.id +'"></div>';

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