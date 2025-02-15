<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/user/staff-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.href = "{{ url('/user/staff-list-for-all') }}";
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        // 【选择用户类型】
        $("#form-edit-item").on('click', "input[name=user_type-]", function() {
            // checkbox
//            if($(this).is(':checked'))
//            {
//                $('.time-show').show();
//            }
//            else
//            {
//                $('.time-show').hide();
//            }

            // $("#select2-superior").find("option[value=0]").attr("selected",true);
            // radio
            var $value = $(this).val();
            // if($value == 77 || $value == 84 || $value == 88)
            if($value == 77)
            {
                // $('.superior-box').show();
                $('.superior-box').hide();
            }
            else
            {
                $('.superior-box').hide();
            }

            if($value == 77)
            {
                $('#select2-superior').prop('data-type','inspector');
            }
            else if($value == 84)
            {
                $('#select2-superior').prop('data-type','customer_service_supervisor');
            }
            else if($value == 88)
            {
                $('#select2-superior').prop('data-type','customer_service');
            }
            else
            {
                $('#select2-superior').prop('data-type','');
            }
            console.log($('#select2-superior').prop('data-type'));

            //
            $('#select2-superior').select2({
                ajax: {
                    url: "{{ url('/user/user_select2_superior') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: params.page,
                            type: $('#select2-superior').prop('data-type')
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

        //
        $('#select2-superior').select2({
            ajax: {
                url: "{{ url('/user/user_select2_superior') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        type: $('#select2-superior').prop('data-type')
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

        // var $user_type = $("input[name=user_type]").val();
        //
        // if($user_type == 77 || $user_type == 84 || $user_type == 88)
        // {
        //     $('.superior-box').show();
        // }
        // else
        // {
        //     $('.superior-box').hide();
        // }
        //
        // if($user_type == 77)
        // {
        //     $('#select2-superior').prop('data-type','inspector');
        // }
        // else if($user_type == 84)
        // {
        //     $('#select2-superior').prop('data-type','customer_service_supervisor');
        // }
        // else if($user_type == 88)
        // {
        //     $('#select2-superior').prop('data-type','customer_service');
        // }
        // else
        // {
        //     $('#select2-superior').prop('data-type','');
        // }

        // console.log($("input[name=user_type]").val())
        // console.log($('#select2-superior').prop('data-type'));
        // console.log($("#select2-superior").find('option:checked').val());






        // 【选择用户类型】
        $("#form-edit-item").on('click', "input[name=user_type]", function() {

            // radio
            var $value = $(this).val();
            if($value == 11)
            {
                $('.department-box').hide();
            }
            else if($value == 41)
            {
                $('.department-box').show();
                $('.department-group-box').hide();
            }
            else if($value == 81)
            {
                $('.department-box').show();
                $('.department-group-box').hide();
            }
            else if($value == 84)
            {
                $('.department-box').show();
                $('.department-group-box').show();
            }
            else if($value == 88)
            {
                $('.department-box').show();
                $('.department-group-box').show();
            }
            else if($value == 71)
            {
                $('.department-box').show();
                $('.department-group-box').hide();
            }
            else if($value == 77)
            {
                $('.department-box').show();
                $('.department-group-box').hide();
            }
            else if($value == 61)
            {
                $('.department-box').hide();
            }
            else if($value == 66)
            {
                $('.department-box').hide();
            }

            $('.superior-box').hide();


            // if($value == 81 || $value == 84 || $value == 88)
            // {
            //     $('.department-box').show();
            //     $('.superior-box').hide();
            //
            //     if($value == 81)
            //     {
            //         $('.department-group-box').hide();
            //     }
            //     else if($value == 81)
            //     {
            //         $('.department-group-box').hide();
            //     }
            //     else if($value == 84)
            //     {
            //         $('.department-group-box').show();
            //     }
            //     else if($value == 88)
            //     {
            //         $('.department-group-box').show();
            //     }
            // }
            // else
            // {
            //     $('.department-box').hide();
            //     if($value == 77)
            //     {
            //         // $('.superior-box').show();
            //         $('.superior-box').hide();
            //     }
            //     else $('.superior-box').hide();
            // }

        });

        var $user_type = $("input[name=user_type]:checked").val();
        console.log($user_type);

        // if($user_type == 81 || $user_type == 84 || $user_type == 88)
        // {
        //     $('.department-box').show();
        //     $('.superior-box').hide();
        // }
        // else
        // {
        //     $('.department-box').hide();
        // }

        if($user_type == 41)
        {
            $('.department-box').show();
            $('.department-group-box').hide();
        }
        else if($user_type == 81)
        {
            $('.department-box').show();
            $('.department-group-box').hide();
        }
        else if($user_type == 84)
        {
            $('.department-box').show();
            $('.department-group-box').show();
        }
        else if($user_type == 88)
        {
            $('.department-box').show();
            $('.department-group-box').show();
        }
        else if($user_type == 71)
        {
            $('.department-box').show();
            $('.department-group-box').hide();
        }
        else if($user_type == 77)
        {
            $('.department-box').show();
            $('.department-group-box').hide();
        }
        else if($user_type == 61)
        {
            $('.department-box').hide();
        }
        else if($user_type == 66)
        {
            $('.department-box').hide();
        }


        //
        $('#select2-department-district').select2({
            ajax: {
                url: "{{ url('/user/user_select2_department?type=district') }}",
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
        $("#select2-department-district").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                //
                // 清空原有选项 得到select标签对象 Jquery写法
                // var $select = $('#select2-department-group')[0];
                // $select.length = 0;

                $('#select2-department-group').html(''); // 清空原有选项

                // 去除选中值
                // $('#select2-department-group').val(null).trigger('change');
                // $('#select2-department-group').val("").trigger('change');

                $('#select2-department-group').select2({
                    ajax: {
                        url: "{{ url('/user/user_select2_department?type=group') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page,
                                superior_id: $id
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
            }
        });


        $('#select2-department-group').select2({
            ajax: {
                url: "{{ url('/user/user_select2_department?type=group') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page,
                        @if(in_array($me->user_type,[41,81]))
                        superior_id: {{ $me->department_district_id or 0 }}
                                @else
                            superior_id: {{ $data->department_district_id or 0 }}
                    @endif
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