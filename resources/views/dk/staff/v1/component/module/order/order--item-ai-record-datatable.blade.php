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
                        "title": "Token",
                        "data": "token_total",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
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

                            var $return_html = '';
                            // $.each(data, function($index, $value) {
                            //     $return_html += '【'+ $index +'】' + $value + ' <br>';
                            // });
                            var $city_html = '【城市】' + data.city + ' - ' + data.district + ' <br>';
                            var $teeth_count_html = '【牙齿数量】' + data.teeth_count + ' <br>';
                            var $willingness_html = '';
                            if(data.willingness == true) $willingness_html = '【上门意愿】是';
                            else if(data.willingness == false) $willingness_html = '【上门意愿】否';
                            else $willingness_html = '【上门意愿】' + data.willingness;

                            return $city_html + $teeth_count_html + $willingness_html;
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

                },
                "language": { url: '/common/dataTableI18n' },
            });


        };
        return {
            init: datatableAjax__order_item_ai_record
        }
    }();
</script>