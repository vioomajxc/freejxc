<?php
namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username'       => 'require|min:3|max:30|unique:user',
        'password'       => 'require|min:6|max:30',
        'email'          => 'require|email|unique:user',
        'check_password' => 'require|confirm:password',
    ];
    protected $field = [
        'username'=>'用户名',
        'password'=>'密码',
        'email'=>'邮箱',
        'check_password'=>'确认'
    ];
    protected $scene = [
        'login' => ['username' => 'require|min:3|max:30', 'password'],
        'edit'  => [
            'username' => 'require|min:3|max:30|unique:user',
            'email'    => 'require|email',
        ],
        'editPassword'  => [
            'username' => 'require|min:3|max:30|unique:user',
            'email'    => 'require|email',
            'password',
            'check_password',
        ],
    ];
}
