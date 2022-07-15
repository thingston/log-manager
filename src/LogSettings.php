<?php

declare(strict_types=1);

namespace Thingston\Log;

use Monolog\Handler\NullHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;
use Psr\Log\LogLevel;
use Thingston\Settings\AbstractSettings;

final class LogSettings extends AbstractSettings
{
    public const DEFAULT = 'default';
    public const HANDLER = 'handler';
    public const ARGUMENTS = 'arguments';

    public const LOG_STACK = 'stack';
    public const LOG_STREAM = 'stream';
    public const LOG_NULL = 'null';
    public const LOG_ROTATING = 'rotating';
    public const LOG_ERROR = 'error';

    /**
     * @param array<string, array<mixed>|scalar|\Thingston\Settings\SettingsInterface> $settings
     */
    public function __construct(array $settings = [])
    {
        parent::__construct(array_merge([
            self::DEFAULT => self::LOG_STACK,
            self::LOG_STACK => [self::LOG_STREAM, self::LOG_ERROR],
            self::LOG_NULL => [
                self::HANDLER => NullHandler::class,
                self::ARGUMENTS => [
                    'level' => LogLevel::DEBUG,
                ],
            ],
            self::LOG_STREAM => [
                self::HANDLER => StreamHandler::class,
                self::ARGUMENTS => [
                    'stream' => sys_get_temp_dir() . '/thingston.log',
                    'level' => LogLevel::DEBUG,
                ],
            ],
            self::LOG_ROTATING => [
                self::HANDLER => RotatingFileHandler::class,
                self::ARGUMENTS => [
                    'filename' => sys_get_temp_dir() . '/thingston.log',
                    'maxFiles' => 7,
                    'level' => LogLevel::DEBUG,
                ],
            ],
            self::LOG_ERROR => [
                self::HANDLER => ErrorLogHandler::class,
                self::ARGUMENTS => [
                    'messageType' => 0,
                    'level' => LogLevel::ERROR,
                ],
            ],
        ], $settings));
    }
}
