<script>
    $(function() {





        // 【质量评估】
        $(".main-content").on('click', ".item-quality-evaluate-submit", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '选择质量！'
                ,content: '<select class="form-control form-filter" name="order-quality" style="width:160px;">'+
                    '<option value ="-1">选择质量</option>'+
                    '<option value ="有效">有效</option>'+
                    '<option value ="无效">无效</option>'+
                    '<option value ="重单">重单</option>'+
                    '<option value ="无法联系">无法联系</option>'+
                    '</select>'
                ,yes: function(index){
                    $.post(
                        {{--"{{ url('/item/delivery-quality-evaluate') }}",--}}
                        "{{ url('/v1/operate/delivery/item-quality-evaluate') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery-quality-evaluate",
                            item_id: $that.attr('data-id'),
                            order_quality: $('select[name="order-quality"]').val()
                        },
                        function(data){
                            layer.close(index);
                            // layer.form.render();
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });



        // 【批量操作】
        $(".main-content").on('click', '.bulk-submit-for-export', function() {
            console.log('.bulk-submit-for-export.click');
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
                // $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $item_category = $that.data('item-category');
            // var $url = url_build('/statistic/statistic-export-for-order-by-ids?item_category='+$item_category+'&ids='+$ids);
            var $url = url_build('/v1/operate/statistic/statistic-export-for-delivery-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-分配状态
        $(".main-content").on('click', '.bulk-submit-for-assign-status', function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            var $assign_status = $datatable_wrapper.find('select[name="bulk-operate-assign-status"]').val();


            layer.msg('确定"批量导出"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/bulk-assign-status') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "bulk-assign-status",
                            item_category: $item_category,
                            ids: $ids,
                            assign_status: $assign_status
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
                                // $('#'+$table_id).DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    if($assign_status == "1")
                                    {
                                        $row.find('td[data-key=assign_status]').html('<small class="btn-xs btn-success">已分配</small>');
                                    }
                                    else if($assign_status == "0")
                                    {
                                        $row.find('td[data-key=assign_status]').html('<small class="btn-xs btn-warning">待分配</small>');
                                    }
                                    else
                                    {
                                    }

                                });
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


                }
            });

        });
        // 【批量操作】批量-指派
        $(".main-content").on('click', '.bulk-submit-for-assign-staff', function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            var $staff = $datatable_wrapper.find('select[name="bulk-operate-staff-id"]');
            var $staff_id = $staff.val();


            layer.msg('确定"批量分派员工"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/bulk-assign-staff') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "bulk-assign-staff",
                            item_category: $item_category,
                            ids: $ids,
                            staff_id: $staff_id
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
                                // $('#'+$table_id).DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $username = $staff.find('option:selected').html();

                                    $row.find('td[data-key=client_staff_id]').html('<a href="javascript:void(0);">'+$username+'</a>');

                                });
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


                }
            });

        });
        // 【批量操作】批量-API推送
        $(".main-content").on('click', '.bulk-submit-for-api-push', function() {

            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);


            layer.msg('确定"批量推送"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/bulk-api-push') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "bulk-api-push",
                            item_category: $item_category,
                            ids: $ids
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
                                // $('#'+$table_id).DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    $row.find('td[data-key=is_api_pushed]').html('<small class="btn-xs btn-success">已推送</small>');

                                });
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


                }
            });

        });
        $(".main-content").on('click', '.bulk-submit-for-api-push1', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);


            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量API推送"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){


                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">耐心等待中</span>',
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



                    $.post(
                        "{{ url('/item/delivery-bulk-api-push') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery-api-push-bulk",
                            ids: $ids
                        },
                        'json'
                    )
                        .done(function($response) {
                            // console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {

                                console.log($response.data.count);
                                layer.msg("推送 "+$response.data.count+" 条！");
                                // $('#datatable_ajax').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $username = $('select[name="bulk-operate-staff-id"]').find('option:selected').html();

                                    $row.find('td[data-key=is_api_pushed]').html('<small class="btn-xs btn-success">已推送</small>');

                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            // console.log('fail');
                            // console.log(jqXHR);
                            // console.log(textStatus);
                            // console.log(errorThrown);

                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            // console.log('always');
                            // console.log(jqXHR);
                            // console.log(textStatus);

                            layer.closeAll('loading');
                        });




                }
            });

        });




        // 【通用】显示详情
        $(".main-content").on('dblclick', ".modal-show-for-item-detail", function() {
            var $that = $(this);
            var $order_category = $(this).data('order-category');
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            var $modal = $('#modal-for-delivery-item-detail');
            $modal.find('.id-title').html('【'+$id+'】');
            $modal.find('.delivery-location-box').html($row.find('[data-key="location"]').html());
            $modal.find('.delivery-client-name-box').html($row.find('[data-key="client_name"]').html());
            $modal.find('.delivery-client-mobile-box').html($row.find('[data-key="client_phone"]').html());
            $modal.find('.delivery-client-wx-box').html($row.find('[data-key="client_wx"]').html());
            $modal.find('.delivery-client-intention-box').html($row.find('[data-key="client_intention"]').html());
            $modal.find('.delivery-teeth-count-box').html($row.find('[data-key="teeth_count"]').html());
            $modal.find('.delivery-description-box').html($row.find('[data-key="description"]').data('value'));
            $modal.find('.delivery-recording-address-box').html('');
            $modal.find('.delivery-recording-address-box').html($row.find('[data-key="description"]').data('recording-address'));

            if($order_category == 1)
            {
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').hide();
                $modal.find('.dental-show').show();
            }
            if($order_category == 11)
            {
                $modal.find('.dental-show').hide();
                $modal.find('.luxury-show').hide();
                $modal.find('.aesthetic-show').show();
            }
            if($order_category == 31)
            {
                $modal.find('.dental-show').hide();
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').show();
            }
            else
            {
                $modal.find('.dental-show').hide();
                $modal.find('.aesthetic-show').hide();
                $modal.find('.luxury-show').hide();
            }

            $modal.modal('show');
        });



        // 【通用】修改记录
        $(".main-content").on('click', ".item-modal-show-for-follow-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            Datatable_Delivery_Follow_Record.init($id);

            $('#modal-for-delivery-follow-record-list').modal('show');
        });






        // 【交付-管理】【客户信息】编辑-显示
        $(".main-content").off('click', ".item-modal-show-for-customer-update").on('click', ".item-modal-show-for-customer-update", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

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
                "{{ url('/v1/operate/delivery/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "delivery",
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
                        var $modal = $('#modal-for-delivery-customer-update');
                        // $modal.find('.box-title').html('更新客户信息【'+$that.attr('data-id')+'】');
                        $modal.find('.id-title').html('【'+$id+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="mobile"]').val($response.data.mobile);
                        $modal.find('input[name="customer_remark"]').val($response.data.customer_remark);
                        $modal.find('input[name="is_wx"][value="'+$response.data.is_wx+'"]').prop('checked', true);
                        $modal.find('input[name="is_come"][value="'+$response.data.is_come+'"]').prop('checked', true);
                        // $modal.find('select[name="department_id"]').val($response.data.department_id);

                        if($response.data.client_contact_er)
                        {
                            $modal.find('select[name="client_contact_id"]').append(new Option($response.data.client_contact_er.name, $response.data.client_contact_id, true, true)).trigger('change');
                        }

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
        // 【交付-管理】【客户信息】编辑-提交
        $(".main-content").off('click', "#form-submit-for-delivery-customer-update").on('click', "#form-submit-for-delivery-customer-update", function() {
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
                url: "{{ url('/v1/operate/delivery/item-customer-save') }}",
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
                        form_reset('#form-for-delivery-customer-update');

                        $('#modal-for-delivery-customer-update').modal('hide');
                        // $('#modal-for-delivery-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-delivery-customer-update").ajaxSubmit(options);
        });




        // 【交付-管理】【上门状态】编辑-显示
        $(".main-content").off('click', ".item-modal-show-for-come-update").on('click', ".item-modal-show-for-come-update", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

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
                "{{ url('/v1/operate/delivery/item-get') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    item_type: "delivery",
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
                        var $modal = $('#modal-for-delivery-come-update');

                        // $modal.find('.box-title').html('更新上门状态【'+$that.attr('data-id')+'】');
                        $modal.find('.id-title').html('【'+$id+'】');
                        $modal.find('input[name="operate[type]"]').val('edit');
                        $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));

                        $modal.find('input[name="is_come"][value="'+$response.data.is_come+'"]').prop('checked', true);
                        $modal.find('input[name="come_datetime"]').val($response.data.come_datetime);


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
        // 【交付-管理】【上门状态】编辑-提交
        $(".main-content").off('click', "#form-submit-for-delivery-come-update").on('click', "#form-submit-for-delivery-come-update", function() {
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
                url: "{{ url('/v1/operate/delivery/item-come-save') }}",
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
                        form_reset('#form-for-delivery-come-update');

                        $('#modal-for-delivery-come-update').modal('hide');
                        // $('#modal-for-delivery-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-delivery-come-update").ajaxSubmit(options);
        });




        // 【交付-管理】【添加跟进记录】添加-显示
        $(".main-content").off('click', ".item-modal-show-for-follow-create").on('click', ".item-modal-show-for-follow-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form-for-delivery-follow-create');

            var $modal = $('#modal-for-delivery-follow-create');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.modal('show');
            // $('#modal-for-delivery-follow-create').modal({show: true,backdrop: 'static'});
            // $('.modal-backdrop').each(function() {
            //     $(this).attr('id', 'id_' + Math.random());
            // });
        });
        // 【交付-管理】【添加跟进记录】编辑-提交
        $(".main-content").off('click', "#form-submit-for-delivery-follow-create").on('click', "#form-submit-for-delivery-follow-create", function() {
            var $that = $(this);

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
                url: "{{ url('/v1/operate/delivery/item-follow-save') }}",
                type: "post",
                dataType: "json",
                // data: { _token: $('meta[name="_token"]').attr('content') },
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
                        form_reset('#form-for-delivery-follow-create');

                        $('#modal-for-delivery-follow-create').modal('hide');
                        // $('#modal-for-delivery-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-delivery-follow-create").ajaxSubmit(options);
        });




        // 【交付-管理】【添加成交记录】添加-显示
        $(".main-content").off('click', ".item-modal-show-for-trade-create").on('click', ".item-modal-show-for-trade-create", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            form_reset('#form-for-delivery-trade-create');

            var $modal = $('#modal-for-delivery-trade-create');
            $modal.find('.id-title').html('【'+$id+'】');

            $modal.find('input[name="operate[id]"]').val($that.attr('data-id'));
            $modal.find('.follow-create-delivery-id').html($id);

            $modal.modal('show');
        });
        // 【交付-管理】【添加成交记录】编辑-提交
        $(".main-content").off('click', "#item-submit-for-delivery-trade-create").on('click', "#item-submit-for-delivery-trade-create", function() {
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
                url: "{{ url('/v1/operate/delivery/item-trade-save') }}",
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
                        form_reset('#form-for-delivery-trade-create');

                        $('#modal-for-delivery-trade-create').modal('hide');
                        // $('#modal-for-delivery-trade-create').modal('hide').on("hidden.bs.modal", function () {
                        //     $("body").addClass("modal-open");
                        // });

                        // $('#'+$table_id).DataTable().ajax.reload(null,false);
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
            $("#form-for-delivery-trade-create").ajaxSubmit(options);
        });




        $('.modal-wrapper').on('hide.bs.modal', function (event) {
            $('.datatable-wrapper').removeClass('operating');
            $('.datatable-wrapper').find('tr.operating').removeClass('operating');
        });
        // 【modal】取消
        $(".main-content").on('click', ".modal-cancel", function() {
            var $that = $(this);
            var $modal = $that.closest('.modal-wrapper');
            $modal.modal('hide');

            // $modal.modal('hide').on("hidden.bs.modal", function () {
            //     $("body").addClass("modal-open");
            // });
        });



    });
</script>