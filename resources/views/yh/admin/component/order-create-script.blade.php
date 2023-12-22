<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【选择车辆所属】
        $("#form-edit-item").on('click', "input[name=car_owner_type]", function() {
            // checkbox
//            if($(this).is(':checked'))
//            {
//                $('.time-show').show();
//            }
//            else
//            {
//                $('.time-show').hide();
//            }
            // radio
            var $value = $(this).val();
            if($value == 1 || $value == 11 || $value == 41)
            {
                $('.inside-car').show();
                $('.outside-car').hide();
            }
            else
            {
                $('.outside-car').show();
                $('.inside-car').hide();
            }
        });

        // 【选择线路类型】
        $("#form-edit-item").on('click', "input[name=route_type]", function() {
            // radio
            var $value = $(this).val();
            if($value == 1)
            {
                $('.route-fixed-box').show();
                $('.route-temporary-box').hide();


                var $select2_route_val = $('#select2-route').val();
                console.log($select2_route_val);
                var $select2_route_selected = $('#select2-route').find('option:selected');
                if($select2_route_selected.val() > 0)
                {
                    $('#order-price').attr('readonly','readonly').val($select2_route_selected.attr('data-price'));
                    $('input[name=departure_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-departure'));
                    $('input[name=destination_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-destination'));
                    $('input[name=stopover_place]').attr('readonly','readonly').val($select2_route_selected.attr('data-stopover'));
                    $('input[name=travel_distance]').attr('readonly','readonly').val($select2_route_selected.attr('data-distance'));
                    $('input[name=time_limitation_prescribed]').attr('readonly','readonly').val($select2_route_selected.attr('data-prescribed'));
                }
            }
            else
            {
                $('.route-temporary-box').show();
                $('.route-fixed-box').hide();

                $('#order-price').removeAttr('readonly');
                $('input[name=departure_place]').removeAttr('readonly');
                $('input[name=destination_place]').removeAttr('readonly');
                $('input[name=stopover_place]').removeAttr('readonly');
                $('input[name=travel_distance]').removeAttr('readonly');
                $('input[name=time_limitation_prescribed]').removeAttr('readonly');
            }
        });


        $('input[name=assign_date]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });

        $('input[name=should_departure]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('input[name=should_arrival]').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });




        //
        $('#select2-project').select2({
            ajax: {
                url: "{{ url('/item/item_select2_project') }}",
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




        //
        $('#select2-client').select2({
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


        //
        $('#select2-circle').select2({
            ajax: {
                url: "{{ url('/item/order_select2_circle') }}",
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


        //
        $('#select2-route').select2({
            ajax: {
                url: "{{ url('/item/order_select2_route') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

//                    var $o = [];
//                    var $lt = data;
//                    $.each($lt, function(i,item) {
//                        item.id = item.id;
//                        item.text = item.text;
//                        item.data_id = item.text;
//                        $o.push(item);
//                    });
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
                $(data.element).attr("data-price",data.amount_with_cash);
                $(data.element).attr("data-departure",data.departure_place);
                $(data.element).attr("data-destination",data.destination_place);
                $(data.element).attr("data-stopover",data.stopover_place);
                $(data.element).attr("data-distance",data.travel_distance);
                $(data.element).attr("data-prescribed",data.time_limitation_prescribed);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $("#select2-route").on("select2:select",function(){
            var $id = $(this).val();
            var $price = $(this).find('option:selected').attr('data-price');
            if($id > 0)
            {
                $('#order-price').attr('readonly','readonly').val($price);
                $('input[name=departure_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-departure'));
                $('input[name=destination_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-destination'));
                $('input[name=stopover_place]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-stopover'));
                $('input[name=travel_distance]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-distance'));
                $('input[name=time_limitation_prescribed]').attr('readonly','readonly').val($(this).find('option:selected').attr('data-prescribed'));
            }
            else
            {
                $('#order-price').removeAttr('readonly');
                $('input[name=departure_place]').removeAttr('readonly');
                $('input[name=destination_place]').removeAttr('readonly');
                $('input[name=stopover_place]').removeAttr('readonly');
                $('input[name=travel_distance]').removeAttr('readonly');
                $('input[name=time_limitation_prescribed]').removeAttr('readonly');
            }
        });







        // 【添加工单】取消
        $(".main-content").on('click', "#edit-item-cancel", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.assign_date').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-body-for-order-create').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });

        // 【添加工单】提交
        $("#edit-item-submit").on('click', function() {

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在上传</span>',
                success: function (layer) {
                    layer.find('.layui-layer-content').css({
                        'padding-top': '40px',
                        'width': '100px',
                    });
                    layer.find('.loadtip').css({
                        'font-size':'20px',
                        'margin-left':'-18px'
                    });
                }
            });

            var options = {
                url: "{{ url('/item/order-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (data) {

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        layer.msg(data.msg);

                        // 重置输入框
                        $("#form-edit-item").find(".select2-container").val(0).trigger("change");
                        $("#form-edit-item").find('input[type=text]').each(function () {
                            $(this).val($(this).attr('data-default'));
                        });

                        $("#form-edit-item").find("input[name=car_owner_type][value='1']").click();
                        $("#form-edit-item").find("input[name=route_type][value='1']").click();
                        $("#form-edit-item").find("input[name=receipt_need][value='0']").click();

                        $('#order-price').removeAttr('readonly');
                        $("#form-edit-item").find('input[name=departure_place]').removeAttr('readonly');
                        $("#form-edit-item").find('input[name=destination_place]').removeAttr('readonly');
                        $("#form-edit-item").find('input[name=stopover_place]').removeAttr('readonly');
                        $("#form-edit-item").find('input[name=travel_distance]').removeAttr('readonly');
                        $("#form-edit-item").find('input[name=time_limitation_prescribed]').removeAttr('readonly');


                        $('#modal-body-for-order-create').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });
                        $('#datatable_ajax').DataTable().ajax.reload(null,false);
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });

    });
</script>
