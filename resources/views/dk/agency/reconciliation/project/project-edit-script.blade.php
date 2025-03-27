<script>
    $(function() {





        // 【交付-管理】【添加成交记录】添加-显示
        $(".main-content").off('click', ".item-modal-show-for-recharge-create").on('click', ".item-modal-show-for-recharge-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form-for-reconciliation-project-recharge-create');

            var $modal = $('#modal-for-reconciliation-project-recharge-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find("#item-submit-for-reconciliation-project-recharge-create").data('datatable-list-id',$table_id);

            $modal.modal('show');
        });
        // 【交付-管理】【添加-充值-记录】编辑-提交
        $(".main-content").off('click', "#item-submit-for-reconciliation-project-recharge-create").on('click', "#item-submit-for-reconciliation-project-recharge-create", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

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

            var options = {
                url: "{{ url('/reconciliation/v1/operate/project/item-recharge-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#form-for-reconciliation-project-recharge-create');

                        $('#modal-for-reconciliation-project-recharge-create').modal('hide');
                        // $('#modal-for-reconciliation-project-recharge-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-reconciliation-project-recharge-create").ajaxSubmit(options);
        });




        $('.modal-wrapper').on('hide.bs.modal', function (event) {
            $('.datatable-wrapper').removeClass('operating');
            $('.datatable-wrapper').find('tr.operating').removeClass('operating');
        });
        // 【modal】取消
        $(".main-content").on('click', ".modal-cancel", function() {
            var $that = $(this);
            var $modal = $that.closest('.modal-wrapper');
            $modal.modal('hide');

            // $modal.modal('hide').on("hidden.bs.modal", function () {
            //     $("body").addClass("modal-open");
            // });
        });



    });
</script>