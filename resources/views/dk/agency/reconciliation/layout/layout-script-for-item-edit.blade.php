<script>
    $(function() {

        $("#multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });


        // 【通用】编辑-现实-创建
        $(".main-content").on('click', ".item-create-show", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('.box-title').html($title);
            $modal.find('.edit-submit').data('datatable-list-id',$table_id);
            $modal.modal('show');
        });
        // 【通用】编辑-取消
        $(".main-content").on('click', ".edit-cancel", function() {
            var $that = $(this);
            var $modal_wrapper = $that.parents('.modal-wrapper');

            var $form_id = $modal_wrapper.find('from').filter('[id][id!=""]').attr("id");
            form_reset('#'+$form_id);

            $modal_wrapper.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });




        // 【部门-管理】编辑-显示-编辑
        $(".main-content").on('click', ".project-edit-show", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

            var $data = new Object();

            //
            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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

            //
            $.post(
                "{{ url('/reconciliation/v1/operate/project/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "project",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        var $modal = $('#modal-for-reconciliation-project-edit');
                        $modal.find('.box-title').html('编辑项目【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="name"]').val($response.data.name);
                        $modal.find('input[name="cooperative_unit_price"]').val($response.data.cooperative_unit_price);

                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('fail');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('always');
                    layer.closeAll('loading');
                });

        });
        // 【部门-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-reconciliation-project", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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
                url: "{{ url('/reconciliation/v1/operate/project/item-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#form-edit-for-reconciliation-project');

                        $('#modal-for-reconciliation-project-edit').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('always');
                    layer.closeAll('loading');
                }


            };
            $("#form-for-reconciliation-project-edit").ajaxSubmit(options);
        });



        // 【部门-管理】编辑-显示-编辑
        $(".main-content").on('click', ".daily-edit-show", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

            var $data = new Object();

            //
            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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

            //
            $.post(
                "{{ url('/reconciliation/v1/operate/daily/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "project",
                    item_id: $that.data('id')
                },
                'json'
            )
                .done(function($response, status, jqXHR) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        var $modal = $('#modal-for-reconciliation-daily-edit');
                        $modal.find('.box-title').html('编辑每日结算【'+$that.attr('data-id')+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="assign_date"]').val($response.data.assign_date);
                        $modal.find('input[name="delivery_quantity"]').val($response.data.delivery_quantity);
                        $modal.find('input[name="channel_commission"]').val($response.data.channel_commission);
                        $modal.find('input[name="daily_cost"]').val($response.data.daily_cost);

                        var $datatable_wrapper = $that.closest('.datatable-wrapper');
                        var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
                        $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);

                        $modal.modal('show');
                    }
                })
                .fail(function(jqXHR, status, error) {
                    console.log('fail');
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, status) {
                    console.log('always');
                    layer.closeAll('loading');
                });

        });
        // 【部门-管理】编辑-提交
        $(".main-content").on('click', "#edit-submit-for-reconciliation-daily", function() {
            var $that = $(this);
            var $table_id = $that.data('datatable-list-id');

            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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
                url: "{{ url('/reconciliation/v1/operate/daily/item-save') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function (response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!response.success)
                    {
                        layer.msg(response.msg);
                    }
                    else
                    {
                        layer.msg(response.msg);

                        // 重置输入框
                        form_reset('#form-edit-for-reconciliation-daily');

                        $('#modal-for-reconciliation-daily-edit').modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        $('#'+$table_id).DataTable().ajax.reload(null,false);
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('always');
                    layer.closeAll('loading');
                }


            };
            $("#form-for-reconciliation-daily-edit").ajaxSubmit(options);
        });





        // 【通用】【字段-编辑】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-field-set", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $modal = $('#modal-for-field-set');
            $modal.attr('data-datatable-id',$table_id);
            $modal.attr('data-datatable-row-index',$that.data('row-index'));

            var $form = $('#form-for-field-set');
            form_reset('#form-for-field-set');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');
            $datatable_wrapper.find('td').removeClass('operating');
            $that.addClass('operating');


            $('.field-set-item-name').html($datatable_wrapper.attr("data-item-name"));
            $('.field-set-item-id').html($that.attr("data-id"));
            $('.field-set-column-name').html($that.attr("data-column-name"));

            $('input[name="operate-type"]').val($that.attr('data-operate-type'));

            $('input[name="item-category"]').val($datatable_wrapper.data("datatable-item-category"));
            $('input[name="item-id"]').val($that.attr("data-id"));

            $('input[name="column-key"]').val($that.attr("data-key"));
            $('input[name="column-key2"]').val($that.attr("data-key2"));

            $modal.find('.column-value').val('').hide();
            // $modal.find('.select-assistant').val('').hide();
            if($modal.find('select[name="field-set-select-value"]').data('select2'))
            {
                $modal.find('select[name="field-set-select-value"]').select2('destroy');
            }
            if($modal.find('select[name="field-set-select-value2"]').data('select2'))
            {
                $modal.find('select[name="field-set-select-value2"]').select2('destroy');
            }
            $form.find('.select2-container').remove();
            $form.find('.select2-dropdown').remove();
            $form.find('.select2-results__options').remove();
            $form.find('.radio-wrapper').html('');

            $modal.find(".select2-city").off('change');
            $('select[name=field-set-select-value2]').html('').hide();
            $('select[name=field-set-select-value]').removeClass('select2-city');

            var $column_type = $that.attr('data-column-type');
            $('input[name="column-type"]').val($column_type);
            if($column_type == "text")
            {
                $modal.find('input[name="field-set-text-value"]').val($that.attr("data-value")).show();
            }
            else if($column_type == "textarea")
            {
                $modal.find('textarea[name="field-set-textarea-value"]').val($that.attr("data-value")).show();
            }
            else if($column_type == "radio")
            {
                if($that.attr("data-key") == "is_distributive_condition")
                {
                    var $option_html = $('#option-list-for-is_distributive_condition').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('input[name=option_is_distributive_condition][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
                else if($that.attr("data-key") == "is_wx")
                {
                    var $option_html = $('#option-list-for-is-wx').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('.radio-wrapper').find('input[name="field-set-radio-value"][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
                else if($that.attr("data-key") == "is_distributive")
                {
                    var $option_html = $('#option-list-for-is-distributive').html();
                    $modal.find('.radio-wrapper').html($option_html).show();
                    $modal.find('.radio-wrapper').find('input[name="field-set-radio-value"][value="'+$that.attr("data-value")+'"]').prop("checked",true);
                }
            }
            else if($column_type == "select")
            {
                // console.log("select");

                if($that.attr("data-key") == "location_city")
                {
                    $('select[name=info-select-set-column-value]').removeClass('select2-city');
                    $('select[name=info-select-set-column-value2]').removeClass('select2-district');
                    var $option_html = $('#location-city-option-list').html();

                    $('#modal-body-for-info-select-set').find('select[name=info-select-set-column-value2]').show();
                }
                else if($that.attr("data-key") == "teeth_count")
                {
                    var $option_html = $('#option-list-for-teeth-count').html();
                }
                else if($that.attr("data-key") == "channel_source")
                {
                    var $option_html = $('#option-list-for-channel-source').html();
                }
                else if($that.attr("data-key") == "inspected_result")
                {
                    var $option_html = $('#option-list-for-inspected-result').html();
                }
                else if($that.attr("data-key") == "client_id")
                {
                    var $option_html = $('#option-list-for-client').html();
                }
                else if($that.attr("data-key") == "client_intention")
                {
                    var $option_html = $('#option-list-for-client-intention').html();
                }
                else if($that.attr("data-key") == "client_type")
                {
                    var $option_html = $('#option-list-for-client-type').html();
                }
                $('select[name=field-set-select-value]').html($option_html).show();
                $('select[name=field-set-select-value]').find("option[value='"+$that.attr("data-value")+"']").prop("selected",true);

            }
            else if($column_type == "select2")
            {
                // console.log("select2");
                // console.log($that.attr("data-key"));

                var $select_value2 = $modal.find('select[name="field-set-select-value2"]');
                // console.log($select_value2);
                // $select_value2.hide();

                if ($select_value2.data('select2'))
                {
                    $select_value2.select2('destroy'); // 销毁旧实例
                }
                else
                {
                }
                console.log($that.attr('data-option-name'));

                if($that.attr("data-key") == "location_city")
                {


                    // console.log("location_city11");
                    // var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    // var $option_html = $('#location-city-option-list').html();
                    // $select2_dom.html($option_html);
                    //
                    // var $select2_dom2 = $modal.find('select[name="field-set-select-value2"]');
                    // $select2_dom2.show();
                    // var $existed_class = $select2_dom.data('class');
                    // $select2_dom.attr('data-class','select2-city');
                    // $select2_dom.removeClass($existed_class).addClass('select2-');
                    // select2_location_district_init($select2_dom2);
                    // $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');



                    $('select[name="field-set-select-value2"]').show();

                    var $option_html = $('#location-city-option-list').html();
                    $('select[name="field-set-select-value"]').html($option_html);
                    $('select[name="field-set-select-value"]').find("option[value='"+$that.attr("data-value")+"']").prop("selected",true);

                    $('select[name="field-set-select-value"]').removeClass('select2-project').addClass('select2-city');
                    $('select[name="field-set-select-value2"]').addClass('select2-district');

                    $('select[name="field-set-select-value2"]').show();

                    // var $city_index = $(".select2-city").find('option:selected').attr('data-index');
                    // $(".select2-district").html('<option value="">选择区划</option>');
                    // $.each($district_list[$city_index], function($i,$val) {
                    //     $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                    // });
                    // $('.select2-district').find("option[value='"+$that.attr("data-value2")+"']").attr("selected","selected");

                    $('.select2-city').select2();
                    $('.select2-district').select2();
                    $('.select2-district').val($that.attr("data-value2")).trigger('change');


                    var $city_value = $that.attr("data-value");
                    // console.log($that.attr("data-value"));
                    $('.select2-district').select2({
                        ajax: {
                            url: "/district/district_select2_district?district_city=" + $city_value,
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
                    $('.select2-district').find("option[value='"+$that.attr("data-value2")+"']").prop("selected",true);
                    $('.select2-district').append(new Option($that.attr("data-value2"), $that.attr("data-value2"), true, true)).trigger('change');


                    $(".select2-city").change(function() {

                        $that = $(this);

                        var $city_index = $that.find('option:selected').attr('data-index');

                        $(".select2-district").html('<option value="">选择区划</option>');

                        // $.each($district_list[$city_index], function($i,$val) {
                        //
                        //     $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                        // });
                        //
                        // $('.select2-district').select2();


                        var $city_value = $(this).val();
                        $('.select2-district').select2({
                            ajax: {
                                url: "/district/district_select2_district?district_city=" + $city_value,
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




                }
                else if($that.attr("data-key") == "client_id")
                {
                    var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    var $existed_class = $select2_dom.data('class');
                    $select2_dom.attr('data-class','select2-client');
                    $select2_dom.removeClass($existed_class).addClass('select2-client');
                    select2_client_init($select2_dom);
                    $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');
                    $('select[name=field-set-select-value2]').html('').hide();
                }
                else if($that.attr("data-key") == "project_id")
                {
                    var $select2_dom = $modal.find('select[name="field-set-select-value"]');
                    var $existed_class = $select2_dom.data('class');
                    $select2_dom.attr('data-class','select2-project');
                    $select2_dom.removeClass($existed_class).addClass('select2-project');
                    select2_project_init($select2_dom);
                    $select2_dom.append(new Option($that.attr("data-option-name"), $that.attr("data-value"), true, true)).trigger('change');

                    if ($('select[name=field-set-select-value2]').data('select2'))
                    {
                        // $select_value2.select2('destroy'); // 销毁旧实例
                        $('select[name=field-set-select-value2]').select2('destroy');
                    }

                }
            }


            $modal.modal('show');
        });
        // 【通用】【字段-编辑】【取消】
        $(".main-content").on('click', "#edit-cancel-for-field-set", function() {
            var that = $(this);
            $('#modal-for-field-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });

            form_reset('#modal-for-field-set');
        });
        // 【通用】【字段-编辑】【提交】
        $(".main-content").on('click', "#edit-submit-for-field-set", function() {
            var $that = $(this);
            var $modal = $('#modal-for-field-set');
            var $table_id = $modal.data('datatable-id');

            var $row = $('.datatable-wrapper.operating').find('tr.operating');
            var $td = $('.datatable-wrapper.operating').find('td.operating');

            //
            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在提交</span>',
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

            //
            var options = {
                url: "{{ url('/reconciliation/v1/operate/universal/field-set') }}",
                type: "post",
                dataType: "json",
                // target: "#div2",
                // clearForm: true,
                // restForm: true,
                success: function ($response, status, xhr, $form) {
                    // 请求成功时的回调
                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg($response.msg);

                        console.log($response.data);
                        // $('#'+$table_id).DataTable().ajax.reload(null,false);

                        var $form = $('#form-for-field-set');
                        var item_category = $form.find('input[name="item-category"]').val();
                        var column_key = $form.find('input[name="column-key"]').val();
                        console.log(column_key);
                        if(column_key == 'location_city')
                        {
                            $td.data('value2',$response.data.data.value2);
                        }
                        else if(column_key == 'is_wx')
                        {
                            var $radio_value = $form.find('input[name="field-set-radio-value"]').val();
                            console.log($radio_value);
                            if($radio_value == 0)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('--');
                            }
                            else if($radio_value == 1)
                            {
                                $row.find('[data-key="is_wx"]').attr('data-value',$radio_value).html('<small class="btn-xs btn-primary">是</small>');
                            }
                            else
                            {

                            }
                        }


                        $('#'+$table_id).DataTable().ajax.reload(null,false);

                        // var $rowIndex = $modal.data('datatable-row-index');
                        // $('#'+$table_id).DataTable().row($rowIndex).data($response.data.data).invalidate().draw(false);

                        $td.attr('data-value',$response.data.data.value);
                        $td.attr('data-option-name',$response.data.data.text);
                        $td.html($response.data.data.text);



                        // 重置输入框
                        form_reset('#form-for-field-set');

                        $modal.modal('hide').on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });
                    }
                },
                error: function(xhr, status, error, $form) {
                    // 请求失败时的回调
                    console.log('error');
                    layer.closeAll('loading');
                },
                complete: function(xhr, status, $form) {
                    // 无论成功或失败都会执行的回调
                    console.log('always');
                    layer.closeAll('loading');
                }


            };
            $("#form-for-field-set").ajaxSubmit(options);

        });



    });


    function form_reset($form_id)
    {
        var $form = $($form_id);
        $form.find('textarea.form-control, input.form-control, select').each(function () {
            $(this).val("");
            $(this).val($(this).data('default'));
        });
        $form.find(".select2-box").val(-1).trigger("change");
        $form.find(".select2-box").select2("val", "");

        $form.find('select option').prop("selected",false);
        $form.find('select').find('option:eq(0)').prop('selected', true);


        // $form.find(".select2-box").val(-1).trigger("change");
        // $form.find(".select2-box").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box");
        $.each( $form.find(".select2-reset"), function(index, element) {
            select2FirstOptionSelected(element);
        });


        // $form.find(".select2-box-c").val(-1).trigger("change");
        // $form.find(".select2-box-c").val("-1").trigger("change");
        // selectFirstOption($form_id + " .select2-box-c");
        $.each( $form.find(".select2-reset"), function(index, element) {
            select2FirstOptionSelected(element);
        });

        $form.find(".select2-multi-reset").val([]).trigger('change');
        $form.find(".select2-multi-reset").val(null).trigger('change');
        $form.find(".select2-multi-reset").empty().trigger('change');

        $form.find('select option').prop("selected",false);
        $form.find('select').find('option:eq(0)').prop('selected', true);
    }

    //
    function select2FirstOptionSelected(dom)
    {
        var $dom = $(dom);
        var firstVal = $dom.find('option:first').val();
        if(firstVal)
        {
            $dom.val(firstVal).trigger('change');
        }
        else
        {
            $dom.val(null).trigger('change');
        }
    }


</script>
