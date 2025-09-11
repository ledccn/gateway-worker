# webman基础插件之 GatewayWorker

## 简介

基于GatewayWorker的webman基础插件，通过命令行单独启动，重启 `webman` 时，不影响 `GatewayWorker` ，从而与业务解耦。

## 特性

- 命令行单独启动
- 可单独配置 `Register`、`Gateway`、`BusinessWorker` 各进程是否启动
- 内置通用的 `BusinessWorker` 事件处理类 `\Ledc\GatewayWorker\EventHandler`
- 内置事件枚举 `\Ledc\GatewayWorker\EventEnums`

## 安装

PHP版本：>=8.3

```shell
composer require ledc/gateway-worker
```

忽略扩展安装

```shell
composer require ledc/gateway-worker --ignore-platform-req=ext-posix -W
```

## 启动

```shell
php webman gateway:worker start
```

## 停止

```shell
php webman gateway:worker stop
```

## nginx配置

```conf
location /websocket
{
  proxy_pass http://127.0.0.1:这里是Gateway端口;
  proxy_read_timeout 3600;
  proxy_http_version 1.1;
  proxy_set_header Upgrade $http_upgrade;
  proxy_set_header Connection "Upgrade";
  proxy_set_header X-Real-IP $remote_addr;
}
```

## 唯一配置文件

`/config/gateway_worker.php`

配置文件内，有详细的注释，可以帮助您理解各种使用场景下的配置。

## 最佳实践

通过 `.env` 设置环境变量，以供 `/config/gateway_worker.php` 使用，配置项见下述。

## Env环境变量

```env
GATEWAY_SECRET_KEY = 
GATEWAY_REGISTER_LISTEN_ADDRESS = 127.0.0.1
GATEWAY_REGISTER_ADDRESS = 127.0.0.1
GATEWAY_REGISTER_PORT = 1236
GATEWAY_LOCAL_IP = 127.0.0.1
GATEWAY_START_PORT = 4000
```

## 应用场景举例

[与ThinkPHP等框架结合](https://www.workerman.net/doc/gateway-worker/work-with-other-frameworks.html)

## 与webman结合

以提供websocket服务为例：

### 1. 进程启动，返回连接参数

进程启动后 `\Ledc\GatewayWorker\EventHandler::onConnect`，服务会返回 `{"event":"init","client_id":"7f0000010fa0000000a2","timestamp":1757583080,"auth":"21826584bdaf086f7e9910baac281eef"}`

### 2. 前端页面转发 上述参数到后端webman接口

### 3. webman控制器接收到参数，验证用户登陆状态

验证通过后，调用 `\Ledc\GatewayWorker\EventHandler::bindUid` 绑定用户。

或者，调用 `\Ledc\GatewayWorker\EventHandler::joinGroup` 把客户端加入群组。

上述，也可同时操作（看业务需要）。

### 4. webman后端注册事件监听

#### 配置文件监听

编辑 `/config/event.php`，根据需要监听 `\Ledc\GatewayWorker\EventEnums` 内列举的枚举事件

#### 或者在Bootstrap监听

参考 [业务初始化](https://www.workerman.net/doc/webman/others/bootstrap.html)

比如你监听 前端 websocket连接的 `ping` 事件

只需要在 `Bootstrap` 内添加：

```php
// 监听ping事件
\Ledc\GatewayWorker\EventEnums::onPing->on(function (string $client_id) {
    // 你的业务代码
});
```

也可以继续注册其他事件的监听，事件调用时机或参数，请参考 `\Ledc\GatewayWorker\EventHandler`。
