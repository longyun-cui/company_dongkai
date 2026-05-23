<script>

    // window.dataTableInstances = window.dataTableInstances || {};

    function Datatable__for__Order_Dental__Appealed_List($tableId)
    {
        // var table_Id = $tableId;
        // if (window.dataTableInstances[table_Id])
        // {
        //     return window.dataTableInstances[table_Id];
        // }

        let $that = $('#'+$tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        var table = $('#'+$tableId).DataTable({
            "aLengthMenu": [[20, 50, 100, 200], ["20", "50", "100", "200"]],
            "processing": true,
            "serverSide": true,
            "searching": true,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "scrollX": true,
//                "scrollY": true,
            "scrollCollapse": true,
            "showRefresh": true,
            "ajax": {
                'url': "{{ url('/o1/order/order-appealed-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.order_category = 1;
                    d.id = $tableSearch.find('input[name="order-id"]').val();
                    d.remark = $tableSearch.find('input[name="order-remark"]').val();
                    d.description = $tableSearch.find('input[name="order-description"]').val();
                    d.delivered_date = $tableSearch.find('input[name="order-delivered_date"]').val();
                    d.assign = $tableSearch.find('input[name="order-assign"]').val();
                    d.assign_start = $tableSearch.find('input[name="order-start"]').val();
                    d.assign_ended = $tableSearch.find('input[name="order-ended"]').val();
                    d.name = $tableSearch.find('input[name="order-name"]').val();
                    d.title = $tableSearch.find('input[name="order-title"]').val();
                    d.keyword = $tableSearch.find('input[name="order-keyword"]').val();
                    d.team_list = $tableSearch.find('select[name="order-team-list[]"]').val();
                    d.staff = $tableSearch.find('select[name="order-staff"]').val();
                    d.distribute_type = $tableSearch.find('select[name="order-distribute-type"]').val();
                    d.project = $tableSearch.find('select[name="order-project"]').val();
                    d.client = $tableSearch.find('select[name="order-client"]').val();
                    d.status = $tableSearch.find('select[name="order-status"]').val();
                    d.order_type = $tableSearch.find('select[name="order-type"]').val();
                    d.client_name = $tableSearch.find('input[name="order-client-name"]').val();
                    d.client_phone = $tableSearch.find('input[name="order-client-phone"]').val();
                    d.client_type = $tableSearch.find('select[name="order-client-type"]').val();
                    d.is_wx = $tableSearch.find('select[name="order-is-wx"]').val();
                    d.is_repeat = $tableSearch.find('select[name="order-is-repeat"]').val();
                    d.created_type = $tableSearch.find('select[name="order-created-type"]').val();
                    d.recording_quality = $tableSearch.find('select[name="order-recording-quality"]').val();
                    d.inspected_status = $tableSearch.find('select[name="order-inspected-status"]').val();
                    d.inspected_result = $tableSearch.find('select[name="order-inspected-result[]"]').val();
                    d.appealed_status = $tableSearch.find('select[name="order-appealed-status"]').val();
                    d.delivered_status = $tableSearch.find('select[name="order-delivered-status"]').val();
                    d.delivered_result = $tableSearch.find('select[name="order-delivered-result[]"]').val();
                    d.location_city = $tableSearch.find('select[name="order-city"]').val();
                    d.location_district = $tableSearch.find('select[name="order-district[]"]').val();
                },
            },
            "fixedColumns": {

                @if($me->department_district_id == 0)
                "leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif",
                @else
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif",
                @endif

            },
            "columnDefs": [
                    {{--@if(!in_array($me->user_type,[0,1,11]))--}}
{{--                @if($me->department_district_id != 0)--}}
{{--                {--}}
{{--                    "targets": [0,5,6,7,8,9,10,11],--}}
{{--                    "visible": false,--}}
{{--                }--}}
{{--                @endif--}}
            ],
            "columns": [
                {
                    "title": "ID",
                    "name": "id",
                    "data": "id",
                    "className": "",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).addClass('order_id');
                            $(nTd).attr('data-id',row.id).attr('data-name','工单ID');
                            $(nTd).attr('data-key','order_id').attr('data-value',row.id);
                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return row.id;
                    }
                },
                {
                    "title": "项目",
                    "name": "project_id",
                    "data": "project_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','项目');
                            $(nTd).attr('data-key','project_id').attr('data-value',data);
                            if(row.project_er == null) $(nTd).attr('data-option-name','未指定');
                            else {
                                $(nTd).attr('data-option-name',row.project_er.name);
                            }

                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','项目');

                            if(row.project_id) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(row.project_er == null)
                        {
                            return '未指定';
                        }
                        else
                        {
                            return '<a href="javascript:void(0);">'+row.project_er.name+'</a>';
                        }
                    }
                },
                {
                    "title": "客户电话",
                    "name": "client_phone",
                    "data": "client_phone",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                        {
                            $(nTd).attr('data-row-index',iRow);

                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','客户电话');
                            $(nTd).attr('data-key','client_phone').attr('data-value',data);

                            $(nTd).attr('data-column-type','text');
                            $(nTd).attr('data-column-name','客户电话');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "所在城市",
                    "name": "location_city_district",
                    "data": "location_city",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(!(row.is_published == 1) || (row.inspected_result == "二次待审"))
                        {
                            $(nTd).addClass('modal-show-for-field-set');
                            $(nTd).attr('data-id',row.id).attr('data-name','所在城市');
                            $(nTd).attr('data-key','location_city').attr('data-value',data);
                            $(nTd).attr('data-key2','location_district').attr('data-value2',row.location_district);

                            $(nTd).attr('data-column-type','select2');
                            $(nTd).attr('data-column-name','所在城市');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        else {
                            if(!row.location_district) return data;
                            else return data+' - '+row.location_district;
                        }
                    }
                },
                {
                    "title": "创建人",
                    "name": "creator_id",
                    "data": "creator_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.creator == null ? '未知' : row.creator.name;
                    }
                },
                {
                    "title": "团队",
                    "name": "creator_team_id",
                    "data": "creator_team_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';

                        var $creator_team = row.creator_team_er == null ? '' : row.creator_team_er.name;
                        var $creator_team_group = row.creator_team_group_er == null ? '' : ' - ' + row.creator_team_group_er.name;
                        return '<a href="javascript:void(0);">'+$creator_team + $creator_team_group+'</a>';
                    }
                },
                {
                    "title": "审核人",
                    "name": "inspector_id",
                    "data": "inspector_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.inspector == null ? '未知' : row.inspector.name;
                    }
                },
                {
                    "title": "复核人",
                    "name": "appealed_handler_id",
                    "data": "appealed_handler_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return row.appealed_handler == null ? '--' : row.appealed_handler.name;
                    }
                },
                {
                    "title": "工单状态",
                    "name": "appealed_status",
                    "data": "appealed_status",
                    "className": "",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('appealed_status');
                            $(nTd).attr('data-id',row.id).attr('data-name','工单状态');
                            $(nTd).attr('data-key','order_status').attr('data-value',row.id);
                        }
                    },
                    render: function(data, type, row, meta) {

                        if(row.appealed_status == 1)
                        {
                            return '<small class="btn-xs bg-blue">申诉·申请</small>';
                        }
                        else if(row.appealed_status == 2)
                        {
                            return '<small class="btn-xs bg-orange">申诉中</small>';
                        }
                        else if(row.appealed_status == 7)
                        {
                            return '<small class="btn-xs bg-red">申诉·驳回</small>';
                        }
                        else if(row.appealed_status == 9)
                        {
                            return '<small class="btn-xs bg-aqua">申诉·结束</small>';
                        }
                        else if(row.appealed_status == 11)
                        {
                            return '<small class="btn-xs bg-green">申诉·成功</small>';
                        }
                        else if(row.appealed_status == 19)
                        {
                            return '<small class="btn-xs bg-red">申诉·失败</small>';
                        }
                        else
                        {
                            return '<small class="btn-xs bg-blue">已审核</small>';
                        }

                    }
                },
                {
                    "title": "审核结果",
                    "name": "inspected_result",
                    "data": "inspected_result",
                    "className": "text-center",
                    "width": "72px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).addClass('modal-show-for-field-set-');
                            $(nTd).attr('data-id',row.id).attr('data-name','审核结果');
                            $(nTd).attr('data-key','inspected_result').attr('data-value',data);

                            $(nTd).attr('data-column-name','审核结果');

                            if(data) $(nTd).attr('data-operate-type','edit');
                            else $(nTd).attr('data-operate-type','add');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!row.inspected_at) return '--';
                        var $result_html = '';
                        var $result_2_html = '';
                        var $inspected_result_2 = row.inspected_result_2;
                        if(data == "通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                            if($inspected_result_2 && ['一档','二档','三挡'].includes(row.inspected_result))
                            {
                                $result_2_html = '<small class="btn-xs bg-green">'+$inspected_result_2+'</small>';
                            }
                        }
                        else if(data == "折扣通过" || data == "郊区通过" || data == "内部通过")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "拒绝")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "拒绝可交付")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "不合格")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "虚假")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "超区")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "超龄")
                        {
                            $result_html = '<small class="btn-xs bg-red">拒绝</small>';
                        }
                        else if(data == "重复")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                        }
                        else if(data == "重复•可分发")
                        {
                            $result_html = '<small class="btn-xs bg-yellow">重复</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        return $result_html + $result_2_html;
                    }
                },
                {
                    "title": "拒绝类型",
                    "name": "rejected_reason",
                    "data": "rejected_reason",
                    "className": "",
                    "width": "",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $reason_list_html = '';

                        if(row.rejected_reason_arr)
                        {
                            var $reason_list = row.rejected_reason_arr;
                            $.each($reason_list, function(index, value)
                            {
                                $reason_list_html += value + '&nbsp;/&nbsp;';
                            });
                        }

                        return $reason_list_html;
                    }
                },
                {
                    "title": "操作",
                    "name": 'operation',
                    "data": 'id',
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_record = '';

                        // 记录
                        if(row.created_type != 9)
                        {
                            $html_record = '<a class="btn btn-xs modal-show--for--order--item-operation-record" data-id="'+data+'">记录</a>';
                        }


                        var $html =
                            $html_record+
                            '';
                        return $html;

                    }
                }
            ],
            "drawCallback": function (settings) {

                console.log('order-dental-list-datatable--for--CSD--execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });



                // var api = this.api();
                //
                // // 使用列名隐藏指定列
                // try {
                //     // 方法1：直接使用列名选择器
                //     api.column('id:name').visible(false);
                //     api.column('is_repeat:name').visible(false);
                //
                // } catch (e) {
                //     console.log('列名选择器错误，尝试备用方法:', e);
                // }

            },
            "language": { url: '/common/dataTableI18n' },
        });

        // window.dataTableInstances[table_Id] = table;
        //
        // return table;
    }
</script>