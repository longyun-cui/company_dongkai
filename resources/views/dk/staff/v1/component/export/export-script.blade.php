<script>
    $(function() {


        /*
            订单
         */
        // 【导出】工单倒垂
        $(".main-content").on('click', ".submit--for--order-export", function() {
            console.log(".submit--for--order-export.click");

            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $order_category = $('select[name="order-export--order-category"]').val();
            if($order_category == '')
            {
                layer.msg('请先选择工单类型!');
                return false;
            }

            var $month = $('input[name="export-month"]').val();
            var $date = $('input[name="export-date"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="order-export--order-category"]').val() > 0)  $obj.order_category = $('select[name="order-export--order-category"]').val();
            if($('select[name="order-export--order-type"]').val() > 0)  $obj.order_type = $('select[name="order-export--order-type"]').val();
            if($('input[name="order-export--month"]').val())  $obj.month = $('input[name="order-export--month"]').val();
            if($('input[name="order-export--date"]').val())  $obj.date = $('input[name="order-export--date"]').val();
            if($('input[name="order-export--start"]').val())  $obj.order_start = $('input[name="order-export--start"]').val();
            if($('input[name="order-export--ended"]').val())  $obj.order_ended = $('input[name="order-export--ended"]').val();
            if($('select[name="order-export--project"]').val() > 0)  $obj.project = $('select[name="order-export--project"]').val();
            if($('select[name="order-export--inspected-result"]').val() != -1)  $obj.inspected_result = $('select[name="order-export--inspected-result"]').val();

            var $url = url_build('/o1/export/order-export',$obj);
            window.open($url);

        });
        // 【导出】交付导出
        $(".main-content").on('click', ".submit--for--delivery-export", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $order_category = $('select[name="delivery-export--order-category"]').val();
            if($order_category == '')
            {
                layer.msg('请先选择工单类型!');
                return false;
            }

            var $month = $('input[name="export-month"]').val();
            var $date = $('input[name="export-date"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="delivery-export--order-category"]').val() > 0)  $obj.order_category = $('select[name="delivery-export--order-category"]').val();
            if($('select[name="delivery-export--order-type"]').val() > 0)  $obj.order_type = $('select[name="delivery-export--order-type"]').val();
            if($('input[name="delivery-export--month"]').val())  $obj.month = $('input[name="delivery-export--month"]').val();
            if($('input[name="delivery-export--date"]').val())  $obj.date = $('input[name="delivery-export--date"]').val();
            if($('input[name="delivery-export--start"]').val())  $obj.order_start = $('input[name="delivery-export--start"]').val();
            if($('input[name="delivery-export--ended"]').val())  $obj.order_ended = $('input[name="delivery-export--ended"]').val();
            if($('select[name="delivery-export--project"]').val() > 0)  $obj.project = $('select[name="delivery-export--project"]').val();
            if($('select[name="delivery-export--client"]').val() > 0)  $obj.client = $('select[name="delivery-export--client"]').val();
            if($('select[name="delivery-export--delivered-result"]').val() != -1)  $obj.delivered_result = $('select[name="delivery-export--delivered-result"]').val();


            var $url = url_build('/o1/export/delivery-export',$obj);
            window.open($url);

        });
        // 【导出】去重导出
        $(".main-content").on('click', ".submit--for--duplicate-export", function() {
            var $that = $(this);
            var $id = $that.attr("data-id");
            var $time_type = $that.data("time-type");

            var $order_category = $('select[name="duplicate-export--order-category"]').val();

            var $date = $('input[name="duplicate-export--date"]').val();
            var $month = $('input[name="duplicate-export--month"]').val();
            var $start = $('input[name="duplicate-export--start"]').val();
            var $ended = $('input[name="duplicate-export--ended"]').val();

            var $project = $('select[name="duplicate-export--project"]').val();
            var $client = $('select[name="duplicate-export--client"]').val();
            var $city = $('select[name="duplicate-export--city"]').val();
            var $district = $('select[name="duplicate-export--district"]').val();

            // if($project <= 0 && $client <= 0)
            // {
            //     layer.msg('项目和客户必选一个！');
            //     return false;
            // }

            if($project <= 0)
            {
                layer.msg('请选择项目！');
                return false;
            }

            var $obj = new Object();
            $obj.time_type = $time_type;
            if($order_category > 0)  $obj.order_category = $order_category;
            if($date)  $obj.date = $date;
            if($month)  $obj.month = $month;
            if($start)  $obj.start = $start;
            if($ended)  $obj.ended = $ended;
            if($project > 0)  $obj.project = $project;
            if($client > 0)  $obj.client = $client;
            if($city > 0)  $obj.client = $city;
            if($district > 0)  $obj.district = $district;

            var $url = url_build('/o1/export/duplicate-export',$obj);
            window.open($url);
        });




        // 【清空重选】
        $(".main-content").on('click', ".filter-empty--for--export", function() {

            var $that = $(this);
            var $filter_box = $that.closest('.filter-box');
            // console.log(1);

            $filter_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });

            $filter_box.find('select.form-filter option').prop("selected",false);
            $filter_box.find('select.form-filter').find('option:eq(-1)').prop('selected', true);

            $filter_box.find(".select2-reset").val(-1).trigger("change");

        });


    });
</script>