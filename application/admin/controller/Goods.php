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
use app\admin\model\Category;
use app\admin\model\Mcategory;
use app\admin\model\Supplier;
use app\admin\model\Member;
//use app\admin\model\User as UserModel;
use app\admin\model\Goods as GoodsModel;
use think\Hook;

class Goods extends Base
{


    public function goodslist()
    {
        return $this->fetch('goodsList');
    }

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

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        $data = Db::name('goods g')->join('category c','g.category=c.id')->order('g.id desc')->where($where)->paginate($param['limit']);
        
        //$data = Db::view('Goods','id,goodsname,unit,category,price,create_time,word,status')
        //    ->view('Category',['category_name'],'goods.category = category.id','LEFT')
        //    ->order('id desc')
        //    ->where($where)
        //    ->paginate($param['limit']);
        
        // $data = Db::name('Goods')
        //     ->order('id desc')
        //     ->where($where)
        //     ->field('id,goodsname,unit,category,cost,price,word')
        //     ->paginate($param['limit']);
        $this->genTableJson($data);
    }

    //店铺状态
    public function shop_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("参数错误");
        Db::name('shop')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '禁用成功':'解禁成功'
        ];
    }

    //产品状态
    public function goods_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("参数错误");
        Db::name('goods')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '禁用成功':'解禁成功'
        ];
    }
    //产品状态
    public function supplier_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("参数错误");
        Db::name('supplier')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '禁用成功':'解禁成功'
        ];
    }

    //增加商品
    public function addGoods()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            if($post['create_time']=="")
                 $post['create_time']   = strtotime($post['create_time']);
                 //$post['lead_time']  = strtotime($post['lead_time']);
                 $post['word']     = pinyin_long($post['goodsname']);
                
                 Db::name('goods')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加商品'.$post['goodsname'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('success');
        }else{
            $auth_group = Db::name('auth_group')
                ->field('id,title')
                ->where('status',1)
                ->order('id desc')
                ->select();
        $category = Category::order(['sort' => 'asc'])->select();
        $category = array2Level($category);
        $supplier = Supplier::where('status','=','1')->order(['id' => 'desc'])->select();
        $ctime = date("Y-m-d",time());
            return $this->fetch('add',
                [
                    'auth_group'=>$auth_group,
                    'category'=>$category,
                    'supplier'=>$supplier,
                    'ctime' =>$ctime
            ]);
        }

    }
    //编辑商品
    public function editGoods($id)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //加载场景
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            $post['create_time'] = strtotime($post['create_time']);
            //$post['lead_time'] = strtotime($post['lead_time']);
            Db::name('goods')->where('id', $post['id'])->update($post);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $category = Category::find()->order(['sort' => 'asc'])->select();
        $category = array2Level($category);
        $data = Db::name('goods')->where('id',$id)->find();
        $supplier = Supplier::where('status','=','1')->order(['id' => 'desc'])->select();
        return $this->fetch('editGoods',[
            'category'=>$category,
            'supplier'=>$supplier,
            'data'=>$data
        ]);

    }
    //删除商品
    public function delGoods()
    {
        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('goods')
                    ->where('id', $id)
                    ->delete();
                $this->success('删除成功');
            }else{
                $this->error('指定的删除参数无效');
            }
    }


public function categorylist()
    {
        return $this->fetch('categoryList');
    }

//商品分类列表   
function categoryListJson(){
        $category = model('category');
        $data = $category
            ->order('id desc')
            ->select();
        $list = array2level($data);
        foreach ($list as &$value){
            $value['category_name'] = str_repeat('| ---',$value['level']-1).$value['category_name'];
         }
        $res = [
            'code'=>0,
            'msg'=>'success',
            'count'=>count($list),
            'data'=> $list
        ];
        echo json_encode($res);exit;
    }

