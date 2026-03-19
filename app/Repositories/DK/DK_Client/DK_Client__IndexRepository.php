<?php
namespace App\Repositories\DK\DK_Client;

use App\Models\DK\DK_Order;

use App\Models\DK\DK_Client\DK_Client__Team;
use App\Models\DK\DK_Client\DK_Client__Staff;

use App\Models\DK_CC\DK_CC_Call_Record;
use App\Models\DK_CC\DK_CC_Call_Record_Current;


use App\Models\DK\DK_Common\DK_Common__Order;
use App\Models\DK\DK_Common\DK_Common__Delivery;



use App\Jobs\DK_Client\AutomaticDispatchingJob;


use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache, Blade, Carbon;
use QrCode, Excel;

class DK_Client__IndexRepository {

    private $env;
    private $auth_check;
    private $me;
    private $me_admin;
    private $modelUser;
    private $modelItem;
    private $view_blade_403;
    private $view_blade_404;

    public function __construct()
    {
        $this->modelUser = new DK_Client__Staff;

        $this->view_blade_403 = env('DK_CLIENT__TEMPLATE').'403';
        $this->view_blade_404 = env('DK_CLIENT__TEMPLATE').'404';

        Blade::setEchoFormat('%s');
        Blade::setEchoFormat('e(%s)');
        Blade::setEchoFormat('nl2br(e(%s))');
    }


    // 登录情况
    public function get_me()
    {
        if(Auth::guard("dk_client__user")->check())
        {
            $this->auth_check = 1;
            $this->me = Auth::guard("dk_client__user")->user();
            $this->me->load('client_er');
            view()->share('me',$this->me);
        }
        else $this->auth_check = 0;

        view()->share('auth_check',$this->auth_check);

        if(isMobileEquipment()) $is_mobile_equipment = 1;
        else $is_mobile_equipment = 0;
        view()->share('is_mobile_equipment',$is_mobile_equipment);
    }




    // 返回（后台）主页视图
    public function view_client__index()
    {
        $this->get_me();
        $me = $this->me;

//        $condition = request()->all();
//        $return['condition'] = $condition;
//
//        $condition['task-list-type'] = 'unfinished';
//        $parameter_result = http_build_query($condition);
//        return redirect('/?'.$parameter_result);

        $team_list = DK_Client__Team::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('client_id',$me->client_id)
            ->get();
        $return['team_list'] = $team_list;

        $staff_list = DK_Client__Staff::select('id','name')
            ->where('active',1)
            ->where('item_status',1)
            ->where('client_id',$me->client_id)
            ->whereNotIn('staff_position',[0,1,9])
            ->get();
        $return['staff_list'] = $staff_list;

        $view_blade = env('DK_CLIENT__TEMPLATE').'index';
        return view($view_blade)->with($return);
    }


    // 返回（后台）主页视图
    public function view_admin_404()
    {
        $this->get_me();
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.errors.404';
        return view($view_blade);
    }



