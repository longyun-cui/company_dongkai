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
        $("#form-for-staff-edit").on('change', "input[name=staff_category]", function() {


            // radio
            var $value = $(this).val();
            console.log($value);
            // if($value == 11)
            // {
            //     $('.department-box').hide();
            // }
            // else if($value == 41)
            // {
            //     $('.department-box').show();
            //     $('.department-group-box').hide();
            // }
            // else if($value == 81)
            // {
            //     $('.department-box').show();
            //     $('.department-group-box').hide();
            // }
            // else if($value == 84)
            // {
            //     $('.department-box').show();
            //     $('.department-group-box').show();
            // }
            // else if($value == 88)
            // {
            //     $('.department-box').show();
            //     $('.department-group-box').show();
            // }

            $('.superior-box').hide();



            // select2
            // 销毁Select2实例
            $('#staff-edit-select2-department').select2('destroy');
            // 清除残留的DOM元素（可选但推荐）
            $('.staff-edit-select2-department-container').remove();
            // 重新初始化
            $('#staff-edit-select2-department').select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2-department') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            keyword: params.term, // search term
                            page: params.page,
                            // staff_category: $('input[name="staff_category"]').val()
                            department_category: $value
                            // team_type: this.data('team-type')
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
                // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
                minimumInputLength: 0,
                theme: 'classic'
            });

        });


        // select2 选择项目
        $('#staff-edit-select2-department').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-department') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        department_category: this.parents('form').find('input[name="staff_category"]').val(),
                        keyword: params.term, // search term
                        page: params.page
                        // staff_category: $('input[name="staff_category"]').val()
                        // team_type: this.data('team-type')
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
            // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#staff-edit-select2-department').on('select2:select', function(e) {
            console.log(e);
            // select2
            // 销毁Select2实例
            $('#staff-edit-select2-team').select2('destroy');
            // 清除残留的DOM元素（可选但推荐）
            $('.staff-edit-select2-team-container').remove();
            // 重新初始化
            $('#staff-edit-select2-team').select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2-team') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            department_id: this.parents('form').find('select[name="department_id"]').val(),
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
        //
        $('#staff-edit-select2-team').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-team1') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        department_id: this.parents('form').find('input[name="department_id"]').val(),
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


    });
</script>