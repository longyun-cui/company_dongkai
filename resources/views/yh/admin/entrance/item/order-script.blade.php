<script>
    $(function() {

        // 【搜索】
        $("#datatable-for-order-list").on('click', ".filter-submit", function() {
            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【刷新】
        $("#datatable-for-order-list").on('click', ".filter-refresh", function() {
            $('#datatable_ajax').DataTable().ajax.reload(null,false);
        });
        // 【重置】
        $("#datatable-for-order-list").on('click', ".filter-cancel", function() {
            $("#datatable-for-order-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $(".select2-container").val(-1).trigger("change");
            // $(".order-select2-circle").val(-1).trigger("change");
            // $(".order-select2-car").val(-1).trigger("change");
            // $(".order-select2-trailer").val(-1).trigger("change");
            // $(".order-select2-driver").val(-1).trigger("change");

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-order-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-order-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);

            $('#datatable_ajax').DataTable().ajax.reload();
        });
        // 【清空重选】
        $("#datatable-for-order-list").on('click', ".filter-empty", function() {
            $("#datatable-for-order-list").find('textarea.form-filter, input.form-filter, select.form-filter').each(function () {
                $(this).val("");
            });
            $(".select2-container").val(-1).trigger("change");

//            $('select.form-filter').selectpicker('refresh');
            $("#datatable-for-order-list").find('select.form-filter option').attr("selected",false);
            $("#datatable-for-order-list").find('select.form-filter').find('option:eq(0)').attr('selected', true);
        });
        // 【查询】回车
        $("#datatable-for-order-list").on('keyup', ".filter-keyup", function(event) {
            if(event.keyCode ==13)
            {
                $("#datatable-for-order-list").find(".filter-submit").click();
            }
        });




        // 【提示】
        $(".main-content").on('dblclick', ".alert-published-first", function() {
            layer.msg('未发布内容，可以编辑，或请先发布！');
        });




        // 【编辑】
        $(".main-content").on('click', ".item-create-show", function() {
            var $that = $(this);
            $('#modal-body-for-order-create').modal('show');
        });

        // 【编辑】
        $(".main-content").on('click', ".item-create-link", function() {
            var $that = $(this);
            var $url = "/item/order-create?&referrer="+encodeURIComponent(window.location.href);
            // window.location.href = $url;
            window.open($url);
        });

        // 【编辑】
        $(".main-content").on('click', ".item-edit-link", function() {
            var $that = $(this);
            var $url = "/item/order-edit?id="+$that.attr('data-id')+"&referrer="+encodeURIComponent(window.location.href);
            window.location.href = $url;
            // window.open($url);
        });


        // 内容【获取详情】
        $(".main-content").on('click', ".item-modal-show-for-detail", function() {
            var $that = $(this);
            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/item/order-get-html') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    order_id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });

//            $('input[name=id]').val($that.attr('data-id'));
            $('input[name=info-set-order-id]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            $('.info-body').html($data.html);

            $('#modal-body-for-info-detail').modal('show');

        });


        // 【删除】
        $(".main-content").on('click', ".item-delete-submit", function() {
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
        // 【弃用】
        $(".main-content").on('click', ".item-abandon-submit", function() {
            var $that = $(this);
            layer.msg('确定"弃用"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-abandon') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-abandon",
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
        $(".main-content").on('click', ".item-publish-submit", function() {
            var $that = $(this);
            layer.msg('确定"发布"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-publish",
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
        // 【完成】
        $(".main-content").on('click', ".item-complete-submit", function() {
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




        // 【管理员-删除】
        $(".main-content").on('click', ".item-admin-delete-submit", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
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
        // 【管理员-恢复】
        $(".main-content").on('click', ".item-admin-restore-submit", function() {
            var $that = $(this);
            layer.msg('确定"恢复"么？', {
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
        // 【管理员-永久删除】
        $(".main-content").on('click', ".item-admin-delete-permanently-submit", function() {
            var $that = $(this);
            layer.msg('确定"永久删除"么？', {
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


        // 【管理员-启用】
        $(".main-content").on('click', ".item-admin-enable-submit", function() {
            var $that = $(this);
            // layer.msg('确定"启用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/item/task-admin-enable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-enable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
        });
        // 【管理员-禁用】
        $(".main-content").on('click', ".item-admin-disable-submit", function() {
            var $that = $(this);
            // layer.msg('确定"禁用"？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });
                    $.post(
                        "{{ url('/item/task-admin-disable') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "task-admin-disable",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
        });








        // 【行程管理】
        $(".main-content").on('click', ".item-modal-show-for-travel", function() {
            var that = $(this);
            var $that = $(this);
            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/item/order-get') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get",
                    order_id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('input[name=order_id]').val($that.attr('data-id'));
//            $('.item-travel-should-departure-time').html($that.attr('data-user-id'));

            $('.item-travel-should-departure-time').html($data.should_departure_time_html);
            $('.item-travel-should-arrival-time').html($data.should_arrival_time_html);


            if($data.is_actual_departure == 1) $('.item-travel-actual-departure-time').html($data.actual_departure_time_html);
            else
            {
                $actual_departure_html = '<a class="btn btn-xs item-travel-time-set-show" data-type="actual_departure">添加实际出发时间</a>';
                $('.item-travel-actual-departure-time').html($actual_departure_html);
            }

            if($data.is_actual_arrival == 1) $('.item-travel-actual-arrival-time').html($data.actual_arrival_time_html);
            else
            {
                $actual_arrival_html = '<a class="btn btn-xs item-travel-time-set-show" data-type="actual_arrival">添加实际到达时间</a>';
                $('.item-travel-actual-arrival-time').html($actual_arrival_html);
            }


            if($data.is_stopover == 1)
            {
                $('.item-travel-stopover-container').show();

                if($data.is_stopover_arrival == 1) $('.item-travel-stopover-arrival-time').html($data.stopover_arrival_time_html);
                else
                {
                    $stopover_arrival_html = '<a class="btn btn-xs item-travel-time-set-show" data-type="stopover_arrival">添加经停到达时间</a>';
                    $('.item-travel-stopover-arrival-time').html($stopover_arrival_html);
                }

                if($data.is_stopover_departure == 1) $('.item-travel-stopover-departure-time').html($data.stopover_departure_time_html);
                else
                {
                    $stopover_departure_html = '<a class="btn btn-xs item-travel-time-set-show" data-type="stopover_departure">添加经停出发时间</a>';
                    $('.item-travel-stopover-departure-time').html($stopover_departure_html);
                }
            }
            else $('.item-travel-stopover-container').hide();

            $order_id = $that.attr('data-id');
            $('input[name="travel-set-order-id"]').val($order_id);
            $('.travel-set-order-id').html($order_id);

            $('#modal-body-for-travel-detail').modal('show');
        });

        // 【设置行程时间】【显示】
        $(".main-content").on('click', ".item-travel-time-set-show", function() {
            var $that = $(this);

            $object_type = $that.attr('data-type');
            if($object_type == "actual_departure") $('.travel-set-object-title').html("实际出发时间");
            else if($object_type == "actual_arrival") $('.travel-set-object-title').html("实际到达时间");
            else if($object_type == "stopover_arrival") $('.travel-set-object-title').html("经停到达时间");
            else if($object_type == "stopover_departure") $('.travel-set-object-title').html("经停出发时间");

            $('input[name="travel-set-object-type"]').val($object_type);

            $('#modal-body-for-travel-set').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【设置行程时间】【取消】
        $(".main-content").on('click', "#item-cancel-for-travel-set", function() {
            var that = $(this);
            $('input[name="travel-set-object-type"]').val('');

            $('#modal-body-for-travel-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【设置行程时间】【提交】
        $(".main-content").on('click', "#item-submit-for-travel-set", function() {
            var that = $(this);
            layer.msg('确定"添加"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-travel-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="travel-set-operate"]').val(),
                            order_id: $('input[name="travel-set-order-id"]').val(),
                            travel_type: $('input[name="travel-set-object-type"]').val(),
                            travel_time: $('input[name="travel-set-time"]').val()
                        },
                        function(data){
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-travel-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );
                }
            });
        });








        // 【财务记录】【跳转】
        $(".main-content").on('click', ".item-data-finance-link", function() {
            var that = $(this);
            window.open("/admin/business/keyword-detect-record?id="+that.attr('data-id'));
        });
        // 【财务记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-finance", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id);

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务-收入-记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance-income", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id,"income");

            $('#modal-body-for-finance-list').modal('show');
        });
        // 【财务-支出-记录】【显示】
        $(".main-content").on('dblclick', ".item-show-for-finance-expenditure", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_finance.init($id,"expenditure");

            $('#modal-body-for-finance-list').modal('show');
        });




        // 【修改记录】【显示】
        $(".main-content").on('click', ".item-modal-show-for-modify", function() {
            var that = $(this);
            var $id = that.attr("data-id");

            TableDatatablesAjax_record.init($id);

            $('#modal-body-for-modify-list').modal('show');
        });




        // 【修改-文本-text-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-text-set", function() {
            var $that = $(this);
            $('.info-text-set-title').html($that.attr("data-id"));
            $('.info-text-set-column-name').html($that.attr("data-name"));
            $('input[name=info-text-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-text-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-text-set-operate-type]').val($that.attr('data-operate-type'));
            if($that.attr('data-text-type') == "textarea")
            {
                $('input[name=info-text-set-column-value]').val('').hide();
                $('textarea[name=info-textarea-set-column-value]').text($that.attr("data-value")).show();
            }
            else
            {
                $('textarea[name=info-textarea-set-column-value]').val('').hide();
                $('input[name=info-text-set-column-value]').val($that.attr("data-value")).show();
            }

            $('#item-submit-for-info-text-set').attr('data-text-type',$that.attr('data-text-type'));

            $('#modal-body-for-info-text-set').modal('show');
        });
        // 【修改-文本-text-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-text-set", function() {
            var that = $(this);
            $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
            $('input[name=info-text-set-column-value]').val('');
            $('textarea[name=info-textarea-set-column-value]').val('');
        });
        // 【修改-文本-text-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-text-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-text-set-column-key"]').val();
            if($that.attr('data-text-type') == "textarea")
            {
                var $column_value = $('textarea[name="info-textarea-set-column-value"]').val();
            }
            else
            {
                var $column_value = $('input[name="info-text-set-column-value"]').val();
            }

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    $.post(
                        "{{ url('/item/order-info-text-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-text-set-operate"]').val(),
                            order_id: $('input[name="info-text-set-order-id"]').val(),
                            operate_type: $('input[name="info-text-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-text-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                $('input[name=info-text-set-column-value]').val('');
                                $('textarea[name=info-textarea-set-column-value]').text('');

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );

        });




        // 【修改-时间-time-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-time-set", function() {
            var $that = $(this);
            $('.info-time-set-title').html($that.attr("data-id"));
            $('.info-time-set-column-name').html($that.attr("data-name"));
            $('input[name=info-time-set-operate-type]').val($that.attr('data-operate-type'));
            $('input[name=info-time-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-time-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-time-set-time-type]').val($that.attr('data-time-type'));
            if($that.attr('data-time-type') == "datetime")
            {
                $('input[name=info-time-set-column-value]').show();
                $('input[name=info-date-set-column-value]').hide();
                $('input[name=info-time-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }
            else if($that.attr('data-time-type') == "date")
            {
                $('input[name=info-time-set-column-value]').hide();
                $('input[name=info-date-set-column-value]').show();
                $('input[name=info-date-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            }

            $('#modal-body-for-info-time-set').modal('show');
        });
        // 【修改-时间-time-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-time-set", function() {
            var that = $(this);
            $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-时间-time-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-time-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-time-set-column-key"]').val();
            var $time_type = $('input[name="info-time-set-time-type"]').val();
            var $column_value = '';
            if($time_type == "datetime")
            {
                $column_value = $('input[name="info-time-set-column-value"]').val();
            }
            else if($time_type == "date")
            {
                $column_value = $('input[name="info-date-set-column-value"]').val();
            }

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    $.post(
                        "{{ url('/item/order-info-time-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-time-set-operate"]').val(),
                            order_id: $('input[name="info-time-set-order-id"]').val(),
                            operate_type: $('input[name="info-time-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                            time_type: $time_type
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-time-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);

                            }
                        },
                        'json'
                    );

        });




        // 【修改-radio-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-radio-set", function() {

            $('select[name=info-radio-set-column-value]').attr("selected","");
            $('select[name=info-radio-set-column-value]').find('option').eq(0).val(0).text('');
            $('select[name=info-radio-set-column-value]').find('option:not(:first)').remove();

            var $that = $(this);
            $('.info-radio-set-title').html($that.attr("data-id"));
            $('.info-radio-set-column-name').html($that.attr("data-name"));
            $('input[name=info-radio-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-radio-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-radio-set-operate-type]').val($that.attr('data-operate-type'));


            if($that.attr("data-key") == "receipt_need")
            {
                var $option_html = $('#receipt_need-option-list').html();
                $('.radio-box').html($option_html);
                $('input[name=receipt_need][value="'+$that.attr("data-value")+'"]').attr("checked","checked");
            }
            else if($that.attr("data-key") == "is_delay")
            {
                var $option_html = $('#is_delay-option-list').html();
                $('.radio-box').html($option_html);
                $('input[name=is_delay][value="'+$that.attr("data-value")+'"]').attr("checked","checked");
            }


            $('#modal-body-for-info-radio-set').modal('show');

        });
        // 【修改-radio-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-radio-set", function() {
            var that = $(this);
            $('#modal-body-for-info-radio-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-radio-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-radio-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-radio-set-column-key"]').val();
            var $column_value = $('#modal-info-radio-set-form').find('input:radio:checked').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    $.post(
                        "{{ url('/item/order-info-radio-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-radio-set-operate"]').val(),
                            order_id: $('input[name="info-radio-set-order-id"]').val(),
                            operate_type: $('input[name="info-radio-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-radio-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );

        });




        // 【修改-select-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select-set", function() {

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


            $('select[name=info-select-set-column-value]').removeClass('select2-car').removeClass('select2-client');
            if($that.attr("data-key") == "car_owner_type")
            {
                var $option_html = $('#car_owner_type-option-list').html();
            }
            else if($that.attr("data-key") == "receipt_status")
            {
                var $option_html = $('#receipt_status-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_type")
            {
                var $option_html = $('#trailer_type-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_length")
            {
                var $option_html = $('#trailer_length-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_volume")
            {
                var $option_html = $('#trailer_volume-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_weight")
            {
                var $option_html = $('#trailer_weight-option-list').html();
            }
            else if($that.attr("data-key") == "trailer_axis_count")
            {
                var $option_html = $('#trailer_axis_count-option-list').html();
            }
            $('select[name=info-select-set-column-value]').html($option_html);
            $('select[name=info-select-set-column-value]').find("option[value='"+$that.attr("data-value")+"']").attr("selected","selected");


            $('#modal-body-for-info-select-set').modal('show');



        });
        // 【修改-select2-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-info-select2-set", function() {

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
            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-body-for-info-select-set').modal('show');


            if($that.attr("data-key") == "client_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-circle').removeClass('select2-route').removeClass('select2-car').removeClass('select2-driver').addClass('select2-client');
                $('.select2-client').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_client') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            if($that.attr("data-key") == "circle_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-route').removeClass('select2-car').removeClass('select2-driver').removeClass('select2-client').addClass('select2-circle');
                $('.select2-circle').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_circle') }}"+"?car_id="+$that.attr("data-car"),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            else if($that.attr("data-key") == "route_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').removeClass('select2-circle').removeClass('select2-car').removeClass('select2-driver').removeClass('select2-pricing').addClass('select2-route');
                $('.select2-route').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_route') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            else if($that.attr("data-key") == "pricing_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').removeClass('select2-circle').removeClass('select2-car').removeClass('select2-driver').removeClass('select2-route').addClass('select2-pricing');
                $('.select2-pricing').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_pricing') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            else if($that.attr("data-key") == "car_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').removeClass('select2-circle').removeClass('select2-driver').removeClass('select2-route').removeClass('select2-pricing').addClass('select2-car');
                $('.select2-car').select2({
                    ajax: {
                        url: "{{ url('/item/order_list_select2_car?car_type=car') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            else if($that.attr("data-key") == "trailer_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').removeClass('select2-circle').removeClass('select2-driver').removeClass('select2-route').removeClass('select2-pricing').addClass('select2-car');
                $('.select2-car').select2({
                    ajax: {
                        url: "{{ url('/item/order_list_select2_car?car_type=trailer') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }
            else if($that.attr("data-key") == "driver_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-client').removeClass('select2-circle').removeClass('select2-car').removeClass('select2-route').removeClass('select2-pricing').addClass('select2-driver');
                $('.select2-driver').select2({
                    ajax: {
                        url: "{{ url('/item/order_select2_driver') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {

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
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 0,
                    theme: 'classic'
                });
            }

        });
        // 【修改-select-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-info-select-set", function() {
            var that = $(this);
            $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【修改-select-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-info-select-set", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-select-set-column-key"]').val();
            var $column_value = $('select[name="info-select-set-column-value"]').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    $.post(
                        "{{ url('/item/order-info-select-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-select-set-operate"]').val(),
                            order_id: $('input[name="info-select-set-order-id"]').val(),
                            operate_type: $('input[name="info-select-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                $('#modal-body-for-info-select-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

//                                var $keyword_id = $("#set-rank-bulk-submit").attr("data-keyword-id");
////                                TableDatatablesAjax_inner.init($keyword_id);

                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        },
                        'json'
                    );

        });




        // 【附件-attachment-属性】【显示】
        $(".main-content").on('dblclick', ".modal-show-for-attachment", function() {
            var $that = $(this);

            $('.attachment-set-title').html($that.attr("data-id"));
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=attachment-set-order-id]').val($that.attr("data-id"));
            $('input[name=attachment-set-column-key]').val($that.attr("data-key"));
            $('input[name=attachment-set-column-value]').val($that.attr("data-value"));
            $('input[name=attachment-set-operate-type]').val($that.attr('data-operate-type'));


            $('#modal-attachment-set-form input[name=order_id]').val($that.attr("data-id"));
            $('#modal-attachment-set-form input[name=column-key]').val($that.attr("data-key"));
            $('#modal-attachment-set-form input[name=column-value]').val($that.attr("data-value"));
            $('#modal-attachment-set-form input[name=-operate-type]').val($that.attr('data-operate-type'));


            var $data = new Object();
            $.ajax({
                type:"post",
                dataType:'json',
                async:false,
                url: "{{ url('/item/order-get-attachment-html') }}",
                data: {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate:"item-get",
                    order_id: $that.attr('data-id')
                },
                success:function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        $data = data.data;
                    }
                }
            });
            $('.attachment-box').html($data.html);

            $('#modal-body-for-attachment').modal('show');
        });
        // 【附件-attachment-属性】【取消】
        $(".main-content").on('click', "#item-cancel-for-attachment-set", function() {
            var that = $(this);
            $('#modal-body-for-attachment').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【附件-attachment-属性】【提交】
        $(".main-content").on('click', "#item-submit-for-attachment-set", function() {
            var $that = $(this);

            var $column_key = $('input[name="info-attachment-set-column-key"]').val();
            var $column_value = $('select[name="info-attachment-set-column-value"]').val();

            // layer.msg('确定"提交"么？', {
            //     time: 0
            //     ,btn: ['确定', '取消']
            //     ,yes: function(index){
            //     }
            // });

                    var options = {
                        url: "{{ url('/item/order-info-attachment-set') }}",
                        type: "post",
                        dataType: "json",
                        // target: "#div2",
                        success: function (data) {
                            layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
//                                $('#modal-body-for-attachment').modal('hide').on("hidden.bs.modal", function () {
//                                    $("body").addClass("modal-open");
//                                });

                                $('.fileinput-exists[data-dismiss="fileinput"]').click();
                                $('#modal-attachment-set-form input[name=attachment_name]').val('');

                                var $data = new Object();
                                $.ajax({
                                    type:"post",
                                    dataType:'json',
                                    async:false,
                                    url: "{{ url('/item/order-get-attachment-html') }}",
                                    data: {
                                        _token: $('meta[name="_token"]').attr('content'),
                                        operate:"item-get",
                                        order_id: $('#modal-attachment-set-form input[name=order_id]').val()
                                    },
                                    success:function(data){
                                        if(!data.success) layer.msg(data.msg);
                                        else
                                        {
                                            $data = data.data;
                                        }
                                    }
                                });
                                $('.attachment-box').html($data.html);
                                $(".fileinput-remove-button").click();

//                                $('#datatable_ajax').DataTable().ajax.reload(null, false);
//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);
                            }
                        }
                    };
                    $("#modal-attachment-set-form").ajaxSubmit(options);



//                    var formData = new FormData();
//                    formData.append('attachment_file', fileItem);
//                    formData.append('operate', $('input[name="attachment-set-operate"]').val());
//                    formData.append('order_id', $('input[name="attachment-set-order-id"]').val());

                    {{--$.post(--}}
                        {{--"{{ url('/item/order-info-attachment-set') }}",--}}
                        {{--{--}}
                            {{--_token: $('meta[name="_token"]').attr('content'),--}}
                            {{--operate: $('input[name="attachment-set-operate"]').val(),--}}
                            {{--order_id: $('input[name="attachment-set-order-id"]').val(),--}}
                            {{--operate_type: $('input[name="attachment-set-operate-type"]').val(),--}}
                            {{--attachment_file: $blob--}}
                        {{--},--}}
                        {{--function(data){--}}
                            {{--layer.close(index);--}}
                            {{--if(!data.success) layer.msg(data.msg);--}}
{{--//                            else location.reload();--}}
                            {{--else--}}
                            {{--{--}}
                                {{--layer.close(index);--}}
                                {{--$('#modal-body-for-attachment-set').modal('hide').on("hidden.bs.modal", function () {--}}
                                    {{--$("body").addClass("modal-open");--}}
                                {{--});--}}

                                {{--$('#datatable_ajax').DataTable().ajax.reload(null, false);--}}
{{--//                                $('#datatable_ajax_inner').DataTable().ajax.reload(null, false);--}}
                            {{--}--}}
                        {{--},--}}
                        {{--'json'--}}
                    {{--);--}}



        });

        // 【附件-attachment-属性】【删除】
        $(".main-content").on('click', ".order-attachment-delete-this", function() {
            var $that = $(this);
            layer.msg('确定"删除"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-info-attachment-delete') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-attachment-delete",
                            item_id: $that.attr('data-id')
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                $that.parents('.attachment-option').remove();
//                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });




        $('.time_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $('.date_picker').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD",
            ignoreReadonly: true
        });


        $('.form_datetime').datetimepicker({
            locale: moment.locale('zh-cn'),
            format: "YYYY-MM-DD HH:mm",
            ignoreReadonly: true
        });
        $(".form_date").datepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            ignoreReadonly: true
        });




        // 全部车辆
        $('.order-list-select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_list_select2_car') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        // 车辆
        $('.order-select2-car').select2({
            ajax: {
                url: "{{ url('/item/order_select2_car') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });
        // 车挂
        $('.order-select2-trailer').select2({
            ajax: {
                url: "{{ url('/item/order_select2_trailer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });

        // 驾驶员
        $('.order-select2-driver').select2({
            ajax: {
                url: "{{ url('/item/order_select2_driver') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });


        // 环线
        $('.order-select2-circle').select2({
            ajax: {
                url: "{{ url('/item/order_select2_circle') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {

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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            theme: 'classic'
        });



        $('.lightcase-image').lightcase({
            maxWidth: 9999,
            maxHeight: 9999
        });


        $(".file-multiple-images").fileinput({
            allowedFileExtensions : [ 'jpg', 'jpeg', 'png', 'gif' ],
            showUpload: false
        });

    });
</script>