<?php

declare(strict_types=1);

namespace Thingston\Tests\Log\Adapter;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Thingston\Log\Adapter\AbstractAdapter;
use Thingston\Log\Adapter\NullAdapter;
use Thingston\Log\Exception\InvalidArgumentException;
use Thingston\Tests\Log\TestLoggerTrait;

final class AbstractAdapterTest extends TestCase
{
    use TestLoggerTrait;

    protected function createLogger(): LoggerInterface
    {
        return new NullAdapter('logger');
    }

    public function testArguments(): void
    {
        $adapter = new NullAdapter('logger');

        $this->assertInstanceOf(AbstractAdapter::class, $adapter);
        $this->assertSame('logger', $adapter->getName());
        $this->assertSame(LogLevel::DEBUG, $adapter->getLevel());
        $this->assertTrue($adapter->shouldBubble());
    }

    public function testInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NullAdapter('');
    }

    public function testInvalidLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NullAdapter('logger', 'foo'); // @phpstan-ignore-line
    }
}
