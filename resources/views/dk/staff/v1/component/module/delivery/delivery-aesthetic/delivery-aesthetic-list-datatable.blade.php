<script>
    function Datatable__for__Delivery_Aesthetic_List($tableId)
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
                    d.order_category = 11;
                    d.id = $tableSearch.find('input[name="delivery-id"]').val();
                    d.order_id = $tableSearch.find('input[name="delivery-order-id"]').val();
                    d.remark = $tableSearch.find('input[name="order-remark"]').val();
                    d.description = $tableSearch.find('input[name="order-description"]').val();
                    d.assign = $tableSearch.find('input[name="order-assign"]').val();
                    d.assign_start = $tableSearch.find('input[name="order-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="order-ended"]').val();
                    d.name = $tableSearch.find('input[name="order-name"]').val();
                    d.title = $tableSearch.find('input[name="order-title"]').val();
                    d.keyword = $tableSearch.find('input[name="order-keyword"]').val();
                    d.client = $tableSearch.find('select[name="delivery-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project"]').val();
                    d.status = $tableSearch.find('select[name="order-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="order-delivery-type"]').val();
                    d.order_type = $tableSearch.find('select[name="order-type"]').val();
                    d.client_name = $tableSearch.find('input[name="order-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="order-client-phone"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    "data": "id",
                    "width": "40px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-order-id="'+row.order_id+'"></label>';
                    }
                },
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
                    "width": "100px",
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
                        var $html_delete = '';
                        var $html_exported = '<a class="btn btn-xs bg-blue- item-exported-submit" data-id="'+data+'">导出</a>';
                        var $html_follow = '<a class="btn btn-xs bg-blue item-modal-show-for-follow" data-id="'+data+'">客户跟进</a>';
                        var $html_quality = '<a class="btn btn-xs bg-olive item-quality-evaluate-submit" data-id="'+data+'">质量评估</a>';



                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-by-admin-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs bg-grey- item-restore-by-admin-submit" data-id="'+data+'">恢复</a>';
                        }

                        var $more_html =
                            '<div class="btn-group">'+
                            '<button type="button" class="btn btn-xs btn-success" style="padding:2px 8px; margin-right:0;">操作</button>'+
                            '<button type="button" class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="padding:2px 6px; margin-left:-1px;">'+
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
                            $html_exported+
                            $html_delete+
                            // $html_follow+
                            // $html_quality+
                            // $html_record+
                            '';
                        return $html;

                    }
                },
                {
                    "title": "导出状态",
                    "data": "is_exported",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-select-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','交付结果');
                            $(nTd).attr('data-key','is_exported').attr('data-value',data);
                            $(nTd).attr('data-column-name','审核结果');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 0) return '<small class="btn-xs btn-primary">未导出</small>';
                        else if(data == 1) return '<small class="btn-xs btn-success">已导出</small>';
                        else if(data == -1) return '<small class="btn-xs btn-warning">未选择</small>';
                        return data;
                    }
                },
                // {
                //     "title": "工单质量",
                //     "data": "order_quality",
                //     "className": "",
                //     "width": "80px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         if(data == "有效") return '<small class="btn-xs btn-success">有效</small>';
                //         else if(data == "无效") return '<small class="btn-xs btn-danger">无效</small>';
                //         else if(data == "重单") return '<small class="btn-xs btn-info">重单</small>';
                //         else if(data == "无法联系") return '<small class="btn-xs btn-warning">无法联系</small>';
                //         return data;
                //     }
                // },
                {
                    "title": "工单种类",
                    "data": "order_category",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        var $result_html = '';
                        if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-orange">口腔</small>';
                        }
                        else if(data == 11)
                        {
                            $result_html = '<small class="btn-xs bg-red">医美</small>';
                        }
                        else if(data == 31)
                        {
                            $result_html = '<small class="btn-xs bg-purple">奢侈品</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "工单ID",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        if(data) return '<a target="_blank" href="/item/order-list?order_id='+data+'">'+data+'</a>';;
                        return "--";
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
                    "title": "交付类型",
                    "data": "pivot_type",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return "--";
                        var $result_html = '';
                        if(data == 95) return '<small class="btn-xs bg-green">交付</small>';
                        else if(data == 96) return '<small class="btn-xs bg-orange">分发</small>';
                        return $result_html;
                    }
                },
                {
                    "title": "交付结果",
                    "data": "delivered_result",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return "--";
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
                    "title": "原始项目",
                    "data": "original_project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.original_project_er == null)
                        {
                            return '未指定';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.original_project_er.name+'</a>';
                        }
                    }
                },
                {
                    "title": "交付项目",
                    "data": "project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
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
                {
                    "title": "交付客户",
                    "data": "client_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.client_er == null)
                        {
                            return '未指定';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.client_er.username+'</a>';
                        }
                    }
                },
                    @if(in_array($me->user_type,[0,1,9,11]))
                {
                    "title": "所属公司",
                    "data": "company_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.company_er == null)
                        {
                            return '--';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.company_er.name+'</a>';
                        }
                    }
                },
                {
                    "title": "所属渠道",
                    "data": "channel_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.channel_er == null)
                        {
                            return '--';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.channel_er.name+'</a>';
                        }
                    }
                },
                {
                    "title": "所属商务",
                    "data": "business_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.business_er == null)
                        {
                            return '--';
                        }
                        else {
                            return '<a href="javascript:void(0);">'+row.business_er.name+'</a>';
                        }
                    }
                },
                    @endif
                {
                    "title": "品类",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
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
                        return "--";
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
                    "title": "渠道来源",
                    "data": "order_id",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er) return row.order_er.channel_source;
                        return "--";
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
                {
                    "title": "录音地址",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er)
                        {
                            if(row.order_er.recording_address) return '<a target="_blank" href="'+row.order_er.recording_address+'">录音地址</a>';
                            else return "--";
                        }
                        else return "--";
                    }
                },
                {
                    "className": "text-center",
                    "width": "80px",
                    "title": "创建者",
                    "data": "creator_id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
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
            ],
            "drawCallback": function (settings) {

                console.log('delivery-aesthetic-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>