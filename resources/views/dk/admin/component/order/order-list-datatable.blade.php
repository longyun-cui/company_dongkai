<script>
    function Datatable_for_OrderList($tableId)
    {

        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 100], ["10", "50", "100"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
//                "scrollY": true,
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/v1/operate/order/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
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
                    d.project = $tableSearch.find('select[name="order-project"]').val();
                    d.client = $tableSearch.find('select[name="order-client"]').val();
                    d.status = $tableSearch.find('select[name="order-status"]').val();
                    d.order_type = $tableSearch.find('select[name="order-type"]').val();
                    d.client_name = $tableSearch.find('input[name="order-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="order-client-phone"]').val();
                    d.is_wx = $tableSearch.find('select[name="order-is-wx"]').val();
                    d.is_repeat = $tableSearch.find('select[name="order-is-repeat"]').val();
                    d.created_type = $tableSearch.find('select[name="order-created-type"]').val();
                    d.inspected_status = $tableSearch.find('select[name="order-inspected-status"]').val();
                    d.inspected_result = $tableSearch.find('select[name="order-inspected-result[]"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();
                    d.district_city = $tableSearch.find('select[name="order-city"]').val();
                    d.district_district = $tableSearch.find('select[name="order-district[]"]').val();
                },
            },
            "fixedColumns": {

                @if($me->department_district_id == 0)
                "leftColumns": "@if($is_mobile_equipment) 1 @else 5 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                @else
                "leftColumns": "@if($is_mobile_equipment) 1 @else 7 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
                @endif

            },
            "columnDefs": [
                    {{--@if(!in_array($me->user_type,[0,1,11]))--}}
                    @if($me->department_district_id != 0)
                {
                    "targets": [0,5,9,10,11],
                    "visible": false,
                }
                @endif
            ],
            "columns": [
                {
                    "title": '<input type="checkbox" id="check-review-all">',
                    "width": "id",
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
                    render: function(data, type, row, meta) {
                        return row.id;
                    }
                },
                {
                    "title": "来源",
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
                                return '<small class="btn-xs bg-blue">已审核</small>';
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
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "符合分发",
                    "data": "is_distributive_condition",
                    "className": "",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-radio-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','是否符合分发');
                            $(nTd).attr('data-key','is_distributive_condition').attr('data-value',data);

                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','是否符合分发');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!row.inspected_at) return '--';
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '--';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-red">是</small>';
                        }
                        else
                        {
                            $result_html = '--';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "交付状态",
                    "data": "delivered_status",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付状态');
                            $(nTd).attr('data-key','delivered_status').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','交付状态');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!row.delivered_at) return '--';
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs bg-teal">待交付</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">已操作</small>';
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
                    "data": "delivered_result",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付结果');
                            $(nTd).attr('data-key','delivered_result').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','审核结果');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!row.delivered_at) return '--';
                        var $result_html = '';
                        if(data == "已交付")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "待交付")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "驳回")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "等待再审" || data == "隔日交付")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">'+data+'</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-purple">'+data+'</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "交付说明",
                    "data": "delivered_description",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付说明');
                            $(nTd).attr('data-key','delivered_description').attr('data-value',data);

                            $(nTd).attr('data-column-type','textarea');
                            $(nTd).attr('data-column-name','交付说明');

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
                    "title": "交付人",
                    "data": "deliverer_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付人');
                            $(nTd).attr('data-key','deliverer_name').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','交付人');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.deliverer == null ? '--' : '<a href="javascript:void(0);">'+row.deliverer.true_name+'</a>';
                    }
                },
                {
                    "title": "交付时间",
                    "data": 'delivered_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-time-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付时间');
                            $(nTd).attr('data-key','delivered_at').attr('data-value',data);

                            $(nTd).attr('data-column-type','select');
                            $(nTd).attr('data-column-name','交付时间');
                            $(nTd).attr('data-time-type','date');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
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
                    "title": "交付客户",
                    "data": "client_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        // if(row.is_completed != 1 && row.item_status != 97 && row.client_id > 0)
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户');
                            $(nTd).attr('data-key','client_id').attr('data-value',data);
                            if(row.client_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                $(nTd).attr('data-option-name',row.client_er.name);
                            }

                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','客户');

                            if(row.project_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.client_er == null)
                        {
                            return '--';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.client_er.username+'</a>';
                        }
                    }
                },
