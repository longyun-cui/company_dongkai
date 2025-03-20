<script>
    $(function() {

        // 【通用【搜索】
        $(".main-content").on('click', ".filter-submit-c", function() {
            var $that = $(this);
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $id = $module_wrapper.attr("id");


            var $time_type = $that.data('time-type');
            $search_wrapper.find('.time-type').val($time_type);

            var functionName = $module_wrapper.data('function');
            // var params = $(this).data('params') || []; // 获取参数数组
            var params = ['#'+$id]; // 获取参数数组

            var func = window[functionName];
            if (typeof func === 'function') {
                func.apply(null, params); // 将参数数组展开传递
            }

        });
        // 【通用【刷新】
        $(".main-content").on('click', ".filter-refresh-c", function() {
            var $that = $(this);
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");
            $search_wrapper.find(".filter-submit-c").click();
        });
        // 【通用【重置】
        $(".main-content").on('click', ".filter-cancel-c", function() {

            var $that = $(this);
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });
            $search_wrapper.find(".select2-box-c").val(-1).trigger("change");
            $search_wrapper.find(".select2-box-c").select2("val", "");

            $search_wrapper.find('select.form-filter-c option').prop("selected", false);
            $search_wrapper.find('select.form-filter-c').find('option:eq(0)').prop('selected', true);

            $search_wrapper.find(".filter-submit-c").click();
        });
        // 【通用【清空重选】
        $(".main-content").on('click', ".filter-empty-c", function() {

            var $that = $(this);
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
                $(this).val($(this).data("default"));
            });
            $search_wrapper.find(".select2-box-c").val(-1).trigger("change");
            $search_wrapper.find(".select2-box-c").select2("val", "");

            $search_wrapper.find('select.form-filter-c option').prop("selected", false);
            $search_wrapper.find('select.form-filter-c').find('option:eq(0)').prop('selected', true);
        });
        // 【通用【查询】回车
        $(".main-content").on('keyup', ".filter-keyup-c", function(event) {
            if(event.keyCode ==13)
            {
                var $that = $(this);
                var $search_wrapper = $that.parents('.search-wrapper');
                var $module_wrapper = $that.parents('.module-wrapper');
                var $module_id = $module_wrapper.attr("id");
                $search_wrapper.find(".filter-submit-c").click();
            }
        });


        // 【通用【前一月】
        $(".main-content").on('click', ".month-pre-c", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('.time-type').val('month');

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

            $search_wrapper.find('.filter-submit-c').click();

        });
        // 【通用【后一月】
        $(".main-content").on('click', ".month-next-c", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('.time-type').val('month');

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

            $search_wrapper.find('.filter-submit-c').click();
        });

        // 【通用【前一天】
        $(".main-content").on('click', ".date-pre-c", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('.time-type').val('date');

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

            $search_wrapper.find('.filter-submit-c').click();
        });
        // 【通用【后一天】
        $(".main-content").on('click', ".date-next-c", function() {
            var $that = $(this);
            var $target = $that.attr('data-target');
            var $search_wrapper = $that.parents('.search-wrapper');
            var $module_wrapper = $that.parents('.module-wrapper');
            var $module_id = $module_wrapper.attr("id");

            $search_wrapper.find('.time-type').val('date');

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

            $search_wrapper.find('.filter-submit-c').click();
        });


    });
</script>