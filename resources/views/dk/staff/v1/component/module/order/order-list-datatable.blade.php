<script>
    function Datatable__for__Order_List($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 200], ["10", "50", "200"]],
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
                'url': "{{ url('/o1/order/order-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.item_category = 1;
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
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif",
                @else
                "leftColumns": "@if($is_mobile_equipment) 1 @else 4 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif",
                @endif

            },
            "columnDefs": [
            ],
            "columns": [
//                    {
//                        "title": "选择",
//                        "width": "32px",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                {
                    "title": "ID",
                    "className": "",
                    "width": "60px",
                    "data": "id",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(true)
                        {
                            $(nTd).attr('data-id',row.id).attr('data-name','附件');
                            $(nTd).attr('data-key','id').attr('data-value',data);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "订单类型",
                    "className": "",
                    "width": "80px",
                    "data": "order_type",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-select-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','订单类型');
                            $(nTd).attr('data-key','car_owner_type').attr('data-value',data);
                            $(nTd).attr('data-column-name','订单类型');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<small class="btn-xs bg-green">自有</small>';
                        }
                        else if(data == 11)
                        {
                            return '<small class="btn-xs bg-teal">空单</small>';
                        }
                        else if(data == 41)
                        {
                            return '<small class="btn-xs bg-blue">外配·配货</small>';
                        }
                        else if(data == 61)
                        {
                            return '<small class="btn-xs bg-purple">外请·调车</small>';
                        }
                        else return "有误";
                    }
                },
                {
                    "title": "订单状态",
                    "className": "",
                    "width": "80px",
                    "data": "id",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--attachment');
                            $(nTd).attr('data-id',row.id).attr('data-name','附件');
                            $(nTd).attr('data-key','attachment_list').attr('data-value',row.attachment_list);
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

                        var $travel_status_html = '';
                        var $travel_result_html = '';
                        var $travel_result_time = '';
//
                        if(row.travel_status == "待发车")
                        {
                            $travel_status_html = '<small class="btn-xs bg-yellow">待发车</small>';
                        }
                        else if(row.travel_status == "进行中")
                        {
                            $travel_status_html = '<small class="btn-xs bg-blue">进行中</small>';
                        }
                        else if(row.travel_status == "已到达")
                        {
                            if(row.travel_result == "待收款") $travel_status_html = '<small class="btn-xs bg-orange">待收款</small>';
                            if(row.travel_result == "已收款") $travel_status_html = '<small class="btn-xs bg-maroon">已收款</small>';
                            else $travel_status_html = '<small class="btn-xs bg-olive">已到达</small>';
                        }
                        else if(row.travel_status == "待收款")
                        {
                            $travel_status_html = '<small class="btn-xs bg-maroon">待收款</small>';
                        }
                        else if(row.travel_status == "已收款")
                        {
                            $travel_status_html = '<small class="btn-xs bg-purple">已收款</small>';
                        }
                        else if(row.travel_status == "已完成")
                        {
                            $travel_status_html = '<small class="btn-xs bg-olive">已完成</small>';
                        }
//
//
//                            if(row.travel_result == "正常")
//                            {
//                                $travel_result_html = '<small class="btn-xs bg-olive">正常</small>';
//                            }
//                            else if(row.travel_result == "超时")
//                            {
//                                $travel_result_html = '<small class="btn-xs bg-red">超时</small><br>';
//                                $travel_result_time = '<small class="btn-xs bg-gray">'+row.travel_result_time+'</small>';
//                            }
//                            else if(row.travel_result == "已超时")
//                            {
//                                $travel_result_html = '<small class="btn-xs btn-danger">已超时</small>';
//                            }
//
                        return $travel_status_html + $travel_result_html + $travel_result_time;

                    }
                },
                {
                    "title": "状态",
                    "data": "id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;

                        if(row.deleted_at != null)
                        {
                            return '';
                        }

                        if(row.is_published == 0)
                        {
                            return '';
                        }


                        var $travel_status_html = '';
                        var $travel_result_html = '';



                        if(row.travel_result == "正常")
                        {
                            $travel_result_html = '<small class="btn-xs bg-olive">正常</small>';
                        }
                        else if(row.travel_result == "超时")
                        {
                            $travel_result_html = '<small class="btn-xs bg-red">超时</small><br>';
                        }
                        else if(row.travel_result == "发车超时")
                        {
                            $travel_result_html = '<small class="btn-xs btn-danger">发车超时</small>';
                        }
                        else if(row.travel_result == "待收款")
                        {
                            $travel_result_html = '<small class="btn-xs bg-orange">待收款</small>';
                        }
                        else if(row.travel_result == "已收款")
                        {
                            $travel_result_html = '<small class="btn-xs bg-blue">已收款</small>';
                        }


                        if(row.is_completed == 1)
                        {
                            $travel_result_html = '<small class="btn-xs bg-grey">已结束</small>';
                        }

                        return $travel_status_html + $travel_result_html;

                    }
                },
                {
                    "title": "派车日期",
                    "name": 'assign_date',
                    "data": 'assign_date',
                    "className": "",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $assign_time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                $assign_time_value = $year+'-'+$month+'-'+$day;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','派车日期');
                            $(nTd).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                            $(nTd).attr('data-column-name','派车日期');
                            $(nTd).attr('data-time-type','date');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                        // if(!data) return '';
                        //
                        // var $date = new Date(data*1000);
                        // var $year = $date.getFullYear();
                        // var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        // var $day = ('00'+($date.getDate())).slice(-2);
                        // var $hour = ('00'+$date.getHours()).slice(-2);
                        // var $minute = ('00'+$date.getMinutes()).slice(-2);
                        // var $second = ('00'+$date.getSeconds()).slice(-2);
                        //
                        // var $currentYear = new Date().getFullYear();
                        // if($year == $currentYear) return $month+'-'+$day;
                        // else return $year+'-'+$month+'-'+$day;
                    }
                },
                {
                    "title": "任务日期",
                    "name": 'task_date',
                    "data": 'task_date',
                    "className": "",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $assign_time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                $assign_time_value = $year+'-'+$month+'-'+$day;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','派车日期');
                            $(nTd).attr('data-key','assign_time').attr('data-value',$assign_time_value);
                            $(nTd).attr('data-column-name','派车日期');
                            $(nTd).attr('data-time-type','date');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                        // if(!data) return '';
                        //
                        // var $date = new Date(data*1000);
                        // var $year = $date.getFullYear();
                        // var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        // var $day = ('00'+($date.getDate())).slice(-2);
                        // var $hour = ('00'+$date.getHours()).slice(-2);
                        // var $minute = ('00'+$date.getMinutes()).slice(-2);
                        // var $second = ('00'+$date.getSeconds()).slice(-2);
                        //
                        // var $currentYear = new Date().getFullYear();
                        // if($year == $currentYear) return $month+'-'+$day;
                        // else return $year+'-'+$month+'-'+$day;
                    }
                },
                {
                    "title": "客户",
                    "data": "client_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-select2-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户');
                            $(nTd).attr('data-key','client_id').attr('data-value',data);
                            if(row.client_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                if(row.client_er.short_name) $(nTd).attr('data-option-name',row.client_er.name);
                                else $(nTd).attr('data-option-name',row.client_er.name);
                            }
                            $(nTd).attr('data-column-name','客户');
                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.client_er)
                        {
                            // return '<a href="javascript:void(0);" class="text-black">'+row.client_er.name+'</a>';
                            return '<a class="client-control" data-id="'+row.client_id+'" data-title="'+row.client_er.name+'">'+row.client_er.name+'</a>';
                        }
                        else return '未指定';
                    }
                },
                {
                    "title": "项目",
                    "data": "project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-select2-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户');
                            $(nTd).attr('data-key','client_id').attr('data-value',data);
                            if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                if(row.project_er) $(nTd).attr('data-option-name',row.project_er.name);
                                else $(nTd).attr('data-option-name','');
                            }
                            $(nTd).attr('data-column-name','项目');
                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.project_er)
                        {
                            // return '<a href="javascript:void(0);" class="text-black">'+row.project_er.name+'</a>';
                            return '<a class="project-control" data-id="'+row.project_id+'" data-title="'+row.project_er.name+'">'+row.project_er.name+'</a>';
                        }
                        else return '未指定';
                    }
                },
                {
                    "title": "车辆",
                    "data": "car_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                $(nTd).addClass('modal-show--for--info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车辆');
                                $(nTd).attr('data-key','car_id').attr('data-value',row.car_id);
                                if(row.car_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.car_er.name);
                                $(nTd).attr('data-column-name','车辆');
                                if(row.car_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                            else if(row.car_owner_type == 61)
                            {
                                $(nTd).addClass('modal-show--for--info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车辆');
                                $(nTd).attr('data-key','outside_car').attr('data-value',row.outside_car);
                                $(nTd).attr('data-column-name','车辆');
                                if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        }
                    },
                    render: function(data, type, row, meta) {
                        var car_html = '';
                        if(row.car_owner_type == 1)
                        {
                            if(row.car_er != null)
                            {
                                car_html = '<a class="car-control" data-id="'+row.car_id+'" data-title="'+row.car_er.name+'">'+row.car_er.name+'</a>';
                            }
                        }
                        else
                        {
                            car_html = row.external_car;
                        }
                        return car_html;
                    }
                },
                {
                    "title": "车挂",
                    "data": "trailer_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            if(row.car_owner_type == 1 || row.car_owner_type == 11 || row.car_owner_type == 41)
                            {
                                $(nTd).addClass('modal-show--for--info-select2-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车挂');
                                $(nTd).attr('data-key','trailer_id').attr('data-value',row.trailer_id);
                                if(row.trailer_er == null) $(nTd).attr('data-option-name','未指定');
                                else $(nTd).attr('data-option-name',row.trailer_er.name);
                                $(nTd).attr('data-column-name','车挂');
                                if(row.trailer_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                            else if(row.car_owner_type == 61)
                            {
                                $(nTd).addClass('modal-show--for--info-text-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','车挂');
                                $(nTd).attr('data-key','outside_trailer').attr('data-value',row.outside_trailer);
                                $(nTd).attr('data-column-name','车挂');
                                if(row.outside_car) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        }
                    },
                    render: function(data, type, row, meta) {
                        var trailer_html = '';
                        if(row.car_owner_type == 1)
                        {
                            if(row.trailer_er != null) trailer_html = '<a href="javascript:void(0);" class="text-black">'+row.trailer_er.name+'</a>';
                        }
                        else
                        {
                            trailer_html = row.external_trailer;
                        }
                        return trailer_html;
                    }
                },


                {
                    "title": "驾驶员",
                    "data": "driver_id",
                    "className": "",
                    "width": "160px",
                    "orderable": false,
                    "visible" : true,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','主驾姓名');
                            $(nTd).attr('data-key','driver_name').attr('data-value',data);
                            $(nTd).attr('data-column-name','主驾姓名');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $driver_id = 0;
                        var $driver_name = '';
                        var $driver_phone = '';
                        var $copilot_id = 0;
                        var $copilot_name = '';
                        var $copilot_phone = '';

                        var $driver_text = '';
                        var $driver_html = '';
                        var $copilot_text = '';
                        var $copilot_html = '';


                        if(row.car_owner_type == 1)
                        {
                            // 主驾
                            if(row.driver_er != null)
                            {
                                $driver_id = row.driver_id;
                                $driver_name = row.driver_er.driver_name;
                                $driver_phone = row.driver_er.driver_phone;

                                $driver_text = $driver_name + ' (' +  $driver_phone + ')';
                                $driver_html = '<a class="driver-control" data-id="'+$driver_id+'" data-title="'+$driver_name+'">'+$driver_text+'</a> <br>';
                            }
                            // 副驾
                            if(row.copilot_er != null)
                            {
                                $copilot_id = row.copilot_id;
                                $copilot_name = row.copilot_er.driver_name;
                                $copilot_phone = row.copilot_er.driver_phone;

                                $copilot_text = $copilot_name + ' (' +  $copilot_phone + ')';
                                $copilot_html = '<a class="driver-control" data-id="'+$copilot_id+'" data-title="'+$copilot_name+'">'+$copilot_text+'</a>';
                            }
                        }
                        else
                        {
                            // 主驾
                            if(row.driver_phone) $driver_html = row.driver_name + ' (' +  row.driver_phone + ') <br>';
                            else $driver_html = row.copilot_name;
                            // 副驾
                            if(row.copilot_phone) $copilot_html = row.copilot_name + ' (' +  row.copilot_phone + ')';
                            else $copilot_html = row.copilot_name;
                        }


                        return $driver_html + $copilot_html;
                    }
                },



                // {
                //     "title": "线路",
                //     "data": "route_type",
                //     "className": "bg-route",
                //     "width": "160px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1)
                //         {
                //             if(data == 1)
                //             {
                //                 $(nTd).addClass('modal-show--for--info-select2-set');
                //                 $(nTd).attr('data-id',row.id).attr('data-name','固定线路');
                //                 $(nTd).attr('data-key','route_id').attr('data-value',row.route_id);
                //                 if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                //                 else $(nTd).attr('data-option-name',row.route_er.title);
                //                 $(nTd).attr('data-column-name','固定线路');
                //                 if(row.route_id) $(nTd).attr('data-operate-type','edit');
                //                 else $(nTd).attr('data-operate-type','add');
                //             }
                //             else if(data == 11)
                //             {
                //                 $(nTd).addClass('modal-show--for--info-text-set');
                //                 $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                //                 $(nTd).attr('data-key','route_temporary').attr('data-value',row.route_temporary);
                //                 if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                //                 $(nTd).attr('data-column-name','临时线路');
                //                 if(row.route_id) $(nTd).attr('data-operate-type','edit');
                //                 else $(nTd).attr('data-operate-type','add');
                //             }
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(data == 1)
                //         {
                //             if(row.route_er == null) return '--';
                //             else return '<a href="javascript:void(0);">'+row.route_er.title+'</a>';
                //         }
                //         else if(data == 11)
                //         {
                //             if(row.route_temporary) return '[临] ' + row.route_temporary;
                //             else return '[临时]';
                //         }
                //         else return '有误';
                //     }
                // },
                // {
                //     "title": "固定线路",
                //     "className": "bg-route",
                //     "width": "120px",
                //     "data": "route_id",
                //     "orderable": false,
                //     "visible" : true,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1)
                //         {
                //             $(nTd).addClass('modal-show--for--info-select2-set');
                //             $(nTd).attr('data-id',row.id).attr('data-name','固定线路');
                //             $(nTd).attr('data-key','route_id').attr('data-value',row.route_id);
                //             if(row.route_er == null) $(nTd).attr('data-option-name','未指定');
                //             else $(nTd).attr('data-option-name',row.route_er.title);
                //             $(nTd).attr('data-column-name','固定线路');
                //             if(row.route_id) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(row.route_er == null) return '--';
                //         else return '<a href="javascript:void(0);">'+row.route_er.title+'</a>';
                //     }
                // },
                // {
                //     "title": "临时线路",
                //     "className": "bg-route",
                //     "width": "120px",
                //     "data": "route_temporary",
                //     "orderable": false,
                //     "visible" : true,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1)
                //         {
                //             $(nTd).addClass('modal-show--for--info-text-set');
                //             $(nTd).attr('data-id',row.id).attr('data-name','临时线路');
                //             $(nTd).attr('data-key','route_temporary').attr('data-value',data);
                //             $(nTd).attr('data-column-name','临时线路');
                //             if(row.route_id) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(row.route_temporary) return '' + row.route_temporary;
                //         else return '';
                //     }
                // },
                {
                    "title": "出发地",
                    "data": "transport_departure_place",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','出发地');
                            $(nTd).attr('data-key','departure_place').attr('data-value',data);
                            $(nTd).attr('data-column-name','出发地');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data == null ? '--' : data;
                    }
                },
                {
                    "title": "目的地",
                    "data": "transport_destination_place",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','目的地');
                            $(nTd).attr('data-key','destination_place').attr('data-value',data);
                            $(nTd).attr('data-column-name','目的地');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data == null ? '--' : data;
                    }
                },
                {
                    "title": "距离(km)",
                    "name": "transport_distance",
                    "data": "transport_distance",
                    "className": "bg-route",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','距离');
                            $(nTd).attr('data-key','transport_distance').attr('data-value',data);
                            $(nTd).attr('data-column-name','距离');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '';
                        else return data;
                    }
                },
                {
                    "title": "时效(H)",
                    "data": "transport_time_limitation",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','时效');
                            $(nTd).attr('data-key','time_limitation_prescribed').attr('data-value',data);
                            $(nTd).attr('data-column-name','时效');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return convertMinutesToHoursAndMinutes(data);
                    }
                },

                {
                    "title": "账期",
                    "data": "settlement_period",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--item-field-set-');
                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','账期');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','运费');
                            $(nTd).attr('data-key','freight_amount');
                            $(nTd).attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<i class="fa fa-clock-o text-red"></i> 单次结算';
                        }
                        if(data == 3)
                        {
                            return '<i class="fa fa-clock-o text-yellow"></i> 多次结算</small>';
                        }
                        if(data == 7)
                        {
                            return '<i class="fa fa-clock-o text-blue"></i> 周结</small>';
                        }
                        if(data == 31)
                        {
                            return '<i class="fa fa-clock-o text-green"></i> 月结</small>';
                        }
                        else
                        {
                            return '有误';
                        }
                    }
                },
                {
                    "title": "运价",
                    "data": "freight_amount",
                    "className": "bg-fee",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','运价');
                            $(nTd).attr('data-key','amount').attr('data-value',parseFloat(data));
                            $(nTd).attr('data-column-name','运价');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "费用",
                    "name": "financial_expense_total",
                    "data": "financial_expense_total",
                    "className": "bg-fee",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('modal-show--for--order-finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','费用');
                            $(nTd).attr('data-key','financial_expense_total').attr('data-value',parseFloat(data));
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "订单扣款",
                    "name": "financial_deduction_total",
                    "data": "financial_deduction_total",
                    "className": "bg-fee",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('modal-show--for--order-finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','费用');
                            $(nTd).attr('data-key','financial_deduction_total').attr('data-value',parseFloat(data));
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "应收款",
                    "name": "financial_income_should",
                    "data": "id",
                    "className": "bg-fee",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            var $income_should = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total);

                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('item-modal-show--for--finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','应收款');
                            $(nTd).attr('data-key','financial_income_should').attr('data-value',parseFloat($income_should));
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $income_should = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total);
                        return parseFloat($income_should);
                    }
                },
                {
                    "title": "已收款",
                    "name": "financial_income_total",
                    "data": "financial_income_total",
                    "className": "bg-fee",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('item-modal-show--for--finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','已收款');
                            $(nTd).attr('data-key','financial_income_total').attr('data-value',parseFloat(data));
                        }
                    },
                    render: function(data, type, row, meta) {
                        return parseFloat(data);
                    }
                },
                {
                    "title": "待收款",
                    "name": "financial_income_pending",
                    "data": "id",
                    "className": "bg-fee _bold_",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            var $income_pending = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total) - parseFloat(row.financial_income_total);
                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('item-modal-show--for--finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','待收款');
                            $(nTd).attr('data-key','financial_income_pending').attr('data-value',parseFloat($income_pending));
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $income_pending = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total) - parseFloat(row.financial_income_total);
                        return parseFloat($income_pending);
                    }
                },
                {
                    "title": "利润",
                    "name": "financial_profit",
                    "data": "id",
                    "className": "bg-fee _bold_",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_published != 0)
                        {
                            var $profit = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total) - parseFloat(row.financial_expense_total);
                            // $(nTd).addClass('color-green');
                            $(nTd).addClass('item-modal-show--for--finance');
                            $(nTd).attr('data-type','all');
                            $(nTd).attr('data-id',row.id).attr('data-name','利润');
                            $(nTd).attr('data-key','financial_profit').attr('data-value',parseFloat($profit));
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $profit = parseFloat(row.freight_amount) - parseFloat(row.financial_deduction_total) - parseFloat(row.financial_expense_total);
                        return parseFloat($profit);
                    }
                },

                {
                    "title": "安排人",
                    "name": "arrange_people",
                    "data": "arrange_people",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','安排人');
                            $(nTd).attr('data-key','arrange_people').attr('data-value',data);
                            $(nTd).attr('data-column-name','安排人');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "收款人",
                    "name": "payee_name",
                    "data": "payee_name",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','收款人');
                            $(nTd).attr('data-key','payee_name').attr('data-value',data);
                            $(nTd).attr('data-column-name','收款人');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "车货源",
                    "name": "car_supply",
                    "data": "car_supply",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','车货源');
                            $(nTd).attr('data-key','car_supply').attr('data-value',data);
                            $(nTd).attr('data-column-name','车货源');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },


                {
                    "title": "应出发时间",
                    "data": 'should_departure_time',
                    "className": "order-info-time-edit bg-journey",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','应出发时间');
                            $(nTd).attr('data-key','should_departure_time').attr('data-value',$time_value);
                            $(nTd).attr('data-column-name','应出发时间');
                            $(nTd).attr('data-time-type','datetime');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '';

                        var $time = new Date(data*1000);
                        var $year = $time.getFullYear();
                        var $month = ('00'+($time.getMonth()+1)).slice(-2);
                        var $day = ('00'+($time.getDate())).slice(-2);
                        var $hour = ('00'+$time.getHours()).slice(-2);
                        var $minute = ('00'+$time.getMinutes()).slice(-2);
                        var $second = ('00'+$time.getSeconds()).slice(-2);

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear)
                        {
                            return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                        }
                    }
                },
                {
                    "title": "应到达时间",
                    "data": 'should_arrival_time',
                    "className": "bg-journey",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','应到达时间');
                            $(nTd).attr('data-key','should_arrival_time').attr('data-value',$time_value);
                            $(nTd).attr('data-column-name','应到达时间');
                            $(nTd).attr('data-time-type','datetime');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '';

                        var $time = new Date(data*1000);
                        var $year = $time.getFullYear();
                        var $month = ('00'+($time.getMonth()+1)).slice(-2);
                        var $day = ('00'+($time.getDate())).slice(-2);
                        var $hour = ('00'+$time.getHours()).slice(-2);
                        var $minute = ('00'+$time.getMinutes()).slice(-2);
                        var $second = ('00'+$time.getSeconds()).slice(-2);

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear)
                        {
                            return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>'+'<br>';
                        }

                    }
                },
                {
                    "title": "实际出发",
                    "data": 'actual_departure_time',
                    "className": "bg-journey",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','实际出发时间');
                            $(nTd).attr('data-key','actual_departure_time').attr('data-value',$time_value);
                            $(nTd).attr('data-column-name','实际出发时间');
                            $(nTd).attr('data-time-type','datetime');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '';

                        var $time = new Date(data*1000);
                        var $year = $time.getFullYear();
                        var $month = ('00'+($time.getMonth()+1)).slice(-2);
                        var $day = ('00'+($time.getDate())).slice(-2);
                        var $hour = ('00'+$time.getHours()).slice(-2);
                        var $minute = ('00'+$time.getMinutes()).slice(-2);
                        var $second = ('00'+$time.getSeconds()).slice(-2);

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear)
                        {
                            return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                        }
                    }
                },
                {
                    "title": "实际到达",
                    "data": 'actual_arrival_time',
                    "className": "bg-journey",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            var $time_value = '';
                            if(data)
                            {
                                var $date = new Date(data*1000);
                                var $year = $date.getFullYear();
                                var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                var $day = ('00'+($date.getDate())).slice(-2);
                                var $hour = ('00'+$date.getHours()).slice(-2);
                                var $minute = ('00'+$date.getMinutes()).slice(-2);
                                $time_value = $year+'-'+$month+'-'+$day+' '+$hour+':'+$minute;
                            }

                            $(nTd).addClass('modal-show--for--info-time-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','实际到达时间');
                            $(nTd).attr('data-key','actual_arrival_time').attr('data-value',$time_value);
                            $(nTd).attr('data-column-name','实际到达时间');
                            $(nTd).attr('data-time-type','datetime');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.is_completed != 1)
                        {
                        }
                        if(!data) return '';

                        var $time = new Date(data*1000);
                        var $year = $time.getFullYear();
                        var $month = ('00'+($time.getMonth()+1)).slice(-2);
                        var $day = ('00'+($time.getDate())).slice(-2);
                        var $hour = ('00'+$time.getHours()).slice(-2);
                        var $minute = ('00'+$time.getMinutes()).slice(-2);
                        var $second = ('00'+$time.getSeconds()).slice(-2);

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear)
                        {
                            return '<a href="javascript:void(0);">'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+$year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+'</a>';
                        }
                    }
                },
                {
                    "title": "行程",
                    "data": "id",
                    "className": "bg-journey",
                    "width": "200px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $journey_time = '';
                        var $travel_departure_overtime_time = '';
                        var $travel_arrival_overtime_time = '';

                        if(row.travel_journey_time) $journey_time = '<small class="btn-xs bg-gray">行程 '+row.travel_journey_time+'</small><br>';
                        if(row.travel_departure_overtime_time) $travel_departure_overtime_time = '<small class="btn-xs bg-red">发车超时 '+row.travel_departure_overtime_time+'</small><br>';
                        if(row.travel_arrival_overtime_time) $travel_arrival_overtime_time = '<small class="btn-xs bg-red">到达超时 '+row.travel_arrival_overtime_time+'</small><br>';

                        return $journey_time + $travel_departure_overtime_time + $travel_arrival_overtime_time;
                    }
                },


                {
                    "title": "备注",
                    "data": "remark",
                    "className": "text-left",
                    "width": "",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show--for--info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','备注');
                            $(nTd).attr('data-key','remark').attr('data-value',data);
                            $(nTd).attr('data-column-name','备注');
                            $(nTd).attr('data-text-type','textarea');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
//                            if(data) return '<small class="btn-xs bg-yellow">查看</small>';
//                            else return '';
                    }
                },

                {
                    "title": "创建人",
                    "data": "creator_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.true_name+'</a>';
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "className": "",
                    "width": "200px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_travel = '';
                        var $html_journey = '';
                        var $html_finance = '';
                        var $html_record = '';
                        var $html_delete = '';
                        var $html_publish = '';
                        var $html_abandon = '';
                        var $html_completed = '';
                        var $html_verified = '';
                        var $html_follow = '';
                        var $html_fee = '';
                        var $html_operation_record = '<a class="btn btn-xs bg-default modal-show--for--order-item-operation-record" data-id="'+data+'">记录</a>';
                        var $html_fee_record = '<a class="btn btn-xs bg-default modal-show--for--order-fee-record" data-id="'+data+'">费用记录</a>';



                        var $car_etc = '';
                        if(row.car_er != null) var $car_etc = row.car_er.ETC_account;

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
                            $html_publish = '<a class="btn btn-xs bg-olive- order--item-publish-submit" data-id="'+data+'">发布</a>';
                            $html_edit = '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>';
                            $html_edit = '<a class="btn btn-xs btn-primary- modal-show--for--order-item-edit" data-id="'+data+'">编辑</a>';
                            $html_verified = '<a class="btn btn-xs btn-default disabled">审核</a>';
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-submit" data-id="'+data+'">删除</a>';
                            $html_journey = '<a class="btn btn-xs btn-default disabled">行程</a>';
                        }
                        else
                        {
                            $html_detail = '<a class="btn btn-xs bg-primary item-modal-show--for--detail" data-id="'+data+'">详情</a>';
//                                $html_travel = '<a class="btn btn-xs bg-olive item-modal-show--for--travel" data-id="'+data+'">行程</a>';
//                             $html_finance = '<a class="btn btn-xs bg-orange item-modal-show--for--finance" data-id="'+data+'" data-etc="'+$car_etc+'">财务</a>';

                            $html_follow = '<a class="btn btn-xs modal-show--for--order-item-follow-create" data-id="'+data+'">跟进</a>';
                            $html_journey = '<a class="btn btn-xs modal-show--for--order-item-journey-create" data-id="'+data+'">行程</a>';
                            $html_fee = '<a class="btn btn-xs modal-show--for--order-item-fee-create" data-id="'+data+'">费用</a>';


                            if(row.is_completed == 1)
                            {
                                $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                            }
                            else
                            {
                                var $to_be_collected = parseFloat(row.amount) + parseFloat(row.oil_card_amount) - parseFloat(row.time_limitation_deduction) - parseFloat(row.income_total);
                                if($to_be_collected > 0)
                                {
                                    $html_completed = '<a class="btn btn-xs btn-default disabled">完成</a>';
                                }
                                else $html_completed = '<a class="btn btn-xs bg-blue- item-complete-submit" data-id="'+data+'">完成</a>';

                                if(row.item_status == 97)
                                {
                                    // $html_abandon = '<a class="btn btn-xs btn-default disabled">弃用</a>';
                                    $html_abandon = '<a class="btn btn-xs bg-teal item-reuse-submit" data-id="'+data+'">复用</a>';
                                }
                                else $html_abandon = '<a class="btn btn-xs bg-gray item-abandon-submit" data-id="'+data+'">弃用</a>';
                            }

                            // 审核
                            if(row.verifier_id == 0)
                            {
                                $html_verified = '<a class="btn btn-xs bg-teal item-verify-submit" data-id="'+data+'">审核</a>';
                            }
                            else
                            {
                                $html_verified = '<a class="btn btn-xs bg-aqua-gradient disabled">已审</a>';
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

                        var $more_html =
                            '<div class="btn-group">'+
                            '<button type="button" class="btn btn-xs btn-success-" style="padding:2px 8px; margin-right:0;">操作</button>'+
                            '<button type="button" class="btn btn-xs btn-success- dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding:2px 6px; margin-left:-1px;">'+
                            '<span class="caret"></span>'+
                            '<span class="sr-only">Toggle Dropdown</span>'+
                            '</button>'+
                            '<ul class="dropdown-menu" role="menu">'+
                            '<li><a href="#">Action</a></li>'+
                            '<li><a href="#">删除</a></li>'+
                            '<li><a href="#">弃用</a></li>'+
                            '<li class="divider"></li>'+
                            '<li><a href="#">Separate</a></li>'+
                            '</ul>'+
                            '</div>';

                        var $html =
                            // $html_able+
                            $html_edit+
                            $html_publish+
                            // $html_detail+
                            $html_follow+
                            $html_travel+
                            $html_journey+
                            $html_fee+
                            // $html_fee_record+
                            // $html_finance+
                            // $html_record+
                            // $html_verified+
                            $html_completed+
                            $html_delete+
                            $html_operation_record+
                            // $html_abandon+
                            '';
                        return $html;

                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('order-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

        // window.dataTableInstances[table_Id] = table;

        // return table;
    }
</script>