//增加商品分类
    function  addCategory(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $post['enterprise_code']  = session('enterprisecode');
            $Category = new Category();
            $res =  Db::name("Category")
                    ->where([
                        'category_name'  => ['=',$post['category_name']]
                    ])
                    ->field('id')
                    ->select();
                    //$this->error(var_dump($res));
            if($res){
                $this->error("商品分类名称重复！");
            }
            $Category->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加分类'.$post['category_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        $category = Db::name("Category")->order(['sort' => 'asc'])->select();
        $category = array2Level($category);
        return $this->fetch('addCategory',[
            'category'=>$category
        ]);
    }

     /**
     * 编辑商品分类
     */
    function  editCategory($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //加载场景
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            Category::get($post['id'])->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'编辑商品（ID：'.$post['id']."）",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $category = Category::order(['sort' => 'asc'])->select();
        $category = array2Level($category);
        $data = Category::where('id',$id)->find();
        return $this->fetch('editCategory',[
            'category'=>$category,
            'data'=>$data
        ]);
    }

    /**
     * 删除商品分类
     */
    function  delCategory($id)
    {

        $Category = Category::destroy($id);
        UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'编辑商品（ID：'.$id."）",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
        $this->success('success');
    }
    /*客户分类控制器*/
public function mcategorylist()
    {
        return $this->fetch('mcategoryList');
    }

//客户分类列表   
function mcategoryListJson(){
        $mcategory = model('mcategory');
        $data = $mcategory
            ->order('id desc')
            ->select();
        $list = array2level($data);
        foreach ($list as &$value){
            $value['mcategory_name'] = str_repeat('| ---',$value['level']-1).$value['mcategory_name'];
         }
        $res = [
            'code'=>0,
            'msg'=>'success',
            'count'=>count($list),
            'data'=> $list
        ];
        echo json_encode($res);exit;
    }

//增加客户分类
    function  addMcategory(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $Mcategory = new Mcategory();
            $res =  Db::name("Mcategory")
                    ->where([
                        'mcategory_name'  => ['=',$post['mcategory_name']]
                    ])
                    ->field('id')
                    ->select();
                    //$this->error(var_dump($res));
            if($res){
                $this->error("客户分类名称重复！");
            }
            $Mcategory->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加客户分类'.$post['mcategory_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        $mcategory = Db::name("Mcategory")->order(['sort' => 'asc'])->select();
        $mcategory = array2Level($mcategory);
        return $this->fetch('addMcategory',[
            'mcategory'=>$mcategory
        ]);
    }

     /**
     * 编辑客户分类
     */
    function  editMcategory($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //加载场景
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            Mcategory::get($post['id'])->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'编辑客户分类（ID：'.$post['id']."）",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $mcategory = Mcategory::order(['sort' => 'asc'])->select();
        $mcategory = array2Level($mcategory);
        $data = Mcategory::where('id',$id)->find();
        return $this->fetch('editMcategory',[
            'mcategory'=>$mcategory,
            'data'=>$data
        ]);
    }

    /**
     * 删除客户分类
     */
    function  delMcategory($id)
    {

        $Mcategory = Mcategory::destroy($id);
        UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除客户分类（ID：'.$id."）",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
        $this->success('success');
    }
    /*店铺操作控制器*/
    //店铺列表   
    public function shoplist()
    {
        return $this->fetch('shopList');
    }

//店铺列表   
function shopListJson(){
        $param = $this->request->get();
        $where = [];

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }

        $data = Db::name('Shop')
            ->field('id,shop_name,shop_director,shop_phone,status')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

