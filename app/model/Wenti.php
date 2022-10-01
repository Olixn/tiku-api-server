<?php

namespace app\model;

use think\Model;

class Wenti extends Model
{

    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'hash'        => 'string',
        'question'    => 'string',
        'ip'          => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];
    // 自动时间戳
    protected $autoWriteTimestamp = true;
}