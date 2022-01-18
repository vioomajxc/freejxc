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
use app\admin\model\Order;
class Financial extends Base
{
	//TODO:订单组合收款
        public function unionPay(){
		$request = $this->request;
        $data = $this->request->post('data');
        if(empty($data))$this->error("没选择任何订单");
        $err = $money = 0;
        $ors = '';
        $idarr = [];
        foreach($data as $key=>$dval){
        	$money += Order::getValuebyId($dval['id'],'or_money');
        	$ors .= $dval['or_id'].',';
        	$idarr[] = $dval['or_id'];
        	if($dval['or_verify_status']==0 || $dval['or_finish']==1)$err++;
        }
        if($err>0)$this->error("存在没有审核或已完成的单据");
        $ors = substr($ors,0,(strlen($ors)-1));
        $or_ids = '('.$ors.')';
        Order::whereIn('or_id',$idarr)->update(['or_paied' => 1,'or_finish' => 1]);//订单状态修改
        $f = [
        	'f_type' => 0,
        	'f_money' => $money,
        	'f_reason' => '订单'.$or_ids.'组合付款',
        	'f_username' => session('username'),
        	'f_time' => time(),
        	'f_channel' => 1,
        	'f_come' => 1,
        	'handover' => 1,
        	'handtime' => time()
        ];
        Db::name('financial_details')->insert($f);//写财务记录
        return [
        	'code' => 1,
        	'msg' => '订单'.$or_ids.'组合付款成功'
        ];
	}

        //TODO:订单组合收款
        public function unionCollection(){
                $request = $this->request;
        $data = $this->request->post('data');
        if(empty($data))$this->error("没选择任何订单");
        $err = $money = 0;
        $ors = '';
        $idarr = [];
        foreach($data as $key=>$dval){
                $money += Order::getValuebyId($dval['id'],'or_money');
                $ors .= $dval['or_id'].',';
                $idarr[] = $dval['or_id'];
                if($dval['or_verify_status']==0 || $dval['or_finish']==1)$err++;
        }
        if($err>0)$this->error("存在没有审核或已完成的单据");
        $ors = substr($ors,0,(strlen($ors)-1));
        $or_ids = '('.$ors.')';
        Order::whereIn('or_id',$idarr)->update(['or_paied' => 1,'or_finish' => 1]);//订单状态修改
        $f = [
                'f_type' => 1,
                'f_money' => $money,
                'f_reason' => '订单'.$or_ids.'组合收款',
                'f_username' => session('username'),
                'f_time' => time(),
                'f_channel' => 1,
                'f_come' => 1,
                'handover' => 1,
                'handtime' => time()
        ];
        Db::name('financial_details')->insert($f);//写财务记录
        return [
                'code' => 1,
                'msg' => '订单'.$or_ids.'组合收款成功'
        ];
        }

        //应收账单
        public function collectionList(){
                $members = Db::name('member')->field('id,member_name')->where('member_status',1)->select();
                return $this->fetch('collection',['members'=>$members]);
        }

        //应收账单
        public function payList(){
                $suppliers = Db::name('supplier')->field('id,supplier_name')->where('status',1)->select();
                return $this->fetch('pay',['suppliers'=>$suppliers]);
        }

        //帐务列表
        public function list(){
                $users = Db::name('user')->field('id,username')->where('status',1)->select();
                $channel = array('卡扣','现金','微信','支付宝','银联','其它');
                return $this->fetch('list',['users'=>$users,'channel'=>$channel]);
        }

        //读取财务列表   
    public function ListJson(){
        $param = $this->request->get();
        $where = [];
        if(!empty($param['f_username'])){
            $where[] =[ 'f_username','=',$param['f_username'] ];
        }
        if(isset($param['f_type'])){
            $where[] =[ 'f_type','=',$param['f_type'] ];
        }
        if(isset($param['f_channel'])){
            $where[] =[ 'f_channel','=',$param['f_channel'] ];
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
        $data = Db::name('financial_details')
            ->order('id desc')
            ->where($where)
            ->paginate($param['limit']);
        
        $this->genTableJson($data);
    }

    //查看/打印订单
    public function seeOrder(){
        $or_id = $this->request->get("or_id");
        $data = Db::view("order",'*')
        ->view("user",['username'],'order.or_user=user.username','LEFT')
        ->view("user_ext",['fullname'],'user_ext.userid=user.id','LEFT')
        ->view("storehouse",'house_name','or_house=storehouse.id','LEFT')
        ->view('member','member_name','or_contact=member.id','LEFT')
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
'print_date' => $print_date
        ]);
    }
}
?>