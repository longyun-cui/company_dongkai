<script>
    $(function() {





        // 【质量评估】
        $(".main-content").on('click', ".item-quality-evaluate-submit", function() {
            var $that = $(this);
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
                        "{{ url('/item/delivery-quality-evaluate') }}",
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
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });



        // 【批量操作】
        $(".main-content").on('click', '.bulk-submit-for-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
                // $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
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




    });
</script>