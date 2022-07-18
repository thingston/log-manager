<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Thingston\Log\Exception\InvalidArgumentException;
use Thingston\Log\LoggerInterfaceTrait;
use Throwable;

abstract class AbstractAdapter implements AdapterInterface
{
    use LoggerInterfaceTrait;

    protected ?LoggerInterface $logger = null;

    abstract protected function createLogger(): LoggerInterface;

    /**
     * @param string $name
     * @param LogLevel::* $level
     * @param bool $shouldBubble
     */
    public function __construct(
        protected string $name,
        protected string $level = LogLevel::DEBUG,
        protected bool $shouldBubble = true
    ) {
        $this->name = $this->assertName($name);
        $this->level = $this->assertLevel($level);
        $this->shouldBubble = $shouldBubble;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Psr\Log\LogLevel::*
     */
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

        try {
            $this->logger->log($level, $message, $context);
        } catch (Throwable $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param mixed $name
     * @return string
     */
    protected static function assertName($name): string
    {
        if (false === is_string($name) || '' === trim($name)) {
            throw new InvalidArgumentException('Adapter name must be a string and can\'t be empty.');
        }

        return $name;
    }

    /**
     * @param mixed $level
     * @return LogLevel::*
     */
    protected static function assertLevel($level): string
    {
        $levels = [LogLevel::ALERT, LogLevel::CRITICAL, LogLevel::DEBUG, LogLevel::EMERGENCY,
            LogLevel::ERROR, LogLevel::INFO, LogLevel::NOTICE, LogLevel::WARNING];

        foreach ($levels as $logLevel) {
            if ($level === $logLevel) {
                return $level;
            }
        }

        throw new InvalidArgumentException('Invalid level argument value.');
    }
}
