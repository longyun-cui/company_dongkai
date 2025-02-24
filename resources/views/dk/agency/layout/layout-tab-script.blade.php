<script>
    $(document).ready(function() {

        // 通用标签控制逻辑
        $(".wrapper").on('click', ".tab-control", function() {

            const $btn = $(this);
            const $unique = $btn.data('unique');

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
            const $unique = $btn.data('datatable-unique');

            if($unique == 'y')
            {
                const $id = $btn.data('datatable-id');
                const $target = $btn.data('datatable-target');
                const $clone_object = $btn.data('datatable-clone-object');

                if($.fn.DataTable.isDataTable('#'+$id))
                {
                    console.log('DataTable 已存在！');
                }
                else
                {
                    console.log('DataTable 未初始化！');

                    let $clone = $('.'+$clone_object).clone(true);
                    $clone.removeClass($clone_object);
                    $clone.addClass('datatable-wrapper');
                    $clone.find('table').attr('id',$id);

                    $('#'+$target).prepend($clone);
                    $('#'+$target).find('.select2-box-c').select2({
                        theme: 'classic'
                    });

                    Datatable_for_OrderList('#'+$id);
                }
            }
            else
            {
                let $session_unique_id = sessionStorage.getItem('session_unique_id');

                const $config = {
                    type: $btn.data('datatable-type'),
                    unique: $btn.data('datatable-unique'),
                    id: $btn.data('datatable-id') + '-' + $session_unique_id,
                    target: $btn.data('datatable-target') + '-' + $session_unique_id,
                    clone_object: $btn.data('datatable-clone-object')
                };

                if($.fn.DataTable.isDataTable('#'+$config.id))
                {
                    console.log('DataTable 已存在！');
                }
                else
                {
                    console.log('DataTable 未初始化！');

                    let $clone = $('.'+$config.clone_object).clone(true);
                    $clone.removeClass($config.clone_object);
                    $clone.addClass('datatable-wrapper');
                    $clone.find('table').attr('id',$config.id);

                    $('#'+$config.target).prepend($clone);
                    $('#'+$config.target).find('.select2-box-c').select2({
                        theme: 'classic'
                    });

                    Datatable_for_OrderList('#'+$config.id);
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
                    +'<i class="fa fa-close ml-2 close-tab"></i>'
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