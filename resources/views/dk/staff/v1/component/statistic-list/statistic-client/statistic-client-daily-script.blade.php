<script>
    $(function() {


        // 【提交】生成日报
        $(".main-content").on('click', ".statistic-list-client-daily-create", function() {
            var $that = $(this);
            var $search_wrapper = $that.closest('.search-wrapper');
            var $assign_date = $search_wrapper.find('input[name="statistic-list-client-daily-date"]').val();

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
                "{{ url('/o1/statistic-list/statistic-client-daily/daily-create') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "statistic-client-daily-create",
                    assign_date: $assign_date
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
                        $search_wrapper.find('.filter-refresh').click();
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




        // 【删除】
        $(".main-content").on('click', ".item-delete-submit-of-statistic-client-daily", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

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
                        "{{ url('/o1/statistic-list/statistic-client-daily/item-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "daily-item-delete",
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        })
                        .fail(function(jqXHR, status, error) {
                            console.log('fail');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('always');
                            layer.close(index);
                            layer.closeAll('loading');
                        });
                }
            });
        });
        // 【确认】
        $(".main-content").on('click', ".item-confirm-submit-of-statistic-client-daily", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            layer.msg('确定"确认"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

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
                        "{{ url('/o1/statistic-list/statistic-client-daily/item-confirm') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "daily-item-confirm",
                            item_id: $that.attr('data-id')
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
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        })
                        .fail(function(jqXHR, status, error) {
                            console.log('fail');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('always');
                            layer.close(index);
                            layer.closeAll('loading');
                        });
                }
            });
        });




        // 【通用】【字段-编辑】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-item-field-set-of-statistic-client-daily", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $modal = $('#modal-for-item-field-set-of-statistic-client-daily');
            $modal.attr('data-datatable-id',$table_id);
            $modal.attr('data-datatable-row-index',$that.data('row-index'));

            var $form = $('#form-for-item-field-set-of-statistic-client-daily');
            form_reset('#form-for-item-field-set-of-statistic-client-daily');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');
            $datatable_wrapper.find('td').removeClass('operating');
            $that.addClass('operating');


            $('.item-field-set-item-name').html($datatable_wrapper.attr("data-item-name"));
            $('.item-field-set-item-id').html($that.attr("data-id"));
            $('.item-field-set-column-name').html($that.attr("data-column-name"));

            $('input[name="operate-type"]').val($that.attr('data-operate-type'));

            $('input[name="item-category"]').val($datatable_wrapper.data("datatable-item-category"));
            $('input[name="item-id"]').val($that.attr("data-id"));

            $('input[name="column-key"]').val($that.attr("data-key"));
            $('input[name="column-key2"]').val($that.attr("data-key2"));

            $modal.find('.column-value').val('').hide();

            var $column_type = $that.attr('data-column-type');
            $('input[name="column-type"]').val($column_type);
            if($column_type == "text")
            {
                $modal.find('input[name="item-field-set-text-value"]').val($that.attr("data-value")).show();
            }
            else if($column_type == "textarea")
            {
                $modal.find('textarea[name="item-field-set-textarea-value"]').val($that.attr("data-value")).show();
            }

            $modal.modal('show');
        });
        // 【通用】【字段-编辑】【取消】
        $(".main-content").on('click', "#edit-cancel-for-item-field-set-of-statistic-client-daily", function() {
            var that = $(this);
            $('#modal-for-item-field-set-of-statistic-client-daily').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });

            form_reset('#modal-for-item-field-set-of-statistic-client-daily');
        });
        // 【通用】【字段-编辑】【提交】
        $(".main-content").on('click', "#edit-submit-for-item-field-set-of-statistic-client-daily", function() {
            var $that = $(this);
            var $modal = $('#modal-for-item-field-set-of-statistic-client-daily');
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
                url: "{{ url('/o1/statistic-list/statistic-client-daily/item-field-set') }}",
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

                        var $form = $('#form-for-item-field-set-of-statistic-client-daily');
                        var item_category = $form.find('input[name="item-category"]').val();
                        var column_key = $form.find('input[name="column-key"]').val();

                        // var $rowIndex = $modal.data('datatable-row-index');
                        // $('#'+$table_id).DataTable().row($rowIndex).data($response.data.data).invalidate().draw(false);

                        $td.attr('data-value',$response.data.data.value);
                        $td.attr('data-option-name',$response.data.data.text);
                        $td.html($response.data.data.text);

                        // 重置输入框
                        form_reset('#form-for-item-field-set-of-statistic-client-daily');

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
            $("#form-for-item-field-set-of-statistic-client-daily").ajaxSubmit(options);

        });








        // 【修改记录】【显示】
        $(".main-content").on('click', ".modal-show-for-modify-1", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });


    });
</script>