<?php
declare (strict_types=1);

namespace app\controller;

use app\common\Utils;
use app\model\Tiku as TikuModel;
use app\model\Wenti as WentiModel;
use think\exception\ErrorException;
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
     * @param Request $request
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
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
     * 生成Enc加密串
     *
     * @param Request $request
     * @return Response
     */
    public function enc(Request $request): Response
    {
        $params = $request->param();

        if (!$params) {
            return $this->create('', '传入参数为空', 400);
        }

        try {
            $classId = $params['a'];
            $userId = $params['b'];
            $jobId = $params['c'];
            $objectId = $params['d'];
            $playingTime = $params['e'];
            $duration = $params['f'];
            $clipTime = $params['g'];
        } catch (ErrorException $e) {
            return $this->create('', '传入参数不全', 400);
        }


        $enc = sprintf("[%s][%s][%s][%s][%s][%s][%s][%s]",
            $classId,
            $userId,
            $jobId,
            $objectId,
            $playingTime * 1000,
            'd_yHJ!$pdA~5',
            $duration * 1000,
            $clipTime
        );

        return $this->create(['ne21enc' => md5($enc)]);
    }

    /**
     * 录入单条题目
     *
     * @param Request $request
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveOneQuestion(Request $request): Response
    {
        $question = $request->param('question');
        $answer = $request->param('answer');

        if (!$question || !$answer) {
            return $this->create('', '录入参数不全', 400);
        }

        $q = (new Utils())->filterStr($question);
        $msg = $this->save2db($q, $answer);

        return $this->create('', $msg, 200);
    }

    /**
     * 数据存入数据库
     *
     * @param string $q
     * @param string $answer
     * @param int $type
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function save2db(string $q, string $answer, int $type = 6): string
    {
        $q_hash = md5($q);

        $t = (new TikuModel())->where('hash', $q_hash)->field(['id'])->find();
        if (!$t) {
            TikuModel::create([
                'hash' => $q_hash,
                'type' => $type,
                'question' => $q,
                'answer' => $answer,
                'ip' => $this->ip
            ]);
            $msg = 'success';
        } else {
            TikuModel::update(['answer' => $answer], ['id' => $t['id']]);
            $msg = 'update';
        }
        return $msg;
    }

    /**
     * 录入多条题目
     *
     * @param Request $request
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveAllQuestion(Request $request): Response
    {
        $data = $request->param('data');

        if (!$data) {
            return $this->create('', '录入参数不全', 400);
        }

        $data = json_decode($data);

        $total = 0;
        $status = [];
        foreach ($data as $k => $v) {
            $q = (new Utils())->filterStr($v->question);
            $_m = $this->save2db($q, $v->answer, $v->type);
            $status[] = [$k + 1 => $_m];
            $total += 1;
        }
        return $this->create(['total' => $total, 'status' => $status], '', 200);
    }
}
