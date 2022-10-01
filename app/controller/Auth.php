<?php

namespace app\controller;

use app\common\Jwt as JwtUtils;
use app\model\Users as UsersModel;
use think\facade\Validate;
use think\Request;
use think\Response;

/**
 * 用户授权认证
 */
class Auth extends Base
{

    protected $version = "1.7.2";
    protected $activeUrl = "http://127.0.0.1/active.html?uid=";

    /**
     * @param Request $request
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(Request $request): Response
    {
        $data = $request->param();

        $validate = Validate::rule([
            'uid' => 'require|number'
        ])->message([
            'uid.require' => "权限验证失败，用户ID不能为空！",
            'uid.number' => "权限验证失败，用户ID不合法！",
        ]);
        if (!$validate->check($data)) {
            return $this->create('', $validate->getError(), 400);
        }

        $user = UsersModel::where('uid', $data['uid'])->select();
        if ($user->isEmpty()) {
            $rep = [
                "version" => $this->version,
                "notice" => "<div><img src='https://pic.521daigua.cn/qr.png' width='100%'><p style='color:red;text-align:center;'>Tips:一个纯粹的搜题公众号，脚本没有答案可以试试哦</p><p style='color:red;text-align:center;'>AD:如何给自己的公众号添加搜题功能？<a href='http://ydfq.pkbff.com' target='_blank'>点击了解</a></p><hr><p style='color:green;text-align:center;'>用户ID:" . $data['uid'] . "，未授权，<a href='" . $this->activeUrl . $data['uid'] . "' >点此授权</a></p></div>",
                "token" => ""
            ];
            return $this->create($rep, '用户未授权！');
        }

        $this->redis->select(3);
        $token = (new JwtUtils())->createJwt($data['uid']);
        $this->redis->set($data['uid'], $token, 18000);

        $rep = [
            "version" => $this->version,
            "notice" => "<div><img src='https://pic.521daigua.cn/qr.png' width='100%'><p style='color:red;text-align:center;'>Tips:一个纯粹的搜题公众号，脚本没有答案可以试试哦</p><p style='color:red;text-align:center;'>AD:如何给自己的公众号添加搜题功能？<a href='http://ydfq.pkbff.com' target='_blank'>点击了解</a></p><hr><p style='color:green;text-align:center;'>用户ID:" . $data['uid'] . "|当前版本:" . $data['v'] . "|最新版本:" . $this->version . "</p><p>请务必更新至最新版脚本(v1.7.2)，低版本脚本会出现各种问题，强烈建议使用Microsoft Edge浏览器+ScriptCat脚本猫运行此脚本，否则会出现一些睿智的bug！！脚本失效、问题反馈邮箱：nawlgzs@gmail.com</p><p style='text-align:right;width:100%;margin:0;'><a href='https://scriptcat.org/script-show-page/639' target='_blank' style='color: #9933CC;'>点击获取最新版脚本</a>|<a href='https://www.bilibili.com/video/BV1t14y1a7gn' target='_blank' style='color:red;'>📔答案收录教程</a>|<a href='https://scriptcat.org' target='_blank' style='color:#FF6633;'>ScriptCat脚本猫</a></p><br /><p style='text-align:right;width:100%;margin:0;'><a href='https://t.me/beijiyulin' target='_blank' style='color:green;'>Telegram</a>|<a href='http://t.cn/A6qFbO2t' target='_blank'>省钱不吃土群</a>|<a href='http://bbs.tampermonkey.net.cn/?fromuser=Ne-21' target='_blank' style='color:#FF6633;' >油猴中文网</a></p></div>",
            "token" => $token
        ];

        return $this->create($rep);
    }
}