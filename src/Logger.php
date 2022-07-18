<?php

declare(strict_types=1);

namespace Thingston\Log;

use Psr\Log\LoggerInterface;
use Stringable;
use Thingston\Log\Adapter\AdapterInterface;

final class Logger implements LoggerInterface
{
    use LoggerInterfaceTrait;

    /**
     * @param AdapterInterface[] $adapters
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
}
