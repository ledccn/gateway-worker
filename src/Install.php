<?php

namespace Ledc\GatewayWorker;

/**
 * webman安装类
 */
class Install
{
    public const bool WEBMAN_PLUGIN = true;
    /**
     * 配置文件
     */
    public const string CONFIG_FILE = '/config/gateway_worker.php';
    /**
     * @var array
     */
    protected static array $pathRelation = [
        'config/plugin/ledc/gateway-worker' => 'config/plugin/ledc/gateway-worker',
    ];
    /**
     * 插件配置前缀
     * - 插件使用以下方法获取配置：config('plugin.厂商.插件名.配置文件.具体配置项');
     * - 例如：config('plugin.webman.push.app.app_key');
     */
    public const string PLUGIN_CONFIG_PREFIX = 'plugin.ledc.gateway-worker';

    /**
     * Install
     * @return void
     */
    public static function install(): void
    {
        static::installByRelation();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall(): void
    {
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     * @return void
     */
    public static function installByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            //symlink(__DIR__ . "/$source", base_path()."/$dest");
            // 强制覆盖 2025年8月31日
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest", true);
            echo "Create $dest" . PHP_EOL;
        }
        // 创建配置文件
        copy_dir(__DIR__ . self::CONFIG_FILE, base_path() . self::CONFIG_FILE);
    }

    /**
     * uninstallByRelation
     * @return void
     */
    public static function uninstallByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest" . PHP_EOL;
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
        // 删除配置文件
        if (is_file(base_path() . self::CONFIG_FILE) || is_link(base_path() . self::CONFIG_FILE)) {
            unlink(base_path() . self::CONFIG_FILE);
        }
    }
}
