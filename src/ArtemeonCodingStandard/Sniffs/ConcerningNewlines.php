<?php

declare(strict_types=1);

namespace Artemeon\CodingStandard\ArtemeonCodingStandard\Sniffs;

use PHP_CodeSniffer\Files\File;

trait ConcerningNewlines
{
    /**
     * @param File $phpcsFile
     * @param int $stackPointer
     * @return int[]
     */
    protected function findNextNewlineTokenPointers(File $phpcsFile, int $stackPointer): array
    {
        $newlineTokenPointers = [];
        $tokens = $phpcsFile->getTokens();

        for ($pointer = $stackPointer, $tokenCount = count($tokens); $pointer < $tokenCount; ++$pointer) {
            $token = $tokens[$pointer];
            if ($token['code'] !== T_WHITESPACE || $token['content'] !== "\n") {
                break;
            }

            $newlineTokenPointers[] = $pointer;
        }

        return $newlineTokenPointers;
    }

    /**
     * @param File $phpcsFile
     * @param int[] $newlineTokenPointers
     */
    protected function fixSuperfluousNewlineTokens(File $phpcsFile, array $newlineTokenPointers): void
    {
        $phpcsFile->fixer->beginChangeset();
        foreach ($newlineTokenPointers as $newlineTokenPointer) {
            $phpcsFile->fixer->replaceToken($newlineTokenPointer, '');
        }
        $phpcsFile->fixer->endChangeset();
    }
}
