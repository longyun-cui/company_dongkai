<script>

    function Datatable_for_BY_List($tableId)
    {

        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        var table = $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 100, 200], ["10", "50", "100", "200"]],
            "processing": true,
            "serverSide": true,
            "searching": true,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
//                "scrollY": true,
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/v1/operate/by/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.item_category = 1;
                    d.id = $tableSearch.find('input[name="by-id"]').val();
                    d.remark = $tableSearch.find('input[name="by-remark"]').val();
                    d.description = $tableSearch.find('input[name="by-description"]').val();
                    d.delivered_date = $tableSearch.find('input[name="by-delivered_date"]').val();
                    d.assign = $tableSearch.find('input[name="by-assign"]').val();
                    d.assign_start = $tableSearch.find('input[name="by-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="by-ended"]').val();
                    d.name = $tableSearch.find('input[name="by-name"]').val();
                    d.title = $tableSearch.find('input[name="by-title"]').val();
                    d.keyword = $tableSearch.find('input[name="by-keyword"]').val();
                    d.project = $tableSearch.find('select[name="by-project"]').val();
                    d.client = $tableSearch.find('select[name="by-client"]').val();
                    d.status = $tableSearch.find('select[name="by-status"]').val();
                    d.order_type = $tableSearch.find('select[name="by-type"]').val();
                    d.client_name = $tableSearch.find('input[name="by-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="by-client-phone"]').val();
                    d.is_wx = $tableSearch.find('select[name="by-is-wx"]').val();
                    d.is_repeat = $tableSearch.find('select[name="by-is-repeat"]').val();
                    d.created_type = $tableSearch.find('select[name="by-created-type"]').val();
                    d.recording_quality = $tableSearch.find('select[name="by-recording-quality"]').val();
                    d.inspected_status = $tableSearch.find('select[name="by-inspected-status"]').val();
                    d.inspected_result = $tableSearch.find('select[name="by-inspected-result[]"]').val();
                    d.location_city = $tableSearch.find('select[name="by-city"]').val();
                    d.location_district = $tableSearch.find('select[name="by-district[]"]').val();
                },
            },
            "fixedColumns": {

                @if($me->department_district_id == 0)
                "leftColumns": "@if($is_mobile_equipment) 1 @else 5 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                @else
                "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                @endif

            },
            "columnDefs": [
                    {{--@if(!in_array($me->user_type,[0,1,11]))--}}
{{--                @if($me->department_district_id != 0)--}}
{{--                {--}}
{{--                    "targets": [0,5,6,7,8,9,10,11],--}}
{{--                    "visible": false,--}}
{{--                }--}}
{{--                @endif--}}
            ],
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    // "data": "id",
                    "data": null,
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+row.id+'"></label>';
                    }
                },
