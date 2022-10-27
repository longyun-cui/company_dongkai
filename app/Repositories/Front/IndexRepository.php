<?php
namespace App\Repositories\Front;

use App\Models\RootModule;
use App\Models\RootMenu;
use App\Models\RootItem;
use App\Models\RootMessage;

use App\Repositories\Common\CommonRepository;

use Response, Auth, Validator, DB, Exception, Cache;
use QrCode;

class IndexRepository {

    private $model;
    private $repo;
    public function __construct()
    {
//        $this->model = new OrgOrganization;
    }


    // root
    public function root()
    {
//        $info = json_decode(json_encode(config('mitong.company.info')));
//        $menus = RootMenu::where(['active'=>1])->orderby('order', 'asc')->get();

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->limit(8)->get();
        foreach($rent_items as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $wholesale_items = RootItem::where(['category'=>12, 'active'=>1])->orderby('id', 'desc')->limit(4)->get();
        foreach($wholesale_items as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $cooperation_items = RootItem::where(['category'=>9, 'active'=>1])->orderby('id', 'desc')->get();
        $coverage_items = RootItem::where(['category'=>31, 'active'=>1])->orderby('id', 'desc')->get();

        $html = view('frontend.entrance.root')->with([
                'rent_items'=>$rent_items,
                'wholesale_items'=>$wholesale_items,
                'cooperation_items'=>$cooperation_items,
                'coverage_items'=>$coverage_items
            ])->__toString();
        return $html;
    }


    // contact
    public function contact()
    {
        $houses = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->get();
        foreach($houses as $item)
        {
            $item->custom = json_decode($item->custom);
            $item->custom2 = json_decode($item->custom2);
        }

        $html = view('frontend.entrance.contact')->with(['houses'=>$houses])->__toString();
        return $html;
    }




    // 【出租】
    public function view_rent_out_list()
    {
        $items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->paginate(8);
        foreach($items as $item)
        {
            $item->custom = json_decode($item->custom);
            $item->custom2 = json_decode($item->custom2);
        }

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->limit(6)->get();

        $html = view('frontend.entrance.list-for-rent-out')->with(['items'=>$items, 'rent_items'=>$rent_items])->__toString();
        return $html;
    }

    // 【二手批发】
    public function view_second_wholesale_list()
    {
        $items = RootItem::where(['category'=>12, 'active'=>1])->orderby('id', 'desc')->paginate(8);
        foreach($items as $item)
        {
            $item->custom = json_decode($item->custom);
            $item->custom2 = json_decode($item->custom2);
        }

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->limit(6)->get();

        $html = view('frontend.entrance.list-for-second-wholesale')->with(['items'=>$items, 'rent_items'=>$rent_items])->__toString();
        return $html;
    }

    // 【回收】
    public function view_recycle_page()
    {
        $items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->get();
        foreach($items as $item)
        {
            $item->custom = json_decode($item->custom);
            $item->custom2 = json_decode($item->custom2);
        }

        $html = view('frontend.entrance.page-for-recycle')->with(['items'=>$items])->__toString();
        return $html;
    }

    // 【资讯】
    public function view_coverage_list()
    {
        $items = RootItem::where(['category'=>31, 'active'=>1])->orderby('id', 'desc')->paginate(8);
        foreach($items as $item)
        {
            $item->custom = json_decode($item->custom);
            $item->custom2 = json_decode($item->custom2);
        }

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->orderby('id', 'desc')->limit(6)->get();

        $html = view('frontend.entrance.list-for-coverage')->with(['items'=>$items, 'rent_items'=>$rent_items])->__toString();
        return $html;
    }




    // item
    public function view_item($id = 0)
    {
        if($id != 0) $mine = RootItem::where(['id'=>$id])->orderby('id', 'desc')->first();
        else $mine = RootItem::orderby('id', 'desc')->first();

        $mine->timestamps = false;
        $mine->increment('visit_num');

        $mine->custom = json_decode($mine->custom);
        $mine->custom2 = json_decode($mine->custom2);

        $rent_items = RootItem::where(['category'=>11, 'active'=>1])->where('id', '<>', $id)->orderby('id', 'desc')->limit(6)->get();
        foreach($rent_items as $item)
        {
            $item->custom = json_decode($item->custom);
        }

        $html = view('frontend.entrance.item')->with(['rent_items'=>$rent_items, 'item'=>$mine])->__toString();
        return $html;
    }




    // 留言
    public function message_contact($post_data)
    {
        $messages = [
            'name.required' => '请输入姓名',
            'mobile.required' => '请输入电话',
        ];
        $v = Validator::make($post_data, [
            'name' => 'required',
            'mobile' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $post_data['category'] = 1;
            $mine = new RootMessage;
            $bool = $mine->fill($post_data)->save();
            if(!$bool) throw new Exception("insert--message--fail");

            DB::commit();
            $msg = '提交成功！';
            return response_success([],$msg);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '提交失败，请重试！';
//            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



    }


    // ITEM 留言
    public function message_grab_item($post_data)
    {
        $messages = [
            'name.required' => '请输入姓名',
            'mobile.required' => '请输入电话',
        ];
        $v = Validator::make($post_data, [
            'name' => 'required',
            'mobile' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        // 启动数据库事务
        DB::beginTransaction();
        try
        {
            $post_data['category'] = 11;
            $mine = new RootMessage;
            $bool = $mine->fill($post_data)->save();
            if(!$bool) throw new Exception("insert--message--fail");

            DB::commit();
            $msg = '提交成功！';
            return response_success([],$msg);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $msg = '提交失败，请重试！';
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([],$msg);
        }



    }


}