<?php

declare(strict_types=1);

namespace Thingston\Tests\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Thingston\Log\Exception\InvalidArgumentException;

trait TestLoggerTrait
{
    abstract protected function createLogger(): LoggerInterface;

    public function testLoggerInterface(): void
    {
        $logger = $this->createLogger();

        $this->assertInstanceOf(LoggerInterface::class, $logger);

        $logger->log(LogLevel::DEBUG, 'Some message');
        $logger->alert('Some message');
        $logger->critical('Some message');
        $logger->debug('Some message');
        $logger->emergency('Some message');
        $logger->error('Some message');
        $logger->info('Some message');
        $logger->notice('Some message');
        $logger->warning('Some message');
    }

    public function testInvalidLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createLogger()->log('foo', 'Some message');
    }
}
