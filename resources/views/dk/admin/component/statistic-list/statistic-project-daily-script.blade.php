<script>
    $(function() {


        // 【提交】生成日报
        $(".main-content").on('click', ".statistic-list-project-daily-create", function() {
            var $that = $(this);
            var $search_wrapper = $that.closest('.search-wrapper');
            var $assign_date = $search_wrapper.find('input[name="statistic-list-project-daily-date"]').val();

            //
            $.post(
                "{{ url('/v1/operate/statistic-list/statistic-project-daily/daily-create') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "statistic-project-daily-create",
                    assign_date: $assign_date
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


        // 【获取】内容详情-审核
        $(".main-content").on('click', ".modal-show-for-order-inspecting", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
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

            $('input[name="detail-inspected-order-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-for-order-inspecting');
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


            var $inspected_result = $row.find('td[data-key=inspected_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-inspecting-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-inspecting-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);

            // $modal.find('input[name="recording-quality"]').val('0');
            var $recording_quality = $row.find('td[data-key=recording_quality]').attr('data-value');
            $modal.find('input[name="recording-quality"][value='+$recording_quality+']').prop('checked', true);

            var $inspected_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-inspected-description"]').val('');
            $modal.find('textarea[name="detail-inspected-description"]').val($inspected_description);

            Datatable_Order_Item_Phone_Delivered_Record.init($id,'datatable-for-order-inspected-item-deliverer-record');

            $modal.modal('show');

        });

        // 【获取】内容详情-审核
        $(".main-content").on('click', ".item-modal-show-for-detail-inspected", function() {
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

            $('input[name="detail-inspected-order-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-body-for-detail-inspected');
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
        $(".main-content").on('click', ".item-cancel-for-detail-inspected", function() {
            var that = $(this);
            var $modal = $('#modal-body-for-detail-inspected');
            $modal.find('select[name="detail-inspected-result"]').prop("checked", false);
            $modal.find('select[name="detail-inspected-result"]').find('option').attr("selected",false);
            $modal.find('select[name="detail-inspected-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="detail-inspected-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-审核
        $(".main-content").on('click', ".item-summit-for-detail-inspected", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-inspected');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="detail-inspected-order-id"]').val();
            var $inspected_result = $('select[name="detail-inspected-result"]').val();
            var $inspected_description = $('textarea[name="detail-inspected-description"]').val();
            var $recording_quality = $('input[name="recording-quality"]:checked').val();
            // console.log($recording_quality);

            $.post(
                "{{ url('/v1/operate/order/item-inspect') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-inspect",
                    item_id: $('input[name="detail-inspected-order-id"]').val(),
                    inspected_result: $('select[name="detail-inspected-result"]').val(),
                    inspected_description: $('textarea[name="detail-inspected-description"]').val(),
                    recording_quality: $('input[name="recording-quality"]:checked').val()
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
                        // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);

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




        // 【工单】审核-显示
        $(".main-content").on('click', ".modal-show-for-order-inspecting", function() {
            var $that = $(this);
            var $id = $that.attr('data-id');
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

            $('input[name="detail-inspected-order-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-for-order-inspecting');
            $modal.attr('data-datatable-id',$table_id);

            $modal.find('.item-project-box').find('.item-detail-text').html($row.find('td[data-key=project_id]').attr('data-option-name'));
            $modal.find('.item-name-box').find('.item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-phone-box').find('.item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-is-wx-box').find('.item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-wx-id-box').find('.item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-city-district-box').find('.item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-teeth-count-box').find('.item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-description-box').find('.item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.find('.item-recording-box').find('.item-detail-text').html('');
            $modal.find('.item-recording-box').find('.item-detail-text').html($row.find('[data-key="description"]').attr('data-recording-address'));


            var $inspected_result = $row.find('td[data-key=inspected_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="order-inspecting-result"]').find("option").prop("selected",false);
            $modal.find('select[name="order-inspecting-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);

            // $modal.find('input[name="recording-quality"]').val('0');
            var $recording_quality = $row.find('td[data-key=recording_quality]').attr('data-value');
            $modal.find('input[name="order-inspecting-recording-quality"][value='+$recording_quality+']').prop('checked', true);

            var $inspected_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="order-inspecting--description"]').val('');
            $modal.find('textarea[name="order-inspecting--description"]').val($inspected_description);

            Datatable_Order_Item_Phone_Delivered_Record.init($id,'datatable-for-order-inspecting-phone-delivered-record');

            $modal.modal('show');

        });
        // 【工单】审核-取消
        $(".main-content").on('click', ".item-cancel-for-order-inspecting", function() {
            var that = $(this);
            var $modal = $('#modal-for-detail-inspecting');
            $modal.find('select[name="detail-inspected-result"]').prop("checked", false);
            $modal.find('select[name="detail-inspected-result"]').find('option').attr("selected",false);
            $modal.find('select[name="detail-inspected-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="detail-inspected-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【工单】审核-提交
        $(".main-content").on('click', ".item-summit-for-order-inspecting", function() {
        });



        // 【获取】内容详情-申诉
        $(".main-content").on('click', ".modal-show-for-detail-appealed", function() {
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

            $('input[name="detail-appealed-order-id"]').val($that.attr('data-id'));
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
        $(".main-content").on('click', ".modal-cancel-for-detail-appealed", function() {
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
        $(".main-content").on('click', ".modal-summit-for-detail-appealed", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-appealed');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="detail-appealed-order-id"]').val();
            var $appealed_description = $('textarea[name="detail-appealed-description"]').val();
            // console.log($recording_quality);

            $.post(
                "{{ url('/v1/operate/order/item-appeal') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-appeal",
                    item_id: $('input[name="detail-appealed-order-id"]').val(),
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
                        // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);

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




        // 【推送】【显示】
        $(".main-content").on('click', ".item-modal-show-for-push", function() {

            $('select[name=info-select-set-column-value]').attr("selected","");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-select-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-select-set-title').html($that.attr("data-id"));
            $('.info-select-set-column-name').html($that.attr("data-name"));
            $('input[name=info-select-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-select-set-column-key]').val($that.attr("data-key"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
//            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
//            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));


            $('#modal-body-for-info-select-set').find('select[name=info-select-set-column-value2]').next('.select2-container').hide();
            $('#modal-body-for-info-select-set').find('select[name=info-select-set-column-value2]').hide();

            var $option_html = $('#option-list-for-client').html();
            $('select[name=info-select-set-column-value]').html($option_html);
            $('select[name=info-select-set-column-value]').find("option[value='"+$that.attr("data-value")+"']").attr("selected","selected");


            $('#modal-body-for-info-select-set').modal('show');

        });




        // 【删除】
        $(".main-content").on('click', ".item-delete-submit-of-statistic-project-daily", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【完成】
        $(".main-content").on('click', ".item-complete-submit-of-statistic-project-daily", function() {
            var $that = $(this);
            layer.msg('确定"完成"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-complete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-complete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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


    });
</script>