<?php
declare (strict_types=1);

namespace app\controller;

use app\model\Codes;
use app\model\Codes as CodesModel;
use app\model\Users as UsersModel;
use app\validate\Active as ActiveValidate;
use think\exception\ValidateException;
use think\Request;

class Active extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return redirect('/');
    }

    /**
     * 用户激活
     *
     * @param \think\Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save(Request $request)
    {
        $data = $request->param('data');

        try {
            // 验证传入参数
            validate(ActiveValidate::class)->check($data);
        } catch (ValidateException $e) {
            // 验证出错
            return $this->create('', $e->getError(), 400);
        }

        $res = CodesModel::where('code', $data['code'])->field(['id', 'status', 'time'])->find();
        if ($res && $res['status']) {
            $user = UsersModel::create([
                'uid' => $data['uid'],
                'code' => $data['code'],
                'ip' => $this->ip,
                'end_time' => 86400 * (int)$res['time'] + time()
            ]);
            $code = CodesModel::update(['status' => 0], ['id' => $res['id']]);
            if ($user->isEmpty() || $code->isEmpty()) {
                return $this->create('', '用户激活失败！', 400);
            }
            return $this->create('', '用户激活成功~', 201);
        }

        return $this->create('', '用户激活失败，激活码不存在或已被使用', 400);
    }

}
