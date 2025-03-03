<script>
    $(function() {


        // 【选择部门类型】
        $("#form-for-company-edit").on('change', "input[name=company_category]", function() {
            // radio
            var $value = $(this).val();
            console.log($value);
            if($value == 1)
            {
                $('.company-type-box').hide();
                $('#select2-superior-company').prop('data-type','company');
                $('.company-select2-superior-box').hide();
                $('#select2-leader').prop('data-type','manager');
            }
            else if($value == 11)
            {
                $('.company-type-box').show();
                $('#select2-superior-company').prop('data-type','company');
                $('.company-select2-superior-box').show();
                $('#select2-leader').prop('data-type','supervisor');
            }
            else if($value == 21)
            {
                $('.company-type-box').hide();
                $('#select2-superior-company').prop('data-type','channel');
                $('.company-select2-superior-box').show();
                $('#select2-leader').prop('data-type','supervisor');
            }
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