<script>
    var Datatable__for__Order_Item_Delivery_Record_List = function ($datatable_id,$id) {
        var datatableAjax__order__item_delivery_record = function ($datatable_id,$id) {

            // var dt__order__item_delivery_record = $('#datatable--for--order-item-delivery-record-list');
            var dt__order__item_delivery_record = $('#'+$datatable_id);
            if($.fn.DataTable.isDataTable(dt__order__item_delivery_record))
            {
                // 已经初始化
                console.log('#'+$datatable_id+' // 已经初始化');
                $(dt__order__item_delivery_record).DataTable().destroy();
            }
            else
            {
                // 未初始化
                console.log('#'+$datatable_id+' // 未初始化');
            }

            var ajax_datatable__order__item_delivery_record = dt__order__item_delivery_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/o1/order/item-delivery-record-list/datatable-query?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="order-delivery-name"]').val();
                        d.title = $('input[name="order-delivery-title"]').val();
                        d.keyword = $('input[name="order-delivery-keyword"]').val();
                        d.type = $('select[name="order-delivery-type"]').val();
                        d.status = $('select[name="order-delivery-status"]').val();
                        d.draw = (new Date().getTime());
                    },
                    "cache": false,
                },
                "pagingType": "simple_numbers",
                "sDom": '<t>',
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
                        "data": "item_type",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 'order') return '<small class="btn-xs bg-green">工单</small>';
                            else if(data == 'delivery') return '<small class="btn-xs bg-blue">交付</small>';
                            else return '有误';
                        }
                    },
                    {
                        "title": "来源",
                        "data": "id",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $result_html = '';
                            if(row.item_type == 'order')
                            {
                                if(row.created_type == 1)
                                {
                                    $result_html = '<small class="btn-xs bg-green">人工</small>';
                                }
                                else if(row.created_type == 91)
                                {
                                    $result_html = '<small class="btn-xs bg-red">百应AI</small>';
                                }
                                else if(row.created_type == 99)
                                {
                                    $result_html = '<small class="btn-xs bg-red">API</small>';
                                }
                                else if(row.created_type == 9)
                                {
                                    $result_html = '<small class="btn-xs bg-yellow">导入</small>';
                                }
                                else
                                {
                                    $result_html = '<small class="btn-xs bg-black">有误</small>';
                                }
                            }
                            else if(row.item_type == 'delivery')
                            {
                                if(row.pivot_type == 95) return '<small class="btn-xs bg-green">交付</small>';
                                else if(row.pivot_type == 96) return '<small class="btn-xs bg-orange">分发</small>';
                            }
                            return $result_html;
                        }
                    },
                    {
                        "title": "项目",
                        "data": "id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            if(row.item_type == 'order')
                            {
                                if(row.project_er)
                                {
                                    if(row.project_er.alias_name) $return_html = row.project_er.name + ' ('+row.project_er.alias_name+')';
                                    else $return_html = row.project_er.name;
                                }
                            }
                            else if(row.item_type == 'delivery')
                            {
                                if(row.original_project_er)
                                {
                                    if(row.original_project_er.alias_name) $return_html = row.original_project_er.name + ' ('+row.original_project_er.alias_name+')';
                                    else $return_html = row.original_project_er.name;
                                }
                            }
                            return $return_html;
                        }
                    },
                    {
                        "title": "交付项目",
                        "data": "id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            if(row.item_type == 'order')
                            {
                                if(row.delivered_project_er)
                                {
                                    if(row.project_er.alias_name) $return_html = row.project_er.name + ' ('+row.project_er.alias_name+')';
                                    else $return_html = row.project_er.name;
                                }
                            }
                            else if(row.item_type == 'delivery')
                            {
                                if(row.project_er)
                                {
                                    if(row.project_er.alias_name) $return_html = row.project_er.name + ' ('+row.project_er.alias_name+')';
                                    else $return_html = row.project_er.name;
                                }
                            }
                            return $return_html;
                        }
                    },
                    {
                        "title": "交付客户",
                        "data": "id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            if(row.item_type == 'order')
                            {
                                if(row.delivered_client_er)
                                {
                                    $return_html = row.client_er.name;
                                }
                            }
                            else if(row.item_type == 'delivery')
                            {
                                if(row.project_er)
                                {
                                    $return_html = row.client_er.name;
                                }
                            }
                            return $return_html;
                        }
                    },
                    {
                        "className": "",
                        "width": "160px",
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

                },
                "language": { url: '/common/dataTableI18n' },
            });


        };
        return {
            init: datatableAjax__order__item_delivery_record
        }
    }();
</script>