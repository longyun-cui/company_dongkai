<script>
    $(function() {

        // 【搜索】
        $(".item-main-body").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $(".item-main-body").on('click', ".filter-cancel", function() {
            $('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//                $('select.form-filter').selectpicker('refresh');
            $('select.form-filter option').attr("selected",false);
            $('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【查询】回车
        $(".item-main-body").on('keyup', ".item-search-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#filter-submit").click();
            }
        });


        // 【下载二维码】
        $("#item-main-body").on('click', ".item-download-qr-code-submit", function() {
            var $that = $(this);
            window.open("/download/qr-code?type=user&id="+$that.attr('data-id'));
        });

        // 【数据分析】
        $("#item-main-body").on('click', ".item-statistic-link", function() {
            var $that = $(this);
            window.open("/statistic/statistic-user?user-id="+$that.attr('data-id'));
//            window.location.href = "/statistic/statistic-user?id="+$that.attr('data-id');
        });

        // 【编辑】
        $("#item-main-body").on('click', ".item-admin-edit-submit", function() {
            var $that = $(this);
            window.location.href = "/user/staff-edit?id="+$that.attr('data-id');
        });




        // 显示【修改密码】
        $("#item-main-body").on('click', ".item-password-admin-change-show", function() {
            var $that = $(this);
            $('input[name=user_id]').val($that.attr('data-id'));
            $('#modal-password-body .user-name').html($that.attr('data-name'));
            $('input[name=user-password]').val('');
            $('input[name=user-password-confirm]').val('');
            $('#modal-password-body').modal('show');
        });
        // 【修改密码】取消
        $("#modal-password-body").on('click', "#item-password-admin-change-cancel", function() {
            $('input[name=user_id]').val('');
            $('#modal-password-body .user-name').html('');
            $('input[name=user-password]').val('');
            $('input[name=user-password-confirm]').val('');
            $('#modal-password-body').modal('hide');
        });
        // 【修改密码】提交
        $("#modal-password-body").on('click', "#item-password-admin-change-submit", function() {
            var $that = $(this);
            layer.msg('确定"修改"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    var options = {
                        url: "{{ url('/user/staff-password-admin-change') }}",
                        type: "post",
                        dataType: "json",
                        // target: "#div2",
                        success: function (data) {
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg(data.msg);
                                $('#modal-password-body').modal('hide');
                            }
                        }
                    };
                    $("#form-password-admin-change-modal").ajaxSubmit(options);
                }
            });
        });
        // 【重置密码】提交
        $("#item-main-body").on('click', ".item-password-admin-reset-submit", function() {
            var $that = $(this);
            layer.msg('确定"重置"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-password-admin-reset') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-password-admin-reset",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg('重置成功！');
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【登录】
        $("#item-main-body").on('click', ".item-login-submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/admin/user/user-login') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    user_id: $that.attr('data-id')
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        console.log(data);
//                        window.open('/');
                        var temp_window=window.open();
                        if(data.data.env == 'test') temp_window.location = "{{ env('DOMAIN_TEST_DEFAULT') }}";
                        else temp_window.location = "{{ env('DOMAIN_DEFAULT') }}";

                    }
                },
                'json'
            );
        });




        // 【删除】
        $("#item-main-body").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-admin-delete",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【恢复】
        $("#item-main-body").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定要"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-admin-restore",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【永久删除】
        $("#item-main-body").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-admin-delete-permanently",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【启用】
        $("#item-main-body").on('click', ".item-admin-enable-submit", function() {
            var $that = $(this);
            layer.msg('确定"启用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-admin-enable",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【禁用】
        $("#item-main-body").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            layer.msg('确定"禁用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-admin-disable",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });

    });
</script>