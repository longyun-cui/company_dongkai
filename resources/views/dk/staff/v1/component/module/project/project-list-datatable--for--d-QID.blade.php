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
            "scrollX": true,
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
                    d.is_automatic = $tableSearch.find('select[name="project-is-automatic"]').val();
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
                    "title": "类型",
                    "data": "project_category",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<small class="btn-xs bg-orange">口腔</small>';
                        }
                        if(data == 11)
                        {
                            return '<small class="btn-xs bg-red">医美</small>';
                        }
                        if(data == 31)
                        {
                            return '<small class="btn-xs bg-purple">二奢</small>';
                        }
                        else
                        {
                            return '未知类型';
                        }
                    }
                },
                {
                    "title": "项目名称",
                    "data": "name",
                    "className": "text-center",
                    "width": "160px",
                    "orderable": true,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');

                            $(nTd).attr('data-id',row.id).attr('data-name','项目名称');
                            $(nTd).attr('data-key','name').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','项目名称');

                            if(row.client_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "团队",
                    "data": "pivot__project_team",
                    "className": "text-center white-space-normal",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        var html = '';
                        $.each(data,function( key, val ) {
//                                console.log( key, val, this );
                            html += '<a href="javascript:void(0);">'+this.name+'</a> &nbsp; <br>';
                        });
                        return html;
                    }
                },
                {
                    "title": "专属员工",
                    "data": "pivot__project_department__qid",
                    "className": "text-center white-space-normal",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        var html = '';
                        $.each(data,function( key, val ) {
//                                console.log( key, val, this );
                            html += '<a href="javascript:void(0);">'+this.name+'</a> &nbsp; <br>';
                        });
                        return html;
                    }
                },
                // {
                //     "title": "审核要求",
                //     "data": "ai_prompt",
                //     "className": "",
                //     "width": "500px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.is_completed != 1 && row.item_status != 97)
                //         {
                //             $(nTd).addClass('modal-show-for-info-text-set');
                //             $(nTd).attr('data-id',row.id).attr('data-name','AI审核');
                //             $(nTd).attr('data-key','ai_prompt').attr('data-value',data);
                //             $(nTd).attr('data-column-name','AI审核');
                //             $(nTd).attr('data-text-type','textarea');
                //             if(data) $(nTd).attr('data-operate-type','edit');
                //             else $(nTd).attr('data-operate-type','add');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //         // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                //         // else return '';
                //     }
                // },
                {
                    "title": "备注",
                    "data": "description",
                    "className": "",
                    "width": "200px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-text-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','备注');
                            $(nTd).attr('data-key','description').attr('data-value',data);
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