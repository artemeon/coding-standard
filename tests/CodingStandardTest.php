<?php

declare(strict_types=1);

namespace Artemeon\CodingStandard\Tests;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function glob;
use function str_replace;

final class CodingStandardTest extends TestCase
{
    /**
     * @var Config
     */
    private static $phpcsConfig;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        self::$phpcsConfig = new Config();
        self::$phpcsConfig->stdin = true;
        self::$phpcsConfig->verbosity = 0;
        self::$phpcsConfig->standards = ['ArtemeonCodingStandard'];

        $phpcsRunner = new Runner();
        $phpcsRunner->config = self::$phpcsConfig;
        $phpcsRunner->init();
        $phpcsRunner->ruleset->populateTokenListeners();
    }

    /**
     * @return string[][]
     */
    public function provideTestFilePaths(): iterable
    {
        foreach (glob(__DIR__ . '/files/expected/*.php') as $expectedFilePath) {
            $actualFilePath = str_replace('expected', 'actual', $expectedFilePath);
            yield [$expectedFilePath, $actualFilePath];
        }
    }

    /**
     * @dataProvider provideTestFilePaths
     * @param string $expectedFilePath
     * @param string $actualFilePath
     */
    public function testFixesFilesAccordingToCodingStandard(string $expectedFilePath, string $actualFilePath): void
    {
        $actualFileContents = file_get_contents($actualFilePath);
        $this->assertStringEqualsFile(
            $expectedFilePath,
            $this->getResultOfCodeSnifferFix($actualFileContents)
        );
    }

    private function getResultOfCodeSnifferFix(string $fileContents): string
    {
        $file = new DummyFile($fileContents, new Ruleset(self::$phpcsConfig), self::$phpcsConfig);
        $file->fixer->enabled = true;
        $file->process();

        return $file->fixer->getContents();
    }
}
