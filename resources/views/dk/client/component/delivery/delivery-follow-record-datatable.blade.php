<script>
    var Datatable_Delivery_Follow_Record = function ($id) {
        var datatableAjax_record = function ($id) {

            var dt_delivery_follow_record = $('#datatable-delivery-follow-record');
            dt_delivery_follow_record.DataTable().destroy();
            var ajax_datatable_delivery_follow_record = dt_delivery_follow_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[10, 50], ["10", "50"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "pagingType": "simple_numbers",
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
                "order": [],
                "orderCellsTop": true,
                "ajax": {
                    'url': "/v1/operate/delivery/item-follow-record-datatable-query?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="modify-name"]').val();
                        d.title = $('input[name="modify-title"]').val();
                        d.keyword = $('input[name="modify-keyword"]').val();
                        d.status = $('select[name="modify-status"]').val();
                    },
                },
                "columns": [
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "title": "ID",
                        "data": "id",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "类型",
                        "data": "follow_type",
                        "className": "",
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-green">跟进记录</small>';
                            else if(data == 11) return '<small class="btn-xs bg-teal">用户信息</small>';
                            else if(data == 31) return '<small class="btn-xs bg-yellow">上门状态</small>';
                            else if(data == 88) return '<small class="btn-xs bg-red">成交记录</small>';
                            else return '有误';
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "跟进时间",
                        "data": "follow_datetime",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.follow_type == 11)
                            {
                                return '';
                            }

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
                            return data;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "480px",
                        "title": "跟进情况",
                        "data": "custom_text_1",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.follow_type == 1)
                            {
                                return data;
                            }
                            else if(row.follow_type == 11)
                            {
                                if($.trim(data))
                                {
                                    try
                                    {
                                        var $customer_list = JSON.parse(data);

                                        var $return_html = '';
                                        $.each($customer_list, function($index, $value) {
                                            if($value.field == 'is_wx')
                                            {
                                                if($value.after == 0) $return_html += '【是否+V】否 <br>';
                                                else if($value.after == 1) $return_html += '【是否+V】是 <br>';
                                                else $return_html += '';
                                            }
                                            else if($value.field == 'customer_remark')
                                            {
                                                if($value.before == '') $return_html += '【客户备注】' + $value.after + ' <br>';
                                                else $return_html  += '【客户备注】' + $value.before + ' → ' + $value.after + ' <br>';
                                            }
                                            else if($value.field == 'client_contact_id')
                                            {
                                                if($value.before == '') $return_html += '【联系渠道】' + $value.after + ' <br>';
                                                else $return_html  += '【联系渠道】' + $value.before + ' → ' + $value.after + ' <br>';
                                            }
                                        });
                                        return $return_html;
                                    }
                                    catch(e)
                                    {
                                        return '';
                                    }
                                }
                                else return '';
                            }
                            else if(row.follow_type == 31)
                            {
                                if($.trim(data))
                                {
                                    try
                                    {
                                        var $customer_list = JSON.parse(data);

                                        var $return_html = '';
                                        $.each($customer_list, function($index, $value) {
                                            if($value.field == 'is_come')
                                            {
                                                if($value.after == 0) $return_html += '【上门状态】否 <br>';
                                                else if($value.after == 9) $return_html += '【上门状态】预约上门 <br>';
                                                else if($value.after == 11) $return_html += '【上门状态】已上门 <br>';
                                                else $return_html += '';
                                            }
                                            else if($value.field == 'come_datetime')
                                            {
                                                $return_html += '【上门时间】' + $value.after + ' <br>';
                                            }
                                            else if($value.field == 'come_description')
                                            {
                                                $return_html += '【上门备注】' + $value.after + ' <br>';
                                            }
                                        });
                                        return $return_html;
                                    }
                                    catch(e)
                                    {
                                        return '';
                                    }
                                }
                                else return '';
                            }
                            else if(row.follow_type == 88)
                            {
                                if($.trim(data))
                                {
                                    try
                                    {
                                        var $customer_list = JSON.parse(data);

                                        var $return_html = '';
                                        $.each($customer_list, function($index, $value) {
                                            if($value.field == 'transaction_datetime')
                                            {
                                                $return_html += '【交易时间】' + $value.after + ' <br>';
                                            }
                                            else if($value.field == 'transaction_count')
                                            {
                                                $return_html += '【交易数量】' + $value.after + ' <br>';
                                            }
                                            else if($value.field == 'transaction_amount')
                                            {
                                                $return_html += '【交易金额】' + $value.after + ' <br>';
                                            }
                                            else if($value.field == 'transaction_description')
                                            {
                                                $return_html += '【交易备注】' + $value.after + ' <br>';
                                            }
                                        });
                                        return $return_html;
                                    }
                                    catch(e)
                                    {
                                        return '';
                                    }
                                }
                                else return '';
                            }
                            else return '有误';
                        }
                    },
                    {
                        "className": "text-center",
                        "width": "60px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "记录时间",
                        "data": "created_at",
                        "orderable": false,
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
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                            var $currentYear = new Date().getFullYear();
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                    $('.lightcase-image').lightcase({
                        maxWidth: 9999,
                        maxHeight: 9999
                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


        };
        return {
            init: datatableAjax_record
        }
    }();
</script>