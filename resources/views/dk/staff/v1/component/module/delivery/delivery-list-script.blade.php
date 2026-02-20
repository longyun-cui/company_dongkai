<script>
    $(function() {


        // 【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】
        $(".main-content").on('click', '#bulk-submit-for-delivery-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            window.open($url);

            {{--layer.msg('确定"批量审核"么', {--}}
            {{--    time: 0--}}
            {{--    ,btn: ['确定', '取消']--}}
            {{--    ,yes: function(index){--}}

            {{--        $.post(--}}
            {{--            "{{ url('/admin/business/keyword-review-bulk') }}",--}}
            {{--            {--}}
            {{--                _token: $('meta[name="_token"]').attr('content'),--}}
            {{--                operate: "keyword-review-bulk",--}}
            {{--                bulk_keyword_id: $checked,--}}
            {{--                bulk_keyword_status:$('select[name="bulk-review-keyword-status"]').val()--}}
            {{--            },--}}
            {{--            function(data){--}}
            {{--                layer.close(index);--}}
            {{--                if(!data.success) layer.msg(data.msg);--}}
            {{--                else--}}
            {{--                {--}}
            {{--                    $('#datatable_ajax').DataTable().ajax.reload(null,false);--}}
            {{--                }--}}
            {{--            },--}}
            {{--            'json'--}}
            {{--        );--}}

            {{--    }--}}
            {{--});--}}

        });
        // 【批量操作】批量-导出
        $(".main-content").on('click', '#bulk-submit-for-exported', function() {
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





        // 【工单】删除
        $(".main-wrapper").off('click', ".delivery--item-delete-submit").on('click', ".delivery--item-delete-submit", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            layer.msg('确定"删除"么?', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index)
                {
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
                        "{{ url('/o1/delivery/item-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "delivery--item-delete",
                            item_category: $item_category,
                            item_id: $that.attr('data-id')
                        },
                        'json'
                    )
                        .done(function($response, status, jqXHR) {
                            console.log('#'+$that.attr('id')+'.post.done.');

                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                if($response.msg) layer.msg($response.msg);
                            }
                            else
                            {
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        })
                        .fail(function(jqXHR, status, error) {
                            console.log('#'+$that.attr('id')+'.post.fail.');
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, status) {
                            console.log('#'+$that.attr('id')+'.post.always.');
                            layer.closeAll('loading');
                        });
                }
            });
        });

    });
</script>