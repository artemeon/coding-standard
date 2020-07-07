<?php

declare(strict_types=1);

namespace Artemeon\CodingStandard\ArtemeonCodingStandard;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function in_array;
use function lcfirst;
use function preg_match;

final class TypePrefixOnVariableNameSniff implements Sniff
{
    private const RECOGNIZED_TYPE_PREFIXES = [
        'str',
        'int',
        'bit',
        'float',
        'obj',
        'arr',
    ];

    /**
     * @inheritDoc
     */
    public function register(): array
    {
        return [
            T_VARIABLE,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        if ($token['code'] === T_VARIABLE) {
            $prefix = $this->getVariableNamePrefix($token['content']);

            if (isset($prefix) && in_array($prefix, self::RECOGNIZED_TYPE_PREFIXES, true)) {
                $phpcsFile->addFixableWarning(
                    'Type prefixes on variable names are discouraged.',
                    $stackPtr,
                    'TypePrefixOnVariableNameDetected'
                );
                $phpcsFile->fixer->replaceToken(
                    $stackPtr,
                    $this->removeVariableNamePrefix($token['content'])
                );
            }
        }
    }

    private function getVariableNamePrefix(string $variableName): ?string
    {
        if (!preg_match('/^\$([a-z]+)(?=[A-Z_])/', $variableName, $match)) {
            return null;
        }

        return $match[1];
    }

    private function removeVariableNamePrefix(string $variableName): string
    {
        preg_match('/^\$[a-z]+(?=[A-Z_])(.+)$/', $variableName, $match);

        return '$' . lcfirst($match[1]);
    }
}
