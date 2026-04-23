<script>
    var Datatable__for__Order_Item_AI_Record_List = function ($datatable_id,$id,$type) {
        var datatableAjax__order_item_ai_record = function ($datatable_id,$id) {

            // var dt__order_item_operation_record = $('#datatable--for--order--item-operation-record-list');
            var dt__order_item_operation_record = $('#'+$datatable_id);
            if($.fn.DataTable.isDataTable(dt__order_item_operation_record))
            {
                // 已经初始化
                console.log('#datatable--for--order--item-operation-record-list // 已经初始化');
                $(dt__order_item_operation_record).DataTable().destroy();
            }
            else
            {
                // 未初始化
                console.log('#datatable--for--order--item-ai-record-list // 未初始化');
            }

            let $that = $('#'+$datatable_id);
            let $datatable_wrapper = $that.parents('.datatable-wrapper');
            let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

            var ajax_datatable_order_operation_record = dt__order_item_operation_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1], ["全部"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': "/o1/order/item-ai-record-list/datatable-query?id="+$id,
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                        d.name = $tableSearch.find('input[name="order-operation-name"]').val();
                        d.title = $tableSearch.find('input[name="order-operation-title"]').val();
                        d.keyword = $tableSearch.find('input[name="order-operation-keyword"]').val();
                        d.type = $tableSearch.find('select[name="order-operation-type"]').val();
                        d.status = $tableSearch.find('select[name="order-operation-status"]').val();
                    },
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
                        "title": "平台",
                        "data": "ai_platform",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "模型",
                        "data": "ai_model",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "消耗Token",
                        "data": "usage",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '--';

                            var $return_html = '';
                            $return_html += '【全部消耗】' + data.total_tokens + ' <br>';
                            $return_html += '【提示总耗】' + data.prompt_tokens + ' <br>';
                            $return_html += '【音频消耗】' + data.prompt_tokens_details.audio_tokens + ' <br>';
                            $return_html += '【文本消耗】' + data.prompt_tokens_details.text_tokens + ' <br>';
                            $return_html += '【返回消耗】' + data.completion_tokens + ' <br>';
                            // $.each(data, function($index, $value) {
                            //     $return_html += '【'+ $index +'】' + $value + ' <br>';
                            // });
                            return $return_html;
                        }
                    },
                    {
                        "title": "耗时(s)",
                        "data": "ai_used_time",
                        "className": "text-left",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            var $return_html = '';
                            $return_html += '【程序】' + row.program_used_time + ' <br>';
                            $return_html += '【质检】' + data + ' <br>';
                            return $return_html;
                        }
                    },
                    {
                        "className": "text-left",
                        "width": "480px",
                        "title": "详情",
                        "data": "content",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '--';

                            var $return_html = '';
                            $.each(data, function($index, $value) {
                                $return_html += '【'+ $index +'】' + $value + ' <br>';
                            });
                            return $return_html;
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

                },
                "language": { url: '/common/dataTableI18n' },
            });


        };
        return {
            init: datatableAjax__order_item_ai_record
        }
    }();
</script>