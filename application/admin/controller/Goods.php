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

    //????????????
    public function shop_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("????????????");
        Db::name('shop')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '????????????':'????????????'
        ];
    }

    //????????????
    public function goods_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("????????????");
        Db::name('goods')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '????????????':'????????????'
        ];
    }
    //????????????
    public function supplier_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("????????????");
        Db::name('supplier')->where('id',$id)->update(['status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '????????????':'????????????'
        ];
    }

    //????????????
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
                    'name'=>'????????????'.$post['goodsname'],
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
    //????????????
    public function editGoods($id)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //????????????
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            $post['create_time'] = strtotime($post['create_time']);
            //$post['lead_time'] = strtotime($post['lead_time']);
            Db::name('goods')->where('id', $post['id'])->update($post);
            $this->success('????????????,?????????????????????????????????????????????');
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
    //????????????
    public function delGoods()
    {
        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('goods')
                    ->where('id', $id)
                    ->delete();
                $this->success('????????????');
            }else{
                $this->error('???????????????????????????');
            }
    }


public function categorylist()
    {
        return $this->fetch('categoryList');
    }

//??????????????????   
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

//??????????????????
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
                $this->error("???????????????????????????");
            }
            $Category->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['category_name'],
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
     * ??????????????????
     */
    function  editCategory($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //????????????
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            Category::get($post['id'])->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????ID???'.$post['id']."???",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????????????????????????????');
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
     * ??????????????????
     */
    function  delCategory($id)
    {

        $Category = Category::destroy($id);
        UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????ID???'.$id."???",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
        $this->success('success');
    }
    /*?????????????????????*/
public function mcategorylist()
    {
        return $this->fetch('mcategoryList');
    }

//??????????????????   
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

//??????????????????
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
                $this->error("???????????????????????????");
            }
            $Mcategory->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'??????????????????'.$post['mcategory_name'],
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
     * ??????????????????
     */
    function  editMcategory($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            //$validate =  validate('Category');
            //????????????
            //$validate->scene('edit');
            //$res =  $validate->check($post);
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            Mcategory::get($post['id'])->allowField(true)->save($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'?????????????????????ID???'.$post['id']."???",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????????????????????????????');
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
     * ??????????????????
     */
    function  delMcategory($id)
    {

        $Mcategory = Mcategory::destroy($id);
        UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'?????????????????????ID???'.$id."???",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
        $this->success('success');
    }
    /*?????????????????????*/
    //????????????   
    public function shoplist()
    {
        return $this->fetch('shopList');
    }

//????????????   
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

//????????????
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
                $this->error("?????????????????????");
            }
             Db::name('shop')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['shop_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        return $this->fetch('addShop');
    }

    //???????????????????????????
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
                $this->error("????????????????????????????????????????????????");
            }
            //???????????????????????????
            switch($post['print_brand']){
                case "1"://???????????????
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
                    'name'=>'????????????????????? '.$post['print_name'].' ??????',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????????????? '.$post['print_name'].' ??????');
        }else{
        $get = $request->get();
        $shop_id = $get['id'];
        $shop = Db::name('shop')->field('shop_name')->where('id',$shop_id)->find();
        return $this->fetch('addPrint',['id'=>$shop_id,'shop_name'=>$shop['shop_name']]);
        }
    }

     /**
     * ????????????
     */
    function  editShop($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            Db::name('shop')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['shop_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????????????????????????????');
        }
        $data = Db::name('shop')->where('id',$id)->find();
        return $this->fetch('editShop',[
            'data'=>$data
        ]);
    }

    /**
     * ????????????
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
                    'name'=>'???????????????ID:'.$id.'?????????',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('????????????');
            }else{
                $this->error('???????????????????????????');
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

    //????????????
    public function addStorehouse()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
                if($post['pos']==1){
                    //???????????????????????????POS????????????
                    Db::name('storehouse')->where('shop',$post['shop'])->setField('pos',0);
                }
                 Db::name('storehouse')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['house_name'],
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
    //????????????
    public function editStorehouse($id)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            if($post['pos']==1){
                    //???????????????????????????POS????????????
                    Db::name('storehouse')->where('shop',$post['shop'])->setField('pos',0);
                }
            Db::name('storehouse')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'????????????'.$post['house_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????????????????????????????');
        }
        $shop= Db::name('shop')->where('status',1)->select();
        $data = Db::name('storehouse')->where('id',$id)->find();
        return $this->fetch('editStorehouse',[
            'shop'=>$shop,
            'data'=>$data
        ]);

    }
    //????????????
    public function delStorehouse()
    {
        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('storehouse')
                    ->where('id', $id)
                    ->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????ID:'.$id.'?????????',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('????????????');
            }else{
                $this->error('???????????????????????????');
            }
    }

    public function supplierlist()
    {
        return $this->fetch('supplierList');
    }

//???????????????   
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

    //???????????????
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
                $this->error("????????????????????????");
            }
             Db::name('supplier')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['supplier_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('success');
        }
        return $this->fetch('addSupplier');
    }

     /**
     * ???????????????
     */
    function  editSupplier($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("?????????????????????");
            }
            Db::name('supplier')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'???????????????'.$post['supplier_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('????????????,?????????????????????????????????????????????');
        }
        $data = Db::name('supplier')->where('id',$id)->find();
        return $this->fetch('editSupplier',[
            'data'=>$data
        ]);
    }

    /**
     * ???????????????
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
                    'name'=>'??????????????????ID:'.$id.'?????????',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('????????????');
            }else{
                $this->error('???????????????????????????');
            }
    }

}
