<script>

    // window.dataTableInstances = window.dataTableInstances || {};

    function Datatable__for__Order_Dental_List($tableId)
    {
        // var table_Id = $tableId;
        // if (window.dataTableInstances[table_Id])
        // {
        //     return window.dataTableInstances[table_Id];
        // }

        let $that = $('#'+$tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        var table = $('#'+$tableId).DataTable({
            "aLengthMenu": [[15, 50, 100, 200], ["15", "50", "100", "200"]],
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
                    d.department = $tableSearch.find('select[name="order-department"]').val();
                    d.team_list = $tableSearch.find('select[name="order-team-list[]"]').val();
                    d.staff = $tableSearch.find('select[name="order-staff"]').val();
                    d.inspector = $tableSearch.find('select[name="order-inspector"]').val();
                    d.distribute_type = $tableSearch.find('select[name="order-distribute-type"]').val();
                    d.project = $tableSearch.find('select[name="order-project"]').val();
                    d.delivered_project = $tableSearch.find('select[name="order-delivered-project"]').val();
                    d.delivered_client = $tableSearch.find('select[name="order-delivered-client"]').val();
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
                    d.ai_inspected_status = $tableSearch.find('select[name="order-ai-inspected-status"]').val();
                    d.inspected_result = $tableSearch.find('select[name="order-inspected-result[]"]').val();
                    d.inspected_result_2 = $tableSearch.find('select[name="order-inspected-result-2[]"]').val();
                    d.appealed_status = $tableSearch.find('select[name="order-appealed-status"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();
                    d.location_city = $tableSearch.find('select[name="order-city"]').val();
                    d.location_district = $tableSearch.find('select[name="order-district[]"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif",
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
                    "name": "checkbox",
                    // "data": "id",
                    "data": null,
                    "width": "40px",
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
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('order_id');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','工单ID');
                        $(nTd).attr('data-key','order_id');
                        $(nTd).attr('data-value',row.id);

                        if(row.is_completed != 1)
                        {
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
                    "name": "order_status",
                    "data": "id",
                    "className": "",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('order_status');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','工单状态');
                        $(nTd).attr('data-key','order_status');
                        $(nTd).attr('data-value',row.id);

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
                    "title": "来源",
                    "name": "created_type",
                    "data": "created_type",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('created_type');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','来源');
                        $(nTd).attr('data-key','created_type');
                        $(nTd).attr('data-value',row.id);

                    },
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
                    "title": "AI",
                    "data": "ai_inspected_status",
                    "className": "",
                    "width": "72px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '--';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">审核中</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-green">已审核</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "审核状态",
                    "name": "inspected_status",
                    "data": "inspected_status",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('inspected_status');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','审核状态');
                        $(nTd).attr('data-key','inspected_status');
                        $(nTd).attr('data-value',row.id);

                    },
                    render: function(data, type, row, meta) {
                        if(row.created_type == 9) return '--';
                        // if(!row.inspected_at) return '--';
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs bg-teal">待审核</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">已审核</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-purple">不审核</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-red">审核失败</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">error</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "审核结果",
                    "name": "inspected_result",
                    "data": "inspected_result",
                    "className": "text-center",
                    "width": "88px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('inspected_status');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','审核结果');
                        $(nTd).attr('data-key','inspected_result');
                        $(nTd).attr('data-value',data);

                    },
                    render: function(data, type, row, meta) {
                        if(!row.inspected_at) return '--';
                        var $result_html = '';
                        var $result_2_html = '';
                        var $inspected_result_2 = row.inspected_result_2;
                        if(data == "通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">通过</small>';
                            if($inspected_result_2)
                            {
                                $result_2_html = '<small class="btn-xs bg-green">'+$inspected_result_2+'</small>';
                            }
                        }
                        else if(data == "折扣通过" || data == "郊区通过" || data == "内部通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "拒绝")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                            if($inspected_result_2)
                            {
                                $result_2_html = '<small class="btn-xs bg-red">'+$inspected_result_2+'</small>';
                            }
                        }
                        else if(data == "拒绝可交付")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝可交付</small>';
                        }
                        else if(data == "不合格")
                        {
                            $result_html = '<small class="btn-xs bg-red">不合格</small>';
                        }
                        else if(data == "重复")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                        }
                        else if(data == "重复•可分发")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复•可分发</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                        }

                        return $result_html + $result_2_html;
                    }
                },
                // {
                //     "title": "是否分发",
                //     "name": "is_distributive_condition",
                //     "data": "is_distributive_condition",
                //     "className": "",
                //     "width": "72px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                //         {
                //             $(nTd).attr('data-row-index',iRow);
                //
                //             $(nTd).addClass('modal-show-for-field-set');
                //             $(nTd).attr('data-id',row.id).attr('data-name','是否分发');
                //             $(nTd).attr('data-key','is_distributive_condition').attr('data-value',data);
                //
                //             $(nTd).attr('data-column-type','select');
                //             $(nTd).attr('data-column-name','是否分发');
                //
                //             if(data) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         // if(!row.inspected_at) return '--';
                //         var $result_html = '';
                //         if(data == 0)
                //         {
                //             $result_html = '--';
                //         }
                //         else if(data == 1)
                //         {
                //             $result_html = '<small class="btn-xs bg-green">允许</small>';
                //         }
                //         else if(data == 9)
                //         {
                //             $result_html = '<small class="btn-xs bg-red">禁止</small>';
                //         }
                //         else
                //         {
                //             $result_html = '--';
                //         }
                //         return $result_html;
                //     }
                // },
                {
                    "title": "交付状态",
                    "name": "delivered_status",
                    "data": "delivered_status",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('delivered_status');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','交付状态');
                        $(nTd).attr('data-key','delivered_status');
                        $(nTd).attr('data-value',row.id);

                    },
                    render: function(data, type, row, meta) {
                        if(!row.delivered_at) return '--';
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs bg-teal">未操作</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">已交付</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-orange">待交付</small>';
                        }
                        else if(data == 91)
                        {
                            $result_html = '<small class="btn-xs bg-purple">不交付</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-red">交付失败</small>';
                        }
                        else if(data == 101)
                        {
                            $result_html = '<small class="btn-xs bg-red">交付撤回</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">error</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "交付结果",
                    "name": "delivered_result",
                    "data": "delivered_result",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('delivered_result');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','交付结果');
                        $(nTd).attr('data-key','delivered_result');
                        $(nTd).attr('data-value',row.id);

                    },
                    render: function(data, type, row, meta) {
                        if(!row.delivered_at) return '--';
                        var $result_html = '';
                        // if(data == "交付" || data == "正常交付" || data == "折扣交付" || data == "郊区交付" || data == "内部交付")
                        if(["交付","正常交付","折扣交付","郊区交付","内部交付"].includes(data))
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "待交付")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "等待再审" || data == "隔日交付")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">'+data+'</small>';
                        }
                        else if(data == "驳回")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "交付时间",
                    "name": 'delivered_at',
                    "data": 'delivered_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('delivered_at');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','交付时间');
                        $(nTd).attr('data-key','delivered_at');
                        $(nTd).attr('data-value',data);

                    },
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
                    "title": "交付项目",
                    "name": "delivered_project_id",
                    "data": "delivered_project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('delivered_project_id');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','交付项目');
                        $(nTd).attr('data-key','delivered_project_id');
                        $(nTd).attr('data-value',data);

                    },
                    render: function(data, type, row, meta) {
                        if(row.delivered_project_er) return '<a href="javascript:void(0);">'+row.delivered_project_er.name+'</a>';
                        else return '--';
                    }
                },
                {
                    "title": "交付客户",
                    "name": "delivered_client_id",
                    "data": "delivered_client_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {

                        $(nTd).addClass('delivered_client_id');
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','交付客户');
                        $(nTd).attr('data-key','delivered_client_id');
                        $(nTd).attr('data-value',data);

                    },
                    render: function(data, type, row, meta) {
                        if(row.delivered_client_er) return '<a href="javascript:void(0);">'+row.delivered_client_er.name+'</a>';
                        else return '--';
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
                        if("{{ in_array($me->user_type,[0,1,11,61,66]) }}")
                        {
                            if(row.project_er == null)
                            {
                                return '未指定';
                            }
                            else
                            {
                                if(row.project_er.alias_name)
                                {
                                    return '<a href="javascript:void(0);">'+row.project_er.name+' ('+row.project_er.alias_name+')'+'</a>';
                                }
                                else
                                {
                                    return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                                }
                            }
                        }
                        else
                        {
                            if(row.project_er == null)
                            {
                                return '未指定';
                            }
                            else
                            {
                                return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                            }
                        }
                    }
                },
                {
                    "title": "重复",
                    "name": "is_repeat",
                    "data": "is_repeat",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                    "sTitle": "患者类型1",
                    "name": "client_type",
                    "data": "client_type",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(data == 1) return '单颗';
                        else if(data == 2) return '2-5颗';
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
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
                    {{--        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))--}}
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
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--order--item-detail-editing--by-dbl');
                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','通话小结');
                            $(nTd).attr('data-key','description');
                            $(nTd).attr('data-value',data);
                            $(nTd).attr('data-role','admin');

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
                    "title": "录音质量",
                    "name": "recording_quality",
                    "data": "recording_quality",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','录音质量');
                            $(nTd).attr('data-key','recording_quality').attr('data-value',data);

                            $(nTd).attr('data-column-name','录音质量');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        var $result_html = '';

                        if(row.inspected_at)
                        {
                            if(data == 0)
                            {
                                $result_html = '<small class="btn-xs bg-blue">合格</small>';
                            }
                            else if(data == 1)
                            {
                                $result_html = '<small class="btn-xs bg-green">优秀</small>';
                            }
                            else if(data == 9)
                            {
                                $result_html = '<small class="btn-xs bg-red">问题</small>';
                            }
                            else
                            {
                                $result_html = '<small class="btn-xs bg-black">有误</small>';
                            }
                        }
                        return $result_html;
                    }
                },
                // {
                //     "title": "录音播放",
                //     "name": "recording_address_list",
                //     "data": "recording_address_list",
                //     "className": "",
                //     "width": "400px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1)
                //         {
                //             $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                //             $(nTd).attr('data-key','recording_address_play').attr('data-value',data);
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         // return data;
                //         if($.trim(data))
                //         {
                //             try
                //             {
                //                 var $recording_list = JSON.parse(data);
                //
                //                 var $return_html = '';
                //                 $.each($recording_list, function(index, value)
                //                 {
                //
                //                     var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                //                     $return_html += $audio_html;
                //                 });
                //                 return $return_html;
                //             }
                //             catch(e)
                //             {
                //                 // console.log(e);
                //                 return '';
                //             }
                //         }
                //         else
                //         {
                //             if(row.recording_address)
                //             {
                //                 return '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+row.recording_address+'" type="audio/mpeg"></audio>';
                //             }
                //             else return '';
                //         }
                //     }
                // },
                {
                    "title": "录音下载",
                    "name": "recording_address_get_download",
                    "data": "recording_address_list",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
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
                                var $recording_redirection = '<a class="btn btn-xs item-redirection-recording-list-submit" data-id="'+row.id+'">跳转</a>';
                                var $recording_get = '<a class="btn btn-xs order--item-recording-list-get-submit" data-id="'+row.id+'">获取</a>';
                                return $recording_get + $recording_redirection + $recording_download;
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
                                return '<a class="btn btn-xs order--item-recording-list-get-submit" data-id="'+row.id+'">获取录音</a>';
                            }
                        }

                        if($.trim(data) || row.recording_address)
                        {
                            return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                        }
                        else
                        {
                            return '<a class="btn btn-xs order--item-recording-list-get-submit" data-id="'+row.id+'">获取录音1</a>';
                        }
                    }
                },
                {
                    "title": "班次",
                    "name": "work_shift",
                    "data": "work_shift",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','班次');
                            $(nTd).attr('data-key','work_shift').attr('data-value',data);

                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','班次');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs bg-green">白班</small>';
                        else if(data == 2) return '<small class="btn-xs bg-navy">夜班</small>';
                        else return '--';
                    }
                },
                {
                    "title": "团队",
                    "name": "creator_team_id",
                    "data": "creator_team_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(true)
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
                    "title": "是否推送",
                    "name": "api_is_pushed",
                    "data": "api_is_pushed",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs btn-primary">是</small>';
                        else return '--';
                    }
                },
                {
                    "title": "CPA",
                    "name": "api_is_pushed_for_cpa",
                    "data": "api_is_pushed_for_cpa",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        {
                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','CPA');
                            $(nTd).attr('data-key','api_is_pushed_for_cpa');
                            $(nTd).attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs btn-primary">神书</small>';
                        else if(data == 2) return '<small class="btn-xs btn-primary">数智</small>';
                        else return '--';
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
                        return row.creator == null ? '未知' : '<a class="caller-control" data-id="'+data+'" data-title="'+row.creator.name+'">'+row.creator.name+'</a>';
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
                    "width": "180px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_record = '';
                        var $html_delete = '';
                        var $html_publish = '';
                        var $html_inspect = '';
                        var $html_api_cpa_pushing = '';
                        var $html_ai_inspect = '';
                        var $html_appeal = '';
                        var $html_appeal_handle = '';
                        var $html_deliver_fool = '';
                        var $html_deliver = '';
                        var $html_distribute = '';


                        // $html_ai_inspect = '<a class="btn btn-xs order--item-ai-inspecting--submit" data-id="'+data+'">AI审核</a>';
                        $html_ai_inspect = '<a class="btn btn-xs modal-show--for--order--item-ai-inspecting" data-id="'+data+'">AI审核</a>';


                        // 记录
                        if(row.created_type != 9)
                        {
                            $html_record = '<a class="btn btn-xs modal-show--for--order--item-operation-record" data-id="'+data+'">记录</a>';
                        }

                        // 删除
                        if(row.is_published == 0)
                        {
                            $html_delete = '<a class="btn btn-xs order--item-delete-submit" data-id="'+data+'">删除</a>';
                        }

                        // 编辑
                        if(row.is_published == 0)
                        {
                            $html_edit = '<a class="btn btn-xs modal-show--for--order-dental--item-edit" data-id="'+data+'">编辑</a>';
                        }

                        // 发布
                        if(row.is_published == 0 || (row.inspected_status == 1 && row.inspected_result == '二次待审'))
                        {
                            $html_publish = '<a class="btn btn-xs order--item-publish-submit" data-id="'+data+'">发布</a>';
                        }

                        // 已发布
                        if(row.is_published > 0)
                        {
                            // 编辑
                            if(row.inspected_status == 1 && row.inspected_result == '二次待审')
                            {
                                $html_edit = '<a class="btn btn-xs modal-show--for--order-dental--item-edit" data-id="'+data+'">编辑</a>';
                            }

                            // 详情编辑
                            $html_detail = '<a class="btn btn-xs modal-show--for--order--item-detail-editing" data-role="admin" data-id="'+data+'">详情</a>';

                            // 审核
                            if(row.inspector_id == 0)
                            {
                                $html_inspect = '<a class="btn btn-xs modal-show--for--order--item-inspecting" data-id="'+data+'">审核</a>';
                            }
                            else
                            {
                                $html_inspect = '<a class="btn btn-xs modal-show--for--order--item-inspecting" data-id="'+data+'">再审</a>';
                            }

                            // 申诉
                            if(row.appealed_status == 0 && ['拒绝','拒绝可交付','不合格'].includes(row.inspected_result))
                            {
                                $html_appeal = '<a class="btn btn-xs modal-show--for--order--item-appealing" data-id="'+data+'">申诉</a>';
                            }

                            // 申诉处理
                            if(row.appealed_status == 1)
                            {
                                $html_appeal_handle = '<a class="btn btn-xs modal-show--for--order--item-appealed-handling" data-id="'+data+'">处理</a>';
                            }

                            // 交付
                            if(row.delivered_status == 0)
                            {
                                $html_deliver = '<a class="btn btn-xs modal-show--for--order--item-delivering" data-id="'+data+'">交付</a>';
                            }
                            else
                            {
                                $html_deliver = '<a class="btn btn-xs modal-show--for--order--item-delivering" data-id="'+data+'">重交</a>';
                            }

                            if(row.delivered_project_id <= 0 && row.delivered_client_id <= 0 && row.delivered_status == 0)
                            {
                                $html_deliver_fool = '<a class="btn btn-xs order--item-delivering-summit--by-fool" data-id="'+data+'">一键交付</a>';
                            }

                            // 分发
                            if(row.project_er == null)
                            {
                                $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                            }
                            else
                            {
                                if(row.project_er.is_distributive == 1)
                                {
                                    $html_distribute = '<a class="btn btn-xs modal-show--for--order--item-distributing" data-id="'+data+'">分发</a>';
                                }
                                else
                                {
                                    $html_distribute = '<a class="btn btn-xs bg-default disabled" data-id="'+data+'">分发</a>';
                                }
                            }

                            $html_api_cpa_pushing = '<a class="btn btn-xs order--item--api-cpa-push-submit" data-id="'+data+'">推送</a>';

                        }


                        if(row.created_type == 9)
                        {
                            $html_edit = '';
                            $html_publish = '';
                            $html_delete = '';
                            $html_ai_inspect = '';
                            $html_inspect = '';
                            $html_appeal = '';
                            $html_appeal_handle = '';
                            $html_deliver_fool = '';
                            $html_deliver = '';
                            $html_api_cpa_pushing = '';
                            $html_distribute = '';
                            $html_detail = '';
                        }


                        var $html =
                            $html_edit+
                            $html_publish+
                            $html_delete+
                            $html_ai_inspect+
                            $html_inspect+
                            $html_appeal+
                            $html_appeal_handle+
                            $html_deliver_fool+
                            $html_deliver+
                            $html_api_cpa_pushing+
                            $html_distribute+
                            $html_detail+
                            $html_record+
                            // $more_html+
                            '';
                        return $html;

                    }
                }
            ],
            "initComplete": function() {
                // var api = this.api();
                //
                // // 1. 获取“班次”列的所有数据
                // var shiftData = api.column('field_2:name').data();
                // var dayCount = 0;
                // var nightCount = 0;
                //
                // // 2. 统计班次类型
                // shiftData.each(function(value) {
                //     if (value == 1) dayCount++;
                //     if (value == 9) nightCount++;
                // });
                //
                // // 3. 根据统计结果动态设置标题
                // var column = api.column('field_2:name');
                // var header = $(column.header());
                // header.html('班次1');
            },
            "drawCallback": function (settings) {

                console.log('order-dental-list-datatable--for--admin--execute');

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

        // window.dataTableInstances[table_Id] = table;
        //
        // return table;
    }
</script>