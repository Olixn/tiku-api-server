<?php
declare (strict_types=1);

namespace app\controller;

use app\model\Codes as CodesModel;
use app\model\Tiku as TikuModel;
use app\model\Users as UsersModel;
use app\model\Wenti as WentiModel;
use app\validate\Admin as AdminValidate;
use think\exception\ValidateException;
use think\facade\Validate;
use think\Request;
use think\Response;


class Admin extends Base
{

    /**
     * 录入激活码
     *
     * @param \think\Request $request
     * @return Response
     */
    public function save(Request $request)
    {
        $data = $request->param();

        try {
            validate(AdminValidate::class)->check($data);
        } catch (ValidateException $e) {
            return $this->create('', $e->getError(), 400);
        }

        if (!$data['sign'] || $data['sign'] != md5($this->sign)) {
            return $this->create('', 'sign error ~', 400);
        }

        $code = CodesModel::create([
            'code' => $data['code'],
            'time' => $data['time'] ? (int)$data['time'] : 365
        ]);

        if ($code->isEmpty()) {
            return $this->create('', '激活码添加失败~', 400);
        }

        return $this->create('', '激活码添加成功~');
    }

    /**
     * 录入用户
     *
     * @param Request $request
     * @return Response
     */
    public function saveUser(Request $request): Response
    {
        $data = $request->param();

        $validate = Validate::rule([
            'sign' => 'require',
            'uid' => 'require|number|unique:users',
            'ip' => 'require',
            'code' => 'require',
            'time' => 'require|number'
        ])->message([
            'sign.require' => "权限验证失败",
            'uid.require' => "权限验证失败，用户ID不能为空！",
            'uid.number' => "权限验证失败，用户ID不合法！",
        ]);

        if (!$validate->check($data)) {
            return $this->create('', $validate->getError(), 400);
        }

        if (!$data['sign'] || $data['sign'] != md5($this->sign)) {
            return $this->create('', 'sign error ~', 400);
        }

        $res = UsersModel::create([
            'uid' => $data['uid'],
            'code' => $data['code'],
            'end_time' => $data['time'],
            'ip' => $data['ip']
        ]);

        return $this->create(['userId' => $res['id']], 'success', 200);
    }

    /**
     * 监控到期用户
     *
     * @param Request $request
     * @return Response
     */
    public function cron(Request $request)
    {
        $sign = $request->param('sign', '');

        if (!$sign || $sign != md5($this->sign)) {
            return $this->create('', 'sign error ~', 400);
        }

        $cur_time = time();

        $res = UsersModel::where('end_time', '<=', $cur_time)->delete();

        if ($res) {
            return $this->create('', 'delete user ~');
        }

        return $this->create('', 'cron is running~');
    }

    /**
     * 获取单条问题
     *
     * @param Request $request
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function outPut(Request $request): Response
    {
        $sign = $request->param('sign', '');

        if (!$sign || $sign != md5($this->sign)) {
            return $this->create('', 'sign error ~', 400);
        }


        // (new WentiModel())->find(); tp6 好像用不了了？
        // (new Wenti())->order('id desc')->find(); 可以。
        $res = (new WentiModel())->field(['question', 'hash'])->order('id ASC')->find();
        if ($res) {
            (new WentiModel())->where('hash', $res['hash'])->delete();
            return $this->create($res, '', 200);
        }

        return $this->create('', '无题目~', 400);
    }

    /**
     * 系统统计信息
     *
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sysStatus(): Response
    {
        $t = (new TikuModel())->order('id DESC')->find();
        $this->redis->select(2);
        $r = $this->redis->dbSize();

        return $this->create(['online' => $r, 'total' => $t['id'], 'update_at' => $t['create_time']]);
    }

}
