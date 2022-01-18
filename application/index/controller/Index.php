<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\admin\model\UserLog;
use think\Loader;

class Index extends Main
{
    public function index()
    {
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
                $categorys = Db::name("category")->where('pid',0)->select();
                //创建POS单号
                $status = Db::name("order")->field('or_id')->where('or_type','=','pos')->where('or_status','=','0')->limit(1)->find();
        if($status)
            $or_id = $status['or_id'];
        else{
        $last_orid = Db::name("order")->where('id','>','0')->max('id');
        $or_id = "PS_".date("Ymd",time())."-".session("userid")."-".($last_orid+1);
        //创建订单记录
        $data['or_id'] = $or_id;
        $data['or_type'] = "pos";
        $data['or_contact'] = 0;
        $data['or_user'] = session("posusername");
        $data['or_create_time'] = time();
        $data['or_finish'] = 0;
        Db::name("order")->insert($data);
        }
        //pos仓库
        $storehouse = Db::name('storehouse')->where('shop',session('shop'))->where('pos',1)->where('status',1)->find();
        $houseid = $storehouse["id"];
                $goods = Db::view("stocks","numbers")
                ->view("goods",'id,goodsname,price','stocks.goods_id=goods.id','LEFT')
                ->where('house_id',$houseid)
                ->limit(20)
                ->select();//默认分类展示产品
                $totals = Db::name("item")
            ->field('sum(it_number) as numbers,sum(it_number*it_price) as moneys')
            ->where('or_id','=',$or_id)
            ->select();
        return $this->fetch('index/index',[
            'thistime' => $thistime,
            'usinfo' => $usinfo,
            'shopname' => $shopname,
            'categorys' => $categorys,
            'goods' => $goods,
            'or_id' => $or_id,
            'totals' => $totals
        ]);
        //$this->redirect("/admin/");
        //$this->success('success','https://www.viooma.com/');
    }

    //交接班处理
    public function handover(){
        $request = $this->request;
        if($request->isAjax()){
            //处理表单提交
            $post = $request->post();
            $d['handover'] = 1;
            $d['handtime'] = time();
            Db::name('financial_details')->where('handover',0)->where('f_type',1)->where('f_username',session('posusername'))->update($d);
            Db::name('member')->where('member_handover',0)->whereTime('member_regtime','today')->setField('member_handover',1);
            $post['hand_user'] = session('posusername');
            $post['hand_time'] = time();
            $post['hand_shop'] = session('shop');
            Db::name('handover')->insert($post);
             //$this->success('交接处理成功！');
			 return [
				 'code' => 2,
				 'msg'  => '交接处理成功！',
				 'url'  => '/index/index/'
			 ];
        }else{
        $shop = Db::name('shop')->where('id',session('shop'))->field('id,shop_name')->find();
        $channel = array('卡扣','现金','微信','支付宝');
        $channels = [];
        $comes = [];
        $moneys = [];
        foreach($channel as $key=>$cval){
            $thismoney = Db::name('financial_details')->where('handover',0)->where('f_type',1)->where('f_username',session('posusername'))->where('f_come',1)->where('f_channel',$key)->sum('f_money')+0;
            $channels[] = $thismoney;
            $moneys[$key] = $thismoney;
        }
        foreach($channel as $key=>$cval){
            $comemoney = Db::name('financial_details')->where('handover',0)->where('f_type',1)->where('f_username',session('posusername'))->where('f_come',2)->where('f_channel',$key)->sum('f_money')+0;
            $comes[] = $comemoney;
            $moneys[$key] += $comemoney;
        }
        $userinfo = Db::view('user','username,last_login_time')
        ->view('user_ext','fullname','user.id=userid','LEFT')
        ->where('user.id',session('posuserid'))
        ->find();
        $member_count = Db::name('member')->where('member_status',1)->where('member_handover',0)->whereTime('member_regtime','today')->count('id');
        $site_config = Db::name('system')->where('name','site_config')->value('value');
                $this->assign('site_config',unserialize($site_config));
        return $this->fetch('index/handover',[
            'shopname' => $shop['shop_name'],
            'channels' => $channels,
            'userinfo' => $userinfo,
            'handover_time' => date('Y-m-d H:i:s',time()),
            'channel' => $channel,
            'comes' => $comes,
            'moneys' => $moneys,
            'member_count' => $member_count
        ]);
        }
    }

