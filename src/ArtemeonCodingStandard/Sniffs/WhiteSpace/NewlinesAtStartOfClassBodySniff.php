<?php

declare(strict_types=1);

namespace Artemeon\CodingStandard\ArtemeonCodingStandard\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_shift;
use function count;

final class NewlinesAtStartOfClassBodySniff implements Sniff
{
    /**
     * @inheritDoc
     */
    public function register(): array
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $openCurlyBracketTokenPointer = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr);
        if (!\is_int($openCurlyBracketTokenPointer)) {
            return;
        }

        $newlineTokenPointers = $this->findNextNewlineTokenPointers($phpcsFile, $openCurlyBracketTokenPointer + 1);

        if (count($newlineTokenPointers) > 1) {
            $phpcsFile->addFixableError(
                'The opening brace of a class body must not be followed by more than one newline.',
                array_shift($newlineTokenPointers),
                'Found'
            );
            $this->fixSuperfluousNewlineTokens($phpcsFile, $newlineTokenPointers);
        }
    }

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
