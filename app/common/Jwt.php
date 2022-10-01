<?php

namespace app\common;

use Firebase\JWT\JWT as JWTUtil;
use Firebase\JWT\Key;
use app\model\Users as UsersModel;

class Jwt
{
    //生成token
    public function createJwt($userId = 'zq'): string
    {
        $key = md5('Ne-21!@!'); //jwt的签发密钥，验证token的时候需要用到
        $time = time(); //签发时间
        $expire = $time + 10800; //过期时间
        $token = array(
            "user_id" => $userId,
            "iss" => "https://api.gocos.cn/",//签发组织
            "aud" => "Ne-21", //签发作者
            "iat" => $time,
            "nbf" => $time,
            "exp" => $expire
        );
        return JWTUtil::encode($token, $key,'HS256');
    }


    //校验jwt权限API
    public function verifyJwt(string $jwt)
    {
        $key = md5('Ne-21!@!');
        try {
            $jwtAuth = json_encode(JWTUtil::decode($jwt, new Key($key, 'HS256')));
            $authInfo = json_decode($jwtAuth, true);

            $msg = [];
            if (!empty($authInfo['user_id']) && (new UsersModel())->where('uid',$authInfo['user_id'])->find()) {
                $msg = [
                    'status' => 1001,
                    'msg' => '验证通过'
                ];
            } else {
                $msg = [
                    'status' => 1002,
                    'msg' => '验证不通过，用户不存在，请激活脚本！'
                ];
            }
            return $msg;
        }  catch (\Firebase\JWT\ExpiredException $e) {
            return [
                'status' => 1003,
                'msg' => '验证过期，请刷新页面，重新获取验证'
            ];
            exit;
        } catch (\Exception $e) {
            return [
                'status' => 1002,
                'msg' => '无效的token，请激活脚本！'
            ];
        }
    }
}