<script>
    $(function() {


        // 【部门】添加-显示
        $(".main-wrapper").on('click', ".modal-show--for--department-item-create", function() {
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
        // 【部门】编辑-显示
        $(".main-wrapper").on('click', ".modal-show--for--department-item-edit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

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
                "{{ url('/o1/department/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "department",
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

                        form_reset('#modal--for--department-item-edit');

                        var $modal = $('#modal--for--department-item-edit');
                        $modal.find('.box-title').html('编辑部门【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="name"]').val($response.data.name);
                        $modal.find('input[name="department_category"]').prop('checked', false);
                        $modal.find('input[name="department_category"][value="'+$response.data.department_category+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-department-category').hide();
                        $modal.find('input[name="department_category"][value="'+$response.data.department_category+'"]').parents('.radio-department-category').show();

                        // 公司
                        if($response.data.company_er)
                        {
                            $modal.find('select[name="company_id"]').append(new Option($response.data.company_er.name, $response.data.company_id, true, true)).trigger('change');
                        }
                        // 上级部门
                        if($response.data.superior_department_er)
                        {
                            $modal.find('select[name="superior_department_id"]').append(new Option($response.data.superior_department_er.name, $response.data.superior_department_id, true, true)).trigger('change');
                        }
                        // 负责人
                        if($response.data.leader)
                        {
                            // $modal.find('#select2-leader option[value="'+$response.data.leader_id+'"]').prop('selected', true);
                            $modal.find('select[name="leader_id"]').append(new Option($response.data.leader.username, $response.data.leader_id, true, true)).trigger('change');
                        }

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
        // 【部门】编辑-提交
        $(".main-wrapper").on('click', "#submit--for--department-item-edit", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--department-item-edit';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--department-item-edit';
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
                url: "{{ url('/o1/department/item-save') }}",
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

                        $table.DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.error.');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.complete.');
                    layer.closeAll('loading');
                }
            };
            $form.ajaxSubmit(options);
        });


        // 【部门】删除
        $(".main-wrapper").off('click', ".department--item-delete-submit").on('click', ".department--item-delete-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


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
                        "{{ url('/o1/department/item-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "department--item-delete",
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
                ,btn2: function(index)
                {
                    layer.close(index);
                    $row.removeClass('operating');
                }
            });
        });
        // 【部门】恢复
        $(".main-wrapper").off('click', ".department--item-restore-submit").on('click', ".department--item-restore-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


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
                        "{{ url('/o1/department/item-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "department--item-restore",
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
                ,btn2: function(index)
                {
                    layer.close(index);
                    $row.removeClass('operating');
                }
            });
        });
        // 【部门】永久删除
        $(".main-wrapper").off('click', ".department--item-delete-permanently-submit").on('click', ".department--item-delete-permanently-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


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
                        "{{ url('/o1/department/item-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "department--item-delete-permanently",
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
                ,btn2: function(index)
                {
                    layer.close(index);
                    $row.removeClass('operating');
                }
            });
        });


        // 【部门】启用
        $(".main-wrapper").off('click', ".department--item-enable-submit").on('click', ".department--item-enable-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


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
                        "{{ url('/o1/department/item-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "department--item-enable",
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
                ,btn2: function(index)
                {
                    layer.close(index);
                    $row.removeClass('operating');
                }
            });

        });
        // 【部门】禁用
        $(".main-wrapper").off('click', ".department--item-disable-submit").on('click', ".department--item-disable-submit", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');


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
                        "{{ url('/o1/department/item-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "department--item-disable",
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
                ,btn2: function(index)
                {
                    layer.close(index);
                    $row.removeClass('operating');
                }
            });
        });




        // 【部门】操作记录
        $(".main-content").off('click', ".modal-show--for--department--item-operation-record").on('click', ".modal-show--for--department--item-operation-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            // $row.addClass('operating');

            Datatable__for__Department_Item_Operation_Record_List.init($id);

            var $modal = $('#modal--for--department-item-operation-record-list');
            $modal.find('.id-title').html('【'+$id+'】');
            $modal.modal('show');
        });
        

    });
</script>