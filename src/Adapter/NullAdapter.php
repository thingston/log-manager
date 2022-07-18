<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class NullAdapter extends AbstractAdapter
{
    /**
     * @param array<string, mixed> $arguments
     * @return AdapterInterface
     */
    public static function create(array $arguments): AdapterInterface
    {
        return new NullAdapter(
            name: static::assertName($arguments['name'] ?? null),
            level: static::assertLevel($arguments['level'] ?? \Psr\Log\LogLevel::DEBUG),
            shouldBubble: (bool) ($arguments['shouldBubble'] ?? true)
        );
    }

    protected function createLogger(): LoggerInterface
    {
        $handler = new NullHandler($this->level);

        return new Logger($this->name, [$handler]);
    }
}
