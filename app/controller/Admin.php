<?php
declare (strict_types = 1);

namespace app\controller;

use think\exception\ValidateException;
use think\Request;
use app\validate\Admin as AdminValidate;
use app\model\Codes as CodesModel;
use app\model\Users as UsersModel;


class Admin extends Base
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
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request->param();

        try {
            validate(AdminValidate::class)->check($data);
        } catch (ValidateException $e) {
            return $this->create('',$e->getError(),400);
        }

        if (!$data['sign'] || $data['sign'] != md5($this->sign)) {
            return $this->create('','sign error ~',400);
        }

        $code = CodesModel::create([
            'code' => $data['code'],
            'time' => $data['time'] ? (int)$data['time'] : 365
        ]);

        if ($code->isEmpty()) {
            return $this->create('','激活码添加失败~',400);
        }

        return $this->create('','激活码添加成功~');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 监控到期用户
     *
     * @param Request $request
     * @return \think\Response
     */
    public function cron(Request $request)
    {
        $sign = $request->param('sign','');

        if (!$sign || $sign != md5($this->sign)) {
            return $this->create('','sign error ~',400);
        }

        $cur_time = time();

        $res = UsersModel::where('end_time','<=',$cur_time)->delete();

        if ($res) {
            return $this->create('','delete user ~');
        }

        return $this->create('','cron is running~');
    }
}
