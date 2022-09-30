<?php

namespace app\model;

use think\Model;

class Codes extends Model
{
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'status'      => 'int',
        'code'        => 'string',
        'time'        => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
    ];
    // 自动时间戳
    protected $autoWriteTimestamp = true;
}