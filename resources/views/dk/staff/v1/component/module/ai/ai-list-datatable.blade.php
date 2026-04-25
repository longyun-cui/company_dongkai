<script>
    function Datatable__for__AI__Record__List($tableId)
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
                'url': "{{ url('/o1/ai/ai-record-list/datatable-query') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="ai-id"]').val();
                    d.title = $tableSearch.find('input[name="ai-title"]').val();
                    d.keyword = $tableSearch.find('input[name="ai-keyword"]').val();
                    d.type = $tableSearch.find('select[name="ai-type"]').val();
                    d.item_status = $tableSearch.find('select[name="ai-item-status"]').val();
                    d.order_id = $tableSearch.find('input[name="ai-order-id"]').val();
                },
            },
            "fixedColumns": {
                "leftColumns": "@if($is_mobile_equipment) 1 @else 1 @endif",
                "rightColumns": "0"
            },
            "columns": [
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "序号",
//                        "data": null,
//                        "targets": 0,
//                        "orderable": false
//                    },
//                    {
//                        "className": "",
//                        "width": "32px",
//                        "title": "选择",
//                        "data": "id",
//                        "orderable": true,
//                        render: function(data, type, row, meta) {
//                            return '<label><input type="checkbox" name="bulk-detect-record-id" class="minimal" value="'+data+'"></label>';
//                        }
//                    },
                {
                    "title": "ID",
                    "data": "id",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "状态",
                    "data": "item_status",
                    "className": "",
                    "width": "100px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $result_html = '';
                        if(data == 0)
                        {
                            $result_html = '<small class="btn-xs bg-aqua">待审核</small>';
                        }
                        else if(data == 1)
                        {
                            $result_html = '<small class="btn-xs bg-blue">审核中</small>';
                        }
                        else if(data == 9)
                        {
                            $result_html = '<small class="btn-xs bg-green">已审核</small>';
                        }
                        else if(data == 99)
                        {
                            $result_html = '<small class="btn-xs bg-black">审核有误</small>';
                        }
                        else
                        {
                            $result_html = '<small class="btn-xs bg-black">有误</small>';
                        }
                        return $result_html;
                    }
                },
                {
                    "title": "工单ID",
                    "data": "order_id",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "平台",
                    "data": "ai_platform",
                    "className": "",
                    "width": "60px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "模型",
                    "data": "ai_model",
                    "className": "",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                },
                {
                    "title": "消耗Token",
                    "data": "usage",
                    "className": "text-left",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(!data) return '--';

                        var $return_html = '';
                        $return_html += '【全部消耗】' + data.total_tokens + ' <br>';
                        $return_html += '【提示总耗】' + data.prompt_tokens + ' <br>';
                        $return_html += '【音频消耗】' + data.prompt_tokens_details.audio_tokens + ' <br>';
                        $return_html += '【文本消耗】' + data.prompt_tokens_details.text_tokens + ' <br>';
                        $return_html += '【返回消耗】' + data.completion_tokens + ' <br>';
                        // $.each(data, function($index, $value) {
                        //     $return_html += '【'+ $index +'】' + $value + ' <br>';
                        // });
                        return $return_html;
                    }
                },
                {
                    "title": "耗时(s)",
                    "data": "ai_used_time",
                    "className": "text-left",
                    "width": "120px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        var $return_html = '';
                        $return_html += '【程序】' + row.program_used_time + ' <br>';
                        $return_html += '【质检】' + data + ' <br>';
                        return $return_html;
                    }
                },
                {
                    "className": "text-left",
                    "width": "400px",
                    "title": "返回结果",
                    "data": "content",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(row.item_status == 99)
                        {
                            return row.description;
                        }
                        if(!data) return '--';

                        var $return_html = '';
                        $.each(data, function($index, $value) {
                            $return_html += '【'+ $index +'】' + $value + ' <br>';
                        });
                        return $return_html;
                    }
                },
                {
                    "title": "录音播放",
                    "name": "order_id",
                    "data": "order_id",
                    "className": "",
                    "width": "400px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1)
                        {
                            $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                            $(nTd).attr('data-key','recording_address_play').attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        if(row.order_er)
                        {
                            try
                            {
                                var $recording_list = JSON.parse(row.order_er.recording_address_list);

                                var $return_html = '';
                                $.each($recording_list, function(index, value)
                                {

                                    var $audio_html = '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+value+'" type="audio/mpeg"></audio><br>'
                                    $return_html += $audio_html;
                                });
                                return $return_html;
                            }
                            catch(e)
                            {
                                // console.log(e);
                                return '';
                            }
                        }
                        else
                        {
                            if(row.recording_address)
                            {
                                return '<audio controls controlsList="nodownload" style="width:380px;height:20px;"><source src="'+row.recording_address+'" type="audio/mpeg"></audio>';
                            }
                            else return '';
                        }
                    }
                },
                {
                    "className": "",
                    "width": "120px",
                    "title": "记录时间",
                    "data": "created_at",
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

                console.log('ai-list.datatable-query.execute');

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "language": { url: '/common/dataTableI18n' },
        });

    }
</script>