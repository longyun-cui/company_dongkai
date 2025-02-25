<script>
    $(function() {


        // 【批量】导出
        $(".main-content").on('click', '.bulk-submit-for-export', function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
                // $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);

            var $url = url_build('/delivery/delivery-export-by-ids?ids='+$ids)
            window.open($url);

        });

    });
</script>