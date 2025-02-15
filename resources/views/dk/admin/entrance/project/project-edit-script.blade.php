<script>
    $(function() {

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

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/project-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/item/project-list') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        //
        $('#select2-client').select2({
            ajax: {
                url: "{{ url('/item/item_select2_client') }}",
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
            templateSelection: function(data, container) {
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-inspector').select2({
            ajax: {
                url: "{{ url('/item/item_select2_user?type=inspector') }}",
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
            templateSelection: function(data, container) {
                $(data.element).attr("data-name",data.driver_name);
                $(data.element).attr("data-phone",data.driver_phone);
                $(data.element).attr("data-sub-name",data.sub_driver_name);
                $(data.element).attr("data-sub-phone",data.sub_driver_phone);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        //
        $('#select2-team').select2({
            ajax: {
                url: "{{ url('/item/item_select2_team?type=district') }}",
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