<script>
    $(function() {


        // 【下载】
        $(".main-content").on('click', ".item-call-down-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

            var $obj = new Object();

            var $randomNumber = Math.floor(Math.random() * 100) + 1;
            $obj.randomNumber = $randomNumber;

            $phone = $row.find('td[data-key=phone]').attr('data-value');
            $obj.phone = $phone;

            $recording = $row.find('td[data-key=recording]').attr('data-value');
            $obj.url = $recording;

            console.log($obj);

            var $url = url_build('/download/phone-recording-download',$obj);
            window.open($url);

        });




        // 【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });

        // 【批量操作】
        $(".main-content").on('click', '#bulk-submit-for-down', function() {
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
        $(".main-content").on('click', '#bulk-submit-for-exported--', function() {
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









    });
</script>