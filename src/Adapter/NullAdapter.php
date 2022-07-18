<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class NullAdapter extends AbstractAdapter
{
    protected function createLogger(): LoggerInterface
    {
        $handler = new NullHandler($this->level);

        return new Logger($this->name, [$handler]);
    }
}
