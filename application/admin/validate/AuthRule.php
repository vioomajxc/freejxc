<?php
namespace app\admin\validate;

use think\Validate;

class AuthRule extends Validate
{
    protected $rule = [
        'pid'       => 'require',
        'title'     => 'require|min:2|max:15|unique:auth_rule',
        'name'      => 'require|unique:auth_rule',
        'sort'      =>'require|number',
    ];

    protected $field = [
        'pid'=>'上次菜单',
        'title'=>'权限名称',
        'name'=>'路由',
        'sort'=>'权重'
    ];

    protected $scene = [
        'edit'  => [
            'title',
            'name',
            'sort',
        ],
    ];

}