    public function Login()
    {
        return $this->fetch("login");
    }
//pos销售单明细记录
    public function posListJson(){
        $param = $this->request->get();
        
        $where = [];

        $or_id = $param["or_id"];

        $where[] = ['or_id','=',$or_id];

        if(empty($param['limit'])){
            $param['limit'] = 50;
        }
        
        $data = Db::view('item','id,gd_id,it_number,it_price,it_discount')
            ->view('goods',['goodsname'],'item.gd_id=goods.id','LEFT')
            ->where($where)
            ->select();
            $res = [
            'code'=>0,
            'msg'=>'',
            'count'=>1000,
            'data'=>$data
        ];
        return $res;exit;
    }

    //手动添加POS项目
    public function manualToPos(){
        $post = $this->request->post();
        $or_id = $post['or_id'];
        $gd_id = $post['id'];
        $price = $post['price'];
        $where[] = ['or_id','=',$or_id];
        $where[] = ['gd_id','=',$gd_id];
        $db = Db::name("item")
        ->where($where)
        ->field('id')
        ->select();
        $postx['or_id'] = $or_id;
        $postx['gd_id'] = $gd_id;
        $postx['it_number'] = 1;
        $postx['it_price'] = $price;
        $postx['it_discount'] = 10;
        if($db){//找到了记录
                    Db::name("item")->where('gd_id', $gd_id)->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Db::name("item")->where('gd_id', $gd_id)->where('or_id',$or_id)->insert($postx);
                }
                $res = [
            'code'=>1,
            'msg'=>'添加成功'
                ];
                return $res;
    }

