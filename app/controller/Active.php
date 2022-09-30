<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Codes;
use think\exception\ValidateException;
use think\Request;
use app\validate\Active as ActiveValidate;
use app\model\Codes as CodesModel;
use app\model\Users as UsersModel;

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
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data = $request->param();

        try {
            // 验证传入参数
            validate(ActiveValidate::class)->check($data);
        } catch (ValidateException $e) {
            // 验证出错
            return $this->create('',$e->getError(),400);
        }

        $res = CodesModel::where('code',$data['code'])->field(['id','status','time'])->find();
        if ($res && $res['status']) {
            $user = UsersModel::create([
                'uid'       => $data['uid'],
                'code'      => $data['code'],
                'ip'        => $this->ip,
                'end_time'  => 864000 * (int)$res['time'] + time()
            ]);
            $code = CodesModel::update(['status' => 0],['id'=>$res['id']]);
            if ($user->isEmpty() || $code->isEmpty()) {
                return $this->create('', '用户激活失败！', 400);
            }
            return $this->create('','用户激活成功~',201);
        }

        return $this->create('','用户激活失败，激活码不存在或已被使用',400);
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
}
