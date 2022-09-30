<?php

namespace app\model;

use think\Model;

class Users extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'uid'         => 'int',
        'status'      => 'int',
        'code'        => 'string',
        'ip'          => 'string',
        'end_time'    => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];
    // 自动时间戳
    protected $autoWriteTimestamp = true;
}