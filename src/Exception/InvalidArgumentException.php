<?php

declare(strict_types=1);

namespace Thingston\Log\Exception;

use Monolog\Handler\HandlerInterface;

class InvalidArgumentException extends \InvalidArgumentException implements LogExceptionInterface
{
    public static function forInvalidLoggerName(string $name): self
    {
        return new self(sprintf('Invalid logger name "%s".', $name));
    }

    public static function forInvalidConfigType(string $type, string $name): self
    {
        return new self(sprintf('Invalid config type "%s" for logger "%s".', $type, $name));
    }

    public static function forMissingAdapterClass(): self
    {
        return new self('Missing adapter class in config.');
    }

    public static function forInvalidAdapterClass(): self
    {
        return new self(sprintf('Adapter class is not an instance of "%s".', HandlerInterface::class));
    }
}
