<script>
    $(function() {

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


        // 【选择用户类型】
        $("#form-for-staff-edit").on('change', "input[name=user_type]", function() {

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