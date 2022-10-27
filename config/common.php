<?php

    return [

        'name' => 'GPS',

        'host' => [

            'local' => [
                'root' => 'http://gps.com',
                'www' => 'http://www.gps.com',
                'cdn' => 'http://cdn.gps.com',
            ],

            'online' => [
                'root' => 'http://cui.party',
                'www' => 'http://www.cui.party',
                'cdn' => 'http://cdn.cui.party',
            ],

            'front' => [
                'prefix' => '',
                'root' => '',
                'index' => ''
            ],

            'admin' => [
                'prefix' => ''
            ],

        ],


        'website' => [

            'front' => [
                'prefix' => '',
                'root' => '',
                'index' => ''
            ],

            'admin' => [
                'prefix' => ''
            ],

        ],


        'MailService' => 'http://live2.pub:8088',


    ];
