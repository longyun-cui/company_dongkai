<script>
    $(function() {

        var $company_category = $("input[name=company_category]").val();
        if($company_category == 1)
        {
            $('#select2-superior-company').prop('data-type','company');
            $('#select2-leader').prop('data-type','manager');
            $('.company-type-box').hide();
            $('.select2-superior-box').hide();
        }
        else if($company_category == 11)
        {
            $('#select2-superior-company').prop('data-type','company');
            $('#select2-leader').prop('data-type','supervisor');
            $('.company-type-box').show();
            $('.select2-superior-box').show();
        }
        else if($company_category == 11)
        {
            $('#select2-superior-company').prop('data-type','channel');
            $('#select2-leader').prop('data-type','supervisor');
            $('.company-type-box').hide();
            $('.select2-superior-box').show();
        }


        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-CN'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });


        // 【选择部门类型】
        $("#form-edit-item").on('click', "input[name=company_category]", function() {
            // radio
            var $value = $(this).val();
            if($value == 1)
            {
                $('#select2-superior-company').prop('data-type','company');
                $('#select2-leader').prop('data-type','manager');
                $('.company-type-box').hide();
                $('.select2-superior-box').hide();
            }
            else if($value == 11)
            {
                $('#select2-superior-company').prop('data-type','company');
                $('#select2-leader').prop('data-type','supervisor');
                $('.company-type-box').show();
                $('.select2-superior-box').show();
            }
            else if($value == 21)
            {
                $('#select2-superior-company').prop('data-type','channel');
                $('#select2-leader').prop('data-type','supervisor');
                $('.company-type-box').hide();
                $('.select2-superior-box').show();
            }
            else
            {
                $('#select2-superior-company').prop('data-type','company');
                $('#select2-leader').prop('data-type','manager');
                $('.select2-superior-box').hide();
            }
        });


        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/company/company-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/company/company-list') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        //
        $('#select2-leader').select2({
            ajax: {
                url: "{{ url('/company/company_select2_leader') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-leader').prop('data-type')
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
        $('#select2-superior-company').select2({
            ajax: {
                url: "{{ url('/select2/select2_company') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-superior-company').prop('data-type')
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