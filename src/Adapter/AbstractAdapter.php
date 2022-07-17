<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Thingston\Log\Exception\InvalidArgumentException;

abstract class AbstractAdapter implements AdapterInterface
{
    protected ?LoggerInterface $logger = null;

    abstract protected function createLogger(): LoggerInterface;

    /**
     * @param string $name
     * @param array<string, mixed> $arguments
     * @param LogLevel::* $level
     * @param bool $shouldBubble
     */
    public function __construct(
        protected string $name,
        protected array $arguments = [],
        protected string $level = LogLevel::DEBUG,
        protected bool $shouldBubble = true
    ) {
        $levels = [LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::DEBUG, LogLevel::EMERGENCY,
            LogLevel::ERROR, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING];

        if (false === in_array($level, $levels)) {
            throw new InvalidArgumentException('Invalid level argument value.');
        }

        $this->name = $name;
        $this->arguments = $arguments;
        $this->level = $level;
        $this->shouldBubble = $shouldBubble;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function shouldBubble(): bool
    {
        return $this->shouldBubble;
    }

    /**
     * @param mixed $level
     * @param string|Stringable $message
     * @param array<mixed> $context
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        if (null === $this->logger) {
            $this->logger = $this->createLogger();
        }

        $this->logger->log($level, $message, $context);
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
