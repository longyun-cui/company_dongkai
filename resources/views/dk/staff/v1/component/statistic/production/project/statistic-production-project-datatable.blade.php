<script>
    function Datatable__for__Statistic_Production_Project($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/o1/statistic/production/project') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="statistic-production-project-id"]').val();
                    d.name = $tableSearch.find('input[name="statistic-production-project-name"]').val();
                    d.title = $tableSearch.find('input[name="statistic-production-project-title"]').val();
                    d.keyword = $tableSearch.find('input[name="statistic-production-project-keyword"]').val();
                    d.status = $tableSearch.find('select[name="statistic-production-project-status"]').val();
                    d.order_category = $tableSearch.find('select[name="statistic-production-project-order-category"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-production-project-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-production-project-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-production-project-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-production-project-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-production-project-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-production-project-project"]').val();
                    d.team = $tableSearch.find('select[name="statistic-production-project-team"]').val();
                    d.team_group = $tableSearch.find('select[name="statistic-production-project-team-group"]').val();
                },
            },
            // "fixedColumns": {
            {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
            {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
            // },
            "columns": [
//                    {
//                        "title": "选择",
//                        "data": "id",
//                        "width": "32px",
//                        "orderable": false,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
//                    {
//                        "title": "序号",
//                        "data": null,
//                        "width": "32px",
//                        "targets": 0,
//                        "orderable": false
//                    },
                {
                    "title": "项目ID",
                    "data": "id",
                    "className": "text-center",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "项目名称",
                    "data": "name",
                    "className": "text-center",
                    "width": "200px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;

                    }
                },
                {
                    "title": "报单量",
                    "data": "order_count_for_all",
                    "className": "bg-service-customer",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },


                // {
                //     "title": "审核<br>有效量",
                //     "data": "order_count_for_effective",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                {
                    "title": "审核量",
                    "data": "order_count_for_inspected",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "通过量",
                    "data": "order_count_for_accepted",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "折扣通过",
                    "data": "order_count_for_accepted_discount",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "郊区通过",
                    "data": "order_count_for_accepted_suburb",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "内部通过",
                    "data": "order_count_for_accepted_inside",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                // {
                //     "title": "审核<br>完成率",
                //     "data": "order_rate_for_achieved",
                //     "className": "text-blue _bold bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(data) return data + " %";
                //         return data
                //     }
                // },
                {
                    "title": "重复量",
                    "data": "order_count_for_repeated",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {

                        return data;
                    }
                },
                {
                    "title": "拒绝量",
                    "data": "order_count_for_refused",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "待审核量",
                    "data": "order_count_for_inspected",
                    "className": "bg-inspected",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                        var $waiting_count = row.order_count_for_all - data;
                        if($waiting_count > 0)
                        {
                            $(nTd).addClass('_bold').addClass('text-red');
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $waiting_count = row.order_count_for_all - data;
                        if($waiting_count > 0) return $waiting_count;
                        else return '--';
                    }
                },
                // {
                //     "title": "审核<br>内部通过",
                //     "data": "order_count_for_accepted_inside",
                //     "className": "bg-inspected",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },


                @if(in_array($me->staff_category,[0,1,9,71]))
                {
                    "title": "不合格",
                    "data": "order_count_for_accepted_non",
                    "className": "bg-grey",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "交付量",
                    "data": "order_count_for_delivered",
                    "className": "bg-grey",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "待交付量",
                    "data": "order_count_for_delivered",
                    "className": "bg-grey",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                        var $waiting_count = row.order_count_for_accepted + row.order_count_for_accepted_discount + row.order_count_for_accepted_suburb + row.order_count_for_accepted_non - data;
                        if($waiting_count > 0)
                        {
                            $(nTd).addClass('_bold').addClass('text-red');
                        }
                    },
                    render: function(data, type, row, meta) {
                        var $waiting_count = row.order_count_for_accepted + row.order_count_for_accepted_discount + row.order_count_for_accepted_suburb + row.order_count_for_accepted_non - data;
                        if($waiting_count > 0) return $waiting_count;
                        else return '--';
                    }
                },
                @endif


                {
                    "title": "通过率",
                    "data": "order_rate_for_accepted",
                    "className": "bg-gray",
                    "width": "60px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data) return data + " %";
                        return data
                    }
                },


                // {
                //     "title": "交付<br>有效量",
                //     "data": "order_count_for_delivered_effective",
                //     "className": "bg-delivered _bold",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>实际产出",
                //     "data": "order_count_for_delivered_actual",
                //     "className": "bg-delivered _bold",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>已交付",
                //     "data": "order_count_for_delivered_completed",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>隔日交付",
                //     "data": "order_count_for_delivered_tomorrow",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>内部交付",
                //     "data": "order_count_for_delivered_inside",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>重复",
                //     "data": "order_count_for_delivered_repeated",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>驳回",
                //     "data": "order_count_for_delivered_rejected",
                //     "className": "bg-delivered",
                //     "width": "80px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         return data;
                //     }
                // },
                // {
                //     "title": "交付<br>有效率",
                //     "data": "order_rate_for_delivered_effective",
                //     "className": "bg-delivered",
                //     "width": "100px",
                //     "orderable": false,
                //     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                //         if(row.id == "统计")
                //         {
                //             $(nTd).addClass('_bold');
                //         }
                //     },
                //     render: function(data, type, row, meta) {
                //         if(data) return data + " %";
                //         return data
                //     }
                // },


                // {
                //     "title": "备注",
                //     "data": "remark",
                //     "className": "text-left",
                //     "width": "",
                //     "orderable": false,
                //     render: function(data, type, row, meta) {
                //         return data;
                //         // if(data) return '<small class="btn-xs bg-yellow">查看</small>';
                //         // else return '';
                //     }
                // }
                @if($me->team_id == 0)
                {
                    "title": "团队",
                    "data": "pivot__project_team",
                    "className": "text-center",
                    "width": "240px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        var html = '';
                        $.each(data,function( key, val ) {
//                                console.log( key, val, this );
                            html += '<a href="javascript:void(0);">'+this.name+'</a> &nbsp;';
                        });
                        return html;
                    }
                },
                @endif
                {
                    "title": "每日目标",
                    "data": "daily_goal",
                    "className": "text-center text-green",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
            ],
            "columnDefs": [
            ],
            "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>