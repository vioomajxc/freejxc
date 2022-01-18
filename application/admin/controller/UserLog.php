<?php
// +----------------------------------------------------------------------
// | LotusAdmin
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wenhainan <qq 610176732>
// +----------------------------------------------------------------------
namespace app\admin\controller;


use org\Auth;
use think\Db;
use think\Session;
use think\Validate;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\User as UserModel;
class UserLog extends Base
{
    function index()
    {
        return $this->fetch('index');
    }
    public function userLogListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['username'])){
            $where[] =[ 'username','like','%'.$param['username'].'%' ];
        }

        if(!empty($param['start'])){
            $where[] = ['a.create_time','>=',strtotime($param['start'].' 00:00:00') ];
        }

        if(!empty($param['end'])){
            $where[] = ['a.create_time','<=',strtotime($param['end'].' 23:59:59') ];
        }

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        $data = model('UserLog')
            ->alias('a')
            ->order('id desc')
            ->where($where)
            ->join('user b','a.uid=b.id','left')
            ->field('a.*,b.username')
            ->paginate($param['limit']);

        foreach ($data->items() as &$value){
            if(empty($value['username'])){
                $value['username'] = '匿名';
            }
        }

        $this->genTableJson($data);
    }
}