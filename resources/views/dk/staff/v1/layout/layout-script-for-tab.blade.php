<script>
    $(document).ready(function() {

        // 【通用标签】控制逻辑
        $(".main-wrapper").on('click', ".tab-control", function() {

            var $btn = $(this);
            var $unique = $btn.data('unique');

            // $(".nav-header-title").html($btn.data('title'));

            // document.title = $btn.data('title');

            if($unique == 'y')
            {
                var $config = {
                    type: $btn.data('type'),
                    unique: $btn.data('unique'),
                    id: $btn.data('id'),
                    title: $btn.data('title') || '默认标题',
                    content: $btn.data('content') || '默认内容',
                    icon: $btn.data('icon') || ''
                };

                var $tabLink = $('a[href="#'+ $config.id +'"]');
                var $tabPane = $('#'+$config.id);

                if($tabPane.length)
                {
                    // 存在则激活
                    console.log('【Tab】 '+'#'+$config.id+' 已存在！');
                    $tabLink.tab('show');
                }
                else
                {
                    // 创建新标签页
                    console.log('【Tab】 '+'#'+$config.id+' 不存在！');
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
            e.preventDefault();     // 阻止链接默认行为
            e.stopPropagation();    // 阻止事件冒泡
            var $targetTab = $(this).closest('.nav-item');
            var $tabId = $targetTab.find('a').attr('href');

            // 移除对应内容
            $($tabId).remove();
            $targetTab.remove();

            // 自动激活剩余第一个标签页
            $('.nav-tabs .nav-item:first-child a').tab('show');
        });




        // 【datatable】控制逻辑
        $(".main-wrapper").on('click', ".datatable-control", function() {

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
                console.log('【DataTable】' + '#'+$config.id + ' 已存在！');

                if($reload == 'y')
                {
                    $('#'+$config.id).DataTable().ajax.reload(null,false);
                }
            }
            else
            {
                console.log('【DataTable】' + '#'+$config.id + ' 未初始化！');

                var $clone = $('.'+$config.clone_object).clone(true);
                $clone.removeClass($config.clone_object);
                $clone.addClass('datatable-wrapper');
                $clone.find('table').attr('id',$config.id);

                $clone.find('.eChart').attr('id',$config.chart_id);

                $('#'+$config.target).prepend($clone);


                createTabInit($config);


                if($id == "datatable-list")
                {
                }
                else if($id == "datatable-company-list")
                {
                    Datatable__for__Company_List('#'+$config.id);
                }
                else if($id == "datatable-department-list")
                {
                    Datatable__for__Department_List('#'+$config.id);
                }
                else if($id == "datatable-team-list")
                {
                    Datatable__for__Team_List('#'+$config.id);
                }
                else if($id == "datatable-staff-list")
                {
                    Datatable__for__Staff_List('#'+$config.id);
                }
                else if($id == "datatable-motorcade-list")
                {
                    Datatable__for__Motorcade_List('#'+$config.id);
                }
                else if($id == "datatable-car-list")
                {
                    Datatable__for__Car_List('#'+$config.id);
                }
                else if($id == "datatable-driver-list")
                {
                    Datatable__for__Driver_List('#'+$config.id);
                }
                else if($id == "datatable-client-list")
                {
                    Datatable__for__Client_List('#'+$config.id);
                }
                else if($id == "datatable-project-list")
                {
                    Datatable__for__Project_List('#'+$config.id);
                }
                else if($id == "datatable-order-list")
                {
                    Datatable__for__Order_List('#'+$config.id);
                }
                else if($id == "datatable-fee-list")
                {
                    Datatable__for__Fee_List('#'+$config.id);
                }
                else if($id == "datatable-finance-list")
                {
                    Datatable__for__Finance_List('#'+$config.id);
                }
                else if($id == "datatable-statistic-order-daily")
                {
                    Datatable_Statistic_Order_Daily('#'+$config.id, $config.chart_id);
                }
            }


        });



        // 【客户】控制逻辑
        $(".main-wrapper").on('click', ".client-control", function() {

            var $that = $(this);
            var $id = $that.data('id');
            var $title = $that.data('title');
            var $content = $that.data('content');
            var $icon = $that.data('icon');
            var $statistic_daily_id = 'client-daily-' + $id;
            var $datatable_id = 'datatable-client-by-daily-' + $id;
            var $datatable_id_for_order = 'datatable-client-order-by-daily-' + $id;
            var $datatable_id_for_fee = 'datatable-client-fee-by-daily-' + $id;
            var $datatable_clone_object = 'statistic-client-by-daily-clone';
            var $datatable_target = $statistic_daily_id;
            var $chart_id = "eChart-client-by-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            var $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $statistic_daily_id,
                title: $title,
                content: $content,
                icon: '<i class="fa fa-user-secret text-red"></i>'
            };

            var $tabLink = $('a[href="#'+ $statistic_daily_id +'"]');
            var $tabPane = $('#' + $statistic_daily_id);

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
                $('a[href="#' + $statistic_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$datatable_id_for_order))
            {
                console.log($config.id);
                console.log('【statistic-client】DataTable 已存在！');
            }
            else
            {
                console.log($config.id);
                console.log('【statistic-client】DataTable 未初始化！');

                var $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                // $clone.find('table').attr('id',$datatable_id);
                $clone.find('table.table-for-order').attr('id',$datatable_id_for_order);
                $clone.find('table.table-for-fee').attr('id',$datatable_id_for_fee);
                $clone.find('input[name="statistic-client-by-daily-client-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$statistic_daily_id).prepend($clone);
                $('#'+$statistic_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$statistic_daily_id).find('.time-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.date-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.month-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                // Datatable__for__Statistic_Staff_Daily('#'+$datatable_id,$chart_id);

                Datatable_Statistic_of_Client_by_Daily_for_Order('#'+$datatable_id_for_order,$chart_id);
                Datatable_Statistic_of_Client_by_Daily_for_Fee('#'+$datatable_id_for_fee,$chart_id);
            }

        });
        // 【项目】控制逻辑
        $(".main-wrapper").on('click', ".project-control", function() {

            var $that = $(this);
            var $id = $that.data('id');
            var $title = $that.data('title');
            var $content = $that.data('content');
            var $icon = $that.data('icon');
            var $statistic_daily_id = 'project-daily-' + $id;
            var $datatable_id = 'datatable-project-by-daily-' + $id;
            var $datatable_id_for_order = 'datatable-project-order-by-daily-' + $id;
            var $datatable_id_for_fee = 'datatable-project-fee-by-daily-' + $id;
            var $datatable_clone_object = 'statistic-project-by-daily-clone';
            var $datatable_target = $statistic_daily_id;
            var $chart_id = "eChart-project-by-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            var $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $statistic_daily_id,
                title: $title,
                content: $content,
                icon: '<i class="fa fa-cube text-red"></i> '
            };

            var $tabLink = $('a[href="#'+ $statistic_daily_id +'"]');
            var $tabPane = $('#' + $statistic_daily_id);

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
                $('a[href="#' + $statistic_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$datatable_id_for_order))
            {
                console.log($config.id);
                console.log('DataTable 已存在！');
            }
            else
            {
                console.log($config.id);
                console.log('【statistic-project】DataTable 未初始化！');

                var $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                // $clone.find('table').attr('id',$datatable_id);
                $clone.find('table.table-for-order').attr('id',$datatable_id_for_order);
                $clone.find('table.table-for-fee').attr('id',$datatable_id_for_fee);
                $clone.find('input[name="statistic-project-by-daily-project-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$statistic_daily_id).prepend($clone);
                $('#'+$statistic_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$statistic_daily_id).find('.time-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.date-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.month-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                Datatable_Statistic_of_Project_by_Daily_for_Order('#'+$datatable_id_for_order,$chart_id);
                Datatable_Statistic_of_Project_by_Daily_for_Fee('#'+$datatable_id_for_fee,$chart_id);
            }

        });

        // 【员工】控制逻辑
        $(".main-wrapper").on('click', ".staff-control", function() {

            var $that = $(this);
            var $id = $that.data('id');
            var $title = $that.data('title');
            var $staff_daily_id = 'staff-daily-' + $id;
            var $datatable_id = 'datatable-staff-by-daily-' + $id;
            var $datatable_clone_object = 'statistic-staff-by-daily-clone';
            var $datatable_target = $staff_daily_id;
            var $chart_id = "eChart-staff-by-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            var $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $staff_daily_id,
                title: $that.data('title'),
                content: $that.data('content') || '默认内容'
            };

            var $tabLink = $('a[href="#'+ $staff_daily_id +'"]');
            var $tabPane = $('#' + $staff_daily_id);

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
                $('a[href="#' + $staff_daily_id + '"]').tab('show');
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

                var $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                $clone.find('table').attr('id',$datatable_id);
                $clone.find('input[name="statistic-staff-daily-staff-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$staff_daily_id).prepend($clone);
                $('#'+$staff_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$staff_daily_id).find('.time-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$staff_daily_id).find('.date-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$staff_daily_id).find('.month-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                // Datatable__for__Statistic_Staff_Daily('#'+$datatable_id,$chart_id);
            }

        });
        // 【车辆】控制逻辑
        $(".main-wrapper").on('click', ".car-control", function() {

            var $that = $(this);
            var $id = $that.data('id');
            var $title = $that.data('title');
            var $content = $that.data('content');
            var $icon = $that.data('icon');
            var $statistic_daily_id = 'car-daily-' + $id;
            var $datatable_id = 'datatable-car-by-daily-' + $id;
            var $datatable_id_for_order = 'datatable-car-order-by-daily-' + $id;
            var $datatable_id_for_fee = 'datatable-car-fee-by-daily-' + $id;
            var $datatable_clone_object = 'statistic-car-by-daily-clone';
            var $datatable_target = $statistic_daily_id;
            var $chart_id = "eChart-car-by-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            var $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $statistic_daily_id,
                title: $title,
                content: $content,
                icon: '<i class="fa fa-truck text-red"></i> '
            };

            var $tabLink = $('a[href="#'+ $statistic_daily_id +'"]');
            var $tabPane = $('#' + $statistic_daily_id);

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
                $('a[href="#' + $statistic_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$datatable_id_for_order))
            {
                console.log($config.id);
                console.log('【statistic-car】DataTable 已存在！');
            }
            else
            {
                console.log($config.id);
                console.log('【statistic-car】DataTable 未初始化！');

                var $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                // $clone.find('table').attr('id',$datatable_id);
                $clone.find('table.table-for-order').attr('id',$datatable_id_for_order);
                $clone.find('table.table-for-fee').attr('id',$datatable_id_for_fee);
                $clone.find('input[name="statistic-car-by-daily-car-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$statistic_daily_id).prepend($clone);
                $('#'+$statistic_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$statistic_daily_id).find('.time-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.date-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$statistic_daily_id).find('.month-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                Datatable_Statistic_of_Car_by_Daily_for_Order('#'+$datatable_id_for_order,$chart_id);
                Datatable_Statistic_of_Car_by_Daily_for_Fee('#'+$datatable_id_for_fee,$chart_id);
            }

        });
        // 【司机】控制逻辑
        $(".main-wrapper").on('click', ".driver-control", function() {

            var $that = $(this);
            var $id = $that.data('id');
            var $title = $that.data('title');
            var $content = $that.data('content');
            var $icon = $that.data('icon');
            var $driver_daily_id = 'driver-daily-' + $id;
            var $datatable_id = 'datatable-driver-by-daily-' + $id;
            var $datatable_id_for_order = 'datatable-driver-order-by-daily-' + $id;
            var $datatable_id_for_fee = 'datatable-driver-fee-by-daily-' + $id;
            var $datatable_clone_object = 'statistic-driver-by-daily-clone';
            var $datatable_target = $driver_daily_id;
            var $chart_id = "eChart-driver-by-daily-" + $id;

            // $(".nav-header-title").html($btn.data('title'));

            var $config = {
                type: $that.data('type'),
                unique: 'y',
                id: $driver_daily_id,
                title: $title,
                content: $content,
                icon: '<i class="fa fa-male text-red"></i>'
            };

            var $tabLink = $('a[href="#'+ $driver_daily_id +'"]');
            var $tabPane = $('#' + $driver_daily_id);

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
                $('a[href="#' + $driver_daily_id + '"]').tab('show');
            }


            // data-datatable-id="datatable-location-list"
            // data-datatable-target="location-list"
            // data-datatable-clone-object="location-list-clone"
            // data-chart-id="eChart-statistic-company-daily"


            if($.fn.DataTable.isDataTable('#'+$datatable_id_for_order))
            {
                console.log($config.id);
                console.log('【statistic-driver】DataTable 已存在！');
            }
            else
            {
                console.log($config.id);
                console.log('【statistic-driver】DataTable 未初始化！');

                var $clone = $('.'+$datatable_clone_object).clone(true);
                $clone.removeClass($datatable_clone_object);
                $clone.addClass('datatable-wrapper');
                // $clone.find('table').attr('id',$datatable_id);
                $clone.find('table.table-for-order').attr('id',$datatable_id_for_order);
                $clone.find('table.table-for-fee').attr('id',$datatable_id_for_fee);
                $clone.find('input[name="statistic-driver-by-daily-driver-id"]').val($id);
                $clone.find('.eChart').attr('id',$chart_id);

                $('#'+$driver_daily_id).prepend($clone);
                $('#'+$driver_daily_id).find('.select2-box-c').select2({
                    theme: 'classic'
                });
                $('#'+$driver_daily_id).find('.time-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD HH:mm",
                    ignoreReadonly: true
                });
                $('#'+$driver_daily_id).find('.date-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM-DD",
                    ignoreReadonly: true
                });
                $('#'+$driver_daily_id).find('.month-picker-c').datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format: "YYYY-MM",
                    ignoreReadonly: true
                });

                // Datatable__for__Statistic_Staff_Daily('#'+$datatable_id,$chart_id);

                Datatable_Statistic_of_Driver_by_Daily_for_Order('#'+$datatable_id_for_order,$chart_id);
                Datatable_Statistic_of_Driver_by_Daily_for_Fee('#'+$datatable_id_for_fee,$chart_id);
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
            + $config.icon
            + ' '
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


    // 创建标签页函数
    function createTabInit($config)
    {

        //iCheck for checkbox and radio inputs
        $('#'+$config.target).find('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        $('#'+$config.target).find('.select2-box-c').select2({
            theme: 'classic'
        });
        $('#'+$config.target).find('.time-picker-c').datetimepicker({
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
        $('#'+$config.target).find('.date-picker-c').datetimepicker({
            // 1. 格式控制是否显示时间
            // format: 'YYYY-MM-DD HH:mm',  // 包含HH:mm自动启用时间选择
            format: 'YYYY-MM-DD',      // 只显示日期

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
        $('#'+$config.target).find('.month-picker-c').datetimepicker({
            // 1. 格式控制是否显示时间
            // format: 'YYYY-MM-DD HH:mm',  // 包含HH:mm自动启用时间选择
            // format: 'YYYY-MM-DD',      // 只显示日期
            format: 'YYYY-MM',      // 只显示月份

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

        // select2
        $('#'+$config.target).find('.select2-department-c').select2({
            ajax: {
                url: "{{ url('/o1/select2/select2-department') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        department_category: this.data('department-category'),
                        department_type: this.data('department-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#'+$config.target).find('.select2-team-c').select2({
            ajax: {
                url: "{{ url('/o1/select2/select2-team') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        department_category: this.data('department-category'),
                        team_category: this.data('team-category'),
                        team_type: this.data('team-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#'+$config.target).find('.select2-staff-c').select2({
            ajax: {
                url: "{{ url('/o1/select2/select2-staff') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        staff_category: this.data('staff-category'),
                        staff_type: this.data('staff-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#'+$config.target).find('.select2-car-c').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-car') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        car_category: this.data('car-category'),
                        car_type: this.data('car-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            // dropdownParent: this.data('modal'), // 替换为你的模态框 ID
            // dropdownParent: function() {
            //     // 获取当前元素的 modal 属性
            //     var modalSelector = $(this).data('modal');
            //     return $(modalSelector).length ? $(modalSelector) : $(document.body);
            // },
            minimumInputLength: 0,
            // width: '100%',
            theme: 'classic'
            // placeholder: "搜索或选择车辆..."
        });
        $('#'+$config.target).find('.select2-driver-c').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-driver') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        car_category: this.data('driver-category'),
                        car_type: this.data('driver-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#'+$config.target).find('.select2-client-c').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-client') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        client_category: this.data('client-category'),
                        client_type: this.data('client-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#'+$config.target).find('.select2-project-c').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-project') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        project_category: this.data('project-category'),
                        project_type: this.data('project-type'),
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
            escapeMarkup: function (markup) { return markup; }, // var our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
    }


</script>