<script>
    function Datatable__for__Project_List($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 200, 500], ["10", "50", "200", "500"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": false,
//                "scrollY": true,
            "scrollCollapse": true,
            "ajax": {
                'url': "{{ url('/o1/project/project-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="project-id"]').val();
                    d.name = $tableSearch.find('input[name="project-name"]').val();
                    d.title = $tableSearch.find('input[name="project-title"]').val();
                    d.keyword = $tableSearch.find('input[name="project-keyword"]').val();
                    d.item_status = $tableSearch.find('select[name="project-item-status"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    "width": "60px",
                    "data": "id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<input type="checkbox" name="bulk-id" class="minimal" value="'+data+'">';
                    }
                },
//                    {
//                        "title": "序号",
//                        "data": null,
//                        "width": "40px",
//                        "targets": 0,
//                        'orderable': false
//                    },
                {
                    "title": "ID",
                    "data": "id",
                    "className": "",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "状态",
                    "data": "item_status",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.deleted_at != null)
                        {
                            return '<i class="fa fa-times-circle text-black"></i> 已删除';
                        }

                        if(data == 1)
                        {
                            return '<i class="fa fa-circle-o text-green"></i> 正常';
                        }
                        else
                        {
                            return '<i class="fa fa-ban text-red"></i> 禁用';
                        }
                    }
                },
                {
                    "title": "类型",
                    "data": "project_category",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<i class="fa fa-clock-o text-green"></i> 长期';
                        }
                        if(data == 11)
                        {
                            return '<i class="fa fa-clock-o text-blue"></i> 短期</small>';
                        }
                        if(data == 91)
                        {
                            return '<i class="fa fa-clock-o text-yellow"></i> 临时</small>';
                        }
                        else
                        {
                            return '未知类型';
                        }
                    }
                },
                {
                    "title": "名称",
                    "data": "name",
                    "className": "",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','项目名称');

                            $(nTd).attr('data-id',row.id).attr('data-name','项目名称');
                            $(nTd).attr('data-key','name').attr('data-value',data);


                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "客户",
                    "data": "client_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','客户');

                            $(nTd).attr('data-id',row.id).attr('data-name','客户');
                            $(nTd).attr('data-key','client_id').attr('data-value',data);
                            if(row.client_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                $(nTd).attr('data-option-name',row.client_er.name);
                            }

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.client_er == null) return '--';
                        else return '<a href="javascript:void(0);" class="text-black">'+row.client_er.name+' </a>';
                    }
                },
                {
                    "title": "账期",
                    "data": "settlement_period",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','radio');
                            $(nTd).attr('data-column-name','账期');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','运费');
                            $(nTd).attr('data-key','freight_amount');
                            $(nTd).attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<i class="fa fa-clock-o text-red"></i> 单次结算';
                        }
                        if(data == 3)
                        {
                            return '<i class="fa fa-clock-o text-yellow"></i> 多次结算</small>';
                        }
                        if(data == 7)
                        {
                            return '<i class="fa fa-clock-o text-blue"></i> 周结</small>';
                        }
                        if(data == 31)
                        {
                            return '<i class="fa fa-clock-o text-green"></i> 月结</small>';
                        }
                        else
                        {
                            return '有误';
                        }
                    }
                },
                {
                    "title": "运费",
                    "data": "freight_amount",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','运费');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','运费');
                            $(nTd).attr('data-key','freight_amount');
                            $(nTd).attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "出发地",
                    "data": "transport_departure_place",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','出发地');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','出发地');
                            $(nTd).attr('data-key','transport_departure_place');
                            $(nTd).attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "目的地",
                    "data": "transport_destination_place",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','目的地');

                            $(nTd).attr('data-id',row.id);
                            $(nTd).attr('data-name','目的地');
                            $(nTd).attr('data-key','transport_destination_place');
                            $(nTd).attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "距离(km)",
                    "data": "transport_distance",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','距离');

                            $(nTd).attr('data-id',row.id).attr('data-name','距离');
                            $(nTd).attr('data-key','transport_distance').attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "时效(H)",
                    "data": "transport_time_limitation",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show--for--project-field-set');
                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','时效');

                            $(nTd).attr('data-id',row.id).attr('data-name','时效');
                            $(nTd).attr('data-key','transport_time_limitation').attr('data-value',data);

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return convertMinutesToHoursAndMinutes(data);
                    }
                },
                {
                    "title": "备注",
                    "data": "remark",
                    "className": "",
                    "width": "",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','备注');
                            $(nTd).attr('data-key','remark').attr('data-value',data);
                            $(nTd).attr('data-column-name','备注');
                            $(nTd).attr('data-text-type','textarea');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                        // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                        // else return '';
                    }
                },
                {
                    "title": "创建者",
                    "data": "creator_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a target="_blank" href="/user/'+row.creator.id+'">'+row.creator.username+'</a>';
                    }
                },
                {
                    "title": "更新时间",
                    "data": 'updated_at',
                    "className": "font-12px",
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
                },
                {
                    "title": "操作",
                    "data": 'id',
                    "width": "160px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_able = '';
                        var $html_delete = '';
                        var $html_operation_record = '<a class="btn btn-xs modal-show--for--project--item-operation-record" data-id="'+data+'">记录</a>';

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs project--item-disable-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs project--item-enable-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs project--item-delete-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs project--item-restore-submit" data-id="'+data+'">恢复</a>';
                        }

                        var html =
                            '<a class="btn btn-xs modal-show--for--project-item-edit" data-id="'+data+'">编辑</a>'+
                            $html_able+
                            $html_delete+
                            $html_operation_record+
                            // '<a class="btn btn-xs project--item-statistic" data-id="'+data+'">统计</a>'+
                            // '<a class="btn btn-xs project--item-login-submit" data-id="'+data+'">登录</a>'+
                            '';
                        return html;

                    }
                },
            ],
            "drawCallback": function (settings) {

                console.log('project-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>