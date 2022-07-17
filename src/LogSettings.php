<?php

declare(strict_types=1);

namespace Thingston\Log;

use Psr\Log\LogLevel;
use Thingston\Log\Adapter\NullAdapter;
use Thingston\Log\Adapter\StreamAdapter;
use Thingston\Settings\AbstractSettings;

final class LogSettings extends AbstractSettings
{
    public const DEFAULT = 'default';
    public const ADAPTER = 'adapter';
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
            self::LOG_STACK => [self::LOG_STREAM],
            self::LOG_NULL => [
                self::ADAPTER => NullAdapter::class,
                self::ARGUMENTS => [
                    'level' => LogLevel::DEBUG,
                ],
            ],
            self::LOG_STREAM => [
                self::ADAPTER => StreamAdapter::class,
                self::ARGUMENTS => [
                    'stream' => sys_get_temp_dir() . '/thingston.log',
                    'level' => LogLevel::DEBUG,
                ],
            ],
        ], $settings));
    }
}
