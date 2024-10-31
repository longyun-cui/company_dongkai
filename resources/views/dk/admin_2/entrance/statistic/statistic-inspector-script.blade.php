<script>
    $(function() {

        // 【客服看板】按月搜索
        $(".main-content").on('click', "#filter-submit-for-inspector-by-month", function() {

            $("#statistic-for-inspector").find('input[name=inspector-time-type]').val('month');
            var $month_dom = $('input[name="inspector-month"]');
            var $the_month_str = $month_dom.val();
            $(".statistic-title").html($the_month_str);
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【客服看板】按天搜索
        $(".main-content").on('click', "#filter-submit-for-inspector-by-day", function() {

            $("#statistic-for-inspector").find('input[name=inspector-time-type]').val('day');
            var $date_dom = $('input[name="inspector-date"]');
            var $the_date_str = $date_dom.val();
            $(".statistic-title").html($the_date_str);
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【客服看板】【重置】
        $("#statistic-for-inspector").on('click', ".filter-cancel", function() {
            $("#statistic-for-inspector").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-inspector").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-inspector").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $("#statistic-for-inspector").find('input[name=inspector-time-type]').val('');

            var $month_dom = $('input[name="inspector-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

            var $date_dom = $('input[name="inspector-date"]');
            var $date_default = $date_dom.attr('data-default')
            $date_dom.val($date_default);

            $(".statistic-title").html("全部");

            // $("#filter-submit-for-inspector").click();
            $('#datatable_ajax').DataTable().ajax.reload();

        });




        // 【客服看板】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-inspector", function() {

            var $month_dom = $('input[name="inspector-month"]');
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

            $("#filter-submit-for-inspector-by-month").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });
        // 【客服看板】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-inspector", function() {

            var $month_dom = $('input[name="inspector-month"]');
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

            $("#filter-submit-for-inspector-by-month").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });

        // 【客服看板】【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-inspector", function() {

            var $date_dom = $('input[name="inspector-date"]');
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


            $("#filter-submit-for-inspector-by-day").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });
        // 【客服看板】【后一天】
        $(".main-content").on('click', ".date-pick-next-for-inspector", function() {

            var $date_dom = $('input[name="inspector-date"]');
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

            $("#filter-submit-for-inspector-by-day").click();
            // $('#datatable_ajax').DataTable().ajax.reload();

        });




    });



</script>