<script>

    // window.dataTableInstances = window.dataTableInstances || {};

    function Datatable__for__Order_Dental_Different_List($tableId)
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
                'url': "{{ url('/o1/order/order-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.order_category = 1;
                    d.order_different = 1;
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
                    },
                    render: function(data, type, row, meta) {
                        return row.id;
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
                    "title": "人工质检",
                    "name": "manual_inspected_result",
                    "data": "manual_inspected_result",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        if(data)
                        {
                            if (data.includes('通过'))
                            {
                                return '<small class="btn-xs bg-green">'+data+'</small>';
                            }
                            else if (data.includes('拒绝'))
                            {
                                return '<small class="btn-xs bg-red">'+data+'</small>';
                            }
                            else
                            {
                                return data;
                            }
                        }
                        else return '';
                    }
                },
                {
                    "title": "ai质检",
                    "name": "ai_inspected_result",
                    "data": "ai_inspected_result",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        if(data)
                        {
                            if (data.includes('通过'))
                            {
                                return '<small class="btn-xs bg-green">'+data+'</small>';
                            }
                            else if (data.includes('拒绝'))
                            {
                                return '<small class="btn-xs bg-red">'+data+'</small>';
                            }
                            else
                            {
                                return data;
                            }
                        }
                        else return '';
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
                    "title": "是否重复",
                    "name": "is_repeat",
                    "data": "is_repeat",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        if(data == 0) return '--';
                        else return '<small class="btn-xs btn-primary">是</small><small class="btn-xs btn-danger">'+(data+1)+'</small>';
                    }
                },
                {
                    "title": "客户姓名",
                    "name": "client_name",
                    "data": "client_name",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        return data;
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
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "患者类型",
                    "name": "client_type",
                    "data": "client_type",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs ">未选择</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">种植牙</small>';
                        }
                        else if(data == 2)
                        {
                            $result_html = '<small class="btn-xs bg-green">矫正</small>';
                        }
                        else if(data == 3)
                        {
                            $result_html = '<small class="btn-xs bg-red">正畸</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-black">其他</small>';
                        }
                        else
                        {
                            $result_html = '未知类型';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "客户意向",
                    "name": "client_intention",
                    "data": "client_intention",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        // if(!data) return '--';
                        // return data;
                        var $result_html = '';
                        if(data == "到店")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "A类")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "B类")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "C类")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else if(data == "A")
                        {
                            $result_html = '<small class="btn-xs bg-red">'+data+'</small>';
                        }
                        else if(data == "B")
                        {
                            $result_html = '<small class="btn-xs bg-blue">'+data+'</small>';
                        }
                        else if(data == "C")
                        {
                            $result_html = '<small class="btn-xs bg-green">'+data+'</small>';
                        }
                        else
                        {
                            $result_html = data;
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "牙齿数量",
                    "name": "teeth_count",
                    "data": "field_1",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        data = parseInt(data);
                        if(data == 1) return '单颗';
                        else if(data == 2) return '2-5颗';
                        else if(data == 3) return '6颗';
                        else if(data == 11) return '半口';
                        else if(data == 19) return '全口';
                        else if(data == 99) return '其他';
                        else return data;
                    }
                },
                {
                    "title": "拒单原因",
                    "name": "rejected_reason",
                    "data": "rejected_reason",
                    "className": "text-center",
                    "width": "180px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        return row.rejected_reason_text;
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
                        return row.creator == null ? '未知' : '<a class="caller-control" data-id="'+data+'" data-title="'+row.creator.name+'">'+row.creator.name+'</a>';
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
                    "title": "发布时间",
                    "name": 'published_at',
                    "data": 'published_at',
                    "className": "",
                    "width": "100px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    render: function(data, type, row, meta) {
//                            return data;
                        if(!data) return '';
                        var $date = new Date(data*1000);
                        var $year = $date.getFullYear();
                        var $month = ('00'+($date.getMonth()+1)).slice(-2);
                        var $day = ('00'+($date.getDate())).slice(-2);
                        var $hour = ('00'+$date.getHours()).slice(-2);
                        var $minute = ('00'+$date.getMinutes()).slice(-2);
                        var $second = ('00'+$date.getSeconds()).slice(-2);

//                            return $year+'-'+$month+'-'+$day;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
                    }
                },
                {
                    "title": "创建时间",
                    "name": 'created_at',
                    "data": 'created_at',
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    "orderSequence": ["desc", "asc"],
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
//                            return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute;
//                            return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;

                        var $currentYear = new Date().getFullYear();
                        if($year == $currentYear) return $month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                        else return $year+'-'+$month+'-'+$day+'&nbsp;'+$hour+':'+$minute+':'+$second;
                    }
                },
                {
                    "title": "操作",
                    "name": 'operation',
                    "data": 'id',
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_detail = '';
                        var $html_inspect = '';
                        var $html_record = '';

                        // 记录
                        if(row.created_type != 9)
                        {
                            $html_record = '<a class="btn btn-xs modal-show--for--order--item-operation-record" data-id="'+data+'">记录</a>';
                        }

                        // 已发布
                        if(row.is_published > 0)
                        {

                            // 详情编辑
                            var $role = '';
                            if(window.staffRole == 'director') $role = 'admin';
                            $html_detail = '<a class="btn btn-xs modal-show--for--order--item-detail-editing" data-role="'+$role+'" data-id="'+data+'">详情</a>';

                        }

                        $html_inspect = '<a class="btn btn-xs modal-show--for--order--item-inspecting" data-id="'+data+'">审核</a>';


                        var $html =
                            $html_inspect+
                            $html_detail+
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