<?php

namespace app\controller;

use Redis;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\Response;

/**
 *
 */
abstract class Base
{
    /**
     * @var string
     */
    protected $ip;
    /**
     * @var mixed
     */
    protected $sign;
    /**
     * @var Redis
     */
    protected $redis;

    /**
     *
     */
    public function __construct()
    {
        $this->redis = new Redis();
        $r = $this->redis->connect(
            env('redis.host','127.0.0.1'),
            env('redis.port','6379'),
            2.5
        );
        if (!$r) {
            throw new HttpResponseException($this->create('','Redis error ~',500));
        }


        $this->ip = request()->ip();
        $this->sign = env('sign');
    }

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @param string $type
     * @return Response
     */
    protected function create($data, string $msg = '', int $code = 200, string $type = "json"): Response
    {
        $result = [
            'code' => $code,
            'data' => $data,
            'msg'  => $msg
        ];
        return Response::create($result,$type,$code);
    }

    /**
     * @param $name
     * @param $arguments
     * @return Response
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->create([],'资源不存在~',404);
    }
}