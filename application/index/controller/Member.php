<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\admin\model\UserLog;
use app\admin\model\Mcategory;
use think\Loader;

class Member extends Main
{
	public function index(){
		$param = $this->request->get();
		if(!empty($param['category']))
		$category = $param['category'];
	else
		$category = "";
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
                $categorys = Db::name('mcategory')->where('pid','>',0)->where('status',1)->order('sort asc')->select();
                $usinfo = $shops["shop_name"]." | ".$shops["fullname"];
                $shopname = $shops["shop_name"];
                $mcategory = Mcategory::find()->order(['sort' => 'asc'])->select();
        $mcategory = array2Level($mcategory);
                return $this->fetch('index',[
            'thistime' => $thistime,
            'usinfo' => $usinfo,
            'shopname' => $shopname,
            'categorys' => $categorys,
            'mcategory' => $mcategory,
            'category' => $category
        ]);
	}

	//会员明细记录
    public function memberListJson(){
        $param = $this->request->get();
        
        $where = [];
        $where[] = ['member_status','=',1];
        $where[] = ['member_shop','=',session('shop')];

        if(!empty($param['smember_name'])){
            $where[] =[ 'member_name','like','%'.$param['smember_name'].'%' ];
        }
        if(!empty($param['category'])){
            $where[] =['member_category','=',$param['category']];
        }

        if(!empty($param['start'])){
            $where[] = ['member_regtime','>=',strtotime($param['start'].' 00:00:00') ];
        }

        if(!empty($param['end'])){
            $where[] = ['member_regtime','<=',strtotime($param['end'].' 23:59:59') ];
        }

        if(empty($param['limit'])){
            $param['limit'] = 20;
        }
        //var_dump($where);
        
        $data = Db::view('member','id,member_code,member_name,member_sname,member_phone,member_regtime')
            ->view('mcategory',['mcategory_name'],'member.member_category=mcategory.id','LEFT')
            ->where($where)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }

//前台添加会员
    public function addMember()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
                 $post['member_regtime']  = time();
                 $post['member_code']     = "MR-".pinyin_long($post['member_name']);
                 $post['member_shop']     = session('shop');
                
                 Db::name('member')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加客户'.$post['member_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                return [
                	'code' => 2,
                	'msg'  => '添加客户'.$post['member_name'].'成功',
                	'url'  => '/index/member/'
                ];
        }

    }

    public function addMemberCard(){
    	$post = $this->request->post();
    	$member = Db::view('member','member_name,member_phone')
    	->view('mcategory','mcategory_name','member_category=mcategory.id','LEFT')
    	->where('member.id',$post['id'])
    	->find();
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
                                    <td>&nbsp;</td>
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
                                    <td>&nbsp;</td>
                                </tr>
                                <input type="hidden" name="memberid" value="'.$post['id'].'">
                            </tbody>
            </table>
            '
        ];
        return $res;
    }
//会员充值管理
    public function payMent(){
        $request = $this->request;
        if($request->isAjax()){
            //处理表单提交
            $post = $request->post();
            $pay_type = $post['pay_type'];
            $memberid = $post['memberid'];
            $cardmoney = $post['cardmoney'];//结算金额
            $addmoney = $post['addmoney'];//赠送金额
            $cardno = $post['cardno'];//会员卡号
            //发送打印指令
                //读取店铺云打印机
                $print = Db::view("print",'print_sn,print_key,print_mould')
                ->view("shop","shop_name,shop_phone,shop_address","shop.id=print_shop","LEFT")
                ->where('print_shop',session("shop"))
                ->where('print_status',1)
                ->find();
                $items = array("goodsname"=>"会员充值","it_number"=>1,"it_price"=>$cardmoney);
                if($print['print_sn']){
                    $list = print_setting($items,14,6,3,6);//打印排版
                    $printContent = str_replace("{SHOP_NAME}",$print["shop_name"],$print["print_mould"]);
                    $printContent = str_replace("{CREATE_TIME}",date("Y-m-d h:i:s",time()),$printContent);
                    $printContent = str_replace("{ORDER_ID}",'CZ'.time(),$printContent);
                    
                    $printContent = str_replace("{MONEY}",number_format($cardmoney,2,'.',',')."元",$printContent);
                    $printContent = str_replace("{SHOP_ADDRESS}",$print['shop_address'],$printContent);
                    $printContent = str_replace("{SHOP_PHONE}",$print['shop_phone'],$printContent);
                    $printContent = str_replace("{ITEM_LIST}",$list,$printContent);
                    
                }else $printContent = "";
            switch($pay_type){
                case "cash"://现金收款
                $cash['f_channel'] = 1;
                $printContent = str_replace("{COMMENT}",'现金充值',$printContent);
                $cash['f_type'] = 1;
                $cash['f_money'] = $cardmoney;
                $cash['f_reason'] = '会员充值('.$cardno.')';
                $cash['f_username'] = session('posusername');
                $cash['f_time'] = time();
                Db::name('financial_details')->insert($cash);//开卡进账
                $data['card_no'] = $cardno;
                $data['money'] = $cardmoney;
                $data['type'] = 1;
                $data['time'] = time();
                Db::name('card_details')->insert($data);//充值
                $existcard = Db::name('member_card')->where('card_no',$cardno)->find();
                if($existcard){
                	//存在会员卡
                	Db::name('member_card')->where('card_no',$cardno)->setInc('card_money',$cardmoney);
                	Db::name('member_card')->where('card_no',$cardno)->setInc('card_balance',($cardmoney+$addmoney));
                	Db::name('member_card')->where('card_no',$cardno)->setInc('card_give',$addmoney);
                }else{
                
                $card['member_id'] = $memberid;
                $card['card_no'] = $cardno;
                $card['card_pwd'] = md5('123456');
                $card['card_money'] = $cardmoney;
                $card['card_balance'] = $cardmoney+$addmoney;
                $card['card_give'] = $addmoney;
                $card['status'] = 1;
                $card['channel'] = 1;
                $card['card_time'] = time();
                Db::name('member_card')->insert($card);//开卡
                }
                if($print['print_sn']) printMsg($print['print_sn'],$printContent,1);//打印指令
                //发送反馈数据
                UserLog::addLog([
                    'uid'=>session('posuserid'),
                    'name'=>'会员卡('.$cardno.')现金充值成功',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                return [
                    'code' => 2,
                    'msg' => '会员卡('.$cardno.')现金充值'.$cardmoney.'元',
                    'url' => '/index/member'
                ];
                break;
                
                //处理公共业务
                
            }
        }else{
        $param = $this->request->get();
        $memberid = $param['memberid'];
        $cardmoney = $param['cardmoney'];//充值金额
        $addmoney = $param['addmoney'];
        $cardno = $param['cardno'];
        $pay_type = $param['pay_type'];
        $members = Db::name('member')->where('id',$memberid)->find();
        $site_config = Db::name('system')->where('name','site_config')->value('value');
        $this->assign('site_config',unserialize($site_config));
        switch($pay_type){
            case "cash"://现金结账
            return $this->fetch('payCash',[
            'memberid' => $memberid,
            'cardmoney' => $cardmoney,//充值金额
            'addmoney' => $addmoney,
            'cardno' => $cardno,
            'members' => $members
        ]);
            break;
            
        }
    }
    }
}
?>