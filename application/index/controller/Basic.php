<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\admin\model\UserLog;
use think\Loader;

class Basic extends Main
{
	public function index(){
		$param = $this->request->get();
		
		$site_config = Db::name('system')
                ->where('name','site_config')
                ->value('value');
                $this->assign('site_config',unserialize($site_config));
                $thistime = date("Y-m-d h:i:s",time());
                $shops = Db::view('user','username')
                ->view('user_ext','fullname','user.id=user_ext.userid','LEFT')
                ->view('shop','shop_name','user.shop=shop.id','LEFT')
                ->where('user.username',session("posusername"))
                ->find();
                $usinfo = $shops["shop_name"]." | ".$shops["fullname"];
                $shopname = $shops["shop_name"];
                $categorys = Db::name('category')->where('status',1)->order('sort asc')->select();
                $categorys = array2Level($categorys);
                $goods = Db::view('goods')
                ->view('category','category_name','category=category.id','LEFT')
                ->where('shop',session('shop'))
                ->where('goods.status',1)
                ->select();
                return $this->fetch('index',[
            'thistime' => $thistime,
            'usinfo' => $usinfo,
            'shopname' => $shopname,
            'categorys' => $categorys,
            'goods' => $goods
        ]);
	}

	//商品列表
    public function goodsListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['goodsname'])){
            $where[] =[ 'goodsname','like','%'.$param['goodsname'].'%' ];
        }

        if(!empty($param['start'])){
            $where[] = ['create_time','>=',strtotime($param['start'].' 00:00:00')  ];
        }

        if(!empty($param['end'])){
            $where[] = ['create_time','<=',strtotime($param['end'].' 23:59:59') ];
        }
        $where[] = ['goods.status','=','1'];

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        
        $data = Db::view('Goods','id,goodsname,unit,category,price,create_time,word,status')
            ->view('Category',['category_name'],'goods.category = category.id','LEFT')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }



}
?>