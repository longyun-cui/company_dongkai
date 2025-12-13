<script>
    $(function() {


        // 【提交】生成日报
        $(".main-content").on('click', ".statistic-list-client-daily-create", function() {
            var $that = $(this);
            var $search_wrapper = $that.closest('.search-wrapper');
            var $assign_date = $search_wrapper.find('input[name="statistic-list-client-daily-date"]').val();

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
                "{{ url('/v1/operate/statistic-list/statistic-client-daily/daily-create') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "statistic-client-daily-create",
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
                        $search_wrapper.find('.filter-refresh').click();
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




        // 【删除】
        $(".main-content").on('click', ".item-delete-submit-of-statistic-client-daily", function() {
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
        $(".main-content").on('click', ".item-complete-submit-of-statistic-client-daily", function() {
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
        $(".main-content").on('click', ".modal-show-for-modify-1", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });


    });
</script>