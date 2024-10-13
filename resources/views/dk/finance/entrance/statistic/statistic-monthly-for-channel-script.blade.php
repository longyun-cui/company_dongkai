<script>
    $(function() {

        statistic_get_data_for_finance_of_dealings();

        // 【搜索】
        $(".main-content").on('click', "#filter-submit-for-finance", function() {

            var $statistic_title = '';

            var $company = $('select[name=finance-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=finance-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=finance-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=finance-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            statistic_get_data_for_finance_of_dealings();
            @if(in_array($me->user_type,[0,1,9,11,31]))
            $('#datatable_ajax_channel').DataTable().ajax.reload();
            @endif
            $('#datatable_ajax_project').DataTable().ajax.reload();
            
        });
        // 【搜索】【回车】
        $("#statistic-for-finance").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#statistic-for-finance").find(".filter-submit").click();
            }
        });
        // 【清空重选】
        $("#statistic-for-finance").on('click', ".filter-empty", function() {
            $("#statistic-for-finance").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $(".select2-box").val(-1).trigger("change");
            $(".select2-box").select2("val", "");

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-finance").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-finance").find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【刷新】
        $("#statistic-for-finance").on('click', ".filter-refresh", function() {
            $('#datatable_ajax').DataTable().ajax.reload(null,false);
        });
        // 【重置】
        $("#statistic-for-finance").on('click', ".filter-cancel", function() {
            $("#statistic-for-finance").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#statistic-for-finance").find('select.form-filter option').attr("selected",false);
            $("#statistic-for-finance").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $("#statistic-for-finance").find('input[name=service-time-type]').val('all');

            var $month_dom = $('input[name="finance-month"]');
            var $month_default = $month_dom.attr('data-default')
            $month_dom.val($month_default);

            var $date_dom = $('input[name="finance-date"]');
            var $date_default = $date_dom.attr('data-default')
            $date_dom.val($date_default);

            $(".statistic-title").html("全部");

            // $("#filter-submit-for-finance").click();
            statistic_get_data_for_finance_of_dealings();
            $('#datatable_ajax_channel').DataTable().ajax.reload();
            $('#datatable_ajax_project').DataTable().ajax.reload();

        });


        // 【财务报表】按【全部】搜索
        $(".main-content").on('click', "#filter-submit-for-finance-by-all", function() {

            $("#statistic-for-finance").find('input[name=finance-time-type]').val('all');

            $(".statistic-time-type-title").html('全部');
            $(".statistic-time-title").html('');

            filter_submit_for_finance();

            // $("#filter-submit-for-finance").click();
            // statistic_get_data_for_finance_of_dealings();
            // $('#datatable_ajax_channel').DataTable().ajax.reload();
            // $('#datatable_ajax_project').DataTable().ajax.reload();
        });
        // 【财务报表】按【月】搜索
        $(".main-content").on('click', "#filter-submit-for-finance-by-month", function() {

            $("#statistic-for-finance").find('input[name=finance-time-type]').val('month');

            var $statistic_title = '';

            var $company = $('select[name=finance-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=finance-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=finance-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=finance-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按月');
            var $month_dom = $('input[name="finance-month"]');
            var $the_month_str = $month_dom.val();
            $(".statistic-time-title").html('（'+$the_month_str+'月）');

            filter_submit_for_finance();

            // $("#filter-submit-for-finance").click();
            // statistic_get_data_for_finance_of_dealings();
            // $('#datatable_ajax_channel').DataTable().ajax.reload();
            // $('#datatable_ajax_project').DataTable().ajax.reload();
        });
        // 【财务报表】按【天】搜索
        $(".main-content").on('click', "#filter-submit-for-finance-by-date", function() {

            $("#statistic-for-finance").find('input[name=finance-time-type]').val('date');

            var $statistic_title = '';

            var $company = $('select[name=finance-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=finance-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=finance-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=finance-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按天');
            var $date_dom = $('input[name="finance-date"]');
            var $the_date_str = $date_dom.val();
            $(".statistic-time-title").html('（'+$the_date_str+'）');

            filter_submit_for_finance();

            // $("#filter-submit-for-finance").click();
            // statistic_get_data_for_finance_of_dealings();
            // $('#datatable_ajax_channel').DataTable().ajax.reload();
            // $('#datatable_ajax_project').DataTable().ajax.reload();
        });
        // 【财务报表】按【时间段】搜索
        $(".main-content").on('click', "#filter-submit-for-finance-by-period", function() {

            $("#statistic-for-finance").find('input[name=finance-time-type]').val('period');

            var $statistic_title = '';

            var $company = $('select[name=finance-company]').find("option:selected");
            if($company.val() != "-1")
            {
                var $company_title = $company.text();
                $statistic_title = $company_title;
            }

            var $channel = $('select[name=finance-channel]').find("option:selected");
            if($channel.val() != "-1")
            {
                var $channel_title = $channel.text();
                $statistic_title = $channel_title;
            }

            var $business = $('select[name=finance-business]').find("option:selected");
            if($business.val() != "-1")
            {
                var $business_title = $business.text();
                $statistic_title = $business_title;
            }

            var $project = $('select[name=finance-project]').find("option:selected");
            if($project.val() != "-1")
            {
                var $project_title = $project.text();
                $statistic_title = $project_title;
            }

            if($statistic_title)
            {
                $statistic_title = "【" + $statistic_title + "】";
                $(".statistic-title").html($statistic_title);
            }

            $(".statistic-time-type-title").html('按时间段查询');
            var $date_start = $('input[name="finance-start"]');
            var $date_ended = $('input[name="finance-ended"]');
            var $the_date_str = $date_start.val() + " - " + $date_ended.val();
            $(".statistic-time-title").html('（'+$the_date_str+'）');

            filter_submit_for_finance();

            // $("#filter-submit-for-finance").click();
            // statistic_get_data_for_finance_of_dealings();
            // $('#datatable_ajax_channel').DataTable().ajax.reload();
            // $('#datatable_ajax_project').DataTable().ajax.reload();
        });




        // 【财务报表】【前一月】
        $(".main-content").on('click', ".month-pick-pre-for-finance", function() {

            var $month_dom = $('input[name="finance-month"]');
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

            $("#filter-submit-for-finance-by-month").click();

        });
        // 【财务报表】【后一月】
        $(".main-content").on('click', ".month-pick-next-for-finance", function() {

            var $month_dom = $('input[name="finance-month"]');
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

            $("#filter-submit-for-finance-by-month").click();

        });

        // 【财务报表】【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-finance", function() {

            var $date_dom = $('input[name="finance-date"]');
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


            $("#filter-submit-for-finance-by-date").click();

        });
        // 【财务报表】【后一天】
        $(".main-content").on('click', ".date-pick-next-for-finance", function() {

            var $date_dom = $('input[name="finance-date"]');
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

            $("#filter-submit-for-finance-by-date").click();

        });




        $(".finance-company").on("select2:select", function() {
            $(".finance-channel").val(-1).trigger("change");
            $(".finance-business").val(-1).trigger("change");
            $(".finance-project").val(-1).trigger("change");
        });
        $(".finance-channel").on("select2:select", function() {
            $(".finance-company").val(-1).trigger("change");
            $(".finance-business").val(-1).trigger("change");
            $(".finance-project").val(-1).trigger("change");
        });
        $(".finance-business").on("select2:select", function() {
            $(".finance-company").val(-1).trigger("change");
            $(".finance-channel").val(-1).trigger("change");
            $(".finance-project").val(-1).trigger("change");
        });
        $(".finance-project").on("select2:select", function() {
            $(".finance-company").val(-1).trigger("change");
            $(".finance-business").val(-1).trigger("change");
            $(".finance-channel").val(-1).trigger("change");
        });



    });


    function filter_submit_for_finance()
    {

        var $statistic_title = '';

        var $company = $('select[name=finance-company]').find("option:selected");
        if($company.val() != "-1")
        {
            var $company_title = $company.text();
            $statistic_title = $company_title;
        }

        var $channel = $('select[name=finance-channel]').find("option:selected");
        if($channel.val() != "-1")
        {
            var $channel_title = $channel.text();
            $statistic_title = $channel_title;
        }

        var $business = $('select[name=finance-business]').find("option:selected");
        if($business.val() != "-1")
        {
            var $business_title = $business.text();
            $statistic_title = $business_title;
        }

        var $project = $('select[name=finance-project]').find("option:selected");
        if($project.val() != "-1")
        {
            var $project_title = $project.text();
            $statistic_title = $project_title;
        }

        if($statistic_title)
        {
            $statistic_title = "【" + $statistic_title + "】";
            $(".statistic-title").html($statistic_title);
        }

        statistic_get_data_for_finance_of_dealings();
        @if(in_array($me->user_type,[0,1,9,11,31]))
        $('#datatable_ajax_channel').DataTable().ajax.reload();
        @endif
        $('#datatable_ajax_project').DataTable().ajax.reload();

    }


    function statistic_get_data_for_finance_of_dealings()
    {
        var $data = new Object();
        $.ajax({
            type:"post",
            dataType:'json',
            async:false,
            url: "{{ url('/statistic/statistic-get-data-for-finance-of-dealings') }}",
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                company: $('select[name="finance-company"]').val(),
                channel: $('select[name="finance-channel"]').val(),
                business: $('select[name="finance-business"]').val(),
                project: $('select[name="finance-project"]').val(),
                time_type: $('input[name="finance-time-type"]').val(),
                month: $('input[name="finance-month"]').val(),
                date: $('input[name="finance-date"]').val(),
                assign_start: $('input[name="finance-start"]').val(),
                assign_ended: $('input[name="finance-ended"]').val(),
                operate:"statistic-get-data-for-finance-of-dealings"
            },
            success:function(data){
                if(!data.success)
                {
                    layer.msg(data.msg);
                }
                else
                {
                    $data = data.data;
                }
            }
        });

        $(".recharge_total").html(moneyAddCommas($data.statistics_data.recharge_total));
        $(".recharge_refund_total").html(moneyAddCommas($data.statistics_data.recharge_refund_total));
        $(".using_settled_total").html(moneyAddCommas($data.statistics_data.using_settled_total));
        $(".using_bad_total").html(moneyAddCommas($data.statistics_data.using_bad_total));
        $(".using_refund_amount").html(moneyAddCommas($data.statistics_data.using_refund_total));

    }


</script>