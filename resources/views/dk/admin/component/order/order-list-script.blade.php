<script>
    $(function() {

        window.addEventListener('play', function(e) {
            console.log('[全局监听] 播放事件触发:', e.target);
            const audio = e.target;

            // 1. 获取音频源地址
            const audioSrc = audio.currentSrc || $(audio).find('source').attr('src');
            // console.log('音频开始播放:', audioSrc);

            // 2. 设置播放速度 (1.5倍速)
            var $speed = $('input[name="recording-speed"]:checked').val();
            audio.playbackRate = $speed; // 默认值1.0，范围0.5-4.0
            console.log('播放速率已设置:', audio.playbackRate);

            // 3. 可选: 防止多音频同时播放
            // $('audio').not(this).each(function() {
            //     this.pause();
            //     this.currentTime = 0;
            // });
            // 暂停其他音频
            $('audio').each(function() {
                if (this !== audio && !this.paused) {
                    this.pause();
                    this.currentTime = 0;
                    // updateAudioStatus(this, '已停止');
                    // addLog(`暂停其他音频: ${this.dataset.audioId}`, 'warning');
                }
                else
                {

                }
            });

            // this.play();

            // 4. 业务逻辑可在此处添加
        }, true);

        // 【录音】播放速度
        $(".main-content").on('click', 'input[name="recording-speed"]', function() {
            var $speed = $('input[name="recording-speed"]:checked').val();

            $('#modal-body-for-detail-inspected audio').each(function() {
                if (this.played)
                {
                    this.playbackRate = $speed; // 默认值1.0，范围0.5-4.0
                }
            });
        });

        // 【获取录音】
        $(".main-content").on('click', ".item-get-recording-list-submit", function() {
            var $that = $(this);
            var $td = $that.parents('td');
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $rowIndex = $td.attr('data-row-index');

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

            $.post(
                "{{ url('/v1/operate/order/item-get-api-call-record') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get-api-call-record",
                    item_id: $that.attr('data-id')
                },
                'json'
            )
                .done(function($response) {

                    layer.closeAll('loading');
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        // console.log(JSON.parse($response.data));

                        console.log($response.data.data);
                        var $item = $response.data.data;
                        if($item.recording_address_list)
                        {
                            // var $html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+$item.recording_address+'" type="audio/mpeg"></audio>'
                            // $row.find('[data-key="recording_address_play"]').html($html);

                            var $recording_list = JSON.parse($item.recording_address_list);
                            var $recording_list_html = '';
                            $.each($recording_list, function(index, value)
                            {

                                var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                $recording_list_html += $audio_html;
                            });
                            $row.find('[data-key="recording_address_play"]').html($recording_list_html);
                            $row.find('[data-key="description"]').attr('data-recording-address',$recording_list_html);
                        }

                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    layer.closeAll('loading');
                });

        });


        // 【获取录音】
        $(".main-content").on('click', ".item-inspected-get-recording-list-submit", function() {
            var $that = $(this);
            var $modal_wrapper = $that.closest('.modal-wrapper');
            var $id = $modal_wrapper.find('input[name="detail-inspected-order-id"]').val();
            var $row = $('tr.operating');
            console.log($id);
            // return false;


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

            $.post(
                "{{ url('/v1/operate/order/item-get-api-call-record') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "item-get-api-call-record",
                    item_id: $id
                },
                'json'
            )
                .done(function($response) {

                    layer.closeAll('loading');
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        // console.log(JSON.parse($response.data));

                        console.log($response.data.data);
                        var $item = $response.data.data;
                        if($item.recording_address_list)
                        {
                            // var $html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+$item.recording_address+'" type="audio/mpeg"></audio>'
                            // $row.find('[data-key="recording_address_play"]').html($html);

                            var $recording_list = JSON.parse($item.recording_address_list);
                            var $recording_list_html = '';
                            $.each($recording_list, function(index, value)
                            {

                                var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                $recording_list_html += $audio_html;
                            });
                            $modal_wrapper.find('.item-detail-recording .item-detail-text').html($recording_list_html);
                            $row.find('[data-key="recording_address_play"]').html($recording_list_html);
                            $row.find('[data-key="description"]').attr('data-recording-address',$recording_list_html);
                        }

                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    layer.closeAll('loading');
                });

        });




        // 【下载】
        $(".main-content").on('click', ".item-download-recording-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');

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

            $.post(
                "{{ url('/item/order-download-recording') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-download-recording",
                    item_id: $that.attr('data-id')
                },
                'json'
            )
                .done(function($response) {
                    console.log('done');
                    $response = JSON.parse($response);
                    if(!$response.success)
                    {
                        if($response.msg) layer.msg($response.msg);
                    }
                    else
                    {
                        layer.msg("请求成功！");
                        // console.log(JSON.parse($response.data));
                        $.each(JSON.parse($response.data), function(index, value) {
                            console.log(value);
                            console.log(value.url);
                            console.log(value.path);
                            console.log(value.name);

                            // var $obj = new Object();
                            // $obj.type = 'url';
                            // $obj.url = value.url;
                            // $obj.name = value.name;
                            //
                            // var $url = url_build('/download/file-download',$obj);
                            // window.open($url);

                            var $obj = new Object();
                            $obj.call_record_id = value.call_record_id;

                            var $url = url_build('/download/call-recording-download',$obj);
                            window.open($url);

                        });
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('fail');
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                    layer.msg('服务器错误！');

                })
                .always(function(jqXHR, textStatus) {
                    console.log('always');
                    // console.log(jqXHR);
                    // console.log(textStatus);
                    layer.closeAll('loading');
                });

        });

        // 【下载】
        $(".main-content").on('click', ".item-download-recording-list-submit", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $item_id = $that.data('id');
            console.log($item_id);

            $recording_list_str = $row.find('td[data-key=recording_address_download]').attr('data-address-list');
            if($recording_list_str)
            {
                var $recording_list = JSON.parse($recording_list_str);
                console.log($recording_list);

                $.each($recording_list, function($index, $value) {

                    console.log($index);
                    console.log($value);

                    var $obj = new Object();
                    $obj.item_id = $item_id;

                    $obj.url = $value;

                    var $randomNumber = Math.floor(Math.random() * 100) + 1;
                    $obj.randomNumber = $randomNumber;
                    console.log($obj);

                    var $url = url_build('/download/item-recording-download',$obj);
                    window.open($url);

                    // var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);

                    // setTimeout(() => {
                    //     window.open($url, $randomNumber);
                    //     this.printOrderDialogShow = false;
                    // }, 0.3);


                });
            }
            else
            {
                $call_record_id = $row.find('td[data-key=recording_address_download]').attr('data-call-record-id');
                if($call_record_id && $call_record_id > 0)
                {
                    console.log($call_record_id);

                    var $obj = new Object();
                    $obj.call_record_id = $call_record_id;

                    var $url = url_build('/download/call-recording-download',$obj);
                    // window.open($url);
                }

            }

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




        // 【获取】内容详情
        $(".main-content").on('click', ".item-modal-show-for-detail", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            console.log();
            var $data = new Object();
            {{--$.ajax({--}}
            {{--    type:"post",--}}
            {{--    dataType:'json',--}}
            {{--    async:false,--}}
            {{--    url: "{{ url('/item/order-get-html') }}",--}}
            {{--    data: {--}}
            {{--        _token: $('meta[name="_token"]').attr('content'),--}}
            {{--        operate:"item-get",--}}
            {{--        order_id: $that.attr('data-id')--}}
            {{--    },--}}
            {{--    success:function(data){--}}
            {{--        if(!data.success) layer.msg(data.msg);--}}
            {{--        else--}}
            {{--        {--}}
            {{--            $data = data.data;--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}

//            $('input[name=id]').val($that.attr('data-id'));
            $('input[name=info-set-order-id]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            $('.info-body').html($data.html);

            var $modal = $('#modal-body-for-info-detail');

            $modal.find('.item-detail-project .item-detail-text').html($row.find('td[data-key=project_id]').attr('data-value'));
            $modal.find('.item-detail-client .item-detail-text').html($row.find('td[data-key=client_name]').attr('data-value'));
            $modal.find('.item-detail-phone .item-detail-text').html($row.find('td[data-key=client_phone]').attr('data-value'));
            $modal.find('.item-detail-is-wx .item-detail-text').html($row.find('td[data-key=is_wx]').html());
            $modal.find('.item-detail-wx-id .item-detail-text').html($row.find('td[data-key=wx_id]').attr('data-value'));
            $modal.find('.item-detail-city-district .item-detail-text').html($row.find('td[data-key=location_city]').html());
            $modal.find('.item-detail-teeth-count .item-detail-text').html($row.find('td[data-key=teeth_count]').html());
            $modal.find('.item-detail-description .item-detail-text').html($row.find('td[data-key=description]').attr('data-value'));
            $modal.modal('show');

        });
        // 【取消】内容详情
        $(".main-content").on('click', ".item-cancel-for-detail", function() {
            var that = $(this);
            $('#modal-body-for-info-detail').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
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
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-inspected');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="order-inspecting-order-id"]').val();
            var $inspected_result = $('select[name="order-inspecting-result"]').val();
            var $inspected_description = $('textarea[name="order-inspecting-description"]').val();
            var $recording_quality = $('input[name="order-inspectingrecording-quality"]:checked').val();
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


        // 【获取】内容详情-申诉-处理
        $(".main-content").on('click', ".modal-show-for-detail-appealed-handled", function() {
            var $that = $(this);
            var $row = $that.parents('tr');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $row = $that.parents('tr');
            $table.find('tr').removeClass('handling');
            $row.addClass('handling');

            $('.datatable-wrapper').removeClass('operating');
            $datatable_wrapper.addClass('operating');
            $datatable_wrapper.find('tr').removeClass('operating');
            $row.addClass('operating');

            $('input[name="detail-appealed-handled-order-id"]').val($that.attr('data-id'));
            $('.info-detail-title').html($that.attr('data-id'));
            $('.info-set-title').html($that.attr('data-id'));

            var $modal = $('#modal-body-for-detail-appealed-handled');
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

            var $url = $row.find('td[data-key=inspected_description]').attr('data-appealed-url');
            var $url_html = '<a target="_blank" href="' + $url + '">' + $url + '</a>';
            $modal.find('.item-appealed-url .item-detail-text').html($url_html);
            $modal.find('.item-appealed-description .item-detail-text').html($row.find('td[data-key=inspected_description]').attr('data-appealed-description'));


            var $inspected_result = $row.find('td[data-key=appealed_result]').attr('data-value');
            // console.log($inspected_result);
            $modal.find('select[name="detail-appealed-handled-result"]').find("option").prop("selected",false);
            $modal.find('select[name="detail-appealed-handled-result"]').find("option[value='"+$inspected_result+"']").prop("selected",true);


            // var $appealed_description = $row.find('td[data-key=inspected_description]').attr('data-value');
            // console.log($inspected_description);
            $modal.find('textarea[name="detail-appealed-description"]').val('');
            // $modal.find('textarea[name="detail-appealed-description"]').val($appealed_description);

            $modal.modal('show');

        });
        // 【取消】内容详情-申诉-处理
        $(".main-content").on('click', ".modal-cancel-for-detail-appealed-handled", function() {
            var that = $(this);
            var $modal = $('#modal-body-for-detail-appealed-handled');
            $modal.find('select[name="detail-appealed-handled-result"]').prop("checked", false);
            $modal.find('select[name="detail-appealed-handled-result"]').find('option').attr("selected",false);
            $modal.find('select[name="detail-appealed-handled-result"]').find('option[value="-1"]').attr("selected",true);
            $modal.find('textarea[name="detail-appealed-handled-description"]').val('');
            $modal.modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【提交】内容详情-申诉-处理
        $(".main-content").on('click', ".modal-summit-for-detail-appealed-handled", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-detail-appealed-handled');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            var $id = $('input[name="detail-appealed-handled-order-id"]').val();
            var $appealed_handled_result = $('select[name="detail-appealed-handled-result"]').val();
            var $appealed_handled_description = $('textarea[name="detail-appealed-handled-description"]').val();
            // console.log($recording_quality);

            $.post(
                "{{ url('/v1/operate/order/item-appeal-handle') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-appeal-handle",
                    item_id: $('input[name="detail-appealed-handled-order-id"]').val(),
                    appealed_handled_result: $('select[name="detail-appealed-handled-result"]').val(),
                    appealed_handled_description: $('textarea[name="detail-appealed-handled-description"]').val()
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

                        $(".modal-cancel-for-detail-appealed-handled").click();
                        // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);


                        var $row = $table.find('tr.handling');
                        console.log($row);
                        $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-green">申诉·结束</small>');

                        var $inspected_result = '';
                        var $inspected_result_html = '';
                        if($appealed_handled_result == 1)
                        {
                            $inspected_result = '通过';
                            $inspected_result_html = '<small class="btn-xs bg-green">通过</small>';
                        }
                        else if($appealed_handled_result == 9)
                        {
                            $inspected_result = '拒绝';
                            $inspected_result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        $row.find('td[data-key=inspected_result]').attr('data-value',$inspected_result);
                        $row.find('td[data-key=inspected_result]').html($inspected_result_html);
                        $row.find('td[data-key=inspected_description]').attr('data-appealed-value',$appealed_handled_description);

                        $row.find('.modal-show-for-detail-appealed-handled').removeClass('bg-red').addClass('bg-default').addClass('disabled').remove();
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
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

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
                        {{--"{{ url('/item/order-publish') }}",--}}
                        "{{ url('/v1/operate/order/item-publish') }}",
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
                                layer.msg(data.msg);
                                // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
                                $table.DataTable().ajax.reload(null,false);
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
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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
                                $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
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
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");

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
                            inspected_description: $('textarea[name="inspected-description"]').val(),
                            is_distributive_condition: $('input[name="is_distributive_condition"]:checked').val()
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
                                // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);
                                $('#'+$table_id).DataTable().ajax.reload(null,false);
                            }
                        },
                        'json'
                    );
                }
            });
        });


        // 【工单】一键交付
        $(".main-content").on('click', ".item-summit-for-deliver-by-fool", function() {
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


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
                "{{ url('/v1/operate/order/item-deliver-by-fool') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-item-deliver-by-fool",
                    item_id: $that.attr('data-id')
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
                        $('#'+$table_id).DataTable().ajax.reload(null,false);
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








        // 【交付】
        $(".main-content").on('click', ".item-deliver-submit", function() {

            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-order-list').find('tr').removeClass('operating');
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
                "{{ url('/item/order-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:100px;float:left;">[订单]' + $order.id + '</span>' +
                                    '<span style="width:160px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                            })
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已分发客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:160px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';


            var $option_html_for_client = $('#option-list-for-client').html();
            var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();
            var $option_html_for_is_distributive_condition = $('#option-list-for-is_distributive_condition-2').html();

            var $delivered_result = $('select[name="select-delivered-result"]').val();
            var $client_id = $('select[name="select-client-id"]').val();
            var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
            var $is_distributive_condition = $('input[name="option_is_distributive_condition"]:checked').val();

            layer.open({
                time: 0
                ,btn: ['确定', '取消']
                ,title: '工单【交付】！'
                ,area:['480px;']
                ,content:
                    $html+
                    '<select class="form-control select-primary" name="select-client-id" style="width:48%;" id="">'+
                    $option_html_for_client+
                    '</select>'+
                    '<select class="form-control select-primary" name="select-delivered-result" style="width:48%;" id="">'+
                    $option_html_for_delivered_result+
                    '</select>'+
                    '<input type="text" class="form-control" name="input-recording-address" rows="2" placeholder="录音地址"></textarea>'+
                    '<textarea class="form-control" name="textarea-delivered-description" rows="2" placeholder="交付说明"></textarea>'+
                    $option_html_for_is_distributive_condition
                ,yes: function(index){
                    $.post(
                        "{{ url('/item/order-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-deliver",
                            item_id: $that.attr('data-id'),
                            client_id: $('select[name="select-client-id"]').val(),
                            delivered_result: $('select[name="select-delivered-result"]').val(),
                            recording_address: $('input[name="input-recording-address"]').val(),
                            delivered_description: $('textarea[name="textarea-delivered-description"]').val(),
                            is_distributive_condition: $('input[name="option_is_distributive_condition"]:checked').val()
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
                                // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);

                                var $delivered_result = $('select[name="select-delivered-result"]').val();
                                var $client_id = $('select[name="select-client-id"]').val();
                                var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
                                var $is_distributive_condition = $('input[name="option_is_distributive_condition"]:checked').val();

                                $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                // 是否符合分发条件
                                if($is_distributive_condition == 0)
                                {
                                    $row.find('td[data-key=is_distributive_condition]').html('--');
                                }
                                else if($is_distributive_condition == 1)
                                {
                                    $row.find('td[data-key=is_distributive_condition]').html('<small class="btn-xs bg-red">是</small>');
                                }

                                $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                if($client_id != "-1")
                                {
                                    $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                }
                                $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                var $date = new Date();
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                var $second = ('00'+$date.getSeconds()).slice(-2);
                                var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                $row.find('td[data-key=delivered_at]').html($time_html);
                            }
                        },
                        'json'
                    );
                }
            });



                    }
                },
                'json'
            );

        });
        // 【交付】显示
        $(".main-content").on('click', ".item-deliver-show", function() {


            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");
            var $table = $('#'+$table_id);

            var $modal = $('#modal-body-for-deliver-set');
            $modal.attr('data-datatable-id',$table_id);

            var $row = $that.parents('tr');
            $('#datatable-for-order-list').find('tr').removeClass('operating');
            $row.addClass('operating');

            $('.deliver-set-title').html($that.attr("data-id"));
            $('.deliver-set-column-name').html($that.attr("data-name"));
            $('input[name=deliver-set-order-id]').val($that.attr("data-id"));
            $('input[name=deliver-set-column-key]').val($that.attr("data-key"));
            $('#deliver-set-distributed-list').html('');
            $('#deliver-set-distributed-order-list').html('');
            $('#deliver-set-distributed-client-list').html('');

            $modal.modal('show');

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
            var $html_for_order = '';
            var $html_for_distributed = '';

            $.post(
                "{{ url('/item/order-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:120px;float:left;">[工单ID]' + $order.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                                $html_for_order += $html_order;
                            });
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已交付客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:120px;float:left;">[交付ID] ' + $deliver.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                                $html_for_distributed += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';
                        $('#deliver-set-distributed-list').html($html);
                        $('#deliver-set-distributed-order-list').html($html_for_order);
                        $('#deliver-set-distributed-client-list').html($html_for_distributed);


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                    }
                },
                'json'
            );

        });
        // 【交付】【取消】
        $(".main-content").on('click', "#item-cancel-for-deliver-set", function() {
            var that = $(this);
            $('#modal-body-for-deliver-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【交付】确认
        $(".main-content").on('click', "#item-submit-for-deliver-set", function() {
            var $that = $(this);
            var $modal = $('#modal-body-for-deliver-set');
            var $table_id = $modal.attr('data-datatable-id');
            var $table = $('#'+$table_id);

            layer.msg('确定"交付"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/order-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-deliver",
                            item_id: $('input[name="deliver-set-order-id"]').val(),
                            project_id: $('select[name="deliver-set-project-id"]').val(),
                            client_id: $('select[name="deliver-set-client-id"]').val(),
                            delivered_result: $('select[name="deliver-set-delivered-result"]').val(),
                            recording_address: $('input[name="deliver-set-recording-address"]').val(),
                            delivered_description: $('textarea[name="deliver-set-delivered-description"]').val(),
                            is_distributive_condition: $('input[name="deliver-set-is_distributive_condition"]:checked').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');
                            layer.closeAll('loading');

                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                layer.msg($response.msg);
                            }
                            else
                            {
                                layer.msg("已交付");
                                $table.DataTable().ajax.reload(null,false);
                                $('#modal-body-for-deliver-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });
                }
            });
        });




        // 【分发】
        $(".main-content").on('click', ".item-distribute-submit", function() {

            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-order-list').find('tr').removeClass('operating');
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
                "{{ url('/item/order-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【已交付订单】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:100px;float:left;">[订单]' + $order.id + '</span>' +
                                    '<span style="width:160px;float:left;">s[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                            })
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【已交付客户】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:160px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                        var $delivered_result = $('select[name="select-delivered-result"]').val();
                        var $client_id = $('select[name="select-client-id"]').val();
                        var $client_name = $('select[name="select-client-id"]').find('option:selected').html();
                        // console.log($client_name);

                        layer.open({
                            time: 0
                            ,btn: ['确定', '取消']
                            ,title: '工单【分发】！'
                            ,area:['480px;']
                            ,content:
                                $html+
                                '<select class="form-control select-primary" name="select-client-id" style="width:48%;" id="">'+
                                $option_html_for_client+
                                '</select>'+
                                '<select class="form-control select-primary select2-box" name="select-delivered-result" style="width:48%;" id="">'+
                                $option_html_for_delivered_result+
                                '</select>'+
                                '<input type="text" class="form-control" name="input-recording-address" rows="2" placeholder="录音地址"></textarea>'+
                                '<textarea class="form-control" name="textarea-delivered-description" rows="2" placeholder="交付说明"></textarea>'
                            ,yes: function(index){
                                $.post(
                                    "{{ url('/item/order-distribute') }}",
                                    {
                                        _token: $('meta[name="_token"]').attr('content'),
                                        operate: "order-distribute",
                                        item_id: $that.attr('data-id'),
                                        client_id: $('select[name="select-client-id"]').val(),
                                        delivered_result: $('select[name="select-delivered-result"]').val(),
                                        recording_address: $('input[name="input-recording-address"]').val(),
                                        delivered_description: $('textarea[name="textarea-delivered-description"]').val()
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
                                            layer.msg("已分发");
                                        }
                                    },
                                    'json'
                                );
                            }
                        });



                    }
                },
                'json'
            );

        });
        // 【分发】显示
        $(".main-content").on('click', ".item-distribute-show", function() {


            var $that = $(this);
            var $row = $that.parents('tr');
            $('#datatable-for-order-list').find('tr').removeClass('operating');
            $row.addClass('operating');

            $('.distribute-set-title').html($that.attr("data-id"));
            $('.distribute-set-column-name').html($that.attr("data-name"));
            $('input[name=distribute-set-order-id]').val($that.attr("data-id"));
            $('input[name=distribute-set-column-key]').val($that.attr("data-key"));
            $('#distribute-set-distributed-list').html('');
            $('#distribute-set-distributed-order-list').html('');
            $('#distribute-set-distributed-client-list').html('');

            $('#modal-body-for-distribute-set').modal('show');

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
            var $html_for_order = '';
            var $html_for_distributed = '';

            $.post(
                "{{ url('/item/order-deliver-get-delivered') }}",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    operate: "order-deliver-get-delivered",
                    item_id: $that.attr('data-id')
                },
                function(data){

                    layer.closeAll('loading');

                    if(!data.success)
                    {
                        layer.msg(data.msg);
                    }
                    else
                    {
                        if(data.data.order_repeat.length)
                        {
                            $html += '<div>【订单列表】</div>';
                            var $order_list = data.data.order_repeat;
                            $.each($order_list, function(index,$order) {

                                var $client_username = '';
                                if($order.client_er) $client_username = $order.client_er.username;

                                var $project_name = '';
                                if($order.project_er) $project_name = $order.project_er.name;

                                var $html_order =
                                    '<div>'+
                                    '<span style="width:120px;float:left;">[工单ID]' + $order.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户]' + $client_username + '</span>' +
                                    '[项目]' + $project_name +
                                    '</div>';
                                $html += $html_order;
                                $html_for_order += $html_order;
                            });
                            $html += '<br>';
                        }
                        if(data.data.deliver_repeat.length)
                        {
                            $html += '<div>【交付列表】</div>';
                            var $deliver_list = data.data.deliver_repeat;
                            $.each($deliver_list, function(index,$deliver) {

                                var $client_username = '';
                                if($deliver.client_er) $client_username = $deliver.client_er.username;

                                var $project_name = '';
                                if($deliver.project_er) $project_name = $deliver.project_er.name;

                                var $html_deliver =
                                    '<div>' +
                                    '<span style="width:120px;float:left;">[交付ID] ' + $deliver.id + '</span>' +
                                    '<span style="width:240px;float:left;">[客户] ' + $client_username + '</span>' +
                                    '[项目] '+ $project_name +
                                    '</div>';
                                $html += $html_deliver;
                                $html_for_distributed += $html_deliver;
                            })
                            $html += '<br>';
                        }
                        $html += '<br>';
                        $('#distribute-set-distributed-list').html($html);
                        $('#distribute-set-distributed-order-list').html($html_for_order);
                        $('#distribute-set-distributed-client-list').html($html_for_distributed);


                        var $option_html_for_client = $('#option-list-for-client').html();
                        var $option_html_for_delivered_result = $('#option-list-for-delivered-result').html();

                    }
                },
                'json'
            );

        });
        // 【分发】【取消】
        $(".main-content").on('click', "#item-cancel-for-distribute-set", function() {
            var that = $(this);
            $('#modal-body-for-distribute-set').modal('hide').on("hidden.bs.modal", function () {
                $("body").addClass("modal-open");
            });
        });
        // 【分发】确认
        $(".main-content").on('click', "#item-submit-for-distribute-set", function() {
            var $that = $(this);
            layer.msg('确定"分发"么？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/order-distribute') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-distribute",
                            item_id: $('input[name="distribute-set-order-id"]').val(),
                            project_id: $('select[name="distribute-set-project-id"]').val(),
                            client_id: $('select[name="distribute-set-client-id"]').val(),
                            delivered_result: $('select[name="distribute-set-delivered-result"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');
                            layer.closeAll('loading');

                            $response = JSON.parse($response);
                            if(!$response.success)
                            {
                                layer.msg($response.msg);
                            }
                            else
                            {
                                layer.msg("已分发");
                                $('#modal-body-for-distribute-set').modal('hide').on("hidden.bs.modal", function () {
                                    $("body").addClass("modal-open");
                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });
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






        // 【批量操作】全选or反选
        $(".main-content").on('click', '#check-review-all', function () {
            console.log('#check-review-all.click');
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            $('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】全选or反选
        $(".main-content").on('click', '.check-review-all', function () {
            console.log('.check-review-all.click');
            var $that = $(this);
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            $datatable_wrapper.find('input[name="bulk-id"]').prop('checked',this.checked); // checked为true时为默认显示的状态
        });
        // 【批量操作】批量-导出
        $(".main-content").on('click', '#bulk-submit-for-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-content").on('click', '#bulk-submit-for-delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/order-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);

                                if($response.msg != '') layer.msg($response.msg);


                                var $ids = $response.data.ids;
                                // console.log($ids);

                                $ids.forEach(function(value,index) {
                                    console.log(value);
                                    var $item = $('.order_id[data-value='+value+']');
                                    var $row = $item.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);
                                });

                                {{--$('input[name="bulk-id"]:checked').each(function() {--}}

                                {{--    var $that = $(this);--}}
                                {{--    var $row = $that.parents('tr');--}}

                                {{--    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();--}}
                                {{--    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();--}}
                                {{--    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();--}}
                                {{--    console.log($client_name);--}}

                                {{--    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');--}}
                                {{--    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');--}}
                                {{--    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');--}}
                                {{--    $row.find('td[data-key=client_id]').attr('data-value',$client_id);--}}
                                {{--    if($client_id != "-1")--}}
                                {{--    {--}}
                                {{--        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');--}}
                                {{--    }--}}
                                {{--    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');--}}
                                {{--    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');--}}


                                {{--    var $date = new Date();--}}
                                {{--    var $year = $date.getFullYear();--}}
                                {{--    var $month = ('00'+($date.getMonth()+1)).slice(-2);--}}
                                {{--    var $day = ('00'+($date.getDate())).slice(-2);--}}
                                {{--    var $hour = ('00'+$date.getHours()).slice(-2);--}}
                                {{--    var $minute = ('00'+$date.getMinutes()).slice(-2);--}}
                                {{--    var $second = ('00'+$date.getSeconds()).slice(-2);--}}
                                {{--    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;--}}
                                {{--    $row.find('td[data-key=delivered_at]').html($time_html);--}}

                                {{--});--}}
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });
        // 【批量操作】批量-导出
        $(".main-content").off('click', '.bulk-submit-for-order-export').on('click', '.bulk-submit-for-order-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $that = $(this);
            var $item_category = $that.data('item-category');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/v1/operate/statistic/order-export-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);


        });
        // 【批量操作】批量-交付
        $(".main-content").off('click', '.bulk-submit-for-order-delivered').on('click', '.bulk-submit-for-order-delivered', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).val()+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            // var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            // window.open($url);

            layer.msg('确定"批量交付"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){

                    layer.close(index);

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
                        "{{ url('/item/order-bulk-deliver') }}",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            operate: "order-delivered-bulk",
                            ids: $ids,
                            project_id:$('select[name="bulk-operate-delivered-project"]').val(),
                            client_id:$('select[name="bulk-operate-delivered-client"]').val(),
                            delivered_result:$('select[name="bulk-operate-delivered-result"]').val(),
                            delivered_description:$('input[name="bulk-operate-delivered-description"]').val()
                        },
                        'json'
                    )
                        .done(function($response) {
                            console.log('done');

                            $response = JSON.parse($response);
                            if(!$response.success) layer.msg($response.msg);
                            else
                            {
                                layer.closeAll('loading');
                                // $('#datatable-for-order-list').DataTable().ajax.reload(null,false);

                                $('input[name="bulk-id"]:checked').each(function() {

                                    var $that = $(this);
                                    var $row = $that.parents('tr');

                                    var $delivered_result = $('select[name="bulk-operate-delivered-result"]').val();
                                    var $client_id = $('select[name="bulk-operate-delivered-client"]').val();
                                    var $client_name = $('select[name="bulk-operate-delivered-client"]').find('option:selected').html();
                                    console.log($client_name);

                                    $row.find('td[data-key=deliverer_name]').html('<a href="javascript:void(0);">{{ $me->true_name }}</a>');
                                    $row.find('td[data-key=delivered_status]').html('<small class="btn-xs bg-blue">已交付</small>');
                                    $row.find('td[data-key=delivered_result]').html('<small class="btn-xs bg-olive">'+$delivered_result+'</small>');
                                    $row.find('td[data-key=client_id]').attr('data-value',$client_id);
                                    if($client_id != "-1")
                                    {
                                        $row.find('td[data-key=client_id]').html('<a href="javascript:void(0);">'+$client_name+'</a>');
                                    }
                                    $row.find('td[data-key=order_status]').html('<small class="btn-xs bg-olive">已交付</small>');
                                    // $row.find('.item-deliver-submit').replaceWith('<a class="btn btn-xs bg-green disabled">已交</a>');


                                    var $date = new Date();
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);
                                    var $time_html = $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                                    $row.find('td[data-key=delivered_at]').html($time_html);

                                });
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(jqXHR);
                            console.log(textStatus);
                            console.log(errorThrown);
                            layer.msg('服务器错误！');

                        })
                        .always(function(jqXHR, textStatus) {
                            layer.closeAll('loading');
                            console.log(jqXHR);
                            console.log(textStatus);
                        });

                }
            });

        });



        // 【交付列表】【批量操作】批量-导出
        $(".main-content").on('click', '#bulk-submit-for-delivery-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            // console.log($ids);

            var $url = url_build('/statistic/statistic-export-for-order-by-ids?ids='+$ids);
            window.open($url);

        });
        // 【交付列表】【批量操作】批量-更改导出状态
        $(".main-content").on('click', '#bulk-submit-for-exported', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
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
        // 【交付列表】【批量操作】批量-导出
        $(".main-content").off('click', '.bulk-submit-for-delivery-export').on('click', '.bulk-submit-for-delivery-export', function() {
            // var $checked = [];
            // $('input[name="bulk-id"]:checked').each(function() {
            //     $checked.push($(this).val());
            // });
            // console.log($checked);

            var $that = $(this);
            var $item_category = $that.data('item-category');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');

            var $ids = '';
            $datatable_wrapper.find('input[name="bulk-id"]:checked').each(function() {
                $ids += $(this).attr('data-order-id')+'-';
            });
            $ids = $ids.slice(0, -1);
            console.log($ids);

            var $url = url_build('/v1/operate/statistic/order-export-by-ids?item_category='+$item_category+'&ids='+$ids);
            window.open($url);

        });
        // 【交付列表】【批量操作】批量-更改导出状态
        $(".main-content").off('click', '.bulk-submit-for-delivery-exported').on('click', '.bulk-submit-for-delivery-exported', function() {
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




        // 【通用】修改记录
        $(".main-content").on('click', ".modal-show-for-order-delivery-record", function() {
            var $that = $(this);
            var $id = $(this).data('id');
            var $datatable_wrapper = $that.closest('.datatable-wrapper');
            var $item_category = $datatable_wrapper.data('datatable-item-category');
            var $table_id = $datatable_wrapper.find('table').filter('[id][id!=""]').attr("id");


            Datatable_Order_Operation_Record.init($id);

            $('#modal-for-order-delivery-record-datatable').modal('show');
        });





        // select2 项目
        {{--$('.select2-project').select2({--}}
        {{--    ajax: {--}}
        {{--        url: "{{ url('/v1/operate/select2/select2_project') }}",--}}
        {{--        type: 'post',--}}
        {{--        dataType: 'json',--}}
        {{--        delay: 250,--}}
        {{--        data: function (params) {--}}
        {{--            return {--}}
        {{--                _token: $('meta[name="_token"]').attr('content'),--}}
        {{--                item_category: this.data('item-category'),--}}
        {{--                keyword: params.term, // search term--}}
        {{--                page: params.page--}}
        {{--            };--}}
        {{--        },--}}
        {{--        processResults: function (data, params) {--}}

        {{--            params.page = params.page || 1;--}}
        {{--            return {--}}
        {{--                results: data,--}}
        {{--                pagination: {--}}
        {{--                    more: (params.page * 30) < data.total_count--}}
        {{--                }--}}
        {{--            };--}}
        {{--        },--}}
        {{--        cache: true--}}
        {{--    },--}}
        {{--    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work--}}
        {{--    minimumInputLength: 0,--}}
        {{--    theme: 'classic'--}}
        {{--});--}}

        $('.select2-project').each(function() {
            // 获取当前 Select2 元素的 jQuery 对象
            const $select = $(this);

            // 动态查找最近的模态框父容器
            const $modalWrapper = $select.closest('.modal-wrapper');

            // 初始化 Select2
            $select.select2({
                ajax: {
                    url: "{{ url('/v1/operate/select2/select2_project') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: $('meta[name="_token"]').attr('content'),
                            item_category: $select.data('item-category'), // 使用 $select 获取 data 属性
                            keyword: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
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
                escapeMarkup: function(markup) { return markup; },
                dropdownParent: $modalWrapper.find('.modal-content'), // 直接使用找到的模态框元素
                minimumInputLength: 0,
                theme: 'classic'
            });
        });



    });
</script>