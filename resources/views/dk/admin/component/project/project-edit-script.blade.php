<script>
    $(function() {


        //
        $('#select2-client').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2_client') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        // user_category: this.data('user-category'),
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
            dropdownParent: $('#modal-for-project-edit'), // 替换为你的模态框 ID
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