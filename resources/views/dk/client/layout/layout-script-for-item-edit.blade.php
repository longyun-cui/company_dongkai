<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【通用】编辑-现实-创建
        $(".main-content").on('click', ".item-create-show", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('.box-title').html($title);
            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);
            $modal.modal('show');
        });
        // 【通用】编辑-取消
        $(".main-content").on('click', ".edit-cancel", function() {
            var $that = $(this);
            var $modal_wrapper = $that.parents('.modal-wrapper');

            var $form_id = $modal_wrapper.find('from').filter('[id][id!=""]').attr("id");
            form_reset('#'+$form_id);

            $modal_wrapper.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });




        // 【部门管理】编辑-显示-编辑
        $(".main-content").on('click', ".department-edit-show", function() {
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
                "{{ url('/item/item-get-for-department') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "department",
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
                        var $modal = $('#modal-for-department-edit');
                        $modal.find('.box-title').html('编辑部门【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="name"]').val($response.data.name);

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
        // 【部门管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-department", function() {
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
                url: "{{ url('/item/item-edit-for-department') }}",
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
                        form_reset('#form-edit-for-department');

                        $('#modal-for-department-edit').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-department-edit").ajaxSubmit(options);
        });




        // 【员工管理】编辑-显示-编辑
        $(".main-content").on('click', ".staff-edit-show", function() {
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
                "{{ url('/item/item-get-for-staff') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "department",
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
                        var $modal = $('#modal-for-staff-edit');
                        $modal.find('.box-title').html('编辑员工【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="mobile"]').val($response.data.mobile);
                        $modal.find('input[name="username"]').val($response.data.username);
                        $modal.find('input[name="user_type"][value="'+$response.data.user_type+'"]').prop('checked', true);
                        $modal.find('select[name="department_id"]').val($response.data.department_id);

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
        // 【员工管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-staff", function() {
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
                url: "{{ url('/item/item-edit-for-staff') }}",
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
                        form_reset('#form-edit-for-staff');

                        $('#modal-for-staff-edit').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-staff-edit").ajaxSubmit(options);
        });


    });


    function form_reset($form_id)
    {
        var $form = $($form_id);
        $form.find('textarea.form-control, input.form-control, select').each(function () {
            $(this).val("");
            $(this).val($(this).data('default'));
        });
        $form.find(".select2-box").val(-1).trigger("change");
        $form.find(".select2-box").select2("val", "");

        // $form.find('select').selectpicker('refresh');

        $form.find('select option').attr("selected",false);
        $form.find('select').find('option:eq(0)').attr('selected', true);
    }


</script>
