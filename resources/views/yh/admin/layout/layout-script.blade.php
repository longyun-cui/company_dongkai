<script>
    $(function() {


        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });
        $('.month_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM",
            ignoreReadonly: true
        });


        $('.form_datetime').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $(".form_date").datepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            ignoreReadonly: true
        });




        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });


    });
</script>