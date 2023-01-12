<?php
declare (strict_types=1);

namespace app\middleware;

use app\controller\Base;
use Closure;
use think\Request;
use think\Response;

class ServeLimit extends Base
{

    // 限制时间(秒)
    private $duration = 60;
    // 限制次数
    private $number = 60;

    /**
     * IP流量控制
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        //判断是否可访问，1：可访问 0：不可访问
        $isCan = $this->isCanAccess($ip);
        if ($isCan == "1") {
            return $next($request);
        } else {
            return $this->create('访问太快了~触发流控限制，请稍后再试！', '访问太快了~触发流控限制，请稍后再试！', 200);
        }
    }

    /**
     * 判断是否允许访问
     *
     * @param $ip
     * @return mixed|\Redis
     */
    private function isCanAccess($ip)
    {
        $day = date("Ymd");
        $key = $day . "_" . $ip;
        //访问redis,用lua脚本判断是否可访问
        $lua = <<<SCRIPT
local key = KEYS[1];
local limit = tonumber(KEYS[2])
local duration = tonumber(KEYS[3])
--redis.log(redis.LOG_NOTICE,' duration: '..duration)
local current = redis.call('GET', key)
if current == false then
   --redis.log(redis.LOG_NOTICE,key..' is nil ')
   redis.call('SET', key,1)
   redis.call('EXPIRE',key,duration)
   --redis.log(redis.LOG_NOTICE,' set expire end')
   return '1'
else
   --redis.log(redis.LOG_NOTICE,key..' value: '..current)
   local num_current = tonumber(current)
   if num_current+1 > limit then
       return '0'
   else
       redis.call('INCRBY',key,1)
       return '1'
   end
end
SCRIPT;

        $this->redis->select(2);
        $duration = $this->duration;   //时长，以秒为单位
        $number = $this->number;      //允许访问的次数
        $isSucc = $this->redis->eval($lua, [$key, $number, $duration], 3);
        return $isSucc;
    }
}
