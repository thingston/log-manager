<?php

declare(strict_types=1);

namespace Thingston\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

final class Logger implements LoggerInterface
{
    /**
     * @param \Thingston\Log\Adapter\AdapterInterface[] $adapters
     */
    public function __construct(private array $adapters)
    {
        $this->adapters = $adapters;
    }

    /**
     * @param mixed $level
     * @param string|Stringable $message
     * @param array<mixed> $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        foreach ($this->adapters as $adapter) {
            $adapter->log($level, $message, $context);

            if (false === $adapter->shouldBubble()) {
                break;
            }
        }
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
