<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Psr\Log\LoggerInterface;

interface AdapterInterface extends LoggerInterface
{
    /**
     * @return \Psr\Log\LogLevel::*
     */
    public function getLevel(): string;

    public function shouldBubble(): bool;
}
