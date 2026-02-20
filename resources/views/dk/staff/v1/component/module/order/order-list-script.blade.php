<script>
    $(function() {


        // 【工单】添加-显示
        $(".main-wrapper").on('click', ".modal-show--for--order--item-create", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('input[name="operate[type]"]').val('create');
            $modal.find('input[name="operate[id]"]').val(0);
            $modal.find('.box-title').html($title);
            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);
            $modal.find('.radio-btn').show();
            $modal.modal('show');

            $('.modal-select2').select2({
                dropdownParent: $('#'+$modal_id), // 替换为你的模态框 ID
                minimumInputLength: 0,
                width: '100%',
                theme: 'classic'
            });
        });
        // 【工单】编辑-显示
        $(".main-wrapper").on('click', ".modal-show--for--order-dental--item-edit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

            var $modal_id = 'modal--for--order-dental--item-edit';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-edit';
            var $form = $("#"+$form_id);

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
                "{{ url('/o1/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('#'+$that.attr('id')+'.post.done.');

                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        form_reset("#"+$form_id);


                        $modal.find('.box-title').html('编辑工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        // 班次
                        $modal.find('select[name="work_shift"]').val($response.data.work_shift).trigger('change');

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);
                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');

                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('select[name="location_district"]').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('select[name="project_id"]').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');
                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);


                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('#'+$that.attr('id')+'.post.fail.');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('#'+$that.attr('id')+'.post.always.');
                    layer.closeAll('loading');
                });

        });
        // 【工单】编辑-提交
        $(".main-wrapper").on('click', ".submit--for--order--item-edit", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            // var $modal_id = 'modal--for--order--item-edit';
            var $modal_id = $that.data('modal-id');
            var $modal = $("#"+$modal_id);

            // var $form_id = 'form--for--order--item-edit';
            var $form_id = $that.data('form-id');
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.success.');

                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.error.');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.complete');
                    layer.closeAll('loading');
                }


            };
            $form.ajaxSubmit(options);
        });



        $(".txt-file-upload").fileinput({
            allowedFileExtensions : [ 'txt' ],
            showUpload: false
        });


        // 【工单】导入-显示
        $(".main-wrapper").on('click', ".modal-show--for--order--import--by-txt", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('input[name="operate[type]"]').val('import');
            $modal.find('input[name="operate[id]"]').val(0);
            $modal.find('.box-title').html($title);
            $modal.modal('show');
        });
        // 【工单】导入-提交
        // 【工单】编辑-提交
        $(".main-wrapper").on('click', "#submit--for--order--import--by-txt", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--order--import--by-txt';
            // var $modal_id = $that.data('modal-id');
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--import--by-txt';
            // var $form_id = $that.data('form-id');
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/import--by-txt') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.success.');

                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);
                        layer.msg("添加" + $response.data.count + "条数据！");

                        // 重置输入框
                        // form_reset('#'+$form_id);
                        $form.find(".fileinput-remove-button").click();

                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown, $form) {
                    // 请求失败时的回调
                    // console.log(XMLHttpRequest);
                    // console.log(textStatus);
                    // console.log(errorThrown);
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.error.');
                    layer.closeAll('loading');
                },
                complete: function(jqXHR, textStatus, $form) {
                    // 无论成功或失败都会执行的回调
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.complete');
                    layer.closeAll('loading');
                }


            };
            $form.ajaxSubmit(options);
        });



        // 【工单】【添加工单】select2 选择项目
        $('#select2--project--for-order--item-edit').on('select2:select', function(e) {
            $('input[name=transport_departure_place]').val(e.params.data.transport_departure_place);
            $('input[name=transport_destination_place]').val(e.params.data.transport_destination_place);
            // 距离
            $('input[name=transport_distance]').val(e.params.data.transport_distance);
            // 时效
            var $transport_time_limitation = parseFloat((e.params.data.transport_time_limitation / 60).toFixed(2));
            $('input[name=transport_time_limitation]').val($transport_time_limitation);
            $('input[name=freight_amount]').val(parseFloat(e.params.data.freight_amount));
        });
        // 【工单】【添加工单】select2 选择车辆
        $('#select2--car--for-order--item-edit').on('select2:select', function(e) {

            console.log("用户选择了:", e.params.data); // 仅触发1次
            var $that = $(this);
            var $modal = $that.parents('.modal-wrapper');


            var $id = $(this).val();

            //
            $.post(
                "{{ url('/o1/car/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "car",
                    item_id: $id
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
                        // 挂 (select2)
                        if($response.data.trailer_er)
                        {
                            var $trailer_option = new Option($response.data.trailer_er.name, $response.data.trailer_id, true, true);
                            $modal.find('select[name="trailer_id"]').append($trailer_option).trigger('change');
                        }
                        // 主驾 (select2)
                        if($response.data.driver_er)
                        {
                            var $driver_option = new Option($response.data.driver_er.driver_name, $response.data.driver_id, true, true);
                            $modal.find('select[name="driver_id"]').append($driver_option).trigger('change');
                        }
                        // 副驾 (select2)
                        if($response.data.copilot_er)
                        {
                            var $copilot_option = new Option($response.data.copilot_er.driver_name, $response.data.copilot_id, true, true);
                            $modal.find('select[name="copilot_id"]').append($copilot_option).trigger('change');
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
        // 【工单】【添加工单】select2 选择主驾
        $('#select2--driver--for--order--item-edit').on('select2:select', function(e) {
            console.log("用户选择了:", e.params.data); // 仅触发1次
            var $that = $(this);
            var $modal = $that.parents('.modal-wrapper');

            var $that_name = $that.attr('name');
            if($that_name == 'driver_id')
            {
                $modal.find('input[name=driver_name]').val(e.params.data.text);
                $modal.find('input[name=driver_phone]').val(e.params.data.driver_phone);
            }
            else if($that_name == 'copilot_id')
            {
                $modal.find('input[name=copilot_name]').val(e.params.data.text);
                $modal.find('input[name=copilot_phone]').val(e.params.data.driver_phone);
            }
        });




        // 【工单】删除
        $(".main-wrapper").off('click', ".order--item-delete-submit").on('click', ".order--item-delete-submit", function() {
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
                        "{{ url('/o1/order/item-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-delete",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });
        // 【工单】恢复
        $(".main-wrapper").off('click', ".order--item-restore-submit").on('click', ".order--item-restore-submit", function() {
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
                        "{{ url('/o1/order/item-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-restore",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });
        // 【工单】永久删除
        $(".main-wrapper").off('click', ".order--item-delete-permanently-submit").on('click', ".order--item-delete-permanently-submit", function() {
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
                        "{{ url('/o1/order/item-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-delete-permanently",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });


        // 【工单】发布
        $(".main-wrapper").off('click', ".order--item-publish-submit").on('click', ".order--item-publish-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"发布"么?', {
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
                        "{{ url('/o1/order/item-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-publish",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });
        // 【工单】一键交付
        $(".main-wrapper").off('click', ".order--item-delivering-summit--by-fool").on('click', ".order--item-delivering-summit--by-fool", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"一键交付"么?', {
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
                        "{{ url('/o1/order/item-delivering-save--by-fool') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-delivering-save--by-fool",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });
        // 【工单】完成
        $(".main-wrapper").off('click', ".order--item-complete-submit").on('click', ".order--item-complete-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"完成"么?', {
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
                        "{{ url('/o1/order/item-complete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--item-complete",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

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
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });



        window.addEventListener('play', function(e) {
            console.log('[全局监听] 播放事件触发:', e.target);
            const audio = e.target;

            // 1. 获取音频源地址
            const audioSrc = audio.currentSrc || $(audio).find('source').attr('src');
            // console.log('音频开始播放:', audioSrc);

            // 2. 设置播放速度 (1.5倍速)
            var $speed = $('input[name="recording-speed"]:checked').val();
            audio.playbackRate = $speed; // 默认值1.0，范围0.5-4.0
            console.log('播放速率已设置:', audio.playbackRate);

            // 3. 可选: 防止多音频同时播放
            // $('audio').not(this).each(function() {
            //     this.pause();
            //     this.currentTime = 0;
            // });
            // 暂停其他音频
            $('audio').each(function() {
                if (this !== audio && !this.paused) {
                    this.pause();
                    this.currentTime = 0;
                    // updateAudioStatus(this, '已停止');
                    // addLog(`暂停其他音频: ${this.dataset.audioId}`, 'warning');
                }
                else
                {

                }
            });

            // this.play();

            // 4. 业务逻辑可在此处添加
        }, true);

        // 【录音】播放速度
        $(".main-wrapper").on('click', 'input[name="recording-speed"]', function() {
            var $speed = $('input[name="recording-speed"]:checked').val();

            $('#modal--for--order--item-inspecting audio').each(function() {
                if (this.played)
                {
                    this.playbackRate = $speed; // 默认值1.0，范围0.5-4.0
                }
            });
        });


        // 【获取录音】
        $(".main-wrapper").on('click', ".order--item-recording-list-get-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $td = $that.parents('td');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $rowIndex = $td.attr('data-row-index');

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">耐心等待中</span>',
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

            $.post(
                "{{ url('order--item-recording-list-get-submit') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order--item-call-recording--get--by-api",
                    item_id: $that.attr('data-id')
                },
                'json'
            )
                .done(function($response) {

                    layer.closeAll('loading');
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        // console.log(JSON.parse($response.data));

                        console.log($response.data.data);
                        var $item = $response.data.data;
                        if($item.recording_address_list)
                        {
                            // var $html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+$item.recording_address+'" type="audio/mpeg"></audio>'
                            // $row.find('[data-key="recording_address_play"]').html($html);

                            var $recording_list = JSON.parse($item.recording_address_list);
                            var $recording_list_html = '';
                            $.each($recording_list, function(index, value)
                            {

                                var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                $recording_list_html += $audio_html;
                            });
                            $row.find('[data-key="recording_address_play"]').html($recording_list_html);
                            $row.find('[data-key="description"]').attr('data-recording-address',$recording_list_html);

                            $row.find('[data-key=recording_address_download]').attr('data-address-list',$item.recording_address_list);
                            var $recording_download = '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+$id+'">下载</a>';
                            $that.after($recording_download);
                            var $recording_redirection = '<a class="btn btn-xs item-redirection-recording-list-submit" data-id="'+$id+'">跳转</a>';
                            $that.after($recording_redirection);
                        }



                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    layer.closeAll('loading');
                });

        });



        // 【强制跳转】
        $(".main-wrapper").on('click', ".item-redirection-recording-list-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $item_id = $that.data('id');
            console.log($item_id);
            console.log($row);

            $recording_list_str = $row.find('td[data-key=recording_address_download]').attr('data-address-list');
            if($recording_list_str)
            {
                var $recording_list = JSON.parse($recording_list_str);
                console.log($recording_list);

                $.each($recording_list, function($index, $value) {

                    console.log('$recording_list_str');
                    console.log($index);
                    console.log($value);

                    var $path = new URL($value).pathname;
                    var $url = 'http://8.142.7.121:9091/res/rs1/recordFile/listen?file='+$path;
                    window.open($url);

                    // var $obj = new Object();
                    // $obj.item_id = $item_id;
                    //
                    // $obj.url = $value;
                    //
                    // var $randomNumber = Math.floor(Math.random() * 100) + 1;
                    // $obj.randomNumber = $randomNumber;
                    // console.log($obj);
                    //
                    // var $url = url_build('/download/item-recording-download',$obj);
                    // window.open($url);

                    // var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);

                    // setTimeout(() => {
                    //     window.open($url, $randomNumber);
                    //     this.printOrderDialogShow = false;
                    // }, 0.3);


                });
            }
            else
            {
                $call_record_id = $row.find('td[data-key=recording_address_download]').attr('data-call-record-id');
                if($call_record_id && $call_record_id > 0)
                {
                    console.log('else');
                    console.log($call_record_id);

                    // var $obj = new Object();
                    // $obj.call_record_id = $call_record_id;
                    //
                    // var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);
                }

            }

        });
        // 【审核-强制跳转】
        $(".main-wrapper").on('click', ".item-inspected-redirection-recording-list-submit", function() {
            var $that = $(this);
            var $modal_wrapper = $that.closest('.modal-wrapper');
            var $id = $modal_wrapper.find('input[name="detail-inspected-order-id"]').val();
            var $row = $('tr.operating');
            console.log($id);

            $recording_list_str = $row.find('td[data-key=recording_address_download]').attr('data-address-list');
            if($recording_list_str)
            {
                var $recording_list = JSON.parse($recording_list_str);
                console.log($recording_list);

                $.each($recording_list, function($index, $value) {

                    console.log('$recording_list_str');
                    console.log($index);
                    console.log($value);

                    var $path = new URL($value).pathname;
                    var $url = 'http://8.142.7.121:9091/res/rs1/recordFile/listen?file='+$path;
                    window.open($url);

                    // var $obj = new Object();
                    // $obj.item_id = $item_id;
                    //
                    // $obj.url = $value;
                    //
                    // var $randomNumber = Math.floor(Math.random() * 100) + 1;
                    // $obj.randomNumber = $randomNumber;
                    // console.log($obj);
                    //
                    // var $url = url_build('/download/item-recording-download',$obj);
                    // window.open($url);

                    // var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);

                    // setTimeout(() => {
                    //     window.open($url, $randomNumber);
                    //     this.printOrderDialogShow = false;
                    // }, 0.3);


                });
            }
            else
            {
                $call_record_id = $row.find('td[data-key=recording_address_download]').attr('data-call-record-id');
                if($call_record_id && $call_record_id > 0)
                {
                    console.log('else');
                    console.log($call_record_id);

                    // var $obj = new Object();
                    // $obj.call_record_id = $call_record_id;
                    //
                    // var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);
                }
            }

        });




        // 【通用】显示详情
        $(".main-content").off('dblclick', ".modal-show--for--order--item-detail").on('dblclick', ".modal-show--for--order--item-detail", function() {
            var $that = $(this);
            var $order_category = $(this).data('order-category');
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            var $modal = $('#modal--for--delivery-item-detail');
            $modal.find('.id-title').html('【'+$id+'】');
            $modal.find('.delivery-location-box').html($row.find('[data-key="location"]').html());
            $modal.find('.delivery-client-name-box').html($row.find('[data-key="client_name"]').html());
            $modal.find('.delivery-client-mobile-box').html($row.find('[data-key="client_phone"]').html());
            $modal.find('.delivery-client-wx-box').html($row.find('[data-key="client_wx"]').html());
            $modal.find('.delivery-client-intention-box').html($row.find('[data-key="client_intention"]').html());
            $modal.find('.delivery-teeth-count-box').html($row.find('[data-key="teeth_count"]').html());
            $modal.find('.delivery-description-box').html($row.find('[data-key="description"]').data('value'));
            $modal.find('.delivery-recording-address-box').html('');
            $modal.find('.delivery-recording-address-box').html($row.find('[data-key="description"]').data('recording-address'));

            if($order_category == 1)
            {
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').hide();
                $modal.find('.dental-show').show();
            }
            if($order_category == 11)
            {
                $modal.find('.dental-show').hide();
                $modal.find('.luxury-show').hide();
                $modal.find('.aesthetic-show').show();
            }
            if($order_category == 31)
            {
                $modal.find('.dental-show').hide();
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').show();
            }
            else
            {
                $modal.find('.dental-show').hide();
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').hide();
            }

            $modal.modal('show');
        });




        // 【工单】操作记录
        $(".main-content").off('click', ".modal-show--for--order--item-operation-record").on('click', ".modal-show--for--order--item-operation-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $datatable_id = 'datatable--for--order--item-operation-record-list';

            Datatable__for__Order_Item_Operation_Record_List.init($datatable_id,$id);

            $('#modal--for--order--item-operation-record-list').modal('show');
        });
        // 【工单】操作记录
        $(".main-content").off('click', ".modal-show--for--order--item-fee-record").on('click', ".modal-show--for--order--item-fee-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            Datatable_Order_Fee_Record.init($id);

            $('#modal--for--order-fee-record-list').modal('show');
        });




        // 【工单】【跟进】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-follow-create").on('click', ".modal-show--for--order--item-follow-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form--for--order--item-follow-create');

            var $modal = $('#modal--for--order--item-follow-create');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.modal('show');
            // $('#modal--for--order-follow-create').modal({show: true,backdrop: 'static'});
            // $('.modal-backdrop').each(function() {
            //     $(this).attr('id', 'id_' + Math.random());
            // });
        });
        // 【工单】【跟进】提交
        $(".main-wrapper").off('click', "#form-submit--for--order--item-follow-create").on('click', "#form-submit--for--order--item-follow-create", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

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

            var options = {
                url: "{{ url('/o1/order/item-follow-save') }}",
                type: "post",
                dataType: "json",
                // data: { _token: $('meta[name="_token"]').attr('content') },
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset('#form--for--order-follow-create');

                        $('#modal--for--order--item-follow-create').modal('hide');
                        // $('#modal--for--order--item-follow-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    console.log('#form-submit--for--order--item-follow-create.click.error');
                    layer.msg('服务器错误！');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log('#form-submit--for--order--item-follow-create.click.complete');
                }


            };
            $("#form--for--order--item-follow-create").ajaxSubmit(options);
        });



        // 【工单】【审核】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-inspecting").on('click', ".modal-show--for--order--item-inspecting", function() {
            var $that = $(this);
            var $id = $that.data('id');
            var $item_id = $that.data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


            var $delivery_datatable_id = 'datatable--for--order--item-inspecting--of--delivery-record-list';
            Datatable__for__Order_Item_Delivery_Record_List.init($delivery_datatable_id,$id);


            var $modal_id = 'modal--for--order--item-inspecting';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-inspecting';
            var $form = $("#"+$form_id);

            $modal.find('input[name="operate[id]"]').val($id);
            $modal.find('input[name="item_id"]').val($id);


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
                "{{ url('/o1/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('#'+$that.attr('id')+'.post.done.');

                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        form_reset("#"+$form_id);


                        $modal.find('.id-box').html('【'+$id+'】');
                        // $modal.find('.title-box').html('审核工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('inspecting');
                        $modal.find('input[name="operate[id]"]').val($item_id);
                        $modal.find('input[name="item_id"]').val($item_id);

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="teeth_count"]').val($response.data.teeth_count).trigger('change');
                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('select[name="location_district"]').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('select[name="project_id"]').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');
                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);


                        if($response.data.recording_address)
                        {
                            // var $html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+$item.recording_address+'" type="audio/mpeg"></audio>'
                            // $row.find('[data-key="recording_address_play"]').html($html);

                            var $recording_list = JSON.parse($response.data.recording_address);
                            var $recording_list_html = '';
                            $.each($recording_list, function(index, value)
                            {

                                var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                $recording_list_html += $audio_html;
                            });
                            $modal.find('.item-recording-box .item-detail-text').html($recording_list_html);

                            $row.find('[data-key="recording_address_play"]').html($recording_list_html);
                            $row.find('[data-key="order_status"]').attr('data-recording-address',$recording_list_html);

                            $row.find('[data-key=recording_address_download]').attr('data-address-list',$item.recording_address_list);
                            // var $recording_redirection = '<a class="btn btn-xs item-inspected-redirection-recording-list-submit" data-id="'+$id+'">跳转</a>';
                            // $that.after($recording_redirection);
                        }


                        $modal.find('textarea[name="description"]').val($response.data.description);


                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('#'+$that.attr('id')+'.post.fail.');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('#'+$that.attr('id')+'.post.always.');
                    layer.closeAll('loading');
                });



            $modal.modal('show');
        });
        // 【工单】【审核】提交
        $(".main-wrapper").off('click', "#item-submit--for--order--item-inspecting").on('click', "#item-submit--for--order--item-inspecting", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

            var $modal_id = 'modal--for--order--item-inspecting';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-inspecting';
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-inspecting-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    layer.closeAll('loading');
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset("#"+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $order = $response.data.order;
                        // update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    layer.msg('服务器错误！');
                    console.log($(this).attr('id')+'.click.error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }


            };
            $form.ajaxSubmit(options);
        });


        // 【工单】【审核】【获取录音】
        $(".main-wrapper").on('click', ".item-recording-list-get--for--order--item-inspecting", function() {
            var $that = $(this);
            var $modal_wrapper = $that.closest('.modal-wrapper');
            var $id = $modal_wrapper.find('input[name="item_id"]').val();
            var $row = $('tr.operating');
            console.log($id);
            // return false;


            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">耐心等待中</span>',
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

            $.post(
                "{{ url('/o1/order/item-call-recording--get--by-api') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order--item-call-recording--get--by-api",
                    item_id: $id
                },
                'json'
            )
                .done(function($response) {

                    layer.closeAll('loading');
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        // console.log(JSON.parse($response.data));

                        console.log($response.data.data);
                        var $item = $response.data.data;
                        if($item.recording_address_list)
                        {
                            // var $html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+$item.recording_address+'" type="audio/mpeg"></audio>'
                            // $row.find('[data-key="recording_address_play"]').html($html);

                            var $recording_list = JSON.parse($item.recording_address_list);
                            var $recording_list_html = '';
                            $.each($recording_list, function(index, value)
                            {

                                var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                $recording_list_html += $audio_html;
                            });
                            $modal_wrapper.find('.item-recording-box .item-detail-text').html($recording_list_html);
                            $row.find('[data-key="recording_address_play"]').html($recording_list_html);
                            $row.find('[data-key="description"]').attr('data-recording-address',$recording_list_html);

                            $row.find('[data-key=recording_address_download]').attr('data-address-list',$item.recording_address_list);
                            // var $recording_redirection = '<a class="btn btn-xs item-inspected-redirection-recording-list-submit" data-id="'+$id+'">跳转</a>';
                            // $that.after($recording_redirection);
                        }

                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    layer.closeAll('loading');
                });

        });



        // 【工单】【申诉】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-appealing").on('click', ".modal-show--for--order--item-appealing", function() {
            var $that = $(this);
            var $id = $that.data('id');
            var $item_id = $that.data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


            var $operation_datatable_id = 'datatable--for--order--item-appealing--of--operation-record-list';
            Datatable__for__Order_Item_Operation_Record_List.init($operation_datatable_id,$id);


            var $modal_id = 'modal--for--order--item-appealing';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-appealing';
            var $form = $("#"+$form_id);

            $modal.find('input[name="operate[id]"]').val($id);
            $modal.find('input[name="item_id"]').val($id);


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
                "{{ url('/o1/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('#'+$that.attr('id')+'.post.done.');

                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        form_reset("#"+$form_id);


                        $modal.find('.id-box').html('【'+$id+'】');
                        // $modal.find('.title-box').html('审核工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('inspecting');
                        $modal.find('input[name="operate[id]"]').val($item_id);
                        $modal.find('input[name="item_id"]').val($item_id);

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="teeth_count"]').val($response.data.teeth_count).trigger('change');
                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('select[name="location_district"]').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('select[name="project_id"]').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');
                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);


                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('#'+$that.attr('id')+'.post.fail.');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('#'+$that.attr('id')+'.post.always.');
                    layer.closeAll('loading');
                });



            $modal.modal('show');
        });
        // 【工单】【申诉】提交
        $(".main-wrapper").off('click', "#item-submit--for--order--item-appealing").on('click', "#item-submit--for--order--item-appealing", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

            var $modal_id = 'modal--for--order--item-appealing';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-appealing';
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-appealing-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    layer.closeAll('loading');
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset("#"+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $order = $response.data.order;
                        // update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    layer.msg('服务器错误！');
                    console.log($(this).attr('id')+'.click.error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }


            };
            $form.ajaxSubmit(options);
        });


        // 【工单】【申诉处理】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-appealed-handling").on('click', ".modal-show--for--order--item-appealed-handling", function() {
            var $that = $(this);
            var $id = $that.data('id');
            var $item_id = $that.data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


            var $operation_datatable_id = 'datatable--for--order--item-appealed-handling--of--operation-record-list';
            Datatable__for__Order_Item_Operation_Record_List.init($operation_datatable_id,$id);


            var $modal_id = 'modal--for--order--item-appealed-handling';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-appealed-handling';
            var $form = $("#"+$form_id);

            $modal.find('input[name="operate[id]"]').val($id);
            $modal.find('input[name="item_id"]').val($id);


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
                "{{ url('/o1/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('#'+$that.attr('id')+'.post.done.');

                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        form_reset("#"+$form_id);


                        $modal.find('.id-box').html('【'+$id+'】');
                        // $modal.find('.title-box').html('审核工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('inspecting');
                        $modal.find('input[name="operate[id]"]').val($item_id);
                        $modal.find('input[name="item_id"]').val($item_id);

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="teeth_count"]').val($response.data.teeth_count).trigger('change');
                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('select[name="location_district"]').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('select[name="project_id"]').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');
                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);


                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('#'+$that.attr('id')+'.post.fail.');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('#'+$that.attr('id')+'.post.always.');
                    layer.closeAll('loading');
                });



            $modal.modal('show');
        });
        // 【工单】【申诉处理】提交
        $(".main-wrapper").off('click', "#item-submit--for--order--item-appealed-handling").on('click', "#item-submit--for--order--item-appealed-handling", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

            var $modal_id = 'modal--for--order--item-appealed-handling';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-appealed-handling';
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-appealed-handling-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    layer.closeAll('loading');
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset("#"+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $order = $response.data.order;
                        // update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    layer.msg('服务器错误！');
                    console.log($(this).attr('id')+'.click.error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }


            };
            $form.ajaxSubmit(options);
        });




        // 【工单】【交付】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-delivering").on('click', ".modal-show--for--order--item-delivering", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $delivery_datatable_id = 'datatable--for--order--item-delivering--of--delivery-record-list';
            Datatable__for__Order_Item_Delivery_Record_List.init($delivery_datatable_id,$id);


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


            var $modal_id = 'modal--for--order--item-delivering';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-delivering';
            var $form = $("#"+$form_id);


            $modal.find('input[name="operate[id]"]').val($id);
            $modal.find('input[name="item_id"]').val($id);


            form_reset("#"+$form_id);


            $modal.find('.id-box').html('【'+$id+'】');


            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

            $modal.modal('show');
        });
        // 【工单】【交付】提交
        $(".main-wrapper").off('click', "#item-submit--for--order--item-delivering").on('click', "#item-submit--for--order--item-delivering", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

            var $modal_id = 'modal--for--order--item-delivering';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-delivering';
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-delivering-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    layer.closeAll('loading');
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset("#"+$form_id);

                        // $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $order = $response.data.order;
                        // console.log($row);
                        // console.log($order);
                        // update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    layer.msg('服务器错误！');
                    console.log($(this).attr('id')+'.click.error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }
            };
            $form.ajaxSubmit(options);
        });


        // 【工单】【分发】显示
        $(".main-wrapper").off('click', ".modal-show--for--order--item-distributing").on('click', ".modal-show--for--order--item-distributing", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $delivery_datatable_id = 'datatable--for--order--item-distributing--of--delivery-record-list';
            Datatable__for__Order_Item_Delivery_Record_List.init($delivery_datatable_id,$id);


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


            var $modal_id = 'modal--for--order--item-distributing';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-distributing';
            var $form = $("#"+$form_id);


            $modal.find('input[name="operate[id]"]').val($id);
            $modal.find('input[name="item_id"]').val($id);


            form_reset("#"+$form_id);


            $modal.find('.id-box').html('【'+$id+'】');


            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

            $modal.modal('show');
        });
        // 【工单】【分发】提交
        $(".main-wrapper").off('click', "#item-submit--for--order--item-distributing").on('click', "#item-submit--for--order--item-distributing", function() {
            var $that = $(this);
            var $item_id = $that.data('item-id');
            var $table_id = $that.data('datatable-list-id');
            var $row = $('#'+$table_id).find('[data-key="id"][data-value='+$item_id+']').parents('tr');

            var $modal_id = 'modal--for--order--item-distributing';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order--item-distributing';
            var $form = $("#"+$form_id);

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

            var options = {
                url: "{{ url('/o1/order/item-distributing-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    layer.closeAll('loading');
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        // 重置输入框
                        form_reset("#"+$form_id);

                        // $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $order = $response.data.order;
                        // update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    layer.msg('服务器错误！');
                    console.log($(this).attr('id')+'.click.error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }
            };
            $form.ajaxSubmit(options);
        });








        // 【删除】
        $(".main-wrapper").on('click', ".order--item-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                $('#datatable--for--order-list').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });








        // 【批量操作】全选or反选
        $(".main-wrapper").on('click', '#check-review-all', function () {
            console.log('#check-review-all.click');
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】全选or反选
        $(".main-wrapper").on('click', '.check-review-all', function () {
            console.log('.check-review-all.click');
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            $datatable_wrapper.find('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】批量-导出
        $(".main-wrapper").on('click', '#bulk-submit--for--export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export--for--order-by-ids?ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-wrapper").on('click', '#bulk-submit--for--delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export--for--order-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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

                    $.post(
                        "{{ url('/item/order-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable--for--order-list').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);

                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });
        // 【批量操作】批量-导出
        $(".main-wrapper").off('click', '.bulk-submit--for--order-export').on('click', '.bulk-submit--for--order-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $that = $(this);
            var $item_category = $that.data('item-category');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/v1/operate/statistic/order-export-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-wrapper").off('click', '.bulk-submit--for--order-delivered').on('click', '.bulk-submit--for--order-delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export--for--order-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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

                    $.post(
                        "{{ url('/item/order-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable--for--order-list').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);

                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });




        // 【批量操作】批量-导出
        $(".main-wrapper").off('click', '.order--bulk-export-summit').on('click', '.order--bulk-export-summit', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $that = $(this);
            var $order_category = $that.data('order-category');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/o1/export/order--export--by-ids?order_category='+$order_category+'&ids='+$ids);
            window.open($url);


        });

        // 【批量操作】批量-一键交付
        $(".main-wrapper").off('click', '.order--bulk-delivering-summit--by-fool').on('click', '.order--bulk-delivering-summit--by-fool', function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $that.data('item-category');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);


            layer.msg('确定"批量一键交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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

                    $.post(
                        "{{ url('/o1/order/bulk-delivering-save--by-fool') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order--bulk-delivering-save--by-fool",
                            ids: $ids
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                        });

                }
            });

        });




    });

    function update_order_row($row,$order)
    {
        // 派车日期
        var $assign_date = $order.assign_date;
        var $assign_time_value = '';
        if($assign_date)
        {
            var $date = new Date($assign_date*1000);
            var $year = $date.getFullYear();
            var $month = ('00'+($date.getMonth()+1)).slice(-2);
            var $day = ('00'+($date.getDate())).slice(-2);
            $assign_time_value = $year+'-'+$month+'-'+$day;
        }
        $row.find('[data-key="assign_date"]').attr('data-value',$assign_time_value).html($assign_time_value);

        // 任务日期
        var $task_date = $order.task_date;
        var $task_time_value = '';
        if($assign_date)
        {
            var $date = new Date($task_date*1000);
            var $year = $date.getFullYear();
            var $month = ('00'+($date.getMonth()+1)).slice(-2);
            var $day = ('00'+($date.getDate())).slice(-2);
            $task_time_value = $year+'-'+$month+'-'+$day;
        }
        $row.find('[data-key="task_date"]').attr('data-value',$task_time_value).html($task_time_value);

        // 费用
        var $financial_expense_total = parseFloat($order.financial_expense_total);
        $row.find('[data-key="financial_expense_total"]').attr('data-value',$financial_expense_total).html($financial_expense_total);

        // 订单扣款
        var $financial_deduction_total = parseFloat($order.financial_deduction_total);
        $row.find('[data-key="financial_deduction_total"]').attr('data-value',$financial_deduction_total).html($financial_deduction_total);

        // 已收款
        var $financial_income_total = parseFloat($order.financial_income_total);
        $row.find('[data-key="financial_income_total"]').attr('data-value',$financial_income_total).html($financial_income_total);

        // 应收款
        var $financial_income_should = parseFloat($order.freight_amount) - parseFloat($order.financial_deduction_total);
        $financial_income_should = parseFloat($financial_income_should);
        $row.find('[data-key="financial_income_should"]').attr('data-value',$financial_income_should).html($financial_income_should);

        // 待收款
        var $financial_income_pending = parseFloat($order.freight_amount) - parseFloat($order.financial_deduction_total) - parseFloat($order.financial_income_total);
        $financial_income_pending = parseFloat($financial_income_pending);
        $row.find('[data-key="financial_income_pending"]').attr('data-value',$financial_income_pending).html($financial_income_pending);

        // 利润
        var $financial_profit = parseFloat($order.freight_amount) - parseFloat($order.financial_deduction_total) - parseFloat($order.financial_expense_total);
        $financial_profit = parseFloat($financial_profit);
        $row.find('[data-key="financial_profit"]').attr('data-value',$financial_profit).html($financial_profit);

    }

</script>