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
use app\admin\model\Mcategory;
use app\admin\model\Member as MemberModel;
use think\Hook;

class Member extends Base
{
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

    /*客户管理控制器*/
    /*客户列表*/
    public function memberlist()
    {
        return $this->fetch('memberList');
    }

    public function memberListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['member_name'])){
            $where[] =[ 'member_name','like','%'.$param['member_name'].'%' ];
        }

        if(!empty($param['start'])){
            $where[] = ['member_regtime','>=',strtotime($param['start'].' 00:00:00') ];
        }

        if(!empty($param['end'])){
            $where[] = ['member_regtime','<=',strtotime($param['end'].' 23:59:59') ];
        }

        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        $data = Db::name('member m')->join('mcategory c','m.member_category=c.id')->order('m.id desc')->where($where)->paginate($param['limit']);
        
        // $data = Db::view('Member','id,member_code,member_name,member_category,member_site,member_regtime,member_status')
        //     ->view('Mcategory',['mcategory_name'],'member.member_category = mcategory.id')
        //     ->order('member.id desc')
        //     ->where($where)
        //     ->paginate($param['limit']);
        $this->genTableJson($data);
    }

    //增加客户
    public function addMember()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
                 $post['member_regtime']   = time();
                 $post['member_code']     = "MR-".pinyin_long($post['member_name']);
                
                 Db::name('member')
                     ->insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加客户'.$post['member_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('success');
        }else{
        $mcategory = Mcategory::order(['sort' => 'asc'])->select();
        $mcategory = array2Level($mcategory);
        $shop = Db::name('shop')->where('status',1)->select();
            return $this->fetch('addMember',
                [
                    'mcategory'=>$mcategory,
                    'shop'=>$shop
            ]);
        }
    }
    //会员卡充值操作
    public function cardRecharge()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['card_no'])){
                $this->error("会员卡号不能为空");
            }
            $data['card_money'] = $post['card_money'] + $post['money'];
            $data['card_balance'] = $post['card_balance'] + $post['money'] + $post['card_give'];
            $data['card_give'] = $post['card_give1'] + $post['card_give'];
            Db::name('member_card')->where('card_no', $post['card_no'])->update($data);
            $fdata['f_type'] = 1;
            $fdata['f_money'] = $post['money'];
            $fdata['f_reason'] = "会员卡（".$post['card_no']."）充值收入";
            $fdata['f_username'] = session("username");
            $fdata['f_time'] = time();
            $fdata['f_channel'] = $post['channel'];
            $fdata['f_come'] = 2;
            Db::name('financial_details')->insert($fdata);
            $cdata = [
                'card_no' => $post['card_no'],
                'money' => $post['money'],
                'type' => 1,
                'time' => time()
            ];
            Db::name('card_details')->insert($cdata);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>"会员卡（".$post['card_no']."）充值收入",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success("会员卡（".$post['card_no']."）充值收入");
        }
        $card = $request->get("card_no");
        $members = Db::view("member_card","member_id,card_no,card_money,card_balance,card_give")
                       ->view("member","member_name","member.id=member_id","LEFT")
                       ->where('card_no',$card)
                       ->find();
        return $this->fetch('cardRecharge',[
            'members'=>$members
        ]);

    }

    //会员卡消费操作
    public function cardCost()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(empty($post['card_no']))$this->error("会员卡号不能为空");
            if($post['money']>$post['card_balance'])$this->error('消费金额不能大于卡余额');
            Db::name('member_card')->where('card_no', $post['card_no'])->setDec('card_balance',$post['money']);
            $cdata = [
                'card_no' => $post['card_no'],
                'money' => $post['money'],
                'type' => 0,
                'time' => time()
            ];
            Db::name('card_details')->insert($cdata);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>"会员卡（".$post['card_no']."）消费",
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success("会员卡（".$post['card_no']."）消费成功");
        }
        $card = $request->get("card_no");
        $members = Db::view("member_card","member_id,card_no,card_balance")
                       ->view("member","member_name","member.id=member_id","LEFT")
                       ->where('card_no',$card)
                       ->find();
        return $this->fetch('cardCost',[
            'members'=>$members
        ]);

    }

    //会员状态
    public function set_status($status,$id = 0){
        if($id==0 || $status == "")$this->error("参数错误");
        Db::name('member')->where('id',$id)->update(['member_status'=>$status]);
        return [
            'code' => 1,
            'msg' => $status==0 ? '禁用成功':'解禁成功'
        ];
    }
    //编辑客户
    public function editMember($id)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            if(!$post['id']>0){
                $this->error("提交了非法数据");
            }
            Db::name('member')->where('id', $post['id'])->update($post);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'编辑客户'.$post['member_name'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $mcategory = Mcategory::find()->order(['sort' => 'asc'])->select();
        $mcategory = array2Level($mcategory);
        $data = Db::name('member')->where('id',$id)->find();
        $shop = Db::name('shop')->where('status',1)->select();
        return $this->fetch('editMember',[
            'mcategory'=>$mcategory,
            'data'=>$data,
            'shop'=>$shop
        ]);

    }
    //删除客户
    public function delMember()
    {
        $id = $this->request->post('id');
        if($id>0){
                $db = Db::name('member')
                    ->where('id', $id)
                    ->delete();
                    UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除客户（ID：'.$id.'）',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('删除成功');
            }else{
                $this->error('指定的删除参数无效');
            }
    }

    //客户开卡
    function  addMemberCard(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $res =  Db::name("member_card")
                    ->where([
                        'card_no'  => ['=',$post['card_no']]
                    ])
                    ->field('id')
                    ->select();
                    //$this->error(var_dump($res));
            if($res){
                $this->error("此卡已被使用，请更换！");
            }
            $post['card_balance'] = $post['card_money'] + $post['card_give'];
            $post['card_pwd'] = md5($post['card_pwd']);
            $post['card_time'] = time();
            Db::name("member_card")->insert($post);
            //写财务流水
            $fdata['f_type'] = 1;
            $fdata['f_money'] = $post['card_money'];
            $fdata['f_reason'] = '会员开卡('.$post['card_no'].')储值收入';
            $fdata['f_username'] = session('username');
            $fdata['f_time'] = time();
            $fdata['f_channel'] = $post['channel'];
            $fdata['f_comment'] = "无";
            Db::name('financial_details')->insert($fdata);
            UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'会员卡（'.$post['card_no'].'）成功开出',
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
            $this->success('会员卡（'.$post['card_no'].'）成功开出');
        }
        $id = $request->get("id");
        $member = Db::name("Member")->where(['id' => $id])->field('member_name')->find();
        $mname = $member['member_name'];
        return $this->fetch('addMemberCard',[
            'mname'=>$mname,
            'member_id' => $id
        ]);
    }

}
