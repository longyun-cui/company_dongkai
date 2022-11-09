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
//            $('.item-user-id').html($that.attr('data-user-id'));
//            $('.item-username').html($that.attr('data-username'));
//            $('.item-title').html($data.title);
//            $('.item-content').html($data.content);
//            if($data.attachment_name)
//            {
//                var $attachment_html = $data.attachment_name+'&nbsp&nbsp&nbsp&nbsp'+'<a href="/all/download-item-attachment?item-id='+$data.id+'">下载</a>';
//                $('.item-attachment').html($attachment_html);
//            }

            $('.info-body').html($data.html);
            $('#modal-info-detail-body').modal('show');

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












        // 【行程管理】
        $(".item-main-body").on('click', ".item-travel-show", function() {
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

            $('#modal-travel-body').modal('show');
        });

        // 显示【设置行程时间】
        $(".modal-main-body").on('click', ".item-travel-time-set-show", function() {
            var $that = $(this);

            $object_type = $that.attr('data-type');
            if($object_type == "actual_departure") $('.travel-set-object-title').html("实际出发时间");
            else if($object_type == "actual_arrival") $('.travel-set-object-title').html("实际到达时间");
            else if($object_type == "stopover_arrival") $('.travel-set-object-title').html("经停到达时间");
            else if($object_type == "stopover_departure") $('.travel-set-object-title').html("经停出发时间");

            $('input[name="travel-set-object-type"]').val($object_type);

            $('#modal-travel-set-body').modal({show: true,backdrop: 'static'});
            $('.modal-backdrop').each(function() {
                $(this).attr('id', 'id_' + Math.random());
            });
        });
        // 【设置行程时间】取消
        $(".modal-main-body").on('click', "#item-travel-set-cancel", function() {
            var that = $(this);
            $('input[name="travel-set-object-type"]').val('');

            $('#modal-travel-set-body').modal('hide');
            $("#modal-travel-set-body").on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【设置行程时间】提交
        $(".modal-main-body").on('click', "#item-travel-set-submit", function() {
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
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                layer.close(index);
                                $('#modal-travel-set-body').modal('hide');
                                $("#modal-travel-set-body").on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });

                                $('#datatable_ajax').DataTable().ajax.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });








        // 【财务记录】
        $(".item-main-body").on('click', ".item-data-finance-link", function() {
            var that = $(this);
            window.open("/admin/business/keyword-detect-record?id="+that.attr('data-id'));
        });
        // 【财务记录】
        $(".item-main-body").on('click', ".item-finance-show", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('#set-rank-bulk-submit').attr('data-keyword-id',$id);
            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

            TableDatatablesAjax_inner.init($id);

            $('#modal-finance-body').modal('show');
        });




        // 【修改记录】
        $(".item-main-body").on('click', ".item-record-show", function() {
            var that = $(this);
            var $id = that.attr("data-id");
            var $keyword = that.attr("data-keyword");

            $('#set-rank-bulk-submit').attr('data-keyword-id',$id);
            $('input[name="finance-create-order-id"]').val($id);
            $('.finance-create-order-id').html($id);
            $('.finance-create-order-title').html($keyword);

//            $('#datatable_ajax_inner_record').empty();
//            $('#datatable_ajax_inner_record').DataTable().destroy();
            TableDatatablesAjax_inner_record.init($id);

            $('#modal-modify-body').modal('show');
        });




        $('.form_datetime').datetimepicker({
            locale: moment.locale('zh-cn'),
            format:"YYYY-MM-DD HH:mm"
        });


        $(".form_date").datepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true
        });




        // 显示【修改属性】
        $(".item-main-body").on('dblclick', ".order-info-set-text", function() {
            var $that = $(this);
            $('.info-set-title').html($that.attr("data-id"));
            $('.info-set-column-name').html($that.attr("data-name"));
            $('input[name=info-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-set-column-value]').val($that.attr("data-value"));
            $('input[name=info-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-info-set-body').modal('show');
        });
        // 显示【修改时间】
        $(".item-main-body").on('dblclick', ".order-info-set-time", function() {
            var $that = $(this);
            $('.info-time-set-title').html($that.attr("data-id"));
            $('.info-time-set-column-name').html($that.attr("data-name"));
            $('input[name=info-time-set-order-id]').val($that.attr("data-id"));
            $('input[name=info-time-set-column-key]').val($that.attr("data-key"));
            $('input[name=info-time-set-column-value]').val($that.attr("data-value")).attr('data-time-type',$that.attr('data-time-type'));
            if($that.attr('data-time-type') == "datetime") $('input[name=info-time-set-column-value]').removeClass("form_date").addClass("form_datetime");
            else if($that.attr('data-time-type') == "date") $('input[name=info-time-set-column-value]').removeClass("form_datetime").addClass("form_date");
            $('input[name=info-time-set-operate-type]').val($that.attr('data-operate-type'));

            $('#modal-info-time-set-body').modal('show');
        });
        // 【修改时间】提交
        $(".modal-main-body").on('click', "#item-info-time-set-submit", function() {
            var $that = $(this);
            var $column_key = $('input[name="info-time-set-column-key"]').val();
            var $column_value = $('input[name="info-time-set-column-value"]').val();
            var $time_type = $('input[name="info-time-set-column-value"]').attr('data-time-type');
            layer.msg('确定"提交"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-info-time-set') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: $('input[name="info-time-set-operate"]').val(),
                            order_id: $('input[name="info-time-set-order-id"]').val(),
                            operate_type: $('input[name="info-time-set-operate-type"]').val(),
                            column_key: $column_key,
                            column_value: $column_value,
                            time_type: $time_type,
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
//                            else location.reload();
                            else
                            {
                                layer.close(index);
                                $('#modal-info-time-set-body').modal('hide');
//                                $("#modal-info-time-set-body").on("hidden.bs.modal", function () {
//                                    $("body").addClass("modal-open");
//                                });

                                $('#datatable_ajax').DataTable().ajax.reload();

                            }
                        },
                        'json'
                    );
                }
            });
        });



