<?php
namespace app\admin\controller;
// +----------------------------------------------------------------------
// | LotusAdmin
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wenhainan <qq 610176732>
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
use app\admin\model\Storehouse;
use app\admin\model\Shop;
use app\admin\model\Category;
use app\admin\model\Order;
use app\admin\model\Item;
use app\admin\model\Stocks as StocksModel;
use think\Hook;

class Stocks extends Base
{

public function stockslist()
    {
        $storehouse = Db::name('storehouse')
                ->field('id,house_name')
                ->where('status',1)
                ->order('id desc')
                ->select();
        $category = Category::order(['sort' => 'asc'])->select();
        $category = array2Level($category);
        return $this->fetch('stocksList',[
            'category' => $category,
            'storehouse' => $storehouse
        ]);
    }

    public function stocksListJson()
    {
        $param = $this->request->get();
        $where = [];

        if(isset($param['category'])){
            $where[] = ['goods.category','>=',$param['category'] ];
        }

        if(isset($param['house_id'])){
            $where[] = ['stocks.house_id','>=',$param['house_id'] ];
        }

        if(!empty($param['goodsname'])){
            $where[] =[ 'goods.goodsname','like','%'.$param['goodsname'].'%' ];
        }

        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
        
        
        $data = Db::view('Stocks s','id,numbers,contact')
            ->view('Storehouse t',['house_name'],'t.id = s.house_id','LEFT')
            ->view('Goods g',['goodsname,unit,price'],'g.id = s.goods_id','LEFT')
            ->view('Category c',['category_name'],'c.id = g.category','LEFT')
            ->view('Shop h',['shop_name'],'t.shop = h.id','LEFT')
            ->order('s.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    public function stocksTake()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('?????????????????????????????????');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????');
        }
        $or_id = Order::getOrderId('stocks_take');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('stocksTake',[
'or_id' => $or_id,
'ctime' => $ctime,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    public function stockstakeList(){
        return $this->fetch('stockstakeList',['ordertype' => 'stocks_take']);
    }

    //??????????????????   
function stockstakeListJson(){
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
            $where[] = ['or_type','=','stocks_take'];
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('storehouse s','house_name','o.or_house=s.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    public function allot()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']) || empty($post['or_house1']))$this->error('???????????????????????????');
            if($post['or_house'] == $post['or_house1'])$this->error('???????????????????????????????????????');
            $postx['or_house'] = $post['or_house'];
            $postx['or_house1'] = $post['or_house1'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('?????????????????????,?????????????????????');
        }
        $or_id = Order::getOrderId('allot');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('allot',[
'or_id' => $or_id,
'ctime' => $ctime,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    public function allotList(){
        return $this->fetch('allotList',['ordertype' => 'allot']);
    }

        //??????????????????   
    function allotListJson(){
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
            $where[] = ['or_type','=','allot'];
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('storehouse s','house_name','o.or_house=s.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }
    //??????????????????
    public function editAllot()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']) || empty($post['or_house1']))$this->error('???????????????????????????');
            if($post['or_house'] == $post['or_house1'])$this->error('???????????????????????????????????????');
            if($verify_old==1 || empty($post['or_verify_status'])){
                //????????????????????????
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('???????????????????????????,?????????????????????');
            }else{//????????????????????????
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_verify_user'] = session("username");
            $post['or_verify_time'] = time();
            $post['or_comment'] = $post['or_comment'];
            
            //?????????????????????????????????
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                //???????????????????????????????????????
                if(empty($stocks['id'])){ 
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = 0 - $ival['it_number'];
                    StocksModel::insert($ipost);
                }else{
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setDec('numbers',$ival['it_number']);
                }
                //????????????????????????????????????
                $tostocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1']])->find();
                if(empty($tostocks))
                StocksModel::insert(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1'],'numbers'=>$ival['it_number']]);//?????????????????????
            else
                StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1']])->setInc('numbers',$ival['it_number']);
            }
            Db::name("order")->where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('?????????????????????,?????????????????????');
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
        return $this->fetch('editAllot',[
'or_id' => $or_id,
'orders' => $orders,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

    public function increaseList(){
        return $this->fetch('increaseList',['ordertype' => 'increase']);
    }

    //???????????????
    public function addIncrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('???????????????????????????');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            //$this->success('?????????????????????,?????????????????????');
            return [
                'code'=>2,
                'msg'=>'?????????????????????,?????????????????????',
                'url'=>'/admin/stocks/increaseList'
            ];
        }
        $or_id = Order::getOrderId('increase');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('increase',[
'or_id' => $or_id,
'ctime' => $ctime,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    //??????????????????
    public function editIncrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('???????????????????????????');
            if($verify_old==1 || empty($post['or_verify_status'])){
                //????????????????????????
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('???????????????????????????,?????????????????????');
            }else{//????????????????????????
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_verify_user'] = session("username");
            $post['or_verify_time'] = time();
            $post['or_comment'] = $post['or_comment'];
            
            //?????????????????????????????????,???????????????
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //????????????????????????
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = $ival['it_number'];
                    StocksModel::insert($ipost);
                }else{
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setInc('numbers',$ival['it_number']);
                }
            }
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editIncrease',[
'or_id' => $or_id,
'orders' => $orders,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }


    //??????????????????   
    function increaseListJson(){
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
            $where[] = ['or_type','=','increase'];
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('storehouse s','house_name','o.or_house=s.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    //???????????????
    public function addDecrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('???????????????????????????');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            //$this->success('?????????????????????,?????????????????????');
            return [
                'code'=>2,
                'msg'=>'?????????????????????,?????????????????????',
                'url'=>'/admin/stocks/decreaseList'
            ];
        }
        $or_id = Order::getOrderId('decrease');
        $ctime = date("Y-m-d",time());
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $curUser = session("username");
        return $this->fetch('decrease',[
'or_id' => $or_id,
'ctime' => $ctime,
'user' => $user,
'house' => $house,
'curUser' => $curUser
        ]);
    }

    public function decreaseList(){
        return $this->fetch('decreaseList',['ordertype' => 'decrease']);
    }

    //??????????????????   
    function decreaseListJson(){
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
            $where[] = ['or_type','=','decrease'];
            $where[] = ['or_status','=','1'];

        $data = Db::view('Order o','id,or_id,or_contact,or_user,or_house,or_money,or_finish,or_verify_status,or_create_time')
            ->view('storehouse s','house_name','o.or_house=s.id','LEFT')
            ->order('o.id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }
    //??????????????????
    public function editDecrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('???????????????????????????');
            if($verify_old==1 || empty($post['or_verify_status'])){
                //????????????????????????
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('???????????????????????????,?????????????????????');
            }else{//????????????????????????
            $post['or_create_time'] = strtotime($post['or_create_time']);
            $post['or_status'] = 1;
            $post['or_verify_user'] = session("username");
            $post['or_verify_time'] = time();
            $post['or_comment'] = $post['or_comment'];
            
            //?????????????????????????????????,???????????????
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //????????????????????????
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = 0 - $ival['it_number'];
                    StocksModel::insert($ipost);
                }else{
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setDec('numbers',$ival['it_number']);
                }
            }
            Order::where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editDecrease',[
'or_id' => $or_id,
'orders' => $orders,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }


    //???????????????????????????
    public function editStocksTake()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if($verify_old==1 || empty($post['or_verify_status'])){
                //????????????????????????,?????????????????????
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('???????????????????????????,?????????????????????');
            }else{//????????????????????????
            $increaseId = Order::getOrderId('increase');//TODO ????????????
            $decreaseId = Order::getOrderId('decrease');//TODO ????????????
            $increaseNum = $decreaseNum = 0;
            $house_id = $post['or_house'];
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_verify_user'] = session("username");
            $postx['or_verify_time'] = time();
            $postx['or_verify_status'] = 1;
            $postx['or_comment'] = $post['or_comment'];
            $postx['or_house'] = $house_id;
            $postx['or_paied'] = 1;
            $postx['or_finish'] = 1;
            //?????????????????????????????????
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $is_stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($is_stocks['id'])){
                    //????????????????????????,?????????????????????????????????????????????????????????,???????????????
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $house_id;
                    $ipost['numbers'] = $ival['it_number'];
                    StocksModel::insert($ipost);
                    Item::insert([ 'or_id'=>$increaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$ival['it_number'],'it_price'=>$ival['it_price'] ]);//????????????
                    $increaseNum ++;
                }else{//??????????????????
                    if($ival['it_number'] > $is_stocks['numbers']){
                        //????????????
                        $increase_number = $ival['it_number'] - $is_stocks['numbers'];//????????????
                        Item::insert([ 'or_id'=>$increaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$increase_number,'it_price'=>$ival['it_price'] ]);
                        $increaseNum ++;
                    }elseif($ival['it_number'] < $is_stocks['numbers']){
                        //????????????
                        $decrease_number = $is_stocks['numbers'] - $ival['it_number'];//????????????
                        Item::insert([ 'or_id'=>$decreaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$decrease_number,'it_price'=>$ival['it_price'] ]);
                        $decreaseNum ++;
                    }
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->update(['numbers'=>$ival['it_number']]);//???????????????????????????????????????
                }
                //??????????????????
                if($increaseNum > 0){
                    $or_money = Order::getOrderInfo('orderMoney',$increaseId);
                    Order::where('or_id',$increaseId)->update(['or_verify_status'=>1,'or_verify_user'=>session('username'),'or_house'=>$post['or_house'],'or_verify_time'=>time(),'or_unique'=>md5(time()),'or_money'=>$or_money,'or_comment'=>'?????????'.$post['or_id'].'??????????????????']);
                }
                //??????????????????
                if($decreaseNum > 0){
                    $or_money = Order::getOrderInfo('orderMoney',$decreaseId);
                    Order::where('or_id',$decreaseId)->update(['or_verify_status'=>1,'or_verify_user'=>session('username'),'or_house'=>$post['or_house'],'or_verify_time'=>time(),'or_unique'=>md5(time()),'or_money'=>$or_money,'or_comment'=>'?????????'.$post['or_id'].'??????????????????']);
                }
            }
            Order::where('or_id',$post['or_id'])->update($postx);//??????????????????
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['or_id']."???????????????",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('???????????????????????????,?????????????????????');
        }
        }
        
        $or_id = $this->request->get("or_id");
        $orders = Db::view("order")->where('or_id',$or_id)->find();
        $house = Db::name("storehouse")->where('status','=','1')->select();
        $user = Db::view("user",'username')
        ->view("user_ext",'fullname','user.id=user_ext.userid','LEFT')
        ->where('status',1)
        ->select();
        $orders['or_create_time'] = date('Y-m-d',$orders['or_create_time']);
        $verify_status = $orders['or_verify_status'];
        return $this->fetch('editStocksTake',[
'or_id' => $or_id,
'orders' => $orders,
'user' => $user,
'house' => $house,
'verify_status' => $verify_status
        ]);
    }

    //??????/????????????
    public function seeOrder(){
        $or_id = $this->request->get("or_id");
        $ordertype = $this->request->get("ordertype");
        if($ordertype=="stocks_take")
            $title = "???????????????";
        elseif($ordertype=="decrease")
            $title = "???????????????";
        elseif($ordertype=="increase")
            $title = "???????????????";
            else
            $title = "???????????????";
        $data = Db::view("order o",'*')
        ->view("user u",['username'],'o.or_user=u.username','LEFT')
        ->view("user_ext ue",['fullname'],'ue.userid=u.id','LEFT')
        ->view("storehouse s",'house_name','o.or_house=s.id','LEFT')
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
            $print_date = date("Y???m???d???",time());
            $delivery = Db::view("user",'id')
            ->view('user_ext','fullname','user.id=userid','LEFT')
            ->where('user.username',$data['or_delivery_id'])
            ->find();
            if($data['or_verify_status']==1)
                $verify_status = "??????";
            else
                $verify_status = "??????";
            $house1 = Order::where('or_id',$or_id)->value('or_house1');
            $house1_name = Db::name('storehouse')->where('id',$house1)->value('house_name');
            if(empty($house1_name))$house1_name = "???";
        return $this->fetch('seeOrder',[
'or_id' => $or_id,
'data' => $data,
'items' => $items,
'totals' => $totals,
'verify_status' => $verify_status,
'delivery' => $delivery['fullname'],
'print_date' => $print_date,
'title' => $title,
'house1_name' => $house1_name
        ]);
    }


//????????????
public function selectSupplier()
    {
        return $this->fetch('selectSupplier');
    }
    //?????????????????????   
function selectsupplierJson(){
        $param = $this->request->get();
        $where = [];

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }

        $data = Db::name('Supplier')
            ->field('id,supplier_name,supplier_director,supplier_phone,status')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }



 

}
