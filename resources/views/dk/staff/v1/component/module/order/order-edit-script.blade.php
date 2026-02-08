<script>


    // document.addEventListener('DOMContentLoaded', function() {
    //     var dropdown = new bootstrap.Dropdown(
    //         document.querySelector('.dropdown-toggle'), {
    //             autoClose: 'outside' // 或 false
    //         }
    //     );
    // });

    $(function() {


        // 添加or编辑
        $("#edit-item-submit").on('click', function() {
            var options = {
                url: "{{ url('/item/order-edit') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);

                        if($.getUrlParam('referrer')) location.href = decodeURIComponent($.getUrlParam('referrer'));
                        else if(document.referrer) location.href = document.referrer;
                        else location.href = "{{ url('/item/order-list-for-all') }}";
                        // history.go(-1);
                    }
                }
            };
            $("#form-edit-item").ajaxSubmit(options);
        });


        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【选择车辆所属】
        $("#form--for--order-item-edit").on('click', "input[name=car_owner_type]", function() {
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
            if($value == 1)
            {
                $('.internal-car').show();
                $('.external-car').hide();
            }
            else if($value == 11)
            {
                $('.external-car').show();
                $('.internal-car').hide();
            }
            else
            {
                $('.external-car').hide();
                $('.internal-car').hide();
            }
        });

        // 【选择线路类型】
        $("#form--for--order-edit").on('click', "input[name=route_type]", function() {
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




        // select2 选择客户
        $('#order-edit-select2-client').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-client') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
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
            // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
            minimumInputLength: 0,
            theme: 'classic'
        });

        // select2 选择项目
        $('#order-edit-select2-project').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-project') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
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
            // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('#order-edit-select2-project').on('select2:select', function(e) {
            $('input[name=transport_departure_place]').val(e.params.data.transport_departure_place);
            $('input[name=transport_destination_place]').val(e.params.data.transport_destination_place);
            $('input[name=transport_distance]').val(e.params.data.transport_distance);
            $('input[name=transport_time_limitation]').val(e.params.data.transport_time_limitation);
            $('input[name=freight_amount]').val(e.params.data.freight_amount);
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




        //
        $('.order-edit-select2-car').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-car') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
                        keyword: params.term, // search term
                        page: params.page,
                        car_type: this.data('car-type')
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
                // 检查是否为有效选中项（避免空数据打印）
                // if (data.id && data.text)
                // {
                //     console.log("Selected:", data.id, data.text);
                //     console.log(data);
                // }
                // if(data.driver_er)
                // {
                //     $(data.element).attr("data-id",data.driver_id);
                //     $(data.element).attr("data-name",data.driver_er.driver_name);
                //     $(data.element).attr("data-phone",data.driver_er.driver_phone);
                //     $(data.element).attr("data-sub-name",data.driver_er.sub_driver_name);
                //     $(data.element).attr("data-sub-phone",data.driver_er.sub_driver_phone);
                // }
                // else
                // {
                //     $(data.element).attr("data-id",data.driver_id);
                //     $(data.element).attr("data-name",data.linkman_name);
                //     $(data.element).attr("data-phone",data.linkman_phone);
                //     $(data.element).attr("data-sub-name",'');
                //     $(data.element).attr("data-sub-phone",'');
                // }
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
            minimumInputLength: 0,
            width: '100%',
            theme: 'classic'
        });
        $('.order-edit-select2-car').on('select2:select', function(e) {
            console.log("用户选择了:", e.params.data); // 仅触发1次
        });
        $("#select2-car").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                $('input[name=driver_name]').val($(this).find('option:selected').attr('data-driver-name'));
                $('input[name=driver_phone]').val($(this).find('option:selected').attr('data-driver-phone'));
                $('input[name=copilot_name]').val($(this).find('option:selected').attr('data-copilot-name'));
                $('input[name=copilot_phone]').val($(this).find('option:selected').attr('data-copilot-phone'));
            }

            var $driver_id = $(this).find('option:selected').attr('data-id');
            var $driver_name = $(this).find('option:selected').attr('data-name');
            var option = new Option($driver_name, $driver_id);
            option.selected = true;
            console.log(option);
            $("#form-edit-item").find("select[name=driver_id]").append(option);
            $("#form-edit-item").find("select[name=driver_id]").trigger("change");
        });


        //
        $('#select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
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
                $(data.element).attr("data-driver-name",data.driver_name);
                $(data.element).attr("data-driver-phone",data.driver_phone);
                $(data.element).attr("data-copilot-name",data.copilot_name);
                $(data.element).attr("data-copilot-phone",data.copilot_phone);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        $("#select2-driver").on("select2:select",function(){
            var $id = $(this).val();
            if($id > 0)
            {
                $('input[name=driver_name]').val($(this).find('option:selected').attr('data-driver-name'));
                $('input[name=driver_phone]').val($(this).find('option:selected').attr('data-driver-phone'));
                $('input[name=copilot_name]').val($(this).find('option:selected').attr('data-copilot-name'));
                $('input[name=copilot_phone]').val($(this).find('option:selected').attr('data-copilot-phone'));
            }
        });
        // select2 选择司机
        $('.order-edit-select2-driver').select2({
            ajax: {
                url: "{{ url('/v1/operate/select2/select2-driver') }}",
                type: 'post',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: $('meta[name="_token"]').attr('content'),
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
                $(data.element).attr("data-driver-name",data.driver_name);
                $(data.element).attr("data-driver-phone",data.driver_phone);
                $(data.element).attr("data-copilot-name",data.copilot_name);
                $(data.element).attr("data-copilot-phone",data.copilot_phone);
                return data.text;
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            // dropdownParent: $('#modal-for-order-edit'), // 替换为你的模态框 ID
            minimumInputLength: 0,
            theme: 'classic'
        });
        $('.order-edit-select2-driver-').on('select2:select', function(e) {
            // console.log("用户选择了:", e.params.data); // 仅触发1次
            var $that = $(this);
            var $modal = $that.parents('.modal-wrapper');

            var $that_name = $that.attr('name');
            if($that_name == 'driver_id')
            {
                $modal.find('input[name=driver_name]').val(e.params.data.text);
                $modal.find('input[name=driver_phone]').val(e.params.data.driver_phone);
            }
            else if($that_name == 'copilot_id')
            {
                $modal.find('input[name=copilot_name]').val(e.params.data.text);
                $modal.find('input[name=copilot_phone]').val(e.params.data.driver_phone);
            }
        });




    });


</script>