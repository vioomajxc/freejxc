<?php
namespace app\admin\validate;

use think\Validate;

class Role extends Validate
{
    protected $rule = [
        'title'       => 'require|min:2|max:20|unique:auth_group',
    ];
    protected $message = [
    	'title.require' =>'角色名称不能为空',
    	'title.unique'		=>'角色名称不能重复',
    	'title.min'		=>'角色名称太短',
    	'title.max'		=>'角色名称太长',
    ];
}