    //处理扫码枪操作
    public function posItem(){
        $post = $this->request->post();
        $sku = $post['sku'];
        $or_id = $post['or_id'];
        $where = [];
        $goods = Db::name("goods")
        ->where('barcode',$sku)
        ->field('id,price')
        ->find();
        if(!$goods['id'])
            $this->error("无此产品，请重试");
        $where[] = ['or_id','=',$or_id];
        $where[] = ['gd_id','=',$goods['id']];
        $db = Db::name("item")
        ->where($where)
        ->field('id')
        ->select();
        $postx['or_id'] = $or_id;
        $postx['gd_id'] = $goods['id'];
        $postx['it_number'] = 1;
        $postx['it_price'] = $goods['price'];
        $postx['it_discount'] = 10;
        if($db){//找到了记录
                    Db::name("item")->where('gd_id', $goods['id'])->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Db::name("item")->where('gd_id', $goods['id'])->where('or_id',$or_id)->insert($postx);
                }
                $res = [
            'code'=>1,
            'msg'=>'添加成功'
                ];
                return $res;
    }
    //删除POS订单中选择的商品
    public function delposGoods()
    {
        $id = $this->request->post('id');
                $db = Db::name('item')
                    ->where('or_id', $id)
                    ->delete();
                $res = [
            'code'=>1,
            'msg'=>'删除成功'
                ];
                return $res;
    }
    //选择商品
    function selectGoods(){
        $get = $this->request->get();
        $supplier = Db::name("supplier")->where('status','=','1')->select();
        return $this->fetch('selectgoodsList',[
            'supplier' => $supplier,
            'or_id' => $get['or_id']
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
        
        $data = Db::name('Goods')->order('id desc')->where($where)->paginate($param['limit']);
        $this->genTableJson($data);
    }

    //增加单个商品
    public function selectsingleGoods()
    {
        $request = $this->request;
        $gd_id = $this->request->post('id');//商品ID
        $or_id = $this->request->post('or_id');
                $db = Db::name('item')
                ->field('id')
                ->where('gd_id',$gd_id)
                ->where('or_id',$or_id)
                ->select();
                $data = Db::name("goods")->where("id",$gd_id)->select();

                    $datas['or_id'] = $or_id;
                    $datas['gd_id'] = $gd_id;
                    $datas['it_number'] = 1;
                    $datas['it_price'] = $data[0]['price'];
                    $datas['it_discount'] = 10;
                if($db){//找到了记录
                    Db::name("item")->where('gd_id', $gd_id)->where('or_id',$or_id)->setInc('it_number',1);
                }else{
                    Db::name("item")->where('gd_id', $gd_id)->where('or_id',$or_id)->insert($datas);
                }
                $res = [
            'code'=>1,
            'msg'=>'添加成功'
                ];
                return $res;

    }

    //读取会员卡信息
    public function readCard(){
        $post = $this->request->post();
        $cardno = $post['cardno'];
        $member = Db::view('member_card','card_no,card_money,card_balance,card_give,status')
        ->view('member','id,member_name,member_card,member_phone,member_status','member_id=member.id','LEFT')
        ->view('mcategory','mcategory_name','member_category=mcategory.id','LEFT')
        ->whereOr('card_no',$cardno)
        ->whereOr('member_card',$cardno)
        ->whereOr('member_phone',$cardno)
        ->find();
        if($member['status']==0)
            $this->error('无此会员或会员卡状态为禁用！');
        if($member['member_status']==0)
            $this->error('无此会员或会员状态为禁用！');
        $res = [
            'code' => 1,
            'msg' => '',
            'html' => '
            <table class="member-table">
            <tbody>
                                <tr>
                                    <th>会员姓名：</th>
                                    <td>'.$member['member_name'].'</td>
                                </tr>
                                <tr>
                                    <th>会员卡号：</th>
                                    <td>'.$member['card_no'].'</td>
                                </tr>
                                <tr>
                                    <th>电话号码：</th>
                                    <td>'.$member['member_phone'].'</td>
                                </tr>
                                <tr>
                                    <th>会员级别：</th>
                                    <td>'.$member['mcategory_name'].'</td>
                                </tr>
                                <tr>
                                    <th>现金余额：</th>
                                    <td>￥'.number_format($member['card_balance'],2,'.',',').'</td>
                                </tr>
                                <input type="hidden" name="memberid" value="'.$member['id'].'">
                            </tbody>
            </table>
            '
        ];
        return $res;
    }

    public function payMent(){
        $request = $this->request;
        if($request->isAjax()){
            //处理表单提交
            $post = $request->post();
            $pay_type = $post['pay_type'];
            $or_id = $post['or_id'];
            $payed = $post['payed'];//结算金额
            //发送打印指令
                //读取店铺云打印机
                $print = Db::view("print",'print_sn,print_key,print_mould')
                ->view("shop","shop_name,shop_phone,shop_address","shop.id=print_shop","LEFT")
                ->where('print_shop',session("shop"))
                ->where('print_status',1)
                ->find();
                $items = Db::view('item','gd_id,it_number,it_price')
                    ->view('goods','goodsname','goods.id=gd_id','LEFT')
                    ->where('or_id',$or_id)
                    ->select();
                    $house = Db::name("storehouse")
                    ->field('id')
                    ->where('shop',session('shop'))
                    ->where('status',1)
                    ->where('pos',1)
                    ->find();
                    if(!$house['id'])
                        $this->error('该店铺没有设置默认的POS仓库，无法销售！');
                    $er = 0;
                    foreach($items as $val){
                        Db::name('goods')->where('id',$val['gd_id'])->setInc('sales',$val['it_number']);
                        $stocks = Db::name('stocks')->where('goods_id',$val['gd_id'])->where('house_id',$house['id'])->find();
                        if(!$stocks['id'])
                            $er ++;
                    }
                    if($er>0)
                        $this->error('订单中有商品无库存记录，请修改！');
                    
                if($print['print_sn']){
                    $list = print_setting($items,18,4,3,4);//打印排版
                    $printContent = str_replace("{SHOP_NAME}",$print["shop_name"],$print["print_mould"]);
                    $printContent = str_replace("{CREATE_TIME}",date("Y-m-d h:i:s",time()),$printContent);
                    $printContent = str_replace("{ORDER_ID}",$or_id,$printContent);
                    
                    $printContent = str_replace("{MONEY}",number_format($payed,2,'.',',')."元",$printContent);
                    $printContent = str_replace("{SHOP_ADDRESS}",$print['shop_address'],$printContent);
                    $printContent = str_replace("{SHOP_PHONE}",$print['shop_phone'],$printContent);
                    $printContent = str_replace("{ITEM_LIST}",$list,$printContent);
                    
                }else $printContent = "";
            switch($pay_type){
                case "card"://卡扣
                $printContent = str_replace("{COMMENT}",'会员卡扣款',$printContent);
                $balance = $post['card_balance'];
                if($payed>$balance)
                    $payed = $balance;//使用最大余额付款
                $card_no = $post['card_no'];
                $data['or_status'] = 1;
                $data['or_finish'] = 1;
                $data['or_verify_status'] = 1;
                $data['or_paied'] = 1;
                $data['or_house'] = $house['house_id'];
                $data['or_contact'] = $post['or_contact'];
                $data['or_money'] = $payed;
                //判断卡密
                $result_pwd = Db::name('member_card')->where('card_no',$card_no)->find();
                if($result_pwd['card_pwd']!=md5($post['cardpwd']))
                    $this->error("会员卡卡密码错误，请重试！");
                Db::name('member_card')->where('card_no',$card_no)->setDec('card_balance',$payed);//卡扣
                $cdata['card_no'] = $card_no;
                $cdata['money'] = $payed;
                $cdata['type'] = 0;
                $cdata['or_id'] = $or_id;
                $cdata['time'] = time();
                Db::name('card_details')->insert($cdata);//卡消耗记录
                
                if($payed>$balance)//卡余额不足
                $this->success('订单('.$or_id.')卡扣付款:'.$payed.'元，但余额不足，请继续支付！');
                else{
                Db::name('order')->where('or_id',$or_id)->update($data);//保存订单
                //减库存
                foreach($items as $val){
                        $stocks = Db::name('stocks')->where('goods_id',$val['gd_id'])->where('house_id',$house['id'])->setDec('numbers',$val['it_number']);
                    }
            }
                //发送消费SMS指令
            if($print['print_sn']) printMsg($print['print_sn'],$printContent,1);//打印指令
                //发送反馈数据
                UserLog::addLog([
                    'uid'=>session('posuserid'),
                    'name'=>'订单('.$or_id.')卡扣付款成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                return [
                    'code' => 2,
                    'msg' => '订单('.$or_id.')卡扣付款:'.$payed.'元',
                    'url' => '/index/index'
                ];
                //$this->success('订单('.$or_id.')卡扣付款:'.$payed.'元');
                break;
                case "cash"://现金收款
                $printContent = str_replace("{COMMENT}",'现金收款',$printContent);
                if($payed<$post['pay_money'])
                    $this->error('收款金额不能小于订单金额！');
                $cashdata['f_type'] = 1;
                $cashdata['f_money'] = $payed;
                $cashdata['f_reason'] = 'POS收款('.$or_id.')';
                $cashdata['f_username'] = session('posusername');
                $cashdata['f_time'] = time();
                $cashdata['f_channel'] = 1;
                Db::name('financial_details')->insert($cashdata);
                $data['or_status'] = 1;
                $data['or_finish'] = 1;
                $data['or_verify_status'] = 1;
                $data['or_paied'] = 1;
                $data['or_house'] = $house['id'];
                $data['or_contact'] = $post['memberid'];
                $data['or_money'] = $payed;
                Db::name('order')->where('or_id',$or_id)->update($data);//保存订单
                foreach($items as $val){
                        $stocks = Db::name('stocks')->where('goods_id',$val['gd_id'])->where('house_id',$house['id'])->setDec('numbers',$val['it_number']);
                    }
                if($print['print_sn']) printMsg($print['print_sn'],$printContent,1);//打印指令
                //发送反馈数据
                UserLog::addLog([
                    'uid'=>session('posuserid'),
                    'name'=>'订单('.$or_id.')现金付款成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                return [
                    'code' => 2,
                    'msg' => '订单('.$or_id.')现金付款:'.$payed.'元',
                    'url' => '/index/index'
                ];
                break;
                
                
            }
        }else{//支付前界面
        $param = $this->request->get();
        $or_id = $param['or_id'];
        $pay_type = $param['pay_type'];
        $card_no = $param['cardno'];
        $money = Db::name("item")->field('sum(it_number*it_price) as money')->where('or_id',$or_id)->find();//订单金额
        $payed_money = Db::name("card_details")->field("sum(money) as payed_money")->where('or_id',$or_id)->find();//订单已付金额
        $site_config = Db::name('system')
                ->where('name','site_config')
                ->value('value');
                $this->assign('site_config',unserialize($site_config));
        switch($pay_type){
            case "card"://会员卡结账
            $members = Db::view('member_card','card_no,card_balance')
        ->view('member','id,member_name,member_card,member_phone','member_id=member.id','LEFT')
        ->view('mcategory','mcategory_name,discount','member_category=mcategory.id','LEFT')
        ->whereOr('card_no',$card_no)
        ->whereOr('member_card',$card_no)
        ->whereOr('member_phone',$card_no)
        ->find();
        $discount = $members['discount'];
        $member_name = $members['member_name'];
        $payed = $payed_money['payed_money'];//已付金额
            return $this->fetch('payCard',[
            'or_id' => $or_id,
            'allmoney' => round($money['money'],2),//订单金额
            'payed' => round($payed,2),//已付金额
            'money' => round($money['money']*($discount/10),2),//折后金额
            'members' => $members
        ]);
            break;
            case "cash"://现金结账
            if($card_no==""){
                $members = [
                    "member_name" => "散客",
                    "card_no" => "",
                    "card_balance" => 0,
                    "discount" => 10,
                    "id" => 0
                ];
                $discount = 10;
                }else{
            $members = Db::view('member_card','card_no,card_balance')
        ->view('member','id,member_name,member_card,member_phone','member_id=member.id','LEFT')
        ->view('mcategory','mcategory_name,discount','member_category=mcategory.id','LEFT')
        ->whereOr('card_no',$card_no)
        ->whereOr('member_card',$card_no)
        ->whereOr('member_phone',$card_no)
        ->find();
        $discount = $members['discount'];
        $member_name = $members['member_name'];
        }
        $payed = $payed_money['payed_money'];//已付金额
            return $this->fetch('payCash',[
            'or_id' => $or_id,
            'allmoney' => round($money['money'],2),//订单金额
            'payed' => round($payed,2),//已付金额
            'money' => round($money['money']*($discount/10),2),//折后金额
            'members' => $members
        ]);
            break;
            
        }
    }
    }

    //读取指定分类产品
    public function openCategory(){
        $post = $this->request->post();
        $id = $post['id'];//分类ID
        $or_id = $post['or_id'];
        //pos仓库
        $storehouse = Db::name('storehouse')->where('shop',session('shop'))->where('pos',1)->where('status',1)->find();
        $houseid = $storehouse["id"];
        $subcates = Db::name('category')->where('pid',$id)->select();
        if(!$subcates){
            //无子分类即读取当前分类
            $thiscate = $id;
            $titles = "";
        }else{
            $titles = "";
            $x = 0;
            foreach($subcates as $key => $sval){
                if($x==0)
                $thiscate = $sval['id'];
                $titles .= "
                <button type='button' class='layui-btn layui-btn-sm' onclick='openCategory(".$sval['id'].",\"$or_id\")'>".$sval["category_name"]."</button>
                ";
                $x++;
                  }
               }

        //读取初始分类
                $goods = Db::view("stocks","numbers")
                ->view("goods",'id,goodsname,price','stocks.goods_id=goods.id','LEFT')
                ->where('house_id',$houseid)
                ->where('category',$thiscate)
                ->limit(20)
                ->select();
                $bodys = "";
                foreach($goods as $key => $gval){
                    $bodys .= '
                    <div class="layui-col-md3 layui-fluid layui-col-space50">
                            <div class="layui-card">
                                <a href="javascript:void();" title="'.$gval['goodsname'].'库存数量为'.$gval['numbers'].'" onclick="manualToPos('.$gval['id'].',\''.$or_id.'\',\''.$gval['price'].'\');">
                        <div class="layui-card-header goods_title">'.$gval['goodsname'].'</div>
                        <div class="layui-card-body goods_pal">
                            <span class="price">￥'.number_format($gval['price'],2).'</span>
                    <span class="layui-badge" style="float:right;">'.$gval['numbers'].'</span>        
                        </div>
                    </a>
                    </div>
                       </div>
                    ';
                }
                if($bodys=="")$bodys = "此分类下无产品";
        $res = [
            'code' => 1,
            'msg' => '成功',
            'title' => $titles,
            'body' => $bodys
                ];
        return $res;
}
//订单挂起
public function hangUp(){
    $post = $this->request->post();
    Db::name('order')->where('or_id',$post['id'])->setField('or_type','hangUp');
    $tal = Db::name('item')->where('or_id',$post['id'])->find();
    if(!$tal['id'])
        return [
            'code' => 0,
            'msg' => '此POS单没有添加产品',
            'body' => ''
        ];
        else
    return [
        'code' => 1,
        'msg' => '挂单成功',
        'body' => ''
    ];
}
//取出订单
public function getOutList(){
    $post = $this->request->post();
    if($post){
        Db::name('order')->where('id',$post['id'])->setField('or_type','pos');
        return [
        'code' => 1,
        'msg' => '取单成功',
        'body' => ''
    ];
    }
    $site_config = Db::name('system')
                ->where('name','site_config')
                ->value('value');
                $this->assign('site_config',unserialize($site_config));
        $lists = Db::name('order')->where('or_type','hangUp')->order('id desc')->select();
        return $this->fetch('getOutList',['lists'=>$lists]);
}

}
