<?php

declare(strict_types=1);

namespace Thingston\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Thingston\Log\Adapter\AdapterInterface;
use Thingston\Log\Exception\InvalidArgumentException;
use Thingston\Settings\SettingsInterface;

final class LogManager implements LogManagerInterface
{
    use LoggerInterfaceTrait;

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

        $adapters = [];

        foreach ($this->resolveConfig($name) as $config) {
            $adapters[] = $this->createAdapter($name, $config);
        }

        return $this->loggers[$name] = new Logger($adapters);
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

        if (isset($config[LogSettings::ADAPTER])) {
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
     * @param string $name
     * @param array<string, mixed> $config
     * @return AdapterInterface
     */
    private function createAdapter(string $name, array $config): AdapterInterface
    {
        if (false === isset($config[LogSettings::ADAPTER])) {
            throw InvalidArgumentException::forMissingAdapterClass();
        }

        $arguments = $config[LogSettings::ARGUMENTS] ?? [];

        if (false === is_array($arguments)) {
            throw InvalidArgumentException::forInvalidConfigType(gettype($arguments), LogSettings::ARGUMENTS);
        }

        $class = $config[LogSettings::ADAPTER];

        if (false === is_string($class) || false === is_a($class, AdapterInterface::class, true)) {
            throw InvalidArgumentException::forInvalidAdapterClass();
        }

        $arguments['name'] = $name;

        /** @var AdapterInterface $adapter */
        $adapter = $class::create($arguments);

        return $adapter;
    }

    public function log($level, string|Stringable $message, mixed $context = []): void
    {
        $this->getLogger()->log($level, $message, $context);
    }
}
