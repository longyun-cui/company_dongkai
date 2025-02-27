<script>

    function Datatable_for_DeliveryList($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');
        console.log($tableSearch);

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 200, 500], ["10", "50", "200", "500"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "iDisplayStart": 0,
            "iDisplayLength": 10,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
            // "scrollY": ($(document).height() - 448)+"px",
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/delivery/delivery-list') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
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
                    d.department_district = $tableSearch.find('select[name="order-department-district[]"]').val();
                    d.client = $tableSearch.find('select[name="delivery-client"]').val();
                    d.project = $tableSearch.find('select[name="delivery-project"]').val();
                    d.status = $tableSearch.find('select[name="order-status"]').val();
                    d.delivery_type = $tableSearch.find('select[name="order-delivery-type"]').val();
                    d.order_type = $tableSearch.find('select[name="order-type"]').val();
                    d.client_name = $tableSearch.find('input[name="delivery-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="delivery-client-phone"]').val();
                    d.is_wx = $tableSearch.find('select[name="order-is-wx"]').val();
                    d.is_repeat = $tableSearch.find('select[name="order-is-repeat"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();

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
                "leftColumns": "@if($is_mobile_equipment) 1 @else 6 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"
            },
            "columns": [
                // {
                //     "title": '<input type="checkbox" id="check-review-all">',
                //     "data": "id",
                //     "width": "40px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'" data-item-id="'+row.id+'"></label>';
                //     }
                // },
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
                    "title": "交付项目",
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
                {
                    "title": "患者类型",
                    "data": "client_type",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er)
                        {
                            var $result_html = '';
                            var $client_type = row.client_type;
                            if($client_type == 0)
                            {
                                $result_html = '<small class="btn-xs ">未选择</small>';
                            }
                            else if($client_type == 1)
                            {
                                $result_html = '<small class="btn-xs bg-blue">种植牙</small>';
                            }
                            else if($client_type == 2)
                            {
                                $result_html = '<small class="btn-xs bg-green">矫正</small>';
                            }
                            else if($client_type == 3)
                            {
                                $result_html = '<small class="btn-xs bg-red">正畸</small>';
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
                    "title": "客户意向",
                    "data": "order_id",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
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
                    "title": "牙齿数量",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.order_er) return row.order_er.teeth_count;
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
            ],
            "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }
</script>