<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Date: 2021-08-26 22:54:03
// +----------------------------------------------------------------------
// | Desc: 宪法小卫士
// +----------------------------------------------------------------------
// | Author: Ne-21
// +----------------------------------------------------------------------

namespace app\controller;

use app\common\Utils;
use app\model\Xfp as XfModel;
use think\exception\HttpException;
use think\Request;

class Xfp
{
    protected $courseData;


    public function getAnswer(Request $request)
    {
        if ($request->has('v')) {
            if ($request->param('v') != 3) {
                return json([
                    'code' => 0,
                    'msg' => '请更新脚本https://greasyfork.org/zh-CN/scripts/430038',
                    'data' => ''
                ]);
            }
        } else {
            return json([
                'code' => 0,
                'msg' => '请更新脚本https://greasyfork.org/zh-CN/scripts/430038',
                'data' => ''
            ]);
        }


        if ($request->has('data')) {

            $data = json_decode(urldecode($request->param('data')), true);
            $question = (new Utils())->filterStr($data['question']);
            $answerops = (new Utils())->filterStr($data['answerops']);

            $result = XfModel::where('hash', $this->getHash($question . $answerops))->field(['content', 'answer', 'answerText'])->select();
            if ($result->isEmpty()) {
                return json([
                    'code' => 0,
                    'msg' => '无答案,请先打开各个练习来收集答案。作者博客https://blog.gocos.cn',
                    'data' => ''
                ]);
            }
            return json([
                'code' => 1,
                'msg' => '作者博客https://gocos.cn',
                'data' => $result
            ]);
        } else {
            throw new HttpException(404, '糟糕！出错了。');
        }
    }

    public function getHash($data)
    {
        return md5($data);
    }

    public function upload(Request $request)
    {
        if ($request->has('data')) {
            $this->courseData = $request->param('data');
            if ($this->courseData == '') {
                throw new HttpException(404, '糟糕！出错了。缺少必需的参数');
            }
        } else {
            throw new HttpException(404, '糟糕！出错了。缺少必需的参数');
        };

        $this->courseData = json_decode(urldecode($this->courseData), true);
        $data = $this->courseData['questionBankList'];
        foreach ($data as $value) {
            $content = (new Utils())->filterStr($value['content']);
            $answerops = (new Utils())->filterStr($value['answerOptions']);

            $options = preg_split('/@!@/', $value['answerOptions']);
            $answer = $value['answer'];
            $hash = $this->getHash($content . $answerops);
            $array = [
                'answer' => $answer,
                'answerText' => $options[$this->getAnswerId($answer)],
                'answerOptions' => $answerops,
                'content' => $content,
                'type' => $value['type'],
                'columnId' => $value['columnId'],
                'questionId' => $value['id'],
                'hash' => $hash,
            ];
            $this->saveAll($array);
        }
        return;

    }

    public function getAnswerId($answer)
    {
        $data = ["A" => 0, "B" => 1, "C" => 2, "D" => 3];
        return $data[$answer];
    }

    protected function saveAll($data)
    {
        $hash = $data['hash'];
        $xf = new XfModel();
        $a = $xf->where('hash', $hash)->select();
        if ($a->isEmpty()) {
            $xf->save($data);
        } else {
            $xf->update($data, ['hash' => $hash]);
        }
    }
}