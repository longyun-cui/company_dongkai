<script>
    $(function() {

        // 【搜索】
        $(".item-main-body").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【重置】
        $(".item-main-body").on('click', ".filter-cancel", function() {
            $('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });

//            $('select.form-filter').selectpicker('refresh');
            $('select.form-filter option').attr("selected",false);
            $('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【查询】回车
        $(".item-main-body").on('keyup', ".item-search-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#filter-submit").click();
            }
        });




        // 【下载二维码】
        $(".item-main-body").on('click', ".item-download-qr-code-submit", function() {
            var $that = $(this);
            window.open("/download/qr-code?type=item&id="+$that.attr('data-id'));
        });

        // 【数据分析】
        $(".item-main-body").on('click', ".item-statistic-submit", function() {
            var $that = $(this);
            window.open("/statistic/statistic-item?id="+$that.attr('data-id'));
//            window.location.href = "/admin/statistic/statistic-item?id="+$that.attr('data-id');
        });

        // 【编辑】
        $(".item-main-body").on('click', ".item-edit-link", function() {
            var $that = $(this);
            window.location.href = "/item/order-edit?id="+$that.attr('data-id');
        });




        /*
            // 批量操作
         */
        // 【批量操作】全选or反选
        $(".main-list-body").on('click', '#check-review-all', function () {
            $('input[name="bulk-id"]').prop('checked',this.checked);//checked为true时为默认显示的状态
        });

        // 【批量操作】
        $(".main-list-body").on('click', '#operate-bulk-submit', function() {
            var $checked = [];
            $('input[name="bulk-id"]:checked').each(function() {
                $checked.push($(this).val());
            });

            if($checked.length == 0)
            {
                layer.msg("请先选择操作对象！");
                return false;
            }

//            var $operate_set = new Array("启用","禁用","删除","彻底删除");
            var $operate_set = ["启用","禁用","删除","彻底删除"];
            var $operate_result = $('select[name="bulk-operate-status"]').val();
            if($.inArray($operate_result, $operate_set) == -1)
            {
                layer.msg("请选择操作类型！");
                return false;
            }


            layer.msg('确定"批量操作"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    $.post(
                        "{{ url('/item/task-admin-operate-bulk') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "operate-bulk",
                            bulk_item_id: $checked,
                            bulk_item_operate:$('select[name="bulk-operate-status"]').val()
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                                $("#check-review-all").prop('checked',false);
                            }
                        },
                        'json'
                    );

                }
            });

        });

        // 【批量删除】
        $(".main-list-body").on('click', '#delete-bulk-submit', function() {
            var $checked = [];
            $('input[name="bulk-id"]:checked').each(function() {
                $checked.push($(this).val());
            });

            layer.msg('确定"批量删除"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    $.post(
                        "{{ url('/item/task-admin-delete-bulk') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-delete-bulk",
                            bulk_item_id: $checked
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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




        // 内容【获取详情】
        $(".item-main-body").on('click', ".item-detail-show", function() {
            var $that = $(this);
            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/item/task-get') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('input[name=id]').val($that.attr('data-id'));
            $('.item-user-id').html($that.attr('data-user-id'));
            $('.item-username').html($that.attr('data-username'));
            $('.item-title').html($data.title);
            $('.item-content').html($data.content);
            if($data.attachment_name)
            {
                var $attachment_html = $data.attachment_name+'&nbsp&nbsp&nbsp&nbsp'+'<a href="/all/download-item-attachment?item-id='+$data.id+'">下载</a>';
                $('.item-attachment').html($attachment_html);
            }
            $('#modal-body').modal('show');

        });

        // 内容【管理员-删除】
        $(".item-main-body").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定要"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/task-admin-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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

        // 内容【管理员-恢复】
        $(".item-main-body").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定要"恢复"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/task-admin-restore') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-restore",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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

        // 内容【管理员-永久删除】
        $(".item-main-body").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定要"永久删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/task-admin-delete-permanently') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-delete-permanently",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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

        // 【发布】
        $(".item-main-body").on('click', ".item-publish-submit", function() {
            var $that = $(this);
            layer.msg('确定要"发布"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "item-publish",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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


        // 【启用】
        $(".item-main-body").on('click', ".item-admin-enable-submit", function() {
            var $that = $(this);
            layer.msg('确定"启用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/task-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-enable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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
        // 【禁用】
        $(".item-main-body").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            layer.msg('确定"禁用"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/task-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-disable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
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





        // 【选择类别】
        $("#modal-finance-create-form").on('click', "input[name=finance-create-type]", function() {

            var $value = $(this).val();

            if($value == 1) {
                $('.income-show').show();

                // checkbox
//                if($("input[name=time_type]").is(':checked')) {
//                    $('.time-show').show();
//                } else {
//                    $('.time-show').hide();
//                }
                // radio
//                var $time_type = $("input[name=time_type]:checked").val();
//                if($time_type == 1) {
//                    $('.time-show').show();
//                } else {
//                    $('.time-show').hide();
//                }
            } else {
                $('.income-show').hide();
            }

            if($value == 21) {
                $('.expenditure-show').show();
            } else {
                $('.expenditure-show').hide();
            }

        });


        // 显示【添加财务记录】
        $(".item-main-body").on('click', ".item-finance-create-show", function() {
            var that = $(this);

            $('#modal-finance-create-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【添加财务记录】取消
        $(".modal-main-body").on('click', "#item-finance-create-cancel", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.detect-set-keyword').html('');
            $('.detect-set-id').html(0);
            $('.detect-set-date').html('');
            $('.detect-set-original-rank').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-finance-create-body').modal('hide');
            $("#modal-finance-create-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【添加财务记录】提交
        $(".modal-main-body").on('click', "#item-finance-create-submit", function() {
            var that = $(this);
            layer.msg('确定"添加"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-finance-record-create') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="finance-create-operate"]').val(),
                            order_id: $('input[name="finance-create-order-id"]').val(),
                            record_type: $("input[name='finance-create-type']:checked").val(),
                            transaction_amount: $('input[name="finance-create-transaction-amount"]').val(),
                            transaction_date: $('input[name="finance-create-transaction-date"]').val(),
                            transaction_type: $('input[name="finance-create-transaction-type"]').val(),
                            transaction_account: $('input[name="finance-create-transaction-account"]').val(),
                            transaction_order: $('input[name="finance-create-transaction-order"]').val(),
                            transaction_title: $('input[name="finance-create-transaction-title"]').val(),
                            transaction_description: $('input[name="finance-create-transaction-description"]').val()
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                layer.close(index);
                                $('#modal-finance-create-body').modal('hide');
                                $("#modal-finance-create-body").on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
//                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload();
                                $('#datatable_ajax_inner').DataTable().ajax.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });




        // 显示【修改排名】
        $(".item-main-body").on('click', ".item-finance-edit-show", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(that.attr('data-id'));
            $('input[name=detect-set-date]').val(that.attr('data-date'));
            $('.detect-set-keyword').html(that.attr('data-name'));
            $('.detect-set-id').html(that.attr('data-id'));
            $('.detect-set-date').html(that.attr('data-date'));
            $('.detect-set-original-rank').html(that.attr('data-rank'));
            $('input[name=detect-set-rank]').val('');

            $('#modal-set-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【修改排名】取消
        $(".modal-main-body").on('click', "#item-finance-edit-cancel", function() {
            var that = $(this);
            $('input[name=detect-set-id]').val(0);
            $('.detect-set-keyword').html('');
            $('.detect-set-id').html(0);
            $('.detect-set-date').html('');
            $('.detect-set-original-rank').html('');
            $('input[name=detect-set-rank]').val('');

            $('#modal-set-body').modal('hide');
            $("#modal-set-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改排名】提交
        $(".modal-main-body").on('click', "#item-finance-edit-submit", function() {
            var that = $(this);
//            layer.msg('确定"提交"么？', {
//                time: 0
//                ,btn: ['确定', '取消']
//                ,yes: function(index){
//                }
//            });
            $.post(
                "{{ url('/item/order-finance-record-edit') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:$('input[name="detect-set-operate"]').val(),
                    detect_id:$('input[name="detect-set-id"]').val(),
                    detect_date:$('input[name="detect-set-date"]').val(),
                    detect_rank:$('input[name="detect-set-rank"]').val(),
                    detect_description:$('input[name="detect-set-description"]').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
//                    else location.reload();
                    else
                    {
//                        layer.close(index);
                        $('#modal-set-body').modal('hide');
                        $("#modal-set-body").on("hidden.bs.modal", function () {
                            $("body").addClass("modal-open");
                        });

                        var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
//                        TableDatatablesAjax_inner.init($keyword_id);
                        $('#datatable_ajax_inner').DataTable().ajax.reload(null,false);
                    }
                },
                'json'
            );
        });




    });
</script>