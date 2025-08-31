<?php

namespace Ledc\GatewayWorker;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use InvalidArgumentException;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

/**
 * Workerman进程管理器
 */
class Process
{
    /**
     * 初始化
     * - 在主进程设置静态属性等
     * @return void
     */
    public static function run(): void
    {
        // 限定CLI
        if (!in_array(PHP_SAPI, ['cli', 'mirco'], true)) {
            exit("You must run the CLI environment\n");
        }
        // 永不超时
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        // 内存限制，如果外面设置的内存比 /etc/php/php-cli.ini 大，就不要设置了
        if (intval(ini_get("memory_limit")) < 1024) {
            ini_set('memory_limit', '1024M');
        }

        $app = config('app');
        if (isset($app['error_reporting'])) {
            error_reporting($app['error_reporting']);
        } else {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        }
        if ($timezone = $app['default_timezone'] ?? null) {
            date_default_timezone_set($timezone);
        }

        static::setMaster();
        static::startProcess();
        Worker::runAll();
    }

    /**
     * 在Master进程设置属性
     * @return void
     */
    protected static function setMaster(): void
    {
        $default = config('gateway_worker.default', []);
        Worker::$onMasterStop = function () {
            echo date('Y-m-d H:i:s') . ' GatewayWorker onMasterStop' . PHP_EOL;
        };
        Worker::$onMasterReload = function () {
            if (function_exists('opcache_get_status') && function_exists('opcache_invalidate')) {
                if ($status = opcache_get_status()) {
                    if (isset($status['scripts']) && $scripts = $status['scripts']) {
                        foreach (array_keys($scripts) as $file) {
                            opcache_invalidate($file, true);
                        }
                    }
                }
            }
        };
        Worker::$pidFile = $default['pid_file'];
        Worker::$stdoutFile = $default['stdout_file'];
        Worker::$logFile = $default['log_file'];
        Worker::$eventLoopClass = $default['event_loop'];
        TcpConnection::$defaultMaxPackageSize = $default['max_package_size'];
        if (property_exists(Worker::class, 'statusFile')) {
            Worker::$statusFile = $default['status_file'];
        }
        if (property_exists(Worker::class, 'stopTimeout')) {
            Worker::$stopTimeout = $default['stop_timeout'];
        }
    }

    /**
     * 启动Worker容器实例
     * @return int
     */
    protected static function startProcess(): int
    {
        $i = 0;
        // 启动进程
        $process = config('gateway_worker.process', []);
        /**
         * @var string $name 进程名称
         * @var array $config 进程配置
         */
        foreach ($process as $name => $config) {
            $enable = $config['enable'] ?? false;
            if (empty($enable)) {
                continue;
            }

            $handler = $config['handler'];
            $listen = $config['listen'] ?? '';
            $context = $config['context'] ?? [];
            $properties = $config['properties'] ?? [];
            /** @var Register|Gateway|BusinessWorker $worker */
            $worker = new $handler($listen, $context);
            static::setWorkerProperties($worker, $name, $properties);
            $i++;
        }

        if (0 === $i) {
            throw new InvalidArgumentException('No process to start');
        }
        return $i;
    }

    /**
     * 设置属性
     * - 支持workerman的所有属性
     * @param Worker $worker Worker容器实例
     * @param string $name 进程名称
     * @param array $properties Worker容器属性
     * @return void
     */
    protected static function setWorkerProperties(Worker $worker, string $name, array $properties): void
    {
        $worker->name = $name;
        unset($properties['name']);
        foreach ($properties as $property => $value) {
            $worker->{$property} = $value;
        }
    }
}
