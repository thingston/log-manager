<?php

declare(strict_types=1);

namespace Thingston\Log\Adapter;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Thingston\Log\Exception\InvalidArgumentException;
use Throwable;

final class StreamAdapter extends AbstractAdapter
{
    /**
     * @param resource|string $stream
     * @param string $name
     * @param LogLevel::* $level
     * @param bool $shouldBubble
     * @param int|null $filePermission
     * @param bool $useLocking
     */
    public function __construct(
        private $stream,
        string $name,
        string $level = LogLevel::DEBUG,
        bool $shouldBubble = true,
        private ?int $filePermission = null,
        private bool $useLocking = false
    ) {
        parent::__construct($name, $level, $shouldBubble);

        $this->stream = $this->assertStream($stream);
        $this->filePermission = $filePermission;
        $this->useLocking = $useLocking;
    }

    /**
     * @param array<string, mixed> $arguments
     * @return AdapterInterface
     */
    public static function create(array $arguments): AdapterInterface
    {
        return new StreamAdapter(
            stream: static::assertStream($arguments['stream'] ?? null),
            name: static::assertName($arguments['name'] ?? null),
            level: static::assertLevel($arguments['level'] ?? \Psr\Log\LogLevel::DEBUG),
            shouldBubble: (bool) ($arguments['shouldBubble'] ?? true),
            useLocking: (bool) ($arguments['useLocking'] ?? false),
            filePermission: static::assertFilePermission($arguments['filePermission'] ?? null)
        );
    }

    /**
     * @param mixed $stream
     * @return resource|string
     */
    private static function assertStream($stream)
    {
        if (is_resource($stream) || (is_string($stream) && '' !== trim($stream))) {
            return $stream;
        }

        throw new InvalidArgumentException('Invalid "stream" value.');
    }

    /**
     * @param mixed $filePermission
     * @return int|null
     */
    private static function assertFilePermission($filePermission)
    {
        if (is_int($filePermission) || null === $filePermission) {
            return $filePermission;
        }

        throw new InvalidArgumentException('Invalid "filePermission" value.');
    }

    protected function createLogger(): LoggerInterface
    {
        try {
            $handler = new StreamHandler(
                $this->stream,
                $this->level,
                $this->shouldBubble,
                $this->filePermission,
                $this->useLocking
            );
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
