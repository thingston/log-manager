<?php

declare(strict_types=1);

namespace Thingston\Log;

use Psr\Log\LoggerInterface;

interface LogManagerInterface extends LoggerInterface
{
    public function getLogger(?string $name = null): LoggerInterface;
}
