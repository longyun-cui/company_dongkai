<script>
    $(function() {




        // 【编辑】
        $(".main-content").on('click', ".by-item-create-show", function() {
            var $that = $(this);
            $('#modal-body-for-by-create').modal('show');
        });

        // 【编辑】
        $(".main-content").on('click', ".by-item-create-link", function() {
            var $that = $(this);
            var $url = "/item/by-create?&referrer="+encodeURIComponent(window.location.href);
            // window.location.href = $url;
            window.open($url);
        });

        // 【编辑】
        $(".main-content").on('click', ".by-item-edit-link", function() {
            var $that = $(this);
            var $url = "/item/by-edit?id="+$that.attr('data-id')+"&referrer="+encodeURIComponent(window.location.href);
            window.location.href = $url;
            // window.open($url);
        });




        // 【预处理】提交
        $(".main-content").on('click', ".by-item-preprocess-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $data = new Object();

            //
            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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

            //
            $.post(
                "{{ url('/v1/operate/by/item-preprocess') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-preprocess",
                    item_type: "by",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        $($table).DataTable().ajax.reload(null,false);
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('fail');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('always');
                    layer.closeAll('loading');
                });

        });




        // 【获取】内容详情
        $(".main-content").on('click', ".by-modal-show-for-item-detail", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            console.log();
            var $data = new Object();
            {{--$.ajax({--}}
            {{--    type:"post",--}}
            {{--    dataType:'json',--}}
            {{--    async:false,--}}
            {{--    url: "{{ url('/item/by-get-html') }}",--}}
            {{--    data: {--}}
            {{--        _token: $('meta[name="_token"]').attr('content'),--}}
            {{--        operate:"item-get",--}}
            {{--        order_id: $that.attr('data-id')--}}
            {{--    },--}}
            {{--    success:function(data){--}}
            {{--        if(!data.success) layer.msg(data.msg);--}}
            {{--        else--}}
            {{--        {--}}
            {{--            $data = data.data;--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}

//            $('input[name=id]').val($that.attr('data-id'));
            $('input[name=info-set-by-id]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            $('.info-body').html($data.html);

            var $modal = $('#modal-body-for-info-detail');

            $modal.find('.item-detail-project .item-detail-text').html($row.find('td[data-key=project_id]').attr('data-value'));
            $modal.find('.item-detail-client .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-detail-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-detail-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-detail-wx-id .item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-detail-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-detail-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-detail-description .item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.modal('show');

        });
        // 【取消】内容详情
        $(".main-content").on('click', ".by-modal-cancel-for-item-detail", function() {
            var that = $(this);
            $('#modal-body-for-info-detail').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });






        $(".main-content").on('click', ".by-modal-show-for-item-inspected1", function() {
            var $modal = $('#by-modal-body-for-item-inspected');
            var $modalContent = $modal.find('.modal-content');

            // 先显示模态框
            $modal.modal('show');

            // 确保模态框完全显示后再初始化
            $modal.one('shown.bs.modal', function() {
                // 1. 解决多个select2只初始化第一个的问题：遍历所有元素
                $modal.find('.modal-select2').each(function() {
                    var $select = $(this);

                    // 避免重复初始化
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    // 初始化每个select2
                    $select.select2({
                        dropdownParent: $modalContent, // 解决失焦问题
                        minimumInputLength: 0,
                        width: '100%',
                        theme: 'classic',
                        dropdownCssClass: 'modal-select2-dropdown' // 用于自定义样式
                    });
                });

                // 2. 解决滚动问题：添加下拉框打开时的处理
                $modal.find('.modal-select2').on('select2:open', function() {
                    var $dropdown = $('.select2-container--open');
                    if ($dropdown.length) {
                        // 设置z-index确保下拉框在模态框上方
                        $dropdown.css('z-index', '9999');

                        // 限制下拉框最大高度避免滚动问题
                        var $results = $dropdown.find('.select2-results');
                        $results.css('max-height', '200px');
                        $results.css('overflow-y', 'auto');
                    }
                });
            });

            // 模态框关闭时清理
            $modal.on('hidden.bs.modal', function() {
                $modal.find('.modal-select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                // 移除事件绑定
                $modal.find('.modal-select2').off('select2:open');
            });
        });
        $(".main-content").on('click', ".by-modal-show-for-item-inspected2", function() {
            var $modal = $('#by-modal-body-for-item-inspected');

            // 先显示模态框
            $modal.modal('show');

            // 确保在模态框完全显示后初始化select2
            $modal.off('shown.bs.modal').on('shown.bs.modal', function() {
                // 给浏览器一点时间完成渲染
                setTimeout(function() {
                    initializeSelect2InModal($modal);
                }, 100);
            });
        });
        // 模态框关闭时清理
        $('#by-modal-body-for-item-inspected1').on('hidden.bs.modal', function() {
            $(this).find('.modal-select2').each(function() {
                var $select = $(this);
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
            });
        });




        // 【获取】审核
        $(".main-content").on('click', ".by-modal-show-for-item-inspected", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $row = $that.parents('tr');
            $table.find('tr').removeClass('inspecting');
            $row.addClass('inspecting');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            $('input[name="by-inspected-item-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#by-modal-body-for-item-inspected');
            var $modalContent = $modal.find('.modal-content');
            $modal.attr('data-datatable-id',$table_id);

            var $item_info = $row.find('td[data-key=item_info]');
            var $client_name = $item_info.data('client-name');
            var $client_phone = $item_info.data('client-phone');
            var $is_wx = $item_info.data('is-wx');

            $modal.find('input[name="by_client_name"]').val($client_name);
            $modal.find('.item-inspected-client .item-detail-text').html($client_name + " - " + $client_phone + '（' + $row.find('td[data-key=location_city]').html() + '）');
            // $modal.find('.item-inspected-client-name .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            // $modal.find('.item-inspected-client-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-inspected-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            // $modal.find('.item-inspected-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-inspected-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-inspected-recording .item-detail-text').html('');
            $modal.find('.item-inspected-recording .item-detail-text').html($row.find('[data-key="recording_address_play"]').html());


            var $inspected_result = $row.find('td[data-key=inspected_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-inspected-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-inspected-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);

            // $modal.find('input[name="recording-quality"]').val('0');
            var $recording_quality = $row.find('td[data-key=recording_quality]').attr('data-value');
            $modal.find('input[name="recording-quality"][value='+$recording_quality+']').prop('checked', true);

            var $inspected_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-inspected-description"]').val('');
            $modal.find('textarea[name="detail-inspected-description"]').val($inspected_description);


            $modal.find('.modal-select2').each(function() {
                var $select = $(this);
                // 初始化 Select2
                $select.select2({
                    dropdownParent: $modal.find('.modal-content'),
                    minimumInputLength: 0,
                    width: '100%',
                    theme: 'classic'
                });
            });

            $modal.modal('show');


        });
        // 【取消】内容详情-审核
        $(".main-content").on('click', ".by-modal-cancel-for-item-inspected", function() {
            var that = $(this);
            var $modal = $('#by-modal-body-for-item-inspected');
            $modal.find('select[name="item-inspected-result"]').prop("checked", false);
            $modal.find('select[name="item-inspected-result"]').find('option').attr("selected",false);
            $modal.find('select[name="item-inspected-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="item-inspected-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-审核
        $(".main-content").on('click', ".by-modal-summit-for-item-inspected", function() {
            var $that = $(this);
            var $modal = $('#by-modal-body-for-item-inspected');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="by-inspected-item-id"]').val();
            var $inspected_result = $('select[name="by-inspected-result"]').val();
            var $inspected_description = $('textarea[name="by-inspected-description"]').val();
            var $recording_quality = $('input[name="by-inspected-recording-quality"]:checked').val();
            // console.log($recording_quality);

            //
            $.post(
                "{{ url('/v1/operate/by/item-inspect') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-inspect",
                    item_id: $modal.find('input[name="by-inspected-item-id"]').val(),
                    project_id: $modal.find('select[name="by_project_id"]').val(),
                    client_name: $modal.find('input[name="by_client_name"]').val(),
                    client_type: $modal.find('select[name="by_client_type"]').val(),
                    client_intention: $modal.find('select[name="by_client_intention"]').val(),
                    teeth_count: $modal.find('select[name="by_teeth_count"]').val(),
                    location_city: $modal.find('select[name="by_location_city"]').val(),
                    location_district: $modal.find('select[name="by_location_district"]').val(),
                    description: $modal.find('textarea[name="by_description"]').val(),
                    inspected_result: $modal.find('select[name="by-inspected-result"]').val(),
                    inspected_description: $modal.find('textarea[name="by-inspected-description"]').val(),
                    recording_quality: $modal.find('input[name="by-recording-quality"]:checked').val()
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        $($table).DataTable().ajax.reload(null,false);
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('fail');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('always');
                    layer.closeAll('loading');

                    $(".by-modal-cancel-for-item-inspected").click();
                });

        });






        // 【操作记录】【显示】
        $(".main-content").on('click', ".by-modal-show-for-item-operation", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });






        // select2 项目
        {{--$('.select2-project').select2({--}}
        {{--    ajax: {--}}
        {{--        url: "{{ url('/v1/operate/select2/select2_project') }}",--}}
        {{--        type: 'post',--}}
        {{--        dataType: 'json',--}}
        {{--        delay: 250,--}}
        {{--        data: function (params) {--}}
        {{--            return {--}}
        {{--                _token: $('meta[name="_token"]').attr('content'),--}}
        {{--                item_category: this.data('item-category'),--}}
        {{--                keyword: params.term, // search term--}}
        {{--                page: params.page--}}
        {{--            };--}}
        {{--        },--}}
        {{--        processResults: function (data, params) {--}}

        {{--            params.page = params.page || 1;--}}
        {{--            return {--}}
        {{--                results: data,--}}
        {{--                pagination: {--}}
        {{--                    more: (params.page * 30) < data.total_count--}}
        {{--                }--}}
        {{--            };--}}
        {{--        },--}}
        {{--        cache: true--}}
        {{--    },--}}
        {{--    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work--}}
        {{--    minimumInputLength: 0,--}}
        {{--    theme: 'classic'--}}
        {{--});--}}

        $('.select2-project-').each(function() {
            // 获取当前 Select2 元素的 jQuery 对象
            const $select = $(this);

            // 动态查找最近的模态框父容器
            const $modalWrapper = $select.closest('.modal-wrapper');

            // 初始化 Select2
            $select.select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2_project') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: $select.data('item-category'), // 使用 $select 获取 data 属性
                            keyword: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
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
                escapeMarkup: function(markup) { return markup; },
                dropdownParent: $modalWrapper, // 直接使用找到的模态框元素
                minimumInputLength: 0,
                theme: 'classic'
            });
        });



    });


    // 启用 Select2 滚动跟随功能
    function enableSelect2ScrollFollow($modal) {
        var $modalContent = $modal.find('.modal-content');
        var scrollTimer = null;

        // 监听模态框内容滚动
        $modalContent.off('scroll.select2-follow').on('scroll.select2-follow', function() {
            // 防抖处理，避免频繁触发
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                updateSelect2Position($modal);
            }, 50);
        });

        // 监听窗口大小变化
        $(window).off('resize.select2-follow').on('resize.select2-follow', function() {
            updateSelect2Position($modal);
        });

        // 初始更新一次位置
        updateSelect2Position($modal);
    }

    // 更新 Select2 下拉框位置
    function updateSelect2Position($modal) {
        $modal.find('.modal-select2').each(function() {
            var $select = $(this);
            var select2 = $select.data('select2');

            if (select2 && select2.isOpen()) {
                // 强制 Select2 重新计算位置
                try {
                    // 方法1: 关闭再打开（简单但会有闪烁）
                    // $select.select2('close');
                    // setTimeout(function() {
                    //     $select.select2('open');
                    // }, 10);

                    // 方法2: 使用 Select2 的内部方法重新定位
                    if (select2.dropdown && select2.dropdown._position) {
                        select2.dropdown._position();
                    }

                    // 方法3: 触发 resize 事件让 Select2 自动重新定位
                    $(window).trigger('resize.select2');

                } catch (e) {
                    console.log('更新 Select2 位置时出错:', e);
                }
            }
        });
    }


    // 单独初始化函数
    function initializeSingleSelect2($select, $dropdownParent) {
        try {
            $select.select2({
                dropdownParent: $dropdownParent,
                minimumInputLength: 0,
                width: '100%',
                theme: 'classic'
            });
            console.log('Select2 初始化完成');
        } catch (error) {
            console.error('Select2 初始化错误:', error);
            // 错误恢复：使用默认配置
            setTimeout(function() {
                $select.select2({
                    minimumInputLength: 0,
                    width: '100%',
                    theme: 'classic'
                });
            }, 100);
        }
    }

    function initializeSelect2InModal($modal) {
        var $modalContent = $modal.find('.modal-content');

        // 销毁已存在的select2实例
        $modal.find('.modal-select2').each(function() {
            var $select = $(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
        });

        // 初始化所有select2元素
        $modal.find('.modal-select2').each(function() {
            var $select = $(this);

            $select.select2({
                dropdownParent: $modalContent,
                minimumInputLength: 0,
                width: '100%',
                theme: 'classic',
                dropdownCssClass: 'modal-select2-dropdown'
            });
        });

        // 处理下拉框打开事件
        $modal.find('.modal-select2').on('select2:open', function(e) {
            // 延迟处理，确保下拉框已创建
            setTimeout(function() {
                var $dropdown = $('.select2-container--open .select2-dropdown');
                if ($dropdown.length) {
                    $dropdown.css({
                        'z-index': 9999,
                        'position': 'fixed'
                    });
                }
            }, 10);
        });
    }


</script>