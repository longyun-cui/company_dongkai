<script>
    function Datatable_for_ExportList($tableId)
    {

        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 100], ["10", "50", "100"]],
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
                'url': "{{ url('/v1/operate/record/datatable-list-query?item_type=record') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="record-id"]').val();
                    d.name = $tableSearch.find('input[name="record-name"]').val();
                    d.title = $tableSearch.find('input[name="record-title"]').val();
                    d.keyword = $tableSearch.find('input[name="record-keyword"]').val();
                    d.operate_type = $tableSearch.find('select[name="record-operate-type"]').val();
                    d.staff = $tableSearch.find('select[name="record-staff"]').val();
                    d.status = $tableSearch.find('select[name="record-status"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
//                    {
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
                {
                    "className": "",
                    "width": "60px",
                    "title": "ID",
                    "data": "id",
                    "orderable": true,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-attachment');
                            $(nTd).attr('data-id',row.id).attr('data-name','附件');
                            $(nTd).attr('data-key','attachment_list').attr('data-value',row.attachment_list);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "className": "text-center",
                    "width": "80px",
                    "title": "对象",
                    "data": "operate_object",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','对象');
                            $(nTd).attr('data-key','title').attr('data-value',data);
                            $(nTd).attr('data-column-name','对象');
                            $(nTd).attr('data-text-type','text');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data == 11) return '<small class="btn-xs bg-blue">管理员</small>';
                        else if(data == 21) return '<small class="btn-xs bg-blue">员工</small>';
                        else if(data == 25) return '<small class="btn-xs bg-blue">驾驶员</small>';
                        else if(data == 31) return '<small class="btn-xs bg-green">客户</small>';
                        else if(data == 41) return '<small class="btn-xs bg-green">车辆</small>';
                        else if(data == 51) return '<small class="btn-xs bg-green">线路</small>';
                        else if(data == 61) return '<small class="btn-xs bg-green">包油油耗</small>';
                        else if(data == 71) return '<small class="btn-xs bg-yellow">工单</small>';
                        else if(data == 77) return '<small class="btn-xs bg-yellow"><i class="fa fa-refresh"></i> 环线</small>';
                        else if(data == 88) return '<small class="btn-xs bg-red">财务</small>';
                        else return data;
                    }
                },
                {
                    "className": "font-12px",
                    "width": "80px",
                    "title": "操作",
                    "data": "operate_category",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        if(data == 0) return '<small class="btn-xs bg-blue">访问</small>';
                        else if(data == 1)
                        {
                            if(row.operate_type == 1) return '<small class="btn-xs bg-olive">添加</small>';
                            else if(row.operate_type == 11) return '<small class="btn-xs bg-orange">修改</small>';
                            else return '有误';
                        }
                        else if(data == 11) return '<small class="btn-xs bg-blue">发布</small>';
                        else if(data == 21) return '<small class="btn-xs bg-green">启用</small>';
                        else if(data == 22) return '<small class="btn-xs bg-red">禁用</small>';
                        else if(data == 71)
                        {
                            if(row.operate_type == 1)
                            {
                                return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-green">添加</small>';
                            }
                            else if(row.operate_type == 91)
                            {
                                return '<small class="btn-xs bg-purple">附件</small><small class="btn-xs bg-red">删除</small>';
                            }
                            else return '';

                        }
                        else if(data == 97) return '<small class="btn-xs bg-navy">弃用</small>';
                        else if(data == 98) return '<small class="btn-xs bg-teal">复用</small>';
                        else if(data == 99) return '<small class="btn-xs bg-olive">确认</small>';
                        else if(data == 101) return '<small class="btn-xs bg-black">删除</small>';
                        else if(data == 102) return '<small class="btn-xs bg-grey">恢复</small>';
                        else if(data == 103) return '<small class="btn-xs bg-black">永久删除</small>';
                        else if(data == 109) return '<small class="btn-xs bg-blue">录单•导出</small>';
                        else if(data == 110) return '<small class="btn-xs bg-green">交付•导出</small>';
                        else if(data == 111) return '<small class="btn-xs bg-maroon">去重•导出</small>';
                        else return '有误';
                    }
                },
                {
                    "title": "导出类型",
                    "data": "operate_type",
                    "className": "font-12px",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '自定义时间';
                        else if(data == 11) return '按月';
                        else if(data == 21) return '按时间段';
                        else if(data == 31) return '按日';
                        else if(data == 99) return '最新导出';
                        else if(data == 100) return 'ID导出';
                        else if(data == 101) return '全部';
                        else return '';
                    }
                },
                {
                    "title": "导出范围",
                    "data": "title",
                    "className": " white-space-normal",
                    "width": "600px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "起始时间",
                    "data": "before",
                    "className": "font-12px",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        if(row.column_name == 'is_delay')
                        {
                            if(data == 1) return '正常';
                            else if(data == 9) return '压车';
                            else return '--';
                        }

                        if(row.column_type == 'datetime' || row.column_type == 'date')
                        {
                            if(!isNaN(data) && data.trim() !== "")
                            {
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }
                            else return data;
                        }

                        if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 91)
                        {
                            var $cdn = "{{ env('DOMAIN_CDN') }}";
                            var $src = $cdn = $cdn + "/" + data;
                            return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                        }

                        if(data == 0) return '';
                        return data;
                    }
                },
                {
                    "title": "终止时间",
                    "data": "after",
                    "className": "font-12px",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        if(row.column_type == 'datetime' || row.column_type == 'date')
                        {
                            if(!isNaN(data) && data.trim() !== "")
                            {
                                if(parseInt(data))
                                {
                                    var $date = new Date(data*1000);
                                    var $year = $date.getFullYear();
                                    var $month = ('00'+($date.getMonth()+1)).slice(-2);
                                    var $day = ('00'+($date.getDate())).slice(-2);
                                    var $hour = ('00'+$date.getHours()).slice(-2);
                                    var $minute = ('00'+$date.getMinutes()).slice(-2);
                                    var $second = ('00'+$date.getSeconds()).slice(-2);

                                    var $currentYear = new Date().getFullYear();
                                    if($year == $currentYear)
                                    {
                                        if(row.column_type == 'datetime') return $month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $month+'-'+$day;
                                    }
                                    else
                                    {
                                        if(row.column_type == 'datetime') return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                                        else if(row.column_type == 'date') return $year+'-'+$month+'-'+$day;
                                    }
                                }
                                else return '';
                            }
                            else return data;
                        }

                        if(row.column_name == 'attachment' && row.operate_category == 71 && row.operate_type == 1)
                        {
                            var $cdn = "{{ env('DOMAIN_CDN') }}";
                            var $src = $cdn = $cdn + "/" + data;
                            return '<a class="lightcase-image" data-rel="lightcase" href="'+$src+'">查看图片</a>';
                        }

                        return data;
                    }
                },
                {
                    "className": "text-center",
                    "width": "100px",
                    "title": "操作人",
                    "data": "creator_id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.true_name+'</a>';
                    }
                },
                {
                    "className": "",
                    "width": "120px",
                    "title": "操作时间",
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
                },
                {
                    "className": "text-center",
                    "width": "100px",
                    "title": "IP",
                    "data": "ip",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('export-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>