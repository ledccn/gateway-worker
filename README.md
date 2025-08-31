# webman基础插件之 GatewayWorker

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
  proxy_pass http://127.0.0.1:3333;
  proxy_read_timeout 3600;
  proxy_http_version 1.1;
  proxy_set_header Upgrade $http_upgrade;
  proxy_set_header Connection "Upgrade";
  proxy_set_header X-Real-IP $remote_addr;
}
```

## Env环境变量

```env
GATEWAY_SECRET_KEY = 
GATEWAY_REGISTER_LISTEN_ADDRESS = 127.0.0.1
GATEWAY_REGISTER_ADDRESS = 127.0.0.1
GATEWAY_REGISTER_PORT = 1236
GATEWAY_LOCAL_IP = 127.0.0.1
GATEWAY_START_PORT = 4000
```