//                    {
//                        "title": "序号",
//                        "width": "32px",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                {
                    "title": "ID",
                    "data": "id",
                    "className": "",
                    "width": "40px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_id');
                            $(nTd).attr('data-id',row.id).attr('data-name','工单ID');
                            $(nTd).attr('data-key','order_id').attr('data-value',row.id);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.id;
                    }
                },
                {
                    "title": "工单状态",
                    "className": "",
                    "width": "72px",
                    "data": "id",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_status');
                            $(nTd).attr('data-id',row.id).attr('data-name','工单状态');
                            $(nTd).attr('data-key','order_status').attr('data-value',row.id);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
//                            return data;

                        if(row.deleted_at != null)
                        {
                            return '<small class="btn-xs bg-black">已删除</small>';
                        }



                        // if(row.client_id > 0)
                        // {
                        //     return '<small class="btn-xs bg-olive">已交付</small>';
                        // }

                        if(row.inspected_at)
                        {

                            if(row.inspected_status == 1)
                            {
                                if(row.appealed_status == 0)
                                {
                                    return '<small class="btn-xs bg-blue">已审核</small>';
                                }
                                else if(row.appealed_status == 1)
                                {
                                    return '<small class="btn-xs bg-red">申诉中</small>';
                                }
                                else if(row.appealed_status == 9)
                                {
                                    return '<small class="btn-xs bg-green">申诉·结束</small>';
                                }
                                else
                                {
                                    return '<small class="btn-xs bg-blue">已审核</small>';
                                }
                            }
                            else if(row.inspected_status == 9)
                            {
                                return '<small class="btn-xs bg-aqua">等待再审</small>';
                            }
                            else return '--';
                        }
                        else
                        {
                            return '<small class="btn-xs bg-aqua">待审核</small>';
                        }

                    }
                },
                {
                    "title": "审核结果",
                    "data": "inspected_result",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','审核结果');
                            $(nTd).attr('data-key','inspected_result').attr('data-value',data);

                            $(nTd).attr('data-column-name','审核结果');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!row.inspected_at) return '--';
                        var $result_html = '';
                        if(data == "通过" || data == "内部通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "拒绝")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "重复")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                        }
                        else if(data == "重复•可分发")
                        {
                            console.log('x1');
                            if("{{ in_array($me->user_type,[0,1,9,11,61,66]) }}" == "1")
                            {
                                console.log('x');
                                $result_html = '<small class="btn-xs bg-yellow">重复•可分发</small>';
                            }
                            else
                            {
                                $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                            }
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "是否重复",
                    "data": "is_repeat",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            // $(nTd).addClass('modal-show-for-info-radio-set-');
                            // $(nTd).attr('data-id',row.id).attr('data-name','是否重复');
                            $(nTd).attr('data-key','is_repeat').attr('data-value',data);
                            // $(nTd).attr('data-column-name','是否重复');
                            // if(data) $(nTd).attr('data-operate-type','edit');
                            // else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 0) return '--';
                        else return '<small class="btn-xs btn-primary">是</small><small class="btn-xs btn-danger">'+(data+1)+'</small>';
                    }
                },
                {
                    "title": "客户姓名",
                    "data": "client_name",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户姓名');
                            $(nTd).attr('data-key','client_name').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','客户姓名');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "客户电话",
                    "data": "client_phone",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户电话');
                            $(nTd).attr('data-key','client_phone').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','客户电话');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "客户意向",
                    "data": "client_intention",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户意向');
                            $(nTd).attr('data-key','client_intention').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','客户意向');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(data == "到店")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "A类")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "B类")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "C类")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "A")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "B")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "C")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else
                        {
                            $result_html = data;
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "牙齿数量",
                    "data": "teeth_count",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','牙齿数量');
                            $(nTd).attr('data-key','teeth_count').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','牙齿数量');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "微信同号",
                    "data": "is_wx",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','是否+V');
                            $(nTd).attr('data-key','is_wx').attr('data-value',data);

                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','是否+V');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs btn-primary">是</small>';
                        else return '--';
                    }
                },
                {
                    "title": "所在城市",
                    "data": "location_city",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','所在城市');
                            $(nTd).attr('data-key','location_city').attr('data-value',data);
                            $(nTd).attr('data-key2','location_district').attr('data-value2',row.location_district);

                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','所在城市');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        else {
                            if(!row.location_district) return data;
                            else return data+' - '+row.location_district;
                        }
                    }
                },
                {
                    "title": "通话小结",
                    "data": "description",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','通话小结');
                            $(nTd).attr('data-key','description').attr('data-value',data);

                            $(nTd).attr('data-column-type','textarea');
                            $(nTd).attr('data-column-name','通话小结');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');

                            if(row.recording_address_list)
                            {
                                var $recording_address = row.recording_address_list;
                                if($recording_address)
                                {
                                    var $recording_list = JSON.parse($recording_address);
                                    var $recording_list_html = '';
                                    $.each($recording_list, function(index, value)
                                    {
                                        var $audio_html = '<audio controls controlsList="nodownload" style="width:480px;height:40px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                        $recording_list_html += $audio_html;
                                    });
                                    $(nTd).attr('data-recording-address',$recording_list_html);
                                }
                            }
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        if(data) return '<small class="btn-xs bg-yellow">双击查看</small>';
                        else return '';
                    }
                },
                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                {
                    "title": "录音播放",
                    "data": "recording_address",
                    "className": "",
                    "width": "400px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                            $(nTd).attr('data-key','recording_address_play').attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        if($.trim(data))
                        {
                            if(row.recording_address)
                            {
                                return '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+row.recording_address+'" type="audio/mpeg"></audio>';
                            }
                            else return '';
                        }
                    }
                },
                {
                    "title": "录音下载",
                    "data": "recording_address_list",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            console.log(iRow);
                            console.log(iCol);
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).attr('data-id',row.id).attr('data-name','录音下载');
                            $(nTd).attr('data-key','recording_address_download').attr('data-value',data);
                            $(nTd).attr('data-address-list',data);
                            $(nTd).attr('data-address',row.recording_address);
                            $(nTd).attr('data-call-record-id',row.call_record_id);
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;

                        if($.trim(data))
                        {
                            try
                            {
                                var $recording_list = JSON.parse(data);
                                // return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                                var $recording_download = '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载</a>';
                                var $recording_get = '<a class="btn btn-xs item-get-recording-list-submit" data-id="'+row.id+'">获取</a>';
                                return $recording_get + $recording_download;
                            }
                            catch(e)
                            {
                                // console.log(e);
                                return '';
                            }
                        }
                        else
                        {
                            if(row.recording_address)
                            {
                                return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                            }
                            else
                            {
                                return '<a class="btn btn-xs item-get-recording-list-submit" data-id="'+row.id+'">获取录音</a>';
                            }
                        }

                        if($.trim(data) || row.recording_address)
                        {
                            return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                        }
                        else
                        {
                            return '<a class="btn btn-xs item-get-recording-list-submit" data-id="'+row.id+'">获取录音1</a>';
                        }
                    }
                },
                @endif
                {
                    "title": "审核人",
                    "data": "inspector_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        @if(in_array($me->user_type,[41,81,84,88]))
                            return row.inspector == null ? '--' : '****';
                        @else
                            return row.inspector == null ? '--' : '<a href="javascript:void(0);">'+row.inspector.username+'</a>';
                        @endif
                    }
                },
                {
                    "title": "审核时间",
                    "data": 'inspected_at',
                    "className": "",
                    "width": "120px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
//                            return data;
                        var $date = new Date(data*1000);
                        var $year = $date.getFullYear();
                        var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        var $day = ('00'+($date.getDate())).slice(-2);
                        var $hour = ('00'+$date.getHours()).slice(-2);
                        var $minute = ('00'+$date.getMinutes()).slice(-2);
                        var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                    }
                },
                {
                    "title": "审核说明",
                    "data": "inspected_description",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','审核说明');
                            $(nTd).attr('data-key','inspected_description').attr('data-value',data).attr('data-appealed-url',row.appealed_url).attr('data-appealed-description',row.appealed_description);

                            $(nTd).attr('data-column-type','textarea');
                            $(nTd).attr('data-column-name','审核说明');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        if(data) return '<small class="btn-xs bg-yellow">双击查看</small>';
                        else return '';
                    }
                },
                {
                    "title": "创建时间",
                    "data": 'created_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
//                            return data;
                        var $date = new Date(data*1000);
                        var $year = $date.getFullYear();
                        var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        var $day = ('00'+($date.getDate())).slice(-2);
                        var $hour = ('00'+$date.getHours()).slice(-2);
                        var $minute = ('00'+$date.getMinutes()).slice(-2);
                        var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "className": "",
                    "width": "180px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_record = '';
                        var $html_delete = '';
                        var $html_publish = '';
                        var $html_abandon = '';
                        var $html_completed = '';
                        var $html_verified = '';
                        var $html_inspected = '';
                        var $html_detail_inspected = '';
                        var $html_push = '';
                        var $html_deliver = '';
                        var $html_distribute = '';
                        var $html_appeal = '';
                        var $html_appeal_handle = '';


                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs btn-danger item-admin-disable-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs btn-success item-admin-enable-submit" data-id="'+data+'">启用</a>';
                        }

