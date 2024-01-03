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


        // 【完整显示】
        $(".main-content").on('click', "#order-show-for-full", function() {
            // $('#datatable_ajax').dataTable().fnSetColumnVis(11, true);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(12, true);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(16, true);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(17, true);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(18, true);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(19, true);
        });
        // 【简要显示】
        $(".main-content").on('click', "#order-show-for-brief", function() {
            // $('#datatable_ajax').dataTable().fnSetColumnVis(11, false);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(12, false);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(16, false);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(17, false);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(18, false);
            // $('#datatable_ajax').dataTable().fnSetColumnVis(19, false);
        });
        // 【财务显示】
        $(".main-content").on('click', "#order-show-for-finance", function() {
            // $('#datatable_ajax').dataTable().fnSetColumnVis(s19, false);
        });




        // 【综合概览】【前一天】
        $(".main-content").on('click', ".date-pick-pre-for-order", function() {

            var $assign_dom = $('input[name="order-assign"]');
            var $the_date = $assign_dom.val();

            if($the_date)
            {

                var $pre_date = getNextDate($the_date, -1);
                $assign_dom.val($pre_date);
            }
            else
            {
                var $date = new Date();
                var $year = $date.getFullYear();
                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                var $day = ('00'+($date.getDate())).slice(-2);

                var $pre_date = $year+'-'+$month+'-'+$day;
                $assign_dom.val($pre_date);
                console.log($pre_date);
            }

            $("#datatable-for-order-list").find(".filter-submit").click();

        });
        // 【综合概览】【后一添】
        $(".main-content").on('click', ".date-pick-next-for-order", function() {

            var $assign_dom = $('input[name="order-assign"]');
            var $the_date = $assign_dom.val();

            if($the_date)
            {

                var $pre_date = getNextDate($the_date, 1);
                $assign_dom.val($pre_date);
            }
            else
            {
                var $date = new Date();
                var $year = $date.getFullYear();
                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                var $day = ('00'+($date.getDate())).slice(-2);

                var $pre_date = $year+'-'+$month+'-'+$day;
                $assign_dom.val($pre_date);
                console.log($pre_date);
            }

            $("#datatable-for-order-list").find(".filter-submit").click();

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
        // 【复用】
        $(".main-content").on('click', ".item-reuse-submit", function() {
            var $that = $(this);
            layer.msg('确定"复用"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-reuse') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-reuse",
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
                        "{{ url('/item/order-publish') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-publish",
                            item_id: $that.attr('data-id')
                        },
                        function(data){

                            // layer.close(index);
                            layer.closeAll('loading');

                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
                            else
                            {
                                layer.msg("发布成功！");
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
        // 【验证】
        $(".main-content").on('click', ".item-verify-submit", function() {
            var $that = $(this);
            layer.msg('确定"审核"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-verify') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-verify",
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
                                $('#datatable_ajax').DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });
        // 【审核】
        $(".main-content").on('click', ".item-inspect-submit", function() {
            var $that = $(this);
            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '选择审核状态！'
                ,content: '<select class="form-control form-filter" name="inspected-result" style="width:160px;">'+
                    '<option value ="-1">选择审核状态</option>'+
                    '<option value ="通过">通过</option>'+
                    '<option value ="拒绝">拒绝</option>'+
                    '<option value ="重复">重复</option>'+
                    '<option value ="内部通过">内部通过</option>'+
                    '<option value ="二次待审">二次待审</option>'+
                    '<option value ="已审未提">已审未提</option>'+
                    '<option value ="回访重提">回访重提</option>'+
                    '</select>'+
                    '<textarea class="form-control" name="inspected-description" placeholder="审核说明" rows="3"></textarea>'
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-inspect') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-inspect",
                            item_id: $that.attr('data-id'),
                            inspected_result: $('select[name="inspected-result"]').val(),
                            inspected_description: $('textarea[name="inspected-description"]').val()
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
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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
            // console.log($that.attr("data-value"));
            if($that.attr('data-text-type') == "textarea")
            {
                $('input[name=info-text-set-column-value]').val('').hide();
                $('textarea[name=info-textarea-set-column-value]').text('').text($that.attr("data-value")).show();
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
            $('textarea[name=info-textarea-set-column-value]').text('');
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
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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
            if($that.attr("data-key") == "location_city")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-city');
                $('select[name=info-select-set-column-value2]').removeClass('select2-district');
                var $option_html = $('#location-city-option-list').html();
            }
            else if($that.attr("data-key") == "receipt_status")
            {
                var $option_html = $('#receipt_status-option-list').html();
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
            $('input[name=info-select-set-column-key2]').val($that.attr("data-key2"));
//            $('select[name=info-select-set-column-value]').find("option").eq(0).prop("selected",true);
//            $('select[name=info-select-set-column-value]').find("option").eq(0).attr("selected","selected");
            $('select[name=info-select-set-column-value]').find('option').eq(0).val($that.attr("data-value"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).text($that.attr("data-option-name"));
            $('select[name=info-select-set-column-value]').find('option').eq(0).attr('data-id',$that.attr("data-value"));
            $('input[name=info-select-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-body-for-info-select-set').modal('show');


            if($that.attr("data-key") == "location_city")
            {

                var $district_list = [
                    ['东城区','西城区','海淀区','朝阳区','丰台区','门头沟区','石景山区','房山区','通州区','顺义区','昌平区','大兴区','怀柔区','平谷区','延庆区','密云区','其他'],
                    ['和平区','河东区','河西区','南开区','河北区','红桥区','滨海新区','东丽区','西青区','津南区','北辰区','武清区','宝坻区','宁河区','静海区','蓟州区','其他'],
                    ['玄武区','秦淮区','建邺区','鼓楼区','浦口区','栖霞区','雨花台区','江宁区','六合区','溧水区','高淳区','江北新区','其他'],
                    ['黄浦区','徐汇区','长宁区','静安区','普陀区','虹口区','杨浦区','闵行区','宝山区','嘉定区','浦东新区','金山区','松江区','青浦区','奉贤区','崇明区','其他'],
                    ['海曙区','江北区','北仑区','镇海区','鄞州区','奉化区','象山县','宁海县','余姚市','慈溪市','其他'],
                    ['锦江区','青羊区','金牛区','武侯区','成华区','龙泉驿区','新都区','郫都区','温江区','双流区','青白江区','新津区','都江堰市','彭州市','邛崃市','崇州市','简阳市','金堂县','大邑县','蒲江县','其他'],
                    ['越秀区','荔湾区','海珠区','天河区','白云区','黄埔区','南沙区','番禺区','花都区','从化区','增城区','其他']
                ];

                var $option_html = $('#location-city-option-list').html();
                $('select[name=info-select-set-column-value]').html($option_html);
                $('select[name=info-select-set-column-value]').find("option[value='"+$that.attr("data-value")+"']").attr("selected","selected");

                $('select[name=info-select-set-column-value]').removeClass('select2-project').addClass('select2-city');
                $('select[name=info-select-set-column-value2]').removeClass('select2-project').addClass('select2-district');


                var $city_index = $(".select2-city").find('option:selected').attr('data-index');
                $(".select2-district").html('<option value="">选择区划</option>');
                $.each($district_list[$city_index], function($i,$val) {
                    $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                });
                $('.select2-district').find("option[value='"+$that.attr("data-value2")+"']").attr("selected","selected");
                $('.select2-district').select2();


                $(".select2-city").change(function() {

                    $that = $(this);

                    var $city_index = $that.find('option:selected').attr('data-index');

                    $(".select2-district").html('<option value="">选择区划</option>');

                    $.each($district_list[$city_index], function($i,$val) {

                        $(".select2-district").append('<option value="' + $val + '">' + $val + '</option>');
                    });

                    $('.select2-district').select2();
                });
            }
            else if($that.attr("data-key") == "project_id")
            {
                $('select[name=info-select-set-column-value]').removeClass('select2-city').addClass('select2-project');
                $('.select2-project').select2({
                    ajax: {
                        url: "{{ url('/item/item_select2_project') }}",
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
            var $column_key2 = $('input[name="info-select-set-column-key2"]').val();
            var $column_value = $('select[name="info-select-set-column-value"]').val();
            var $column_value2 = $('select[name="info-select-set-column-value2"]').val();

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
                            column_key2: $column_key2,
                            column_value: $column_value,
                            column_value2: $column_value2,
                        },
                        function(data){
                            // layer.close(index);
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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


            var $index = layer.load(1, {
                shade: [0.3, '#fff'],
                content: '<span class="loadtip">正在上传</span>',
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
                        url: "{{ url('/item/order-info-attachment-set') }}",
                        type: "post",
                        dataType: "json",
                        // target: "#div2",
                        success: function (data) {

                            // layer.close(index);
                            layer.closeAll('loading');

                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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
                                        if(!data.success)
                                        {
                                            layer.msg(data.msg);
                                        }
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
                        },
                        error: function (res) {
                            layer.closeAll('loading');
                            layer.msg("上传失败");
                        },
                        complete: function () {
                        }
                    };
                    $("#modal-attachment-set-form").ajaxSubmit(options);


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
                            if(!data.success)
                            {
                                layer.msg(data.msg);
                            }
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













        $('.order-select2').select2({
            theme: 'classic'
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






        // select2-环线
        $('.order-select2-project').select2({
            ajax: {
                url: "{{ url('/item/item_select2_project') }}",
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