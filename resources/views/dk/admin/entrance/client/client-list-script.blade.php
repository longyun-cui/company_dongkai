<script>
    $(function() {

        // 【搜索】
        $(".main-content").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $(".main-content").on('click', ".filter-cancel", function() {
            $('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//                $('select.form-filter').selectpicker('refresh');
            $('select.form-filter option').attr("selected",false);
            $('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【清空重选】
        $(".main-content").on('click', ".filter-empty", function(e) {

            e.stopPropagation(); // 阻止事件冒泡

            $("#datatable-search-for-client-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $(".select2-box").val(-1).trigger("change");
            $(".select2-box").select2("val", "");

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-search-for-client-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-search-for-client-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【查询】回车
        $(".main-content").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $('#datatable_ajax').DataTable().ajax.reload();
            }
        });




        // 【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });


        // 【登录】
        $(".main-content").on('click', ".item-admin-login-submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/user/client-login') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: 'client-login',
                    client_id: $that.attr('data-id')
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        console.log(data);
                        var temp_window=window.open();
                        temp_window.location = "{{ env('DOMAIN_DK_CLIENT') }}/";
                    }
                },
                'json'
            );
        });


        // 【下载二维码】
        $(".main-content").on('click', ".item-download-qr-code-submit", function() {
            var $that = $(this);
            window.open("/download/qr-code?type=user&id="+$that.attr('data-id'));
        });

        // 【数据分析】
        $(".main-content").on('click', ".item-statistic-link", function() {
            var $that = $(this);
            window.open("/statistic/statistic-user?user-id="+$that.attr('data-id'));
//            window.location.href = "/statistic/statistic-user?id="+$that.attr('data-id');
        });

        // 【编辑】
        $(".main-content").on('click', ".item-admin-edit-submit", function() {
            var $that = $(this);
            window.location.href = "/user/client-edit?id="+$that.attr('data-id');
        });




        // 【重置密码】提交
        $(".main-content").on('click', ".item-password-admin-reset-submit", function() {
            var $that = $(this);
            layer.msg('确定"重置"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-password-admin-reset') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-password-admin-reset",
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




        // 【删除】
        $(".main-content").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-admin-delete",
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
        $(".main-content").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定要"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-admin-restore",
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
        $(".main-content").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-admin-delete-permanently",
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
        $(".main-content").on('click', ".item-admin-enable-submit", function() {
            var $that = $(this);
            layer.msg('确定"启用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-admin-enable",
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
        $(".main-content").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            layer.msg('确定"禁用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-admin-disable",
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




        // 【修改-文本-属性】显示
        $(".main-content").on('dblclick', ".modal-show-for-info-text-set", function() {
            var $that = $(this);
            $('.info-text-set-title').html($that.attr("data-name"));
            $('.info-text-set-column-name').html($that.attr("data-column-name"));
            $('input[name=info-text-set-item-id]').val($that.attr("data-id"));
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
        // 【修改-文本-属性】取消
        $(".main-content").on('click', "#item-cancel-for-info-text-set", function() {
            var that = $(this);
            $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
            $('input[name=info-text-set-column-value]').val('');
            $('textarea[name=info-textarea-set-column-value]').val('');
        });
        // 【修改-文本-属性】提交
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
                "{{ url('/user/client-info-text-set') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: $('input[name="info-text-set-operate"]').val(),
                    item_id: $('input[name="info-text-set-item-id"]').val(),
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

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                        $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                    }
                },
                'json'
            );

        });




        // 【修改-时间-属性】显示
        $(".main-content").on('dblclick', ".modal-show-for-info-time-set", function() {
            var $that = $(this);
            $('.info-time-set-title').html($that.attr("data-name"));
            $('.info-time-set-column-name').html($that.attr("data-column-name"));
            $('input[name=info-time-set-operate-type]').val($that.attr('data-operate-type'));
            $('input[name=info-time-set-item-id]').val($that.attr("data-id"));
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
        // 【修改-时间-属性】取消
        $(".main-content").on('click', "#item-cancel-for-info-time-set", function() {
            var that = $(this);

            $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-时间-属性】提交
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
                "{{ url('/user/client-info-text-set') }}",
                        {{--"{{ url('/user/client-info-time-set') }}",--}}
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: $('input[name="info-time-set-operate"]').val(),
                    item_id: $('input[name="info-time-set-item-id"]').val(),
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

                    }
                },
                'json'
            );

        });




        // 【修改-select-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select-set", function() {

            $('select[name=info-select-set-column-value]').attr("selected","");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-select-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-select-set-title').html($that.attr("data-id"));
            $('.info-select-set-column-name').html($that.attr("data-name"));
            $('input[name=info-select-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
//            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));


            $('select[name=info-select-set-column-value]').removeClass('select2-department').removeClass('select2-client');
            if($that.attr("data-key") == "receipt_status")
            {
                var $option_html = $('#receipt_status-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_type")
            {
                var $option_html = $('#trailer_type-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_length")
            {
                var $option_html = $('#trailer_length-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_volume")
            {
                var $option_html = $('#trailer_volume-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_weight")
            {
                var $option_html = $('#trailer_weight-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_axis_count")
            {
                var $option_html = $('#trailer_axis_count-option-list').html();
            }
            $('select[name=info-select-set-column-value]').html($option_html);
            $('select[name=info-select-set-column-value]').find("option[value='"+$that.attr("data-value")+"']").attr("selected","selected");


            $('#modal-body-for-info-select-set').modal('show');



        });
        // 【修改-select2-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select2-set", function() {

            $('select[name=info-select-set-column-value]').attr("selected","");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-select-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-select-set-title').html($that.attr("data-id"));
            $('.info-select-set-column-name').html($that.attr("data-name"));
            $('input[name=info-select-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-select-set-column-key]').prop('data-client-type',$that.attr("data-client-type"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-body-for-info-select-set').modal('show');


            if($that.attr("data-key") == "leader_id")
            {
                $('select[name=info-select-set-column-value]').addClass('select2-leader');
                $('.select2-leader').select2({
                    ajax: {
                        url: "{{ url('/user/client_select2_leader') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page,
                                type: $('input[name=info-select-set-column-key]').prop('data-client-type')
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
            }

        });
        // 【修改-select-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-select-set", function() {
            var that = $(this);
            $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-select-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-select-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-select-set-column-key"]').val();
            var $column_value = $('select[name="info-select-set-column-value"]').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

            $.post(
                "{{ url('/user/client-info-select-set') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: $('input[name="info-select-set-operate"]').val(),
                    item_id: $('input[name="info-select-set-item-id"]').val(),
                    operate_type: $('input[name="info-select-set-operate-type"]').val(),
                    column_key: $column_key,
                    column_value: $column_value,
                },
                function(data){
                    // layer.close(index);
                    if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                    else
                    {
                        $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                        $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                    }
                },
                'json'
            );

        });




        // 【附件-attachment-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-attachment", function() {
            var $that = $(this);

            $('.attachment-set-title').html($that.attr("data-id"));
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=attachment-set-item-id]').val($that.attr("data-id"));
            $('input[name=attachment-set-column-key]').val($that.attr("data-key"));
            $('input[name=attachment-set-column-value]').val($that.attr("data-value"));
            $('input[name=attachment-set-operate-type]').val($that.attr('data-operate-type'));


            $('#modal-attachment-set-form input[name=item_id]').val($that.attr("data-id"));
            $('#modal-attachment-set-form input[name=column-key]').val($that.attr("data-key"));
            $('#modal-attachment-set-form input[name=column-value]').val($that.attr("data-value"));
            $('#modal-attachment-set-form input[name=-operate-type]').val($that.attr('data-operate-type'));


            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/user/client-get-attachment-html') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    item_id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('.attachment-box').html($data.html);

            $('#modal-body-for-attachment').modal('show');
        });
        // 【附件-attachment-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-attachment-set", function() {
            var that = $(this);
            $('#modal-body-for-attachment').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【附件-attachment-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-attachment-set", function() {
            var $that = $(this);

            var $column_key = $('input[name="info-attachment-set-column-key"]').val();
            var $column_value = $('select[name="info-attachment-set-column-value"]').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

            var index1 = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在上传</span>',
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
                url: "{{ url('/user/client-info-attachment-set') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {

                    // layer.close(index);
                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
//                                $('#modal-body-for-attachment').modal('hide').on("hidden.bs.modal", function () {
//                                    $("body").addClass("modal-open");
//                                });

                        $('.fileinput-exists[data-dismiss="fileinput"]').click();
                        $('#modal-attachment-set-form input[name=attachment_name]').val('');

                        var $data = new Object();
                        $.ajax({
                            type:"post",
                            dataType:'json',
                            async:false,
                            url: "{{ url('/user/client-get-attachment-html') }}",
                            data: {
                                _token: $('meta[name="_token"]').attr('content'),
                                operate:"item-get",
                                item_id: $('#modal-attachment-set-form input[name=item_id]').val()
                            },
                            success:function(data){
                                if(!data.success) layer.msg(data.msg);
                                else
                                {
                                    $data = data.data;
                                }
                            }
                        });
                        $('.attachment-box').html($data.html);
                        $(".fileinput-remove-button").click();

//                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (res) {
                    layer.closeAll('loading');
                    layer.msg("上传失败");
                },
                complete: function () {
                }
            };
            $("#modal-attachment-set-form").ajaxSubmit(options);

        });

        // 【附件-attachment-属性】【删除】
        $(".main-content").on('click', ".item-attachment-delete-this", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/client-info-attachment-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "client-attachment-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $that.parents('.attachment-option').remove();
//                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });








        // 【财务记录】添加-显示
        $(".main-content").on('click', ".item-modal-show-for-recharge", function() {
            var $that = $(this);
            var $id = $that.attr("data-id");
            var $row = $that.parents('tr');
            var $name = $row.find('.client-name').html();


            $('input[name="finance-create-client-id"]').val($id);
            $('.finance-create-client-name').html($name);

            $('#modal-body-for-finance-create').modal('show');

            // $('#modal-body-for-finance-create').modal({show: true,backdrop: 'static'});
            // $('.modal-backdrop').each(function() {
            //     $(this).attr('id', 'id_' + Math.random());
            // });

        });
        // 【财务记录】添加-取消
        $(".main-content").on('click', "#item-cancel-for-finance-create", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.detect-set-keyword').html('');
            $('.detect-set-id').html(0);
            $('.detect-set-date').html('');
            $('.detect-set-original-rank').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-body-for-finance-create').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【财务记录】添加-提交
        $(".main-content").on('click', "#item-submit-for-finance-create", function() {
            var that = $(this);
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

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

                    $.post(
                        "{{ url('/user/client-finance-recharge-create') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="finance-create-operate"]').val(),
                            client_id: $('input[name="finance-create-client-id"]').val(),
                            finance_type: $("input[name='finance-create-type']:checked").val(),
                            transaction_amount: $('input[name="finance-create-transaction-amount"]').val(),
                            transaction_date: $('input[name="finance-create-transaction-date"]').val(),
                            transaction_type: $('input[name="finance-create-transaction-type"]').val(),
//                            transaction_account: $('input[name="finance-create-transaction-account"]').val(),
                            transaction_receipt_account: $('input[name="finance-create-transaction-receipt-account"]').val(),
                            transaction_payment_account: $('input[name="finance-create-transaction-payment-account"]').val(),
                            transaction_order: $('input[name="finance-create-transaction-order"]').val(),
                            transaction_title: $('input[name="finance-create-transaction-title"]').val(),
                            transaction_description: $('textarea[name="finance-create-transaction-description"]').val(),
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
                                // location.reload();

                                $("#modal-form-for-finance-create").find('input[type=text], textarea').each(function () {
                                    $(this).val($(this).attr('data-default'));
                                });
                                $("#modal-form-for-finance-create").find("input[name=finance-create-type][value='1']").click();

                                $('#modal-body-for-finance-create').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                TableDatatablesAjax_finance.init($('input[name="finance-create-order-id"]').val());

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
                                // $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });


        // 【财务记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-recharge-record", function() {
            var $that = $(this);
            var $id = $that.attr("data-id");

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-finance-list').modal('show');
        });


        // 【财务记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-recharge", function() {
            var $that = $(this);
            var $id = $that.attr("data-id");

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务-收入-记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance-income", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id,"income");

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务-支出-记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance-expenditure", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id,"expenditure");

            $('#modal-body-for-finance-list').modal('show');
        });




        // 【财务记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-using", function() {
            var $that = $(this);
            var $id = $that.attr("data-id");

            TableDatatablesAjax_funds_using.init($id);

            $('#modal-body-for-funds_using-list').modal('show');
        });




        // 【修改记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });

    });
</script>