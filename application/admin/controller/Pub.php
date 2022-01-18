<?php
namespace app\admin\controller;
// +----------------------------------------------------------------------
// | 通用操作控制器
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: xingma <qq 12612019>
// +----------------------------------------------------------------------
use think\Controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\UserLog;
use org\Auth;
use think\Validate;
use think\Db;
use think\Session;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\Order;
use app\admin\model\Item;
use app\admin\model\Goods;
use think\Hook;

class Pub extends Base
{
	//选择商品
    function selectGoods(){
        $get = $this->request->get();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        return $this->fetch('selectgoodsList',[
            'supplier' => $supplier,
            'or_id' => $get['or_id'],
            'ordertype' => $get['ordertype']
        ]);
    }

    //选择商品列表
    public function selectgoodsListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['goodsname'])){
            $where[] =[ 'goodsname','like','%'.$param['goodsname'].'%' ];
        }
        if(!empty($param['contact'])){
            $where[] =[ 'contact','=',$param['contact'] ];
        }
        $where[] = ['status','=','1'];
        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
        
        $data = Db::name('Goods','id,goodsname,status,unit,category,min_pack,lead_time,word,spec')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }

    //增加单个商品
    public function selectsingleGoods()
    {
        $request = $this->request;
        $gd_id = $this->request->post('id');//商品ID
        $or_id = $this->request->post('or_id');
        $ordertype = $this->request->get('ordertype');//获取订单类型
                $db = Db::name('item')->field('id')->where('gd_id',$gd_id)->where('or_id',$or_id)->find();
                $data = Db::name("goods")->where("id",$gd_id)->find();
                    $datas['or_id'] = $or_id;
                    $datas['gd_id'] = $gd_id;
                    $datas['it_number'] = 1;
                    if($ordertype=="procure"||$ordertype=="procure_return"||$ordertype=="stocks_take"||$ordertype=="increase"||$ordertype=="decrease")
                    	$datas['it_price'] = $data['cost'];
                    else
                    	$datas['it_price'] = $data['price'];
                    $datas['it_discount'] = 10;
                if($db){//找到了记录
                    Item::where('gd_id', $gd_id)->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Item::where('gd_id', $gd_id)->where('or_id',$or_id)->insert($datas);
                }
                $this->success('添加成功');

    }

    //批量增加商品
    public function selectBatchGoods()
    {
        $request = $this->request;
        $or_id = $this->request->get('or_id');//订单号
        $ordertype = $this->request->get('ordertype');//单据类型
        $data = $this->request->post('data');
        foreach($data as $key=>$dval){
            $gd_id = $dval["id"];
            $db = Item::field('id')->where('gd_id',$gd_id)->where('or_id',$or_id)->find();
                $data = Db::name("goods")->where("id",$gd_id)->find();
                    $datas['or_id'] = $or_id;
                    $datas['gd_id'] = $gd_id;
                    $datas['it_number'] = 1;
                    if($ordertype=="procure"||$ordertype=="procure_return"||$ordertype=="stocks_take"||$ordertype=="increase"||$ordertype=="decrease")
                    	$datas['it_price'] = $data['cost'];
                    else
                    	$datas['it_price'] = $data['price'];
                    $datas['it_discount'] = 10;
                if($db){//找到了记录
                    Item::where('gd_id', $gd_id)->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Item::where('gd_id', $gd_id)->where('or_id',$or_id)->insert($datas);
                }
        }
                $this->success('添加成功');

    }

    //删除订单中选择的商品
    public function delitemGoods()
    {
        $id = $this->request->post('id');
                $db = Item::where('id', $id)->delete();
                $this->success('删除成功');
    }

    //删除订单中商品
    public function delitemallGoods()
    {
        $or_id = $this->request->post('or_id');
                $db = Item::where('or_id', $or_id)->delete();
                $this->success('删除成功');
    }

    //修改订单中商品数量、价格等数据
    public function changeItemValue(){
       $request = $this->request;
        $id = $this->request->post('id');//数据记录ID值
        $val = $this->request->post('val');//取得的值
        $field = $this->request->post('field');//字段名称
        eval("\$data['".$field."'] = \"$val\";");
        Item::where('id',$id)->update($data);
        $this->success('修改成功');
    }

    //单据项目列表   
    public function ItemJson(){
    $or_id = $this->request->get('or_id');
        $where = [];       
        $where[] = ['or_id','=',$or_id];
        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
        $data = Db::view('Item i','id,or_id,it_number,it_price,it_discount')
            ->view('goods g','goodsname,spec,unit','g.id=i.gd_id','LEFT')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }

    //删除单据   
    public function delOrder(){
        $or_id = $this->request->post('or_id');
        if($or_id=='0' || empty($or_id))$this->error('订单号参数错误');
            Order::where('or_id', $or_id)->delete();
            Item::where('or_id',$or_id)->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'单据（'.$or_id.'）删除成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('单据（'.$or_id.'）删除成功');
        
    }

    //审核订单 @id 按单号进行操作
    public function verifyOrder()
    {
        $id = $this->request->post('id');
        $ordertype = $this->request->get('ordertype');
        $order = Order::where("or_id",$id)->find();
        if($id!=""){
            //处理库存数据
            $items = Item::where("or_id",$id)->select();
            foreach($items as $ival){
                $stocks = Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$order['or_house']])->find();
                if(empty($stocks['id'])){
                    //写入新的库存记录
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $order['or_house'];
                    if($ordertype=='dec')
                    $ipost['numbers'] = 0 - $ival['it_number'];
                else
                	$ipost['numbers'] = $ival['it_number'];
                    Db::name("stocks")->insert($ipost);
                }else{
                	if($ordertype=='dec')
                		Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$order['or_house']])->setDec('numbers',$ival['it_number']);
                		else
                    Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$order['or_house']])->setInc('numbers',$ival['it_number']);
                }
                if($order['or_type']=='sales' || $order['or_type']=='sales_return'){
                if($ordertype=='dec')
                    Goods::where('id',$ival['gd_id'])->setDec('sales',$ival['it_number']);//商品销量减
                else
                    Goods::where('id',$ival['gd_id'])->setInc('sales',$ival['it_number']);//商品销量加
                }
            }
                Order::where('or_id', $id)
                    ->update([
                        'or_verify_status' => '1',
                        'or_verify_time' => time(),
                        'or_verify_user' => session("username")
                    ]);
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'审核单据（:'.$id.'）成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('审核（:'.$id.'）成功');
            }else{
                $this->error('指定的审核参数无效');
            }
    }

    //完成订单 @id 根据单据id进行操作
    public function finishOrder()
    {
        $id = $this->request->post('id');
        $ordertype = $this->request->get('ordertype');
        $verify = Order::getValuebyId($id,'or_verify_status');
        $or_id = Order::getValuebyId($id,'or_id');
        if($verify==0)
            $this->error("单据还未审核，不允许完成此单据");
        if($id>0){
            //写财务数据
            switch($ordertype){
            	case "collection"://进账
            	$ftype = 1;
            	$freason = '订单'.$or_id.'收款';
            	break;
            	case "pay"://出账
            	$ftype = 0;
            	$freason = '订单'.$or_id.'付款';
            	break;
            }
            $f = [
                'f_type' => $ftype,
                'f_money' => Order::getOrderInfo('orderMoney',$or_id),
                'f_reason' => $freason,
                'f_username' => session('username'),
                'f_time' => time(),
                'f_channel' => 1,
                'f_come' => 1,
                'handover' => 1,
                'handtime' => time()
            ];
            Db::name('financial_details')->insert($f);
                $db = Order::where('id', $id)
                    ->update([
                        'or_finish' => '1',
                        'or_paied' => '1'
                    ]);
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'完成单据（ID:'.$id.'）成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('设置成功');
            }else{
                $this->error('指定的设置参数无效');
            }
    }

    //处理扫码枪操作
    public function barcodeScan(){
        $post = $this->request->post();
        $sku = $post['sku'];
        $or_id = $post['or_id'];
        $ordertype = $this->request->get('ordertype');
        $where = [];
        $goods = Db::name("goods")->where('barcode',$sku)->field('id,cost,price')->find();
        if(!$goods['id'])
            $this->error("无此产品，请重试");
        $where[] = ['or_id','=',$or_id];
        $where[] = ['gd_id','=',$goods['id']];
        $db = Item::where($where)->field('id')->find();
        $postx['or_id'] = $or_id;
        $postx['gd_id'] = $goods['id'];
        $postx['it_number'] = 1;
        if($ordertype == 'stocks_take' || $ordertype == 'procure' || $ordertype == 'procure_return')
        	$postx['it_price'] = $goods['cost'];
        else
            $postx['it_price'] = $goods['price'];
        $postx['it_discount'] = 10;
        if($db){//找到了记录
                    Item::where('gd_id', $goods['id'])->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Item::where('gd_id', $goods['id'])->where('or_id',$or_id)->insert($postx);
                }
                $res = [
            'code'=>1,
            'msg'=>'添加成功'
                ];
                return $res;
    }
}