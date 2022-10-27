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
        $("#item-main-body").on('click', ".item-statistic-submit", function() {
            var $that = $(this);
            window.open("/statistic/statistic-user?id="+$that.attr('data-id'));
//            window.location.href = "/admin/statistic/statistic-user?id="+$that.attr('data-id');
        });

        // 【编辑】
        $("#item-main-body").on('click', ".item-edit-submit", function() {
            var $that = $(this);
            window.location.href = "/user/user-edit?id="+$that.attr('data-id');
        });




        // 显示【修改密码】
        $("#item-main-body").on('click', ".item-change-password-show", function() {
            var $that = $(this);
            $('input[name=id]').val($that.attr('data-id'));
            $('input[name=user-password]').val('');
            $('input[name=user-password-confirm]').val('');
            $('#modal-password-body').modal('show');
        });
        // 【修改密码】取消
        $("#modal-password-body").on('click', "#item-change-password-cancel", function() {
            $('input[name=id]').val('');
            $('input[name=user-password]').val('');
            $('input[name=user-password-confirm]').val('');
            $('#modal-password-body').modal('hide');
        });
        // 【修改密码】提交
        $("#modal-password-body").on('click', "#item-change-password-submit", function() {
            var $that = $(this);
            layer.msg('确定"修改"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    var options = {
                        url: "{{ url('/user/change-password') }}",
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
                    $("#form-change-password-modal").ajaxSubmit(options);
                }
            });
        });






        // 【登录】
        $("#item-main-body").on('click', ".item-login-submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/user/user-login') }}",
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
                        temp_window.location = data.data.url;
                    }
                },
                'json'
            );
        });
        // 【管理员登录】
        $("#item-main-body").on('click', ".item-admin-login-submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/user/user-admin-login') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    user_id: $that.attr('data-id'),
                    admin_id: $that.attr('data-id'),
                    type:'admin'
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        console.log(data);
                        var temp_window=window.open();
                        temp_window.location = "{{ env('DOMAIN_ZY_ADMIN') }}/";
                    }
                },
                'json'
            );
        });
        // 【员工登录】
        $("#item-main-body").on('click', ".item-staff-login-submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/user/user-staff-login') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    user_id: $that.attr('data-id'),
                    admin_id: $that.attr('data-id'),
                    type:'staff'
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        console.log(data);
                        var temp_window=window.open();
                        temp_window.location = "{{ env('DOMAIN_ZY_STAFF') }}/home";
                    }
                },
                'json'
            );
        });




        // 【删除】
        $("#item-main-body").on('click', ".item-super-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-super-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-super-enable",
                            user_id: $that.attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg("操作完成");
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
//                                location.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });

        // 【启用】
        $("#item-main-body").on('click', ".user-super-enable-submit", function() {
            var $that = $(this);
            layer.msg('确定"封禁"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-super-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-super-enable",
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
        $("#item-main-body").on('click', ".user-super-disable-submit", function() {
            var $that = $(this);
            layer.msg('确定"解封"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/staff-super-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "staff-super-disable",
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