<script>
    $(function() {

        // 【通用【搜索】
        $(".main-content").on('click', ".filter-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $time_type = $that.attr('data-time-type');
            $datatable_wrapper.find('.time-type').val($time_type);
            if($time_type == "all")
            {
            }
            else if($time_type == "date")
            {
                var $date_dom = $datatable_wrapper.find('.search-date');
                var $the_date_str = $date_dom.val();
                $(".nav-header-title-3").html($the_date_str);
            }
            else if($time_type == "month")
            {
                var $month_dom = $datatable_wrapper.find('.search-month');
                var $the_month_str = $month_dom.val();
                $(".nav-header-title-3").html($the_month_str);
            }
            else if($time_type == "period")
            {
                var $start_dom = $datatable_wrapper.find('input[name="search-start"]');
                var $ended_dom = $datatable_wrapper.find('input[name="search-ended"]');
                var $the_start_str = $start_dom.val();
                var $the_ended_str = $ended_dom.val();
                $(".nav-header-title-3").html($the_start_str + '-' + $the_ended_str);
            }

            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【通用【刷新】
        $(".main-content").on('click', ".filter-refresh", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【通用【重置】
        $(".main-content").on('click', ".filter-cancel", function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $search_box = $datatable_wrapper.find('.datatable-search-box');

            $search_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });
            $search_box.find(".select2-box-c").val(-1).trigger("change");
            $search_box.find(".select2-box-c").select2("val", "");

            $search_box.find('select.form-filter option').attr("selected",false);
            $search_box.find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【通用【清空重选】
        $(".main-content").on('click', ".filter-empty", function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $search_box = $datatable_wrapper.find('.datatable-search-box');

            $search_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });
            $search_box.find(".select2-box-c").val(-1).trigger("change");
            $search_box.find(".select2-box-c").select2("val", "");

            $search_box.find('select.form-filter option').attr("selected",false);
            $search_box.find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【通用【查询】回车
        $(".main-content").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                var $that = $(this);
                var $datatable_wrapper = $that.closest('.datatable-wrapper');
                var $search_box = $datatable_wrapper.find('.datatable-search-box');
                $search_box.find(".filter-submit").click();
            }
        });


        // 【通用【前一月】
        $(".main-content").on('click', ".month-pre", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $datatable_wrapper.find('.time-type').val('month');

            var $month_dom = $('input[name='+$target+']');
            var $the_month = $month_dom.val();
            var $date = new Date($the_month);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $pre_year = $year;
            var $pre_month = $month;

            if(parseInt($month) == 0)
            {
                $pre_year = $year - 1;
                $pre_month = 12;
            }

            if($pre_month < 10) $pre_month = '0'+$pre_month;

            var $pre_month_str = $pre_year+'-'+$pre_month;
            $month_dom.val($pre_month_str);

            $('#'+$table_id).DataTable().ajax.reload(null,false);

        });
        // 【通用【后一月】
        $(".main-content").on('click', ".month-next", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $datatable_wrapper.find('.time-type').val('month');

            var $month_dom = $('input[name='+$target+']');
            var $the_month_str = $month_dom.val();

            var $date = new Date($the_month_str);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $next_year = $year;
            var $next_month = $month;

            if(parseInt($month) == 11)
            {
                $next_year = $year + 1;
                $next_month = 1;
            }
            else $next_month = $month + 2;

            if($next_month < 10) $next_month = '0'+$next_month;

            var $next_month_str = $next_year+'-'+$next_month;
            $month_dom.val($next_month_str);

            $('#'+$table_id).DataTable().ajax.reload(null,false);

        });

        // 【通用【前一天】
        $(".main-content").on('click', ".date-pre", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $datatable_wrapper.find('.time-type').val('date');

            var $date_dom = $('input[name='+$target+']');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $yesterday_time = $time - (24*60*60*1000);

            var $yesterday = new Date($yesterday_time);
            var $yesterday_year = $yesterday.getFullYear();
            var $yesterday_month = ('00'+($yesterday.getMonth()+1)).slice(-2);
            var $yesterday_day = ('00'+($yesterday.getDate())).slice(-2);

            var $yesterday_date_str = $yesterday_year + '-' + $yesterday_month + '-' + $yesterday_day;
            $date_dom.val($yesterday_date_str);

            $('#'+$table_id).DataTable().ajax.reload(null,false);

        });
        // 【通用【后一天】
        $(".main-content").on('click', ".date-next", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $datatable_wrapper.find('.time-type').val('date');

            var $date_dom = $('input[name='+$target+']');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $tomorrow_time = $time + (24*60*60*1000);

            var $tomorrow = new Date($tomorrow_time);
            var $tomorrow_year = $tomorrow.getFullYear();
            var $tomorrow_month = ('00'+($tomorrow.getMonth()+1)).slice(-2);
            var $tomorrow_day = ('00'+($tomorrow.getDate())).slice(-2);

            var $tomorrow_date_str = $tomorrow_year + '-' + $tomorrow_month + '-' + $tomorrow_day;
            $date_dom.val($tomorrow_date_str);

            $('#'+$table_id).DataTable().ajax.reload(null,false);

        });




        // 【通用【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            $datatable_wrapper.find('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });


        // 【通用】启用
        $(".main-content").on('click', ".item-enable-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"启用"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/item-enable-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-enable-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });

        });
        // 【通用】禁用
        $(".main-content").on('click', ".item-disable-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"禁用"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/item-disable-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-disable-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });
        });


        // 【通用】重置密码
        $(".main-content").on('click', ".item-password-reset-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"重置密码"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/password-reset-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-password-reset-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                layer.msg('重置成功！');
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
                }
            });
        });

        // 【通用】登录
        $(".main-content").on('click', ".item-login-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


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
                "{{ url('/v1/operate/universal/item-login-by-admin') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-login-by-admin",
                    item_category: $item_category,
                    item_id: $that.attr('data-id')
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
                        layer.msg('登录成功，即将跳转！');
                        if($item_category == 'staff')
                        {
                            var temp_window=window.open();
                            temp_window.location = "{{ env('DOMAIN_DK_ADMIN') }}/";
                        }
                        else if($item_category == 'company')
                        {
                            var temp_window=window.open();
                            temp_window.location = "{{ env('DOMAIN_DK_AGENCY') }}/";
                        }
                        else if($item_category == 'client')
                        {
                            var temp_window=window.open();
                            temp_window.location = "{{ env('DOMAIN_DK_CLIENT') }}/";
                        }
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


        // 【通用】删除
        $(".main-content").on('click', ".item-delete-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/item-delete-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-delete-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });
        });
        // 【通用】恢复
        $(".main-content").on('click', ".item-restore-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"恢复"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/item-restore-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-restore-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });
        });
        // 【通用】永久删除
        $(".main-content").on('click', ".item-delete-permanently-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"永久删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/item/item-delete-permanently-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-delete-permanently-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });
        });


        // 【通用】晋升
        $(".main-content").on('click', ".item-promote-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"晋升"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);

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
                        "{{ url('/v1/operate/universal/item-promote-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-promote-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });

        });
        // 【通用】降职
        $(".main-content").on('click', ".item-demote-by-admin-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"降职"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
                    layer.close(index);
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
                        "{{ url('/v1/operate/universal/item-demote-by-admin') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-demote-by-admin",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
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
                }
            });


        });



    });
</script>