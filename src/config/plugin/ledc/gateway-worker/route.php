<?php
/**
 * 这个文件会在更新时，强制覆盖
 */

use Webman\Http\Request;
use Webman\Route;

Route::get('/ledc/gateway-worker/config', function (Request $request) {
    $protocol = $request->header('x-forwarded-proto', 'https');
    $wss_protocol = 'http' === $protocol ? 'ws://' : 'wss://';
    $host = $request->host();
    $data = [
        // URL
        'url' => $wss_protocol . $host,
        // WebSocKet
        'websocket' => '/websocket',
        // 鉴权
        'auth' => '/ledc/gateway-worker/auth',
    ];
    return json(['code' => 0, 'data' => $data, 'msg' => 'ok']);
});
