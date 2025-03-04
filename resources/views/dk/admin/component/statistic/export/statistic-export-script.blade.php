<script>
    $(function() {






        /*
            订单
         */
        // 【导出】
        $(".main-content").on('click', ".filter-submit-for-order-export", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $month = $('input[name="export-month"]').val();
            var $date = $('input[name="export-date"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
            if($('input[name="export-month"]').val())  $obj.month = $('input[name="export-month"]').val();
            if($('input[name="export-date"]').val())  $obj.date = $('input[name="export-date"]').val();
            if($('input[name="export-start"]').val())  $obj.order_start = $('input[name="export-start"]').val();
            if($('input[name="export-ended"]').val())  $obj.order_ended = $('input[name="export-ended"]').val();
            if($('select[name="export-client"]').val() > 0)  $obj.client = $('select[name="export-client"]').val();
            if($('select[name="export-staff"]').val() > 0)  $obj.staff = $('select[name="export-staff"]').val();
            if($('select[name="export-project"]').val() > 0)  $obj.project = $('select[name="export-project"]').val();
            if($('select[name="export-inspected-result"]').val() != -1)  $obj.inspected_result = $('select[name="export-inspected-result"]').val();

            var $url = url_build('/v1/operate/statistic/order-export',$obj);
            window.open($url);

        });
        $(".main-content").on('click', ".filter-submit-for-delivery-export", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $month = $('input[name="export-month"]').val();
            var $date = $('input[name="export-date"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
            if($('input[name="export-month"]').val())  $obj.month = $('input[name="export-month"]').val();
            if($('input[name="export-date"]').val())  $obj.date = $('input[name="export-date"]').val();
            if($('input[name="export-start"]').val())  $obj.order_start = $('input[name="export-start"]').val();
            if($('input[name="export-ended"]').val())  $obj.order_ended = $('input[name="export-ended"]').val();
            if($('select[name="order-client"]').val() > 0)  $obj.client = $('select[name="order-client"]').val();
            if($('select[name="order-staff"]').val() > 0)  $obj.staff = $('select[name="order-staff"]').val();
            if($('select[name="order-project"]').val() > 0)  $obj.project = $('select[name="order-project"]').val();
            if($('select[name="order-inspected-result"]').val() != -1)  $obj.inspected_result = $('select[name="order-inspected-result"]').val();

            var $url = url_build('/v1/operate/statistic/delivery-export',$obj);
            window.open($url);

        });




        // 【清空重选】
        $(".main-content").on('click', ".filter-empty-for-export", function() {

            var $that = $(this);
            var $filter_box = $that.closest('.filer-box');

            $filter_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });

            $filter_box.find('select.form-filter option').attr("selected",false);
            $filter_box.find('select.form-filter').find('option:eq(0)').attr('selected', true);

        });





    });
</script>