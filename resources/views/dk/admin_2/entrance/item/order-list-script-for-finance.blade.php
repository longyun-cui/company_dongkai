<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-finance-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax_finance').DataTable().ajax.reload();
        });
        // 【重置】
        $("#datatable-for-finance-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-finance-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-finance-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-finance-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax_finance').DataTable().ajax.reload();
        });
        // 【查询】回车
        $("#datatable-for-finance-list").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-finance-list").find(".filter-submit").click();
            }
        });




        // 【编辑】跳转
        $(".main-content").on('click', ".item-finance-edit-link", function() {
            var $that = $(this);
            window.location.href = "/item/order-finance-edit?id="+$that.attr('data-id');
        });




        // 【选择类别】
        $("#modal-form-for-finance-create").on('click', "input[name=finance-create-type]", function() {

            var $value = $(this).val();

            if($value == 1) {
                $('.income-show').show();

                // checkbox
//                if($("input[name=time_type]").is(':checked')) {
//                    $('.time-show').show();
//                } else {
//                    $('.time-show').hide();
//                }
                // radio
//                var $time_type = $("input[name=time_type]:checked").val();
//                if($time_type == 1) {
//                    $('.time-show').show();
//                } else {
//                    $('.time-show').hide();
//                }
            } else {
                $('.income-show').hide();
            }

            if($value == 21) {
                $('.expenditure-show').show();
            } else {
                $('.expenditure-show').hide();
            }

        });


        // 【财务记录】添加-显示
        $(".main-content").on('click', ".modal-show-for-finance-create", function() {
            var $that = $(this);

            $('#modal-body-for-finance-create').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
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
                        "{{ url('/item/order-finance-record-create') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="finance-create-operate"]').val(),
                            order_id: $('input[name="finance-create-order-id"]').val(),
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
                                $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });


        // 【财务记录】【删除】
        $(".main-content").on('click', ".item-finance-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"删除"么？', {
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
                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
                                $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【财务记录】【确认】
        $(".main-content").on('click', ".item-finance-confirm-submit", function() {
            var $that = $(this);
            layer.msg('确定要"确认"么？', {
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
                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
                                $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });






        // 【修改-财务-文本-text-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-finance-text-set", function() {
            var $that = $(this);
            $('.finance-text-set-title').html($that.attr("data-id"));
            $('.finance-text-set-column-name').html($that.attr("data-name"));
            $('input[name=finance-text-set-finance-id]').val($that.attr("data-id"));
            $('input[name=finance-text-set-column-key]').val($that.attr("data-key"));
            $('input[name=finance-text-set-operate-type]').val($that.attr('data-operate-type'));
            if($that.attr('data-text-type') == "textarea")
            {
                $('input[name=finance-text-set-column-value]').val('').hide();
                $('textarea[name=finance-textarea-set-column-value]').text($that.attr("data-value")).show();
            }
            else
            {
                $('textarea[name=finance-textarea-set-column-value]').val('').hide();
                $('input[name=finance-text-set-column-value]').val($that.attr("data-value")).show();
            }

            $('#item-submit-for-finance-text-set').attr('data-text-type',$that.attr('data-text-type'));

            $('#modal-body-for-finance-text-set').modal('show');
        });
        // 【修改-财务-文本-text-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-finance-text-set", function() {
            var that = $(this);
            $('#modal-body-for-finance-text-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
            $('input[name=finance-text-set-column-value]').val('');
            $('textarea[name=finance-textarea-set-column-value]').val('');
        });
        // 【修改-财务-文本-text-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-finance-text-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="finance-text-set-column-key"]').val();
            if($that.attr('data-text-type') == "textarea")
            {
                var $column_value = $('textarea[name="finance-textarea-set-column-value"]').val();
            }
            else
            {
                var $column_value = $('input[name="finance-text-set-column-value"]').val();
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
                    operate: $('input[name="finance-text-set-operate"]').val(),
                    item_id: $('input[name="finance-text-set-finance-id"]').val(),
                    operate_type: $('input[name="finance-text-set-operate-type"]').val(),
                    column_key: $column_key,
                    column_value: $column_value,
                },
                function(data){
                    // layer.close(index);
                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        // location.reload();

                        $('#modal-body-for-finance-text-set').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('input[name=finance-text-set-column-value]').val('');
                        $('textarea[name=finance-textarea-set-column-value]').text('');


                        // $('#datatable_ajax').DataTable().ajax.reload(null, false);
                        $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);
                    }
                },
                'json'
            );

        });




        // 【修改-财务-时间-time-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-finance-time-set", function() {
            var $that = $(this);
            $('.finance-time-set-title').html($that.attr("data-id"));
            $('.finance-time-set-column-name').html($that.attr("data-name"));
            $('input[name=finance-time-set-operate-type]').val($that.attr('data-operate-type'));
            $('input[name=finance-time-set-finance-id]').val($that.attr("data-id"));
            $('input[name=finance-time-set-column-key]').val($that.attr("data-key"));
            $('input[name=finance-time-set-time-type]').val($that.attr('data-time-type'));
            if($that.attr('data-time-type') == "datetime")
            {
                $('input[name=finance-time-set-column-value]').show();
                $('input[name=finance-date-set-column-value]').hide();
                $('input[name=finance-time-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }
            else if($that.attr('data-time-type') == "date")
            {
                $('input[name=finance-time-set-column-value]').hide();
                $('input[name=finance-date-set-column-value]').show();
                $('input[name=finance-date-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }

            $('#modal-body-for-finance-time-set').modal('show');
        });
        // 【修改-财务-时间-time-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-finance-time-set", function() {
            var that = $(this);
            $('#modal-body-for-finance-time-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-财务-时间-time-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-finance-time-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="finance-time-set-column-key"]').val();
            var $time_type = $('input[name="finance-time-set-time-type"]').val();
            var $column_value = '';
            if($time_type == "datetime")
            {
                $column_value = $('input[name="finance-time-set-column-value"]').val();
            }
            else if($time_type == "date")
            {
                $column_value = $('input[name="finance-date-set-column-value"]').val();
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
                    operate: $('input[name="finance-time-set-operate"]').val(),
                    item_id: $('input[name="finance-time-set-finance-id"]').val(),
                    operate_type: $('input[name="finance-time-set-operate-type"]').val(),
                    column_key: $column_key,
                    column_value: $column_value,
                    time_type: $time_type
                },
                function(data){
                    // layer.close(index);
                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        // location.reload();

                        $('#modal-body-for-finance-time-set').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        // $('#datatable_ajax').DataTable().ajax.reload(null, false);
                        $('#datatable_ajax_finance').DataTable().ajax.reload(null, false);

                    }
                },
                'json'
            );

        });


    });
</script>