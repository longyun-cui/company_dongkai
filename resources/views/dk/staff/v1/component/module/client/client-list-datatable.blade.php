<script>
    function Datatable__for__Client_List($tableId)
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
                'url': "{{ url('/o1/client/client-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="client-id"]').val();
                    d.name = $tableSearch.find('input[name="client-name"]').val();
                    d.username = $tableSearch.find('input[name="client-username"]').val();
                    d.title = $tableSearch.find('input[name="client-title"]').val();
                    d.keyword = $tableSearch.find('input[name="client-keyword"]').val();
                    d.item_status = $tableSearch.find('select[name="client-item-status"]').val();
                    d.client_type = $tableSearch.find('select[name="client-type"]').val();
                    d.client_work_status = $tableSearch.find('select[name="client-work-status"]').val();
                    d.company = $tableSearch.find('select[name="client-company"]').val();
                    d.channel = $tableSearch.find('select[name="client-channel"]').val();
                    d.business = $tableSearch.find('select[name="client-business"]').val();
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
                {
                    "title": "ID",
                    "data": "id",
                    "className": "font-12px",
                    "width": "60px",
                    "orderable": true,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "状态",
                    "data": "item_status",
                    "width": "80px",
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
                    "data": "client_category",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1)
                        {
                            return '<i class="fa fa-clock-o text-green"></i> 长期';
                        }
                        if(data == 11)
                        {
                            return '<i class="fa fa-clock-o text-yellow"></i> 短期</small>';
                        }
                        if(data == 31)
                        {
                            return '<i class="fa fa-clock-o text-grey"></i> 临时</small>';
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
                    "className": "client-name",
                    "width": "160px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);" class="text-black">'+data+'</a>';
                    }
                },
{{--                @if(in_array($me->staff_type,[0,1,11,19]))--}}
{{--                {--}}
{{--                    "title": "合作单价",--}}
{{--                    "data": "cooperative_unit_price",--}}
{{--                    "className": "",--}}
{{--                    "width": "80px",--}}
{{--                    "orderable": false,--}}
{{--                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {--}}
{{--                        if(row.is_completed != 1 && row.item_status != 97)--}}
{{--                        {--}}
{{--                            $(nTd).addClass('modal-show-for-info-text-set');--}}
{{--                            $(nTd).attr('data-id',row.id).attr('data-name','合作单价');--}}
{{--                            $(nTd).attr('data-key','cooperative_unit_price').attr('data-value',data);--}}
{{--                            $(nTd).attr('data-column-name','合作单价');--}}
{{--                            $(nTd).attr('data-text-type','text');--}}
{{--                            if(data) $(nTd).attr('data-operate-type','edit');--}}
{{--                            else $(nTd).attr('data-operate-type','add');--}}
{{--                        }--}}
{{--                    },--}}
{{--                    render: function(data, type, row, meta) {--}}
{{--                        return parseFloat(data);--}}
{{--                    }--}}
{{--                },--}}
{{--                {--}}
{{--                    "title": "累计充值",--}}
{{--                    "data": "funds_recharge_total",--}}
{{--                    "className": "item-show-for-recharge",--}}
{{--                    "width": "80px",--}}
{{--                    "orderable": false,--}}
{{--                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {--}}
{{--                        if(true)--}}
{{--                        {--}}
{{--                            $(nTd).attr('data-id',row.id).attr('data-name','累充金额');--}}
{{--                            $(nTd).attr('data-key','funds_recharge_total').attr('data-value',data);--}}
{{--                            $(nTd).attr('data-column-name','累充金额');--}}
{{--                        }--}}
{{--                    },--}}
{{--                    render: function(data, type, row, meta) {--}}
{{--                        return parseFloat(data);--}}
{{--                    }--}}
{{--                },--}}
{{--                @endif--}}
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
                    "className": "font-12px",
                    "width": "80px",
                    "title": "创建人",
                    "data": "creator_id",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.creator) return row.creator.username;
                        else return "--";
                    }
                },
                {
                    "title": "创建时间",
                    "data": 'created_at',
                    "className": "font-12px",
                    "width": "120px",
                    "orderable": true,
                    render: function(data, type, row, meta) {
//                            return data;
//                            newDate = new Date();
//                            newDate.setTime(data * 1000);
//                            return newDate.toLocaleString('chinese',{hour12:false});
//                            return newDate.toLocaleDateString();

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

                        return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                    }
                },
                {
                    "title": "操作",
                    "data": "id",
                    "width": "240px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_edit = '';
                        var $html_detail = '';
                        var $html_able = '';
                        var $html_delete = '';
                        var $html_operation_record = '<a class="btn btn-xs bg-default modal-show--for--client--item-operation-record" data-id="'+data+'">记录</a>';

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs client--item-disable-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs client--item-enable-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs client--item-delete-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs client--item-restore-submit" data-id="'+data+'">恢复</a>';
                        }

                        var html =
                            '<a class="btn btn-xs modal-show--for--client-item-edit" data-id="'+data+'">编辑</a>'+
                            $html_able+
                            $html_delete+
                            $html_operation_record+
                            // '<a class="btn btn-xs client--item-statistic" data-id="'+data+'">统计</a>'+
                            // '<a class="btn btn-xs client--item-login-submit" data-id="'+data+'">登录</a>'+
                            '';
                        return html;
                    }
                },
            ],
            "drawCallback": function (settings) {

                console.log('client-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>