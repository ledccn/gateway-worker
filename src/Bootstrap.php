<?php

namespace Ledc\GatewayWorker;

use Workerman\Worker;
use GatewayWorker\Lib\Gateway as LibGateway;

/**
 * 进程启动时onWorkerStart时运行的回调配置
 * @link https://learnku.com/articles/6657/model-events-and-observer-in-laravel
 */
class Bootstrap implements \Webman\Bootstrap
{
    /**
     * @param Worker|null $worker
     * @return void
     */
    public static function start(?Worker $worker): void
    {
        if (class_exists(LibGateway::class)) {
            LibGateway::$registerAddress = Config::getGatewayRegisterAddress() . ':' . Config::getGatewayRegisterListenPort();
            LibGateway::$secretKey = Config::getGatewaySecretKey();
        }
    }
}
