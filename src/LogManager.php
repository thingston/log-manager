<?php

declare(strict_types=1);

namespace Thingston\Log;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Thingston\Log\Exception\InvalidArgumentException;
use Thingston\Settings\SettingsInterface;

class LogManager implements LogManagerInterface
{
    /**
     * @var array<string, LoggerInterface>
     */
    private array $loggers = [];

    private SettingsInterface $settings;

    public function __construct(?SettingsInterface $settings = null)
    {
        $this->settings = $settings ?? new LogSettings();
    }

    public function getLogger(?string $name = null): LoggerInterface
    {
        if (null === $name) {
            $name = LogSettings::DEFAULT;
        }

        if (isset($this->loggers[$name])) {
            return $this->loggers[$name];
        }

        $handlers = [];

        foreach ($this->resolveConfig($name) as $config) {
            $handlers[] = $this->createHandler($config);
        }

        return $this->loggers[$name] = new Logger($name, $handlers);
    }

    /**
     * @param string $name
     * @return array<array<string, mixed>>
     */
    private function resolveConfig(string $name): array
    {
        if (false === $this->settings->has($name)) {
            throw InvalidArgumentException::forInvalidLoggerName($name);
        }

        $config = $this->settings->get($name);

        if (is_string($config)) {
            return $this->resolveConfig($config);
        }

        if (false === is_array($config)) {
            throw InvalidArgumentException::forInvalidConfigType(gettype($config), $name);
        }

        if (isset($config[LogSettings::HANDLER])) {
            return [$config];
        }

        $configs = [];

        foreach ($config as $params) {
            if (is_string($params)) {
                $configs = array_merge($configs, $this->resolveConfig($params));
                continue;
            }

            if (false === is_array($params)) {
                throw InvalidArgumentException::forInvalidConfigType(gettype($params), $name);
            }

            $configs[] = $params;
        }

        return $configs;
    }

    /**
     * @param array<string, mixed> $config
     * @return HandlerInterface
     */
    private function createHandler(array $config): HandlerInterface
    {
        if (false === isset($config[LogSettings::HANDLER])) {
            throw InvalidArgumentException::forMissingHandlerClass();
        }

        $arguments = $config[LogSettings::ARGUMENTS] ?? [];

        if (false === is_array($arguments)) {
            throw InvalidArgumentException::forInvalidConfigType(gettype($arguments), LogSettings::ARGUMENTS);
        }

        $class = $config[LogSettings::HANDLER];

        if (false === is_string($class) || false === is_a($class, HandlerInterface::class, true)) {
            throw InvalidArgumentException::forInvalidHandlerClass();
        }

        /** @var HandlerInterface $handler */
        $handler = new $class(...$arguments);

        return $handler;
    }

    public function alert(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function debug(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function emergency(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function error(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function info(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function log($level, string|Stringable $message, mixed $context = []): void
    {
        $this->getLogger()->log($level, $message, $context);
    }

    public function notice(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function warning(string|Stringable $message, mixed $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
}
