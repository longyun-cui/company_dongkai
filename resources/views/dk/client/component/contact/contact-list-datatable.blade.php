<script>

    function Datatable_for_ContactList($tableId)
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
                'url': "{{ url('/v1/operate/contact/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="contact-id"]').val();
                    d.name = $tableSearch.find('input[name="contact-name"]').val();
                    d.title = $tableSearch.find('input[name="contact-title"]').val();
                    d.keyword = $tableSearch.find('input[name="contact-keyword"]').val();
                    d.item_status = $tableSearch.find('select[name="contact-status"]').val();
                    d.contact_type = $tableSearch.find('select[name="v-type"]').val();
                    d.contact_work_status = $tableSearch.find('select[name="contact-work-status"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" class="check-review-all">',
                    "width": "40px",
                    "data": "id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
                    }
                },
//                    {
//                        "width": "40px",
//                        "title": "序号",
//                        "data": null,
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
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-attachment-');
                            $(nTd).attr('data-id',row.id).attr('data-name','附件');
                            $(nTd).attr('data-key','attachment_list').attr('data-value','');
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
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
                        var $html_record = '';
                        var $html_able = '';
                        var $html_delete = '';
                        var $html_publish = '';
                        var $html_abandon = '';

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs btn-danger- item-disable-by-admin-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs btn-success- item-enable-by-admin-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-by-admin-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs bg-grey- item-restore-by-admin-submit" data-id="'+data+'">恢复</a>';
                        }

                        $html_record = '<a class="btn btn-xs bg-purple- item-modal-show-for-modify" data-id="'+data+'">记录</a>';

                        var html =
                            // '<a class="btn btn-xs btn-primary item-edit-link" data-id="'+data+'">编辑</a>'+
                            '<a class="btn btn-xs btn-primary- contact-edit-show" data-id="'+data+'">编辑</a>'+
                            $html_able+
                            // '<a class="btn btn-xs" href="/item/edit?id='+data+'">编辑</a>'+
                            // $html_publish+
                            $html_delete+
                            // $html_record+
                            // '<a class="btn btn-xs bg-navy item-admin-delete-permanently-submit" data-id="'+data+'">彻底删除</a>'+
                            // '<a class="btn btn-xs bg-primary item-detail-show" data-id="'+data+'">查看详情</a>'+
                            // '<a class="btn btn-xs bg-olive item-download-qr-code-submit" data-id="'+data+'">下载二维码</a>'+
                            '';
                        return html;

                    }
                },
                {
                    "title": "状态",
                    "data": "item_status",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        if(row.deleted_at != null)
                        {
                            return '<small class="btn-xs bg-black">已删除</small>';
                        }

                        if(data == 1)
                        {
                            return '<small class="btn-xs btn-success">启用</small>';
                        }
                        else
                        {
                            return '<small class="btn-xs btn-danger">禁用</small>';
                        }
                    }
                },
                {
                    "title": "类型",
                    "data": 'contact_type',
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs bg-green">微信</small>';
                        else return "有误";
                    }
                },
                {
                    "title": "名称",
                    "data": "name",
                    "className": "text-center",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);">'+data+'</a>';
                    }
                },
                {
                    "title": "负责人",
                    "data": "client_staff_id",
                    "className": "text-center",
                    "width": "160px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('modal-show-for-info-select2-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','负责人');
                            $(nTd).attr('data-key','client_staff_id').attr('data-value',data);
                            if(row.client_staff_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                $(nTd).attr('data-option-name',row.client_staff_er.username);
                            }
                            $(nTd).attr('data-column-name','负责人');
                            if(row.client_staff_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.client_staff_er == null) return '--';
                        else return '<a href="javascript:void(0);">'+row.client_staff_er.username+'</a>';
                    }
                },
                {
                    "title": "备注",
                    "data": "remark",
                    "className": "text-center",
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
                    "className": "text-center",
                    "width": "80px",
                    "title": "创建者",
                    "data": "creator_id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : '<a href="javascript:void(0);">'+row.creator.username+'</a>';
                    }
                },
                {
                    "className": "font-12px",
                    "width": "120px",
                    "title": "创建时间",
                    "data": 'created_at',
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
                    "className": "font-12px",
                    "width": "120px",
                    "title": "更新时间",
                    "data": 'updated_at',
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

                console.log('contact-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>