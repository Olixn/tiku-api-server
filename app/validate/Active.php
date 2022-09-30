<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Active extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'uid'   => 'require|number|unique:users',
        'code'  => 'require|length:32'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'uid.require'   => '用户ID不能为空~',
        'uid.number'    => '用户ID格式不正确~',
        'uid.unique'    => '该用户已被激活~',
        'code.require'  => '激活码不能为空~',
        'code.length'   => '激活码格式不对~',
    ];
}
