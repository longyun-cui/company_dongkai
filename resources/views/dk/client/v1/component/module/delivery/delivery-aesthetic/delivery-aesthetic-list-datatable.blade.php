<script>
    function Datatable__for__Delivery_List($tableId)
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
            "ajax": {
                'url': "{{ url('/o1/delivery/delivery-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.order_category = 1;
                    d.id = $tableSearch.find('input[name="delivery-id"]').val();
                    d.order_id = $tableSearch.find('input[name="delivery-order-id"]').val();
                    d.remark = $tableSearch.find('input[name="order-remark"]').val();
                    d.description = $tableSearch.find('input[name="order-description"]').val();

                    d.staff = $tableSearch.find('select[name="delivery-staff"]').val();
                    d.client = $tableSearch.find('select[name="delivery-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project"]').val();

                    d.status = $tableSearch.find('select[name="delivery-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="delivery-delivery-type"]').val();
                    d.order_type = $tableSearch.find('select[name="delivery-type"]').val();
                    d.client_type = $tableSearch.find('select[name="delivery-client-type"]').val();
                    d.client_name = $tableSearch.find('input[name="delivery-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="delivery-client-phone"]').val();
                    d.is_wx = $tableSearch.find('select[name="delivery-is-wx"]').val();

                    d.city = $('select[name="delivery-city[]"]').val();
                    d.district = $('select[name="delivery-district[]"]').val();

                    d.exported_status = $('select[name="delivery-exported-status"]').val();
                    d.assign_status = $('select[name="delivery-assign-status"]').val();
                    d.is_api_pushed = $('select[name="delivery-is-api-pushed"]').val();

                    d.quality = $('select[name="delivery-quality"]').val();

                    d.delivered_status = $tableSearch.find('select[name="delivery-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="delivery-delivered-result[]"]').val();
                    d.time_type = $tableSearch.find('input[name="delivery-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="delivery-month"]').val();
                    d.time_date = $tableSearch.find('input[name="delivery-date"]').val();
                    d.date_start = $tableSearch.find('input[name="delivery-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="delivery-ended"]').val();

                    d.callback_date = $tableSearch.find('input[name="delivery-callback-date"]').val();

                    d.is_come = $tableSearch.find('select[name="delivery-is-come"]').val();
                    d.come_date = $tableSearch.find('input[name="delivery-come-date"]').val();

                    d.contact = $tableSearch.find('select[name="delivery-contact[]"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
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
                        var $html_quality = '<a class="btn btn-xs bg-default delivery--item--quality-evaluate--submit" data-id="'+data+'">质量评价</a>';
                        var $html_update = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-customer-update" data-id="'+data+'">更新</a>';
                        var $html_callback = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-callback-update" data-id="'+data+'">回访</a>';
                        var $html_come = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-come-update" data-id="'+data+'">上门</a>';
                        var $html_trade = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-trade-create" data-id="'+data+'">成交</a>';
                        var $html_follow = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-follow-create" data-id="'+data+'">跟进</a>';
                        var $html_follow_record = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-operation-record" data-id="'+data+'">记录</a>';
                        var $html_detail = '<a class="btn btn-xs bg-default modal-show--for--delivery--item-detail" data-id="'+data+'">详情</a>';

                        var $html =
                            $html_quality+
                            $html_callback+
                            $html_come+
                            $html_trade+
                            $html_follow+
                            $html_follow_record+
                            // $html_record+
                            $html_detail+
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
                        if(row.is_completed != 1)
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
                {
                    "title": "工单质量",
                    "data": "order_quality",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('order_quality');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','工单质量');
                            $(nTd).attr('data-key','order_quality');
                            $(nTd).attr('data-value',data);

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
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('assign_status');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','分配状态');
                            $(nTd).attr('data-key','assign_status');
                            $(nTd).attr('data-value',row.id);

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 0)
                        {
                            // return '<small class="btn-xs btn-warning">待分配</small>';
                            if(row.client_staff_id == 0) return '<small class="btn-xs btn-warning">待分配</small>';
                            else if(row.client_staff_id > 1) return '<small class="btn-xs btn-success">已分配</small>';
                            return data;
                        }
                        else if(data == 1) return '<small class="btn-xs btn-success">已分配</small>';
                        return data;
                    }
                },
                {
                    "title": "分派员工",
                    "data": "client_staff_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('client_staff');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','分派员工');
                            $(nTd).attr('data-key','client_staff_id');
                            $(nTd).attr('data-value',row.id);

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
                            return '<a href="javascript:void(0);">'+row.client_staff_er.name+'</a>';
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
                    "title": "提单坐席",
                    "data": 'id',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
//                            return data;
                        if(!row.order_er) return '';
                        if(!row.order_er.creator) return '';
                        return row.order_er.creator.name;
                    }
                },
                {
                    "title": "提单时间",
                    "data": 'created_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
//                            return data;
                        if(!row.order_er) return '';
                        var $date = new Date(row.order_er.published_at*1000);
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
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','客户姓名');
                        $(nTd).attr('data-key','client_name');
                        $(nTd).attr('data-value',data);
                    },
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
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','客户电话');
                        $(nTd).attr('data-key','client_phone');
                        $(nTd).attr('data-value',data);
                    },
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
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','微信号');
                        $(nTd).attr('data-key','client_wx');
                        $(nTd).attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        if(row.order_er) return row.order_er.wx_id;
                        return "--";
                    }
                },
                {
                    "title": "客户意向",
                    "data": "order_id",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','客户意向');
                        $(nTd).attr('data-key','client_intention');
                        $(nTd).attr('data-value',data);
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(row.order_er)
                        {
                            var $data = row.order_er.client_intention;
                            if($data == "A类")
                            {
                                $result_html = '<small class="btn-xs bg-red">'+$data+'</small>';
                            }
                            else if($data == "B类")
                            {
                                $result_html = '<small class="btn-xs bg-blue">'+$data+'</small>';
                            }
                            else if($data == "C类")
                            {
                                $result_html = '<small class="btn-xs bg-green">'+$data+'</small>';
                            }
                            else
                            {
                                $result_html = '--';
                            }
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "品类",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','品类');
                        $(nTd).attr('data-key','aesthetic_category');
                        $(nTd).attr('data-value',data);
                    },
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
                                $result_html = '<small class="btn-xs bg-blue">脸部</small>';
                            }
                            else if($field_1 == 21)
                            {
                                $result_html = '<small class="btn-xs bg-green">植发</small>';
                            }
                            else if($field_1 == 31)
                            {
                                $result_html = '<small class="btn-xs bg-orange">身体</small>';
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
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).attr('data-id',row.id);
                        $(nTd).attr('data-name','所在城市');
                        $(nTd).attr('data-key','location');
                        $(nTd).attr('data-value','');
                    },
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
                    "width": "240px",
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
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        $(nTd).addClass('modal-show-for-item-detail');
                        $(nTd).attr('data-order-category','1');
                        $(nTd).attr('data-id',row.id).attr('data-name','通话小结');
                        $(nTd).attr('data-key','description').attr('data-value',row.order_er.description);
                        if(row.order_er.recording_address_list)
                        {
                            var $recording_address = row.order_er.recording_address_list;
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

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>