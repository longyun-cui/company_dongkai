<script>
    var Datatable__for__Order_Item_Fee_Record_List = function ($id) {
        var datatableAjax_order_item_fee_record = function ($id) {

            var dt_order_item_fee_record = $('#datatable--for--order-item-fee-record-list');
            if($.fn.DataTable.isDataTable(dt_order_item_fee_record))
            {
                // 已经初始化
                console.log('#datatable--for--order-item-fee-record-list // 已经初始化');
                $(dt_order_item_fee_record).DataTable().destroy();
                dt_order_item_fee_record.DataTable().destroy();
            }
            else
            {
                // 未初始化
                console.log('#datatable--for--order-item-fee-record-list // 未初始化');
            }

            var ajax_datatable_order_item_fee_record = dt_order_item_fee_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[50], ["50"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/o1/order/item-fee-record-list/datatable-query?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $('input[name="order-fee-name"]').val();
                        d.title = $('input[name="order-fee-title"]').val();
                        d.keyword = $('input[name="order-fee-keyword"]').val();
                        d.type = $('select[name="order-fee-type"]').val();
                        d.status = $('select[name="order-fee-status"]').val();
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
                        "data": "fee_type",
                        "className": "",
                        "width": "100px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(data == 1) return '<small class="btn-xs bg-green">收入</small>';
                            else if(data == 99) return '<small class="btn-xs bg-red">费用</small>';
                            else if(data == 101) return '<small class="btn-xs bg-orange">订单扣款</small>';
                            else if(data == 111) return '<small class="btn-xs bg-yellow">司机罚款</small>';
                            else return '有误';
                        }
                    },
                    {
                        "title": "时间",
                        "data": "fee_datetime",
                        "className": "",
                        "width": "160px",
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
                        "title": "名目",
                        "data": "fee_title",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "金额",
                        "data": "fee_amount",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "说明",
                        "data": "fee_description",
                        "className": "",
                        "width": "300px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
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
            init: datatableAjax_order_item_fee_record
        }
    }();
</script>