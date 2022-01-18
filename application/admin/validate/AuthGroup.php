<?php
namespace app\admin\validate;

use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'title'     => 'require|min:2|max:15|unique:auth_group',
        'status'      => 'require',
    ];

    protected $field = [
        'title'=>'角色名称',
        'status'=>'状态',
    ];

    protected $scene = [

    ];

}
