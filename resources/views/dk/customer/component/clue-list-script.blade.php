<script>
    $(function() {



        // 【拨号】
        $(".main-content").on('click', ".item-call-submit", function() {

            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable_ajax').find('tr').removeClass('operating');
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
                "{{ url('/item/clue-call') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "telephone-call",
                    item_id: $that.attr('data-id')
                },
                'json'
            )
                .done(function($response) {
                    console.log('done');
                    $response = JSON.parse($response);

                    if(!$response.success)
                    {
                        layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("拨号成功，请接电话！");
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误');

                })
                .always(function(jqXHR, textStatus) {
                    layer.closeAll('loading');
                    $('#datatable_ajax').DataTable().ajax.reload(null,false);
                });




        });


        // 【拨号记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-call-record", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            $('#modal-body-for-call-record-list').modal('show');

            TableDatatablesAjax_call_record.init('clue',$id);
        });

    });
</script>