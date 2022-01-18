<?php
namespace app\admin\controller;
// +----------------------------------------------------------------------
// | LotusAdmin
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wenhainan <qq 12612019>
// +----------------------------------------------------------------------

use app\admin\model\AuthGroupAccess;
use app\admin\model\UserLog;
use org\Auth;
use think\Db;
use think\Session;
use think\Validate;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\User as UserModel;
use think\Hook;

class User extends Base
{


    public function userlist()
    {
        return $this->fetch('userList');
    }

    public function userListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['username'])){
            $where[] =[ 'username','like','%'.$param['username'].'%' ];
        }

        if(!empty($param['start'])){
            $where[] = ['create_time','>=',$param['start'] ];
        }

        if(!empty($param['end'])){
            $where[] = ['create_time','<=',$param['end'] ];
        }


        if(empty($param['limit'])){
            $param['limit'] = 10;
        }
        $data = Db::name('User')
            ->order('id desc')
            ->where($where)
            ->where('id','not in',[session('userid'),1])
            ->field('id,username,email,create_time')
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }
    //增加用户
    public function addUser()
    {
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $group_id = $post['group_id'];
            unset($post['group_id']);
            $validate = validate('User');
            $res      = $validate->check($post);
            if ($res !== true) {
                $this->error($validate->getError());
            } else {
                unset($post['check_password']);
                $post['password'] = md5($post['password']);
                $post['last_login_ip'] = '0.0.0.0';
                $post['create_time']   = date('Y-m-d h:i:s', time());
                Db::name('user')
                    ->insert($post);
                $userId = Db::name('user')->getLastInsID();
                //写入用户详细数据
                $data['userid'] = $userId;
                $data['fullname'] = "未知";
                $data['sex'] = "未知";
                Db::name("user_ext")->insert($data);
                Db::name('auth_group_access')
                    ->insert(['uid'=>$userId,'group_id'=>$group_id]);
                UserLog::addLog([
                    'uid'=>$userId,
                    'name'=>'添加用户'.$post['username'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->success('success');
            }
        }else{
            $auth_group = Db::name('auth_group')
                ->field('id,title')
                ->where('status',1)
                ->order('id desc')
                ->select();
                $shops = Db::name('shop')
                ->field('id,shop_name')
                ->where('status',1)
                ->select();
            return $this->fetch('add',[
                'auth_group' => $auth_group,
                'shops' => $shops
            ]);
        }

    }
    //编辑提交
    public function editUser($id)
    {
        $request = $this->request;
        if($request->isPost()){
            $post     = $this->request->post();
            if($post['id']==1 ){
                if(session('user_id')!==1)  $this->error('系统管理员无法修改');
            }
            $group_id = $post['group_id'];
            unset($post['group_id']);
            $validate = validate('User');
            if (empty($post['password']) && empty($post['check_password'])) {
                $res = $validate->scene('edit')->check($post);
                if ($res !== true) {
                    $this->error($validate->getError());
                } else {
                    unset($post['check_password'],$post['password']);
                    $db = Db::name('user')
                        ->where('id', $post['id'])
                        ->update(
                            [
                                'username' => $post['username'],
                                'email'    => $post['email'],
                            ]);
                    //授予用户权限
                    Auth::setRole($post['id'],$group_id);
                    $this->success('success');
                }
            } else {
                $res = $validate->scene('editPassword')->check($post);
                if ($res !== true) {
                    $this->error($validate->getError());
                } else {
                    unset($post['check_password']);
                    $post['password'] = md5($post['password']);
                    $db               = Db::name('user')
                        ->where('id', $post['id'])
                        ->update($post);
                    $this->success('success');
                }
            }
        }else{
            $data = Db::name('User')
                ->alias('a')
                ->join('auth_group_access b','b.uid=a.id','left')
                ->field('a.*,b.group_id')
                ->where('id', $id)
                ->find();
            $auth_group = Db::name('auth_group')
                ->field('id,title')
                ->order('id desc')
                ->select();
                $shops = Db::name('shop')
                ->field('id,shop_name')
                ->where('status',1)
                ->select();
            $this->assign('auth_group', $auth_group);
            $this->assign('data', $data);
            return $this->fetch('edit',['shops'=>$shops]);
        }

    }
    //删除用户
    public function deleteUser()
    {
        $id = $this->request->post('id');
        $username =  Db::name('user')
            ->where('id',$id)
            ->value('username');
        if ((int) $id !== 1) {
            if($username!==session('username')){
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'删除用户'.UserModel::get($id)->username,
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $db = Db::name('user')
                    ->where('id', $id)
                    ->delete();
                $this->success('删除成功');
            }else{
                $this->error('无法删除当前登录用户');
            }
        } else {
            $this->error('超级管理员无法删除');
        }
    }

    /**
     * 节点管理
     */
    function  ruleList(){
        return $this->fetch('ruleList');
    }

    function ruleListJson(){
        $authRule = model('AuthRule');
        $data = $authRule
            ->order('id desc')
            ->select();
        $list = array2level($data);
        foreach ($list as &$value){
            $value['title'] = str_repeat('| ---',$value['level']-1).$value['title'];
            $value['icon']  = '<i class="layui-icon '.$value['icon'].'"></i>';
         }
        $res = [
            'code'=>0,
            'msg'=>'success',
            'count'=>count($list),
            'data'=> $list
        ];
        echo json_encode($res);exit;
    }

    function  addRule(){
        $request = $this->request;
        if($request->isAjax()){
            $post     = $request->post();
            $validate =  validate('AuthRule');
            $res =  $validate->check($post);
            if($res==false){
                $this->error($validate->getError());
            }
            $authRule = new AuthRule();
            $authRule->allowField(true)->save($post);
            $this->success('success');
        }
        $auth = AuthRule::find()->order(['sort' => 'asc'])->select();
        $auth = array2Level($auth);
        return $this->fetch('addRule',[
            'auth'=>$auth
        ]);
    }

    /**
     * 编辑路由
     */
    function  editRule($id=null)
    {
        $request = $this->request;
        if($request->isAjax()){
            $post = $request->post();
            $validate =  validate('AuthRule');
            //加载场景
            $validate->scene('edit');
            $res =  $validate->check($post);
            if($res==false){
                $this->error($validate->getError());
            }
            AuthRule::get($post['id'])->allowField(true)->save($post);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面');
        }
        $auth = AuthRule::order(['sort' => 'asc'])->select();
        $auth = array2Level($auth);
        $data = AuthRule::where('id',$id)->find();
        return $this->fetch('editRule',[
            'auth'=>$auth,
            'data'=>$data
        ]);
    }

    /**
     * 删除权限
     */
    function  delRule($id)
    {

        $authRule = AuthRule::destroy($id);
        $this->success('success');
    }

    /**
     * 角色列表
     */
    function roleList()
    {
        return $this->fetch('roleList');
    }

    function  roleListJson()
    {
        $param = $this->request->get();
        $where = [];
        if(!empty($param['username'])){
            $where['username'] = $param['username'];
        }
        $data = AuthGroup::where($where)
            ->order('id desc')
            //->where('id','<>',1)
            ->paginate($param['limit']);
        $this->genTableJson($data);
    }

    function addRole(){
        if($this->request->isPost()){
            $post = $this->request->post();
            $validate = validate('AuthGroup');
            $res =  $validate->check($post);
            if(!$res){
                $this->error($validate->getError());
            }else{
                AuthGroup::insert($post);
                UserLog::addLog([
                    'uid'=>session('userid'),
                    'name'=>'添加角色'.$post['title'],
                    'url' => $this->request->path(),
                    'ip' =>$this->request->ip(),
                ]);
                $this->successJson('添加成功');
            }
        }
        return $this->fetch('addRole');
    }

    //角色编辑
    function editRole($id){
        if($this->request->isPost()){
            $post     =  $this->request->post();
            $res  = $this->validate($post,'AuthGroup');
            if(!isset($post['rules'])){
                $this->error('未授予任何权限,拒绝提交');
            }
            if(true!==$res){
                $this->error($res);
            }else{
                $post['rules'] = is_array($post['rules'])?implode(',',$post['rules']):'';
                Db::name('auth_group')
                    ->where('id',$post['id'])
                    ->update($post);
                $this->success('授权成功');
            }
        }else{
            $authGroup =  AuthGroup::get($id);
            return $this->fetch('editRole',[
                'authGroup'=>$authGroup
            ]);
        }
    }

    //获取规则数据
    public function getJson()
    {
        $id = $this->request->post('id');
        $auth_group_data = Db::name('auth_group')->find($id);
        $auth_rules      = explode(',', $auth_group_data['rules']);
        $auth_rule_list  = Db::name('auth_rule')->field('id,pid,title')->select();

        foreach ($auth_rule_list as $key => $value) {
            in_array($value['id'], $auth_rules) && $auth_rule_list[$key]['checked'] = true;
        }
        return $auth_rule_list;
    }

    public function  editPasswd()
    {
        $request = $this->request;
        if($request->isPost()){
            $post = $request->post();
            $validate = validate('user');
            $validate->scene('editPasswd');
            $res =  $validate->check($post);
            if(!$res){
                $this->error($validate->getError());
            }
            $post['password'] = md5($post['password']);
            UserModel::update($post);
            UserLog::addLog([
                'uid'=>session('userid'),
                'name'=>'修改密码',
                'url' => $this->request->path(),
                'ip' =>$this->request->ip(),
            ]);
            $this->successJson('修改成功,请重新登录...',2,'/admin/login/logout');
        }
        $sid  = session('userid');
        $data = UserModel::get($sid);
        return $this->fetch('editPasswd',[
            'data'=>$data
        ]);
    }

    public function  delRole($id){
        $res = AuthGroupAccess::where('group_id',$id)->find();
        if(!empty($res)){
            $this->error('有用户已经分配该权限,请先删除用户');
        }
        UserLog::addLog([
            'uid'=>session('userid'),
            'name'=>'删除角色'.AuthGroup::get($id)->title,
            'url' => $this->request->path(),
            'ip' =>$this->request->ip(),
        ]);

        AuthGroup::destroy($id);
        $this->success('删除成功');
    }



}
