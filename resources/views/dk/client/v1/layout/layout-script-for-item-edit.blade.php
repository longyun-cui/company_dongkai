<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【通用】编辑-显示-创建
        $(".main-content").on('click', ".item-create-modal-show", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            console.log('#'+$form_id);
            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('.box-title').html($title);
            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);
            $modal.find('.radio-btn').show();
            $modal.modal('show');

            $('#'+$modal_id).find('.modal-select2').select2({
                dropdownParent: $('#'+$modal_id).find('.modal-content'), // 替换为你的模态框 ID
                minimumInputLength: 0,
                width: '100%',
                theme: 'classic'
            });

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




        // 【部门-管理】编辑-显示-编辑
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
                "{{ url('/v1/operate/department/item-get') }}",
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

                        form_reset('#modal-for-department-edit');

                        var $modal = $('#modal-for-department-edit');
                        $modal.find('.box-title').html('编辑部门【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="name"]').val($response.data.name);
                        console.log($response.data.department_type);
                        $modal.find('input[name="department_type"]').prop('checked', false);
                        $modal.find('input[name="department_type"][value="'+$response.data.department_type+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-department-type').hide();
                        $modal.find('input[name="department_type"][value="'+$response.data.department_type+'"]').parents('.radio-department-type').show();

                        if($response.data.leader)
                        {
                            // $modal.find('#select2-leader option[value="'+$response.data.leader_id+'"]').prop('selected', true);
                            $modal.find('#select2-leader').append(new Option($response.data.leader.username, $response.data.leader_id, true, true)).trigger('change');
                        }

                        if($response.data.superior_department_er)
                        {
                            $modal.find('#select2-superior-department').append(new Option($response.data.superior_department_er.name, $response.data.superior_department_id, true, true)).trigger('change');
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
        // 【部门-管理】编辑-提交
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
                url: "{{ url('/v1/operate/department/item-save') }}",
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


        // 【员工-管理】编辑-显示-编辑
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
                "{{ url('/v1/operate/staff/item-get') }}",
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
                        form_reset('#modal-for-staff-edit');

                        var $modal = $('#modal-for-staff-edit');
                        $modal.find('.box-title').html('编辑员工【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="mobile"]').val($response.data.mobile);
                        $modal.find('input[name="username"]').val($response.data.username);
                        $modal.find('input[name="true_name"]').val($response.data.true_name);
                        $modal.find('input[name="api_staffNo"]').val($response.data.api_staffNo);

                        $modal.find('input[name="user_type"]').prop('checked', false);
                        $modal.find('input[name="user_type"][value="'+$response.data.user_type+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-user-type').hide();
                        $modal.find('input[name="user_type"][value="'+$response.data.user_type+'"]').parents('.radio-user-type').show();

                        $modal.find('select[name="department_district_id"]').val($response.data.department_district_id);
                        $modal.find('select[name="department_group_id"]').val($response.data.department_group_id);

                        if($response.data.department_district_er)
                        {
                            $modal.find('#select2-department-district').append(new Option($response.data.department_district_er.name, $response.data.department_district_id, true, true)).trigger('change');
                        }
                        if($response.data.department_group_er)
                        {
                            $modal.find('#select2-department-group').append(new Option($response.data.department_group_er.name, $response.data.department_group_id, true, true)).trigger('change');
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
        // 【员工-管理】编辑-提交
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
                url: "{{ url('/v1/operate/staff/item-save') }}",
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


        // 【公司-管理】编辑-显示-编辑
        $(".main-content").on('click', ".company-edit-show", function() {
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
                "{{ url('/v1/operate/company/item-get') }}",
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

                        form_reset('#modal-for-company-edit');

                        var $modal = $('#modal-for-company-edit');
                        $modal.find('.box-title').html('编辑渠道【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="mobile"]').val($response.data.mobile);
                        $modal.find('input[name="name"]').val($response.data.name);

                        $modal.find('input[name="company_category"]').prop('checked', false);
                        $modal.find('input[name="company_category"][value="'+$response.data.company_category+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-company-category').hide();
                        $modal.find('input[name="company_category"][value="'+$response.data.company_category+'"]').parents('.radio-company-category').show();


                        if($response.data.superior_company_er)
                        {
                            $modal.find('#select2-superior-company').append(new Option($response.data.superior_company_er.name, $response.data.superior_company_id, true, true)).trigger('change');
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
        // 【公司-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-company", function() {
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
                url: "{{ url('/v1/operate/company/item-save') }}",
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
                        form_reset('#form-edit-for-company');

                        $('#modal-for-company-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-company-edit").ajaxSubmit(options);
        });


        // 【客户-管理】编辑-显示-编辑
        $(".main-content").on('click', ".client-edit-show", function() {
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
                "{{ url('/v1/operate/client/item-get') }}",
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

                        form_reset('#modal-for-client-edit');

                        var $modal = $('#modal-for-client-edit');
                        $modal.find('.box-title').html('编辑客户【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="user_category"]').prop('checked', false);
                        $modal.find('input[name="user_category"][value="'+$response.data.user_category+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-user-category').hide();
                        $modal.find('input[name="user_category"][value="'+$response.data.user_category+'"]').parents('.radio-user-category').show();

                        $modal.find('input[name="username"]').val($response.data.username);
                        $modal.find('input[name="ip_whitelist"]').val($response.data.ip_whitelist);
                        $modal.find('input[name="client_admin_name"]').val($response.data.client_admin_name);
                        $modal.find('input[name="client_admin_mobile"]').val($response.data.client_admin_mobile);

                        $modal.find('input[name="is_ip"]').prop('checked', false);
                        $modal.find('input[name="is_ip"][value="'+$response.data.is_ip+'"]').prop('checked', true).trigger('change');


                        if($response.data.channel_er)
                        {
                            $modal.find('#select2-company-channel').append(new Option($response.data.channel_er.name, $response.data.channel_id, true, true)).trigger('change');
                        }
                        if($response.data.business_er)
                        {
                            $modal.find('#select2-company-business').append(new Option($response.data.business_er.name, $response.data.business_id, true, true)).trigger('change');
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
        // 【客户-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-client", function() {
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
                url: "{{ url('/v1/operate/client/item-save') }}",
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
                        form_reset('#form-edit-for-client');

                        $('#modal-for-client-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-client-edit").ajaxSubmit(options);
        });


        // 【项目-管理】编辑-显示-编辑
        $(".main-content").on('click', ".project-edit-show", function() {
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
                "{{ url('/v1/operate/project/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "project",
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
                        form_reset('#modal-for-project-edit');

                        var $modal = $('#modal-for-project-edit');
                        $modal.find('.box-title').html('编辑项目【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="item_category"]').prop('checked', false);
                        $modal.find('input[name="item_category"][value="'+$response.data.item_category+'"]').prop('checked', true).trigger('change');
                        $modal.find('.radio-item-category').hide();
                        $modal.find('input[name="item_category"][value="'+$response.data.item_category+'"]').parents('.radio-item-category').show();


                        $modal.find('input[name="name"]').val($response.data.name);
                        $modal.find('input[name="alias_name"]').val($response.data.alias_name);
                        $modal.find('input[name="location_city"]').val($response.data.location_city);


                        if($response.data.client_er)
                        {
                            $modal.find('#select2-client').append(new Option($response.data.client_er.username, $response.data.client_id, true, true)).trigger('change');
                        }

                        if($response.data.pivot_project_user)
                        {
                            $.each($response.data.pivot_project_user, function( key, val ) {
                                $modal.find('#select2-inspector').append(new Option(this.username, this.id, true, true)).trigger('change');
                            });
                        }
                        if($response.data.pivot_project_team)
                        {
                            $.each($response.data.pivot_project_team, function( key, val ) {
                                $modal.find('#select2-team').append(new Option(this.name, this.id, true, true)).trigger('change');
                            });
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
        // 【项目-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-project", function() {
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
                url: "{{ url('/v1/operate/project/item-save') }}",
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
                        form_reset('#form-edit-for-project');

                        $('#modal-for-project-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-project-edit").ajaxSubmit(options);
        });


        // 【地域-管理】编辑-显示-编辑
        $(".main-content").on('click', ".location-edit-show", function() {
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
                "{{ url('/v1/operate/location/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "location",
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

                        form_reset('#modal-for-location-edit');

                        var $modal = $('#modal-for-location-edit');
                        $modal.find('.box-title').html('编辑地域【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="name"]').val($response.data.name);
                        $modal.find('input[name="district_city"]').val($response.data.district_city);
                        $modal.find('input[name="district_district"]').val($response.data.district_district);


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
        // 【地域-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-location", function() {
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
                url: "{{ url('/v1/operate/location/item-save') }}",
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
                        form_reset('#form-edit-for-location');

                        $('#modal-for-location-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-location-edit").ajaxSubmit(options);
        });


        // 【工单-管理】编辑-显示-编辑
        $(".main-content").on('click', ".order-edit-show", function() {
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
                "{{ url('/v1/operate/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
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

                        form_reset('#modal-for-order-edit');

                        var $modal = $('#modal-for-order-edit');
                        $modal.find('.box-title').html('编辑地域【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="teeth_count"]').val($response.data.teeth_count).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('#select-district-1').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('#order-edit-select2-project').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);
                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);

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
        // 【工单-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-order", function() {
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
                url: "{{ url('/v1/operate/order/item-save') }}",
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
                        form_reset('#form-for-order-edit');

                        $('#modal-for-order-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-order-edit").ajaxSubmit(options);
        });


        // 【工单-管理】编辑-显示-编辑
        $(".main-content").on('click', ".order-aesthetic-edit-show", function() {
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
                "{{ url('/v1/operate/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
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

                        form_reset('#modal-for-order-aesthetic-edit');

                        var $modal = $('#modal-for-order-aesthetic-edit');
                        $modal.find('.box-title').html('编辑医美工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        // $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        // $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('#select-district-1').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('#order-edit-select2-project').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);
                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);

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
        // 【工单-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-order-aesthetic", function() {
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
                url: "{{ url('/v1/operate/order/aesthetic/item-save') }}",
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
                        form_reset('#form-for-order-aesthetic-edit');

                        $('#modal-for-order-aesthetic-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-order-aesthetic-edit").ajaxSubmit(options);
        });


        // 【工单-管理】编辑-显示-编辑
        $(".main-content").on('click', ".order-luxury-edit-show", function() {
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
                "{{ url('/v1/operate/order/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "order",
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

                        form_reset('#modal-for-order-luxury-edit');

                        var $modal = $('#modal-for-order-luxury-edit');
                        $modal.find('.box-title').html('编辑二奢工单【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="client_name"]').val($response.data.client_name);
                        $modal.find('input[name="client_phone"]').val($response.data.client_phone);

                        // $modal.find('select[name="client_type"]').val($response.data.client_type).trigger('change');
                        // $modal.find('select[name="client_intention"]').val($response.data.client_intention).trigger('change');
                        $modal.find('select[name="field_1"]').val($response.data.field_1).trigger('change');

                        $modal.find('select[name="location_city"]').val($response.data.location_city).trigger('change');
                        $modal.find('#select-district-1').append(new Option($response.data.location_district, $response.data.location_district, true, true)).trigger('change');

                        if($response.data.project_er)
                        {
                            $modal.find('#order-edit-select2-project').append(new Option($response.data.project_er.name, $response.data.project_id, true, true)).trigger('change');
                        }

                        $modal.find('input[name="is_wx"]').prop('checked', false);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="field_2"]').prop('checked', false);
                        $modal.find('input[name="field_2"][value="'+$response.data.field_2+'"]').prop('checked', true).trigger('change');

                        $modal.find('input[name="wx_id"]').val($response.data.wx_id);
                        $modal.find('input[name="recording_address"]').val($response.data.recording_address);
                        $modal.find('textarea[name="description"]').val($response.data.description);

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
        // 【工单-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-order-luxury", function() {
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
                url: "{{ url('/v1/operate/order/luxury/item-save') }}",
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
                        form_reset('#form-for-order-luxury-edit');

                        $('#modal-for-order-luxury-edit').modal('hide').on("hidden.bs.modal", function () {
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
            $("#form-for-order-luxury-edit").ajaxSubmit(options);
        });






        // 【通用】【字段-编辑】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-field-set", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $modal = $('#modal-for-field-set');
            $modal.attr('data-datatable-id',$table_id);
            $modal.attr('data-datatable-row-index',$that.data('row-index'));

            var $form = $('#form-for-field-set');
            form_reset('#form-for-field-set');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');
            $datatable_wrapper.find('td').removeClass('operating');
            $that.addClass('operating');


            $('.field-set-item-name').html($datatable_wrapper.attr("data-item-name"));
            $('.field-set-item-id').html($that.attr("data-id"));
            $('.field-set-column-name').html($that.attr("data-column-name"));

            $('input[name="operate-type"]').val($that.attr('data-operate-type'));

            $('input[name="item-category"]').val($datatable_wrapper.data("datatable-item-category"));
            $('input[name="item-id"]').val($that.attr("data-id"));

            $('input[name="column-key"]').val($that.attr("data-key"));
            $('input[name="column-key2"]').val($that.attr("data-key2"));

            $modal.find('.column-value').val('').hide();
            // $modal.find('.select-assistant').val('').hide();
            if($modal.find('select[name="field-set-select-value"]').data('select2'))
            {
                $modal.find('select[name="field-set-select-value"]').select2('destroy');
            }
            if($modal.find('select[name="field-set-select-value2"]').data('select2'))
            {
                $modal.find('select[name="field-set-select-value2"]').select2('destroy');
            }
            $form.find('.select2-container').remove();
            $form.find('.select2-dropdown').remove();
            $form.find('.select2-results__options').remove();
            $form.find('.radio-wrapper').html('');

            $modal.find(".select2-city").off('change');
            $('select[name=field-set-select-value2]').html('').hide();
            $('select[name=field-set-select-value]').removeClass('select2-city');

            var $column_type = $that.attr('data-column-type');
            $('input[name="column-type"]').val($column_type);
            if($column_type == "text")
            {
                $modal.find('input[name="field-set-text-value"]').val($that.attr("data-value")).show();
            }
            else if($column_type == "textarea")
            {
                $modal.find('textarea[name="field-set-textarea-value"]').val($that.attr("data-value")).show();
            }
            else if($column_type == "radio")
            {
                if($that.attr("data-key") == "is_distributive_condition")
                {
                    var $option_html = $('#option-list-for-is_distributive_condition').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('input[name=option_is_distributive_condition][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
                else if($that.attr("data-key") == "is_wx")
                {
                    var $option_html = $('#option-list-for-is-wx').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('.radio-wrapper').find('input[name="field-set-radio-value"][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
                else if($that.attr("data-key") == "is_distributive")
                {
                    var $option_html = $('#option-list-for-is-distributive').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('.radio-wrapper').find('input[name="field-set-radio-value"][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
                else if($that.attr("data-key") == "field_2")
                {
                    var $option_html = $('#option-list-for-field_2').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('.radio-wrapper').find('input[name="field-set-radio-value"][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
            }
            else if($column_type == "select")
            {
                // console.log("select");

                if($that.attr("data-key") == "location_city")
                {
                    $('select[name=info-select-set-column-value]').removeClass('select2-city');
                    $('select[name=info-select-set-column-value2]').removeClass('select2-district');
                    var $option_html = $('#location-city-option-list').html();

                    $('#modal-body-for-info-select-set').find('select[name=info-select-set-column-value2]').show();
                }
                else if($that.attr("data-key") == "teeth_count")
                {
                    var $option_html = $('#option-list-for-teeth-count').html();
                }
                else if($that.attr("data-key") == "channel_source")
                {
                    var $option_html = $('#option-list-for-channel-source').html();
                }
                else if($that.attr("data-key") == "inspected_result")
                {
                    var $option_html = $('#option-list-for-inspected-result').html();
                }
                else if($that.attr("data-key") == "client_id")
                {
                    var $option_html = $('#option-list-for-client').html();
                }
                else if($that.attr("data-key") == "client_intention")
                {
                    var $option_html = $('#option-list-for-client-intention').html();
                }
                else if($that.attr("data-key") == "client_type")
                {
                    var $option_html = $('#option-list-for-client-type').html();
                }
                else if($that.attr("data-key") == "is_distributive_condition")
                {
                    var $option_html = $('#option-list-for-is_distributive_condition').html();
                }
                $('select[name=field-set-select-value]').html($option_html).show();
                $('select[name=field-set-select-value]').find("option[value='"+$that.attr("data-value")+"']").prop("selected",true);

            }
            else if($column_type == "select2")
            {
                // console.log("select2");
                // console.log($that.attr("data-key"));

                var $select_value2 = $modal.find('select[name="field-set-select-value2"]');
                // console.log($select_value2);
                // $select_value2.hide();

                if ($select_value2.data('select2'))
                {
                    $select_value2.select2('destroy'); // 销毁旧实例
                }
                else
                {
                }
                console.log($that.attr('data-option-name'));

                if($that.attr("data-key") == "location_city")
                {


                    // console.log("location_city11");
                    // var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    // var $option_html = $('#location-city-option-list').html();
                    // $select2_dom.html($option_html);
                    //
                    // var $select2_dom2 = $modal.find('select[name="field-set-select-value2"]');
                    // $select2_dom2.show();
                    // var $existed_class = $select2_dom.data('class');
                    // $select2_dom.attr('data-class','select2-city');
                    // $select2_dom.removeClass($existed_class).addClass('select2-');
                    // select2_location_district_init($select2_dom2);
                    // $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');



                    $('select[name="field-set-select-value2"]').show();

                    var $option_html = $('#location-city-option-list').html();
                    $('select[name="field-set-select-value"]').html($option_html);
                    $('select[name="field-set-select-value"]').find("option[value='"+$that.attr("data-value")+"']").prop("selected",true);

                    $('select[name="field-set-select-value"]').removeClass('select2-project').addClass('select2-city');
                    $('select[name="field-set-select-value2"]').addClass('select2-district');

                    $('select[name="field-set-select-value2"]').show();

                    // var $city_index = $(".select2-city").find('option:selected').attr('data-index');
                    // $(".select2-district").html('<option value="">选择区划</option>');
                    // $.each($district_list[$city_index], function($i,$val) {
                    //     $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                    // });
                    // $('.select2-district').find("option[value='"+$that.attr("data-value2")+"']").attr("selected","selected");

                    $('.select2-city').select2();
                    $('.select2-district').select2();
                    $('.select2-district').val($that.attr("data-value2")).trigger('change');


                    var $city_value = $that.attr("data-value");
                    // console.log($that.attr("data-value"));
                    $('.select2-district').select2({
                        ajax: {
                            url: "/district/district_select2_district?district_city=" + $city_value,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
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
                        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                        minimumInputLength: 0,
                        theme: 'classic'
                    });
                    $('.select2-district').find("option[value='"+$that.attr("data-value2")+"']").prop("selected",true);
                    $('.select2-district').append(new Option($that.attr("data-value2"), $that.attr("data-value2"), true, true)).trigger('change');


                    $(".select2-city").change(function() {

                        $that = $(this);

                        var $city_index = $that.find('option:selected').attr('data-index');

                        $(".select2-district").html('<option value="">选择区划</option>');

                        // $.each($district_list[$city_index], function($i,$val) {
                        //
                        //     $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                        // });
                        //
                        // $('.select2-district').select2();


                        var $city_value = $(this).val();
                        $('.select2-district').select2({
                            ajax: {
                                url: "/district/district_select2_district?district_city=" + $city_value,
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return {
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
                            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                            minimumInputLength: 0,
                            theme: 'classic'
                        });
                    });




                }
                else if($that.attr("data-key") == "client_id")
                {
                    var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    var $existed_class = $select2_dom.data('class');
                    $select2_dom.attr('data-class','select2-client');
                    $select2_dom.removeClass($existed_class).addClass('select2-client');
                    select2_client_init($select2_dom);
                    $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');
                    $('select[name=field-set-select-value2]').html('').hide();
                }
                else if($that.attr("data-key") == "project_id")
                {
                    var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    var $existed_class = $select2_dom.data('class');
                    $select2_dom.attr('data-class','select2-project');
                    $select2_dom.removeClass($existed_class).addClass('select2-project');
                    select2_project_init($select2_dom);
                    $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');

                    if ($('select[name=field-set-select-value2]').data('select2'))
                    {
                        // $select_value2.select2('destroy'); // 销毁旧实例
                        $('select[name=field-set-select-value2]').select2('destroy');
                    }

                }
            }


            $modal.modal('show');
        });
        // 【通用】【字段-编辑】【取消】
        $(".main-content").on('click', "#edit-cancel-for-field-set", function() {
            var that = $(this);
            $('#modal-for-field-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });

            form_reset('#modal-for-field-set');
        });
        // 【通用】【字段-编辑】【提交】
        $(".main-content").on('click', "#edit-submit-for-field-set", function() {
            var $that = $(this);
            var $modal = $('#modal-for-field-set');
            var $table_id = $modal.data('datatable-id');

            var $row = $('.datatable-wrapper.operating').find('tr.operating');
            var $td = $('.datatable-wrapper.operating').find('td.operating');

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
            var options = {
                url: "{{ url('/v1/operate/universal/field-set') }}",
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

                        console.log($response.data);
                        // $('#'+$table_id).DataTable().ajax.reload(null,false);

                        var $form = $('#form-for-field-set');
                        var item_category = $form.find('input[name="item-category"]').val();
                        var column_key = $form.find('input[name="column-key"]').val();
                        console.log(column_key);
                        if(column_key == 'location_city')
                        {
                            $td.data('value2',$response.data.data.value2);
                        }
                        else if(column_key == 'is_wx')
                        {
                            var $radio_value = $form.find('input[name="field-set-radio-value"]').val();
                            console.log($radio_value);
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('--');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-primary">是</small>');
                            }
                            else
                            {

                            }
                        }


                        // var $rowIndex = $modal.data('datatable-row-index');
                        // $('#'+$table_id).DataTable().row($rowIndex).data($response.data.data).invalidate().draw(false);

                        $td.attr('data-value',$response.data.data.value);
                        $td.attr('data-option-name',$response.data.data.text);
                        $td.html($response.data.data.text);

                        if(column_key == 'is_wx')
                        {
                            var $radio_value = $response.data.data.value;
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('--');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-primary">是</small>');
                            }
                            else
                            {
                            }
                        }

                        if(column_key == 'is_distributive_condition')
                        {
                            var $radio_value = $response.data.data.value;
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="is_distributive_condition"]').attr('data-value',$radio_value).html('--');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="is_distributive_condition"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-success">允许</small>');
                            }
                            else if($radio_value == 9)
                            {
                                $row.find('[data-key="is_distributive_condition"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-danger">禁止</small>');
                            }
                            else
                            {
                            }
                        }

                        if(column_key == 'is_distributive')
                        {
                            var $radio_value = $response.data.data.value;
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="is_distributive"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-danger">否</small>');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="is_distributive"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-success">是</small>');
                            }
                            else
                            {
                            }
                        }

                        if(column_key == 'field_2')
                        {
                            var $radio_value = $response.data.data.value;
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="field_2"]').attr('data-value',$radio_value).html('--');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="field_2"]').attr('data-value',$radio_value).html('<small class="btn-xs bg-green">白班</small>');
                            }
                            else if($radio_value == 9)
                            {
                                $row.find('[data-key="field_2"]').attr('data-value',$radio_value).html('<small class="btn-xs bg-navy">夜班</small>');
                            }
                            else
                            {
                            }
                        }

                        if(item_category == 'order')
                        {
                            var $item = $response.data.data.item;
                            $row.find('[data-key="is_repeat"]').html('');
                            if($item.is_repeat == 0)
                            {
                                $row.find('[data-key="is_repeat"]').attr('data-value',$item.is_repeat).html('');
                            }
                            else
                            {
                                console.log($item.is_repeat);
                                var $is_repeat_html = '<small class="btn-xs btn-primary">是</small><small class="btn-xs btn-danger">'+($item.is_repeat+1)+'</small>';
                                $row.find('[data-key="is_repeat"]').attr('data-value',$item.is_repeat).html($is_repeat_html);
                            }

                        }

                        // 重置输入框
                        form_reset('#form-for-field-set');

                        $modal.modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });
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
            $("#form-for-field-set").ajaxSubmit(options);

        });




        // 【通用】【字段-编辑】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-phone-pool-info", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $modal = $('#modal-for-field-info');


            var $that = $(this);
            var $row = $that.parents('tr');

            var $phone = $that.data('phone');
            var $city = $that.data('city');

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
                "{{ url('/v1/operate/order/item-get-phone-pool-info') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    phone: $phone,
                    city: $city
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('done');
                    $response = JSON.parse($response);
                    console.$response;
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        var $modal = $('#modal-for-phone-pool-info');
                        $modal.find('.box-title').html('电话【'+$phone+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));


                        $modal.find('.item-detail-quality .item-detail-text').html($response.data.quality);
                        $modal.find('.item-detail-order_cnt .item-detail-text').html($response.data.order_cnt);
                        $modal.find('.item-detail-order_date .item-detail-text').html($response.data.order_date);
                        $modal.find('.item-detail-call_cnt .item-detail-text').html($response.data.call_cnt);
                        $modal.find('.item-detail-call_cnt_1_8 .item-detail-text').html($response.data.call_cnt_1_8);
                        $modal.find('.item-detail-call_cnt_9_above .item-detail-text').html($response.data.call_cnt_9_above);
                        $modal.find('.item-detail-last_extraction_date .item-detail-text').html($response.data.last_extraction_date);

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
        // 【通用】【字段-编辑】【取消】
        $(".main-content").on('click', "#modal-cancel-for-field-info", function() {
            var that = $(this);
            $('#modal-for-field-info').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });


    });


    //
    function form_reset_($form_id)
    {
        console.log('form_reset');
        var $form = $($form_id);
        // $form.find('.select2-container').remove();
        $form.find('textarea.form-control, input.form-control, select').each(function () {
            $(this).val("");
            $(this).val($(this).data('default'));
        });


        // $form.find(".select2-box").val(-1).trigger("change");
        // $form.find(".select2-box").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box");
        $.each( $form.find(".select2-box"), function(index, element) {
            select2FirstOptionSelected(element);
        });


        // $form.find(".select2-box-c").val(-1).trigger("change");
        // $form.find(".select2-box-c").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box-c");
        $.each( $form.find(".select2-box-c"), function(index, element) {
            select2FirstOptionSelected(element);
        });

        $.each( $form.find(".select2-reset"), function(index, element) {
            select2FirstOptionSelected(element);
        });

        $form.find(".select2-multi-box-c").val([]).trigger('change');
        $form.find(".select2-multi-box-c").val(null).trigger('change');
        $form.find(".select2-multi-box-c").empty().trigger('change');

        $form.find('select option').prop("selected",false);
        $form.find('select').find('option:eq(0)').prop('selected', true);
    }


    //
    function selectFirstOption_(selector)
    {
        var $select = $(selector);
        var firstVal = $select.find('option:first').val();
        if(firstVal)
        {
            console.log('selectFirstOption is');
            $select.val(firstVal).trigger('change');
        }
        else
        {
            console.log('selectFirstOption not');
            // $select.val([]).trigger('change');
            $select.val(null).trigger('change');
        }
    }

    //
    function select2FirstOptionSelected_(dom)
    {
        var $dom = $(dom);
        var firstVal = $dom.find('option:first').val();
        if(firstVal)
        {
            $dom.val(firstVal).trigger('change');
        }
        else
        {
            $dom.val(null).trigger('change');
        }
    }

    function select2_client_init_(selector,$default)
    {
        var $element = $(selector);
        if ($element.data('select2'))
        {
            $element.select2('destroy'); // 销毁旧实例
        }

        // 重新初始化
        $element.select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2_client') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        if($default) $element.val($default).trigger('change');
    }

    function select2_project_init_(selector,$default)
    {
        var $element = $(selector);
        if ($element.data('select2'))
        {
            $element.select2('destroy'); // 销毁旧实例
        }

        // 重新初始化
        $element.select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2_project') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        if($default) $element.val($default).trigger('change');
    }

    function select2_location_district_init_(selector,$default)
    {
        var $element = $(selector);
        if ($element.data('select2'))
        {
            $element.select2('destroy'); // 销毁旧实例
        }

        var $city_dom = $($element.data('city-target'));
        var $city_value = '';
        // var $city_value = $city_dom.val();

        // 重新初始化
        $element.select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2_location' ) }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        keyword: params.term, // search term
                        page: params.page,
                        type: 'district',
                        location_city: $city_value
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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        if($default) $element.val($default).trigger('change');
    }


</script>
