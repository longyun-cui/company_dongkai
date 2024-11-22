<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-finance-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【刷新】
        $("#datatable-for-finance-list").on('click', ".filter-refresh", function() {
            $('#datatable_ajax').DataTable().ajax.reload(null,false);
        });
        // 【重置】
        $("#datatable-for-finance-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-finance-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-finance-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-finance-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $("#datatable-for-finance-list").on('click', ".filter-empty", function() {
            $("#datatable-for-finance-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-finance-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-finance-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【查询】回车
        $("#datatable-for-finance-list").on('keyup', ".item-search-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-finance-list").find(".filter-submit").click();
            }
        });




        // 【编辑】
        $("#item-main-body").on('click', ".item-edit-link", function() {
            var $that = $(this);
            window.location.href = "/item/car-edit?id="+$that.attr('data-id');
        });





        // 【确认】
        $(".main-content").on('click', ".item-confirm-submit", function() {
            var $that = $(this);
            layer.msg('确定"确认"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在确认</span>',
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
                        "{{ url('/finance/finance-confirm') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-confirm",
                            item_id: $that.attr('data-id')
                        },
                        function(data){

                            layer.close(index);
                            layer.closeAll('loading');

                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                layer.msg("确认成功！");
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });

        // 【删除】
        $(".main-content").on('click', ".item-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在删除</span>',
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
                        "{{ url('/finance/finance-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){

                            layer.close(index);
                            layer.closeAll('loading');

                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                layer.msg("删除成功！");
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【获取详情】
        $(".main-content").on('click', ".item-detail-show", function() {
            var $that = $(this);
            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/item/task-get') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('input[name=id]').val($that.attr('data-id'));
            $('.item-user-id').html($that.attr('data-user-id'));
            $('.item-username').html($that.attr('data-username'));
            $('.item-title').html($data.title);
            $('.item-content').html($data.content);
            if($data.attachment_name)
            {
                var $attachment_html = $data.attachment_name+'&nbsp&nbsp&nbsp&nbsp'+'<a href="/all/download-item-attachment?item-id='+$data.id+'">下载</a>';
                $('.item-attachment').html($attachment_html);
            }
            $('#modal-body').modal('show');

        });

        // 【管理员-删除】
        $(".main-content").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/finance/finance-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-admin-delete",
                            item_id: $that.attr('data-id')
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

        // 【管理员-恢复】
        $(".main-content").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/finance/finance-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-admin-restore",
                            item_id: $that.attr('data-id')
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

        // 【管理员-永久删除】
        $(".main-content").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定"永久删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/finance/finance-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-admin-delete-permanently",
                            item_id: $that.attr('data-id')
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
            // layer.msg('确定"启用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/finance/finance-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-admin-enable",
                            item_id: $that.attr('data-id')
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
        });
        // 【禁用】
        $("#item-main-body").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            // layer.msg('确定"禁用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/finance/finance-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-admin-disable",
                            item_id: $that.attr('data-id')
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
        });








        // 【修改-文本-text-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-text-set", function() {
            var $that = $(this);
            $('.info-text-set-title').html($that.attr("data-id"));
            $('.info-text-set-column-name').html($that.attr("data-name"));
            $('input[name=info-text-set-finance-id]').val($that.attr("data-id"));
            $('input[name=info-text-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-text-set-operate-type]').val($that.attr('data-operate-type'));
            if($that.attr('data-text-type') == "textarea")
            {
                $('input[name=info-text-set-column-value]').val('').hide();
                $('textarea[name=info-textarea-set-column-value]').text($that.attr("data-value")).show();
            }
            else
            {
                $('textarea[name=info-textarea-set-column-value]').val('').hide();
                $('input[name=info-text-set-column-value]').val($that.attr("data-value")).show();
            }

            $('#item-submit-for-info-text-set').attr('data-text-type',$that.attr('data-text-type'));

            $('#modal-body-for-info-text-set').modal('show');
        });
        // 【修改-文本-text-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-text-set", function() {
            var that = $(this);
            $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
            $('input[name=info-text-set-column-value]').val('');
            $('textarea[name=info-textarea-set-column-value]').val('');
        });
        // 【修改-文本-text-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-text-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-text-set-column-key"]').val();
            if($that.attr('data-text-type') == "textarea")
            {
                var $column_value = $('textarea[name="info-textarea-set-column-value"]').val();
            }
            else
            {
                var $column_value = $('input[name="info-text-set-column-value"]').val();
            }

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

            $.post(
                "{{ url('/finance/finance-info-text-set') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: $('input[name="info-text-set-operate"]').val(),
                    item_id: $('input[name="info-text-set-finance-id"]').val(),
                    operate_type: $('input[name="info-text-set-operate-type"]').val(),
                    column_key: $column_key,
                    column_value: $column_value,
                },
                function(data){
                    // layer.close(index);
                    if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                    else
                    {
                        $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('input[name=info-text-set-column-value]').val('');
                        $('textarea[name=info-textarea-set-column-value]').text('');

                        $('#datatable_ajax').DataTable().ajax.reload(null, false);
                        // $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                    }
                },
                'json'
            );

        });




        // 【修改-时间-time-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-time-set", function() {
            var $that = $(this);
            $('.info-time-set-title').html($that.attr("data-id"));
            $('.info-time-set-column-name').html($that.attr("data-name"));
            $('input[name=info-time-set-operate-type]').val($that.attr('data-operate-type'));
            $('input[name=info-time-set-finance-id]').val($that.attr("data-id"));
            $('input[name=info-time-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-time-set-time-type]').val($that.attr('data-time-type'));
            if($that.attr('data-time-type') == "datetime")
            {
                $('input[name=info-time-set-column-value]').show();
                $('input[name=info-date-set-column-value]').hide();
                $('input[name=info-time-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }
            else if($that.attr('data-time-type') == "date")
            {
                $('input[name=info-time-set-column-value]').hide();
                $('input[name=info-date-set-column-value]').show();
                $('input[name=info-date-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }

            $('#modal-body-for-info-time-set').modal('show');
        });
        // 【修改-时间-time-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-time-set", function() {
            var that = $(this);
            $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-时间-time-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-time-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-time-set-column-key"]').val();
            var $time_type = $('input[name="info-time-set-time-type"]').val();
            var $column_value = '';
            if($time_type == "datetime")
            {
                $column_value = $('input[name="info-time-set-column-value"]').val();
            }
            else if($time_type == "date")
            {
                $column_value = $('input[name="info-date-set-column-value"]').val();
            }

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

            $.post(
                "{{ url('/finance/finance-info-time-set') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: $('input[name="info-time-set-operate"]').val(),
                    item_id: $('input[name="info-time-set-finance-id"]').val(),
                    operate_type: $('input[name="info-time-set-operate-type"]').val(),
                    column_key: $column_key,
                    column_value: $column_value,
                    time_type: $time_type
                },
                function(data){
                    // layer.close(index);
                    if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                    else
                    {
                        $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#datatable_ajax').DataTable().ajax.reload(null, false);
                        // $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                    }
                },
                'json'
            );

        });




        // 【修改记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });







        //
        $('.finance-select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
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
</script>