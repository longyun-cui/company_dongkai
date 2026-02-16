<script>
    $(function() {

        var $location_type = $("input[name=location_type]").val();
        if($location_type == 11)
        {
            $('#select2-leader').prop('data-type','manager');
            $('.select2-superior-box').hide();
        }
        else if($location_type == 21)
        {
            $('#select2-leader').prop('data-type','supervisor');
            $('.select2-superior-box').show();
        }



        // 【选择部门类型】
        $(".main-content").on('change', 'input[name="location_type"]', function() {
            // radio
            var $value = $(this).val();
            if($value == 11)
            {
                $('#select2-leader').prop('data-type','manager');
                $('.select2-superior-box').hide();
            }
            else if($value == 21)
            {
                $('#select2-leader').prop('data-type','supervisor');
                $('.select2-superior-box').show();
            }
            else
            {
                $('#select2-leader').prop('data-type','manager');
                $('.select2-superior-box').hide();
            }

            $('#select2-leader').select2({
                ajax: {
                    url: "{{ url('/location/location_select2_leader') }}",
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
        });





        // 添加or编辑
        $("#edit-submit--for--location").on('click', function() {
            var options = {
                url: "{{ url('/location/location-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/location/location-list') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        //
        $('#select2-leader').select2({
            ajax: {
                url: "{{ url('/location/location_select2_leader') }}",
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
        $('#select2-superior-location').select2({
            ajax: {
                url: "{{ url('/location/location_select2_superior_location') }}",
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



    });
</script>