//        $(".modal-main-body").off('click').on('click', 'input[data-time-type="datetime"]', function(){
//            $(this).datetimepicker({
//                locale: moment.locale('zh-cn'),
//                format:"YYYY-MM-DD HH:mm"
//            });
//        });
//        $(".modal-main-body").off('click').on('click', 'input[data-time-type="date"]', function(){
//            $(this).datetimepicker({
//                locale: moment.locale('zh-cn'),
//                format:"YYYY-MM-DD"
//            });
//        });
//        $(".modal-main-body").off('click').on('click', '.form_datetime', function(){
//            $(this).datetimepicker({
//                locale: moment.locale('zh-cn'),
//                format:"YYYY-MM-DD HH:mm"
//            });
//        });
//        $(".modal-main-body").off('click').on('click', '.form_date', function(){
//            $(this).datetimepicker({
//                locale: moment.locale('zh-cn'),
//                format:"YYYY-MM-DD"
//            });
//        });
        $(".modal-main-body").on('click', '.time_picker', function(){
//            $(this).blur();
            var $that = $(this);
            if($that.attr('data-time-type') == 'date')
            {
//                layer.msg($that.attr('data-time-type'));
                $(this).datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format:"YYYY-MM-DD"
                });
            }
            else
            {
//                layer.msg($that.attr('data-time-type'));
                $(this).datetimepicker({
                    locale: moment.locale('zh-cn'),
                    format:"YYYY-MM-DD HH:mm"
                });
            }
        });

//        $('.time_picker[data-time-type="datetime"]').datetimepicker({
//            locale: moment.locale('zh-cn'),
//            format:"YYYY-MM-DD HH:mm"
//        });
//        $('.time_picker[data-time-type="date"]').datetimepicker({
//            locale: moment.locale('zh-cn'),
//            format:"YYYY-MM-DD"
//        });
//        $('input.time_picker[data-time-type="datetime"]').datetimepicker({
//            locale: moment.locale('zh-cn'),
//            format:"YYYY-MM-DD HH:mm"
//        });
//        $('input.time_picker[data-time-type="date"]').datetimepicker({
//            locale: moment.locale('zh-cn'),
//            format:"YYYY-MM-DD"
//        });


    });
</script>