//                     {
//                         "title": "交付客户日期 ",
//                         "data": 'delivered_time',
//                         "className": "",
//                         "width": "100px",
//                         "orderable": false,
//                         "orderSequence": ["desc", "asc"],
//                         "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                             if(row.is_completed != 1 && row.item_status != 97)
//                             {
//                                 $(nTd).addClass('modal-show-for-info-time-set');
//                                 $(nTd).attr('data-id',row.id).attr('data-name','交付客户日期');
//                                 $(nTd).attr('data-key','delivered_time').attr('data-value',data);
//                                 $(nTd).attr('data-column-name','交付客户日期');
//                                 $(nTd).attr('data-time-type','date');
//                                 if(data) $(nTd).attr('data-operate-type','edit');
//                                 else $(nTd).attr('data-operate-type','add');
//                             }
//                         },
//                         render: function(data, type, row, meta) {
//                             if(!data) return '--';
// //                            return data;
//                             var $date = new Date(data*1000);
//                             var $year = $date.getFullYear();
//                             var $month = ('00'+($date.getMonth()+1)).slice(-2);
//                             var $day = ('00'+($date.getDate())).slice(-2);
//
// //                            return $year+'-'+$month+'-'+$day;
// //                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
// //                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
//
//                             var $currentYear = new Date().getFullYear();
//                             if($year == $currentYear) return $month+'-'+$day;
//                             else return $year+'-'+$month+'-'+$day;
//                         }
//                     },
                {
                    "title": "创建人",
                    "data": "creator_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                    }
                },
                {
                    "title": "发布时间",
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
                    "data": "project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','项目');
                            $(nTd).attr('data-key','project_id').attr('data-value',data);
                            if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                $(nTd).attr('data-option-name',row.project_er.name);
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
                        else {
                            return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                        }
                    }
                },
                // {
                //     "title": "提交日期",
                //     "data": 'assign_time',
                //     "className": "text-center",
                //     "width": "72px",
                //     "orderable": true,
                //     "orderSequence": ["desc", "asc"],
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1 && row.item_status != 97)
                //         {
                //             var $assign_time_value = '';
                //             if(data)
                //             {
                //                 var $date = new Date(data*1000);
                //                 var $year = $date.getFullYear();
                //                 var $month = ('00'+($date.getMonth()+1)).slice(-2);
                //                 var $day = ('00'+($date.getDate())).slice(-2);
                //                 $assign_time_value = $year+'-'+$month+'-'+$day;
                //             }
                //
                //             $(nTd).addClass('modal-show-for-info-time-set-');
                //             $(nTd).attr('data-id',row.id).attr('data-name','提交日期');
                //             $(nTd).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                //             $(nTd).attr('data-column-name','提交日期');
                //             $(nTd).attr('data-time-type','date');
                //             if(data) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(!data) return '';
                //
                //         var $date = new Date(data*1000);
                //         var $year = $date.getFullYear();
                //         var $month = ('00'+($date.getMonth()+1)).slice(-2);
                //         var $day = ('00'+($date.getDate())).slice(-2);
                //         var $hour = ('00'+$date.getHours()).slice(-2);
                //         var $minute = ('00'+$date.getMinutes()).slice(-2);
                //         var $second = ('00'+$date.getSeconds()).slice(-2);
                //
                //         var $currentYear = new Date().getFullYear();
                //         if($year == $currentYear) return $month+'-'+$day;
                //         else return $year+'-'+$month+'-'+$day;
                //     }
                // },
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
                    "title": "患者类型",
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
                        else
                        {
                            $result_html = '未知类型';
                        }
                        return $result_html;
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
                    "title": "是否+V",
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

                            $(nTd).attr('data-column-type','text');
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
                    "title": "录音地址",
                    "data": "recording_address",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','录音地址');
                            $(nTd).attr('data-key','recording_address').attr('data-value',data);

                            $(nTd).attr('data-column-type','textarea');
                            $(nTd).attr('data-column-name','录音地址');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        if(data)
                        {
                            return '<small class="btn-xs bg-yellow">双击查看</small>';
                        }
                        else return '';
                    }
                },
                {
                    "title": "录音播放",
                    "data": "recording_address_list",
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
                            try
                            {
                                var $recording_list = JSON.parse(data);

                                var $return_html = '';
                                $.each($recording_list, function(index, value)
                                {

                                    var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio>'
                                    $return_html += $audio_html;
                                });
                                return $return_html;
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
                                return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
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
                            else return '';
                        }

                        if(data || row.recording_address)
                        {
                            return '<a class="btn btn-xs item-download-recording-list-submit" data-id="'+row.id+'">下载录音</a>';
                        }
                        else return '';
                    }
                },
                    @endif
                {
                    "title": "部门",
                    "data": "department_district_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','团队大区');
                            $(nTd).attr('data-key','team_district').attr('data-value',data);
                            $(nTd).attr('data-column-name','团队大区');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';

                        var $district = row.department_district_er == null ? '' : row.department_district_er.name;
                        var $group = row.department_group_er == null ? '' : ' - ' + row.department_group_er.name;
                        return '<a href="javascript:void(0);">'+$district + $group+'</a>';
                    }
                },
                {
                    "title": "部门经理",
                    "data": "department_manager_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','团队大区');
                            $(nTd).attr('data-key','department_manager_id').attr('data-value',data);
                            $(nTd).attr('data-column-name','团队大区');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.department_manager_er == null ? '--' : '<a href="javascript:void(0);">'+row.department_manager_er.true_name+'</a>';
                    }
                },
                {
                    "title": "部门主管",
                    "data": "department_supervisor_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','部门主管');
                            $(nTd).attr('data-key','department_supervisor_id').attr('data-value',data);
                            $(nTd).attr('data-column-name','部门经理');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.department_supervisor_er == null ? '--' : '<a href="javascript:void(0);">'+row.department_supervisor_er.true_name+'</a>';
                    }
                },
                {
                    "title": "审核人",
                    "data": "inspector_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.inspector == null ? '--' : '<a href="javascript:void(0);">'+row.inspector.true_name+'</a>';
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
                            $(nTd).attr('data-key','inspected_description').attr('data-value',data);

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
                    "title": "是否推送",
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
                            $html_edit = '<a class="btn btn-xs btn-primary- order-edit-show" data-id="'+data+'">编辑</a>';
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';
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
                            $html_record = '<a class="btn btn-xs bg-purple item-modal-show-for-modify" data-id="'+data+'">记录</a>';


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

                console.log('order-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>