<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\UserLog;
use org\Auth;
use think\Validate;
use think\Db;
use think\Session;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\Storehouse;
use app\admin\model\Shop;
use app\admin\model\Category;
use app\admin\model\Order;
//use app\admin\model\User as UserModel;
use app\admin\model\Stocks as StocksModel;
use think\Hook;

class Storage extends Base
{
    //采购入库列表

public function storageList(){
        return $this->fetch('storageList',['ordertype' => 'procure']);
    }

    public function returnnewList(){
        return $this->fetch('returnnewList',['ordertype'=>'procure_return']);
    }

//获取采购退货列表   
function returnnewListJson(){
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
            $param['limit'] = 10;
        }
            $where[] = ['or_type','=','procure_return'];
            $where[] = ['or_status','=','1'];
        $data = Db::name('order o')->join('storehouse s','o.or_house=s.id')->order('o.id desc')->where($where)->paginate($param['limit']);
        // $data = Db::view('Order','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
        //     ->view('storehouse','house_name','or_house=storehouse.id','LEFT')
        //     ->order('order.id desc')
        //     ->where($where)
        //     ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }
//获取采购列表   
function storageListJson(){
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
            $param['limit'] = 10;
        }
            $where[] = ['or_type','=','procure'];
            $where[] = ['or_status','=','1'];

        $data = Db::name('order o')->join('storehouse s','o.or_house=s.id')->order('o.id desc')->where($where)->paginate($param['limit']);
        // $data = Db::view('Order','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
        //     ->view('storehouse','house_name','or_house=storehouse.id','LEFT')
        //     ->order('order.id desc')
        //     ->where($where)
        //     ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

      //添加采购入库

public function addStorage()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            
            $post['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_unique'] = md5(time());
            Db::name("order")
            ->where('or_id',$post['or_id'])
            ->update($post);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        $or_id = Order::getOrderId('procure');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('addStorage',[
'or_id' => $or_id,
'ctime' => $ctime,
'supplier' => $supplier,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }
//新增采购退货
    public function returnNew()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $post['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_unique'] = md5(time());
            Db::name("order")
            ->where('or_id',$post['or_id'])
            ->update($post);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'采购退货订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        $or_id = Order::getOrderId('procure_return');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('returnNew',[
'or_id' => $or_id,
'ctime' => $ctime,
'supplier' => $supplier,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    //编辑采购订单
    public function editStorage()
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
                    'name'=>'采购订单（'.$post['or_id']."）成功保存",
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
            }
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'采购订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editStorage',[
'or_id' => $or_id,
'orders' => $orders,
'supplier' => $supplier,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

    //编辑采购退货订单
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
                    'name'=>'采购退货订单（'.$post['or_id']."）成功保存",
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
                    $ipost['numbers'] = 0 - $ival['it_number'];
                    Db::name("stocks")->insert($ipost);
                }else{
                    Db::name("stocks")->where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setDec('numbers',$ival['it_number']);
                }
            }
            Db::name("order")->where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'采购退货订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editReturn',[
'or_id' => $or_id,
'orders' => $orders,
'supplier' => $supplier,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

//采购单临时列表   
function addstorageListJson(){
        $param = $this->request->get();
        $where = [];
        
        $where[] = ['or_type','=','procure'];
        $where[] = ['or_status','=','1'];
        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        $data = Db::name('Order')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }


    //查看/打印订单
    public function seeOrder(){
        //通用单据详情
        $or_id = $this->request->get("or_id");
        $ordertype = $this->request->get("ordertype");
        if($ordertype=="procure")
            $title = "采购订单信息";
        else
            $title ="采购退货单信息";
        $data = Db::view("order",'*')
        ->view("user",['username'],'order.or_user=user.username','LEFT')
        ->view("user_ext",['fullname'],'user_ext.userid=user.id','LEFT')
        ->view("storehouse",'house_name','or_house=storehouse.id','LEFT')
        ->view('supplier','supplier_name','or_contact=supplier.id','LEFT')
        ->where('order.or_id','=',$or_id)
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
