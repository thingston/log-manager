<?php

declare(strict_types=1);

namespace Thingston\Tests\Log;

use Monolog\Handler\NullHandler;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Thingston\Log\LogManager;
use Thingston\Settings\Settings;
use Thingston\Log\Exception\InvalidArgumentException;

final class LogManagerTest extends TestCase
{
    public function testGetDefaultLogger(): void
    {
        $manager = new LogManager();

        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger());
    }

    public function testGetNamedLogger(): void
    {
        $manager = new LogManager();

        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('default'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('stack'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('stream'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('null'));
    }

    public function testGetCustomLogger(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => ['handler' => NullHandler::class],
            'logger2' => [['handler' => NullHandler::class], 'logger1'],
            'logger3' => ['logger2', ['handler' => NullHandler::class], 'logger1'],
        ]));

        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('logger1'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('logger1'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('logger2'));
        $this->assertInstanceOf(LoggerInterface::class, $manager->getLogger('logger3'));
    }

    public function testInvalidNameLogger(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => ['handler' => NullHandler::class],
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger();
    }

    public function testInvalidConfigType(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => true,
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger('logger1');
    }

    public function testInvalidConfigType2(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => [true],
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger('logger1');
    }

    public function testMissingHandler(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => [['foo' => 'bar']],
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger('logger1');
    }

    public function testInvalidArguments(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => [['handler' => NullHandler::class, 'arguments' => true]],
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger('logger1');
    }

    public function testInvalidHandler(): void
    {
        $manager = new LogManager(new Settings([
            'logger1' => [['handler' => 'foo']],
        ]));

        $this->expectException(InvalidArgumentException::class);
        $manager->getLogger('logger1');
    }

    public function testLogManagerIsLogger(): void
    {
        $manager = new LogManager();

        $manager->alert('Some alert');
        $manager->critical('Some critical');
        $manager->debug('Some debug');
        $manager->emergency('Some emergency');
        $manager->error('Some error');
        $manager->info('Some info');
        $manager->notice('Some notice');
        $manager->warning('Some warning');

        $this->assertInstanceOf(LoggerInterface::class, $manager);
    }
}
