<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    //获取活动列表
    public function apiActivityList(Request $request){

        $openid = $request -> input('openid');
        $res_make = DB::table('activity') -> where([
            'openid' => $openid,
            'flag' => 0
        ]) -> get();
        return response() -> json($res_make);

    }
    //添加活动
    public function apiAddActivity(Request $request){
        $id_res = DB::table('activity') -> insertGetId([
            'title' => $request -> input('title'),
            'content' => $request -> input('content'),
            'xiaoyou_id' => $request -> input('xiaoyou_id'),
            'date' => $request -> input('date'),
            'time' => $request -> input('time'),
            'address' => $request -> input('address'),
            'openid' => $request -> input('openid'),
            'baoming' => 1,
            'created_at' => time()
        ]);

        //添加报名信息
        DB::table('baoming') -> insert([
            'huodong_id' => $id_res,
            'openid' => $request -> input('openid'),
            'created_at' => time()
        ]);


        if($id_res){
            echo 'success';
        }else{
            echo 'error';
        }

    }

    //活动详情
    public function apiActivityDetail(Request $request){
        $res = DB::table('activity') -> where([
            'id' => $request -> input('id')
        ]) -> first();
        if($res){
            $res -> userinfo = DB::table('user') -> where([
                'openid' => $res -> openid
            ]) -> first();
            $res -> xiaoyouinfo = DB::table('xiaoyouhui') -> where([
                'id' => $res -> xiaoyou_id
            ]) -> first();
            //返回此人是否报名过
            if($request -> input('openid')){
                $temp = DB::table('baoming') -> where([
                    'huodong_id' => $request -> input('id'),
                    'openid' => $request -> input('openid')
                ]) -> first();
                if($temp){
                    $res -> is_baoming = 1;
                }
            }
            $res -> baominguser = DB::table('baoming')
                ->leftJoin('user', 'users.openid', '=', 'baoming.openid')
                -> where([
                'baoming.huodong_id' => $request -> input('id')
            ]) -> get();
            return response() -> json($res);
        }else{
            echo 'error';
        }
    }

    //编辑活动
    public function apiEditActivity(Request $request){
        $res = DB::table('activity') -> where([
            'id' => $request -> input('id')
        ]) -> update([
            'title' => $request -> input('title'),
            'content' => $request -> input('content'),
            'xiaoyou_id' => $request -> input('xiaoyou_id'),
            'date' => $request -> input('date'),
            'time' => $request -> input('time'),
            'address' => $request -> input('address'),
            'openid' => $request -> input('openid'),
        ]);
    }


    //活动报名
    public function apiBaoming(Request $request){
        //先查下他有没有报名
        $isset = DB::table('baoming') -> where([
            'huodong_id' => $request -> input('huodong_id'),
            'openid' => $request -> input('openid'),
        ]) -> first();
        if(!$isset){
            echo 'isset';exit;
        }
        $res = DB::table('baoming') -> insert([
            'huodong_id' => $request -> input('huodong_id'),
            'openid' => $request -> input('openid'),
            'openid_yaoqing' => $request -> input('openid_yaoqing'),
        ]);
        //活动报名人数+1
        DB::table('activity') -> increment('baoming');
        echo 'success';
    }

    public function index(){
        echo '正在开发中';
    }


}
