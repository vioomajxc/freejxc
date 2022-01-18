<?php
namespace app\admin\controller;

use think\Session;
use think\Validate;

class Addon extends Base
{ 
    function index()
    {
        return $this->fetch('index');
    }

    public function addonJson()
    {
        $this->not_check = [
            'admin/Addon/addonJson'
        ];
        $param = $this->request->get();
        $where = [];
        $data = model('Addon')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }
}