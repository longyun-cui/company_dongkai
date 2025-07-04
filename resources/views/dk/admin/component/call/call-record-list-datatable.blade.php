<script>

    function Datatable_Call_Record_List($tableId)
    {
        let $that = $($tableId);
        let $datatable_wrapper = $that.parents('.datatable-wrapper');
        let $tableSearch = $datatable_wrapper.find('.datatable-search-box');

        $($tableId).DataTable({

            "aLengthMenu": [[50, 200, 500], ["50", "200", "500"]],
            // "aLengthMenu": [[-1], ["全部"]],
            "processing": true,
            "serverSide": true,
            "searching": false,
            "pagingType": "simple_numbers",
            "sDom": '<"dataTables_length_box"l> <"dataTables_info_box"i> <"dataTables_paginate_box"p> <t>',
            "order": [],
            "orderCellsTop": true,
            "ajax": {
                'url': "{{ url('/v1/operate/call/call-record-list') }}",
                "type": 'POST',
                "dataType" : 'json',
                "data": function (d) {
                    d._token = $('meta[name="_token"]').attr('content');
                    d.id = $tableSearch.find('input[name="call-record-list-id"]').val();
                    d.name = $tableSearch.find('input[name="call-record-list-name"]').val();
                    d.title = $tableSearch.find('input[name="call-record-list-title"]').val();
                    d.keyword = $tableSearch.find('input[name="call-record-list-keyword"]').val();
                    d.phone_list = $tableSearch.find('textarea[name="call-record-list-phone"]').val();
                    d.status = $tableSearch.find('select[name="call-record-list-status"]').val();
                    d.time_type = $tableSearch.find('input[name="call-record-list-time-type"]').val();
                    d.time_month = $tableSearch.find('input[name="call-record-list-month"]').val();
                    d.time_date = $tableSearch.find('input[name="call-record-list-date"]').val();
                    d.date_start = $tableSearch.find('input[name="call-record-list-start"]').val();
                    d.date_ended = $tableSearch.find('input[name="call-record-list-ended"]').val();
                },
            },
            // "fixedColumns": {
            {{--"leftColumns": "@if($is_mobile_equipment) 1 @else 3 @endif",--}}
            {{--"rightColumns": "@if($is_mobile_equipment) 0 @else 1 @endif"--}}
            // },
            "columns": [
                {
                    "title": "团队",
                    "data": "agent",
                    "className": "text-center",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                    },
                    render: function(data, type, row, meta) {
                        if(row.customer) return row.customer;
                        else return '--';
                    }
                },
                {
                    "title": "坐席",
                    "data": "agent",
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
                    }
                },
                {
                    "title": "时间",
                    "data": "answerTime",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "电话号码",
                    "data": "callee",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-id',row.id).attr('data-name','电话号码');
                            $(nTd).attr('data-key','phone').attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "通话时长",
                    "data": "timeLength",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "城市",
                    "data": "city",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {
                        if(data) return data;
                        else return '';
                    }
                },
                {
                    "title": "录音播放",
                    "data": "recording",
                    "className": "",
                    "width": "400px",
                    "orderable": false,
                    "fnCreatedCell": function (nTd, data, row, iRow, iCol) {
                        if(row.is_completed != 1 && row.item_status != 97)
                        {
                            $(nTd).attr('data-id',row.id).attr('data-name','录音播放');
                            $(nTd).attr('data-key','recording').attr('data-value',data);
                        }
                    },
                    render: function(data, type, row, meta) {
                        // return data;
                        var $audio_html = '<audio controls controlsList="nodownload--" style="width:380px;height:20px;"><source src="' + data + '" type="audio/mpeg"></audio>';
                        return $audio_html;
                    }
                },
                {
                    "title": "操作",
                    "data": "city",
                    "className": "",
                    "width": "80px",
                    "orderable": false,
                    render: function(data, type, row, meta) {

                        var $html_paly = '<a class="btn btn-xs btn-success item-call-play-submit" data-id="'+data+'">播放</a>';
                        var $html_down = '<a class="btn btn-xs btn-success item-call-down-submit" data-id="'+data+'">下载</a>';

                        var $html =
                            // $html_paly+
                            $html_down+
                            '';
                        return $html;
                    }
                }
            ],
            "drawCallback": function (settings) {

//                    let startIndex = this.api().context[0]._iDisplayStart;//获取本页开始的条数
//                    this.api().column(1).nodes().each(function(cell, i) {
//                        cell.innerHTML =  startIndex + i + 1;
//                    });

            },
            "columnDefs": [
            ],
            "language": { url: '/common/dataTableI18n' },
        });
    }

</script>