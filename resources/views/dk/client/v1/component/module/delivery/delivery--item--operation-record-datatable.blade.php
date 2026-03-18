<script>
    var Datatable__for__Delivery__Item_Operation_Record_List = function ($datatable_id,$id,$type) {
        var datatableAjax__delivery__item_operation_record = function ($datatable_id,$id) {

            // var dt__delivery_operation_record = $('#datatable-delivery-follow-record');
            // dt__delivery_operation_record.DataTable().destroy();

            var dt__delivery__item_operation_record = $('#'+$datatable_id);
            if($.fn.DataTable.isDataTable(dt__delivery__item_operation_record))
            {
                // 已经初始化
                console.log('#datatable--for--delivery--item-operation-record-list // 已经初始化');
                $(dt__delivery__item_operation_record).DataTable().destroy();
            }
            else
            {
                // 未初始化
                console.log('#datatable--for--delivery--item-operation-record-list // 未初始化');
            }

            var ajax_datatable_delivery_operation_record = dt__delivery__item_operation_record.DataTable({
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
                    'url': "/o1/delivery/item-operation-record-list/datatable-query?id="+$id,
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
                        "data": "operate_category",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $category_html = '';
                            var $html_type = '';

                            if(data == 1)
                            {
                                $category_html = '<small class="btn-xs bg-blue">操作</small>';

                                if(row.operate_type == 1) $html_type = '<small class="btn-xs bg-blue">编辑</small>';
                                if(row.operate_type == 2) $html_type = '<small class="btn-xs bg-aqua">字段编辑</small>';
                                if(row.operate_type == 9) $html_type = '<small class="btn-xs bg-blue">发布</small>';
                                if(row.operate_type == 91) $html_type = '<small class="btn-xs bg-blue">质量评价</small>';
                                if(row.operate_type == 96) $html_type = '<small class="btn-xs bg-teal">客户回访</small>';
                                if(row.operate_type == 98) $html_type = '<small class="btn-xs bg-green">上门状态</small>';
                            }
                            if(data == 11)
                            {
                                $category_html = '<small class="btn-xs bg-blue">发布</small>';
                                // $html_type = '<small class="btn-xs bg-blue">发布</small>';
                            }
                            else if(data == 81)
                            {
                                $category_text = "成交记录";
                                // $category_html = '<small class="btn-xs bg-yellow">费用</small>';
                                if(row.operate_type == 88)
                                {
                                    $category_text += "&入账";
                                    // $category_html += '<small class="btn-xs bg-yellow">入账</small>';
                                }
                                $category_html = '<small class="btn-xs bg-yellow">'+$category_text+'</small>';
                            }
                            else if(data == 88)
                            {
                                $category_html = '<small class="btn-xs bg-red">入账</small>';
                            }
                            else if(data == 91)
                            {
                                $category_html = '<small class="btn-xs bg-aqua">跟进</small>';
                                if(row.operate_type == 11) $html_type = '<small class="btn-xs bg-aqua">跟进</small>';
                            }
                            else if(data == 101)
                            {
                                $category_html = '<small class="btn-xs bg-red">附件</small>';
                            }
                            else $category_html = '';


                            return $category_html + $html_type;
                        }
                    },
                    // {
                    //     "className": "",
                    //     "width": "120px",
                    //     "title": "时间",
                    //     "data": "custom_datetime",
                    //     "orderable": false,
                    //     render: function(data, type, row, meta) {
                    //         if(row.operate_type == 11)
                    //         {
                    //             return '';
                    //         }
                    //
                    //         if(data)
                    //         {
                    //             let d = new Date(data);
                    //             let year = d.getFullYear();
                    //             let month = ('0' + (d.getMonth() + 1)).slice(-2); // 月份是从0开始的
                    //             let day = ('0' + d.getDate()).slice(-2);
                    //             let hours = ('0' + d.getHours()).slice(-2);
                    //             let minutes = ('0' + d.getMinutes()).slice(-2);
                    //             let seconds = ('0' + d.getSeconds()).slice(-2);
                    //
                    //             return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                    //         }
                    //         return data;
                    //     }
                    // },
                    {
                        "className": "text-left",
                        "width": "480px",
                        "title": "详情",
                        "data": "content",
                        "orderable": false,
                        render: function(data, type, row, meta) {

                            if($.trim(data))
                            {
                                try
                                {
                                    var $customer_list = JSON.parse(data);

                                    var $return_html = '';
                                    $.each($customer_list, function($index, $value) {
                                        if($value.before == '') $return_html += '【'+ $value.title +'】' + $value.after + ' <br>';
                                        else $return_html  += '【'+ $value.title +'】' + $value.before + ' → ' + $value.after + ' <br>';
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
                    },
                    {
                        "className": "text-center",
                        "width": "120px",
                        "title": "操作人",
                        "data": "creator_id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(window.staffDepartment == 'CSD' && window.staffRole != 'director')
                            {
                                if(row.creator_team_id != window.teamId)
                                {
                                    return row.creator == null ? '--' : '****';
                                }

                                if([1,11].includes(row.operate_category))
                                {
                                    return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.name+'</a>';
                                }
                                else
                                {
                                    return row.creator == null ? '--' : '****';
                                }
                            }
                            else
                            {
                                return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.name+'</a>';
                            }
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
            init: datatableAjax__delivery__item_operation_record
        }
    }();
</script>