<script>
    $(function() {




        // 【编辑】
        $(".main-content").on('click', ".by-item-create-show", function() {
            var $that = $(this);
            $('#modal-body-for-by-create').modal('show');
        });

        // 【编辑】
        $(".main-content").on('click', ".by-item-create-link", function() {
            var $that = $(this);
            var $url = "/item/by-create?&referrer="+encodeURIComponent(window.location.href);
            // window.location.href = $url;
            window.open($url);
        });

        // 【编辑】
        $(".main-content").on('click', ".by-item-edit-link", function() {
            var $that = $(this);
            var $url = "/item/by-edit?id="+$that.attr('data-id')+"&referrer="+encodeURIComponent(window.location.href);
            window.location.href = $url;
            // window.open($url);
        });




        // 【预处理】提交
        $(".main-content").on('click', ".by-item-preprocess-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

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
                "{{ url('/v1/operate/by/item-preprocess') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-preprocess",
                    item_type: "by",
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
                        $($table).DataTable().ajax.reload(null,false);
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




        // 【获取】内容详情
        $(".main-content").on('click', ".by-modal-show-for-item-detail", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            console.log();
            var $data = new Object();
            {{--$.ajax({--}}
            {{--    type:"post",--}}
            {{--    dataType:'json',--}}
            {{--    async:false,--}}
            {{--    url: "{{ url('/item/by-get-html') }}",--}}
            {{--    data: {--}}
            {{--        _token: $('meta[name="_token"]').attr('content'),--}}
            {{--        operate:"item-get",--}}
            {{--        order_id: $that.attr('data-id')--}}
            {{--    },--}}
            {{--    success:function(data){--}}
            {{--        if(!data.success) layer.msg(data.msg);--}}
            {{--        else--}}
            {{--        {--}}
            {{--            $data = data.data;--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}

//            $('input[name=id]').val($that.attr('data-id'));
            $('input[name=info-set-by-id]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            $('.info-body').html($data.html);

            var $modal = $('#modal-body-for-info-detail');

            $modal.find('.item-detail-project .item-detail-text').html($row.find('td[data-key=project_id]').attr('data-value'));
            $modal.find('.item-detail-client .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-detail-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-detail-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-detail-wx-id .item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-detail-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-detail-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-detail-description .item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.modal('show');

        });
        // 【取消】内容详情
        $(".main-content").on('click', ".by-modal-cancel-for-item-detail", function() {
            var that = $(this);
            $('#modal-body-for-info-detail').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });


        // 【获取】内容详情-审核
        $(".main-content").on('click', ".by-modal-show-for-item-inspected", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $row = $that.parents('tr');
            $table.find('tr').removeClass('inspecting');
            $row.addClass('inspecting');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            $('input[name="detail-inspected-by-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#by-modal-body-for-item-inspected');
            $modal.attr('data-datatable-id',$table_id);

            var $item_info = $row.find('td[data-key=item_info]');
            var $client_name = $item_info.data('client-name');
            var $client_phone = $item_info.data('client-phone');
            var $is_wx = $item_info.data('is-wx');

            $modal.find('.item-inspected-client .item-detail-text').html($client_name + " - " + $client_phone);
            // $modal.find('.item-inspected-client-name .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            // $modal.find('.item-inspected-client-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-inspected-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-inspected-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-inspected-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-inspected-recording .item-detail-text').html('');
            $modal.find('.item-inspected-recording .item-detail-text').html($row.find('[data-key="item_info"]').attr('data-recording-address'));


            var $inspected_result = $row.find('td[data-key=inspected_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-inspected-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-inspected-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);

            // $modal.find('input[name="recording-quality"]').val('0');
            var $recording_quality = $row.find('td[data-key=recording_quality]').attr('data-value');
            $modal.find('input[name="recording-quality"][value='+$recording_quality+']').prop('checked', true);

            var $inspected_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-inspected-description"]').val('');
            $modal.find('textarea[name="detail-inspected-description"]').val($inspected_description);

            $modal.modal('show');

        });
        // 【取消】内容详情-审核
        $(".main-content").on('click', ".by-modal-cancel-for-item-inspected", function() {
            var that = $(this);
            var $modal = $('#by-modal-body-for-item-inspected');
            $modal.find('select[name="item-inspected-result"]').prop("checked", false);
            $modal.find('select[name="item-inspected-result"]').find('option').attr("selected",false);
            $modal.find('select[name="item-inspected-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="item-inspected-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-审核
        $(".main-content").on('click', ".by-modal-summit-for-item-inspected", function() {
            var $that = $(this);
            var $modal = $('#by-modal-body-for-item-inspected');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="item-inspected-by-id"]').val();
            var $inspected_result = $('select[name="item-inspected-result"]').val();
            var $inspected_description = $('textarea[name="item-inspected-description"]').val();
            var $recording_quality = $('input[name="item-inspected-recording-quality"]:checked').val();
            // console.log($recording_quality);

            //
            $.post(
                "{{ url('/v1/operate/by/item-inspect') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-inspect",
                    item_id: $('input[name="item-inspected-by-id"]').val(),
                    inspected_result: $('select[name="item-inspected-result"]').val(),
                    inspected_description: $('textarea[name="item-inspected-description"]').val(),
                    recording_quality: $('input[name="recording-quality"]:checked').val()
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
                        $($table).DataTable().ajax.reload(null,false);
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



            $.post(
                "{{ url('/v1/operate/by/item-inspect') }}",
                {
                },
                function(data){
                    // layer.close(index);
                    // layer.form.render();
                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        layer.msg(data.msg);

                        $(".item-cancel-for-detail-inspected").click();
                        // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);

                        var $result_html = '--';
                        if($inspected_result == "通过" || $inspected_result == "内部通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+$inspected_result+'</small>';
                        }
                        else if($inspected_result == "拒绝")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if($inspected_result == "重复")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+$inspected_result+'</small>';
                        }

                        var $row = $table.find('tr.inspecting');
                        $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-blue">已审核</small>');
                        $row.find('td[data-key=recording_quality]').attr('data-value',$recording_quality);
                        if($recording_quality == 0)
                        {
                            $row.find('td[data-key=recording_quality]').html('<small class="btn-xs bg-blue">合格</small>');
                        }
                        else if($recording_quality == 1)
                        {
                            $row.find('td[data-key=recording_quality]').html('<small class="btn-xs bg-green">优秀</small>');
                        }
                        else if($recording_quality == 9)
                        {
                            $row.find('td[data-key=recording_quality]').html('<small class="btn-xs bg-red">问题</small>');
                        }
                        else
                        {
                            $row.find('td[data-key=recording_quality]').html('<small class="btn-xs bg-black">有误</small>');
                        }
                        $row.find('td[data-key=inspected_result]').attr('data-value',$inspected_result);
                        $row.find('td[data-key=inspected_result]').html($result_html);
                        $row.find('td[data-key=inspected_description]').attr('data-value',$inspected_description);
                        $row.find('.item-modal-show-for-detail-inspected').removeClass('bg-teal').addClass('bg-blue').html('再审');

                    }
                },
                'json'
            );
        });





        // 【获取】内容详情-申诉
        $(".main-content").on('click', ".by-modal-show-for-by-detail-appealed", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $row = $that.parents('tr');
            $table.find('tr').removeClass('appealing');
            $row.addClass('appealing');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            $('input[name="detail-appealed-by-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-body-for-detail-appealed');
            $modal.attr('data-datatable-id',$table_id);

            $modal.find('.item-detail-project .item-detail-text').html($row.find('td[data-key=project_id]').attr('data-option-name'));
            $modal.find('.item-detail-client .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-detail-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-detail-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-detail-wx-id .item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-detail-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-detail-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-detail-description .item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.find('.item-detail-recording .item-detail-text').html('');
            $modal.find('.item-detail-recording .item-detail-text').html($row.find('[data-key="description"]').attr('data-recording-address'));
            $modal.find('.item-inspected-description .item-detail-text').html($row.find('td[data-key=inspected_description]').attr('data-value'));


            var $inspected_result = $row.find('td[data-key=appealed_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-appealed-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-appealed-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);

            // $modal.find('input[name="recording-quality"]').val('0');
            var $recording_quality = $row.find('td[data-key=recording_quality]').attr('data-value');
            $modal.find('input[name="recording-quality"][value='+$recording_quality+']').prop('checked', true);

            // var $appealed_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-appealed-description"]').val('');
            // $modal.find('textarea[name="detail-appealed-description"]').val($appealed_description);

            $modal.modal('show');

        });
        // 【取消】内容详情-申诉
        $(".main-content").on('click', ".by-modal-cancel-for-by-detail-appealed", function() {
            var that = $(this);
            var $modal = $('#modal-body-for-detail-appealed');
            $modal.find('select[name="detail-appealed-result"]').prop("checked", false);
            $modal.find('select[name="detail-appealed-result"]').find('option').attr("selected",false);
            $modal.find('select[name="detail-appealed-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="detail-appealed-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-申诉
        $(".main-content").on('click', ".by-modal-summit-for-by-detail-appealed", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-appealed');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="detail-appealed-by-id"]').val();
            var $appealed_description = $('textarea[name="detail-appealed-description"]').val();
            // console.log($recording_quality);

            $.post(
                "{{ url('/v1/operate/order/item-appeal') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-appeal",
                    item_id: $('input[name="detail-appealed-by-id"]').val(),
                    appealed_url: $('input[name="detail-appealed-url"]').val(),
                    appealed_description: $('textarea[name="detail-appealed-description"]').val()
                },
                function(data){
                    // layer.close(index);
                    // layer.form.render();
                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        layer.msg(data.msg);

                        $(".modal-cancel-for-detail-appealed").click();
                        // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);

                        var $row = $table.find('tr.appealing');
                        $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-red">申诉中</small>');
                        $row.find('td[data-key=appealed_description]').attr('data-value',$appealed_description);
                        $row.find('.modal-show-for-detail-appealed').removeClass('bg-red').addClass('bg-default').addClass('disabled');
                        // $row.find('.modal-show-for-detail-appealed').removeClass('bg-red').addClass('bg-default').addClass('disabled').remove();

                    }
                },
                'json'
            );
        });


        // 【获取】内容详情-申诉-处理
        $(".main-content").on('click', ".by-modal-show-for-by-detail-appealed-handled", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $row = $that.parents('tr');
            $table.find('tr').removeClass('handling');
            $row.addClass('handling');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            $('input[name="detail-appealed-handled-by-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-body-for-detail-appealed-handled');
            $modal.attr('data-datatable-id',$table_id);

            $modal.find('.item-detail-project .item-detail-text').html($row.find('td[data-key=project_id]').attr('data-option-name'));
            $modal.find('.item-detail-client .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-detail-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-detail-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-detail-wx-id .item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-detail-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-detail-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-detail-description .item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.find('.item-detail-recording .item-detail-text').html('');
            $modal.find('.item-detail-recording .item-detail-text').html($row.find('[data-key="description"]').attr('data-recording-address'));
            $modal.find('.item-inspected-description .item-detail-text').html($row.find('td[data-key=inspected_description]').attr('data-value'));

            var $url = $row.find('td[data-key=inspected_description]').attr('data-appealed-url');
            var $url_html = '<a target="_blank" href="' + $url + '">' + $url + '</a>';
            $modal.find('.item-appealed-url .item-detail-text').html($url_html);
            $modal.find('.item-appealed-description .item-detail-text').html($row.find('td[data-key=inspected_description]').attr('data-appealed-description'));


            var $inspected_result = $row.find('td[data-key=appealed_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-appealed-handled-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-appealed-handled-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);


            // var $appealed_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-appealed-description"]').val('');
            // $modal.find('textarea[name="detail-appealed-description"]').val($appealed_description);

            $modal.modal('show');

        });
        // 【取消】内容详情-申诉-处理
        $(".main-content").on('click', ".by-modal-cancel-for-by-detail-appealed-handled", function() {
            var that = $(this);
            var $modal = $('#modal-body-for-detail-appealed-handled');
            $modal.find('select[name="detail-appealed-handled-result"]').prop("checked", false);
            $modal.find('select[name="detail-appealed-handled-result"]').find('option').attr("selected",false);
            $modal.find('select[name="detail-appealed-handled-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="detail-appealed-handled-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-申诉-处理
        $(".main-content").on('click', ".by-modal-summit-for-by-detail-appealed-handled", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-appealed-handled');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="detail-appealed-handled-by-id"]').val();
            var $appealed_handled_result = $('select[name="detail-appealed-handled-result"]').val();
            var $appealed_handled_description = $('textarea[name="detail-appealed-handled-description"]').val();
            // console.log($recording_quality);

            $.post(
                "{{ url('/v1/operate/order/item-appeal-handle') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-appeal-handle",
                    item_id: $('input[name="detail-appealed-handled-by-id"]').val(),
                    appealed_handled_result: $('select[name="detail-appealed-handled-result"]').val(),
                    appealed_handled_description: $('textarea[name="detail-appealed-handled-description"]').val()
                },
                function(data){
                    // layer.close(index);
                    // layer.form.render();
                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        layer.msg(data.msg);

                        $(".modal-cancel-for-detail-appealed-handled").click();
                        // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);


                        var $row = $table.find('tr.handling');
                        console.log($row);
                        $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-green">申诉·结束</small>');

                        var $inspected_result = '';
                        var $inspected_result_html = '';
                        if($appealed_handled_result == 1)
                        {
                            $inspected_result = '通过';
                            $inspected_result_html = '<small class="btn-xs bg-green">通过</small>';
                        }
                        else if($appealed_handled_result == 9)
                        {
                            $inspected_result = '拒绝';
                            $inspected_result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        $row.find('td[data-key=inspected_result]').attr('data-value',$inspected_result);
                        $row.find('td[data-key=inspected_result]').html($inspected_result_html);
                        $row.find('td[data-key=inspected_description]').attr('data-appealed-value',$appealed_handled_description);

                        $row.find('.modal-show-for-detail-appealed-handled').removeClass('bg-red').addClass('bg-default').addClass('disabled').remove();
                    }
                },
                'json'
            );
        });





        // 【交付】
        $(".main-content").on('click', ".by-item-deliver-submit", function() {

            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-by-list').find('tr').removeClass('operating');
            $row.addClass('operating');

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

            var $html = '';

            $.post(
                "{{ url('/item/by-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:100px;float:left;">[订单]' + $order.id + '</span>' +
                                    '<span style="width:160px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                            })
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已分发客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:160px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';


            var $option_html_for_client = $('#option-list-for-client').html();
            var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();
            var $option_html_for_is_distributive_condition = $('#option-list-for-is_distributive_condition-2').html();

            var $delivered_result = $('select[name="select-delivered-result"]').val();
            var $client_id = $('select[name="select-client-id"]').val();
            var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
            var $is_distributive_condition = $('input[name="option_is_distributive_condition"]:checked').val();

            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '工单【交付】！'
                ,area:['480px;']
                ,content:
                    $html+
                    '<select class="form-control select-primary" name="select-client-id" style="width:48%;" id="">'+
                    $option_html_for_client+
                    '</select>'+
                    '<select class="form-control select-primary" name="select-delivered-result" style="width:48%;" id="">'+
                    $option_html_for_delivered_result+
                    '</select>'+
                    '<input type="text" class="form-control" name="input-recording-address" rows="2" placeholder="录音地址"></textarea>'+
                    '<textarea class="form-control" name="textarea-delivered-description" rows="2" placeholder="交付说明"></textarea>'+
                    $option_html_for_is_distributive_condition
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/by-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "by-deliver",
                            item_id: $that.attr('data-id'),
                            client_id: $('select[name="select-client-id"]').val(),
                            delivered_result: $('select[name="select-delivered-result"]').val(),
                            recording_address: $('input[name="input-recording-address"]').val(),
                            delivered_description: $('textarea[name="textarea-delivered-description"]').val(),
                            is_distributive_condition: $('input[name="option_is_distributive_condition"]:checked').val()
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
                                // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);

                                var $delivered_result = $('select[name="select-delivered-result"]').val();
                                var $client_id = $('select[name="select-client-id"]').val();
                                var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
                                var $is_distributive_condition = $('input[name="option_is_distributive_condition"]:checked').val();

                                $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                // 是否符合分发条件
                                if($is_distributive_condition == 0)
                                {
                                    $row.find('td[data-key=is_distributive_condition]').html('--');
                                }
                                else if($is_distributive_condition == 1)
                                {
                                    $row.find('td[data-key=is_distributive_condition]').html('<small class="btn-xs bg-red">是</small>');
                                }

                                $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                if($client_id != "-1")
                                {
                                    $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                }
                                $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                var $date = new Date();
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);
                                var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                $row.find('td[data-key=delivered_at]').html($time_html);
                            }
                        },
                        'json'
                    );
                }
            });



                    }
                },
                'json'
            );

        });
        // 【交付】显示
        $(".main-content").on('click', ".by-item-deliver-show", function() {


            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $modal = $('#modal-body-for-deliver-set');
            $modal.attr('data-datatable-id',$table_id);

            var $row = $that.parents('tr');
            $('#datatable-for-by-list').find('tr').removeClass('operating');
            $row.addClass('operating');

            $('.deliver-set-title').html($that.attr("data-id"));
            $('.deliver-set-column-name').html($that.attr("data-name"));
            $('input[name=deliver-set-by-id]').val($that.attr("data-id"));
            $('input[name=deliver-set-column-key]').val($that.attr("data-key"));
            $('#deliver-set-distributed-list').html('');
            $('#deliver-set-distributed-by-list').html('');
            $('#deliver-set-distributed-client-list').html('');

            $modal.modal('show');

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

            var $html = '';
            var $html_for_order = '';
            var $html_for_distributed = '';

            $.post(
                "{{ url('/item/by-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:120px;float:left;">[工单ID]' + $order.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                                $html_for_order += $html_order;
                            });
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已交付客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:120px;float:left;">[交付ID] ' + $deliver.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                                $html_for_distributed += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';
                        $('#deliver-set-distributed-list').html($html);
                        $('#deliver-set-distributed-by-list').html($html_for_order);
                        $('#deliver-set-distributed-client-list').html($html_for_distributed);


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                    }
                },
                'json'
            );

        });
        // 【交付】【取消】
        $(".main-content").on('click', "#item-cancel-for-by-deliver-set", function() {
            var that = $(this);
            $('#modal-body-for-deliver-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【交付】确认
        $(".main-content").on('click', "#item-submit-for-by-deliver-set", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-deliver-set');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            layer.msg('确定"交付"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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
                        "{{ url('/item/by-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "by-deliver",
                            item_id: $('input[name="deliver-set-by-id"]').val(),
                            project_id: $('select[name="deliver-set-project-id"]').val(),
                            client_id: $('select[name="deliver-set-client-id"]').val(),
                            delivered_result: $('select[name="deliver-set-delivered-result"]').val(),
                            recording_address: $('input[name="deliver-set-recording-address"]').val(),
                            delivered_description: $('textarea[name="deliver-set-delivered-description"]').val(),
                            is_distributive_condition: $('input[name="deliver-set-is_distributive_condition"]:checked').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');
                            layer.closeAll('loading');

                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                layer.msg($response.msg);
                            }
                            else
                            {
                                layer.msg("已交付");
                                $table.DataTable().ajax.reload(null,false);
                                $('#modal-body-for-deliver-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });
                }
            });
        });




        // 【分发】
        $(".main-content").on('click', ".by-item-distribute-submit", function() {

            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-by-list').find('tr').removeClass('operating');
            $row.addClass('operating');

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

            var $html = '';

            $.post(
                "{{ url('/item/by-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:100px;float:left;">[订单]' + $order.id + '</span>' +
                                    '<span style="width:160px;float:left;">s[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                            })
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已交付客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:160px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                        var $delivered_result = $('select[name="select-delivered-result"]').val();
                        var $client_id = $('select[name="select-client-id"]').val();
                        var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
                        // console.log($client_name);

                        layer.open({
                            time: 0
                            ,btn: ['确定', '取消']
                            ,title: '工单【分发】！'
                            ,area:['480px;']
                            ,content:
                                $html+
                                '<select class="form-control select-primary" name="select-client-id" style="width:48%;" id="">'+
                                $option_html_for_client+
                                '</select>'+
                                '<select class="form-control select-primary select2-box" name="select-delivered-result" style="width:48%;" id="">'+
                                $option_html_for_delivered_result+
                                '</select>'+
                                '<input type="text" class="form-control" name="input-recording-address" rows="2" placeholder="录音地址"></textarea>'+
                                '<textarea class="form-control" name="textarea-delivered-description" rows="2" placeholder="交付说明"></textarea>'
                            ,yes: function(index){
                                $.post(
                                    "{{ url('/item/by-distribute') }}",
                                    {
                                        _token: $('meta[name="_token"]').attr('content'),
                                        operate: "by-distribute",
                                        item_id: $that.attr('data-id'),
                                        client_id: $('select[name="select-client-id"]').val(),
                                        delivered_result: $('select[name="select-delivered-result"]').val(),
                                        recording_address: $('input[name="input-recording-address"]').val(),
                                        delivered_description: $('textarea[name="textarea-delivered-description"]').val()
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
                                            layer.msg("已分发");
                                        }
                                    },
                                    'json'
                                );
                            }
                        });



                    }
                },
                'json'
            );

        });
        // 【分发】显示
        $(".main-content").on('click', ".by-item-distribute-show", function() {


            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-by-list').find('tr').removeClass('operating');
            $row.addClass('operating');

            $('.distribute-set-title').html($that.attr("data-id"));
            $('.distribute-set-column-name').html($that.attr("data-name"));
            $('input[name=distribute-set-by-id]').val($that.attr("data-id"));
            $('input[name=distribute-set-column-key]').val($that.attr("data-key"));
            $('#distribute-set-distributed-list').html('');
            $('#distribute-set-distributed-by-list').html('');
            $('#distribute-set-distributed-client-list').html('');

            $('#modal-body-for-distribute-set').modal('show');

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

            var $html = '';
            var $html_for_order = '';
            var $html_for_distributed = '';

            $.post(
                "{{ url('/item/by-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "by-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【订单列表】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:120px;float:left;">[工单ID]' + $order.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                                $html_for_order += $html_order;
                            });
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【交付列表】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:120px;float:left;">[交付ID] ' + $deliver.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                                $html_for_distributed += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';
                        $('#distribute-set-distributed-list').html($html);
                        $('#distribute-set-distributed-by-list').html($html_for_order);
                        $('#distribute-set-distributed-client-list').html($html_for_distributed);


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                    }
                },
                'json'
            );

        });
        // 【分发】【取消】
        $(".main-content").on('click', "#item-cancel-for-by-distribute-set", function() {
            var that = $(this);
            $('#modal-body-for-distribute-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【分发】确认
        $(".main-content").on('click', "#item-submit-for-by-distribute-set", function() {
            var $that = $(this);
            layer.msg('确定"分发"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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
                        "{{ url('/item/by-distribute') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "by-distribute",
                            item_id: $('input[name="distribute-set-by-id"]').val(),
                            project_id: $('select[name="distribute-set-project-id"]').val(),
                            client_id: $('select[name="distribute-set-client-id"]').val(),
                            delivered_result: $('select[name="distribute-set-delivered-result"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');
                            layer.closeAll('loading');

                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                layer.msg($response.msg);
                            }
                            else
                            {
                                layer.msg("已分发");
                                $('#modal-body-for-distribute-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });
                }
            });
        });





        // 【操作记录】【显示】
        $(".main-content").on('click', ".by-modal-show-for-item-operation", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });




        // 【批量操作】批量-导出
        $(".main-content").on('click', '#bulk-submit-for-by-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-by-by-ids?ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-content").on('click', '#bulk-submit-for-by-delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export-for-by-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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
                        "{{ url('/item/by-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "by-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);

                                if($response.msg != '') layer.msg($response.msg);


                                var $ids = $response.data.ids;
                                // console.log($ids);

                                $ids.forEach(function(value,index) {
                                    console.log(value);
                                    var $item = $('.order_id[data-value='+value+']');
                                    var $row = $item.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);
                                });

                                {{--$('input[name="bulk-id"]:checked').each(function() {--}}

                                {{--    var $that = $(this);--}}
                                {{--    var $row = $that.parents('tr');--}}

                                {{--    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();--}}
                                {{--    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();--}}
                                {{--    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();--}}
                                {{--    console.log($client_name);--}}

                                {{--    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');--}}
                                {{--    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');--}}
                                {{--    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');--}}
                                {{--    $row.find('td[data-key=client_id]').attr('data-value',$client_id);--}}
                                {{--    if($client_id != "-1")--}}
                                {{--    {--}}
                                {{--        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');--}}
                                {{--    }--}}
                                {{--    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');--}}
                                {{--    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');--}}


                                {{--    var $date = new Date();--}}
                                {{--    var $year = $date.getFullYear();--}}
                                {{--    var $month = ('00'+($date.getMonth()+1)).slice(-2);--}}
                                {{--    var $day = ('00'+($date.getDate())).slice(-2);--}}
                                {{--    var $hour = ('00'+$date.getHours()).slice(-2);--}}
                                {{--    var $minute = ('00'+$date.getMinutes()).slice(-2);--}}
                                {{--    var $second = ('00'+$date.getSeconds()).slice(-2);--}}
                                {{--    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;--}}
                                {{--    $row.find('td[data-key=delivered_at]').html($time_html);--}}

                                {{--});--}}
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });
        // 【批量操作】批量-导出
        $(".main-content").off('click', '.bulk-submit-for-by-export').on('click', '.bulk-submit-for-by-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $that = $(this);
            var $item_category = $that.data('item-category');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/v1/operate/statistic/by-export-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-content").off('click', '.bulk-submit-for-by-delivered').on('click', '.bulk-submit-for-by-delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export-for-by-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在发布</span>',
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
                        "{{ url('/item/by-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "by-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable-for-by-list').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);

                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });



        // 【交付列表】【批量操作】批量-导出
        $(".main-content").on('click', '#bulk-submit-for-by-delivery-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-by-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-by-by-ids?ids='+$ids);
            window.open($url);

        });
        // 【交付列表】【批量操作】批量-更改导出状态
        $(".main-content").on('click', '#bulk-submit-for-by-exported', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);


            layer.msg('确定"批量导出"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    $.post(
                        "{{ url('/item/delivery-bulk-exported') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery-exported-bulk",
                            ids: $ids,
                            operate_result:$('select[name="bulk-operate-status"]').val()
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                // $('#datatable_ajax').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $operate_result = $('select[name="bulk-operate-status"]').val();

                                    if($operate_result == "1")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-success">已导出</small>');
                                    }
                                    else if($operate_result == "0")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-primary">未导出</small>');
                                    }
                                    else
                                    {
                                    }


                                });
                            }
                        },
                        'json'
                    );

                }
            });

        });
        // 【交付列表】【批量操作】批量-导出
        $(".main-content").off('click', '.bulk-submit-for-by-delivery-export').on('click', '.bulk-submit-for-by-delivery-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-by-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/v1/operate/statistic/by-export-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);

        });
        // 【交付列表】【批量操作】批量-更改导出状态
        $(".main-content").off('click', '.bulk-submit-for-by-delivery-exported').on('click', '.bulk-submit-for-by-delivery-exported', function() {
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


            layer.msg('确定"批量导出"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    $.post(
                        "{{ url('/item/delivery-bulk-exported') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery-exported-bulk",
                            ids: $ids,
                            operate_result:$('select[name="bulk-operate-status"]').val()
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                // $('#datatable_ajax').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $operate_result = $('select[name="bulk-operate-status"]').val();

                                    if($operate_result == "1")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-success">已导出</small>');
                                    }
                                    else if($operate_result == "0")
                                    {
                                        $row.find('td[data-key=is_exported]').html('<small class="btn-xs btn-primary">未导出</small>');
                                    }
                                    else
                                    {
                                    }


                                });
                            }
                        },
                        'json'
                    );

                }
            });

        });




        // select2 项目
        {{--$('.select2-project').select2({--}}
        {{--    ajax: {--}}
        {{--        url: "{{ url('/v1/operate/select2/select2_project') }}",--}}
        {{--        type: 'post',--}}
        {{--        dataType: 'json',--}}
        {{--        delay: 250,--}}
        {{--        data: function (params) {--}}
        {{--            return {--}}
        {{--                _token: $('meta[name="_token"]').attr('content'),--}}
        {{--                item_category: this.data('item-category'),--}}
        {{--                keyword: params.term, // search term--}}
        {{--                page: params.page--}}
        {{--            };--}}
        {{--        },--}}
        {{--        processResults: function (data, params) {--}}

        {{--            params.page = params.page || 1;--}}
        {{--            return {--}}
        {{--                results: data,--}}
        {{--                pagination: {--}}
        {{--                    more: (params.page * 30) < data.total_count--}}
        {{--                }--}}
        {{--            };--}}
        {{--        },--}}
        {{--        cache: true--}}
        {{--    },--}}
        {{--    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work--}}
        {{--    minimumInputLength: 0,--}}
        {{--    theme: 'classic'--}}
        {{--});--}}

        $('.select2-project-').each(function() {
            // 获取当前 Select2 元素的 jQuery 对象
            const $select = $(this);

            // 动态查找最近的模态框父容器
            const $modalWrapper = $select.closest('.modal-wrapper');

            // 初始化 Select2
            $select.select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2_project') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: $select.data('item-category'), // 使用 $select 获取 data 属性
                            keyword: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
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
                escapeMarkup: function(markup) { return markup; },
                dropdownParent: $modalWrapper, // 直接使用找到的模态框元素
                minimumInputLength: 0,
                theme: 'classic'
            });
        });



    });
</script>