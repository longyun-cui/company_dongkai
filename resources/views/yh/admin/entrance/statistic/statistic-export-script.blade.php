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
        // 【重置】
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
        $(".main-content").on('click', ".month-pick-pre-for-comprehensive", function() {

            var $month_dom = $('input[name="comprehensive-month"]');
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
            $("#filter-submit-for-comprehensive").click();

        });
        // 【后一月】
        $(".main-content").on('click', ".month-pick-next-for-comprehensive", function() {

            var $month_dom = $('input[name="comprehensive-month"]');
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
            $("#filter-submit-for-comprehensive").click();

        });





        // 【分量分析】
        $(".main-content").on('click', "#filter-submit-for-order", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="order-month"]').val();


            var $obj = new Object();
            if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
            if($('input[name="order-month"]').val())  $obj.month = $('input[name="order-month"]').val();
            if($('select[name="order-staff"]').val() > 0)  $obj.staff_id = $('select[name="order-staff"]').val();
            if($('select[name="order-client"]').val() > 0)  $obj.client_id = $('select[name="order-client"]').val();
            if($('select[name="order-circle"]').val() > 0)  $obj.circle_id = $('select[name="order-circle"]').val();
            if($('select[name="order-route"]').val() > 0)  $obj.route_id = $('select[name="order-route"]').val();
            if($('select[name="order-pricing"]').val() > 0)  $obj.pricing_id = $('select[name="order-pricing"]').val();
            if($('select[name="order-car"]').val() > 0)  $obj.car_id = $('select[name="order-car"]').val();
            if($('select[name="order-trailer"]').val() > 0)  $obj.trailer_id = $('select[name="order-trailer"]').val();
            if($('select[name="order-driver"]').val() > 0)  $obj.driver_id = $('select[name="order-driver"]').val();

            var $url = url_build('/statistic/statistic-order-export',$obj);
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
        // 【重置】
        $("#export-for-order").on('click', ".filter-cancel", function() {
            $("#export-for-order").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#export-for-order").find('select.form-filter option').attr("selected",false);
            $("#export-for-order").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            // $('input[name="order-month"]').val('');

            var $month_dom = $('input[name="order-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

        });

        // 【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-order", function() {

            var $month_dom = $('input[name="order-month"]');
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
        $(".main-content").on('click', ".month-pick-next-for-order", function() {

            var $month_dom = $('input[name="order-month"]');
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


    });
</script>