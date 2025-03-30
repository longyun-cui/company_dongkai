<script>

    function Datatable_for_DeliveryList($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 200, 500], ["10", "50", "200", "500"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "iDisplayStart": 0,
            "iDisplayLength": 10,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": ($(document).height() - 448)+"px",
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/v1/operate/delivery/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="delivery-id"]').val();
                    d.order_id = $tableSearch.find('input[name="delivery-order-id"]').val();
                    d.name = $tableSearch.find('input[name="delivery-name"]').val();
                    d.title = $tableSearch.find('input[name="delivery-title"]').val();
                    d.keyword = $tableSearch.find('input[name="delivery-keyword"]').val();
                    d.remark = $tableSearch.find('input[name="delivery-remark"]').val();
                    d.description = $tableSearch.find('input[name="delivery-description"]').val();
                    d.department_district = $tableSearch.find('select[name="delivery-department-district[]"]').val();
                    d.client = $tableSearch.find('select[name="delivery-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project"]').val();
                    d.status = $tableSearch.find('select[name="delivery-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="delivery-delivery-type"]').val();
                    d.order_type = $tableSearch.find('select[name="delivery-type"]').val();
                    d.client_type = $tableSearch.find('select[name="delivery-client-type"]').val();
                    d.client_name = $tableSearch.find('input[name="delivery-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="delivery-client-phone"]').val();

                    d.exported_status = $('select[name="delivery-exported-status"]').val();
                    d.assign_status = $('select[name="delivery-assign-status"]').val();
                    d.is_api_pushed = $('select[name="delivery-is-api-pushed"]').val();

                    d.delivered_status = $tableSearch.find('select[name="delivery-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="delivery-delivered-result[]"]').val();
                    d.time_type = $tableSearch.find('input[name="delivery-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="delivery-month"]').val();
                    d.time_date = $tableSearch.find('input[name="delivery-date"]').val();
                    d.date_start = $tableSearch.find('input[name="delivery-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="delivery-ended"]').val();

                    d.is_come = $tableSearch.find('select[name="delivery-is-come"]').val();
                    d.come_date = $tableSearch.find('input[name="delivery-come-date"]').val();

                    d.city = $('select[name="delivery-city[]"]').val();
                    d.district = $('select[name="delivery-district[]"]').val();

                },
            },
            "columnDefs": [
                {
                    // "targets": [10, 11, 15, 16],
                    "targets": [],
                    "visible": false,
                    "searchable": false
                }
            ],
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 0 @endif"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" id="check-review-all">',
                    "data": "id",
                    "width": "40px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-item-id="'+row.id+'"></label>';
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
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "className": "",
                    "width": "240px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_operate');
                            $(nTd).attr('data-id',row.id).attr('data-name','操作');
                            $(nTd).attr('data-key','order_operate').attr('data-value',row.id);
                            $(nTd).attr('data-content',JSON.stringify(row.content_decode));
                        }
                    },
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_quality = '<a class="btn btn-xs bg-default item-quality-evaluate-submit" data-id="'+data+'">质量评价</a>';
                        var $html_update = '<a class="btn btn-xs bg-default item-modal-show-for-customer-update" data-id="'+data+'">更新</a>';
                        var $html_come = '<a class="btn btn-xs bg-default item-modal-show-for-come-update" data-id="'+data+'">上门</a>';
                        var $html_trade = '<a class="btn btn-xs bg-default item-modal-show-for-trade-create" data-id="'+data+'">成交</a>';
                        var $html_follow = '<a class="btn btn-xs bg-default item-modal-show-for-follow-create" data-id="'+data+'">跟进</a>';
                        var $html_follow_record = '<a class="btn btn-xs bg-default item-modal-show-for-follow-record" data-id="'+data+'">记录</a>';


                        var $html =
                            $html_quality+
                            $html_update+
                            $html_come+
                            $html_trade+
                            $html_follow+
                            $html_follow_record+
                            // $html_record+
                            '';
                        return $html;

                    }
                },
                @if($me->client_er->is_api_scrm == 1)
                {
                    "title": "API推送",
                    "data": "is_api_pushed",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('is_api_pushed');
                            $(nTd).attr('data-id',row.id).attr('data-name','分配状态');
                            $(nTd).attr('data-key','is_api_pushed').attr('data-value',data);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 0) return '<small class="btn-xs btn-info">未推送</small>';
                        else if(data == 1) return '<small class="btn-xs btn-success">已推送</small>';
                        return data;
                    }
                },
                @endif
                @if(in_array($me->user_type,[0,1,9,11]))
                {
                    "title": "工单质量",
                    "data": "order_quality",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_quality');
                            $(nTd).attr('data-id',row.id).attr('data-name','工单质量');
                            $(nTd).attr('data-key','order_quality').attr('data-value',data);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == "有效") return '<small class="btn-xs btn-success">有效</small>';
                        else if(data == "无效") return '<small class="btn-xs btn-danger">无效</small>';
                        else if(data == "重单") return '<small class="btn-xs btn-info">重单</small>';
                        else if(data == "无法联系") return '<small class="btn-xs btn-warning">无法联系</small>';
                        return data;
                    }
                },
                {
                    "title": "分配状态",
                    "data": "assign_status",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('assign_status');
                            $(nTd).attr('data-id',row.id).attr('data-name','分配状态');
                            $(nTd).attr('data-key','assign_status').attr('data-value',row.id);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        // if(data == 0) return '<small class="btn-xs btn-warning">待分配</small>';
                        // else if(data == 1) return '<small class="btn-xs btn-success">已分配</small>';
                        // return data;
                        if(row.client_staff_id == 0) return '<small class="btn-xs btn-warning">待分配</small>';
                        else if(row.client_staff_id > 1) return '<small class="btn-xs btn-success">已分配</small>';
                        return data;
                    }
                },
                @endif
                {
                    "title": "分派员工",
                    "data": "client_staff_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('client_staff');
                            $(nTd).attr('data-id',row.id).attr('data-name','分派员工');
                            $(nTd).attr('data-key','client_staff_id').attr('data-value',row.id);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.client_staff_er == null)
                        {
                            return '未指定';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+row.client_staff_er.username+'</a>';
                        }
                    }
                },
                {
                    "title": "交付时间",
                    "data": 'created_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
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
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                    }
                },
                {
                    "title": "客户姓名",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er) return row.order_er.client_name;
                        return "--";
                    }
                },
                {
                    "title": "客户电话",
                    "data": "client_phone",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "微信号",
                    "data": "order_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er) return row.order_er.wx_id;
                        return "--";
                    }
                },
                {
                    "title": "品类",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(row.order_er)
                        {
                            var $field_1 = row.order_er.field_1;
                            var $result_html = '';
                            if($field_1 == 0)
                            {
                                $result_html = '<small class="btn-xs bg-default"></small>';
                            }
                            else if($field_1 == 1)
                            {
                                $result_html = '<small class="btn-xs bg-blue">鞋帽服装</small>';
                            }
                            else if($field_1 == 2)
                            {
                                $result_html = '<small class="btn-xs bg-green">包</small>';
                            }
                            else if($field_1 == 3)
                            {
                                $result_html = '<small class="btn-xs bg-orange">手表</small>';
                            }
                            else if($field_1 == 4)
                            {
                                $result_html = '<small class="btn-xs bg-red">珠宝</small>';
                            }
                            else if($field_1 == 99)
                            {
                                $result_html = '<small class="btn-xs bg-navy">其他</small>';
                            }
                            else
                            {
                                $result_html = '未知类型';
                            }
                            return $result_html;
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "所在城市",
                    "data": "order_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er)
                        {
                            if(row.order_er.location_city)
                            {
                                if(row.order_er.location_district)
                                {
                                    return row.order_er.location_city + ' - ' + row.order_er.location_district;
                                }
                                else return row.order_er.location_city;
                            }
                            else return '--';
                        }
                        else return '--';
                    }
                },
                {
                    "title": "是否+V",
                    "data": "is_wx",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs btn-primary">是</small>';
                        else return '--';
                    }
                },
                {
                    "title": "客户备注",
                    "data": "customer_remark",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "联系渠道",
                    "data": "client_contact_id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.client_contact_er == null ? '--' : '<a href="javascript:void(0);">'+row.client_contact_er.name+'</a>';
                    }
                },
                {
                    "title": "上门状态",
                    "data": "is_come",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 0) return '<small class="btn-xs btn-info">否</small>';
                        if(data == 9) return '<small class="btn-xs btn-warning">预约中</small>';
                        if(data == 11) return '<small class="btn-xs btn-success">已上门</small>';
                        else return '--';
                    }
                },
                {
                    "title": "上门时间",
                    "data": "come_datetime",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data)
                        {
                            let d = new Date(data);
                            let year = d.getFullYear();
                            let month = ('0' + (d.getMonth() + 1)).slice(-2); // 月份是从0开始的
                            let day = ('0' + d.getDate()).slice(-2);
                            let hours = ('0' + d.getHours()).slice(-2);
                            let minutes = ('0' + d.getMinutes()).slice(-2);
                            let seconds = ('0' + d.getSeconds()).slice(-2);

                            return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                        }
                        else return '--';
                    }
                },
                {
                    "title": "成交次数",
                    "data": "transaction_num",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "成交总数",
                    "data": "transaction_count",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "成交总额",
                    "data": "transaction_amount",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "最新跟进说明",
                    "data": "follow_latest_description",
                    "className": "",
                    "width": "",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "通话小结",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er)
                        {
                            if(row.order_er.description) return '<small class="btn-xs bg-yellow">双击查看</small>';
                            else return "--";
                        }
                        else return "--";
                    }
                },
                // {
                //     "title": "录音地址",
                //     "data": "order_id",
                //     "className": "",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         if(row.order_er)
                //         {
                //             if(row.order_er.recording_address) return '<a target="_blank" href="'+row.order_er.recording_address+'">录音地址</a>';
                //             else return "--";
                //         }
                //         else return "--";
                //     }
                // },
            ],
            "drawCallback": function (settings) {

                console.log('delivery-list-datatable-execute');
                var firstRow = this.api().row(0).data();
                if (firstRow)
                {
                    var $last_id = firstRow.id;
                    var $last_delivery_id = localStorage.getItem('last_delivery_id');
                    if($last_delivery_id)
                    {
                        if($last_id > $last_delivery_id)
                        {
                            localStorage.setItem('last_delivery_id',$last_id);
                            // $('.notification-dom').show();
                            // alertSound();
                        }
                    }
                    else
                    {
                        localStorage.setItem('last_delivery_id',$last_id);
                        // $('.notification-dom').show();
                        // alertSound();
                    }
                    $('.notification-dom').hide();
                }

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>