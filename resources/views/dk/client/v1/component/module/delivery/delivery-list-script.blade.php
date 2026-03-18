<script>
    $(function() {


        // 【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】
        $(".main-content").off('click', '.delivery--bulk-export--summit').on('click', '.delivery--bulk-export--summit', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $order_category = $that.data('order-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');


            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);

            var $url = url_build('/o1/export/order--export--by-ids?order_category='+$order_category+'&ids='+$ids);
            window.open($url);
        });
        // 【批量操作】批量-导出
        $(".main-content").on('click', '.delivery--bulk-exported-status-change--submit', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);


            layer.msg('确定"批量导出"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    $.post(
                        "{{ url('/o1/delivery/bulk-exported-status-change') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery--bulk-exported-status-change",
                            ids: $ids,
                            operate_result:$('select[name="bulk-operate-status"]').val()
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                // $('#datatable_ajax').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $operate_result = $('select[name="bulk-operate-status"]').val();

                                    if($operate_result == "1")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-success">已导出</small>');
                                    }
                                    else if($operate_result == "0")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-info">未导出</small>');
                                    }
                                    else if($operate_result == "9")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-primary">待导出</small>');
                                    }
                                    else
                                    {
                                    }

                                });
                            }
                        },
                        'json'
                    );

                }
            });

        });




        // 【工单】操作记录
        $(".main-content").off('click', ".modal-show--for--delivery--item-operation-record").on('click', ".modal-show--for--delivery--item-operation-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $datatable_id = 'datatable--for--delivery--item-operation-record-list';

            Datatable__for__Delivery__Item_Operation_Record_List.init($datatable_id,$id);

            $('#modal--for--delivery--item-operation-record-list').modal('show');
        });





        // 【质量评估】
        $(".main-content").on('click', ".delivery--item--quality-evaluate--submit", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '选择质量！'
                ,content: '<select class="form-control form-filter" name="order-quality" style="width:160px;">'+
                    '<option value ="-1">选择质量</option>'+
                    '<option value ="有效">有效</option>'+
                    '<option value ="无效">无效</option>'+
                    '<option value ="重单">重单</option>'+
                    '<option value ="待联系">待联系</option>'+
                    '</select>'
                ,yes: function(index){
                    $.post(
                            {{--"{{ url('/item/delivery-quality-evaluate') }}",--}}
                                "{{ url('/o1/delivery/item--quality-evaluate--save') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery--item--quality-evaluate",
                            item_id: $that.attr('data-id'),
                            order_quality: $('select[name="order-quality"]').val()
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【交付】【客户信息】编辑-显示
        $(".main-content").off('click', ".modal-show--for--delivery--item-customer-update").on('click', ".modal-show--for--delivery--item-customer-update", function() {
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
                "{{ url('/o1/delivery/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "delivery",
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
                        var $modal = $('#modal--for--delivery--item-operating--customer-update');

                        // $modal.find('.box-title').html('更新客户信息【'+$that.attr('data-id')+'】');
                        $modal.find('.id-title').html('【'+$id+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="mobile"]').val($response.data.mobile);
                        $modal.find('input[name="customer_remark"]').val($response.data.customer_remark);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true);
                        $modal.find('input[name="is_come"][value="'+$response.data.is_come+'"]').prop('checked', true);
                        // $modal.find('select[name="department_id"]').val($response.data.department_id);

                        if($response.data.client_contact_er)
                        {
                            $modal.find('select[name="client_contact_id"]').append(new Option($response.data.client_contact_er.name, $response.data.client_contact_id, true, true)).trigger('change');
                        }

                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
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
        // 【交付】【客户信息】编辑-提交
        $(".main-content").off('click', "#item-submit--for--delivery--item-operating--customer-update").on('click', "#item-submit--for--delivery--item-operating--customer-update", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $modal_id = $that.data('modal-id');
            var $modal = $('#'+$modal_id);

            var $form_id = $that.data('form-id');
            var $form = $('#'+$form_id);

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
                url: "{{ url('/o1/delivery/item--customer-update--save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
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
            $form.ajaxSubmit(options);
        });




        // 【交付】【回访】编辑-显示
        $(".main-content").off('click', ".modal-show--for--delivery--item-callback-update").on('click', ".modal-show--for--delivery--item-callback-update", function() {
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
                "{{ url('/o1/delivery/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "delivery",
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
                        var $modal = $('#modal--for--delivery--item-operating--callback-update');

                        // $modal.find('.box-title').html('更新上门状态【'+$that.attr('data-id')+'】');
                        $modal.find('.id-title').html('【'+$id+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="is_come"][value="'+$response.data.is_come+'"]').prop('checked', true);
                        $modal.find('input[name="callback_datetime"]').val($response.data.callback_datetime);


                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
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
        // 【交付】【回访】编辑-提交
        $(".main-content").off('click', "#item-submit--for--delivery--item-operating--callback-update").on('click', "#item-submit--for--delivery--item-operating--callback-update", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $modal_id = $that.data('modal-id');
            var $modal = $('#'+$modal_id);

            var $form_id = $that.data('form-id');
            var $form = $('#'+$form_id);

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
                url: "{{ url('/o1/delivery/item--callback-update--save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
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
            $form.ajaxSubmit(options);
        });




        // 【交付】【上门状态】编辑-显示
        $(".main-content").off('click', ".modal-show--for--delivery--item-come-update").on('click', ".modal-show--for--delivery--item-come-update", function() {
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
                "{{ url('/o1/delivery/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "delivery",
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
                        var $modal = $('#modal--for--delivery--item-operating--come-update');

                        // $modal.find('.box-title').html('更新上门状态【'+$that.attr('data-id')+'】');
                        $modal.find('.id-title').html('【'+$id+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="is_come"][value="'+$response.data.is_come+'"]').prop('checked', true);
                        $modal.find('input[name="come_datetime"]').val($response.data.come_datetime);


                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
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
        // 【交付】【上门状态】编辑-提交
        $(".main-content").off('click', "#item-submit--for--delivery--item-operating--come-update").on('click', "#item-submit--for--delivery--item-operating--come-update", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $modal_id = $that.data('modal-id');
            var $modal = $('#'+$modal_id);

            var $form_id = $that.data('form-id');
            var $form = $('#'+$form_id);

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
                url: "{{ url('/o1/delivery/item--come-update--save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
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
            $form.ajaxSubmit(options);
        });




        // 【交付】【跟进】添加-显示
        $(".main-content").off('click', ".modal-show--for--delivery--item-follow-create").on('click', ".modal-show--for--delivery--item-follow-create", function() {
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

            form_reset('#form--for--delivery--item-operating--follow-create');

            var $modal = $('#modal--for--delivery--item-operating--follow-create');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.modal('show');
            // $('#modal-for-delivery-follow-create').modal({show: true,backdrop: 'static'});
            // $('.modal-backdrop').each(function() {
            //     $(this).attr('id', 'id_' + Math.random());
            // });
        });
        // 【交付】【跟进】编辑-提交
        $(".main-content").off('click', "#item-submit--for--delivery--item-operating--follow-create").on('click', "#item-submit--for--delivery--item-operating--follow-create", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $modal_id = $that.data('modal-id');
            var $modal = $('#'+$modal_id);

            var $form_id = $that.data('form-id');
            var $form = $('#'+$form_id);

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
                url: "{{ url('/o1/delivery/item--follow-create--save') }}",
                type: "post",
                dataType: "json",
                // data: { _token: $('meta[name="_token"]').attr('content') },
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                }


            };
            $form.ajaxSubmit(options);
        });




        // 【交付】【成交】添加-显示
        $(".main-content").off('click', ".modal-show--for--delivery--item-trade-create").on('click', ".modal-show--for--delivery--item-trade-create", function() {
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

            form_reset('#form--for--delivery--item-operating--trade-create');

            var $modal = $('#modal--for--delivery--item-operating--trade-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.follow-create-delivery-id').html($id);

            $modal.modal('show');
        });
        // 【交付】【成交】编辑-提交
        $(".main-content").off('click', "#item-submit--for--delivery--item-operating--trade-create").on('click', "#item-submit--for--delivery--item-operating--trade-create", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $modal_id = $that.data('modal-id');
            var $modal = $('#'+$modal_id);

            var $form_id = $that.data('form-id');
            var $form = $('#'+$form_id);

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
                url: "{{ url('/o1/delivery/item--trade-create--save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#'+$form_id);

                        $modal.modal('hide');
                        // $modal.modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    layer.closeAll('loading');
                }


            };
            $form.ajaxSubmit(options);
        });




        $('.modal-wrapper').on('hide.bs.modal', function (event) {
            $('.datatable-wrapper').removeClass('operating');
            $('.datatable-wrapper').find('tr.operating').removeClass('operating');
        });
        // 【modal】取消
        $(".main-content").on('click', ".modal-cancel", function() {
            var $that = $(this);
            var $modal = $that.closest('.modal-wrapper');
            $modal.modal('hide');

            // $modal.modal('hide').on("hidden.bs.modal", function () {
            //     $("body").addClass("modal-open");
            // });
        });


    });
</script>