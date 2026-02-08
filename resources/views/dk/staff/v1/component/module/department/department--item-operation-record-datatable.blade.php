<script>
    var Datatable__for__Department_Item_Operation_Record_List = function ($id) {
        var datatableAjax__department_item_operation_record = function ($id) {

            var $datatable_id = 'datatable--for--department-item-operation-record-list';
            var dt__department_item_operation_record = $('#'+$datatable_id);
            if($.fn.DataTable.isDataTable(dt__department_item_operation_record))
            {
                // 已经初始化
                console.log('#'+$datatable_id+' // 已经初始化');
                $(dt__department_item_operation_record).DataTable().destroy();
            }
            else
            {
                // 未初始化
                console.log('#'+$datatable_id+' // 未初始化');
            }

            var ajax_datatable__department_item_operation_record = dt__department_item_operation_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50], ["50"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/o1/department/item-operation-record-list/datatable-query?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="department-item-operation-name"]').val();
                        d.title = $('input[name="department-item-operation-title"]').val();
                        d.keyword = $('input[name="department-item-operation-keyword"]').val();
                        d.type = $('select[name="department-item-operation-type"]').val();
                        d.status = $('select[name="department-item-operation-status"]').val();
                    },
                },
                "pagingType": "simple_numbers",
                "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t> <"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p>',
                "order": [],
                "orderCellsTop": true,
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
                        "width": "80px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $category_html = '' ;
                            var $type_html ='' ;

                            if(data == 1)
                            {
                                $category_html = '<small class="btn-xs bg-blue">操作</small>';

                                if(row.operate_type == 1) $type_html = '<small class="btn-xs bg-blue">编辑</small>';
                                if(row.operate_type == 9) $type_html = '<small class="btn-xs bg-blue">发布</small>';
                            }
                            else if(data == 11) $category_html = '<small class="btn-xs bg-teal">跟进</small>';
                            else if(data == 21) $category_html = '<small class="btn-xs bg-yellow">客户回访</small>';
                            else if(data == 31) $category_html = '<small class="btn-xs bg-yellow">上门状态</small>';
                            else if(data == 71) $category_html = '<small class="btn-xs bg-purple">行程</small>';
                            else if(data == 81) $category_html = '<small class="btn-xs bg-red">费用</small>';
                            else if(data == 88) $category_html = '<small class="btn-xs bg-red">成交</small>';
                            else if(data == 101) $category_html = '<small class="btn-xs bg-red">附件</small>';
                            else $category_html = '<small class="btn-xs bg-red">附件</small>';


                            return $category_html + $type_html;
                        }
                    },
                    {
                        "className": "",
                        "width": "120px",
                        "title": "时间",
                        "data": "custom_datetime",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(row.operate_type == 11)
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
                            if($year == $currentYear) return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                            else return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
                        }
                    }
                ],
                "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(0).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

                },
                "language": { url: '/common/dataTableI18n' },
            });


        };
        return {
            init: datatableAjax__department_item_operation_record
        }
    }();
</script>