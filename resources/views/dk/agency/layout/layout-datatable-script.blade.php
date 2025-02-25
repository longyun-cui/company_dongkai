<script>
    $(function() {

        // 【搜索】
        $(".main-content").on('click', ".filter-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            console.log($table_id);

            var $time_type = $that.attr('data-time-type');
            $datatable_wrapper.find('.time-type').val($time_type);
            if($time_type == "all")
            {
            }
            else if($time_type == "date")
            {
                var $date_dom = $datatable_wrapper.find('.search-date');
                var $the_date_str = $date_dom.val();
                $(".nav-header-title-3").html($the_date_str);
            }
            else if($time_type == "month")
            {
                var $month_dom = $datatable_wrapper.find('.search-month');
                var $the_month_str = $month_dom.val();
                $(".nav-header-title-3").html($the_month_str);
            }
            else if($time_type == "period")
            {
                var $start_dom = $datatable_wrapper.find('input[name="search-start"]');
                var $ended_dom = $datatable_wrapper.find('input[name="search-ended"]');
                var $the_start_str = $date_dom.val();
                var $the_ended_str = $date_dom.val();
                $(".nav-header-title-3").html($the_start_str + '-' + $the_ended_str);
            }


            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【刷新】
        $(".main-content").on('click', ".filter-refresh", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【重置】
        $(".main-content").on('click', ".filter-cancel", function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $search_box = $datatable_wrapper.find('.datatable-search-box');

            $search_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $search_box.find(".select2-box").val(-1).trigger("change");
            $search_box.find(".select2-box").select2("val", "");

//            $('select.form-filter').selectpicker('refresh');
            $search_box.find('select.form-filter option').attr("selected",false);
            $search_box.find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#'+$table_id).DataTable().ajax.reload(null,false);
        });
        // 【清空重选】
        $(".main-content").on('click', ".filter-empty", function() {

            let $that = $(this);
            let $datatable_wrapper = $that.closest('.datatable-wrapper');
            let $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            let $search_box = $datatable_wrapper.find('.datatable-search-box');

            $search_box.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $search_box.find(".select2-box").val(-1).trigger("change");
            $search_box.find(".select2-box").select2("val", "");

//            $('select.form-filter').selectpicker('refresh');
            $search_box.find('select.form-filter option').attr("selected",false);
            $search_box.find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【查询】回车
        $(".main-content").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                let $that = $(this);
                let $datatable_wrapper = $that.closest('.datatable-wrapper');
                let $search_box = $datatable_wrapper.find('.datatable-search-box');
                $search_box.find(".filter-submit").click();
            }
        });



        // 【前一月】
        $(".main-content").on('click', ".month-pre", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');

            // var $month_dom = $('input[name="statistic-company-month"]');
            var $month_dom = $('input[name='+$target+']');
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

            // $("#filter-submit-statistic-company-by-month").click();
            // $('#datatable-for-statistic-company').DataTable().ajax.reload();

        });
        // 【后一月】
        $(".main-content").on('click', ".month-next", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');

            // var $month_dom = $('input[name="statistic-company-month"]');
            var $month_dom = $('input[name='+$target+']');
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

            // $("#filter-submit-statistic-company-by-month").click();
            // $('#datatable-for-statistic-company').DataTable().ajax.reload();

        });

        // 【前一天】
        $(".main-content").on('click', ".date-pre", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');

            // var $date_dom = $('input[name="statistic-company-date"]');
            var $date_dom = $('input[name='+$target+']');
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


            // $("#filter-submit-statistic-company-by-day").click();
            // $('#datatable-for-statistic-company').DataTable().ajax.reload();

        });
        // 【后一天】
        $(".main-content").on('click', ".date-next", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');

            // var $date_dom = $('input[name="statistic-company-date"]');
            var $date_dom = $('input[name='+$target+']');
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

            // $("#filter-submit-statistic-company-by-day").click();
            // $('#datatable-for-statistic-company').DataTable().ajax.reload();

        });




    });
</script>