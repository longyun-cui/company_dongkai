<script>
    $(function() {


        // 【工单】添加-显示
        $(".main-wrapper").on('click', ".modal-show--for--order-item-create", function() {
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
        $(".main-content").on('click', ".modal-show--for--order-item-edit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

            var $modal_id = 'modal--for--order-item-edit';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order-item-edit';
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

                        $modal.find('.box-title').html('编辑订单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="assign_date"]').val($response.data.assign_date);
                        $modal.find('input[name="task_date"]').val($response.data.task_date);

                        // 订单类型
                        $modal.find('input[name="order_type"]').prop('checked', false);
                        $modal.find('input[name="order_type"][value="'+$response.data.order_type+'"]').prop('checked', true).trigger('change');

                        // select2 客户
                        if($response.data.client_er)
                        {
                            $modal.find('#order-edit-select2-client').append(new Option($response.data.client_er.name, $response.data.client_id, true, true)).trigger('change');
                        }

                        // select2 项目
                        if($response.data.project_er)
                        {
                            $modal.find('select[name="project_id"]').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        // select
                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');

                        $modal.find('input[name="transport_departure_place"]').val($response.data.transport_departure_place);
                        $modal.find('input[name="transport_destination_place"]').val($response.data.transport_destination_place);

                        // 距离
                        $modal.find('input[name="transport_distance"]').val($response.data.transport_distance);
                        // 时效
                        var $transport_time_limitation = parseFloat(($response.data.transport_time_limitation / 60).toFixed(2));
                        $modal.find('input[name="transport_time_limitation"]').val($transport_time_limitation);

                        // 运费 & 油卡 & 信息费
                        $modal.find('input[name="freight_amount"]').val(parseFloat($response.data.freight_amount));
                        $modal.find('input[name="oil_card_amount"]').val(parseFloat($response.data.oil_card_amount));
                        $modal.find('input[name="information_fee"]').val(parseFloat($response.data.information_fee));

                        // radio 车辆所属
                        $modal.find('input[name="car_owner_type"]').prop('checked', false);
                        $modal.find('input[name="car_owner_type"][value="'+$response.data.car_owner_type+'"]').prop('checked', true).trigger('change');
                        if($response.data.car_owner_type == 1)
                        {
                            $('.internal-car').show();
                            $('.external-car').hide();
                        }
                        else if($response.data.car_owner_type == 11)
                        {
                            $('.internal-car').hide();
                            $('.external-car').show();
                        }

                        // select2 车辆
                        if($response.data.car_er)
                        {
                            $modal.find('select[name="car_id"]').append(new Option($response.data.car_er.name, $response.data.car_id, true, true)).trigger('change');
                        }
                        // select2 车挂
                        if($response.data.trailer_er)
                        {
                            $modal.find('select[name="trailer_id"]').append(new Option($response.data.trailer_er.name, $response.data.trailer_id, true, true)).trigger('change');
                        }

                        // select2 车辆
                        if($response.data.driver_er)
                        {
                            $modal.find('select[name="driver_id"]').append(new Option($response.data.driver_er.driver_name, $response.data.driver_id, true, true)).trigger('change');
                        }
                        // select2 车挂
                        if($response.data.copilot_er)
                        {
                            $modal.find('select[name="copilot_id"]').append(new Option($response.data.copilot_er.driver_name, $response.data.copilot_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="external_car"]').val($response.data.external_car);
                        $modal.find('input[name="external_trailer"]').val($response.data.external_trailer);

                        $modal.find('input[name="driver_name"]').val($response.data.driver_name);
                        $modal.find('input[name="driver_phone"]').val($response.data.driver_phone);
                        $modal.find('input[name="copilot_name"]').val($response.data.copilot_name);
                        $modal.find('input[name="copilot_phone"]').val($response.data.copilot_phone);

                        $modal.find('input[name="arrange_people"]').val($response.data.arrange_people);
                        $modal.find('input[name="payee_name"]').val($response.data.payee_name);
                        $modal.find('input[name="car_supply"]').val($response.data.car_supply);

                        $modal.find('textarea[name="description"]').val($response.data.description);

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

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
        $(".main-content").on('click', "#submit--for--order-item-edit", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--order-item-edit';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--order-item-edit';
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


        // 【工单】【添加工单】select2 选择项目
        $('#select2--project--for-order-item-edit').on('select2:select', function(e) {
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
        $('#select2--car--for-order-item-edit').on('select2:select', function(e) {

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
        $('#select2--driver--for--order-item-edit').on('select2:select', function(e) {
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


        // 【工单】【添加费用】费用类型
        $(".main-wrapper").on('change', '#modal--for--order-item-fee-create input[name="fee-type"]', function() {
            var $that = $(this);
            var $modal = $that.parents('.modal-wrapper');

            var $value = $(this).val();
            if($value == 1)
            {
                $modal.find('input[name="fee-record-type"][value="1"]').prop('checked', true).trigger('change');
                $modal.find('.fee-record-type-box').show();
                $modal.find('.collection-box').show();
                $modal.find('.advance-box').hide();
            }
            else if($value == 99)
            {
                $modal.find('input[name="fee-record-type"][value="1"]').prop('checked', true).trigger('change');
                $modal.find('.fee-record-type-box').show();
                $modal.find('.advance-box').show();
                $modal.find('.collection-box').hide();
            }
            else
            {
                $modal.find('input[name="fee-record-type"][value="1"]').prop('checked', true).trigger('change');
                $modal.find('.fee-record-type-box').hide();
                $modal.find('.payment-show').hide();
            }
        });
        // 【工单】【添加费用】记录类型
        $(".main-wrapper").on('change', '#modal--for--order-item-fee-create input[name="fee-record-type"]', function() {
            var $that = $(this);
            var $modal = $that.parents('.modal-wrapper');

            var $value = $(this).val();
            if($value == 81)
            {
                $modal.find('.payment-show').show();
            }
            else
            {
                $modal.find('.payment-show').hide();
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












        // 【通用】显示详情
        $(".main-content").off('dblclick', ".modal-show--for--item-detail").on('dblclick', ".modal-show--for--item-detail", function() {
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
        $(".main-content").off('click', ".modal-show--for--order-item-operation-record").on('click', ".modal-show--for--order-item-operation-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            Datatable__for__Order_Item_Operation_Record_List.init($id);

            $('#modal--for--order-item-operation-record-list').modal('show');
        });
        // 【工单】操作记录
        $(".main-content").off('click', ".modal-show--for--order-item-fee-record").on('click', ".modal-show--for--order-item-fee-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            Datatable_Order_Fee_Record.init($id);

            $('#modal--for--order-fee-record-list').modal('show');
        });




        // 【工单】【跟进】显示
        $(".main-wrapper").off('click', ".modal-show--for--order-item-follow-create").on('click', ".modal-show--for--order-item-follow-create", function() {
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

            form_reset('#form--for--order-item-follow-create');

            var $modal = $('#modal--for--order-item-follow-create');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.modal('show');
            // $('#modal--for--order-follow-create').modal({show: true,backdrop: 'static'});
            // $('.modal-backdrop').each(function() {
            //     $(this).attr('id', 'id_' + Math.random());
            // });
        });
        // 【工单】【跟进】提交
        $(".main-wrapper").off('click', "#form-submit--for--order-item-follow-create").on('click', "#form-submit--for--order-item-follow-create", function() {
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

                        $('#modal--for--order-item-follow-create').modal('hide');
                        // $('#modal--for--order-item-follow-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    console.log('#form-submit--for--order-item-follow-create.click.error');
                    layer.msg('服务器错误！');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log('#form-submit--for--order-item-follow-create.click.complete');
                }


            };
            $("#form--for--order-item-follow-create").ajaxSubmit(options);
        });



        // 【工单】【行程】显示
        $(".main-wrapper").off('click', ".modal-show--for--order-item-journey-create").on('click', ".modal-show--for--order-item-journey-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            Datatable__for__Order_Item_Journey_Record_List.init($id);


            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form--for--order-item-journey-create');


            var $modal = $('#modal--for--order-item-journey-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('#item-submit--for--order-item-journey-create').data('item-id',$id);
            $modal.find('#item-submit--for--order-item-journey-create').data('datatable-list-id',$table_id);
            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.journey-create-order-id').html($id);

            $modal.modal('show');
        });
        // 【工单】【行程】提交
        $(".main-wrapper").off('click', "#item-submit--for--order-item-journey-create").on('click', "#item-submit--for--order-item-journey-create", function() {
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
                url: "{{ url('/o1/order/item-journey-save') }}",
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
                        form_reset('#form--for--order-item-journey-create');

                        // $('#modal--for--order-item-journey-create').modal('hide');
                        // $('#modal--for--order-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        var $datatable_id = 'datatable--for--order-item-journey-record-list'
                        $('#'+$datatable_id).DataTable().ajax.reload(null,false);

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
            $("#form--for--order-item-journey-create").ajaxSubmit(options);
        });




        // 【工单】【费用】显示
        $(".main-wrapper").off('click', ".modal-show--for--order-item-fee-create").on('click', ".modal-show--for--order-item-fee-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            Datatable__for__Order_Item_Fee_Record_List.init($id);

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form--for--order-item-fee-create');

            var $modal = $('#modal--for--order-item-fee-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('#item-submit--for--order-item-fee-create').data('item-id',$id);
            $modal.find('#item-submit--for--order-item-fee-create').data('datatable-list-id',$table_id);
            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.fee-create-order-id').html($id);

            $modal.modal('show');
        });
        // 【工单】【费用】提交
        $(".main-wrapper").off('click', "#item-submit--for--order-item-fee-create").on('click', "#item-submit--for--order-item-fee-create", function() {
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
                url: "{{ url('/o1/order/item-fee-save') }}",
                type: "post",
                dataType: "json",
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
                        form_reset('#form--for--order-item-fee-create');

                        $('#modal--for--order-item-fee-create').modal('hide');
                        // $('#modal--for--order-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);

                        var $order = $response.data.order;
                        console.log($row);
                        console.log($order);
                        update_order_row($row,$order);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.error');
                    layer.msg('服务器错误！');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                    console.log($(this).attr('id')+'.click.complete');
                }


            };
            $("#form--for--order-item-fee-create").ajaxSubmit(options);
        });




        // 【工单】【成交】显示
        $(".main-wrapper").off('click', ".item-modal-show--for--order-item-trade-create").on('click', ".item-modal-show--for--order-item-trade-create", function() {
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

            form_reset('#form--for--order-trade-create');

            var $modal = $('#modal--for--order-trade-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.follow-create-order-id').html($id);

            $modal.modal('show');
        });
        // 【工单】【成交】提交
        $(".main-wrapper").off('click', "#item-submit--for--order-item-trade-create").on('click', "#item-submit--for--order-item-trade-create", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

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
                url: "{{ url('/v1/operate/order/item-trade-save') }}",
                type: "post",
                dataType: "json",
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
                        form_reset('#form--for--order-trade-create');

                        $('#modal--for--order-trade-create').modal('hide');
                        // $('#modal--for--order-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('always');
                    layer.closeAll('loading');
                }


            };
            $("#form--for--order-trade-create").ajaxSubmit(options);
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
        // 【弃用】
        $(".main-wrapper").on('click', ".item-abandon-submit", function() {
            var $that = $(this);
            layer.msg('确定"弃用"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-abandon') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-abandon",
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
        // 【复用】
        $(".main-wrapper").on('click', ".item-reuse-submit", function() {
            var $that = $(this);
            layer.msg('确定"复用"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-reuse') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-reuse",
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
        // 【发布】
        $(".main-wrapper").on('click', ".item-publish-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);
            console.log($table);

            layer.msg('确定"发布"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

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
                        {{--"{{ url('/item/order-publish') }}",--}}
                        "{{ url('/v1/operate/order/item-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-publish",
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                    .done(function($response, status, jqXHR) {
                        console.log('done');
                        $response = JSON.parse($response);

                        layer.msg($response.msg);
                        if(!$response.success)
                        {
                        }
                        else
                        {
                            $table.DataTable().ajax.reload(null,false);
                        }
                    })
                    .fail(function(jqXHR, status, error) {
                        console.log('fail');
                        layer.msg('服务器错误！');

                    })
                    .always(function(jqXHR, status) {
                        console.log('always');
                        layer.close(index);
                        layer.closeAll('loading');
                    });

                }
            });
        });
        // 【完成】
        $(".main-wrapper").on('click', ".item-complete-submit", function() {
            var $that = $(this);
            layer.msg('确定"完成"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-complete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-complete",
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
        // 【验证】
        $(".main-wrapper").on('click', ".item-verify-submit", function() {
            var $that = $(this);
            layer.msg('确定"审核"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-verify') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-verify",
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
        // 【审核】
        $(".main-wrapper").on('click', ".item-inspect-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '选择审核状态！'
                ,content: '<select class="form-control form-filter" name="inspected-result" style="width:160px;">'+
                    '<option value ="-1">选择审核状态</option>'+
                    '<option value ="通过">通过</option>'+
                    '<option value ="拒绝">拒绝</option>'+
                    '<option value ="重复">重复</option>'+
                    '<option value ="内部通过">内部通过</option>'+
                    '<option value ="二次待审">二次待审</option>'+
                    '<option value ="已审未提">已审未提</option>'+
                    '<option value ="回访重提">回访重提</option>'+
                    '</select>'+
                    '<textarea class="form-control" name="inspected-description" placeholder="审核说明" rows="3"></textarea>'
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-inspect') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-inspect",
                            item_id: $that.attr('data-id'),
                            inspected_result: $('select[name="inspected-result"]').val(),
                            inspected_description: $('textarea[name="inspected-description"]').val(),
                            is_distributive_condition: $('input[name="is_distributive_condition"]:checked').val()
                        },
                        function(data){
                            layer.close(index);
                            // layer.form.render();
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                // $('#datatable--for--order-list').DataTable().ajax.reload(null,false);
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });







        // 【修改记录】【显示】
        $(".main-wrapper").on('click', ".item-modal-show--for--modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body--for--modify-list').modal('show');
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