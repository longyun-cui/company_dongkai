<script>
    $(function() {




        var $team_type = $("input[name=team_type]").val();
        if($team_type == 11)
        {
            $('#select2-leader').prop('data-type','manager');
            $('.select2-superior-box').hide();
        }
        else if($team_type == 21)
        {
            $('#select2-leader').prop('data-type','supervisor');
            $('.select2-superior-box').show();
        }




        // 选择类型
        $(".main-content").on('change', 'input[name="team_type"]', function() {
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
                    url: "{{ url('/team/team_select2_leader') }}",
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

        // 【选择用户类型】
        $("#form-for-team-edit").on('change', "input[name=team_category]", function() {


            // radio
            var $value = $(this).val();

            $('.superior-box').hide();

            // select2
            // 销毁Select2实例
            $('#team-edit-select2-department').select2('destroy');
            // 清除残留的DOM元素（可选但推荐）
            $('.team-edit-select2-department-container').remove();
            // 重新初始化
            $('#team-edit-select2-department').select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2-department') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            department_category: $value,
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
                // dropdownParent: $('#modal-for-team-edit'), // 替换为你的模态框 ID
                minimumInputLength: 0,
                theme: 'classic'
            });

        });



    });
</script>