<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-modify-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax_record').DataTable().ajax.reload();
        });
        // 【重置】
        $("#datatable-for-modify-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-modify-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-modify-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-modify-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax_record').DataTable().ajax.reload();
        });
        // 【查询】回车
        $("#datatable-for-modify-list").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-modify-list").find(".filter-submit").click();
            }
        });




        // 显示【修改属性】
        $(".main-content").on('click', ".item-detail-operate", function() {
            var $that = $(this);
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=info-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-set-column-value]').val($that.attr("data-value"));
            $('input[name=info-set-operate-type]').val($that.find('a').attr('data-type'));

            $('#modal-body-for-info-text-set').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【修改属性】取消
        $(".main-content").on('click', "#item-info-set-cancel", function() {
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





    });
</script>