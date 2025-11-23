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
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
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
                        if(true)
                        {
                            $(nTd).addClass('by_id');
                            $(nTd).attr('data-id',row.id).attr('data-name','ID');
                            $(nTd).attr('data-key','by_id').attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.id;
                    }
                },
                {
                    "title": "状态",
                    "data": "api_status",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('api_status');
                            $(nTd).attr('data-id',row.id).attr('data-name','状态');
                            $(nTd).attr('data-key','api_status').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','状态');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {

                        if(row.deleted_at != null)
                        {
                            return '<small class="btn-xs bg-black">已删除</small>';
                        }


                        if(data == 0)
                        {

                            return '<small class="btn-xs bg-aqua">待处理</small>';
                        }
                        else if(data == 1)
                        {
                            return '<small class="btn-xs bg-teal">待审核</small>';
                        }
                        else if(data == 9)
                        {
                            return '<small class="btn-xs bg-blue">已审核</small>';
                        }
                        else
                        {
                            return '<small class="btn-xs bg-black">未知状态</small>';
                        }
                    }
                },
                {
                    "title": "审核结果",
                    "data": "inspected_result",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','审核结果');
                            $(nTd).attr('data-key','inspected_result').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','审核结果');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!row.inspected_at) return '--';
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '--';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-green">通过</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-red">驳回</small>';
                        }
                        else if(data == 7)
                        {
                            $result_html = '<small class="btn-xs bg-yellow">异议</small>';
                        }
                        else
                        {
                            $result_html = '--';
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
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','是否重复');
                            $(nTd).attr('data-key','is_repeat').attr('data-value',data);

                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','是否重复');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
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
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
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
                        if(true)
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
                        if(true)
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
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
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
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
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
                        if(true)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set-');
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
                @if(in_array($me->user_type,[0,1,9,11,61,66,71,77]))
                {
                    "title": "录音播放",
                    "data": "recording_address",
                    "className": "",
                    "width": "320px",
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
                        if(row.recording_address)
                        {
                            return '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+row.recording_address+'" type="audio/mpeg"></audio>';
                        }
                        else return '';
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
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_record = '';
                        var $html_publish = '';
                        var $html_inspected = '';
                        var $html_detail_inspected = '';
                        var $html_preprocess = '';

                        if(row.api_status == 0)
                        {
                            $html_preprocess = '<a class="btn btn-xs bg-aqua by-item-preprocess-submit" data-id="'+data+'">预处理</a>';
                        }

//                            if(row.is_me == 1 && row.item_active == 0)
                        if(row.is_published == 0)
                        {
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';
                        }
                        else
                        {
                            $html_detail = '<a class="btn btn-xs bg-primary item-modal-show-for-detail" data-id="'+data+'">详情</a>';
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-operation-record" data-id="'+data+'">记录</a>';




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
                                $html_edit = '';
                                $html_publish = '';
                            }



                        }


                        var $html =
                            $html_edit+
                            $html_publish+
                            $html_preprocess+
                            $html_detail_inspected+
                            $html_record+
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