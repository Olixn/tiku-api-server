<?php
declare (strict_types=1);

namespace app\controller;

use app\common\Utils;
use app\model\Tiku as TikuModel;
use app\model\Wenti as WentiModel;
use think\Request;
use think\Response;

class Chaoxing extends Base
{
    /**
     * Tips公告
     *
     * @return Response
     */
    public function tips()
    {
        $rep = [
            'msg' => "倍速播放、秒过会导致不良记录且清空学习进度！<br />题库在慢慢补充，搜不到的题目系统会记录并在后台进行异步更新，请换个时间再试。<br /><span style='color: red;'>题库自助收录视频教程：</span><a href='https://www.bilibili.com/video/BV1t14y1a7gn' style='color: blue;' target='_blank'>点击观看</a><br /><a href='https://scriptcat.org/script-show-page/639' style='color: blue;' target='_blank'>脚本源代码托管(v.1.7.2)</a>"
        ];

        return $this->create($rep);
    }

    /**
     * 查询题目
     *
     * @param \think\Request $request
     * @return Response
     */
    public function queryAnswer(Request $request): Response
    {
        $question = $request->param('question');

        if (!$question) {
            $rep = [
                'answer' => ""
            ];
            return $this->create($rep, '题目为空', 400);
        }

        // 过滤题目标点等
        $q = (new Utils())->filterStr($question);
        $q_hash = md5($q);

        // Redis中查询
        $this->redis->select(1);
        $res = $this->redis->get($q_hash);
        if ($res) {
            $rep = ['answer' => $res];
            return $this->create($rep, '', 200);
        }

        // 数据库查询
        $res = (new TikuModel())->where('hash', $q_hash)->field('answer')->find();
        if ($res) {
            $this->redis->set($q_hash, $res['answer'], 3600);
            $rep = ['answer' => $res['answer']];
            return $this->create($rep, '', 200);
        }

        // 题库中无此题，存入数据库
        if ((new WentiModel())->where('hash', $q_hash)->select()->isEmpty()) {
            WentiModel::create([
                'question' => $question,
                'hash' => $q_hash,
                'ip' => $this->ip
            ]);
        }

        $rep = ['answer' => ""];
        return $this->create($rep, '暂无答案', 400);
    }


    /**
     * 录入单条题目
     *
     * @param Request $request
     * @return Response
     */
    public function saveOneQuestion(Request $request): Response
    {
        $question = $request->param('question');
        $answer = $request->param('answer');

        if (!$question || !$answer) {
            return $this->create('', '录入参数不全', 400);
        }

        $q = (new Utils())->filterStr($question);
        $q_hash = md5($q);

        $t = (new TikuModel())->where('hash', $q_hash)->field(['id'])->find();
        if (!$t) {
            TikuModel::create([
                'hash' => $q_hash,
                'question' => $q,
                'answer' => $answer,
                'ip' => $this->ip
            ]);
            $msg = '录入';
        } else {
            TikuModel::update(['answer' => $answer], ['id' => $t['id']]);
            $msg = '更新';
        }

        return $this->create('', $msg, 200);
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        //
    }
}
