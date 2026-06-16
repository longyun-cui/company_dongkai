<script>
    function Datatable__for__Statistic_Production_Order_Rejected($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            // "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
            "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "language": {
                url: '/common/dataTableI18n',
                // processing: '<div class="spinner"></div> 加载中...' // 自定义处理提示，包含动画
                processing: '<div class="spinner"></div> 正在加载数据，请稍候...',
            },
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/o1/statistic/production/order/rejected') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="production-order-rejected-id"]').val();
                    d.name = $tableSearch.find('input[name="production-order-rejected-name"]').val();
                    d.title = $tableSearch.find('input[name="production-order-rejected-title"]').val();
                    d.keyword = $tableSearch.find('input[name="production-order-rejected-keyword"]').val();
                    d.status = $tableSearch.find('select[name="production-order-rejected-status"]').val();
                    d.order_category = $tableSearch.find('select[name="statistic-production-order-rejected-order-category"]').val();
                    d.department = $tableSearch.find('select[name="statistic-production-order-rejected-department"]').val();
                    d.team_list = $tableSearch.find('select[name="statistic-production-order-rejected-team-list[]"]').val();
                    d.time_type = $tableSearch.find('input[name="statistic-production-order-rejected-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="statistic-production-order-rejected-month"]').val();
                    d.time_date = $tableSearch.find('input[name="statistic-production-order-rejected-date"]').val();
                    d.date_start = $tableSearch.find('input[name="statistic-production-order-rejected-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="statistic-production-order-rejected-ended"]').val();
                    d.project = $tableSearch.find('input[name="statistic-production-order-rejected-project"]').val();
                },
                "beforeSend": function() {
                    // 显示加载提示，例如使用一个div，或者调用layer.load等
                    // $('#loading').show();
                    var $index = layer.load(1, {
                        shade: [0.3, '#fff'],
                        content: '<span class="loadtip">正在加载</span>',
                        success: function (layer) {
                            layer.find('.layui-layer-content').css({
                                'padding-top': '40px',
                                'width': '100px',
                            });
                            layer.find('.loadtip').css({
                                'font-size':'20px',
                                'margin-left':'-18px'
                            });
                        }
                    });
                },
                "error": function() {
                    layer.msg("请求失败！");
                },
                "complete": function() {
                    // 隐藏加载提示
                    // $('#loading').hide();
                    layer.closeAll('loading');
                }
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
//                 {
//                     "title": "ID",
//                     "data": "id",
//                     "className": "text-center vertical-middle ",
//                     "width": "80px",
//                     "orderable": false,
//                     "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
//                         if(row.id == "统计")
//                         {
//                             $(nTd).addClass('_bold');
//                         }
//                     },
//                     render: function(data, type, row, meta) {
//                         return data;
//                     }
//                 },
                {
                    "title": "类型",
                    "data": "code",
                    "className": "text-center",
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
                        // return data+'('+row.creator_team_group_id+')';

                    }
                },
                {
                    "title": "原因",
                    "data": "name",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;

                    }
                },
                {
                    "title": "拒单量",
                    "data": "count",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
                        return data;
                    }
                },
                {
                    "title": "占比",
                    "data": "percentage",
                    "className": "bg-inspected",
                    "width": "80px",
                    "orderable": true,
                    "orderSequence": ["desc", "asc"],
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.id == "统计")
                        {
                            $(nTd).addClass('_bold');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(!data) return '--';
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