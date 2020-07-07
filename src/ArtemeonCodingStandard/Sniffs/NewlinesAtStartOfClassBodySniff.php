<?php

declare(strict_types=1);

namespace Artemeon\CodingStandard\ArtemeonCodingStandard\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function array_shift;
use function count;

final class NewlinesAtStartOfClassBodySniff implements Sniff
{
    use ConcerningNewlines;

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
                'NewlinesAtStartOfClassBody'
            );
            $this->fixSuperfluousNewlineTokens($phpcsFile, $newlineTokenPointers);
        }
    }
}