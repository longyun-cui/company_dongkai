<script>
    $(function() {


        // 【员工】编辑-显示
        $(".main-wrapper").on('click', ".modal-show--for--my-account--edit", function() {
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
                "{{ url('/o1/staff/item-get') }}",
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
                        form_reset('#modal--for--staff-item-edit');

                        var $modal = $('#modal--for--staff-item-edit');

                        $modal.find('.box-title').html('编辑员工【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="login_number"]').val($response.data.login_number);
                        $modal.find('input[name="name"]').val($response.data.name);

                        $modal.find('input[name="staff_category"]').prop('checked', false);
                        $modal.find('input[name="staff_category"][value="'+$response.data.staff_category+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-staff-category').hide();
                        $modal.find('input[name="staff_category"][value="'+$response.data.staff_category+'"]').parents('.radio-staff-category').show();

                        $modal.find('input[name="staff_position"]').prop('checked', false);
                        $modal.find('input[name="staff_position"][value="'+$response.data.staff_position+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-staff-position').hide();
                        $modal.find('input[name="staff_position"][value="'+$response.data.staff_position+'"]').parents('.radio-staff-position').show();

                        // $modal.find('select[name="department_id"]').val($response.data.department_id);
                        // $modal.find('select[name="team_id"]').val($response.data.team_id);

                        // 公司
                        if($response.data.company_er)
                        {
                            $modal.find('select[name="company_id"]').append(new Option($response.data.company_er.name, $response.data.company_id, true, true)).trigger('change');
                        }
                        // 部门
                        if($response.data.department_er)
                        {
                            $modal.find('select[name="department_id"]').append(new Option($response.data.department_er.name, $response.data.department_id, true, true)).trigger('change');
                        }
                        // 团队
                        if($response.data.team_er)
                        {
                            $modal.find('select[name="team_id"]').append(new Option($response.data.team_er.name, $response.data.team_id, true, true)).trigger('change');
                        }
                        // 小组
                        if($response.data.team_group_er)
                        {
                            $modal.find('select[name="team_group_id"]').append(new Option($response.data.team_group_er.name, $response.data.team_group_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="api_staffNo"]').val($response.data.api_staffNo);

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
        // 【员工】编辑-提交
        $(".main-wrapper").on('click', "#submit--for--staff-item-edit", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--staff-item-edit';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--staff-item-edit';
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
                url: "{{ url('/o1/staff/item-save') }}",
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


        // 【账号】修改密码-显示
        $(".main-wrapper").on('click', ".modal-show--for--my-account--password-change", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);

            $modal.modal('show');
        });
        // 【员工】编辑-提交
        $(".main-wrapper").on('click', "#submit--for--my-account--password-change", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--my-account--password-change';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--my-account--password-change';
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
                url: "{{ url('/o1/my-account/password-change/save') }}",
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
                    layer.msg('服务器错误！');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('#'+$that.attr('id')+'.form.ajaxSubmit.complete.');
                    layer.closeAll('loading');
                }
            };
            $form.ajaxSubmit(options);
        });



    });
</script>