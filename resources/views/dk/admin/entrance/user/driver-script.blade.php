<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-driver-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $("#datatable-for-driver-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-driver-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-driver-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-driver-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【查询】回车
        $("#datatable-for-driver-list").on('keyup', ".item-search-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-driver-list").find(".filter-submit").click();
            }
        });




        // 【编辑】
        $(".main-content").on('click', ".item-admin-edit-link", function() {
            var $that = $(this);
            window.location.href = "/user/driver-edit?id="+$that.attr('data-id');
        });


        // 【获取详情】
        $(".main-content").on('click', ".item-detail-show", function() {
            var $that = $(this);
            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/user/driver-get') }}",
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
                        "{{ url('/user/driver-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "driver-admin-delete",
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
        // 【管理员-恢复】
        $(".main-content").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/driver-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "driver-admin-restore",
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
        // 【管理员-永久删除】
        $(".main-content").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定"永久删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/driver-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "driver-admin-delete-permanently",
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


        // 【发布】
        $(".main-content").on('click', ".item-publish-submit", function() {
            var $that = $(this);
            layer.msg('确定"发布"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/item-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-publish",
                            id: $that.attr('data-id')
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
            // layer.msg('确定"启用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/user/driver-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "driver-admin-enable",
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
        });
        // 【禁用】
        $(".main-content").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            // layer.msg('确定"禁用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/user/driver-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "driver-admin-disable",
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
        });




        // 【修改-文本-属性】显示
        $(".main-content").on('dblclick', ".modal-show-for-info-text-set", function() {
            var $that = $(this);
            $('.info-text-set-title').html($that.attr("data-name"));
            $('.info-text-set-column-name').html($that.attr("data-column-name"));
            $('input[name=info-text-set-user-id]').val($that.attr("data-id"));
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
                        "{{ url('/user/driver-info-text-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-text-set-operate"]').val(),
                            user_id: $('input[name="info-text-set-user-id"]').val(),
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
            $('input[name=info-time-set-user-id]').val($that.attr("data-id"));
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
                        "{{ url('/user/driver-info-text-set') }}",
                                {{--"{{ url('/user/driver-info-time-set') }}",--}}
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-time-set-operate"]').val(),
                            user_id: $('input[name="info-time-set-user-id"]').val(),
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
            $('input[name=info-select-set-user-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
//            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));


            $('select[name=info-select-set-column-value]').removeClass('select2-car').removeClass('select2-client');
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
            $('input[name=info-select-set-driver-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-body-for-info-select-set').modal('show');

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
                        "{{ url('/user/driver-info-select-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-select-set-operate"]').val(),
                            user_id: $('input[name="info-select-set-user-id"]').val(),
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




        // 【图片-image-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-image", function() {
            var $that = $(this);

            $('.attachment-set-title').html($that.attr("data-id"));
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=attachment-set-driver-id]').val($that.attr("data-id"));
            $('input[name=attachment-set-column-key]').val($that.attr("data-key"));
            $('input[name=attachment-set-column-value]').val($that.attr("data-value"));
            $('input[name=attachment-set-operate-type]').val($that.attr('data-operate-type'));


            $('#modal-attachment-set-form input[name=user_id]').val($that.attr("data-id"));
            $('#modal-attachment-set-form input[name=column-key]').val($that.attr("data-key"));
            $('#modal-attachment-set-form input[name=column-value]').val($that.attr("data-value"));
            $('#modal-attachment-set-form input[name=-operate-type]').val($that.attr('data-operate-type'));


            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/user/driver-get-attachment-html') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    user_id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('.attachment-edit-box').html($data.html);

            $('#modal-body-for-image').modal('show');

            $(".file-multiple-images").fileinput({
                allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
                showUpload: false
            });

            $('.lightcase-image').lightcase({
                maxWidth: 9999,
                maxHeight: 9999
            });

        });
        // 【图片-image-属性】【提交】
        $(".main-content").on('click', ".item-submit-for-image-set", function() {
            var $that = $(this);
            var $parent = $that.parents('.form-group');

            var $column_key = $that.attr('data-key');
            $('input[name=column_key]').val($column_key);
            $('input[name=attachment-set-column-key]').val($column_key);

            // var $column_key = $('input[name="info-attachment-set-column-key"]').val();
            // var $column_value = $('select[name="info-attachment-set-column-value"]').val();

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
                        url: "{{ url('/user/driver-info-attachment-set') }}",
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
                                var $result = '';
                                var $domain = "{{ env('DOMAIN_CDN').'/' }}";

                                if($column_key == 'driver_licence') $result = $domain + data.data.driver.driver_licence;
                                else if($column_key == 'driver_certification') $result = $domain + data.data.driver.driver_certification;
                                else if($column_key == 'driver_ID_front') $result = $domain + data.data.driver.driver_ID_front;
                                else if($column_key == 'driver_ID_back') $result = $domain + data.data.driver.driver_ID_back;

                                var $a_html = '<a class="lightcase-image" data-rel="lightcase" href="'+$result+'">'+
                                    '<img src="'+$result+'" alt="" />'+
                                    '</a>';
                                console.log(data);
                                console.log($result);
                                console.log($a_html);

                                $parent.find('.thumbnail').html($a_html);

                                $('.fileinput-exists[data-dismiss="fileinput"]').click();

                                $parent.find(".fileinput-remove-button").click();

                                $('.lightcase-image').lightcase({
                                    maxWidth: 9999,
                                    maxHeight: 9999
                                });
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




        // 【附件-attachment-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-attachment", function() {
            var $that = $(this);

            $('.attachment-set-title').html($that.attr("data-id") + '-' + $that.attr("data-name"));
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=attachment-set-driver-id]').val($that.attr("data-id"));
            $('input[name=attachment-set-column-key]').val($that.attr("data-key"));
            $('input[name=attachment-set-column-value]').val($that.attr("data-value"));
            $('input[name=attachment-set-operate-type]').val($that.attr('data-operate-type'));


            $('#modal-attachment-set-form input[name=user_id]').val($that.attr("data-id"));
            $('#modal-attachment-set-form input[name=column-key]').val($that.attr("data-key"));
            $('#modal-attachment-set-form input[name=column-value]').val($that.attr("data-value"));
            $('#modal-attachment-set-form input[name=-operate-type]').val($that.attr('data-operate-type'));


            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/user/driver-info-attachment-get-html') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    user_id: $that.attr('data-id')
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

            $('.lightcase-image').lightcase({
                maxWidth: 9999,
                maxHeight: 9999
            });
        });
        // 【附件-attachment-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-attachment-set", function() {
            var that = $(this);
            $('#modal-body-for-attachment').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【附件-attachment-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-attachment-set-", function() {
            var $that = $(this);
            var $parent = $that.parents('.form-group');

            var $column_key = $that.attr('data-key');
            $('input[name=column_key]').val($column_key);
            $('input[name=attachment-set-column-key]').val($column_key);

            // var $column_key = $('input[name="info-attachment-set-column-key"]').val();
            // var $column_value = $('select[name="info-attachment-set-column-value"]').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    var options = {
                        url: "{{ url('/user/driver-info-attachment-set') }}",
                        type: "post",
                        dataType: "json",
                        // target: "#div2",
                        success: function (data) {
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                var $result = '';
                                var $domain = "{{ env('DOMAIN_CDN').'/' }}";

                                if($column_key == 'driver_licence') $result = $domain + data.data.driver.driver_licence;
                                else if($column_key == 'driver_certification') $result = $domain + data.data.driver.driver_certification;
                                else if($column_key == 'driver_ID_front') $result = $domain + data.data.driver.driver_ID_front;
                                else if($column_key == 'driver_ID_back') $result = $domain + data.data.driver.driver_ID_back;

                                var $a_html = '<a class="lightcase-image" data-rel="lightcase" href="'+$result+'">'+
                                    '<img src="'+$result+'" alt="" />'+
                                    '</a>';
                                console.log(data);
                                console.log($result);
                                console.log($a_html);

                                $parent.find('.thumbnail').html($a_html);

                                $('.fileinput-exists[data-dismiss="fileinput"]').click();

                                $parent.find(".fileinput-remove-button").click();

                                $('.lightcase-image').lightcase({
                                    maxWidth: 9999,
                                    maxHeight: 9999
                                });
                            }
                        }
                    };
                    $("#modal-attachment-set-form").ajaxSubmit(options);

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

                    var options = {
                        url: "{{ url('/user/driver-info-attachment-set') }}",
                        type: "post",
                        dataType: "json",
                        // target: "#div2",
                        success: function (data) {
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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
                                    url: "{{ url('/user/driver-info-attachment-get-html') }}",
                                    data: {
                                        _token: $('meta[name="_token"]').attr('content'),
                                        operate:"item-get",
                                        user_id: $('#modal-attachment-set-form input[name=user_id]').val()
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

                                $('.lightcase-image').lightcase({
                                    maxWidth: 9999,
                                    maxHeight: 9999
                                });

//                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        }
                    };
                    $("#modal-attachment-set-form").ajaxSubmit(options);



//                    var formData = new FormData();
//                    formData.append('attachment_file', fileItem);
//                    formData.append('operate', $('input[name="attachment-set-operate"]').val());
//                    formData.append('user_id', $('input[name="attachment-set-user-id"]').val());

                    {{--$.post(--}}
                    {{--"{{ url('/user/driver-info-attachment-set') }}",--}}
                    {{--{--}}
                    {{--_token: $('meta[name="_token"]').attr('content'),--}}
                    {{--operate: $('input[name="attachment-set-operate"]').val(),--}}
                    {{--user_id: $('input[name="attachment-set-driver-id"]').val(),--}}
                    {{--operate_type: $('input[name="attachment-set-operate-type"]').val(),--}}
                    {{--attachment_file: $blob--}}
                    {{--},--}}
                    {{--function(data){--}}
                    {{--layer.close(index);--}}
                    {{--if(!data.success) layer.msg(data.msg);--}}
                    {{--//                            else location.reload();--}}
                    {{--else--}}
                    {{--{--}}
                    {{--layer.close(index);--}}
                    {{--$('#modal-body-for-attachment-set').modal('hide').on("hidden.bs.modal", function () {--}}
                    {{--$("body").addClass("modal-open");--}}
                    {{--});--}}

                    {{--$('#datatable_ajax').DataTable().ajax.reload(null, false);--}}
                    {{--//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);--}}
                    {{--}--}}
                    {{--},--}}
                    {{--'json'--}}
                    {{--);--}}


        });

        // 【附件-attachment-属性】【删除】
        $(".main-content").on('click', ".item-attachment-delete-this", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/user/driver-info-attachment-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "user-driver-attachment-delete",
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




        // 【修改记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });




        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });


        $(".file-multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

    });
</script>