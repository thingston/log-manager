<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Psr\Log\LoggerInterface;

interface AdapterInterface extends LoggerInterface
{
    /**
     * @param array<string, mixed> $arguments
     * @return AdapterInterface
     */
    public static function create(array $arguments): AdapterInterface;

    /**
     * @return \Psr\Log\LogLevel::*
     */
    public function getLevel(): string;

    public function shouldBubble(): bool;
}
