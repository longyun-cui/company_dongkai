<script>
    $(function() {


        // 【项目】添加-显示
        $(".main-wrapper").on('click', ".modal-show--for--ai-item-inspecting", function() {
            var $that = $(this);
            var $form_id = $that.data('form-id');
            var $modal_id = $that.data('modal-id');
            var $title = $that.data('title');

            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

            form_reset('#'+$form_id);

            var $modal = $('#'+$modal_id);
            $modal.find('input[name="operate[type]"]').val('create');
            $modal.find('input[name="operate[id]"]').val(0);
            $modal.find('.box-title').html($title);
            $modal.find('.edit-submit').attr('data-datatable-list-id',$table_id);
            $modal.find('.radio-btn').show();
            $modal.modal('show');
        });
        // 【项目】编辑-提交
        $(".main-wrapper").on('click', "#submit--for--ai-item-inspecting", function() {
            var $that = $(this);

            var $table_id = $that.data('datatable-list-id');
            var $table = $('#'+$table_id);

            var $modal_id = 'modal--for--ai-item-inspecting';
            var $modal = $("#"+$modal_id);

            var $form_id = 'form--for--ai-item-inspecting';
            var $form = $("#"+$form_id);

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
                url: "{{ url('/o1/ai/item-inspecting') }}",
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
                        // form_reset("#"+$form_id);

                        var $return_html = '';
                        $.each(response.data.result, function($index, $value) {
                            $return_html += '【'+ $index +'】' + $value + ' <br>';
                        });
                        $modal.find('.result-box').html($return_html);

                        // $modal.modal('hide').on("hidden.bs.modal", function () {
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
            $form.ajaxSubmit(options);
        });


    });
</script>