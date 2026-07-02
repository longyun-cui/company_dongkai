<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" type="image/ico" href="{{ env('FAVICON_DK_CLIENT') }}">
        <link rel="shortcut icon" type="image/png" href="{{ env('FAVICON_DK_CLIENT') }}">
        <link rel="icon" sizes="16x16 32x32 64x64" href="{{ env('FAVICON_DK_CLIENT') }}">
        <link rel="icon" type="image/png" sizes="196x196" href="{{ env('FAVICON_DK_CLIENT') }}">

        <title>FNJ</title>

        <!-- Fonts -->
{{--        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">--}}

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 40px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .item-download-recording-list-submit
            {
                cursor:pointer;
            }
            .item-download-recording-list-submit:hover
            {
                color: purple;
                display: inline-block; /* 或其他合适的display值 */
                box-shadow: inset 0 -2px purple; /* 在底部添加一个黑色的阴影 */
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height main-content">

            @if(!empty($data))
            <div class="content">
{{--                <div class="title m-b-md">--}}
{{--                    <span>{{ $data->client_name or '' }}</span>--}}
{{--                </div>--}}
{{--                <div class="title m-b-md">--}}
{{--                    <span>{{ $data->client_phone or '' }}</span>--}}
{{--                </div>--}}
{{--                <div class="title m-b-md">--}}
{{--                    <span>{{ $data->wx_id or '' }}</span>--}}
{{--                </div>--}}
{{--                <div class="title m-b-md">--}}
{{--                    <span>{{ $data->location_city or '' }} - {{ $data->location_district or '' }}</span>--}}
{{--                </div>--}}
{{--                <div class="title m-b-md">--}}
{{--                    <span>{{ $data->description or '' }}</span>--}}
{{--                </div>--}}
                <div class="title m-b-md recording_address_download" data-address-list="{{ $data->recording_address_list }}">
                    @if(!empty($recording_list) && count($recording_list) > 0)
                    @foreach($recording_list as $recording)
                        <audio controls controlsList="nodownload" style="width:480px;height:80px;">
                            <source src="{{ $recording or '' }}" type="audio/mpeg">
                        </audio>
                    @endforeach
                    @endif
                </div>
                <div class="title m-b-xs">
                    <span class="">
                        <a class="btn btn-xs item-download-recording-list-submit" data-id="{{ $data->id }}">下载</a>
                    </span>
                </div>
            </div>
            @endif
            <div class="content">
                <div class="title m-b-md">
                    <span>{{ $error or '' }}</span>
                </div>
            </div>
        </div>
        <script src="{{ asset('/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <script>
            $(function() {

                // 【下载录音】
                $(".main-content").on('click', ".item-download-recording-list-submit", function() {
                    var $that = $(this);
                    var $item_id = $that.data('id');

                    $recording_list_str = $('.recording_address_download').data('address-list');
                    console.log($recording_list_str);
                    if($recording_list_str)
                    {
                        // var $recording_list = JSON.parse($recording_list_str);
                        // console.log($recording_list);

                        $.each($recording_list_str, function($index, $value) {

                            console.log($index);
                            console.log($value);

                            var $obj = new Object();
                            $obj.item_id = $item_id;

                            $obj.url = $value;

                            var $randomNumber = Math.floor(Math.random() * 100) + 1;
                            $obj.randomNumber = $randomNumber;
                            console.log($obj);

                            var $url = url_build('/download/item-recording-download',$obj);
                            window.open($url);

                        });
                    }
                });

            });


            function url_build(path, params)
            {
                var url = "" + path;
                var _paramUrl = "";
                // url 拼接 a=b&c=d
                if(params)
                {
                    _paramUrl = Object.keys(params).map(function (k) {
                        return [encodeURIComponent(k), encodeURIComponent(params[k])].join("=");
                    }).join("&");
                    _paramUrl = "?" + _paramUrl
                }
                return url + _paramUrl
            }s
        </script>
    </body>
</html>
