<style>

    /* 移动端专用样式 */
    @media screen and (max-width: 767px) {
        #dataTable_wrapper .dataTables_filter input {
            width: 150px !important;  /* 缩小搜索框 */
        }

        .dataTables_info {
            float: none !important;
            text-align: center;
        }

        .dataTables_paginate {
            text-align: center;
            float: none !important;
            margin-top: 10px;
        }

        /* 触控优化 */
        .dataTables_wrapper .dt-buttons button {
            padding: 8px 12px;
            margin: 2px;
            font-size: 14px;
        }

        /* 行高优化 */
        #dataTable tbody td {
            padding: 8px 10px !important;
            font-size: 14px;
        }

        /* 操作按钮样式 */
        .btn-mobile {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 60px;
        }
    }

</style>