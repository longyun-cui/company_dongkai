<script>
    $(function() {

        // 【综合分析】
        $(".main-content").on('click', "#filter-submit-for-export", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            var $month = $('input[name="export-month"]').val();
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
        $("#statistic-for-export").on('click', ".filter-cancel", function() {
            $("#statistic-for-export").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-export").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-export").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            var $month_dom = $('input[name="export-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);
            $("#filter-submit-for-export").click();

        });



        // 【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-export", function() {
            var $that = $(this);
            var $month_dom = $('input[name="export-month"]');

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

        });
        // 【后一月】
        $(".main-content").on('click', ".month-pick-next-for-export", function() {
            var $that = $(this);
            var $month_dom = $('input[name="export-month"]');
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
        });

        // 【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-export", function() {

            var $date_dom = $('input[name="export-date"]');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $yesterday_time = $time - (24*60*60*1000);

            var $yesterday = new Date($yesterday_time);
            var $yesterday_year = $yesterday.getFullYear();
            var $yesterday_month = ('00'+($yesterday.getMonth()+1)).slice(-2);
            var $yesterday_day = ('00'+($yesterday.getDate())).slice(-2);

            var $yesterday_date_str = $yesterday_year + '-' + $yesterday_month + '-' + $yesterday_day;
            $date_dom.val($yesterday_date_str);

        });
        // 【后一天】
        $(".main-content").on('click', ".date-pick-next-for-export", function() {

            var $date_dom = $('input[name="export-date"]');
            var $the_date_str = $date_dom.val();

            var $date = new Date($the_date_str);
            var $time = $date.getTime();
            var $tomorrow_time = $time + (24*60*60*1000);

            var $tomorrow = new Date($tomorrow_time);
            var $tomorrow_year = $tomorrow.getFullYear();
            var $tomorrow_month = ('00'+($tomorrow.getMonth()+1)).slice(-2);
            var $tomorrow_day = ('00'+($tomorrow.getDate())).slice(-2);

            var $tomorrow_date_str = $tomorrow_year + '-' + $tomorrow_month + '-' + $tomorrow_day;
            $date_dom.val($tomorrow_date_str);

        });





        /*
            订单
         */
        // 【导出】
        $(".main-content").on('click', ".filter-submit-for-order", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $export_type = that.attr("data-type");

            var $month = $('input[name="export-month"]').val();
            var $day = $('input[name="export-date"]').val();

            var $obj = new Object();
            $obj.export_type = $export_type;
            if($('select[name="order-type"]').val() > 0)  $obj.order_type = $('select[name="order-type"]').val();
            if($('input[name="export-month"]').val())  $obj.month = $('input[name="export-month"]').val();
            if($('input[name="export-date"]').val())  $obj.day = $('input[name="export-date"]').val();
            if($('input[name="export-start"]').val())  $obj.order_start = $('input[name="export-start"]').val();
            if($('input[name="export-ended"]').val())  $obj.order_ended = $('input[name="export-ended"]').val();
            if($('select[name="order-staff"]').val() > 0)  $obj.staff = $('select[name="order-staff"]').val();
            if($('select[name="order-project"]').val() > 0)  $obj.project = $('select[name="order-project"]').val();
            if($('select[name="order-inspected-result"]').val() != -1)  $obj.inspected_result = $('select[name="order-inspected-result"]').val();

            var $url = url_build('/statistic/statistic-export-for-order',$obj);
            window.open($url);

            // setTimeout(function(){
            //     $('#datatable_ajax').DataTable().ajax.reload(null,false);
            // }, 1000);

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




        //
        $('.select2-project').select2({
            ajax: {
                url: "{{ url('/item/item_select2_project') }}",
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