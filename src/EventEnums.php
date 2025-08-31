<?php

namespace Ledc\GatewayWorker;

use Webman\Event\Event;

/**
 * BusinessWorker进程，所支持的Webman事件枚举
 */
enum EventEnums
{
    /**
     * Worker进程启动时触发
     */
    case onWorkerStart;
    /**
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发的回调函数
     */
    case onConnect;
    /**
     * 当客户端连接上gateway完成websocket握手时触发的回调函数
     */
    case onWebSocketConnect;
    /**
     * 当客户端发来数据(Gateway进程收到数据)后触发的回调函数
     */
    case onMessage;
    /**
     * 客户端与Gateway进程的连接断开时触发
     */
    case onClose;
    /**
     * 当businessWorker进程退出时触发
     */
    case onWorkerStop;
    /**
     * 收到ping心跳包
     */
    case onPing;

    /**
     * 获取事件完整名称
     * @return string
     */
    public function getEventName(): string
    {
        return 'BusinessWorker.' . $this->name;
    }

    /**
     * 绑定事件
     * @param callable $fn
     * @return int
     */
    public function on(callable $fn): int
    {
        return Event::on($this->getEventName(), $fn);
    }

    /**
     * 移除事件
     * @param int $id
     * @return int
     */
    public function off(int $id): int
    {
        return Event::off($this->getEventName(), $id);
    }

    /**
     * 触发事件
     * @param mixed $data
     * @param bool $halt
     * @return array|null|mixed
     */
    public function emit(mixed $data, bool $halt = false): mixed
    {
        return Event::emit($this->getEventName(), $data, $halt);
    }
}
