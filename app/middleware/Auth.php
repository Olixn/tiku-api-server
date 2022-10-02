<?php

namespace app\middleware;

use app\controller\Base;
use Closure;
use think\Request;
use think\Response;
use app\common\Jwt as JwtUtils;

class Auth extends Base

{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        //第一步先取token
        // var_dump($request->header('Authorization'));
        $token = $request->header('Authorization');
        //jwt进行校验token
        if (!$token) {
            $token = '';
        }
        $res = (new JwtUtils())->verifyJwt($token);
        if ($res['status'] != 1001) {
            return $this->create('',$res['msg'],400);
        }
        return $next($request);
    }

}