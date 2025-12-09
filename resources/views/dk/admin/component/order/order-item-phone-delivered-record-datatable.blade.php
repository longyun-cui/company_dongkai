<script>
    var Datatable_Order_Item_Phone_Delivered_Record = function ($id,$datatable_id) {
        var datatableAjax_Item_Phone_Delivered_Record = function ($id,$datatable_id) {

            var dt_order_item_phone_delivered_record = $('#'+$datatable_id);
            dt_order_item_phone_delivered_record.DataTable().destroy();
            var ajax_datatable_order_item_phone_delivered_record = dt_order_item_phone_delivered_record.DataTable({
                "retrieve": true,
                "destroy": true,
//                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "aLengthMenu": [[-1, 20, 50], ["全部", "20", "50"]],
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "searching": false,
                "pagingType": "simple_numbers",
                "sDom": '<t>',
                "order": [],
                "orderCellsTop": true,
                "ajax": {
                    'url': "/v1/operate/order/item-phone-delivery-record/datatable-list-query?id="+$id,
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
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "font-12px",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                    {
                        "className": "font-12px",
                        "width": "60px",
                        "title": "ID",
                        "data": "id",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        "title": "来源",
                        "name": "created_type",
                        "data": "created_type",
                        "className": "",
                        "width": "60px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            if(!data) return '--';
                            var $result_html = '';
                            if(data == 1)
                            {
                                $result_html = '<small class="btn-xs bg-green">人工</small>';
                            }
                            else if(data == 91)
                            {
                                $result_html = '<small class="btn-xs bg-red">百应AI</small>';
                            }
                            else if(data == 99)
                            {
                                $result_html = '<small class="btn-xs bg-red">API</small>';
                            }
                            else if(data == 9)
                            {
                                $result_html = '<small class="btn-xs bg-yellow">导入</small>';
                            }
                            else
                            {
                                $result_html = '<small class="btn-xs bg-black">有误</small>';
                            }
                            return $result_html;
                        }
                    },
                    {
                        "title": "交付结果",
                        "name": "delivered_result",
                        "data": "delivered_result",
                        "className": "text-center",
                        "width": "72px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(row.is_completed != 1 && row.item_status != 97)
                            {
                                $(nTd).addClass('modal-show-for-field-set-');
                                $(nTd).attr('data-id',row.id).attr('data-name','交付结果');
                                $(nTd).attr('data-key','delivered_result').attr('data-value',data);

                                $(nTd).attr('data-column-type','select');
                                $(nTd).attr('data-column-name','审核结果');

                                if(data) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if(!row.delivered_at) return '--';
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
                        "title": "项目",
                        "name": "project_id",
                        "data": "project_id",
                        "className": "",
                        "width": "160px",
                        "orderable": false,
                        "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                            if(!("{{ in_array($me->user_type,[84,88]) }}" && row.is_published == 1) || ("{{ in_array($me->user_type,[84,88]) }}" && row.inspected_result == "二次待审"))
                            {
                                $(nTd).attr('data-row-index',iRow);

                                $(nTd).addClass('modal-show-for-field-set');
                                $(nTd).attr('data-id',row.id).attr('data-name','项目');
                                $(nTd).attr('data-key','project_id').attr('data-value',data);
                                if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                                else {
                                    if(row.project_er.alias_name)
                                    {
                                        $(nTd).attr('data-option-name',row.project_er.name+' ('+row.project_er.alias_name+')');
                                    }
                                    else
                                    {
                                        $(nTd).attr('data-option-name',row.project_er.name);
                                    }
                                }

                                $(nTd).attr('data-column-type','select2');
                                $(nTd).attr('data-column-name','项目');

                                if(row.project_id) $(nTd).attr('data-operate-type','edit');
                                else $(nTd).attr('data-operate-type','add');
                            }
                        },
                        render: function(data, type, row, meta) {
                            if("{{ in_array($me->user_type,[0,1,11,61,66]) }}")
                            {
                                if(row.project_er == null)
                                {
                                    return '未指定';
                                }
                                else
                                {
                                    if(row.project_er.alias_name)
                                    {
                                        return '<a href="javascript:void(0);">'+row.project_er.name+' ('+row.project_er.alias_name+')'+'</a>';
                                    }
                                    else
                                    {
                                        return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                                    }
                                }
                            }
                            else
                            {
                                if(row.project_er == null)
                                {
                                    return '未指定';
                                }
                                else
                                {
                                    return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                                }
                            }
                        }
                    },
                    {
                        "title": "操作人",
                        "data": "creator_id",
                        "className": "",
                        "width": "120px",
                        "orderable": false,
                        render: function(data, type, row, meta) {
                            @if(in_array($me->user_type,[81,84,88]))
                                return row.creator == null ? '--' : '****';
                            @else
                                return row.creator == null ? '--' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
                            @endif
                        }
                    },
                    {
                        "title": "操作时间",
                        "data": "created_at",
                        "className": "",
                        "width": "120px",
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
            init: datatableAjax_Item_Phone_Delivered_Record
        }
    }();
</script>