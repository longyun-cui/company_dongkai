<script>
    function Datatable__for__Mac_Address__List($tableId)
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
                'url': "{{ url('/o1/mac-address/mac-address-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $('input[name="mac-address-id"]').val();
                    d.login_number = $('input[name="mac-address-number"]').val();
                    d.name = $('input[name="mac-address-name"]').val();
                    d.team = $tableSearch.find('select[name="mac-address-team"]').val();
                    d.item_status = $tableSearch.find('select[name="mac-address-status"]').val();
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
                    "title": "mac地址",
                    "data": "mac_address",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "客户名",
                    "data": "api_customerName",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '--';
                    }
                },
                {
                    "title": "用户名",
                    "data": "api_userName",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '--';
                    }
                },
                {
                    "title": "密码",
                    "data": "api_password",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '--';
                    }
                },
                // {
                //     "title": "公司",
                //     "data": "company_id",
                //     "className": "",
                //     "width": "120px",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         if(row.company_er) {
                //             return '<a href="javascript:void(0);" class="text-black">'+row.company_er.name+'</a>';
                //         }
                //         else return '--';
                //     }
                // },
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

                            if(row.team_sub_er)
                            {
                                var $team_sub_name = row.team_sub_er.name;
                                $return += ' - ' + $team_sub_name;
                            }

                            if(row.team_group_er)
                            {
                                var $team_group_name = row.team_group_er.name;
                                $return += ' - ' + $team_group_name;
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
                        var $html_login = '<a class="btn btn-xs mac-address--item-login-submit" data-id="'+data+'">登录</a>';
                        var $html_password_reset = '<a class="btn btn-xs mac-address--item-password-reset-submit" data-id="'+data+'">重置密码</a>';
                        var $html_operation_record = '<a class="btn btn-xs modal-show--for--mac-address--item-operation-record" data-id="'+data+'">记录</a>';

                        if(row.user_category == 1)
                        {
                            $html_edit = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">编辑</a>';
                        }
                        else
                        {
                            $html_edit = '<a class="btn btn-xs mac-address--item-edit-submit" data-id="'+data+'">编辑</a>';
                        }

                        if(row.item_status == 1)
                        {
                            $html_able = '<a class="btn btn-xs mac-address--item-disable-submit" data-id="'+data+'">禁用</a>';
                        }
                        else
                        {
                            $html_able = '<a class="btn btn-xs mac-address--item-enable-submit" data-id="'+data+'">启用</a>';
                        }

                        if(row.deleted_at == null)
                        {
                            $html_delete = '<a class="btn btn-xs mac-address--item-delete-submit" data-id="'+data+'">删除</a>';
                        }
                        else
                        {
                            $html_delete = '<a class="btn btn-xs mac-address--item-restore-submit" data-id="'+data+'">恢复</a>';
                        }

                        // if(row.user_type == 88)
                        // {
                        //     $html_promote = '<a class="btn btn-xs mac-address--item-promote-submit" data-id="'+data+'">晋升</a>';
                        // }
                        // else if(row.user_type == 84)
                        // {
                        //     $html_promote = '<a class="btn btn-xs mac-address--item-demote-submit" data-id="'+data+'">降职</a>';
                        // }
                        // else
                        // {
                        //     $html_promote = '<a class="btn btn-xs btn-default disabled" data-id="'+data+'">晋升</a>';
                        // }


                        var html =
                            '<a class="btn btn-xs modal-show--for--mac-address-item-edit" data-id="'+data+'">编辑</a>'+
                            $html_promote+
                            $html_able+
                            $html_delete+
                            $html_operation_record+
                            // '<a class="btn btn-xs mac-address--item-statistic" data-id="'+data+'">统计</a>'+
                            // '<a class="btn btn-xs mac-address--item-login-submit" data-id="'+data+'">登录</a>'+
                            '';
                        return html;
                    }
                },
            ],
            "drawCallback": function (settings) {

                console.log('mac-address-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>