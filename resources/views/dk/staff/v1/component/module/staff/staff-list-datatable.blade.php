<script>
    function Datatable__for__Staff_List($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({
            "aLengthMenu": [[10, 50, 100, 200, -1], ["10", "50", "100", "200", "全部"]],
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
                'url': "{{ url('/o1/staff/staff-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $('input[name="staff-id"]').val();
                    d.mobile = $('input[name="staff-mobile"]').val();
                    d.username = $('input[name="staff-username"]').val();
                    d.department_district = $tableSearch.find('select[name="staff-department-district"]').val();
                    d.staff_type = $tableSearch.find('select[name="staff-type"]').val();
                    d.staff_status = $tableSearch.find('select[name="staff-status"]').val();
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
                        return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
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
                    "width": "80px",
                    "data": "item_status",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        if(row.deleted_at != null)
                        {
                            return '<i class="fa fa-times-circle text-black"></i> 已删除';
                        }

                        if(row.item_status == 1)
                        {
                            return '<i class="fa fa-circle-o text-green"></i> 正常';
                        }
                        else if(row.item_status == 99)
                        {
                            return '<i class="fa fa-lock text-orange"></i> 锁定';
                        }
                        else
                        {
                            return '<i class="fa fa-ban text-red"></i> 禁用';
                        }
                    }
                },
                {
                    "title": "登录工号",
                    "data": "login_number",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "姓名",
                    "data": "name",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+data+'</a>';
                        if(data) return data;
                        else return '--';
                    }
                },
//                 {
//                     "title": "用户名",
//                     "data": "id",
//                     "className": "",
//                     "width": "100px",
//                     "orderable": false,
//                     render: function(data, type, row, meta) {
// //                            return '<a target="_blank" href="/user/'+data+'">'+row.nickname+'</a>';
//                         if(row.username)
//                         {
//                             if(row.user_type == 88)
//                             {
//                                 return '<a class="caller-control" data-id="'+row.id+'" data-title="'+data+'">'+data+' ('+row.id+')'+'</a>';
//                             }
//                             else return row.username;
//                         }
//                         else return '--';
//                     }
//                 },
                {
                    "title": "员工类型",
                    "data": 'staff_category',
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 0) return '<small class="btn-xs bg-black">Admin</small>';
                        else if(data == 1) return '<i class="fa fa-genderless text-black"></i> 总裁';
                        else if(data == 11) return '<i class="fa fa-genderless text-red"></i> 人事';
                        else if(data == 21) return '<i class="fa fa-genderless text-orange"></i> 行政';
                        else if(data == 31) return '<i class="fa fa-genderless text-green"></i> 财务';
                        else if(data == 41) return '<i class="fa fa-genderless text-blue"></i> 客服';
                        else if(data == 51) return '<i class="fa fa-genderless text-blue"></i> 质检';
                        else if(data == 61) return '<i class="fa fa-genderless text-blue"></i> 三方';
                        else if(data == 71) return '<i class="fa fa-genderless text-blue"></i> 运营';
                        else if(data == 81) return '<i class="fa fa-genderless text-blue"></i> 业务';
                        else if(data == 88) return '<i class="fa fa-genderless text-blue"></i> 销售商务';
                        else return "有误";
                    }
                },
                {
                    "title": "员工职位",
                    "data": 'staff_position',
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs bg-black">BOSS</small>';
                        else if(data == 11) return '<i class="fa fa-gear text-red"></i> 公司老总';
                        else if(data == 31) return '<i class="fa fa-diamond text-red"></i> 部门总监';
                        else if(data == 41) return '<i class="fa fa-star text-red"></i> 团队经理';
                        else if(data == 51) return '<i class="fa fa-star-half-o text-red"></i> 分部主管';
                        else if(data == 61) return '<i class="fa fa-star-o text-red"></i> 小组组长';
                        else if(data == 71) return '<i class="fa fa-star-o text-red"></i> 小队队长';
                        else if(data == 88) return '<i class="fa fa-genderless text-red"></i> 业务员';
                        else if(data == 99) return '<i class="fa fa-genderless text-red"></i> 职员';
                        else return "有误";
                    }
                },
                {
                    "title": "公司",
                    "data": "company_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.company_er) {
                            return '<a href="javascript:void(0);" class="text-black">'+row.company_er.name+'</a>';
                        }
                        else return '--';
                    }
                },
                {
                    "title": "部门",
                    "data": "department_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.department_er) {
                            return '<a href="javascript:void(0);" class="text-black">'+row.department_er.name+'</a>';
                        }
                        else return '--';
                    }
                },
                {
                    "title": "团队",
                    "data": "team_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $return = '';
                        if(row.team_er)
                        {
                            var $team = row.team_er.name;
                            $return += $team;

                            if(row.sub_team_er)
                            {
                                var $sub_team = row.sub_team_er.name;
                                $return += ' - ' + $sub_team;
                            }

                            if(row.group_er)
                            {
                                var $group = row.group_er.name;
                                $return += ' - ' + $group;
                            }

                            return '<a href="javascript:void(0);" class="text-black">'+$return+'</a>';
                        }
                        else return '--';
                    }
                },
                {
                    "title": "创建人",
                    "data": "creator_id",
                    "className": "font-12px",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 0) return '未知';
                        if(row.creator) return '<a href="javascript:void(0);">'+row.creator.name+'</a>';
                        else return '--';
                    }
                },
                {
                    "title": "创建时间",
                    "data": 'created_at',
                    "className": "font-12px",
                    "width": "160px",
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

                        // return $year+'-'+$month+'-'+$day;
                        // return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute;
                        return $year+'-'+$month+'-'+$day+'&nbsp;&nbsp;'+$hour+':'+$minute+':'+$second;
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
                        var $html_promote = '';
                        var $html_login = '<a class="btn btn-xs staff--item-login-submit" data-id="'+data+'">登录</a>';
                        var $html_password_reset = '<a class="btn btn-xs item-password-reset-submit" data-id="'+data+'">重置密码</a>';
                        var $html_operation_record = '<a class="btn btn-xs modal-show--for--staff--item-operation-record" data-id="'+data+'">记录</a>';

                        if(row.user_category == 1)
                        {
                            $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                        }
                        else
                        {
                            $html_edit = '<a class="btn btn-xs staff--item-edit-submit" data-id="'+data+'">编辑</a>';
                        }

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs staff--item-disable-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs staff--item-enable-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs staff--item-delete-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs staff--item-restore-submit" data-id="'+data+'">恢复</a>';
                        }

                        if(row.user_type == 88)
                        {
                            $html_promote = '<a class="btn btn-xs staff--item-promote-submit" data-id="'+data+'">晋升</a>';
                        }
                        else if(row.user_type == 84)
                        {
                            $html_promote = '<a class="btn btn-xs staff--item-demote-submit" data-id="'+data+'">降职</a>';
                        }
                        else
                        {
                            $html_promote = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">晋升</a>';
                        }


                        var html =
                            '<a class="btn btn-xs modal-show--for--staff-item-edit" data-id="'+data+'">编辑</a>'+
                            $html_promote+
                            $html_password_reset+
                            $html_able+
                            $html_delete+
                            $html_operation_record+
                            $html_login+
                            // '<a class="btn btn-xs staff--item-statistic" data-id="'+data+'">统计</a>'+
                            // '<a class="btn btn-xs staff--item-login-submit" data-id="'+data+'">登录</a>'+
                            '';
                        return html;
                    }
                },
            ],
            "drawCallback": function (settings) {

                console.log('staff-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>