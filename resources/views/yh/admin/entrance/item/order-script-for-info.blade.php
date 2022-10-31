<script>
    $(function() {




        // 显示【修改属性】
        $(".modal-main-body").on('click', ".item-detail-operate", function() {
            var $that = $(this);
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=info-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-set-column-value]').val($that.attr("data-value"));
            $('input[name=info-set-operate-type]').val($that.find('a').attr('data-type'));

            $('#modal-info-set-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【修改属性】取消
        $(".modal-main-body").on('click', "#item-info-set-cancel", function() {
            var $that = $(this);
            var $group = $(this).parents('form-group');

//            $('input[name=info-set-order-id]').val(0);
//            $('input[name=info-set-operate-type]').val('');
            $('input[name=info-set-column-key]').val('');
            $('input[name=info-set-column-value]').val('');

            $('#modal-info-set-body').modal('hide');
            $("#modal-info-set-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改属性】提交
        $(".modal-main-body").on('click', "#item-info-set-submit", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-set-column-key"]').val();
            var $column_value = $('input[name="info-set-column-value"]').val();
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-info-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-set-operate"]').val(),
                            order_id: $('input[name="info-set-order-id"]').val(),
                            operate_type: $('input[name="info-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                layer.close(index);
                                $('#modal-info-set-body').modal('hide');
                                $("#modal-info-set-body").on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

//                                $('#datatable_ajax').DataTable().ajax.reload();
//                                $('#datatable_ajax_inner').DataTable().ajax.reload();

                                $set_column = $('.item-detail-operate[data-key='+$column_key+']');
                                $set_column.attr('data-value',$column_value);
                                $set_column.html('<a href="javascript:void(0);" data-type="edit">修改</a>');
                                $set_column.parents('.form-group').find('.item-detail-text').html($column_value);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 显示【修改排名】
        $(".item-main-body").on('click', ".item-finance-edit-show", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(that.attr('data-id'));
            $('input[name=detect-set-date]').val(that.attr('data-date'));
            $('.detect-set-keyword').html(that.attr('data-name'));
            $('.detect-set-id').html(that.attr('data-id'));
            $('.detect-set-date').html(that.attr('data-date'));
            $('.detect-set-original-rank').html(that.attr('data-rank'));
            $('input[name=detect-set-rank]').val('');

            $('#modal-set-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【修改排名】取消
        $(".modal-main-body").on('click', "#item-finance-edit-cancel", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.detect-set-keyword').html('');
            $('.detect-set-id').html(0);
            $('.detect-set-date').html('');
            $('.detect-set-original-rank').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-set-body').modal('hide');
            $("#modal-set-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改排名】提交
        $(".modal-main-body").on('click', "#item-finance-edit-submit", function() {
            var that = $(this);
//            layer.msg('确定"提交"么？', {
//                time: 0
//                ,btn: ['确定', '取消']
//                ,yes: function(index){
//                }
//            });
            $.post(
                "{{ url('/item/order-finance-record-edit') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:$('input[name="detect-set-operate"]').val(),
                    detect_id:$('input[name="detect-set-id"]').val(),
                    detect_date:$('input[name="detect-set-date"]').val(),
                    detect_rank:$('input[name="detect-set-rank"]').val(),
                    detect_description:$('input[name="detect-set-description"]').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
//                    else location.reload();
                    else
                    {
//                        layer.close(index);
                        $('#modal-set-body').modal('hide');
                        $("#modal-set-body").on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
//                        TableDatatablesAjax_inner.init($keyword_id);
                        $('#datatable_ajax_inner').DataTable().ajax.reload(null,false);
                    }
                },
                'json'
            );
        });




    });
</script>