//增加店铺
    function  addShop(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $res =  Db::name("Shop")
                    ->where([
                        'shop_name'  => ['=',$post['shop_name']],
                    ])
                    ->field('id')
                    ->select();
                    //$this->error(var_dump($res));
            if($res){
                $this->error("店铺名称重复！");
            }
             Db::name('shop')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加店铺'.$post['shop_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        return $this->fetch('addShop');
    }

    //增加店铺小票打印机
    function  addPrint(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $res =  Db::name("Print")
                    ->where([
                        'print_sn'  => ['=',$post['print_sn']],
                    ])
                    ->field('id')
                    ->find();
            if($res['id']){
                $this->error("此打印机已经添加过了，请先解绑！");
            }
            //开始添加云端打印机
            switch($post['print_brand']){
                case "1"://飞鹅打印机
            $printerContent = $post['print_sn'].'#'.$post['print_key'];
            $returnMsg = printerAddlist($printerContent);
            $resultx = json_decode($returnMsg,true);
            if($resultx['data']['no']!=''){
                $this->error($resultx['data']['no']);
            }
                break;
            }
             Db::name('Print')
               ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加店铺打印机 '.$post['print_name'].' 成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('添加店铺打印机 '.$post['print_name'].' 成功');
        }else{
        $get = $request->get();
        $shop_id = $get['id'];
        $shop = Db::name('shop')->field('shop_name')->where('id',$shop_id)->find();
        return $this->fetch('addPrint',['id'=>$shop_id,'shop_name'=>$shop['shop_name']]);
        }
    }

     /**
     * 编辑店铺
     */
    function  editShop($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            Db::name('shop')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'修改店铺'.$post['shop_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $data = Db::name('shop')->where('id',$id)->find();
        return $this->fetch('editShop',[
            'data'=>$data
        ]);
    }

    /**
     * 删除店铺
     */
    function  delShop($id)
    {

        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('shop')
                    ->where('id', $id)
                    ->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除店铺（ID:'.$id.'）成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('删除成功');
            }else{
                $this->error('指定的删除参数无效');
            }
    }
    public function storehouselist()
    {
        return $this->fetch('storehouseList');
    }

    public function storehouseListJson()
    {
        $param = $this->request->get();
        $where = [];

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        
        $data = Db::view('Storehouse','id,house_name,status,shop')
            ->view('Shop',['shop_name'],'storehouse.shop = shop.id')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    //增加仓库
    public function addStorehouse()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
                if($post['pos']==1){
                    //每个店铺只允许一个POS仓库存在
                    Db::name('storehouse')->where('shop',$post['shop'])->setField('pos',0);
                }
                 Db::name('storehouse')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加仓库'.$post['house_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('success');
        }else{
            $shop = Db::name('shop')
                ->field('id,shop_name')
                ->where('status',1)
                ->order('id desc')
                ->select();
            return $this->fetch('addStorehouse',
                [
                    'shop'=>$shop
            ]);
        }

    }
    //编辑仓库
    public function editStorehouse($id)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            if($post['pos']==1){
                    //每个店铺只允许一个POS仓库存在
                    Db::name('storehouse')->where('shop',$post['shop'])->setField('pos',0);
                }
            Db::name('storehouse')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'修改仓库'.$post['house_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $shop= Db::name('shop')->where('status',1)->select();
        $data = Db::name('storehouse')->where('id',$id)->find();
        return $this->fetch('editStorehouse',[
            'shop'=>$shop,
            'data'=>$data
        ]);

    }
    //删除仓库
    public function delStorehouse()
    {
        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('storehouse')
                    ->where('id', $id)
                    ->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除仓库（ID:'.$id.'）成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('删除成功');
            }else{
                $this->error('指定的删除参数无效');
            }
    }

    public function supplierlist()
    {
        return $this->fetch('supplierList');
    }

//供应商列表   
function supplierListJson(){
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

    //增加供应商
    function  addSupplier(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $res =  Db::name("Supplier")
                    ->where([
                        'supplier_name'  => ['=',$post['supplier_name']],
                    ])
                    ->field('id')
                    ->select();
                    //$this->error(var_dump($res));
            if($res){
                $this->error("供应商名称重复！");
            }
             Db::name('supplier')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加供应商'.$post['supplier_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        return $this->fetch('addSupplier');
    }

     /**
     * 编辑供应商
     */
    function  editSupplier($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            Db::name('supplier')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'修改供应商'.$post['supplier_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $data = Db::name('supplier')->where('id',$id)->find();
        return $this->fetch('editSupplier',[
            'data'=>$data
        ]);
    }

    /**
     * 删除供应商
     */
    function  delSupplier($id)
    {

        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('supplier')
                    ->where('id', $id)
                    ->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除供应商（ID:'.$id.'）成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('删除成功');
            }else{
                $this->error('指定的删除参数无效');
            }
    }

}
