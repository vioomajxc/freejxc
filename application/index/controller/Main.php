<?php
namespace app\index\controller;

use think\Controller;

class Main extends  Controller
{
    function  initialize()
    {
        parent::initialize();
        $module     = $this->request->module();
        if (!lotus_is_installed() && $module != 'install') {
            header('Location: ' . lotus_get_root() . 'index.php/?s=install');
            exit;
        }
        $posuser = session('posusername');
        if(empty($posuser)){
            $this->redirect('index/Login/login');
        }
    }

    public function  genTableJson($paginationObj,$code=0,$msg=''){
        $total = $paginationObj->total();
        $items = $paginationObj->items();
        $res = [
            'code'=>$code,
            'msg'=>$msg,
            'count'=>$total,
            'data'=>$items
        ];
        echo json_encode($res);exit;
    }

}