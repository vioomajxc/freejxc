<?php
namespace app\admin\controller;

use think\facade\Hook;
use think\Controller;
use think\Db;
use app\admin\model\Order;
use app\admin\model\UserLog;
use app\admin\model\Goods;
use app\admin\model\Shop;

class Sales extends  Base
{
    public function addSales()
    {//新建销售单
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $post['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        $or_id = Order::getOrderId('sales');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $members = Db::name("member")->where('member_status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('addSales',[
'or_id' => $or_id,
'ctime' => $ctime,
'members' => $members,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    public function addReturn()
    {//新建销售退货单
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $post['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        $or_id = Order::getOrderId('sales_return');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $members = Db::name("member")->where('member_status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('addReturn',[
'or_id' => $or_id,
'ctime' => $ctime,
'members' => $members,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    //编辑销售订单
    public function editSales()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('仓库和操作员为必填');
            if($verify_old==1 || empty($post['or_verify_status'])){
                //编辑审核后的单据
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功（审核后）,刷新浏览器页面');
            }else{//编辑审核前的单据
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_verify_user'] = session("username");
            $post['or_verify_time'] = time();
            $post['or_comment'] = $post['or_comment'];
            
            //处理审核时库存数据变化
            $items = Db::name("item")->where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //写入新的库存记录
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = 0-$ival['it_number'];
                    Db::name("stocks")->insert($ipost);
                }else{
                    Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setDec('numbers',$ival['it_number']);
                }
                Goods::where('id',$ival['gd_id'])->setInc('sales',$ival['it_number']);//商品销量
            }
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $members = Db::name("member")->where('member_status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editSales',[
'or_id' => $or_id,
'orders' => $orders,
'members' => $members,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

    //编辑销售退货订单
    public function editReturn()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('仓库和操作员为必填');
            if($verify_old==1 || empty($post['or_verify_status'])){
                //编辑审核后的单据
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Db::name("order")->where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售退货订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功（审核后）,刷新浏览器页面');
            }else{//编辑审核前的单据
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_verify_user'] = session("username");
            $post['or_verify_time'] = time();
            $post['or_comment'] = $post['or_comment'];
            
            //处理审核时库存数据变化
            $items = Db::name("item")->where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //写入新的库存记录
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = $ival['it_number'];
                    Db::name("stocks")->insert($ipost);
                }else{
                    Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setInc('numbers',$ival['it_number']);
                }
                Goods::where('id',$ival['gd_id'])->setDec('sales',$ival['it_number']);//商品销量减少
            }
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'销售退货单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $members = Db::name("member")->where('member_status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editReturn',[
'or_id' => $or_id,
'orders' => $orders,
'members' => $members,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

    //销售单列表
    public function salesList()
    {
        $shops = Shop::where('status',1)->select();
        return $this->fetch('salesList',['ordertype'=>'sales','shops'=>$shops]);
    }

    //销售单列表   
    public function salesListJson(){
        $param = $this->request->get();
        $where = [];
        if(!empty($param['or_id'])){
            $where[] =[ 'or_id','like','%'.$param['or_id'].'%' ];
        }
        if(!empty($param['or_shop'])){
            $where[] =[ 'or_shop','=', $param['or_shop'] ];
        }
        if(!empty($param['start'])){
            $where[] = ['or_create_time','>=',strtotime($param['start'].' 00:00:00') ];
        }
        if(!empty($param['end'])){
            $where[] = ['or_create_time','<=',strtotime($param['end'].' 23:59:59') ];
        }
        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('member m','member_name','o.or_contact=m.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->where("o.or_type='sales' or o.or_type='pos'")
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    //销售单列表
    public function returnList()
    {
        return $this->fetch('returnList',['ordertype'=>'sales_return']);
    }

    //销售单列表   
    public function returnListJson(){
        $param = $this->request->get();
        $where = [];
        if(!empty($param['or_id'])){
            $where[] =[ 'or_id','like','%'.$param['or_id'].'%' ];
        }
        if(!empty($param['start'])){
            $where[] = ['or_create_time','>=',strtotime($param['start'].' 00:00:00') ];
        }
        if(!empty($param['end'])){
            $where[] = ['or_create_time','<=',strtotime($param['end'].' 23:59:59') ];
        }
        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
            $where[] = ['or_type','=','sales_return'];
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('member m','member_name','o.or_contact=m.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    //查看/打印订单
    public function seeOrder(){
        $or_id = $this->request->get("or_id");
        $ordertype = $this->request->get("ordertype");
        if($ordertype=="sales")
            $title = "销售单详情";
        else
            $title = "销售退货详情";
        $data = Db::view("order o",'*')
        ->view("user u",['username'],'o.or_user=u.username','LEFT')
        ->view("user_ext ue",['fullname'],'ue.userid=u.id','LEFT')
        ->view("storehouse s",'house_name','o.or_house=s.id','LEFT')
        ->view('member m','member_name','o.or_contact=m.id','LEFT')
        ->where('o.or_id','=',$or_id)
        ->limit(1)
        ->find();
        $data['or_create_time'] = date('Y-m-d',$data['or_create_time']);
        $items = Db::view('item')
        ->view('goods','goodsname,spec,unit','gd_id=goods.id')
            ->order('item.id asc')
            ->where('or_id','=',$or_id)
            ->select();
            $totals = Db::name("item")
            ->field('sum(it_number) as numbers,sum(it_number*it_price) as moneys')
            ->where('or_id','=',$or_id)
            ->select();
            $print_date = date("Y年m月d日",time());
            $delivery = Db::view("user",'id')
            ->view('user_ext','fullname','user.id=userid','LEFT')
            ->where('user.username',$data['or_delivery_id'])
            ->find();
            if($data['or_verify_status']==1)
                $verify_status = "已审";
            else
                $verify_status = "未审";
        return $this->fetch('seeOrder',[
'or_id' => $or_id,
'data' => $data,
'items' => $items,
'totals' => $totals,
'verify_status' => $verify_status,
'delivery' => $delivery['fullname'],
'print_date' => $print_date,
'title' => $title
        ]);
    }

}
