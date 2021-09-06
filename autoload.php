<?php

declare(strict_types=1);

if (!defined('ARTEMEON_CODING_STANDARD_PHPCS_AUTOLOAD_SET')) {
    if (is_file(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    } else {
        spl_autoload_register(static function (string $class): void {
            if (stripos($class, 'Artemeon\\CodingStandard\\') !== 0) {
                return;
            }

            $file = realpath(__DIR__) . DIRECTORY_SEPARATOR
                . 'src' . DIRECTORY_SEPARATOR
                . 'ArtemeonCodingStandard' . DIRECTORY_SEPARATOR
                . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

            if (file_exists($file)) {
                include_once $file;
            }
        });

    }

    define('ARTEMEON_CODING_STANDARD_PHPCS_AUTOLOAD_SET', true);
}
