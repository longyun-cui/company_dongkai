<?php
namespace App\Repositories\DK\DK_Staff;

use App\Models\DK\DK_Common\DK_Common__Company;
use App\Models\DK\DK_Common\DK_Common__Department;
use App\Models\DK\DK_Common\DK_Common__Team;
use App\Models\DK\DK_Common\DK_Common__Staff;

use App\Models\DK\DK_Common\DK_Common__Location;

use App\Models\DK\DK_Common\DK_Common__Client;
use App\Models\DK\DK_Common\DK_Common__Project;
use App\Models\DK\DK_Common\DK_Common__Order;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;


class DK_Staff__DownloadRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelOrder;
    private $view_blade_403;
    private $view_blade_404;


    public function __construct()
    {
        $this->modelUser = new DK_Common__Staff;
        $this->modelOrder = new DK_Common__Order;

        $this->view_blade_403 = env('DK_STAFF__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_STAFF__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_staff_user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_staff_user")->user();
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 【电话池】返回-导入-视图
    public function operate_download_file_download($post_data)
    {
        $type = $post_data['type'];
//        dd($type);

        if($type == 'url')
        {

//            $date = date('Y-m-d');
//            $upload_path = <<<EOF
//resource/dk/admin/telephone/$date/
//EOF;
//            $storage_path = storage_path($upload_path);
//            if (!is_dir($storage_path))
//            {
//                mkdir($storage_path, 0766, true);
//            }

            $url = $post_data['url'];

            if(!empty($post_data['name']))
            {
                $name = $post_data['name'];
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

//                $file = $storage_path.$name;
            }
            else
            {
                $url_path = parse_url($url, PHP_URL_PATH);
                $name = substr($url_path, strrpos($url_path, '/') + 1);
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

//                $file = $storage_path.$name;
            }

            $file = file_get_contents($url);
            return Response::make($file, 200)
                ->header('Content-Type', 'application/'.$extension)
                ->header('Content-Disposition', "attachment; filename=$name");

//            file_put_contents($file, $data);
        }
        else if($type == 'path')
        {
            $file = $post_data['path'];
        }
        else
        {
            $file = $post_data['path'];
        }

        if(!empty($post_data['name']))
        {
            $name = $post_data['name'];
            return response()->download($file,$name);
        }
        else return response()->download($file);


    }

    public function operate_download_call_recording_download($post_data)
    {
        $call_record_id = $post_data['call_record_id'];
        $call = DK_CC_Call_Record::find($call_record_id);
        $record_url = 'https://feiniji.cn'.$call->recordFile;


        $name = $call->callee.'-'.$call->id.'.mp3';
        $extension = 'mp3';
//        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $file = file_get_contents($record_url);
        return Response::make($file, 200)
            ->header('Content-Type', 'application/'.$extension)
            ->header('Content-Disposition', "attachment; filename=$name");

    }

    public function operate_download_item_recording_download($post_data)
    {
        $item_id = $post_data['item_id'];
        $record_url = $post_data['url'];

        $randomNumber = rand(1000, 9999);

        $name = $item_id.'-'.$randomNumber.'.mp3';
        $extension = 'mp3';
//        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $file = file_get_contents($record_url);
        return Response::make($file, 200)
            ->header('Content-Type', 'application/'.$extension)
            ->header('Content-Disposition', "attachment; filename=$name");

    }

    public function operate_download_phone_recording_download($post_data)
    {
        $phone = $post_data['phone'];
        $record_url = $post_data['url'];

        $randomNumber = rand(100000, 999900);

        $time = time();

        $name = $phone.'-'.$randomNumber.'.mp3';
        $extension = 'mp3';
//        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $file = file_get_contents($record_url);
        return Response::make($file, 200)
            ->header('Content-Type', 'application/'.$extension)
            ->header('Content-Disposition', "attachment; filename=$name");

    }




}