    public function view_data_voice_record($post_data)
    {
        $record_id = $post_data['record_id'];

        $order = DK_Order::where('call_record_id',$record_id)->orderBy("id", "desc")->first();
        if($order)
        {
            $call_record = DK_CC_Call_Record::find($record_id);
            if($call_record)
            {
                $serverFrom = $call_record['serverFrom_name'];
                if($serverFrom == 'FNJ')
                {
                    $server_http = 'http://feiniji.cn';
                }
                else if($serverFrom == 'call-01')
                {
                    $server_http = 'http://call01.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-02')
                {
                    $server_http = 'http://call02.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-03')
                {
                    $server_http = 'http://call03.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-04')
                {
                    $server_http = 'http://call04.zlyx.jjccyun.cn';
                }
                else
                {
                    $server_http = 'http://feiniji.cn';
                }

                $record_file_address = $server_http . $call_record->recordFile;


                $ch = curl_init($record_file_address);
                curl_setopt_array($ch, [
                    CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                    CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                    CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                    CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($statusCode === 200)
                {
                    $view_data['record_file_address'] = $record_file_address;
                    $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
                    return view($view_blade)->with($view_data);
                }
            }
            else
            {
                $call_record_current = DK_CC_Call_Record_Current::find($record_id);

                $serverFrom = $call_record_current['serverFrom_name'];
                if($serverFrom == 'FNJ')
                {
                    $server_http = 'http://feiniji.cn';
                }
                else if($serverFrom == 'call-01')
                {
                    $server_http = 'http://call01.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-02')
                {
                    $server_http = 'http://call02.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-03')
                {
                    $server_http = 'http://call03.zlyx.jjccyun.cn';
                }
                else if($serverFrom == 'call-04')
                {
                    $server_http = 'http://call04.zlyx.jjccyun.cn';
                }
                else
                {
                    $server_http = 'http://feiniji.cn';
                }

                $record_file_address = $server_http . $call_record_current->recordFile;
                $ch = curl_init($record_file_address);
                curl_setopt_array($ch, [
                    CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                    CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                    CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                    CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);
                curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if($statusCode === 200)
                {
                    $view_data['record_file_address'] = $record_file_address;
                }
                $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
                return view($view_blade)->with($view_data);

            }
        }
        else
        {
            $call_record_current = DK_CC_Call_Record_Current::find($record_id);

            $serverFrom = $call_record_current['serverFrom_name'];
            if($serverFrom == 'FNJ')
            {
                $server_http = 'http://feiniji.cn';
            }
            else if($serverFrom == 'call-01')
            {
                $server_http = 'http://call01.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-02')
            {
                $server_http = 'http://call02.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-03')
            {
                $server_http = 'http://call03.zlyx.jjccyun.cn';
            }
            else if($serverFrom == 'call-04')
            {
                $server_http = 'http://call04.zlyx.jjccyun.cn';
            }
            else
            {
                $server_http = 'http://feiniji.cn';
            }

            $record_file_address = $server_http . $call_record_current->recordFile;
            $ch = curl_init($record_file_address);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,        // 不下载内容，仅请求头
                CURLOPT_FOLLOWLOCATION => true,// 跟随重定向
                CURLOPT_TIMEOUT => 5,          // 超时时间（秒）
                CURLOPT_SSL_VERIFYHOST => false, // 如需跳过SSL验证
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($statusCode === 200)
            {
                $view_data['record_file_address'] = $record_file_address;
            }
            $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.voice-record';
            return view($view_blade)->with($view_data);

        }


    }


    public function view_data_of_order_detail($post_data)
    {
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.delivery-detail';

        $order_id  = isset($post_data['order_id']) ? medsci_decode($post_data['order_id'],'2024') : 0;
        if(!$order_id)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数1有误！';
            return view($view_blade)->with($view_data);
        }

        $phone  = isset($post_data['phone']) ? $post_data['phone']  : 0;
        if(!$phone)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数2有误！';
            return view($view_blade)->with($view_data);
        }

        $order = DK_Common__Order::select(['id','client_name','client_phone','wx_id','location_city','location_district','description','recording_address_list'])->find($order_id);
        if($order)
        {
            if($order->client_phone == $phone)
            {
                if($order->recording_address_list)
                {
                    $recording_list = json_decode($order->recording_address_list);
                    $order->recording_list = $recording_list;
                    $view_data['recording_list'] = $recording_list;
                }
                $view_data['data'] = $order;
                return view($view_blade)->with($view_data);
            }
            else
            {
                $view_data['data'] = null;
                $view_data['error'] = '电话有误！';
                return view($view_blade)->with($view_data);
            }
        }
        else
        {
            $view_data['data'] = null;
            $view_data['error'] = '交付有误！';
            return view($view_blade)->with($view_data);
        }
    }


    public function view_data_of_delivery_detail($post_data)
    {
        $view_blade = env('DK_CLIENT__TEMPLATE').'entrance.data.delivery-detail';

        $delivery_id  = isset($post_data['delivery_id']) ? $post_data['delivery_id']  : 0;
        if(!$delivery_id)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数1有误！';
            return view($view_blade)->with($view_data);
        }

        $phone  = isset($post_data['phone']) ? $post_data['phone']  : 0;
        if(!$phone)
        {
            $view_data['data'] = null;
            $view_data['error'] = '参数2有误！';
            return view($view_blade)->with($view_data);
        }

        $delivery = DK_Common__Delivery::with([
            'order_er'=>function($query) {
                $query->select(['id','client_name','client_phone','wx_id','location_city','location_district','description','recording_address_list']);
        }
        ])->find($delivery_id);
        if($delivery)
        {
            if($delivery->client_phone == $phone)
            {
                if($delivery->order_er)
                {
                    $order = $delivery->order_er;
                    if($order->recording_address_list)
                    {
                        $recording_list = json_decode($order->recording_address_list);
                        $order->recording_list = $recording_list;
                        $view_data['recording_list'] = $recording_list;
                    }
                    $view_data['data'] = $order;
                    return view($view_blade)->with($view_data);
                }
            }
            else
            {
                $view_data['data'] = null;
                $view_data['error'] = '电话有误！';
                return view($view_blade)->with($view_data);
            }
        }
        else
        {
            $view_data['data'] = null;
            $view_data['error'] = '交付有误！';
            return view($view_blade)->with($view_data);
        }
    }



    // 【交付管理】返回-列表-数据
    public function query_last_delivery()
    {
        $this->get_me();
        $me = $this->me;

        $last_delivery = DK_Common__Delivery::select('*')
//            ->selectAdd(DB::Raw("FROM_UNIXTIME(assign_time, '%Y-%m-%d') as assign_date"))
            ->where('client_id',$me->client_id)
            ->when(in_array($me->user_type,[81,84]), function ($query) use ($me) {
                return $query->where('department_id', $me->department_id);
            })
            ->when(in_array($me->user_type,[88]), function ($query) use ($me) {
                return $query->where('client_staff_id', $me->id);
            })
            ->orderBy('id','desc')
            ->first();
        if($last_delivery) return response_success(['last_delivery'=>$last_delivery]);
        else return response_success([]);
    }




}