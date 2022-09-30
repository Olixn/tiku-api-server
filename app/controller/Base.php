<?php

namespace app\controller;

use think\Response;

abstract class Base
{
    protected $ip;
    protected $sign;

    public function __construct()
    {
        $this->ip = request()->ip();
        $this->sign = env('sign');
    }

    protected function create($data, string $msg = '',int $code = 200,string $type = "json"): Response
    {
        $result = [
            'code' => $code,
            'data' => $data,
            'msg'  => $msg
        ];
        return Response::create($result,$type,$code);
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->create([],'资源不存在~',404);
    }
}