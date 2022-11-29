<?php

namespace app\queue;

use app\model\Tiku as TikuModel;
use Exception;
use think\facade\Log;


class ChaoxingUpdate extends Queue
{
    /**
     * 消费题库更新数据业务
     *
     * @param $data
     * @return bool
     */
    protected function execute($data): bool
    {
        // TODO: Implement execute() method.
        $data = json_decode($data);
        try {
            $t = (new TikuModel())->where('hash', $data->hash)->field(['id'])->find();
            if (!$t) {
                TikuModel::create($data);
            } else {
                TikuModel::update($data, ['id' => $t['id']]);
            }
            return true;
        } catch (Exception $e) {
            Log::error(sprintf('[%s][%s][%s]', __CLASS__, __FUNCTION__,$e->getMessage()));
            return false;
        }

    }


}