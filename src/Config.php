<?php

namespace Ledc\GatewayWorker;

/**
 * 插件配置管理类
 */
class Config
{
    /**
     * 获取插件配置（app.php文件）
     * @param string|null $key
     * @param mixed|null $default
     * @return array
     */
    public static function get(?string $key = null, mixed $default = null): mixed
    {
        if (null === $key || '' === $key) {
            return config(Install::PLUGIN_CONFIG_PREFIX . '.app', $default);
        }
        return config(Install::PLUGIN_CONFIG_PREFIX . '.app.' . ltrim($key, '.'), $default);
    }

    /**
     * 获取通信密钥
     * @return string
     */
    public static function getGatewaySecretKey(): string
    {
        return getenv('GATEWAY_SECRET_KEY') ?: '';
    }

    /**
     * 获取注册中心监听地址
     * @return string
     */
    public static function getGatewayRegisterListenAddress(): string
    {
        return getenv('GATEWAY_REGISTER_LISTEN_ADDRESS') ?: '127.0.0.1';
    }

    /**
     * 获取注册中心监听端口
     * @return int
     */
    public static function getGatewayRegisterListenPort(): int
    {
        return (int)(getenv('GATEWAY_REGISTER_PORT') ?: 1236);
    }

    /**
     * 获取注册中心地址
     * @return string
     */
    public static function getGatewayRegisterAddress(): string
    {
        return getenv('GATEWAY_REGISTER_ADDRESS') ?: '127.0.0.1';
    }

    /**
     * 获取网关本机IP（分布式部署时使用内网IP）
     */
    public static function getGatewayLocalIp(): string
    {
        return getenv('GATEWAY_LOCAL_IP') ?: '127.0.0.1';
    }

    /**
     * 获取网关内部通讯起始端口
     * @return int
     */
    public static function getGatewayStartPort(): int
    {
        return (int)(getenv('GATEWAY_START_PORT') ?: 5000);
    }
}
