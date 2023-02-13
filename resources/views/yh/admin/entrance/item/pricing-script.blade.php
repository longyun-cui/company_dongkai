<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-pricing-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $("#datatable-for-pricing-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-pricing-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-pricing-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-pricing-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【查询】回车
        $("#datatable-for-pricing-list").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-pricing-list").find(".filter-submit").click();
            }
        });




        // 【提示】
        $(".main-content").on('dblclick', ".alert-published-first", function() {
            layer.msg('未发布内容，可以编辑，或请先发布！');
        });




        // 【编辑】
        $(".main-content").on('click', ".item-edit-link", function() {
            var $that = $(this);
            window.location.href = "/item/pricing-edit?id="+$that.attr('data-id');
        });




        // 【删除】
        $(".main-content").on('click', ".item-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【弃用】
        $(".main-content").on('click', ".item-abandon-submit", function() {
            var $that = $(this);
            layer.msg('确定要"弃用"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-abandon') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-abandon",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【发布】
        $(".main-content").on('click', ".item-publish-submit", function() {
            var $that = $(this);
            layer.msg('确定要"发布"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-publish",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【完成】
        $(".main-content").on('click', ".item-complete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"完成"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-complete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-complete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【管理员-删除】
        $(".main-content").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-admin-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【管理员-恢复】
        $(".main-content").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定要"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-admin-restore",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【管理员-永久删除】
        $(".main-content").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定要"永久删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-admin-delete-permanently",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });


        // 【管理员-启用】
        $(".main-content").on('click', ".item-admin-enable-submit", function() {
            var $that = $(this);
            layer.msg('确定"启用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-admin-enable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【管理员-禁用】
        $(".main-content").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            layer.msg('确定"禁用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "pricing-admin-disable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });









        // 【修改-文本-text-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-text-set", function() {
            var $that = $(this);
            $('.info-text-set-title').html($that.attr("data-id"));
            $('.info-text-set-column-name').html($that.attr("data-name"));
            $('input[name=info-text-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-text-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-text-set-operate-type]').val($that.attr('data-operate-type'));
            if($that.attr('data-text-type') == "textarea")
            {
                $('input[name=info-text-set-column-value]').val('').hide();
                $('textarea[name=info-textarea-set-column-value]').text($that.attr("data-value")).show();
            }
            else
            {
                $('textarea[name=info-textarea-set-column-value]').val('').hide();
                $('input[name=info-text-set-column-value]').val($that.attr("data-value")).show();
            }

            $('#item-submit-for-info-text-set').attr('data-text-type',$that.attr('data-text-type'));

            $('#modal-body-for-info-text-set').modal('show');
        });
        // 【修改-文本-text-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-text-set", function() {
            var that = $(this);
            $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
            $('input[name=info-text-set-column-value]').val('');
            $('textarea[name=info-textarea-set-column-value]').val('');
        });
        // 【修改-文本-text-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-text-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-text-set-column-key"]').val();
            if($that.attr('data-text-type') == "textarea")
            {
                var $column_value = $('textarea[name="info-textarea-set-column-value"]').val();
            }
            else
            {
                var $column_value = $('input[name="info-text-set-column-value"]').val();
            }
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-info-text-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-text-set-operate"]').val(),
                            item_id: $('input[name="info-text-set-item-id"]').val(),
                            operate_type: $('input[name="info-text-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【修改-时间-time-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-time-set", function() {
            var $that = $(this);
            $('.info-time-set-title').html($that.attr("data-id"));
            $('.info-time-set-column-name').html($that.attr("data-name"));
            $('input[name=info-time-set-operate-type]').val($that.attr('data-operate-type'));
            $('input[name=info-time-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-time-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-time-set-time-type]').val($that.attr('data-time-type'));
            if($that.attr('data-time-type') == "datetime")
            {
                $('input[name=info-date-set-column-value]').hide();
                $('input[name=info-time-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type')).show();
            }
            else if($that.attr('data-time-type') == "date")
            {
                $('input[name=info-time-set-column-value]').hide();
                $('input[name=info-date-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type')).show();
            }

            $('#modal-body-for-info-time-set').modal('show');
        });
        // 【修改-时间-time-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-time-set", function() {
            var that = $(this);
            $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-时间-time-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-time-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-time-set-column-key"]').val();
            var $time_type = $('input[name="info-time-set-time-type"]').val();
            var $column_value = '';
            if($time_type == "datetime")
            {
                $column_value = $('input[name="info-time-set-column-value"]').val();
            }
            else if($time_type == "date")
            {
                $column_value = $('input[name="info-date-set-column-value"]').val();
            }
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/route-info-time-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-time-set-operate"]').val(),
                            item_id: $('input[name="info-time-set-item-id"]').val(),
                            operate_type: $('input[name="info-time-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                            time_type: $time_type
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【修改-radio-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-radio-set", function() {

            $('select[name=info-radio-set-column-value]').attr("selected","");
            $('select[name=info-radio-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-radio-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-radio-set-title').html($that.attr("data-id"));
            $('.info-radio-set-column-name').html($that.attr("data-name"));
            $('input[name=info-radio-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-radio-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-radio-set-operate-type]').val($that.attr('data-operate-type'));


            if($that.attr("data-key") == "receipt_need")
            {
                var $option_html = $('#receipt_need-option-list').html();
            }
            $('.radio-box').html($option_html);
            $('input[name=receipt_need][value="'+$that.attr("data-value")+'"]').attr("checked","checked");


            $('#modal-body-for-info-radio-set').modal('show');

        });
        // 【修改-radio-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-radio-set", function() {
            var that = $(this);
            $('#modal-body-for-info-radio-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-radio-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-radio-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-radio-set-column-key"]').val();
            var $column_value = $('#modal-info-radio-set-form').find('input:radio:checked').val();
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-info-radio-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-radio-set-operate"]').val(),
                            item_id: $('input[name="info-radio-set-item-id"]').val(),
                            operate_type: $('input[name="info-radio-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-radio-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【修改-select-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select-set", function() {


            $('select[name=info-select-set-column-value]').attr("selected","");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-select-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-select-set-title').html($that.attr("data-id"));
            $('.info-select-set-column-name').html($that.attr("data-name"));
            $('input[name=info-select-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
//            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));


            $('select[name=info-select-set-column-value]').removeClass('select2-car').removeClass('select2-client');
            if($that.attr("data-key") == "receipt_status")
            {
                var $option_html = $('#receipt_status-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_type")
            {
                var $option_html = $('#trailer_type-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_length")
            {
                var $option_html = $('#trailer_length-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_volume")
            {
                var $option_html = $('#trailer_volume-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_weight")
            {
                var $option_html = $('#trailer_weight-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_axis_count")
            {
                var $option_html = $('#trailer_axis_count-option-list').html();
            }
            $('select[name=info-select-set-column-value]').html($option_html);
            $('select[name=info-select-set-column-value]').find("option[value='"+$that.attr("data-value")+"']").attr("selected","selected");


            $('#modal-body-for-info-select-set').modal('show');



        });
        // 【修改-select2-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select2-set", function() {


            $('select[name=info-select-set-column-value]').attr("selected","");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-select-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-select-set-title').html($that.attr("data-id"));
            $('.info-select-set-column-name').html($that.attr("data-name"));
            $('input[name=info-select-set-item-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-body-for-info-select-set').modal('show');


            if($that.attr("data-key") == "client_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-car').addClass('select2-client');
                $('.select2-client').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_client') }}",
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
            }
            else if($that.attr("data-key") == "car_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').addClass('select2-car');
                $('.select2-car').select2({
                    ajax: {
                        url: "{{ url('/item/order_list_select2_car?car_type=car') }}",
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
            }
            else if($that.attr("data-key") == "trailer_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').addClass('select2-car');
                $('.select2-car').select2({
                    ajax: {
                        url: "{{ url('/item/order_list_select2_car?car_type=trailer') }}",
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
            }

        });
        // 【修改-select-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-select-set", function() {
            var that = $(this);
            $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-select-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-select-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-select-set-column-key"]').val();
            var $column_value = $('select[name="info-select-set-column-value"]').val();
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/pricing-info-select-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-select-set-operate"]').val(),
                            item_id: $('input[name="info-select-set-item-id"]').val(),
                            operate_type: $('input[name="info-select-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 【修改记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });








        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });


        $('.form_datetime').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $(".form_date").datepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            ignoreReadonly: true
        });




        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });

    });
</script>