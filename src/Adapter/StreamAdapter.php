<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Thingston\Log\Exception\InvalidArgumentException;
use Throwable;

final class StreamAdapter extends AbstractAdapter
{
    protected function createLogger(): LoggerInterface
    {
        $stream = $this->arguments['stream'] ?? null;
        $level = $this->level;
        $bubble = $this->shouldBubble;
        $filePermission = $this->arguments['filePermission'] ?? null;
        $useLocking = (bool) ($this->arguments['useLocking'] ?? false);

        if (false === is_resource($stream) && false === is_string($stream)) {
            throw new InvalidArgumentException('Invalid type for "stream" argument.');
        }

        if (false === is_int($filePermission) && null !== $filePermission) {
            throw new InvalidArgumentException('Invalid type for "filePermission" argument.');
        }

        try {
            $handler = new StreamHandler($stream, $level, $bubble, $filePermission, $useLocking);
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
