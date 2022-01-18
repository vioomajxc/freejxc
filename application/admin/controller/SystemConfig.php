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
namespace app\admin\controller;

use think\Db;
use think\facade\Hook;
use think\Controller;
use think\Request;

class SystemConfig extends  Base
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
                ->update([
                    'value'=>serialize($param)
                ]);
				$data = [
					'merchant_name' => $param['site_name'],
					'merchant_contact' => $param['admin'],
					'merchant_mobile' => $param['phone'],
					'merchant_address' => $param['address'],
					'merchant_mail' => $param['email'],
					'merchant_url' => $_SERVER['HTTP_HOST']
				];
				$result = doPost(API_SERVER.'merchant/index/reg',$data,'application/x-www-form-urlencoded');
			$this->success('修改成功,下次登录生效或者刷新浏览器页面.');
        }
        $site_config =   Db::name('system')
            ->where('name','site_config')
            ->value('value');
        $this->assign('site_config',unserialize($site_config));
        return $this->fetch('set');
    }

}