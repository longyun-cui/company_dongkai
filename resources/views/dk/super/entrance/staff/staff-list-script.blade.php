<script>
    $(function() {

        // 【搜索】
        $(".item-main-body").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $(".item-main-body").on('click', ".filter-cancel", function() {
            $('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//                $('select.form-filter').selectpicker('refresh');
            $('select.form-filter option').attr("selected",false);
            $('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【查询】回车
        $(".item-main-body").on('keyup', ".item-search-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#filter-submit").click();
            }
        });


        // 【登录】
        $("#item-main-body").on('click', ".staff--item-login--submit", function() {
            var $that = $(this);
            $.post(
                "{{ url('/staff/staff--item-login') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    staff_id: $that.attr('data-id')
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        console.log(data);
//                        window.open('/');
                        var temp_window=window.open();
                        temp_window.location = data.data.url;
                    }
                },
                'json'
            );
        });


    });
</script>