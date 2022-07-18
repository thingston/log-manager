<?php

declare(strict_types=1);

namespace Thingston\Tests\Log;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Thingston\Log\Adapter\NullAdapter;
use Thingston\Log\Logger;

final class LoggerTest extends TestCase
{
    use TestLoggerTrait;

    protected function createLogger(): LoggerInterface
    {
        return new Logger([new NullAdapter('logger')]);
    }

    public function testLogWithoutBubbling(): void
    {
        $logger = new Logger([new NullAdapter('logger', LogLevel::DEBUG, false)]);

        $this->assertInstanceOf(Logger::class, $logger);

        $logger->log(LogLevel::DEBUG, 'Some message');
    }
}
