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
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('盘点仓库和操作员为必填');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'盘点订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
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

    //获取采购列表   
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
            if(empty($post['or_house']) || empty($post['or_user']) || empty($post['or_house1']))$this->error('仓库和操作员为必填');
            if($post['or_house'] == $post['or_house1'])$this->error('原始仓库和目标仓库不能相同');
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
                    'name'=>'调拔订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('调拔单保存成功,刷新浏览器页面');
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

        //获取调拔列表   
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
    //编辑调拔订单
    public function editAllot()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if(empty($post['or_house']) || empty($post['or_user']) || empty($post['or_house1']))$this->error('仓库和操作员为必填');
            if($post['or_house'] == $post['or_house1'])$this->error('原始仓库和目标仓库不能相同');
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
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                //处理原始仓库中的新库存数据
                if(empty($stocks['id'])){ 
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $post['or_house'];
                    $ipost['numbers'] = 0 - $ival['it_number'];
                    StocksModel::insert($ipost);
                }else{
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->setDec('numbers',$ival['it_number']);
                }
                //处理目标仓库中的库存数据
                $tostocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1']])->find();
                if(empty($tostocks))
                StocksModel::insert(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1'],'numbers'=>$ival['it_number']]);//写入目标数据库
            else
                StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house1']])->setInc('numbers',$ival['it_number']);
            }
            Db::name("order")->where('or_id',$post['or_id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'采购订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('调拔单保存成功,刷新浏览器页面');
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

    //新增增溢单
    public function addIncrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('仓库和操作员为必填');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'增溢订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            //$this->success('增溢单保存成功,刷新浏览器页面');
            return [
                'code'=>2,
                'msg'=>'增溢单保存成功,刷新浏览器页面',
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

    //编辑增溢订单
    public function editIncrease()
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
                    'name'=>'增溢单（'.$post['or_id']."）成功保存",
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
            
            //处理审核时库存数据变化,报溢加库存
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //写入新的库存记录
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
                    'name'=>'增溢单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
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


    //获取增溢列表   
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

    //新增损耗单
    public function addDecrease()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['or_house']) || empty($post['or_user']))$this->error('仓库和操作员为必填');
            $postx['or_house'] = $post['or_house'];
            $postx['or_user'] = $post['or_user'];
            $postx['or_money'] = Order::getOrderInfo('orderMoney',$post['or_id']);
            $postx['or_create_time'] = strtotime($post['or_create_time']);
            $postx['or_status'] = 1;
            $postx['or_unique'] = md5(time());
            Order::where('or_id',$post['or_id'])->update($postx);

            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'损耗订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            //$this->success('损耗单保存成功,刷新浏览器页面');
            return [
                'code'=>2,
                'msg'=>'损耗单保存成功,刷新浏览器页面',
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

    //获取损耗列表   
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
    //编辑损耗订单
    public function editDecrease()
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
                    'name'=>'损耗单（'.$post['or_id']."）成功保存",
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
            
            //处理审核时库存数据变化,报溢减库存
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($stocks['id'])){
                    //写入新的库存记录
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
                    'name'=>'损耗单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功,刷新浏览器页面');
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


    //编辑或审核盘点订单
    public function editStocksTake()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $verify_old = $this->request->get("verify_status_old");
            if($verify_old==1 || empty($post['or_verify_status'])){
                //编辑审核后的单据,只修改单据信息
                $vdata['or_verify_user'] = session("username");
                $vdata['or_verify_time'] = time();
                $vdata['or_comment'] = $post['or_comment'];
                Order::where('or_id',$post['or_id'])->update($vdata);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'盘点订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存成功（审核后）,刷新浏览器页面');
            }else{//编辑审核前的单据
            $increaseId = Order::getOrderId('increase');//TODO 报溢单号
            $decreaseId = Order::getOrderId('decrease');//TODO 报损单号
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
            //处理审核时库存数据变化
            $items = Item::where("or_id",$post['or_id'])->select();
            foreach($items as $ival){
                $is_stocks = StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->find();
                if(empty($is_stocks['id'])){
                    //写入新的库存记录,没找到库存记录，可做库存初始化功能使用,并处理报溢
                    $ipost['goods_id'] = $ival['gd_id'];
                    $ipost['house_id'] = $house_id;
                    $ipost['numbers'] = $ival['it_number'];
                    StocksModel::insert($ipost);
                    Item::insert([ 'or_id'=>$increaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$ival['it_number'],'it_price'=>$ival['it_price'] ]);//写入报溢
                    $increaseNum ++;
                }else{//矫正库存数据
                    if($ival['it_number'] > $is_stocks['numbers']){
                        //存在增溢
                        $increase_number = $ival['it_number'] - $is_stocks['numbers'];//增溢数量
                        Item::insert([ 'or_id'=>$increaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$increase_number,'it_price'=>$ival['it_price'] ]);
                        $increaseNum ++;
                    }elseif($ival['it_number'] < $is_stocks['numbers']){
                        //存在损耗
                        $decrease_number = $is_stocks['numbers'] - $ival['it_number'];//损耗数量
                        Item::insert([ 'or_id'=>$decreaseId,'gd_id'=>$ival['gd_id'],'it_number'=>$decrease_number,'it_price'=>$ival['it_price'] ]);
                        $decreaseNum ++;
                    }
                    StocksModel::where(['goods_id'=>$ival['gd_id'],'house_id'=>$post['or_house']])->update(['numbers'=>$ival['it_number']]);//矫正库存数量为盘点实际数量
                }
                //保存增溢单据
                if($increaseNum > 0){
                    $or_money = Order::getOrderInfo('orderMoney',$increaseId);
                    Order::where('or_id',$increaseId)->update(['or_verify_status'=>1,'or_verify_user'=>session('username'),'or_house'=>$post['or_house'],'or_verify_time'=>time(),'or_unique'=>md5(time()),'or_money'=>$or_money,'or_comment'=>'盘点（'.$post['or_id'].'）单自动生成']);
                }
                //保存损耗单据
                if($decreaseNum > 0){
                    $or_money = Order::getOrderInfo('orderMoney',$decreaseId);
                    Order::where('or_id',$decreaseId)->update(['or_verify_status'=>1,'or_verify_user'=>session('username'),'or_house'=>$post['or_house'],'or_verify_time'=>time(),'or_unique'=>md5(time()),'or_money'=>$or_money,'or_comment'=>'盘点（'.$post['or_id'].'）单自动生成']);
                }
            }
            Order::where('or_id',$post['or_id'])->update($postx);//更新盘点单据
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'盘点订单（'.$post['or_id']."）成功保存",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('保存并矫正库存成功,刷新浏览器页面');
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

    //查看/打印订单
    public function seeOrder(){
        $or_id = $this->request->get("or_id");
        $ordertype = $this->request->get("ordertype");
        if($ordertype=="stocks_take")
            $title = "盘点单详情";
        elseif($ordertype=="decrease")
            $title = "损耗单详情";
        elseif($ordertype=="increase")
            $title = "增溢单详情";
            else
            $title = "调拔单详情";
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
            $print_date = date("Y年m月d日",time());
            $delivery = Db::view("user",'id')
            ->view('user_ext','fullname','user.id=userid','LEFT')
            ->where('user.username',$data['or_delivery_id'])
            ->find();
            if($data['or_verify_status']==1)
                $verify_status = "已审";
            else
                $verify_status = "未审";
            $house1 = Order::where('or_id',$or_id)->value('or_house1');
            $house1_name = Db::name('storehouse')->where('id',$house1)->value('house_name');
            if(empty($house1_name))$house1_name = "无";
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


//选择供应
public function selectSupplier()
    {
        return $this->fetch('selectSupplier');
    }
    //选择供应商列表   
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
