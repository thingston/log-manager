<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Thingston\Log\Exception\InvalidArgumentException;
use Throwable;

final class NullAdapter extends AbstractAdapter
{
    protected function createLogger(): LoggerInterface
    {
        $level = $this->level;

        try {
            $handler = new NullHandler($level);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(sprintf(
                'Unable to create adapter "%s" for logger "%s".',
                self::class,
                $this->name
            ), 0, $e);
        }

        return new Logger($this->name, [$handler]);
    }
}
