<script>
    $(function() {

        // 【搜索】
        $(".item-main-body").on('click', ".finance-filter-submit", function() {
            $('#datatable_ajax_finance').DataTable().ajax.reload();
        });
        // 【重置】
        $(".item-main-body").on('click', ".finance-filter-cancel", function() {
            $('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $('select.form-filter option').attr("selected",false);
            $('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax_finance').DataTable().ajax.reload();
        });
        // 【查询】回车
//        $(".item-main-body").on('keyup', ".item-search-keyup", function(event) {
//            if(event.keyCode ==13)
//            {
//                $("#filter-submit").click();
//            }
//        });


        // 【编辑】
        $(".item-main-body").on('click', ".item-finance-edit-link", function() {
            var $that = $(this);
            window.location.href = "/item/order-finance-edit?id="+$that.attr('data-id');
        });




        // 【选择类别】
        $("#modal-finance-create-form").on('click', "input[name=finance-create-type]", function() {

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
        $(".item-main-body").on('click', ".item-finance-create-show", function() {
            var that = $(this);

            $('#modal-finance-create-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【添加财务记录】取消
        $(".modal-main-body").on('click', "#item-finance-create-cancel", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.detect-set-keyword').html('');
            $('.detect-set-id').html(0);
            $('.detect-set-date').html('');
            $('.detect-set-original-rank').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-finance-create-body').modal('hide');
            $("#modal-finance-create-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【添加财务记录】提交
        $(".modal-main-body").on('click', "#item-finance-create-submit", function() {
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
                            record_type: $("input[name='finance-create-type']:checked").val(),
                            transaction_amount: $('input[name="finance-create-transaction-amount"]').val(),
                            transaction_date: $('input[name="finance-create-transaction-date"]').val(),
                            transaction_type: $('input[name="finance-create-transaction-type"]').val(),
                            transaction_account: $('input[name="finance-create-transaction-account"]').val(),
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
                                $('#modal-finance-create-body').modal('hide');
                                $("#modal-finance-create-body").on("hidden.bs.modal", function () {
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