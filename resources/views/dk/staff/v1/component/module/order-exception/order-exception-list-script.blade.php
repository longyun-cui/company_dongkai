<script>

    $(function() {




        // 【批量操作】批量-导出
        $(".main-wrapper").off('click', '.submit--for--exception-export').on('click', '.submit--for--exception-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $search_row = $that.closest('.datatable-search-row');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $start = $search_row.find('input[name="order-exception-start"]').val();
            var $ended = $search_row.find('input[name="order-exception-ended"]').val();
            var $exception_type = $search_row.find('select[name="order-exception-exception-type"]').val();

            var $url = url_build('/o1/order-exception/order-exception-export?assign_start='+$start+'&assign_ended='+$ended+'&exception_type='+$exception_type);
            window.open($url);


        });


    });





</script>