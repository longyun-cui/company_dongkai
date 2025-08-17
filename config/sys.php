<?php

    return [


        'data' => [

            'table' => [

                'cc' => '数据系统',
                'admin' => '交付系统',
                'client' => '客户后台',
                'admin2' => '自选系统',
                'customer' => '客户后台',
            ],


            'city_pool_table_kv' => [
                '北京市' => 'a_pool_city_bj',
                '上海市' => 'a_pool_city_sh',
                '天津市' => 'a_pool_city_tj',
                '重庆市-重庆' => 'a_pool_city_cq_cq',
                '湖北-武汉市' => 'a_pool_city_hub_wh',
                '四川-成都市' => 'a_pool_city_sc_cd',
                '广东-汕头市' => 'a_pool_city_gd_st',
                '广东-广州市' => 'a_pool_city_gd_gz',
                '浙江-杭州市' => 'a_pool_city_zj_hz',
                '浙江-绍兴市' => 'a_pool_city_zj_sx',
            ],
            'city_pool' => [
                '北京市',
                '上海市',
                '天津市',
                '重庆-重庆',
                '湖北-武汉市',
                '四川-成都市',
                '广东-汕头市',
                '广东-广州市',
                '浙江-杭州市',
                '浙江-绍兴市',
            ],


            'phone_table_kv' => [
                'a_current_test' => '测试',
                'a_current_task' => '任务',
                'a_pool_black' => '黑名单',
                'a_pool_city_bj' => '北京',
                'a_pool_city_sh' => '上海',
                'a_pool_city_tj' => '天津',
                'a_pool_city_cq_cq' => '重庆-重庆',
                'a_pool_city_hub_wh' => '湖北-武汉市',
                'a_pool_city_sc_cd' => '四川-成都市',
                'a_pool_city_gd_gz' => '广东-广州市',
                'a_pool_city_gd_st' => '广东-汕头市',
                'a_pool_city_zj_hz' => '浙江-杭州市',
                'a_pool_city_zj_sx' => '浙江-绍兴市',
            ],
            'phone_table' => [
                'a_current_test',
                'a_current_task',
                'a_pool_black',
                'a_pool_city_bj',
                'a_pool_city_sh',
                'a_pool_city_tj',
                'a_pool_city_cq_cq',
                'a_pool_city_hub_wh',
                'a_pool_city_sc_cd',
                'a_pool_city_gd_gz',
                'a_pool_city_gd_st',
                'a_pool_city_zj_hz',
                'a_pool_city_zj_sx',
            ],

            'wb_address' => '',
        ],





        'team_district' => [
            '一区','二区','三区','四区','五区','六区','七区','八区','九区'
        ],

        'channel_source' => [
            '抖音',
            '快手',
            '百度',
            '腾讯',
            '其他'
        ],



        'teeth_count' => [
            '1-2颗',
            '3-5颗',
            '6颗',
            '半口',
            '全口',
            '其他'
        ],

        'order_quality' => [
            '有效',
            '无效',
            '重单',
            '无法联系'
        ],



        'api' => [

            'sys_1__call_01__C1' => [
                'sys' => '01',
                'call' => '01',
                'server' => 'http://call01.zlyx.jjccyun.cn',
                'account' => 'C1',
                'password' => '032CCBC3C2D0D83E9B6DCEA27D3D795A',
            ],
//            'sys_1__call_01__C13' => [
//                'server' => 'http://call01.zlyx.jjccyun.cn',
//                'account' => 'C13',
//                'password' => 'D60D4F0B58C03CB2A67D886451AFB2E1',
//                'name' => 'FNJ三区',
//                'team' => '三区',
//            ],

            'sys_1__call_02__C1' => [
                'sys' => '02',
                'call' => '02',
                'server' => 'http://call02.zlyx.jjccyun.cn',
                'account' => 'C1',
                'password' => '2690964BADF2F3DA80582753D983FA0B',
            ],
//            'sys_1__call_02__C7' => [
//                'server' => 'http://call02.zlyx.jjccyun.cn',
//                'account' => 'C7',
//                'password' => 'F74556C506E5A6045DC1CFAF8E55026F',
//                'name' => 'FNJ五区',
//                'team' => '五区',
//            ],

            'sys_1__call_03__C1' => [
                'sys' => '03',
                'call' => '03',
                'server' => 'http://call03.zlyx.jjccyun.cn',
                'account' => 'C1',
                'password' => '201784A0FA1C3AB5353B6CE0D2C64BBD',
            ],


            'sys_2__C1' => [
                'sys' => '21',
                'call' => '01',
                'server' => 'http://okcc8.zytchina.net',
                'account' => 'C1',
                'password' => '19024252118532F0D0EF0C98F0B122EC',
            ],


//            'sys_3__C1' => [
//                'sys' => '31',
//                'call' => '01',
//                'server' => 'http://180.109.247.218:8089',
//                'account' => 'C1',
//                'password' => '40DD14D4C624B14AD1EC18E8986EBD2D1C6929E6',
//            ],
        ],

        'api_sys_team' => [

            '01' => [
                'C5' => 'FNJ大区',
                'C13' => 'FNJ三区',
                'C14' => 'FNJ四区',
                'C15' => 'FNJ五区',
                'C16' => 'FNJ六区',
                'C10' => 'FNJ七区',
                'C18' => 'FNJ八区',
                'C19' => 'FNJ九区',
            ],
            '02' => [
                'C7' => 'FNJ五区',
                'C6' => 'FNJ六区',
                'C9' => 'FNJ八区',
                'C8' => 'FNJ十区',
                'C11' => 'FNJ十三区',
                'C10' => 'FNJTL',
            ],
            '03' => [
                'C4' => 'FNJ七区',
                'C2' => 'FNJ八区',
                'C3' => 'FNJ九区',
                'C12' => 'FNJ十一区',
                'C8' => 'FNJJY',
                'C10' => 'FNJYM',
                'C20' => 'FNJZF',
            ],

            '21' => [
                'C3' => '青年医美',
                'C4' => '青年口腔',
            ],

            '31' => [
                'C3' => '青年口腔',
            ],
        ],


    ];














