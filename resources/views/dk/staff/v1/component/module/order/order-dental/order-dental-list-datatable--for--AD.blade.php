<script>

    window.dataTableInstances = window.dataTableInstances || {};

    function Datatable__for__Order_List($tableId)
    {
        console.log($tableId);
        var table_Id = $tableId
        if (window.dataTableInstances[table_Id])
        {
            return window.dataTableInstances[table_Id];
        }

        let $that = $('#'+$tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        var table = $('#'+$tableId).DataTable({
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
                'url': "{{ url('/o1/order/order-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.order_category = 1;
                    d.id = $tableSearch.find('input[name="order-id"]').val();
                    d.remark = $tableSearch.find('input[name="order-remark"]').val();
                    d.description = $tableSearch.find('input[name="order-description"]').val();
                    d.delivered_date = $tableSearch.find('input[name="order-delivered_date"]').val();
                    d.assign = $tableSearch.find('input[name="order-assign"]').val();
                    d.assign_start = $tableSearch.find('input[name="order-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="order-ended"]').val();
                    d.name = $tableSearch.find('input[name="order-name"]').val();
                    d.title = $tableSearch.find('input[name="order-title"]').val();
                    d.keyword = $tableSearch.find('input[name="order-keyword"]').val();
                    d.department_district = $tableSearch.find('select[name="order-department-district[]"]').val();
                    d.staff = $tableSearch.find('select[name="order-staff"]').val();
                    d.distribute_type = $tableSearch.find('select[name="order-distribute-type"]').val();
                    d.project = $tableSearch.find('select[name="order-project"]').val();
                    d.client = $tableSearch.find('select[name="order-client"]').val();
                    d.status = $tableSearch.find('select[name="order-status"]').val();
                    d.order_type = $tableSearch.find('select[name="order-type"]').val();
                    d.client_name = $tableSearch.find('input[name="order-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="order-client-phone"]').val();
                    d.client_type = $tableSearch.find('select[name="order-client-type"]').val();
                    d.is_wx = $tableSearch.find('select[name="order-is-wx"]').val();
                    d.is_repeat = $tableSearch.find('select[name="order-is-repeat"]').val();
                    d.created_type = $tableSearch.find('select[name="order-created-type"]').val();
                    d.recording_quality = $tableSearch.find('select[name="order-recording-quality"]').val();
                    d.inspected_status = $tableSearch.find('select[name="order-inspected-status"]').val();
                    d.inspected_result = $tableSearch.find('select[name="order-inspected-result[]"]').val();
                    d.appealed_status = $tableSearch.find('select[name="order-appealed-status"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();
                    d.location_city = $tableSearch.find('select[name="order-city"]').val();
                    d.location_district = $tableSearch.find('select[name="order-district[]"]').val();
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
                    @if($me->department_district_id != 0)
                {
                    "targets": [0,5,6,7,8,9,10,11],
                    "visible": false,
                }
                @endif
            ],
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    "name": "checkbox",
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
                    "name": "id",
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
                    "title": "来源",
                    "name": "created_type",
                    "data": "created_type",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        var $result_html = '';
                        if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-green">人工</small>';
                        }
                        else if(data == 91)
                        {
                            $result_html = '<small class="btn-xs bg-red">百应AI</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-red">API</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-yellow">导入</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "工单状态",
                    "name": "order_status",
                    "data": "id",
                    "className": "",
                    "width": "72px",
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

                        if(row.item_status == 97)
                        {
                            return '<small class="btn-xs bg-navy">已弃用</small>';
                        }

                        if(row.is_published == 0)
                        {
                            return '<small class="btn-xs bg-teal">未发布</small>';
                        }
                        else
                        {
                            if(row.is_completed == 1)
                            {
                                return '<small class="btn-xs bg-olive">已结束</small>';
                            }
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
                            if(row.created_type == 9)
                            {
                                return '<small class="btn-xs bg-blue">导入</small>';
                            }
                            else
                            {
                                return '<small class="btn-xs bg-aqua">待审核</small>';
                            }
                        }

                    }
                },
                {
                    "title": "审核结果",
                    "name": "inspected_result",
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
                        if(data == "通过" || data == "折扣通过" || data == "郊区通过" || data == "内部通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "拒绝")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "拒绝可交付")
                        {
                            if("{{ in_array($me->user_type,[0,1,9,11,61,66]) }}" == "1")
                            {
                                console.log('x');
                                $result_html = '<small class="btn-xs bg-red">拒绝可交付</small>';
                            }
                            else
                            {
                                $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                            }
                        }
                        else if(data == "不合格")
                        {
                            if("{{ in_array($me->user_type,[0,1,9,11,61,66]) }}" == "1")
                            {
                                console.log('x');
                                $result_html = '<small class="btn-xs bg-red">不合格</small>';
                            }
                            else
                            {
                                $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                            }
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
                    "title": "创建人",
                    "name": "creator_id",
                    "data": "creator_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a class="caller-control" data-id="'+data+'" data-title="'+row.creator.username+'">'+row.creator.username+'</a>';
                    }
                },
                {
                    "title": "发布时间",
                    "name": 'published_at',
                    "data": 'published_at',
                    "className": "",
                    "width": "120px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if("{{ in_array($me->user_type,[0,1,11,19]) }}")
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-phone-pool-info');
                            $(nTd).attr('data-id',row.id).attr('data-name','电话池');
                            $(nTd).attr('data-key','pool').attr('data-value',row.id);
                            $(nTd).attr('data-phone',row.client_phone);
                            $(nTd).attr('data-city',row.location_city);

                            $(nTd).attr('data-column-type','info');
                            $(nTd).attr('data-column-name','电话池');
                        }
                    },
                    render: function(data, type, row, meta) {
//                            return data;
                        if(!data) return '';
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
                    "title": "班次",
                    "name": "field_2",
                    "data": "field_2",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','班次');
                            $(nTd).attr('data-key','field_2').attr('data-value',data);

                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','班次');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs bg-green">白班</small>';
                        else if(data == 9) return '<small class="btn-xs bg-navy">夜班</small>';
                        else return '--';
                    }
                },
                {
                    "title": "项目",
                    "name": "project_id",
                    "data": "project_id",
                    "className": "",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','项目');
                            $(nTd).attr('data-key','project_id').attr('data-value',data);
                            if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                if(row.project_er.alias_name)
                                {
                                    $(nTd).attr('data-option-name',row.project_er.name+' ('+row.project_er.alias_name+')');
                                }
                                else
                                {
                                    $(nTd).attr('data-option-name',row.project_er.name);
                                }
                            }

                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','项目');

                            if(row.project_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.project_er == null)
                        {
                            return '未指定';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                        }
                    }
                },
                {
                    "title": "是否重复",
                    "name": "is_repeat",
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
                    "name": "client_name",
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
                    "name": "client_phone",
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
                    "title": "患者类型",
                    "name": "client_type",
                    "data": "client_type",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','患者类型');
                            $(nTd).attr('data-key','client_type').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','患者类型');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs ">未选择</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">种植牙</small>';
                        }
                        else if(data == 2)
                        {
                            $result_html = '<small class="btn-xs bg-green">矫正</small>';
                        }
                        else if(data == 3)
                        {
                            $result_html = '<small class="btn-xs bg-red">正畸</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-black">其他</small>';
                        }
                        else
                        {
                            $result_html = '未知类型';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "客户意向",
                    "name": "client_intention",
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
                    "name": "teeth_count",
                    "data": "field_1",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(data == 1) return '1-2颗';
                        else if(data == 2) return '3-5颗';
                        else if(data == 3) return '6颗';
                        else if(data == 11) return '半口';
                        else if(data == 19) return '全口';
                        else if(data == 99) return '其他';
                        else return data;
                    }
                },
                {
                    "title": "是否+V",
                    "name": "is_wx",
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
                    "title": "微信号",
                    "name": "wx_id",
                    "data": "wx_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','微信号');
                            $(nTd).attr('data-key','wx_id').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','微信号');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "所在城市",
                    "name": "location_city_district",
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
                    {{--{--}}
                    {{--    "title": "渠道来源",--}}
                    {{--    "data": "channel_source",--}}
                    {{--    "className": "",--}}
                    {{--    "width": "60px",--}}
                    {{--    "orderable": false,--}}
                    {{--    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {--}}
                    {{--        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))--}}
                    {{--        {--}}
                    {{--            $(nTd).addClass('modal-show-for-field-set');--}}
                    {{--            $(nTd).attr('data-id',row.id).attr('data-name','渠道来源');--}}
                    {{--            $(nTd).attr('data-key','channel_source').attr('data-value',data);--}}
                    {{--            $(nTd).attr('data-column-name','渠道来源');--}}
                    {{--            if(data) $(nTd).attr('data-operate-type','edit');--}}
                    {{--            else $(nTd).attr('data-operate-type','add');--}}
                    {{--        }--}}
                    {{--    },--}}
                    {{--    render: function(data, type, row, meta) {--}}
                    {{--        if(!data) return '--';--}}
                    {{--        return data;--}}
                    {{--    }--}}
                    {{--},--}}
                {
                    "title": "通话小结",
                    "name": "description",
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
                {
                    "title": "团队",
                    "name": "creator_team_id",
                    "data": "creator_team_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';

                        var $creator_team = row.creator_team_er == null ? '' : row.creator_team_er.name;
                        var $creator_team_group = row.creator_team_group_er == null ? '' : ' - ' + row.creator_team_group_er.name;
                        return '<a href="javascript:void(0);">'+$creator_team + $creator_team_group+'</a>';
                    }
                },
                {
                    "title": "审核人",
                    "name": "inspector_id",
                    "data": "inspector_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.inspector == null ? '--' : '<a href="javascript:void(0);">'+row.inspector.name+'</a>';
                    }
                },
                {
                    "title": "审核时间",
                    "name": 'inspected_at',
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
                    "name": 'created_at',
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
                    "name": 'operation',
                    "data": 'id',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_detail = '';
                        var $html_record = '';
                        var $html_inspect = '';
                        var $html_appeal_handle = '';


                        $html_detail = '<a class="btn btn-xs item-modal-show-for-detail" data-id="'+data+'">详情</a>';

                        // 记录
                        if(row.created_type != 9)
                        {
                            $html_record = '<a class="btn btn-xs modal-show--for--order--item-operation-record" data-id="'+data+'">记录</a>';
                        }

                        // 已发布
                        if(row.is_published > 0)
                        {
                            // 审核
                            if(row.inspector_id == 0)
                            {
                                $html_inspect = '<a class="btn btn-xs modal-show--for--order--item-inspecting" data-id="'+data+'">审核</a>';
                            }
                            else
                            {
                                $html_inspect = '<a class="btn btn-xs modal-show--for--order--item-inspecting" data-id="'+data+'">再审</a>';
                            }

                            // 申诉处理
                            if(row.appealed_status == 1)
                            {
                                $html_appeal_handle = '<a class="btn btn-xs modal-show--for--order--item-appealed-handling" data-id="'+data+'">处理</a>';
                            }

                        }


                        if(row.created_type == 9)
                        {
                            $html_inspect = '';
                            $html_appeal_handle = '';
                        }


                        var $html =
                            // $html_inspect+
                            $html_appeal_handle+
                            $html_record+
                            // $more_html+
                            '';
                        return $html;

                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('order-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });



                // var api = this.api();
                //
                // // 使用列名隐藏指定列
                // try {
                //     // 方法1：直接使用列名选择器
                //     api.column('id:name').visible(false);
                //     api.column('is_repeat:name').visible(false);
                //
                // } catch (e) {
                //     console.log('列名选择器错误，尝试备用方法:', e);
                // }

            },
            "language": { url: '/common/dataTableI18n' },
        });

        window.dataTableInstances[table_Id] = table;

        return table;
    }
</script>