//                            if(row.is_me == 1 && row.item_active == 0)
                        if(row.is_published == 0)
                        {
                            $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                            // $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                            $html_edit = '<a class="btn btn-xs btn-primary- by-edit-show" data-id="'+data+'">编辑</a>';
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';
                            $html_verified = '<a class="btn btn-xs btn-default disabled">验证</a>';
                            $html_delete = '<a class="btn btn-xs bg-black item-delete-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            if(row.inspected_status == 1 && row.inspected_result == '二次待审')
                            {
                                $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                                $html_publish = '<a class="btn btn-xs bg-olive item-publish-submit" data-id="'+data+'">发布</a>';
                            }
                            $html_detail = '<a class="btn btn-xs bg-primary item-modal-show-for-detail" data-id="'+data+'">详情</a>';
//                                $html_travel = '<a class="btn btn-xs bg-olive item-modal-show-for-travel" data-id="'+data+'">行程</a>';
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';


                            if(row.is_completed == 1)
                            {
                                $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                            }
                            else
                            {
                                if(row.item_status == 97)
                                {
                                    // $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                    $html_abandon = '<a class="btn btn-xs bg-teal item-reuse-submit" data-id="'+data+'">复用</a>';
                                }
                                else $html_abandon = '<a class="btn btn-xs bg-gray item-abandon-submit" data-id="'+data+'">弃用</a>';
                            }

                            // 验证
                            if(row.verifier_id == 0)
                            {
                                $html_verified = '<a class="btn btn-xs bg-teal item-verify-submit" data-id="'+data+'">验证</a>';
                            }
                            else
                            {
                                $html_verified = '<a class="btn btn-xs bg-aqua-gradient disabled">已验</a>';
                            }

                            // 审核
                            if("{{ in_array($me->user_type,[0,1,11,61,66,71,77]) }}")
                            {
                                if(row.created_type == 9)
                                {
                                    $html_inspected = '<a class="btn btn-xs bg-default disabled">审核</a>';
                                    $html_detail_inspected = '<a class="btn btn-xs bg-default disabled">审核</a>';
                                }
                                else
                                {
                                    if(row.inspector_id == 0)
                                    {
                                        $html_inspected = '<a class="btn btn-xs bg-teal item-inspect-submit" data-id="'+data+'">审核</a>';
                                        $html_detail_inspected = '<a class="btn btn-xs bg-teal item-modal-show-for-detail-inspected" data-id="'+data+'">审核</a>';
                                    }
                                    else
                                    {
                                        // $html_inspected = '<a class="btn btn-xs bg-aqua-gradient disabled">已审</a>';
                                        $html_inspected = '<a class="btn btn-xs bg-blue item-inspect-submit" data-id="'+data+'">再审</a>';
                                        $html_detail_inspected = '<a class="btn btn-xs bg-blue item-modal-show-for-detail-inspected" data-id="'+data+'">再审</a>';
                                    }
                                }

                                @if($me->department_district_id == 0)
                                if(row.delivered_status == 0)
                                {
                                    // $html_push = '<a class="btn btn-xs bg-teal item-modal-show-for-deliver" data-id="'+data+'" data-key="client_id">交付</a>';
                                    // $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-submit" data-id="'+data+'">交付</a>';
                                    $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-show" data-id="'+data+'">交付</a>';
                                }
                                else
                                {
                                    // $html_deliver = '<a class="btn btn-xs bg-green disabled- item-deliver-submit" data-id="'+data+'">再交4</a>';
                                    $html_deliver = '<a class="btn btn-xs bg-yellow item-deliver-show" data-id="'+data+'">重交</a>';
                                }

                                if(row.project_er == null)
                                {
                                    $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                                }
                                else
                                {
                                    if(row.project_er.is_distributive == 1)
                                    {

                                        // $html_distribute = '<a class="btn btn-xs bg-green item-distribute-submit" data-id="'+data+'">分发</a>';
                                        $html_distribute = '<a class="btn btn-xs bg-green item-distribute-show" data-id="'+data+'">分发</a>';
                                    }
                                    else
                                    {
                                        $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                                    }
                                }
                                @endif
                                    $html_edit = '';
                                $html_publish = '';
                            }


                            // 申诉
                            if("{{ in_array($me->user_type,[0,1,81,84,88]) }}")
                            {
                                if(row.appealed_status == 0 && row.inspected_result == '拒绝')
                                {
                                    $html_appeal = '<a class="btn btn-xs bg-red modal-show-for-detail-appealed" data-id="'+data+'">申诉</a>';
                                }
                            }

                            // 申诉处理
                            if("{{ in_array($me->user_type,[0,1,91]) }}")
                            {
                                if(row.appealed_status == 1)
                                {
                                    $html_appeal_handle = '<a class="btn btn-xs bg-red modal-show-for-detail-appealed-handled" data-id="'+data+'">处理</a>';
                                }
                            }

                        }





//                            if(row.deleted_at == null)
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-black item-admin-delete-submit" data-id="'+data+'">删除</a>';
//                            }
//                            else
//                            {
//                                $html_delete = '<a class="btn btn-xs bg-grey item-admin-restore-submit" data-id="'+data+'">恢复</a>';
//                            }


                        var $html =
//                                    $html_able+
//                                    '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
//                                 $html_completed+
                            $html_edit+
                            $html_publish+
                            // $html_detail+
                            // $html_verified+
                            $html_detail_inspected+
                            // $html_inspected+
                            $html_delete+
                            $html_push+
                            $html_appeal+
                            $html_appeal_handle+
                            $html_deliver+
                            $html_distribute+
                            $html_record+
                            // $html_abandon+
                            // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                            // '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                            // $more_html+
                            '';
                        return $html;

                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('by-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

        window.dataTableInstances[table_Id] = table;

        return table;
    }
</script>