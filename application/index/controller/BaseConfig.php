<?php
// +----------------------------------------------------------------------
// | LotusAdmin
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.lotusadmin.top/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wenhainan <qq 610176732>
// +----------------------------------------------------------------------
namespace app\index\controller;

use think\Db;
use think\facade\Hook;
use think\Controller;

class BaseConfig extends  Main
{
    //系统设置
    function set(){
        $request = $this->request;
        if($request->isPost()){
            $param = $request->post();
            //演示代码.开发中请删除
            //$this->error('演示站点,无法修改');
            Db::name('system')
                ->where('name','site_config')
                ->where('shop',session('shop'))
                ->update([
                    'value'=>serialize($param)
                ]);
            $this->success('修改成功,下次登录生效或者刷新浏览器页面.','',1);
        }
        $site_config =   Db::name('system')
            ->where('name','site_config')
            ->where('shop',session('shop'))
            ->value('value');
        $this->assign('site_config',unserialize($site_config));
        return $this->fetch('set');
    }
}