<?php

declare(strict_types=1);

namespace Thingston\Log;

use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Thingston\Settings\AbstractSettings;

final class LogSettings extends AbstractSettings
{
    public const DEFAULT = 'default';
    public const HANDLER = 'handler';
    public const ARGUMENTS = 'arguments';

    public function __construct()
    {
        parent::__construct([
            self::DEFAULT => 'stream',
            'stack' => ['stream'],
            'null' => [
                [
                    self::HANDLER => \Monolog\Handler\NullHandler::class,
                    self::ARGUMENTS => [
                        'level' => LogLevel::DEBUG,
                    ],
                ],
            ],
            'stream' => [
                [
                    self::HANDLER => StreamHandler::class,
                    self::ARGUMENTS => [
                        'stream' => sys_get_temp_dir() . '/thingston.log',
                        'level' => LogLevel::DEBUG,
                    ],
                ],
            ],
            'rotating' => [
                [
                    self::HANDLER => \Monolog\Handler\RotatingFileHandler::class,
                    self::ARGUMENTS => [
                        'filename' => sys_get_temp_dir() . '/thingston.log',
                        'maxFiles' => 7,
                        'level' => LogLevel::DEBUG,
                    ],
                ],
            ],
        ]);
    }
}
