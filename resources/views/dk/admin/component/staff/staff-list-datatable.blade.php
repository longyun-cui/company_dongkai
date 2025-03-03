<script>
    function Datatable_for_StaffList($tableId)
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
                'url': "{{ url('/v1/operate/staff/datatable-list-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $('input[name="staff-id"]').val();
                    d.mobile = $('input[name="staff-mobile"]').val();
                    d.username = $('input[name="staff-username"]').val();
                    d.department_district = $tableSearch.find('select[name="staff-department-district"]').val();
                    d.user_type = $tableSearch.find('select[name="staff-user-type"]').val();
                    d.user_status = $tableSearch.find('select[name="staff-user-status"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
                {
                    "title": '<input type="checkbox" id="check-review-all">',
                    "width": "40px",
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
                    "title": "操作",
                    "data": "id",
                    "width": "240px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.user_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs btn-danger- item-disable-by-admin-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs btn-success- item-enable-by-admin-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.user_category == 1)
                        {
                            $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                        }
                        else
                        {
                            $html_edit = '<a class="btn btn-xs btn-primary- item-admin-edit-submit" data-id="'+data+'">编辑</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs bg-black- item-delete-by-admin-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs bg-grey- item-restore-by-admin-submit" data-id="'+data+'">恢复</a>';
                        }

                        if(row.user_type == 88)
                        {
                            $html_promote = '<a class="btn btn-xs bg-olive- item-promote-by-admin-submit" data-id="'+data+'">晋升</a>';
                        }
                        else if(row.user_type == 84)
                        {
                            $html_promote = '<a class="btn btn-xs bg-blue- item-demote-by-admin-submit" data-id="'+data+'">降职</a>';
                        }
                        else
                        {
                            $html_promote = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">晋升</a>';
                        }


                        var html =
                            // $html_edit+
                            '<a class="btn btn-xs btn-primary- staff-edit-show" data-id="'+data+'">编辑</a>'+
                            // '<a class="btn btn-xs bg-maroon item-password-admin-change-show" data-id="'+data+'" data-name="'+row.username+'">修改密码</a>'+
                            '<a class="btn btn-xs bg-maroon- item-password-reset-by-admin-submit" data-id="'+data+'">重置密码</a>'+
                            $html_promote+
                            $html_able+
                            $html_delete+
                            // '<a class="btn btn-xs bg-olive item-login-submit" data-id="'+data+'">登录</a>'+
                            // '<a class="btn btn-xs bg-purple item-statistic-link" data-id="'+data+'">统计</a>'+
                            '';
                        return html;
                    }
                },
                {
                    "title": "状态",
                    "width": "80px",
                    "data": "user_status",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return data;
                        if(row.deleted_at != null)
                        {
                            return '<small class="btn-xs bg-black">已删除</small>';
                        }

                        if(row.user_status == 1)
                        {
                            return '<small class="btn-xs btn-success">正常</small>';
                        }
                        else if(row.user_status == 99)
                        {
                            return '<small class="btn-xs btn-warning">锁定('+row.login_error_num+')</small>';
                        }
                        else
                        {
                            return '<small class="btn-xs btn-danger">禁用</small>';
                        }
                    }
                },
                {
                    "title": "员工职位",
                    "data": 'user_type',
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 1) return '<small class="btn-xs bg-black">BOSS</small>';
                        else if(data == 11) return '<small class="btn-xs btn-danger">总经理</small>';
                        else if(data == 21) return '<small class="btn-xs bg-purple">人事经理</small>';
                        else if(data == 22) return '<small class="btn-xs bg-purple">人事</small>';
                        else if(data == 31) return '<small class="btn-xs bg-orange">财务经理</small>';
                        else if(data == 33) return '<small class="btn-xs bg-orange">财务</small>';
                        else if(data == 41) return '<small class="btn-xs bg-purple">团队·总经理</small>';
                        else if(data == 71) return '<small class="btn-xs bg-purple">质检</small><small class="btn-xs btn-danger">经理</small>';
                        else if(data == 77) return '<small class="btn-xs bg-purple">质检员</small>';
                        else if(data == 81) return '<small class="btn-xs bg-olive">客服</small><small class="btn-xs btn-danger">经理</small>';
                        else if(data == 84) return '<small class="btn-xs bg-olive">客服</small><small class="btn-xs bg-olive">主管</small>';
                        else if(data == 88) return '<small class="btn-xs bg-olive">客服</small>';
                        else if(data == 61) return '<small class="btn-xs bg-blue">运营</small><small class="btn-xs btn-danger">经理</small>';
                        else if(data == 66) return '<small class="btn-xs bg-blue">运营人员</small>';
                        else return "有误";
                    }
                },
                {
                    "title": "登录工号",
                    "data": "mobile",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "真实姓名",
                    "data": "id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+row.true_name+'</a>';
                        if(row.true_name) return row.true_name;
                        else return '--';
                    }
                },
                {
                    "title": "用户名",
                    "data": "id",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
//                            return '<a target="_blank" href="/user/'+data+'">'+row.nickname+'</a>';
                        if(row.username) return row.username;
                        else return '--';
                    }
                },
                {
                    "title": "大区",
                    "data": "department_district_id",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.department_district_er) {
                            return '<a href="javascript:void(0);">'+row.department_district_er.name+'</a>';
                        }
                        else return '--';
                    }
                },
                {
                    "title": "小组",
                    "data": "department_group_id",
                    "className": "",
                    "width":"120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.department_group_er) {
                            return '<a href="javascript:void(0);">'+row.department_group_er.name+'</a>';
                        }
                        else return '--';
                    }
                },
                // {
                //    "title": "上级领导",
                //    "data": "id",
                //    "className": "",
                //    "width":"100px",
                //    "orderable": false,
                //    render: function(data, type, row, meta) {
                //        if(row.superior) {
                //            return '<a href="javascript:void(0);">'+row.superior.true_name+'</a>';
                //        }
                //        else return '--';
                //    }
                // },

                // {
                //     "title": "API服务器",
                //     "data": "api_serverFrom",
                //     "className": "",
                //     "width": "100px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return '<a href="javascript:void(0);">'+data+'</a>';
                //     }
                // },
                {
                    "title": "API坐席ID",
                    "data": "api_staffNo",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return '<a href="javascript:void(0);">'+data+'</a>';
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
                    "title": "创建人",
                    "data": "creator_id",
                    "className": "font-12px",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data == 0) return '未知';
                        // return row.creator.true_name;
                        if(row.creator) return '<a href="javascript:void(0);">'+row.creator.username+'</a>';
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
                }
            ],
            "drawCallback": function (settings) {

                console.log('staff-list-datatable-execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>