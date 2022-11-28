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

        // 【删除】
        $(".main-content").on('click', ".item-finance-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/finance/finance-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload();
                                $('#datatable_ajax_finance').DataTable().ajax.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【确认】
        $(".main-content").on('click', ".item-finance-confirm-submit", function() {
            var $that = $(this);
            layer.msg('确定要"确认"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/finance/finance-confirm') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "finance-confirm",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload();
                                $('#datatable_ajax_finance').DataTable().ajax.reload();
                            }
                        },
                        'json'
                    );
                }
            });
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


        // 显示【添加财务记录】
        $(".main-content").on('click', ".modal-show-for-finance-create", function() {
            var that = $(this);

            $('#modal-body-for-finance-create').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【添加财务记录】取消
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
        // 【添加财务记录】提交
        $(".main-content").on('click', "#item-submit-for-finance-create", function() {
            var that = $(this);
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
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
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                layer.close(index);
                                $('#modal-body-for-finance-create').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                TableDatatablesAjax_finance.init($('input[name="finance-create-order-id"]').val());

                                $('#datatable_ajax').DataTable().ajax.reload();
                                $('#datatable_ajax_finance').DataTable().ajax.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });




    });
</script>