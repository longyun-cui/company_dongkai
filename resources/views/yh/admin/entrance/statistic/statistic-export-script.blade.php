<script>
    $(function() {

        // 【综合分析】
        $(".main-content").on('click', "#filter-submit-for-export", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="comprehensive-month"]').val();
            $('.statistic-title').html($month+'月');

            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/statistic/statistic-export') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    month: $month,
                    operate:"statistic-index"
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });

        });
        // 【清空重选】
        $("#statistic-for-comprehensive").on('click', ".filter-cancel", function() {
            $("#statistic-for-comprehensive").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-comprehensive").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-comprehensive").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="comprehensive-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
            $("#filter-submit-for-comprehensive").click();

        });



        // 【前一月】
        $(".main-content").on('click', ".month-pick-pre", function() {
            var $that = $(this);
            var $box = $that.parents('.month-picker-box');

            var $month_dom = $box.find('.month-picker');
            // var $month_dom = $('input[name="order-month"]');

            var $the_month = $month_dom.val();
            var $date = new Date($the_month);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $pre_year = $year;
            var $pre_month = $month;

            if(parseInt($month) == 0)
            {
                $pre_year = $year - 1;
                $pre_month = 12;
            }

            if($pre_month < 10) $pre_month = '0'+$pre_month;

            var $pre_month_str = $pre_year+'-'+$pre_month;
            $month_dom.val($pre_month_str);

            // $("#filter-submit-for-order").click();

        });
        // 【后一月】
        $(".main-content").on('click', ".month-pick-next", function() {
            var $that = $(this);
            var $box = $that.parents('.month-picker-box');

            var $month_dom = $box.find('.month-picker');
            var $the_month_str = $month_dom.val();

            var $date = new Date($the_month_str);
            var $year = $date.getFullYear();
            var $month = $date.getMonth();

            var $next_year = $year;
            var $next_month = $month;

            if(parseInt($month) == 11)
            {
                $next_year = $year + 1;
                $next_month = 1;
            }
            else $next_month = $month + 2;

            if($next_month < 10) $next_month = '0'+$next_month;

            var $next_month_str = $next_year+'-'+$next_month;
            $month_dom.val($next_month_str);

            // $("#filter-submit-for-order").click();
        });





        /*
            订单
         */
        // 【导出】
        $(".main-content").on('click', ".filter-submit-for-order", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $month = $('input[name="order-month"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
            if($('input[name="order-month"]').val())  $obj.month = $('input[name="order-month"]').val();
            if($('input[name="order-start"]').val())  $obj.order_start = $('input[name="order-start"]').val();
            if($('input[name="order-ended"]').val())  $obj.order_ended = $('input[name="order-ended"]').val();
            if($('select[name="order-staff"]').val() > 0)  $obj.staff_id = $('select[name="order-staff"]').val();
            if($('select[name="order-client"]').val() > 0)  $obj.client_id = $('select[name="order-client"]').val();
            if($('select[name="order-circle"]').val() > 0)  $obj.circle_id = $('select[name="order-circle"]').val();
            if($('select[name="order-route"]').val() > 0)  $obj.route_id = $('select[name="order-route"]').val();
            if($('select[name="order-pricing"]').val() > 0)  $obj.pricing_id = $('select[name="order-pricing"]').val();
            if($('select[name="order-car"]').val() > 0)  $obj.car_id = $('select[name="order-car"]').val();
            if($('select[name="order-trailer"]').val() > 0)  $obj.trailer_id = $('select[name="order-trailer"]').val();
            if($('select[name="order-driver"]').val() > 0)  $obj.driver_id = $('select[name="order-driver"]').val();

            var $url = url_build('/statistic/statistic-export-for-order',$obj);
            window.open($url);

            {{--var $data = new Object();--}}
            {{--$.ajax({--}}
            {{--    type:"post",--}}
            {{--    dataType:'json',--}}
            {{--    async:false,--}}
            {{--    url: "{{ url('/statistic/statistic-order-export') }}",--}}
            {{--    data: {--}}
            {{--        _token: $('meta[name="_token"]').attr('content'),--}}
            {{--        order_type: $('select[name="order-type"]').val(),--}}
            {{--        month: $month,--}}
            {{--        staff: $('select[name="order-staff"]').val(),--}}
            {{--        client: $('select[name="order-client"]').val(),--}}
            {{--        route: $('select[name="order-route"]').val(),--}}
            {{--        pricing: $('select[name="order-pricing"]').val(),--}}
            {{--        car: $('select[name="order-car"]').val(),--}}
            {{--        trailer: $('select[name="order-trailer"]').val(),--}}
            {{--        driver: $('select[name="order-driver"]').val(),--}}
            {{--        operate:"statistic-export"--}}
            {{--    },--}}
            {{--    success:function(data){--}}
            {{--        if(!data.success) layer.msg(data.msg);--}}
            {{--        else--}}
            {{--        {--}}
            {{--            $data = data.data;--}}
            {{--            window.open($data.file);--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}


        });
        // 【清空重选】
        $("#export-for-order").on('click', ".filter-empty", function() {
            $("#export-for-order").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#export-for-order").find('select.form-filter option').attr("selected",false);
            $("#export-for-order").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $(".select2-container").val(-1).trigger("change");

            // $('input[name="order-month"]').val('');
            var $month_dom = $('input[name="order-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
        });




        /*
            财务
         */
        // 【导出】
        $(".main-content").on('click', ".filter-submit-for-finance", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $month = $('input[name="order-month"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="finance-type"]').val() > 0)  $obj.finance_type = $('select[name="finance-type"]').val();
            if($('input[name="finance-month"]').val())  $obj.month = $('input[name="finance-month"]').val();
            if($('input[name="finance-start"]').val())  $obj.finance_start = $('input[name="finance-start"]').val();
            if($('input[name="finance-ended"]').val())  $obj.finance_ended = $('input[name="finance-ended"]').val();
            if($('input[name="finance-order_id"]').val())  $obj.order_id = $('input[name="finance-order_id"]').val();
            if($('input[name="finance-title"]').val())  $obj.title = $('input[name="finance-title"]').val();
            if($('input[name="finance-transaction_type"]').val())  $obj.transaction_type = $('select[name="finance-transaction_type"]').val();
            if($('input[name="finance-transaction_receipt_account"]').val())  $obj.transaction_receipt_account = $('select[name="finance-transaction_receipt_account"]').val();
            if($('input[name="finance-transaction_payment_account"]').val())  $obj.transaction_payment_account = $('select[name="finance-transaction_payment_account"]').val();
            if($('input[name="finance-transaction_order"]').val())  $obj.transaction_order = $('select[name="finance-transaction_order"]').val();

            var $url = url_build('/statistic/statistic-export-for-finance',$obj);
            window.open($url);


        });
        // 【清空重选】
        $("#export-for-finance").on('click', ".filter-empty", function() {
            $("#export-for-finance").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#export-for-finance").find('select.form-filter option').attr("selected",false);
            $("#export-for-finance").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            // $('input[name="finance-month"]').val('');
            var $month_dom = $('input[name="finance-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
        });




        //
        $('.select2-circle').select2({
            ajax: {
                url: "{{ url('/item/order_select2_circle') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        //
        $('.select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        //
        $('.select2-trailer').select2({
            ajax: {
                url: "{{ url('/item/order_select2_trailer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        //
        $('.select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


    });
</script>